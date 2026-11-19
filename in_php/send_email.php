<?php
require_once __DIR__ . '/includes/site_config.php';
// Contact form handler: validate -> rate limit -> store enquiry -> notify by SMTP (when configured)
header('Content-Type: application/json; charset=UTF-8');
header('X-Robots-Tag: noindex, nofollow');
header('Cache-Control: no-store');

error_reporting(E_ALL);
ini_set('display_errors', 0);

const CONTACT_RATE_LIMIT = 5;
const CONTACT_RATE_WINDOW = 900;

const CONTACT_MSG_SENT = 'Thank you! Your message has been sent to our team. We will contact you soon.';
const CONTACT_MSG_STORED = 'Thank you! Your enquiry has been received and saved for our team. Email notification is temporarily unavailable, so for a faster reply please also contact us on WhatsApp or by phone.';
const CONTACT_MSG_FAILED = 'Sorry, we could not submit your enquiry right now. Please contact us directly on WhatsApp or by phone.';

function contact_respond(array $payload, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($payload);
    exit;
}

// Single-line header-safe value (no CR/LF or control characters)
function clean_line($data, int $max = 200): string
{
    $data = preg_replace('/[\x00-\x1F\x7F]+/u', ' ', (string) $data);
    $data = trim((string) preg_replace('/\s+/u', ' ', (string) $data));

    return mb_substr($data, 0, $max, 'UTF-8');
}

function clean_text($data, int $max = 5000): string
{
    $data = str_replace(["\r\n", "\r"], "\n", (string) $data);
    $data = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/u', '', $data);

    return mb_substr(trim((string) $data), 0, $max, 'UTF-8');
}

function contact_storage_dir(): string
{
    $dir = __DIR__ . '/storage/submissions';
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }

    return $dir;
}

function contact_rate_limited(): bool
{
    // The hosting proxy appends the real client address last; earlier entries are client-controlled
    $forwarded = array_filter(array_map('trim', explode(',', (string) ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? ''))));
    $ip = $forwarded ? (string) end($forwarded) : (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    $dir = contact_storage_dir();
    $file = $dir . '/.rate_' . hash('sha256', $ip) . '.json';
    $now = time();

    if (random_int(1, 50) === 1) {
        foreach (glob($dir . '/.rate_*.json') ?: [] as $old) {
            if (@filemtime($old) < $now - CONTACT_RATE_WINDOW) {
                @unlink($old);
            }
        }
    }

    $hits = [];
    if (is_file($file)) {
        $decoded = json_decode((string) @file_get_contents($file), true);
        if (is_array($decoded)) {
            $hits = array_values(array_filter($decoded, function ($t) use ($now) {
                return is_int($t) && $t > $now - CONTACT_RATE_WINDOW;
            }));
        }
    }

    if (count($hits) >= CONTACT_RATE_LIMIT) {
        return true;
    }

    $hits[] = $now;
    @file_put_contents($file, json_encode($hits), LOCK_EX);

    return false;
}

// Fallback only: used when the database cannot be written, so the enquiry is not lost
function save_submission_to_file(string $name, string $email, string $phone, string $service, string $message)
{
    $filename = contact_storage_dir() . '/submission_' . date('Y_m_d_H_i_s') . '_' . bin2hex(random_bytes(6)) . '.txt';
    $content = "=== CONTACT FORM SUBMISSION ===\n";
    $content .= 'Timestamp: ' . date('Y-m-d H:i:s') . "\n";
    $content .= "-------------------------------\n";
    $content .= 'Name: ' . $name . "\n";
    $content .= 'Email: ' . $email . "\n";
    $content .= 'Phone: ' . $phone . "\n";
    if ($service !== '') {
        $content .= 'Service: ' . $service . "\n";
    }
    $content .= "-------------------------------\n";
    $content .= "Message:\n" . $message . "\n";
    $content .= "=== END SUBMISSION ===\n\n";

    return @file_put_contents($filename, $content, LOCK_EX);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    contact_respond(['success' => false, 'message' => 'Invalid request method'], 405);
}

// Honeypot: real visitors never see or fill this field. Nothing is stored or sent.
if (trim((string) ($_POST['company_website'] ?? '')) !== '') {
    contact_respond(['success' => true, 'message' => CONTACT_MSG_SENT]);
}

$name = clean_line($_POST['name'] ?? '', 120);
$email = clean_line($_POST['email'] ?? '', 190);
$phone = clean_line($_POST['phone'] ?? '', 30);
$service = clean_line($_POST['service'] ?? '', 100);
$message = clean_text($_POST['message'] ?? '');
$source = clean_line($_POST['form_id'] ?? '', 40);

