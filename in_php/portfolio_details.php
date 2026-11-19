<?php
require_once __DIR__ . '/includes/portfolio_app.php';

$slug = trim((string) ($_GET['slug'] ?? $_GET['id'] ?? ''));
if ($slug === '') {
    header('Location: ' . site_url('portfolio'), true, 301);
    exit;
}

$project = portfolio_get_by_slug($slug);

if (!$project) {
    require __DIR__ . '/404.php';
    exit;
}

$features = portfolio_features($project);
$related = array_values(array_filter(portfolio_published($project['category']), function ($item) use ($project) {
    return (int) $item['id'] !== (int) $project['id'];
}));
$related = array_slice($related, 0, 3);

$isSample = portfolio_is_sample($project);
$service = portfolio_service_link((string) $project['category']);
$kindLabel = portfolio_kind_label($project);
$imageAlt = portfolio_image_alt($project);

$pageTitle = portfolio_seo_title($project);
$pageDescription = portfolio_meta_description($project);
$pageCanonical = site_url('portfolio_details') . '?slug=' . rawurlencode($project['slug']);
$pageOgType = 'article';
$pageBreadcrumbs = [
    ['name' => 'Home', 'url' => site_url('')],
    ['name' => 'Portfolio', 'url' => site_url('portfolio')],
    ['name' => $project['title'], 'url' => $pageCanonical],
];

$imgPath = (string) ($project['image'] ?? '');
$webpPath = portfolio_webp_path($imgPath);
[$imgW, $imgH] = portfolio_image_size($webpPath !== '' ? $webpPath : $imgPath);
if ($imgPath !== '') {
    $pageOgImage = site_url(implode('/', array_map('rawurlencode', explode('/', ltrim($imgPath, '/')))));
    $pageOgImageAlt = $imageAlt;
    [$pageOgImageWidth, $pageOgImageHeight] = portfolio_image_size($imgPath);
    $pagePreloadImage = $webpPath !== '' ? $webpPath : $imgPath;
}

