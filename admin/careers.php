<?php

require_once __DIR__ . '/../includes/careers_app.php';
blog_require_login();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_career_id'])) {
    if (!blog_verify_csrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Security token expired. Please refresh and try again.';
    } else {
        careers_soft_delete((int) $_POST['delete_career_id']);
        blog_flash('success', 'Career opening removed.');
        header('Location: careers.php');
        exit;
    }
}

$filters = [
    'search' => trim((string) ($_GET['search'] ?? '')),
    'status' => trim((string) ($_GET['status'] ?? '')),
];

$jobs = careers_list($filters);
$pageTitle = 'Careers';
$pageSubtitle = 'Manage open roles shown on the public Careers page.';
$activeMenu = 'careers';

include __DIR__ . '/includes/header.php';
?>
<?php if ($errors) : ?>
  <div class="alert alert-danger admin-alert">
    <?php foreach ($errors as $error) : ?>
      <div><?php echo blog_escape($error); ?></div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<div class="admin-table-card">
  <div class="admin-section-head">
    <div>
      <strong>Career openings</strong>
      <p class="admin-muted mb-0">Publish, edit, or hide roles for the Careers page.</p>
    </div>
    <a class="btn btn-primary" href="career-form.php"><i class="ri-add-line"></i> Add opening</a>
  </div>

  <form class="admin-filter-bar mb-4" method="get">
    <input class="form-control" type="search" name="search" value="<?php echo blog_escape($filters['search']); ?>" placeholder="Search title or location">
    <select class="form-select" name="status">
      <option value="">All Status</option>
      <option value="draft" <?php echo $filters['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
      <option value="published" <?php echo $filters['status'] === 'published' ? 'selected' : ''; ?>>Published</option>
    </select>
    <button class="btn btn-outline-primary" type="submit"><i class="ri-filter-3-line"></i> Filter</button>
  </form>

  <div class="table-responsive">
    <table class="table admin-table align-middle">
      <thead>
        <tr>
          <th>Title</th>
          <th>Type</th>
          <th>Location</th>
          <th>Status</th>
          <th>Order</th>
          <th>Updated</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$jobs) : ?>
          <tr>
            <td colspan="7">
              <div class="admin-empty">
                <i class="ri-briefcase-4-line"></i>
                <strong>No openings yet</strong>
                <p class="mb-3">Add your first role to show on the Careers page.</p>
                <a class="btn btn-primary" href="career-form.php"><i class="ri-add-line"></i> Add opening</a>
              </div>
            </td>
          </tr>
        <?php endif; ?>
        <?php foreach ($jobs as $job) : ?>
          <tr>
            <td>
              <strong><?php echo blog_escape($job['title']); ?></strong>
              <?php if (!empty($job['experience'])) : ?>
                <br><small><?php echo blog_escape($job['experience']); ?></small>
              <?php endif; ?>
            </td>
            <td><?php echo blog_escape($job['employment_type']); ?></td>
            <td><?php echo blog_escape($job['location']); ?></td>
            <td><span class="status-pill <?php echo blog_escape($job['status']); ?>"><?php echo blog_escape(ucfirst($job['status'])); ?></span></td>
            <td><?php echo (int) $job['sort_order']; ?></td>
            <td><?php echo blog_escape(date('d M Y', strtotime($job['updated_at']))); ?></td>
            <td class="text-end">
              <div class="admin-actions">
                <a class="btn-action btn-action-edit" href="career-form.php?id=<?php echo (int) $job['id']; ?>" title="Edit"><i class="ri-pencil-line"></i><span class="action-label">Edit</span></a>
                <form method="post" onsubmit="return confirm('Remove this opening?');">
                  <input type="hidden" name="csrf_token" value="<?php echo blog_escape(blog_csrf_token()); ?>">
                  <input type="hidden" name="delete_career_id" value="<?php echo (int) $job['id']; ?>">
                  <button type="submit" class="btn-action btn-action-delete" title="Delete"><i class="ri-delete-bin-line"></i><span class="action-label">Delete</span></button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
