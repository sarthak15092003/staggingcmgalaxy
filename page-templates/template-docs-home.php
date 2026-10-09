<?php
/**
 * Template Name: Docs Homepage
 * Description: Clean, modern Knowledge Base / Documentation Homepage matching Docy & CMGalaxy styling.
 *
 * @package HelloElementor
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

get_header();

// Fetch category taxonomy for docs
$taxonomy_name = taxonomy_exists('doc_category') ? 'doc_category' : 'category';
$categories = get_terms(array(
    'taxonomy'   => array('doc_category', 'category'),
    'parent'     => 0,
    'hide_empty' => false,
    'orderby'    => 'name',
    'order'      => 'ASC'
));

// CMGalaxy Custom Category Icons Map
$custom_icons = array(
    'User Management'           => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/usermanagement.png',
    'User Manegement'           => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/usermanagement.png',
    'Account Management'        => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/account-management-1.png',
    'Account Mangement'         => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/account-management-1.png',
    'Master Dashboard'          => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/master-dashboard.png',
    'Main Dashboard'            => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/category-2.png',
    'Dashboard'                 => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/category-2.png',
    'Funnel Attribution'        => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/funnel.png',
    'Integrations'              => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/integrations.png',
    'Google Dashboard'          => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/google.png',
    'Meta Dashboard'            => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/meta.png',
    'DV360 Dashboard'           => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/DV360.png',
    'Amazon Dashboard'          => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/amzone.png',
    'Recommendation'            => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/recommendation.png',
    'Pinterest Dashboard'       => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/pinterest.png',
    'Milestone'                 => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/milestonte.png',
    'Notification Center'       => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/notification.png',
    'Ticket/ Support'           => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/support.png',
    'Tickets / Supports'        => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/support.png',
    'Reporting Hub'             => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/report.png',
    'Reporting HUb'             => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/report.png',
    'Lex'                       => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/recommendation.png',
    'User Journey'              => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/user-jounery.png',
    'Onboarding'                => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/onboarding.png',
    'Getting started'           => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/onboarding.png',
    'Getting Started'           => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/onboarding.png',
    'Linkedin Dashboard'        => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/linkedin.png',
    'LinkedIn Dashboard'        => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/linkedin.png',
    'Teads Dashboard'           => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/teads.png',
    'UTM Parameters Guidelines' => 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/UTM-.png'
);

$custom_order = array(
    'Getting started'           => 1,
    'Getting Started'           => 1,
    'Account Management'        => 2,
    'Account Mangement'         => 2,
    'User Management'           => 3,
    'User Manegement'           => 3,
    'Master Dashboard'          => 4,
    'Main Dashboard'            => 5,
    'Funnel Attribution'        => 6,
    'Integrations'              => 7,
    'Google Dashboard'          => 8,
    'Meta Dashboard'            => 9,
    'Linkedin Dashboard'        => 10,
    'LinkedIn Dashboard'        => 10,
    'Teads Dashboard'           => 11,
    'Pinterest Dashboard'       => 12,
    'DV360 Dashboard'           => 13,
    'Amazon Dashboard'          => 14,
    'Recommendation'            => 15,
    'Milestone'                 => 16,
    'Notification Center'       => 17,
    'Ticket/ Support'           => 18,
    'Reporting Hub'             => 19,
    'Lex'                       => 20,
    'User Journey'              => 21
);

if (!empty($categories) && !is_wp_error($categories)) {
    usort($categories, function($a, $b) use ($custom_order) {
        $pos_a = isset($custom_order[$a->name]) ? $custom_order[$a->name] : 999;
        $pos_b = isset($custom_order[$b->name]) ? $custom_order[$b->name] : 999;
        if ($pos_a == $pos_b) {
            return strcmp($a->name, $b->name);
        }
        return $pos_a - $pos_b;
    });
}
?>

<div class="docs-main-wrapper">
    
    <?php get_template_part('template-parts/docs/search-banner'); ?>

    <div class="docs-container">
        <div class="docs-category-grid">
            <?php if (!empty($categories) && !is_wp_error($categories)) : ?>
                <?php foreach ($categories as $cat) :
                    $cat_link = get_term_link($cat->term_id, $cat->taxonomy);
                    $cat_link = is_wp_error($cat_link) ? '#' : $cat_link;
                    $icon_url = isset($custom_icons[$cat->name]) ? $custom_icons[$cat->name] : 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/category-2.png';
                    
                    // Count posts in this category (docs and posts)
                    $doc_count_query = new WP_Query(array(
                        'post_type' => array('docs', 'post'),
                        'tax_query' => array(
                            array(
                                'taxonomy' => $cat->taxonomy,
                                'field'    => 'term_id',
                                'terms'    => $cat->term_id,
                            )
                        ),
                        'posts_per_page' => 1,
                        'fields' => 'ids'
                    ));
                    $doc_count = $doc_count_query->found_posts;
                    $cat_desc = !empty($cat->description) ? $cat->description : 'Explore guides, setup instructions, and troubleshooting for ' . esc_html($cat->name) . '.';
                ?>
                    <a href="<?php echo esc_url($cat_link); ?>" class="category-card">
                        <div class="category-card__icon">
                            <img src="<?php echo esc_url($icon_url); ?>" alt="<?php echo esc_attr($cat->name); ?>" />
                        </div>
                        <h2 class="category-card__title"><?php echo esc_html($cat->name); ?></h2>
                        <p class="category-card__desc"><?php echo esc_html(wp_trim_words($cat_desc, 18, '...')); ?></p>
                        <div class="category-card__meta">
                            <span class="doc-count"><?php echo intval($doc_count); ?> <?php echo _n('article', 'articles', $doc_count, 'hello-elementor'); ?></span>
                            <span class="doc-arrow">Explore &rarr;</span>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else : ?>
                <p>No documentation categories found yet. Please add them from WordPress Admin &gt; Docs &gt; Doc Categories.</p>
            <?php endif; ?>
        </div>
    </div>

</div>

<?php
get_footer();
