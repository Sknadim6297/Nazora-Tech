<?php
header('Location: dashboard.php');
exit;

require_once __DIR__ . '/../includes/blog_app.php';
blog_require_login();

$postId = isset($_GET['id']) ? (int) $_GET['id'] : null;
$activeMenu = 'posts';
$includeEditor = true;
$errors = [];
$post = $postId ? blog_get_post($postId) : null;

$formData = [
    'title' => $post['title'] ?? '',
    'slug' => $post['slug'] ?? '',
    'excerpt' => $post['excerpt'] ?? '',
    'content' => $post['content'] ?? '',
    'status' => $post['status'] ?? 'draft',
    'category_id' => $post['category_id'] ?? '',
    'tags' => $post ? implode(', ', array_map(static fn ($tag) => $tag['name'], $post['tags'])) : '',
    'featured_image_alt' => $post['featured_image_alt'] ?? '',
    'seo_title' => $post['seo_title'] ?? '',
    'meta_description' => $post['meta_description'] ?? '',
    'meta_keywords' => $post['meta_keywords'] ?? '',
    'canonical_url' => $post['canonical_url'] ?? '',
    'og_title' => $post['og_title'] ?? '',
    'og_description' => $post['og_description'] ?? '',
    'twitter_card' => $post['twitter_card'] ?? 'summary_large_image',
    'schema_markup' => $post['schema_markup'] ?? '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = (int) ($_POST['post_id'] ?? $_GET['id'] ?? 0) ?: null;
    if (!blog_verify_csrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Security token expired. Please refresh and try again.';
    } else {
        $saveResult = blog_save_post($_POST, $_FILES, $postId);
        if ($saveResult['success']) {
            blog_flash('success', $postId ? 'Blog post updated successfully.' : 'Blog post created successfully.');
            header('Location: post-view.php?id=' . $saveResult['id']);
            exit;
        }
        $errors = $saveResult['errors'];
        $formData = array_merge($formData, $_POST);
    }
}

$pageTitle = $post ? 'Edit Blog' : 'Add Blog';
$pageSubtitle = $post ? 'Update content, media, and SEO for this post.' : 'Create a new SEO-ready blog article.';
$categories = blog_category_list();
$images = $post['images'] ?? [];
$featuredPreview = !empty($post['featured_image']) ? blog_admin_media_url($post['featured_image']) : '';

