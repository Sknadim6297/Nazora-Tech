<?php

// Admin session idle timeout (seconds) and login throttling limits
const BLOG_ADMIN_IDLE_TIMEOUT = 7200;
const BLOG_LOGIN_WINDOW = 900;
const BLOG_LOGIN_MAX_PER_ACCOUNT = 5;
const BLOG_LOGIN_MAX_PER_IP = 20;
const BLOG_UPLOAD_MAX_BYTES = 5242880;

if (!defined('BLOG_APP_BOOTSTRAPPED')) {
    define('BLOG_APP_BOOTSTRAPPED', true);

    require_once __DIR__ . '/site_config.php';

    // Sessions are only needed for the admin panel; public pages stay cookie-free and cacheable
    $blogScript = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
    if (strpos($blogScript, '/admin/') !== false && session_status() === PHP_SESSION_NONE) {
        $blogHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'secure' => $blogHttps,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();

        // Admin pages hold private data: never cache or index them
        if (!headers_sent()) {
            header('Cache-Control: no-store, no-cache, must-revalidate, private');
            header('Pragma: no-cache');
            header('X-Robots-Tag: noindex, nofollow');
        }

        // Sign out after a period of inactivity
        if (isset($_SESSION['blog_user'])) {
            $blogLastActivity = (int) ($_SESSION['blog_last_activity'] ?? time());
            if (time() - $blogLastActivity > BLOG_ADMIN_IDLE_TIMEOUT) {
                unset($_SESSION['blog_user'], $_SESSION['blog_csrf_token'], $_SESSION['blog_last_activity']);
                session_regenerate_id(true);
            } else {
                $_SESSION['blog_last_activity'] = time();
            }
        }
    }

    date_default_timezone_set('Asia/Kolkata');
}

function blog_root_path(): string
{
    return dirname(__DIR__);
}

function blog_base_url(): string
{
    static $baseUrl;

    if ($baseUrl === null) {
        $scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        $baseUrl = preg_replace('~/admin$~', '', $scriptName);
        $baseUrl = rtrim($baseUrl, '/');
    }

    return $baseUrl ?: '';
}

function blog_url(string $path = ''): string
{
    $path = ltrim($path, '/');

    return blog_base_url() . ($path !== '' ? '/' . $path : '');
}

function blog_asset(string $path): string
{
    return blog_url($path);
}

function blog_image_url(?string $path): string
{
    $path = blog_normalize_media_path($path);
    if ($path === null || $path === '') {
        return blog_url('assets/images/logo/header_logo-removebg.png');
    }

    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }

    $fullPath = blog_root_path() . '/' . ltrim($path, '/');
    $version = is_file($fullPath) ? '?v=' . filemtime($fullPath) : '';

    return blog_url(ltrim($path, '/')) . $version;
}

function blog_post_url(string $slug): string
{
    return blog_url('blog/' . ltrim($slug, '/'));
}

function blog_storage_path(): string
{
    return blog_root_path() . '/storage';
}

function blog_upload_dir(string $group = 'blog'): string
{
    return blog_storage_path() . '/uploads/' . trim($group, '/');
}

function blog_db_path(): string
{
    return blog_storage_path() . '/blog.sqlite';
}

function blog_ensure_directory(string $path): void
{
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }
}

function blog_now(): string
{
    return date('Y-m-d H:i:s');
}

