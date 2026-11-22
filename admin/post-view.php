<?php
header('Location: dashboard.php');
exit;

require_once __DIR__ . '/../includes/blog_app.php';
blog_require_login();

$postId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$post = $postId ? blog_get_post($postId) : null;

if (!$post) {
    blog_flash('danger', 'Blog post not found.');
    header('Location: posts.php');
    exit;
}

$pageTitle = 'View Blog';
$pageSubtitle = 'Preview post content and SEO metadata.';
$activeMenu = 'posts';

include __DIR__ . '/includes/header.php';
?>
<div class="admin-panel-card">
  <div class="admin-section-head">
    <div>
      <strong><?php echo blog_escape($post['title']); ?></strong>
      <p class="admin-muted mb-0"><?php echo blog_escape($post['slug']); ?></p>
    </div>
    <div class="admin-toolbar">
      <a class="btn btn-outline-secondary" href="posts.php"><i class="ri-arrow-left-line"></i> Back</a>
      <a class="btn btn-primary" href="post-form.php?id=<?php echo (int) $post['id']; ?>"><i class="ri-pencil-line"></i> Edit Post</a>
      <a class="btn btn-outline-primary" href="<?php echo blog_escape(blog_url('blog_details.php?slug=' . urlencode($post['slug']))); ?>" target="_blank" rel="noopener"><i class="ri-external-link-line"></i> Live Preview</a>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-lg-8">
      <div class="admin-panel-card mb-4">
        <div class="d-flex flex-wrap gap-2 mb-3">
          <span class="status-pill <?php echo blog_escape($post['status']); ?>"><?php echo blog_escape(ucfirst($post['status'])); ?></span>
          <span class="badge-soft badge-soft-info"><?php echo blog_escape($post['category_name'] ?? 'Uncategorized'); ?></span>
        </div>
        <?php if (!empty($post['featured_image'])) : ?>
          <img src="<?php echo blog_escape(blog_admin_media_url($post['featured_image'])); ?>" class="preview-image mb-4" alt="Featured image">
        <?php endif; ?>
        <h2 class="h4 mb-3"><?php echo blog_escape($post['excerpt']); ?></h2>
        <div class="admin-prose">
          <?php echo $post['content']; ?>
        </div>
      </div>

      <?php if (!empty($post['images'])) : ?>
        <div class="admin-panel-card">
          <strong class="d-block mb-3">Gallery</strong>
          <div class="admin-media-grid">
            <?php foreach ($post['images'] as $image) : ?>
              <div class="admin-media-card">
                <img src="<?php echo blog_escape(blog_admin_media_url($image['image_path'])); ?>" alt="Gallery image">
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <div class="col-lg-4">
      <div class="admin-panel-card mb-4">
        <strong class="d-block mb-3">SEO Preview</strong>
        <div class="mb-3">
          <small class="admin-muted d-block">SEO Title</small>
          <strong><?php echo blog_escape($post['seo_title'] ?: $post['title']); ?></strong>
        </div>
        <div class="mb-3">
          <small class="admin-muted d-block">Meta Description</small>
          <p class="mb-0"><?php echo blog_escape($post['meta_description'] ?: $post['excerpt']); ?></p>
        </div>
        <div class="mb-3">
          <small class="admin-muted d-block">Canonical URL</small>
          <p class="mb-0"><?php echo blog_escape($post['canonical_url'] ?: '-'); ?></p>
        </div>
        <div class="mb-3">
          <small class="admin-muted d-block">Tags</small>
          <p class="mb-0"><?php echo blog_escape(implode(', ', array_map(static fn ($tag) => $tag['name'], $post['tags'])) ?: '-'); ?></p>
        </div>
      </div>

      <div class="admin-panel-card">
        <strong class="d-block mb-3">Post Details</strong>
        <div class="mb-2"><small class="admin-muted d-block">Author</small><div><?php echo blog_escape($post['author_name'] ?? 'Admin'); ?></div></div>
        <div class="mb-2"><small class="admin-muted d-block">Created</small><div><?php echo blog_escape($post['created_at']); ?></div></div>
        <div class="mb-2"><small class="admin-muted d-block">Updated</small><div><?php echo blog_escape($post['updated_at']); ?></div></div>
        <div class="mb-2"><small class="admin-muted d-block">Published</small><div><?php echo blog_escape($post['published_at'] ?: '-'); ?></div></div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