include __DIR__ . '/includes/header.php';
?>
<div class="admin-form-card">
  <div class="admin-section-head">
    <div>
      <strong><?php echo blog_escape($pageTitle); ?></strong>
      <p class="admin-muted mb-0">Build SEO-ready articles with gallery images and tags.</p>
    </div>
    <a class="btn btn-outline-secondary" href="posts.php"><i class="ri-arrow-left-line"></i> Back to list</a>
  </div>

  <?php if ($errors) : ?>
    <div class="alert alert-danger">
      <?php foreach ($errors as $error) : ?>
        <div><?php echo blog_escape($error); ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data" class="row g-4" data-blog-form novalidate action="<?php echo $postId ? 'post-form.php?id=' . (int) $postId : 'post-form.php'; ?>">
    <input type="hidden" name="csrf_token" value="<?php echo blog_escape(blog_csrf_token()); ?>">
    <?php if ($postId) : ?>
      <input type="hidden" name="post_id" value="<?php echo (int) $postId; ?>">
    <?php endif; ?>
    <div class="col-lg-8">
      <div class="admin-panel-card mb-4">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label">Blog Title</label>
            <input type="text" class="form-control" name="title" value="<?php echo blog_escape($formData['title']); ?>" data-slug-source required>
          </div>
          <div class="col-md-8">
            <label class="form-label">Slug</label>
            <input type="text" class="form-control" name="slug" value="<?php echo blog_escape($formData['slug']); ?>" data-slug-target>
          </div>
          <div class="col-md-4">
            <label class="form-label">Status</label>
            <select class="form-select" name="status" required>
              <option value="draft" <?php echo $formData['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
              <option value="published" <?php echo $formData['status'] === 'published' ? 'selected' : ''; ?>>Published</option>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label">Excerpt</label>
            <textarea class="form-control" name="excerpt" rows="3" required><?php echo blog_escape($formData['excerpt']); ?></textarea>
          </div>
          <div class="col-12 editor-wrap">
            <label class="form-label">Content</label>
            <textarea class="form-control" name="content" id="post-content-field" data-rich-editor data-required-content rows="10"><?php echo blog_escape($formData['content']); ?></textarea>
            <small class="admin-helper">Rich text content is required before saving.</small>
          </div>
        </div>
      </div>

      <div class="admin-panel-card mb-4">
        <strong class="d-block mb-3">SEO Settings</strong>
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label">SEO Title</label>
            <input type="text" class="form-control" name="seo_title" value="<?php echo blog_escape($formData['seo_title']); ?>">
          </div>
          <div class="col-12">
            <label class="form-label">Meta Description</label>
            <textarea class="form-control" name="meta_description" rows="3"><?php echo blog_escape($formData['meta_description']); ?></textarea>
          </div>
          <div class="col-12">
            <label class="form-label">Meta Keywords</label>
            <input type="text" class="form-control" name="meta_keywords" value="<?php echo blog_escape($formData['meta_keywords']); ?>" placeholder="seo, design, performance">
          </div>
          <div class="col-12">
            <label class="form-label">Canonical URL</label>
            <input type="url" class="form-control" name="canonical_url" value="<?php echo blog_escape($formData['canonical_url']); ?>" placeholder="https://example.com/blog/your-post">
          </div>
          <div class="col-md-6">
            <label class="form-label">Open Graph Title</label>
            <input type="text" class="form-control" name="og_title" value="<?php echo blog_escape($formData['og_title']); ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Twitter Card</label>
            <select class="form-select" name="twitter_card">
              <option value="summary" <?php echo $formData['twitter_card'] === 'summary' ? 'selected' : ''; ?>>Summary</option>
              <option value="summary_large_image" <?php echo $formData['twitter_card'] === 'summary_large_image' ? 'selected' : ''; ?>>Summary Large Image</option>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label">Open Graph Description</label>
            <textarea class="form-control" name="og_description" rows="3"><?php echo blog_escape($formData['og_description']); ?></textarea>
          </div>
          <div class="col-12">
            <label class="form-label">Schema Markup</label>
            <textarea class="form-control" name="schema_markup" rows="5" placeholder='{"@context":"https://schema.org","@type":"BlogPosting"}'><?php echo blog_escape($formData['schema_markup']); ?></textarea>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="admin-panel-card mb-4">
        <strong class="d-block mb-3">Publishing</strong>
        <div class="mb-3">
          <label class="form-label">Category</label>
          <select class="form-select" name="category_id">
            <option value="0">Uncategorized</option>
            <?php foreach ($categories as $category) : ?>
              <option value="<?php echo (int) $category['id']; ?>" <?php echo (int) $formData['category_id'] === (int) $category['id'] ? 'selected' : ''; ?>><?php echo blog_escape($category['name']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Tags</label>
          <input type="text" class="form-control" name="tags" value="<?php echo blog_escape($formData['tags']); ?>" placeholder="SEO, UI, PHP">
        </div>
        <div class="mb-3">
          <label class="form-label">Featured Image Alt Text</label>
          <input type="text" class="form-control" name="featured_image_alt" value="<?php echo blog_escape($formData['featured_image_alt']); ?>">
        </div>
        <div class="mb-3 file-dropzone">
          <label class="form-label mb-0">Featured Image</label>
          <input type="file" class="form-control" name="featured_image" accept="image/jpeg,image/png,image/webp,image/gif" data-image-preview-input>
          <small class="admin-helper d-block mt-2">JPG, PNG, WEBP, or GIF. Choose a new file to replace the current image.</small>
          <?php if ($featuredPreview) : ?>
            <div class="form-check mt-2">
              <input class="form-check-input" type="checkbox" name="remove_featured_image" value="1" id="removeFeaturedImage">
              <label class="form-check-label" for="removeFeaturedImage">Remove current featured image</label>
            </div>
          <?php endif; ?>
          <img class="preview-image mt-3" src="<?php echo blog_escape($featuredPreview ?: 'data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 800 500%22><rect width=%22800%22 height=%22500%22 fill=%22%23eef2f8%22/></svg>'); ?>" data-image-preview-target data-placeholder="data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 800 500%22><rect width=%22800%22 height=%22500%22 fill=%22%23eef2f8%22/></svg>">
        </div>
        <div class="mb-3">
          <label class="form-label">Gallery Images</label>
          <input type="file" class="form-control" name="gallery_images[]" accept="image/*" multiple>
          <small class="admin-helper">Upload more than one image to build a gallery inside the post.</small>
        </div>
      </div>

      <?php if ($images) : ?>
        <div class="admin-panel-card mb-4">
          <strong class="d-block mb-3">Existing Gallery</strong>
          <div class="admin-media-grid">
            <?php foreach ($images as $image) : ?>
              <div class="admin-media-card">
                <img src="<?php echo blog_escape(blog_admin_media_url($image['image_path'])); ?>" alt="Gallery image">
                <small class="d-block mt-2"><?php echo blog_escape($image['alt_text'] ?? 'Gallery image'); ?></small>
                <label class="form-check mt-2">
                  <input class="form-check-input" type="checkbox" name="delete_gallery_ids[]" value="<?php echo (int) $image['id']; ?>">
                  <span>Remove this image</span>
                </label>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <button type="submit" class="btn btn-primary w-100 py-3"><i class="ri-save-3-line"></i> <?php echo $post ? 'Update Blog Post' : 'Publish Blog Post'; ?></button>
    </div>
  </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
