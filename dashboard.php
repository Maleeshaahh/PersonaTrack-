<?php

require_once 'includes/functions.php';
startSession();
requireLogin(); 

$userName  = htmlspecialchars($_SESSION['user_name']  ?? 'Student');
$userEmail = htmlspecialchars($_SESSION['user_email'] ?? '');
$userId    = (int)$_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PersonaTrack – Dashboard</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="app-layout">

  <aside class="sidebar">
    <div class="sidebar-logo"><div class="logo-icon">🎓</div><span>PersonaTrack</span></div>
    <div class="sidebar-user">
      <div class="avatar" id="sidebar-avatar"><?= strtoupper(substr($userName, 0, 2)) ?></div>
      <div class="user-info">
        <div class="name"><?= $userName ?></div>
        <div class="role">Member</div>
      </div>
    </div>
    <nav class="sidebar-nav">
      <div class="nav-section-label">Main Menu</div>
      <a href="dashboard.php" class="nav-item active"><span class="nav-icon">🏠</span><span>Dashboard</span></a>
      <a href="todo.php"      class="nav-item"><span class="nav-icon">✅</span><span>To-Do List</span></a>
      <a href="expenses.php"  class="nav-item"><span class="nav-icon">💰</span><span>Expenses</span></a>
      <a href="goals.php"     class="nav-item"><span class="nav-icon">🎯</span><span>Goals</span></a>
      <a href="notes.php"     class="nav-item"><span class="nav-icon">📝</span><span>Notes</span></a>
      <a href="reminders.php" class="nav-item"><span class="nav-icon">🔔</span><span>Reminders</span></a>
      <a href="analytics.php" class="nav-item"><span class="nav-icon">📊</span><span>Analytics</span></a>
      <a href="contact.php"   class="nav-item"><span class="nav-icon">📩</span><span>Contact</span></a>
      <div class="nav-section-label">Account</div>
      <a href="profile.php"   class="nav-item"><span class="nav-icon">👤</span><span>My Profile</span></a>
    </nav>
    <div class="sidebar-bottom">
      <a href="auth/logout.php" class="logout-btn"><span>🚪</span><span>Logout</span></a>
    </div>
  </aside>

  <div class="main-content">
    <header class="topbar">
      <div class="topbar-title">📊 Dashboard</div>
      <div class="topbar-search"><span>🔍</span><input type="text" placeholder="Search anything..."></div>
      <div class="topbar-actions">
        <button class="icon-btn" onclick="window.location.href='reminders.php'">🔔</button>
        <div class="topbar-date" id="today-date">📅</div>
      </div>
    </header>

    <main class="page-body">

      <div class="welcome-banner" style="background:linear-gradient(135deg,#0d47a1 0%,#1976d2 60%,#42a5f5 100%);border-radius:14px;padding:28px 32px;color:white;margin-bottom:24px;position:relative;overflow:hidden;">
        <div style="position:absolute;right:28px;top:50%;transform:translateY(-50%);font-size:5rem;opacity:.18;">🎓</div>
        <h2 style="font-family:'Poppins',sans-serif;font-size:1.5rem;font-weight:800;margin-bottom:5px;" id="welcome-msg">Welcome! 👋</h2>
        <p style="opacity:.85;font-size:.92rem;">Here's what's happening in your life today.</p>
        <a href="todo.php" style="margin-top:14px;display:inline-block;background:white;color:#0d47a1;border-radius:20px;padding:7px 18px;font-weight:700;font-size:.85rem;text-decoration:none;">📋 View Today's Tasks →</a>
      </div>

      <div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:24px;">
        <a href="todo.php"      class="quick-btn" style="display:flex;align-items:center;gap:9px;padding:10px 18px;border-radius:12px;border:1.8px solid #bbdefb;background:white;color:#0d47a1;font-size:.85rem;font-weight:700;cursor:pointer;transition:.22s;box-shadow:0 4px 20px rgba(13,71,161,.1);text-decoration:none;">✅ Add Task</a>
        <a href="expenses.php"  class="quick-btn" style="display:flex;align-items:center;gap:9px;padding:10px 18px;border-radius:12px;border:1.8px solid #bbdefb;background:white;color:#0d47a1;font-size:.85rem;font-weight:700;cursor:pointer;transition:.22s;box-shadow:0 4px 20px rgba(13,71,161,.1);text-decoration:none;">💸 Add Expense</a>
        <a href="goals.php"     class="quick-btn" style="display:flex;align-items:center;gap:9px;padding:10px 18px;border-radius:12px;border:1.8px solid #bbdefb;background:white;color:#0d47a1;font-size:.85rem;font-weight:700;cursor:pointer;transition:.22s;box-shadow:0 4px 20px rgba(13,71,161,.1);text-decoration:none;">🎯 Add Goal</a>
        <a href="notes.php"     class="quick-btn" style="display:flex;align-items:center;gap:9px;padding:10px 18px;border-radius:12px;border:1.8px solid #bbdefb;background:white;color:#0d47a1;font-size:.85rem;font-weight:700;cursor:pointer;transition:.22s;box-shadow:0 4px 20px rgba(13,71,161,.1);text-decoration:none;">📝 Add Note</a>
        <a href="reminders.php" class="quick-btn" style="display:flex;align-items:center;gap:9px;padding:10px 18px;border-radius:12px;border:1.8px solid #bbdefb;background:white;color:#0d47a1;font-size:.85rem;font-weight:700;cursor:pointer;transition:.22s;box-shadow:0 4px 20px rgba(13,71,161,.1);text-decoration:none;">🔔 Add Reminder</a>
      </div>

      <div class="grid-4" style="margin-bottom:24px;">
        <div class="stat-card">
          <div class="stat-icon" style="background:#e3f2fd;">✅</div>
          <div class="stat-info"><div class="label">Tasks Today</div><div class="value" id="stat-tasks">–</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:#e0f7fa;">💰</div>
          <div class="stat-info"><div class="label">Balance</div><div class="value" id="stat-balance">–</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:#f3e5f5;">🎯</div>
          <div class="stat-info"><div class="label">Active Goals</div><div class="value" id="stat-goals">–</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:#fff3e0;">🔔</div>
          <div class="stat-info"><div class="label">Reminders</div><div class="value" id="stat-reminders">–</div></div>
        </div>
      </div>

      <div class="grid-2" style="margin-bottom:24px;">
        <div class="card"><div class="card-title">📈 Weekly Activity</div><canvas id="weeklyChart" height="180"></canvas></div>
        <div class="card"><div class="card-title">💰 Expense Breakdown</div><canvas id="expenseChart" height="180"></canvas></div>
      </div>

      <div class="grid-2">
        <div class="card">
          <div class="card-title" style="justify-content:space-between;">
            <span>✅ Today's Tasks</span>
            <a href="todo.php" style="font-size:.78rem;color:#1976d2;font-weight:700;">View All →</a>
          </div>
          <div id="dashboard-tasks"><div class="empty-state"><div class="empty-icon">📋</div><p>Loading...</p></div></div>
        </div>
        <div class="card">
          <div class="card-title" style="justify-content:space-between;">
            <span>💸 Recent Expenses</span>
            <a href="expenses.php" style="font-size:.78rem;color:#1976d2;font-weight:700;">View All →</a>
          </div>
          <div id="dashboard-expenses"><div class="empty-state"><div class="empty-icon">💰</div><p>Loading...</p></div></div>
        </div>
      </div>

    </main>
    <footer class="footer"><span>PersonaTrack</span> © 2026 — Made with ❤️ for YOU!</footer>
  </div>
