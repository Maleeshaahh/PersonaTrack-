<?php

require_once 'includes/functions.php';
startSession();
requireLogin();

$userName = htmlspecialchars($_SESSION['user_name'] ?? 'Student');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PersonaTrack – Goals</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    .goal-card {
      background:white; border-radius:14px; padding:20px;
      box-shadow:0 4px 20px rgba(13,71,161,.1); transition:.22s;
      border-top:4px solid #1976d2; position:relative;
    }
    .goal-card:hover { transform:translateY(-3px); box-shadow:0 8px 32px rgba(13,71,161,.18); }
    .goal-card.achieved { border-top-color:#26a69a; }
    .goal-card.club     { border-top-color:#7b1fa2; }
    .goal-card.personal { border-top-color:#e65100; }
    .goal-icon  { font-size:1.8rem; margin-bottom:8px; }
    .goal-title { font-family:'Poppins',sans-serif; font-weight:700; font-size:1rem; color:#0d47a1; }
    .goal-desc  { font-size:.8rem; color:#78909c; margin:4px 0 12px; }
    .achieve-badge { position:absolute; top:14px; right:14px; background:#e8f5e9; color:#388e3c; border-radius:20px; padding:3px 10px; font-size:.72rem; font-weight:700; }
    .progress-slider { -webkit-appearance:none; width:100%; height:6px; background:#bbdefb; border-radius:30px; outline:none; cursor:pointer; margin-top:8px; }
    .progress-slider::-webkit-slider-thumb { -webkit-appearance:none; width:16px; height:16px; border-radius:50%; background:#1976d2; cursor:pointer; }
  </style>
</head>
<body>
<div class="app-layout">

  <aside class="sidebar">
    <div class="sidebar-logo"><div class="logo-icon">🎓</div><span>PersonaTrack</span></div>
    <div class="sidebar-user">
      <div class="avatar"><?= strtoupper(substr($userName,0,2)) ?></div>
      <div class="user-info"><div class="name"><?= $userName ?></div><div class="role">Member</div></div>
    </div>
    <nav class="sidebar-nav">
      <div class="nav-section-label">Main Menu</div>
      <a href="dashboard.php" class="nav-item"><span class="nav-icon">🏠</span><span>Dashboard</span></a>
      <a href="todo.php"      class="nav-item"><span class="nav-icon">✅</span><span>To-Do List</span></a>
      <a href="expenses.php"  class="nav-item"><span class="nav-icon">💰</span><span>Expenses</span></a>
      <a href="goals.php"     class="nav-item active"><span class="nav-icon">🎯</span><span>Goals</span></a>
      <a href="notes.php"     class="nav-item"><span class="nav-icon">📝</span><span>Notes</span></a>
      <a href="reminders.php" class="nav-item"><span class="nav-icon">🔔</span><span>Reminders</span></a>
      <a href="analytics.php" class="nav-item"><span class="nav-icon">📊</span><span>Analytics</span></a>
      <a href="contact.php"   class="nav-item"><span class="nav-icon">📩</span><span>Contact</span></a>
      <div class="nav-section-label">Account</div>
      <a href="profile.php"   class="nav-item"><span class="nav-icon">👤</span><span>My Profile</span></a>
    </nav>
    <div class="sidebar-bottom"><a href="auth/logout.php" class="logout-btn"><span>🚪</span><span>Logout</span></a></div>
  </aside>

  <div class="main-content">
    <header class="topbar">
      <div class="topbar-title">🎯 Goals Tracker</div>
      <div class="topbar-search"><span>🔍</span><input type="text" id="search-input" placeholder="Search goals..." oninput="renderGoals()"></div>
      <div class="topbar-actions">
        <button class="icon-btn" onclick="location.href='reminders.php'">🔔</button>
        <div class="topbar-date" id="today-date">📅</div>
      </div>
    </header>

    <main class="page-body">
      <div class="page-header">
        <div><h1>Goals Tracker 🎯</h1><p>Set, track, and achieve your dreams</p></div>
        <button class="btn btn-primary" onclick="openModal()">➕ Add Goal</button>
      </div>

      <!-- Stats -->
      <div class="grid-4" style="margin-bottom:22px;">
        <div class="stat-card"><div class="stat-icon" style="background:#e3f2fd;">🎯</div><div class="stat-info"><div class="label">Total Goals</div><div class="value" id="st-total">–</div></div></div>
        <div class="stat-card"><div class="stat-icon" style="background:#e0f2f1;">🏆</div><div class="stat-info"><div class="label">Achieved</div><div class="value" id="st-achieved">–</div></div></div>
        <div class="stat-card"><div class="stat-icon" style="background:#f3e5f5;">📚</div><div class="stat-info"><div class="label">Academic</div><div class="value" id="st-academic">–</div></div></div>
        <div class="stat-card"><div class="stat-icon" style="background:#e8f5e9;">🏅</div><div class="stat-info"><div class="label">Club Goals</div><div class="value" id="st-club">–</div></div></div>
      </div>

      <div class="grid-2" style="margin-bottom:22px;">
        <div class="card"><div class="card-title">📊 Goals by Category</div><canvas id="goalChart" height="180"></canvas></div>
        <div class="card"><div class="card-title">📈 Overall Progress</div><canvas id="progressChart" height="180"></canvas></div>
      </div>

      <div class="tab-bar" style="margin-bottom:18px;">
        <button class="tab-btn active" onclick="setTab('all',this)">All Goals</button>
        <button class="tab-btn" onclick="setTab('Academic',this)">📚 Academic</button>
        <button class="tab-btn" onclick="setTab('Club',this)">🏅 Club</button>
        <button class="tab-btn" onclick="setTab('Personal',this)">👤 Personal</button>
        <button class="tab-btn" onclick="setTab('achieved',this)">🏆 Achieved</button>
      </div>

      <div class="grid-3" id="goals-grid"></div>
    </main>
    <footer class="footer"><span>PersonaTrack</span> © 2026 — Made with ❤️ for YOU!</footer>
  </div>
</div>

<div class="modal-overlay" id="goal-modal">
  <div class="modal-box">
    <div class="modal-header">
      <h3>🎯 Add New Goal</h3>
      <button class="btn-icon" onclick="closeModal()">✕</button>
    </div>
    <div class="form-group"><label class="form-label">Goal Title *</label><input type="text" class="form-control" id="g-title" placeholder="e.g. Get a 3.8 GPA"></div>
    <div class="form-group"><label class="form-label">Description</label><textarea class="form-control" id="g-desc" rows="2" placeholder="What does achieving this mean to you?"></textarea></div>
    <div class="grid-2">
      <div class="form-group"><label class="form-label">Category</label><select class="form-control" id="g-cat"><option>Academic</option><option>Club</option><option>Personal</option><option>Other</option></select></div>
      <div class="form-group"><label class="form-label">Target Date</label><input type="date" class="form-control" id="g-date"></div>
    </div>
    <div class="form-group">
      <label class="form-label">Initial Progress: <span id="prog-val">0</span>%</label>
      <input type="range" class="progress-slider" id="g-progress" min="0" max="100" value="0" oninput="document.getElementById('prog-val').textContent=this.value">
    </div>
    <div style="display:flex;gap:10px;justify-content:flex-end;">
      <button class="btn btn-outline" onclick="closeModal()">Cancel</button>
      <button class="btn btn-primary" onclick="saveGoal()">🎯 Save Goal</button>
    </div>
  </div>
</div>

<div id="toast-container" class="toast-container"></div>

<script>
  document.getElementById('today-date').textContent = '📅 ' + new Date().toLocaleDateString('en-US',{weekday:'short',month:'short',day:'numeric'});

  let goals      = [];
  let currentTab = 'all';
  const catIcons  = { Academic:'📚', Club:'🏅', Personal:'👤', Other:'🌟' };
  const catColors = { Academic:'', Club:'club', Personal:'personal', Other:'' };

  async function loadGoals() {
    const res  = await fetch('api/goals.php');
    const data = await res.json();
    goals = data.data || [];
    renderGoals();
  }

  function setTab(tab, btn) {
    currentTab = tab;
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active'); renderGoals();
  }

  function renderGoals() {
    const search = document.getElementById('search-input').value.toLowerCase();

    document.getElementById('st-total').textContent    = goals.length;
    document.getElementById('st-achieved').textContent = goals.filter(g => g.is_achieved==1 || g.progress>=100).length;
    document.getElementById('st-academic').textContent = goals.filter(g => g.category==='Academic').length;
    document.getElementById('st-club').textContent     = goals.filter(g => g.category==='Club').length;

    let filtered = goals.filter(g => g.title.toLowerCase().includes(search));
    if (currentTab === 'achieved') filtered = filtered.filter(g => g.is_achieved==1 || g.progress>=100);
    else if (currentTab !== 'all') filtered = filtered.filter(g => g.category === currentTab);

    if (filtered.length === 0) {
      document.getElementById('goals-grid').innerHTML = `<div class="empty-state" style="grid-column:1/-1"><div class="empty-icon">🎯</div><p>No goals yet. Set your first goal!</p></div>`;
    } else {
      document.getElementById('goals-grid').innerHTML = filtered.map(g => {
        const done = g.is_achieved==1 || g.progress>=100;
        const pct  = parseInt(g.progress)||0;
        return `
          <div class="goal-card ${done?'achieved':catColors[g.category]||''}">
            ${done ? '<div class="achieve-badge">🏆 Achieved!</div>' : ''}
            <div class="goal-icon">${catIcons[g.category]||'🌟'}</div>
            <div class="goal-title">${g.title}</div>
            <div class="goal-desc">${g.description||''}</div>
            <div style="display:flex;justify-content:space-between;margin-bottom:7px;">
              <span style="font-size:.8rem;font-weight:600;color:#37474f;">Progress</span>
              <span style="font-size:.85rem;font-weight:700;color:#1976d2;">${pct}%</span>
            </div>
            <div class="progress-wrap"><div class="progress-fill green" style="width:${pct}%"></div></div>
            <input type="range" class="progress-slider" value="${pct}" min="0" max="100"
                   oninput="updateProgress(${g.id},this.value)">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-top:14px;font-size:.78rem;color:#78909c;">
              <span>${g.target_date ? '📅 '+g.target_date : ''}</span>
              <div style="display:flex;gap:6px;">
                <span style="background:#e3f2fd;color:#1976d2;padding:3px 11px;border-radius:20px;font-size:.75rem;font-weight:700;">${g.category||'Other'}</span>
                <button class="btn-icon" onclick="deleteGoal(${g.id})">🗑️</button>
              </div>
            </div>
          </div>`;
      }).join('');
    }
    updateCharts();
  }

  async function updateProgress(id, val) {
    await fetch('api/goals.php', {
      method: 'PUT',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({ id, progress: parseInt(val) })
    });
    await loadGoals();
  }

  async function deleteGoal(id) {
    if (!confirm('Delete this goal?')) return;
    await fetch('api/goals.php',{ method:'DELETE', headers:{'Content-Type':'application/json'}, body:JSON.stringify({id}) });
    showToast('🗑️ Goal deleted!'); await loadGoals();
  }

  async function saveGoal() {
    const title = document.getElementById('g-title').value.trim();
    if (!title) { showToast('⚠️ Please enter a goal title!','error'); return; }

    const res = await fetch('api/goals.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({
        title,
        description: document.getElementById('g-desc').value,
        category:    document.getElementById('g-cat').value,
        target_date: document.getElementById('g-date').value,
        progress:    parseInt(document.getElementById('g-progress').value)||0,
      })
    });
    const data = await res.json();
    if (data.success) { showToast('🎯 Goal added!','success'); closeModal(); await loadGoals(); }
  }

  let goalChart, progressChart;
  function updateCharts() {
    const cats      = ['Academic','Club','Personal','Other'];
    const catCounts = cats.map(c => goals.filter(g => g.category===c).length);

    if (goalChart) goalChart.destroy();
    goalChart = new Chart(document.getElementById('goalChart'),{
      type:'bar',
      data:{ labels:cats, datasets:[{ label:'Goals', data:catCounts, backgroundColor:['#1976d2','#7b1fa2','#e65100','#00838f'], borderRadius:8, borderWidth:0 }]},
      options:{ responsive:true, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true,grid:{color:'#f0f5fb'}},x:{grid:{display:false}}} }
    });

    const done     = goals.filter(g => g.is_achieved==1||g.progress>=100).length;
    const inprog   = goals.filter(g => !g.is_achieved && g.progress>0 && g.progress<100).length;
    const notStart = goals.filter(g => g.progress==0).length;

    if (progressChart) progressChart.destroy();
    progressChart = new Chart(document.getElementById('progressChart'),{
      type:'doughnut',
      data:{ labels:['Achieved','In Progress','Not Started'], datasets:[{ data:[done,inprog,notStart], backgroundColor:['#26a69a','#1976d2','#eceff1'], borderWidth:0 }]},
      options:{ responsive:true, plugins:{legend:{position:'bottom',labels:{font:{size:11},padding:10}}} }
    });
  }

  function openModal()  { document.getElementById('goal-modal').classList.add('open'); }
  function closeModal() {
    document.getElementById('goal-modal').classList.remove('open');
    ['g-title','g-desc'].forEach(id => document.getElementById(id).value='');
    document.getElementById('g-progress').value=0;
    document.getElementById('prog-val').textContent='0';
  }

  function showToast(msg,type='info') {
    const c=document.getElementById('toast-container');
    const t=document.createElement('div'); t.className='toast';
    t.style.background=type==='success'?'#00897b':type==='error'?'#c62828':'var(--sky-dark)';
    t.textContent=msg; c.appendChild(t); setTimeout(()=>t.remove(),3200);
  }

  loadGoals();
</script>
</body>
</html>
