<?php
require_once __DIR__ . '/../includes/blog_app.php';

if (!blog_current_user()) {
    header('Location: login.php');
    exit;
}

header('Location: dashboard.php');
exit;
