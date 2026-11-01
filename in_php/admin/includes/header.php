<?php
$flashMessage = blog_get_flash();
$currentUser = blog_current_user();
$pageTitle = $pageTitle ?? 'Admin Panel';
$pageSubtitle = $pageSubtitle ?? 'Manage careers and site content.';
$activeMenu = $activeMenu ?? '';

$navItems = [
    ['id' => 'dashboard', 'label' => 'Dashboard', 'href' => 'dashboard.php', 'icon' => 'ri-dashboard-3-line'],
    ['id' => 'careers', 'label' => 'Careers', 'href' => 'careers.php', 'icon' => 'ri-briefcase-4-line'],
    ['id' => 'portfolio', 'label' => 'Portfolio', 'href' => 'portfolio.php', 'icon' => 'ri-gallery-line'],
];
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <?php include __DIR__ . '/head.php'; ?>
</head>
<body class="admin-app">
  <div class="admin-shell" id="adminShell">
    <div class="admin-overlay" data-sidebar-backdrop aria-hidden="true"></div>

    <aside class="admin-sidebar" id="adminSidebar">
      <div class="admin-sidebar-inner">
        <a href="dashboard.php" class="admin-brand">
          <img src="../assets/images/logo/nazora-logo.png" alt="<?php echo blog_escape(site_company_name()); ?>" class="admin-brand-logo">
          <div class="admin-brand-text">
            <strong>Nazora TECH</strong>
            <span>Admin Panel</span>
          </div>
        </a>

        <div class="admin-nav-group">
          <span class="admin-nav-label">Main Menu</span>
          <nav class="admin-nav">
            <?php foreach ($navItems as $item) : ?>
              <a href="<?php echo blog_escape($item['href']); ?>" class="admin-nav-link<?php echo $activeMenu === $item['id'] ? ' is-active' : ''; ?>">
                <span class="admin-nav-icon"><i class="<?php echo blog_escape($item['icon']); ?>"></i></span>
                <span class="admin-nav-text"><?php echo blog_escape($item['label']); ?></span>
                <i class="ri-arrow-right-s-line admin-nav-arrow"></i>
              </a>
            <?php endforeach; ?>
          </nav>
        </div>

        <div class="admin-nav-group">
          <span class="admin-nav-label">Quick Actions</span>
          <nav class="admin-nav admin-nav--compact">
            <a href="career-form.php" class="admin-nav-link admin-nav-link--cta">
              <span class="admin-nav-icon"><i class="ri-briefcase-line"></i></span>
              <span class="admin-nav-text">New Career Opening</span>
            </a>
            <a href="portfolio-form.php" class="admin-nav-link">
              <span class="admin-nav-icon"><i class="ri-gallery-line"></i></span>
              <span class="admin-nav-text">New Portfolio Project</span>
            </a>
            <a href="<?php echo blog_escape(blog_url('index.php')); ?>" class="admin-nav-link" target="_blank" rel="noopener">
              <span class="admin-nav-icon"><i class="ri-external-link-line"></i></span>
              <span class="admin-nav-text">View Website</span>
            </a>
          </nav>
        </div>

        <div class="admin-sidebar-footer">
          <div class="admin-sidebar-tip">
            <i class="ri-lightbulb-flash-line"></i>
            <div>
              <strong>Pro tip</strong>
              <p>Published career openings appear on the public Careers page.</p>
            </div>
          </div>
        </div>
      </div>
    </aside>

    <div class="admin-main">
      <header class="admin-header">
        <div class="admin-header-left">
          <button class="admin-header-btn admin-menu-btn" type="button" data-sidebar-toggle aria-label="Open menu">
            <i class="ri-menu-line"></i>
          </button>
          <div class="admin-page-head">
            <h1><?php echo blog_escape($pageTitle); ?></h1>
            <p><?php echo blog_escape($pageSubtitle); ?></p>
          </div>
        </div>
        <div class="admin-header-right">
          <button class="admin-header-btn" type="button" data-theme-toggle aria-label="Toggle theme">
            <i class="ri-moon-line"></i>
          </button>
          <div class="admin-user">
            <div class="admin-user-avatar"><?php echo blog_escape(strtoupper(substr($currentUser['name'] ?? 'A', 0, 1))); ?></div>
            <div class="admin-user-info">
              <strong><?php echo blog_escape($currentUser['name'] ?? 'Administrator'); ?></strong>
              <small><?php echo blog_escape($currentUser['email'] ?? ''); ?></small>
            </div>
          </div>
          <a class="admin-header-btn admin-header-btn--danger" href="logout.php" title="Logout">
            <i class="ri-logout-box-r-line"></i>
            <span class="d-none d-lg-inline">Logout</span>
          </a>
        </div>
      </header>

      <main class="admin-content">
        <?php if ($flashMessage) : ?>
          <div class="admin-toast admin-toast--<?php echo blog_escape($flashMessage['type']); ?>" data-auto-dismiss>
            <i class="ri-<?php echo $flashMessage['type'] === 'success' ? 'checkbox-circle' : 'error-warning'; ?>-line"></i>
            <span><?php echo blog_escape($flashMessage['message']); ?></span>
          </div>
        <?php endif; ?>
