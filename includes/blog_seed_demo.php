<?php

function blog_seed_placeholder_image(string $slug, int $variant = 0): ?string
{
    $directory = blog_upload_dir('blog');
    blog_ensure_directory($directory);

    $fileName = blog_slugify($slug) . '-' . $variant . '-' . bin2hex(random_bytes(4)) . '.jpg';
    $targetPath = $directory . '/' . $fileName;
    $relativePath = 'storage/uploads/blog/' . $fileName;

    $logoPath = blog_root_path() . '/assets/images/logo/header_logo-removebg.png';
    if ($variant === 0 && is_file($logoPath)) {
        if (@copy($logoPath, $targetPath)) {
            return $relativePath;
        }
    }

    if (!function_exists('imagecreatetruecolor')) {
        return is_file($logoPath) && @copy($logoPath, $targetPath) ? $relativePath : null;
    }

    $width = 1200;
    $height = 675;
    $image = imagecreatetruecolor($width, $height);
    $hash = crc32($slug . (string) $variant);
    $r = 40 + ($hash % 160);
    $g = 60 + (($hash >> 8) % 140);
    $b = 90 + (($hash >> 16) % 120);
    $bg = imagecolorallocate($image, $r, $g, $b);
    imagefilledrectangle($image, 0, 0, $width, $height, $bg);

    $accent = imagecolorallocate($image, min($r + 40, 255), min($g + 40, 255), min($b + 40, 255));
    imagefilledrectangle($image, 0, (int) ($height * 0.72), $width, $height, $accent);

    $white = imagecolorallocate($image, 255, 255, 255);
    $label = substr(str_replace('-', ' ', $slug), 0, 48);
    imagestring($image, 5, 36, 36, SITE_COMPANY_BLOG, $white);
    imagestring($image, 4, 36, 68, $label, $white);

    imagejpeg($image, $targetPath, 86);
    imagedestroy($image);

    return $relativePath;
}

function blog_seed_paragraphs(string $topic, string $category, int $paragraphs = 8): string
{
    $sentences = [
        "Organizations in the {$category} space are rethinking how {$topic} supports measurable growth across every customer touchpoint.",
        'Leadership teams now expect faster reporting cycles, clearer attribution, and stronger alignment between marketing, sales, and product delivery.',
        'A practical roadmap starts with baseline audits, stakeholder interviews, and a prioritized backlog tied to revenue outcomes.',
        'When execution is coordinated, companies reduce wasted spend, improve conversion rates, and build durable brand trust in competitive markets.',
        'Modern buyers research extensively before contacting sales, which means educational content, social proof, and technical clarity are essential.',
        'Teams that document processes, automate repetitive tasks, and review performance weekly consistently outperform peers who rely on ad hoc campaigns.',
        'Security, accessibility, and performance should be treated as business requirements rather than late-stage technical checkboxes.',
        'Cross-functional workshops help uncover friction in onboarding, checkout, support, and retention journeys that analytics alone may miss.',
        'Investing in training empowers internal champions to maintain quality after the initial project launch or platform migration.',
        'Clear KPIs such as qualified leads, cost per acquisition, lifetime value, and engagement depth keep initiatives accountable.',
        'Pilot programs reduce risk by validating assumptions on a small audience before scaling budgets or engineering effort.',
        'Partners who understand industry regulations, local market behavior, and enterprise procurement cycles deliver more resilient outcomes.',
    ];

    $html = '<p>' . str_replace(['{topic}', '{category}'], [$topic, $category], $sentences[0]) . '</p>';
    $used = [0];

    for ($i = 1; $i < $paragraphs; $i++) {
        do {
            $index = array_rand($sentences);
        } while (in_array($index, $used, true) && count($used) < count($sentences));
        $used[] = $index;

        if ($i % 3 === 0) {
            $html .= '<h2>' . htmlspecialchars(ucfirst($topic) . ' insight ' . (int) ($i / 3), ENT_QUOTES, 'UTF-8') . '</h2>';
        }

        $line = str_replace(['{topic}', '{category}'], [$topic, $category], $sentences[$index]);
        $html .= '<p>' . $line . ' ' . $sentences[($index + 3) % count($sentences)] . ' ' . $sentences[($index + 6) % count($sentences)] . '</p>';
    }

    $html .= '<h2>Key takeaways</h2><ul>';
    foreach (array_slice($sentences, 0, 4) as $sentence) {
        $html .= '<li>' . str_replace(['{topic}', '{category}'], [$topic, $category], $sentence) . '</li>';
    }
    $html .= '</ul>';

    return $html;
}

