<?php
// ============================================================
// reminders.php  –  Reminders page (session protected)
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
  <title>PersonaTrack – Reminders</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <style>
    .rem-card {
      background:white; border-radius:14px; padding:18px 20px; margin-bottom:12px;
      display:flex; align-items:center; gap:16px; box-shadow:0 4px 20px rgba(13,71,161,.1);
      transition:.22s; border-left:5px solid #1976d2;
    }
    .rem-card:hover  { box-shadow:0 8px 32px rgba(13,71,161,.18); transform:translateX(3px); }
    .rem-card.done   { opacity:.6; border-left-color:#b0bec5; }
    .rem-card.today  { border-left-color:#ffa726; background:#fffde7; }
    .rem-card.urgent { border-left-color:#ef5350; background:#fff5f5; }
    .rem-card.club   { border-left-color:#7b1fa2; }
    .rem-icon { width:46px; height:46px; border-radius:12px; background:#e3f2fd; display:flex; align-items:center; justify-content:center; font-size:1.4rem; flex-shrink:0; }
    .rem-title { font-family:'Poppins',sans-serif; font-size:.97rem; font-weight:700; color:#0d47a1; }
    .rem-title.done { text-decoration:line-through; color:#78909c; }
    .rem-meta  { font-size:.78rem; color:#78909c; margin-top:4px; display:flex; gap:12px; flex-wrap:wrap; }
    /* Mini calendar */
    .mini-cal { background:white; border-radius:14px; padding:18px; box-shadow:0 4px 20px rgba(13,71,161,.1); }
    .cal-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; }
    .cal-month  { font-family:'Poppins',sans-serif; font-weight:700; color:#0d47a1; }
    .cal-grid   { display:grid; grid-template-columns:repeat(7,1fr); gap:2px; text-align:center; }
    .cal-day-label { font-size:.72rem; font-weight:700; color:#78909c; padding:4px 0; }
    .cal-day { padding:6px 2px; font-size:.82rem; border-radius:8px; cursor:pointer; color:#0d1b2a; }
    .cal-day.today { background:#1976d2; color:white; font-weight:700; }
    .cal-day.has-reminder { position:relative; }
    .cal-day.has-reminder::after { content:''; position:absolute; bottom:2px; left:50%; transform:translateX(-50%); width:4px; height:4px; border-radius:50%; background:#ffa726; }
    .cal-day.other-month { color:#bbdefb; }
    .today-section { background:linear-gradient(135deg,#fff8e1,#fff3cd); border-radius:14px; padding:18px 22px; margin-bottom:22px; border:1.5px solid #ffe082; }
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
      <a href="reminders.php" class="nav-item active"><span class="nav-icon">🔔</span><span>Reminders</span></a>
      <a href="analytics.php" class="nav-item"><span class="nav-icon">📊</span><span>Analytics</span></a>
      <a href="contact.php"   class="nav-item"><span class="nav-icon">📩</span><span>Contact</span></a>
      <div class="nav-section-label">Account</div>
      <a href="profile.php"   class="nav-item"><span class="nav-icon">👤</span><span>My Profile</span></a>
    </nav>
    <div class="sidebar-bottom"><a href="auth/logout.php" class="logout-btn"><span>🚪</span><span>Logout</span></a></div>
  </aside>

  <div class="main-content">
    <header class="topbar">
      <div class="topbar-title">🔔 Reminders</div>
      <div class="topbar-search"><span>🔍</span><input type="text" id="search-input" placeholder="Search reminders..." oninput="renderReminders()"></div>
      <div class="topbar-actions">
        <button class="icon-btn">🔔</button>
        <div class="topbar-date" id="today-date">📅</div>
      </div>
    </header>

    <main class="page-body">
      <div class="page-header">
        <div><h1>Reminders 🔔</h1><p>Never miss an important event or deadline</p></div>
        <button class="btn btn-primary" onclick="openModal()">➕ Add Reminder</button>
      </div>

      <!-- Today's events -->
      <div class="today-section">
        <h3 style="font-family:'Poppins',sans-serif;font-weight:700;font-size:1rem;color:#e65100;margin-bottom:12px;">⚡ Today – Important Events</h3>
        <div id="today-list"><p style="font-size:.87rem;color:#78909c;">No events today. Enjoy your free time! 🎉</p></div>
      </div>

      <div class="grid-2">
        <!-- Reminders list -->
        <div>
          <div class="tab-bar" style="margin-bottom:16px;">
            <button class="tab-btn active" onclick="setTab('all',this)">All</button>
            <button class="tab-btn" onclick="setTab('upcoming',this)">Upcoming</button>
            <button class="tab-btn" onclick="setTab('Academic',this)">📚 Academic</button>
            <button class="tab-btn" onclick="setTab('Club',this)">🏅 Club</button>
            <button class="tab-btn" onclick="setTab('Personal',this)">👤 Personal</button>
          </div>
          <div id="reminders-list"></div>
        </div>

        <!-- Mini calendar -->
        <div class="mini-cal">
          <div class="cal-header">
            <button onclick="changeMonth(-1)" style="background:none;border:none;cursor:pointer;font-size:1rem;color:#1976d2;">‹</button>
            <div class="cal-month" id="cal-month-label"></div>
            <button onclick="changeMonth(1)"  style="background:none;border:none;cursor:pointer;font-size:1rem;color:#1976d2;">›</button>
          </div>
          <div class="cal-grid" id="cal-grid"></div>
          <div style="margin-top:14px;">
            <div style="font-family:'Poppins',sans-serif;font-weight:700;font-size:.85rem;color:#0d47a1;margin-bottom:10px;">Upcoming This Week 📅</div>
            <div id="week-list"></div>
          </div>
        </div>
      </div>
    </main>
    <footer class="footer"><span>PersonaTrack</span> © 2026 — Made with ❤️ for YOU!</footer>
  </div>
</div>

<!-- Add Reminder Modal -->
<div class="modal-overlay" id="rem-modal">
  <div class="modal-box">
    <div class="modal-header"><h3>🔔 Add Reminder</h3><button class="btn-icon" onclick="closeModal()">✕</button></div>
    <div class="form-group"><label class="form-label">Event / Reminder Title *</label><input type="text" class="form-control" id="r-title" placeholder="e.g. Submit assignment"></div>
    <div class="grid-2">
      <div class="form-group"><label class="form-label">Category</label><select class="form-control" id="r-cat"><option>Academic</option><option>Club</option><option>Personal</option><option>Other</option></select></div>
      <div class="form-group"><label class="form-label">Priority</label><select class="form-control" id="r-pri"><option>Normal</option><option>Important</option><option>Urgent</option></select></div>
    </div>
    <div class="grid-2">
      <div class="form-group"><label class="form-label">Date *</label><input type="date" class="form-control" id="r-date"></div>
      <div class="form-group"><label class="form-label">Time</label><input type="time" class="form-control" id="r-time"></div>
    </div>
    <div class="form-group"><label class="form-label">Notes (optional)</label><input type="text" class="form-control" id="r-notes" placeholder="Extra details..."></div>
    <div style="display:flex;gap:10px;justify-content:flex-end;">
      <button class="btn btn-outline" onclick="closeModal()">Cancel</button>
      <button class="btn btn-primary" onclick="saveReminder()">🔔 Save Reminder</button>
    </div>
  </div>
</div>

<div id="toast-container" class="toast-container"></div>

<script>
  document.getElementById('today-date').textContent = '📅 ' + new Date().toLocaleDateString('en-US',{weekday:'short',month:'short',day:'numeric'});

  let reminders  = [];
  let currentTab = 'all';
  let calYear    = new Date().getFullYear();
  let calMonth   = new Date().getMonth();
  const typeIcons = { Academic:'📚', Club:'🏅', Personal:'👤', Other:'📌' };

  async function loadReminders() {
    const res  = await fetch('api/reminders.php');
    const data = await res.json();
    reminders  = data.data || [];
    renderReminders();
  }

  function setTab(tab, btn) {
    currentTab = tab;
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active'); renderReminders();
  }

  function renderReminders() {
    const search   = document.getElementById('search-input').value.toLowerCase();
    const todayStr = new Date().toDateString();
    const now      = new Date(); now.setHours(0,0,0,0);

    // Today's events
    const todayRems = reminders.filter(r => r.rem_date && new Date(r.rem_date).toDateString()===todayStr && !r.is_done);
    document.getElementById('today-list').innerHTML = todayRems.length > 0
      ? todayRems.map(r => `
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
            <span>${typeIcons[r.rem_type]||'🔔'}</span>
            <div>
              <div style="font-weight:700;font-size:.88rem;">${r.title}</div>
              <div style="font-size:.75rem;color:#78909c;">${r.rem_time||'All day'} • ${r.rem_type||'General'}</div>
            </div>
            <span style="background:${r.priority==='Urgent'?'#ffebee':r.priority==='Important'?'#fff3e0':'#e3f2fd'};color:${r.priority==='Urgent'?'#c62828':r.priority==='Important'?'#e65100':'#1976d2'};padding:3px 11px;border-radius:20px;font-size:.75rem;font-weight:700;">${r.priority}</span>
          </div>`).join('')
      : '<p style="font-size:.87rem;color:#78909c;">No events today. Enjoy your free time! 🎉</p>';

    // Filter list
    let filtered = reminders.filter(r => r.title.toLowerCase().includes(search));
    if (currentTab === 'upcoming') filtered = filtered.filter(r => !r.is_done && r.rem_date && new Date(r.rem_date) >= now);
    else if (currentTab !== 'all') filtered = filtered.filter(r => r.rem_type===currentTab);
    filtered.sort((a,b) => new Date(a.rem_date||0) - new Date(b.rem_date||0));

    document.getElementById('reminders-list').innerHTML = filtered.length === 0
      ? `<div class="empty-state"><div class="empty-icon">🔔</div><p>No reminders. Add your first!</p></div>`
      : filtered.map(r => {
          const isToday = r.rem_date && new Date(r.rem_date).toDateString()===todayStr;
          const cls = r.is_done==1?'done':r.priority==='Urgent'?'urgent':isToday?'today':r.rem_type==='Club'?'club':'';
          return `
            <div class="rem-card ${cls}">
              <div class="rem-icon">${typeIcons[r.rem_type]||'🔔'}</div>
              <div style="flex:1;">
                <div class="rem-title ${r.is_done==1?'done':''}">${r.title}</div>
                <div class="rem-meta">
                  ${r.rem_date?`<span>📅 ${r.rem_date}</span>`:''}
                  ${r.rem_time?`<span>⏰ ${r.rem_time}</span>`:''}
                  <span style="background:${r.priority==='Urgent'?'#ffebee':r.priority==='Important'?'#fff3e0':'#e3f2fd'};color:${r.priority==='Urgent'?'#c62828':r.priority==='Important'?'#e65100':'#1976d2'};padding:2px 9px;border-radius:20px;font-size:.72rem;font-weight:700;">${r.priority||'Normal'}</span>
                  <span style="background:#e0f7fa;color:#00838f;padding:2px 9px;border-radius:20px;font-size:.72rem;font-weight:700;">${r.rem_type||'Other'}</span>
                </div>
                ${r.notes?`<div style="font-size:.78rem;color:#78909c;margin-top:4px;">📄 ${r.notes}</div>`:''}
              </div>
              <div style="display:flex;gap:8px;">
                <button class="btn-icon" onclick="toggleDone(${r.id},${r.is_done})" title="${r.is_done==1?'Undo':'Mark done'}">${r.is_done==1?'↩️':'✅'}</button>
                <button class="btn-icon" onclick="deleteReminder(${r.id})">🗑️</button>
              </div>
            </div>`;
        }).join('');

    // Upcoming this week
    const weekEnd  = new Date(); weekEnd.setDate(weekEnd.getDate()+7);
    const weekRems = reminders.filter(r => r.rem_date && new Date(r.rem_date)>=now && new Date(r.rem_date)<=weekEnd && !r.is_done)
                              .sort((a,b) => new Date(a.rem_date)-new Date(b.rem_date));
    document.getElementById('week-list').innerHTML = weekRems.length===0
      ? '<p style="font-size:.82rem;color:#78909c;">No upcoming events this week</p>'
      : weekRems.slice(0,5).map(r=>`
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;font-size:.82rem;">
            <span>${typeIcons[r.rem_type]||'🔔'}</span>
            <div><div style="font-weight:700;color:#0d1b2a;">${r.title}</div><div style="color:#78909c;">${r.rem_date||''}</div></div>
          </div>`).join('');

    renderCalendar();
  }

  function renderCalendar() {
    const months   = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    document.getElementById('cal-month-label').textContent = months[calMonth]+' '+calYear;

    const remDates    = reminders.map(r=>r.rem_date).filter(Boolean);
    const firstDay    = new Date(calYear,calMonth,1).getDay();
    const daysInMonth = new Date(calYear,calMonth+1,0).getDate();
    const today       = new Date();

    const dayLabels = ['Su','Mo','Tu','We','Th','Fr','Sa'];
    let html = dayLabels.map(d=>`<div class="cal-day-label">${d}</div>`).join('');
    for (let i=0;i<firstDay;i++) html += `<div class="cal-day other-month"></div>`;
    for (let d=1;d<=daysInMonth;d++) {
      const ds = `${calYear}-${String(calMonth+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
      const isToday = today.getFullYear()===calYear && today.getMonth()===calMonth && today.getDate()===d;
      html += `<div class="cal-day ${isToday?'today':''} ${remDates.includes(ds)?'has-reminder':''}">${d}</div>`;
    }
    document.getElementById('cal-grid').innerHTML = html;
  }

  function changeMonth(dir) { calMonth+=dir; if(calMonth>11){calMonth=0;calYear++;} if(calMonth<0){calMonth=11;calYear--;} renderCalendar(); }

  async function toggleDone(id, current) {
    await fetch('api/reminders.php',{ method:'PUT', headers:{'Content-Type':'application/json'}, body:JSON.stringify({id, is_done: current==1?0:1}) });
    await loadReminders();
  }

  async function deleteReminder(id) {
    if (!confirm('Delete this reminder?')) return;
    await fetch('api/reminders.php',{ method:'DELETE', headers:{'Content-Type':'application/json'}, body:JSON.stringify({id}) });
    showToast('🗑️ Deleted!'); await loadReminders();
  }

  async function saveReminder() {
    const title = document.getElementById('r-title').value.trim();
    const date  = document.getElementById('r-date').value;
    if (!title || !date) { showToast('⚠️ Please fill title and date!','error'); return; }

    const res = await fetch('api/reminders.php',{
      method:'POST', headers:{'Content-Type':'application/json'},
      body: JSON.stringify({ title, rem_date:date, rem_time:document.getElementById('r-time').value, type:document.getElementById('r-cat').value, priority:document.getElementById('r-pri').value, notes:document.getElementById('r-notes').value })
    });
    const data = await res.json();
    if (data.success) { showToast('🔔 Reminder set!','success'); closeModal(); await loadReminders(); }
  }

  function openModal()  { document.getElementById('r-date').value=new Date().toISOString().split('T')[0]; document.getElementById('rem-modal').classList.add('open'); }
  function closeModal() { document.getElementById('rem-modal').classList.remove('open'); ['r-title','r-notes'].forEach(id=>document.getElementById(id).value=''); }

  function showToast(msg,type='info') {
    const c=document.getElementById('toast-container'); const t=document.createElement('div'); t.className='toast';
    t.style.background=type==='success'?'#00897b':type==='error'?'#c62828':'var(--sky-dark)';
    t.textContent=msg; c.appendChild(t); setTimeout(()=>t.remove(),3200);
  }

  loadReminders();
</script>
</body>
</html>
