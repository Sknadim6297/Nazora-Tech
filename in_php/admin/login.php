<?php
require_once __DIR__ . '/../includes/blog_app.php';

if (blog_current_user()) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];
$pageTitle = 'Sign In';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!blog_verify_csrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Security token expired. Please refresh and try again.';
    } else {
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            $errors[] = 'Email and password are required.';
        } elseif (blog_login($email, $password)) {
            blog_flash('success', 'Welcome back. You are now signed in.');
            header('Location: dashboard.php');
            exit;
        } else {
            $errors[] = 'Invalid credentials.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <?php include __DIR__ . '/includes/head.php'; ?>
</head>
<body class="admin-app">
  <div class="admin-login-wrap">
    <section class="admin-login-hero">
      <div>
        <img src="../assets/images/logo/nazora-logo.png" alt="<?php echo blog_escape(site_company_name()); ?>" class="login-logo">
        <h1>Nazora TECH Admin</h1>
        <p>Manage careers and portfolio projects from one dashboard.</p>
      </div>
    </section>

    <section class="d-flex align-items-center justify-content-center p-4">
      <div class="admin-login-card">
        <h2>Welcome back</h2>
        <p class="admin-muted mb-4">Sign in to your <?php echo blog_escape(site_company_name()); ?> admin account.</p>

        <?php if ($errors) : ?>
          <div class="alert alert-danger">
            <?php foreach ($errors as $error) : ?>
              <div><?php echo blog_escape($error); ?></div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <form method="post" class="d-grid gap-3">
          <input type="hidden" name="csrf_token" value="<?php echo blog_escape(blog_csrf_token()); ?>">
          <div>
            <label class="form-label"><i class="ri-mail-line"></i> Email address</label>
            <input type="email" class="form-control" name="email" placeholder="you@company.com" required>
          </div>
          <div>
            <label class="form-label"><i class="ri-lock-line"></i> Password</label>
            <input type="password" class="form-control" name="password" placeholder="Enter password" required>
          </div>
          <button type="submit" class="btn btn-primary w-100 py-3"><i class="ri-login-circle-line"></i> Sign In</button>
        </form>
      </div>
    </section>
  </div>
  <script src="assets/js/admin.js"></script>
</body>
</html>
