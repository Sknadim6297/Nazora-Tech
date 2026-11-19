<?php
/**
 * Website enquiries (leads): storage, admin listing, notes, retention and CSV helpers.
 *
 * Data minimisation: only what the visitor typed into the form plus the form source, status and
 * delivery status are stored. No IP address, user agent or tracking identifiers.
 */

require_once __DIR__ . '/blog_app.php';

const ENQUIRY_STATUSES = [
    'new' => 'New',
    'contacted' => 'Contacted',
    'qualified' => 'Qualified',
    'closed' => 'Closed',
];

const ENQUIRY_EMAIL_STATUSES = [
    'pending' => 'Pending',
    'sending' => 'Sending (not confirmed)',
    'sent' => 'Emailed',
    'failed' => 'Email failed',
    'disabled' => 'Email not configured',
    'imported' => 'Imported backup',
];

const ENQUIRY_CUSTOMER_EMAIL_STATUSES = [
    'pending' => 'Pending',
    'sending' => 'Sending (not confirmed)',
    'sent' => 'Confirmation sent',
    'failed' => 'Confirmation failed',
    'off' => 'Auto-confirmation off',
    'disabled' => 'Email not configured',
    'skipped' => 'Not sent (invalid address)',
];

// A repeat of the same form (same email, phone and message) within this window is treated as a retry.
const ENQUIRY_DUPLICATE_WINDOW = 600;

const ENQUIRY_SOURCES = [
    'home_consult' => 'Home page form',
    'contact_page' => 'Contact page form',
    'about_consult' => 'About page form',
    'legacy_file' => 'Imported file backup',
    'website' => 'Website form',
];

const ENQUIRY_RETENTION_CHOICES = [6, 12, 24];

function enquiries_db(): PDO
{
    static $ready = false;

    $pdo = blog_db();
    if (!$ready) {
        enquiries_init_schema($pdo);
        $ready = true;
    }

    return $pdo;
}

function enquiries_init_schema(PDO $pdo): void
{
    // Additive only: never drops or rewrites existing rows
    $pdo->exec("CREATE TABLE IF NOT EXISTS enquiries (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL,
        phone TEXT NOT NULL,
        service TEXT NULL,
        message TEXT NOT NULL,
        source TEXT NOT NULL DEFAULT 'website',
        status TEXT NOT NULL DEFAULT 'new',
        email_status TEXT NOT NULL DEFAULT 'pending',
        legacy_ref TEXT NULL UNIQUE,
        status_changed_at TEXT NULL,
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS enquiry_notes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        enquiry_id INTEGER NOT NULL,
        author_name TEXT NULL,
        note TEXT NOT NULL,
        is_system INTEGER NOT NULL DEFAULT 0,
        created_at TEXT NOT NULL,
        FOREIGN KEY (enquiry_id) REFERENCES enquiries(id) ON DELETE CASCADE
    )");

    $columns = array_column($pdo->query('PRAGMA table_info(enquiries)')->fetchAll(), 'name');
    if (!in_array('customer_email_status', $columns, true)) {
        // Existing rows predate customer confirmations, which were off by default
        $pdo->exec("ALTER TABLE enquiries ADD COLUMN customer_email_status TEXT NOT NULL DEFAULT 'off'");
    }

    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_enquiries_status ON enquiries(status)');
    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_enquiries_created ON enquiries(created_at)');
    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_enquiry_notes_enquiry ON enquiry_notes(enquiry_id)');
}

function enquiry_status_label(string $status): string
{
    return ENQUIRY_STATUSES[$status] ?? ucfirst($status);
}

function enquiry_email_status_label(string $status): string
{
    return ENQUIRY_EMAIL_STATUSES[$status] ?? ucfirst($status);
}

function enquiry_source_label(string $source): string
{
    return ENQUIRY_SOURCES[$source] ?? 'Website form';
}

function enquiry_normalize_source(string $source): string
{
    return isset(ENQUIRY_SOURCES[$source]) && $source !== 'legacy_file' ? $source : 'website';
}

/**
 * Inserts a new enquiry (never updates or replaces an existing row). Returns the new id.
 */
