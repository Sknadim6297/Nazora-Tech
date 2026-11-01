<?php

if (!defined('SITE_COMPANY_NAME')) {
    define('SITE_COMPANY_NAME', 'Nazora Tech');
    define('SITE_COMPANY_BLOG', 'Nazora Tech Blog');
    define('SITE_COMPANY_TEAM', 'Nazora Tech Team');
    define('SITE_CONTACT_EMAIL', 'skonlineitsolution@gmail.com');
    define('SITE_WHATSAPP_MESSAGE', 'Hi Nazora Tech, I need a quote.');
}

function site_company_name(): string
{
    return SITE_COMPANY_NAME;
}

function site_contact_email(): string
{
    return SITE_CONTACT_EMAIL;
}

function site_whatsapp_url(string $phone = '916297616918'): string
{
    return 'https://wa.me/' . $phone . '?text=' . rawurlencode(SITE_WHATSAPP_MESSAGE);
}
