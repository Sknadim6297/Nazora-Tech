<?php
/**
 * Branded transactional email templates (customer confirmation and internal enquiry notification).
 *
 * Email-client safe: table layout, inline CSS, no JavaScript, no external stylesheets or fonts.
 * Every customer-supplied value is HTML-escaped; URLs are built only from server-side values.
 * Each renderer returns ['subject' => string, 'html' => string, 'text' => string].
 */

require_once __DIR__ . '/site_config.php';

const EMAIL_NAVY = '#0b1526';
const EMAIL_RED = '#c41e3a';
const EMAIL_TEXT = '#334155';
const EMAIL_MUTED = '#64748b';
const EMAIL_BORDER = '#e5e9f0';
const EMAIL_FONT = "Arial, 'Helvetica Neue', Helvetica, sans-serif";

const EMAIL_SUBJECT_CUSTOMER = 'Thank You for Contacting Nazora Tech';
const EMAIL_SUBJECT_ADMIN = 'New Website Enquiry | Nazora Tech';

function email_e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function email_reference(?int $id): string
{
    return $id ? 'NZ-' . str_pad((string) $id, 5, '0', STR_PAD_LEFT) : '';
}

/**
 * Name used in the customer greeting. The name field is visitor-controlled, so anything that looks like a
 * link, address or long text is dropped to stop the confirmation email being used to deliver spam.
 */
function email_greeting_name(string $name): string
{
    $name = trim((string) preg_replace('/\s+/u', ' ', $name));
    if ($name === '' || mb_strlen($name, 'UTF-8') > 60 || preg_match('~(https?:|www\.|@|/|\\\\|\.[a-z]{2,}\b|\d{4,})~iu', $name)) {
        return '';
    }

    return $name;
}

function email_tel_href(string $phone): string
{
    $digits = (string) preg_replace('/\D+/', '', $phone);
    if (strlen($digits) === 10) {
        return 'tel:+91' . $digits;
    }
    if (strlen($digits) === 12 && strncmp($digits, '91', 2) === 0) {
        return 'tel:+' . $digits;
    }

    return $digits !== '' ? 'tel:' . $digits : '';
}

function email_mailto_href(string $email, string $subject = ''): string
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return '';
    }

    return 'mailto:' . str_replace('%40', '@', rawurlencode($email)) . ($subject !== '' ? '?subject=' . rawurlencode($subject) : '');
}

function email_format_datetime(?string $value = null): string
{
    $zone = new DateTimeZone('Asia/Kolkata');
    try {
        $date = $value ? new DateTime($value, $zone) : new DateTime('now', $zone);
    } catch (Exception $exception) {
        $date = new DateTime('now', $zone);
    }

    return $date->format('d M Y, h:i A') . ' IST';
}

/**
 * Shared context (brand, contact details, links) for both templates.
 */
function email_brand_context(array $overrides = []): array
{
    $website = site_public_url();

    return array_merge([
        'company' => site_company_name(),
        'logo_url' => site_email_logo_url(),
        'website_url' => $website . '/',
        'website_label' => (string) preg_replace('#^https?://#i', '', $website),
        'contact_email' => site_contact_email(),
        'phone_display' => site_phone_display(),
        'phone_tel' => site_phone_tel(),
        'location' => SITE_LOCATION,
    ], $overrides);
}

/**
 * Bulletproof button (table cell background, so it renders in Outlook as well).
 */
function email_button(string $href, string $label, string $background, string $color = '#ffffff', string $border = ''): string
{
    $borderCss = $border !== '' ? 'border:2px solid ' . $border . ';' : 'border:2px solid ' . $background . ';';

    return '<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="border-collapse:separate;">'
        . '<tr><td align="center" bgcolor="' . $background . '" style="border-radius:6px;background:' . $background . ';' . $borderCss . '">'
        . '<a href="' . email_e($href) . '" target="_blank" rel="noopener" style="display:inline-block;padding:12px 26px;font-family:' . EMAIL_FONT . ';font-size:15px;font-weight:bold;line-height:20px;color:' . $color . ';text-decoration:none;border-radius:6px;">'
        . email_e($label) . '</a></td></tr></table>';
}