function enquiry_create(array $data): int
{
    $pdo = enquiries_db();
    $now = blog_now();

    $stmt = $pdo->prepare('INSERT INTO enquiries (name, email, phone, service, message, source, status, email_status, customer_email_status, created_at, updated_at)
        VALUES (:name, :email, :phone, :service, :message, :source, :status, :email_status, :customer_email_status, :created_at, :updated_at)');
    $stmt->execute([
        ':name' => (string) $data['name'],
        ':email' => (string) $data['email'],
        ':phone' => (string) $data['phone'],
        ':service' => ($data['service'] ?? '') !== '' ? (string) $data['service'] : null,
        ':message' => (string) $data['message'],
        ':source' => enquiry_normalize_source((string) ($data['source'] ?? 'website')),
        ':status' => 'new',
        ':email_status' => 'pending',
        ':customer_email_status' => 'pending',
        ':created_at' => $now,
        ':updated_at' => $now,
    ]);

    return (int) $pdo->lastInsertId();
}

/**
 * Finds the same submission (email, phone and message) received within the retry window,
 * so a double click or network retry does not create a second lead or a second email.
 */
function enquiry_find_recent_duplicate(array $data, int $windowSeconds = ENQUIRY_DUPLICATE_WINDOW): ?array
{
    $stmt = enquiries_db()->prepare('SELECT * FROM enquiries
        WHERE email = :email AND phone = :phone AND message = :message AND legacy_ref IS NULL AND created_at >= :since
        ORDER BY id DESC LIMIT 1');
    $stmt->execute([
        ':email' => (string) $data['email'],
        ':phone' => (string) $data['phone'],
        ':message' => (string) $data['message'],
        ':since' => date('Y-m-d H:i:s', time() - $windowSeconds),
    ]);
    $row = $stmt->fetch();

    return $row ?: null;
}

function enquiry_email_column(string $channel): string
{
    return $channel === 'customer' ? 'customer_email_status' : 'email_status';
}

function enquiry_email_statuses(string $channel): array
{
    return $channel === 'customer' ? ENQUIRY_CUSTOMER_EMAIL_STATUSES : ENQUIRY_EMAIL_STATUSES;
}

/**
 * Atomically moves a delivery channel ('admin' or 'customer') from pending to sending.
 * Only the request that wins the claim may send, which prevents duplicate emails.
 */
function enquiry_claim_email(int $id, string $channel): bool
{
    $column = enquiry_email_column($channel);
    $stmt = enquiries_db()->prepare("UPDATE enquiries SET {$column} = 'sending' WHERE id = :id AND {$column} = 'pending'");
    $stmt->execute([':id' => $id]);

    return $stmt->rowCount() === 1;
}

function enquiry_set_delivery_status(int $id, string $channel, string $status): void
{
    if (!isset(enquiry_email_statuses($channel)[$status])) {
        return;
    }

    $column = enquiry_email_column($channel);
    enquiries_db()->prepare("UPDATE enquiries SET {$column} = :status WHERE id = :id")->execute([
        ':status' => $status,
        ':id' => $id,
    ]);
}

function enquiry_set_email_status(int $id, string $status): void
{
    enquiry_set_delivery_status($id, 'admin', $status);
}

function enquiry_customer_email_status_label(string $status): string
{
    return ENQUIRY_CUSTOMER_EMAIL_STATUSES[$status] ?? ucfirst($status);
}

function enquiry_get(int $id): ?array
{
    $stmt = enquiries_db()->prepare('SELECT * FROM enquiries WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();

    return $row ?: null;
}

function enquiry_notes(int $enquiryId): array
{
    $stmt = enquiries_db()->prepare('SELECT * FROM enquiry_notes WHERE enquiry_id = :id ORDER BY created_at DESC, id DESC');
    $stmt->execute([':id' => $enquiryId]);

    return $stmt->fetchAll();
}

function enquiry_add_note(int $enquiryId, string $note, ?array $user, bool $system = false): bool
{
    $note = trim(str_replace(["\r\n", "\r"], "\n", $note));
    $note = (string) preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/u', '', $note);
    if ($note === '' || !enquiry_get($enquiryId)) {
        return false;
    }

    $pdo = enquiries_db();
    $now = blog_now();
    $pdo->prepare('INSERT INTO enquiry_notes (enquiry_id, author_name, note, is_system, created_at) VALUES (:enquiry_id, :author_name, :note, :is_system, :created_at)')->execute([
        ':enquiry_id' => $enquiryId,
        ':author_name' => $user['name'] ?? null,
        ':note' => mb_substr($note, 0, 4000, 'UTF-8'),
        ':is_system' => $system ? 1 : 0,
        ':created_at' => $now,
    ]);
    $pdo->prepare('UPDATE enquiries SET updated_at = :updated_at WHERE id = :id')->execute([
        ':updated_at' => $now,
        ':id' => $enquiryId,
    ]);

    return true;
}

function enquiry_update_status(int $enquiryId, string $status, ?array $user): bool
{
    if (!isset(ENQUIRY_STATUSES[$status])) {
        return false;
    }

    $enquiry = enquiry_get($enquiryId);
    if (!$enquiry) {
        return false;
    }
    if ($enquiry['status'] === $status) {
        return true;
    }

    $now = blog_now();
    enquiries_db()->prepare('UPDATE enquiries SET status = :status, status_changed_at = :changed, updated_at = :updated WHERE id = :id')->execute([
        ':status' => $status,
        ':changed' => $now,
        ':updated' => $now,
        ':id' => $enquiryId,
    ]);

    enquiry_add_note(
        $enquiryId,
        'Status changed from ' . enquiry_status_label((string) $enquiry['status']) . ' to ' . enquiry_status_label($status) . '.',
        $user,
        true
    );

    return true;
}

function enquiry_delete(int $enquiryId): bool
{
    $stmt = enquiries_db()->prepare('DELETE FROM enquiries WHERE id = :id');
    $stmt->execute([':id' => $enquiryId]);

    return $stmt->rowCount() > 0;
}

function enquiries_valid_date(string $value): string
{
    $date = DateTime::createFromFormat('!Y-m-d', $value);

    return ($date && $date->format('Y-m-d') === $value) ? $value : '';
}

/**
 * Normalises list filters from a request array (GET or POST).
 */
function enquiries_filters_from(array $source): array
{
    $status = (string) ($source['status'] ?? '');

    return [
        'search' => mb_substr(trim((string) ($source['search'] ?? '')), 0, 100, 'UTF-8'),
        'status' => isset(ENQUIRY_STATUSES[$status]) ? $status : '',
        'service' => mb_substr(trim((string) ($source['service'] ?? '')), 0, 100, 'UTF-8'),
        'from' => enquiries_valid_date(trim((string) ($source['from'] ?? ''))),
        'to' => enquiries_valid_date(trim((string) ($source['to'] ?? ''))),
        'page' => max(1, (int) ($source['page'] ?? 1)),
    ];
}

function enquiries_where(array $filters): array
{
    $where = ['1 = 1'];
    $params = [];

    if ($filters['search'] !== '') {
        $like = '%' . addcslashes($filters['search'], '\\%_') . '%';
        $where[] = "(name LIKE :search ESCAPE '\\' OR email LIKE :search ESCAPE '\\' OR phone LIKE :search ESCAPE '\\' OR message LIKE :search ESCAPE '\\' OR service LIKE :search ESCAPE '\\')";
        $params[':search'] = $like;
    }
    if ($filters['status'] !== '') {
        $where[] = 'status = :status';
        $params[':status'] = $filters['status'];
    }
    if ($filters['service'] !== '') {
        $where[] = 'service = :service';
        $params[':service'] = $filters['service'];
    }
    if ($filters['from'] !== '') {
        $where[] = 'created_at >= :from';
        $params[':from'] = $filters['from'] . ' 00:00:00';
    }
    if ($filters['to'] !== '') {
        $where[] = 'created_at <= :to';
        $params[':to'] = $filters['to'] . ' 23:59:59';
    }

    return [implode(' AND ', $where), $params];
}

function enquiries_list(array $filters, int $perPage = 20): array
{
    $pdo = enquiries_db();
    [$whereSql, $params] = enquiries_where($filters);

    $countStmt = $pdo->prepare('SELECT COUNT(*) FROM enquiries WHERE ' . $whereSql);
    $countStmt->execute($params);
    $total = (int) $countStmt->fetchColumn();

    $pages = max(1, (int) ceil($total / $perPage));
    $page = min(max(1, (int) $filters['page']), $pages);

    $listStmt = $pdo->prepare('SELECT id, name, email, phone, service, source, status, email_status, created_at, updated_at,
            (SELECT COUNT(*) FROM enquiry_notes n WHERE n.enquiry_id = enquiries.id AND n.is_system = 0) AS note_count
        FROM enquiries WHERE ' . $whereSql . ' ORDER BY created_at DESC, id DESC LIMIT :limit OFFSET :offset');
    foreach ($params as $key => $value) {
        $listStmt->bindValue($key, $value, PDO::PARAM_STR);
    }
    $listStmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $listStmt->bindValue(':offset', ($page - 1) * $perPage, PDO::PARAM_INT);
    $listStmt->execute();

    return [
        'items' => $listStmt->fetchAll(),
        'total' => $total,
        'page' => $page,
        'pages' => $pages,
        'per_page' => $perPage,
    ];
}

function enquiries_export_rows(array $filters): array
{
    [$whereSql, $params] = enquiries_where($filters);
    $stmt = enquiries_db()->prepare('SELECT id, created_at, name, email, phone, service, message, source, status, email_status, customer_email_status, updated_at
        FROM enquiries WHERE ' . $whereSql . ' ORDER BY created_at DESC, id DESC');
    $stmt->execute($params);

    return $stmt->fetchAll();
}

function enquiries_status_counts(): array
{
    $counts = array_fill_keys(array_keys(ENQUIRY_STATUSES), 0);
    foreach (enquiries_db()->query('SELECT status, COUNT(*) AS total FROM enquiries GROUP BY status')->fetchAll() as $row) {
        if (isset($counts[$row['status']])) {
            $counts[$row['status']] = (int) $row['total'];
        }
    }
    $counts['all'] = array_sum($counts);

    return $counts;
}

function enquiries_count_new(): int
{
    try {
        return (int) enquiries_db()->query("SELECT COUNT(*) FROM enquiries WHERE status = 'new'")->fetchColumn();
    } catch (Throwable $exception) {
        return 0;
    }
}

function enquiries_recent(int $limit = 5): array
{
    $stmt = enquiries_db()->prepare('SELECT id, name, service, status, created_at FROM enquiries ORDER BY created_at DESC, id DESC LIMIT :limit');
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function enquiries_services(): array
{
    return array_column(
        enquiries_db()->query("SELECT DISTINCT service FROM enquiries WHERE service IS NOT NULL AND service <> '' ORDER BY service ASC")->fetchAll(),
        'service'
    );
}

/* ─── Retention ─────────────────────────────────────────────────────────── */

function enquiries_retention_cutoff(int $months): string
{
    $months = in_array($months, ENQUIRY_RETENTION_CHOICES, true) ? $months : 12;

    return (new DateTime('now'))->modify('-' . $months . ' months')->format('Y-m-d H:i:s');
}

function enquiries_count_purgeable(int $months): int
{
    $stmt = enquiries_db()->prepare("SELECT COUNT(*) FROM enquiries WHERE status = 'closed' AND COALESCE(status_changed_at, updated_at) < :cutoff");
    $stmt->execute([':cutoff' => enquiries_retention_cutoff($months)]);

    return (int) $stmt->fetchColumn();
}

/**
 * Permanently deletes Closed enquiries whose last status change is older than $months. Admin-triggered only.
 */
function enquiries_purge_closed(int $months): int
{
    $stmt = enquiries_db()->prepare("DELETE FROM enquiries WHERE status = 'closed' AND COALESCE(status_changed_at, updated_at) < :cutoff");
    $stmt->execute([':cutoff' => enquiries_retention_cutoff($months)]);

    return $stmt->rowCount();
}

/* ─── CSV ───────────────────────────────────────────────────────────────── */

/**
 * Neutralises spreadsheet formula injection (OWASP CSV injection guidance).
 */
function enquiries_csv_safe(?string $value): string
{
    $value = (string) $value;
    $value = str_replace("\0", '', $value);
    if ($value !== '' && in_array($value[0], ['=', '+', '-', '@', "\t", "\r", "\n", '|', '%'], true)) {
        $value = "'" . $value;
    }

    return $value;
}

/* ─── Legacy text-file backups (storage/submissions) ────────────────────── */

function enquiries_legacy_dir(): string
{
    return blog_storage_path() . '/submissions';
}

function enquiries_legacy_files(): array
{
    $files = glob(enquiries_legacy_dir() . '/submission_*.txt') ?: [];
    sort($files);

    return $files;
}

function enquiries_parse_legacy_file(string $path): ?array
{
    $raw = @file_get_contents($path);
    if ($raw === false || $raw === '') {
        return null;
    }

    $raw = str_replace(["\r\n", "\r"], "\n", $raw);
    $fields = [];
    foreach (['Timestamp', 'Name', 'Email', 'Phone', 'Service'] as $label) {
        if (preg_match('/^' . $label . ': (.*)$/m', $raw, $match)) {
            $fields[strtolower($label)] = trim($match[1]);
        }
    }
    $message = '';
    if (preg_match("/^Message:\n(.*?)\n=== END SUBMISSION ===/ms", $raw, $match)) {
        $message = trim($match[1]);
    }

    if (empty($fields['name']) || empty($fields['email']) || $message === '') {
        return null;
    }

    $timestamp = $fields['timestamp'] ?? '';
    $created = DateTime::createFromFormat('Y-m-d H:i:s', $timestamp);

    return [
        'name' => mb_substr($fields['name'], 0, 120, 'UTF-8'),
        'email' => mb_substr($fields['email'], 0, 190, 'UTF-8'),
        'phone' => mb_substr($fields['phone'] ?? '', 0, 30, 'UTF-8'),
        'service' => mb_substr($fields['service'] ?? '', 0, 100, 'UTF-8'),
        'message' => mb_substr($message, 0, 5000, 'UTF-8'),
        'created_at' => $created ? $created->format('Y-m-d H:i:s') : date('Y-m-d H:i:s', (int) @filemtime($path)),
    ];
}

function enquiries_legacy_is_imported(string $ref): bool
{
    $stmt = enquiries_db()->prepare('SELECT 1 FROM enquiries WHERE legacy_ref = :ref LIMIT 1');
    $stmt->execute([':ref' => $ref]);

    return (bool) $stmt->fetchColumn();
}

function enquiries_legacy_pending(): array
{
    return array_values(array_filter(enquiries_legacy_files(), static function ($path) {
        return !enquiries_legacy_is_imported(basename($path));
    }));
}

/**
 * Copies legacy file backups into the database. Idempotent and non-destructive: files are kept.
 */
function enquiries_import_legacy(): array
{
    $pdo = enquiries_db();
    $result = ['imported' => 0, 'skipped' => 0];
    $stmt = $pdo->prepare('INSERT OR IGNORE INTO enquiries (name, email, phone, service, message, source, status, email_status, legacy_ref, created_at, updated_at)
        VALUES (:name, :email, :phone, :service, :message, :source, :status, :email_status, :legacy_ref, :created_at, :updated_at)');

    foreach (enquiries_legacy_pending() as $path) {
        $data = enquiries_parse_legacy_file($path);
        if ($data === null) {
            $result['skipped']++;
            continue;
        }
        $stmt->execute([
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':phone' => $data['phone'],
            ':service' => $data['service'] !== '' ? $data['service'] : null,
            ':message' => $data['message'],
            ':source' => 'legacy_file',
            ':status' => 'new',
            ':email_status' => 'imported',
            ':legacy_ref' => basename($path),
            ':created_at' => $data['created_at'],
            ':updated_at' => blog_now(),
        ]);
        $stmt->rowCount() > 0 ? $result['imported']++ : $result['skipped']++;
    }

    return $result;
}

/**
 * Deletes only the backup files that are already safely stored in the database.
 */
function enquiries_delete_imported_legacy_files(): int
{
    $deleted = 0;
    foreach (enquiries_legacy_files() as $path) {
        if (enquiries_legacy_is_imported(basename($path)) && @unlink($path)) {
            $deleted++;
        }
    }

    return $deleted;
}

function enquiries_legacy_imported_file_count(): int
{
    $count = 0;
    foreach (enquiries_legacy_files() as $path) {
        if (enquiries_legacy_is_imported(basename($path))) {
            $count++;
        }
    }

    return $count;
}
