<?php
require_once __DIR__ . '/includes/site_config.php';
$pageBreadcrumbs = [
  ['name' => 'Home', 'url' => site_url('')],
  ['name' => 'Our Team', 'url' => site_url('team')],
];
include 'header.php';
?>

  <div class="optech-breadcrumb nz-breadcrumb">
    <div class="container">
      <h1 class="post__title">Our Team at Nazora Tech</h1>
      <nav class="breadcrumbs" aria-label="Breadcrumb">
        <ul>
          <li><a href="./">Home</a></li>
          <li aria-current="page">Our Team</li>
        </ul>
      </nav>
    </div>
  </div>

  <div class="section optech-section-padding">
    <div class="container">
      <div class="nz-page-intro text-center mb-5">
        <span class="nz-kicker">Leadership</span>
        <h2 class="nz-title">The people behind Nazora Tech</h2>
        <p class="nz-lead">A small Kolkata leadership team that stays close to the work, from the first brief through launch and growth across Kestopur, Salt Lake, New Town, and West Bengal.</p>
      </div>

      <div class="nz-team-grid">
        <article class="nz-team-card">
          <div class="nz-team-photo nz-team-photo--studio">
            <picture>
              <source srcset="assets/team-image/sk-nadim.webp" type="image/webp">
              <img src="assets/team-image/sk-nadim.png" alt="Sk Nadim — Founder and Senior Developer at Nazora Tech" width="446" height="375" loading="lazy" decoding="async">
            </picture>
          </div>
          <h3>Sk Nadim</h3>
          <span>Founder &amp; Senior Developer</span>
        </article>
        <article class="nz-team-card">
          <div class="nz-team-photo">
            <picture>
              <source srcset="assets/team-image/shazia-afrin.webp" type="image/webp">
              <img src="assets/team-image/shazia-afrin.jpeg" alt="Shazia Afrin — Partner and Marketing Head at Nazora Tech" width="972" height="983" loading="lazy" decoding="async">
            </picture>
          </div>
          <h3>Shazia Afrin</h3>
          <span>Partner &amp; Marketing Head</span>
        </article>
        <article class="nz-team-card">
          <div class="nz-team-photo nz-team-photo--studio">
            <img src="assets/team-image/Rahul-saha.webp" alt="Rahul Saha — Partner and Senior Developer at Nazora Tech" width="800" height="800" loading="lazy" decoding="async">
          </div>
          <h3>Rahul Saha</h3>
          <span>Partner &amp; Senior Developer</span>
        </article>
      </div>

      <div class="nz-page-cta mt-5 text-center">
        <h3>Want to work with us?</h3>
        <p>We're growing — explore open roles on the <a href="careers">careers page</a> or view our <a href="portfolio">portfolio</a>.</p>
        <a class="nz-header-btn" href="careers">View Careers</a>
      </div>
    </div>
  </div>

<?php include 'footer.php'; ?>