function blog_seed_demo_posts(): array
{
    $posts = [
        ['title' => 'How to Build a High-Converting Digital Marketing Funnel in 2026', 'category' => 'digital-marketing', 'tags' => 'Digital Marketing, Conversion, Funnel'],
        ['title' => 'Social Media Advertising Strategies That Actually Drive ROI', 'category' => 'digital-marketing', 'tags' => 'Social Media, Paid Ads, ROI'],
        ['title' => 'Email Marketing Automation for B2B Lead Nurturing', 'category' => 'digital-marketing', 'tags' => 'Email Marketing, Automation, B2B'],
        ['title' => 'Technical SEO Checklist for Modern Business Websites', 'category' => 'seo-services', 'tags' => 'SEO, Technical SEO, Website'],
        ['title' => 'Local SEO Playbook for Service-Based Companies', 'category' => 'seo-services', 'tags' => 'Local SEO, Google Business, Leads'],
        ['title' => 'Core Web Vitals Optimization Guide for Developers', 'category' => 'seo-services', 'tags' => 'Core Web Vitals, Performance, SEO'],
        ['title' => 'Why Responsive Web Design Still Matters for Enterprise Brands', 'category' => 'web-development', 'tags' => 'Web Design, Responsive, UX'],
        ['title' => 'Choosing the Right CMS for Scalable Business Growth', 'category' => 'web-development', 'tags' => 'CMS, Web Development, Scale'],
        ['title' => 'Headless Architecture Explained for Marketing Teams', 'category' => 'web-development', 'tags' => 'Headless CMS, API, Marketing'],
        ['title' => 'Native vs Cross-Platform Mobile Apps: A Practical Comparison', 'category' => 'mobile-app-development', 'tags' => 'Mobile Apps, iOS, Android'],
        ['title' => 'Mobile App UX Patterns That Improve User Retention', 'category' => 'mobile-app-development', 'tags' => 'UX, Retention, Mobile'],
        ['title' => 'Push Notifications Best Practices for Mobile Apps', 'category' => 'mobile-app-development', 'tags' => 'Push Notifications, Engagement, Apps'],
        ['title' => 'Custom Software Solutions vs Off-the-Shelf Products', 'category' => 'software-solutions', 'tags' => 'Software, Enterprise, SaaS'],
        ['title' => 'How APIs Power Modern Business Automation', 'category' => 'software-solutions', 'tags' => 'API, Integration, Automation'],
        ['title' => 'Cloud Migration Planning for Growing Businesses', 'category' => 'software-solutions', 'tags' => 'Cloud, Migration, Infrastructure'],
        ['title' => 'AI Adoption Trends Shaping the Digital Workplace in 2026', 'category' => 'technology-news', 'tags' => 'AI, Technology, Workplace'],
        ['title' => 'Cybersecurity Priorities Every SMB Should Address This Year', 'category' => 'technology-news', 'tags' => 'Cybersecurity, SMB, Risk'],
        ['title' => 'Data Privacy Regulations: What Marketers Need to Know', 'category' => 'technology-news', 'tags' => 'Privacy, Compliance, Marketing'],
        ['title' => 'How to Align Your Digital Strategy With Revenue Goals', 'category' => 'business-tips', 'tags' => 'Strategy, Revenue, Leadership'],
        ['title' => 'Building Customer Trust Through Transparent Online Presence', 'category' => 'business-tips', 'tags' => 'Trust, Branding, Customer Experience'],
        ['title' => 'Scaling a Remote Team With the Right Digital Tools', 'category' => 'business-tips', 'tags' => 'Remote Work, Productivity, Tools'],
    ];

    $baseUrl = blog_base_url();
    $enriched = [];

    foreach ($posts as $index => $post) {
        $slug = blog_slugify($post['title']);
        $topic = strtolower($post['category']);
        $categoryLabel = ucwords(str_replace('-', ' ', $post['category']));
        $excerpt = 'Explore practical guidance on ' . strtolower($post['title']) . ' with actionable steps for modern businesses.';
        $seoTitle = mb_substr($post['title'], 0, 60);
        $metaDescription = mb_substr($excerpt . ' Learn frameworks, metrics, and execution tips from ' . SITE_COMPANY_NAME . '.', 0, 160);
        $canonical = rtrim($baseUrl, '/') . '/blog/' . $slug;

        $enriched[] = array_merge($post, [
            'slug' => $slug,
            'excerpt' => $excerpt,
            'content' => blog_seed_paragraphs($topic, $categoryLabel, 8),
            'seo_title' => $seoTitle,
            'meta_description' => $metaDescription,
            'meta_keywords' => str_replace(', ', ', ', $post['tags']),
            'canonical_url' => $canonical,
            'og_title' => $seoTitle,
            'og_description' => $metaDescription,
            'twitter_card' => 'summary_large_image',
            'schema_markup' => json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'BlogPosting',
                'headline' => $post['title'],
                'description' => $metaDescription,
                'author' => ['@type' => 'Organization', 'name' => SITE_COMPANY_NAME],
                'publisher' => ['@type' => 'Organization', 'name' => SITE_COMPANY_NAME],
            ], JSON_UNESCAPED_SLASHES),
            'featured_image_alt' => $post['title'] . ' featured image',
            'days_ago' => 3 + ($index * 2),
        ]);
    }

    return $enriched;
}

