<?php
require_once __DIR__ . '/includes/careers_app.php';
$jobs = careers_published();
include 'header.php';
?>

  <div class="optech-breadcrumb nz-breadcrumb">
    <div class="container">
      <h1 class="post__title">Careers</h1>
      <nav class="breadcrumbs">
        <ul>
          <li><a href="index">Home</a></li>
          <li aria-current="page">Careers</li>
        </ul>
      </nav>
    </div>
  </div>

  <div class="section optech-section-padding">
    <div class="container">
      <div class="nz-page-intro text-center mb-5">
        <span class="nz-kicker">We're Hiring</span>
        <h2 class="nz-title">Build your career with us</h2>
        <p class="nz-lead">Join a Kolkata-based team shipping websites, apps, software, and AI automation for real businesses. We value clear communication, craft, and ownership.</p>
      </div>

      <div class="row g-4">
        <?php if (!$jobs) : ?>
          <div class="col-12 text-center">
            <p class="nz-lead mb-4">No open roles right now — send your profile and we will keep you in mind.</p>
            <a class="nz-header-btn" href="mailto:<?php echo htmlspecialchars(site_contact_email(), ENT_QUOTES, 'UTF-8'); ?>">Email Your CV</a>
          </div>
        <?php else : ?>
          <?php foreach ($jobs as $job) : ?>
            <div class="col-md-6 col-lg-4">
              <article class="nz-job-card">
                <span class="nz-job-tag"><?php echo htmlspecialchars($job['employment_type'], ENT_QUOTES, 'UTF-8'); ?></span>
                <h3><?php echo htmlspecialchars($job['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <p><?php echo htmlspecialchars($job['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                <ul>
                  <?php if (!empty($job['location'])) : ?>
                    <li><?php echo htmlspecialchars($job['location'], ENT_QUOTES, 'UTF-8'); ?></li>
                  <?php endif; ?>
                  <?php if (!empty($job['experience'])) : ?>
                    <li><?php echo htmlspecialchars($job['experience'], ENT_QUOTES, 'UTF-8'); ?></li>
                  <?php endif; ?>
                </ul>
                <a class="nz-addon-link" href="<?php echo htmlspecialchars(careers_apply_href((string) ($job['apply_url'] ?? 'contact')), ENT_QUOTES, 'UTF-8'); ?>">Apply Now</a>
              </article>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <div class="nz-page-cta mt-5 text-center">
        <h3>Don't see the right role?</h3>
        <p>Send your profile anyway — we hire for attitude and growth potential.</p>
        <a class="nz-header-btn" href="mailto:<?php echo htmlspecialchars(site_contact_email(), ENT_QUOTES, 'UTF-8'); ?>">Email Your CV</a>
      </div>
    </div>
  </div>

<?php include 'footer.php'; ?>
