<?php
/**
 * Modern Docs Sidebar Navigation Component
 * Supports both 'docs' custom post type and standard 'post'
 * Supports 'doc_category' and 'category' taxonomies
 * Matches Docy ordering logic and drag-and-drop sortable structure
 */

$is_single_doc = is_singular('docs') || is_singular('post');
$current_post_id = get_queried_object_id();
$taxonomy_name = taxonomy_exists('doc_category') ? 'doc_category' : 'category';
$post_type_name = post_type_exists('docs') ? 'docs' : 'post';

// Get current post's terms or current archive term
$current_categories = array();
$top_cat_slug = '';
$back_url = home_url('/docs/');

if ($is_single_doc) {
    $terms = get_the_terms($current_post_id, 'doc_category');
    if (!$terms || is_wp_error($terms)) {
        $terms = get_the_terms($current_post_id, 'category');
    }
    if ($terms && !is_wp_error($terms)) {
        // Filter out parent categories if a subcategory is also assigned
        $filtered_terms = array();
        foreach ($terms as $t) {
            $is_parent = false;
            foreach ($terms as $other_t) {
                if ($other_t->parent == $t->term_id) {
                    $is_parent = true;
                    break;
                }
            }
            if (!$is_parent) {
                $filtered_terms[] = $t;
            }
        }
        foreach ($filtered_terms as $t) {
            $current_categories[] = $t->slug;
        }

        $top_cat = $terms[0];
        while ($top_cat->parent) {
            $parent_term = get_term($top_cat->parent, $top_cat->taxonomy);
            if ($parent_term && !is_wp_error($parent_term)) {
                $top_cat = $parent_term;
            } else {
                break;
            }
        }
        $top_cat_slug = $top_cat->slug;
        $term_link = get_term_link($top_cat->term_id, $top_cat->taxonomy);
        $back_url = is_wp_error($term_link) ? home_url('/docs/') : $term_link;
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
    'orderby'    => 'ID',
    'order'      => 'ASC'
));

