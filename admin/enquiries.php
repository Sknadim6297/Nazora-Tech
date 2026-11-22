<?php
require_once __DIR__ . '/../includes/enquiries_app.php';
blog_require_login();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!blog_verify_csrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Security token expired. Please refresh and try again.';
    } else {
        $action = (string) ($_POST['action'] ?? '');

        if ($action === 'purge') {
            $months = (int) ($_POST['months'] ?? 0);
            if (!in_array($months, ENQUIRY_RETENTION_CHOICES, true) || ($_POST['confirm_purge'] ?? '') !== 'DELETE') {
                $errors[] = 'Type DELETE in the confirmation box to permanently remove old closed enquiries.';
            } else {
                $deleted = enquiries_purge_closed($months);
                blog_flash('success', $deleted . ' closed enquir' . ($deleted === 1 ? 'y' : 'ies') . ' older than ' . $months . ' months permanently deleted.');
                header('Location: enquiries.php');
                exit;
            }
        } elseif ($action === 'import_legacy') {
            $result = enquiries_import_legacy();
            blog_flash('success', 'Imported ' . $result['imported'] . ' backup file(s) into Enquiries. Skipped ' . $result['skipped'] . '. Original files were kept.');
            header('Location: enquiries.php');
            exit;
        } elseif ($action === 'delete_legacy_files') {
            $deleted = enquiries_delete_imported_legacy_files();
            blog_flash('success', $deleted . ' imported backup file(s) deleted from storage. Their enquiries remain in the database.');
            header('Location: enquiries.php');
            exit;
        }
    }
}

$filters = enquiries_filters_from($_GET);
$result = enquiries_list($filters, 20);
$counts = enquiries_status_counts();
$services = enquiries_services();
$legacyPending = count(enquiries_legacy_pending());
$legacyImported = enquiries_legacy_imported_file_count();
$purgeCounts = [];
foreach (ENQUIRY_RETENTION_CHOICES as $months) {
    $purgeCounts[$months] = enquiries_count_purgeable($months);
}

function enquiries_query(array $filters, array $overrides = []): string
{
    $params = array_merge($filters, $overrides);
    $params = array_filter($params, static fn ($value, $key) => $value !== '' && !($key === 'page' && (int) $value <= 1), ARRAY_FILTER_USE_BOTH);

    return $params ? '?' . http_build_query($params) : '';
}

$pageTitle = 'Enquiries';
$pageSubtitle = 'Leads from the website contact forms.';
$activeMenu = 'enquiries';

include __DIR__ . '/includes/header.php';
?>
<?php if ($errors) : ?>
  <div class="alert alert-danger admin-alert">
    <?php foreach ($errors as $error) : ?>
      <div><?php echo blog_escape($error); ?></div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php if ($legacyPending > 0) : ?>
  <div class="admin-notice admin-notice--warning">
    <i class="ri-inbox-archive-line"></i>
    <div>
      <strong><?php echo (int) $legacyPending; ?> enquiry backup file(s) found in storage.</strong>
      <p class="mb-0">These were saved by the old contact form. Import them so they appear here. Existing enquiries are never overwritten and the files are kept.</p>
    </div>
    <form method="post" class="m-0">
      <input type="hidden" name="csrf_token" value="<?php echo blog_escape(blog_csrf_token()); ?>">
      <input type="hidden" name="action" value="import_legacy">
      <button type="submit" class="btn btn-primary btn-sm"><i class="ri-download-2-line"></i> Import</button>
    </form>
  </div>
<?php elseif ($legacyImported > 0) : ?>
  <div class="admin-notice">
    <i class="ri-file-shield-2-line"></i>
    <div>
      <strong><?php echo (int) $legacyImported; ?> old backup file(s) already imported.</strong>
      <p class="mb-0">The enquiries are safely in the database. You can delete the duplicate text files to reduce stored personal data.</p>
    </div>
    <form method="post" class="m-0" onsubmit="return confirm('Delete the imported backup text files? The enquiries stay in the database.');">
      <input type="hidden" name="csrf_token" value="<?php echo blog_escape(blog_csrf_token()); ?>">
      <input type="hidden" name="action" value="delete_legacy_files">
      <button type="submit" class="btn btn-outline-danger btn-sm"><i class="ri-delete-bin-line"></i> Delete files</button>
    </form>
  </div>
<?php endif; ?>