function blog_escape(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function blog_slugify(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/', '-', $value);
    $value = trim((string) $value, '-');

    return $value !== '' ? $value : 'item';
}

function blog_db(): PDO
{
    static $pdo;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    blog_ensure_directory(blog_storage_path());
    blog_ensure_directory(blog_upload_dir());

    $pdo = new PDO('sqlite:' . blog_db_path());
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA foreign_keys = ON');
    $pdo->exec('PRAGMA busy_timeout = 5000');

    blog_init_schema($pdo);

    return $pdo;
}

function blog_init_schema(PDO $pdo): void
{
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        password_hash TEXT NOT NULL,
        role TEXT NOT NULL DEFAULT 'admin',
        last_login_at TEXT NULL,
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        slug TEXT NOT NULL UNIQUE,
        description TEXT NULL,
        deleted_at TEXT NULL,
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS tags (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        slug TEXT NOT NULL UNIQUE,
        deleted_at TEXT NULL,
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS posts (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        slug TEXT NOT NULL UNIQUE,
        excerpt TEXT NOT NULL,
        content TEXT NOT NULL,
        status TEXT NOT NULL DEFAULT 'draft',
        category_id INTEGER NULL,
        author_id INTEGER NULL,
        featured_image TEXT NULL,
        featured_image_alt TEXT NULL,
        seo_title TEXT NULL,
        meta_description TEXT NULL,
        meta_keywords TEXT NULL,
        canonical_url TEXT NULL,
        og_title TEXT NULL,
        og_description TEXT NULL,
        twitter_card TEXT NULL,
        schema_markup TEXT NULL,
        published_at TEXT NULL,
        deleted_at TEXT NULL,
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
        FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS post_tags (
        post_id INTEGER NOT NULL,
        tag_id INTEGER NOT NULL,
        created_at TEXT NOT NULL,
        PRIMARY KEY (post_id, tag_id),
        FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
        FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS post_images (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        post_id INTEGER NOT NULL,
        image_path TEXT NOT NULL,
        alt_text TEXT NULL,
        sort_order INTEGER NOT NULL DEFAULT 0,
        created_at TEXT NOT NULL,
        FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        setting_key TEXT PRIMARY KEY,
        setting_value TEXT NULL,
        updated_at TEXT NOT NULL
    )");

    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_posts_status ON posts(status)');
    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_posts_deleted_at ON posts(deleted_at)');
    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_categories_deleted_at ON categories(deleted_at)');
    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_tags_deleted_at ON tags(deleted_at)');

    blog_seed_defaults($pdo);

    // Careers openings (managed from admin)
    $pdo->exec("CREATE TABLE IF NOT EXISTS careers (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        employment_type TEXT NOT NULL DEFAULT 'Full-time',
        location TEXT NOT NULL DEFAULT 'Kolkata',
        experience TEXT NULL,
        description TEXT NOT NULL,
        apply_url TEXT NULL,
        status TEXT NOT NULL DEFAULT 'published',
        sort_order INTEGER NOT NULL DEFAULT 0,
        deleted_at TEXT NULL,
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL
    )");
    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_careers_status ON careers(status)');
    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_careers_deleted ON careers(deleted_at)');

    $careersCount = (int) $pdo->query('SELECT COUNT(*) FROM careers WHERE deleted_at IS NULL')->fetchColumn();
    if ($careersCount === 0) {
        $now = blog_now();
        $seedJobs = [
            ['Frontend Developer', 'Full-time', 'Kolkata / Hybrid', '1–3 years experience', 'Build responsive UI with HTML, CSS, JavaScript, and modern frameworks. Collaborate with design and backend.'],
            ['PHP / Laravel Developer', 'Full-time', 'Kolkata / Hybrid', '2+ years experience', 'Develop and maintain business applications, APIs, and admin systems with clean, secure PHP code.'],
            ['Digital Marketing Intern', 'Internship', 'Kolkata', 'Freshers welcome', 'Support SEO, content, and campaign work for client projects and our own growth channels.'],
        ];
        $jobStmt = $pdo->prepare('INSERT INTO careers (title, employment_type, location, experience, description, apply_url, status, sort_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        foreach ($seedJobs as $i => $row) {
            $jobStmt->execute([$row[0], $row[1], $row[2], $row[3], $row[4], 'contact', 'published', $i + 1, $now, $now]);
        }
    }
}

function blog_seed_defaults(PDO $pdo): void
{
    $userCount = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    if ($userCount === 0) {
        $now = blog_now();
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash, role, created_at, updated_at) VALUES (:name, :email, :password_hash, :role, :created_at, :updated_at)');
        $stmt->execute([
            ':name' => 'Administrator',
            ':email' => 'admin@snfteam.local',
            // Only used when the users table is empty (first boot). Set ADMIN_INITIAL_PASSWORD on the host to avoid the default.
            ':password_hash' => password_hash((string) (site_env('ADMIN_INITIAL_PASSWORD') ?: 'Admin@12345'), PASSWORD_DEFAULT),
            ':role' => 'admin',
            ':created_at' => $now,
            ':updated_at' => $now,
        ]);
    }

    $categoryCount = (int) $pdo->query('SELECT COUNT(*) FROM categories WHERE deleted_at IS NULL')->fetchColumn();
    if ($categoryCount === 0) {
        blog_save_category($pdo, ['name' => 'Updates', 'description' => 'Company announcements, product updates, and service news']);
        blog_save_category($pdo, ['name' => 'Insights', 'description' => 'Strategy, SEO, UX, and digital growth articles']);
    }

    $tagCount = (int) $pdo->query('SELECT COUNT(*) FROM tags WHERE deleted_at IS NULL')->fetchColumn();
    if ($tagCount === 0) {
        blog_save_tag($pdo, ['name' => 'Business']);
        blog_save_tag($pdo, ['name' => 'Technology']);
        blog_save_tag($pdo, ['name' => 'SEO']);
    }
}

function blog_flash(string $type, string $message): void
{
    $_SESSION['blog_flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function blog_get_flash(): ?array
{
    if (!isset($_SESSION['blog_flash'])) {
        return null;
    }

    $flash = $_SESSION['blog_flash'];
    unset($_SESSION['blog_flash']);

    return $flash;
}

function blog_csrf_token(): string
{
    if (empty($_SESSION['blog_csrf_token'])) {
        $_SESSION['blog_csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['blog_csrf_token'];
}

function blog_verify_csrf(?string $token): bool
{
    return is_string($token) && hash_equals($_SESSION['blog_csrf_token'] ?? '', $token);
}

function blog_current_user(): ?array
{
    return $_SESSION['blog_user'] ?? null;
}

function blog_require_login(): void
{
    if (!blog_current_user()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Client address for throttling. The hosting proxy appends the real address last.
 */
function blog_client_ip(): string
{
    $forwarded = array_filter(array_map('trim', explode(',', (string) ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? ''))));

    return $forwarded ? (string) end($forwarded) : (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
}

function blog_login_attempts_table(PDO $pdo): void
{
    static $ready = false;
    if ($ready) {
        return;
    }
    // Stores only one-way hashes and timestamps, never the email or password typed
    $pdo->exec('CREATE TABLE IF NOT EXISTS admin_login_attempts (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        key_hash TEXT NOT NULL,
        attempted_at INTEGER NOT NULL
    )');
    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_login_attempts_key ON admin_login_attempts(key_hash, attempted_at)');
    $ready = true;
}

function blog_login_keys(string $email): array
{
    $ip = blog_client_ip();

    return [
        'account' => hash('sha256', 'acct|' . $ip . '|' . strtolower(trim($email))),
        'ip' => hash('sha256', 'ip|' . $ip),
    ];
}

function blog_login_is_throttled(string $email): bool
{
    $pdo = blog_db();
    blog_login_attempts_table($pdo);
    $keys = blog_login_keys($email);
    $since = time() - BLOG_LOGIN_WINDOW;

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM admin_login_attempts WHERE key_hash = :key AND attempted_at > :since');
    $stmt->execute([':key' => $keys['account'], ':since' => $since]);
    if ((int) $stmt->fetchColumn() >= BLOG_LOGIN_MAX_PER_ACCOUNT) {
        return true;
    }
    $stmt->execute([':key' => $keys['ip'], ':since' => $since]);

    return (int) $stmt->fetchColumn() >= BLOG_LOGIN_MAX_PER_IP;
}

function blog_login_record_failure(string $email): void
{
    $pdo = blog_db();
    blog_login_attempts_table($pdo);
    $now = time();
    $insert = $pdo->prepare('INSERT INTO admin_login_attempts (key_hash, attempted_at) VALUES (:key, :at)');
    foreach (blog_login_keys($email) as $key) {
        $insert->execute([':key' => $key, ':at' => $now]);
    }
    $pdo->prepare('DELETE FROM admin_login_attempts WHERE attempted_at < :old')->execute([':old' => $now - 86400]);
    error_log('[admin] Failed sign-in attempt');
}

function blog_login_clear_failures(string $email): void
{
    $pdo = blog_db();
    blog_login_attempts_table($pdo);
    $pdo->prepare('DELETE FROM admin_login_attempts WHERE key_hash = :key')->execute([':key' => blog_login_keys($email)['account']]);
}

function blog_login(string $email, string $password): bool
{
    $pdo = blog_db();
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if (!$user) {
        // Equalise timing so response time does not reveal whether the account exists
        password_verify($password, password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT));
        blog_login_record_failure($email);

        return false;
    }

    if (!password_verify($password, $user['password_hash'])) {
        blog_login_record_failure($email);

        return false;
    }

    blog_login_clear_failures($email);

    $now = blog_now();
    $pdo->prepare('UPDATE users SET last_login_at = :last_login_at, updated_at = :updated_at WHERE id = :id')->execute([
        ':last_login_at' => $now,
        ':updated_at' => $now,
        ':id' => $user['id'],
    ]);

    // New session id on privilege change prevents session fixation
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }

    $_SESSION['blog_user'] = [
        'id' => (int) $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role'],
    ];
    $_SESSION['blog_last_activity'] = time();
    unset($_SESSION['blog_csrf_token']);

    return true;
}

function blog_logout(): void
{
    unset($_SESSION['blog_user'], $_SESSION['blog_csrf_token'], $_SESSION['blog_last_activity']);
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }
}

function blog_unique_slug(PDO $pdo, string $table, string $slug, ?int $ignoreId = null): string
{
    $baseSlug = $slug !== '' ? $slug : 'item';
    $candidate = $baseSlug;
    $index = 2;

    while (true) {
        $sql = 'SELECT id FROM ' . $table . ' WHERE slug = :slug';
        if ($table === 'posts' || $table === 'categories' || $table === 'tags') {
            $sql .= ' AND deleted_at IS NULL';
        }
        if ($ignoreId !== null) {
            $sql .= ' AND id <> :ignore_id';
        }
        $sql .= ' LIMIT 1';

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':slug', $candidate, PDO::PARAM_STR);
        if ($ignoreId !== null) {
            $stmt->bindValue(':ignore_id', $ignoreId, PDO::PARAM_INT);
        }
        $stmt->execute();

        if (!$stmt->fetchColumn()) {
            return $candidate;
        }

        $candidate = $baseSlug . '-' . $index;
        $index++;
    }
}

function blog_category_list(bool $includeDeleted = false, bool $publishedOnly = false): array
{
    $pdo = blog_db();
    $sql = 'SELECT c.*, COUNT(p.id) AS post_count FROM categories c LEFT JOIN posts p ON p.category_id = c.id AND p.deleted_at IS NULL';
    if ($publishedOnly) {
        $sql .= " AND p.status = 'published'";
    }
    if (!$includeDeleted) {
        $sql .= ' WHERE c.deleted_at IS NULL';
    }
    $sql .= ' GROUP BY c.id ORDER BY c.name ASC';

    return $pdo->query($sql)->fetchAll();
}

function blog_tag_list(bool $includeDeleted = false): array
{
    $pdo = blog_db();
    $sql = 'SELECT t.*, COUNT(pt.post_id) AS post_count FROM tags t LEFT JOIN post_tags pt ON pt.tag_id = t.id LEFT JOIN posts p ON p.id = pt.post_id AND p.deleted_at IS NULL';
    if (!$includeDeleted) {
        $sql .= ' WHERE t.deleted_at IS NULL';
    }
    $sql .= ' GROUP BY t.id ORDER BY t.name ASC';

    return $pdo->query($sql)->fetchAll();
}

function blog_get_category(int $id): ?array
{
    $stmt = blog_db()->prepare('SELECT * FROM categories WHERE id = :id AND deleted_at IS NULL LIMIT 1');
    $stmt->execute([':id' => $id]);

    $category = $stmt->fetch();

    return $category ?: null;
}

function blog_save_category(PDO $pdo, array $input, ?int $existingId = null): array
{
    $name = trim((string) ($input['name'] ?? ''));
    $description = trim((string) ($input['description'] ?? ''));
    $errors = [];

    if ($name === '') {
        $errors[] = 'Category name is required.';
    }

    if ($name !== '' && mb_strlen($name) > 100) {
        $errors[] = 'Category name must be 100 characters or less.';
    }

    if ($description !== '' && mb_strlen($description) > 255) {
        $errors[] = 'Category description must be 255 characters or less.';
    }

    if ($errors) {
        return ['success' => false, 'errors' => $errors];
    }

    $slug = blog_unique_slug($pdo, 'categories', blog_slugify($input['slug'] ?? $name), $existingId);
    $now = blog_now();

    if ($existingId !== null) {
        $pdo->prepare('UPDATE categories SET name = :name, slug = :slug, description = :description, updated_at = :updated_at WHERE id = :id')->execute([
            ':name' => $name,
            ':slug' => $slug,
            ':description' => $description !== '' ? $description : null,
            ':updated_at' => $now,
            ':id' => $existingId,
        ]);

        return ['success' => true, 'id' => $existingId, 'errors' => []];
    }

    $pdo->prepare('INSERT INTO categories (name, slug, description, created_at, updated_at) VALUES (:name, :slug, :description, :created_at, :updated_at)')->execute([
        ':name' => $name,
        ':slug' => $slug,
        ':description' => $description !== '' ? $description : null,
        ':created_at' => $now,
        ':updated_at' => $now,
    ]);

    return ['success' => true, 'id' => (int) $pdo->lastInsertId(), 'errors' => []];
}

function blog_save_tag(PDO $pdo, array $input, ?int $existingId = null): array
{
    $name = trim((string) ($input['name'] ?? ''));
    $errors = [];

    if ($name === '') {
        $errors[] = 'Tag name is required.';
    }

    if ($name !== '' && mb_strlen($name) > 80) {
        $errors[] = 'Tag name must be 80 characters or less.';
    }

    if ($errors) {
        return ['success' => false, 'errors' => $errors];
    }

    $slug = blog_unique_slug($pdo, 'tags', blog_slugify($input['slug'] ?? $name), $existingId);
    $now = blog_now();

    if ($existingId !== null) {
        $pdo->prepare('UPDATE tags SET name = :name, slug = :slug, updated_at = :updated_at WHERE id = :id')->execute([
            ':name' => $name,
            ':slug' => $slug,
            ':updated_at' => $now,
            ':id' => $existingId,
        ]);

        return ['success' => true, 'id' => $existingId, 'errors' => []];
    }

    $pdo->prepare('INSERT INTO tags (name, slug, created_at, updated_at) VALUES (:name, :slug, :created_at, :updated_at)')->execute([
        ':name' => $name,
        ':slug' => $slug,
        ':created_at' => $now,
        ':updated_at' => $now,
    ]);

    return ['success' => true, 'id' => (int) $pdo->lastInsertId(), 'errors' => []];
}

function blog_delete_category(PDO $pdo, int $id): void
{
    $pdo->prepare('UPDATE categories SET deleted_at = :deleted_at, updated_at = :updated_at WHERE id = :id')->execute([
        ':deleted_at' => blog_now(),
        ':updated_at' => blog_now(),
        ':id' => $id,
    ]);
}

function blog_delete_tag(PDO $pdo, int $id): void
{
    $pdo->prepare('UPDATE tags SET deleted_at = :deleted_at, updated_at = :updated_at WHERE id = :id')->execute([
        ':deleted_at' => blog_now(),
        ':updated_at' => blog_now(),
        ':id' => $id,
    ]);
}

function blog_delete_file(?string $relativePath): void
{
    $relativePath = blog_normalize_media_path($relativePath);
    if (!$relativePath) {
        return;
    }

    $fullPath = blog_root_path() . '/' . ltrim($relativePath, '/');
    if (is_file($fullPath)) {
        @unlink($fullPath);
    }
}

function blog_setting_get(string $key, ?string $default = null): ?string
{
    $stmt = blog_db()->prepare('SELECT setting_value FROM settings WHERE setting_key = :setting_key LIMIT 1');
    $stmt->execute([':setting_key' => $key]);

    $value = $stmt->fetchColumn();
    if ($value === false) {
        return $default;
    }

    return $value !== null ? (string) $value : $default;
}

function blog_setting_set(string $key, ?string $value): void
{
    $pdo = blog_db();
    $now = blog_now();
    $pdo->prepare('INSERT INTO settings (setting_key, setting_value, updated_at) VALUES (:setting_key, :setting_value, :updated_at) ON CONFLICT(setting_key) DO UPDATE SET setting_value = excluded.setting_value, updated_at = excluded.updated_at')->execute([
        ':setting_key' => $key,
        ':setting_value' => $value,
        ':updated_at' => $now,
    ]);
}

function blog_settings_map(array $defaults = []): array
{
    $settings = $defaults;
    $stmt = blog_db()->query('SELECT setting_key, setting_value FROM settings');
    foreach ($stmt->fetchAll() as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }

    return $settings;
}

function blog_normalize_media_path(?string $path): ?string
{
    if ($path === null || $path === '') {
        return null;
    }

    $path = str_replace('\\', '/', ltrim($path, '/'));

    if (strpos($path, 'storage/uploads/') === 0) {
        return $path;
    }

    if (strpos($path, 'uploads/') === 0) {
        return 'storage/' . $path;
    }

    return $path;
}

function blog_media_file_exists(?string $path): bool
{
    $path = blog_normalize_media_path($path);
    if (!$path) {
        return false;
    }

    return is_file(blog_root_path() . '/' . $path);
}

function blog_admin_media_url(?string $path): string
{
    $path = blog_normalize_media_path($path);
    if (!$path) {
        return '';
    }

    $fullPath = blog_root_path() . '/' . $path;
    $version = is_file($fullPath) ? (string) filemtime($fullPath) : (string) time();

    return '../' . $path . '?v=' . rawurlencode($version);
}

function blog_upload_error_message(int $errorCode): string
{
    return match ($errorCode) {
        UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Image is too large. Increase upload_max_filesize in PHP settings.',
        UPLOAD_ERR_PARTIAL => 'Image upload was interrupted. Please try again.',
        UPLOAD_ERR_NO_FILE => 'No image file was uploaded.',
        default => 'Image upload failed. Please use JPG, PNG, GIF, or WEBP under 5 MB.',
    };
}

function blog_detect_image_mime(array $file): string
{
    $tmpName = $file['tmp_name'] ?? '';
    if ($tmpName && is_file($tmpName) && function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo) {
            $mime = (string) finfo_file($finfo, $tmpName);
            finfo_close($finfo);
            if ($mime !== '') {
                return $mime;
            }
        }
    }

    $clientType = strtolower((string) ($file['type'] ?? ''));
    if ($clientType !== '' && strpos($clientType, 'image/') === 0) {
        return $clientType;
    }

    $extension = strtolower((string) pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
    $map = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
    ];

    return $map[$extension] ?? '';
}

function blog_reformat_files_array(array $files): array
{
    if (!isset($files['name'])) {
        return [];
    }

    if (!is_array($files['name'])) {
        return [[
            'name' => $files['name'] ?? '',
            'type' => $files['type'] ?? '',
            'tmp_name' => $files['tmp_name'] ?? '',
            'error' => $files['error'] ?? UPLOAD_ERR_NO_FILE,
            'size' => $files['size'] ?? 0,
        ]];
    }

    $normalized = [];
    $total = count($files['name']);

    for ($index = 0; $index < $total; $index++) {
        $normalized[] = [
            'name' => $files['name'][$index] ?? '',
            'type' => $files['type'][$index] ?? '',
            'tmp_name' => $files['tmp_name'][$index] ?? '',
            'error' => $files['error'][$index] ?? UPLOAD_ERR_NO_FILE,
            'size' => $files['size'][$index] ?? 0,
        ];
    }

    return $normalized;
}

function blog_store_image(array $file, string $group = 'blog'): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return null;
    }

    $allowedMimes = [
        'image/jpeg' => 'jpg',
        'image/pjpeg' => 'jpg',
        'image/png' => 'png',
        'image/x-png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];

    if ((int) ($file['size'] ?? 0) > BLOG_UPLOAD_MAX_BYTES || (int) @filesize($file['tmp_name']) > BLOG_UPLOAD_MAX_BYTES) {
        return null;
    }

    $mime = blog_detect_image_mime($file);
    if (!isset($allowedMimes[$mime])) {
        return null;
    }

    // Must be a real, decodable image (rejects polyglots and renamed scripts)
    $imageInfo = @getimagesize($file['tmp_name']);
    if ($imageInfo === false || empty($imageInfo['mime']) || !isset($allowedMimes[strtolower((string) $imageInfo['mime'])])) {
        return null;
    }

    $extension = $allowedMimes[$mime];
    $directory = blog_upload_dir($group);
    blog_ensure_directory($directory);

    $fileName = blog_slugify(pathinfo((string) $file['name'], PATHINFO_FILENAME)) . '-' . bin2hex(random_bytes(6)) . '.' . $extension;
    $targetPath = $directory . '/' . $fileName;
    $relativePath = 'storage/uploads/' . trim($group, '/') . '/' . $fileName;

    $saved = false;

    if (function_exists('imagecreatefromjpeg') && function_exists('imagecreatetruecolor')) {
        $imageResource = null;
        if (in_array($mime, ['image/jpeg', 'image/pjpeg'], true)) {
            $imageResource = @imagecreatefromjpeg($file['tmp_name']);
        } elseif (in_array($mime, ['image/png', 'image/x-png'], true)) {
            $imageResource = @imagecreatefrompng($file['tmp_name']);
        } elseif ($mime === 'image/webp' && function_exists('imagecreatefromwebp')) {
            $imageResource = @imagecreatefromwebp($file['tmp_name']);
        } elseif ($mime === 'image/gif') {
            $imageResource = @imagecreatefromgif($file['tmp_name']);
        }

        if ($imageResource) {
            $width = imagesx($imageResource);
            $height = imagesy($imageResource);
            $maxWidth = 1600;

            if ($width > $maxWidth) {
                $newWidth = $maxWidth;
                $newHeight = (int) round(($height / $width) * $newWidth);
                $canvas = imagecreatetruecolor($newWidth, $newHeight);

                if (in_array($mime, ['image/png', 'image/x-png', 'image/webp', 'image/gif'], true)) {
                    imagealphablending($canvas, false);
                    imagesavealpha($canvas, true);
                }

                imagecopyresampled($canvas, $imageResource, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                if (in_array($mime, ['image/jpeg', 'image/pjpeg'], true)) {
                    $saved = imagejpeg($canvas, $targetPath, 84);
                } elseif (in_array($mime, ['image/png', 'image/x-png'], true)) {
                    $saved = imagepng($canvas, $targetPath, 6);
                } elseif ($mime === 'image/webp' && function_exists('imagewebp')) {
                    $saved = imagewebp($canvas, $targetPath, 84);
                } else {
                    $saved = imagegif($canvas, $targetPath);
                }

                imagedestroy($canvas);
            } else {
                if (in_array($mime, ['image/jpeg', 'image/pjpeg'], true)) {
                    $saved = imagejpeg($imageResource, $targetPath, 84);
                } elseif (in_array($mime, ['image/png', 'image/x-png'], true)) {
                    $saved = imagepng($imageResource, $targetPath, 6);
                } elseif ($mime === 'image/webp' && function_exists('imagewebp')) {
                    $saved = imagewebp($imageResource, $targetPath, 84);
                } else {
                    $saved = imagegif($imageResource, $targetPath);
                }
            }

            imagedestroy($imageResource);
        }
    }

    if (!$saved && move_uploaded_file($file['tmp_name'], $targetPath)) {
        $saved = true;
    }

    return $saved ? $relativePath : null;
}

function blog_sync_post_tags(PDO $pdo, int $postId, string $rawTags): void
{
    $pdo->prepare('DELETE FROM post_tags WHERE post_id = :post_id')->execute([':post_id' => $postId]);

    $tags = array_filter(array_map(static fn ($tag) => trim($tag), preg_split('/,/', $rawTags) ?: []));
    $tags = array_values(array_unique($tags));

    if (!$tags) {
        return;
    }

    $tagLookup = $pdo->prepare('SELECT id FROM tags WHERE slug = :slug AND deleted_at IS NULL LIMIT 1');
    $now = blog_now();

    foreach ($tags as $tagName) {
        $slug = blog_slugify($tagName);
        $tagLookup->execute([':slug' => $slug]);
        $tagId = (int) $tagLookup->fetchColumn();

        if (!$tagId) {
            $savedTag = blog_save_tag($pdo, ['name' => $tagName]);
            if (!$savedTag['success']) {
                continue;
            }
            $tagId = (int) $savedTag['id'];
        }

        $pdo->prepare('INSERT OR IGNORE INTO post_tags (post_id, tag_id, created_at) VALUES (:post_id, :tag_id, :created_at)')->execute([
            ':post_id' => $postId,
            ':tag_id' => $tagId,
            ':created_at' => $now,
        ]);
    }
}

function blog_get_post_tags(int $postId): array
{
    $stmt = blog_db()->prepare('SELECT t.* FROM tags t INNER JOIN post_tags pt ON pt.tag_id = t.id WHERE pt.post_id = :post_id AND t.deleted_at IS NULL ORDER BY t.name ASC');
    $stmt->execute([':post_id' => $postId]);

    return $stmt->fetchAll();
}

function blog_get_post_images(int $postId): array
{
    $stmt = blog_db()->prepare('SELECT * FROM post_images WHERE post_id = :post_id ORDER BY sort_order ASC, id ASC');
    $stmt->execute([':post_id' => $postId]);

    return $stmt->fetchAll();
}

function blog_get_post(int $id): ?array
{
    $stmt = blog_db()->prepare('SELECT p.*, c.name AS category_name, c.slug AS category_slug, u.name AS author_name FROM posts p LEFT JOIN categories c ON c.id = p.category_id LEFT JOIN users u ON u.id = p.author_id WHERE p.id = :id AND p.deleted_at IS NULL LIMIT 1');
    $stmt->execute([':id' => $id]);

    $post = $stmt->fetch();
    if (!$post) {
        return null;
    }

    $post['tags'] = blog_get_post_tags($id);
    $post['images'] = blog_get_post_images($id);

    return $post;
}

function blog_get_post_by_slug(string $slug, bool $publicOnly = true): ?array
{
    $sql = 'SELECT p.*, c.name AS category_name, c.slug AS category_slug, u.name AS author_name FROM posts p LEFT JOIN categories c ON c.id = p.category_id LEFT JOIN users u ON u.id = p.author_id WHERE p.slug = :slug AND p.deleted_at IS NULL';
    if ($publicOnly) {
        $sql .= " AND p.status = 'published'";
    }
    $sql .= ' LIMIT 1';
    $stmt = blog_db()->prepare($sql);
    $stmt->execute([':slug' => $slug]);

    $post = $stmt->fetch();
    if (!$post) {
        return null;
    }

    $post['tags'] = blog_get_post_tags((int) $post['id']);
    $post['images'] = blog_get_post_images((int) $post['id']);

    return $post;
}

function blog_get_recent_posts(int $limit = 3): array
{
    $stmt = blog_db()->prepare("SELECT p.*, c.name AS category_name FROM posts p LEFT JOIN categories c ON c.id = p.category_id WHERE p.deleted_at IS NULL AND p.status = 'published' ORDER BY COALESCE(p.published_at, p.created_at) DESC LIMIT :limit");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function blog_get_related_posts(?int $postId, ?int $categoryId = null, int $limit = 3): array
{
    $pdo = blog_db();
    $sql = "SELECT p.*, c.name AS category_name FROM posts p LEFT JOIN categories c ON c.id = p.category_id WHERE p.deleted_at IS NULL AND p.status = 'published'";
    $params = [];

    if ($postId !== null) {
        $sql .= ' AND p.id <> :post_id';
        $params[':post_id'] = $postId;
    }

    if ($categoryId !== null && $categoryId > 0) {
        $sql .= ' AND p.category_id = :category_id';
        $params[':category_id'] = $categoryId;
    }

    $sql .= ' ORDER BY COALESCE(p.published_at, p.created_at) DESC LIMIT :limit';

    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_INT);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function blog_get_public_posts(array $filters = [], int $perPage = 9): array
{
    $pdo = blog_db();
    $page = max(1, (int) ($filters['page'] ?? 1));
    $search = trim((string) ($filters['search'] ?? ''));
    $categorySlug = trim((string) ($filters['category'] ?? ''));
    $offset = ($page - 1) * $perPage;

    $where = ["p.deleted_at IS NULL", "p.status = 'published'"];
    $params = [];

    if ($search !== '') {
        $where[] = '(p.title LIKE :search OR p.excerpt LIKE :search OR p.content LIKE :search)';
        $params[':search'] = '%' . $search . '%';
    }

    if ($categorySlug !== '') {
        $where[] = 'c.slug = :category_slug';
        $params[':category_slug'] = $categorySlug;
    }

    $whereSql = implode(' AND ', $where);
    $countStmt = $pdo->prepare('SELECT COUNT(*) FROM posts p LEFT JOIN categories c ON c.id = p.category_id WHERE ' . $whereSql);
    $countStmt->execute($params);
    $total = (int) $countStmt->fetchColumn();

    $listStmt = $pdo->prepare('SELECT p.*, c.name AS category_name, c.slug AS category_slug, (SELECT GROUP_CONCAT(t.name, ", ") FROM tags t INNER JOIN post_tags pt ON pt.tag_id = t.id WHERE pt.post_id = p.id AND t.deleted_at IS NULL) AS tag_names FROM posts p LEFT JOIN categories c ON c.id = p.category_id WHERE ' . $whereSql . ' ORDER BY COALESCE(p.published_at, p.created_at) DESC LIMIT :limit OFFSET :offset');
    foreach ($params as $key => $value) {
        $listStmt->bindValue($key, $value, PDO::PARAM_STR);
    }
    $listStmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $listStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $listStmt->execute();

    return [
        'items' => $listStmt->fetchAll(),
        'total' => $total,
        'page' => $page,
        'per_page' => $perPage,
        'pages' => max(1, (int) ceil($total / $perPage)),
    ];
}

function blog_get_post_editor_payload(?int $postId = null): ?array
{
    return $postId ? blog_get_post($postId) : null;
}

function blog_save_post(array $input, array $files, ?int $existingId = null): array
{
    $pdo = blog_db();
    $errors = [];

    $title = trim((string) ($input['title'] ?? ''));
    $slugInput = trim((string) ($input['slug'] ?? ''));
    $excerpt = trim((string) ($input['excerpt'] ?? ''));
    $content = trim((string) ($input['content'] ?? ''));
    $status = trim((string) ($input['status'] ?? 'draft'));
    $categoryId = (int) ($input['category_id'] ?? 0);
    $tags = trim((string) ($input['tags'] ?? ''));
    $featuredImageAlt = trim((string) ($input['featured_image_alt'] ?? ''));
    $seoTitle = trim((string) ($input['seo_title'] ?? ''));
    $metaDescription = trim((string) ($input['meta_description'] ?? ''));
    $metaKeywords = trim((string) ($input['meta_keywords'] ?? ''));
    $canonicalUrl = trim((string) ($input['canonical_url'] ?? ''));
    $ogTitle = trim((string) ($input['og_title'] ?? ''));
    $ogDescription = trim((string) ($input['og_description'] ?? ''));
    $twitterCard = trim((string) ($input['twitter_card'] ?? 'summary_large_image'));
    $schemaMarkup = trim((string) ($input['schema_markup'] ?? ''));

    if ($title === '') {
        $errors[] = 'Post title is required.';
    }

    if ($excerpt === '') {
        $errors[] = 'Excerpt is required.';
    }

    if ($content === '') {
        $errors[] = 'Post content is required.';
    }

    if (!in_array($status, ['draft', 'published'], true)) {
        $errors[] = 'Invalid status selected.';
    }

    if ($categoryId > 0 && !blog_get_category($categoryId)) {
        $errors[] = 'Selected category does not exist.';
    }

    if ($seoTitle !== '' && mb_strlen($seoTitle) > 70) {
        $errors[] = 'SEO title must be 70 characters or less.';
    }

    if ($metaDescription !== '' && mb_strlen($metaDescription) > 170) {
        $errors[] = 'Meta description must be 170 characters or less.';
    }

    if ($metaKeywords !== '' && mb_strlen($metaKeywords) > 255) {
        $errors[] = 'Meta keywords must be 255 characters or less.';
    }

    if ($canonicalUrl !== '' && !filter_var($canonicalUrl, FILTER_VALIDATE_URL)) {
        $errors[] = 'Canonical URL must be a valid URL.';
    }

    if ($twitterCard !== '' && !in_array($twitterCard, ['summary', 'summary_large_image'], true)) {
        $errors[] = 'Invalid Twitter card type selected.';
    }

    $removeFeaturedImage = !empty($input['remove_featured_image']);
    $deleteGalleryIds = array_map('intval', (array) ($input['delete_gallery_ids'] ?? []));
    $deleteGalleryIds = array_values(array_filter($deleteGalleryIds, static fn ($id) => $id > 0));

    $featuredImage = null;
    $featuredFile = $files['featured_image'] ?? null;
    if (is_array($featuredFile) && ($featuredFile['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        if (($featuredFile['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            $errors[] = 'Featured image: ' . blog_upload_error_message((int) $featuredFile['error']);
        } elseif (!empty($featuredFile['name'])) {
            $featuredImage = blog_store_image($featuredFile, 'blog');
            if ($featuredImage === null) {
                $errors[] = 'Featured image upload failed. Use JPG, PNG, GIF, or WEBP images up to 5 MB.';
            }
        }
    }

    $galleryUploads = [];
    if (!empty($files['gallery_images'])) {
        foreach (blog_reformat_files_array($files['gallery_images']) as $galleryFile) {
            if (empty($galleryFile['name'])) {
                continue;
            }
            if (($galleryFile['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                $errors[] = 'Gallery image: ' . blog_upload_error_message((int) $galleryFile['error']);
                continue;
            }
            $stored = blog_store_image($galleryFile, 'blog');
            if ($stored !== null) {
                $galleryUploads[] = $stored;
            } else {
                $errors[] = 'One gallery image could not be uploaded. Use JPG, PNG, GIF, or WEBP.';
            }
        }
    }

    if ($errors) {
        return ['success' => false, 'errors' => $errors];
    }

    $slug = blog_unique_slug($pdo, 'posts', blog_slugify($slugInput !== '' ? $slugInput : $title), $existingId);
    $now = blog_now();
    $currentUser = blog_current_user();
    $authorId = $currentUser ? (int) $currentUser['id'] : null;

    $pdo->beginTransaction();

    try {
        $existingPost = null;
        if ($existingId !== null) {
            $existingPost = blog_get_post($existingId);
        }

        if ($existingId !== null) {
            if ($featuredImage !== null) {
                $resolvedFeaturedImage = $featuredImage;
            } elseif ($removeFeaturedImage) {
                if (!empty($existingPost['featured_image'])) {
                    blog_delete_file($existingPost['featured_image']);
                }
                $resolvedFeaturedImage = null;
            } else {
                $resolvedFeaturedImage = blog_normalize_media_path($existingPost['featured_image'] ?? null);
            }

            if ($deleteGalleryIds) {
                $placeholders = implode(',', array_fill(0, count($deleteGalleryIds), '?'));
                $galleryStmt = $pdo->prepare('SELECT id, image_path FROM post_images WHERE post_id = ? AND id IN (' . $placeholders . ')');
                $galleryStmt->execute(array_merge([$existingId], $deleteGalleryIds));
                foreach ($galleryStmt->fetchAll() as $galleryRow) {
                    blog_delete_file($galleryRow['image_path']);
                }
                $deleteStmt = $pdo->prepare('DELETE FROM post_images WHERE post_id = ? AND id IN (' . $placeholders . ')');
                $deleteStmt->execute(array_merge([$existingId], $deleteGalleryIds));
            }

            $pdo->prepare('UPDATE posts SET title = :title, slug = :slug, excerpt = :excerpt, content = :content, status = :status, category_id = :category_id, author_id = :author_id, featured_image = :featured_image, featured_image_alt = :featured_image_alt, seo_title = :seo_title, meta_description = :meta_description, meta_keywords = :meta_keywords, canonical_url = :canonical_url, og_title = :og_title, og_description = :og_description, twitter_card = :twitter_card, schema_markup = :schema_markup, published_at = :published_at, updated_at = :updated_at WHERE id = :id')->execute([
                ':title' => $title,
                ':slug' => $slug,
                ':excerpt' => $excerpt,
                ':content' => $content,
                ':status' => $status,
                ':category_id' => $categoryId > 0 ? $categoryId : null,
                ':author_id' => $authorId,
                ':featured_image' => $resolvedFeaturedImage,
                ':featured_image_alt' => $featuredImageAlt !== '' ? $featuredImageAlt : ($existingPost['featured_image_alt'] ?? null),
                ':seo_title' => $seoTitle !== '' ? $seoTitle : null,
                ':meta_description' => $metaDescription !== '' ? $metaDescription : null,
                ':meta_keywords' => $metaKeywords !== '' ? $metaKeywords : null,
                ':canonical_url' => $canonicalUrl !== '' ? $canonicalUrl : null,
                ':og_title' => $ogTitle !== '' ? $ogTitle : null,
                ':og_description' => $ogDescription !== '' ? $ogDescription : null,
                ':twitter_card' => $twitterCard !== '' ? $twitterCard : null,
                ':schema_markup' => $schemaMarkup !== '' ? $schemaMarkup : null,
                ':published_at' => $status === 'published' ? ($existingPost['published_at'] ?? $now) : null,
                ':updated_at' => $now,
                ':id' => $existingId,
            ]);

            if ($featuredImage !== null && !empty($existingPost['featured_image']) && blog_normalize_media_path($existingPost['featured_image']) !== $featuredImage) {
                blog_delete_file($existingPost['featured_image']);
            }

            $pdo->prepare('DELETE FROM post_tags WHERE post_id = :post_id')->execute([':post_id' => $existingId]);
            blog_sync_post_tags($pdo, $existingId, $tags);

            $galleryOrderStmt = $pdo->prepare('SELECT COALESCE(MAX(sort_order), -1) FROM post_images WHERE post_id = :post_id');
            $galleryOrderStmt->execute([':post_id' => $existingId]);
            $nextSort = ((int) $galleryOrderStmt->fetchColumn()) + 1;

            foreach ($galleryUploads as $galleryPath) {
                $pdo->prepare('INSERT INTO post_images (post_id, image_path, alt_text, sort_order, created_at) VALUES (:post_id, :image_path, :alt_text, :sort_order, :created_at)')->execute([
                    ':post_id' => $existingId,
                    ':image_path' => $galleryPath,
                    ':alt_text' => $featuredImageAlt !== '' ? $featuredImageAlt : $title,
                    ':sort_order' => $nextSort,
                    ':created_at' => $now,
                ]);
                $nextSort++;
            }

            $pdo->commit();

            return ['success' => true, 'id' => $existingId, 'errors' => []];
        }

        $pdo->prepare('INSERT INTO posts (title, slug, excerpt, content, status, category_id, author_id, featured_image, featured_image_alt, seo_title, meta_description, meta_keywords, canonical_url, og_title, og_description, twitter_card, schema_markup, published_at, created_at, updated_at) VALUES (:title, :slug, :excerpt, :content, :status, :category_id, :author_id, :featured_image, :featured_image_alt, :seo_title, :meta_description, :meta_keywords, :canonical_url, :og_title, :og_description, :twitter_card, :schema_markup, :published_at, :created_at, :updated_at)')->execute([
            ':title' => $title,
            ':slug' => $slug,
            ':excerpt' => $excerpt,
            ':content' => $content,
            ':status' => $status,
            ':category_id' => $categoryId > 0 ? $categoryId : null,
            ':author_id' => $authorId,
            ':featured_image' => $featuredImage,
            ':featured_image_alt' => $featuredImageAlt !== '' ? $featuredImageAlt : null,
            ':seo_title' => $seoTitle !== '' ? $seoTitle : null,
            ':meta_description' => $metaDescription !== '' ? $metaDescription : null,
            ':meta_keywords' => $metaKeywords !== '' ? $metaKeywords : null,
            ':canonical_url' => $canonicalUrl !== '' ? $canonicalUrl : null,
            ':og_title' => $ogTitle !== '' ? $ogTitle : null,
            ':og_description' => $ogDescription !== '' ? $ogDescription : null,
            ':twitter_card' => $twitterCard !== '' ? $twitterCard : null,
            ':schema_markup' => $schemaMarkup !== '' ? $schemaMarkup : null,
            ':published_at' => $status === 'published' ? $now : null,
            ':created_at' => $now,
            ':updated_at' => $now,
        ]);

        $postId = (int) $pdo->lastInsertId();
        blog_sync_post_tags($pdo, $postId, $tags);

        $galleryOrder = 0;
        foreach ($galleryUploads as $galleryPath) {
            $pdo->prepare('INSERT INTO post_images (post_id, image_path, alt_text, sort_order, created_at) VALUES (:post_id, :image_path, :alt_text, :sort_order, :created_at)')->execute([
                ':post_id' => $postId,
                ':image_path' => $galleryPath,
                ':alt_text' => $featuredImageAlt !== '' ? $featuredImageAlt : $title,
                ':sort_order' => $galleryOrder,
                ':created_at' => $now,
            ]);
            $galleryOrder++;
        }

        $pdo->commit();

        return ['success' => true, 'id' => $postId, 'errors' => []];
    } catch (Throwable $throwable) {
        $pdo->rollBack();

        if ($featuredImage !== null) {
            blog_delete_file($featuredImage);
        }
        foreach ($galleryUploads as $galleryPath) {
            blog_delete_file($galleryPath);
        }

        return ['success' => false, 'errors' => ['Unable to save the post. ' . $throwable->getMessage()]];
    }
}

function blog_get_posts_list(array $filters = [], int $perPage = 10): array
{
    $pdo = blog_db();
    $page = max(1, (int) ($filters['page'] ?? 1));
    $search = trim((string) ($filters['search'] ?? ''));
    $status = trim((string) ($filters['status'] ?? ''));
    $categoryId = (int) ($filters['category_id'] ?? 0);
    $offset = ($page - 1) * $perPage;

    $where = ['p.deleted_at IS NULL'];
    $params = [];

    if ($search !== '') {
        $where[] = '(p.title LIKE :search OR p.slug LIKE :search OR p.excerpt LIKE :search)';
        $params[':search'] = '%' . $search . '%';
    }

    if (in_array($status, ['draft', 'published'], true)) {
        $where[] = 'p.status = :status';
        $params[':status'] = $status;
    }

    if ($categoryId > 0) {
        $where[] = 'p.category_id = :category_id';
        $params[':category_id'] = $categoryId;
    }

    $whereSql = implode(' AND ', $where);

    $countStmt = $pdo->prepare('SELECT COUNT(*) FROM posts p WHERE ' . $whereSql);
    $countStmt->execute($params);
    $total = (int) $countStmt->fetchColumn();

    $listStmt = $pdo->prepare('SELECT p.*, c.name AS category_name, (SELECT GROUP_CONCAT(t.name, ", ") FROM tags t INNER JOIN post_tags pt ON pt.tag_id = t.id WHERE pt.post_id = p.id AND t.deleted_at IS NULL) AS tag_names FROM posts p LEFT JOIN categories c ON c.id = p.category_id WHERE ' . $whereSql . ' ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset');
    foreach ($params as $key => $value) {
        $listStmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    $listStmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $listStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $listStmt->execute();

    return [
        'items' => $listStmt->fetchAll(),
        'total' => $total,
        'page' => $page,
        'per_page' => $perPage,
        'pages' => max(1, (int) ceil($total / $perPage)),
    ];
}

function blog_dashboard_stats(): array
{
    $pdo = blog_db();

    $stats = [
        'total_posts' => (int) $pdo->query('SELECT COUNT(*) FROM posts WHERE deleted_at IS NULL')->fetchColumn(),
        'published_posts' => (int) $pdo->query("SELECT COUNT(*) FROM posts WHERE status = 'published' AND deleted_at IS NULL")->fetchColumn(),
        'draft_posts' => (int) $pdo->query("SELECT COUNT(*) FROM posts WHERE status = 'draft' AND deleted_at IS NULL")->fetchColumn(),
        'categories' => (int) $pdo->query('SELECT COUNT(*) FROM categories WHERE deleted_at IS NULL')->fetchColumn(),
        'tags' => (int) $pdo->query('SELECT COUNT(*) FROM tags WHERE deleted_at IS NULL')->fetchColumn(),
        'media' => (int) $pdo->query('SELECT COUNT(*) FROM post_images')->fetchColumn(),
    ];

    $statusRows = $pdo->query("SELECT status, COUNT(*) AS total FROM posts WHERE deleted_at IS NULL GROUP BY status")->fetchAll();
    $statusMap = ['draft' => 0, 'published' => 0];
    foreach ($statusRows as $row) {
        $statusMap[$row['status']] = (int) $row['total'];
    }
    $stats['status_chart'] = $statusMap;

    $stats['category_chart'] = $pdo->query('SELECT c.name, COUNT(p.id) AS total FROM categories c LEFT JOIN posts p ON p.category_id = c.id AND p.deleted_at IS NULL WHERE c.deleted_at IS NULL GROUP BY c.id ORDER BY total DESC, c.name ASC LIMIT 6')->fetchAll();

    return $stats;
}

function blog_soft_delete_post(int $id): void
{
    $pdo = blog_db();
    $post = blog_get_post($id);
    if (!$post) {
        return;
    }

    $pdo->prepare('UPDATE posts SET deleted_at = :deleted_at, updated_at = :updated_at WHERE id = :id')->execute([
        ':deleted_at' => blog_now(),
        ':updated_at' => blog_now(),
        ':id' => $id,
    ]);
}