include 'header.php';
?>

  <div class="optech-breadcrumb nz-breadcrumb">
    <div class="container">
      <h1 class="post__title"><?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
      <nav class="breadcrumbs" aria-label="Breadcrumb">
        <ul>
          <li><a href="./">Home</a></li>
          <li><a href="portfolio">Portfolio</a></li>
          <li aria-current="page"><?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?></li>
        </ul>
      </nav>
    </div>
  </div>

  <section class="nz-about-block">
    <div class="container">
      <div class="nz-case">
        <div>
          <?php if ($imgPath !== '') : ?>
            <picture>
              <?php if ($webpPath !== '') : ?>
                <source srcset="<?php echo htmlspecialchars($webpPath, ENT_QUOTES, 'UTF-8'); ?>" type="image/webp">
              <?php endif; ?>
              <img class="nz-case-cover" src="<?php echo htmlspecialchars($imgPath, ENT_QUOTES, 'UTF-8'); ?>" fetchpriority="high" decoding="async" alt="<?php echo htmlspecialchars($imageAlt, ENT_QUOTES, 'UTF-8'); ?>"<?php echo $imgW > 0 ? ' width="' . $imgW . '" height="' . $imgH . '"' : ''; ?>>
            </picture>
          <?php else : ?>
            <div class="nz-case-cover nz-work-fallback"><?php echo htmlspecialchars($project['category'], ENT_QUOTES, 'UTF-8'); ?></div>
          <?php endif; ?>

          <div class="nz-case-copy">
            <span class="nz-kicker"><?php echo htmlspecialchars($kindLabel, ENT_QUOTES, 'UTF-8'); ?></span>
            <h2 class="nz-title"><?php echo htmlspecialchars($isSample ? 'About this sample case study' : 'Project overview', ENT_QUOTES, 'UTF-8'); ?></h2>
            <?php if ($isSample) : ?>
              <p class="nz-case-note"><strong>Sample project:</strong> this is a demonstration case study, not work for a named client. It shows how we would scope and build a similar <?php echo htmlspecialchars(strtolower(portfolio_kind_label(['category' => $project['category']])), ENT_QUOTES, 'UTF-8'); ?>.</p>
            <?php endif; ?>
            <p class="nz-about-lead"><?php echo htmlspecialchars($project['overview'], ENT_QUOTES, 'UTF-8'); ?></p>

            <?php if (!empty($project['challenge'])) : ?>
              <h3>The challenge</h3>
              <p><?php echo htmlspecialchars($project['challenge'], ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>

            <?php if ($features) : ?>
              <h3>What we delivered</h3>
              <ul class="nz-case-list">
                <?php foreach ($features as $feature) : ?>
                  <li><?php echo htmlspecialchars($feature, ENT_QUOTES, 'UTF-8'); ?></li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>

            <?php if (!empty($project['result'])) : ?>
              <h3><?php echo $isSample ? 'What the sample covers' : 'The result'; ?></h3>
              <p><?php echo htmlspecialchars($project['result'], ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
          </div>
        </div>

        <aside class="nz-case-side">
          <div class="nz-case-facts">
            <h3>Project details</h3>
            <?php if (!empty($project['client_name'])) : ?>
              <p><span><?php echo $isSample ? 'Brief' : 'Client'; ?></span><?php echo htmlspecialchars($project['client_name'], ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <p><span>Type</span><?php echo htmlspecialchars($kindLabel, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php if (!empty($project['project_date'])) : ?>
              <p><span>Date</span><?php echo htmlspecialchars($project['project_date'], ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <?php if (!empty($project['live_url']) && preg_match('#^https?://#i', (string) $project['live_url'])) : ?>
              <a class="nz-header-btn" href="<?php echo htmlspecialchars($project['live_url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">Visit live site</a>
            <?php endif; ?>
          </div>
          <div class="nz-case-next">
            <h3>Related services</h3>
            <ul class="nz-case-links">
              <li><a href="<?php echo htmlspecialchars($service['url'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($service['label'], ENT_QUOTES, 'UTF-8'); ?></a></li>
              <li><a href="pricing">Starting plans and pricing</a></li>
              <li><a href="portfolio">All portfolio projects</a></li>
            </ul>
          </div>
          <div class="nz-case-next">
            <h3>Start a similar project</h3>
            <p>Share a short brief and we will map the first version, timeline, and <a href="pricing">starting plan</a> with our Kolkata team.</p>
            <div class="nz-case-cta-actions">
              <a class="nz-header-btn" href="contact">Request a Quote</a>
              <a class="nz-addon-link" href="<?php echo site_whatsapp_url(); ?>" target="_blank" rel="noopener">Chat on WhatsApp</a>
            </div>
          </div>
        </aside>
      </div>

      <?php if ($related) : ?>
        <div class="nz-case-related">
          <h2 class="nz-title">More <?php echo htmlspecialchars($project['category'], ENT_QUOTES, 'UTF-8'); ?> work</h2>
          <div class="nz-work-grid">
            <?php foreach ($related as $item) : ?>
              <?php
                $relImg = (string) ($item['image'] ?? '');
                $relWebp = portfolio_webp_path($relImg);
                [$relW, $relH] = portfolio_image_size($relWebp !== '' ? $relWebp : $relImg);
                $relUrl = 'portfolio_details?slug=' . rawurlencode($item['slug']);
              ?>
              <article class="nz-work-card">
                <a class="nz-work-media" href="<?php echo $relUrl; ?>" tabindex="-1" aria-hidden="true">
                  <?php if ($relImg !== '') : ?>
                    <picture>
                      <?php if ($relWebp !== '') : ?>
                        <source srcset="<?php echo htmlspecialchars($relWebp, ENT_QUOTES, 'UTF-8'); ?>" type="image/webp">
                      <?php endif; ?>
                      <img src="<?php echo htmlspecialchars($relImg, ENT_QUOTES, 'UTF-8'); ?>" loading="lazy" decoding="async" alt="<?php echo htmlspecialchars(portfolio_image_alt($item), ENT_QUOTES, 'UTF-8'); ?>"<?php echo $relW > 0 ? ' width="' . $relW . '" height="' . $relH . '"' : ''; ?>>
                    </picture>
                  <?php else : ?>
                    <span class="nz-work-fallback"><?php echo htmlspecialchars($item['category'], ENT_QUOTES, 'UTF-8'); ?></span>
                  <?php endif; ?>
                </a>
                <div class="nz-work-body">
                  <?php if (portfolio_is_sample($item)) : ?>
                    <div class="nz-work-tags"><span class="nz-work-badge is-sample">Sample project</span></div>
                  <?php endif; ?>
                  <h3><a href="<?php echo $relUrl; ?>"><?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?></a></h3>
                  <p><?php echo htmlspecialchars($item['summary'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </section>

<?php include 'footer.php'; ?>
