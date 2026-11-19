<?php
require_once __DIR__ . '/includes/site_config.php';

$pageBreadcrumbs = [
  ['name' => 'Home', 'url' => site_url('')],
  ['name' => 'Services', 'url' => site_url('service')],
  ['name' => 'Mobile App Development', 'url' => site_url('app_development_details')],
];
$pageServiceSchema = [
  'name' => 'Mobile App Development',
  'serviceType' => 'Mobile Application Development',
  'url' => site_url('app_development_details'),
  'description' => 'Android and iOS mobile app design and development in Kolkata by Nazora Tech, starting from ₹7,999.',
  'offers' => [
    '@type' => 'Offer',
    'priceCurrency' => 'INR',
    'price' => '7999',
    'url' => site_url('pricing'),
  ],
];

include 'header.php';
?>

  <div class="optech-breadcrumb nz-breadcrumb">
    <div class="container">
      <h1 class="post__title">Mobile App Development in Kolkata</h1>
      <nav class="breadcrumbs" aria-label="Breadcrumb">
        <ul>
          <li><a href="./">Home</a></li>
          <li><a href="service">Service</a></li>
          <li aria-current="page">Mobile App Development</li>
        </ul>
      </nav>
    </div>
  </div>
  <!-- End breadcrumb -->

  <div class="section optech-section-padding">
    <div class="container">
      <div class="row">
        <div class="col-lg-8">
          <div class="optech-service-details-wrap">
            <img src="assets/images/service/service-deatils.webp" width="1200" height="799" fetchpriority="high" alt="Mobile app development services in Kolkata by Nazora Tech">

            <div class="optech-service-details-item">
              <h2>Mobile App Development Company in Kolkata</h2>
              <p>Turn your booking, customer service, or field workflow into a focused mobile app with <?php echo htmlspecialchars(site_company_name(), ENT_QUOTES, 'UTF-8'); ?>. From our studio in Kestopur, Kolkata, we work with businesses across Salt Lake, New Town, West Bengal, and India to build practical Android and iOS apps without unnecessary complexity.</p>
              <p>You can start lean with an Android starter release and expand into accounts, push alerts, admin panels, and payment gateways as usage grows. Review our <a href="pricing">app plans starting from ₹7,999</a> or browse our <a href="portfolio">portfolio</a>.</p>
            </div>

            <div class="optech-service-details-item">
              <h2>Business Problems We Solve</h2>
              <p>Mobile apps make sense when customers or staff repeat the same steps every week on their phones.</p>
              <div class="nz-svc-cards">
                <article class="nz-svc-mini">
                  <h3>Phone Queue Bottlenecks</h3>
                  <p>Let customers view services, check hours, and submit appointment or booking requests directly from the app.</p>
                </article>
                <article class="nz-svc-mini">
                  <h3>Clunky Mobile Browser Forms</h3>
                  <p>Give repeat users saved profiles, quick re-orders, and push notifications instead of re-entering details.</p>
                </article>
                <article class="nz-svc-mini">
                  <h3>No Central Staff Control</h3>
                  <p>Pair the mobile app with a clean web admin panel so your team can review requests and update availability.</p>
                </article>
                <article class="nz-svc-mini">
                  <h3>Overbuilt First Versions</h3>
                  <p>Scope the essential screens first so you launch faster and test real demand before funding larger modules.</p>
                </article>
              </div>
            </div>

            <div class="optech-service-details-item">
              <h2>Mobile App Services Offered</h2>
              <p>We scope features clearly across three practical starting tiers on our <a href="pricing">pricing page</a>.</p>
              <div class="optech-icon-list">
                <ul>
                  <li><i class="ri-check-line"></i><strong>Android Starter Apps (from ₹7,999):</strong> Focused up-to-5-screen apps for service showcases, call, WhatsApp, and enquiry capture.</li>
                  <li><i class="ri-check-line"></i><strong>Android &amp; iOS Cross-Platform Apps (from ₹14,999):</strong> Single-codebase builds with accounts, push notifications, and a basic admin panel.</li>
                  <li><i class="ri-check-line"></i><strong>Custom &amp; Payment-Enabled Apps (from ₹24,999):</strong> Custom user flows, payment gateways, third-party APIs, and reporting.</li>
                  <li><i class="ri-check-line"></i><strong>UI/UX Screen Prototyping:</strong> Clear screen flows reviewed before heavy engineering begins.</li>
                  <li><i class="ri-check-line"></i><strong>Backend API &amp; Admin Sync:</strong> Connect your mobile app cleanly to web dashboards and databases.</li>
                  <li><i class="ri-check-line"></i><strong>Store Submission &amp; After-Launch Care:</strong> Play Store / App Store checklist guidance and post-release support.</li>
                </ul>
              </div>
            </div>

            <div class="optech-service-details-item">
              <h2>Technology Stack</h2>
              <p>We match the mobile and backend stack to your screen count, performance needs, and maintenance budget.</p>
              <div class="nz-tech-pills">
                <span>Flutter</span>
                <span>React Native</span>
                <span>Android &amp; iOS</span>
                <span>REST APIs</span>
                <span>PHP Backend</span>
                <span>SQLite &amp; MySQL</span>
                <span>Push Notifications</span>
              </div>
            </div>

            <div class="optech-service-details-item">
              <h2>Our App Development Process</h2>
              <div class="nz-svc-steps">
                <article class="nz-about-step">
                  <span>01</span>
                  <h3>Screen &amp; Goal Mapping</h3>
                  <p>We list the exact user roles, screens, and actions needed for version one.</p>
                </article>
                <article class="nz-about-step">
                  <span>02</span>
                  <h3>Scope &amp; Fixed Quote</h3>
                  <p>You approve the feature list, backend admin scope, timeline, and transparent estimate.</p>
                </article>
                <article class="nz-about-step">
                  <span>03</span>
                  <h3>App &amp; API Build</h3>
                  <p>We build the mobile screens, APIs, and admin controls in testable milestones.</p>
                </article>
                <article class="nz-about-step">
                  <span>04</span>
                  <h3>QA, Release &amp; Support</h3>
                  <p>We test on real devices, assist with release setup, and support post-launch updates.</p>
                </article>
              </div>
            </div>

            <div class="optech-service-details-item">
              <h2>Sample App Architecture Case</h2>
              <div class="nz-svc-case">
                <div>
                  <span class="nz-svc-case-tag">Sample App Concept</span>
                  <h3>Clinic Connect</h3>
                  <p>A sample clinic appointment concept showing service listings, doctor hours, booking requests, and front-desk admin notes to cut down phone queues.</p>
                </div>
                <div class="nz-svc-case-actions">
                  <a class="nz-header-btn" href="portfolio_details?slug=clinic-connect">View Sample Case</a>
                  <a class="nz-addon-link" href="portfolio">All Portfolio Work</a>
                </div>
              </div>
            </div>

            <div class="optech-service-details-item">
              <h2>Mobile App Development FAQs</h2>
              <div class="faq-accordion init-wrap nz-svc-faq">
                <div class="faq-item open">
                  <h3 class="init-header">What does the ₹7,999 App Starter plan cover?</h3>
                  <div class="init-body">
                    <p>App Starter is a focused Android information and enquiry app (up to 5 screens) with call, WhatsApp, and enquiry flow plus 1 month of support. For both Android and iOS with accounts and notifications, see App Standard from ₹14,999 on our <a href="pricing">pricing page</a>.</p>
                  </div>
                </div>
                <div class="faq-item">
                  <h3 class="init-header">Do you build for both Android and iOS?</h3>
                  <div class="init-body">
                    <p>Yes. Using Flutter or React Native, we can target both Android and iOS efficiently from one shared codebase once your scope covers both platforms.</p>
                  </div>
                </div>
                <div class="faq-item">
                  <h3 class="init-header">Can the app connect to a website or billing software?</h3>
                  <div class="init-body">
                    <p>Yes. We design clean REST APIs so your mobile app, web admin panel, and <a href="software_development_details">custom software</a> share one database.</p>
                  </div>
                </div>
                <div class="faq-item">
                  <h3 class="init-header">How do you price custom app features?</h3>
                  <div class="init-body">
                    <p>After a short discovery call or brief on our <a href="contact">contact page</a>, we list the exact screens, integrations, and support period and send a fixed quote.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="optech-service-sidebar">
            <div class="optech-service-menu">
              <ul>
                <li><a href="service_details">Website Design &amp; Development <i class="ri-arrow-right-up-line"></i></a></li>
                <li><a href="app_development_details">Application Design &amp; Development <i class="ri-arrow-right-up-line"></i></a></li>
                <li><a href="software_development_details">Software Design &amp; Development <i class="ri-arrow-right-up-line"></i></a></li>
                <li><a href="digital_marketing_details">Digital Marketing Services <i class="ri-arrow-right-up-line"></i></a></li>
                <li><a href="pricing">View Pricing Plans <i class="ri-arrow-right-up-line"></i></a></li>
                <li><a href="portfolio">Explore Portfolio <i class="ri-arrow-right-up-line"></i></a></li>
              </ul>
            </div>
            <div class="optech-service-contact" style="background-image: url(assets/images/service/bg.png)">
              <div class="optech-service-contact-icon">
                <img src="assets/images/service/icon.svg" alt="" width="48" height="48">
              </div>
              <h2>Planning a mobile app?</h2>
              <p>Tell us your core screens and goals and our Kolkata team will map a practical first release.</p>
              <a class="optech-default-btn" data-text="Get in Touch" href="contact"><span class="btn-wraper">Get in Touch</span></a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- End section -->

  <div class="section optech-section-padding bg-light1 home-cta-section">
    <div class="container">
      <div class="optech-cta-wrap home-cta-card">
        <div class="optech-cta-content center">
          <h2>Ready to scope your mobile app?</h2>
          <p>Explore our <a href="pricing">app pricing from ₹7,999</a> or message our Kolkata studio for a screen-by-screen estimate.</p>
          <div class="optech-extra-mt d-flex flex-wrap justify-content-center gap-3">
            <a class="optech-default-btn optech-white-btn" data-text="Request App Quote" href="contact"><span class="btn-wraper">Request App Quote</span></a>
            <a class="optech-default-btn nz-btn-red" data-text="Chat on WhatsApp" href="<?php echo site_whatsapp_url(); ?>" target="_blank" rel="noopener"><span class="btn-wraper">Chat on WhatsApp</span></a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- End section -->

<?php include 'footer.php'; ?>
