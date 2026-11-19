<?php
require_once __DIR__ . '/../includes/backup_app.php';
require_once __DIR__ . '/../includes/contact_mailer.php';
blog_require_login();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!blog_verify_csrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Security token expired. Please refresh and try again.';
    } else {
        $action = (string) ($_POST['action'] ?? '');

        if ($action === 'backup') {
            // Immediate consistent SQLite download
            $tmp = tempnam(sys_get_temp_dir(), 'nzbk');
            @unlink($tmp);
            try {
                blog_db()->exec('VACUUM INTO ' . blog_db()->quote($tmp));
            } catch (Throwable $exception) {
                error_log('[admin] Database backup failed: ' . get_class($exception));
                $tmp = '';
            }

            if ($tmp !== '' && is_file($tmp)) {
                header('Content-Type: application/vnd.sqlite3');
                header('Content-Disposition: attachment; filename="nazora-db-' . date('Y-m-d-His') . '.sqlite"');
                header('Content-Length: ' . filesize($tmp));
                header('Cache-Control: no-store, no-cache, must-revalidate, private');
                header('X-Content-Type-Options: nosniff');
                readfile($tmp);
                @unlink($tmp);
                exit;
            }
            $errors[] = 'Backup could not be created. Check the server log.';
        } elseif ($action === 'full_backup') {
            try {
                $created = backup_create();
                blog_flash('success', 'Full backup saved (' . $created['filename'] . ', ' . ($created['outside_webroot'] ? 'outside public_html' : 'private backup store') . ').');
                header('Location: system.php');
                exit;
            } catch (Throwable $exception) {
                error_log('[admin] Full backup failed: ' . get_class($exception));
                $errors[] = 'Full backup could not be created. Check folder permissions and server logs.';
            }
        } elseif ($action === 'download_zip') {
            $zipName = (string) ($_POST['backup_file'] ?? '');
            $zipPath = backup_resolve_zip($zipName);
            if ($zipPath !== null) {
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="' . basename($zipPath) . '"');
                header('Content-Length: ' . filesize($zipPath));
                header('Cache-Control: no-store, no-cache, must-revalidate, private');
                header('X-Content-Type-Options: nosniff');
                readfile($zipPath);
                exit;
            }
            $errors[] = 'Requested backup file could not be read.';
        } elseif ($action === 'test_email') {
            // Sends only to the business notification inbox, never to a customer
            $status = contact_send_notification([
                'name' => 'SMTP test',
                'email' => '',
                'phone' => '',
                'service' => '',
                'message' => 'This is a test message from the admin System page at ' . blog_now() . '. No action is needed.',
            ], true);
            if ($status === 'sent') {
                blog_flash('success', 'Test email sent to the notification inbox. Check it (and the spam folder).');
            } elseif ($status === 'disabled') {
                blog_flash('danger', 'SMTP is not configured yet, so no email was sent.');
            } else {
                blog_flash('danger', 'The SMTP server rejected the test email. Check the SMTP settings and the server log.');
            }
            header('Location: system.php');
            exit;
        }
    }
}

$missing = [];
$mailConfig = contact_mail_config($missing);
$mailWarnings = $mailConfig ? contact_mail_config_warnings($mailConfig) : [];
$configSource = contact_mail_config_source();
$configSourceLabels = [
    'env' => 'Server environment variables',
    'parent_config' => 'External file (../nazora-config.php)',
    'storage_config' => 'Private storage file (storage/config.local.php)',
    'none' => 'Not configured',
];

$dbPath = blog_db_path();
$dbSize = is_file($dbPath) ? filesize($dbPath) : 0;
$storageWritable = is_writable(blog_storage_path());
$uploadsWritable = is_writable(blog_upload_dir('portfolio')) || is_writable(blog_storage_path() . '/uploads');
$submissionsWritable = is_writable(blog_storage_path() . '/submissions') || $storageWritable;
$onRender = contact_env('RENDER') !== '';
$persistentFlag = in_array(strtolower(contact_env('STORAGE_PERSISTENT')), ['1', 'true', 'yes'], true);
$counts = enquiries_status_counts();
$failedEmails = (int) enquiries_db()->query("SELECT COUNT(*) FROM enquiries WHERE email_status IN ('failed','disabled')")->fetchColumn();

$requiredExtensions = ['pdo_sqlite', 'openssl', 'mbstring', 'gd', 'fileinfo', 'zip'];
$missingExtensions = array_values(array_filter($requiredExtensions, static fn ($ext) => !extension_loaded($ext)));
$vendorReady = is_file(blog_root_path() . '/vendor/autoload.php');

$backupDir = backup_target_dir();
$backupOutside = backup_is_outside_webroot($backupDir);
$backupRetention = backup_retention_count();
$backups = backup_list($backupDir);

function system_bytes(int $bytes): string
{
    if ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 1) . ' MB';
    }

    return number_format($bytes / 1024, 1) . ' KB';
}

