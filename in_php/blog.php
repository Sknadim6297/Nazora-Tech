<?php
$siteBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
header('Location: ' . ($siteBase === '' ? '' : $siteBase) . '/index');
exit;

require_once __DIR__ . '/includes/blog_app.php';
require_once __DIR__ . '/includes/blog_frontend.php';

$pageTitle = 'Blog | ' . site_company_name();
$pageDescription = 'Read practical articles on web development, SEO, mobile apps, software solutions, and digital growth from our Kolkata team.';
$pageKeywords = 'IT blog Kolkata, web development blog, SEO tips, digital marketing articles';
$pageCanonical = blog_url('blog');
$pageSchemaMarkup = json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Blog',
    'name' => SITE_COMPANY_BLOG,
    'url' => $pageCanonical,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

$filters = [
    'search' => $_GET['search'] ?? '',
    'category' => $_GET['category'] ?? '',
    'page' => (int) ($_GET['page'] ?? 1),
];
$posts = blog_get_public_posts($filters, (int) (blog_setting_get('posts_per_page', '9') ?? 9));
$categories = blog_category_list(false, true);
$latestPosts = blog_get_recent_posts(5);
$activeCategory = null;
foreach ($categories as $cat) {
    if ($cat['slug'] === $filters['category']) {
        $activeCategory = $cat['name'];
        break;
    }
}

$breadcrumbTitle = $activeCategory ? $activeCategory . ' Articles' : 'Our Blog';
$loadBlogAssets = true;
include 'header.php';

blog_render_page_breadcrumb($breadcrumbTitle, [
    ['label' => 'Home', 'url' => 'index'],
    ['label' => 'Blog', 'url' => 'blog'],
    ['label' => $activeCategory ?: 'All Articles'],
]);
?>

  <div class="section optech-section-padding bg-light1">
    <div class="container">
      <div class="optech-section-title center mb-4">
        <span class="hero-eyebrow">Insights & updates</span>
        <h2>Practical guides for your business</h2>
        <p class="text-muted mb-0">Articles on websites, apps, software, SEO, and digital growth.</p>
      </div>

      <form class="snf-blog-filter row g-3 align-items-end" method="get">
        <div class="col-lg-5">
          <label class="form-label">Search articles</label>
          <input type="search" name="search" class="form-control" value="<?php echo blog_escape($filters['search']); ?>" placeholder="Keywords, topics, titles...">
        </div>
        <div class="col-lg-4">
          <label class="form-label">Category</label>
          <select class="form-select" name="category">
            <option value="">All categories</option>
            <?php foreach ($categories as $category) : ?>
              <option value="<?php echo blog_escape($category['slug']); ?>" <?php echo $filters['category'] === $category['slug'] ? 'selected' : ''; ?>><?php echo blog_escape($category['name']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-lg-3">
          <button class="optech-default-btn w-100" type="submit" data-text="Search"><span class="btn-wraper">Search</span></button>
        </div>
      </form>

      <div class="row g-4">
        <div class="col-lg-8">
          <?php if (!$posts['items']) : ?>
            <div class="snf-blog-empty">
              <h4>No articles found</h4>
              <p>Try a different search term or browse all categories.</p>
              <a class="optech-default-btn mt-2" href="<?php echo blog_escape(blog_url('blog')); ?>" data-text="View All"><span class="btn-wraper">View All Posts</span></a>
            </div>
          <?php else : ?>
            <div class="snf-blog-grid">
              <?php foreach ($posts['items'] as $post) : ?>
                <?php blog_render_post_card($post, 'grid'); ?>
              <?php endforeach; ?>
            </div>

            <?php if ($posts['pages'] > 1) : ?>
              <nav class="snf-blog-pagination" aria-label="Blog pagination">
                <ul class="pagination flex-wrap justify-content-center mb-0">
                  <?php for ($page = 1; $page <= $posts['pages']; $page++) : ?>
                    <li class="page-item <?php echo $page === $posts['page'] ? 'active' : ''; ?>">
                      <a class="page-link" href="?<?php echo http_build_query(array_merge($filters, ['page' => $page])); ?>"><?php echo $page; ?></a>
                    </li>
                  <?php endfor; ?>
                </ul>
              </nav>
            <?php endif; ?>
          <?php endif; ?>
        </div>
        <div class="col-lg-4">
          <?php blog_render_sidebar($latestPosts, $categories); ?>
        </div>
      </div>
    </div>
  </div>

<?php include 'footer.php'; ?>