function email_layout(array $ctx, string $title, string $preheader, string $bodyHtml, string $footerNote): string
{
    $font = EMAIL_FONT;
    $logo = '<img src="' . email_e($ctx['logo_url']) . '" width="160" height="73" alt="' . email_e($ctx['company']) . '"'
        . ' style="display:block;width:160px;max-width:160px;height:auto;border:0;outline:none;text-decoration:none;'
        . 'font-family:' . $font . ';font-size:24px;font-weight:bold;line-height:30px;color:' . EMAIL_NAVY . ';">';

    $mailto = email_mailto_href($ctx['contact_email']);
    $contactLinks = '<a href="' . email_e($ctx['phone_tel']) . '" style="color:#ffffff;text-decoration:none;">' . email_e($ctx['phone_display']) . '</a>'
        . ' &nbsp;&middot;&nbsp; <a href="' . email_e($ctx['website_url']) . '" target="_blank" rel="noopener" style="color:#ffffff;text-decoration:none;">' . email_e($ctx['website_label']) . '</a>'
        . ($mailto !== '' ? ' &nbsp;&middot;&nbsp; <a href="' . email_e($mailto) . '" style="color:#ffffff;text-decoration:none;">' . email_e($ctx['contact_email']) . '</a>' : '');

    return '<!DOCTYPE html>'
        . '<html lang="en" xmlns="http://www.w3.org/1999/xhtml"><head>'
        . '<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">'
        . '<meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="x-apple-disable-message-reformatting">'
        . '<meta name="color-scheme" content="light"><meta name="supported-color-schemes" content="light">'
        . '<title>' . email_e($title) . '</title>'
        . '<style>'
        . 'body{margin:0;padding:0;width:100%!important;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;}'
        . 'table{border-collapse:collapse;}img{-ms-interpolation-mode:bicubic;}a{color:' . EMAIL_RED . ';}'
        . '@media only screen and (max-width:620px){'
        . '.nz-container{width:100%!important;max-width:100%!important;border-radius:0!important;}'
        . '.nz-pad{padding-left:22px!important;padding-right:22px!important;}'
        . '.nz-h1{font-size:23px!important;line-height:30px!important;}'
        . '.nz-btns{width:100%!important;}'
        . '.nz-stack{display:block!important;width:100%!important;padding:0 0 10px 0!important;}'
        . '.nz-stack table{width:100%!important;}.nz-stack a{display:block!important;}'
        . '.nz-label{display:block!important;width:100%!important;box-sizing:border-box!important;padding-bottom:2px!important;border-bottom:0!important;}'
        . '.nz-value{display:block!important;width:100%!important;box-sizing:border-box!important;padding-top:0!important;}'
        . '}'
        . '</style></head>'
        . '<body style="margin:0;padding:0;background-color:#ffffff;">'
        . '<div style="display:none;font-size:1px;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;mso-hide:all;">' . email_e($preheader) . '</div>'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff" style="background-color:#ffffff;">'
        . '<tr><td align="center" style="padding:24px 10px;">'
        . '<table role="presentation" class="nz-container" width="600" cellpadding="0" cellspacing="0" border="0" style="width:600px;max-width:600px;background-color:#ffffff;border:1px solid ' . EMAIL_BORDER . ';border-radius:10px;">'
        . '<tr><td height="5" bgcolor="' . EMAIL_RED . '" style="height:5px;font-size:0;line-height:0;background-color:' . EMAIL_RED . ';border-radius:10px 10px 0 0;">&nbsp;</td></tr>'
        . '<tr><td align="center" class="nz-pad" style="padding:28px 40px 6px 40px;">'
        . '<a href="' . email_e($ctx['website_url']) . '" target="_blank" rel="noopener" style="text-decoration:none;">' . $logo . '</a>'
        . '</td></tr>'
        . '<tr><td class="nz-pad" style="padding:18px 40px 30px 40px;font-family:' . $font . ';font-size:15px;line-height:24px;color:' . EMAIL_TEXT . ';">'
        . $bodyHtml
        . '</td></tr>'
        . '<tr><td class="nz-pad" bgcolor="' . EMAIL_NAVY . '" style="padding:24px 40px;background-color:' . EMAIL_NAVY . ';border-radius:0 0 10px 10px;font-family:' . $font . ';text-align:center;">'
        . '<p style="margin:0 0 6px 0;font-size:15px;line-height:22px;font-weight:bold;color:#ffffff;">' . email_e($ctx['company']) . '</p>'
        . '<p style="margin:0 0 10px 0;font-size:13px;line-height:22px;color:#ffffff;">' . $contactLinks . '</p>'
        . '<p style="margin:0 0 10px 0;font-size:12px;line-height:18px;color:#cbd5e1;">' . email_e($ctx['location']) . '</p>'
        . '<p style="margin:0;font-size:11px;line-height:17px;color:#94a3b8;">' . $footerNote . '</p>'
        . '</td></tr>'
        . '</table>'
        . '</td></tr></table>'
        . '</body></html>';
}

