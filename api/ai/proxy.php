<?php
/**
 * AI Studio — proxy.php  [server-side LLM proxy]
 *
 * WHY: Browsers block direct calls to most LLM APIs (NVIDIA, SambaNova, Chutes,
 * etc.) via CORS -> "Failed to fetch". This proxy makes the call SERVER-SIDE
 * (cURL, no CORS) and returns a normalized result the client can use.
 *
 * Deploy to: {app}/api/ai/proxy.php  (called by building.php / generate.php as "api/ai/proxy.php")
 *
 * Request  (POST JSON): { provider, api_key, model, prompt, max_tokens }
 * Response (JSON):       { ok: bool, code: int, text: string, msg: string }
 */
session_name('ai_studio_session');
session_start();
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store');
@set_time_limit(180); // big models (253B) can take a while

if (!isset($_SESSION['studio_user_id'])) {
    echo json_encode(['ok' => false, 'code' => 401, 'msg' => 'Not logged in']);
    exit;
}

$in        = json_decode(file_get_contents('php://input'), true);
$provider  = trim($in['provider']   ?? '');
$apiKey    = trim($in['api_key']    ?? '');
$model     = trim($in['model']      ?? '');
$prompt    = (string)($in['prompt'] ?? '');
$maxTokens = (int)($in['max_tokens'] ?? 6000);
$temp      = isset($in['temperature']) ? (float)$in['temperature'] : 0.6;

if ($provider === '' || $apiKey === '' || $model === '' || $prompt === '') {
    echo json_encode(['ok' => false, 'code' => 0, 'msg' => 'provider, api_key, model, prompt required']);
    exit;
}

$OPENAI_STYLE_ENDPOINTS = [
    'nvidia'     => 'https://integrate.api.nvidia.com/v1/chat/completions',
    'sambanova'  => 'https://api.sambanova.ai/v1/chat/completions',
    'chutes'     => 'https://llm.chutes.ai/v1/chat/completions',
    'openrouter' => 'https://openrouter.ai/api/v1/chat/completions',
];

// Build the request (Gemini has its own format; everything else is OpenAI-style)
if ($provider === 'gemini') {
    $url     = 'https://generativelanguage.googleapis.com/v1beta/models/' . rawurlencode($model)
             . ':generateContent?key=' . urlencode($apiKey);
    $headers = ['Content-Type: application/json'];
    $payload = json_encode([
        'contents'         => [['parts' => [['text' => $prompt]]]],
        'generationConfig' => ['temperature' => $temp, 'maxOutputTokens' => $maxTokens],
    ]);
} elseif (isset($OPENAI_STYLE_ENDPOINTS[$provider])) {
    $url     = $OPENAI_STYLE_ENDPOINTS[$provider];
    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey,
        'HTTP-Referer: https://internshipadda.ai',
        'X-Title: AI Studio',
    ];
    $payload = json_encode([
        'model'       => $model,
        'messages'    => [['role' => 'user', 'content' => $prompt]],
        'max_tokens'  => $maxTokens,
        'temperature' => $temp,
    ]);
} else {
    echo json_encode(['ok' => false, 'code' => 0, 'msg' => 'Unknown provider: ' . $provider]);
    exit;
}

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_HTTPHEADER     => $headers,
    CURLOPT_TIMEOUT        => 150,
    CURLOPT_CONNECTTIMEOUT => 20,
    CURLOPT_SSL_VERIFYPEER => false,
]);
$resp   = curl_exec($ch);
$status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
$cerr   = curl_error($ch);
curl_close($ch);

if ($resp === false || $cerr) {
    echo json_encode(['ok' => false, 'code' => 0, 'msg' => 'Server fetch error: ' . $cerr]);
    exit;
}

$j = json_decode($resp, true);

// Extract the generated text
$text = '';
if ($provider === 'gemini') {
    $text = $j['candidates'][0]['content']['parts'][0]['text'] ?? '';
} else {
    $text = $j['choices'][0]['message']['content'] ?? '';
}

if ($status === 200 && is_string($text) && trim($text) !== '') {
    echo json_encode(['ok' => true, 'code' => 200, 'text' => $text]);
    exit;
}

// Build a clear error message
$msg = 'HTTP ' . $status;
if (is_array($j)) {
    if (isset($j['error']['message']))                 $msg = substr($j['error']['message'], 0, 120);
    elseif (isset($j['error']) && is_string($j['error'])) $msg = substr($j['error'], 0, 120);
    elseif (isset($j['message']))                      $msg = substr($j['message'], 0, 120);
}
if ($status === 200) $msg = 'Empty response from model';
if ($status === 401) $msg = 'Invalid API key (401)';
if ($status === 402) $msg = 'No balance/credit (402)';
if ($status === 429) $msg = 'Rate limited (429)';

echo json_encode(['ok' => false, 'code' => $status, 'msg' => $msg]);
