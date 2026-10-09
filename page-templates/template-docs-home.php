<?php
/**
 * Template Name: Docs Homepage
 * Description: Exact Knowledge Base / Documentation Homepage matching Docy theme.
 *
 * @package HelloElementor
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Fetch categories from doc_category first, fallback or merge with standard category
$doc_terms = get_terms(array(
    'taxonomy'   => array('doc_category', 'category'),
    'parent'     => 0,
    'hide_empty' => false,
    'exclude'    => array(1), // Exclude Uncategorized
));

// Filter out Uncategorized by name or slug as well
$docy_categories = array();
if (!empty($doc_terms) && !is_wp_error($doc_terms)) {
    foreach ($doc_terms as $term) {
        if (strtolower($term->slug) === 'uncategorized' || strtolower($term->name) === 'uncategorized') {
            continue;
        }
        $docy_categories[] = $term;
    }
}

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

if (!empty($docy_categories)) {
    usort($docy_categories, function($a, $b) use ($custom_order) {
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
    
    <!-- Exact Search Banner from Docy -->
    <?php get_template_part('template-parts/docs/search-banner'); ?>

    <section class="doc_blog_grid_area" style="padding: 40px 0 80px;">
        
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            
            <div class="row">
                <div class="col-12">
                    <p class="category-intro lead mb-10" style="font-size: 18px; font-weight: 400; line-height: 170%; text-align: center; margin: 10px auto 45px; max-width: 900px; color: #484a61;">
                        Your CMGalaxy questions, answered. Learn how to connect your platforms, read your attribution data, analyse creatives, and turn insights into better ROAS.
                    </p>
                </div>
            </div>

            <div class="row g-4 mb-4" style="display: flex; flex-wrap: wrap; margin: -12px;">
                <?php if (!empty($docy_categories)) : ?>
                    <?php foreach ($docy_categories as $cat) :
                        $cat_link = get_term_link($cat->term_id, $cat->taxonomy);
                        $cat_link = is_wp_error($cat_link) ? '#' : $cat_link;
                        $cat_name = esc_html($cat->name);
                        $cat_desc = !empty($cat->description) ? esc_html(wp_trim_words($cat->description, 18, '...')) : 'Learn how to configure and use ' . $cat_name . ' effectively.';
                        
                        // Count articles (both docs and posts)
                        $post_count_query = new WP_Query(array(
                            'post_type'      => array('docs', 'post'),
                            'posts_per_page' => 1,
                            'tax_query'      => array(
                                array(
                                    'taxonomy'         => $cat->taxonomy,
                                    'field'            => 'term_id',
                                    'terms'            => $cat->term_id,
                                    'include_children' => true
                                )
                            ),
                            'fields'         => 'ids'
                        ));
                        $cat_count = $post_count_query->found_posts;
                        
                        $author_icon_url = 'https://docs.cmgalaxy.com/wp-content/uploads/2026/06/cropped-Group-1000004539-300x300-1.png';
                        $cat_icon_url = isset($custom_icons[$cat->name]) ? $custom_icons[$cat->name] : 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/category-2.png';
                    ?>
                        <div class="col-md-4 col-lg-4" style="flex: 0 0 33.333333%; max-width: 33.333333%; padding: 12px; box-sizing: border-box;">
                            <a class="card h-100 category-card border text-reset text-decoration-none" href="<?php echo esc_url($cat_link); ?>" style="display: flex; flex-direction: column; height: 100%; text-decoration: none !important;">
                                <div class="card-body" style="padding: 24px; display: flex; flex-direction: column; height: 100%; box-sizing: border-box;">
                                    <div class="category-card__icon" aria-hidden="true" style="margin-bottom: 16px;">
                                        <img src="<?php echo esc_url($cat_icon_url); ?>" alt="<?php echo esc_attr($cat_name); ?>" style="width: 32px; height: 32px; object-fit: contain;">
                                    </div>
                                    <h5 class="card-title mb-2" style="font-size: 18px; font-weight: 600; color: #111827; margin: 0 0 8px 0;"><?php echo $cat_name; ?></h5>
                                    <p class="card-text text-muted mb-3" style="font-size: 14px; color: #6b7280; line-height: 1.5; margin: 0 0 16px 0; flex-grow: 1;"><?php echo $cat_desc; ?></p>
                                    <div class="category-card__meta small text-muted" style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid #f1f5f9; padding-top: 12px; font-size: 13px; color: #9ca3af; margin-top: auto;">
                                        <div class="category-card__author d-flex align-items-center gap-2" style="display: flex; align-items: center; gap: 8px;">
                                            <span class="category-card__author-icon" style="display: inline-flex; width: 18px; height: 18px;">
                                                <img class="category-card__author-icon-img" src="<?php echo esc_url($author_icon_url); ?>" alt="" style="width: 18px; height: 18px; object-fit: contain; display: block;" />
                                            </span>
                                            <span class="category-card__byline" style="color: #64748b; font-size: 12.5px;">By CMGalaxy</span>
                                        </div>
                                        <span style="color: #4b5563; font-weight: 500;"><?php echo sprintf(_n('%s article', '%s articles', $cat_count, 'hello-elementor'), number_format_i18n($cat_count)); ?></span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="col-12 text-center" style="padding: 40px;">
                        <p style="color: #64748b; font-size: 16px;">No documentation categories found. Create categories under WP Admin &gt; Docs &gt; Doc Categories.</p>
                    </div>
                <?php endif; ?>
            </div>

        </div>

    </section>

</div>

<?php
get_footer();
