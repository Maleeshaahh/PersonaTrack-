<?php

require_once 'includes/functions.php';
startSession();
requireLogin();

$userName  = htmlspecialchars($_SESSION['user_name']  ?? 'Student');
$userEmail = htmlspecialchars($_SESSION['user_email'] ?? '');
$initials  = strtoupper(substr($userName,0,2));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PersonaTrack – My Profile</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <style>
    .profile-hero {
      background:linear-gradient(135deg,#0d47a1 0%,#1976d2 100%);
      border-radius:14px; padding:32px; color:white;
      display:flex; align-items:center; gap:28px; margin-bottom:24px; position:relative; overflow:hidden;
    }
    .profile-hero::after { content:'🎓'; position:absolute; right:28px; font-size:6rem; opacity:.1; }
    .avatar-lg {
      width:90px; height:90px; border-radius:50%;
      background:rgba(255,255,255,.25); border:4px solid rgba(255,255,255,.5);
      display:flex; align-items:center; justify-content:center;
      font-size:2.4rem; font-weight:800; color:white; flex-shrink:0; font-family:'Poppins',sans-serif;
    }
    .profile-stats { display:flex; background:white; border-radius:14px; box-shadow:0 4px 20px rgba(13,71,161,.1); overflow:hidden; margin-bottom:24px; }
    .ps-item { flex:1; text-align:center; padding:18px 10px; border-right:1px solid #f0f5fb; }
    .ps-item:last-child { border-right:none; }
    .ps-val  { font-family:'Poppins',sans-serif; font-size:1.5rem; font-weight:800; color:#0d47a1; }
    .ps-lbl  { font-size:.77rem; color:#78909c; font-weight:600; }
    .info-section { background:white; border-radius:14px; padding:22px; box-shadow:0 4px 20px rgba(13,71,161,.1); margin-bottom:20px; }
    .info-row { display:flex; align-items:flex-start; gap:14px; padding:11px 0; border-bottom:1px solid #f0f5fb; }
    .info-row:last-child { border-bottom:none; }
    .ir-label { font-size:.77rem; color:#78909c; font-weight:600; }
    .ir-value { font-size:.9rem; color:#0d1b2a; font-weight:600; }
  </style>
</head>
<body>
<div class="app-layout">

  <aside class="sidebar">
    <div class="sidebar-logo"><div class="logo-icon">🎓</div><span>PersonaTrack</span></div>
    <div class="sidebar-user">
      <div class="avatar"><?= $initials ?></div>
      <div class="user-info"><div class="name"><?= $userName ?></div><div class="role">Member</div></div>
    </div>
    <nav class="sidebar-nav">
      <div class="nav-section-label">Main Menu</div>
      <a href="dashboard.php" class="nav-item"><span class="nav-icon">🏠</span><span>Dashboard</span></a>
      <a href="todo.php"      class="nav-item"><span class="nav-icon">✅</span><span>To-Do List</span></a>
      <a href="expenses.php"  class="nav-item"><span class="nav-icon">💰</span><span>Expenses</span></a>
      <a href="goals.php"     class="nav-item"><span class="nav-icon">🎯</span><span>Goals</span></a>
      <a href="notes.php"     class="nav-item"><span class="nav-icon">📝</span><span>Notes</span></a>
      <a href="reminders.php" class="nav-item"><span class="nav-icon">🔔</span><span>Reminders</span></a>
      <a href="analytics.php" class="nav-item"><span class="nav-icon">📊</span><span>Analytics</span></a>
      <a href="contact.php"   class="nav-item"><span class="nav-icon">📩</span><span>Contact</span></a>
      <div class="nav-section-label">Account</div>
      <a href="profile.php"   class="nav-item active"><span class="nav-icon">👤</span><span>My Profile</span></a>
    </nav>
    <div class="sidebar-bottom"><a href="auth/logout.php" class="logout-btn"><span>🚪</span><span>Logout</span></a></div>
  </aside>

  <div class="main-content">
    <header class="topbar">
      <div class="topbar-title">👤 My Profile</div>
      <div class="topbar-search"><span>🔍</span><input type="text" placeholder="Search..."></div>
      <div class="topbar-actions">
        <button class="icon-btn" onclick="location.href='reminders.php'">🔔</button>
        <div class="topbar-date" id="today-date">📅</div>
      </div>
    </header>

    <main class="page-body">
      <div class="page-header">
        <div><h1>My Profile 👤</h1><p>Your personal information and stats</p></div>
        <button class="btn btn-primary" onclick="openModal()">✏️ Edit Profile</button>
      </div>

      <div class="profile-hero">
        <div class="avatar-lg" id="hero-initials"><?= $initials ?></div>
        <div>
          <h2 style="font-family:'Poppins',sans-serif;font-size:1.5rem;font-weight:800;margin-bottom:4px;" id="hero-name"><?= $userName ?></h2>
          <div style="opacity:.85;font-size:.92rem;margin-bottom:12px;" id="hero-uni">University</div>
          <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <span style="background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.3);padding:4px 13px;border-radius:20px;font-size:.78rem;font-weight:700;">🎓 Undergraduate</span>
            <span id="hero-year" style="background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.3);padding:4px 13px;border-radius:20px;font-size:.78rem;font-weight:700;">Year 1</span>
          </div>
        </div>
      </div>

      <div class="profile-stats">
        <div class="ps-item"><div class="ps-val" id="ps-tasks">–</div><div class="ps-lbl">Tasks</div></div>
        <div class="ps-item"><div class="ps-val" id="ps-goals">–</div><div class="ps-lbl">Goals</div></div>
        <div class="ps-item"><div class="ps-val" id="ps-notes">–</div><div class="ps-lbl">Notes</div></div>
        <div class="ps-item"><div class="ps-val" id="ps-expenses">–</div><div class="ps-lbl">Transactions</div></div>
        <div class="ps-item"><div class="ps-val" id="ps-done">–</div><div class="ps-lbl">Completion</div></div>
      </div>

      <div class="grid-2">
    
        <div class="info-section">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
            <h3 style="font-family:'Poppins',sans-serif;font-weight:700;font-size:1rem;color:#0d47a1;">👤 Personal Information</h3>
          </div>
          <div class="info-row"><span style="font-size:1.1rem;">📛</span><div><div class="ir-label">Full Name</div><div class="ir-value" id="pi-name"><?= $userName ?></div></div></div>
          <div class="info-row"><span style="font-size:1.1rem;">📧</span><div><div class="ir-label">Email</div><div class="ir-value"><?= $userEmail ?></div></div></div>
          <div class="info-row"><span style="font-size:1.1rem;">🏫</span><div><div class="ir-label">University</div><div class="ir-value" id="pi-uni">–</div></div></div>
          <div class="info-row"><span style="font-size:1.1rem;">📚</span><div><div class="ir-label">Faculty / Department</div><div class="ir-value" id="pi-faculty">–</div></div></div>
          <div class="info-row"><span style="font-size:1.1rem;">📅</span><div><div class="ir-label">Academic Year</div><div class="ir-value" id="pi-year">–</div></div></div>
          <div class="info-row"><span style="font-size:1.1rem;">🎂</span><div><div class="ir-label">Date of Birth</div><div class="ir-value" id="pi-dob">–</div></div></div>
          <div class="info-row"><span style="font-size:1.1rem;">📱</span><div><div class="ir-label">Phone</div><div class="ir-value" id="pi-phone">–</div></div></div>
        </div>

        <div class="info-section">
          <h3 style="font-family:'Poppins',sans-serif;font-weight:700;font-size:1rem;color:#0d47a1;margin-bottom:18px;">⚙️ App Settings</h3>
          <div class="info-row">
            <span style="font-size:1.1rem;">🔔</span>
            <div style="flex:1;"><div class="ir-label">Reminder Notifications</div><div class="ir-value">Enabled</div></div>
            <input type="checkbox" checked style="cursor:pointer;">
          </div>
          <div class="info-row">
            <span style="font-size:1.1rem;">📊</span>
            <div style="flex:1;"><div class="ir-label">Weekly Analytics Report</div><div class="ir-value">Enabled</div></div>
            <input type="checkbox" checked style="cursor:pointer;">
          </div>
          <div class="info-row">
            <span style="font-size:1.1rem;">🎯</span>
            <div style="flex:1;"><div class="ir-label">Goal Deadline Reminders</div><div class="ir-value">Enabled</div></div>
            <input type="checkbox" checked style="cursor:pointer;">
          </div>
          <div style="margin-top:18px;">
            <a href="contact.php" class="btn btn-outline btn-sm" style="text-decoration:none;">📩 Contact Support</a>
          </div>
        </div>
      </div>
    </main>
    <footer class="footer"><span>PersonaTrack</span> © 2026 — Made with ❤️ for YOU!</footer>
  </div>
</div>

<div class="modal-overlay" id="profile-modal">
  <div class="modal-box" style="max-width:520px;">
    <div class="modal-header"><h3>✏️ Edit Profile</h3><button class="btn-icon" onclick="closeModal()">✕</button></div>
    <div class="grid-2">
      <div class="form-group"><label class="form-label">Full Name</label><input type="text" class="form-control" id="e-name"></div>
      <div class="form-group"><label class="form-label">Phone</label><input type="text" class="form-control" id="e-phone" placeholder="+94..."></div>
    </div>
    <div class="grid-2">
      <div class="form-group"><label class="form-label">University</label><input type="text" class="form-control" id="e-uni"></div>
      <div class="form-group"><label class="form-label">Faculty / Dept.</label><input type="text" class="form-control" id="e-faculty"></div>
    </div>
    <div class="grid-2">
      <div class="form-group"><label class="form-label">Academic Year</label><select class="form-control" id="e-year"><option>Year 1</option><option>Year 2</option><option>Year 3</option><option>Year 4</option></select></div>
      <div class="form-group"><label class="form-label">Date of Birth</label><input type="date" class="form-control" id="e-dob"></div>
    </div>
    <div style="display:flex;gap:10px;justify-content:flex-end;">
      <button class="btn btn-outline" onclick="closeModal()">Cancel</button>
      <button class="btn btn-primary" onclick="saveProfile()">💾 Save Profile</button>
    </div>
  </div>
</div>

<div id="toast-container" class="toast-container"></div>

<script>
  document.getElementById('today-date').textContent = '📅 ' + new Date().toLocaleDateString('en-US',{weekday:'short',month:'short',day:'numeric'});

  async function loadProfile() {
    const pRes = await fetch('api/profile.php');
    const p    = (await pRes.json()).data || {};

    document.getElementById('pi-uni').textContent     = p.university || '–';
    document.getElementById('pi-faculty').textContent = p.faculty    || '–';
    document.getElementById('pi-year').textContent    = p.academic_yr|| '–';
    document.getElementById('pi-dob').textContent     = p.dob        || '–';
    document.getElementById('pi-phone').textContent   = p.phone      || '–';
    document.getElementById('hero-uni').textContent   = (p.university||'University') + (p.faculty ? ' · '+p.faculty : '');
    document.getElementById('hero-year').textContent  = p.academic_yr || 'Year 1';

    const [tRes, gRes, nRes, eRes] = await Promise.all([
      fetch('api/todos.php'), fetch('api/goals.php'),
      fetch('api/notes.php'), fetch('api/expenses.php'),
    ]);
    const todos    = (await tRes.json()).data || [];
    const goals    = (await gRes.json()).data || [];
    const notes    = (await nRes.json()).data || [];
    const expenses = (await eRes.json()).data || [];

    document.getElementById('ps-tasks').textContent    = todos.length;
    document.getElementById('ps-goals').textContent    = goals.length;
    document.getElementById('ps-notes').textContent    = notes.length;
    document.getElementById('ps-expenses').textContent = expenses.length;
    const rate = todos.length > 0 ? Math.round(todos.filter(t=>t.is_done==1).length/todos.length*100) : 0;
    document.getElementById('ps-done').textContent = rate + '%';
  }

  async function saveProfile() {
    const res = await fetch('api/profile.php', {
      method: 'PUT',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({
        name:        document.getElementById('e-name').value.trim(),
        university:  document.getElementById('e-uni').value.trim(),
        faculty:     document.getElementById('e-faculty').value.trim(),
        academic_yr: document.getElementById('e-year').value,
        phone:       document.getElementById('e-phone').value.trim(),
        dob:         document.getElementById('e-dob').value,
      })
    });
    const data = await res.json();
    if (data.success) {
      showToast('✅ Profile updated!','success');
      closeModal();
      // Update hero name if changed
      const newName = document.getElementById('e-name').value.trim();
      if (newName) {
        document.getElementById('hero-name').textContent    = newName;
        document.getElementById('hero-initials').textContent= newName.split(' ').map(n=>n[0]).join('').toUpperCase().slice(0,2)||'S';
        document.getElementById('pi-name').textContent = newName;
      }
      loadProfile();
    }
  }

  function openModal() {
   
    document.getElementById('e-name').value    = document.getElementById('hero-name').textContent;
    document.getElementById('e-uni').value     = document.getElementById('pi-uni').textContent  !== '–' ? document.getElementById('pi-uni').textContent   : '';
    document.getElementById('e-faculty').value = document.getElementById('pi-faculty').textContent !== '–' ? document.getElementById('pi-faculty').textContent : '';
    document.getElementById('e-phone').value   = document.getElementById('pi-phone').textContent !== '–' ? document.getElementById('pi-phone').textContent   : '';
    document.getElementById('e-dob').value     = document.getElementById('pi-dob').textContent  !== '–' ? document.getElementById('pi-dob').textContent    : '';
    document.getElementById('profile-modal').classList.add('open');
  }
  function closeModal() { document.getElementById('profile-modal').classList.remove('open'); }

  function showToast(msg,type='info') {
    const c=document.getElementById('toast-container'); const t=document.createElement('div'); t.className='toast';
    t.style.background=type==='success'?'#00897b':type==='error'?'#c62828':'var(--sky-dark)';
    t.textContent=msg; c.appendChild(t); setTimeout(()=>t.remove(),3200);
  }

  loadProfile();
</script>
</body>
</html>
