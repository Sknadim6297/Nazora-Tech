<?php
/**
 * Contact enquiry email delivery over authenticated SMTP (PHPMailer).
 *
 * Configuration is read from server environment variables or an optional non-tracked
 * private PHP config file (../nazora-config.php above public_html, or storage/config.local.php).
 * Nothing is hardcoded:
 *   SMTP_HOST             SMTP server hostname (Hostinger Email: smtp.hostinger.com)
 *   SMTP_PORT             SMTP port (Hostinger Email: 465 for SSL, or 587 for TLS/STARTTLS)
 *   SMTP_ENCRYPTION       ssl (implicit TLS, port 465) | tls (STARTTLS, port 587) | none
 *   SMTP_USERNAME         Full mailbox login (e.g. info@nazora.tech once created & verified)
 *   SMTP_PASSWORD         Mailbox password / app password
 *   SMTP_FROM_EMAIL       Sender address matching the authenticated mailbox (required)
 *   SMTP_FROM_NAME        Sender display name (default: company name)
 *   CONTACT_NOTIFY_EMAIL  Inbox that receives enquiries (default: site_contact_email())
 *   CONTACT_AUTOREPLY     1 to send a confirmation email to the visitor (default: off)
 *   EMAIL_LOGO_URL        Optional HTTPS logo address for email templates
 *
 * If required values are missing, external delivery stays disabled and enquiries are only stored.
 * TLS certificates are always verified (PHPMailer default); there is no option to disable it.
 */

require_once __DIR__ . '/site_config.php';
require_once __DIR__ . '/email_templates.php';

function contact_env(string $key): string
{
    return site_env($key);
}

/**
 * Strips CR/LF and control characters so arbitrary strings cannot alter email headers.
 */
function contact_header_safe(string $value, int $max = 200): string
{
    $value = preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $value);
    $value = trim((string) preg_replace('/\s+/u', ' ', (string) $value));

    return mb_substr($value, 0, $max, 'UTF-8');
}

/**
 * Reports where runtime configuration was found ('env', 'parent_config', 'storage_config', 'none').
 */
function contact_mail_config_source(): string
{
    foreach (['SMTP_HOST', 'SMTP_PORT', 'SMTP_FROM_EMAIL'] as $envKey) {
        foreach ([getenv($envKey), $_ENV[$envKey] ?? null, $_SERVER[$envKey] ?? null, $_SERVER['REDIRECT_' . $envKey] ?? null] as $val) {
            if (is_string($val) && trim($val) !== '') {
                return 'env';
            }
        }
    }

    $source = 'none';
    site_local_config($source);

    return $source;
}

/**
 * Returns the SMTP configuration, or null with the list of missing variables when incomplete.
 */
function contact_mail_config(?array &$missing = null): ?array
{
    $missing = [];

    $host = contact_env('SMTP_HOST');
    $port = contact_env('SMTP_PORT');
    $encryption = strtolower(contact_env('SMTP_ENCRYPTION') ?: 'tls');
    $username = contact_env('SMTP_USERNAME');
    $password = contact_env('SMTP_PASSWORD');
    $fromEmail = contact_header_safe(contact_env('SMTP_FROM_EMAIL'), 190);
    $fromName = contact_header_safe(contact_env('SMTP_FROM_NAME') ?: site_company_name(), 120);
    $notify = contact_header_safe(contact_env('CONTACT_NOTIFY_EMAIL') ?: site_contact_email(), 190);

    if ($host === '') {
        $missing[] = 'SMTP_HOST';
    }
    if ($port === '' || !ctype_digit($port) || (int) $port < 1 || (int) $port > 65535) {
        $missing[] = 'SMTP_PORT';
    }
    if ($fromEmail === '' || !filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
        $missing[] = 'SMTP_FROM_EMAIL';
    }
    if ($username !== '' && $password === '') {
        $missing[] = 'SMTP_PASSWORD';
    }
    if (!in_array($encryption, ['tls', 'ssl', 'none'], true)) {
        $missing[] = 'SMTP_ENCRYPTION (tls|ssl|none)';
    }
    if (!filter_var($notify, FILTER_VALIDATE_EMAIL)) {
        $missing[] = 'CONTACT_NOTIFY_EMAIL';
    }
    if (!is_file(dirname(__DIR__) . '/vendor/autoload.php')) {
        $missing[] = 'vendor/autoload.php (run composer install --no-dev)';
    }

    if ($missing) {
        return null;
    }

    return [
        'host' => contact_header_safe($host, 255),
        'port' => (int) $port,
        'encryption' => $encryption,
        'username' => $username,
        'password' => $password,
        'from_email' => $fromEmail,
        'from_name' => $fromName !== '' ? $fromName : site_company_name(),
        'notify_email' => $notify,
        'autoreply' => in_array(strtolower(contact_env('CONTACT_AUTOREPLY')), ['1', 'true', 'yes', 'on'], true),
    ];
}

