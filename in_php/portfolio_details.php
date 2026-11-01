<?php
require_once __DIR__ . '/includes/portfolio_app.php';

$slug = trim((string) ($_GET['slug'] ?? $_GET['id'] ?? ''));
$project = $slug !== '' ? portfolio_get_by_slug($slug) : null;

if (!$project) {
    header('Location: portfolio');
    exit;
}

$features = portfolio_features($project);
$related = array_values(array_filter(portfolio_published($project['category']), function ($item) use ($project) {
    return (int) $item['id'] !== (int) $project['id'];
}));
$related = array_slice($related, 0, 3);

$pageTitle = $project['title'] . ' | Portfolio | ' . site_company_name();
$pageDescription = $project['summary'];

include 'header.php';
?>

  <div class="optech-breadcrumb nz-breadcrumb">
    <div class="container">
      <h1 class="post__title"><?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
      <nav class="breadcrumbs">
        <ul>
          <li><a href="index">Home</a></li>
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
          <?php if (!empty($project['image'])) : ?>
            <img class="nz-case-cover" src="<?php echo htmlspecialchars($project['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?>">
          <?php else : ?>
            <div class="nz-case-cover nz-work-fallback"><?php echo htmlspecialchars($project['category'], ENT_QUOTES, 'UTF-8'); ?></div>
          <?php endif; ?>

          <div class="nz-case-copy">
            <span class="nz-kicker"><?php echo htmlspecialchars($project['category'], ENT_QUOTES, 'UTF-8'); ?></span>
            <h2 class="nz-title"><?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
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
              <h3>The result</h3>
              <p><?php echo htmlspecialchars($project['result'], ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
          </div>
        </div>

        <aside class="nz-case-side">
          <div class="nz-case-facts">
            <h3>Project details</h3>
            <?php if (!empty($project['client_name'])) : ?>
              <p><span>Client</span><?php echo htmlspecialchars($project['client_name'], ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <p><span>Category</span><?php echo htmlspecialchars($project['category'], ENT_QUOTES, 'UTF-8'); ?></p>
            <?php if (!empty($project['project_date'])) : ?>
              <p><span>Date</span><?php echo htmlspecialchars($project['project_date'], ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <?php if (!empty($project['live_url'])) : ?>
              <a class="nz-header-btn" href="<?php echo htmlspecialchars($project['live_url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">Open live demo</a>
            <?php endif; ?>
          </div>
          <div class="nz-case-next">
            <h3>Start a similar project</h3>
            <p>Share a short brief and we will map the first version, timeline, and starting plan.</p>
            <a class="nz-addon-link" href="contact">Contact Nazora Tech</a>
          </div>
        </aside>
      </div>

      <?php if ($related) : ?>
        <div class="nz-case-related">
          <h2 class="nz-title">More <?php echo htmlspecialchars($project['category'], ENT_QUOTES, 'UTF-8'); ?> work</h2>
          <div class="nz-work-grid">
            <?php foreach ($related as $item) : ?>
              <article class="nz-work-card">
                <a class="nz-work-media" href="portfolio_details?slug=<?php echo rawurlencode($item['slug']); ?>">
                  <?php if (!empty($item['image'])) : ?>
                    <img src="<?php echo htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?>">
                  <?php else : ?>
                    <span class="nz-work-fallback"><?php echo htmlspecialchars($item['category'], ENT_QUOTES, 'UTF-8'); ?></span>
                  <?php endif; ?>
                </a>
                <div class="nz-work-body">
                  <h3><a href="portfolio_details?slug=<?php echo rawurlencode($item['slug']); ?>"><?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?></a></h3>
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
