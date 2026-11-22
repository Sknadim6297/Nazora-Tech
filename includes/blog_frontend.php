<?php

function blog_format_date(?string $date): string
{
    if (!$date) {
        return '';
    }

    return date('d M Y', strtotime($date));
}

function blog_render_page_breadcrumb(string $title, array $items): void
{
    ?>
    <div class="optech-breadcrumb nz-breadcrumb">
      <div class="container">
        <h1 class="post__title"><?php echo blog_escape($title); ?></h1>
        <nav class="breadcrumbs">
          <ul>
            <?php foreach ($items as $index => $item) : ?>
              <?php $isLast = $index === array_key_last($items); ?>
              <li>
                <?php if (!$isLast && !empty($item['url'])) : ?>
                  <a href="<?php echo blog_escape($item['url']); ?>"><?php echo blog_escape($item['label']); ?></a>
                <?php else : ?>
                  <?php echo blog_escape($item['label']); ?>
                <?php endif; ?>
              </li>
            <?php endforeach; ?>
          </ul>
        </nav>
      </div>
    </div>
    <?php
}

function blog_render_post_card(array $post, string $layout = 'grid'): void
{
    $postUrl = blog_post_url($post['slug']);
    $imageUrl = blog_image_url($post['featured_image'] ?? null);
    $imageAlt = blog_escape($post['title']);
    $category = blog_escape($post['category_name'] ?? 'General');
    $date = blog_escape(blog_format_date($post['published_at'] ?? $post['created_at']));
    $excerpt = blog_escape(mb_strimwidth(strip_tags($post['excerpt'] ?? ''), 0, 140, '...'));
    ?>
    <article class="snf-blog-card snf-blog-card--<?php echo blog_escape($layout); ?>">
      <a href="<?php echo blog_escape($postUrl); ?>" class="snf-blog-card__media">
        <img src="<?php echo blog_escape($imageUrl); ?>" alt="<?php echo $imageAlt; ?>" loading="lazy">
      </a>
      <div class="snf-blog-card__body">
        <div class="snf-blog-card__meta">
          <span class="snf-blog-card__category"><?php echo $category; ?></span>
          <span class="snf-blog-card__date"><?php echo $date; ?></span>
        </div>
        <h3 class="snf-blog-card__title">
          <a href="<?php echo blog_escape($postUrl); ?>"><?php echo blog_escape($post['title']); ?></a>
        </h3>
        <p class="snf-blog-card__excerpt"><?php echo $excerpt; ?></p>
        <a href="<?php echo blog_escape($postUrl); ?>" class="snf-blog-card__link">
          Read Article <i class="ri-arrow-right-line"></i>
        </a>
      </div>
    </article>
    <?php
}

function blog_render_sidebar(?array $latestPosts = null, ?array $categories = null): void
{
    $latestPosts = $latestPosts ?? blog_get_recent_posts(5);
    $categories = $categories ?? blog_category_list(false, true);
    ?>
    <aside class="snf-blog-sidebar">
      <div class="snf-blog-widget">
        <h5>Latest Posts</h5>
        <ul class="snf-blog-latest">
          <?php foreach ($latestPosts as $latest) : ?>
            <li>
              <a href="<?php echo blog_escape(blog_post_url($latest['slug'])); ?>" class="snf-blog-latest__thumb">
                <img src="<?php echo blog_escape(blog_image_url($latest['featured_image'] ?? null)); ?>" alt="" loading="lazy">
              </a>
              <div>
                <a href="<?php echo blog_escape(blog_post_url($latest['slug'])); ?>"><?php echo blog_escape($latest['title']); ?></a>
                <small><?php echo blog_escape(blog_format_date($latest['published_at'] ?? $latest['created_at'])); ?></small>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="snf-blog-widget">
        <h5>Categories</h5>
        <ul class="snf-blog-categories">
          <li><a href="<?php echo blog_escape(blog_url('blog')); ?>">All Categories</a></li>
          <?php foreach ($categories as $category) : ?>
            <li>
              <a href="<?php echo blog_escape(blog_url('blog?category=' . urlencode($category['slug']))); ?>">
                <?php echo blog_escape($category['name']); ?>
                <span>(<?php echo (int) $category['post_count']; ?>)</span>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </aside>
    <?php
}
