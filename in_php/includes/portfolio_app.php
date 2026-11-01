<?php

require_once __DIR__ . '/blog_app.php';

function portfolio_ensure_schema(?PDO $pdo = null): void
{
    $pdo = $pdo ?? blog_db();
    $pdo->exec("CREATE TABLE IF NOT EXISTS portfolio_projects (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        slug TEXT NOT NULL,
        category TEXT NOT NULL DEFAULT 'Website',
        summary TEXT NOT NULL,
        overview TEXT NOT NULL,
        challenge TEXT NOT NULL DEFAULT '',
        result TEXT NOT NULL DEFAULT '',
        features TEXT NOT NULL DEFAULT '',
        client_name TEXT NOT NULL DEFAULT '',
        project_date TEXT NOT NULL DEFAULT '',
        image TEXT NULL,
        live_url TEXT NULL,
        status TEXT NOT NULL DEFAULT 'published',
        sort_order INTEGER NOT NULL DEFAULT 0,
        deleted_at TEXT NULL,
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL
    )");
    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_portfolio_status ON portfolio_projects(status)');
    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_portfolio_slug ON portfolio_projects(slug)');
    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_portfolio_deleted ON portfolio_projects(deleted_at)');

    $count = (int) $pdo->query('SELECT COUNT(*) FROM portfolio_projects WHERE deleted_at IS NULL')->fetchColumn();
    if ($count > 0) {
        return;
    }

    $now = blog_now();
    $seed = [
        [
            'RC Logistics',
            'rc-logistics',
            'Website',
            'A logistics website with a fast quote path and a mobile-first layout.',
            'RC Logistics needed a clear online home for domestic transport. The site explains their services, shows trust details, and gives visitors a direct way to request a quote.',
            'The old experience was slow on mobile and buried the quote request. Visitors had to hunt for a phone number before they could ask for a booking.',
            'The new site loads faster, explains the service in plain language, and puts call, WhatsApp, and quote actions where people can use them.',
            "Mobile-first layout\nQuote and contact actions\nService pages for domestic logistics\nSEO-ready page structure",
            'RC Logistics',
            'December 2024',
            'assets/images/projects/rg-logistic.png',
            'https://rclogistics.org.in/',
            1,
        ],
        [
            'Clinic Connect',
            'clinic-connect',
            'App',
            'A sample appointment app for a clinic that wants fewer phone queues.',
            'Clinic Connect is a sample case for a small clinic. Patients see services, pick a time, and send an appointment request without calling the front desk.',
            'Staff were repeating the same slot questions on every call. The clinic wanted a simple first version before a full hospital system.',
            'The sample flow covers services, a booking request, and a confirmation screen the clinic can extend later.',
            "Service list and doctor hours\nAppointment request form\nConfirmation screen\nAdmin notes for the front desk",
            'Sample clinic brief',
            '2025',
            '',
            '',
            2,
        ],
        [
            'Ledger Desk',
            'ledger-desk',
            'Software',
            'A sample billing desk for a shop that still tracks invoices in spreadsheets.',
            'Ledger Desk is a sample software story. It replaces a shared sheet with invoices, customer names, and a short daily sales view.',
            'The shop owner could not see which bills were unpaid without opening several files. Staff also typed the same customer details more than once.',
            'The sample system keeps customers, invoices, and a basic due list in one place, ready to grow into stock and reports.',
            "Customer records\nInvoice entry\nDue list\nSimple daily summary",
            'Sample retail brief',
            '2025',
            '',
            '',
            3,
        ],
        [
            'Restaurant Food & Drinks',
            'restaurant-food-drinks-logo',
            'Branding',
            'A flat illustrated mark for a restaurant brand.',
            'A food brand needed a logo that reads clearly on menus, packaging, and social posts. The mark uses simple food and drink shapes so it stays recognizable at small sizes.',
            'The previous wordmark disappeared when it was printed small on cups and delivery stickers.',
            'The new mark works in one color or full color and can sit beside the restaurant name without crowding it.',
            "Menu and packaging use\nSmall-size clarity\nOne-color version\nSocial profile crop",
            'Restaurant brief',
            '2025',
            'assets/images/portfolio-logo-design/Abstract Flat Illustrative Restaurant Food & Drinks Logo.png',
            '',
            4,
        ],
        [
            'Minimal Fashion Mark',
            'minimal-fashion-mark',
            'Branding',
            'A gold, white, and black wordmark for a fashion label.',
            'The label wanted a quiet luxury mark: high contrast, few shapes, and a wordmark that can live on a black or white background.',
            'Busy icons were competing with the clothing photography. The brand needed the logo to step back and still be remembered.',
            'The wordmark holds up on lookbooks, tags, and a dark storefront sign.',
            "Wordmark for labels and tags\nGold, white, and black palette\nDark and light versions\nLookbook placement",
            'Fashion brief',
            '2025',
            'assets/images/portfolio-logo-design/Gold White and Black Minimalist Fashion Logo.png',
            '',
            5,
        ],
        [
            'Esports Eagle',
            'esports-eagle',
            'Branding',
            'An eagle emblem for an esports team.',
            'The team needed a mark that stays sharp on jerseys, stream overlays, and a small app icon.',
            'A detailed illustration turned muddy once it was reduced to a profile picture.',
            'The eagle keeps a strong silhouette in navy and orange, including a one-color print version.',
            "Jersey and overlay use\nApp-icon crop\nNavy and orange palette\nOne-color print version",
            'Esports brief',
            '2025',
            'assets/images/portfolio-logo-design/Navy and Orange Esports Illustrative Gaming Eagle Logo.png',
            '',
            6,
        ],
    ];

    $stmt = $pdo->prepare('INSERT INTO portfolio_projects (title, slug, category, summary, overview, challenge, result, features, client_name, project_date, image, live_url, status, sort_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    foreach ($seed as $row) {
        $stmt->execute([
            $row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7], $row[8], $row[9], $row[10], $row[11], 'published', $row[12], $now, $now,
        ]);
    }
}

