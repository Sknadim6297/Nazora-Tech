<?php
require_once __DIR__ . '/../includes/enquiries_app.php';
blog_require_login();

$enquiryId = (int) ($_GET['id'] ?? 0);
$enquiry = $enquiryId > 0 ? enquiry_get($enquiryId) : null;

if (!$enquiry) {
    blog_flash('danger', 'That enquiry was not found. It may have been deleted.');
    header('Location: enquiries.php');
    exit;
}

$errors = [];
$user = blog_current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!blog_verify_csrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Security token expired. Please refresh and try again.';
    } else {
        $action = (string) ($_POST['action'] ?? '');

        if ($action === 'status') {
            $status = (string) ($_POST['status'] ?? '');
            if (enquiry_update_status($enquiryId, $status, $user)) {
                blog_flash('success', 'Status updated to ' . enquiry_status_label($status) . '.');
                header('Location: enquiry.php?id=' . $enquiryId);
                exit;
            }
            $errors[] = 'Please choose a valid status.';
        } elseif ($action === 'note') {
            $note = (string) ($_POST['note'] ?? '');
            if (mb_strlen(trim($note), 'UTF-8') > 4000) {
                $errors[] = 'Notes must be 4000 characters or less.';
            } elseif (enquiry_add_note($enquiryId, $note, $user)) {
                blog_flash('success', 'Note added.');
                header('Location: enquiry.php?id=' . $enquiryId . '#notes');
                exit;
            } else {
                $errors[] = 'Please write a note before saving.';
            }
        } elseif ($action === 'delete') {
            enquiry_delete($enquiryId);
            blog_flash('success', 'Enquiry permanently deleted.');
            header('Location: enquiries.php');
            exit;
        }
    }
}

$notes = enquiry_notes($enquiryId);
$company = site_company_name();

// Safe quick-action links (address and number are already validated on submission)
$mailHref = 'mailto:' . str_replace('%40', '@', rawurlencode((string) $enquiry['email']))
    . '?subject=' . rawurlencode('Re: Your enquiry' . ($enquiry['service'] ? ' about ' . $enquiry['service'] : '') . ' - ' . $company);
$phoneDigits = preg_replace('/\D+/', '', (string) $enquiry['phone']);
$telHref = 'tel:' . (strlen($phoneDigits) === 10 ? '+91' . $phoneDigits : $phoneDigits);
$waDigits = strlen($phoneDigits) === 10 ? '91' . $phoneDigits : $phoneDigits;
$waHref = 'https://wa.me/' . $waDigits . '?text=' . rawurlencode('Hello ' . $enquiry['name'] . ', thank you for contacting ' . $company . '.');

$pageTitle = 'Enquiry #' . $enquiryId;
$pageSubtitle = 'Received ' . date('d M Y, h:i A', strtotime($enquiry['created_at']));
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

<p class="mb-3"><a href="enquiries.php" class="admin-back-link"><i class="ri-arrow-left-line"></i> Back to enquiries</a></p>

