<?php

require_once 'includes/functions.php';
startSession();

if (!empty($_SESSION['user_id'])) {
    redirect('dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PersonaTrack – Organize Your Life!</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <style>
    body {
      min-height: 100vh;
      background: linear-gradient(135deg, #0d47a1 0%, #1565c0 40%, #1976d2 70%, #42a5f5 100%);
      overflow-x: hidden;
    }
    .bg-circles { position:fixed; inset:0; pointer-events:none; z-index:0; }
    .bg-circles span { position:absolute; border-radius:50%; background:rgba(255,255,255,0.06); animation:floatCircle 8s ease-in-out infinite; }
    .bg-circles span:nth-child(1){width:300px;height:300px;top:-80px;left:-60px;animation-delay:0s;}
    .bg-circles span:nth-child(2){width:200px;height:200px;top:40%;right:-50px;animation-delay:2s;}
    .bg-circles span:nth-child(3){width:150px;height:150px;bottom:10%;left:20%;animation-delay:4s;}
    .bg-circles span:nth-child(4){width:250px;height:250px;bottom:-60px;right:20%;animation-delay:1s;}
    @keyframes floatCircle{0%,100%{transform:translateY(0) scale(1);}50%{transform:translateY(-24px) scale(1.05);}}

    .landing-nav{position:relative;z-index:10;display:flex;align-items:center;justify-content:space-between;padding:22px 48px;}
    .landing-nav .brand{display:flex;align-items:center;gap:10px;font-family:'Poppins',sans-serif;font-weight:800;font-size:1.35rem;color:white;}
    .nav-pill{padding:8px 22px;border-radius:30px;font-size:0.88rem;font-weight:700;cursor:pointer;transition:.22s ease;border:none;}
    .nav-pill.outline{background:rgba(255,255,255,.15);color:white;border:1.5px solid rgba(255,255,255,.4);}
    .nav-pill.filled{background:white;color:#0d47a1;}

    .hero{position:relative;z-index:5;display:flex;align-items:center;justify-content:space-between;padding:48px 48px 0;gap:40px;flex-wrap:wrap;}
    .hero-text{flex:1;min-width:280px;}
    .hero-text h1{font-family:'Poppins',sans-serif;font-size:clamp(2rem,4vw,3.2rem);font-weight:800;color:white;line-height:1.18;margin-bottom:18px;}
    .hero-text h1 span{color:#bbdefb;}
    .hero-text p{color:rgba(255,255,255,.82);font-size:1.05rem;line-height:1.7;margin-bottom:30px;max-width:480px;}
    .hero-cta{display:flex;gap:14px;flex-wrap:wrap;}
    .btn-white{background:white;color:#0d47a1;font-weight:800;padding:13px 28px;border-radius:30px;border:none;font-size:.95rem;cursor:pointer;transition:.22s;}
    .btn-white:hover{transform:translateY(-2px);box-shadow:0 10px 30px rgba(0,0,0,.25);}
    .btn-ghost{background:rgba(255,255,255,.15);color:white;font-weight:700;padding:13px 28px;border-radius:30px;border:1.5px solid rgba(255,255,255,.4);font-size:.95rem;cursor:pointer;}
    .feature-pills{display:flex;flex-wrap:wrap;gap:10px;margin-top:28px;}
    .feature-pill{display:flex;align-items:center;gap:7px;background:rgba(255,255,255,.12);color:white;padding:8px 16px;border-radius:20px;font-size:.82rem;font-weight:600;}

    .auth-card{background:white;border-radius:24px;padding:36px 34px;width:100%;max-width:400px;box-shadow:0 24px 80px rgba(0,0,0,.25);flex-shrink:0;}
    .auth-tabs{display:flex;background:#e3f2fd;border-radius:30px;padding:4px;margin-bottom:28px;}
    .auth-tab{flex:1;text-align:center;padding:9px;border-radius:25px;font-size:.9rem;font-weight:700;cursor:pointer;border:none;background:transparent;color:#78909c;transition:.22s;}
    .auth-tab.active{background:#1976d2;color:white;box-shadow:0 3px 10px rgba(25,118,210,.35);}
    .auth-form{display:none;}
    .auth-form.active{display:block;}
    .auth-form h2{font-family:'Poppins',sans-serif;font-size:1.4rem;font-weight:700;color:#0d47a1;margin-bottom:6px;}
    .auth-form .subtitle{font-size:.85rem;color:#78909c;margin-bottom:24px;}
    .input-group-custom{position:relative;margin-bottom:16px;}
    .input-icon{position:absolute;left:14px;top:50%;transform:translateY(-50%);font-size:1rem;color:#42a5f5;pointer-events:none;}
    .input-group-custom input{width:100%;padding:11px 14px 11px 40px;border:1.8px solid #e0e9f4;border-radius:11px;font-size:.9rem;color:#0d1b2a;outline:none;transition:.22s;background:#fafcff;}
    .input-group-custom input:focus{border-color:#42a5f5;box-shadow:0 0 0 3px rgba(66,165,245,.15);}
    .submit-btn{width:100%;padding:13px;background:linear-gradient(135deg,#1976d2,#0d47a1);color:white;border:none;border-radius:11px;font-size:1rem;font-weight:700;cursor:pointer;transition:.22s;}
    .submit-btn:hover{transform:translateY(-1px);}
    .submit-btn:disabled{opacity:.7;cursor:not-allowed;}
    .auth-footer{text-align:center;margin-top:18px;font-size:.84rem;color:#78909c;}
    .auth-footer a{color:#1976d2;font-weight:700;cursor:pointer;}
    .stats-strip{position:relative;z-index:5;display:flex;justify-content:center;gap:40px;padding:32px 48px;flex-wrap:wrap;}
    .stat-pill{text-align:center;color:white;}
    .stat-pill .num{font-family:'Poppins',sans-serif;font-size:1.8rem;font-weight:800;}
    .stat-pill .lbl{font-size:.82rem;opacity:.75;}
    #auth-error{display:none;background:#ffebee;color:#c62828;padding:10px 14px;border-radius:8px;font-size:.85rem;font-weight:600;margin-bottom:12px;}
  </style>
</head>
<body>
<div class="bg-circles"><span></span><span></span><span></span><span></span></div>

<nav class="landing-nav">
  <div class="brand"><span>🎓</span> PersonaTrack</div>
  <div style="display:flex;gap:10px;">
    <button class="nav-pill outline" onclick="switchTab('login')">Login</button>
    <button class="nav-pill filled" onclick="switchTab('register')">Get Started</button>
  </div>
</nav>

<div class="hero">
  <div class="hero-text">
    <div class="tagline" style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.15);color:white;padding:6px 16px;border-radius:30px;font-size:.82rem;font-weight:700;margin-bottom:20px;">✨ Built for YOU!</div>
    <h1>Balance Your <span>Life</span> Like a Pro</h1>
    <p>Manage your academics, club activities, personal goals, expenses, and time — all in one beautiful place.</p>
    <div class="hero-cta">
      <button class="btn-white" onclick="switchTab('register')">🚀 Start for Free</button>
      <button class="btn-ghost" onclick="switchTab('login')">Already have an account</button>
    </div>
    <div class="feature-pills">
      <div class="feature-pill">✅ To-Do Lists</div>
      <div class="feature-pill">💰 Expense Tracker</div>
      <div class="feature-pill">🎯 Goal Tracker</div>
      <div class="feature-pill">📝 Notes</div>
      <div class="feature-pill">🔔 Reminders</div>
      <div class="feature-pill">📊 Analytics</div>
    </div>
  </div>

  <div class="auth-card">
    <div class="auth-tabs">
      <button class="auth-tab active" id="tab-login" onclick="switchTab('login')">Login</button>
      <button class="auth-tab" id="tab-register" onclick="switchTab('register')">Register</button>
    </div>

    <div id="auth-error"></div>

    <div class="auth-form active" id="form-login">
      <h2>Welcome back! 👋</h2>
      <p class="subtitle">Enter your details to continue</p>
      <div class="input-group-custom">
        <span class="input-icon">📧</span>
        <input type="email" id="login-email" placeholder="Your email address" required>
      </div>
      <div class="input-group-custom">
        <span class="input-icon">🔒</span>
        <input type="password" id="login-password" placeholder="Your password" required>
      </div>
      <button class="submit-btn" id="login-btn" onclick="handleLogin()">Login →</button>
      <div class="auth-footer">Don't have an account? <a onclick="switchTab('register')">Register here</a></div>
    </div>

    <div class="auth-form" id="form-register">
      <h2>Create Account 🎓</h2>
      <p class="subtitle">Join thousands of students today</p>
      <div class="input-group-custom">
        <span class="input-icon">👤</span>
        <input type="text" id="reg-name" placeholder="Your full name" required>
      </div>
      <div class="input-group-custom">
        <span class="input-icon">📧</span>
        <input type="email" id="reg-email" placeholder="University email" required>
      </div>
      <div class="input-group-custom">
        <span class="input-icon">🏫</span>
        <input type="text" id="reg-university" placeholder="University / Faculty" required>
      </div>
      <div class="input-group-custom">
        <span class="input-icon">🔒</span>
        <input type="password" id="reg-password" placeholder="Create a password (min 6 chars)" required>
      </div>
      <button class="submit-btn" id="register-btn" onclick="handleRegister()">Let's Go! 🚀</button>
      <div class="auth-footer">Already have an account? <a onclick="switchTab('login')">Login here</a></div>
    </div>
  </div>
</div>

<div class="stats-strip">
  <div class="stat-pill"><div class="num">📚 7</div><div class="lbl">Modules in One App</div></div>
  <div class="stat-pill"><div class="num">⚡ Free</div><div class="lbl">Always Free to Use</div></div>
  <div class="stat-pill"><div class="num">🎓 100%</div><div class="lbl">Student Focused</div></div>
  <div class="stat-pill"><div class="num">📊 Smart</div><div class="lbl">Built-in Analytics</div></div>
</div>

<div id="toast-container" style="position:fixed;top:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:10px;"></div>

<script>
  function switchTab(tab) {
    document.getElementById('form-login').classList.toggle('active', tab === 'login');
    document.getElementById('form-register').classList.toggle('active', tab === 'register');
    document.getElementById('tab-login').classList.toggle('active', tab === 'login');
    document.getElementById('tab-register').classList.toggle('active', tab === 'register');
    hideError();
  }

  function showError(msg) {
    const el = document.getElementById('auth-error');
    el.textContent = '⚠️ ' + msg;
    el.style.display = 'block';
  }
  function hideError() {
    document.getElementById('auth-error').style.display = 'none';
  }

  async function handleLogin() {
    hideError();
    const email    = document.getElementById('login-email').value.trim();
    const password = document.getElementById('login-password').value.trim();
    if (!email || !password) { showError('Please fill in all fields.'); return; }

    const btn = document.getElementById('login-btn');
    btn.disabled = true; btn.textContent = '⏳ Logging in...';

    const formData = new FormData();
    formData.append('email', email);
    formData.append('password', password);

    try {
      const res  = await fetch('auth/login.php', { method: 'POST', body: formData });
      const data = await res.json();
      if (data.success) {
        showToast('✅ ' + data.message, 'success');
        setTimeout(() => window.location.href = 'dashboard.php', 1000);
      } else {
        showError(data.message);
        btn.disabled = false; btn.textContent = 'Login →';
      }
    } catch {
      showError('Connection error. Make sure XAMPP is running.');
      btn.disabled = false; btn.textContent = 'Login →';
    }
  }

  async function handleRegister() {
    hideError();
    const name       = document.getElementById('reg-name').value.trim();
    const email      = document.getElementById('reg-email').value.trim();
    const university = document.getElementById('reg-university').value.trim();
    const password   = document.getElementById('reg-password').value.trim();
    if (!name || !email || !university || !password) { showError('Please fill in all fields.'); return; }
    if (password.length < 6) { showError('Password must be at least 6 characters.'); return; }

    const btn = document.getElementById('register-btn');
    btn.disabled = true; btn.textContent = '⏳ Creating account...';

    const formData = new FormData();
    formData.append('name', name);
    formData.append('email', email);
    formData.append('university', university);
    formData.append('password', password);

    try {
      const res  = await fetch('auth/register.php', { method: 'POST', body: formData });
      const data = await res.json();
      if (data.success) {
        showToast('🎉 ' + data.message, 'success');
        setTimeout(() => window.location.href = 'dashboard.php', 1200);
      } else {
        showError(data.message);
        btn.disabled = false; btn.textContent = "Let's Go! 🚀";
      }
    } catch {
      showError('Connection error. Make sure XAMPP is running.');
      btn.disabled = false; btn.textContent = "Let's Go! 🚀";
    }
  }

  function showToast(msg, type = 'info') {
    const c = document.getElementById('toast-container');
    const t = document.createElement('div');
    t.style.cssText = `background:${type === 'success' ? '#00897b' : '#0d47a1'};color:white;padding:12px 20px;border-radius:10px;font-size:.87rem;font-weight:600;box-shadow:0 8px 32px rgba(13,71,161,.18);`;
    t.textContent = msg;
    c.appendChild(t);
    setTimeout(() => t.remove(), 3000);
  }

  document.addEventListener('keydown', (e) => {
    if (e.key !== 'Enter') return;
    if (document.getElementById('form-login').classList.contains('active')) handleLogin();
    else handleRegister();
  });
</script>
</body>
</html>
