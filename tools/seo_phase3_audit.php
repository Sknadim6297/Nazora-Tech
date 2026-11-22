<?php
/**
 * Local SEO Phase 3 audit (CLI). Does not hit production.
 * Usage: php tools/seo_phase3_audit.php
 */
require_once dirname(__DIR__) . '/includes/site_config.php';
require_once dirname(__DIR__) . '/includes/portfolio_app.php';

$root = dirname(__DIR__);
$base = 'http://127.0.0.1/SNF_TEAM';
$issues = [];

function issue(array &$issues, string $sev, string $area, string $msg): void
{
    $issues[] = compact('sev', 'area', 'msg');
}

// --- H1 audit on source files ---
$pages = [
    'index.php', 'about_us.php', 'service.php', 'pricing.php', 'portfolio.php',
    'team.php', 'careers.php', 'contact.php', 'service_details.php',
    'app_development_details.php', 'software_development_details.php',
    'digital_marketing_details.php', 'privacy_policy.php', 'terms_and_conditions.php',
    'refund_policy.php', 'cancellation_policy.php', 'blog.php', '404.php',
];

$redirectOnly = ['blog.php', 'blog_details.php'];
foreach ($pages as $file) {
    $path = $root . '/' . $file;
    if (!is_file($path)) {
        issue($issues, 'high', 'h1', "$file missing");
        continue;
    }
    $html = file_get_contents($path);
    // Unpublished blog stubs exit before HTML; skip H1 requirement.
    if (in_array($file, $redirectOnly, true) && preg_match('/header\s*\(\s*[\'"]Location:/i', $html)) {
        continue;
    }
    $count = preg_match_all('/<h1\b/i', $html, $m);
    if ($count === 0) {
        issue($issues, 'high', 'h1', "$file has no H1 in source");
    } elseif ($count > 1) {
        issue($issues, 'medium', 'h1', "$file has $count H1 tags in source");
    }
}

$index = file_get_contents($root . '/index.php');
$header = file_get_contents($root . '/header.php');
if (!preg_match('/<h1>[^<]*Nazora Tech[^<]*Software Development Company in Kolkata\s*<\/h1>/u', $index)) {
    issue($issues, 'high', 'h1', 'Homepage H1 must include Nazora Tech + Kolkata company heading');
}
if (!preg_match('/nz-hero-brand[^>]*>\s*Nazora Tech\s*</', $index)) {
    issue($issues, 'medium', 'brand', 'Homepage brand tagline Nazora Tech missing or changed');
}
if (preg_match('/Nazara/i', $index . $header)) {
    issue($issues, 'high', 'brand', 'Incorrect brand spelling Nazara found in homepage or header metadata');
}
if (strpos($header, 'Nazora Tech Kolkata') === false) {
    issue($issues, 'medium', 'brand', 'alternateName Nazora Tech Kolkata missing from schema');
}
if (preg_match("/'alternateName'\\s*=>\\s*\\[[^\\]]*['\"]Nazora['\"]\\s*[,\\]]/s", $header)) {
    issue($issues, 'medium', 'brand', 'Bare alternateName "Nazora" weakens brand entity clarity');
}

// --- robots.php ---
$robots = file_get_contents($root . '/robots.php');
if (strpos($robots, 'Disallow: /admin/') === false) {
    issue($issues, 'high', 'robots', 'admin not disallowed');
}
if (strpos($robots, 'Disallow: /storage/') === false) {
    issue($issues, 'high', 'robots', 'storage root not disallowed');
}
if (strpos($robots, 'Allow: /storage/uploads/') === false) {
    issue($issues, 'medium', 'robots', 'public uploads not explicitly allowed');
}
if (strpos($robots, 'Disallow: /includes/') === false) {
    issue($issues, 'low', 'robots', 'includes/ not listed in robots (blocked by .htaccess)');
}

// --- Sitemap candidates ---
ob_start();
include $root . '/sitemap.php';
$xml = ob_get_clean();
preg_match_all('#<loc>([^<]+)</loc>#', $xml, $locMatches);
$locs = $locMatches[1] ?? [];
echo "Sitemap URL count: " . count($locs) . PHP_EOL;
foreach ($locs as $loc) {
    if (!preg_match('#^https://nazora\.tech/#i', $loc) && $loc !== 'https://nazora.tech/') {
        issue($issues, 'high', 'sitemap', "Non-canonical loc: $loc");
    }
    if (stripos($loc, '/index') !== false) {
        issue($issues, 'high', 'sitemap', "Homepage index URL in sitemap: $loc");
    }
    if (strpos($loc, 'http://') === 0) {
        issue($issues, 'high', 'sitemap', "HTTP (non-HTTPS) loc: $loc");
    }
}
echo "Sitemap locs:" . PHP_EOL;
foreach ($locs as $loc) {
    echo "  - $loc" . PHP_EOL;
}

// --- Title uniqueness from seoMap in header.php ---
if (strpos($header, "'blog.php'") === false && strpos($header, '"blog.php"') === false) {
    issue($issues, 'medium', 'meta', 'blog.php missing from header.php SEO map');
} else {
    // Present — no issue.
}

// --- JSON-LD presence ---
foreach (['Organization', 'ProfessionalService', 'WebSite'] as $type) {
    if (strpos($header, "'@type' => '$type'") === false && strpos($header, "\"@type\" => \"$type\"") === false) {
        issue($issues, 'high', 'jsonld', "$type schema missing in header.php");
    }
}
if (preg_match('/AggregateRating|reviewRating|"ratingValue"/', $header)) {
    issue($issues, 'high', 'jsonld', 'Invented ratings/reviews detected in schema');
}

// --- Local HTTP checks (best-effort) ---
$paths = [
    '', 'about_us', 'service', 'pricing', 'portfolio', 'team', 'careers', 'contact',
    'service_details', 'app_development_details', 'software_development_details',
    'digital_marketing_details', 'privacy_policy', 'terms_and_conditions',
    'refund_policy', 'cancellation_policy', 'robots.txt', 'sitemap.xml',
];
foreach ($paths as $p) {
    $url = rtrim($base, '/') . '/' . ltrim($p, '/');
    if ($p === '') {
        $url = rtrim($base, '/') . '/';
    }
    $ctx = stream_context_create(['http' => ['timeout' => 4, 'ignore_errors' => true, 'follow_location' => 0]]);
    $headers = @get_headers($url, 1, $ctx);
    if ($headers === false) {
        issue($issues, 'medium', 'http', "Could not fetch locally: $url");
        continue;
    }
    $statusLine = is_array($headers) ? ($headers[0] ?? '') : '';
    if (!preg_match('/\s(200|301|302)\s/', $statusLine)) {
        issue($issues, 'high', 'http', "$url => $statusLine");
    }
}

echo PHP_EOL . 'Issues (' . count($issues) . '):' . PHP_EOL;
foreach ($issues as $i) {
    echo strtoupper($i['sev']) . " [{$i['area']}] {$i['msg']}" . PHP_EOL;
}
