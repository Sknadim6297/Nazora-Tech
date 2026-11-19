<?php
require_once __DIR__ . '/includes/site_config.php';
$company = site_company_name();
$currentPage = basename($_SERVER['PHP_SELF']);
$seoMap = [
  'index.php' => [
    'title' => 'Website & App Development Company Kolkata | ' . $company,
    'description' => $company . ' offers website, mobile app, custom software and AI automation services in Kolkata. Affordable digital solutions for growing businesses.',
    'keywords' => 'website development company Kolkata, app development company Kolkata, software development company Kolkata, AI automation Kolkata, Nazora Tech'
  ],
  'about_us.php' => [
    'title' => 'About ' . $company . ' | Web & App Company Kolkata',
    'description' => $company . ' builds websites, mobile apps, custom software, and AI automation from Kestopur, Kolkata with clear scope and after-launch support.',
    'keywords' => 'about Nazora Tech, web development company Kolkata, app development company Kestopur Kolkata'
  ],
  'service.php' => [
    'title' => 'Web, App & Software Services Kolkata | ' . $company,
    'description' => 'Explore ' . $company . ' services in Kolkata: website design, mobile app development, custom software, digital marketing, AI automation, and support.',
    'keywords' => 'IT services Kolkata, website development Kolkata, app development Kolkata, custom software Kolkata, AI automation'
  ],
  'pricing.php' => [
    'title' => 'Web, App & Software Pricing Kolkata | ' . $company,
    'description' => 'Starting prices in Kolkata: websites from ₹1,999/month, mobile apps from ₹7,999, custom software and digital marketing from ₹4,999.',
    'keywords' => 'website development price Kolkata, app development cost Kolkata, software development pricing, Nazora Tech pricing'
  ],
  'portfolio.php' => [
    'title' => 'Portfolio: Websites, Apps & Software | ' . $company,
    'description' => 'Explore live websites, mobile apps, custom business software, and branding case studies built by ' . $company . ' in Kolkata.',
    'keywords' => 'Nazora Tech portfolio, website portfolio Kolkata, app development case studies, software projects'
  ],
  'team.php' => [
    'title' => 'Our Leadership Team | ' . $company . ' Kolkata',
    'description' => 'Meet the founder, leadership, and senior developers behind ' . $company . ' in Kestopur, Kolkata building websites, apps, and business software.',
    'keywords' => 'Nazora Tech team, Sk Nadim, Shazia Afrin, Rahul Saha, software developers Kolkata'
  ],
  'careers.php' => [
    'title' => 'Careers & Tech Jobs in Kolkata | ' . $company,
    'description' => 'Explore developer, design, and digital marketing job openings at ' . $company . ' in Kestopur, Kolkata and apply directly online.',
    'keywords' => 'Nazora Tech careers, IT jobs Kolkata, web developer jobs Kestopur Kolkata'
  ],
  'contact.php' => [
    'title' => 'Contact ' . $company . ' | Web & App Quote Kolkata',
    'description' => 'Contact ' . $company . ' in Kestopur, Kolkata for websites, mobile apps, custom software, or AI automation. Call ' . site_phone_display() . ' or request a quote.',
    'keywords' => 'contact Nazora Tech, website quote Kolkata, app development contact Kestopur Kolkata'
  ],
  'service_details.php' => [
    'title' => 'Website Development Company in Kolkata | ' . $company,
    'description' => 'Custom business websites, landing pages, and SEO-ready web design in Kolkata starting at ₹1,999/month. Fast, mobile-friendly builds by ' . $company . '.',
    'keywords' => 'website development company Kolkata, web design Kestopur, Salt Lake website developer, New Town web design'
  ],
  'app_development_details.php' => [
    'title' => 'Mobile App Development Company Kolkata | ' . $company,
    'description' => 'Android and iOS mobile app development in Kolkata starting from ₹7,999. Clean UI, secure APIs, and scalable backends built by ' . $company . '.',
    'keywords' => 'mobile app development Kolkata, Android app developer Kolkata, Flutter app development West Bengal'
  ],
  'software_development_details.php' => [
    'title' => 'Custom Software Development Kolkata | ' . $company,
    'description' => 'Custom CRM, ERP, billing software, and business dashboards in Kolkata starting from ₹4,999. Tailored workflows and support by ' . $company . '.',
    'keywords' => 'software development company Kolkata, custom CRM Kolkata, billing software Kolkata, ERP development West Bengal'
  ],
  'digital_marketing_details.php' => [
    'title' => 'Digital Marketing & SEO Kolkata | ' . $company,
    'description' => 'SEO, Google Ads, Meta campaigns, and local digital marketing services in Kolkata starting at ₹4,999/month from ' . $company . '.',
    'keywords' => 'digital marketing Kolkata, local SEO services Kolkata, Google Ads Kolkata, social media marketing'
  ],
  'terms_and_conditions.php' => [
    'title' => 'Terms & Conditions | ' . $company,
    'description' => 'Read the Terms & Conditions for using the ' . $company . ' website and enquiring about web, app, software, and digital services in Kolkata.',
    'keywords' => 'Nazora Tech terms and conditions, service terms Kolkata'
  ],
  'privacy_policy.php' => [
    'title' => 'Privacy Policy | ' . $company,
    'description' => 'How ' . $company . ' collects, uses, stores and protects contact enquiries, emails and analytics choices on our website in Kestopur, Kolkata.',
    'keywords' => 'Nazora Tech privacy policy, data handling Kolkata'
  ],
  'refund_policy.php' => [
    'title' => 'Refund Policy | ' . $company,
    'description' => 'Review the Refund Policy for website, app, software, and digital marketing projects provided by ' . $company . '.',
    'keywords' => 'Nazora Tech refund policy'
  ],
  'cancellation_policy.php' => [
    'title' => 'Cancellation Policy | ' . $company,
    'description' => 'Read the Cancellation Policy for website, mobile app, software, and digital marketing services at ' . $company . '.',
    'keywords' => 'Nazora Tech cancellation policy'
  ],
];

