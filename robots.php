<?php
require_once __DIR__ . '/includes/site_config.php';

header('Content-Type: text/plain; charset=UTF-8');

// Canonical sitemap host matches SITE_PUBLIC_URL / .htaccess (https://nazora.tech, non-www).
$sitemapUrl = rtrim(site_public_url(), '/') . '/sitemap.xml';

echo "User-agent: *\n";
echo "Allow: /\n";
echo "\n";
echo "# Private application areas\n";
echo "Disallow: /admin/\n";
echo "Disallow: /includes/\n";
echo "Disallow: /submissions/\n";
echo "Disallow: /vendor/\n";
echo "Disallow: /tools/\n";
echo "Disallow: /send_email.php\n";
echo "Disallow: /blog\n";
echo "Disallow: /blog/\n";
echo "\n";
echo "# Block private storage (SQLite DB, config, logs). Public media is allowed below.\n";
echo "Disallow: /storage/\n";
echo "Allow: /storage/uploads/\n";
echo "\n";
echo 'Sitemap: ' . $sitemapUrl . "\n";
