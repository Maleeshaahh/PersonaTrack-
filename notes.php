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
  <title>PersonaTrack – Notes</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <style>
    .note-card {
      background:white; border-radius:14px; padding:18px;
      box-shadow:0 4px 20px rgba(13,71,161,.1); transition:.22s;
      cursor:pointer; position:relative; border-top:4px solid #1976d2;
      min-height:160px; display:flex; flex-direction:column;
    }
    .note-card:hover { transform:translateY(-3px); box-shadow:0 8px 32px rgba(13,71,161,.18); }
    .note-card[data-cat="Academic"] { border-top-color:#1976d2; }
    .note-card[data-cat="AIESEC"]   { border-top-color:#e65100; }
    .note-card[data-cat="IEEE"]     { border-top-color:#7b1fa2; }
    .note-card[data-cat="MS"]       { border-top-color:#00838f; }
    .note-card[data-cat="Other"]    { border-top-color:#78909c; }
    .note-title { font-family:'Poppins',sans-serif; font-weight:700; font-size:.97rem; color:#0d47a1; flex:1; }
    .note-body  { font-size:.84rem; color:#37474f; line-height:1.6; flex:1; overflow:hidden; display:-webkit-box; -webkit-line-clamp:4; -webkit-box-orient:vertical; }
    .note-footer{ display:flex; align-items:center; justify-content:space-between; margin-top:12px; }
    .note-date  { font-size:.73rem; color:#78909c; }
    
    .note-panel {
      position:fixed; right:0; top:0; bottom:0; width:400px;
      background:white; box-shadow:-6px 0 30px rgba(13,71,161,.12);
      z-index:150; transform:translateX(100%); transition:transform .3s ease;
      display:flex; flex-direction:column;
    }
    .note-panel.open { transform:translateX(0); }
    .note-panel-header { padding:22px 22px 16px; border-bottom:1px solid #f0f5fb; display:flex; align-items:center; justify-content:space-between; }
    .note-panel-body   { flex:1; padding:22px; overflow-y:auto; }
    .note-panel-body h2{ font-family:'Poppins',sans-serif; font-weight:700; color:#0d47a1; margin-bottom:14px; }
    .note-panel-body p { font-size:.9rem; line-height:1.8; color:#37474f; white-space:pre-wrap; }
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
      <a href="notes.php"     class="nav-item active"><span class="nav-icon">📝</span><span>Notes</span></a>
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
      <div class="topbar-title">📝 Notes</div>
      <div class="topbar-search"><span>🔍</span><input type="text" id="search-input" placeholder="Search notes..." oninput="renderNotes()"></div>
      <div class="topbar-actions">
        <button class="icon-btn" onclick="location.href='reminders.php'">🔔</button>
        <div class="topbar-date" id="today-date">📅</div>
      </div>
    </header>

    <main class="page-body">
      <div class="page-header">
        <div><h1>My Notes 📝</h1><p>Capture ideas, lecture notes, and club minutes</p></div>
        <button class="btn btn-primary" onclick="openModal()">➕ Add Note</button>
      </div>

      <div class="tab-bar" style="margin-bottom:20px;">
        <button class="tab-btn active" onclick="setTab('all',this)">All</button>
        <button class="tab-btn" onclick="setTab('Academic',this)">📚 Academic</button>
        <button class="tab-btn" onclick="setTab('AIESEC',this)">🌍 AIESEC</button>
        <button class="tab-btn" onclick="setTab('IEEE',this)">⚡ IEEE</button>
        <button class="tab-btn" onclick="setTab('MS',this)">💻 MS</button>
        <button class="tab-btn" onclick="setTab('Other',this)">📌 Other</button>
      </div>

      <div class="grid-3" id="notes-grid"></div>
    </main>
    <footer class="footer"><span>PersonaTrack</span> © 2026 — Made with ❤️ for YOU!</footer>
  </div>
</div>

<div class="note-panel" id="note-panel">
  <div class="note-panel-header">
    <span id="panel-cat" style="background:#e3f2fd;color:#1976d2;padding:3px 11px;border-radius:20px;font-size:.75rem;font-weight:700;">Academic</span>
    <div style="display:flex;gap:8px;">
      <button class="btn btn-sm btn-outline" onclick="editFromPanel()">✏️ Edit</button>
      <button class="btn-icon" onclick="document.getElementById('note-panel').classList.remove('open')">✕</button>
    </div>
  </div>
  <div class="note-panel-body">
    <h2 id="panel-title"></h2>
    <p id="panel-body"></p>
  </div>
</div>

<div class="modal-overlay" id="note-modal">
  <div class="modal-box">
    <div class="modal-header">
      <h3 id="modal-title">📝 Add Note</h3>
      <button class="btn-icon" onclick="closeModal()">✕</button>
    </div>
    <input type="hidden" id="n-edit-id" value="">
    <div class="form-group"><label class="form-label">Note Title *</label><input type="text" class="form-control" id="n-title" placeholder="e.g. Chapter 3 Summary"></div>
    <div class="form-group">
      <label class="form-label">Category</label>
      <select class="form-control" id="n-cat"><option>Academic</option><option>AIESEC</option><option>IEEE</option><option>MS</option><option>Other</option></select>
    </div>
    <div class="form-group"><label class="form-label">Note Content *</label><textarea class="form-control" id="n-body" rows="6" placeholder="Write your notes here..."></textarea></div>
    <div style="display:flex;gap:10px;justify-content:flex-end;">
      <button class="btn btn-outline" onclick="closeModal()">Cancel</button>
      <button class="btn btn-primary" onclick="saveNote()">💾 Save Note</button>
    </div>
  </div>
</div>

<div id="toast-container" class="toast-container"></div>

<script>
  document.getElementById('today-date').textContent = '📅 ' + new Date().toLocaleDateString('en-US',{weekday:'short',month:'short',day:'numeric'});

  let notes       = [];
  let currentTab  = 'all';
  let panelNoteId = null;
  const catEmojis = { Academic:'📚', AIESEC:'🌍', IEEE:'⚡', MS:'💻', Other:'📌' };

  async function loadNotes() {
    const res  = await fetch('api/notes.php');
    const data = await res.json();
    notes = data.data || [];
    renderNotes();
  }

  function setTab(tab, btn) {
    currentTab = tab;
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active'); renderNotes();
  }

  function renderNotes() {
    const search   = document.getElementById('search-input').value.toLowerCase();
    const filtered = notes.filter(n => {
      const matchSearch = n.title.toLowerCase().includes(search) || n.body.toLowerCase().includes(search);
      const matchTab    = currentTab==='all' || n.category===currentTab;
      return matchSearch && matchTab;
    });

    if (filtered.length === 0) {
      document.getElementById('notes-grid').innerHTML = `<div class="empty-state" style="grid-column:1/-1"><div class="empty-icon">📝</div><p>No notes found. Add your first note!</p></div>`;
      return;
    }

    document.getElementById('notes-grid').innerHTML = filtered.map(n => `
      <div class="note-card" data-cat="${n.category}" onclick="viewNote(${n.id})">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:10px;">
          <div class="note-title">${n.title}</div>
          <span style="background:#e3f2fd;color:#1976d2;padding:3px 9px;border-radius:20px;font-size:.72rem;font-weight:700;white-space:nowrap;margin-left:8px;">${catEmojis[n.category]||'📌'} ${n.category}</span>
        </div>
        <div class="note-body">${n.body}</div>
        <div class="note-footer">
          <span class="note-date">📅 ${n.created_at ? n.created_at.split(' ')[0] : ''}</span>
          <div style="display:flex;gap:5px;" onclick="event.stopPropagation()">
            <button class="btn-icon" onclick="openEditModal(${n.id})" title="Edit">✏️</button>
            <button class="btn-icon" onclick="deleteNote(${n.id})" title="Delete">🗑️</button>
          </div>
        </div>
      </div>`).join('');
  }

  function viewNote(id) {
    const n = notes.find(n => n.id==id);
    if (!n) return;
    panelNoteId = id;
    document.getElementById('panel-title').textContent = n.title;
    document.getElementById('panel-body').textContent  = n.body;
    document.getElementById('panel-cat').textContent   = (catEmojis[n.category]||'📌')+' '+n.category;
    document.getElementById('note-panel').classList.add('open');
  }

  function editFromPanel() {
    document.getElementById('note-panel').classList.remove('open');
    openEditModal(panelNoteId);
  }

  function openModal() {
    document.getElementById('n-edit-id').value = '';
    document.getElementById('modal-title').textContent = '📝 Add Note';
    ['n-title','n-body'].forEach(id => document.getElementById(id).value='');
    document.getElementById('note-modal').classList.add('open');
  }

  function openEditModal(id) {
    const n = notes.find(n => n.id==id); if (!n) return;
    document.getElementById('n-edit-id').value        = id;
    document.getElementById('n-title').value          = n.title;
    document.getElementById('n-cat').value            = n.category;
    document.getElementById('n-body').value           = n.body;
    document.getElementById('modal-title').textContent= '✏️ Edit Note';
    document.getElementById('note-modal').classList.add('open');
  }

  function closeModal() { document.getElementById('note-modal').classList.remove('open'); }

  async function saveNote() {
    const title  = document.getElementById('n-title').value.trim();
    const body   = document.getElementById('n-body').value.trim();
    const editId = document.getElementById('n-edit-id').value;
    if (!title || !body) { showToast('⚠️ Please fill title and content!','error'); return; }

    const payload = { title, body, category: document.getElementById('n-cat').value };
    let method = 'POST';
    if (editId) { payload.id = parseInt(editId); method = 'PUT'; }

    const res  = await fetch('api/notes.php',{ method, headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload) });
    const data = await res.json();
    if (data.success) { showToast('💾 Note saved!','success'); closeModal(); await loadNotes(); }
  }

  async function deleteNote(id) {
    if (!confirm('Delete this note?')) return;
    await fetch('api/notes.php',{ method:'DELETE', headers:{'Content-Type':'application/json'}, body:JSON.stringify({id}) });
    document.getElementById('note-panel').classList.remove('open');
    showToast('🗑️ Note deleted!'); await loadNotes();
  }

  function showToast(msg,type='info') {
    const c=document.getElementById('toast-container');
    const t=document.createElement('div'); t.className='toast';
    t.style.background=type==='success'?'#00897b':type==='error'?'#c62828':'var(--sky-dark)';
    t.textContent=msg; c.appendChild(t); setTimeout(()=>t.remove(),3200);
  }

  loadNotes();
</script>
</body>
</html>