function email_text_footer(array $ctx): string
{
    return "--\n" . $ctx['company'] . "\n"
        . 'Phone / WhatsApp: ' . $ctx['phone_display'] . "\n"
        . 'Website: ' . $ctx['website_url'] . "\n"
        . 'Email: ' . $ctx['contact_email'] . "\n"
        . $ctx['location'] . "\n";
}

/**
 * Template A: confirmation sent to the visitor. Never echoes the visitor's message.
 */
function email_render_customer_confirmation(array $enquiry, array $ctx): array
{
    $ctx = email_brand_context($ctx);
    $greetingName = email_greeting_name((string) ($enquiry['name'] ?? ''));
    $service = trim((string) ($enquiry['service'] ?? ''));
    $serviceLabel = $service !== '' ? $service : 'General enquiry';
    $reference = email_reference(isset($enquiry['id']) ? (int) $enquiry['id'] : null);
    $received = email_format_datetime($enquiry['created_at'] ?? null);
    $greeting = $greetingName !== '' ? 'Dear ' . $greetingName . ',' : 'Hello,';

    $summaryRows = [['Service', $serviceLabel]];
    if ($reference !== '') {
        $summaryRows[] = ['Reference number', $reference];
    }
    $summaryRows[] = ['Received', $received];

    $summaryHtml = '';
    foreach ($summaryRows as $i => [$label, $value]) {
        $rowBorder = $i < count($summaryRows) - 1 ? 'border-bottom:1px solid ' . EMAIL_BORDER . ';' : '';
        $summaryHtml .= '<tr>'
            . '<td class="nz-label" width="150" valign="top" style="padding:10px 14px;' . $rowBorder . 'font-family:' . EMAIL_FONT . ';font-size:13px;line-height:20px;color:' . EMAIL_MUTED . ';">' . email_e($label) . '</td>'
            . '<td class="nz-value" valign="top" style="padding:10px 14px;' . $rowBorder . 'font-family:' . EMAIL_FONT . ';font-size:14px;line-height:20px;font-weight:bold;color:' . EMAIL_NAVY . ';word-break:break-word;">' . email_e($value) . '</td>'
            . '</tr>';
    }

    $body = '<h1 class="nz-h1" style="margin:0 0 18px 0;font-family:' . EMAIL_FONT . ';font-size:26px;line-height:34px;font-weight:bold;color:' . EMAIL_NAVY . ';text-align:center;">Thank you for contacting us</h1>'
        . '<p style="margin:0 0 14px 0;">' . email_e($greeting) . '</p>'
        . '<p style="margin:0 0 14px 0;">Thank you for getting in touch with ' . email_e($ctx['company']) . '. This email confirms that we have received your enquiry.</p>'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:6px 0 20px 0;background-color:#f8fafc;border:1px solid ' . EMAIL_BORDER . ';border-left:4px solid ' . EMAIL_RED . ';border-radius:6px;">'
        . $summaryHtml
        . '</table>'
        . '<p style="margin:0 0 14px 0;">Our team will review your requirements and get back to you using the contact details you provided. If you would like to add anything, simply reply to this email'
        . ($reference !== '' ? ' and mention your reference number <strong style="color:' . EMAIL_NAVY . ';">' . email_e($reference) . '</strong>' : '') . '.</p>'
        . '<p style="margin:0 0 24px 0;">Prefer to talk? You can call or WhatsApp us on <a href="' . email_e($ctx['phone_tel']) . '" style="color:' . EMAIL_RED . ';font-weight:bold;text-decoration:none;">' . email_e($ctx['phone_display']) . '</a>.</p>'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td align="center" style="padding:0 0 26px 0;">'
        . email_button($ctx['website_url'], 'Visit Our Website', EMAIL_RED)
        . '</td></tr></table>'
        . '<p style="margin:0;">Warm regards,<br><strong style="color:' . EMAIL_NAVY . ';">Team ' . email_e($ctx['company']) . '</strong></p>';

    $footerNote = 'You are receiving this one-time email because an enquiry was submitted on ' . email_e($ctx['website_label'])
        . ' using this email address. If this was not you, please ignore this message.';

    $text = $greeting . "\n\n"
        . 'Thank you for getting in touch with ' . $ctx['company'] . ". This email confirms that we have received your enquiry.\n\n"
        . 'Service: ' . $serviceLabel . "\n"
        . ($reference !== '' ? 'Reference number: ' . $reference . "\n" : '')
        . 'Received: ' . $received . "\n\n"
        . "Our team will review your requirements and get back to you using the contact details you provided. If you would like to add anything, simply reply to this email"
        . ($reference !== '' ? ' and mention your reference number ' . $reference : '') . ".\n\n"
        . 'Prefer to talk? You can call or WhatsApp us on ' . $ctx['phone_display'] . ".\n\n"
        . 'Visit our website: ' . $ctx['website_url'] . "\n\n"
        . "Warm regards,\nTeam " . $ctx['company'] . "\n\n"
        . email_text_footer($ctx) . "\n"
        . 'You are receiving this one-time email because an enquiry was submitted on ' . $ctx['website_label'] . " using this email address. If this was not you, please ignore this message.\n";

    return [
        'subject' => EMAIL_SUBJECT_CUSTOMER,
        'html' => email_layout($ctx, EMAIL_SUBJECT_CUSTOMER, 'We have received your enquiry' . ($reference !== '' ? ' (' . $reference . ')' : '') . '. Thank you for contacting ' . $ctx['company'] . '.', $body, $footerNote),
        'text' => $text,
    ];
}

