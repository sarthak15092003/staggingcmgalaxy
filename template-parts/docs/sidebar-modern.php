<?php
/**
 * Modern Docs Sidebar Navigation Component
 * Supports both 'docs' custom post type and standard 'post'
 * Supports 'doc_category' and 'category' taxonomies
 */

$is_single_doc = is_singular('docs') || is_singular('post');
$current_post_id = get_queried_object_id();
$taxonomy_name = taxonomy_exists('doc_category') ? 'doc_category' : 'category';
$post_type_name = post_type_exists('docs') ? 'docs' : 'post';

// Get current post's terms or current archive term
$current_categories = array();
if ($is_single_doc) {
    $terms = get_the_terms($current_post_id, 'doc_category');
    if (!$terms || is_wp_error($terms)) {
        $terms = get_the_terms($current_post_id, 'category');
    }
    if ($terms && !is_wp_error($terms)) {
        foreach ($terms as $t) {
            $current_categories[] = $t->slug;
        }
    }
} elseif (is_tax('doc_category') || is_category()) {
    $queried_term = get_queried_object();
    if ($queried_term && isset($queried_term->slug)) {
        $current_categories[] = $queried_term->slug;
    }
} elseif (isset($_GET['cat']) && !empty($_GET['cat'])) {
    $term = get_term(intval($_GET['cat']), $taxonomy_name);
    if ($term && !is_wp_error($term)) {
        $current_categories[] = $term->slug;
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

// Fetch top-level terms for documentation
$terms_query = get_terms(array(
    'taxonomy'   => array('doc_category', 'category'),
    'parent'     => 0,
    'hide_empty' => false,
    'orderby'    => 'name',
    'order'      => 'ASC'
));

$sidebar_sections = array();
if (!empty($terms_query) && !is_wp_error($terms_query)) {
    foreach ($terms_query as $term) {
        $icon = isset($custom_icons[$term->name]) ? $custom_icons[$term->name] : 'https://docs.cmgalaxy.com/wp-content/uploads/2026/07/category-2.png';
        $sidebar_sections[] = array(
            'slug'     => $term->slug,
            'title'    => $term->name,
            'icon'     => $icon,
            'term_id'  => $term->term_id,
            'taxonomy' => $term->taxonomy
        );
    }
}

$sidebar_instance_id = uniqid('docs_sb_');
$docs_home_url = get_post_type_archive_link('docs') ? get_post_type_archive_link('docs') : home_url('/docs/');
?>

<div class="modern-sidebar" id="<?php echo esc_attr($sidebar_instance_id); ?>">
    <div class="sidebar-content">
        
        <?php if ($is_single_doc) : ?>
            <a href="<?php echo esc_url($docs_home_url); ?>" class="sidebar-back-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;">
                    <path d="M19 12H5M5 12L12 19M5 12L12 5"/>
                </svg>
                <span>Back to All Docs</span>
            </a>
        <?php endif; ?>

        <?php foreach ($sidebar_sections as $sec) :
            $is_active_cat = in_array($sec['slug'], $current_categories);
            $cat_term_id = $sec['term_id'];
            $sec_tax = $sec['taxonomy'];

            // Fetch subcategories
            $subcats = get_terms(array(
                'taxonomy'   => $sec_tax,
                'parent'     => $cat_term_id,
                'hide_empty' => false
            ));

            // Fetch direct articles
            $direct_posts = get_posts(array(
                'post_type'      => array('docs', 'post'),
                'posts_per_page' => -1,
                'tax_query'      => array(
                    array(
                        'taxonomy'         => $sec_tax,
                        'field'            => 'term_id',
                        'terms'            => $cat_term_id,
                        'include_children' => false
                    )
                ),
                'orderby'        => 'menu_order title',
                'order'          => 'ASC'
            ));

            $has_subcats = !empty($subcats) && !is_wp_error($subcats);
            $has_posts = !empty($direct_posts);
            $has_content = $has_subcats || $has_posts;

            $header_class = 'section-header' . ($has_content ? ' expandable' : '') . ($is_active_cat ? ' active' : '');
            $content_class = 'section-content' . ($is_active_cat && $has_content ? ' expanded' : '');
            $term_link = get_term_link($cat_term_id, $sec_tax);
            $term_link = is_wp_error($term_link) ? '#' : $term_link;
        ?>
            <div class="sidebar-section" data-term-id="<?php echo esc_attr($cat_term_id); ?>">
                <div class="<?php echo esc_attr($header_class); ?>" data-target="<?php echo esc_attr($sidebar_instance_id . '_' . $sec['slug']); ?>">
                    <div class="section-icon">
                        <img src="<?php echo esc_url($sec['icon']); ?>" alt="" />
                    </div>
                    <a href="<?php echo esc_url($term_link); ?>" class="section-title"><?php echo esc_html($sec['title']); ?></a>
                    <?php if ($has_content) : ?>
                        <span class="expand-icon <?php echo $is_active_cat ? 'expanded' : ''; ?>">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </span>
                    <?php endif; ?>
                </div>

                <?php if ($has_content) : ?>
                    <div class="<?php echo esc_attr($content_class); ?>" id="<?php echo esc_attr($sidebar_instance_id . '_' . $sec['slug']); ?>" style="<?php echo $is_active_cat ? 'display:block;' : 'display:none;'; ?>">
                        
                        <?php if ($has_subcats) : ?>
                            <?php foreach ($subcats as $sub) :
                                $is_sub_active = in_array($sub->slug, $current_categories);
                                $sub_posts = get_posts(array(
                                    'post_type'      => array('docs', 'post'),
                                    'posts_per_page' => -1,
                                    'tax_query'      => array(
                                        array(
                                            'taxonomy' => $sec_tax,
                                            'field'    => 'term_id',
                                            'terms'    => $sub->term_id
                                        )
                                    ),
                                    'orderby'        => 'menu_order title',
                                    'order'          => 'ASC'
                                ));
                                $has_sub_posts = !empty($sub_posts);
                                $sub_link = get_term_link($sub->term_id, $sec_tax);
                                $sub_link = is_wp_error($sub_link) ? '#' : $sub_link;
                                $sub_target_id = $sidebar_instance_id . '_sub_' . $sub->term_id;
                            ?>
                                <div class="cmgalaxy-subcat-wrapper">
                                    <div class="subsection-item <?php echo $is_sub_active ? 'current-page' : ''; ?> <?php echo $has_sub_posts ? 'expandable-subcat' : ''; ?>" data-target="<?php echo esc_attr($sub_target_id); ?>" style="display:flex; align-items:center; padding: 6px 0 6px 12px; cursor:pointer;">
                                        <a href="<?php echo esc_url($sub_link); ?>" class="subsection-title" style="color: #475569; font-size: 13.5px; text-decoration: none; flex: 1;">
                                            <?php echo esc_html($sub->name); ?>
                                        </a>
                                        <?php if ($has_sub_posts) : ?>
                                            <span class="expand-icon-subcat" style="color:#94a3b8; margin-left:6px; display:inline-flex;">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="9 18 15 12 9 6"></polyline>
                                                </svg>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($has_sub_posts) : ?>
                                        <div class="sub-subcategories" id="<?php echo esc_attr($sub_target_id); ?>" style="display: <?php echo $is_sub_active ? 'block' : 'none'; ?>; padding-left: 12px; border-left: 2px solid #e2e8f0; margin-left: 12px;">
                                            <?php foreach ($sub_posts as $sp) :
                                                $is_cur = ($current_post_id == $sp->ID);
                                            ?>
                                                <div class="sidebar-subcat-post-item <?php echo $is_cur ? 'active-article' : ''; ?>" style="padding: 4px 0;">
                                                    <a href="<?php echo esc_url(get_permalink($sp->ID)); ?>">
                                                        <?php echo esc_html($sp->post_title); ?>
                                                    </a>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <?php if ($has_posts && !$has_subcats) : ?>
                            <?php foreach ($direct_posts as $dp) :
                                $is_cur = ($current_post_id == $dp->ID);
                            ?>
                                <div class="sidebar-subcat-post-item <?php echo $is_cur ? 'active-article' : ''; ?>">
                                    <a href="<?php echo esc_url(get_permalink($dp->ID)); ?>">
                                        <?php echo esc_html($dp->post_title); ?>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

    </div>
</div>