$sidebar_sections = array();
if (!empty($terms_query) && !is_wp_error($terms_query)) {
    foreach ($terms_query as $term) {
        if (strtolower($term->slug) === 'uncategorized' || strtolower($term->name) === 'uncategorized') {
            continue;
        }
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

// Custom category order matching template-docs-home.php
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

// Custom sort logic: Respect manual order meta if set, otherwise follow homepage custom order
if (!empty($sidebar_sections)) {
    usort($sidebar_sections, function($a, $b) use ($custom_order, $custom_order_slugs) {
        $term_id_a = isset($a['term_id']) ? intval($a['term_id']) : 0;
        $term_id_b = isset($b['term_id']) ? intval($b['term_id']) : 0;

        $order_a = $term_id_a ? get_term_meta($term_id_a, '_cmgalaxy_sidebar_order', true) : '';
        $order_b = $term_id_b ? get_term_meta($term_id_b, '_cmgalaxy_sidebar_order', true) : '';

        // If admin order meta is explicitly set on both and different, use it
        if ($order_a !== '' && $order_b !== '' && intval($order_a) !== intval($order_b)) {
            return intval($order_a) - intval($order_b);
        }

        // Use custom order matching homepage
        $title_a = strtolower(trim($a['title']));
        $title_b = strtolower(trim($b['title']));
        $slug_a = strtolower(trim($a['slug'] ?? ''));
        $slug_b = strtolower(trim($b['slug'] ?? ''));

        $pos_a = isset($custom_order[$title_a]) ? $custom_order[$title_a] : (isset($custom_order_slugs[$slug_a]) ? $custom_order_slugs[$slug_a] : 999);
        $pos_b = isset($custom_order[$title_b]) ? $custom_order[$title_b] : (isset($custom_order_slugs[$slug_b]) ? $custom_order_slugs[$slug_b] : 999);

        if ($pos_a !== $pos_b) {
            return $pos_a - $pos_b;
        }

        return strcasecmp($a['title'], $b['title']);
    });
}

global $wpdb;
$sidebar_instance_id = uniqid('docs_sb_');
?>

<div class="modern-sidebar cmgalaxy-sortable-top" id="<?php echo esc_attr($sidebar_instance_id); ?>">
    <div class="sidebar-content">
        
        <?php if ($is_single_doc) : ?>
            <a href="<?php echo esc_url($back_url); ?>" class="sidebar-back-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;">
                    <path d="M19 12H5M5 12L12 19M5 12L12 5"/>
                </svg>
                <span>Back to categories</span>
            </a>
        <?php endif; ?>

        <?php foreach ($sidebar_sections as $sec) :
            // In single doc view, ONLY show the category of the current doc
            if ($is_single_doc && !empty($top_cat_slug) && $sec['slug'] !== $top_cat_slug) {
                continue;
            }

            $cat_term_id = $sec['term_id'];
            $sec_tax = $sec['taxonomy'];

            // Fetch direct posts of this category ordered by menu_order ASC, ID ASC (Docy exact order)
            $main_direct_posts = $wpdb->get_results($wpdb->prepare("
                SELECT p.ID, p.post_title 
                FROM {$wpdb->posts} p
                INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
                INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                WHERE tt.term_id = %d
                AND tt.taxonomy = %s
                AND p.post_type IN ('docs', 'post')
                AND p.post_status = 'publish'
                ORDER BY p.menu_order ASC, p.ID ASC
            ", $cat_term_id, $sec_tax));
            $has_main_direct_posts = !empty($main_direct_posts);

            // Fetch subcategories
            $subcats_raw = get_terms(array(
                'taxonomy'   => $sec_tax,
                'parent'     => $cat_term_id,
                'hide_empty' => false
            ));

            $subcats = array();
            if (!empty($subcats_raw) && !is_wp_error($subcats_raw)) {
                foreach ($subcats_raw as $sc) {
                    $subcats[] = $sc;
                }
            }
            if (function_exists('cmgalaxy_sort_terms_by_order')) {
                usort($subcats, 'cmgalaxy_sort_terms_by_order');
            }

            $has_subcats = !empty($subcats);
            $has_content = $has_subcats || $has_main_direct_posts;

            // Check if active
            $is_active_cat = in_array($sec['slug'], $current_categories) || ($is_single_doc && $sec['slug'] === $top_cat_slug);
            if (!$is_active_cat && $has_subcats) {
                foreach ($subcats as $sub) {
                    if (in_array($sub->slug, $current_categories)) {
                        $is_active_cat = true;
                        break;
                    }
                    $sub_subcats_check = get_terms(array(
                        'taxonomy'   => $sec_tax,
                        'parent'     => $sub->term_id,
                        'hide_empty' => false
                    ));
                    if (!empty($sub_subcats_check) && !is_wp_error($sub_subcats_check)) {
                        foreach ($sub_subcats_check as $ss) {
                            if (in_array($ss->slug, $current_categories)) {
                                $is_active_cat = true;
                                break 2;
                            }
                        }
                    }
                }
            }

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
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </span>
                    <?php endif; ?>
                </div>

                <?php if ($has_content) : ?>
                    <div class="<?php echo esc_attr($content_class); ?> cmgalaxy-sortable-sub" id="<?php echo esc_attr($sidebar_instance_id . '_' . $sec['slug']); ?>" style="<?php echo $is_active_cat ? 'display:block;' : 'display:none;'; ?>">
                        
                        <?php 
                        $rendered_post_ids = array();

                        // 1. Direct posts if no subcategories (same as Docy)
                        if ($has_main_direct_posts && !$has_subcats) {
                            foreach ($main_direct_posts as $main_post) {
                                $rendered_post_ids[] = $main_post->ID;
                                $is_current_post = $is_single_doc && ($current_post_id == $main_post->ID);
                                $post_wrapper_class = 'sidebar-subcat-post-item' . ($is_current_post ? ' active-article' : '');
                                ?>
                                <div class="<?php echo esc_attr($post_wrapper_class); ?>" style="padding: 6px 12px 6px 20px; position: relative;">
                                    <?php if ($is_current_post): ?>
                                        <div class="active-indicator-bar" style="position: absolute; left: 4px; top: 50%; transform: translateY(-50%); width: 2.5px; height: 18px; background: #2563eb; border-radius: 3px;"></div>
                                    <?php endif; ?>
                                    <a href="<?php echo esc_url(get_permalink($main_post->ID)); ?>" title="<?php echo esc_attr($main_post->post_title); ?>">
                                        <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;"><?php echo esc_html($main_post->post_title); ?></span>
                                    </a>
                                </div>
                                <?php
                            }
                        }

                        // 2. Subcategories list
                        if (!empty($subcats)) {
                            foreach ($subcats as $sub) {
                                // Fetch sub-subcategories natively (Level 3 matching Docy)
                                $sub_subcats_raw = get_terms(array(
                                    'taxonomy'   => $sec_tax,
                                    'parent'     => $sub->term_id,
                                    'hide_empty' => false
                                ));
                                $sub_subcats = array();
                                if (!empty($sub_subcats_raw) && !is_wp_error($sub_subcats_raw)) {
                                    foreach ($sub_subcats_raw as $ss) {
                                        $sub_subcats[] = $ss;
                                    }
                                }
                                if (function_exists('cmgalaxy_sort_terms_by_order')) {
                                    usort($sub_subcats, 'cmgalaxy_sort_terms_by_order');
                                }
                                $has_sub_subcats = !empty($sub_subcats);

                                $sub_posts = $wpdb->get_results($wpdb->prepare("
                                    SELECT p.ID, p.post_title 
                                    FROM {$wpdb->posts} p
                                    INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
                                    INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                                    WHERE tt.term_id = %d
                                    AND tt.taxonomy = %s
                                    AND p.post_type IN ('docs', 'post')
                                    AND p.post_status = 'publish'
                                    ORDER BY p.menu_order ASC, p.ID ASC
                                ", $sub->term_id, $sec_tax));

                                $has_sub_posts = !empty($sub_posts);
                                $is_any_post_active = false;
                                if ($has_sub_posts && $is_single_doc) {
                                    foreach ($sub_posts as $sp) {
                                        if ($sp->ID == $current_post_id) {
                                            $is_any_post_active = true;
                                            break;
                                        }
                                    }
                                }

                                $is_any_sub_sub_active = false;
                                if ($has_sub_subcats) {
                                    foreach ($sub_subcats as $ss) {
                                        if (in_array($ss->slug, $current_categories)) {
                                            $is_any_sub_sub_active = true;
                                            break;
                                        }
                                    }
                                }

                                $is_sub_active = in_array($sub->slug, $current_categories) || $is_any_post_active || $is_any_sub_sub_active;
                                $sub_link = get_term_link($sub->term_id, $sec_tax);
                                $sub_link = is_wp_error($sub_link) ? '#' : $sub_link;
                                $sub_target_id = $sidebar_instance_id . '_sub_' . $sub->term_id;
                                $has_children = $has_sub_posts || $has_sub_subcats;
                                $subcat_expanded_class = ($is_sub_active && $has_children) ? ' expanded' : '';
                            ?>
                                <div class="cmgalaxy-subcat-wrapper" data-term-id="<?php echo esc_attr($sub->term_id); ?>" style="margin: 2px 0;">
                                    <div class="subsection-item <?php echo $is_sub_active ? 'current-page active-subcat' : ''; ?> <?php echo $has_children ? 'expandable-subcat' : ''; ?><?php echo $subcat_expanded_class; ?>" data-target="<?php echo esc_attr($sub_target_id); ?>" style="display:flex; align-items:center; justify-content:space-between; padding: 8px 14px 8px 48px; border-radius: 6px; cursor:pointer; <?php echo $is_sub_active ? 'background: #f1f5f9;' : ''; ?>">
                                        <a href="<?php echo esc_url($sub_link); ?>" class="subsection-title" style="color: <?php echo $is_sub_active ? '#161c52' : '#475569'; ?>; font-size: 13.5px; font-weight: <?php echo $is_sub_active ? '600' : '500'; ?>; text-decoration: none; flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            <?php echo esc_html($sub->name); ?>
                                        </a>
                                        <?php if ($has_children) : ?>
                                            <span class="expand-icon-subcat <?php echo ($is_sub_active && $has_children) ? 'expanded' : ''; ?>" aria-label="Toggle Subcategory">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="9 18 15 12 9 6"></polyline>
                                                </svg>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($has_children) : ?>
                                        <div class="sub-subcategories" id="<?php echo esc_attr($sub_target_id); ?>" style="display: <?php echo $is_sub_active ? 'block' : 'none'; ?>; padding-left: 0; margin-left: 0;">
                                            
                                            <?php 
                                             // Render Sub-subcategories (Level 3)
                                             if ($has_sub_subcats) {
                                                foreach ($sub_subcats as $ss) {
                                                    $is_ss_active = in_array($ss->slug, $current_categories);
                                                    $ss_posts = $wpdb->get_results($wpdb->prepare("
                                                        SELECT p.ID, p.post_title 
                                                        FROM {$wpdb->posts} p
                                                        INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
                                                        INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                                                        WHERE tt.term_id = %d
                                                        AND tt.taxonomy = %s
                                                        AND p.post_type IN ('docs', 'post')
                                                        AND p.post_status = 'publish'
                                                        ORDER BY p.menu_order ASC, p.ID ASC
                                                    ", $ss->term_id, $sec_tax));
                                                    $ss_link = get_term_link($ss->term_id, $sec_tax);
                                                    $ss_link = is_wp_error($ss_link) ? '#' : $ss_link;
                                                    ?>
                                                    <div class="sidebar-sub-subcat-group" style="padding: 4px 0 4px 10px;">
                                                        <a href="<?php echo esc_url($ss_link); ?>" style="color: #64748b; font-size: 13px; font-weight: 600; text-decoration: none; display: block; padding: 4px 8px;">
                                                             <?php echo esc_html($ss->name); ?>
                                                        </a>
                                                        <?php if (!empty($ss_posts)): ?>
                                                            <div style="margin-left: 12px;">
                                                                <?php foreach ($ss_posts as $ssp): 
                                                                    $rendered_post_ids[] = $ssp->ID;
                                                                    $is_cur = ($current_post_id == $ssp->ID);
                                                                    $is_ssp_gated = function_exists('cmg_is_post_restricted_to_logged_in') && cmg_is_post_restricted_to_logged_in($ssp->ID);
                                                                ?>
                                                                    <div class="sidebar-subcat-post-item <?php echo $is_cur ? 'active-article' : ''; ?>" style="padding: 6px 14px 6px 56px; position: relative;">
                                                                        <?php if ($is_cur): ?>
                                                                            <div class="active-indicator-bar" style="position: absolute; left: 44px; top: 50%; transform: translateY(-50%); width: 2.5px; height: 18px; background: #2563eb; border-radius: 3px;"></div>
                                                                        <?php endif; ?>
                                                                        <a href="<?php echo esc_url(get_permalink($ssp->ID)); ?>" title="<?php echo esc_attr($ssp->post_title); ?>" style="display:flex; align-items:center;">
                                                                            <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex: 1;"><?php echo esc_html($ssp->post_title); ?></span>
                                                                        </a>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php }
                                            }

                                            // Direct posts of this subcategory
                                            if ($has_sub_posts) {
                                                foreach ($sub_posts as $sp) :
                                                    $rendered_post_ids[] = $sp->ID;
                                                    $is_cur = ($current_post_id == $sp->ID);
                                                    $is_sp_gated = function_exists('cmg_is_post_restricted_to_logged_in') && cmg_is_post_restricted_to_logged_in($sp->ID);
                                                ?>
                                                    <div class="sidebar-subcat-post-item <?php echo $is_cur ? 'active-article' : ''; ?>" style="padding: 6px 14px 6px 56px; position: relative;">
                                                        <?php if ($is_cur): ?>
                                                            <div class="active-indicator-bar" style="position: absolute; left: 44px; top: 50%; transform: translateY(-50%); width: 2.5px; height: 18px; background: #2563eb; border-radius: 3px;"></div>
                                                        <?php endif; ?>
                                                        <a href="<?php echo esc_url(get_permalink($sp->ID)); ?>" title="<?php echo esc_attr($sp->post_title); ?>" style="display:flex; align-items:center;">
                                                            <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex: 1;"><?php echo esc_html($sp->post_title); ?></span>
                                                        </a>
                                                    </div>
                                                <?php endforeach; 
                                            } ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php }
                        }

                        // 3. Fallback direct posts if both exist (excluding posts already shown in subcategories)
                        if ($has_main_direct_posts && $has_subcats) {
                            foreach ($main_direct_posts as $dp) {
                                if (in_array($dp->ID, $rendered_post_ids)) {
                                    continue; // Do NOT duplicate posts already in subcategories!
                                }
                                $is_cur = ($current_post_id == $dp->ID);
                                $is_dp_gated = function_exists('cmg_is_post_restricted_to_logged_in') && cmg_is_post_restricted_to_logged_in($dp->ID);
                            ?>
                                <div class="sidebar-subcat-post-item <?php echo $is_cur ? 'active-article' : ''; ?>" style="padding: 6px 14px 6px 48px; position: relative;">
                                    <?php if ($is_cur): ?>
                                        <div class="active-indicator-bar" style="position: absolute; left: 36px; top: 50%; transform: translateY(-50%); width: 2.5px; height: 18px; background: #2563eb; border-radius: 3px;"></div>
                                    <?php endif; ?>
                                    <a href="<?php echo esc_url(get_permalink($dp->ID)); ?>" title="<?php echo esc_attr($dp->post_title); ?>" style="display:flex; align-items:center;">
                                        <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex: 1;"><?php echo esc_html($dp->post_title); ?></span>
                                    </a>
                                </div>
                            <?php }
                        }
                        ?>

                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

    </div>
</div>

<?php if (current_user_can('manage_categories')): ?>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    function handleSort(evt) {
        let container = evt.from;
        let items = container.children;
        let orderedIds = [];
        for (let i = 0; i < items.length; i++) {
            let termId = items[i].getAttribute('data-term-id');
            if (termId) orderedIds.push(termId);
        }
        
        if (orderedIds.length > 0) {
            let formData = new FormData();
            formData.append('action', 'cmgalaxy_update_category_order');
            formData.append('ordered_ids', JSON.stringify(orderedIds));
            
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                body: formData
            }).then(res => res.json()).then(data => {
                if(data && data.success) {
                    console.log('Sidebar order updated successfully');
                }
            }).catch(err => console.error(err));
        }
    }

    let topContainer = document.querySelector('.cmgalaxy-sortable-top .sidebar-content');
    if (topContainer) {
        new Sortable(topContainer, {
            animation: 150,
            onEnd: handleSort,
            filter: '.sidebar-back-btn'
        });
    }

    let subContainers = document.querySelectorAll('.cmgalaxy-sortable-sub');
    subContainers.forEach(container => {
        new Sortable(container, {
            animation: 150,
            onEnd: handleSort
        });
    });
});
</script>
<?php endif; ?>
