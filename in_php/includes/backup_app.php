<?php
/**
 * Consistent server-side backup helper for Nazora Tech.
 *
 * Backs up:
 *   - SQLite database via online `VACUUM INTO` snapshot (enquiries, notes, careers, portfolio, users)
 *   - Formula-safe CSV export of enquiries
 *   - Persistent uploaded files under storage/uploads/
 *
 * Stores archives outside public_html when the site lives inside `public_html` (Hostinger layout),
 * or falls back to HTTP-blocked `storage/backups/` (or `BACKUP_DIR` override).
 */

require_once __DIR__ . '/enquiries_app.php';

function backup_target_dir(): string
{
    $custom = site_env('BACKUP_DIR');
    if ($custom !== '') {
        return rtrim(str_replace('\\', '/', $custom), '/');
    }

    $root = blog_root_path();
    $parent = dirname($root);
    if (basename($root) === 'public_html' && is_dir($parent) && is_writable($parent)) {
        return $parent . '/nazora-backups';
    }

    return blog_storage_path() . '/backups';
}

function backup_is_outside_webroot(?string $dir = null): bool
{
    $dir = str_replace('\\', '/', $dir ?? backup_target_dir());
    $root = rtrim(str_replace('\\', '/', blog_root_path()), '/');

    return !str_starts_with($dir . '/', $root . '/');
}

function backup_retention_count(): int
{
    $raw = site_env('BACKUP_KEEP_COUNT', '14');
    if (!ctype_digit($raw)) {
        return 14;
    }

    return max(0, (int) $raw);
}

function backup_ensure_private_dir(string $dir): void
{
    if (!is_dir($dir)) {
        mkdir($dir, 0750, true);
    }

    if (!backup_is_outside_webroot($dir)) {
        $htaccess = $dir . '/.htaccess';
        if (!is_file($htaccess)) {
            @file_put_contents($htaccess, "Require all denied\nOptions -Indexes\n");
        }
        $index = $dir . '/index.html';
        if (!is_file($index)) {
            @file_put_contents($index, '');
        }
    }
}

