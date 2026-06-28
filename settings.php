<?php
session_name('ai_studio_session');
session_start();
if (!isset($_SESSION['studio_user_id'])) { header('Location: index.php'); exit; }
if ($_SESSION['studio_role'] !== 'admin') { header('Location: dashboard.php'); exit; }
$userName = $_SESSION['studio_name'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Settings — AI Studio</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { background: #1F2A44; font-family: 'Segoe UI', sans-serif; color: #fff; }
.sidebar { background: rgba(0,0,0,0.25); border-right: 1px solid rgba(255,255,255,0.08); width: 240px; min-height: 100vh; position: fixed; top: 0; left: 0; z-index: 50; display: flex; flex-direction: column; padding: 24px 16px; }
.nav-link { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 10px; color: rgba(255,255,255,0.5); font-size: 14px; text-decoration: none; transition: all 0.2s; }
.nav-link:hover { background: rgba(255,255,255,0.08); color: #fff; }
.nav-link.active { background: rgba(255,255,255,0.12); color: #fff; border: 1px solid rgba(255,255,255,0.15); }
.main { margin-left: 240px; padding: 40px; }
.card { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 16px; padding: 28px; margin-bottom: 20px; }
.section-title { font-size: 16px; font-weight: 700; margin-bottom: 4px; }
.section-sub { color: rgba(255,255,255,0.4); font-size: 13px; margin-bottom: 22px; }
.input-f { background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.15); color: #fff; border-radius: 10px; padding: 11px 15px; width: 100%; font-size: 14px; outline: none; transition: border-color 0.2s; }
.input-f:focus { border-color: rgba(255,255,255,0.5); }
.input-f::placeholder { color: rgba(255,255,255,0.2); }
select.input-f option { background: #1F2A44; }
label { color: rgba(255,255,255,0.55); font-size: 13px; display: block; margin-bottom: 7px; }
.provider-btn { background: rgba(255,255,255,0.05); border: 2px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 16px 12px; cursor: pointer; transition: all 0.2s; text-align: center; }
.provider-btn:hover { border-color: rgba(255,255,255,0.3); }
.provider-btn.active { border-color: #fff; background: rgba(255,255,255,0.1); }
.badge-free { background: rgba(34,197,94,0.15); color: #4ade80; border: 1px solid rgba(34,197,94,0.3); padding: 3px 9px; border-radius: 20px; font-size: 11px; font-weight: 700; display: inline-block; margin-top: 6px; }
.badge-paid { background: rgba(239,68,68,0.12); color: #f87171; border: 1px solid rgba(239,68,68,0.3); padding: 3px 9px; border-radius: 20px; font-size: 11px; display: inline-block; margin-top: 6px; }
.badge-opt { background: rgba(251,191,36,0.15); color: #fbbf24; border: 1px solid rgba(251,191,36,0.3); padding: 2px 8px; border-radius: 20px; font-size: 10px; font-weight: 700; display: inline-block; margin-left: 6px; vertical-align: middle; }
.btn-save { background: #fff; color: #1F2A44; border: none; border-radius: 10px; padding: 13px 32px; font-size: 15px; font-weight: 700; cursor: pointer; }
.btn-save:hover { background: #f0f0f0; }
.btn-test { background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.2); color: #fff; border-radius: 8px; padding: 10px 18px; font-size: 13px; cursor: pointer; width: 100%; margin-top: 12px; font-weight: 600; }
.btn-test:hover { background: rgba(255,255,255,0.14); }
.btn-test:disabled { opacity: 0.5; cursor: not-allowed; }
.key-group { display: flex; flex-direction: column; gap: 10px; margin-bottom: 14px; }
.key-item { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07); border-radius: 10px; padding: 12px 14px; }
.model-row { display: flex; justify-content: space-between; align-items: center; padding: 9px 10px; border-bottom: 1px solid rgba(255,255,255,0.05); border-radius: 6px; }
.model-row:last-child { border-bottom: none; }
.guide { background: rgba(59,130,246,0.08); border: 1px solid rgba(59,130,246,0.25); border-radius: 10px; padding: 13px 15px; margin-bottom: 16px; }
.guide .g-title { color: #93c5fd; font-size: 13px; font-weight: 700; margin-bottom: 7px; display: flex; align-items: center; gap: 6px; }
.guide ol { margin: 0 0 0 18px; padding: 0; }
.guide li { color: rgba(255,255,255,0.65); font-size: 12.5px; line-height: 1.7; margin-bottom: 2px; }
.guide a { color: #60a5fa; font-weight: 600; text-decoration: none; }
.guide a:hover { text-decoration: underline; }
.guide .g-note { color: rgba(255,255,255,0.4); font-size: 11.5px; margin-top: 7px; }
#toast { display: none; position: fixed; top: 24px; right: 24px; padding: 13px 20px; border-radius: 10px; font-size: 14px; font-weight: 600; z-index: 9999; box-shadow: 0 4px 20px rgba(0,0,0,0.4); }
</style>
</head>
<body>
<div id="toast"></div>

<div class="sidebar">
  <div style="margin-bottom:32px">
    <div style="display:flex;align-items:center;gap:10px">
      <span style="font-size:22px">🤖</span>
      <span style="font-weight:800;font-size:17px">AI Studio</span>
    </div>
    <p style="color:rgba(255,255,255,0.3);font-size:11px;padding-left:32px;margin-top:2px">InternshipADDA</p>
  </div>
  <nav style="flex:1;display:flex;flex-direction:column;gap:4px">
    <a href="dashboard.php" class="nav-link">📊 Dashboard</a>
    <a href="generate.php"  class="nav-link">✨ Generate</a>
    <a href="settings.php"  class="nav-link active">⚙️ Settings</a>
  </nav>
  <div style="border-top:1px solid rgba(255,255,255,0.08);padding-top:16px">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
      <div style="width:34px;height:34px;border-radius:50%;background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;font-weight:700"><?= strtoupper(substr($userName,0,1)) ?></div>
      <div>
        <p style="font-size:13px;font-weight:600"><?= htmlspecialchars($userName) ?></p>
        <p style="color:rgba(255,255,255,0.35);font-size:11px">Admin</p>
      </div>
    </div>
    <a href="logout.php" style="display:flex;align-items:center;gap:8px;padding:9px 12px;border-radius:8px;color:rgba(255,100,100,0.8);font-size:13px;text-decoration:none" onmouseover="this.style.background='rgba(239,68,68,0.1)'" onmouseout="this.style.background='transparent'">🚪 Logout</a>
  </div>
</div>

<div class="main">
  <div style="max-width:720px">
    <div style="margin-bottom:32px">
      <h1 style="font-size:24px;font-weight:800;margin-bottom:6px">⚙️ Settings</h1>
      <p style="color:rgba(255,255,255,0.4);font-size:14px">AI provider aur system configure karo</p>
    </div>

    <!-- PROVIDER RECOMMENDATION -->
    <div class="card" style="border-color:rgba(34,197,94,0.35);background:rgba(34,197,94,0.05)">
      <div class="section-title" style="color:#4ade80">⚡ 5 MASSIVE Free Models — Smart Auto-Chain</div>
      <div class="section-sub">Ek khatam ho to engine apne aap agle massive model pe — hamesha next-level quality</div>
      <div style="background:rgba(255,255,255,0.04);border-radius:10px;padding:14px;line-height:2">
        <p style="color:rgba(255,255,255,0.8);font-size:13px">
          1️⃣ 🟢 <strong>NVIDIA Nemotron Ultra 253B</strong> (primary)<br>
          2️⃣ 🔴 <strong>SambaNova Llama 405B</strong><br>
          3️⃣ 🪂 <strong>Chutes Kimi K2 / DeepSeek V3</strong><br>
          4️⃣ 🌐 <strong>OpenRouter DeepSeek V3</strong> (free)<br>
          5️⃣ ✨ <strong>Gemini 2.5 Flash</strong> (safety net)
        </p>
      </div>
      <div style="margin-top:12px;padding:10px 14px;background:rgba(59,130,246,0.08);border-radius:8px">
        <p style="color:rgba(255,255,255,0.6);font-size:11.5px">💡 NVIDIA + Gemini zaroor daalo. Engine smart hai — jo key+model kaam kar raha hai usi pe rahega, khatam hone pe agle massive model pe switch.</p>
      </div>
    </div>

    <!-- PROVIDER SELECTION -->
    <div class="card">
      <div class="section-title">🤖 AI Provider</div>
      <div class="section-sub">Kaunse AI se course content generate hoga</div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:24px">
        <div class="provider-btn active" id="btnNvidia" onclick="selectAI('nvidia')">
          <div style="font-size:26px;margin-bottom:4px">🟢</div>
          <div style="font-weight:700;font-size:14px">NVIDIA Nemotron</div>
          <div style="color:rgba(255,255,255,0.4);font-size:11px;margin-top:2px">Nemotron Ultra 253B</div>
          <span class="badge-free">FREE</span>
        </div>
        <div class="provider-btn" id="btnSambanova" onclick="selectAI('sambanova')">
          <div style="font-size:26px;margin-bottom:4px">🔴</div>
          <div style="font-weight:700;font-size:14px">SambaNova</div>
          <div style="color:rgba(255,255,255,0.4);font-size:11px;margin-top:2px">Llama 405B (massive)</div>
          <span class="badge-free">FREE</span>
        </div>
        <div class="provider-btn" id="btnChutes" onclick="selectAI('chutes')">
          <div style="font-size:26px;margin-bottom:4px">🪂</div>
          <div style="font-weight:700;font-size:14px">Chutes.ai</div>
          <div style="color:rgba(255,255,255,0.4);font-size:11px;margin-top:2px">Kimi K2, DeepSeek V3</div>
          <span class="badge-free">FREE</span>
        </div>
        <div class="provider-btn" id="btnOpenrouter" onclick="selectAI('openrouter')">
          <div style="font-size:26px;margin-bottom:4px">🌐</div>
          <div style="font-weight:700;font-size:14px">OpenRouter</div>
          <div style="color:rgba(255,255,255,0.4);font-size:11px;margin-top:2px">DeepSeek V3 (free)</div>
          <span class="badge-free">FREE</span>
        </div>
        <div class="provider-btn" id="btnGemini" onclick="selectAI('gemini')">
          <div style="font-size:26px;margin-bottom:4px">✨</div>
          <div style="font-weight:700;font-size:14px">Google Gemini</div>
          <div style="color:rgba(255,255,255,0.4);font-size:11px;margin-top:2px">2.5 Flash (safety net)</div>
          <span class="badge-free">FREE</span>
        </div>
      </div>

      <!-- NVIDIA -->
      <div id="nvidiaSection">
        <div class="guide">
          <div class="g-title">🟢 NVIDIA Nemotron API key kaise laaye? <span style="color:#4ade80;font-size:11px">(FREE credits ~60 courses)</span></div>
          <ol>
            <li>Khol <a href="https://build.nvidia.com" target="_blank">build.nvidia.com</a></li>
            <li>Sign in karo (free NVIDIA account)</li>
            <li>Koi bhi model kholo → <strong>"Get API Key"</strong> → copy (nvapi-... se shuru)</li>
            <li>Yahan API Key 1 me paste → Test → Save</li>
          </ol>
          <p class="g-note">🔥 Nemotron Ultra 253B — sabse powerful free model. ~5000 credits (~60 courses), phir naya account/thoda paisa.</p>
        </div>
        <div class="key-group">
          <div class="key-item">
            <label style="color:#fff;font-weight:600">🔑 API Key 1 <a href="https://build.nvidia.com" target="_blank" style="color:rgba(255,255,255,0.35);font-size:11px;font-weight:400;margin-left:8px">Key lao →</a></label>
            <div style="position:relative"><input type="password" id="nvidiaKey" class="input-f" placeholder="nvapi-..."><button type="button" onclick="tv('nvidiaKey')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:rgba(255,255,255,0.4);cursor:pointer">👁</button></div>
          </div>
          <div class="key-item">
            <label>🔑 API Key 2 <span class="badge-opt">OPTIONAL</span></label>
            <div style="position:relative"><input type="password" id="nvidiaKey2" class="input-f" placeholder="nvapi-..."><button type="button" onclick="tv('nvidiaKey2')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:rgba(255,255,255,0.4);cursor:pointer">👁</button></div>
          </div>
          <div class="key-item">
            <label>🔑 API Key 3 <span class="badge-opt">OPTIONAL</span></label>
            <div style="position:relative"><input type="password" id="nvidiaKey3" class="input-f" placeholder="nvapi-..."><button type="button" onclick="tv('nvidiaKey3')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:rgba(255,255,255,0.4);cursor:pointer">👁</button></div>
          </div>
        </div>
        <button class="btn-test" id="testNvidiaBtn" onclick="testModels('nvidia')">🔍 Test Keys & Auto-Select Best Model</button>
        <div id="nvidiaTestResults" style="display:none;margin-top:14px;background:rgba(0,0,0,0.25);border:1px solid rgba(255,255,255,0.08);border-radius:10px;padding:16px">
          <p style="color:rgba(255,255,255,0.4);font-size:11px;font-weight:700;letter-spacing:1px;margin-bottom:12px">MODEL TEST RESULTS — NVIDIA</p>
          <div id="nvidiaModelList"></div>
          <div id="nvidiaBestBox" style="display:none;margin-top:14px;padding:14px;background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.3);border-radius:8px">
            <p style="color:#4ade80;font-size:12px;font-weight:700;margin-bottom:10px">✅ Working Models — Select karo:</p>
            <select id="nvidiaSelectedModel" class="input-f" style="font-family:monospace;font-weight:700"></select>
          </div>
        </div>
      </div>

      <!-- SAMBANOVA -->
      <div id="sambanovaSection" style="display:none">
        <div class="guide">
          <div class="g-title">🔴 SambaNova API key kaise laaye? <span style="color:#4ade80;font-size:11px">($5 free credit = millions tokens)</span></div>
          <ol>
            <li>Khol <a href="https://cloud.sambanova.ai" target="_blank">cloud.sambanova.ai</a></li>
            <li>Sign up karo (free)</li>
            <li><strong>API Keys</strong> → <strong>Generate</strong> → key copy</li>
            <li>Yahan API Key 1 me paste → Test → Save</li>
          </ol>
          <p class="g-note">⚡ Llama 405B + DeepSeek V3.1, bahut fast. $5 free credit (millions tokens), 30 din valid.</p>
        </div>
        <div class="key-group">
          <div class="key-item">
            <label style="color:#fff;font-weight:600">🔑 API Key 1 <a href="https://cloud.sambanova.ai" target="_blank" style="color:rgba(255,255,255,0.35);font-size:11px;font-weight:400;margin-left:8px">Key lao →</a></label>
            <div style="position:relative"><input type="password" id="sambanovaKey" class="input-f" placeholder="..."><button type="button" onclick="tv('sambanovaKey')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:rgba(255,255,255,0.4);cursor:pointer">👁</button></div>
          </div>
          <div class="key-item">
            <label>🔑 API Key 2 <span class="badge-opt">OPTIONAL</span></label>
            <div style="position:relative"><input type="password" id="sambanovaKey2" class="input-f" placeholder="..."><button type="button" onclick="tv('sambanovaKey2')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:rgba(255,255,255,0.4);cursor:pointer">👁</button></div>
          </div>
          <div class="key-item">
            <label>🔑 API Key 3 <span class="badge-opt">OPTIONAL</span></label>
            <div style="position:relative"><input type="password" id="sambanovaKey3" class="input-f" placeholder="..."><button type="button" onclick="tv('sambanovaKey3')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:rgba(255,255,255,0.4);cursor:pointer">👁</button></div>
          </div>
        </div>
        <button class="btn-test" id="testSambanovaBtn" onclick="testModels('sambanova')">🔍 Test Keys & Auto-Select Best Model</button>
        <div id="sambanovaTestResults" style="display:none;margin-top:14px;background:rgba(0,0,0,0.25);border:1px solid rgba(255,255,255,0.08);border-radius:10px;padding:16px">
          <p style="color:rgba(255,255,255,0.4);font-size:11px;font-weight:700;letter-spacing:1px;margin-bottom:12px">MODEL TEST RESULTS — SAMBANOVA</p>
          <div id="sambanovaModelList"></div>
          <div id="sambanovaBestBox" style="display:none;margin-top:14px;padding:14px;background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.3);border-radius:8px">
            <p style="color:#4ade80;font-size:12px;font-weight:700;margin-bottom:10px">✅ Working Models — Select karo:</p>
            <select id="sambanovaSelectedModel" class="input-f" style="font-family:monospace;font-weight:700"></select>
          </div>
        </div>
      </div>

      <!-- CHUTES -->
      <div id="chutesSection" style="display:none">
        <div class="guide">
          <div class="g-title">🪂 Chutes.ai API key kaise laaye? <span style="color:#4ade80;font-size:11px">(free tier, OpenAI-compatible)</span></div>
          <ol>
            <li>Khol <a href="https://chutes.ai" target="_blank">chutes.ai</a></li>
            <li>Sign up karo</li>
            <li>API key generate karo → copy</li>
            <li>Yahan API Key 1 me paste → Test → Save</li>
          </ol>
          <p class="g-note">🌙 Kimi K2.5, DeepSeek V3, GLM — massive models, free tier daily quota.</p>
        </div>
        <div class="key-group">
          <div class="key-item">
            <label style="color:#fff;font-weight:600">🔑 API Key 1 <a href="https://chutes.ai" target="_blank" style="color:rgba(255,255,255,0.35);font-size:11px;font-weight:400;margin-left:8px">Key lao →</a></label>
            <div style="position:relative"><input type="password" id="chutesKey" class="input-f" placeholder="cpk_..."><button type="button" onclick="tv('chutesKey')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:rgba(255,255,255,0.4);cursor:pointer">👁</button></div>
          </div>
          <div class="key-item">
            <label>🔑 API Key 2 <span class="badge-opt">OPTIONAL</span></label>
            <div style="position:relative"><input type="password" id="chutesKey2" class="input-f" placeholder="cpk_..."><button type="button" onclick="tv('chutesKey2')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:rgba(255,255,255,0.4);cursor:pointer">👁</button></div>
          </div>
          <div class="key-item">
            <label>🔑 API Key 3 <span class="badge-opt">OPTIONAL</span></label>
            <div style="position:relative"><input type="password" id="chutesKey3" class="input-f" placeholder="cpk_..."><button type="button" onclick="tv('chutesKey3')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:rgba(255,255,255,0.4);cursor:pointer">👁</button></div>
          </div>
        </div>
        <button class="btn-test" id="testChutesBtn" onclick="testModels('chutes')">🔍 Test Keys & Auto-Select Best Model</button>
        <div id="chutesTestResults" style="display:none;margin-top:14px;background:rgba(0,0,0,0.25);border:1px solid rgba(255,255,255,0.08);border-radius:10px;padding:16px">
          <p style="color:rgba(255,255,255,0.4);font-size:11px;font-weight:700;letter-spacing:1px;margin-bottom:12px">MODEL TEST RESULTS — CHUTES</p>
          <div id="chutesModelList"></div>
          <div id="chutesBestBox" style="display:none;margin-top:14px;padding:14px;background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.3);border-radius:8px">
            <p style="color:#4ade80;font-size:12px;font-weight:700;margin-bottom:10px">✅ Working Models — Select karo:</p>
            <select id="chutesSelectedModel" class="input-f" style="font-family:monospace;font-weight:700"></select>
          </div>
        </div>
      </div>

      <!-- OPENROUTER -->
      <div id="openrouterSection" style="display:none">
        <div class="guide">
          <div class="g-title">🌐 OpenRouter API key kaise laaye? <span style="color:#4ade80;font-size:11px">(free, ~50/din)</span></div>
          <ol>
            <li>Khol <a href="https://openrouter.ai/keys" target="_blank">openrouter.ai/keys</a></li>
            <li>Sign up (Google/GitHub)</li>
            <li><strong>Create Key</strong> → copy (sk-or-... se shuru)</li>
            <li>Yahan API Key 1 me paste → Test → Save</li>
          </ol>
          <p class="g-note">💡 DeepSeek V3, Llama 4 free (:free models). ~50 req/din — backup ke liye perfect.</p>
        </div>
        <div class="key-group">
          <div class="key-item">
            <label style="color:#fff;font-weight:600">🔑 API Key 1 <a href="https://openrouter.ai/keys" target="_blank" style="color:rgba(255,255,255,0.35);font-size:11px;font-weight:400;margin-left:8px">Key lao →</a></label>
            <div style="position:relative"><input type="password" id="openrouterKey" class="input-f" placeholder="sk-or-..."><button type="button" onclick="tv('openrouterKey')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:rgba(255,255,255,0.4);cursor:pointer">👁</button></div>
          </div>
          <div class="key-item">
            <label>🔑 API Key 2 <span class="badge-opt">OPTIONAL</span></label>
            <div style="position:relative"><input type="password" id="openrouterKey2" class="input-f" placeholder="sk-or-..."><button type="button" onclick="tv('openrouterKey2')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:rgba(255,255,255,0.4);cursor:pointer">👁</button></div>
          </div>
          <div class="key-item">
            <label>🔑 API Key 3 <span class="badge-opt">OPTIONAL</span></label>
            <div style="position:relative"><input type="password" id="openrouterKey3" class="input-f" placeholder="sk-or-..."><button type="button" onclick="tv('openrouterKey3')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:rgba(255,255,255,0.4);cursor:pointer">👁</button></div>
          </div>
        </div>
        <button class="btn-test" id="testOpenrouterBtn" onclick="testModels('openrouter')">🔍 Test Keys & Auto-Select Best Model</button>
        <div id="openrouterTestResults" style="display:none;margin-top:14px;background:rgba(0,0,0,0.25);border:1px solid rgba(255,255,255,0.08);border-radius:10px;padding:16px">
          <p style="color:rgba(255,255,255,0.4);font-size:11px;font-weight:700;letter-spacing:1px;margin-bottom:12px">MODEL TEST RESULTS — OPENROUTER</p>
          <div id="openrouterModelList"></div>
          <div id="openrouterBestBox" style="display:none;margin-top:14px;padding:14px;background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.3);border-radius:8px">
            <p style="color:#4ade80;font-size:12px;font-weight:700;margin-bottom:10px">✅ Working Models — Select karo:</p>
            <select id="openrouterSelectedModel" class="input-f" style="font-family:monospace;font-weight:700"></select>
          </div>
        </div>
      </div>

      <!-- GEMINI -->
      <div id="geminiSection" style="display:none">
        <div class="guide">
          <div class="g-title">✨ Google Gemini API key kaise laaye? <span style="color:#4ade80;font-size:11px">(high free limit, no card)</span></div>
          <ol>
            <li>Khol <a href="https://aistudio.google.com/app/apikey" target="_blank">aistudio.google.com/app/apikey</a></li>
            <li>Google account se login</li>
            <li><strong>Create API key</strong> → copy (AIzaSy... se shuru)</li>
            <li>Yahan API Key 1 me paste → Test → Save</li>
          </ol>
          <p class="g-note">🛟 Gemini 2.5 Flash — high daily free limit, no card. Final safety net.</p>
        </div>
        <div class="key-group">
          <div class="key-item">
            <label style="color:#fff;font-weight:600">🔑 API Key 1 <a href="https://aistudio.google.com/app/apikey" target="_blank" style="color:rgba(255,255,255,0.35);font-size:11px;font-weight:400;margin-left:8px">Key lao →</a></label>
            <div style="position:relative"><input type="password" id="geminiKey" class="input-f" placeholder="AIzaSy..."><button type="button" onclick="tv('geminiKey')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:rgba(255,255,255,0.4);cursor:pointer">👁</button></div>
          </div>
          <div class="key-item">
            <label>🔑 API Key 2 <span class="badge-opt">OPTIONAL</span></label>
            <div style="position:relative"><input type="password" id="geminiKey2" class="input-f" placeholder="AIzaSy..."><button type="button" onclick="tv('geminiKey2')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:rgba(255,255,255,0.4);cursor:pointer">👁</button></div>
          </div>
          <div class="key-item">
            <label>🔑 API Key 3 <span class="badge-opt">OPTIONAL</span></label>
            <div style="position:relative"><input type="password" id="geminiKey3" class="input-f" placeholder="AIzaSy..."><button type="button" onclick="tv('geminiKey3')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:rgba(255,255,255,0.4);cursor:pointer">👁</button></div>
          </div>
        </div>
        <button class="btn-test" id="testGeminiBtn" onclick="testModels('gemini')">🔍 Test Keys & Auto-Select Best Model</button>
        <div id="geminiTestResults" style="display:none;margin-top:14px;background:rgba(0,0,0,0.25);border:1px solid rgba(255,255,255,0.08);border-radius:10px;padding:16px">
          <p style="color:rgba(255,255,255,0.4);font-size:11px;font-weight:700;letter-spacing:1px;margin-bottom:12px">MODEL TEST RESULTS — GEMINI</p>
          <div id="geminiModelList"></div>
          <div id="geminiBestBox" style="display:none;margin-top:14px;padding:14px;background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.3);border-radius:8px">
            <p style="color:#4ade80;font-size:12px;font-weight:700;margin-bottom:10px">✅ Working Models — Select karo:</p>
            <select id="geminiSelectedModel" class="input-f" style="font-family:monospace;font-weight:700"></select>
          </div>
        </div>
      </div>
    </div>

    <!-- CONTENT SETTINGS -->
    <div class="card">
      <div class="section-title">📝 Content Settings</div>
      <div class="section-sub">Default generation preferences</div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
        <div>
          <label>Default Language</label>
          <select id="defaultLang" class="input-f">
            <option value="English">English</option>
            <option value="Hindi">Hindi</option>
            <option value="Hinglish">Hinglish</option>
          </select>
        </div>
        <div>
          <label>Batch Size</label>
          <select id="batchSize" class="input-f">
            <option value="5">5 Days per batch</option>
            <option value="7" selected>7 Days per batch</option>
            <option value="10">10 Days per batch</option>
          </select>
        </div>
      </div>
      <div>
        <label>Image Search</label>
        <select id="imgEngine" class="input-f">
          <option value="google">Google Images</option>
          <option value="unsplash">Unsplash First</option>
          <option value="pexels">Pexels First</option>
        </select>
      </div>
    </div>

    <button class="btn-save" onclick="saveSettings()">💾 Settings Save Karo</button>
    <p id="saveStatus" style="color:rgba(255,255,255,0.3);font-size:12px;margin-top:10px"></p>
  </div>
</div>

<script>
var activeAI = 'nvidia';
var PROVIDERS = ['nvidia','sambanova','chutes','openrouter','gemini'];

// ── Load Settings ────────────────────────────────
async function loadSettings() {
    try {
        var r       = await fetch('api/ai/settings.php?action=get');
        var rawText = await r.text(); // ✅ pehle text lo
        console.log('[LOAD] raw:', rawText.substring(0, 200));

        if (rawText.indexOf('<?php') !== -1 || rawText.indexOf('Fatal') !== -1) {
            console.error('[LOAD] PHP not executing:', rawText);
            return;
        }

        var data;
        try { data = JSON.parse(rawText); }
        catch(e) { console.error('[LOAD] JSON parse fail:', rawText.substring(0,300)); return; }

        if (!data.success || !data.settings) return;
        var s = data.settings;

        activeAI = s['active_ai_provider'] || 'nvidia';
        selectAI(activeAI, false);

        PROVIDERS.forEach(function(p) {
            var k1 = document.getElementById(p+'Key');
            var k2 = document.getElementById(p+'Key2');
            var k3 = document.getElementById(p+'Key3');
            if (k1 && s[p+'_api_key'])   k1.value = s[p+'_api_key'];
            if (k2 && s[p+'_api_key_2']) k2.value = s[p+'_api_key_2'];
            if (k3 && s[p+'_api_key_3']) k3.value = s[p+'_api_key_3'];

            var savedModel = s[p+'_model'];
            if (savedModel && savedModel.trim()) {
                var rt = document.getElementById(p+'TestResults');
                var bb = document.getElementById(p+'BestBox');
                var sl = document.getElementById(p+'SelectedModel');
                var ml = document.getElementById(p+'ModelList');
                if (rt) rt.style.display = 'block';
                if (bb) bb.style.display = 'block';
                if (sl) sl.innerHTML = '<option value="'+savedModel+'" selected>'+savedModel+' ⭐ (Saved)</option>';
                if (ml) ml.innerHTML = '<p style="color:rgba(255,255,255,0.4);font-size:12px">Saved: <strong style="color:#fff">'+savedModel+'</strong></p>';
            }
        });

        if (s['default_language'])    document.getElementById('defaultLang').value = s['default_language'];
        if (s['batch_size'])          document.getElementById('batchSize').value   = s['batch_size'];
        if (s['image_search_engine']) document.getElementById('imgEngine').value   = s['image_search_engine'];

        console.log('[LOAD] Settings loaded OK. Active: '+activeAI);
    } catch(e) {
        console.error('[LOAD] Error:', e);
    }
}

// ── Select AI Provider ───────────────────────────
function selectAI(type, upd) {
    activeAI = type;
    PROVIDERS.forEach(function(p) {
        var cap = p.charAt(0).toUpperCase() + p.slice(1);
        var btn = document.getElementById('btn' + cap);
        var sec = document.getElementById(p + 'Section');
        if (btn) btn.className = 'provider-btn' + (p === type ? ' active' : '');
        if (sec) sec.style.display = p === type ? 'block' : 'none';
    });
}

// ── Test Models ──────────────────────────────────
async function testModels(provider) {
    var keyEl = document.getElementById(provider + 'Key');
    if (!keyEl || !keyEl.value.trim()) { toast('❌ Pehle API Key 1 daalo!', true); return; }

    var cap     = provider.charAt(0).toUpperCase() + provider.slice(1);
    var btn     = document.getElementById('test' + cap + 'Btn');
    var results = document.getElementById(provider + 'TestResults');
    var list    = document.getElementById(provider + 'ModelList');
    var bestBox = document.getElementById(provider + 'BestBox');
    var selEl   = document.getElementById(provider + 'SelectedModel');

    btn.disabled    = true;
    btn.textContent = '⏳ Testing...';
    results.style.display = 'block';
    bestBox.style.display = 'none';
    list.innerHTML = '<p style="color:rgba(255,255,255,0.4);font-size:13px;padding:8px 0">🔄 Testing all models...</p>';

    try {
        var r       = await fetch('api/ai/test-models.php', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ api_key: keyEl.value.trim(), provider: provider })
        });
        var rawText = await r.text(); // ✅ pehle text
        console.log('[TEST] raw:', rawText.substring(0,300));

        var data;
        try { data = JSON.parse(rawText); }
        catch(e) {
            toast('❌ Backend nahi mila — "api/ai/test-models.php" deploy karo apne server pe!', true);
            console.error('Not JSON:', rawText);
            btn.disabled=false; btn.textContent='🔍 Test Keys & Auto-Select Best Model'; return;
        }

        if (!data.results) {
            toast('❌ ' + (data.message || 'Test failed — server error'), true);
            btn.disabled=false; btn.textContent='🔍 Test Keys & Auto-Select Best Model'; return;
        }

        list.innerHTML = data.results.map(function(r) {
            var color = r.working ? '#4ade80' : r.status===429 ? '#fbbf24' : r.status===402 ? '#fbbf24' : 'rgba(255,255,255,0.3)';
            var bg    = r.working ? 'rgba(34,197,94,0.05)' : (r.status===429||r.status===402) ? 'rgba(251,191,36,0.05)' : 'transparent';
            return '<div class="model-row" style="background:'+bg+'"><span style="color:#fff;font-size:13px;font-family:monospace">'+r.model+'</span><span style="color:'+color+';font-size:12px;font-weight:600">'+r.message+'</span></div>';
        }).join('');

        var working = data.results.filter(function(r) { return r.working; });
        if (working.length > 0) {
            bestBox.style.display = 'block';
            selEl.innerHTML = working.map(function(r) {
                return '<option value="'+r.model+'"'+(r.model===data.best_model?' selected':'')+'>'+r.model+(r.model===data.best_model?' ⭐ (Best)':'')+' ('+r.latency_ms+'ms)</option>';
            }).join('');
            toast('✅ '+working.length+' working model(s) mile! Ek select karo phir Save karo.');
        } else if (data.hint === 'no_balance') {
            // The KEY is valid — the account just has no credit/balance
            bestBox.style.display = 'block';
            selEl.innerHTML = data.results.map(function(r) {
                return '<option value="'+r.model+'">'+r.model+'</option>';
            }).join('');
            toast('✅ API Key SAHI hai! Lekin account me balance/credit nahi hai. platform.deepseek.com → Top up me thode funds daalo (₹100 = bahut courses). Phir model select karke Save karo.', true);
        } else if (data.hint === 'bad_key') {
            toast('❌ API Key galat hai — dobara check karke sahi key daalo.', true);
        } else if (data.hint === 'connection') {
            toast('❌ Server connect nahi ho paya — thodi der baad try karo.', true);
        } else {
            var firstMsg = (data.results[0] && data.results[0].message) ? data.results[0].message : 'unknown';
            toast('⚠️ Koi working model nahi mila. Reason: ' + firstMsg, true);
        }
    } catch(e) {
        toast('❌ Test error: '+e.message, true);
        console.error(e);
    }

    btn.disabled    = false;
    btn.textContent = '🔍 Test Keys & Auto-Select Best Model';
}

// ── Save Settings ────────────────────────────────
async function saveSettings() {
    var payload = { active_ai_provider: activeAI };

    PROVIDERS.forEach(function(p) {
        var k1 = document.getElementById(p+'Key');
        var k2 = document.getElementById(p+'Key2');
        var k3 = document.getElementById(p+'Key3');
        if (k1) payload[p+'_api_key']   = k1.value.trim();
        if (k2) payload[p+'_api_key_2'] = k2.value.trim();
        if (k3) payload[p+'_api_key_3'] = k3.value.trim();

        // ✅ Selected model dropdown se lo
        var sel = document.getElementById(p+'SelectedModel');
        if (sel && sel.value && sel.value.trim()) {
            payload[p+'_model'] = sel.value.trim();
            console.log('[SAVE] '+p+'_model = '+sel.value.trim());
        }
    });

    payload['default_language']    = document.getElementById('defaultLang').value;
    payload['batch_size']          = document.getElementById('batchSize').value;
    payload['image_search_engine'] = document.getElementById('imgEngine').value;

    console.log('[SAVE] Full payload:', JSON.stringify(payload, null, 2));
    document.getElementById('saveStatus').textContent = 'Saving...';

    try {
        var r = await fetch('api/ai/settings.php?action=save', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify(payload)
        });

        // ✅ FIXED — pehle raw text, phir JSON parse
        var rawText = await r.text();
        console.log('[SAVE] Raw server response:', rawText);

        // PHP execute nahi hua?
        if (rawText.indexOf('<?php') !== -1 || rawText.indexOf('Fatal error') !== -1 || rawText.indexOf('Parse error') !== -1) {
            toast('❌ PHP server error! Console dekho.', true);
            document.getElementById('saveStatus').textContent = 'PHP error!';
            console.error('[SAVE] PHP not executing:', rawText);
            return;
        }

        // JSON parse
        var data;
        try {
            data = JSON.parse(rawText);
        } catch(parseErr) {
            toast('❌ Response JSON nahi hai: '+rawText.substring(0,80), true);
            document.getElementById('saveStatus').textContent = 'Parse error!';
            console.error('[SAVE] JSON parse failed. Raw:', rawText);
            return;
        }

        if (data.success) {
            // DB se verified values confirm karo
            var prov  = activeAI;
            var model = 'not set';
            if (data.verified) {
                prov  = data.verified['active_ai_provider'] || activeAI;
                model = data.verified[prov+'_model'] || '⚠️ not saved';
            }
            toast('✅ Saved! Provider: '+prov.toUpperCase()+' | Model: '+model);
            document.getElementById('saveStatus').textContent = 'Last saved: '+new Date().toLocaleTimeString();
            console.log('[SAVE] Verified DB:', data.verified);
        } else {
            toast('❌ Save failed: '+(data.message||'unknown'), true);
            document.getElementById('saveStatus').textContent = 'Save failed!';
        }
    } catch(e) {
        toast('❌ Network error: '+e.message, true);
        document.getElementById('saveStatus').textContent = 'Network error!';
        console.error('[SAVE] Fetch error:', e);
    }
}

// ── Helpers ──────────────────────────────────────
function tv(id) {
    var el = document.getElementById(id);
    el.type = el.type === 'password' ? 'text' : 'password';
}

function toast(msg, err) {
    var t = document.getElementById('toast');
    t.textContent      = msg;
    t.style.background = err ? '#ef4444' : '#22c55e';
    t.style.color      = '#fff';
    t.style.display    = 'block';
    clearTimeout(window._toastTimer);
    window._toastTimer = setTimeout(function() { t.style.display = 'none'; }, 5000);
}

// ── Init ─────────────────────────────────────────
loadSettings();
</script>
</body>
</html>