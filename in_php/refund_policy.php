<?php
require_once __DIR__ . '/includes/site_config.php';
require_once __DIR__ . '/includes/legal_page.php';
$pageBreadcrumbs = [
  ['name' => 'Home', 'url' => site_url('')],
  ['name' => 'Refund Policy', 'url' => site_url('refund_policy')],
];
$legalSections = [
  'scope' => 'Scope of This Policy',
  'custom-development' => 'Custom Development Work',
  'deposits' => 'Advance Payments and Deposits',
  'milestones' => 'Milestone Payments and Completed Work',
  'third-party-costs' => 'Third-Party Costs',
  'how-to-request' => 'How to Request a Refund',
  'review' => 'How Requests Are Reviewed',
  'method-timeline' => 'Refund Method and Timeline',
  'cancellations' => 'Cancellations',
  'contact-us' => 'Contact Us',
];
include 'header.php';
?>

<div class="optech-breadcrumb nz-breadcrumb">
  <div class="container">
    <h1 class="post__title">Refund Policy</h1>
    <nav class="breadcrumbs" aria-label="Breadcrumb">
      <ul>
        <li><a href="./">Home</a></li>
        <li aria-current="page">Refund Policy</li>
      </ul>
    </nav>
  </div>
</div>

<div class="section optech-section-padding">
  <div class="container">
    <article class="optech-default-content nz-legal">
      <p class="nz-legal-updated">Last updated: <?php echo legal_e(LEGAL_LAST_UPDATED); ?></p>
      <p class="nz-legal-lead">This Refund Policy explains how refund requests are handled for services from <?php echo legal_e(site_company_name()); ?>. It works together with the written quotation, proposal or agreement for your project.</p>

      <?php legal_toc($legalSections); ?>

      <?php legal_heading('scope', 'Scope of This Policy'); ?>
      <p>This policy applies to payments made to <?php echo legal_e(site_company_name()); ?>, operated by SK NADIM, for website development, app development, software solutions, digital marketing, AI automation, and maintenance and support services. If your project agreement includes specific refund terms, those terms apply to your project.</p>

      <?php legal_heading('custom-development', 'Custom Development Work'); ?>
      <p>Our projects are custom work: time, planning and resources are committed to each project as it progresses. For this reason, whether a refund is available, and how much, depends on your project agreement and on the work completed when the request is made.</p>

      <?php legal_heading('deposits', 'Advance Payments and Deposits'); ?>
      <p>Whether an advance payment or deposit can be refunded, in full or in part, is stated in your quotation or project agreement.</p>
      <?php legal_owner_note('Decide whether advance payments or deposits are refundable, partly refundable or non-refundable, and under what conditions. This page does not assume either way.'); ?>

      <?php legal_heading('milestones', 'Milestone Payments and Completed Work'); ?>
      <p>Where a project is paid in milestones, refund requests are assessed against the milestones and work delivered under your project agreement.</p>
      <?php legal_owner_note('Decide how payments for delivered or approved milestones, and for work in progress, are treated when a refund is requested.'); ?>

      <?php legal_heading('third-party-costs', 'Third-Party Costs'); ?>
      <p>Some projects include costs paid to other providers, such as domain names, hosting, software licences, paid plugins, app store fees or advertising spend. Refunds of these costs depend on that provider's own refund terms.</p>
      <?php legal_owner_note('Confirm how third-party costs bought on a client\'s behalf are handled in a refund (for example, passed back only if the provider refunds them).'); ?>

      <?php legal_heading('how-to-request', 'How to Request a Refund'); ?>
      <p>Send your request by email to <?php echo legal_email_link(); ?> or through our <a href="contact">contact page</a>, and include:</p>
      <ul>
        <li>Your name and contact details</li>
        <li>The project, quotation or invoice reference</li>
        <li>The payment date and amount</li>
        <li>The reason for your request</li>
      </ul>

      <?php legal_heading('review', 'How Requests Are Reviewed'); ?>
      <p>We review each request against your project agreement and the work completed, and we may ask for more information. We will tell you the outcome in writing.</p>

      <?php legal_heading('method-timeline', 'Refund Method and Timeline'); ?>
      <p>Approved refunds are paid using a method we confirm with you.</p>
      <?php legal_owner_note('Confirm the refund method (for example, to the original payment method or by bank transfer) and the processing time before stating one here.'); ?>

      <?php legal_heading('cancellations', 'Cancellations'); ?>
      <p>If you want to cancel a project or service, please also read our <a href="cancellation_policy">Cancellation Policy</a>. Any refund that follows a cancellation is handled under this policy and your project agreement.</p>

      <?php legal_heading('contact-us', 'Contact Us'); ?>
      <?php legal_contact_block(); ?>

      <?php legal_related('refund_policy'); ?>
    </article>
  </div>
</div>

<?php include 'footer.php'; ?>
