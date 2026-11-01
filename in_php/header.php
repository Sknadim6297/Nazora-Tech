<?php
require_once __DIR__ . '/includes/site_config.php';
$company = site_company_name();
$currentPage = basename($_SERVER['PHP_SELF']);
$seoMap = [
  'index.php' => [
    'title' => 'Website, App & Software Development Company in Kolkata | ' . $company,
    'description' => $company . ' builds websites, mobile apps, software platforms, and AI automation solutions for businesses in Kolkata and beyond.',
    'keywords' => 'website development Kolkata, app development Kolkata, software development company Kolkata, AI automation services, IT solutions Kolkata'
  ],
  'service.php' => [
    'title' => 'IT Services in Kolkata | Web, App, Software & Digital Solutions',
    'description' => 'Explore our full range of IT and digital services including website development, app development, software solutions, branding, and support.',
    'keywords' => 'IT services Kolkata, website development, app development, software solutions, digital services'
  ],
  'contact.php' => [
    'title' => 'Contact ' . $company . ' | Kolkata IT Company',
    'description' => 'Contact our Kolkata-based team for website development, mobile apps, software solutions, and AI automation projects.',
    'keywords' => 'contact Kolkata IT company, website quote Kolkata, app development contact'
  ],
  'portfolio.php' => [
    'title' => 'Portfolio | ' . $company,
    'description' => 'View selected website, app, and branding projects completed by ' . $company . '.',
    'keywords' => 'portfolio website development, app portfolio, software project examples'
  ],
  'blog.php' => [
    'title' => 'Blog | ' . $company,
    'description' => 'Practical articles on web development, SEO, mobile apps, and digital business growth.',
    'keywords' => 'IT blog Kolkata, SEO articles, web development tips'
  ],
];

$seoData = $seoMap[$currentPage] ?? [
  'title' => $company,
  'description' => $company . ' provides website, app, software, and digital services from Kolkata.',
  'keywords' => 'website development, app development, software development, Kolkata IT services'
];

$pageTitle = $pageTitle ?? $seoData['title'];
$pageDescription = $pageDescription ?? $seoData['description'];
$pageKeywords = $pageKeywords ?? $seoData['keywords'];
$pageCanonical = $pageCanonical ?? null;
$pageOgImage = $pageOgImage ?? null;
$pageTwitterCard = $pageTwitterCard ?? 'summary_large_image';
$pageSchemaMarkup = $pageSchemaMarkup ?? null;
?>
<!DOCTYPE html>
<html lang="en">


<!-- Mirrored from optechanimation1.netlify.app/index-05 by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 23 Aug 2025 06:49:10 GMT -->
<!-- Added by HTTrack -->
<meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
  <meta name="description" content="<?php echo htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8'); ?>">
  <meta name="keywords" content="<?php echo htmlspecialchars($pageKeywords, ENT_QUOTES, 'UTF-8'); ?>">
  <meta name="robots" content="index, follow">
  <?php if (!empty($pageCanonical)) : ?>
  <link rel="canonical" href="<?php echo htmlspecialchars($pageCanonical, ENT_QUOTES, 'UTF-8'); ?>">
  <meta property="og:url" content="<?php echo htmlspecialchars($pageCanonical, ENT_QUOTES, 'UTF-8'); ?>">
  <?php endif; ?>
  <meta property="og:title" content="<?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?>">
  <meta property="og:description" content="<?php echo htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8'); ?>">
  <meta property="og:type" content="website">
  <?php if (!empty($pageOgImage)) : ?>
  <meta property="og:image" content="<?php echo htmlspecialchars($pageOgImage, ENT_QUOTES, 'UTF-8'); ?>">
  <?php endif; ?>
  <meta name="twitter:card" content="<?php echo htmlspecialchars($pageTwitterCard, ENT_QUOTES, 'UTF-8'); ?>">
  <meta name="twitter:title" content="<?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?>">
  <meta name="twitter:description" content="<?php echo htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8'); ?>">
  <?php if (!empty($pageOgImage)) : ?>
  <meta name="twitter:image" content="<?php echo htmlspecialchars($pageOgImage, ENT_QUOTES, 'UTF-8'); ?>">
  <?php endif; ?>

  <link rel="shortcut icon" href="assets/images/logo/nazora-logo.png" type="image/png">
  <link rel="icon" href="assets/images/logo/nazora-logo.png" type="image/png">
  <!--- End favicon-->

  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@100..800&amp;display=swap" rel="stylesheet">
  <!-- End google font  -->

  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/magnific-popup.css">
  <link rel="stylesheet" href="assets/css/slick.css">
  <link rel="stylesheet" href="assets/css/fontawesome.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css">
  <link rel="stylesheet" href="assets/css/aos.css">


  <!-- Code Editor  -->

  <link rel="stylesheet" href="assets/css/main.css">
  <link rel="stylesheet" href="assets/css/app.min.css">
  <link rel="stylesheet" href="assets/css/nazora-home.css?v=<?php echo (int) @filemtime(__DIR__ . '/assets/css/nazora-home.css'); ?>">
  <link rel="stylesheet" href="assets/css/nazora-theme.css?v=<?php echo (int) @filemtime(__DIR__ . '/assets/css/nazora-theme.css'); ?>">
  <?php if (!empty($loadBlogAssets)) : ?>
  <link rel="stylesheet" href="assets/css/blog.css">
  <?php endif; ?>

  <?php if (!empty($pageSchemaMarkup)) : ?>
  <script type="application/ld+json">
  <?php echo $pageSchemaMarkup; ?>
  </script>
  <?php endif; ?>

  <?php if ($currentPage === 'index.php') : ?>
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "LocalBusiness",
    "name": "<?php echo htmlspecialchars($company, ENT_QUOTES, 'UTF-8'); ?>",
    "description": "Website, app, software, and AI automation company based in Kolkata.",
    "email": "<?php echo htmlspecialchars(site_contact_email(), ENT_QUOTES, 'UTF-8'); ?>",
    "telephone": "+91-6297616918",
    "address": {
      "@type": "PostalAddress",
      "addressLocality": "Kolkata",
      "addressRegion": "West Bengal",
      "addressCountry": "IN"
    },
    "areaServed": "Kolkata"
  }
  </script>
  <?php endif; ?>
