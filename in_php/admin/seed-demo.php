<?php
header('Location: dashboard.php');
exit;

require_once __DIR__ . '/../includes/blog_app.php';
require_once __DIR__ . '/../includes/blog_seed_demo.php';

$isCli = PHP_SAPI === 'cli';

if (!$isCli) {
    blog_require_login();
}

$result = null;

if ($isCli && in_array('--run', $argv ?? [], true)) {
    $force = in_array('--force', $argv ?? [], true);
    $result = blog_run_demo_seed($force);
    echo ($result['message'] ?? 'Done.') . PHP_EOL;
    if (!empty($result['errors'])) {
        foreach ($result['errors'] as $error) {
            echo 'Error: ' . $error . PHP_EOL;
        }
    }
    exit(empty($result['errors']) ? 0 : 1);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!blog_verify_csrf($_POST['csrf_token'] ?? null)) {
        blog_flash('danger', 'Security token expired. Please try again.');
        header('Location: seed-demo.php');
        exit;
    }

    if (isset($_POST['reset_demo'])) {
        blog_reset_demo_seed();
        blog_flash('success', 'Existing posts were archived. You can seed fresh demo data now.');
        header('Location: seed-demo.php');
        exit;
    }

    $force = isset($_POST['force_seed']);
    $result = blog_run_demo_seed($force);
    blog_flash($result['posts'] > 0 || $result['categories'] > 0 ? 'success' : 'danger', $result['message'] ?? 'Seed completed.');
    header('Location: dashboard.php');
    exit;
}

$pageTitle = 'Demo Data';
$pageSubtitle = 'Populate categories, tags, and sample blog posts for testing.';
$activeMenu = 'settings';

include __DIR__ . '/includes/header.php';
?>
<div class="admin-card">
  <div class="admin-card-head">
    <div>
      <h3><i class="ri-database-2-line"></i> Blog demo seed</h3>
      <p>Create 7 categories, tags, and 21 published posts with SEO and images.</p>
    </div>
  </div>
  <div class="admin-card-body">
    <p class="admin-muted">Use this once to fill the admin panel and frontend for testing search, filters, pagination, and CRUD.</p>
    <form method="post" class="d-flex flex-wrap gap-2 mt-3">
      <input type="hidden" name="csrf_token" value="<?php echo blog_escape(blog_csrf_token()); ?>">
      <button type="submit" name="run_seed" value="1" class="btn btn-primary"><i class="ri-download-cloud-2-line"></i> Seed demo data</button>
      <button type="submit" name="force_seed" value="1" class="btn btn-outline-primary"><i class="ri-refresh-line"></i> Force reseed (skip duplicate slugs)</button>
    </form>
    <form method="post" class="mt-3" onsubmit="return confirm('Archive all current posts?');">
      <input type="hidden" name="csrf_token" value="<?php echo blog_escape(blog_csrf_token()); ?>">
      <button type="submit" name="reset_demo" value="1" class="btn btn-outline-danger"><i class="ri-delete-bin-line"></i> Archive all posts</button>
    </form>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
