  <!-- Footer  -->

  <footer class="optech-footer-section nz-footer">
    <div class="container">
      <div class="nz-footer-top">
        <div class="nz-footer-col">
          <p class="nz-footer-heading">Product</p>
          <ul>
            <li><a href="service_details">Website Development</a></li>
            <li><a href="app_development_details">App Development</a></li>
            <li><a href="software_development_details">Software Solutions</a></li>
            <li><a href="contact">AI Automation</a></li>
            <li><a href="digital_marketing_details">Digital Marketing</a></li>
            <li><a href="pricing">Pricing Plans</a></li>
          </ul>
        </div>
        <div class="nz-footer-col">
          <p class="nz-footer-heading">Resources</p>
          <ul>
            <li><a href="portfolio">Portfolio</a></li>
            <li><a href="portfolio_details?slug=rc-logistics">Case Studies</a></li>
            <li><a href="service">Service Catalog</a></li>
          </ul>
        </div>
        <div class="nz-footer-col">
          <p class="nz-footer-heading">Support</p>
          <ul>
            <li><a href="contact">Request a Quote</a></li>
            <li><a href="<?php echo site_phone_tel(); ?>">Call Support</a></li>
            <li><a href="./#faq">FAQ</a></li>
            <li><a href="contact">Contact Us</a></li>
            <li><a href="<?php echo site_whatsapp_url(); ?>" target="_blank" rel="noopener">WhatsApp</a></li>
          </ul>
        </div>
        <div class="nz-footer-col">
          <p class="nz-footer-heading">Company</p>
          <ul>
            <li><a href="about_us">About Us</a></li>
            <li><a href="team">Our Team</a></li>
            <li><a href="careers">Careers</a></li>
            <li><a href="terms_and_conditions">Legal Notices</a></li>
          </ul>
        </div>
        <div class="nz-footer-news">
          <p>Have a project in mind? Send a short brief and get a clear next step from our Kolkata team.</p>
          <a class="nz-news-cta" href="contact"><i class="ri-mail-send-line" aria-hidden="true"></i> Get a Free Quote</a>
          <div class="nz-social">
            <a href="<?php echo site_whatsapp_url(); ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="ri-whatsapp-fill"></i></a>
            <a href="<?php echo site_phone_tel(); ?>" aria-label="Call Nazora Tech"><i class="ri-phone-fill"></i></a>
            <a href="mailto:<?php echo htmlspecialchars(site_contact_email(), ENT_QUOTES, 'UTF-8'); ?>" aria-label="Email Nazora Tech"><i class="ri-mail-fill"></i></a>
          </div>
        </div>
      </div>

      <div class="nz-footer-bottom">
        <a class="brand" href="./">
          <img src="assets/images/logo/nazora-logo.webp" alt="<?php echo htmlspecialchars(site_company_name(), ENT_QUOTES, 'UTF-8'); ?> logo" width="434" height="198" loading="lazy" decoding="async">
        </a>
        <p>Copyright © <?php echo date('Y'); ?> <?php echo htmlspecialchars(site_company_name(), ENT_QUOTES, 'UTF-8'); ?>. All rights reserved.</p>
        <div class="legal">
          <a href="terms_and_conditions">Terms &amp; Conditions</a>
          <span>|</span>
          <a href="privacy_policy">Privacy Policy</a>
          <span>|</span>
          <a href="refund_policy">Refund Policy</a>
          <span>|</span>
          <a href="cancellation_policy">Cancellation Policy</a>
          <?php if (site_ga4_id() !== '') : ?>
          <span>|</span>
          <a href="privacy_policy#analytics" data-consent-open>Cookie Settings</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </footer>

  <div class="floating-contact-actions" aria-label="Quick contact actions">
    <a class="floating-contact-btn floating-whatsapp-btn" href="<?php echo site_whatsapp_url(); ?>" target="_blank" rel="noopener">
      <i class="ri-whatsapp-line"></i>
      <span>WhatsApp</span>
    </a>
    <a class="floating-contact-btn floating-call-btn" href="<?php echo site_phone_tel(); ?>">
      <i class="ri-phone-line"></i>
      <span>Call Now</span>
    </a>
  </div>

  <?php if (site_ga4_id() !== '') : ?>
  <div class="nz-consent" id="nz-consent" role="dialog" aria-live="polite" aria-label="Analytics cookie choice" hidden>
    <p>We use Google Analytics cookies to see which pages help visitors. Your name, phone, and email are never sent. <a href="privacy_policy#analytics">Privacy Policy</a></p>
    <div class="nz-consent-actions">
      <button type="button" class="nz-consent-btn is-ghost" data-consent="denied">Decline</button>
      <button type="button" class="nz-consent-btn" data-consent="granted">Accept</button>
    </div>
  </div>
  <script>window.NZ_ANALYTICS = { id: <?php echo json_encode(site_ga4_id()); ?> };</script>
  <script src="assets/js/nz-analytics.js?v=<?php echo (int) @filemtime(__DIR__ . '/assets/js/nz-analytics.js'); ?>" defer></script>
  <?php endif; ?>

  <!-- scripts -->
  <script src="assets/js/jquery-3.7.1.min.js"></script>
  <script src="assets/js/menu/menu.js"></script>
  <script src="assets/js/faq.js"></script>
  <script src="assets/js/top-to-bottom.js?v=<?php echo (int) @filemtime(__DIR__ . '/assets/js/top-to-bottom.js'); ?>"></script>
  <script src="assets/js/app.js?v=<?php echo (int) @filemtime(__DIR__ . '/assets/js/app.js'); ?>"></script>
  <script src="assets/js/nz-reveal.js?v=<?php echo (int) @filemtime(__DIR__ . '/assets/js/nz-reveal.js'); ?>"></script>
  <script>
    (function ($) {
      function hidePreloader() {
        var $wrap = $('#nz-preloader, .optech-preloader-wrap');
        if (!$wrap.length || $wrap.data('hidden')) return;
        $wrap.data('hidden', 1).addClass('is-done').fadeOut(400);
      }
      $(hidePreloader);
      $(window).on('load', hidePreloader);
      setTimeout(hidePreloader, 2000);
    })(jQuery);
  </script>
</body>
</html>