function portfolio_list(array $filters = []): array
{
    portfolio_ensure_schema();
    $pdo = blog_db();
    $sql = 'SELECT * FROM portfolio_projects WHERE deleted_at IS NULL';
    $params = [];

    if (!empty($filters['status'])) {
        $sql .= ' AND status = ?';
        $params[] = $filters['status'];
    }
    if (!empty($filters['category'])) {
        $sql .= ' AND category = ?';
        $params[] = $filters['category'];
    }
    if (!empty($filters['search'])) {
        $sql .= ' AND (title LIKE ? OR summary LIKE ? OR client_name LIKE ?)';
        $q = '%' . $filters['search'] . '%';
        $params[] = $q;
        $params[] = $q;
        $params[] = $q;
    }

    $sql .= ' ORDER BY sort_order ASC, updated_at DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
}

function portfolio_published(?string $category = null): array
{
    $filters = ['status' => 'published'];
    if ($category) {
        $filters['category'] = $category;
    }

    return portfolio_list($filters);
}

function portfolio_get(int $id): ?array
{
    portfolio_ensure_schema();
    $stmt = blog_db()->prepare('SELECT * FROM portfolio_projects WHERE id = ? AND deleted_at IS NULL LIMIT 1');
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    return $row ?: null;
}

function portfolio_get_by_slug(string $slug): ?array
{
    portfolio_ensure_schema();
    $stmt = blog_db()->prepare("SELECT * FROM portfolio_projects WHERE slug = ? AND deleted_at IS NULL AND status = 'published' LIMIT 1");
    $stmt->execute([$slug]);
    $row = $stmt->fetch();

    return $row ?: null;
}

function portfolio_categories(): array
{
    return ['Website', 'App', 'Software', 'Branding'];
}

function portfolio_features(array $project): array
{
    $lines = preg_split('/\r\n|\r|\n/', (string) ($project['features'] ?? '')) ?: [];
    $clean = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line !== '') {
            $clean[] = $line;
        }
    }

    return $clean;
}

function portfolio_save(array $data, ?int $id = null, ?array $imageFile = null): array
{
    portfolio_ensure_schema();
    $title = trim((string) ($data['title'] ?? ''));
    $category = trim((string) ($data['category'] ?? 'Website'));
    if (!in_array($category, portfolio_categories(), true)) {
        $category = 'Website';
    }
    $summary = trim((string) ($data['summary'] ?? ''));
    $overview = trim((string) ($data['overview'] ?? ''));
    $challenge = trim((string) ($data['challenge'] ?? ''));
    $result = trim((string) ($data['result'] ?? ''));
    $features = trim((string) ($data['features'] ?? ''));
    $client = trim((string) ($data['client_name'] ?? ''));
    $projectDate = trim((string) ($data['project_date'] ?? ''));
    $liveUrl = trim((string) ($data['live_url'] ?? ''));
    $status = ($data['status'] ?? 'published') === 'draft' ? 'draft' : 'published';
    $sortOrder = (int) ($data['sort_order'] ?? 0);
    $now = blog_now();

    if ($title === '' || $summary === '' || $overview === '') {
        return ['success' => false, 'errors' => ['Title, summary, and overview are required.']];
    }

    $pdo = blog_db();
    $existing = $id ? portfolio_get($id) : null;
    $slugInput = trim((string) ($data['slug'] ?? ''));
    $slug = blog_unique_slug($pdo, 'portfolio_projects', blog_slugify($slugInput !== '' ? $slugInput : $title), $id);
    $image = $existing['image'] ?? '';

    if ($imageFile && ($imageFile['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        $stored = blog_store_image($imageFile, 'portfolio');
        if (!$stored) {
            return ['success' => false, 'errors' => ['Upload a JPG, PNG, WEBP, or GIF image.']];
        }
        $image = $stored;
    }

    if ($id && $existing) {
        $stmt = $pdo->prepare('UPDATE portfolio_projects SET title = ?, slug = ?, category = ?, summary = ?, overview = ?, challenge = ?, result = ?, features = ?, client_name = ?, project_date = ?, image = ?, live_url = ?, status = ?, sort_order = ?, updated_at = ? WHERE id = ? AND deleted_at IS NULL');
        $stmt->execute([$title, $slug, $category, $summary, $overview, $challenge, $result, $features, $client, $projectDate, $image, $liveUrl, $status, $sortOrder, $now, $id]);

        return ['success' => true, 'id' => $id];
    }

    $stmt = $pdo->prepare('INSERT INTO portfolio_projects (title, slug, category, summary, overview, challenge, result, features, client_name, project_date, image, live_url, status, sort_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$title, $slug, $category, $summary, $overview, $challenge, $result, $features, $client, $projectDate, $image, $liveUrl, $status, $sortOrder, $now, $now]);

    return ['success' => true, 'id' => (int) $pdo->lastInsertId()];
}

function portfolio_soft_delete(int $id): void
{
    portfolio_ensure_schema();
    $stmt = blog_db()->prepare('UPDATE portfolio_projects SET deleted_at = ?, updated_at = ? WHERE id = ?');
    $stmt->execute([blog_now(), blog_now(), $id]);
}

function portfolio_count_published(): int
{
    portfolio_ensure_schema();

    return (int) blog_db()->query("SELECT COUNT(*) FROM portfolio_projects WHERE deleted_at IS NULL AND status = 'published'")->fetchColumn();
}
