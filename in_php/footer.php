  <!-- Footer  -->

  <footer class="optech-footer-section nz-footer">
    <div class="container">
      <div class="nz-footer-top">
        <div class="nz-footer-col">
          <h5>Product</h5>
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
          <h5>Resources</h5>
          <ul>
            <li><a href="portfolio">Portfolio</a></li>
            <li><a href="about_us">Case Studies</a></li>
            <li><a href="service">Service Catalog</a></li>
          </ul>
        </div>
        <div class="nz-footer-col">
          <h5>Support</h5>
          <ul>
            <li><a href="contact">Help Center</a></li>
            <li><a href="contact">Ticket Support</a></li>
            <li><a href="index#faq">FAQ</a></li>
            <li><a href="contact">Contact Us</a></li>
            <li><a href="<?php echo site_whatsapp_url(); ?>" target="_blank" rel="noopener">WhatsApp</a></li>
          </ul>
        </div>
        <div class="nz-footer-col">
          <h5>Company</h5>
          <ul>
            <li><a href="about_us">About Us</a></li>
            <li><a href="team">Our Team</a></li>
            <li><a href="careers">Careers</a></li>
            <li><a href="contact">Legal Notices</a></li>
          </ul>
        </div>
        <div class="nz-footer-news">
          <p>Sign up for our newsletter to get updates, news, and free insight.</p>
          <form class="nz-news-form" action="contact" method="get">
            <input type="email" name="email" placeholder="Email" aria-label="Email" required>
            <button type="submit"><i class="ri-mail-send-line"></i> SIGN UP</button>
          </form>
          <div class="nz-social">
            <a href="https://www.facebook.com/" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="ri-facebook-fill"></i></a>
            <a href="https://www.instagram.com/skonlineitsolutions?igsh=bTZ3ajRndDd4b2lw&utm_source=qr" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="ri-instagram-fill"></i></a>
            <a href="https://www.linkedin.com/" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn"><i class="ri-linkedin-fill"></i></a>
            <a href="<?php echo site_whatsapp_url(); ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="ri-whatsapp-fill"></i></a>
          </div>
        </div>
      </div>

      <div class="nz-footer-bottom">
        <a class="brand" href="index">
          <img src="assets/images/logo/nazora-logo.png" alt="<?php echo htmlspecialchars(site_company_name(), ENT_QUOTES, 'UTF-8'); ?>">
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
        </div>
      </div>
    </div>
  </footer>

  <div class="floating-contact-actions" aria-label="Quick contact actions">
    <a class="floating-contact-btn floating-whatsapp-btn" href="<?php echo site_whatsapp_url(); ?>" target="_blank" rel="noopener">
      <i class="ri-whatsapp-line"></i>
      <span>WhatsApp</span>
    </a>
    <a class="floating-contact-btn floating-call-btn" href="tel:6297616918">
      <i class="ri-phone-line"></i>
      <span>Call Now</span>
    </a>
  </div>





  <!-- scripts -->
  <script src="assets/js/jquery-3.7.1.min.js"></script>
  <script src="assets/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/menu/menu.js"></script>
  <script src="assets/js/jquery.magnific-popup.min.js"></script>
  <script src="assets/js/slick.js"></script>
  <script src="assets/js/countdown.js"></script>
  <script src="assets/js/skillbar.js"></script>
  <script src="assets/js/slick-animation.js"></script>
  <script src="assets/js/slick-animation.min.js"></script>
  <script src="assets/js/faq.js"></script>
  <script src="assets/js/isotope.pkgd.min.js"></script>
  <script src="assets/js/tabs-slider.js"></script>
  <script src="assets/js/product-increment.js"></script>
  <script src="assets/js/top-to-bottom.js"></script>
  <script src="assets/js/aos.js"></script>
  <?php if (!empty($loadGoogleMaps)) : ?>
  <script src="https://maps.googleapis.com/maps/api/js?v=3&amp;key=AIzaSyArZVfNvjnLNwJZlLJKuOiWHZ6vtQzzb1Y" async defer></script>
  <?php endif; ?>

  <script src="assets/js/app.js"></script>
  <script src="assets/js/nz-reveal.js"></script>
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
