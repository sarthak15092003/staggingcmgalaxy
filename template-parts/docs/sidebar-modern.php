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

// Custom sort logic based on Docy's '_cmgalaxy_sidebar_order' meta field
if (!empty($sidebar_sections)) {
    usort($sidebar_sections, function($a, $b) {
        $term_id_a = isset($a['term_id']) ? intval($a['term_id']) : 0;
        $term_id_b = isset($b['term_id']) ? intval($b['term_id']) : 0;

        $order_a = $term_id_a ? get_term_meta($term_id_a, '_cmgalaxy_sidebar_order', true) : '';
        $order_b = $term_id_b ? get_term_meta($term_id_b, '_cmgalaxy_sidebar_order', true) : '';

        $val_a = ($order_a !== '') ? intval($order_a) : 9999;
        $val_b = ($order_b !== '') ? intval($order_b) : 9999;

        if ($val_a == $val_b) {
            return strcasecmp($a['title'], $b['title']);
        }
        return ($val_a < $val_b) ? -1 : 1;
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
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </span>
                    <?php endif; ?>
                </div>

                <?php if ($has_content) : ?>
                    <div class="<?php echo esc_attr($content_class); ?> cmgalaxy-sortable-sub" id="<?php echo esc_attr($sidebar_instance_id . '_' . $sec['slug']); ?>" style="<?php echo $is_active_cat ? 'display:block;' : 'display:none;'; ?>">
                        
                        <?php 
                        // 1. Direct posts if no subcategories (same as Docy)
                        if ($has_main_direct_posts && !$has_subcats) {
                            foreach ($main_direct_posts as $main_post) {
                                $is_current_post = $is_single_doc && ($current_post_id == $main_post->ID);
                                $post_wrapper_class = 'sidebar-subcat-post-item' . ($is_current_post ? ' active-article' : '');
                                ?>
                                <div class="<?php echo esc_attr($post_wrapper_class); ?>" style="padding: 8px 12px 8px 36px; position: relative;">
                                    <?php if ($is_current_post): ?>
                                        <div style="position: absolute; left: 20px; top: 0; bottom: 0; width: 2px; background: #3B82F6;"></div>
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

                                $is_sub_active = in_array($sub->slug, $current_categories) || $is_any_post_active;
                                $sub_link = get_term_link($sub->term_id, $sec_tax);
                                $sub_link = is_wp_error($sub_link) ? '#' : $sub_link;
                                $sub_target_id = $sidebar_instance_id . '_sub_' . $sub->term_id;
                            ?>
                                <div class="cmgalaxy-subcat-wrapper" data-term-id="<?php echo esc_attr($sub->term_id); ?>">
                                    <div class="subsection-item <?php echo $is_sub_active ? 'current-page' : ''; ?> <?php echo $has_sub_posts ? 'expandable-subcat' : ''; ?>" data-target="<?php echo esc_attr($sub_target_id); ?>" style="display:flex; align-items:center; padding: 7px 0 7px 16px; cursor:pointer;">
                                        <a href="<?php echo esc_url($sub_link); ?>" class="subsection-title" style="color: #475569; font-size: 13.5px; text-decoration: none; flex: 1;">
                                            <?php echo esc_html($sub->name); ?>
                                        </a>
                                        <?php if ($has_sub_posts) : ?>
                                            <span class="expand-icon-subcat" style="color:#94a3b8; margin-left:6px; display:inline-flex; transition: transform 0.2s; <?php echo $is_sub_active ? 'transform: rotate(90deg);' : ''; ?>">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="9 18 15 12 9 6"></polyline>
                                                </svg>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($has_sub_posts) : ?>
                                        <div class="sub-subcategories" id="<?php echo esc_attr($sub_target_id); ?>" style="display: <?php echo $is_sub_active ? 'block' : 'none'; ?>; padding-left: 0; border-left: 2px solid #e2e8f0; margin-left: 24px;">
                                            <?php foreach ($sub_posts as $sp) :
                                                $is_cur = ($current_post_id == $sp->ID);
                                            ?>
                                                <div class="sidebar-subcat-post-item <?php echo $is_cur ? 'active-article' : ''; ?>" style="padding: 7px 8px 7px 16px;">
                                                    <a href="<?php echo esc_url(get_permalink($sp->ID)); ?>" title="<?php echo esc_attr($sp->post_title); ?>">
                                                        <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;"><?php echo esc_html($sp->post_title); ?></span>
                                                    </a>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php }
                        }

                        // 3. Fallback direct posts if both exist
                        if ($has_main_direct_posts && $has_subcats) {
                            foreach ($main_direct_posts as $dp) {
                                $is_cur = ($current_post_id == $dp->ID);
                            ?>
                                <div class="sidebar-subcat-post-item <?php echo $is_cur ? 'active-article' : ''; ?>" style="padding: 7px 8px 7px 36px;">
                                    <a href="<?php echo esc_url(get_permalink($dp->ID)); ?>" title="<?php echo esc_attr($dp->post_title); ?>">
                                        <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;"><?php echo esc_html($dp->post_title); ?></span>
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
