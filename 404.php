<?php
http_response_code(404);
require_once __DIR__ . '/includes/site_config.php';

$pageTitle = 'Page Not Found (404) | ' . site_company_name();
$pageDescription = 'The requested page could not be found on ' . site_company_name() . '.';
$pageRobots = 'noindex, nofollow';
$pageCanonical = '';
$pageBaseHref = site_base_path() . '/';
include 'header.php';
?>

  <div class="optech-breadcrumb nz-breadcrumb">
    <div class="container">
      <h1 class="post__title">Page Not Found</h1>
      <nav class="breadcrumbs" aria-label="Breadcrumb">
        <ul>
          <li><a href="./">Home</a></li>
          <li aria-current="page">404</li>
        </ul>
      </nav>
    </div>
  </div>

  <section class="nz-about-block">
    <div class="container text-center" style="max-width:640px;">
      <img src="assets/images/breadcrumb/errors404.svg" alt="Page not found illustration" width="240" height="180" style="max-width:240px;height:auto;margin:0 auto 24px;display:block;">
      <span class="nz-kicker">404 Error</span>
      <h2 class="nz-title">This page could not be found</h2>
      <p class="nz-lead" style="margin:0 auto 28px;">The page may have moved or the address may have a typo. Use one of the links below to continue exploring <?php echo htmlspecialchars(site_company_name(), ENT_QUOTES, 'UTF-8'); ?>.</p>
      <div class="nz-about-actions justify-content-center">
        <a class="nz-header-btn" href="./">Back to Home</a>
        <a class="nz-about-link" href="service">Our Services</a>
        <a class="nz-about-link" href="portfolio">Portfolio</a>
        <a class="nz-about-link" href="contact">Contact Us</a>
      </div>
    </div>
  </section>

<?php include 'footer.php'; ?>
