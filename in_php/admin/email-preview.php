<?php
require_once __DIR__ . '/../includes/enquiries_app.php';
require_once __DIR__ . '/../includes/contact_mailer.php';
blog_require_login();

// Renders the email templates with clearly fake sample data. Nothing is sent.
$template = (string) ($_GET['template'] ?? 'admin');
$sample = [
    'id' => 123,
    'name' => 'Sample Customer',
    'email' => 'customer@example.com',
    'phone' => '9876543210',
    'service' => 'Website Development',
    'source_label' => 'Contact page form',
    'message' => "Hello, this is sample text used only to preview the email layout.\nNo real customer data is shown here.",
    'created_at' => blog_now(),
];

$config = contact_mail_config();
$context = $config ? contact_template_context($config) : email_brand_context();

if ($template === 'customer') {
    $rendered = email_render_customer_confirmation($sample, $context);
} else {
    $rendered = email_render_admin_notification($sample, $context + ['lead_url' => contact_lead_url((int) $sample['id'])]);
}

header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-store');
header('X-Robots-Tag: noindex, nofollow');
if (($_GET['format'] ?? '') === 'text') {
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Subject: ' . $rendered['subject'] . "\n\n" . $rendered['text'];
    exit;
}
echo $rendered['html'];