function blog_run_demo_seed(bool $force = false): array
{
    $pdo = blog_db();
    $result = ['categories' => 0, 'tags' => 0, 'posts' => 0, 'skipped' => 0, 'errors' => []];

    if (!$force && blog_setting_get('demo_data_seeded') === '1') {
        $result['skipped'] = 1;
        $result['message'] = 'Demo data already exists. Use force reseed to run again.';

        return $result;
    }

    $categories = [
        ['name' => 'Digital Marketing', 'slug' => 'digital-marketing', 'description' => 'Campaign strategy, paid media, and growth marketing insights.'],
        ['name' => 'SEO Services', 'slug' => 'seo-services', 'description' => 'Search optimization, technical SEO, and organic visibility.'],
        ['name' => 'Web Development', 'slug' => 'web-development', 'description' => 'Website engineering, UX, and platform architecture.'],
        ['name' => 'Mobile App Development', 'slug' => 'mobile-app-development', 'description' => 'iOS, Android, and cross-platform application delivery.'],
        ['name' => 'Software Solutions', 'slug' => 'software-solutions', 'description' => 'Custom software, integrations, and enterprise systems.'],
        ['name' => 'Technology News', 'slug' => 'technology-news', 'description' => 'Industry updates, security, and emerging technology.'],
        ['name' => 'Business Tips', 'slug' => 'business-tips', 'description' => 'Operations, leadership, and digital business strategy.'],
    ];

    $categoryIds = [];
    foreach ($categories as $category) {
        $stmt = $pdo->prepare('SELECT id FROM categories WHERE slug = :slug AND deleted_at IS NULL LIMIT 1');
        $stmt->execute([':slug' => $category['slug']]);
        $existingId = $stmt->fetchColumn();

        if ($existingId) {
            $categoryIds[$category['slug']] = (int) $existingId;
            continue;
        }

        $save = blog_save_category($pdo, $category);
        if ($save['success']) {
            $categoryIds[$category['slug']] = (int) $save['id'];
            $result['categories']++;
        }
    }

    $tagPool = ['SEO', 'Marketing', 'Web Design', 'Mobile Apps', 'Automation', 'Analytics', 'Cloud', 'Security', 'UX', 'Strategy', 'Branding', 'Performance'];
    $tagIds = [];
    foreach ($tagPool as $tagName) {
        $slug = blog_slugify($tagName);
        $stmt = $pdo->prepare('SELECT id FROM tags WHERE slug = :slug AND deleted_at IS NULL LIMIT 1');
        $stmt->execute([':slug' => $slug]);
        $existingId = $stmt->fetchColumn();

        if ($existingId) {
            $tagIds[$slug] = (int) $existingId;
            continue;
        }

        $save = blog_save_tag($pdo, ['name' => $tagName]);
        if ($save['success']) {
            $tagIds[$slug] = (int) $save['id'];
            $result['tags']++;
        }
    }

    $authorStmt = $pdo->query('SELECT id FROM users ORDER BY id ASC LIMIT 1');
    $authorId = (int) ($authorStmt->fetchColumn() ?: 0);

    $posts = blog_seed_demo_posts();
    $insertPost = $pdo->prepare('INSERT INTO posts (
        title, slug, excerpt, content, status, category_id, author_id,
        featured_image, featured_image_alt, seo_title, meta_description, meta_keywords,
        canonical_url, og_title, og_description, twitter_card, schema_markup,
        published_at, created_at, updated_at
    ) VALUES (
        :title, :slug, :excerpt, :content, :status, :category_id, :author_id,
        :featured_image, :featured_image_alt, :seo_title, :meta_description, :meta_keywords,
        :canonical_url, :og_title, :og_description, :twitter_card, :schema_markup,
        :published_at, :created_at, :updated_at
    )');

    $insertGallery = $pdo->prepare('INSERT INTO post_images (post_id, image_path, alt_text, sort_order, created_at) VALUES (:post_id, :image_path, :alt_text, :sort_order, :created_at)');
    $insertPostTag = $pdo->prepare('INSERT OR IGNORE INTO post_tags (post_id, tag_id, created_at) VALUES (:post_id, :tag_id, :created_at)');

    foreach ($posts as $post) {
        $check = $pdo->prepare('SELECT id FROM posts WHERE slug = :slug AND deleted_at IS NULL LIMIT 1');
        $check->execute([':slug' => $post['slug']]);
        if ($check->fetchColumn()) {
            $result['skipped']++;
            continue;
        }

        $featured = blog_seed_placeholder_image($post['slug'], 0);
        $galleryOne = blog_seed_placeholder_image($post['slug'], 1);
        $galleryTwo = blog_seed_placeholder_image($post['slug'], 2);

        $publishedAt = date('Y-m-d H:i:s', strtotime('-' . (int) $post['days_ago'] . ' days'));
        $now = blog_now();

        $pdo->beginTransaction();
        try {
            $insertPost->execute([
                ':title' => $post['title'],
                ':slug' => $post['slug'],
                ':excerpt' => $post['excerpt'],
                ':content' => $post['content'],
                ':status' => 'published',
                ':category_id' => $categoryIds[$post['category']] ?? null,
                ':author_id' => $authorId ?: null,
                ':featured_image' => $featured,
                ':featured_image_alt' => $post['featured_image_alt'],
                ':seo_title' => $post['seo_title'],
                ':meta_description' => $post['meta_description'],
                ':meta_keywords' => $post['meta_keywords'],
                ':canonical_url' => $post['canonical_url'],
                ':og_title' => $post['og_title'],
                ':og_description' => $post['og_description'],
                ':twitter_card' => $post['twitter_card'],
                ':schema_markup' => $post['schema_markup'],
                ':published_at' => $publishedAt,
                ':created_at' => $publishedAt,
                ':updated_at' => $now,
            ]);

            $postId = (int) $pdo->lastInsertId();

            foreach (array_filter([$galleryOne, $galleryTwo]) as $sort => $imagePath) {
                $insertGallery->execute([
                    ':post_id' => $postId,
                    ':image_path' => $imagePath,
                    ':alt_text' => $post['title'] . ' gallery ' . ($sort + 1),
                    ':sort_order' => $sort,
                    ':created_at' => $now,
                ]);
            }

            foreach (array_filter(array_map('trim', explode(',', $post['tags']))) as $tagName) {
                $tagSlug = blog_slugify($tagName);
                if (!isset($tagIds[$tagSlug])) {
                    $save = blog_save_tag($pdo, ['name' => $tagName]);
                    if ($save['success']) {
                        $tagIds[$tagSlug] = (int) $save['id'];
                    }
                }
                if (isset($tagIds[$tagSlug])) {
                    $insertPostTag->execute([
                        ':post_id' => $postId,
                        ':tag_id' => $tagIds[$tagSlug],
                        ':created_at' => $now,
                    ]);
                }
            }

            $pdo->commit();
            $result['posts']++;
        } catch (Throwable $e) {
            $pdo->rollBack();
            $result['errors'][] = $post['title'] . ': ' . $e->getMessage();
        }
    }

    blog_setting_set('demo_data_seeded', '1');
    $result['message'] = sprintf(
        'Seeded %d categories, %d tags, and %d blog posts.',
        $result['categories'],
        $result['tags'],
        $result['posts']
    );

    return $result;
}

function blog_reset_demo_seed(): void
{
    $pdo = blog_db();
    $now = blog_now();
    $stmt = $pdo->prepare('UPDATE posts SET deleted_at = :deleted_at, updated_at = :updated_at WHERE deleted_at IS NULL');
    $stmt->execute([':deleted_at' => $now, ':updated_at' => $now]);
    blog_setting_set('demo_data_seeded', '0');
}
