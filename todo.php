<?php
// ============================================================
// todo.php  –  To-Do List page (session protected)
// ============================================================
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
  <title>PersonaTrack – To-Do List</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <style>
    .task-card {
      background: white; border-radius: 12px; padding: 14px 18px;
      margin-bottom: 10px; display: flex; align-items: center; gap: 14px;
      box-shadow: 0 2px 8px rgba(13,71,161,.07); transition: .22s;
      border-left: 4px solid #42a5f5;
    }
    .task-card:hover { box-shadow: 0 8px 32px rgba(13,71,161,.18); transform: translateX(3px); }
    .task-card.done    { border-left-color: #26a69a; opacity: .75; }
    .task-card.overdue { border-left-color: #ef5350; }
    .task-cb {
      width:22px; height:22px; border-radius:50%;
      border:2px solid #42a5f5; cursor:pointer; flex-shrink:0;
      display:flex; align-items:center; justify-content:center; transition:.22s;
    }
    .task-cb.checked { background:#42a5f5; border-color:#42a5f5; }
    .task-cb.checked::after { content:'✓'; color:white; font-size:.75rem; font-weight:700; }
    .task-title { font-size:.92rem; font-weight:600; color:#0d1b2a; }
    .task-title.done { text-decoration:line-through; color:#78909c; }
    .task-meta { display:flex; gap:10px; flex-wrap:wrap; margin-top:4px; }
    .task-meta span { font-size:.75rem; color:#78909c; }
    .section-heading {
      font-family:'Poppins',sans-serif; font-weight:700; font-size:.92rem;
      color:#0d47a1; padding:10px 0 6px; border-bottom:2px solid #bbdefb;
      margin-bottom:12px; margin-top:8px;
    }
  </style>
</head>
<body>
<div class="app-layout">

  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="sidebar-logo"><div class="logo-icon">🎓</div><span>PersonaTrack</span></div>
    <div class="sidebar-user">
      <div class="avatar"><?= strtoupper(substr($userName,0,2)) ?></div>
      <div class="user-info"><div class="name"><?= $userName ?></div><div class="role">Member</div></div>
    </div>
    <nav class="sidebar-nav">
      <div class="nav-section-label">Main Menu</div>
      <a href="dashboard.php"  class="nav-item"><span class="nav-icon">🏠</span><span>Dashboard</span></a>
      <a href="todo.php"       class="nav-item active"><span class="nav-icon">✅</span><span>To-Do List</span></a>
      <a href="expenses.php"   class="nav-item"><span class="nav-icon">💰</span><span>Expenses</span></a>
      <a href="goals.php"      class="nav-item"><span class="nav-icon">🎯</span><span>Goals</span></a>
      <a href="notes.php"      class="nav-item"><span class="nav-icon">📝</span><span>Notes</span></a>
      <a href="reminders.php"  class="nav-item"><span class="nav-icon">🔔</span><span>Reminders</span></a>
      <a href="analytics.php"  class="nav-item"><span class="nav-icon">📊</span><span>Analytics</span></a>
      <a href="contact.php"    class="nav-item"><span class="nav-icon">📩</span><span>Contact</span></a>
      <div class="nav-section-label">Account</div>
      <a href="profile.php"    class="nav-item"><span class="nav-icon">👤</span><span>My Profile</span></a>
    </nav>
    <div class="sidebar-bottom">
      <a href="auth/logout.php" class="logout-btn"><span>🚪</span><span>Logout</span></a>
    </div>
  </aside>

  <div class="main-content">
    <header class="topbar">
      <div class="topbar-title">✅ To-Do List</div>
      <div class="topbar-search">
        <span>🔍</span>
        <input type="text" id="search-input" placeholder="Search tasks..." oninput="renderTasks()">
      </div>
      <div class="topbar-actions">
        <button class="icon-btn" onclick="location.href='reminders.php'">🔔</button>
        <div class="topbar-date" id="today-date">📅</div>
      </div>
    </header>

    <main class="page-body">
      <div class="page-header">
        <div><h1>My To-Do List ✅</h1><p>Plan your day, track your tasks, stay productive</p></div>
        <button class="btn btn-primary" onclick="openModal()">➕ Add Task</button>
      </div>

      <!-- Stats -->
      <div class="grid-4" style="margin-bottom:22px;">
        <div class="stat-card"><div class="stat-icon" style="background:#e3f2fd;">📋</div><div class="stat-info"><div class="label">Total</div><div class="value" id="st-total">–</div></div></div>
        <div class="stat-card"><div class="stat-icon" style="background:#e0f2f1;">✅</div><div class="stat-info"><div class="label">Completed</div><div class="value" id="st-done">–</div></div></div>
        <div class="stat-card"><div class="stat-icon" style="background:#fff3e0;">⏳</div><div class="stat-info"><div class="label">Planned</div><div class="value" id="st-planned">–</div></div></div>
        <div class="stat-card"><div class="stat-icon" style="background:#ffebee;">⚠️</div><div class="stat-info"><div class="label">Overdue</div><div class="value" id="st-overdue">–</div></div></div>
      </div>

      <!-- Filters -->
      <div style="display:flex;align-items:center;gap:14px;margin-bottom:20px;flex-wrap:wrap;">
        <div class="tab-bar">
          <button class="tab-btn active" onclick="setTab('all',this)">All</button>
          <button class="tab-btn" onclick="setTab('daily',this)">Daily</button>
          <button class="tab-btn" onclick="setTab('planned',this)">Planned</button>
          <button class="tab-btn" onclick="setTab('done',this)">Completed</button>
          <button class="tab-btn" onclick="setTab('overdue',this)">Overdue</button>
        </div>
        <select id="cat-filter" onchange="renderTasks()" style="padding:8px 14px;border:1.8px solid #e0e9f4;border-radius:20px;font-size:.83rem;outline:none;">
          <option value="">All Categories</option>
          <option>Academic</option><option>Club</option><option>Personal</option><option>Other</option>
        </select>
        <select id="pri-filter" onchange="renderTasks()" style="padding:8px 14px;border:1.8px solid #e0e9f4;border-radius:20px;font-size:.83rem;outline:none;">
          <option value="">All Priorities</option>
          <option>High</option><option>Medium</option><option>Low</option>
        </select>
      </div>

      <div id="task-list"><div class="empty-state"><div class="empty-icon">📋</div><p>Loading tasks...</p></div></div>
    </main>
    <footer class="footer"><span>PersonaTrack</span> © 2026 — Made with ❤️ for YOU!</footer>
  </div>
</div>

<!-- Add Task Modal -->
<div class="modal-overlay" id="task-modal">
  <div class="modal-box">
    <div class="modal-header">
      <h3>➕ Add New Task</h3>
      <button class="btn-icon" onclick="closeModal()">✕</button>
    </div>
    <div class="form-group">
      <label class="form-label">Task Title *</label>
      <input type="text" class="form-control" id="t-title" placeholder="e.g. Submit assignment">
    </div>
    <div class="grid-2">
      <div class="form-group">
        <label class="form-label">Category</label>
        <select class="form-control" id="t-cat">
          <option>Academic</option><option>Club</option><option>Personal</option><option>Other</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Priority</label>
        <select class="form-control" id="t-pri">
          <option>Low</option><option>Medium</option><option>High</option>
        </select>
      </div>
    </div>
    <div class="grid-2">
      <div class="form-group">
        <label class="form-label">Due Date</label>
        <input type="date" class="form-control" id="t-date">
      </div>
      <div class="form-group">
        <label class="form-label">Due Time</label>
        <input type="time" class="form-control" id="t-time">
      </div>
    </div>
    <div class="form-group">
      <label class="form-label">Notes (optional)</label>
      <textarea class="form-control" id="t-notes" rows="2" placeholder="Extra details..."></textarea>
    </div>
    <div style="display:flex;gap:10px;justify-content:flex-end;">
      <button class="btn btn-outline" onclick="closeModal()">Cancel</button>
      <button class="btn btn-primary" onclick="saveTask()">✅ Save Task</button>
    </div>
  </div>
</div>

<div id="toast-container" class="toast-container"></div>

<script>
  document.getElementById('today-date').textContent = '📅 ' + new Date().toLocaleDateString('en-US',{weekday:'short',month:'short',day:'numeric'});

  let todos      = [];   // API data store
  let currentTab = 'all';

  // ---- API: todos load ----
  async function loadTodos() {
    const res  = await fetch('api/todos.php');
    const data = await res.json();
    todos = data.data || [];
    renderTasks();
  }

  function setTab(tab, btn) {
    currentTab = tab;
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    renderTasks();
  }

  function renderTasks() {
    const search  = document.getElementById('search-input').value.toLowerCase();
    const cat     = document.getElementById('cat-filter').value;
    const pri     = document.getElementById('pri-filter').value;
    const today   = new Date(); today.setHours(0,0,0,0);
    const todayStr= today.toDateString();

    // ---- Stats update ----
    document.getElementById('st-total').textContent   = todos.length;
    document.getElementById('st-done').textContent    = todos.filter(t => t.is_done == 1).length;
    document.getElementById('st-planned').textContent = todos.filter(t => !t.is_done && t.due_date && new Date(t.due_date) > today).length;
    document.getElementById('st-overdue').textContent = todos.filter(t => !t.is_done && t.due_date && new Date(t.due_date) < today && new Date(t.due_date).toDateString() !== todayStr).length;

    // ---- Filter ----
    let filtered = todos.filter(t => {
      if (!t.title.toLowerCase().includes(search)) return false;
      if (cat && t.category !== cat) return false;
      if (pri && t.priority !== pri) return false;

      const dDate = t.due_date ? new Date(t.due_date) : null;
      if (currentTab === 'done')    return t.is_done == 1;
      if (currentTab === 'planned') return !t.is_done && dDate && dDate > today;
      if (currentTab === 'overdue') return !t.is_done && dDate && dDate < today && dDate.toDateString() !== todayStr;
      if (currentTab === 'daily')   return !t.due_date || (dDate && dDate.toDateString() === todayStr);
      return true;
    });

    if (filtered.length === 0) {
      document.getElementById('task-list').innerHTML = `<div class="empty-state"><div class="empty-icon">📋</div><p>No tasks found. Click <strong>Add Task</strong>!</p></div>`;
      return;
    }

    // ---- Group by category ----
    const icons  = { Academic:'📚', Club:'🏆', Personal:'👤', Other:'📌' };
    const groups = {};
    filtered.forEach(t => { const g = t.category||'Other'; if (!groups[g]) groups[g]=[]; groups[g].push(t); });

    let html = '';
    for (const [grp, tasks] of Object.entries(groups)) {
      html += `<div class="section-heading">${icons[grp]||'📌'} ${grp}</div>`;
      tasks.forEach(t => {
        const dDate    = t.due_date ? new Date(t.due_date) : null;
        const isOverdue= !t.is_done && dDate && dDate < today && dDate.toDateString() !== todayStr;
        const priBg    = t.priority==='High' ? '#ffebee' : t.priority==='Medium' ? '#fff3e0' : '#e3f2fd';
        const priColor = t.priority==='High' ? '#c62828' : t.priority==='Medium' ? '#e65100' : '#1976d2';
        html += `
          <div class="task-card ${t.is_done==1?'done':isOverdue?'overdue':''}">
            <div class="task-cb ${t.is_done==1?'checked':''}" onclick="toggleDone(${t.id},${t.is_done})"></div>
            <div style="flex:1;">
              <div class="task-title ${t.is_done==1?'done':''}">${t.title}</div>
              <div class="task-meta">
                ${t.due_date ? `<span>📅 ${t.due_date}</span>` : ''}
                ${t.due_time ? `<span>⏰ ${t.due_time}</span>` : ''}
                ${t.notes    ? `<span>📄 ${t.notes.substring(0,40)}</span>` : ''}
              </div>
            </div>
            <span style="background:${priBg};color:${priColor};padding:3px 11px;border-radius:20px;font-size:.75rem;font-weight:700;">${t.priority||'Low'}</span>
            <button class="btn-icon" onclick="deleteTask(${t.id})" title="Delete">🗑️</button>
          </div>`;
      });
    }
    document.getElementById('task-list').innerHTML = html;
  }

  // ---- Toggle done via API ----
  async function toggleDone(id, current) {
    const res  = await fetch('api/todos.php', {
      method: 'PUT',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({ id, is_done: current==1 ? 0 : 1 })
    });
    const data = await res.json();
    if (data.success) { await loadTodos(); }
  }

  // ---- Delete via API ----
  async function deleteTask(id) {
    if (!confirm('Delete this task?')) return;
    const res  = await fetch('api/todos.php', {
      method: 'DELETE',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({ id })
    });
    const data = await res.json();
    if (data.success) { showToast('🗑️ Task deleted!'); await loadTodos(); }
  }

  // ---- Save new task via API ----
  async function saveTask() {
    const title = document.getElementById('t-title').value.trim();
    if (!title) { showToast('⚠️ Please enter a task title!', 'error'); return; }

    const res  = await fetch('api/todos.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({
        title,
        category: document.getElementById('t-cat').value,
        priority: document.getElementById('t-pri').value,
        due_date: document.getElementById('t-date').value,
        due_time: document.getElementById('t-time').value,
        notes:    document.getElementById('t-notes').value,
      })
    });
    const data = await res.json();
    if (data.success) {
      showToast('✅ Task added!', 'success');
      closeModal();
      await loadTodos();
    }
  }

  function openModal() {
    document.getElementById('t-date').value = new Date().toISOString().split('T')[0];
    document.getElementById('task-modal').classList.add('open');
  }
  function closeModal() {
    document.getElementById('task-modal').classList.remove('open');
    ['t-title','t-notes'].forEach(id => document.getElementById(id).value = '');
  }

  function showToast(msg, type='info') {
    const c=document.getElementById('toast-container');
    const t=document.createElement('div'); t.className='toast';
    t.style.background = type==='success'?'#00897b':type==='error'?'#c62828':'var(--sky-dark)';
    t.textContent=msg; c.appendChild(t); setTimeout(()=>t.remove(),3200);
  }

  // Initial load
  loadTodos();
</script>
</body>
</html>
