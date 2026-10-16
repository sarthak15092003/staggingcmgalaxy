<?php
/**
 * Template Name: All Categories List
 * Description: Lists all documentation categories with Left Sidebar.
 *
 * @package HelloElementor
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

get_header('docs');

get_template_part('template-parts/docs/header-docy');

$taxonomy = taxonomy_exists('doc_category') ? 'doc_category' : 'category';
$terms = get_terms(array(
    'taxonomy'   => $taxonomy,
    'hide_empty' => false,
));

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
    'getting started'           => 1,
    'account management'        => 2,
    'account mangement'         => 2,
    'user management'           => 3,
    'user manegement'           => 3,
    'master dashboard'          => 4,
    'main dashboard'            => 5,
    'funnel attribution'        => 6,
    'integrations'              => 7,
    'google dashboard'          => 8,
    'meta dashboard'            => 9,
    'linkedin dashboard'        => 10,
    'teads dashboard'           => 11,
    'pinterest dashboard'       => 12,
    'dv360 dashboard'           => 13,
    'amazon dashboard'          => 14,
    'recommendation'            => 15,
    'milestone'                 => 16,
    'notification center'       => 17,
    'ticket/ support'           => 18,
    'tickets / supports'        => 18,
    'tickets / support'         => 18,
    'ticket / support'          => 18,
    'reporting hub'             => 19,
    'reporting hub '            => 19,
    'lex'                       => 20,
    'user journey'              => 21
);

$custom_order_slugs = array(
    'getting-started'           => 1,
    'account-management'        => 2,
    'user-management'           => 3,
    'master-dashboard'          => 4,
    'main-dashboard'            => 5,
    'funnel-attribution'        => 6,
    'integrations'              => 7,
    'google-dashboard'          => 8,
    'meta-dashboard'            => 9,
    'linkedin-dashboard'        => 10,
    'teads-dashboard'           => 11,
    'pinterest-dashboard'       => 12,
    'dv360-dashboard'           => 13,
    'amazon-dashboard'          => 14,
    'recommendation'            => 15,
    'milestone'                 => 16,
    'notification-center'       => 17,
    'ticket-support'            => 18,
    'tickets-supports'          => 18,
    'reporting-hub'             => 19,
    'lex'                       => 20,
    'user-journey'              => 21
);

if (!empty($terms) && !is_wp_error($terms)) {
    usort($terms, function($a, $b) use ($custom_order, $custom_order_slugs) {
        $title_a = strtolower(trim($a->name));
        $title_b = strtolower(trim($b->name));
        $slug_a = strtolower(trim($a->slug));
        $slug_b = strtolower(trim($b->slug));

        $pos_a = isset($custom_order[$title_a]) ? $custom_order[$title_a] : (isset($custom_order_slugs[$slug_a]) ? $custom_order_slugs[$slug_a] : 999);
        $pos_b = isset($custom_order[$title_b]) ? $custom_order[$title_b] : (isset($custom_order_slugs[$slug_b]) ? $custom_order_slugs[$slug_b] : 999);

        if ($pos_a !== $pos_b) {
            return $pos_a - $pos_b;
        }

        return strcasecmp($a->name, $b->name);
    });
}
?>

<div class="docs-main-wrapper" style="padding-top: 32px; padding-bottom: 64px;">
    <div class="docs-container">
        <div class="docs-layout-row">
            
            <div class="docs-col-sidebar category-left-sidebar-col">
                <?php get_template_part('template-parts/docs/sidebar-modern'); ?>
            </div>

            <div class="docs-col-content">
                <nav aria-label="breadcrumb">
                    <ol class="custom-breadcrumb">
                        <li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
                        <li class="breadcrumb-separator">/</li>
                        <li class="active-crumb" aria-current="page">All Categories</li>
                    </ol>
                </nav>

                <h1 style="font-size: 32px; font-weight: 700; color: #111827; margin: 16px 0 24px;">All Documentation Categories</h1>

                <div class="tw-category-container">
                    <?php if (!empty($terms) && !is_wp_error($terms)) : ?>
                        <?php foreach ($terms as $term) :
                            $term_link = get_term_link($term->term_id, $term->taxonomy);
                            $term_link = is_wp_error($term_link) ? '#' : $term_link;
                            $cat_name = $term->name;
                            $icon_url = isset($custom_icons[$cat_name]) ? $custom_icons[$cat_name] : 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/category-2.png';
                            $desc = !empty($term->description) ? $term->description : 'Guides, walkthroughs, and documentation for ' . esc_html($cat_name) . '.';
                        ?>
                            <a href="<?php echo esc_url($term_link); ?>" class="category-card">
                                <div class="category-card__icon">
                                    <img src="<?php echo esc_url($icon_url); ?>" alt="<?php echo esc_attr($cat_name); ?>" />
                                </div>
                                <h2 class="category-card__title"><?php echo esc_html($cat_name); ?></h2>
                                <p class="category-card__desc"><?php echo esc_html(wp_trim_words($desc, 18, '...')); ?></p>
                                <div class="category-card__meta">
                                    <span class="doc-count"><?php echo intval($term->count); ?> <?php echo _n('article', 'articles', $term->count, 'hello-elementor'); ?></span>
                                    <span class="doc-arrow">&rarr;</span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            </div>

        </div>
    </div>
</div>

<?php
get_footer();
