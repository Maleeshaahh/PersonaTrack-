<?php
// ============================================================
// contact.php
// Contact Form - Messages database-ල store කරයි
// ============================================================

require_once 'includes/db.php';
require_once 'includes/functions.php';

startSession();

// POST request handle කරයි (AJAX submit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ---- Form data get කරයි ----
    $name    = post('name');
    $email   = post('email');
    $subject = post('subject');
    $message = post('message');

    // ---- Validation ----
    if (empty($name) || empty($email) || empty($message)) {
        jsonResponse(false, 'Please fill in name, email, and message.');
    }

    if (!isValidEmail($email)) {
        jsonResponse(false, 'Please enter a valid email address.');
    }

    // ---- Database save ----
    $db = getDB();
    if (!$db) {
        jsonResponse(false, 'Database connection failed.');
    }

    $stmt = $db->prepare(
        'INSERT INTO messages (name, email, subject, message) VALUES (?, ?, ?, ?)'
    );
    $stmt->execute([$name, $email, $subject, $message]);

    jsonResponse(true, 'Your message has been sent successfully! We will contact you soon.');
    exit;
}

// ---- GET request - Contact page render කරයි ----
$pageTitle = 'Contact Us – PersonaTrack';
$isLoggedIn = !empty($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?></title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <style>
    body {
      min-height: 100vh;
      background: linear-gradient(135deg, #0d47a1 0%, #1565c0 40%, #1976d2 70%, #42a5f5 100%);
      display: flex; align-items: center; justify-content: center;
      padding: 40px 16px;
    }
    .contact-card {
      background: white;
      border-radius: 24px;
      padding: 40px;
      width: 100%;
      max-width: 520px;
      box-shadow: 0 24px 80px rgba(0,0,0,0.25);
    }
    .contact-card h2 {
      font-family: 'Poppins', sans-serif;
      font-weight: 800;
      font-size: 1.6rem;
      color: #0d47a1;
      margin-bottom: 6px;
    }
    .back-link {
      display: inline-flex; align-items: center; gap: 6px;
      color: #1976d2; font-weight: 700; font-size: 0.85rem;
      text-decoration: none; margin-bottom: 22px;
    }
    .back-link:hover { text-decoration: underline; }
    .submit-btn {
      width: 100%; padding: 13px;
      background: linear-gradient(135deg, #1976d2, #0d47a1);
      color: white; border: none; border-radius: 11px;
      font-size: 1rem; font-weight: 700; cursor: pointer;
    }
    .submit-btn:hover { opacity: 0.92; }
    #msg-result { margin-top: 14px; font-size: 0.9rem; font-weight: 600; }
    .success { color: #00897b; }
    .error   { color: #c62828; }
  </style>
</head>
<body>
<div class="contact-card">
  <a href="<?= $isLoggedIn ? 'dashboard.php' : 'index.php' ?>" class="back-link">← Back</a>
  <h2>📩 Contact Us</h2>
  <p style="color:#78909c;font-size:0.88rem;margin-bottom:24px;">
    Have a question or feedback? We'd love to hear from you!
  </p>

  <form id="contact-form">
    <!-- Name -->
    <div class="mb-3">
      <label class="form-label fw-bold" style="font-size:0.83rem;">Your Name *</label>
      <input type="text" name="name" id="c-name" class="form-control" placeholder="Full name" required>
    </div>
    <!-- Email -->
    <div class="mb-3">
      <label class="form-label fw-bold" style="font-size:0.83rem;">Email *</label>
      <input type="email" name="email" id="c-email" class="form-control" placeholder="your@email.com" required>
    </div>
    <!-- Subject -->
    <div class="mb-3">
      <label class="form-label fw-bold" style="font-size:0.83rem;">Subject</label>
      <input type="text" name="subject" id="c-subject" class="form-control" placeholder="What is this about?">
    </div>
    <!-- Message -->
    <div class="mb-4">
      <label class="form-label fw-bold" style="font-size:0.83rem;">Message *</label>
      <textarea name="message" id="c-message" class="form-control" rows="5"
                placeholder="Write your message here..." required></textarea>
    </div>
    <button type="submit" class="submit-btn">📨 Send Message</button>
    <div id="msg-result"></div>
  </form>
</div>

<script>
  // Contact form AJAX submit
  document.getElementById('contact-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn    = this.querySelector('.submit-btn');
    const result = document.getElementById('msg-result');
    btn.textContent = '⏳ Sending...';
    btn.disabled    = true;

    const formData = new FormData(this);
    try {
      const res  = await fetch('contact.php', { method: 'POST', body: formData });
      const data = await res.json();
      result.className = data.success ? 'success' : 'error';
      result.textContent = data.message;
      if (data.success) this.reset();
    } catch {
      result.className   = 'error';
      result.textContent = '⚠️ Something went wrong. Please try again.';
    }
    btn.textContent = '📨 Send Message';
    btn.disabled    = false;
  });
</script>
</body>
</html>