</div>

<script>
  const hour = new Date().getHours();
  const greeting = hour < 12 ? 'Good Morning' : hour < 17 ? 'Good Afternoon' : 'Good Evening';
  document.getElementById('welcome-msg').textContent = greeting + ', <?= $userName ?>! 👋';
  document.getElementById('today-date').textContent  = '📅 ' + new Date().toLocaleDateString('en-US',{weekday:'short',month:'short',day:'numeric',year:'numeric'});

  async function loadDashboard() {
    const [todosRes, expRes, goalsRes, remRes] = await Promise.all([
      fetch('api/todos.php'),
      fetch('api/expenses.php'),
      fetch('api/goals.php'),
      fetch('api/reminders.php'),
    ]);

    const todos     = (await todosRes.json()).data || [];
    const expenses  = (await expRes.json()).data   || [];
    const goals     = (await goalsRes.json()).data  || [];
    const reminders = (await remRes.json()).data    || [];

    const todayStr    = new Date().toDateString();
    const todayTasks  = todos.filter(t => t.due_date && new Date(t.due_date).toDateString() === todayStr || !t.due_date);
    const doneTasks   = todayTasks.filter(t => t.is_done == 1).length;
    document.getElementById('stat-tasks').textContent     = todayTasks.length;

    const income  = expenses.filter(e => e.type === 'income').reduce((s,e) => s + parseFloat(e.amount), 0);
    const spent   = expenses.filter(e => e.type !== 'income').reduce((s,e) => s + parseFloat(e.amount), 0);
    document.getElementById('stat-balance').textContent   = 'Rs ' + (income - spent).toLocaleString();
    document.getElementById('stat-goals').textContent     = goals.filter(g => !g.is_achieved).length;
    document.getElementById('stat-reminders').textContent = reminders.filter(r => !r.is_done).length;

    const taskHtml = todayTasks.slice(0, 5).map(t => `
      <div style="display:flex;align-items:center;gap:12px;padding:11px 0;border-bottom:1px solid #f0f5fb;">
        <div style="width:20px;height:20px;border-radius:50%;border:2px solid #42a5f5;background:${t.is_done ? '#42a5f5' : 'transparent'};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
          ${t.is_done ? '<span style="color:white;font-size:.7rem;font-weight:700;">✓</span>' : ''}
        </div>
        <span style="flex:1;font-size:.88rem;${t.is_done ? 'text-decoration:line-through;color:#78909c;' : ''}">${t.title}</span>
        <span style="background:${t.priority==='High'?'#ffebee':t.priority==='Medium'?'#fff3e0':'#e3f2fd'};color:${t.priority==='High'?'#c62828':t.priority==='Medium'?'#e65100':'#1976d2'};padding:3px 11px;border-radius:20px;font-size:.75rem;font-weight:700;">${t.priority || 'Low'}</span>
      </div>`).join('') || '<div class="empty-state"><div class="empty-icon">📋</div><p>No tasks yet. <a href="todo.php" style="color:#1976d2">Add one!</a></p></div>';
    document.getElementById('dashboard-tasks').innerHTML = taskHtml;

    const expHtml = expenses.slice(-5).reverse().map(e => `
      <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid #f0f5fb;">
        <div style="width:36px;height:36px;border-radius:10px;background:#e3f2fd;display:flex;align-items:center;justify-content:center;font-size:1.1rem;">${e.type === 'income' ? '💵' : '💸'}</div>
        <div style="flex:1"><div style="font-size:.87rem;font-weight:600;">${e.title}</div><div style="font-size:.75rem;color:#78909c;">${e.exp_date || ''}</div></div>
        <div style="font-weight:700;font-size:.9rem;color:${e.type === 'income' ? '#26a69a' : '#ef5350'}">${e.type === 'income' ? '+' : '-'} Rs ${parseFloat(e.amount).toLocaleString()}</div>
      </div>`).join('') || '<div class="empty-state"><div class="empty-icon">💰</div><p>No expenses yet.</p></div>';
    document.getElementById('dashboard-expenses').innerHTML = expHtml;

    renderCharts(todos, expenses);
  }

  function renderCharts(todos, expenses) {
    const days = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
    const weeklyData = days.map((_,i) =>
      todos.filter(t => t.is_done && t.due_date && new Date(t.due_date).getDay() === (i+1)%7).length || 0
    );
    new Chart(document.getElementById('weeklyChart'), {
      type: 'bar',
      data: { labels: days, datasets: [{ label:'Tasks Done', data: weeklyData, backgroundColor:'rgba(25,118,210,.18)', borderColor:'#1976d2', borderWidth:2, borderRadius:8 }]},
      options: { responsive:true, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true,grid:{color:'#f0f5fb'}},x:{grid:{display:false}}} }
    });

    const cats = ['Food','Transport','Books','Entertainment','Other'];
    const catData = cats.map(c => expenses.filter(e=>e.category===c && e.type!=='income').reduce((s,e)=>s+parseFloat(e.amount),0));
    new Chart(document.getElementById('expenseChart'), {
      type: 'doughnut',
      data: { labels:cats, datasets:[{data:catData, backgroundColor:['#1976d2','#42a5f5','#26a69a','#ffa726','#78909c'], borderWidth:0}]},
      options: { responsive:true, plugins:{legend:{position:'bottom', labels:{font:{size:11},padding:10}}} }
    });
  }

  loadDashboard();
</script>
</body>
</html>
