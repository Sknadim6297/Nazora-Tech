<?php
require_once __DIR__ . '/includes/blog_app.php';

header('Content-Type: application/xml; charset=UTF-8');

$baseUrl = rtrim(blog_url(''), '/');
$pages = ['index', 'service', 'about_us', 'team', 'careers', 'portfolio', 'pricing', 'contact'];

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <?php foreach ($pages as $page) : ?>
    <url>
      <loc><?php echo htmlspecialchars($baseUrl . '/' . $page, ENT_QUOTES, 'UTF-8'); ?></loc>
      <changefreq>weekly</changefreq>
      <priority><?php echo $page === 'index' ? '1.0' : '0.8'; ?></priority>
    </url>
  <?php endforeach; ?>
</urlset>