</head>

<body class="nazora-theme<?php echo ($currentPage === 'index.php' || !empty($loadNazoraHome)) ? ' nazora-home' : ''; ?>">

  <div class="optech-preloader-wrap nz-preloader" id="nz-preloader" aria-hidden="true">
    <div class="nz-preloader-inner">
      <img src="assets/images/logo/nazora-logo.png" alt="Nazora TECH" class="nz-preloader-logo">
      <div class="nz-preloader-dots" aria-hidden="true">
        <span></span><span></span><span></span>
      </div>
    </div>
  </div>
  <script>
    (function () {
      var el = document.getElementById('nz-preloader');
      if (!el) return;
      var hide = function () {
        el.classList.add('is-done');
        window.setTimeout(function () {
          el.style.display = 'none';
          el.setAttribute('aria-hidden', 'true');
        }, 420);
      };
      if (document.readyState === 'complete') {
        hide();
      } else {
        window.addEventListener('load', hide);
        window.setTimeout(hide, 1800);
      }
    })();
  </script>
  <!-- End preloader -->

  <!-- progress circle -->
  <div class="paginacontainer">
    <div class="progress-wrap">
      <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
        <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
      </svg>
      <div class="top-arrow">
        <i class="ri-arrow-up-s-line"></i>
      </div>
    </div>
  </div>




  <header class="site-header optech-header-section site-header--menu-right nz-site-header" id="sticky-menu">
    <div class="optech-header-top dark-bg nz-header-top">
      <div class="container">
        <div class="optech-header-info-wrap nz-header-info">
          <div class="optech-header-info">
            <ul>
              <li><i class="ri-map-pin-2-fill"></i>Kestopur, Kolkata, India</li>
            </ul>
          </div>
          <div class="optech-header-info">
            <ul>
              <li><a href="tel:6297616918"><i class="ri-phone-fill"></i>+91-6297616918</a></li>
              <li><a href="mailto:<?php echo htmlspecialchars(site_contact_email(), ENT_QUOTES, 'UTF-8'); ?>"><i class="ri-mail-fill"></i><?php echo htmlspecialchars(site_company_name(), ENT_QUOTES, 'UTF-8'); ?></a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="optech-header-bottom p-0 nz-header-bottom">
      <div class="container">
        <div class="header-bottom-border border-color-light nz-header-bar">
          <nav class="navbar site-navbar nz-navbar">
            <div class="brand-logo">
              <a href="index">
                <img src="assets/images/logo/nazora-logo.png" alt="<?php echo htmlspecialchars($company, ENT_QUOTES, 'UTF-8'); ?>">
              </a>
            </div>
            <div class="menu-block-wrapper">
              <div class="menu-overlay"></div>
              <nav class="menu-block" id="append-menu-header">
                <div class="mobile-menu-head">
                  <div class="go-back">
                    <i class="fa fa-angle-left"></i>
                  </div>
                  <div class="current-menu-title"></div>
                  <div class="mobile-menu-close">&times;</div>
                </div>
                <ul class="site-menu-main">
                  <li class="nav-item">
                    <a href="index" class="nav-link-item">Home</a>
                  </li>
                  <li class="nav-item nav-item-has-children">
                    <a href="#" class="nav-link-item drop-trigger">Pages <i class="ri-arrow-down-s-fill"></i></a>
                    <ul class="sub-menu" id="submenu-2">
                      <li class="sub-menu--item">
                        <a href="about_us"><span class="menu-item-text">About Us</span></a>
                      </li>
                      <li class="sub-menu--item">
                        <a href="team"><span class="menu-item-text">Our Team</span></a>
                      </li>
                      <li class="sub-menu--item">
                        <a href="careers"><span class="menu-item-text">Careers</span></a>
                      </li>
                      <li class="sub-menu--item">
                        <a href="pricing"><span class="menu-item-text">Pricing</span></a>
                      </li>
                      <li class="sub-menu--item">
                        <a href="service"><span class="menu-item-text">Service</span></a>
                      </li>
                    </ul>
                  </li>
                  <li class="nav-item">
                    <a href="portfolio" class="nav-link-item">Portfolio</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link-item" href="contact">Contact</a>
                  </li>
                </ul>
              </nav>
            </div>
            <div class="nz-header-actions">
              <a class="nz-header-btn d-none d-lg-inline-flex" href="contact">Get a Quote</a>
              <button type="button" class="optech-header-barger nz-menu-btn d-none d-md-inline-flex" aria-label="Open menu">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 50 50" aria-hidden="true">
                  <path fill="currentColor" d="M 0 7.5 L 0 12.5 L 50 12.5 L 50 7.5 Z M 0 22.5 L 0 27.5 L 50 27.5 L 50 22.5 Z M 0 37.5 L 0 42.5 L 50 42.5 L 50 37.5 Z"></path>
                </svg>
              </button>
              <button type="button" class="mobile-menu-trigger nz-menu-btn d-inline-flex d-md-none" aria-label="Open navigation">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 50 50" aria-hidden="true">
                  <path fill="currentColor" d="M 0 7.5 L 0 12.5 L 50 12.5 L 50 7.5 Z M 0 22.5 L 0 27.5 L 50 27.5 L 50 22.5 Z M 0 37.5 L 0 42.5 L 50 42.5 L 50 37.5 Z"></path>
                </svg>
              </button>
            </div>
          </nav>
        </div>

      </div>
    </div>

  </header>

  <div class="optech-sidemenu-wraper nz-sidemenu">
    <div class="optech-sidemenu-column">
      <div class="optech-sidemenu-body">
        <div class="optech-sidemenu-logo">
          <a href="index"><img src="assets/images/logo/nazora-logo.png" alt="<?php echo htmlspecialchars($company, ENT_QUOTES, 'UTF-8'); ?>"></a>
        </div>
        <p><?php echo htmlspecialchars($company, ENT_QUOTES, 'UTF-8'); ?> helps businesses build websites, mobile apps, and software products with practical support, clear delivery, and a modern digital approach.</p>
        <div class="optech-social-icon-box style-two nz-side-social">
          <ul>
            <li>
              <a href="https://www.facebook.com/" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                <i class="ri-facebook-fill"></i>
              </a>
            </li>
            <li>
              <a href="https://www.linkedin.com/" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
                <i class="ri-linkedin-fill"></i>
              </a>
            </li>
            <li>
              <a href="https://www.instagram.com/skonlineitsolutions?igsh=bTZ3ajRndDd4b2lw&utm_source=qr" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                <i class="ri-instagram-fill"></i>
              </a>
            </li>
            <li>
              <a href="<?php echo site_whatsapp_url(); ?>" target="_blank" rel="noopener" aria-label="WhatsApp">
                <i class="ri-whatsapp-fill"></i>
              </a>
            </li>
          </ul>
        </div>
        <div class="optech-contact-info-wrap nz-side-contact">
          <div class="optech-contact-info">
            <i class="ri-map-pin-2-fill"></i>
            <h5>Address</h5>
            <p>Kestopur, Kolkata<br>India</p>
          </div>
          <div class="optech-contact-info">
            <i class="ri-mail-fill"></i>
            <h5>Contact</h5>
            <a href="mailto:<?php echo htmlspecialchars(site_contact_email(), ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars(site_company_name(), ENT_QUOTES, 'UTF-8'); ?></a>
            <a href="tel:6297616918">+91-6297616918</a>
          </div>
        </div>
      </div>
      <span class="optech-sidemenu-close">
        <i class="ri-close-line"></i>
      </span>
    </div>
    <div class="offcanvas-overlay"></div>
  </div>

  <div class="offcanves-menu"></div>

  <!-- End sidebar -->