function contact_mail_is_configured(): bool
{
    return contact_mail_config() !== null;
}

/**
 * Non-blocking configuration warnings (e.g. sender not matching the authenticated mailbox).
 */
function contact_mail_config_warnings(array $config): array
{
    $warnings = [];
    if ($config['username'] !== '' && strcasecmp($config['username'], $config['from_email']) !== 0) {
        $warnings[] = 'SMTP_FROM_EMAIL differs from SMTP_USERNAME. Hostinger and most providers reject or spam-flag mail whose sender does not match the signed-in mailbox.';
    }
    if ($config['username'] === '') {
        $warnings[] = 'SMTP_USERNAME is empty, so the server is used without authentication.';
    }
    if ($config['encryption'] === 'none') {
        $warnings[] = 'SMTP_ENCRYPTION is none: credentials and messages would travel unencrypted. Use ssl (465) or tls (587).';
    }
    if (($config['encryption'] === 'ssl' && $config['port'] === 587) || ($config['encryption'] === 'tls' && $config['port'] === 465)) {
        $warnings[] = 'Port and encryption look swapped: use 465 with ssl, or 587 with tls.';
    }

    return $warnings;
}

/**
 * Builds a PHPMailer instance for the given configuration (debug output always off).
 */
function contact_mailer_instance(array $config): \PHPMailer\PHPMailer\PHPMailer
{
    require_once dirname(__DIR__) . '/vendor/autoload.php';

    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = $config['host'];
    $mail->Port = $config['port'];
    $mail->SMTPDebug = 0;
    $mail->Debugoutput = static function (): void {
    };
    $mail->Timeout = 12;
    $mail->CharSet = \PHPMailer\PHPMailer\PHPMailer::CHARSET_UTF8;
    $mail->Encoding = \PHPMailer\PHPMailer\PHPMailer::ENCODING_QUOTED_PRINTABLE;
    // A single space omits the "X-Mailer: PHPMailer" header, which spam filters score against
    $mail->XMailer = ' ';

    if ($config['encryption'] === 'ssl') {
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
    } elseif ($config['encryption'] === 'tls') {
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    } else {
        $mail->SMTPSecure = '';
        $mail->SMTPAutoTLS = false;
    }

    if ($config['username'] !== '') {
        $mail->SMTPAuth = true;
        $mail->Username = $config['username'];
        $mail->Password = $config['password'];
    }

    $mail->setFrom($config['from_email'], $config['from_name'], false);

    return $mail;
}

/**
 * Template context for a live send: the footer shows the authenticated sender mailbox.
 */
function contact_template_context(array $config, array $extra = []): array
{
    return email_brand_context(array_merge(['contact_email' => $config['from_email']], $extra));
}

function contact_lead_url(int $id): string
{
    return $id > 0 ? site_url('admin/enquiry') . '?id=' . $id : '';
}

/**
 * Sends the internal enquiry notification. Never throws; never returns provider error text.
 *
 * @param array $enquiry name, email, phone, service, message, id, created_at, source_label (all optional)
 * @return string 'sent' | 'disabled' | 'failed'
 */
