<?php
require_once __DIR__ . '/../includes/enquiries_app.php';
blog_require_login();

// POST + CSRF only: an export of personal data must be a deliberate admin action
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !blog_verify_csrf($_POST['csrf_token'] ?? null)) {
    blog_flash('danger', 'Export link expired. Please use the Export CSV button again.');
    header('Location: enquiries.php');
    exit;
}

$filters = enquiries_filters_from($_POST);
$rows = enquiries_export_rows($filters);

$filename = 'nazora-enquiries-' . date('Y-m-d-His') . '.csv';
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-store, no-cache, must-revalidate, private');
header('Pragma: no-cache');
header('X-Content-Type-Options: nosniff');

$out = fopen('php://output', 'wb');
// UTF-8 BOM so Excel shows non-English names correctly
fwrite($out, "\xEF\xBB\xBF");
fputcsv($out, ['Reference', 'Received', 'Name', 'Email', 'Phone', 'Service', 'Message', 'Source', 'Status', 'Email notification', 'Customer confirmation', 'Last updated'], ',', '"', '');

foreach ($rows as $row) {
    fputcsv($out, array_map('enquiries_csv_safe', [
        '#' . $row['id'],
        $row['created_at'],
        $row['name'],
        $row['email'],
        $row['phone'],
        (string) $row['service'],
        $row['message'],
        enquiry_source_label((string) $row['source']),
        enquiry_status_label((string) $row['status']),
        enquiry_email_status_label((string) $row['email_status']),
        enquiry_customer_email_status_label((string) ($row['customer_email_status'] ?? 'off')),
        $row['updated_at'],
    ]), ',', '"', '');
}

fclose($out);
exit;
