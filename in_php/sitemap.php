<?php
/**
 * XML sitemap for Nazora Tech.
 * Always emits absolute HTTPS locs on the canonical host from SITE_PUBLIC_URL
 * (https://nazora.tech — non-www, matching .htaccess). Never emits relative paths
 * or /index for the homepage.
 */
require_once __DIR__ . '/includes/site_config.php';
require_once __DIR__ . '/includes/portfolio_app.php';

header('Content-Type: application/xml; charset=UTF-8');
header('X-Robots-Tag: noindex, follow');

/**
 * Build a canonical absolute sitemap URL. Homepage is always trailing-slash root.
 */
function sitemap_loc(string $path = ''): string
{
    return site_seo_url($path);
}

/**
 * Only list a clean URL when the matching public PHP file exists.
 */
function sitemap_page_exists(string $slug): bool
{
    $slug = trim($slug, '/');
    if ($slug === '' || strcasecmp($slug, 'index') === 0) {
        return is_file(__DIR__ . '/index.php');
    }

    $file = preg_replace('/\?.*$/', '', $slug);
    $file = preg_replace('/\.php$/i', '', (string) $file);

    return $file !== '' && is_file(__DIR__ . '/' . $file . '.php');
}

$candidates = [
    ['slug' => '', 'priority' => '1.0', 'changefreq' => 'weekly', 'file' => 'index.php'],
    ['slug' => 'service', 'priority' => '0.9', 'changefreq' => 'monthly', 'file' => 'service.php'],
    ['slug' => 'pricing', 'priority' => '0.9', 'changefreq' => 'monthly', 'file' => 'pricing.php'],
    ['slug' => 'portfolio', 'priority' => '0.9', 'changefreq' => 'weekly', 'file' => 'portfolio.php'],
    ['slug' => 'about_us', 'priority' => '0.8', 'changefreq' => 'monthly', 'file' => 'about_us.php'],
    ['slug' => 'team', 'priority' => '0.7', 'changefreq' => 'monthly', 'file' => 'team.php'],
    ['slug' => 'careers', 'priority' => '0.6', 'changefreq' => 'weekly', 'file' => 'careers.php'],
    ['slug' => 'contact', 'priority' => '0.9', 'changefreq' => 'monthly', 'file' => 'contact.php'],
    ['slug' => 'app_development_details', 'priority' => '0.8', 'changefreq' => 'monthly', 'file' => 'app_development_details.php'],
    ['slug' => 'software_development_details', 'priority' => '0.8', 'changefreq' => 'monthly', 'file' => 'software_development_details.php'],
    ['slug' => 'service_details', 'priority' => '0.8', 'changefreq' => 'monthly', 'file' => 'service_details.php'],
    ['slug' => 'digital_marketing_details', 'priority' => '0.7', 'changefreq' => 'monthly', 'file' => 'digital_marketing_details.php'],
    ['slug' => 'privacy_policy', 'priority' => '0.3', 'changefreq' => 'yearly', 'file' => 'privacy_policy.php'],
    ['slug' => 'terms_and_conditions', 'priority' => '0.3', 'changefreq' => 'yearly', 'file' => 'terms_and_conditions.php'],
    ['slug' => 'refund_policy', 'priority' => '0.3', 'changefreq' => 'yearly', 'file' => 'refund_policy.php'],
    ['slug' => 'cancellation_policy', 'priority' => '0.3', 'changefreq' => 'yearly', 'file' => 'cancellation_policy.php'],
];

$pages = [];
$seen = [];

foreach ($candidates as $item) {
    $filePath = __DIR__ . '/' . $item['file'];
    if (!is_file($filePath)) {
        continue;
    }

    $loc = sitemap_loc($item['slug']);
    // Absolute HTTPS only — never emit relative paths like /service or /index.
    if (!preg_match('#^https://[a-z0-9.-]+/#i', $loc)) {
        continue;
    }

    if (isset($seen[$loc])) {
        continue;
    }
    $seen[$loc] = true;

    $pages[] = [
        'loc' => $loc,
        'priority' => $item['priority'],
        'changefreq' => $item['changefreq'],
        'lastmod' => gmdate('Y-m-d', (int) @filemtime($filePath) ?: time()),
    ];
}

foreach (portfolio_published() as $project) {
    if (empty($project['slug']) || !is_file(__DIR__ . '/portfolio_details.php')) {
        continue;
    }
    $loc = sitemap_loc('portfolio_details?slug=' . rawurlencode((string) $project['slug']));
    if (isset($seen[$loc]) || !preg_match('#^https://#i', $loc)) {
        continue;
    }
    $seen[$loc] = true;
    $pages[] = [
        'loc' => $loc,
        'priority' => '0.7',
        'changefreq' => 'monthly',
        'lastmod' => gmdate('Y-m-d', (int) @filemtime(__DIR__ . '/portfolio_details.php') ?: time()),
    ];
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($pages as $page) : ?>
  <url>
    <loc><?php echo htmlspecialchars($page['loc'], ENT_XML1 | ENT_QUOTES, 'UTF-8'); ?></loc>
    <lastmod><?php echo htmlspecialchars($page['lastmod'], ENT_XML1 | ENT_QUOTES, 'UTF-8'); ?></lastmod>
    <changefreq><?php echo htmlspecialchars($page['changefreq'], ENT_XML1 | ENT_QUOTES, 'UTF-8'); ?></changefreq>
    <priority><?php echo htmlspecialchars($page['priority'], ENT_XML1 | ENT_QUOTES, 'UTF-8'); ?></priority>
  </url>
<?php endforeach; ?>
</urlset>
