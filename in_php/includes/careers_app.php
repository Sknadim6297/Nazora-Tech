<?php

require_once __DIR__ . '/blog_app.php';

function careers_ensure_schema(?PDO $pdo = null): void
{
    $pdo = $pdo ?? blog_db();
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

    $count = (int) $pdo->query('SELECT COUNT(*) FROM careers WHERE deleted_at IS NULL')->fetchColumn();
    if ($count === 0) {
        $now = blog_now();
        $seed = [
            ['Frontend Developer', 'Full-time', 'Kolkata / Hybrid', '1–3 years experience', 'Build responsive UI with HTML, CSS, JavaScript, and modern frameworks. Collaborate with design and backend.'],
            ['PHP / Laravel Developer', 'Full-time', 'Kolkata / Hybrid', '2+ years experience', 'Develop and maintain business applications, APIs, and admin systems with clean, secure PHP code.'],
            ['Digital Marketing Intern', 'Internship', 'Kolkata', 'Freshers welcome', 'Support SEO, content, and campaign work for client projects and our own growth channels.'],
        ];
        $stmt = $pdo->prepare('INSERT INTO careers (title, employment_type, location, experience, description, apply_url, status, sort_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        foreach ($seed as $i => $row) {
            $stmt->execute([$row[0], $row[1], $row[2], $row[3], $row[4], 'contact', 'published', $i + 1, $now, $now]);
        }
    }
}

function careers_list(array $filters = []): array
{
    careers_ensure_schema();
    $pdo = blog_db();
    $sql = 'SELECT * FROM careers WHERE deleted_at IS NULL';
    $params = [];

    if (!empty($filters['status'])) {
        $sql .= ' AND status = ?';
        $params[] = $filters['status'];
    }
    if (!empty($filters['search'])) {
        $sql .= ' AND (title LIKE ? OR description LIKE ? OR location LIKE ?)';
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

function careers_published(): array
{
    return careers_list(['status' => 'published']);
}

function careers_get(int $id): ?array
{
    careers_ensure_schema();
    $stmt = blog_db()->prepare('SELECT * FROM careers WHERE id = ? AND deleted_at IS NULL LIMIT 1');
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    return $row ?: null;
}

function careers_save(array $data, ?int $id = null): array
{
    careers_ensure_schema();
    $title = trim((string) ($data['title'] ?? ''));
    $type = trim((string) ($data['employment_type'] ?? 'Full-time'));
    $location = trim((string) ($data['location'] ?? 'Kolkata'));
    $experience = trim((string) ($data['experience'] ?? ''));
    $description = trim((string) ($data['description'] ?? ''));
    $applyUrl = trim((string) ($data['apply_url'] ?? 'contact'));
    $status = ($data['status'] ?? 'published') === 'draft' ? 'draft' : 'published';
    $sortOrder = (int) ($data['sort_order'] ?? 0);
    $now = blog_now();

    if ($title === '' || $description === '') {
        return ['success' => false, 'errors' => ['Title and description are required.']];
    }

    $pdo = blog_db();
    if ($id) {
        $stmt = $pdo->prepare('UPDATE careers SET title = ?, employment_type = ?, location = ?, experience = ?, description = ?, apply_url = ?, status = ?, sort_order = ?, updated_at = ? WHERE id = ? AND deleted_at IS NULL');
        $stmt->execute([$title, $type, $location, $experience, $description, $applyUrl, $status, $sortOrder, $now, $id]);
        return ['success' => true, 'id' => $id];
    }

    $stmt = $pdo->prepare('INSERT INTO careers (title, employment_type, location, experience, description, apply_url, status, sort_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$title, $type, $location, $experience, $description, $applyUrl, $status, $sortOrder, $now, $now]);

    return ['success' => true, 'id' => (int) $pdo->lastInsertId()];
}

function careers_soft_delete(int $id): void
{
    careers_ensure_schema();
    $stmt = blog_db()->prepare('UPDATE careers SET deleted_at = ?, updated_at = ? WHERE id = ?');
    $stmt->execute([blog_now(), blog_now(), $id]);
}

function careers_count_active(): int
{
    careers_ensure_schema();
    return (int) blog_db()->query("SELECT COUNT(*) FROM careers WHERE deleted_at IS NULL AND status = 'published'")->fetchColumn();
}

function careers_apply_href(string $applyUrl): string
{
    $applyUrl = trim($applyUrl);
    if ($applyUrl === '' || strcasecmp($applyUrl, 'contact') === 0) {
        return 'contact';
    }
    if (preg_match('#^https?://#i', $applyUrl) && filter_var($applyUrl, FILTER_VALIDATE_URL)) {
        return $applyUrl;
    }
    if (str_starts_with(strtolower($applyUrl), 'mailto:')) {
        $addr = substr($applyUrl, 7);
        if (filter_var($addr, FILTER_VALIDATE_EMAIL)) {
            return 'mailto:' . $addr;
        }
    }
    if (preg_match('/^[a-zA-Z0-9_.\-\/?=&%]+$/', $applyUrl) && !str_contains($applyUrl, '//')) {
        return $applyUrl;
    }

    return 'contact';
}
