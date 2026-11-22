<?php

require_once __DIR__ . '/../includes/careers_app.php';
blog_require_login();

$id = (int) ($_GET['id'] ?? 0);
$job = $id > 0 ? careers_get($id) : null;
$errors = [];

if ($id > 0 && !$job) {
    blog_flash('error', 'Career opening not found.');
    header('Location: careers.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!blog_verify_csrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Security token expired. Please refresh and try again.';
    } else {
        $payload = [
            'title' => $_POST['title'] ?? '',
            'employment_type' => $_POST['employment_type'] ?? 'Full-time',
            'location' => $_POST['location'] ?? '',
            'experience' => $_POST['experience'] ?? '',
            'description' => $_POST['description'] ?? '',
            'apply_url' => $_POST['apply_url'] ?? 'contact',
            'status' => $_POST['status'] ?? 'published',
            'sort_order' => $_POST['sort_order'] ?? 0,
        ];
        $result = careers_save($payload, $id > 0 ? $id : null);
        if ($result['success']) {
            blog_flash('success', $id > 0 ? 'Career opening updated.' : 'Career opening created.');
            header('Location: careers.php');
            exit;
        }
        $errors = $result['errors'] ?? ['Could not save opening.'];
        $job = array_merge($job ?: [], $payload);
    }
}

$pageTitle = $id ? 'Edit opening' : 'Add opening';
$pageSubtitle = 'This listing appears on the public Careers page when published.';
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
      <strong><?php echo blog_escape($pageTitle); ?></strong>
      <p class="admin-muted mb-0">Fill in the role details visitors will see.</p>
    </div>
    <a class="btn btn-outline-primary" href="careers.php"><i class="ri-arrow-left-line"></i> Back</a>
  </div>

  <form method="post" class="admin-form-grid">
    <input type="hidden" name="csrf_token" value="<?php echo blog_escape(blog_csrf_token()); ?>">

    <div class="mb-3">
      <label class="form-label">Job title</label>
      <input class="form-control" type="text" name="title" required value="<?php echo blog_escape($job['title'] ?? ''); ?>">
    </div>

    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label class="form-label">Employment type</label>
        <select class="form-select" name="employment_type">
          <?php
          $types = ['Full-time', 'Part-time', 'Internship', 'Contract', 'Remote'];
          $currentType = $job['employment_type'] ?? 'Full-time';
          foreach ($types as $type) :
          ?>
            <option value="<?php echo blog_escape($type); ?>" <?php echo $currentType === $type ? 'selected' : ''; ?>><?php echo blog_escape($type); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Location</label>
        <input class="form-control" type="text" name="location" value="<?php echo blog_escape($job['location'] ?? 'Kolkata / Hybrid'); ?>">
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label">Experience</label>
      <input class="form-control" type="text" name="experience" placeholder="e.g. 2+ years experience" value="<?php echo blog_escape($job['experience'] ?? ''); ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Description</label>
      <textarea class="form-control" name="description" rows="6" required><?php echo blog_escape($job['description'] ?? ''); ?></textarea>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label class="form-label">Apply link</label>
        <input class="form-control" type="text" name="apply_url" value="<?php echo blog_escape($job['apply_url'] ?? 'contact'); ?>" placeholder="contact, mailto:, or URL">
      </div>
      <div class="col-md-3">
        <label class="form-label">Sort order</label>
        <input class="form-control" type="number" name="sort_order" value="<?php echo (int) ($job['sort_order'] ?? 0); ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Status</label>
        <select class="form-select" name="status">
          <option value="published" <?php echo ($job['status'] ?? 'published') === 'published' ? 'selected' : ''; ?>>Published</option>
          <option value="draft" <?php echo ($job['status'] ?? '') === 'draft' ? 'selected' : ''; ?>>Draft</option>
        </select>
      </div>
    </div>

    <div class="d-flex gap-2">
      <button class="btn btn-primary" type="submit"><i class="ri-save-line"></i> Save opening</button>
      <a class="btn btn-outline-secondary" href="careers.php">Cancel</a>
    </div>
  </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
