<?php
header('Location: dashboard.php');
exit;

require_once __DIR__ . '/../includes/blog_app.php';
blog_require_login();

$pdo = blog_db();
$pageTitle = 'Tags';
$pageSubtitle = 'Create reusable labels for filtering and SEO.';
$activeMenu = 'tags';
$errors = [];
$editingTag = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!blog_verify_csrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Security token expired. Please refresh and try again.';
    } elseif (isset($_POST['delete_tag_id'])) {
        blog_delete_tag($pdo, (int) $_POST['delete_tag_id']);
        blog_flash('success', 'Tag moved to trash.');
        header('Location: tags.php');
        exit;
    } else {
        $tagId = isset($_POST['tag_id']) ? (int) $_POST['tag_id'] : null;
        $result = blog_save_tag($pdo, $_POST, $tagId ?: null);
        if ($result['success']) {
            blog_flash('success', $tagId ? 'Tag updated successfully.' : 'Tag created successfully.');
            header('Location: tags.php');
            exit;
        }
        $errors = $result['errors'];
    }
}

if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM tags WHERE id = :id AND deleted_at IS NULL LIMIT 1');
    $stmt->execute([':id' => (int) $_GET['edit']]);
    $editingTag = $stmt->fetch() ?: null;
}

$tags = blog_tag_list();
include __DIR__ . '/includes/header.php';
?>
<div class="admin-form-card mb-4">
  <div class="admin-section-head">
    <div>
      <strong><?php echo $editingTag ? 'Edit Tag' : 'Add Tag'; ?></strong>
      <p class="admin-muted mb-0">Create reusable metadata labels for posts.</p>
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
    <?php if ($editingTag) : ?>
      <input type="hidden" name="tag_id" value="<?php echo (int) $editingTag['id']; ?>">
    <?php endif; ?>
    <div class="col-md-4">
      <label class="form-label">Tag Name</label>
      <input type="text" class="form-control" name="name" value="<?php echo blog_escape($editingTag['name'] ?? ''); ?>" required>
    </div>
    <div class="col-md-4">
      <label class="form-label">Slug</label>
      <input type="text" class="form-control" name="slug" value="<?php echo blog_escape($editingTag['slug'] ?? ''); ?>">
    </div>
    <div class="col-md-4 d-flex align-items-end gap-2">
      <button class="btn btn-primary" type="submit"><i class="ri-save-3-line"></i> <?php echo $editingTag ? 'Update Tag' : 'Save Tag'; ?></button>
      <?php if ($editingTag) : ?><a class="btn btn-outline-secondary" href="tags.php"><i class="ri-close-line"></i> Cancel</a><?php endif; ?>
    </div>
  </form>
</div>

<div class="admin-table-card">
  <strong class="d-block mb-3">Tag Library</strong>
  <div class="table-responsive">
    <table class="table admin-table align-middle">
      <thead><tr><th>Name</th><th>Slug</th><th>Posts</th><th>Updated</th><th class="text-end">Actions</th></tr></thead>
      <tbody>
        <?php foreach ($tags as $tag) : ?>
          <tr>
            <td><?php echo blog_escape($tag['name']); ?></td>
            <td><small><?php echo blog_escape($tag['slug']); ?></small></td>
            <td><?php echo (int) $tag['post_count']; ?></td>
            <td><?php echo blog_escape($tag['updated_at']); ?></td>
            <td class="text-end">
              <div class="admin-actions">
                <a class="btn-action btn-action-edit" href="?edit=<?php echo (int) $tag['id']; ?>" title="Edit tag"><i class="ri-pencil-line"></i><span class="action-label">Edit</span></a>
                <form method="post" onsubmit="return confirm('Move this tag to trash?');">
                  <input type="hidden" name="csrf_token" value="<?php echo blog_escape(blog_csrf_token()); ?>">
                  <input type="hidden" name="delete_tag_id" value="<?php echo (int) $tag['id']; ?>">
                  <button type="submit" class="btn-action btn-action-delete" title="Delete tag"><i class="ri-delete-bin-line"></i><span class="action-label">Delete</span></button>
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
