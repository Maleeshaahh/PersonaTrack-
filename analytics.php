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
  <title>PersonaTrack – Analytics</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    .insight-card { background:white; border-radius:14px; padding:22px; box-shadow:0 4px 20px rgba(13,71,161,.1); text-align:center; transition:.22s; }
    .insight-card:hover { transform:translateY(-3px); box-shadow:0 8px 32px rgba(13,71,161,.18); }
    .insight-icon  { font-size:2.2rem; margin-bottom:8px; }
    .insight-value { font-family:'Poppins',sans-serif; font-size:2rem; font-weight:800; color:#0d47a1; }
    .insight-label { font-size:.82rem; color:#78909c; font-weight:600; margin-top:4px; }
    .insight-change{ font-size:.8rem; font-weight:700; margin-top:6px; color:#26a69a; }
    .tip-card { display:flex; align-items:flex-start; gap:14px; padding:14px; background:#e3f2fd; border-radius:12px; margin-bottom:10px; border-left:4px solid #1976d2; }
    .tip-icon { font-size:1.4rem; flex-shrink:0; }
    .tip-text { font-size:.87rem; color:#37474f; line-height:1.6; }
    .tip-text strong { color:#0d47a1; }
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
      <a href="goals.php"     class="nav-item"><span class="nav-icon">🎯</span><span>Goals</span></a>
      <a href="notes.php"     class="nav-item"><span class="nav-icon">📝</span><span>Notes</span></a>
      <a href="reminders.php" class="nav-item"><span class="nav-icon">🔔</span><span>Reminders</span></a>
      <a href="analytics.php" class="nav-item active"><span class="nav-icon">📊</span><span>Analytics</span></a>
      <a href="contact.php"   class="nav-item"><span class="nav-icon">📩</span><span>Contact</span></a>
      <div class="nav-section-label">Account</div>
      <a href="profile.php"   class="nav-item"><span class="nav-icon">👤</span><span>My Profile</span></a>
    </nav>
    <div class="sidebar-bottom"><a href="auth/logout.php" class="logout-btn"><span>🚪</span><span>Logout</span></a></div>
  </aside>

  <div class="main-content">
    <header class="topbar">
      <div class="topbar-title">📊 Analytics</div>
      <div class="topbar-search"><span>🔍</span><input type="text" placeholder="Search..."></div>
      <div class="topbar-actions">
        <button class="icon-btn" onclick="location.href='reminders.php'">🔔</button>
        <div class="topbar-date" id="today-date">📅</div>
      </div>
    </header>

    <main class="page-body">
      <div class="page-header">
        <div><h1>Analytics & Insights 📊</h1><p>See how you're doing across all areas of your life</p></div>
      </div>

      <div class="grid-4" style="margin-bottom:26px;" id="insight-cards">
        <div class="insight-card"><div class="insight-icon">⏳</div><div class="insight-value">–</div><div class="insight-label">Loading...</div></div>
        <div class="insight-card"><div class="insight-icon">⏳</div><div class="insight-value">–</div><div class="insight-label">Loading...</div></div>
        <div class="insight-card"><div class="insight-icon">⏳</div><div class="insight-value">–</div><div class="insight-label">Loading...</div></div>
        <div class="insight-card"><div class="insight-icon">⏳</div><div class="insight-value">–</div><div class="insight-label">Loading...</div></div>
      </div>

      <div class="grid-2" style="margin-bottom:22px;">
        <div class="card"><div class="card-title">✅ Task Completion This Week</div><canvas id="taskWeekChart" height="190"></canvas></div>
        <div class="card"><div class="card-title">💰 Daily Expenses</div><canvas id="dailyExpChart" height="190"></canvas></div>
      </div>

      <div class="grid-2" style="margin-bottom:22px;">
        <div class="card"><div class="card-title">📚 Activity by Category</div><canvas id="actCatChart" height="190"></canvas></div>
        <div class="card"><div class="card-title">📅 Monthly Task Completion</div><canvas id="monthlyChart" height="190"></canvas></div>
      </div>

      <div class="grid-2">
        <div class="card"><div class="card-title">🎯 Goal Completion Rate</div><canvas id="goalRadarChart" height="220"></canvas></div>
        <div class="card"><div class="card-title">💡 Smart Insights & Tips</div><div id="tips-list"></div></div>
      </div>
    </main>
    <footer class="footer"><span>PersonaTrack</span> © 2026 — Made with ❤️ for YOU!</footer>
  </div>
</div>

<div id="toast-container" class="toast-container"></div>

<script>
  document.getElementById('today-date').textContent = '📅 ' + new Date().toLocaleDateString('en-US',{weekday:'short',month:'short',day:'numeric'});

  async function loadAnalytics() {
    const [tRes, eRes, gRes, rRes, nRes] = await Promise.all([
      fetch('api/todos.php'),
      fetch('api/expenses.php'),
      fetch('api/goals.php'),
      fetch('api/reminders.php'),
      fetch('api/notes.php'),
    ]);
    const todos     = (await tRes.json()).data || [];
    const expenses  = (await eRes.json()).data || [];
    const goals     = (await gRes.json()).data || [];
    const reminders = (await rRes.json()).data || [];
    const notes     = (await nRes.json()).data || [];

    buildInsightCards(todos, expenses, goals, notes);
    buildCharts(todos, expenses, goals, notes);
    buildTips(todos, expenses, goals, notes, reminders);
  }

  function buildInsightCards(todos, expenses, goals, notes) {
    const total    = todos.length;
    const done     = todos.filter(t => t.is_done==1).length;
    const rate     = total > 0 ? Math.round(done/total*100) : 0;
    const spent    = expenses.filter(e => e.type!=='income').reduce((s,e) => s+parseFloat(e.amount),0);
    const achieved = goals.filter(g => g.is_achieved==1||g.progress>=100).length;
    const active   = goals.filter(g => !g.is_achieved&&g.progress<100).length;

    document.getElementById('insight-cards').innerHTML = [
      { icon:'✅', value:rate+'%',                label:'Task Completion Rate',  change:'↑ Keep going!' },
      { icon:'💰', value:'Rs '+spent.toLocaleString(), label:'Total Spent',      change:'This period' },
      { icon:'🏆', value:achieved,                label:'Goals Achieved',        change:active+' still in progress' },
      { icon:'📝', value:notes.length,            label:'Notes Created',         change:'Keep capturing!' },
    ].map(c => `
      <div class="insight-card">
        <div class="insight-icon">${c.icon}</div>
        <div class="insight-value">${c.value}</div>
        <div class="insight-label">${c.label}</div>
        <div class="insight-change">${c.change}</div>
      </div>`).join('');
  }

  function buildCharts(todos, expenses, goals, notes) {
    const days = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];

    new Chart(document.getElementById('taskWeekChart'),{
      type:'bar',
      data:{ labels:days, datasets:[
        { label:'Completed', data:days.map((_,i)=>todos.filter(t=>t.is_done&&t.due_date&&new Date(t.due_date).getDay()===(i+1)%7).length), backgroundColor:'rgba(38,166,154,.7)', borderRadius:6, borderWidth:0 },
        { label:'Total',     data:days.map((_,i)=>todos.filter(t=>t.due_date&&new Date(t.due_date).getDay()===(i+1)%7).length||1), backgroundColor:'rgba(25,118,210,.15)', borderRadius:6, borderWidth:0 },
      ]},
      options:{ responsive:true, plugins:{legend:{position:'top'}}, scales:{y:{beginAtZero:true,grid:{color:'#f0f5fb'}},x:{grid:{display:false}}} }
    });

    new Chart(document.getElementById('dailyExpChart'),{
      type:'line',
      data:{ labels:days, datasets:[{ label:'Spent (Rs)', data:days.map((_,i)=>expenses.filter(e=>e.type!=='income'&&e.exp_date&&new Date(e.exp_date).getDay()===(i+1)%7).reduce((s,e)=>s+parseFloat(e.amount),0)), borderColor:'#1976d2', backgroundColor:'rgba(25,118,210,.08)', fill:true, tension:.4, borderWidth:2.5, pointRadius:5, pointBackgroundColor:'#1976d2' }]},
      options:{ responsive:true, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true,grid:{color:'#f0f5fb'}},x:{grid:{display:false}}} }
    });

    const cats       = ['Academic','Club','Personal','Other'];
    const catTaskData= cats.map(c=>todos.filter(t=>t.category===c).length);
    const catNoteData= cats.map(c=>notes.filter(n=>n.category===c).length);
    new Chart(document.getElementById('actCatChart'),{
      type:'bar',
      data:{ labels:cats, datasets:[
        { label:'Tasks', data:catTaskData, backgroundColor:'rgba(25,118,210,.7)', borderRadius:6, borderWidth:0 },
        { label:'Notes', data:catNoteData, backgroundColor:'rgba(38,166,154,.6)', borderRadius:6, borderWidth:0 },
      ]},
      options:{ responsive:true, plugins:{legend:{position:'top'}}, scales:{y:{beginAtZero:true,grid:{color:'#f0f5fb'}},x:{grid:{display:false}}} }
    });

    const months     = [];
    const monthData  = [];
    for (let i=5;i>=0;i--) {
      const d = new Date(); d.setMonth(d.getMonth()-i);
      months.push(d.toLocaleDateString('en-US',{month:'short'}));
      monthData.push(todos.filter(t=>t.is_done&&t.due_date&&new Date(t.due_date).getMonth()===d.getMonth()).length);
    }
    new Chart(document.getElementById('monthlyChart'),{
      type:'line',
      data:{ labels:months, datasets:[{ label:'Tasks Completed', data:monthData, borderColor:'#7b1fa2', backgroundColor:'rgba(123,31,162,.08)', fill:true, tension:.4, borderWidth:2.5, pointRadius:5, pointBackgroundColor:'#7b1fa2' }]},
      options:{ responsive:true, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true,grid:{color:'#f0f5fb'}},x:{grid:{display:false}}} }
    });

    const avgProgress = cats.map(c => {
      const gs = goals.filter(g=>g.category===c);
      return gs.length > 0 ? Math.round(gs.reduce((s,g)=>s+(parseInt(g.progress)||0),0)/gs.length) : 0;
    });
    new Chart(document.getElementById('goalRadarChart'),{
      type:'radar',
      data:{ labels:cats, datasets:[{ label:'Avg Progress %', data:avgProgress, borderColor:'#1976d2', backgroundColor:'rgba(25,118,210,.15)', borderWidth:2.5, pointBackgroundColor:'#1976d2', pointRadius:5 }]},
      options:{ responsive:true, plugins:{legend:{display:false}}, scales:{r:{beginAtZero:true,max:100,ticks:{stepSize:25,font:{size:10}},grid:{color:'#e3f2fd'}}} }
    });
  }

  function buildTips(todos, expenses, goals, notes, reminders) {
    const rate    = todos.length > 0 ? Math.round(todos.filter(t=>t.is_done==1).length/todos.length*100) : 0;
    const spent   = expenses.filter(e=>e.type!=='income').reduce((s,e)=>s+parseFloat(e.amount),0);
    const active  = goals.filter(g=>!g.is_achieved&&g.progress<100).length;
    const achieved= goals.filter(g=>g.is_achieved==1||g.progress>=100).length;

    const tips = [];
    if (rate < 50)  tips.push({ icon:'⚡', text:'<strong>Task Completion Low:</strong> Try breaking large tasks into smaller steps. Completing 3 small tasks feels better than failing 1 big one!' });
    if (spent > 15000) tips.push({ icon:'💰', text:'<strong>Spending Alert:</strong> You\'ve spent a lot this period. Review your expenses and identify non-essential items to cut back.' });
    if (active > 0 && achieved === 0) tips.push({ icon:'🎯', text:`<strong>Goals Progress:</strong> You have ${active} active goals. Update their progress regularly to stay motivated!` });
    if (notes.length === 0) tips.push({ icon:'📝', text:'<strong>Start Taking Notes:</strong> Regular note-taking improves academic performance. Try capturing key ideas after every lecture.' });
    if (reminders.filter(r=>!r.is_done).length > 5) tips.push({ icon:'🔔', text:'<strong>Many Pending Reminders:</strong> Review them and mark completed ones to stay organised.' });
    tips.push({ icon:'🌟', text:'<strong>Stay Consistent:</strong> Small daily progress beats big occasional efforts. Log your activities every day for best results.' });
    tips.push({ icon:'⚖️', text:'<strong>Balance is Key:</strong> Academic success is important, but don\'t forget rest, social time, and personal goals.' });

    document.getElementById('tips-list').innerHTML = tips.map(t => `
      <div class="tip-card"><span class="tip-icon">${t.icon}</span><div class="tip-text">${t.text}</div></div>`).join('');
  }

  loadAnalytics();
</script>
</body>
</html>
