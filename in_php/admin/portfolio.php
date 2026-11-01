<?php

require_once __DIR__ . '/../includes/portfolio_app.php';
blog_require_login();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_project_id'])) {
    if (!blog_verify_csrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Security token expired. Please refresh and try again.';
    } else {
        portfolio_soft_delete((int) $_POST['delete_project_id']);
        blog_flash('success', 'Project removed from the portfolio.');
        header('Location: portfolio.php');
        exit;
    }
}

$filters = [
    'search' => trim((string) ($_GET['search'] ?? '')),
    'status' => trim((string) ($_GET['status'] ?? '')),
    'category' => trim((string) ($_GET['category'] ?? '')),
];

$projects = portfolio_list($filters);
$pageTitle = 'Portfolio';
$pageSubtitle = 'Add, edit, or hide projects on the public portfolio page.';
$activeMenu = 'portfolio';

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
      <strong>Projects</strong>
      <p class="admin-muted mb-0">Published projects appear on the website portfolio and detail pages.</p>
    </div>
    <a class="btn btn-primary" href="portfolio-form.php"><i class="ri-add-line"></i> Add project</a>
  </div>

  <form class="admin-filter-bar mb-4" method="get">
    <input class="form-control" type="search" name="search" value="<?php echo blog_escape($filters['search']); ?>" placeholder="Search title or client">
    <select class="form-select" name="category">
      <option value="">All categories</option>
      <?php foreach (portfolio_categories() as $category) : ?>
        <option value="<?php echo blog_escape($category); ?>" <?php echo $filters['category'] === $category ? 'selected' : ''; ?>><?php echo blog_escape($category); ?></option>
      <?php endforeach; ?>
    </select>
    <select class="form-select" name="status">
      <option value="">All status</option>
      <option value="published" <?php echo $filters['status'] === 'published' ? 'selected' : ''; ?>>Published</option>
      <option value="draft" <?php echo $filters['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
    </select>
    <button class="btn btn-outline-primary" type="submit"><i class="ri-filter-3-line"></i> Filter</button>
  </form>

  <div class="table-responsive">
    <table class="table admin-table align-middle">
      <thead>
        <tr>
          <th>Project</th>
          <th>Category</th>
          <th>Status</th>
          <th>Order</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$projects) : ?>
          <tr>
            <td colspan="5">
              <div class="admin-empty">
                <i class="ri-gallery-line"></i>
                <strong>No projects yet</strong>
                <p class="mb-3">Add a project to show it on the portfolio page.</p>
                <a class="btn btn-primary" href="portfolio-form.php"><i class="ri-add-line"></i> Add project</a>
              </div>
            </td>
          </tr>
        <?php endif; ?>
        <?php foreach ($projects as $project) : ?>
          <tr>
            <td>
              <strong><?php echo blog_escape($project['title']); ?></strong>
              <?php if (!empty($project['client_name'])) : ?>
                <br><small><?php echo blog_escape($project['client_name']); ?></small>
              <?php endif; ?>
            </td>
            <td><?php echo blog_escape($project['category']); ?></td>
            <td><span class="status-pill <?php echo blog_escape($project['status']); ?>"><?php echo blog_escape(ucfirst($project['status'])); ?></span></td>
            <td><?php echo (int) $project['sort_order']; ?></td>
            <td class="text-end">
              <div class="admin-actions">
                <a class="btn-action btn-action-view" href="../portfolio_details?slug=<?php echo blog_escape($project['slug']); ?>" target="_blank" rel="noopener" title="View"><i class="ri-eye-line"></i><span class="action-label">View</span></a>
                <a class="btn-action btn-action-edit" href="portfolio-form.php?id=<?php echo (int) $project['id']; ?>" title="Edit"><i class="ri-pencil-line"></i><span class="action-label">Edit</span></a>
                <form method="post" onsubmit="return confirm('Remove this project?');">
                  <input type="hidden" name="csrf_token" value="<?php echo blog_escape(blog_csrf_token()); ?>">
                  <input type="hidden" name="delete_project_id" value="<?php echo (int) $project['id']; ?>">
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
