<?php
require_once __DIR__ . '/includes/site_config.php';

$pageBreadcrumbs = [
  ['name' => 'Home', 'url' => site_url('')],
  ['name' => 'Services', 'url' => site_url('service')],
  ['name' => 'Digital Marketing & SEO', 'url' => site_url('digital_marketing_details')],
];
$pageServiceSchema = [
  'name' => 'Digital Marketing & SEO',
  'serviceType' => 'Digital Marketing and Search Engine Optimization',
  'url' => site_url('digital_marketing_details'),
  'description' => 'Local SEO, Google Ads, Meta campaigns, and search visibility services in Kolkata by Nazora Tech, starting at ₹4,999/month.',
  'offers' => [
    '@type' => 'Offer',
    'priceCurrency' => 'INR',
    'price' => '4999',
    'url' => site_url('pricing'),
  ],
];

include 'header.php';
?>

  <div class="optech-breadcrumb nz-breadcrumb">
    <div class="container">
      <h1 class="post__title">Digital Marketing &amp; SEO in Kolkata</h1>
      <nav class="breadcrumbs" aria-label="Breadcrumb">
        <ul>
          <li><a href="./">Home</a></li>
          <li><a href="service">Service</a></li>
          <li aria-current="page">Digital Marketing &amp; SEO</li>
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
            <img src="assets/images/service/service-deatils.webp" width="1200" height="799" fetchpriority="high" alt="Digital marketing and SEO services in Kolkata by Nazora Tech">

            <div class="optech-service-details-item">
              <h2>Local SEO, Paid Campaigns &amp; AI Lead Automation in Kolkata</h2>
              <p>Having a website is only half the job — local customers also need to find your services and reach you easily. <?php echo htmlspecialchars(site_company_name(), ENT_QUOTES, 'UTF-8'); ?> helps businesses in Kestopur, Salt Lake, New Town, Kolkata, and West Bengal turn search visibility and ad clicks into qualified enquiries.</p>
              <p>We pair clean technical SEO and landing page fixes with Google Ads, Meta campaigns, and AI automation for faster lead handling. Compare <a href="pricing">marketing plans starting at ₹4,999/month</a> or see how our <a href="service_details">website builds</a> prepare pages for search.</p>
            </div>

            <div class="optech-service-details-item">
              <h2>Business Problems We Solve</h2>
              <p>Before increasing ad spend, we fix the structural issues that stop local search traffic from converting.</p>
              <div class="nz-svc-cards">
                <article class="nz-svc-mini">
                  <h3>Missing Local Search Presence</h3>
                  <p>Weak titles, unindexed pages, or incomplete Google Business Profile setups keep Kolkata buyers from seeing your business.</p>
                </article>
                <article class="nz-svc-mini">
                  <h3>Ad Clicks Not Converting</h3>
                  <p>Sending paid clicks to a slow homepage with no clear quote button wastes monthly ad budget.</p>
                </article>
                <article class="nz-svc-mini">
                  <h3>Slow Lead Follow-Up</h3>
                  <p>Delayed replies lose warm enquiries; structured WhatsApp flows and AI lead routing keep responses prompt.</p>
                </article>
                <article class="nz-svc-mini">
                  <h3>No Clarity on Performance</h3>
                  <p>We set up Search Console, analytics, and clear monthly enquiry summaries instead of vanity metrics.</p>
                </article>
              </div>
            </div>

            <div class="optech-service-details-item">
              <h2>Digital Marketing Services Offered</h2>
              <p>Focused monthly plans built around steady visibility and measurable enquiry growth.</p>
              <div class="optech-icon-list">
                <ul>
                  <li><i class="ri-check-line"></i><strong>Starter SEO &amp; Presence (from ₹4,999/month):</strong> On-page SEO fixes, social posts, helpful service articles, and monthly reporting.</li>
                  <li><i class="ri-check-line"></i><strong>Growth SEO &amp; Paid Ads (from ₹8,999/month):</strong> Advanced technical SEO, Google Ads search campaigns, Meta Ads management, and key page copy.</li>
                  <li><i class="ri-check-line"></i><strong>Full Growth Program (from ₹14,999/month):</strong> Coordinated search, social, short-form content, and landing page conversion improvements.</li>
                  <li><i class="ri-check-line"></i><strong>Local SEO Preparation:</strong> Kolkata service-area page structure, Schema.org markup, and Google Business Profile alignment.</li>
                  <li><i class="ri-check-line"></i><strong>AI Automation for Enquiries:</strong> Faster enquiry capture, WhatsApp handoffs, and structured lead routing.</li>
                  <li><i class="ri-check-line"></i><strong>Search Console &amp; Tracking Setup:</strong> Clean sitemaps, canonical hygiene, and conversion path verification.</li>
                </ul>
              </div>
            </div>

            <div class="optech-service-details-item">
              <h2>Tools &amp; Platforms Used</h2>
              <p>Practical search, analytics, and campaign tools configured cleanly around your website.</p>
              <div class="nz-tech-pills">
                <span>Technical &amp; On-Page SEO</span>
                <span>Schema.org JSON-LD</span>
                <span>Google Search Console</span>
                <span>Google Analytics</span>
                <span>Google Ads</span>
                <span>Meta Ads Manager</span>
                <span>WhatsApp &amp; AI Lead Flows</span>
              </div>
            </div>

            <div class="optech-service-details-item">
              <h2>How We Run Campaigns &amp; SEO</h2>
              <div class="nz-svc-steps">
                <article class="nz-about-step">
                  <span>01</span>
                  <h3>Site &amp; Local Audit</h3>
                  <p>We review your website speed, titles, indexation, and enquiry paths first.</p>
                </article>
                <article class="nz-about-step">
                  <span>02</span>
                  <h3>Kolkata Keyword Plan</h3>
                  <p>We map realistic local search terms and landing pages around what customers actually search.</p>
                </article>
                <article class="nz-about-step">
                  <span>03</span>
                  <h3>On-Page &amp; Ads Launch</h3>
                  <p>We ship page improvements, structured data, and targeted search or social campaigns.</p>
                </article>
                <article class="nz-about-step">
                  <span>04</span>
                  <h3>Monthly Review</h3>
                  <p>We share plain-language progress on visibility, clicks, and enquiries and refine the next month.</p>
                </article>
              </div>
            </div>

            <div class="optech-service-details-item">
              <h2>Conversion-Ready Build Example</h2>
              <div class="nz-svc-case">
                <div>
                  <span class="nz-svc-case-tag">Live Client Website</span>
                  <h3>RC Logistics</h3>
                  <p>Structured with an SEO-ready page hierarchy, fast mobile layout, and visible call, WhatsApp, and quote paths designed to turn search visits into bookings.</p>
                </div>
                <div class="nz-svc-case-actions">
                  <a class="nz-header-btn" href="portfolio_details?slug=rc-logistics">View Case Study</a>
                  <a class="nz-addon-link" href="portfolio">All Portfolio Work</a>
                </div>
              </div>
            </div>

            <div class="optech-service-details-item">
              <h2>Digital Marketing &amp; SEO FAQs</h2>
              <div class="faq-accordion init-wrap nz-svc-faq">
                <div class="faq-item open">
                  <h3 class="init-header">How much do SEO and digital marketing plans start at?</h3>
                  <div class="init-body">
                    <p>Starter Marketing begins at ₹4,999/month for basic SEO improvements, social posts, an article, and monthly reporting. Paid ads management starts from ₹8,999/month on our <a href="pricing">pricing page</a>.</p>
                  </div>
                </div>
                <div class="faq-item">
                  <h3 class="init-header">Do you guarantee #1 Google rankings?</h3>
                  <div class="init-body">
                    <p>No honest SEO team guarantees #1 rankings because Google controls search results. We focus on technical hygiene, clean local content, Google Business Profile best practices, and measurable enquiry improvements.</p>
                  </div>
                </div>
                <div class="faq-item">
                  <h3 class="init-header">Can you fix my existing website before running ads?</h3>
                  <div class="init-body">
                    <p>Yes. Improving mobile speed, headings, and quote forms through our <a href="service_details">website service</a> helps ensure your ad budget is not wasted on poor landing pages.</p>
                  </div>
                </div>
                <div class="faq-item">
                  <h3 class="init-header">Is ad spend included in the ₹8,999/month Growth plan?</h3>
                  <div class="init-body">
                    <p>Monthly plan fees cover campaign setup, optimization, and management. Your Google or Meta ad budget is paid directly to the ad platform based on what you choose to spend.</p>
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
              <h2>Ready to grow enquiries?</h2>
              <p>Share your website link and Kolkata target area for a practical SEO and campaign plan.</p>
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
          <h2>Turn local searches into real enquiries</h2>
          <p>Review our <a href="pricing">marketing plans from ₹4,999/month</a> or contact our Kestopur, Kolkata team for a site audit.</p>
          <div class="optech-extra-mt d-flex flex-wrap justify-content-center gap-3">
            <a class="optech-default-btn optech-white-btn" data-text="Request Marketing Plan" href="contact"><span class="btn-wraper">Request Marketing Plan</span></a>
            <a class="optech-default-btn nz-btn-red" data-text="Chat on WhatsApp" href="<?php echo site_whatsapp_url(); ?>" target="_blank" rel="noopener"><span class="btn-wraper">Chat on WhatsApp</span></a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- End section -->

<?php include 'footer.php'; ?>
