<?php
require_once __DIR__ . '/includes/site_config.php';
require_once __DIR__ . '/includes/legal_page.php';
$pageBreadcrumbs = [
  ['name' => 'Home', 'url' => site_url('')],
  ['name' => 'Cancellation Policy', 'url' => site_url('cancellation_policy')],
];
$legalSections = [
  'how-to-cancel' => 'How to Request a Cancellation',
  'before-work' => 'Cancellation Before Work Starts',
  'during-development' => 'Cancellation During Development',
  'completed-milestones' => 'Completed Milestones',
  'third-party' => 'Third-Party Expenses',
  'ongoing-services' => 'Ongoing and Recurring Services',
  'handover' => 'Handover of Work and Materials',
  'refunds' => 'Relationship with the Refund Policy',
  'contact-us' => 'Contact Us',
];
include 'header.php';
?>

<div class="optech-breadcrumb nz-breadcrumb">
  <div class="container">
    <h1 class="post__title">Cancellation Policy</h1>
    <nav class="breadcrumbs" aria-label="Breadcrumb">
      <ul>
        <li><a href="./">Home</a></li>
        <li aria-current="page">Cancellation Policy</li>
      </ul>
    </nav>
  </div>
</div>

<div class="section optech-section-padding">
  <div class="container">
    <article class="optech-default-content nz-legal">
      <p class="nz-legal-updated">Last updated: <?php echo legal_e(LEGAL_LAST_UPDATED); ?></p>
      <p class="nz-legal-lead">This Cancellation Policy explains how to cancel a project or service with <?php echo legal_e(site_company_name()); ?> and what happens next. It works together with the written quotation, proposal or agreement for your project.</p>

      <?php legal_toc($legalSections); ?>

      <?php legal_heading('how-to-cancel', 'How to Request a Cancellation'); ?>
      <p>Send your cancellation request in writing by email to <?php echo legal_email_link(); ?> or through our <a href="contact">contact page</a>. Please include your name, the project, quotation or invoice reference, and the date you want the cancellation to take effect. We will confirm that we have received your request and explain the next steps.</p>
      <?php legal_owner_note('Decide whether a notice period applies to cancellations and, if so, how long.'); ?>

      <?php legal_heading('before-work', 'Cancellation Before Work Starts'); ?>
      <p>If you cancel before work on your project has started, any payment already made is handled under our <a href="refund_policy">Refund Policy</a> and your project agreement.</p>

      <?php legal_heading('during-development', 'Cancellation During Development'); ?>
      <p>If you cancel while work is in progress, we will stop the cancelled work once your request is confirmed and let you know the status of the work completed up to that point. Payment for work already carried out is handled as set out in your project agreement.</p>
      <?php legal_owner_note('Decide whether cancellation charges apply during development and how work in progress is valued (for example, by milestone or by time spent).'); ?>

      <?php legal_heading('completed-milestones', 'Completed Milestones'); ?>
      <p>Milestones that have already been delivered or approved are treated as set out in your project agreement.</p>

      <?php legal_heading('third-party', 'Third-Party Expenses'); ?>
      <p>Costs already committed to other providers for your project, such as domain names, hosting, licences, paid plugins or advertising spend, are subject to those providers' own cancellation and refund terms.</p>

      <?php legal_heading('ongoing-services', 'Ongoing and Recurring Services'); ?>
      <p>For ongoing services such as maintenance, support or digital marketing, cancellation follows the terms of the plan or agreement for that service.</p>
      <?php legal_owner_note('Set cancellation terms for recurring plans: notice required, and whether the current billing period is charged in full.'); ?>

      <?php legal_heading('handover', 'Handover of Work and Materials'); ?>
      <p>Materials you supplied to us remain yours and can be returned on request. Handover of completed or partly completed deliverables, files, source code and account access after a cancellation is arranged as set out in your project agreement.</p>
      <?php legal_owner_note('Decide what is handed over on cancellation and whether handover depends on settling payment for completed work.'); ?>

      <?php legal_heading('refunds', 'Relationship with the Refund Policy'); ?>
      <p>A cancellation does not by itself mean a refund is due. Any refund after a cancellation is assessed under our <a href="refund_policy">Refund Policy</a> and your project agreement.</p>

      <?php legal_heading('contact-us', 'Contact Us'); ?>
      <?php legal_contact_block(); ?>

      <?php legal_related('cancellation_policy'); ?>
    </article>
  </div>
</div>

<?php include 'footer.php'; ?>
