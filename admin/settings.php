<?php
header('Location: dashboard.php');
exit;

require_once __DIR__ . '/../includes/blog_app.php';
blog_require_login();

$pageTitle = 'SEO Settings';
$pageSubtitle = 'Configure sitewide SEO defaults for the public blog.';
$activeMenu = 'settings';
$errors = [];

$defaults = [
    'site_name' => SITE_COMPANY_BLOG,
    'site_description' => 'Premium articles, updates, and insights from the ' . SITE_COMPANY_NAME . ' team.',
    'contact_email' => 'admin@snfteam.local',
    'posts_per_page' => '9',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!blog_verify_csrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Security token expired. Please refresh and try again.';
    } else {
        $siteName = trim((string) ($_POST['site_name'] ?? ''));
        $siteDescription = trim((string) ($_POST['site_description'] ?? ''));
        $contactEmail = trim((string) ($_POST['contact_email'] ?? ''));
        $postsPerPage = (int) ($_POST['posts_per_page'] ?? 9);

        if ($siteName === '') {
            $errors[] = 'Site name is required.';
        }
        if ($siteDescription === '') {
            $errors[] = 'Site description is required.';
        }
        if (!filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Contact email is invalid.';
        }
        if ($postsPerPage < 1 || $postsPerPage > 24) {
            $errors[] = 'Posts per page must be between 1 and 24.';
        }

        if (!$errors) {
            blog_setting_set('site_name', $siteName);
            blog_setting_set('site_description', $siteDescription);
            blog_setting_set('contact_email', $contactEmail);
            blog_setting_set('posts_per_page', (string) $postsPerPage);
            blog_flash('success', 'SEO settings saved successfully.');
            header('Location: settings.php');
            exit;
        }
    }
}

$settings = blog_settings_map($defaults);

include __DIR__ . '/includes/header.php';
?>
<div class="admin-form-card">
  <div class="admin-section-head">
    <div>
      <strong>Global SEO Settings</strong>
      <p class="admin-muted mb-0">Store sitewide values used by the public blog experience.</p>
    </div>
  </div>

  <?php if ($errors) : ?>
    <div class="alert alert-danger">
      <?php foreach ($errors as $error) : ?>
        <div><?php echo blog_escape($error); ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="post" class="row g-3">
    <input type="hidden" name="csrf_token" value="<?php echo blog_escape(blog_csrf_token()); ?>">
    <div class="col-md-6">
      <label class="form-label">Site Name</label>
      <input type="text" class="form-control" name="site_name" value="<?php echo blog_escape($settings['site_name'] ?? $defaults['site_name']); ?>" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Contact Email</label>
      <input type="email" class="form-control" name="contact_email" value="<?php echo blog_escape($settings['contact_email'] ?? $defaults['contact_email']); ?>" required>
    </div>
    <div class="col-12">
      <label class="form-label">Site Description</label>
      <textarea class="form-control" name="site_description" rows="3" required><?php echo blog_escape($settings['site_description'] ?? $defaults['site_description']); ?></textarea>
    </div>
    <div class="col-md-4">
      <label class="form-label">Posts Per Page</label>
      <input type="number" class="form-control" name="posts_per_page" value="<?php echo blog_escape($settings['posts_per_page'] ?? $defaults['posts_per_page']); ?>" min="1" max="24">
    </div>
    <div class="col-12">
      <button class="btn btn-primary" type="submit"><i class="ri-save-3-line"></i> Save SEO Settings</button>
    </div>
  </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
