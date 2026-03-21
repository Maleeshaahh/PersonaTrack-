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
  <title>PersonaTrack – Expenses</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    .balance-card {
      background: linear-gradient(135deg,#0d47a1 0%,#1976d2 60%,#42a5f5 100%);
      border-radius:20px; padding:30px 32px; color:white; position:relative;
      overflow:hidden; box-shadow:0 10px 40px rgba(13,71,161,.3);
    }
    .balance-card .label { font-size:.82rem; opacity:.75; font-weight:600; letter-spacing:1px; text-transform:uppercase; }
    .balance-card .amount { font-family:'Poppins',sans-serif; font-size:2.4rem; font-weight:800; margin:6px 0; }
    .balance-card .sub-row { display:flex; gap:24px; margin-top:18px; flex-wrap:wrap; }
    .balance-card .s-label { font-size:.75rem; opacity:.7; }
    .balance-card .s-val   { font-size:1rem; font-weight:700; }
    .txn-item { display:flex; align-items:center; gap:14px; padding:12px 0; border-bottom:1px solid #f0f5fb; }
    .txn-item:last-child { border-bottom:none; }
    .txn-icon { width:40px; height:40px; border-radius:12px; background:#e3f2fd; display:flex; align-items:center; justify-content:center; font-size:1.2rem; flex-shrink:0; }
    .txn-title  { font-size:.9rem; font-weight:600; }
    .txn-meta   { font-size:.75rem; color:#78909c; }
    .txn-amount { font-weight:700; font-size:.95rem; }
    .txn-amount.minus { color:#ef5350; }
    .txn-amount.plus  { color:#26a69a; }
    .budget-bar { margin-top:16px; }
    .budget-label { display:flex; justify-content:space-between; font-size:.82rem; margin-bottom:6px; }
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
      <a href="expenses.php"  class="nav-item active"><span class="nav-icon">💰</span><span>Expenses</span></a>
      <a href="goals.php"     class="nav-item"><span class="nav-icon">🎯</span><span>Goals</span></a>
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
      <div class="topbar-title">💰 Expense Tracker</div>
      <div class="topbar-search">
        <span>🔍</span>
        <input type="text" id="search-input" placeholder="Search transactions..." oninput="renderTxns()">
      </div>
      <div class="topbar-actions">
        <button class="icon-btn" onclick="location.href='reminders.php'">🔔</button>
        <div class="topbar-date" id="today-date">📅</div>
      </div>
    </header>

    <main class="page-body">
      <div class="page-header">
        <div><h1>Expense Tracker 💰</h1><p>Track your money, stay within budget!</p></div>
        <div style="display:flex;gap:10px;">
          <button class="btn btn-outline" onclick="openModal('income')">💵 Add Income</button>
          <button class="btn btn-primary" onclick="openModal('expense')">➕ Add Expense</button>
        </div>
      </div>

      <div class="grid-2" style="margin-bottom:24px;">
        <div class="balance-card">
          <div class="label">💰 Total Balance</div>
          <div class="amount" id="total-balance">Rs 0.00</div>
          <div class="sub-row">
            <div><div class="s-label">📈 Income</div><div class="s-val" id="total-income">Rs 0</div></div>
            <div><div class="s-label">📉 Spent</div><div class="s-val" id="total-spent">Rs 0</div></div>
            <div><div class="s-label">💼 Remaining</div><div class="s-val" id="total-remaining">Rs 0</div></div>
          </div>
        </div>
        <div class="card"><div class="card-title">📊 Category Breakdown</div><canvas id="expPieChart" height="160"></canvas></div>
      </div>

      <div class="grid-2" style="margin-bottom:24px;">
        <div class="card"><div class="card-title">📈 Spending This Week</div><canvas id="weekSpendChart" height="170"></canvas></div>
        <div class="card"><div class="card-title">🎯 Category Budgets</div><div id="budget-bars"></div></div>
      </div>

      <div class="card">
        <div class="card-title" style="justify-content:space-between;margin-bottom:14px;">
          <span>📋 Transaction History</span>
          <div style="display:flex;gap:10px;align-items:center;">
            <select id="cat-filter" class="form-control" style="width:auto;padding:6px 12px;font-size:.82rem;" onchange="renderTxns()">
              <option value="">All Categories</option>
              <option>Food</option><option>Transport</option><option>Books</option>
              <option>Entertainment</option><option>Health</option><option>Clothing</option><option>Other</option>
            </select>
            <button class="btn btn-sm btn-outline" onclick="clearAll()" style="color:#ef5350;border-color:#ef5350;">🗑️ Clear All</button>
          </div>
        </div>
        <div id="txn-list"></div>
      </div>
    </main>
    <footer class="footer"><span>PersonaTrack</span> © 2026 — Made with ❤️ for YOU!</footer>
  </div>
</div>

<div class="modal-overlay" id="exp-modal">
  <div class="modal-box">
    <div class="modal-header">
      <h3 id="modal-title">➕ Add Expense</h3>
      <button class="btn-icon" onclick="closeModal()">✕</button>
    </div>
    <input type="hidden" id="exp-type" value="expense">
    <div class="form-group">
      <label class="form-label">Title *</label>
      <input type="text" class="form-control" id="exp-title" placeholder="e.g. Canteen lunch">
    </div>
    <div class="grid-2">
      <div class="form-group">
        <label class="form-label">Amount (Rs) *</label>
        <input type="number" class="form-control" id="exp-amount" placeholder="0.00" min="0">
      </div>
      <div class="form-group">
        <label class="form-label">Category</label>
        <select class="form-control" id="exp-cat">
          <option>Food</option><option>Transport</option><option>Books</option>
          <option>Entertainment</option><option>Health</option><option>Clothing</option><option>Other</option>
        </select>
      </div>
    </div>
    <div class="form-group">
      <label class="form-label">Date *</label>
      <input type="date" class="form-control" id="exp-date">
    </div>
    <div class="form-group">
      <label class="form-label">Notes (optional)</label>
      <input type="text" class="form-control" id="exp-notes" placeholder="Extra details...">
    </div>
    <div style="display:flex;gap:10px;justify-content:flex-end;">
      <button class="btn btn-outline" onclick="closeModal()">Cancel</button>
      <button class="btn btn-primary" onclick="saveExpense()">💾 Save</button>
    </div>
  </div>
</div>

<div id="toast-container" class="toast-container"></div>

<script>
  document.getElementById('today-date').textContent = '📅 ' + new Date().toLocaleDateString('en-US',{weekday:'short',month:'short',day:'numeric'});

  let expenses = [];
  const catIcons = { Food:'🍜', Transport:'🚌', Books:'📚', Entertainment:'🎮', Health:'💊', Clothing:'👕', Other:'📦' };

  async function loadExpenses() {
    const res  = await fetch('api/expenses.php');
    const data = await res.json();
    expenses = data.data || [];
    renderTxns();
  }

  function renderTxns() {
    const search = document.getElementById('search-input').value.toLowerCase();
    const cat    = document.getElementById('cat-filter').value;

    const income  = expenses.filter(e => e.type==='income').reduce((s,e) => s+parseFloat(e.amount),0);
    const spent   = expenses.filter(e => e.type!=='income').reduce((s,e) => s+parseFloat(e.amount),0);
    document.getElementById('total-income').textContent    = 'Rs ' + income.toLocaleString();
    document.getElementById('total-spent').textContent     = 'Rs ' + spent.toLocaleString();
    document.getElementById('total-remaining').textContent = 'Rs ' + income.toLocaleString();
    document.getElementById('total-balance').textContent   = 'Rs ' + (income - spent).toLocaleString();

    const filtered = expenses.filter(e =>
      e.title.toLowerCase().includes(search) && (!cat || e.category===cat)
    ).reverse();

    document.getElementById('txn-list').innerHTML = filtered.length === 0
      ? `<div class="empty-state"><div class="empty-icon">💰</div><p>No transactions yet.</p></div>`
      : filtered.map(e => `
          <div class="txn-item">
            <div class="txn-icon">${catIcons[e.category]||'💳'}</div>
            <div style="flex:1;">
              <div class="txn-title">${e.title}</div>
              <div class="txn-meta">${e.category||''} • ${e.exp_date||''} ${e.notes?'• '+e.notes:''}</div>
            </div>
            <div class="txn-amount ${e.type==='income'?'plus':'minus'}">${e.type==='income'?'+':'-'} Rs ${parseFloat(e.amount).toLocaleString()}</div>
            <button class="btn-icon" onclick="deleteTxn(${e.id})" style="margin-left:6px;">🗑️</button>
          </div>`).join('');

    const cats    = ['Food','Transport','Books','Entertainment','Health'];
    const budgets = { Food:5000, Transport:2000, Books:3000, Entertainment:1500, Health:1000 };
    document.getElementById('budget-bars').innerHTML = cats.map(c => {
      const s   = expenses.filter(e => e.category===c && e.type!=='income').reduce((s,e)=>s+parseFloat(e.amount),0);
      const pct = Math.min(100, Math.round(s/budgets[c]*100));
      const cls = pct>=90?'red':pct>=60?'orange':'';
      return `<div class="budget-bar">
        <div class="budget-label"><span style="font-weight:600;">${catIcons[c]} ${c}</span><span style="font-weight:700;color:#1976d2;">${pct}% of Rs${budgets[c].toLocaleString()}</span></div>
        <div class="progress-wrap"><div class="progress-fill ${cls}" style="width:${pct}%"></div></div>
      </div>`;
    }).join('');

    updateCharts();
  }

  let pieChart, weekChart;
  function updateCharts() {
    const cats     = ['Food','Transport','Books','Entertainment','Health','Clothing','Other'];
    const catSpend = cats.map(c => expenses.filter(e=>e.category===c && e.type!=='income').reduce((s,e)=>s+parseFloat(e.amount),0));

    if (pieChart) pieChart.destroy();
    pieChart = new Chart(document.getElementById('expPieChart'),{
      type:'doughnut',
      data:{ labels:cats, datasets:[{data:catSpend, backgroundColor:['#1976d2','#42a5f5','#26a69a','#ffa726','#ef5350','#ab47bc','#78909c'], borderWidth:0}]},
      options:{ responsive:true, plugins:{legend:{position:'bottom',labels:{font:{size:10},padding:8}}} }
    });

    const days     = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
    const daySpend = days.map((_,i) =>
      expenses.filter(e => e.type!=='income' && e.exp_date && new Date(e.exp_date).getDay()===(i+1)%7).reduce((s,e)=>s+parseFloat(e.amount),0)
    );

    if (weekChart) weekChart.destroy();
    weekChart = new Chart(document.getElementById('weekSpendChart'),{
      type:'bar',
      data:{ labels:days, datasets:[{label:'Spent (Rs)', data:daySpend, backgroundColor:'rgba(25,118,210,.2)', borderColor:'#1976d2', borderWidth:2, borderRadius:8}]},
      options:{ responsive:true, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true,grid:{color:'#f0f5fb'}},x:{grid:{display:false}}} }
    });
  }

  async function saveExpense() {
    const title  = document.getElementById('exp-title').value.trim();
    const amount = parseFloat(document.getElementById('exp-amount').value);
    if (!title || isNaN(amount) || amount <= 0) { showToast('⚠️ Fill title and amount!','error'); return; }

    const res  = await fetch('api/expenses.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({
        title, amount,
        type:     document.getElementById('exp-type').value,
        category: document.getElementById('exp-cat').value,
        exp_date: document.getElementById('exp-date').value,
        notes:    document.getElementById('exp-notes').value,
      })
    });
    const data = await res.json();
    if (data.success) { showToast('💾 Saved!','success'); closeModal(); await loadExpenses(); }
  }

  async function deleteTxn(id) {
    if (!confirm('Delete this transaction?')) return;
    const res = await fetch('api/expenses.php',{ method:'DELETE', headers:{'Content-Type':'application/json'}, body:JSON.stringify({id}) });
    const data = await res.json();
    if (data.success) { showToast('🗑️ Deleted!'); await loadExpenses(); }
  }

  async function clearAll() {
    if (!confirm('Clear all transactions? Cannot be undone.')) return;
    for (const e of expenses) {
      await fetch('api/expenses.php',{ method:'DELETE', headers:{'Content-Type':'application/json'}, body:JSON.stringify({id:e.id}) });
    }
    showToast('🗑️ All cleared!'); await loadExpenses();
  }

  function openModal(type='expense') {
    document.getElementById('exp-type').value     = type;
    document.getElementById('modal-title').textContent = type==='income'?'💵 Add Income':'➕ Add Expense';
    document.getElementById('exp-date').value     = new Date().toISOString().split('T')[0];
    document.getElementById('exp-modal').classList.add('open');
  }
  function closeModal() {
    document.getElementById('exp-modal').classList.remove('open');
    ['exp-title','exp-amount','exp-notes'].forEach(id => document.getElementById(id).value='');
  }

  function showToast(msg,type='info') {
    const c=document.getElementById('toast-container');
    const t=document.createElement('div'); t.className='toast';
    t.style.background=type==='success'?'#00897b':type==='error'?'#c62828':'var(--sky-dark)';
    t.textContent=msg; c.appendChild(t); setTimeout(()=>t.remove(),3200);
  }

  loadExpenses();
</script>
</body>
</html>