$pageTitle = 'System & Backup';
$pageSubtitle = 'Email delivery, Hostinger readiness, storage and backups.';
$activeMenu = 'system';

include __DIR__ . '/includes/header.php';
?>
<?php if ($errors) : ?>
  <div class="alert alert-danger admin-alert">
    <?php foreach ($errors as $error) : ?>
      <div><?php echo blog_escape($error); ?></div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<div class="admin-grid">
  <div class="admin-col-8">
    <div class="admin-card mb-4">
      <div class="admin-card-head">
        <div>
          <h3><i class="ri-mail-settings-line"></i> Enquiry email delivery (SMTP)</h3>
          <p>Values are read from server config or environment variables and are never shown here.</p>
        </div>
        <span class="status-pill <?php echo $mailConfig ? 'published' : 'draft'; ?>"><?php echo $mailConfig ? 'Configured' : 'Not configured'; ?></span>
      </div>
      <div class="admin-card-body">
        <?php if ($mailConfig) : ?>
          <dl class="enquiry-meta">
            <dt>Config source</dt><dd><?php echo blog_escape($configSourceLabels[$configSource] ?? $configSource); ?></dd>
            <dt>Notifications go to</dt><dd class="enquiry-break"><?php echo blog_escape($mailConfig['notify_email']); ?></dd>
            <dt>Encryption</dt><dd><?php echo blog_escape(strtoupper($mailConfig['encryption'])); ?> · port <?php echo (int) $mailConfig['port']; ?></dd>
            <dt>Sender</dt><dd class="enquiry-break"><?php echo blog_escape($mailConfig['from_name'] . ' <' . $mailConfig['from_email'] . '>'); ?></dd>
            <dt>Customer confirmation</dt><dd><?php echo $mailConfig['autoreply'] ? 'On (CONTACT_AUTOREPLY=1)' : 'Off'; ?></dd>
          </dl>
          <?php foreach ($mailWarnings as $warning) : ?>
            <p class="admin-helper mt-2 mb-0"><i class="ri-error-warning-line"></i> <?php echo blog_escape($warning); ?></p>
          <?php endforeach; ?>
          <p class="admin-helper mt-2 mb-0">A successful test means the SMTP server accepted the message. Confirm it actually arrived (check spam too) before relying on it.</p>
          <form method="post" class="mt-3">
            <input type="hidden" name="csrf_token" value="<?php echo blog_escape(blog_csrf_token()); ?>">
            <input type="hidden" name="action" value="test_email">
            <button type="submit" class="btn btn-outline-primary btn-sm"><i class="ri-send-plane-line"></i> Send test email to notification inbox</button>
          </form>
        <?php else : ?>
          <p>Enquiries are still <strong>saved in Enquiries</strong>, but no notification email is sent and visitors are told their enquiry was saved (not emailed).</p>
          <p class="mb-2">Missing settings:</p>
          <ul class="system-list">
            <?php foreach ($missing as $item) : ?>
              <li><code><?php echo blog_escape($item); ?></code></li>
            <?php endforeach; ?>
          </ul>
          <p class="admin-helper mb-2"><strong>Hostinger setup:</strong> copy <code>includes/config.local.example.php</code> to <code>nazora-config.php</code> one folder above <code>public_html</code> (or <code>storage/config.local.php</code>), or set server environment variables. Never commit real passwords.</p>
          <p class="admin-helper mb-0">For Hostinger Email (<code>info@nazora.tech</code> once created and verified): host <code>smtp.hostinger.com</code>, port <code>465</code> with <code>ssl</code> (or port <code>587</code> with <code>tls</code>), matching <code>SMTP_USERNAME</code> and <code>SMTP_FROM_EMAIL</code>, plus SPF/DKIM/DMARC records in DNS.</p>
        <?php endif; ?>
        <p class="admin-helper mt-3 mb-0"><i class="ri-eye-line"></i> Preview email templates with sample data:
          <a href="email-preview.php?template=admin" target="_blank" rel="noopener">enquiry notification</a> ·
          <a href="email-preview.php?template=customer" target="_blank" rel="noopener">customer confirmation</a></p>
        <?php if ($failedEmails > 0) : ?>
          <p class="admin-helper mt-3 mb-0"><i class="ri-error-warning-line"></i> <?php echo $failedEmails; ?> enquir<?php echo $failedEmails === 1 ? 'y was' : 'ies were'; ?> saved without a notification email. <a href="enquiries.php">Review enquiries</a>.</p>
        <?php endif; ?>
      </div>
    </div>

    <div class="admin-card mb-4">
      <div class="admin-card-head">
        <div>
          <h3><i class="ri-database-2-line"></i> Backups (Database, Enquiries &amp; Uploads)</h3>
          <p>Create consistent SQLite snapshots and bundled ZIP archives (database + enquiries CSV + uploads).</p>
        </div>
      </div>
      <div class="admin-card-body">
        <p>Keep backups private: they include customer enquiries and the admin password hash. Create a backup before every deployment and schedule the CLI script via Hostinger Cron Jobs.</p>
        <div class="d-flex flex-wrap gap-2 mb-3">
          <form method="post" class="m-0">
            <input type="hidden" name="csrf_token" value="<?php echo blog_escape(blog_csrf_token()); ?>">
            <input type="hidden" name="action" value="full_backup">
            <button type="submit" class="btn btn-primary"><i class="ri-archive-line"></i> Save full backup on server</button>
          </form>
          <form method="post" class="m-0">
            <input type="hidden" name="csrf_token" value="<?php echo blog_escape(blog_csrf_token()); ?>">
            <input type="hidden" name="action" value="backup">
            <button type="submit" class="btn btn-outline-primary"><i class="ri-download-cloud-2-line"></i> Download SQLite database</button>
          </form>
        </div>
        <dl class="enquiry-meta mb-3">
          <dt>Server backup location</dt><dd><?php echo $backupOutside ? 'Outside public_html' : 'Private storage/backups (HTTP 403 denied)'; ?></dd>
          <dt>Retention policy</dt><dd><?php echo $backupRetention > 0 ? 'Keep newest ' . (int) $backupRetention . ' managed backups' : 'Keep all backups (auto-prune off)'; ?></dd>
          <dt>Cron command (Hostinger)</dt><dd class="enquiry-break"><code>php /home/&lt;USER&gt;/domains/nazora.tech/public_html/includes/run_backup.php</code></dd>
        </dl>

        <?php if ($backups) : ?>
          <div class="table-responsive">
            <table class="table admin-table align-middle mb-0">
              <thead>
                <tr>
                  <th>Archive</th>
                  <th>Created</th>
                  <th>Size</th>
                  <th class="text-end">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach (array_slice($backups, 0, 10) as $bk) : ?>
                  <tr>
                    <td><code><?php echo blog_escape($bk['filename']); ?></code></td>
                    <td class="text-nowrap"><?php echo blog_escape($bk['modified_at']); ?></td>
                    <td class="text-nowrap"><?php echo $bk['size'] > 0 ? blog_escape(system_bytes((int) $bk['size'])) : 'Folder'; ?></td>
                    <td class="text-end">
                      <?php if ($bk['format'] === 'zip') : ?>
                        <form method="post" class="m-0">
                          <input type="hidden" name="csrf_token" value="<?php echo blog_escape(blog_csrf_token()); ?>">
                          <input type="hidden" name="action" value="download_zip">
                          <input type="hidden" name="backup_file" value="<?php echo blog_escape($bk['filename']); ?>">
                          <button type="submit" class="btn btn-sm btn-outline-primary"><i class="ri-download-line"></i> Download</button>
                        </form>
                      <?php else : ?>
                        <span class="admin-muted">On disk</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else : ?>
          <p class="admin-helper mb-0">No server backup archives saved yet. Click <strong>Save full backup on server</strong> to test.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="admin-col-4">
    <div class="admin-card mb-4">
      <div class="admin-card-head"><div><h3><i class="ri-hard-drive-2-line"></i> Server &amp; Storage</h3></div></div>
      <div class="admin-card-body">
        <dl class="enquiry-meta">
          <dt>PHP version</dt><dd><?php echo blog_escape(PHP_VERSION); ?></dd>
          <dt>SQLite &amp; extensions</dt><dd><?php echo $missingExtensions ? '<strong class="text-danger">Missing: ' . blog_escape(implode(', ', $missingExtensions)) . '</strong>' : 'All active (' . blog_escape(implode(', ', $requiredExtensions)) . ')'; ?></dd>
          <dt>Composer vendor</dt><dd><?php echo $vendorReady ? 'Installed' : '<strong class="text-danger">Missing</strong>'; ?></dd>
          <dt>Database size</dt><dd><?php echo blog_escape(system_bytes((int) $dbSize)); ?></dd>
          <dt>Storage / uploads / rate dir</dt><dd><?php echo ($storageWritable && $uploadsWritable && $submissionsWritable) ? 'Writable' : '<strong class="text-danger">Check permissions (755)</strong>'; ?></dd>
          <dt>Enquiries stored</dt><dd><?php echo (int) $counts['all']; ?></dd>
        </dl>
        <?php if ($onRender && !$persistentFlag) : ?>
          <p class="admin-helper mt-3 mb-0"><i class="ri-alert-line"></i> Render container detected: attach a persistent disk at <code>/var/www/html/storage</code> and set <code>STORAGE_PERSISTENT=1</code>.</p>
        <?php else : ?>
          <p class="admin-helper mt-3 mb-0"><i class="ri-information-line"></i> On Hostinger Web/Cloud hosting, SQLite and uploaded images persist on your hosting disk under <code>storage/</code> (blocked from direct web access by <code>.htaccess</code>).</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
