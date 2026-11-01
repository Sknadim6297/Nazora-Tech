<?php
$siteBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
header('Location: ' . ($siteBase === '' ? '' : $siteBase) . '/index');
exit;

require_once __DIR__ . '/includes/blog_app.php';
require_once __DIR__ . '/includes/blog_frontend.php';

$slug = trim((string) ($_GET['slug'] ?? ''));
$post = $slug !== '' ? blog_get_post_by_slug($slug, true) : null;

if (!$post) {
    http_response_code(404);
    $pageTitle = 'Article Not Found | ' . site_company_name();
    $pageDescription = 'The requested blog article could not be found.';
    $loadBlogAssets = true;
    include 'header.php';
    echo '<div class="section optech-section-padding2"><div class="container">';
    blog_render_page_breadcrumb('Article Not Found', [
        ['label' => 'Home', 'url' => 'index'],
        ['label' => 'Blog', 'url' => 'blog'],
        ['label' => 'Not Found'],
    ]);
    echo '<div class="snf-blog-empty mt-4"><h2>Article not found</h2><p>This post may have been removed or is not published yet.</p>';
    echo '<a class="optech-default-btn mt-3" href="' . blog_escape(blog_url('blog')) . '" data-text="Back to Blog"><span class="btn-wraper">Back to Blog</span></a></div></div></div>';
    include 'footer.php';
    exit;
}

$pageTitle = ($post['seo_title'] ?: $post['title']) . ' | ' . site_company_name();
$pageDescription = $post['meta_description'] ?: $post['excerpt'];
$pageKeywords = $post['meta_keywords'] ?: 'blog, IT services, web development, Kolkata';
$pageCanonical = $post['canonical_url'] ?: blog_post_url($post['slug']);
$pageOgImage = blog_image_url($post['featured_image'] ?? null);
$pageTwitterCard = $post['twitter_card'] ?: 'summary_large_image';
$pageSchemaMarkup = $post['schema_markup'] ?: json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BlogPosting',
    'headline' => $post['title'],
    'description' => $pageDescription,
    'datePublished' => $post['published_at'] ?: $post['created_at'],
    'dateModified' => $post['updated_at'],
    'mainEntityOfPage' => $pageCanonical,
    'author' => ['@type' => 'Organization', 'name' => site_company_name()],
    'publisher' => ['@type' => 'Organization', 'name' => site_company_name()],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

$relatedPosts = blog_get_related_posts((int) $post['id'], (int) ($post['category_id'] ?? 0), 3);
$latestPosts = blog_get_recent_posts(5);
$categories = blog_category_list(false, true);
$loadBlogAssets = true;

include 'header.php';

$breadcrumbHeading = mb_strlen($post['title']) > 72 ? mb_substr($post['title'], 0, 69) . '...' : $post['title'];
blog_render_page_breadcrumb($breadcrumbHeading, [
    ['label' => 'Home', 'url' => 'index'],
    ['label' => 'Blog', 'url' => 'blog'],
    ['label' => 'Article'],
]);
?>

  <div class="section optech-section-padding bg-light1">
    <div class="container">
      <div class="row g-4">
        <div class="col-lg-8">
          <article class="snf-blog-article">
            <div class="snf-blog-article__meta">
              <span><?php echo blog_escape($post['category_name'] ?? 'General'); ?></span>
              <span><?php echo blog_escape(blog_format_date($post['published_at'] ?? $post['created_at'])); ?></span>
              <span><?php echo blog_escape($post['author_name'] ?? SITE_COMPANY_TEAM); ?></span>
            </div>
            <h1><?php echo blog_escape($post['title']); ?></h1>
            <p class="lead"><?php echo blog_escape($post['excerpt']); ?></p>
            <?php if (!empty($post['featured_image'])) : ?>
              <img class="snf-blog-article__featured" src="<?php echo blog_escape(blog_image_url($post['featured_image'])); ?>" alt="<?php echo blog_escape($post['featured_image_alt'] ?: $post['title']); ?>">
            <?php endif; ?>
            <div class="snf-blog-article__content">
              <?php echo $post['content']; ?>
            </div>

            <?php if (!empty($post['images'])) : ?>
              <div class="snf-blog-gallery">
                <?php foreach ($post['images'] as $image) : ?>
                  <img src="<?php echo blog_escape(blog_image_url($image['image_path'])); ?>" alt="<?php echo blog_escape($image['alt_text'] ?: $post['title']); ?>" loading="lazy">
                <?php endforeach; ?>
              </div>
            <?php endif; ?>

            <?php if (!empty($post['tags'])) : ?>
              <div class="snf-blog-tags">
                <?php foreach ($post['tags'] as $tag) : ?>
                  <a href="<?php echo blog_escape(blog_url('blog?search=' . urlencode($tag['name']))); ?>"><?php echo blog_escape($tag['name']); ?></a>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </article>

          <?php if ($relatedPosts) : ?>
            <section class="snf-blog-related">
              <h3>Related Articles</h3>
              <div class="snf-blog-grid">
                <?php foreach ($relatedPosts as $related) : ?>
                  <?php blog_render_post_card($related, 'grid'); ?>
                <?php endforeach; ?>
              </div>
            </section>
          <?php endif; ?>
        </div>

        <div class="col-lg-4">
          <?php blog_render_sidebar($latestPosts, $categories); ?>
        </div>
      </div>
    </div>
  </div>

<?php include 'footer.php'; ?>