$seoData = $seoMap[$currentPage] ?? [
  'title' => $company . ' | Kolkata Web & Software Studio',
  'description' => $company . ' builds websites, mobile apps, custom software, and AI automation in Kolkata.',
  'keywords' => 'website development Kolkata, app development Kolkata, software development Kolkata'
];

$pageTitle = $pageTitle ?? $seoData['title'];
$pageDescription = site_seo_truncate((string) ($pageDescription ?? $seoData['description']), 155);
$pageKeywords = $pageKeywords ?? $seoData['keywords'];
$pageRobots = $pageRobots ?? 'index, follow';
$canonicalSlug = preg_replace('~\.php$~', '', $currentPage);
$canonicalSlug = $canonicalSlug === 'index' ? '' : $canonicalSlug;
$pageCanonical = isset($pageCanonical) ? $pageCanonical : site_url($canonicalSlug);
$pageOgImage = $pageOgImage ?? site_url('assets/images/logo/nazora-logo.png');
$pageOgImageAlt = $pageOgImageAlt ?? ($company . ' logo');
$pageOgImageWidth = (int) ($pageOgImageWidth ?? 0);
$pageOgImageHeight = (int) ($pageOgImageHeight ?? 0);
$pageBaseHref = $pageBaseHref ?? null;
$pageOgType = $pageOgType ?? 'website';
$pageTwitterCard = $pageTwitterCard ?? 'summary_large_image';
$pageSchemaMarkup = $pageSchemaMarkup ?? null;
$pageServiceSchema = $pageServiceSchema ?? null;
$pageBreadcrumbs = $pageBreadcrumbs ?? null;
$pagePreloadImage = $pagePreloadImage ?? null;

$homeUrl = site_url('');
$orgId = $homeUrl . '#organization';
$bizId = $homeUrl . '#business';
$webId = $homeUrl . '#website';

