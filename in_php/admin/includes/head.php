<?php
$pageTitle = $pageTitle ?? 'Admin Panel';
$adminCssVersion = @filemtime(__DIR__ . '/../assets/css/admin.css') ?: time();
?>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title><?php echo blog_escape($pageTitle); ?> | <?php echo blog_escape(site_company_name()); ?> Admin</title>
<link rel="stylesheet" href="../assets/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css">
<link rel="stylesheet" href="assets/css/admin.css?v=<?php echo (int) $adminCssVersion; ?>">
