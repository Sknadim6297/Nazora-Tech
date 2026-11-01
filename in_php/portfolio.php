<?php
require_once __DIR__ . '/includes/portfolio_app.php';

$activeCategory = trim((string) ($_GET['category'] ?? ''));
if ($activeCategory !== '' && !in_array($activeCategory, portfolio_categories(), true)) {
    $activeCategory = '';
}
$projects = portfolio_published($activeCategory !== '' ? $activeCategory : null);

include 'header.php';
?>

  <div class="optech-breadcrumb nz-breadcrumb">
    <div class="container">
      <h1 class="post__title">Portfolio</h1>
      <nav class="breadcrumbs">
        <ul>
          <li><a href="index">Home</a></li>
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
        <p class="nz-lead">A look at live client work and sample case studies. Each card opens a short story of the problem, the build, and the result.</p>
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
          <?php foreach ($projects as $project) : ?>
            <article class="nz-work-card">
              <a class="nz-work-media" href="portfolio_details?slug=<?php echo rawurlencode($project['slug']); ?>">
                <?php if (!empty($project['image'])) : ?>
                  <img src="<?php echo htmlspecialchars($project['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?>">
                <?php else : ?>
                  <span class="nz-work-fallback"><?php echo htmlspecialchars($project['category'], ENT_QUOTES, 'UTF-8'); ?></span>
                <?php endif; ?>
              </a>
              <div class="nz-work-body">
                <span><?php echo htmlspecialchars($project['category'], ENT_QUOTES, 'UTF-8'); ?></span>
                <h3><a href="portfolio_details?slug=<?php echo rawurlencode($project['slug']); ?>"><?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?></a></h3>
                <p><?php echo htmlspecialchars($project['summary'], ENT_QUOTES, 'UTF-8'); ?></p>
                <div class="nz-work-actions">
                  <a class="nz-addon-link" href="portfolio_details?slug=<?php echo rawurlencode($project['slug']); ?>">View details</a>
                  <?php if (!empty($project['live_url'])) : ?>
                    <a class="nz-about-link" href="<?php echo htmlspecialchars($project['live_url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">Live demo</a>
                  <?php endif; ?>
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
          <p>Tell us what you want to launch. We will reply with a practical scope and the right starting plan.</p>
        </div>
        <a class="nz-header-btn" href="contact">Start a project</a>
      </div>
    </div>
  </section>

<?php include 'footer.php'; ?>
