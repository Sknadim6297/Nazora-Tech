<?php
/**
 * CLI-only backup runner for Hostinger Cron Jobs.
 * Usage: php /home/<user>/domains/nazora.tech/public_html/includes/run_backup.php
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Forbidden\n");
}

require_once __DIR__ . '/backup_app.php';

try {
    $result = backup_create();
    echo sprintf(
        "[%s] Backup created: %s (%s, %d bytes, enquiries=%d, uploads=%d, pruned=%d)\n",
        blog_now(),
        $result['path'],
        $result['format'],
        $result['size'],
        $result['enquiries_count'],
        $result['uploads_count'],
        $result['pruned']
    );
    exit(0);
} catch (Throwable $exception) {
    error_log('[backup] Scheduled backup failed: ' . $exception->getMessage());
    fwrite(STDERR, 'Backup failed: ' . $exception->getMessage() . "\n");
    exit(1);
}