function contact_send_notification(array $enquiry, bool $isTest = false): string
{
    $config = contact_mail_config();
    if ($config === null) {
        return 'disabled';
    }

    $clean = [
        'id' => isset($enquiry['id']) ? (int) $enquiry['id'] : 0,
        'name' => contact_header_safe((string) ($enquiry['name'] ?? ''), 120),
        'email' => contact_header_safe((string) ($enquiry['email'] ?? ''), 190),
        'phone' => contact_header_safe((string) ($enquiry['phone'] ?? ''), 30),
        'service' => contact_header_safe((string) ($enquiry['service'] ?? ''), 100),
        'source_label' => contact_header_safe((string) ($enquiry['source_label'] ?? ''), 60),
        'message' => (string) ($enquiry['message'] ?? ''),
        'created_at' => $enquiry['created_at'] ?? null,
    ];
    $reference = email_reference($clean['id'] ?: null);
    $rendered = email_render_admin_notification($clean, contact_template_context($config, [
        'lead_url' => contact_lead_url($clean['id']),
        'test' => $isTest,
    ]));

    try {
        $mail = contact_mailer_instance($config);
        $mail->addAddress($config['notify_email']);
        // Reply goes to the visitor; address is validated and header-safe
        if (filter_var($clean['email'], FILTER_VALIDATE_EMAIL)) {
            $mail->addReplyTo($clean['email'], $clean['name']);
        }
        $mail->Subject = contact_header_safe($rendered['subject'], 200);
        $mail->isHTML(true);
        $mail->Body = $rendered['html'];
        $mail->AltBody = $rendered['text'];
        $mail->send();

        return 'sent';
    } catch (\Throwable $exception) {
        $detail = isset($mail) && $mail instanceof \PHPMailer\PHPMailer\PHPMailer && $mail->ErrorInfo !== ''
            ? $mail->ErrorInfo
            : get_class($exception);
        // Server-side log only: no visitor data, no credentials
        error_log('[contact] SMTP notification failed for enquiry ' . ($reference ?: '(unsaved)') . ': ' . contact_mail_redact($detail, $config));

        return 'failed';
    }
}

/**
 * Customer acknowledgement (CONTACT_AUTOREPLY=1). Goes only to the address the visitor submitted,
 * once per enquiry, and never echoes the visitor's message.
 *
 * @return string 'sent' | 'failed' | 'off' (auto-reply disabled) | 'disabled' (SMTP not configured) | 'skipped' (invalid address)
 */
function contact_send_customer_ack(array $enquiry): string
{
    $config = contact_mail_config();
    if ($config === null) {
        return 'disabled';
    }
    if (!$config['autoreply']) {
        return 'off';
    }

    $email = contact_header_safe((string) ($enquiry['email'] ?? ''), 190);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'skipped';
    }

    $id = isset($enquiry['id']) ? (int) $enquiry['id'] : 0;
    $name = email_greeting_name(contact_header_safe((string) ($enquiry['name'] ?? ''), 120));
    $rendered = email_render_customer_confirmation([
        'id' => $id,
        'name' => $name,
        'service' => contact_header_safe((string) ($enquiry['service'] ?? ''), 100),
        'created_at' => $enquiry['created_at'] ?? null,
    ], contact_template_context($config));

    try {
        $mail = contact_mailer_instance($config);
        $mail->addAddress($email, $name);
        $mail->addReplyTo($config['notify_email'], $config['from_name']);
        $mail->Subject = contact_header_safe($rendered['subject'], 200);
        // RFC 3834: marks the message as automatic so mailbox auto-responders do not reply in a loop
        $mail->addCustomHeader('Auto-Submitted', 'auto-replied');
        $mail->addCustomHeader('X-Auto-Response-Suppress', 'All');
        $mail->isHTML(true);
        $mail->Body = $rendered['html'];
        $mail->AltBody = $rendered['text'];
        $mail->send();

        return 'sent';
    } catch (\Throwable $exception) {
        error_log('[contact] SMTP customer acknowledgement failed for enquiry ' . (email_reference($id ?: null) ?: '(unsaved)') . ': '
            . contact_mail_redact(isset($mail) ? (string) $mail->ErrorInfo : get_class($exception), $config));

        return 'failed';
    }
}

function contact_mail_redact(string $text, array $config): string
{
    foreach (['password', 'username'] as $key) {
        if (!empty($config[$key]) && strlen($config[$key]) >= 3) {
            $text = str_replace($config[$key], '[redacted]', $text);
        }
    }

    return mb_substr(preg_replace('/[\r\n]+/', ' ', $text), 0, 300, 'UTF-8');
}
