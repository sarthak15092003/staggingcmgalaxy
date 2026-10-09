<?php
/**
 * Docs Category Detail Content Component
 * Displays subcategories accordions and doc article rows
 */

$current_cat_id = get_queried_object_id();
if (isset($_GET['cat']) && !empty($_GET['cat'])) {
    $current_cat_id = intval($_GET['cat']);
}
if (!$current_cat_id) {
    return;
}

$taxonomy_name = 'doc_category';
if (!is_tax('doc_category') && is_category()) {
    $taxonomy_name = 'category';
}

$subcategories = get_terms([
    'taxonomy'   => $taxonomy_name,
    'parent'     => $current_cat_id,
    'hide_empty' => false,
]);

$direct_articles = new WP_Query([
    'post_type'      => ['docs', 'post'],
    'tax_query'      => [
        [
            'taxonomy'         => $taxonomy_name,
            'field'            => 'term_id',
            'terms'            => $current_cat_id,
            'include_children' => false,
        ]
    ],
    'posts_per_page' => -1,
    'orderby'        => ['menu_order' => 'ASC', 'title' => 'ASC'],
]);
?>

<div class="tw-cat-detail-container">
    
    <?php if (!empty($subcategories) && !is_wp_error($subcategories)) : ?>
        <?php foreach ($subcategories as $subcat) :
            $sub_articles = new WP_Query([
                'post_type'      => ['docs', 'post'],
                'tax_query'      => [
                    [
                        'taxonomy' => $taxonomy_name,
                        'field'    => 'term_id',
                        'terms'    => $subcat->term_id,
                    ]
                ],
                'posts_per_page' => -1,
                'orderby'        => ['menu_order' => 'ASC', 'title' => 'ASC'],
            ]);
            $count = $sub_articles->found_posts;
        ?>
            <div class="tw-cat-articles-card">
                <div class="tw-subcategory-header" onclick="this.nextElementSibling.style.display = (this.nextElementSibling.style.display === 'none' ? 'block' : 'none');">
                    <h3 class="tw-subcategory-title">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                            </svg>
                            <span><?php echo esc_html($subcat->name); ?></span>
                        </div>
                        <span class="tw-subcategory-count">(<?php echo intval($count); ?> articles)</span>
                    </h3>
                </div>

                <div class="tw-subcategory-articles" style="display: block; border-top: 1px solid #e5e7eb;">
                    <?php if ($sub_articles->have_posts()) : ?>
                        <?php while ($sub_articles->have_posts()) : $sub_articles->the_post(); ?>
                            <a href="<?php the_permalink(); ?>" class="tw-article-row">
                                <div class="tw-article-content">
                                    <div class="tw-article-title"><?php the_title(); ?></div>
                                    <p class="tw-article-desc"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 18, '...')); ?></p>
                                </div>
                                <div class="tw-article-arrow">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="9 18 15 12 9 6"></polyline>
                                    </svg>
                                </div>
                            </a>
                        <?php endwhile; wp_reset_postdata(); ?>
                    <?php else : ?>
                        <p style="padding: 16px 20px; color: #6b7280; margin: 0;">No articles found.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if ($direct_articles->have_posts()) : ?>
        <div class="tw-cat-articles-card">
            <?php if (!empty($subcategories) && !is_wp_error($subcategories)) : ?>
                <div class="tw-subcategory-header" style="background: #ffffff; cursor: default;">
                    <h3 class="tw-subcategory-title">
                        <span>Other Articles</span>
                        <span class="tw-subcategory-count">(<?php echo intval($direct_articles->found_posts); ?> articles)</span>
                    </h3>
                </div>
            <?php endif; ?>
            <div class="tw-subcategory-articles" style="display: block;">
                <?php while ($direct_articles->have_posts()) : $direct_articles->the_post(); ?>
                    <a href="<?php the_permalink(); ?>" class="tw-article-row">
                        <div class="tw-article-content">
                            <div class="tw-article-title"><?php the_title(); ?></div>
                            <p class="tw-article-desc"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 22, '...')); ?></p>
                        </div>
                        <div class="tw-article-arrow">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </div>
                    </a>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
    <?php endif; ?>

</div>