function backup_write_enquiries_csv(string $csvPath): int
{
    $pdo = enquiries_db();
    $rows = $pdo->query('SELECT id, created_at, name, email, phone, service, message, source, status, email_status, updated_at
        FROM enquiries ORDER BY created_at DESC, id DESC')->fetchAll();

    $fh = fopen($csvPath, 'wb');
    if ($fh === false) {
        throw new RuntimeException('Unable to write CSV backup file.');
    }

    fwrite($fh, "\xEF\xBB\xBF");
    fputcsv($fh, ['Reference', 'Received', 'Name', 'Email', 'Phone', 'Service', 'Message', 'Source', 'Status', 'Email notification', 'Last updated'], ',', '"', '');
    foreach ($rows as $row) {
        fputcsv($fh, [
            '#' . (int) $row['id'],
            enquiries_csv_safe((string) $row['created_at']),
            enquiries_csv_safe((string) $row['name']),
            enquiries_csv_safe((string) $row['email']),
            enquiries_csv_safe((string) $row['phone']),
            enquiries_csv_safe((string) $row['service']),
            enquiries_csv_safe((string) $row['message']),
            enquiries_csv_safe((string) $row['source']),
            enquiries_csv_safe((string) $row['status']),
            enquiries_csv_safe((string) $row['email_status']),
            enquiries_csv_safe((string) $row['updated_at']),
        ], ',', '"', '');
    }
    fclose($fh);

    return count($rows);
}

function backup_collect_uploads(): array
{
    $uploadsRoot = blog_storage_path() . '/uploads';
    if (!is_dir($uploadsRoot)) {
        return [];
    }

    $files = [];
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($uploadsRoot, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($it as $file) {
        if ($file instanceof SplFileInfo && $file->isFile()) {
            $full = str_replace('\\', '/', $file->getPathname());
            $rel = ltrim(substr($full, strlen(str_replace('\\', '/', $uploadsRoot))), '/');
            $files[] = ['full' => $file->getPathname(), 'rel' => $rel];
        }
    }

    return $files;
}

/**
 * Creates a full consistent backup (.zip when ZipArchive is available, otherwise folder).
 */
function backup_create(?string $targetDir = null): array
{
    $targetDir = $targetDir ?? backup_target_dir();
    backup_ensure_private_dir($targetDir);

    if (!is_writable($targetDir)) {
        throw new RuntimeException('Backup directory is not writable: ' . $targetDir);
    }

    $stamp = date('Ymd-His');
    $baseName = 'nazora-backup-' . $stamp;
    $tmpDir = sys_get_temp_dir() . '/nzbk-' . $stamp . '-' . bin2hex(random_bytes(4));
    mkdir($tmpDir, 0700, true);

    $tmpDb = $tmpDir . '/blog.sqlite';
    $tmpCsv = $tmpDir . '/enquiries.csv';
    $tmpManifest = $tmpDir . '/manifest.json';

    try {
        $pdo = enquiries_db();
        $pdo->exec('VACUUM INTO ' . $pdo->quote($tmpDb));

        $checkDb = new PDO('sqlite:' . $tmpDb);
        $quickCheck = (string) $checkDb->query('PRAGMA quick_check')->fetchColumn();
        $checkDb = null;
        if (strtolower($quickCheck) !== 'ok') {
            throw new RuntimeException('SQLite snapshot integrity check failed.');
        }

        $enquiryCount = backup_write_enquiries_csv($tmpCsv);
        $uploads = backup_collect_uploads();

        $manifest = [
            'created_at' => blog_now(),
            'php_version' => PHP_VERSION,
            'sqlite_check' => $quickCheck,
            'enquiries_count' => $enquiryCount,
            'uploads_count' => count($uploads),
            'db_bytes' => (int) filesize($tmpDb),
        ];
        file_put_contents($tmpManifest, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");

        if (class_exists('ZipArchive')) {
            $archivePath = $targetDir . '/' . $baseName . '.zip';
            $zip = new ZipArchive();
            if ($zip->open($archivePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new RuntimeException('Could not open ZIP archive for writing.');
            }
            $zip->addFile($tmpDb, 'database/blog.sqlite');
            $zip->addFile($tmpCsv, 'enquiries/enquiries.csv');
            $zip->addFile($tmpManifest, 'manifest.json');
            foreach ($uploads as $up) {
                $zip->addFile($up['full'], 'uploads/' . $up['rel']);
            }
            $zip->close();
            @chmod($archivePath, 0640);
            $finalPath = $archivePath;
            $format = 'zip';
            $size = (int) filesize($archivePath);
        } else {
            $bundleDir = $targetDir . '/' . $baseName;
            mkdir($bundleDir . '/database', 0750, true);
            mkdir($bundleDir . '/enquiries', 0750, true);
            mkdir($bundleDir . '/uploads', 0750, true);
            copy($tmpDb, $bundleDir . '/database/blog.sqlite');
            copy($tmpCsv, $bundleDir . '/enquiries/enquiries.csv');
            copy($tmpManifest, $bundleDir . '/manifest.json');
            $size = (int) filesize($bundleDir . '/database/blog.sqlite') + (int) filesize($bundleDir . '/enquiries/enquiries.csv');
            foreach ($uploads as $up) {
                $dest = $bundleDir . '/uploads/' . $up['rel'];
                $destParent = dirname($dest);
                if (!is_dir($destParent)) {
                    mkdir($destParent, 0750, true);
                }
                copy($up['full'], $dest);
                $size += (int) filesize($dest);
            }
            $finalPath = $bundleDir;
            $format = 'dir';
        }
    } finally {
        @unlink($tmpDb);
        @unlink($tmpCsv);
        @unlink($tmpManifest);
        @rmdir($tmpDir);
    }

    $pruned = backup_prune_old($targetDir, backup_retention_count());

    return [
        'path' => $finalPath,
        'filename' => basename($finalPath),
        'format' => $format,
        'size' => $size,
        'enquiries_count' => $enquiryCount,
        'uploads_count' => count($uploads),
        'outside_webroot' => backup_is_outside_webroot($targetDir),
        'pruned' => $pruned,
    ];
}

function backup_remove_dir(string $dir): void
{
    if (!is_dir($dir)) {
        return;
    }
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($it as $item) {
        if ($item->isDir()) {
            @rmdir($item->getPathname());
        } else {
            @unlink($item->getPathname());
        }
    }
    @rmdir($dir);
}

/**
 * Keeps the newest $keepCount managed backups; never touches non-matching files.
 */
function backup_prune_old(string $targetDir, int $keepCount): int
{
    if ($keepCount <= 0 || !is_dir($targetDir)) {
        return 0;
    }

    $managed = [];
    foreach (scandir($targetDir) ?: [] as $entry) {
        if (preg_match('/^nazora-backup-\d{8}-\d{6}(?:\.zip)?$/', $entry)) {
            $full = $targetDir . '/' . $entry;
            $managed[] = ['name' => $entry, 'path' => $full, 'mtime' => (int) @filemtime($full)];
        }
    }

    usort($managed, static fn ($a, $b) => strcmp($b['name'], $a['name']));
    $removed = 0;
    foreach (array_slice($managed, $keepCount) as $old) {
        if (is_dir($old['path'])) {
            backup_remove_dir($old['path']);
            $removed++;
        } elseif (is_file($old['path']) && @unlink($old['path'])) {
            $removed++;
        }
    }

    return $removed;
}

function backup_list(?string $targetDir = null): array
{
    $targetDir = $targetDir ?? backup_target_dir();
    if (!is_dir($targetDir)) {
        return [];
    }

    $items = [];
    foreach (scandir($targetDir) ?: [] as $entry) {
        if (!preg_match('/^nazora-backup-\d{8}-\d{6}(?:\.zip)?$/', $entry)) {
            continue;
        }
        $full = $targetDir . '/' . $entry;
        $isZip = is_file($full) && str_ends_with($entry, '.zip');
        $items[] = [
            'filename' => $entry,
            'format' => $isZip ? 'zip' : 'dir',
            'size' => $isZip ? (int) filesize($full) : 0,
            'modified_at' => date('Y-m-d H:i:s', (int) @filemtime($full)),
        ];
    }

    usort($items, static fn ($a, $b) => strcmp($b['filename'], $a['filename']));

    return $items;
}

function backup_resolve_zip(string $filename, ?string $targetDir = null): ?string
{
    if (!preg_match('/^nazora-backup-\d{8}-\d{6}\.zip$/', $filename)) {
        return null;
    }

    $targetDir = $targetDir ?? backup_target_dir();
    $full = $targetDir . '/' . $filename;

    return is_file($full) && is_readable($full) ? $full : null;
}
