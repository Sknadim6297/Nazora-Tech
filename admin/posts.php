<?php
header('Location: dashboard.php');
exit;

require_once __DIR__ . '/../includes/blog_app.php';
blog_require_login();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post_id'])) {
    if (!blog_verify_csrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Security token expired. Please refresh and try again.';
    } else {
        blog_soft_delete_post((int) $_POST['delete_post_id']);
        blog_flash('success', 'Blog post moved to trash.');
        header('Location: posts.php');
        exit;
    }
}

$filters = [
    'search' => $_GET['search'] ?? '',
    'status' => $_GET['status'] ?? '',
    'category_id' => (int) ($_GET['category_id'] ?? 0),
    'page' => (int) ($_GET['page'] ?? 1),
];

$postsResult = blog_get_posts_list($filters, 10);
$categories = blog_category_list();
$pageTitle = 'Blog Posts';
$pageSubtitle = 'Search, filter, create, edit, and publish your articles.';
$activeMenu = 'posts';

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
      <strong>Blog Posts</strong>
      <p class="admin-muted mb-0">Search, filter, edit, and publish content.</p>
    </div>
    <a class="btn btn-primary" href="post-form.php"><i class="ri-add-line"></i> Add Blog</a>
  </div>

  <form class="admin-filter-bar mb-4" method="get">
    <input class="form-control" type="search" name="search" value="<?php echo blog_escape($filters['search']); ?>" placeholder="Search title or slug">
    <select class="form-select" name="status">
      <option value="">All Status</option>
      <option value="draft" <?php echo $filters['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
      <option value="published" <?php echo $filters['status'] === 'published' ? 'selected' : ''; ?>>Published</option>
    </select>
    <select class="form-select" name="category_id">
      <option value="0">All Categories</option>
      <?php foreach ($categories as $category) : ?>
        <option value="<?php echo (int) $category['id']; ?>" <?php echo $filters['category_id'] === (int) $category['id'] ? 'selected' : ''; ?>><?php echo blog_escape($category['name']); ?></option>
      <?php endforeach; ?>
    </select>
    <button class="btn btn-outline-primary" type="submit"><i class="ri-filter-3-line"></i> Filter</button>
  </form>

  <div class="table-responsive">
    <table class="table admin-table align-middle">
      <thead>
        <tr>
          <th>Title</th>
          <th>SEO / Slug</th>
          <th>Category</th>
          <th>Status</th>
          <th>Tags</th>
          <th>Updated</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$postsResult['items']) : ?>
          <tr>
            <td colspan="7">
              <div class="admin-empty">
                <i class="ri-article-line"></i>
                <strong>No blog posts yet</strong>
                <p class="mb-3">Create your first article to get started.</p>
                <a class="btn btn-primary" href="post-form.php"><i class="ri-add-line"></i> Add Blog</a>
              </div>
            </td>
          </tr>
        <?php endif; ?>
        <?php foreach ($postsResult['items'] as $post) : ?>
          <tr>
            <td>
              <strong><?php echo blog_escape($post['title']); ?></strong><br>
              <small><?php echo blog_escape(mb_strimwidth($post['excerpt'], 0, 72, '...')); ?></small>
            </td>
            <td><small><?php echo blog_escape($post['slug']); ?></small></td>
            <td><?php echo blog_escape($post['category_name'] ?? 'Uncategorized'); ?></td>
            <td><span class="status-pill <?php echo blog_escape($post['status']); ?>"><?php echo blog_escape(ucfirst($post['status'])); ?></span></td>
            <td><small><?php echo blog_escape($post['tag_names'] ?? '-'); ?></small></td>
            <td><?php echo blog_escape(date('d M Y', strtotime($post['updated_at']))); ?></td>
            <td class="text-end">
              <div class="admin-actions">
                <a class="btn-action btn-action-view" href="post-view.php?id=<?php echo (int) $post['id']; ?>" title="View post"><i class="ri-eye-line"></i><span class="action-label">View</span></a>
                <a class="btn-action btn-action-edit" href="post-form.php?id=<?php echo (int) $post['id']; ?>" title="Edit post"><i class="ri-pencil-line"></i><span class="action-label">Edit</span></a>
                <form method="post" onsubmit="return confirm('Move this post to trash?');">
                  <input type="hidden" name="csrf_token" value="<?php echo blog_escape(blog_csrf_token()); ?>">
                  <input type="hidden" name="delete_post_id" value="<?php echo (int) $post['id']; ?>">
                  <button type="submit" class="btn-action btn-action-delete" title="Delete post"><i class="ri-delete-bin-line"></i><span class="action-label">Delete</span></button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <nav class="mt-4">
    <ul class="pagination admin-pagination flex-wrap">
      <?php for ($page = 1; $page <= $postsResult['pages']; $page++) : ?>
        <li class="page-item <?php echo $page === $postsResult['page'] ? 'active' : ''; ?>">
          <a class="page-link" href="?<?php echo http_build_query(array_merge($filters, ['page' => $page])); ?>"><?php echo $page; ?></a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
