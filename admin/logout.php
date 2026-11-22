<?php
require_once __DIR__ . '/../includes/blog_app.php';

// Sign-out is POST + CSRF (from the header button). A plain GET only shows a confirmation,
// so a third-party page cannot silently sign the admin out.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (blog_verify_csrf($_POST['csrf_token'] ?? null)) {
        blog_logout();
        blog_flash('success', 'You have been signed out.');
    }
    header('Location: login.php');
    exit;
}

if (!blog_current_user()) {
    header('Location: login.php');
    exit;
}
$pageTitle = 'Sign out';
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <?php include __DIR__ . '/includes/head.php'; ?>
</head>
<body class="admin-app">
  <div class="d-flex align-items-center justify-content-center p-4" style="min-height:100vh">
    <div class="admin-login-card text-center">
      <h2>Sign out?</h2>
      <p class="admin-muted mb-4">You will need to sign in again to manage the website.</p>
      <form method="post" class="d-grid gap-2">
        <input type="hidden" name="csrf_token" value="<?php echo blog_escape(blog_csrf_token()); ?>">
        <button type="submit" class="btn btn-primary w-100 py-3"><i class="ri-logout-box-r-line"></i> Sign out</button>
        <a href="dashboard.php" class="btn btn-outline-secondary w-100">Cancel</a>
      </form>
    </div>
  </div>
</body>
</html>
