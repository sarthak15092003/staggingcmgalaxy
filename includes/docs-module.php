<?php
/**
 * CMGalaxy Documentation & Help Center Module
 * Registers 'docs' post type, 'doc_category' and 'doc_tag' taxonomies,
 * assets enqueues, and template loaders.
 *
 * @package HelloElementor
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 1. Register 'docs' Custom Post Type & Taxonomies
 */
function cmg_register_docs_cpt() {
    // Taxonomies: Doc Categories
    $cat_labels = array(
        'name'              => _x('Doc Categories', 'taxonomy general name', 'hello-elementor'),
        'singular_name'     => _x('Doc Category', 'taxonomy singular name', 'hello-elementor'),
        'search_items'      => __('Search Doc Categories', 'hello-elementor'),
        'all_items'         => __('All Doc Categories', 'hello-elementor'),
        'parent_item'       => __('Parent Doc Category', 'hello-elementor'),
        'parent_item_colon' => __('Parent Doc Category:', 'hello-elementor'),
        'edit_item'         => __('Edit Doc Category', 'hello-elementor'),
        'update_item'       => __('Update Doc Category', 'hello-elementor'),
        'add_new_item'      => __('Add New Doc Category', 'hello-elementor'),
        'new_item_name'     => __('New Doc Category Name', 'hello-elementor'),
        'menu_name'         => __('Doc Categories', 'hello-elementor'),
    );

    register_taxonomy('doc_category', array('docs'), array(
        'hierarchical'      => true,
        'labels'            => $cat_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'show_in_rest'      => true,
        'rewrite'           => array('slug' => 'docs/category', 'with_front' => false),
    ));

    // Taxonomies: Doc Tags
    $tag_labels = array(
        'name'          => _x('Doc Tags', 'taxonomy general name', 'hello-elementor'),
        'singular_name' => _x('Doc Tag', 'taxonomy singular name', 'hello-elementor'),
        'search_items'  => __('Search Doc Tags', 'hello-elementor'),
        'all_items'     => __('All Doc Tags', 'hello-elementor'),
        'edit_item'     => __('Edit Doc Tag', 'hello-elementor'),
        'update_item'   => __('Update Doc Tag', 'hello-elementor'),
        'add_new_item'  => __('Add New Doc Tag', 'hello-elementor'),
        'new_item_name' => __('New Doc Tag Name', 'hello-elementor'),
        'menu_name'     => __('Doc Tags', 'hello-elementor'),
    );

    register_taxonomy('doc_tag', array('docs'), array(
        'hierarchical'      => false,
        'labels'            => $tag_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'show_in_rest'      => true,
        'rewrite'           => array('slug' => 'docs/tag', 'with_front' => false),
    ));

    // Post Type: Docs
    $cpt_labels = array(
        'name'               => _x('Docs', 'post type general name', 'hello-elementor'),
        'singular_name'      => _x('Doc', 'post type singular name', 'hello-elementor'),
        'menu_name'          => __('Docs', 'hello-elementor'),
        'name_admin_bar'     => __('Doc', 'hello-elementor'),
        'add_new'            => __('Add New Doc', 'hello-elementor'),
        'add_new_item'       => __('Add New Doc', 'hello-elementor'),
        'new_item'           => __('New Doc', 'hello-elementor'),
        'edit_item'          => __('Edit Doc', 'hello-elementor'),
        'view_item'          => __('View Doc', 'hello-elementor'),
        'all_items'          => __('All Docs', 'hello-elementor'),
        'search_items'       => __('Search Docs', 'hello-elementor'),
        'parent_item_colon'  => __('Parent Docs:', 'hello-elementor'),
        'not_found'          => __('No docs found.', 'hello-elementor'),
        'not_found_in_trash' => __('No docs found in Trash.', 'hello-elementor')
    );

    $cpt_args = array(
        'labels'             => $cpt_labels,
        'description'        => __('CMGalaxy Knowledge Base & Documentation', 'hello-elementor'),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'docs', 'with_front' => false),
        'capability_type'    => 'post',
        'has_archive'        => 'docs',
        'hierarchical'       => true,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-media-document',
        'show_in_rest'       => true,
        'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'revisions', 'custom-fields', 'page-attributes'),
        'taxonomies'         => array('doc_category', 'doc_tag', 'category'),
    );

    register_post_type('docs', $cpt_args);
}
add_action('init', 'cmg_register_docs_cpt', 0);