$errors = [];

if ($name === '') {
    $errors[] = 'Name is required';
}

if ($email === '') {
    $errors[] = 'Email is required';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format';
}

if ($phone === '') {
    $errors[] = 'Phone number is required';
} elseif (!preg_match('/^[0-9]{10}$/', str_replace(['-', ' '], '', $phone))) {
    $errors[] = 'Invalid phone number format';
}

if ($message === '') {
    $errors[] = 'Message is required';
} elseif (mb_strlen($message, 'UTF-8') < 10) {
    $errors[] = 'Message must be at least 10 characters long';
}

if (!empty($errors)) {
    contact_respond([
        'success' => false,
        'message' => 'Validation failed',
        'errors' => $errors,
    ]);
}

if (contact_rate_limited()) {
    contact_respond([
        'success' => false,
        'message' => 'Too many messages from this connection. Please wait a few minutes or contact us on WhatsApp.',
    ], 429);
}

$enquiry = [
    'name' => $name,
    'email' => $email,
    'phone' => $phone,
    'service' => $service,
    'message' => $message,
    'source' => $source,
];

// 1) Store the enquiry (database first; text file only if the database is unavailable).
//    A retry of the same submission reuses the saved lead and sends no further emails.
$enquiryId = null;
$stored = false;
try {
    require_once __DIR__ . '/includes/enquiries_app.php';
    $duplicate = enquiry_find_recent_duplicate($enquiry);
    if ($duplicate) {
        $duplicateSent = in_array($duplicate['email_status'], ['sent', 'sending'], true);
        contact_respond([
            'success' => true,
            'email_sent' => $duplicate['email_status'] === 'sent',
            'delivery' => $duplicate['email_status'] === 'sent' ? 'sent' : 'stored',
            'duplicate' => true,
            'message' => $duplicateSent ? CONTACT_MSG_SENT : CONTACT_MSG_STORED,
        ]);
    }
    $enquiryId = enquiry_create($enquiry);
    $enquiry['id'] = $enquiryId;
    $enquiry['created_at'] = blog_now();
    $enquiry['source_label'] = enquiry_source_label(enquiry_normalize_source($source));
    $stored = $enquiryId > 0;
} catch (Throwable $exception) {
    error_log('[contact] Could not save enquiry to database: ' . get_class($exception));
}

if (!$stored) {
    $stored = save_submission_to_file($name, $email, $phone, $service, $message) !== false;
    if (!$stored) {
        error_log('[contact] Could not write enquiry fallback file to storage/submissions');
    }
}

// 2) Admin notification over SMTP, only when configured. Each channel is claimed before sending
//    and its result recorded separately; a failure never hides the saved enquiry.
$emailStatus = 'disabled';
try {
    require_once __DIR__ . '/includes/contact_mailer.php';
    if (!$enquiryId || enquiry_claim_email($enquiryId, 'admin')) {
        $emailStatus = contact_send_notification($enquiry);
    }
} catch (Throwable $exception) {
    $emailStatus = 'failed';
    error_log('[contact] Mailer unavailable: ' . get_class($exception));
}

if ($enquiryId) {
    try {
        enquiry_set_delivery_status($enquiryId, 'admin', $emailStatus);
    } catch (Throwable $exception) {
        error_log('[contact] Could not record email status for enquiry #' . $enquiryId);
    }

    // 3) Customer acknowledgement: only when CONTACT_AUTOREPLY is on, only for saved enquiries
    try {
        if (enquiry_claim_email($enquiryId, 'customer')) {
            enquiry_set_delivery_status($enquiryId, 'customer', contact_send_customer_ack($enquiry));
        }
    } catch (Throwable $exception) {
        error_log('[contact] Customer acknowledgement not recorded for enquiry #' . $enquiryId . ': ' . get_class($exception));
        try {
            enquiry_set_delivery_status($enquiryId, 'customer', 'failed');
        } catch (Throwable $ignored) {
        }
    }
}

// 4) Honest response: "sent" only when the email really left; "saved" when only stored
if ($emailStatus === 'sent') {
    contact_respond(['success' => true, 'email_sent' => true, 'delivery' => 'sent', 'message' => CONTACT_MSG_SENT]);
}

if ($stored) {
    contact_respond(['success' => true, 'email_sent' => false, 'delivery' => 'stored', 'message' => CONTACT_MSG_STORED]);
}

contact_respond(['success' => false, 'email_sent' => false, 'delivery' => 'failed', 'message' => CONTACT_MSG_FAILED], 503);
