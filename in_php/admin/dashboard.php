<?php
require_once __DIR__ . '/../includes/blog_app.php';
require_once __DIR__ . '/../includes/careers_app.php';
require_once __DIR__ . '/../includes/portfolio_app.php';
blog_require_login();

$pageTitle = 'Dashboard';
$pageSubtitle = 'Overview of careers and portfolio.';
$activeMenu = 'dashboard';
$openRoles = careers_count_active();
$projectCount = portfolio_count_published();
$jobs = careers_list();
$userName = blog_current_user()['name'] ?? 'Admin';

include __DIR__ . '/includes/header.php';
?>

<section class="admin-welcome">
  <div>
    <h2>Welcome back, <?php echo blog_escape(explode(' ', $userName)[0]); ?>!</h2>
    <p>Manage career openings from one place.</p>
  </div>
  <div class="admin-welcome-actions">
    <a href="careers.php" class="btn-ghost"><i class="ri-briefcase-4-line"></i> Careers</a>
    <a href="portfolio.php" class="btn-ghost"><i class="ri-gallery-line"></i> Portfolio</a>
    <a href="career-form.php" class="btn-white"><i class="ri-add-line"></i> Add Opening</a>
  </div>
</section>

<div class="admin-stats">
  <div class="admin-stat">
    <div class="admin-stat-body">
      <span>Open Roles</span>
      <strong><?php echo (int) $openRoles; ?></strong>
    </div>
    <div class="admin-stat-icon admin-stat-icon--amber"><i class="ri-briefcase-4-line"></i></div>
  </div>
  <div class="admin-stat">
    <div class="admin-stat-body">
      <span>All Listings</span>
      <strong><?php echo count($jobs); ?></strong>
    </div>
    <div class="admin-stat-icon admin-stat-icon--blue"><i class="ri-list-check-2"></i></div>
  </div>
  <div class="admin-stat">
    <div class="admin-stat-body">
      <span>Projects</span>
      <strong><?php echo (int) $projectCount; ?></strong>
    </div>
    <div class="admin-stat-icon admin-stat-icon--amber"><i class="ri-gallery-line"></i></div>
  </div>
</div>

<div class="admin-card">
  <div class="admin-card-head">
    <div>
      <h3><i class="ri-briefcase-4-line"></i> Career openings</h3>
      <p>Roles shown on the public Careers page</p>
    </div>
    <a class="btn btn-primary btn-sm" href="career-form.php"><i class="ri-add-line"></i> Add opening</a>
  </div>
  <div class="table-responsive">
    <table class="table admin-table mb-0">
      <thead>
        <tr>
          <th>Title</th>
          <th>Type</th>
          <th>Status</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$jobs) : ?>
          <tr>
            <td colspan="4">
              <div class="admin-empty">
                <i class="ri-briefcase-4-line"></i>
                <strong>No openings yet</strong>
                <p class="admin-muted mb-3">Add a role to show it on the Careers page.</p>
                <a class="btn btn-primary" href="career-form.php"><i class="ri-add-line"></i> Add opening</a>
              </div>
            </td>
          </tr>
        <?php endif; ?>
        <?php foreach (array_slice($jobs, 0, 6) as $job) : ?>
          <tr>
            <td><strong><?php echo blog_escape($job['title']); ?></strong></td>
            <td><?php echo blog_escape($job['employment_type']); ?></td>
            <td><span class="status-pill <?php echo blog_escape($job['status']); ?>"><?php echo blog_escape(ucfirst($job['status'])); ?></span></td>
            <td class="text-end">
              <a class="btn-action btn-action-edit" href="career-form.php?id=<?php echo (int) $job['id']; ?>" title="Edit"><i class="ri-pencil-line"></i><span class="action-label">Edit</span></a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