<div class="admin-table-card">
  <div class="admin-section-head">
    <div>
      <strong>Website enquiries</strong>
      <p class="admin-muted mb-0"><?php echo (int) $result['total']; ?> matching enquir<?php echo $result['total'] === 1 ? 'y' : 'ies'; ?>. Only signed-in admins can see this data.</p>
    </div>
    <form method="post" action="enquiries-export.php" class="m-0">
      <input type="hidden" name="csrf_token" value="<?php echo blog_escape(blog_csrf_token()); ?>">
      <?php foreach (['search', 'status', 'service', 'from', 'to'] as $key) : ?>
        <input type="hidden" name="<?php echo $key; ?>" value="<?php echo blog_escape($filters[$key]); ?>">
      <?php endforeach; ?>
      <button type="submit" class="btn btn-outline-primary"<?php echo $result['total'] === 0 ? ' disabled' : ''; ?>><i class="ri-file-excel-2-line"></i> Export CSV</button>
    </form>
  </div>

  <nav class="enquiry-tabs" aria-label="Filter by status">
    <a href="enquiries.php<?php echo blog_escape(enquiries_query($filters, ['status' => '', 'page' => 1])); ?>" class="enquiry-tab<?php echo $filters['status'] === '' ? ' is-active' : ''; ?>">All <span><?php echo (int) $counts['all']; ?></span></a>
    <?php foreach (ENQUIRY_STATUSES as $key => $label) : ?>
      <a href="enquiries.php<?php echo blog_escape(enquiries_query($filters, ['status' => $key, 'page' => 1])); ?>" class="enquiry-tab enquiry-tab--<?php echo $key; ?><?php echo $filters['status'] === $key ? ' is-active' : ''; ?>"><?php echo blog_escape($label); ?> <span><?php echo (int) $counts[$key]; ?></span></a>
    <?php endforeach; ?>
  </nav>

  <form class="admin-filter-bar mb-4" method="get">
    <input class="form-control" type="search" name="search" value="<?php echo blog_escape($filters['search']); ?>" placeholder="Search name, email, phone or message" maxlength="100">
    <select class="form-select" name="status" aria-label="Status">
      <option value="">All status</option>
      <?php foreach (ENQUIRY_STATUSES as $key => $label) : ?>
        <option value="<?php echo $key; ?>" <?php echo $filters['status'] === $key ? 'selected' : ''; ?>><?php echo blog_escape($label); ?></option>
      <?php endforeach; ?>
    </select>
    <select class="form-select" name="service" aria-label="Service">
      <option value="">All services</option>
      <?php foreach ($services as $serviceName) : ?>
        <option value="<?php echo blog_escape($serviceName); ?>" <?php echo $filters['service'] === $serviceName ? 'selected' : ''; ?>><?php echo blog_escape($serviceName); ?></option>
      <?php endforeach; ?>
    </select>
    <input class="form-control enquiry-date" type="date" name="from" value="<?php echo blog_escape($filters['from']); ?>" aria-label="From date" title="From date">
    <input class="form-control enquiry-date" type="date" name="to" value="<?php echo blog_escape($filters['to']); ?>" aria-label="To date" title="To date">
    <button class="btn btn-outline-primary" type="submit"><i class="ri-filter-3-line"></i> Filter</button>
    <?php if ($filters['search'] !== '' || $filters['status'] !== '' || $filters['service'] !== '' || $filters['from'] !== '' || $filters['to'] !== '') : ?>
      <a class="btn btn-outline-secondary" href="enquiries.php"><i class="ri-close-line"></i> Reset</a>
    <?php endif; ?>
  </form>

  <div class="table-responsive">
    <table class="table admin-table align-middle">
      <thead>
        <tr>
          <th>Received</th>
          <th>Name</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Service</th>
          <th>Status</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$result['items']) : ?>
          <tr>
            <td colspan="7">
              <div class="admin-empty">
                <i class="ri-mail-open-line"></i>
                <strong><?php echo $counts['all'] === 0 ? 'No enquiries yet' : 'No enquiries match these filters'; ?></strong>
                <p class="admin-muted mb-0"><?php echo $counts['all'] === 0 ? 'Messages sent through the website contact forms will appear here.' : 'Try a different search or reset the filters.'; ?></p>
              </div>
            </td>
          </tr>
        <?php endif; ?>
        <?php foreach ($result['items'] as $row) : ?>
          <tr class="<?php echo $row['status'] === 'new' ? 'enquiry-row--new' : ''; ?>">
            <td class="text-nowrap">
              <?php echo blog_escape(date('d M Y', strtotime($row['created_at']))); ?><br>
              <small class="admin-muted"><?php echo blog_escape(date('h:i A', strtotime($row['created_at']))); ?></small>
            </td>
            <td>
              <a href="enquiry.php?id=<?php echo (int) $row['id']; ?>"><strong><?php echo blog_escape($row['name']); ?></strong></a><br>
              <small class="admin-muted"><?php echo blog_escape(enquiry_source_label((string) $row['source'])); ?><?php echo (int) $row['note_count'] > 0 ? ' · ' . (int) $row['note_count'] . ' note' . ((int) $row['note_count'] === 1 ? '' : 's') : ''; ?></small>
            </td>
            <td class="enquiry-break"><?php echo blog_escape($row['email']); ?></td>
            <td class="text-nowrap"><?php echo blog_escape($row['phone']); ?></td>
            <td><?php echo blog_escape($row['service'] ?: '—'); ?></td>
            <td>
              <span class="status-pill enquiry-<?php echo blog_escape($row['status']); ?>"><?php echo blog_escape(enquiry_status_label((string) $row['status'])); ?></span>
              <?php if (in_array($row['email_status'], ['failed', 'disabled'], true)) : ?>
                <br><small class="enquiry-mail-flag" title="The notification email was not delivered"><i class="ri-mail-close-line"></i> <?php echo blog_escape(enquiry_email_status_label((string) $row['email_status'])); ?></small>
              <?php endif; ?>
            </td>
            <td class="text-end">
              <a class="btn-action btn-action-view" href="enquiry.php?id=<?php echo (int) $row['id']; ?>" title="View"><i class="ri-eye-line"></i><span class="action-label">View</span></a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <?php if ($result['pages'] > 1) : ?>
    <nav class="admin-pagination mt-3" aria-label="Enquiry pages">
      <ul class="pagination justify-content-center flex-wrap mb-0">
        <li class="page-item<?php echo $result['page'] <= 1 ? ' disabled' : ''; ?>">
          <a class="page-link" href="enquiries.php<?php echo blog_escape(enquiries_query($filters, ['page' => $result['page'] - 1])); ?>">Previous</a>
        </li>
        <?php for ($p = max(1, $result['page'] - 2); $p <= min($result['pages'], $result['page'] + 2); $p++) : ?>
          <li class="page-item<?php echo $p === $result['page'] ? ' active' : ''; ?>">
            <a class="page-link" href="enquiries.php<?php echo blog_escape(enquiries_query($filters, ['page' => $p])); ?>"><?php echo $p; ?></a>
          </li>
        <?php endfor; ?>
        <li class="page-item<?php echo $result['page'] >= $result['pages'] ? ' disabled' : ''; ?>">
          <a class="page-link" href="enquiries.php<?php echo blog_escape(enquiries_query($filters, ['page' => $result['page'] + 1])); ?>">Next</a>
        </li>
      </ul>
    </nav>
  <?php endif; ?>
