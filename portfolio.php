<?php
require_once __DIR__ . '/includes/portfolio_app.php';

$activeCategory = trim((string) ($_GET['category'] ?? ''));
if ($activeCategory !== '' && !in_array($activeCategory, portfolio_categories(), true)) {
    $activeCategory = '';
}
$projects = portfolio_published($activeCategory !== '' ? $activeCategory : null);

$pageBreadcrumbs = [
    ['name' => 'Home', 'url' => site_url('')],
    ['name' => 'Portfolio', 'url' => site_url('portfolio')],
];

if ($activeCategory !== '') {
    $pageRobots = 'noindex, follow';
}

$pageOgImage = site_url('assets/images/projects/rg-logistic.png');
$pageOgImageAlt = 'RC Logistics website homepage built by ' . site_company_name();
$pageOgImageWidth = 1314;
$pageOgImageHeight = 634;

include 'header.php';
?>

  <div class="optech-breadcrumb nz-breadcrumb">
    <div class="container">
      <h1 class="post__title">Nazora Tech Portfolio</h1>
      <nav class="breadcrumbs" aria-label="Breadcrumb">
        <ul>
          <li><a href="./">Home</a></li>
          <li aria-current="page">Portfolio</li>
        </ul>
      </nav>
    </div>
  </div>

  <section class="nz-about-block">
    <div class="container">
      <div class="nz-head-center">
        <span class="nz-kicker">Selected work</span>
        <h2 class="nz-title">Websites, apps, software, and brand marks</h2>
        <p class="nz-lead">Live client work and clearly labelled sample case studies from our Kolkata team. Each card opens a short story of the problem, the build, and what was delivered.</p>
      </div>

      <div class="nz-work-filters">
        <a class="<?php echo $activeCategory === '' ? 'is-active' : ''; ?>" href="portfolio">All</a>
        <?php foreach (portfolio_categories() as $category) : ?>
          <a class="<?php echo $activeCategory === $category ? 'is-active' : ''; ?>" href="portfolio?category=<?php echo rawurlencode($category); ?>"><?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?></a>
        <?php endforeach; ?>
      </div>

      <?php if (!$projects) : ?>
        <p class="nz-lead text-center">Projects in this category will appear here once they are published.</p>
      <?php else : ?>
        <div class="nz-work-grid">
          <?php foreach ($projects as $idx => $project) : ?>
            <?php
              $imgPath = (string) ($project['image'] ?? '');
              $webpPath = portfolio_webp_path($imgPath);
              [$imgW, $imgH] = portfolio_image_size($webpPath !== '' ? $webpPath : $imgPath);
              $detailUrl = 'portfolio_details?slug=' . rawurlencode($project['slug']);
              $isSample = portfolio_is_sample($project);
              $service = portfolio_service_link((string) $project['category']);
            ?>
            <article class="nz-work-card">
              <a class="nz-work-media" href="<?php echo $detailUrl; ?>" tabindex="-1" aria-hidden="true">
                <?php if ($imgPath !== '') : ?>
                  <picture>
                    <?php if ($webpPath !== '') : ?>
                      <source srcset="<?php echo htmlspecialchars($webpPath, ENT_QUOTES, 'UTF-8'); ?>" type="image/webp">
                    <?php endif; ?>
                    <img src="<?php echo htmlspecialchars($imgPath, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars(portfolio_image_alt($project), ENT_QUOTES, 'UTF-8'); ?>"<?php echo $imgW > 0 ? ' width="' . $imgW . '" height="' . $imgH . '"' : ''; ?><?php echo $idx >= 3 ? ' loading="lazy"' : ''; ?> decoding="async">
                  </picture>
                <?php else : ?>
                  <span class="nz-work-fallback"><?php echo htmlspecialchars($project['category'], ENT_QUOTES, 'UTF-8'); ?></span>
                <?php endif; ?>
              </a>
              <div class="nz-work-body">
                <div class="nz-work-tags">
                  <span><?php echo htmlspecialchars($project['category'], ENT_QUOTES, 'UTF-8'); ?></span>
                  <?php if ($isSample) : ?>
                    <span class="nz-work-badge is-sample">Sample project</span>
                  <?php elseif (!empty($project['live_url'])) : ?>
                    <span class="nz-work-badge is-live">Live client site</span>
                  <?php endif; ?>
                </div>
                <h3><a href="<?php echo $detailUrl; ?>"><?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?></a></h3>
                <p><?php echo htmlspecialchars($project['summary'], ENT_QUOTES, 'UTF-8'); ?></p>
                <div class="nz-work-actions">
                  <a class="nz-addon-link" href="<?php echo $detailUrl; ?>">View case study</a>
                  <a class="nz-about-link" href="<?php echo htmlspecialchars($service['url'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($service['label'], ENT_QUOTES, 'UTF-8'); ?></a>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <section class="nz-about-cta">
    <div class="container">
      <div class="nz-about-cta-card">
        <div>
          <h2>Have a project in mind?</h2>
          <p>Tell us what you want to launch. Compare our <a href="pricing">starting plans</a>, read about <a href="service_details">website development</a>, <a href="app_development_details">mobile apps</a>, and <a href="software_development_details">custom software</a>, or send a brief for a practical scope.</p>
        </div>
        <a class="nz-header-btn" href="contact">Start a project</a>
      </div>
    </div>
  </section>

<?php include 'footer.php'; ?>