$schemaNodes = [];
if ($currentPage === 'index.php') {
  $schemaNodes[] = [
    '@type' => 'Organization',
    '@id' => $orgId,
    'name' => $company,
    'alternateName' => 'Nazora TECH',
    'url' => $homeUrl,
    'logo' => [
      '@type' => 'ImageObject',
      'url' => site_url('assets/images/logo/nazora-logo.png'),
      'width' => 434,
      'height' => 198,
    ],
    'email' => site_contact_email(),
    'telephone' => site_phone_display(),
    'contactPoint' => [
      '@type' => 'ContactPoint',
      'telephone' => site_phone_display(),
      'contactType' => 'customer service',
      'areaServed' => 'IN',
      'availableLanguage' => ['English', 'Bengali', 'Hindi'],
    ],
  ];
  $schemaNodes[] = [
    '@type' => 'ProfessionalService',
    '@id' => $bizId,
    'name' => $company,
    'alternateName' => 'Nazora TECH',
    'url' => $homeUrl,
    'image' => site_url('assets/images/logo/nazora-logo.png'),
    'description' => 'Website development, mobile app development, custom software, digital marketing, and AI automation services in Kolkata, West Bengal, India.',
    'email' => site_contact_email(),
    'telephone' => site_phone_display(),
    'priceRange' => '₹₹',
    'address' => [
      '@type' => 'PostalAddress',
      'addressLocality' => 'Kestopur, Kolkata',
      'addressRegion' => 'West Bengal',
      'addressCountry' => 'IN',
    ],
    'areaServed' => [
      ['@type' => 'Place', 'name' => 'Kestopur, Kolkata'],
      ['@type' => 'Place', 'name' => 'Salt Lake, Kolkata'],
      ['@type' => 'Place', 'name' => 'New Town, Kolkata'],
      ['@type' => 'City', 'name' => 'Kolkata'],
      ['@type' => 'State', 'name' => 'West Bengal'],
      ['@type' => 'Country', 'name' => 'India'],
    ],
    'serviceType' => [
      'Website Development',
      'Mobile App Development',
      'Custom Software Development',
      'Digital Marketing',
      'AI Automation',
    ],
  ];
  $schemaNodes[] = [
    '@type' => 'WebSite',
    '@id' => $webId,
    'name' => $company,
    'url' => $homeUrl,
    'publisher' => ['@id' => $orgId],
    'inLanguage' => 'en-IN',
  ];
}

if (!empty($pageBreadcrumbs) && is_array($pageBreadcrumbs)) {
  $bcItems = [];
  foreach (array_values($pageBreadcrumbs) as $idx => $crumb) {
    $entry = [
      '@type' => 'ListItem',
      'position' => $idx + 1,
      'name' => (string) ($crumb['name'] ?? ''),
    ];
    if (!empty($crumb['url'])) {
      $entry['item'] = (string) $crumb['url'];
    }
    $bcItems[] = $entry;
  }
  $schemaNodes[] = [
    '@type' => 'BreadcrumbList',
    'itemListElement' => $bcItems,
  ];
}