</div>

<div class="admin-table-card">
  <div class="admin-section-head">
    <div>
      <strong><i class="ri-time-line"></i> Data retention</strong>
      <p class="admin-muted mb-0">Keep personal data only as long as needed. This permanently deletes enquiries marked <strong>Closed</strong> whose last status change is older than the period you choose. New, Contacted and Qualified enquiries are never removed. Download a backup first if you may need them.</p>
    </div>
  </div>
  <form method="post" class="enquiry-retention" onsubmit="return confirm('Permanently delete old closed enquiries? This cannot be undone.');">
    <input type="hidden" name="csrf_token" value="<?php echo blog_escape(blog_csrf_token()); ?>">
    <input type="hidden" name="action" value="purge">
    <select class="form-select" name="months" aria-label="Retention period">
      <?php foreach (ENQUIRY_RETENTION_CHOICES as $months) : ?>
        <option value="<?php echo $months; ?>" <?php echo $months === 12 ? 'selected' : ''; ?>>Closed &gt; <?php echo $months; ?> months old (<?php echo (int) $purgeCounts[$months]; ?>)</option>
      <?php endforeach; ?>
    </select>
    <input class="form-control" type="text" name="confirm_purge" placeholder="Type DELETE to confirm" autocomplete="off" aria-label="Type DELETE to confirm">
    <button type="submit" class="btn btn-outline-danger"><i class="ri-delete-bin-6-line"></i> Delete old closed enquiries</button>
  </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
