<?php
require_once __DIR__ . '/includes/site_config.php';
require_once __DIR__ . '/includes/portfolio_app.php';

header('Content-Type: application/xml; charset=UTF-8');

$pages = [
    ['slug' => '', 'priority' => '1.0', 'changefreq' => 'weekly'],
    ['slug' => 'service', 'priority' => '0.9', 'changefreq' => 'monthly'],
    ['slug' => 'pricing', 'priority' => '0.9', 'changefreq' => 'monthly'],
    ['slug' => 'portfolio', 'priority' => '0.9', 'changefreq' => 'weekly'],
    ['slug' => 'about_us', 'priority' => '0.8', 'changefreq' => 'monthly'],
    ['slug' => 'team', 'priority' => '0.7', 'changefreq' => 'monthly'],
    ['slug' => 'careers', 'priority' => '0.6', 'changefreq' => 'weekly'],
    ['slug' => 'contact', 'priority' => '0.9', 'changefreq' => 'monthly'],
    ['slug' => 'app_development_details', 'priority' => '0.8', 'changefreq' => 'monthly'],
    ['slug' => 'software_development_details', 'priority' => '0.8', 'changefreq' => 'monthly'],
    ['slug' => 'service_details', 'priority' => '0.8', 'changefreq' => 'monthly'],
    ['slug' => 'digital_marketing_details', 'priority' => '0.7', 'changefreq' => 'monthly'],
    ['slug' => 'privacy_policy', 'priority' => '0.3', 'changefreq' => 'yearly'],
    ['slug' => 'terms_and_conditions', 'priority' => '0.3', 'changefreq' => 'yearly'],
    ['slug' => 'refund_policy', 'priority' => '0.3', 'changefreq' => 'yearly'],
    ['slug' => 'cancellation_policy', 'priority' => '0.3', 'changefreq' => 'yearly'],
];

foreach (portfolio_published() as $project) {
    $pages[] = [
        'slug' => 'portfolio_details?slug=' . rawurlencode($project['slug']),
        'priority' => '0.7',
        'changefreq' => 'monthly',
    ];
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($pages as $page) : ?>
  <url>
    <loc><?php echo htmlspecialchars(site_url($page['slug']), ENT_QUOTES, 'UTF-8'); ?></loc>
    <changefreq><?php echo $page['changefreq']; ?></changefreq>
    <priority><?php echo $page['priority']; ?></priority>
  </url>
<?php endforeach; ?>
</urlset>
