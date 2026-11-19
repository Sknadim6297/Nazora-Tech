<?php
require_once __DIR__ . '/includes/site_config.php';
require_once __DIR__ . '/includes/legal_page.php';
$pageBreadcrumbs = [
  ['name' => 'Home', 'url' => site_url('')],
  ['name' => 'Terms & Conditions', 'url' => site_url('terms_and_conditions')],
];
$legalSections = [
  'about' => 'About These Terms',
  'website-use' => 'Use of This Website',
  'services' => 'Our Services',
  'quotations' => 'Proposals and Quotations',
  'scope' => 'Project Scope and Changes',
  'client-responsibilities' => 'Client Responsibilities',
  'payments' => 'Payments and Milestones',
  'intellectual-property' => 'Intellectual Property',
  'third-party' => 'Third-Party Services and Integrations',
  'delivery' => 'Delivery and Acceptance',
  'support' => 'Support and Maintenance',
  'liability' => 'Limitation of Liability',
  'disputes' => 'Governing Law and Disputes',
  'changes' => 'Changes to These Terms',
  'contact-us' => 'Contact Us',
];
include 'header.php';
?>

<div class="optech-breadcrumb nz-breadcrumb">
  <div class="container">
    <h1 class="post__title">Terms &amp; Conditions</h1>
    <nav class="breadcrumbs" aria-label="Breadcrumb">
      <ul>
        <li><a href="./">Home</a></li>
        <li aria-current="page">Terms &amp; Conditions</li>
      </ul>
    </nav>
  </div>
</div>

<div class="section optech-section-padding">
  <div class="container">
    <article class="optech-default-content nz-legal">
      <p class="nz-legal-updated">Last updated: <?php echo legal_e(LEGAL_LAST_UPDATED); ?></p>
      <p class="nz-legal-lead">These Terms &amp; Conditions apply to your use of this website and to enquiries about services from <?php echo legal_e(site_company_name()); ?>. Please read them before using the website or engaging us for a project.</p>

      <?php legal_toc($legalSections); ?>

      <?php legal_heading('about', 'About These Terms'); ?>
      <p>This website is operated by <?php echo legal_e(site_company_name()); ?>, a business run by SK NADIM from <?php echo legal_e(SITE_LOCATION); ?>. By using this website you agree to these terms. For client projects, these terms apply together with the written quotation, proposal or agreement for that project. If the two differ, the project document applies to that project.</p>

      <?php legal_heading('website-use', 'Use of This Website'); ?>
      <ul>
        <li>Use the website lawfully and only for genuine enquiries and information.</li>
        <li>Do not misuse the contact form, send spam or harmful content, attempt unauthorised access or interfere with the website's operation.</li>
        <li>Website content, including service descriptions and portfolio examples, is general information. It may be updated or changed at any time and is not a binding offer.</li>
      </ul>

      <?php legal_heading('services', 'Our Services'); ?>
      <p>We offer website design and development, app development, software solutions, digital marketing, AI automation, and maintenance and support. The exact deliverables, schedule, price and support for a project are agreed in writing before work begins.</p>

      <?php legal_heading('quotations', 'Proposals and Quotations'); ?>
      <p>Proposals and quotations are prepared from the information you give us. Prices or features mentioned on the website, including "starting from" prices, are indicative and are confirmed only in a written quotation. A project starts once the quotation or proposal has been accepted in the way it specifies.</p>
      <?php legal_owner_note('State how long quotations remain valid and how a quotation is accepted (for example, written approval and/or advance payment).'); ?>

      <?php legal_heading('scope', 'Project Scope and Changes'); ?>
      <p>The work included in a project is the scope described in the agreed project document. Requests for additional features or changes outside that scope are discussed with you first and may change the price or timeline. Additional work starts only after any change is agreed.</p>

      <?php legal_heading('client-responsibilities', 'Client Responsibilities'); ?>
      <ul>
        <li>Provide the content, information, feedback, approvals and access the project needs in good time. Delays in these can affect the schedule.</li>
        <li>Make sure you have the right to use any text, images, logos, trademarks or other materials you give us for the project.</li>
        <li>Keep your own passwords and account access secure, and tell us promptly about changes that affect the project.</li>
        <li>Review deliverables when they are shared and report issues clearly.</li>
      </ul>

      <?php legal_heading('payments', 'Payments and Milestones'); ?>
      <p>Project fees, payment schedules, milestones, applicable taxes and accepted payment methods are set out in the quotation, proposal or invoice for your project. Refunds and cancellations are covered in our <a href="refund_policy">Refund Policy</a> and <a href="cancellation_policy">Cancellation Policy</a>.</p>
      <?php legal_owner_note('Decide your standard payment terms: advance or deposit amount, milestone structure, due dates, late payment handling and whether prices include GST.'); ?>

      <?php legal_heading('intellectual-property', 'Intellectual Property'); ?>
      <p>The content and design of this website belong to <?php echo legal_e(site_company_name()); ?> or its licensors and may not be copied without permission. Ownership and use rights for project deliverables are set out in the project agreement. Third-party components such as fonts, plugins, libraries, themes and stock media remain subject to their own licences.</p>
      <?php legal_owner_note('Decide when ownership of deliverables passes to the client (for example, on full payment) and whether you may show completed work in your portfolio.'); ?>

      <?php legal_heading('third-party', 'Third-Party Services and Integrations'); ?>
      <p>Projects often rely on third-party services such as domain registrars, hosting, email, payment gateways, APIs, app stores, analytics and advertising platforms. These services are provided under their own terms, and their availability, features and pricing are set by those providers and may change. Who pays for and owns these accounts is agreed for each project.</p>

      <?php legal_heading('delivery', 'Delivery and Acceptance'); ?>
      <p>Delivery dates and the review and acceptance process are set out in the project agreement. Timelines depend on the agreed scope and on receiving content, feedback and approvals when needed.</p>

      <?php legal_heading('support', 'Support and Maintenance'); ?>
      <p>Any support included after launch is described in the project agreement. Ongoing maintenance, updates, hosting management or marketing services can be arranged separately.</p>
      <?php legal_owner_note('Decide whether a post-launch bug-fix or support period is included with projects, and for how long.'); ?>

      <?php legal_heading('liability', 'Limitation of Liability'); ?>
      <p>Website content is provided for general information, and we do not guarantee that it is always complete or current. Responsibilities and liability for a client project are as set out in the project agreement, to the extent permitted by law. Nothing in these terms limits any liability that cannot be limited under applicable law.</p>
      <?php legal_owner_note('Decide whether to include a liability limit (for example, capped at fees paid). Take legal advice before adding one.'); ?>

      <?php legal_heading('disputes', 'Governing Law and Disputes'); ?>
      <p>If you have a concern, please contact us first so that we can try to resolve it through discussion.</p>
      <?php legal_owner_note('Add the governing law and the courts or dispute resolution process that apply. This must be confirmed by you, ideally with legal advice.'); ?>

      <?php legal_heading('changes', 'Changes to These Terms'); ?>
      <p>We may update these terms from time to time. The "Last updated" date shows the current version. Changes do not affect a project agreement that has already been accepted unless both parties agree.</p>

      <?php legal_heading('contact-us', 'Contact Us'); ?>
      <?php legal_contact_block(); ?>

      <?php legal_related('terms_and_conditions'); ?>
    </article>
  </div>
</div>

<?php include 'footer.php'; ?>