/**
 * Template B: internal notification to the business inbox. $ctx['lead_url'] links to the
 * sign-in protected admin detail page (no tokens in the link).
 */
function email_render_admin_notification(array $enquiry, array $ctx): array
{
    $ctx = email_brand_context($ctx);
    $isTest = !empty($ctx['test']);
    $id = isset($enquiry['id']) ? (int) $enquiry['id'] : 0;
    $reference = email_reference($id ?: null);
    $name = trim((string) ($enquiry['name'] ?? ''));
    $email = trim((string) ($enquiry['email'] ?? ''));
    $phone = trim((string) ($enquiry['phone'] ?? ''));
    $service = trim((string) ($enquiry['service'] ?? ''));
    $source = trim((string) ($enquiry['source_label'] ?? ''));
    $message = str_replace(["\r\n", "\r"], "\n", (string) ($enquiry['message'] ?? ''));
    $received = email_format_datetime($enquiry['created_at'] ?? null);
    $leadUrl = ($id && !empty($ctx['lead_url'])) ? (string) $ctx['lead_url'] : '';
    $replyHref = email_mailto_href($email, 'Re: Your enquiry to ' . $ctx['company'] . ($reference !== '' ? ' (' . $reference . ')' : ''));
    $telHref = email_tel_href($phone);
    $subject = ($isTest ? '[Test] ' : '') . EMAIL_SUBJECT_ADMIN;

    $rows = [
        ['Name', $name !== '' ? email_e($name) : '&mdash;'],
        ['Email', $replyHref !== '' ? '<a href="' . email_e(email_mailto_href($email)) . '" style="color:' . EMAIL_RED . ';text-decoration:none;">' . email_e($email) . '</a>' : ($email !== '' ? email_e($email) : '&mdash;')],
        ['Phone', $telHref !== '' ? '<a href="' . email_e($telHref) . '" style="color:' . EMAIL_RED . ';text-decoration:none;">' . email_e($phone) . '</a>' : ($phone !== '' ? email_e($phone) : '&mdash;')],
        ['Service', $service !== '' ? email_e($service) : 'Not specified'],
    ];
    if ($source !== '') {
        $rows[] = ['Form', email_e($source)];
    }
    $rows[] = ['Date &amp; time', email_e($received)];
    $rows[] = ['Enquiry ID', $reference !== '' ? email_e($reference) . ' <span style="color:' . EMAIL_MUTED . ';font-weight:normal;">(#' . $id . ')</span>' : 'Not saved'];

    $rowsHtml = '';
    foreach ($rows as $i => [$label, $valueHtml]) {
        $rowBorder = $i < count($rows) - 1 ? 'border-bottom:1px solid ' . EMAIL_BORDER . ';' : '';
        $rowsHtml .= '<tr>'
            . '<td class="nz-label" width="130" valign="top" style="padding:10px 14px;' . $rowBorder . 'font-family:' . EMAIL_FONT . ';font-size:13px;line-height:20px;color:' . EMAIL_MUTED . ';">' . $label . '</td>'
            . '<td class="nz-value" valign="top" style="padding:10px 14px;' . $rowBorder . 'font-family:' . EMAIL_FONT . ';font-size:14px;line-height:20px;font-weight:bold;color:' . EMAIL_NAVY . ';word-break:break-word;">' . $valueHtml . '</td>'
            . '</tr>';
    }

    $buttons = [];
    if ($leadUrl !== '') {
        $buttons[] = email_button($leadUrl, 'View Lead', EMAIL_RED);
    }
    if ($replyHref !== '') {
        $buttons[] = email_button($replyHref, 'Reply by Email', EMAIL_NAVY);
    }
    if ($telHref !== '') {
        $buttons[] = email_button($telHref, 'Call', '#ffffff', EMAIL_NAVY, EMAIL_NAVY);
    }
    $buttonsHtml = '';
    if ($buttons) {
        $cells = '';
        foreach ($buttons as $button) {
            $cells .= '<td class="nz-stack" valign="top" style="padding:0 10px 10px 0;">' . $button . '</td>';
        }
        $buttonsHtml = '<table role="presentation" class="nz-btns" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 8px 0;"><tr>' . $cells . '</tr></table>';
    }

    $body = '<p style="margin:0 0 8px 0;text-align:center;"><span style="display:inline-block;padding:4px 12px;border-radius:20px;background-color:#fdecef;font-family:' . EMAIL_FONT . ';font-size:11px;line-height:16px;font-weight:bold;letter-spacing:1px;color:' . EMAIL_RED . ';">' . ($isTest ? 'TEST MESSAGE' : 'NEW ENQUIRY') . '</span></p>'
        . '<h1 class="nz-h1" style="margin:0 0 6px 0;font-family:' . EMAIL_FONT . ';font-size:26px;line-height:34px;font-weight:bold;color:' . EMAIL_NAVY . ';text-align:center;">New Website Enquiry</h1>'
        . '<p style="margin:0 0 22px 0;text-align:center;font-size:13px;line-height:20px;color:' . EMAIL_MUTED . ';">Received ' . email_e($received) . ($reference !== '' ? ' &middot; ' . email_e($reference) : '') . '</p>'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 20px 0;border:1px solid ' . EMAIL_BORDER . ';border-radius:6px;">'
        . $rowsHtml
        . '</table>'
        . '<p style="margin:0 0 8px 0;font-size:13px;line-height:20px;font-weight:bold;color:' . EMAIL_MUTED . ';text-transform:uppercase;letter-spacing:1px;">Message</p>'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px 0;"><tr>'
        . '<td style="padding:14px 16px;background-color:#f8fafc;border-left:4px solid ' . EMAIL_RED . ';border-radius:4px;font-family:' . EMAIL_FONT . ';font-size:14px;line-height:22px;color:' . EMAIL_TEXT . ';word-break:break-word;">'
        . nl2br(email_e($message), false)
        . '</td></tr></table>'
        . $buttonsHtml
        . ($leadUrl !== '' ? '<p style="margin:6px 0 0 0;font-size:12px;line-height:18px;color:' . EMAIL_MUTED . ';">View Lead opens the admin panel; sign-in is required.</p>' : '');

    $footerNote = 'Internal notification from the ' . email_e($ctx['website_label']) . ' contact form. It contains personal data; please do not forward it outside ' . email_e($ctx['company']) . '.';

    $text = ($isTest ? "[TEST MESSAGE]\n" : '') . "New Website Enquiry\n\n"
        . 'Name: ' . ($name !== '' ? $name : '-') . "\n"
        . 'Email: ' . ($email !== '' ? $email : '-') . "\n"
        . 'Phone: ' . ($phone !== '' ? $phone : '-') . "\n"
        . 'Service: ' . ($service !== '' ? $service : 'Not specified') . "\n"
        . ($source !== '' ? 'Form: ' . $source . "\n" : '')
        . 'Date & time: ' . $received . "\n"
        . 'Enquiry ID: ' . ($reference !== '' ? $reference . ' (#' . $id . ')' : 'Not saved') . "\n\n"
        . "Message:\n" . $message . "\n\n"
        . ($leadUrl !== '' ? 'View lead (sign-in required): ' . $leadUrl . "\n" : '')
        . ($email !== '' ? 'Reply by email: ' . $email . "\n" : '')
        . ($telHref !== '' ? 'Call: ' . substr($telHref, 4) . "\n" : '')
        . "\n" . email_text_footer($ctx);

    return [
        'subject' => $subject,
        'html' => email_layout($ctx, $subject, 'New enquiry from ' . ($name !== '' ? $name : 'the website') . ($service !== '' ? ' about ' . $service : '') . '.', $body, $footerNote),
        'text' => $text,
    ];
}