if (!empty($pageServiceSchema) && is_array($pageServiceSchema)) {
  $schemaNodes[] = array_merge([
    '@type' => 'Service',
    'provider' => [
      '@type' => 'ProfessionalService',
      '@id' => $bizId,
      'name' => $company,
      'url' => $homeUrl,
      'telephone' => site_phone_display(),
    ],
    'areaServed' => [
      ['@type' => 'City', 'name' => 'Kolkata'],
      ['@type' => 'State', 'name' => 'West Bengal'],
      ['@type' => 'Country', 'name' => 'India'],
    ],
  ], $pageServiceSchema);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php if (!empty($pageBaseHref)) : ?>
  <base href="<?php echo htmlspecialchars($pageBaseHref, ENT_QUOTES, 'UTF-8'); ?>">
  <?php endif; ?>
  <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
  <meta name="description" content="<?php echo htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8'); ?>">
  <meta name="keywords" content="<?php echo htmlspecialchars($pageKeywords, ENT_QUOTES, 'UTF-8'); ?>">
  <meta name="robots" content="<?php echo htmlspecialchars($pageRobots, ENT_QUOTES, 'UTF-8'); ?>">
  <?php if (!empty($pageCanonical)) : ?>
  <link rel="canonical" href="<?php echo htmlspecialchars($pageCanonical, ENT_QUOTES, 'UTF-8'); ?>">
  <meta property="og:url" content="<?php echo htmlspecialchars($pageCanonical, ENT_QUOTES, 'UTF-8'); ?>">
  <?php endif; ?>
  <meta property="og:site_name" content="<?php echo htmlspecialchars($company, ENT_QUOTES, 'UTF-8'); ?>">
  <meta property="og:locale" content="en_IN">
  <meta property="og:title" content="<?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?>">
  <meta property="og:description" content="<?php echo htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8'); ?>">
  <meta property="og:type" content="<?php echo htmlspecialchars($pageOgType, ENT_QUOTES, 'UTF-8'); ?>">
  <?php if (!empty($pageOgImage)) : ?>
  <meta property="og:image" content="<?php echo htmlspecialchars($pageOgImage, ENT_QUOTES, 'UTF-8'); ?>">
  <meta property="og:image:alt" content="<?php echo htmlspecialchars($pageOgImageAlt, ENT_QUOTES, 'UTF-8'); ?>">
  <?php if ($pageOgImageWidth > 0 && $pageOgImageHeight > 0) : ?>
  <meta property="og:image:width" content="<?php echo $pageOgImageWidth; ?>">
  <meta property="og:image:height" content="<?php echo $pageOgImageHeight; ?>">
  <?php endif; ?>
  <?php endif; ?>
  <meta name="twitter:card" content="<?php echo htmlspecialchars($pageTwitterCard, ENT_QUOTES, 'UTF-8'); ?>">
  <meta name="twitter:title" content="<?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?>">
  <meta name="twitter:description" content="<?php echo htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8'); ?>">
  <?php if (!empty($pageOgImage)) : ?>
  <meta name="twitter:image" content="<?php echo htmlspecialchars($pageOgImage, ENT_QUOTES, 'UTF-8'); ?>">
  <meta name="twitter:image:alt" content="<?php echo htmlspecialchars($pageOgImageAlt, ENT_QUOTES, 'UTF-8'); ?>">
  <?php endif; ?>

  <link rel="shortcut icon" href="assets/images/logo/nazora-logo.png" type="image/png">
  <link rel="icon" href="assets/images/logo/nazora-logo.png" type="image/png">

  <link rel="preload" href="assets/fonts/dm-sans-latin-opsz.woff2" as="font" type="font/woff2" crossorigin>
  <?php if (!empty($pagePreloadImage)) : ?>
  <link rel="preload" as="image" href="<?php echo htmlspecialchars($pagePreloadImage, ENT_QUOTES, 'UTF-8'); ?>" fetchpriority="high">
  <?php endif; ?>
  <style>
    /* DM Sans (variable: weight 100-1000, optical size 9-40; self-hosted, SIL OFL licence): one family for the whole site */
    @font-face {
      font-family: 'DM Sans';
      font-style: normal;
      font-weight: 100 1000;
      font-display: swap;
      src: url('assets/fonts/dm-sans-latin-ext-opsz.woff2') format('woff2');
      unicode-range: U+0100-02BA, U+02BD-02C5, U+02C7-02CC, U+02CE-02D7, U+02DD-02FF, U+0304, U+0308, U+0329, U+1D00-1DBF, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20C4, U+2113, U+2C60-2C7F, U+A720-A7FF;
    }
    @font-face {
      font-family: 'DM Sans';
      font-style: normal;
      font-weight: 100 1000;
      font-display: swap;
      src: url('assets/fonts/dm-sans-latin-opsz.woff2') format('woff2');
      unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
    }
  </style>

  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/vendor/remixicon/remixicon.css">

  <link rel="stylesheet" href="assets/css/main.css">
  <link rel="stylesheet" href="assets/css/app.min.css">
  <link rel="stylesheet" href="assets/css/nazora-home.css?v=<?php echo (int) @filemtime(__DIR__ . '/assets/css/nazora-home.css'); ?>">
  <link rel="stylesheet" href="assets/css/nazora-theme.css?v=<?php echo (int) @filemtime(__DIR__ . '/assets/css/nazora-theme.css'); ?>">
  <?php if (!empty($loadBlogAssets)) : ?>
  <link rel="stylesheet" href="assets/css/blog.css">
  <?php endif; ?>

  <?php if (!empty($schemaNodes)) : ?>
  <script type="application/ld+json">
  <?php
    echo json_encode([
      '@context' => 'https://schema.org',
      '@graph' => $schemaNodes,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
  ?>
  </script>
  <?php endif; ?>

  <?php if (!empty($pageSchemaMarkup)) : ?>
  <script type="application/ld+json">
  <?php echo $pageSchemaMarkup; ?>
  </script>
  <?php endif; ?>
</head>

<body class="nazora-theme<?php echo ($currentPage === 'index.php' || !empty($loadNazoraHome)) ? ' nazora-home' : ''; ?>">

  <div class="optech-preloader-wrap nz-preloader" id="nz-preloader" aria-hidden="true">
    <div class="nz-preloader-inner">
      <img src="assets/images/logo/nazora-logo.webp" alt="" width="434" height="198" class="nz-preloader-logo">
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
              <li><a href="<?php echo site_phone_tel(); ?>"><i class="ri-phone-fill"></i><?php echo site_phone_display(); ?></a></li>
              <li><a href="mailto:<?php echo htmlspecialchars(site_contact_email(), ENT_QUOTES, 'UTF-8'); ?>"><i class="ri-mail-fill"></i><?php echo htmlspecialchars(site_company_name(), ENT_QUOTES, 'UTF-8'); ?></a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="optech-header-bottom p-0 nz-header-bottom">
      <div class="container">
        <div class="header-bottom-border border-color-light nz-header-bar">
          <nav class="navbar site-navbar nz-navbar" aria-label="Primary navigation">
            <div class="brand-logo">
              <a href="./">
                <picture>
                  <source srcset="assets/images/logo/nazora-logo.webp" type="image/webp">
                  <img src="assets/images/logo/nazora-logo.png" alt="<?php echo htmlspecialchars($company, ENT_QUOTES, 'UTF-8'); ?> logo" width="434" height="198">
                </picture>
              </a>
            </div>
            <div class="menu-block-wrapper">
              <div class="menu-overlay"></div>
              <nav class="menu-block" id="append-menu-header" aria-label="Main menu">
                <div class="mobile-menu-head">
                  <div class="go-back">
                    <i class="ri-arrow-left-s-line" aria-hidden="true"></i>
                  </div>
                  <div class="current-menu-title"></div>
                  <div class="mobile-menu-close">&times;</div>
                </div>
                <ul class="site-menu-main">
                  <li class="nav-item">
                    <a href="./" class="nav-link-item">Home</a>
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
          <a href="./"><img src="assets/images/logo/nazora-logo.webp" alt="<?php echo htmlspecialchars($company, ENT_QUOTES, 'UTF-8'); ?> logo" width="434" height="198" loading="lazy" decoding="async"></a>
        </div>
        <p><?php echo htmlspecialchars($company, ENT_QUOTES, 'UTF-8'); ?> helps businesses build websites, mobile apps, and software products with practical support, clear delivery, and a modern digital approach.</p>
        <div class="optech-social-icon-box style-two nz-side-social">
          <ul>
            <li>
              <a href="<?php echo site_whatsapp_url(); ?>" target="_blank" rel="noopener" aria-label="WhatsApp">
                <i class="ri-whatsapp-fill"></i>
              </a>
            </li>
            <li>
              <a href="<?php echo site_phone_tel(); ?>" aria-label="Call Nazora Tech">
                <i class="ri-phone-fill"></i>
              </a>
            </li>
            <li>
              <a href="mailto:<?php echo htmlspecialchars(site_contact_email(), ENT_QUOTES, 'UTF-8'); ?>" aria-label="Email Nazora Tech">
                <i class="ri-mail-fill"></i>
              </a>
            </li>
          </ul>
        </div>
        <div class="optech-contact-info-wrap nz-side-contact">
          <div class="optech-contact-info">
            <i class="ri-map-pin-2-fill"></i>
            <p class="nz-side-heading">Address</p>
            <p>Kestopur, Kolkata<br>India</p>
          </div>
          <div class="optech-contact-info">
            <i class="ri-mail-fill"></i>
            <p class="nz-side-heading">Contact</p>
            <a href="mailto:<?php echo htmlspecialchars(site_contact_email(), ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars(site_company_name(), ENT_QUOTES, 'UTF-8'); ?></a>
            <a href="<?php echo site_phone_tel(); ?>"><?php echo site_phone_display(); ?></a>
          </div>
        </div>
      </div>
      <span class="optech-sidemenu-close" aria-label="Close menu">
        <i class="ri-close-line"></i>
      </span>
    </div>
    <div class="offcanvas-overlay"></div>
  </div>

  <div class="offcanves-menu"></div>

  <!-- End sidebar -->
