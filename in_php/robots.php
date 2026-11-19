<?php
require_once __DIR__ . '/includes/site_config.php';

header('Content-Type: text/plain; charset=UTF-8');

echo "User-agent: *\n";
echo "Allow: /\n";
echo "Allow: /storage/uploads/\n";
echo "Disallow: /admin/\n";
echo "Disallow: /storage/\n";
echo "Disallow: /send_email.php\n";
echo "\n";
echo 'Sitemap: ' . site_url('sitemap.xml') . "\n";
