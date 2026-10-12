<?php
/**
 * CMGalaxy Documentation Sub-Header Component
 * Matching Docy theme layout-cmgalaxy sub-header row.
 *
 * @package HelloElementor
 */

if (!defined('ABSPATH')) {
    exit;
}

$current_url = home_url(add_query_arg(array(), $GLOBALS['wp']->request ?? ''));
$is_home_active = is_front_page() || is_home();
$is_docs_active = is_singular('docs') || is_tax('doc_category') || is_tax('doc_tag') || is_post_type_archive('docs') || is_page('docs') || is_page_template('page-all-categories.php');

$is_get_started_active = false;
if (is_tax('doc_category')) {
    $qo = get_queried_object();
    if ($qo && in_array($qo->slug, array('getting-started', 'onboarding', 'get-started'))) {
        $is_get_started_active = true;
    }
} elseif (is_singular('docs')) {
    $terms = get_the_terms(get_the_ID(), 'doc_category');
    if ($terms && !is_wp_error($terms)) {
        foreach ($terms as $t) {
            if (in_array($t->slug, array('getting-started', 'onboarding', 'get-started'))) {
                $is_get_started_active = true;
                break;
            }
        }
    }
}

$docs_url = get_post_type_archive_link('docs') ? get_post_type_archive_link('docs') : home_url('/docs/');

$getting_started_term = get_term_by('slug', 'getting-started', 'doc_category');
if (!$getting_started_term) {
    $getting_started_term = get_term_by('slug', 'getting-started', 'category');
}
$getting_started_url = ($getting_started_term && !is_wp_error($getting_started_term)) 
    ? get_term_link($getting_started_term) 
    : home_url('/docs/category/getting-started/');
?>

<!-- CMGALAXY Docs Sub-Header Navigation Row -->
<div class="cmgalaxy-subnav-bar">
    <div class="cmgalaxy-subnav-container">
        <?php
        if (has_nav_menu('main_menu')) {
            wp_nav_menu(array(
                'theme_location' => 'main_menu',
                'container'      => false,
                'menu_class'     => 'cmgalaxy-main-nav',
                'depth'          => 2,
                'fallback_cb'    => false,
            ));
        } else {
        ?>
            <ul class="cmgalaxy-main-nav">
                <li class="nav-item <?php echo $is_home_active ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="nav-link">Home</a>
                </li>
                <li class="nav-item <?php echo $is_get_started_active ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url($getting_started_url); ?>" class="nav-link">Get Started</a>
                </li>
                <li class="nav-item <?php echo ($is_docs_active && !$is_get_started_active) ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url($docs_url); ?>" class="nav-link">Docs</a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo esc_url(home_url('/guide/')); ?>" class="nav-link">Guide</a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo esc_url(home_url('/faqs/')); ?>" class="nav-link">FAQs</a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo esc_url(home_url('/api-docs/')); ?>" class="nav-link">API Docs</a>
                </li>
            </ul>
        <?php } ?>
    </div>
</div>
