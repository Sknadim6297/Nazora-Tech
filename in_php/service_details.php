<?php
require_once __DIR__ . '/includes/site_config.php';

$pageBreadcrumbs = [
  ['name' => 'Home', 'url' => site_url('')],
  ['name' => 'Services', 'url' => site_url('service')],
  ['name' => 'Website Design and Development', 'url' => site_url('service_details')],
];
$pageServiceSchema = [
  'name' => 'Website Design and Development',
  'serviceType' => 'Website Development',
  'url' => site_url('service_details'),
  'description' => 'Responsive, SEO-ready business websites, landing pages, and web applications built in Kolkata by Nazora Tech, starting from ₹1,999/month.',
  'offers' => [
    '@type' => 'Offer',
    'priceCurrency' => 'INR',
    'price' => '1999',
    'url' => site_url('pricing'),
  ],
];

include 'header.php';
?>

  <div class="optech-breadcrumb nz-breadcrumb">
    <div class="container">
      <h1 class="post__title">Website Design and Development in Kolkata</h1>
      <nav class="breadcrumbs" aria-label="Breadcrumb">
        <ul>
          <li><a href="./">Home</a></li>
          <li><a href="service">Service</a></li>
          <li aria-current="page">Website Design and Development</li>
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
            <img src="assets/images/service/service-deatils.webp" width="1200" height="799" fetchpriority="high" alt="Website design and development services in Kolkata by Nazora Tech">

            <div class="optech-service-details-item">
              <h2>Website Development Company in Kolkata &amp; Affordable Developer in Kestopur</h2>
              <p>Your website is often the first place a customer checks before calling or requesting a quote. At <?php echo htmlspecialchars(site_company_name(), ENT_QUOTES, 'UTF-8'); ?> in Kestopur, Kolkata, we act as a hands-on website development company for startups, local service firms, and growing businesses across Salt Lake, New Town, Kolkata, West Bengal, and India.</p>
              <p>Whether you need an affordable starter site from an experienced developer in Kestopur or a multi-page corporate site with admin controls, we build fast pages structured cleanly for search engines and real phone visitors. Compare <a href="pricing">website plans starting from ₹1,999/month</a> or inspect live client work in our <a href="portfolio">portfolio</a>.</p>
            </div>

            <div class="optech-service-details-item">
              <h2>Business Problems We Solve</h2>
              <p>Many local businesses come to us with a site that looks fine on a laptop but quietly loses enquiries on phones and search.</p>
              <div class="nz-svc-cards">
                <article class="nz-svc-mini">
                  <h3>Slow Mobile Pages</h3>
                  <p>Uncompressed images and bloated scripts push mobile visitors away before the quote form even loads.</p>
                </article>
                <article class="nz-svc-mini">
                  <h3>Hidden Call &amp; Quote Paths</h3>
                  <p>Visitors should not hunt for your phone number, WhatsApp link, or enquiry form when they are ready to talk.</p>
                </article>
                <article class="nz-svc-mini">
                  <h3>Weak Local Search Structure</h3>
                  <p>Generic template titles, missing metadata, and duplicate URLs make it harder for Kolkata customers to find you.</p>
                </article>
                <article class="nz-svc-mini">
                  <h3>Hard-to-Edit Content</h3>
                  <p>Waiting days to update services, pricing, or portfolio items slows down your marketing and sales team.</p>
                </article>
              </div>
            </div>

            <div class="optech-service-details-item">
              <h2>Website Services Offered</h2>
              <p>Every project starts with a written scope matched to your pages, enquiry flow, and budget.</p>
              <div class="optech-icon-list">
                <ul>
                  <li><i class="ri-check-line"></i><strong>Business &amp; Corporate Websites:</strong> Clear service pages, local trust details, and fast mobile layouts starting from ₹1,999/month.</li>
                  <li><i class="ri-check-line"></i><strong>High-Converting Landing Pages:</strong> Focused pages built for Google Ads, Meta Ads, and local lead campaigns.</li>
                  <li><i class="ri-check-line"></i><strong>Website Redesign &amp; Speed Fixes:</strong> Replace slow legacy templates with clean, modern structure without losing key URLs.</li>
                  <li><i class="ri-check-line"></i><strong>Admin &amp; Dynamic Sections:</strong> Manage portfolio case studies, career openings, or catalogs without touching code.</li>
                  <li><i class="ri-check-line"></i><strong>WhatsApp, Call &amp; Payment Flows:</strong> Direct quote capture, validation, and gateway integration where needed.</li>
                  <li><i class="ri-check-line"></i><strong>Technical SEO Readiness:</strong> Clean URLs, canonicals, XML sitemap, Schema.org markup, and WebP media optimization.</li>
                </ul>
              </div>
            </div>

            <div class="optech-service-details-item">
              <h2>Technology Stack</h2>
              <p>We build with dependable, maintainable web technologies suited for fast loading and straightforward future updates.</p>
              <div class="nz-tech-pills">
                <span>HTML5 &amp; Semantic Markup</span>
                <span>CSS3 &amp; Bootstrap</span>
                <span>JavaScript (ES6+)</span>
                <span>PHP 8</span>
                <span>SQLite &amp; MySQL</span>
                <span>Technical SEO &amp; JSON-LD</span>
                <span>WebP Image Optimization</span>
              </div>
            </div>

            <div class="optech-service-details-item">
              <h2>Our Development Process</h2>
              <div class="nz-svc-steps">
                <article class="nz-about-step">
                  <span>01</span>
                  <h3>Discover</h3>
                  <p>We review your services, target customers in Kolkata or pan-India, and the actions visitors should take.</p>
                </article>
                <article class="nz-about-step">
                  <span>02</span>
                  <h3>Scope &amp; Plan</h3>
                  <p>You confirm the page list, timeline, and starting plan or fixed quote before design begins.</p>
                </article>
                <article class="nz-about-step">
                  <span>03</span>
                  <h3>Build &amp; Mobile QA</h3>
                  <p>We code responsive layouts, connect forms and admin panels, and test across desktop and mobile screens.</p>
                </article>
                <article class="nz-about-step">
                  <span>04</span>
                  <h3>Launch &amp; Support</h3>
                  <p>We deploy with clean redirects, sitemap setup, and practical post-launch maintenance.</p>
                </article>
              </div>
            </div>

            <div class="optech-service-details-item">
              <h2>Live Project Example</h2>
              <div class="nz-svc-case">
                <div>
                  <span class="nz-svc-case-tag">Live Client Website</span>
                  <h3>RC Logistics</h3>
                  <p>Domestic transport website built with a mobile-first layout, SEO-ready page structure, and direct call, WhatsApp, and quote actions.</p>
                </div>
                <div class="nz-svc-case-actions">
                  <a class="nz-header-btn" href="portfolio_details?slug=rc-logistics">Read Case Study</a>
                  <a class="nz-addon-link" href="https://rclogistics.org.in/" target="_blank" rel="noopener">Live Demo</a>
                </div>
              </div>
            </div>

            <div class="optech-service-details-item">
              <h2>Website Development FAQs</h2>
              <div class="faq-accordion init-wrap nz-svc-faq">
                <div class="faq-item open">
                  <h3 class="init-header">What is included in the ₹1,999/month starting website plan?</h3>
                  <div class="init-body">
                    <p>Basic Web covers up to 5 pages, a mobile-friendly layout, contact form and WhatsApp integration, basic on-page SEO setup, and email support. See full tier details on our <a href="pricing">pricing page</a>.</p>
                  </div>
                </div>
                <div class="faq-item">
                  <h3 class="init-header">Can I meet or discuss my project with your Kestopur, Kolkata team?</h3>
                  <div class="init-body">
                    <p>Yes. We work from Kestopur, Kolkata and support businesses across Salt Lake, New Town, West Bengal, and remote teams across India by call, WhatsApp, or scheduled meeting.</p>
                  </div>
                </div>
                <div class="faq-item">
                  <h3 class="init-header">Will my website be ready for Google Search Console?</h3>
                  <div class="init-body">
                    <p>Yes. Every new build includes unique page titles, meta descriptions, clean URLs, mobile optimization, XML sitemap output, and basic Schema.org markup.</p>
                  </div>
                </div>
                <div class="faq-item">
                  <h3 class="init-header">Do you support updates after launch?</h3>
                  <div class="init-body">
                    <p>Yes. We provide ongoing maintenance, speed checks, and content updates under monthly care plans or scoped change requests.</p>
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
              <h2>Need a website quote?</h2>
              <p>Share your requirements with our Kestopur, Kolkata team for a clear scope and starting plan.</p>
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
          <h2>Ready to launch your website in Kolkata?</h2>
          <p>Compare our <a href="pricing">website plans from ₹1,999/month</a> or send your requirements for a tailored quote and timeline.</p>
          <div class="optech-extra-mt d-flex flex-wrap justify-content-center gap-3">
            <a class="optech-default-btn optech-white-btn" data-text="Request a Quote" href="contact"><span class="btn-wraper">Request a Quote</span></a>
            <a class="optech-default-btn nz-btn-red" data-text="Chat on WhatsApp" href="<?php echo site_whatsapp_url(); ?>" target="_blank" rel="noopener"><span class="btn-wraper">Chat on WhatsApp</span></a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- End section -->

<?php include 'footer.php'; ?>
