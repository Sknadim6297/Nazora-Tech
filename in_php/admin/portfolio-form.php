<?php

require_once __DIR__ . '/../includes/portfolio_app.php';
blog_require_login();

$id = (int) ($_GET['id'] ?? 0);
$project = $id > 0 ? portfolio_get($id) : null;
$errors = [];

if ($id > 0 && !$project) {
    blog_flash('error', 'Project not found.');
    header('Location: portfolio.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!blog_verify_csrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Security token expired. Please refresh and try again.';
    } else {
        $payload = [
            'title' => $_POST['title'] ?? '',
            'slug' => $_POST['slug'] ?? '',
            'category' => $_POST['category'] ?? 'Website',
            'summary' => $_POST['summary'] ?? '',
            'overview' => $_POST['overview'] ?? '',
            'challenge' => $_POST['challenge'] ?? '',
            'result' => $_POST['result'] ?? '',
            'features' => $_POST['features'] ?? '',
            'client_name' => $_POST['client_name'] ?? '',
            'project_date' => $_POST['project_date'] ?? '',
            'live_url' => $_POST['live_url'] ?? '',
            'status' => $_POST['status'] ?? 'published',
            'sort_order' => $_POST['sort_order'] ?? 0,
        ];
        $result = portfolio_save($payload, $id > 0 ? $id : null, $_FILES['image'] ?? null);
        if ($result['success']) {
            blog_flash('success', $id > 0 ? 'Project updated.' : 'Project created.');
            header('Location: portfolio.php');
            exit;
        }
        $errors = $result['errors'] ?? ['Could not save project.'];
        $project = array_merge($project ?: [], $payload);
    }
}

$pageTitle = $id ? 'Edit project' : 'Add project';
$pageSubtitle = 'This project appears on the public portfolio when it is published.';
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
      <strong><?php echo blog_escape($pageTitle); ?></strong>
      <p class="admin-muted mb-0">Write the case study visitors will read on the detail page.</p>
    </div>
    <a class="btn btn-outline-primary" href="portfolio.php"><i class="ri-arrow-left-line"></i> Back</a>
  </div>

  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo blog_escape(blog_csrf_token()); ?>">

    <div class="row g-3 mb-3">
      <div class="col-md-8">
        <label class="form-label">Title</label>
        <input class="form-control" type="text" name="title" required value="<?php echo blog_escape($project['title'] ?? ''); ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">Category</label>
        <select class="form-select" name="category">
          <?php foreach (portfolio_categories() as $category) : ?>
            <option value="<?php echo blog_escape($category); ?>" <?php echo ($project['category'] ?? 'Website') === $category ? 'selected' : ''; ?>><?php echo blog_escape($category); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label">Short summary</label>
      <input class="form-control" type="text" name="summary" required value="<?php echo blog_escape($project['summary'] ?? ''); ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Overview</label>
      <textarea class="form-control" name="overview" rows="4" required><?php echo blog_escape($project['overview'] ?? ''); ?></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Challenge</label>
      <textarea class="form-control" name="challenge" rows="3"><?php echo blog_escape($project['challenge'] ?? ''); ?></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Result</label>
      <textarea class="form-control" name="result" rows="3"><?php echo blog_escape($project['result'] ?? ''); ?></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Features</label>
      <textarea class="form-control" name="features" rows="5" placeholder="One feature per line"><?php echo blog_escape($project['features'] ?? ''); ?></textarea>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-md-4">
        <label class="form-label">Client</label>
        <input class="form-control" type="text" name="client_name" value="<?php echo blog_escape($project['client_name'] ?? ''); ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">Date</label>
        <input class="form-control" type="text" name="project_date" value="<?php echo blog_escape($project['project_date'] ?? ''); ?>" placeholder="2025">
      </div>
      <div class="col-md-4">
        <label class="form-label">Sort order</label>
        <input class="form-control" type="number" name="sort_order" value="<?php echo (int) ($project['sort_order'] ?? 0); ?>">
      </div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label class="form-label">Live URL</label>
        <input class="form-control" type="url" name="live_url" value="<?php echo blog_escape($project['live_url'] ?? ''); ?>" placeholder="https://">
      </div>
      <div class="col-md-3">
        <label class="form-label">Slug</label>
        <input class="form-control" type="text" name="slug" value="<?php echo blog_escape($project['slug'] ?? ''); ?>" placeholder="auto from title">
      </div>
      <div class="col-md-3">
        <label class="form-label">Status</label>
        <select class="form-select" name="status">
          <option value="published" <?php echo ($project['status'] ?? 'published') === 'published' ? 'selected' : ''; ?>>Published</option>
          <option value="draft" <?php echo ($project['status'] ?? '') === 'draft' ? 'selected' : ''; ?>>Draft</option>
        </select>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label">Cover image</label>
      <input class="form-control" type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif">
      <?php if (!empty($project['image'])) : ?>
        <img src="../<?php echo blog_escape($project['image']); ?>" alt="" style="margin-top:12px;width:160px;height:120px;object-fit:cover;border-radius:10px;">
      <?php endif; ?>
    </div>

    <div class="d-flex gap-2">
      <button class="btn btn-primary" type="submit"><i class="ri-save-line"></i> Save project</button>
      <a class="btn btn-outline-secondary" href="portfolio.php">Cancel</a>
    </div>
  </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
