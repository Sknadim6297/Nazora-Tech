<?php
require_once __DIR__ . '/includes/site_config.php';
$pageBreadcrumbs = [
  ['name' => 'Home', 'url' => site_url('')],
  ['name' => 'Contact Us', 'url' => site_url('contact')],
];
include 'header.php';
?>

  <div class="optech-breadcrumb nz-breadcrumb">
    <div class="container">
      <h1 class="post__title">Contact Nazora Tech</h1>
      <nav class="breadcrumbs" aria-label="Breadcrumb">
        <ul>
          <li><a href="./">Home</a></li>
          <li aria-current="page">Contact us</li>
        </ul>
      </nav>
    </div>
  </div>
  <!-- End breadcrumb -->

  <div class="section optech-section-padding">
    <div class="container">
      <div class="row g-4 align-items-start">
        <div class="col-lg-6">
          <div class="optech-main-form nz-form-card">
            <h2>Fill The Contact Form</h2>
            <p>Feel free to contact with us, we don't spam your email</p>
            <form class="nz-form" action="send_email.php" method="POST" data-form-id="contact_page">
              <div class="nz-hp" aria-hidden="true"><label>Website <input type="text" name="company_website" tabindex="-1" autocomplete="off"></label></div>
              <div class="row">
                <div class="col-lg-6">
                  <div class="optech-main-field">
                    <input type="text" name="name" placeholder="Your name" required>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="optech-main-field">
                    <input type="tel" name="phone" placeholder="Phone number" required>
                  </div>
                </div>
                <div class="col-lg-12">
                  <div class="optech-main-field">
                    <input type="email" name="email" placeholder="Email address" required>
                  </div>
                </div>
                <div class="col-lg-12">
                  <div class="optech-main-field">
                    <select name="service" required>
                      <option value="" selected disabled>Select service</option>
                      <option value="Website Development">Website Development</option>
                      <option value="App Development">App Development</option>
                      <option value="Software Development">Software Development</option>
                      <option value="SEO">SEO</option>
                      <option value="AI Automation">AI Automation</option>
                    </select>
                  </div>
                </div>
                <div class="col-lg-12">
                  <div class="optech-main-field">
                    <textarea name="message" placeholder="Write your message" required></textarea>
                  </div>
                </div>
                <div class="col-lg-12">
                  <button class="nz-form-btn" type="submit">Send Message</button>
                </div>
              </div>
            </form>
          </div>
        </div>
        <div class="col-lg-6 d-flex align-items-center">
          <div class="optech-default-content">
            <h2>Let's build an awesome project together</h2>
            <p>Get in touch with our team in Kestopur, Kolkata. We serve businesses across Salt Lake, New Town, Kolkata, West Bengal, and remote teams across India. Fill out the form and we'll respond within 24 hours.</p>
            <div class="optech-contact-info-column">
              <div class="optech-contact-info">
                <i class="ri-map-pin-2-fill"></i>
                <h3>Business Information</h3>
                <p><strong>Legal Name:</strong> SK NADIM<br>
                  <strong>Operating Area:</strong> Kestopur, Kolkata, West Bengal</p>
              </div>
              <div class="optech-contact-info">
                <i class="ri-mail-fill"></i>
                <h3>Contact</h3>
                <a href="mailto:<?php echo htmlspecialchars(site_contact_email(), ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars(site_company_name(), ENT_QUOTES, 'UTF-8'); ?></a>
                <a href="<?php echo site_phone_tel(); ?>"><?php echo site_phone_display(); ?></a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- End section -->
  <script src="assets/js/contact-form.js?v=<?php echo (int) @filemtime(__DIR__ . '/assets/js/contact-form.js'); ?>" defer></script>
<?php include 'footer.php'; ?>
