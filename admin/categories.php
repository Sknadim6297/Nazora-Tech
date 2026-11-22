<?php
header('Location: dashboard.php');
exit;

require_once __DIR__ . '/../includes/blog_app.php';
blog_require_login();

$pdo = blog_db();
$pageTitle = 'Categories';
$pageSubtitle = 'Organize posts into clear topic groups.';
$activeMenu = 'categories';
$errors = [];
$editingCategory = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!blog_verify_csrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Security token expired. Please refresh and try again.';
    } elseif (isset($_POST['delete_category_id'])) {
        blog_delete_category($pdo, (int) $_POST['delete_category_id']);
        blog_flash('success', 'Category moved to trash.');
        header('Location: categories.php');
        exit;
    } else {
        $categoryId = isset($_POST['category_id']) ? (int) $_POST['category_id'] : null;
        $result = blog_save_category($pdo, $_POST, $categoryId ?: null);
        if ($result['success']) {
            blog_flash('success', $categoryId ? 'Category updated successfully.' : 'Category created successfully.');
            header('Location: categories.php');
            exit;
        }
        $errors = $result['errors'];
    }
}

if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = :id AND deleted_at IS NULL LIMIT 1');
    $stmt->execute([':id' => (int) $_GET['edit']]);
    $editingCategory = $stmt->fetch() ?: null;
}

$categories = blog_category_list();
include __DIR__ . '/includes/header.php';
?>
<div class="admin-form-card mb-4">
  <div class="admin-section-head">
    <div>
      <strong><?php echo $editingCategory ? 'Edit Category' : 'Add Category'; ?></strong>
      <p class="admin-muted mb-0">Organize posts into clear topic groups.</p>
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
    <?php if ($editingCategory) : ?>
      <input type="hidden" name="category_id" value="<?php echo (int) $editingCategory['id']; ?>">
    <?php endif; ?>
    <div class="col-md-4">
      <label class="form-label">Category Name</label>
      <input type="text" class="form-control" name="name" value="<?php echo blog_escape($editingCategory['name'] ?? ''); ?>" required>
    </div>
    <div class="col-md-4">
      <label class="form-label">Slug</label>
      <input type="text" class="form-control" name="slug" value="<?php echo blog_escape($editingCategory['slug'] ?? ''); ?>">
    </div>
    <div class="col-md-4">
      <label class="form-label">Description</label>
      <input type="text" class="form-control" name="description" value="<?php echo blog_escape($editingCategory['description'] ?? ''); ?>">
    </div>
    <div class="col-12 d-flex gap-2">
      <button class="btn btn-primary" type="submit"><i class="ri-save-3-line"></i> <?php echo $editingCategory ? 'Update Category' : 'Save Category'; ?></button>
      <?php if ($editingCategory) : ?><a class="btn btn-outline-secondary" href="categories.php"><i class="ri-close-line"></i> Cancel</a><?php endif; ?>
    </div>
  </form>
</div>

<div class="admin-table-card">
  <strong class="d-block mb-3">Category Library</strong>
  <div class="table-responsive">
    <table class="table admin-table align-middle">
      <thead><tr><th>Name</th><th>Slug</th><th>Posts</th><th>Updated</th><th class="text-end">Actions</th></tr></thead>
      <tbody>
        <?php foreach ($categories as $category) : ?>
          <tr>
            <td><?php echo blog_escape($category['name']); ?></td>
            <td><small><?php echo blog_escape($category['slug']); ?></small></td>
            <td><?php echo (int) $category['post_count']; ?></td>
            <td><?php echo blog_escape($category['updated_at']); ?></td>
            <td class="text-end">
              <div class="admin-actions">
                <a class="btn-action btn-action-edit" href="?edit=<?php echo (int) $category['id']; ?>" title="Edit category"><i class="ri-pencil-line"></i><span class="action-label">Edit</span></a>
                <form method="post" onsubmit="return confirm('Move this category to trash?');">
                  <input type="hidden" name="csrf_token" value="<?php echo blog_escape(blog_csrf_token()); ?>">
                  <input type="hidden" name="delete_category_id" value="<?php echo (int) $category['id']; ?>">
                  <button type="submit" class="btn-action btn-action-delete" title="Delete category"><i class="ri-delete-bin-line"></i><span class="action-label">Delete</span></button>
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