/**
 * 2. Enqueue Docs Styles & Scripts
 */
function cmg_enqueue_docs_assets() {
    global $post;
    
    $is_docs_page = is_singular('docs') || 
                    is_post_type_archive('docs') || 
                    is_tax('doc_category') || 
                    is_tax('doc_tag') ||
                    is_page_template('page-templates/template-docs-home.php') ||
                    is_page_template('page-templates/template-docs-category.php') ||
                    is_page_template('page-all-categories.php');

    // Also check if page slug is docs or kb
    if (!$is_docs_page && is_page()) {
        $slug = $post ? $post->post_name : '';
        if (in_array($slug, array('docs', 'kb', 'knowledge-base', 'documentation', 'all-categories'))) {
            $is_docs_page = true;
        }
    }

    if ($is_docs_page) {
        wp_enqueue_style(
            'cmg-docs-google-fonts',
            'https://fonts.googleapis.com/css2?family=Onest:wght@300;400;500;600;700;800;900&family=Instrument+Sans:wght@400;500;600;700&display=swap',
            array(),
            null
        );

        wp_enqueue_style(
            'cmg-docs-style',
            get_template_directory_uri() . '/css/docs-style.css',
            array('cmg-docs-google-fonts'),
            filemtime(get_template_directory() . '/css/docs-style.css')
        );

        wp_enqueue_script(
            'cmg-docs-main',
            get_template_directory_uri() . '/assets/js/docs-main.js',
            array('jquery'),
            filemtime(get_template_directory() . '/assets/js/docs-main.js'),
            true
        );

        wp_localize_script('cmg-docs-main', 'cmg_docs_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('cmg_docs_nonce')
        ));
    }
}
add_action('wp_enqueue_scripts', 'cmg_enqueue_docs_assets', 20);

/**
 * 3. Robust Template Selection Filter (Prevents Page Mismatch)
 */
function cmg_docs_template_include($template) {
    global $post;

    // 1. Single Doc
    if (is_singular('docs')) {
        $single_file = locate_template('single-docs.php');
        if ($single_file) return $single_file;
    }
    
    // 2. Docs Post Type Archive (/docs/)
    if (is_post_type_archive('docs')) {
        $archive_file = locate_template('archive-docs.php');
        if ($archive_file) return $archive_file;
    }

    // 3. Doc Category Taxonomy (/docs/category/...)
    if (is_tax('doc_category')) {
        $tax_file = locate_template('taxonomy-doc_category.php');
        if ($tax_file) return $tax_file;
    }

    // 4. Regular WordPress Page Mappings (Prevents mismatch if user created a standard page)
    if (is_page() && $post) {
        // If user explicitly chose a template, let WordPress handle it
        $page_template = get_page_template_slug($post->ID);
        if ($page_template) {
            return $template;
        }

        // Auto-match common slugs if no custom template was chosen
        if (in_array($post->post_name, array('docs', 'kb', 'knowledge-base', 'documentation'))) {
            $home_template = locate_template('page-templates/template-docs-home.php');
            if ($home_template) return $home_template;
        }

        if ($post->post_name === 'all-categories') {
            $all_cats = locate_template('page-all-categories.php');
            if ($all_cats) return $all_cats;
        }
    }

    return $template;
}
add_filter('template_include', 'cmg_docs_template_include', 99);

/**
 * 4. AJAX Feedback Handler
 */
function cmg_submit_doc_feedback() {
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $vote    = isset($_POST['vote']) ? sanitize_text_field($_POST['vote']) : '';

    if ($post_id && in_array($vote, array('like', 'dislike', 'yes', 'no'))) {
        $meta_key = ($vote === 'like' || $vote === 'yes') ? '_cmg_doc_likes' : '_cmg_doc_dislikes';
        $current_count = intval(get_post_meta($post_id, $meta_key, true));
        update_post_meta($post_id, $meta_key, $current_count + 1);

        wp_send_json_success(array('message' => 'Feedback received'));
    }

    wp_send_json_error(array('message' => 'Invalid feedback parameters'));
}
add_action('wp_ajax_cm_submit_feedback', 'cmg_submit_doc_feedback');
add_action('wp_ajax_nopriv_cm_submit_feedback', 'cmg_submit_doc_feedback');

/**
 * 5. Load Docs Importer & Sync Tool
 */
require_once get_template_directory() . '/includes/docs-importer.php';
