<?php
/**
 * Shared helpers for the legal pages (Privacy, Terms, Refund, Cancellation).
 *
 * Owner-decision notes mark terms the business owner has not confirmed yet. They are shown on local
 * and staging hosts so the owner can review them, and hidden on the live domain unless
 * LEGAL_SHOW_OWNER_NOTES=1. The public wording around each note is written to be accurate without them.
 */

require_once __DIR__ . '/site_config.php';

const LEGAL_LAST_UPDATED = '19 November 2026';

function legal_e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function legal_show_owner_notes(): bool
{
    $flag = strtolower(site_env('LEGAL_SHOW_OWNER_NOTES'));
    if (in_array($flag, ['1', 'true', 'yes', 'on'], true)) {
        return true;
    }
    if (in_array($flag, ['0', 'false', 'no', 'off'], true)) {
        return false;
    }

    $host = strtolower((string) preg_replace('/:\d+$/', '', (string) ($_SERVER['HTTP_HOST'] ?? 'localhost')));

    return !in_array($host, ['nazora.tech', 'www.nazora.tech'], true);
}

function legal_owner_note(string $text): void
{
    if (!legal_show_owner_notes()) {
        return;
    }

    echo '<div class="nz-legal-note" role="note"><strong>Owner decision required:</strong> ' . legal_e($text) . '</div>';
}

/**
 * @param array<string,string> $sections id => heading
 */
function legal_toc(array $sections): void
{
    echo '<nav class="nz-legal-toc" aria-labelledby="nz-legal-toc-title"><p class="nz-legal-toc-title" id="nz-legal-toc-title">On this page</p><ol>';
    foreach ($sections as $id => $heading) {
        echo '<li><a href="#' . legal_e($id) . '">' . legal_e($heading) . '</a></li>';
    }
    echo '</ol></nav>';
}

function legal_heading(string $id, string $heading): void
{
    echo '<h2 class="nz-legal-heading" id="' . legal_e($id) . '">' . legal_e($heading) . '</h2>';
}

function legal_email_link(): string
{
    $email = site_contact_email();

    return '<a href="mailto:' . legal_e($email) . '">' . legal_e($email) . '</a>';
}

function legal_phone_link(): string
{
    return '<a href="' . legal_e(site_phone_tel()) . '">' . legal_e(site_phone_display()) . '</a>';
}

/**
 * Standard contact block used at the end of each legal page.
 */
function legal_contact_block(): void
{
    echo '<ul class="nz-legal-contact">'
        . '<li><strong>Business:</strong> ' . legal_e(site_company_name()) . ' (operated by SK NADIM)</li>'
        . '<li><strong>Location:</strong> ' . legal_e(SITE_LOCATION) . '</li>'
        . '<li><strong>Email:</strong> ' . legal_email_link() . '</li>'
        . '<li><strong>Phone / WhatsApp:</strong> ' . legal_phone_link() . '</li>'
        . '<li><strong>Online:</strong> <a href="contact">Contact page</a></li>'
        . '</ul>';
}

/**
 * Links to the other legal pages.
 */
function legal_related(string $current): void
{
    $pages = [
        'privacy_policy' => 'Privacy Policy',
        'terms_and_conditions' => 'Terms & Conditions',
        'refund_policy' => 'Refund Policy',
        'cancellation_policy' => 'Cancellation Policy',
    ];
    echo '<nav class="nz-legal-related" aria-label="Related policies"><p class="nz-legal-toc-title">Related policies</p><ul>';
    foreach ($pages as $slug => $label) {
        if ($slug !== $current) {
            echo '<li><a href="' . legal_e($slug) . '">' . legal_e($label) . '</a></li>';
        }
    }
    echo '</ul></nav>';
}