<div class="admin-grid enquiry-detail">
  <div class="admin-col-8">
    <div class="admin-card mb-4">
      <div class="admin-card-head">
        <div>
          <h3><?php echo blog_escape($enquiry['name']); ?></h3>
          <p><?php echo blog_escape($enquiry['service'] ?: 'No service selected'); ?> · <?php echo blog_escape(enquiry_source_label((string) $enquiry['source'])); ?></p>
        </div>
        <span class="status-pill enquiry-<?php echo blog_escape($enquiry['status']); ?>"><?php echo blog_escape(enquiry_status_label((string) $enquiry['status'])); ?></span>
      </div>
      <div class="admin-card-body">
        <dl class="enquiry-fields">
          <div><dt>Email</dt><dd class="enquiry-break"><?php echo blog_escape($enquiry['email']); ?></dd></div>
          <div><dt>Phone</dt><dd><?php echo blog_escape($enquiry['phone']); ?></dd></div>
          <div><dt>Service</dt><dd><?php echo blog_escape($enquiry['service'] ?: '—'); ?></dd></div>
          <div><dt>Received</dt><dd><?php echo blog_escape(date('d M Y, h:i A', strtotime($enquiry['created_at']))); ?></dd></div>
        </dl>
        <h4 class="enquiry-subhead">Message</h4>
        <div class="enquiry-message"><?php echo nl2br(blog_escape($enquiry['message'])); ?></div>
        <div class="enquiry-quick">
          <a class="btn btn-primary btn-sm" href="<?php echo blog_escape($mailHref); ?>"><i class="ri-mail-send-line"></i> Reply by email</a>
          <a class="btn btn-outline-primary btn-sm" href="<?php echo blog_escape($telHref); ?>"><i class="ri-phone-line"></i> Call</a>
          <a class="btn btn-outline-primary btn-sm" href="<?php echo blog_escape($waHref); ?>" target="_blank" rel="noopener noreferrer"><i class="ri-whatsapp-line"></i> WhatsApp</a>
        </div>
      </div>
    </div>

    <div class="admin-card" id="notes">
      <div class="admin-card-head">
        <div>
          <h3><i class="ri-sticky-note-line"></i> Internal notes</h3>
          <p>Visible to admins only. Never shown to the visitor.</p>
        </div>
      </div>
      <div class="admin-card-body">
        <form method="post" class="mb-4">
          <input type="hidden" name="csrf_token" value="<?php echo blog_escape(blog_csrf_token()); ?>">
          <input type="hidden" name="action" value="note">
          <label class="form-label" for="note">Add a note</label>
          <textarea class="form-control mb-2" id="note" name="note" rows="3" maxlength="4000" placeholder="e.g. Called on Monday, sending a quote by Friday" required></textarea>
          <button type="submit" class="btn btn-primary btn-sm"><i class="ri-add-line"></i> Save note</button>
        </form>

        <?php if (!$notes) : ?>
          <p class="admin-muted mb-0">No notes yet.</p>
        <?php else : ?>
          <ul class="enquiry-notes">
            <?php foreach ($notes as $note) : ?>
              <li class="<?php echo (int) $note['is_system'] === 1 ? 'is-system' : ''; ?>">
                <div class="enquiry-note-meta">
                  <i class="<?php echo (int) $note['is_system'] === 1 ? 'ri-history-line' : 'ri-user-3-line'; ?>"></i>
                  <strong><?php echo blog_escape($note['author_name'] ?: 'Admin'); ?></strong>
                  <span><?php echo blog_escape(date('d M Y, h:i A', strtotime($note['created_at']))); ?></span>
                </div>
                <div class="enquiry-note-body"><?php echo nl2br(blog_escape($note['note'])); ?></div>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="admin-col-4">
    <div class="admin-card mb-4">
      <div class="admin-card-head"><div><h3><i class="ri-flag-line"></i> Status</h3><p>Track where this lead is.</p></div></div>
      <div class="admin-card-body">
        <form method="post" class="d-grid gap-2">
          <input type="hidden" name="csrf_token" value="<?php echo blog_escape(blog_csrf_token()); ?>">
          <input type="hidden" name="action" value="status">
          <select class="form-select" name="status" aria-label="Status">
            <?php foreach (ENQUIRY_STATUSES as $key => $label) : ?>
              <option value="<?php echo $key; ?>" <?php echo $enquiry['status'] === $key ? 'selected' : ''; ?>><?php echo blog_escape($label); ?></option>
            <?php endforeach; ?>
          </select>
          <button type="submit" class="btn btn-primary"><i class="ri-check-line"></i> Update status</button>
        </form>
      </div>
    </div>

    <div class="admin-card mb-4">
      <div class="admin-card-head"><div><h3><i class="ri-information-line"></i> Details</h3></div></div>
      <div class="admin-card-body">
        <dl class="enquiry-meta">
          <dt>Reference</dt><dd>#<?php echo (int) $enquiry['id']; ?> <span class="text-muted">(NZ-<?php echo str_pad((string) (int) $enquiry['id'], 5, '0', STR_PAD_LEFT); ?>)</span></dd>
          <dt>Email notification</dt><dd><?php echo blog_escape(enquiry_email_status_label((string) $enquiry['email_status'])); ?></dd>
          <dt>Customer confirmation</dt><dd><?php echo blog_escape(enquiry_customer_email_status_label((string) ($enquiry['customer_email_status'] ?? 'off'))); ?></dd>
          <dt>Source</dt><dd><?php echo blog_escape(enquiry_source_label((string) $enquiry['source'])); ?></dd>
          <dt>Last updated</dt><dd><?php echo blog_escape(date('d M Y, h:i A', strtotime($enquiry['updated_at']))); ?></dd>
        </dl>
      </div>
    </div>

    <div class="admin-card enquiry-danger">
      <div class="admin-card-head"><div><h3><i class="ri-delete-bin-line"></i> Delete</h3><p>Permanently removes this enquiry and its notes.</p></div></div>
      <div class="admin-card-body">
        <form method="post" onsubmit="return confirm('Permanently delete this enquiry and all its notes? This cannot be undone.');">
          <input type="hidden" name="csrf_token" value="<?php echo blog_escape(blog_csrf_token()); ?>">
          <input type="hidden" name="action" value="delete">
          <button type="submit" class="btn btn-outline-danger w-100"><i class="ri-delete-bin-line"></i> Delete enquiry</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
