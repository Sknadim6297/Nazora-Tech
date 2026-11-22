<?php

// Enforce production-safe PHP error handling when running over HTTP outside local dev hosts.
if (PHP_SAPI !== 'cli') {
    $nzHost = strtolower((string) preg_replace('/:\d+$/', '', (string) ($_SERVER['HTTP_HOST'] ?? 'localhost')));
    if (!in_array($nzHost, ['localhost', '127.0.0.1', '::1'], true)) {
        error_reporting(E_ALL);
        @ini_set('display_errors', '0');
        @ini_set('display_startup_errors', '0');
        @ini_set('log_errors', '1');
    }
}

if (!defined('SITE_COMPANY_NAME')) {
    define('SITE_COMPANY_NAME', 'Nazora Tech');
    define('SITE_COMPANY_BLOG', 'Nazora Tech Blog');
    define('SITE_COMPANY_TEAM', 'Nazora Tech Team');
    // Fallback contact inbox until info@nazora.tech is confirmed active in Hostinger Email.
    define('SITE_CONTACT_EMAIL', 'skonlineitsolution@gmail.com');
    define('SITE_WHATSAPP_MESSAGE', 'Hi Nazora Tech, I need a quote.');
    define('SITE_PHONE_E164', '+919332016918');
    define('SITE_PHONE_DISPLAY', '+91 9332016918');
    // Leave empty until the owner creates a GA4 property; GA4_MEASUREMENT_ID in server config overrides this.
    define('SITE_GA4_MEASUREMENT_ID', '');
    define('SITE_PUBLIC_URL', 'https://nazora.tech');
    define('SITE_LOCATION', 'Kestopur, Kolkata, West Bengal, India');
    define('SITE_LOGO_PATH', 'assets/images/logo/nazora-logo.png');
}

/**
 * Loads optional non-tracked PHP configuration for hosts without an env-var dashboard (e.g. Hostinger).
 * Checked paths (first readable wins, empty if none):
 *   1) ../nazora-config.php         (one directory above web root / public_html — preferred on Hostinger)
 *   2) ./storage/config.local.php   (inside HTTP-blocked, gitignored storage/ folder)
 */
function site_local_config(?string &$source = null): array
{
    static $cached;
    static $cachedSource;

    if ($cached !== null) {
        $source = $cachedSource;

        return $cached;
    }

    $root = dirname(__DIR__);
    $candidates = [
        'parent_config' => dirname($root) . '/nazora-config.php',
        'storage_config' => $root . '/storage/config.local.php',
    ];

    $cached = [];
    $cachedSource = 'none';

    foreach ($candidates as $label => $path) {
        if (@is_file($path) && @is_readable($path)) {
            $loaded = include $path;
            if (is_array($loaded)) {
                $cached = $loaded;
                $cachedSource = $label;
                break;
            }
        }
    }

    $source = $cachedSource;

    return $cached;
}

/**
 * Reads configuration from environment variables first, then optional local PHP config.
 */
function site_env(string $key, string $default = ''): string
{
    foreach ([getenv($key), $_ENV[$key] ?? null, $_SERVER[$key] ?? null, $_SERVER['REDIRECT_' . $key] ?? null] as $val) {
        if (is_string($val) && trim($val) !== '') {
            return trim($val);
        }
    }

    $local = site_local_config();
    if (isset($local[$key]) && is_scalar($local[$key])) {
        $val = trim((string) $local[$key]);
        if ($val !== '') {
            return $val;
        }
    }

    return $default;
}

function site_company_name(): string
{
    return SITE_COMPANY_NAME;
}

function site_contact_email(): string
{
    $override = site_env('SITE_CONTACT_EMAIL');
    if ($override !== '' && filter_var($override, FILTER_VALIDATE_EMAIL)) {
        return $override;
    }

    return SITE_CONTACT_EMAIL;
}

/**
 * Public HTTPS website address used in emails (independent of the host that handled the request).
 */
function site_public_url(): string
{
    $override = site_env('SITE_PUBLIC_URL');
    if ($override !== '' && preg_match('#^https://[a-z0-9.-]+(?::\d+)?$#i', rtrim($override, '/'))) {
        return rtrim($override, '/');
    }

    return SITE_PUBLIC_URL;
}

/**
 * Absolute HTTPS logo URL for emails. Remote images are blocked by some mail clients, so templates
 * must always provide alt text. EMAIL_LOGO_URL may override it with another HTTPS address.
 */
