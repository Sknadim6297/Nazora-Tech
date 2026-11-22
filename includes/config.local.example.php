<?php
/**
 * Example server configuration template for Hostinger (DO NOT store real secrets in git).
 *
 * To activate on Hostinger:
 *   1. Preferred: save a copy one folder ABOVE public_html as `nazora-config.php`
 *      (for example: /home/<user>/domains/nazora.tech/nazora-config.php).
 *      Fallback: save as `storage/config.local.php` inside the project (blocked by .htaccess and .gitignore).
 *   2. Wait until info@nazora.tech is created, verified, and tested in Hostinger Email before switching
 *      SMTP_FROM_EMAIL / CONTACT_NOTIFY_EMAIL to info@nazora.tech.
 *   3. Fill in the real mailbox password only on the server, never in the repository.
 */

return [
   'SMTP_HOST' => 'smtp.hostinger.com',
    'SMTP_PORT' => '465',
    'SMTP_ENCRYPTION' => 'ssl',
    'SMTP_USERNAME' => 'info@nazora.tech',
    'SMTP_PASSWORD' => 'REPLACE_WITH_MAILBOX_PASSWORD', // mailbox password, typed only on the server
    'SMTP_FROM_EMAIL' => 'info@nazora.tech',
    'SMTP_FROM_NAME' => 'Nazora Tech',
    'CONTACT_NOTIFY_EMAIL' => 'skonlineitsolution@gmail.com',       // blank = uses skonlineitsolution@gmail.com until info@ works
    'CONTACT_AUTOREPLY' => '1',
    // Backups (defaults to ../nazora-backups outside public_html on Hostinger, or storage/backups)
    'BACKUP_DIR' => '',
    'BACKUP_KEEP_COUNT' => '14', // set to '0' to disable pruning

    // Optional overrides
    'GA4_MEASUREMENT_ID' => '',  // e.g. G-XXXXXXXXXX once created
];
