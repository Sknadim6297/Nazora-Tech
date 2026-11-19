<?php
require_once __DIR__ . '/includes/site_config.php';
require_once __DIR__ . '/includes/legal_page.php';
$pageBreadcrumbs = [
  ['name' => 'Home', 'url' => site_url('')],
  ['name' => 'Privacy Policy', 'url' => site_url('privacy_policy')],
];
$legalSections = [
  'who-we-are' => 'Who We Are',
  'information' => 'Information We Collect',
  'use' => 'How We Use Information',
  'emails' => 'Emails We Send',
  'storage' => 'Hosting, Storage and Service Providers',
  'security' => 'Security',
  'analytics' => 'Cookies and Analytics',
  'sharing' => 'When We Share Information',
  'retention' => 'How Long We Keep Information',
  'requests' => 'Your Choices and Privacy Requests',
  'contact-us' => 'Contact Us',
  'updates' => 'Changes to This Policy',
];
include 'header.php';
?>

<div class="optech-breadcrumb nz-breadcrumb">
  <div class="container">
    <h1 class="post__title">Privacy Policy</h1>
    <nav class="breadcrumbs" aria-label="Breadcrumb">
      <ul>
        <li><a href="./">Home</a></li>
        <li aria-current="page">Privacy Policy</li>
      </ul>
    </nav>
  </div>
</div>

<div class="section optech-section-padding">
  <div class="container">
    <article class="optech-default-content nz-legal">
      <p class="nz-legal-updated">Last updated: <?php echo legal_e(LEGAL_LAST_UPDATED); ?></p>
      <p class="nz-legal-lead">This Privacy Policy explains what information <?php echo legal_e(site_company_name()); ?> collects through this website, how it is used and stored, and the choices you have.</p>

      <?php legal_toc($legalSections); ?>

      <?php legal_heading('who-we-are', 'Who We Are'); ?>
      <p><?php echo legal_e(site_company_name()); ?> is a web, app and software development business operated by SK NADIM from <?php echo legal_e(SITE_LOCATION); ?>. In this policy, "we", "us" and "our" refer to <?php echo legal_e(site_company_name()); ?>.</p>

      <?php legal_heading('information', 'Information We Collect'); ?>
      <h3>Information you give us</h3>
      <p>When you use a contact form on this website, we receive the details you enter: your <strong>name, email address, phone number, the service you select and your message</strong>. We also record which form you used and when it was sent. We do not store your IP address or browser details with your enquiry.</p>
      <p>If you contact us directly by email, phone or WhatsApp, we receive the information you choose to share in that conversation.</p>
      <h3>Information collected automatically</h3>
      <p>Like most websites, the servers that host this website may automatically record standard technical information when a page is requested, such as the IP address, browser type and the pages visited. This is used to operate and secure the website.</p>
      <p>To protect the contact form from automated spam, the website keeps a short-lived, one-way coded (hashed) record of the connection address used to send a message. It is used only to limit repeated submissions and is not linked to your enquiry.</p>

      <?php legal_heading('use', 'How We Use Information'); ?>
      <ul>
        <li>To read and respond to your enquiry and to discuss the service you asked about.</li>
        <li>To prepare proposals or quotations you request and to communicate about a project.</li>
        <li>To keep a record of enquiries so our team can follow up.</li>
        <li>To keep the website secure and to prevent spam and misuse.</li>
      </ul>
      <p>We do not sell your personal information.</p>

      <?php legal_heading('emails', 'Emails We Send'); ?>
      <p>When you submit the contact form, a notification containing your enquiry is emailed to our business inbox so that our team can respond.</p>
      <p>We may also send a one-time confirmation email to the address you entered. It confirms that we received your enquiry and includes the service you selected and a reference number. It does not repeat your message. Any further emails are replies about your enquiry or project.</p>
      <?php legal_owner_note('Decide whether automatic customer confirmation emails (CONTACT_AUTOREPLY) will be switched on. They are currently off.'); ?>

      <?php legal_heading('storage', 'Hosting, Storage and Service Providers'); ?>
      <p>This website and our business email are hosted by <strong>Hostinger</strong>. Enquiries are stored in the website's database on the hosting server and can only be viewed through a password-protected admin area. Private backup copies of the website data, which include enquiries, are kept so that information is not lost.</p>
      <p>Our service providers process information only as needed to provide their services to us, such as hosting the website and delivering email.</p>
      <p>If you choose to contact us on WhatsApp, your messages are handled by WhatsApp under its own terms and privacy policy.</p>
      <?php legal_owner_note('Confirm the inbox that receives enquiry notifications. If it is a Gmail or other non-Hostinger inbox, that provider must be named here. Add the server region only if you have confirmed it with Hostinger.'); ?>

      <?php legal_heading('security', 'Security'); ?>
      <p>We use reasonable measures to protect the information we hold. Connections to this website are encrypted with HTTPS, enquiry emails are sent over an encrypted connection, admin passwords are stored in hashed form and repeated failed sign-in attempts are limited. No website or email system can be guaranteed to be completely secure, so please do not send passwords, payment card details or other highly sensitive information through the contact form.</p>

      <?php legal_heading('analytics', 'Cookies and Analytics'); ?>
      <p>Browsing the public pages of this website does not require cookies. Your cookie choice is saved in your browser's local storage so that the banner does not appear on every page.</p>
      <p>If website analytics is switched on, we use Google Analytics 4 only after you choose <strong>Accept</strong> in the cookie banner. It sets cookies that help us count page visits and anonymous actions such as tapping a WhatsApp, call or email button, or successfully sending the contact form. We do not send your name, phone number, email address or message to Google Analytics, and advertising features are turned off. If you choose <strong>Decline</strong>, analytics is not loaded. You can change your choice at any time using <strong>Cookie Settings</strong> in the website footer.</p>
      <p>The admin area uses a session cookie for signed-in staff only.</p>

      <?php legal_heading('sharing', 'When We Share Information'); ?>
      <p>We share personal information only with the service providers described above, when you ask us to, or when we are required to do so by law.</p>

      <?php legal_heading('retention', 'How Long We Keep Information'); ?>
      <p>We keep enquiry information only for as long as it is needed for the purposes described in this policy, including follow-up, any resulting project and related business records. Older backup copies are replaced over time as newer backups are made.</p>
      <?php legal_owner_note('Choose a retention period for enquiries (the admin panel can delete closed enquiries older than 6, 12 or 24 months) and confirm how many backups are kept (default: newest 14). State the period here once decided.'); ?>

      <?php legal_heading('requests', 'Your Choices and Privacy Requests'); ?>
      <p>You can ask us to tell you what information we hold about you from this website, to correct it or to delete it. You can also withdraw analytics consent at any time using <strong>Cookie Settings</strong>. To make a request, contact us using the details below. We may need to confirm your identity before acting on a request, and we will respond in line with applicable law.</p>
      <?php legal_owner_note('Confirm who handles privacy requests and whether a named grievance contact is required for your business under applicable Indian law. Take legal advice if unsure.'); ?>

      <?php legal_heading('contact-us', 'Contact Us'); ?>
      <p>For questions about this policy or your information, contact:</p>
      <?php legal_contact_block(); ?>

      <?php legal_heading('updates', 'Changes to This Policy'); ?>
      <p>We may update this policy when our website, services or information practices change. The "Last updated" date at the top of this page shows when it was last revised.</p>

      <?php legal_related('privacy_policy'); ?>
    </article>
  </div>
</div>

<?php include 'footer.php'; ?>