function site_email_logo_url(): string
{
    $override = site_env('EMAIL_LOGO_URL');
    if ($override !== '' && preg_match('#^https://#i', $override) && filter_var($override, FILTER_VALIDATE_URL)) {
        return $override;
    }

    return site_public_url() . '/' . SITE_LOGO_PATH;
}

function site_origin(): string
{
    $envUrl = site_env('SITE_URL');
    if ($envUrl !== '' && preg_match('#^https?://[^/]+#i', $envUrl, $m)) {
        return rtrim($m[0], '/');
    }

    $rawHost = (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');
    $hostOnly = strtolower((string) preg_replace('/:\d+$/', '', $rawHost));
    if ($hostOnly === 'nazora.tech' || $hostOnly === 'www.nazora.tech') {
        return 'https://nazora.tech';
    }

    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

    return ($https ? 'https' : 'http') . '://' . $rawHost;
}

function site_base_path(): string
{
    $envUrl = site_env('SITE_URL');
    if ($envUrl !== '') {
        $cleanPath = trim((string) (parse_url($envUrl, PHP_URL_PATH) ?? ''), '/');

        return $cleanPath === '' ? '' : '/' . $cleanPath;
    }

    if (PHP_SAPI === 'cli') {
        return '';
    }

    $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $dir = preg_replace('~/admin$~', '', $dir);
    $dir = trim($dir, '/');

    return $dir === '' || $dir === '.' ? '' : '/' . $dir;
}

/**
 * Absolute public URL for a page slug without .php, e.g. site_url('about_us').
 * Empty slug returns the site home URL.
 */
function site_url(string $page = ''): string
{
    $page = trim($page, '/');

    return site_origin() . site_base_path() . ($page === '' ? '/' : '/' . $page);
}

/**
 * Google-facing absolute URL on the canonical public host (SITE_PUBLIC_URL).
 * Always https://nazora.tech/... — independent of www/local request host.
 * Use for canonical, Open Graph, JSON-LD, and sitemap locs.
 */
function site_seo_url(string $page = ''): string
{
    $base = rtrim(site_public_url(), '/');
    $page = trim($page);

    if ($page === '' || $page === '/' || strcasecmp($page, 'index') === 0 || strcasecmp($page, 'index.php') === 0) {
        return $base . '/';
    }

    if (preg_match('#^https?://#i', $page)) {
        return $page;
    }

    $page = ltrim($page, '/');
    if (strpos($page, '?') !== false) {
        [$file, $query] = explode('?', $page, 2);

        return $base . '/' . trim($file, '/') . '?' . $query;
    }

    return $base . '/' . $page;
}

function site_seo_truncate(string $text, int $maxLength = 155): string
{
    $text = trim((string) preg_replace('/\s+/', ' ', strip_tags($text)));
    if (mb_strlen($text, 'UTF-8') <= $maxLength) {
        return $text;
    }

    $cut = mb_substr($text, 0, $maxLength - 1, 'UTF-8');
    $space = mb_strrpos($cut, ' ', 0, 'UTF-8');
    if ($space !== false && $space > (int) ($maxLength * 0.6)) {
        $cut = mb_substr($cut, 0, $space, 'UTF-8');
    }

    return rtrim($cut, " \t\n\r\0\x0B.,;:-") . '…';
}

/**
 * GA4 Measurement ID, or '' when analytics is not configured (tracking stays fully off).
 */
function site_ga4_id(): string
{
    $id = strtoupper(site_env('GA4_MEASUREMENT_ID', SITE_GA4_MEASUREMENT_ID));

    return preg_match('/^G-[A-Z0-9]{4,16}$/', $id) ? $id : '';
}

/** Phone number for display, e.g. +91 9332016918 */
function site_phone_display(): string
{
    return SITE_PHONE_DISPLAY;
}

/** tel: link target, e.g. tel:+919332016918 */
function site_phone_tel(): string
{
    return 'tel:' . SITE_PHONE_E164;
}

/** WhatsApp URL, e.g. https://wa.me/919332016918?text=... */
function site_whatsapp_url(string $phone = ''): string
{
    $phone = $phone !== '' ? $phone : ltrim(SITE_PHONE_E164, '+');

    return 'https://wa.me/' . $phone . '?text=' . rawurlencode(SITE_WHATSAPP_MESSAGE);
}
