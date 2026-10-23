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
 * Helper to determine if current request is a Documentation or Knowledge Base page.
 *
 * @return bool
 */
function cmgalaxy_is_docs_page() {
    global $post;

    if ( is_admin() ) {
        return false;
    }

    // 0. Check request URI path for docs
    $request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    $path = trim(parse_url($request_uri, PHP_URL_PATH), '/');
    if ( strpos($path, 'docs') === 0 || strpos($path, '/docs') !== false ) {
        return true;
    }

    // 1. Post type 'docs', 'doc', or docs archives / taxonomies
    if ( is_singular(array('docs', 'doc')) || 
         is_post_type_archive(array('docs', 'doc')) || 
         is_tax('doc_category') || 
         is_tax('doc_tag') || 
         is_tax('doc_dir') ) {
        return true;
    }

    // If single post with doc_category or category in docs
    if ( is_single() ) {
        if ( has_term('', 'doc_category', $post) ) {
            return true;
        }
        $terms = get_the_terms($post, 'category');
        if ( $terms && !is_wp_error($terms) ) {
            foreach ($terms as $t) {
                if ( in_array($t->slug, array('getting-started', 'onboarding', 'utm-parameters-guidelines', 'documentation')) ) {
                    return true;
                }
            }
        }
    }

    // 2. Specific Page Templates
    if ( is_page_template('page-templates/template-docs-home.php') ||
         is_page_template('page-templates/template-docs-category.php') ||
         is_page_template('page-all-categories.php') ) {
        return true;
    }

    // 3. Known Documentation Pages by Slug
    if ( is_page() ) {
        $slug = $post ? $post->post_name : '';
        if ( in_array($slug, array('docs', 'kb', 'knowledge-base', 'documentation', 'all-categories', 'getting-started', 'onboarding')) ) {
            return true;
        }
    }

    // 4. Docs category under standard category if applicable
    if ( is_category() ) {
        $cat = get_queried_object();
        if ( $cat && in_array( $cat->slug, array( 'getting-started', 'onboarding', 'utm-parameters-guidelines', 'documentation' ) ) ) {
            return true;
        }
    }

    return false;
}

// Prevent Elementor Theme Builder and ElementsKit from overriding header on docs pages
add_filter('elementor/theme/need_override_location', function($need_override, $location) {
    if ($location === 'header' && function_exists('cmgalaxy_is_docs_page') && cmgalaxy_is_docs_page()) {
        return false;
    }
    return $need_override;
}, 999, 2);

add_filter('elementskit/header-footer/is_header_active', function($active) {
    if (function_exists('cmgalaxy_is_docs_page') && cmgalaxy_is_docs_page()) {
        return false;
    }
    return $active;
}, 999);

add_filter('elementskit/template/is_header_active', function($active) {
    if (function_exists('cmgalaxy_is_docs_page') && cmgalaxy_is_docs_page()) {
        return false;
    }
    return $active;
}, 999);

add_filter('elementskit/header_footer/render', function($render) {
    if (function_exists('cmgalaxy_is_docs_page') && cmgalaxy_is_docs_page()) {
        return false;
    }
    return $render;
}, 999);

add_filter('elementskit/header-footer/header_template', function($template) {
    if (function_exists('cmgalaxy_is_docs_page') && cmgalaxy_is_docs_page()) {
        return 0;
    }
    return $template;
}, 999);

// Remove ElementsKit callbacks from wp_body_open and get_header on docs pages
add_action('wp', function() {
    if (function_exists('cmgalaxy_is_docs_page') && cmgalaxy_is_docs_page()) {
        global $wp_filter;
        foreach (array('wp_body_open', 'get_header') as $hook_tag) {
            if (isset($wp_filter[$hook_tag]) && is_object($wp_filter[$hook_tag])) {
                foreach ($wp_filter[$hook_tag]->callbacks as $priority => $callbacks) {
                    foreach ($callbacks as $idx => $callback) {
                        if (is_array($callback['function']) && is_object($callback['function'][0])) {
                            $class_name = get_class($callback['function'][0]);
                            if (stripos($class_name, 'elementskit') !== false) {
                                remove_action($hook_tag, $callback['function'], $priority);
                            }
                        }
                    }
                }
            }
        }
    }
}, 999);

/**
 * 2. Enqueue Docs Styles & Scripts
 */
function cmg_enqueue_docs_assets() {
    if ( cmgalaxy_is_docs_page() ) {
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
            time()
        );

        wp_enqueue_script(
            'cmg-docs-main',
            get_template_directory_uri() . '/assets/js/docs-main.js',
            array('jquery'),
            time(),
            true
        );

        wp_localize_script('cmg-docs-main', 'cmg_docs_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('cmg_docs_nonce')
        ));
    }
}
add_action('wp_enqueue_scripts', 'cmg_enqueue_docs_assets', 20);

// Prevent aggressive 30-day edge caching on docs pages so updates deploy instantly
add_action('send_headers', function() {
    if ( function_exists('cmgalaxy_is_docs_page') && cmgalaxy_is_docs_page() ) {
        header('Cache-Control: public, max-age=0, must-revalidate, s-maxage=60');
        header('X-CMG-Docs: true');
    }
});

/**
 * 3. Robust Template Selection Filter (Prevents Page Mismatch)
 */
function cmg_docs_template_include($template) {
    global $post;

    // 0. Direct URL routing for /signin, /sign-in, /login
    $request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    $path = trim(parse_url($request_uri, PHP_URL_PATH), '/');
    if ($path === 'signin' || $path === 'sign-in' || $path === 'login') {
        $signin_file = locate_template('page-signin.php');
        if ($signin_file) {
            global $wp_query;
            if ($wp_query) {
                $wp_query->is_404 = false;
            }
            status_header(200);
            return $signin_file;
        }
    }

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
        if (in_array($post->post_name, array('signin', 'sign-in', 'login'))) {
            $signin_file = locate_template('page-signin.php');
            if ($signin_file) return $signin_file;
        }

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

// Non-admin login redirect to requested URL or home
add_filter('login_redirect', function($redirect_to, $request, $user) {
    if (isset($user->roles) && is_array($user->roles)) {
        if (!in_array('administrator', $user->roles)) {
            return !empty($request) ? $request : home_url('/');
        }
    }
    return $redirect_to;
}, 10, 3);

// Auto-create /signin page in WordPress database if not exists
add_action('init', function() {
    if (!get_option('_cmg_signin_page_created')) {
        $page = get_page_by_path('signin');
        if (!$page) {
            $page_id = wp_insert_post(array(
                'post_title'     => 'Sign In',
                'post_name'      => 'signin',
                'post_status'    => 'publish',
                'post_type'      => 'page',
                'comment_status' => 'closed',
            ));
            if ($page_id && !is_wp_error($page_id)) {
                update_post_meta($page_id, '_wp_page_template', 'page-signin.php');
            }
        }
        update_option('_cmg_signin_page_created', 1);
    }
});

/**
 * 4. Feedback Database & AJAX Submission Handler
 */
function cm_feedback_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'cm_feedback';
    $charset_collate = $wpdb->get_charset_collate();

    if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) != $table_name ) {
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            post_id mediumint(9) NOT NULL,
            post_title varchar(255) NOT NULL,
            user_id bigint(20) DEFAULT 0 NOT NULL,
            user_name varchar(255) DEFAULT '' NOT NULL,
            user_email varchar(255) DEFAULT '' NOT NULL,
            ip_address varchar(100) NOT NULL,
            vote varchar(10) NOT NULL,
            reason text NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    } else {
        // Auto-migration for existing installations
        $col_user_id = $wpdb->get_results( "SHOW COLUMNS FROM `{$table_name}` LIKE 'user_id'" );
        if ( empty( $col_user_id ) ) {
            $wpdb->query( "ALTER TABLE `{$table_name}` ADD COLUMN user_id bigint(20) DEFAULT 0 NOT NULL AFTER post_title" );
        }
        $col_user_name = $wpdb->get_results( "SHOW COLUMNS FROM `{$table_name}` LIKE 'user_name'" );
        if ( empty( $col_user_name ) ) {
            $wpdb->query( "ALTER TABLE `{$table_name}` ADD COLUMN user_name varchar(255) DEFAULT '' NOT NULL AFTER user_id" );
        }
        $col_user_email = $wpdb->get_results( "SHOW COLUMNS FROM `{$table_name}` LIKE 'user_email'" );
        if ( empty( $col_user_email ) ) {
            $wpdb->query( "ALTER TABLE `{$table_name}` ADD COLUMN user_email varchar(255) DEFAULT '' NOT NULL AFTER user_name" );
        }
    }
}
add_action( 'admin_init', 'cm_feedback_create_table' );

add_action( 'wp_ajax_cm_submit_feedback', 'cm_handle_submit_feedback' );
add_action( 'wp_ajax_nopriv_cm_submit_feedback', 'cm_handle_submit_feedback' );

function cm_handle_submit_feedback() {
    global $wpdb;

    if ( ! is_user_logged_in() ) {
        wp_send_json_error( 'You must be signed in to submit feedback.' );
    }

    $post_id    = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
    $post_title = isset( $_POST['post_title'] ) ? sanitize_text_field( wp_unslash( $_POST['post_title'] ) ) : '';
    $vote       = isset( $_POST['vote'] ) ? sanitize_text_field( wp_unslash( $_POST['vote'] ) ) : '';
    $reason     = isset( $_POST['reason'] ) ? sanitize_textarea_field( wp_unslash( $_POST['reason'] ) ) : '';
    $ip_address = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '';

    $user_id    = get_current_user_id();
    $user_name  = '';
    $user_email = '';

    if ( $user_id ) {
        $user_obj = wp_get_current_user();
        if ( $user_obj ) {
            $user_name  = ! empty( $user_obj->display_name ) ? $user_obj->display_name : $user_obj->user_login;
            $user_email = ! empty( $user_obj->user_email ) ? $user_obj->user_email : '';
        }
    }

    if ( ! $user_id && ! empty( $_POST['user_id'] ) ) {
        $user_id = intval( $_POST['user_id'] );
    }
    if ( empty( $user_name ) && ! empty( $_POST['user_name'] ) ) {
        $user_name = sanitize_text_field( wp_unslash( $_POST['user_name'] ) );
    }
    if ( empty( $user_email ) && ! empty( $_POST['user_email'] ) ) {
        $user_email = sanitize_email( wp_unslash( $_POST['user_email'] ) );
    }

    if ( ! $post_id || ! $vote || ! $reason ) {
        wp_send_json_error( 'Missing required fields.' );
    }

    $table_name = $wpdb->prefix . 'cm_feedback';

    $inserted = $wpdb->insert(
        $table_name,
        array(
            'post_id'    => $post_id,
            'post_title' => $post_title,
            'user_id'    => $user_id,
            'user_name'  => $user_name,
            'user_email' => $user_email,
            'ip_address' => $ip_address,
            'vote'       => $vote,
            'reason'     => $reason,
        )
    );

    if ( $inserted ) {
        // Also update post meta counter
        $meta_key = ( $vote === 'like' || $vote === 'yes' ) ? '_cmg_doc_likes' : '_cmg_doc_dislikes';
        $current_count = intval( get_post_meta( $post_id, $meta_key, true ) );
        update_post_meta( $post_id, $meta_key, $current_count + 1 );

        wp_send_json_success( 'Feedback saved successfully.' );
    } else {
        wp_send_json_error( 'Failed to save feedback.' );
    }
}

// Add Admin Menu Page for feedback
add_action( 'admin_menu', 'cm_feedback_admin_menu' );
function cm_feedback_admin_menu() {
    add_menu_page(
        'CM Feedback',
        'CM Feedback',
        'manage_options',
        'cm-feedback-entries',
        'cm_feedback_entries_page',
        'dashicons-feedback',
        30
    );
}

function cm_feedback_entries_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'cm_feedback';
    
    $results = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY created_at DESC" );
    
    echo '<div class="wrap">';
    echo '<h1>CM Feedback Entries</h1>';
    
    if ( $results ) {
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr>';
        echo '<th>ID</th>';
        echo '<th>Date</th>';
        echo '<th>Post Title</th>';
        echo '<th>User Name</th>';
        echo '<th>Email</th>';
        echo '<th>IP Address</th>';
        echo '<th>Vote</th>';
        echo '<th>Reason</th>';
        echo '</tr></thead>';
        echo '<tbody>';
        
        foreach ( $results as $row ) {
            $user_name_val = '—';
            if ( isset( $row->user_name ) && ! empty( $row->user_name ) ) {
                $user_name_val = esc_html( stripslashes( $row->user_name ) );
            } elseif ( isset( $row->user_id ) && $row->user_id > 0 ) {
                $u_info = get_userdata( $row->user_id );
                if ( $u_info ) {
                    $user_name_val = esc_html( stripslashes( $u_info->display_name ? $u_info->display_name : $u_info->user_login ) );
                }
            }
            
            $user_email_val = ( isset( $row->user_email ) && ! empty( $row->user_email ) ) ? esc_html( $row->user_email ) : '—';

            echo '<tr>';
            echo '<td>' . esc_html( $row->id ) . '</td>';
            echo '<td>' . esc_html( $row->created_at ) . '</td>';
            echo '<td><a href="' . get_permalink( $row->post_id ) . '" target="_blank">' . esc_html( stripslashes( $row->post_title ) ) . '</a></td>';
            echo '<td>' . $user_name_val . '</td>';
            echo '<td>' . $user_email_val . '</td>';
            echo '<td>' . esc_html( $row->ip_address ) . '</td>';
            $vote_val = strtolower( $row->vote );
            if ( $vote_val === 'yes' || $vote_val === 'like' ) {
                $vote_display = 'Like';
            } elseif ( $vote_val === 'no' || $vote_val === 'dislike' ) {
                $vote_display = 'Dislike';
            } else {
                $vote_display = ucfirst( $row->vote );
            }
            echo '<td>' . esc_html( $vote_display ) . '</td>';
            echo '<td>' . esc_html( stripslashes( $row->reason ) ) . '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>No feedback entries found.</p>';
    }
    
    echo '</div>';
}

/**
 * =========================================================================
 * CMGalaxy Post Restriction & Paywall Gate (Imported from Docy)
 * =========================================================================
 */
function cmg_is_post_restricted_to_logged_in($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    if (!$post_id) {
        return false;
    }

    // Direct preview toggle via URL parameter (supports ?preview_gate=1 or ?gated=1)
    if (isset($_GET['preview_gate']) && $_GET['preview_gate'] === '1') {
        return true;
    }
    if (isset($_GET['gated']) && $_GET['gated'] === '1') {
        return true;
    }

    // Check direct gated post meta
    $is_gated = get_post_meta($post_id, '_cmg_is_gated', true);
    if ($is_gated === '1' || $is_gated === 1 || $is_gated === 'yes' || $is_gated === true) {
        return true;
    }

    // Default known gated articles from live docs (e.g. How to Sign Up)
    $post_obj = get_post($post_id);
    if ($post_obj && (
        $post_obj->post_name === 'how-to-sign-up-for-cmgalaxy-step-by-step-account-registration' ||
        $post_obj->post_name === 'how-to-sign-up-for-cmgalaxy-step-by-step-account-registratio' ||
        strpos($post_obj->post_name, 'how-to-sign-up-for-cmgalaxy') !== false
    )) {
        return true;
    }

    $rbcr_logged_in_only = get_post_meta($post_id, '_rbcr_logged_in_only', true);
    if ($rbcr_logged_in_only === '1' || $rbcr_logged_in_only === 1 || $rbcr_logged_in_only === true) {
        return true;
    }

    $rbcr_login_status = get_post_meta($post_id, '_rbcr_login_status', true);
    if (!empty($rbcr_login_status) && (strpos($rbcr_login_status, 'logged_in') !== false || $rbcr_login_status === 'logged_in_only')) {
        return true;
    }

    if (function_exists('content_control_user_can_view_post')) {
        if (!content_control_user_can_view_post($post_id)) {
            return true;
        }
    }

    $meta_keys = array(
        '_rbcr_logged_in_only',
        '_rbcr_login_status',
        '_content_control_restriction_type',
        '_content_control_user_status',
        '_content_control_who',
        'content_control_restriction_type',
        '_ca_content_control_settings',
        '_content_control_settings',
        'content_control_settings',
        '_content_control_post_restricted',
        'content_control',
        '_content_control',
        'restrict_access_type',
        '_restrict_access_type',
        '_restriction_type',
        'restriction_type'
    );

    foreach ($meta_keys as $key) {
        $val = get_post_meta($post_id, $key, true);
        if (!empty($val)) {
            if (is_string($val)) {
                $val_lower = strtolower($val);
                if (strpos($val_lower, 'logged_in') !== false || strpos($val_lower, 'logged-in') !== false || $val_lower === 'users' || $val_lower === 'loggedin') {
                    return true;
                }
            } elseif (is_array($val)) {
                $serialized = strtolower(serialize($val));
                if (strpos($serialized, 'logged_in') !== false || strpos($serialized, 'logged-in') !== false || strpos($serialized, 'logged_in_only') !== false) {
                    return true;
                }
            }
        }
    }

    $all_meta = get_post_meta($post_id);
    if (!empty($all_meta) && is_array($all_meta)) {
        foreach ($all_meta as $m_key => $m_values) {
            if (strpos($m_key, 'content_control') !== false || strpos($m_key, 'restrict') !== false) {
                foreach ((array)$m_values as $m_val) {
                    $m_val_lower = strtolower(is_string($m_val) ? $m_val : serialize($m_val));
                    if (strpos($m_val_lower, 'logged_in') !== false || strpos($m_val_lower, 'logged-in') !== false) {
                        return true;
                    }
                }
            }
        }
    }

    return false;
}

/**
 * Renders the sticky paywall gate with readable teaser and blurred background content
 */
function cmg_render_paywall_gate($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    $raw_post_content = get_post_field('post_content', $post_id);
    $formatted_content = wpautop($raw_post_content);
    
    // Split content into block chunks (paragraphs, headings, lists, etc.)
    $blocks = preg_split('/(<\/p>|<\/h[1-6]>|<\/div>|<\/ul>|<\/ol>)/i', $formatted_content, -1, PREG_SPLIT_DELIM_CAPTURE);
    
    $teaser_html = '';
    $blurred_html = '';
    $block_count = 0;
    $max_teaser = 3; // Legible intro + heading + requirement intro line (clear / no blur)

    if (is_array($blocks) && count($blocks) > 1) {
        for ($k = 0; $k < count($blocks) - 1; $k += 2) {
            $chunk = $blocks[$k] . (isset($blocks[$k + 1]) ? $blocks[$k + 1] : '');
            if (empty(trim(strip_tags($chunk)))) continue;
            
            $block_count++;
            if ($block_count <= $max_teaser) {
                $teaser_html .= $chunk;
            } else {
                $blurred_html .= $chunk;
            }
        }
    }

    if (empty($teaser_html)) {
        $teaser_html = '<p>' . wp_trim_words(strip_tags($raw_post_content), 40) . '</p>';
    }

    if (empty(trim(strip_tags($blurred_html)))) {
        $blurred_html = '<p>To access the complete step-by-step instructions, comprehensive guidelines, configuration options, advanced examples, and downloadable assets, upgrade to a paid account or sign in to your existing active membership.</p>'
                      . '<p>Our knowledge base contains in-depth documentation crafted by industry experts to help you scale your workflow, optimize configurations, and solve complex integrations effortlessly.</p>'
                      . '<p>Unlock uninterrupted access across all guides, developer tutorials, and real-time updates tailored for high-growth operations and seamless deployment.</p>'
                      . '<p>Join thousands of professionals and teams who rely on CMGalaxy daily for complete technical accuracy and accelerated development.</p>';
    }

    ob_start();
    ?>
    <div class="cmg-paywall-container">
        <!-- Top Teaser (Legible) -->
        <div class="cmg-teaser-content">
            <?php echo wp_kses_post($teaser_html); ?>
        </div>

        <!-- Sticky Paywall Gate -->
        <div class="cmg-paywall-gate">
            <!-- Blurred Backdrop Content (scrolls underneath sticky card) -->
            <div class="cmg-blurred-backdrop" aria-hidden="true">
                <?php echo wp_kses_post($blurred_html); ?>
            </div>

            <!-- Sticky Overlay with Centered Modal Card -->
            <div class="cmg-paywall-sticky-overlay">
                <div class="cmg-paywall-card-wrap">
                    <?php get_template_part('template-parts/docs/modal-upgrade'); ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

add_filter('the_content', function($content) {
    if (is_admin() || !is_singular()) {
        return $content;
    }
    $is_preview = (isset($_GET['preview_gate']) && $_GET['preview_gate'] === '1') || (isset($_GET['gated']) && $_GET['gated'] === '1');
    if (is_user_logged_in() && !$is_preview) {
        return $content;
    }
    if (cmg_is_post_restricted_to_logged_in(get_the_ID())) {
        return cmg_render_paywall_gate(get_the_ID());
    }
    return $content;
}, 999);

add_filter('the_excerpt', function($excerpt) {
    $post_id = get_the_ID();
    if ($post_id && !is_user_logged_in() && cmg_is_post_restricted_to_logged_in($post_id)) {
        return 'Unlock full access to this premium guide and resource by upgrading to a paid account.';
    }
    return $excerpt;
}, 1);

add_filter('content_control/content/replacement_content', function($replacement, $post_id = null) {
    if (!is_user_logged_in() && is_singular()) {
        return cmg_render_paywall_gate($post_id);
    }
    return $replacement;
}, 999, 2);

add_filter('content_control_restriction_message', function($message) {
    if (!is_user_logged_in() && is_singular()) {
        return cmg_render_paywall_gate(get_the_ID());
    }
    return $message;
}, 999);

add_filter('content_control/prevent_redirect', '__return_true', 999);
add_filter('content_control/do_redirect', '__return_false', 999);
add_filter('rbcr_do_redirect', '__return_false', 999);
add_filter('rbcr_redirect_url', '__return_false', 999);
add_filter('rbcr_allow_redirect', '__return_false', 999);

/**
 * 5. Strip unwanted illustration/staging images from doc articles
 */
function cmg_docs_strip_article_images($content) {
    if (is_singular('docs') || (is_single() && get_post_type() === 'docs')) {
        $content = preg_replace('/<figure\b[^>]*>.*?<\/figure>/is', '', $content);
        $content = preg_replace('/<img\b[^>]*\/?>/is', '', $content);
        $content = preg_replace('/[\x{1F300}-\x{1F9FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}\x{1F600}-\x{1F64F}\x{1F680}-\x{1F6FF}\x{1F1E0}-\x{1F1FF}\x{1FA00}-\x{1FA6F}\x{1FA70}-\x{1FAFF}\x{2300}-\x{23FF}\x{200D}\x{FE0F}]/u', '', $content);
    }
    return $content;
}
add_filter('the_content', 'cmg_docs_strip_article_images', 5);

/**
 * 6. Set Clean Page Title "Docs" on Archive & Home
 */
function cmg_docs_document_title_parts($title_parts) {
    global $post;
    if (is_post_type_archive('docs') || 
        is_page_template('page-templates/template-docs-home.php') ||
        (is_page() && $post && in_array($post->post_name, array('docs', 'kb', 'knowledge-base', 'documentation')))) {
        $title_parts['title'] = 'Docs';
    }
    return $title_parts;
}
add_filter('document_title_parts', 'cmg_docs_document_title_parts', 99);

add_filter('post_type_archive_title', function($title, $post_type) {
    if ($post_type === 'docs') {
        return 'Docs';
    }
    return $title;
}, 99, 2);

add_filter('get_the_archive_title', function($title) {
    if (is_post_type_archive('docs')) {
        return 'Docs';
    }
    return $title;
}, 99);

add_filter('get_the_archive_title_prefix', function($prefix) {
    if (is_post_type_archive('docs') || is_tax('doc_category') || is_tax('doc_tag')) {
        return '';
    }
    return $prefix;
}, 99);

add_filter('wp_title', function($title, $sep = '', $seplocation = '') {
    global $post;
    if (is_post_type_archive('docs') || 
        is_page_template('page-templates/template-docs-home.php') ||
        (is_page() && $post && in_array($post->post_name, array('docs', 'kb', 'knowledge-base', 'documentation')))) {
        return 'Docs ' . ($sep ? $sep . ' ' : '');
    }
    return $title;
}, 99, 3);

add_filter('wpseo_title', function($title) {
    global $post;
    if (is_post_type_archive('docs') || 
        is_page_template('page-templates/template-docs-home.php') ||
        (is_page() && $post && in_array($post->post_name, array('docs', 'kb', 'knowledge-base', 'documentation')))) {
        return 'Docs - ' . get_bloginfo('name');
    }
    return $title;
}, 99);

add_filter('rank_math/frontend/title', function($title) {
    global $post;
    if (is_post_type_archive('docs') || 
        is_page_template('page-templates/template-docs-home.php') ||
        (is_page() && $post && in_array($post->post_name, array('docs', 'kb', 'knowledge-base', 'documentation')))) {
        return 'Docs - ' . get_bloginfo('name');
    }
    return $title;
}, 99);

/**
 * 7. Category & Article Ordering System (Matching Docy)
 */
function cmgalaxy_sort_terms_by_order( $a, $b ) {
    if (!is_object($a) || !isset($a->term_id) || !is_object($b) || !isset($b->term_id)) {
        return 0;
    }

    // 1. Explicit term meta order if set
    $order_a = get_term_meta( $a->term_id, '_cmgalaxy_sidebar_order', true );
    $order_b = get_term_meta( $b->term_id, '_cmgalaxy_sidebar_order', true );
    
    $val_a = ($order_a !== '' && $order_a !== false) ? intval($order_a) : null;
    $val_b = ($order_b !== '' && $order_b !== false) ? intval($order_b) : null;

    if ($val_a !== null && $val_b !== null && $val_a !== $val_b) {
        return ($val_a < $val_b) ? -1 : 1;
    }

    $slug_a = isset($a->slug) ? strtolower(trim($a->slug)) : '';
    $slug_b = isset($b->slug) ? strtolower(trim($b->slug)) : '';
    $name_a = isset($a->name) ? strtolower(trim($a->name)) : '';
    $name_b = isset($b->name) ? strtolower(trim($b->name)) : '';

    // 2. Docy exact subcategory ordering mapping
    static $docy_subcat_order = null;
    if ($docy_subcat_order === null) {
$docy_subcat_order = array(
            // amazon-dashboard
            'dashboard-overview'                                         => 10,
            'filters-data-loading'                                       => 20,
            'time-graph-performance-trend'                               => 30,
            'campaign-level'                                             => 40,
            'ad-group-level'                                             => 50,
            'asin-level'                                                 => 60,
            'ad-format-performance'                                      => 70,
            'keyword-performance-search-term-analysis'                   => 80,
            'data-export-csv-download'                                   => 90,
            'data-consistency-troubleshooting'                           => 100,
            'amazon-ads-hierarchy'                                       => 110,
            'campaign-summary'                                           => 120,

            // funnel-attribution
            'funnel-overview'                                            => 10,
            'tofu-top-of-funnel-attribution'                             => 20,
            'mofu-middle-of-funnel-attribution-dashboard'                => 30,
            'bofu-bottom-of-funnel-attribution-dashboard'                => 40,
            'conversion-attribution-dashboard'                           => 50,

            // getting-started
            'onboarding'                                                 => 10,

            // google-dashboard
            'campaign-management'                                        => 10,
            'recommendations-ai-optimization'                            => 20,
            'lex-ai-assistant-support-on-google-dashboard'               => 30,
            'asset-group-ad-group-analysis'                              => 40,
            'ad-creative-performance'                                    => 50,
            'asset-performance-management'                               => 60,
            'clicks-by-devices'                                          => 70,
            'keyword-performance'                                        => 80,
            'region-wise-breakdown'                                      => 90,
            'creative-quadrant-google-dashboard'                         => 100,
            'google-performance-dashboard'                               => 110,
            'performance-metrics-kpis-google-dashboard'                  => 120,
            'demographics-insights-google-dashboard'                     => 130,
            'time-graph-google-dashboard'                                => 140,

            // integrations
            'pixel-installation-and-verification'                        => 10,
            'shopify-integration-and-setup'                              => 20,
            'woocommerce-integration'                                    => 30,
            'ad-platform-integration'                                    => 40,
            'troubleshooting'                                            => 50,

            // linkedin-dashboard
            'linkedin-performance-dashboard'                             => 10,
            'time-graph-linkedln'                                        => 20,
            'top-data-linkedln'                                          => 30,
            'cost-analysis'                                              => 40,
            'placement-wise-cost-breakdown'                              => 50,
            'professional-demographics'                                  => 60,
            'reporting-export'                                           => 70,
            'lex-ai-support-on-linkedin-dashboard'                       => 80,

            // main_dashboard
            'overall-performance-metrics'                                => 10,
            'dashboard-filters-navigation'                               => 20,
            'ai-recommendations-section-in-the-main-dashboard'           => 30,
            'platform-comparison'                                        => 40,
            'user-revenue-by-segments'                                   => 50,
            'marketing-channel-intelligence'                             => 60,
            'top-data-table'                                             => 70,
            'creative-quadrant'                                          => 80,
            'lex-ai-assistant'                                           => 90,

            // meta-dashboard
            'meta-dashboard-overview'                                    => 10,
            'access-meta-performance-dashboard'                          => 20,
            'connect-your-meta-ads-account'                              => 30,
            'filters-and-date-range'                                     => 40,
            'time-graph'                                                 => 50,
            'campaign-hierarchy'                                         => 60,
            'performance-metrics-kpis'                                   => 70,
            'meta-ads-hierarchy'                                         => 80,
            'date-wise-metrics-overview'                                 => 90,
            'demographics-insights'                                      => 100,
            'product-performance'                                        => 110,
            'platform-performance'                                       => 120,
            'region-analysis'                                            => 130,
            'creative-performance'                                       => 140,

            // teads-dashboard
            'teads-ads-performance-dashboard'                            => 10,
            'top-data'                                                   => 20,
            'device-performance'                                         => 30,
            'publisher-performance'                                      => 40,
            'reporting-exports'                                          => 50,
            'lex-ai-teads-ads-dashboard'                                 => 60,

            // user-management
            'overview-of-user-roles-in-cmgalaxy'                         => 10,
            'role-based-access-control-rbac-in-cmgalaxy'                 => 20,
            'what-permissions-does-an-admin-have-in-cmgalaxy'            => 30,
            'what-permissions-does-a-business-head-have-in-cmg'          => 40,
            'what-permissions-does-an-account-manager-have-in'           => 50,
            'how-to-assign-module-level-permissions-in-cmgalaxy'         => 60,
            'understanding-module-permissions-in-cmgalaxy'               => 70,
            'understanding-module-permissions-in-cmg'                    => 80,
            'how-to-invite-a-new-user-to-cmgalaxy'                       => 90,
            'step-by-step-guide-to-inviting-a-user-in-cmgalaxy'          => 100,
            'how-to-assign-role-during-user-invitation-in-cmg'           => 110,
            'how-to-review-and-approve-user-invitations'                 => 120,
            'what-happens-after-sending-an-invitation'                   => 130,
            'understanding-invitation-status'                            => 140,
            'how-to-manage-user-access-from-the-account-detail'          => 150,
            'how-to-edit-user-permissions-after-invitation-in'           => 160,
            'how-to-remove-or-deactivate-a-user-in-cmgalaxy'             => 170,
            'how-to-mark-a-user-as-inactive-in-cmgalaxy'                 => 180,
            'how-to-reactivate-an-inactive-user-in-cmgalaxy'             => 190,
            'how-to-assign-new-account-access-to-an-existing'            => 200,
            'how-to-manage-users-from-the-master'                        => 210,
            'how-to-remove-a-user-completely-from-cmgalaxy'              => 220,
            'how-to-manage-users-at-the-account-level-in-cmg'            => 230,
            'difference-between-master-level-and-account-level'          => 240,
            'how-to-assign-a-user-to-multiple-accounts'                  => 250,
            'how-to-view-assigned-accounts-for-a-user-in-cmg'            => 260,
            'how-to-restrict-user-access-to-specific-accounts'           => 270,
            'admin-view-vs-business-head-view-vs-account-manag'          => 280,
            'what-can-an-account-manager-see-in-cmgalaxy'                => 290,
            'business-head-dashboard-access-explained-in-cmg'            => 300,
            'why-cant-i-invite-a-user-in-cmgalaxy'                       => 310,
            'fix-invitation-not-received-in-cmgalaxy'                    => 320,
            'fix-user-cannot-access-assigned-account-in-cmg'             => 330,
            'fix-permission-changes-not-reflecting-in-cmgalaxy'          => 340,
            'fix-unable-to-remove-a-user-in-cmgalaxy'                    => 350,
            'fix-account-not-visible-to-assigned-user-in-cmg'            => 360,
            'how-to-structure-user-roles-for-large-teams-in-cmg'         => 370,
            'how-to-assign-business-heads-for-multi-region'              => 380,
            'how-to-configure-hierarchical-access-control'               => 390,

        );
    }

    $pos_sub_a = isset($docy_subcat_order[$slug_a]) ? $docy_subcat_order[$slug_a] : 9999;
    $pos_sub_b = isset($docy_subcat_order[$slug_b]) ? $docy_subcat_order[$slug_b] : 9999;

    if ($pos_sub_a !== 9999 || $pos_sub_b !== 9999) {
        if ($pos_sub_a !== $pos_sub_b) {
            return ($pos_sub_a < $pos_sub_b) ? -1 : 1;
        }
    }

    // 3. Top-level categories ordering
    static $docy_top_order = null;
    if ($docy_top_order === null) {
        $docy_top_order = array(
            'getting started'           => 1,
            'getting-started'           => 1,
            'account management'        => 2,
            'account mangement'         => 2,
            'account-management'        => 2,
            'user management'           => 3,
            'user manegement'           => 3,
            'user-management'           => 3,
            'funnel attribution'        => 4,
            'funnel-attribution'        => 4,
            'integrations'              => 5,
            'main dashboard'            => 6,
            'main-dashboard'            => 6,
            'main_dashboard'            => 6,
            'master dashboard'          => 7,
            'master-dashboard'          => 7,
            'google dashboard'          => 8,
            'google-dashboard'          => 8,
            'meta dashboard'            => 9,
            'meta-dashboard'            => 9,
            'linkedin dashboard'        => 10,
            'linkedin-dashboard'        => 10,
            'amazon dashboard'          => 11,
            'amazon-dashboard'          => 11,
            'teads dashboard'           => 12,
            'teads-dashboard'           => 12,
            'milestone'                 => 13,
            'recommendation'            => 14,
            'pinterest dashboard'       => 15,
            'pinterest-dashboard'       => 15,
            'dv360 dashboard'           => 16,
            'dv360-dashboard'           => 16,
            'notification center'       => 17,
            'notification-center'       => 17,
            'ticket/ support'           => 18,
            'tickets / supports'        => 18,
            'tickets / support'         => 18,
            'tickets-supports'          => 18,
            'reporting hub'             => 19,
            'reporting-hub'             => 19,
            'lex'                       => 20,
            'user journey'              => 21,
            'user-journey'              => 21,
            'utm parameters guidelines' => 22,
            'utm-parameters-guidelines' => 22
        );
    }

    $pos_top_a = isset($docy_top_order[$name_a]) ? $docy_top_order[$name_a] : (isset($docy_top_order[$slug_a]) ? $docy_top_order[$slug_a] : 9999);
    $pos_top_b = isset($docy_top_order[$name_b]) ? $docy_top_order[$name_b] : (isset($docy_top_order[$slug_b]) ? $docy_top_order[$slug_b] : 9999);

    if ($pos_top_a !== $pos_top_b) {
        return ($pos_top_a < $pos_top_b) ? -1 : 1;
    }

    // 4. Fallback to creation order (term_id) instead of alphabetical (exact Docy behavior)
    return $a->term_id - $b->term_id;
}

// Save sidebar order field in admin category edit
function cmgalaxy_category_edit_order_field( $term ) {
    $order = get_term_meta( $term->term_id, '_cmgalaxy_sidebar_order', true );
    ?>
    <tr class="form-field">
        <th scope="row"><label for="cmgalaxy_sidebar_order"><?php _e( 'Sidebar Order', 'hello-elementor' ); ?></label></th>
        <td>
            <input type="number" name="cmgalaxy_sidebar_order" id="cmgalaxy_sidebar_order" value="<?php echo esc_attr( $order ); ?>">
            <p class="description"><?php _e( 'Enter a number to order categories in the modern sidebar. Lower numbers appear first. (e.g. 10, 20, 30)', 'hello-elementor' ); ?></p>
        </td>
    </tr>
    <?php
}
add_action( 'category_edit_form_fields', 'cmgalaxy_category_edit_order_field' );
add_action( 'doc_category_edit_form_fields', 'cmgalaxy_category_edit_order_field' );

function cmgalaxy_save_category_order( $term_id ) {
    if ( isset( $_POST['cmgalaxy_sidebar_order'] ) ) {
        update_term_meta( $term_id, '_cmgalaxy_sidebar_order', sanitize_text_field( $_POST['cmgalaxy_sidebar_order'] ) );
    }
}
add_action( 'created_category', 'cmgalaxy_save_category_order' );
add_action( 'edited_category', 'cmgalaxy_save_category_order' );
add_action( 'created_doc_category', 'cmgalaxy_save_category_order' );
add_action( 'edited_doc_category', 'cmgalaxy_save_category_order' );

// AJAX handler for drag and drop category sorting
function cmgalaxy_update_category_order_callback() {
    if ( ! current_user_can( 'manage_categories' ) ) {
        wp_send_json_error( 'Permission denied' );
    }

    if ( isset( $_POST['ordered_ids'] ) ) {
        $ordered_ids = json_decode( stripslashes( $_POST['ordered_ids'] ), true );
        if ( is_array( $ordered_ids ) ) {
            $order = 10;
            foreach ( $ordered_ids as $term_id ) {
                update_term_meta( intval( $term_id ), '_cmgalaxy_sidebar_order', $order );
                $order += 10;
            }
            wp_send_json_success( 'Order updated' );
        }
    }
    wp_send_json_error( 'Invalid data' );
}
add_action( 'wp_ajax_cmgalaxy_update_category_order', 'cmgalaxy_update_category_order_callback' );

/**
 * 8. Load Docs Importer & Sync Tool
 */
require_once get_template_directory() . '/includes/docs-importer.php';

/**
 * 9. Article Ordering System (Matching Docy)
 */

// Enable page-attributes (Order field) for docs and posts
add_action('init', function() {
    add_post_type_support('docs', 'page-attributes');
    add_post_type_support('post', 'page-attributes');
});

// Set default menu_order = 9999 for new posts so they appear at the bottom (exact Docy logic)
function docy_set_new_post_menu_order( $data, $postarr ) {
    if ( isset($data['post_type']) && in_array($data['post_type'], array('docs', 'post')) ) {
        // Check if the post is an auto-draft or if it's completely new
        if ( ( isset( $data['post_status'] ) && $data['post_status'] === 'auto-draft' ) || empty( $postarr['ID'] ) ) {
            $data['menu_order'] = 9999;
        }
        
        // If saving a draft for the first time, and menu_order is 0, bump it up
        if ( isset( $postarr['ID'] ) && $postarr['ID'] > 0 && isset( $data['menu_order'] ) && $data['menu_order'] == 0 ) {
            if ( ! isset( $_POST['action'] ) || $_POST['action'] !== 'update-menu-order' ) {
                $data['menu_order'] = 9999;
            }
        }
    }
    return $data;
}
add_filter( 'wp_insert_post_data', 'docy_set_new_post_menu_order', 10, 2 );

// Support for drag & drop post ordering AJAX (e.g. Simple Page Ordering or custom admin drag-and-drop)
add_action('wp_ajax_update-menu-order', function() {
    if (!current_user_can('edit_posts')) {
        wp_send_json_error('Permission denied');
    }
    if (isset($_POST['id'])) {
        global $wpdb;
        $post_id = intval($_POST['id']);
        $next_id = isset($_POST['nextid']) ? intval($_POST['nextid']) : 0;
        $prev_id = isset($_POST['previd']) ? intval($_POST['previd']) : 0;
        
        if ($next_id > 0) {
            $next_post = get_post($next_id);
            $new_order = $next_post ? max(0, intval($next_post->menu_order) - 1) : 0;
        } elseif ($prev_id > 0) {
            $prev_post = get_post($prev_id);
            $new_order = $prev_post ? intval($prev_post->menu_order) + 1 : 10;
        } else {
            $new_order = 0;
        }
        
        $wpdb->update($wpdb->posts, array('menu_order' => $new_order), array('ID' => $post_id));
        wp_send_json_success();
    }
}, 5);

// Force docs query order by menu_order date ASC (exact Docy logic)
function docy_docs_custom_order( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }
    $post_type = $query->get( 'post_type' );
    $is_docs = false;
    if ( $post_type === 'docs' || ( is_array( $post_type ) && in_array( 'docs', $post_type ) ) ) {
        $is_docs = true;
    } elseif ( $query->get('post_type') == '' && ( $query->is_tax('doc_category') || $query->is_tax('doc_tag') || $query->is_tax('doc_dir') || $query->is_post_type_archive('docs') ) ) {
        $is_docs = true;
    }

    // Apply exact Docy ordering (menu_order ASC, date ASC) to all docs, doc categories, and category archives
    if ( $is_docs || $query->is_tax('doc_category') || $query->is_category() || $query->is_tax('doc_tag') || $query->is_tax('doc_dir') || $query->is_post_type_archive('docs') ) {
        $query->set( 'order', 'ASC' );
        $query->set( 'orderby', 'menu_order date' );
        $query->set( 'posts_per_page', -1 );
        $query->set( 'nopaging', true );
    }

    // Disable pagination and show all posts on category archives (Docy feature)
    if ( $query->is_tax('doc_category') || $query->is_category() ) {
        $query->set( 'posts_per_page', -1 );
        $query->set( 'nopaging', true );
    }
}
add_action( 'pre_get_posts', 'docy_docs_custom_order', 99 );

/**
 * 10. WP Admin Meta Box: CMGalaxy Access & Paywall (Gated Content)
 */
add_action('add_meta_boxes', function() {
    foreach (array('docs', 'post') as $pt) {
        add_meta_box(
            'cmg_gated_access_metabox',
            __('🔒 CMGalaxy Access & Paywall (Gated Content)', 'hello-elementor'),
            'cmg_render_gated_access_metabox',
            $pt,
            'side',
            'high'
        );
    }
});

function cmg_render_gated_access_metabox($post) {
    wp_nonce_field('cmg_save_gated_access', 'cmg_gated_nonce');
    $is_gated = get_post_meta($post->ID, '_cmg_is_gated', true);
    if ($is_gated === '' && strpos($post->post_name, 'how-to-sign-up-for-cmgalaxy') !== false) {
        $is_gated = '1';
    }
    $menu_order = $post->menu_order;
    $preview_url = add_query_arg('preview_gate', '1', get_permalink($post->ID));
    ?>
    <div style="padding: 6px 0;">
        <label style="display: flex; align-items: flex-start; gap: 8px; cursor: pointer; font-weight: 600; color: #1e293b; margin-bottom: 8px;">
            <input type="checkbox" name="cmg_is_gated" value="1" <?php checked($is_gated, '1'); ?> style="margin-top: 3px;">
            <span>Restrict to Logged-in Customers (Gated Paywall)</span>
        </label>
        <p style="font-size: 12px; color: #64748b; margin: 0 0 12px 24px; line-height: 1.4;">
            When checked, visitors see a 3-block teaser with a blurred background and the <strong>"Access everything. Become a customer today!"</strong> modal upgrade card.
        </p>

        <div style="border-top: 1px solid #f1f5f9; padding-top: 10px; margin-top: 10px;">
            <label for="cmg_menu_order" style="display: block; font-weight: 600; font-size: 12px; color: #334155; margin-bottom: 4px;">Article Order (Lower numbers appear first):</label>
            <input type="number" id="cmg_menu_order" name="cmg_menu_order" value="<?php echo esc_attr($menu_order); ?>" style="width: 100%; max-width: 100px;" placeholder="0">
        </div>

        <?php if ($post->ID && $post->post_status === 'publish') : ?>
        <div style="border-top: 1px solid #f1f5f9; padding-top: 10px; margin-top: 12px;">
            <a href="<?php echo esc_url($preview_url); ?>" target="_blank" class="button button-secondary" style="width: 100%; text-align: center; display: block; font-size: 12px;">
                👁️ Preview Paywall as Visitor &rarr;
            </a>
        </div>
        <?php endif; ?>
    </div>
    <?php
}

add_action('save_post', function($post_id) {
    if (!isset($_POST['cmg_gated_nonce']) || !wp_verify_nonce($_POST['cmg_gated_nonce'], 'cmg_save_gated_access')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $is_gated = isset($_POST['cmg_is_gated']) ? '1' : '0';
    update_post_meta($post_id, '_cmg_is_gated', $is_gated);

    if (isset($_POST['cmg_menu_order'])) {
        $order = intval($_POST['cmg_menu_order']);
        global $wpdb;
        $wpdb->update($wpdb->posts, array('menu_order' => $order), array('ID' => $post_id));
    }
});

/**
 * 11. Admin Columns for Docs List Table
 */
add_filter('manage_docs_posts_columns', function($columns) {
    $new_cols = array();
    foreach ($columns as $k => $v) {
        $new_cols[$k] = $v;
        if ($k === 'title') {
            $new_cols['cmg_access'] = __('Access', 'hello-elementor');
            $new_cols['cmg_order'] = __('Order', 'hello-elementor');
        }
    }
    return $new_cols;
});

add_action('manage_docs_posts_custom_column', function($column, $post_id) {
    if ($column === 'cmg_access') {
        $is_gated = cmg_is_post_restricted_to_logged_in($post_id);
        if ($is_gated) {
            $preview = add_query_arg('preview_gate', '1', get_permalink($post_id));
            echo '<span style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; padding: 2px 8px; border-radius: 999px; font-size: 11.5px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">🔒 Gated</span> ';
            echo '<a href="' . esc_url($preview) . '" target="_blank" style="font-size: 11px; color: #2563eb; text-decoration: none; margin-left: 4px;" title="Preview Paywall">Preview</a>';
        } else {
            echo '<span style="background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; padding: 2px 8px; border-radius: 999px; font-size: 11.5px; font-weight: 600;">🌐 Free</span>';
        }
    } elseif ($column === 'cmg_order') {
        $post = get_post($post_id);
        echo '<strong>' . intval($post->menu_order) . '</strong>';
    }
}, 10, 2);

add_filter('manage_edit-docs_sortable_columns', function($sortable) {
    $sortable['cmg_order'] = 'menu_order';
    return $sortable;
});

// Quick Edit fields for Docs
add_action('quick_edit_custom_box', function($column_name, $post_type) {
    if ($post_type !== 'docs' || $column_name !== 'cmg_access') {
        return;
    }
    ?>
    <fieldset class="inline-edit-col-right">
        <div class="inline-edit-col">
            <label class="alignleft" style="margin-bottom: 6px;">
                <input type="checkbox" name="cmg_quick_is_gated" value="1">
                <span class="checkbox-title"><strong>🔒 Restrict to Logged-in (Gated Paywall)</strong></span>
            </label>
        </div>
    </fieldset>
    <?php
}, 10, 2);

// Save Quick Edit
add_action('save_post_docs', function($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (isset($_POST['_inline_edit'])) {
        $is_gated = isset($_POST['cmg_quick_is_gated']) ? '1' : '0';
        update_post_meta($post_id, '_cmg_is_gated', $is_gated);
    }
});

/**
 * 12. WP Admin Submenu: Article Order & Gated Access Manager
 */
function cmg_docs_order_manager_admin_menu() {
    add_submenu_page(
        'edit.php?post_type=docs',
        __('Article Order & Access', 'hello-elementor'),
        __('Article Order & Access', 'hello-elementor'),
        'manage_options',
        'cmgalaxy-docs-order-manager',
        'cmg_render_docs_order_manager_page'
    );
}
add_action('admin_menu', 'cmg_docs_order_manager_admin_menu');

function cmg_render_docs_order_manager_page() {
    global $wpdb;
    
    // Get all top-level doc categories and their subcategories hierarchically
    $top_categories = get_terms(array(
        'taxonomy'   => array('doc_category', 'category'),
        'parent'     => 0,
        'hide_empty' => false,
        'orderby'    => 'name',
        'order'      => 'ASC'
    ));

    $categories = array();
    if (!empty($top_categories) && !is_wp_error($top_categories)) {
        foreach ($top_categories as $top) {
            if (strtolower($top->slug) === 'uncategorized') continue;
            $categories[] = array(
                'id'    => $top->term_id,
                'name'  => $top->name,
                'count' => $top->count,
                'depth' => 0
            );

            // Subcategories
            $children = get_terms(array(
                'taxonomy'   => $top->taxonomy,
                'parent'     => $top->term_id,
                'hide_empty' => false,
                'orderby'    => 'name',
                'order'      => 'ASC'
            ));
            if (!empty($children) && !is_wp_error($children)) {
                foreach ($children as $child) {
                    $categories[] = array(
                        'id'    => $child->term_id,
                        'name'  => $child->name,
                        'count' => $child->count,
                        'depth' => 1
                    );
                    
                    // Sub-subcategories (Level 3)
                    $sub_children = get_terms(array(
                        'taxonomy'   => $child->taxonomy,
                        'parent'     => $child->term_id,
                        'hide_empty' => false
                    ));
                    if (!empty($sub_children) && !is_wp_error($sub_children)) {
                        foreach ($sub_children as $sc) {
                            $categories[] = array(
                                'id'    => $sc->term_id,
                                'name'  => $sc->name,
                                'count' => $sc->count,
                                'depth' => 2
                            );
                        }
                    }
                }
            }
        }
    }

    $selected_cat_id = isset($_GET['cat_id']) ? intval($_GET['cat_id']) : 0;
    if (!$selected_cat_id && !empty($categories)) {
        // Default to Getting started or Onboarding if found
        foreach ($categories as $cat) {
            if ($cat['name'] === 'Getting started' || $cat['name'] === 'Onboarding') {
                $selected_cat_id = $cat['id'];
                break;
            }
        }
        if (!$selected_cat_id) {
            $selected_cat_id = $categories[0]['id'];
        }
    }

    // Fetch posts in this category ordered by menu_order ASC, post_date ASC, ID ASC
    $articles = array();
    if ($selected_cat_id) {
        $articles = $wpdb->get_results($wpdb->prepare("
            SELECT p.ID, p.post_title, p.post_name, p.post_date, p.menu_order, p.post_status
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
            INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            WHERE tt.term_id = %d
            AND p.post_type IN ('docs', 'post')
            AND p.post_status = 'publish'
            ORDER BY p.menu_order ASC, p.post_date ASC, p.ID ASC
        ", $selected_cat_id));
    }
    ?>
    <div class="wrap" style="max-width: 1100px; margin-top: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
        <div style="background: #ffffff; padding: 28px 32px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
            
            <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 20px; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
                <div>
                    <h1 style="font-size: 24px; font-weight: 700; color: #0f172a; margin: 0 0 6px 0;">Article Order & Gated Access Manager</h1>
                    <p style="color: #64748b; font-size: 14.5px; margin: 0;">
                        Rearrange article order (1, 2, 3...) using First Come First Serve, First Come Last Serve, or Drag & Drop.
                    </p>
                </div>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <button type="button" id="cmg-save-orders-top-btn" class="button button-primary" style="background: #10b981; border-color: #10b981; font-weight: 600; padding: 6px 20px; font-size: 13.5px; border-radius: 6px; cursor: pointer;">
                        💾 Save Order (1, 2, 3...)
                    </button>
                    <button type="button" id="cmg-sync-live-docy-btn" class="button" style="padding: 6px 14px; font-size: 13px; border-radius: 6px; cursor: pointer;">
                        ⚡ 1-Click Sync Order
                    </button>
                </div>
            </div>

            <!-- Category Selector Filter -->
            <form method="get" action="" style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px; background: #f8fafc; padding: 14px 18px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <input type="hidden" name="post_type" value="docs">
                <input type="hidden" name="page" value="cmgalaxy-docs-order-manager">
                
                <label for="cat_id" style="font-weight: 700; font-size: 14px; color: #334155;">Select Category or Subcategory:</label>
                <select name="cat_id" id="cat_id" onchange="this.form.submit()" style="padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 14px; min-width: 320px;">
                    <?php if (!empty($categories)) : ?>
                        <?php foreach ($categories as $cat) : 
                            $prefix = '';
                            if ($cat['depth'] === 1) $prefix = '— ';
                            if ($cat['depth'] === 2) $prefix = '—— ';
                        ?>
                            <option value="<?php echo esc_attr($cat['id']); ?>" <?php selected($selected_cat_id, $cat['id']); ?>>
                                <?php echo esc_html($prefix . $cat['name']); ?> (<?php echo intval($cat['count']); ?> articles)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </form>

            <!-- Reordering Action Toolbar -->
            <div style="background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                    <span style="font-weight: 700; font-size: 13.5px; color: #334155;">Order Method:</span>
                    <button type="button" id="cmg-btn-fcfs" class="button button-secondary" style="font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                        🟢 First Come First Serve (Oldest 1, 2, 3...)
                    </button>
                    <button type="button" id="cmg-btn-fcls" class="button button-secondary" style="font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                        🟡 First Come Last Serve (Newest 1, 2, 3...)
                    </button>
                    <button type="button" id="cmg-auto-number-btn" class="button button-secondary" style="font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                        🔢 Auto-Number (1, 2, 3, 4...)
                    </button>
                </div>
                <div style="font-size: 12.5px; color: #64748b;">
                    🖐️ <strong>Tip:</strong> Drag rows up/down to set custom sequence.
                </div>
            </div>

            <div id="cmg-manager-notification" style="display: none; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; font-weight: 600;"></div>

            <!-- Articles Ordering Table -->
            <form id="cmg-order-form">
                <?php wp_nonce_field('cmg_bulk_order_nonce', 'order_nonce'); ?>
                <table class="wp-list-table widefat fixed striped posts" style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; box-shadow: none;">
                    <thead>
                        <tr>
                            <th style="width: 45px; text-align: center; color: #94a3b8;"></th>
                            <th style="width: 55px; text-align: center; font-weight: 600; color: #475569;">#</th>
                            <th style="width: 85px; font-weight: 600; color: #475569;">Order</th>
                            <th style="font-weight: 600; color: #475569;">Article Title</th>
                            <th style="width: 160px; text-align: center; font-weight: 600; color: #475569;">Paywall Gate</th>
                            <th style="width: 140px; text-align: center; font-weight: 600; color: #475569;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="cmg-articles-tbody">
                        <?php if (!empty($articles)) : ?>
                            <?php foreach ($articles as $index => $art) : 
                                $is_gated = cmg_is_post_restricted_to_logged_in($art->ID);
                                $preview_url = add_query_arg('preview_gate', '1', get_permalink($art->ID));
                                $edit_url = get_edit_post_link($art->ID);
                                
                                // Clean sequential order (1, 2, 3, 4... instead of 10, 20, 30...)
                                $clean_order = intval($art->menu_order);
                                if ($clean_order % 10 === 0 && $clean_order >= 10 && $clean_order <= 500) {
                                    $clean_order = intval($clean_order / 10);
                                } elseif ($clean_order <= 0 || $clean_order >= 9000) {
                                    $clean_order = $index + 1;
                                }
                            ?>
                                <tr data-post-id="<?php echo esc_attr($art->ID); ?>" data-post-date="<?php echo esc_attr(strtotime($art->post_date)); ?>" class="cmg-draggable-row" draggable="true" style="cursor: move;">
                                    <td style="text-align: center; color: #94a3b8; width: 45px; user-select: none;">
                                        <span style="font-size: 17px; cursor: move;" title="Drag to reorder">⠿</span>
                                    </td>
                                    <td style="text-align: center; color: #64748b; font-weight: 700;" class="row-num-badge">
                                        <?php echo intval($index + 1); ?>
                                    </td>
                                    <td>
                                        <input type="number" class="cmg-order-input" name="order[<?php echo esc_attr($art->ID); ?>]" value="<?php echo esc_attr($clean_order); ?>" style="width: 65px; padding: 4px 6px; border-radius: 4px; border: 1px solid #cbd5e1; text-align: center; font-weight: 700; color: #0f172a;">
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: #0f172a; font-size: 14px;">
                                            <a href="<?php echo esc_url($edit_url); ?>" style="color: #0f172a; text-decoration: none;">
                                                <?php echo esc_html($art->post_title); ?>
                                            </a>
                                        </div>
                                        <div style="font-size: 12px; color: #94a3b8; margin-top: 2px;">
                                            slug: <?php echo esc_html($art->post_name); ?> &nbsp;&middot;&nbsp; published: <?php echo date_i18n('M j, Y', strtotime($art->post_date)); ?>
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        <label style="display: inline-flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; font-weight: 600; color: <?php echo $is_gated ? '#dc2626' : '#64748b'; ?>;">
                                            <input type="checkbox" class="cmg-gated-checkbox" name="gated[<?php echo esc_attr($art->ID); ?>]" value="1" <?php checked($is_gated, true); ?>>
                                            <span><?php echo $is_gated ? '🔒 Gated' : 'Free'; ?></span>
                                        </label>
                                    </td>
                                    <td style="text-align: center;">
                                        <a href="<?php echo esc_url($preview_url); ?>" target="_blank" class="button button-small" style="font-size: 11.5px; border-radius: 4px;" title="Preview Paywall Modal">
                                            👁️ Preview
                                        </a>
                                        <a href="<?php echo esc_url($edit_url); ?>" class="button button-small" style="font-size: 11.5px; border-radius: 4px; margin-left: 4px;">
                                            Edit
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 32px; color: #64748b;">
                                    No articles found in this category or subcategory.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <?php if (!empty($articles)) : ?>
                    <div style="margin-top: 24px; display: flex; justify-content: space-between; align-items: center;">
                        <button type="button" class="button cmg-quick-renumber-btn" style="font-size: 13px;">
                            Reset Sequence (1, 2, 3, 4...)
                        </button>
                        <button type="button" id="cmg-save-orders-btn" class="button button-primary" style="background: #10b981; border-color: #10b981; font-weight: 700; padding: 7px 26px; font-size: 14px; border-radius: 6px;">
                            💾 Save Order (1, 2, 3...) & Access Settings
                        </button>
                    </div>
                <?php endif; ?>
            </form>

        </div>
    </div>

    <style>
    .cmg-draggable-row.dragging {
        opacity: 0.45;
        background: #eff6ff !important;
    }
    .cmg-draggable-row.drag-over-row {
        border-top: 3px solid #2563eb !important;
    }
    </style>

    <script>
    jQuery(document).ready(function($) {
        
        function renumberRows() {
            $('#cmg-articles-tbody tr').each(function(index) {
                var num = index + 1;
                $(this).find('.row-num-badge').text(num);
                $(this).find('.cmg-order-input').val(num);
            });
        }

        function showNotification(msg, isSuccess) {
            var $note = $('#cmg-manager-notification');
            if (isSuccess) {
                $note.css({'background': '#ecfdf5', 'color': '#065f46', 'border': '1px solid #a7f3d0'}).text(msg).show();
            } else {
                $note.css({'background': '#fef2f2', 'color': '#991b1b', 'border': '1px solid #fecaca'}).text(msg).show();
            }
        }

        // 1. First Come First Serve (Oldest 1, 2, 3...)
        $('#cmg-btn-fcfs').on('click', function(e) {
            e.preventDefault();
            var $tbody = $('#cmg-articles-tbody');
            var rows = $tbody.find('tr').get();
            rows.sort(function(a, b) {
                var dateA = parseInt($(a).data('post-date'), 10) || 0;
                var dateB = parseInt($(b).data('post-date'), 10) || 0;
                return dateA - dateB;
            });
            $.each(rows, function(idx, row) {
                $tbody.append(row);
            });
            renumberRows();
            showNotification('Sorted by First Come First Serve (Oldest First: 1, 2, 3...). Click "Save Order" to apply.', true);
        });

        // 2. First Come Last Serve (Newest 1, 2, 3...)
        $('#cmg-btn-fcls').on('click', function(e) {
            e.preventDefault();
            var $tbody = $('#cmg-articles-tbody');
            var rows = $tbody.find('tr').get();
            rows.sort(function(a, b) {
                var dateA = parseInt($(a).data('post-date'), 10) || 0;
                var dateB = parseInt($(b).data('post-date'), 10) || 0;
                return dateB - dateA;
            });
            $.each(rows, function(idx, row) {
                $tbody.append(row);
            });
            renumberRows();
            showNotification('Sorted by First Come Last Serve (Newest First: 1, 2, 3...). Click "Save Order" to apply.', true);
        });

        // 3. Auto-assign numbers (1, 2, 3, 4...)
        $('#cmg-auto-number-btn, .cmg-quick-renumber-btn').on('click', function(e) {
            e.preventDefault();
            renumberRows();
            showNotification('Renumbered sequence to 1, 2, 3, 4... Click "Save Order" to apply.', true);
        });

        // 4. Native HTML5 Drag and Drop Reordering
        var draggedRow = null;
        var tbody = document.getElementById('cmg-articles-tbody');
        if (tbody) {
            var rows = tbody.querySelectorAll('.cmg-draggable-row');
            rows.forEach(function(row) {
                row.addEventListener('dragstart', function(e) {
                    draggedRow = this;
                    this.classList.add('dragging');
                    e.dataTransfer.effectAllowed = 'move';
                });

                row.addEventListener('dragend', function() {
                    this.classList.remove('dragging');
                    rows.forEach(function(r) { r.classList.remove('drag-over-row'); });
                    renumberRows();
                });

                row.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    e.dataTransfer.dropEffect = 'move';
                    if (draggedRow !== this) {
                        this.classList.add('drag-over-row');
                    }
                });

                row.addEventListener('dragleave', function() {
                    this.classList.remove('drag-over-row');
                });

                row.addEventListener('drop', function(e) {
                    e.preventDefault();
                    this.classList.remove('drag-over-row');
                    if (draggedRow && draggedRow !== this) {
                        var allRows = Array.from(tbody.querySelectorAll('.cmg-draggable-row'));
                        var draggedIdx = allRows.indexOf(draggedRow);
                        var targetIdx = allRows.indexOf(this);

                        if (draggedIdx < targetIdx) {
                            tbody.insertBefore(draggedRow, this.nextSibling);
                        } else {
                            tbody.insertBefore(draggedRow, this);
                        }
                        renumberRows();
                    }
                });
            });
        }

        // 5. 1-Click Sync with Live Docy
        $('#cmg-sync-live-docy-btn').on('click', function(e) {
            e.preventDefault();
            var $btn = $(this);
            $btn.prop('disabled', true).text('Syncing...');

            $.post(ajaxurl, {
                action: 'cmg_sync_live_docy_order',
                _ajax_nonce: '<?php echo wp_create_nonce("cmg_sync_docy_nonce"); ?>'
            }, function(response) {
                $btn.prop('disabled', false).text('⚡ 1-Click Sync Order');
                if (response.success) {
                    showNotification(response.data.message, true);
                    setTimeout(function() { location.reload(); }, 1200);
                } else {
                    showNotification(response.data.message || 'Error syncing', false);
                }
            }).fail(function() {
                $btn.prop('disabled', false).text('⚡ 1-Click Sync Order');
                alert('Connection error occurred.');
            });
        });

        // 6. Save Orders and Gated Status (1, 2, 3...)
        $('#cmg-save-orders-btn, #cmg-save-orders-top-btn').on('click', function(e) {
            e.preventDefault();
            var $btn = $(this);
            $btn.prop('disabled', true).text('Saving...');

            var orders = {};
            var gated = {};
            $('#cmg-articles-tbody tr').each(function() {
                var pid = $(this).data('post-id');
                var ord = $(this).find('.cmg-order-input').val();
                var isGated = $(this).find('.cmg-gated-checkbox').is(':checked') ? 1 : 0;
                orders[pid] = ord;
                gated[pid] = isGated;
            });

            $.post(ajaxurl, {
                action: 'cmg_save_bulk_docs_order',
                orders: orders,
                gated: gated,
                _ajax_nonce: '<?php echo wp_create_nonce("cmg_save_order_nonce"); ?>'
            }, function(response) {
                $btn.prop('disabled', false).text('💾 Save Order (1, 2, 3...)');
                if (response.success) {
                    showNotification('Order saved successfully as 1, 2, 3... and reflected everywhere!', true);
                    setTimeout(function() { location.reload(); }, 1200);
                } else {
                    showNotification(response.data.message || 'Error saving', false);
                }
            }).fail(function() {
                $btn.prop('disabled', false).text('💾 Save Order (1, 2, 3...)');
                alert('Connection error occurred.');
            });
        });

    });
    </script>
    <?php
}

// AJAX: Save bulk orders & gated status
add_action('wp_ajax_cmg_save_bulk_docs_order', function() {
    check_ajax_referer('cmg_save_order_nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Permission denied.'));
    }

    $orders = isset($_POST['orders']) && is_array($_POST['orders']) ? $_POST['orders'] : array();
    $gated  = isset($_POST['gated']) && is_array($_POST['gated']) ? $_POST['gated'] : array();

    global $wpdb;
    foreach ($orders as $post_id => $order_val) {
        $pid = intval($post_id);
        $ord = intval($order_val);
        $wpdb->update($wpdb->posts, array('menu_order' => $ord), array('ID' => $pid));

        $is_gated = isset($gated[$post_id]) && $gated[$post_id] == 1 ? '1' : '0';
        update_post_meta($pid, '_cmg_is_gated', $is_gated);
    }

    wp_send_json_success(array('message' => 'Successfully updated article order and gated paywall access!'));
});

/**
 * 13. Auto-seed & Sync with Live Docy Sequence
 */
function cmg_seed_live_article_order_and_gating() {
    // Exact sequential order of Onboarding articles matching live Docy:
    $onboarding_order = array(
        'how-to-sign-up-for-cmgalaxy-step-by-step-account-registration' => array('order' => 10, 'gated' => 1),
        'how-to-sign-up-for-cmgalaxy-step-by-step-account-registratio'  => array('order' => 10, 'gated' => 1),
        'how-to-verify-your-email-in-cmgalaxy-after-sign-up'            => array('order' => 20, 'gated' => 0),
        'how-to-log-in-to-your-cmgalaxy-account'                       => array('order' => 30, 'gated' => 0),
        'how-to-complete-brand-setup-in-cmgalaxy-initial-account-configuration' => array('order' => 40, 'gated' => 0),
        'how-to-complete-brand-setup-in-cmgalaxy-initial-account-conf' => array('order' => 40, 'gated' => 0),
        'how-to-choose-the-correct-currency-and-timezone-in-cmgalaxy'  => array('order' => 50, 'gated' => 0),
        'how-to-set-up-an-e-commerce-account-type-in-cmgalaxy'         => array('order' => 60, 'gated' => 0),
        'how-to-set-up-a-lead-generation-account-type-in-cmgalaxy'     => array('order' => 70, 'gated' => 0),
        'how-to-connect-google-ads-to-cmgalaxy'                        => array('order' => 80, 'gated' => 0),
        'how-to-fix-google-ads-integration-errors-in-cmgalaxy'         => array('order' => 90, 'gated' => 0),
        'how-to-connect-meta-facebook-ads-to-cmgalaxy'                 => array('order' => 100, 'gated' => 0),
        'how-to-fix-meta-facebook-integration-errors-in-cmgalaxy'      => array('order' => 110, 'gated' => 0),
        'how-to-select-the-correct-ad-account-during-cmgalaxy-setup'   => array('order' => 120, 'gated' => 0),
        'what-happens-after-data-integration-in-cmgalaxy'              => array('order' => 130, 'gated' => 0),
        'understanding-your-first-cmgalaxy-dashboard-after-onboarding' => array('order' => 140, 'gated' => 0),
        'how-to-validate-and-filter-imported-data-in-cmgalaxy'         => array('order' => 150, 'gated' => 0),
        'how-to-reset-your-cmgalaxy-password-forgot-password'          => array('order' => 160, 'gated' => 0),
        'common-cmgalaxy-onboarding-issues-and-how-to-fix-them'        => array('order' => 170, 'gated' => 0),
        'how-to-contact-cmgalaxy-support-during-onboarding'             => array('order' => 180, 'gated' => 0),
    );

    // UTM Parameters Guidelines in exact Docy sequence:
    $utm_order = array(
        'google-ads-utm-parameters'   => array('order' => 10, 'gated' => 0),
        'linkedin-ads-utm-parameters' => array('order' => 20, 'gated' => 0),
        'meta-ads-utm-parameters'     => array('order' => 30, 'gated' => 0),
    );

    $all_known = array_merge($onboarding_order, $utm_order);
    global $wpdb;
    $updated_count = 0;

    foreach ($all_known as $slug => $meta) {
        $posts = $wpdb->get_results($wpdb->prepare("
            SELECT ID, post_name 
            FROM {$wpdb->posts} 
            WHERE (post_name = %s OR post_name LIKE %s)
            AND post_type IN ('docs', 'post')
        ", $slug, '%' . $slug . '%'));

        if (!empty($posts)) {
            foreach ($posts as $p) {
                $wpdb->update($wpdb->posts, array('menu_order' => $meta['order']), array('ID' => $p->ID));
                if ($meta['gated'] == 1) {
                    update_post_meta($p->ID, '_cmg_is_gated', '1');
                }
                $updated_count++;
            }
        }
    }

    return $updated_count;
}

// Auto-seed once on admin load
add_action('admin_init', function() {
    if (!get_option('_cmg_docs_live_order_seeded_v3')) {
        cmg_seed_live_article_order_and_gating();
        update_option('_cmg_docs_live_order_seeded_v3', 1);
    }
});

// AJAX Handler for 1-Click Sync with Live Docy
add_action('wp_ajax_cmg_sync_live_docy_order', function() {
    check_ajax_referer('cmg_sync_docy_nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Permission denied.'));
    }

    $count = cmg_seed_live_article_order_and_gating();
    wp_send_json_success(array('message' => 'Successfully aligned ' . $count . ' articles to live Docy order and set paywall gating!'));
});

/**
 * Handle live search suggestions from the header (Matching Docy layout)
 */
function lex_live_search_handler() {
    $q = isset($_POST['query']) ? sanitize_text_field($_POST['query']) : '';

    if (strlen($q) < 2) {
        wp_send_json_success(array('results' => array()));
    }

    $args = array(
        's'                      => $q,
        'post_type'              => array('docs', 'post'),
        'posts_per_page'         => 6,
        'orderby'                => 'relevance',
        'no_found_rows'          => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => true,
    );

    $query = new WP_Query($args);
    $results = array();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            $category_name = '';
            $terms = get_the_terms(get_the_ID(), 'doc_category');
            if (!empty($terms) && !is_wp_error($terms)) {
                $category_name = $terms[0]->name;
            } else {
                $cats = get_the_category();
                if (!empty($cats) && !is_wp_error($cats)) {
                    $category_name = $cats[0]->name;
                }
            }

            $results[] = array(
                'title'    => get_the_title(),
                'url'      => get_permalink(),
                'type'     => get_post_type(),
                'category' => $category_name ? $category_name : __('Documentation', 'hello-elementor')
            );
        }
        wp_reset_postdata();
    }

    wp_send_json_success(array('results' => $results));
}
add_action('wp_ajax_lex_live_search', 'lex_live_search_handler');
add_action('wp_ajax_nopriv_lex_live_search', 'lex_live_search_handler');

/**
 * Call AI service for Lex Assistant query processing and direct answering
 */
function lex_call_openai($query, $force_direct = false) {
    $api_url = 'https://text.pollinations.ai/openai';

    if ($force_direct) {
        $prompt = "You are Lex, a technical expert for CMGalaxy. ANSWER THE USER'S QUESTION DIRECTLY AND EXPERTLY: \"" . addslashes($query) . "\".\n\n" .
                  "1. Provide a clear technical guide or explanation.\n" .
                  "2. Use bullet points.\n" .
                  "3. NO greeting filler.\n\n" .
                  "Always respond in this exact JSON format only:\n" .
                  "{\"answer\":\"your substantive technical answer here\",\"keywords\":\"\"}";
    } else {
        $prompt = "You are Lex, the AI assistant for 'CMGalaxy'.\n\n" .
                  "GOAL: Extract the core search keywords from the user's question so we can search our knowledge base.\n\n" .
                  "STRICT RULES:\n" .
                  "1. 'answer': Leave this EMPTY string. Do NOT generate any answer, explanation, or article content. Do NOT make up articles or CMGalaxy features. Only the knowledge base search results will be shown.\n" .
                  "2. 'keywords': Extract ONLY the 1-2 most important technical words from the query (e.g. 'whatsapp', 'dv360', 'report'). Ignore filler words like 'is there', 'any', 'related to', 'article about'.\n\n" .
                  "IMPORTANT: Always respond in this exact JSON format only:\n" .
                  "{\n" .
                  "  \"answer\": \"\",\n" .
                  "  \"keywords\": \"technical word(s) only\"\n" .
                  "}";
    }

    $body = json_encode([
        'model' => 'openai',
        'messages' => [
            ['role' => 'system', 'content' => 'You always reply in valid JSON using the requested schema.'],
            ['role' => 'user', 'content' => $prompt]
        ],
        'temperature' => 0.1,
        'jsonMode' => true
    ]);

    $response = wp_remote_post($api_url, [
        'headers'   => [
            'Content-Type'  => 'application/json'
        ],
        'body'      => $body,
        'timeout'   => 15,
        'sslverify' => false,
    ]);

    if (is_wp_error($response)) {
        error_log('Lex OpenAI Error: ' . $response->get_error_message());
        return null;
    }

    $body_response = trim(wp_remote_retrieve_body($response));
    if (empty($body_response)) return null;

    if (strpos($body_response, '{') === 0) {
        $json_body = json_decode($body_response, true);
        if (isset($json_body['choices'][0]['message']['content'])) {
            $text = $json_body['choices'][0]['message']['content'];
        } else {
            $text = $body_response; 
        }
    } else {
        $text = $body_response;
    }

    if (!$text) return null;

    $clean = trim($text);
    $clean = preg_replace('/^```(?:json)?\s*/i', '', $clean);
    $clean = preg_replace('/\s*```$/i', '', $clean);
    
    if (preg_match('/\{.*\}/s', $clean, $matches)) {
        $clean = $matches[0];
    }
    
    $result = json_decode($clean, true);

    if (!$result && !empty($clean)) {
        return [
            'answer' => $clean,
            'keywords' => ''
        ];
    }
    
    return $result;
}

/**
 * Handle Lex Assistant chat and documentation queries
 */
function lex_chat_query_handler() {
    $original_query = isset($_POST['query']) ? sanitize_text_field($_POST['query']) : '';
    if (empty($original_query)) {
        wp_send_json_error(['message' => 'Please enter a search term or question.']);
        wp_die();
    }

    // Ask OpenAI to classify and respond
    $ai = lex_call_openai($original_query);
    
    $answer = isset($ai['answer']) ? $ai['answer'] : '';
    $search_term = isset($ai['keywords']) ? $ai['keywords'] : '';

    if (isset($ai['type']) && $ai['type'] === 'error') {
        wp_send_json_error(['message' => $ai['text']]);
        wp_die();
    }

    // Determine the search term: use AI keywords or fall back to manual cleaning
    if (!$search_term && empty($answer)) {
        $stop_phrases = [
            'open article which tell', 'open article which tells', 'open article about',
            'open article in', 'open article', 'find article about', 'find article in',
            'find article', 'tell me about', 'search for', 'look for', 'how to',
            'where is', 'what is', 'show me', 'which tell', 'which tells', 'i want to',
            'can you', 'please', 'help me with', 'about', 'total number of', 'total number',
            'count of', 'how many', 'number of', 'articles in', 'article in', 'articles', 'article'
        ];
        $q = str_ireplace($stop_phrases, '', $original_query);
        $short_stops = ['the', 'a', 'an', 'in', 'on', 'to', 'for', 'of', 'with', 'at', 'by', 'is'];
        foreach ($short_stops as $word) {
            $q = preg_replace('/\b' . $word . '\b/i', '', $q);
        }
        $mappings = [
            'on board' => 'onboarding', 'platfrom' => 'platform',
            'getting stated' => 'getting started',
        ];
        foreach ($mappings as $wrong => $right) {
            $q = str_ireplace($wrong, $right, $q);
        }
        $search_term = trim(preg_replace('/\s+/', ' ', $q)) ?: $original_query;
    }

    $post_types = ['docs', 'post'];

    $args = [
        's'              => $search_term,
        'post_type'      => $post_types,
        'posts_per_page' => 10,
        'post_status'    => 'publish',
        'orderby'        => 'relevance'
    ];

    $query = new WP_Query($args);
    $candidates = [];

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $candidates[get_the_ID()] = [
                'id' => get_the_ID(),
                'title' => get_the_title(),
                'excerpt' => get_the_excerpt(),
                'content' => get_the_content(),
                'url' => get_permalink(),
                'type' => get_post_type()
            ];
        }
    }

    // Attempt fuzzy taxonomy/original match
    $cleaned_q = str_ireplace(['how to', 'share', 'report'], '', strtolower($original_query));
    if (strlen(trim($cleaned_q)) > 2) {
        $args['s'] = $original_query;
        $query2 = new WP_Query($args);
        while ($query2->have_posts()) {
            $query2->the_post();
            $candidates[get_the_ID()] = [
                'id' => get_the_ID(),
                'title' => get_the_title(),
                'excerpt' => get_the_excerpt(),
                'content' => get_the_content(),
                'url' => get_permalink(),
                'type' => get_post_type()
            ];
        }
    }

    $scored_results = [];
    $search_words = explode(' ', strtolower($search_term . ' ' . $original_query));
    $search_words = array_unique(array_filter($search_words, function($w) {
        return strlen($w) > 2 && !in_array($w, ['how', 'the', 'for', 'and', 'with', 'setup']);
    }));

    foreach ($candidates as $post_id => $data) {
        $score = 0;
        $title = strtolower($data['title']);
        $excerpt = strtolower($data['excerpt']);
        $content = strtolower($data['content'] ?? '');

        if (!empty($search_term) && stripos($title, strtolower($search_term)) !== false) {
            $score += 80;
            if (preg_match('/\b' . preg_quote(strtolower($search_term), '/') . '\b/i', $title)) {
                $score += 40;
            }
        }
        
        foreach ($search_words as $w) {
            if (stripos($title, $w) !== false) $score += 30;
            if (stripos($excerpt, $w) !== false) $score += 15;
            if (!empty($content) && stripos($content, $w) !== false) $score += 10;
        }

        if (!empty($search_term) && strlen($search_term) > 3) {
            $in_title = (stripos($title, $search_term) !== false);
            $in_excerpt = (stripos($excerpt, $search_term) !== false);
            $in_content = (stripos($content, $search_term) !== false);

            if (!$in_title && !$in_excerpt && !$in_content) {
                $score -= 150; 
            } elseif ($in_content && !$in_title) {
                $score += 60;
            }
        }

        if (!empty($search_term) && stripos(trim($title), strtolower($search_term)) === 0) $score += 50;

        $exact_generic_pages = ['Meta Ads', 'Reporting Hub', 'Documentation', 'CMGalaxy Knowledge Base'];
        foreach ($exact_generic_pages as $gp) {
            if (trim($data['title']) === $gp || stripos($title, $gp) !== false) {
                if (stripos($original_query, $gp) === false && count($search_words) > 1) {
                    $score -= 60;
                }
            }
        }

        if ($score > 30) {
            $scored_results[] = [
                'score' => $score,
                'title' => $data['title'],
                'url' => $data['url'],
                'excerpt' => wp_trim_words($data['excerpt'] ?: wp_strip_all_tags($data['content']), 20),
                'type' => $data['type'] === 'docs' ? 'Docs' : ucfirst($data['type'])
            ];
        }
    }

    usort($scored_results, function ($a, $b) {
        return $b['score'] - $a['score'];
    });

    $results = $scored_results;
    wp_reset_postdata();

    if (!empty($results)) {
        $actual_total = count($results);
        $display_results = array_slice($results, 0, 5);
        $article_text = $actual_total == 1 ? 'article' : 'articles';

        if (empty($answer)) {
            $ai_fb = lex_call_openai($original_query, true);
            $answer = $ai_fb['answer'] ?? '';
        }

        $suggest_msg = " I also found {$actual_total} {$article_text} that might help you:";
        if (preg_match('/(how many|total|number|count)/i', trim($original_query))) {
            $suggest_msg = " There are {$actual_total} {$article_text} related to \"{$search_term}\". Here's the list:";
        }

        wp_send_json_success([
            'message' => (!empty($answer) ? $answer . "\n\n" : "") . $suggest_msg,
            'results' => $display_results
        ]);
    } else {
        // Fallback: If no strict scored results, attempt a broad query before giving up
        $fallback_args = [
            's'              => $search_term ?: $original_query,
            'post_type'      => $post_types,
            'posts_per_page' => 4,
            'post_status'    => 'publish',
            'orderby'        => 'relevance'
        ];
        $fb_q = new WP_Query($fallback_args);
        $fb_results = [];
        if ($fb_q->have_posts()) {
            while ($fb_q->have_posts()) {
                $fb_q->the_post();
                $fb_results[] = [
                    'title' => get_the_title(),
                    'url' => get_permalink(),
                    'excerpt' => wp_trim_words(get_the_excerpt() ?: wp_strip_all_tags(get_the_content()), 20),
                    'type' => get_post_type() === 'docs' ? 'Docs' : ucfirst(get_post_type())
                ];
            }
            wp_reset_postdata();
        }

        if (!empty($fb_results)) {
            wp_send_json_success([
                'message' => "Here are some relevant articles from our knowledge base:",
                'results' => $fb_results
            ]);
        } else {
            if (empty($answer)) {
                $ai_direct = lex_call_openai($original_query, true);
                $answer = $ai_direct['answer'] ?? '';
            }

            if (!empty($answer)) {
                wp_send_json_success([
                    'message' => $answer,
                    'results' => []
                ]);
            } else {
                wp_send_json_success([
                    'message' => "I couldn't find any articles related to \"" . esc_html($original_query) . "\" in the knowledge base. Try different keywords or browse the categories.",
                    'results' => []
                ]);
            }
        }
    }
    wp_die();
}
add_action('wp_ajax_lex_chat_query', 'lex_chat_query_handler');
add_action('wp_ajax_nopriv_lex_chat_query', 'lex_chat_query_handler');

/**
 * 14. Advanced Order Settings Modal Hooks & AJAX Handlers
 */

// Footer modal loader for admin only (not front-end)
add_action('admin_footer', 'cmg_render_advanced_order_modal_footer');
function cmg_render_advanced_order_modal_footer() {
    global $pagenow;
    if (current_user_can('edit_posts') && isset($_GET['page']) && $_GET['page'] === 'cmgalaxy-docs-order-manager') {
        get_template_part('template-parts/docs/modal-advanced-order');
    }
}

// 1. AJAX: Get Categories Hierarchy
function cmg_ajax_get_advanced_order_categories() {
    check_ajax_referer('cmg_adv_order_nonce');
    if (!current_user_can('edit_posts')) {
        wp_send_json_error(array('message' => 'Permission denied.'));
    }

    $selected_id = isset($_POST['selected_cat_id']) ? intval($_POST['selected_cat_id']) : 0;

    $top_terms = get_terms(array(
        'taxonomy'   => array('doc_category', 'category'),
        'parent'     => 0,
        'hide_empty' => false,
        'orderby'    => 'name',
        'order'      => 'ASC'
    ));

    $categories_list = array();
    global $wpdb;

    if (!empty($top_terms) && !is_wp_error($top_terms)) {
        foreach ($top_terms as $top) {
            if (strtolower($top->slug) === 'uncategorized') continue;

            $count = (int) $wpdb->get_var($wpdb->prepare("
                SELECT COUNT(p.ID) FROM {$wpdb->posts} p
                INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
                INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                WHERE tt.term_id = %d AND p.post_type IN ('docs', 'post') AND p.post_status = 'publish'
            ", $top->term_id));

            $categories_list[] = array(
                'id'            => $top->term_id,
                'name'          => html_entity_decode($top->name),
                'slug'          => $top->slug,
                'depth'         => 0,
                'article_count' => $count
            );

            // Fetch child subcategories
            $children = get_terms(array(
                'taxonomy'   => $top->taxonomy,
                'parent'     => $top->term_id,
                'hide_empty' => false,
                'orderby'    => 'name',
                'order'      => 'ASC'
            ));

            if (!empty($children) && !is_wp_error($children)) {
                foreach ($children as $child) {
                    $child_count = (int) $wpdb->get_var($wpdb->prepare("
                        SELECT COUNT(p.ID) FROM {$wpdb->posts} p
                        INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
                        INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                        WHERE tt.term_id = %d AND p.post_type IN ('docs', 'post') AND p.post_status = 'publish'
                    ", $child->term_id));

                    $categories_list[] = array(
                        'id'            => $child->term_id,
                        'name'          => html_entity_decode($child->name),
                        'slug'          => $child->slug,
                        'depth'         => 1,
                        'article_count' => $child_count
                    );

                    // Level 3 sub-subcategories if any
                    $sub_children = get_terms(array(
                        'taxonomy'   => $child->taxonomy,
                        'parent'     => $child->term_id,
                        'hide_empty' => false
                    ));
                    if (!empty($sub_children) && !is_wp_error($sub_children)) {
                        foreach ($sub_children as $sc) {
                            $sc_count = (int) $wpdb->get_var($wpdb->prepare("
                                SELECT COUNT(p.ID) FROM {$wpdb->posts} p
                                INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
                                INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                                WHERE tt.term_id = %d AND p.post_type IN ('docs', 'post') AND p.post_status = 'publish'
                            ", $sc->term_id));

                            $categories_list[] = array(
                                'id'            => $sc->term_id,
                                'name'          => html_entity_decode($sc->name),
                                'slug'          => $sc->slug,
                                'depth'         => 2,
                                'article_count' => $sc_count
                            );
                        }
                    }
                }
            }
        }
    }

    if (!$selected_id && !empty($categories_list)) {
        $selected_id = $categories_list[0]['id'];
    }

    wp_send_json_success(array(
        'categories' => $categories_list,
        'active_id'  => $selected_id
    ));
}
add_action('wp_ajax_cmg_get_advanced_order_categories', 'cmg_ajax_get_advanced_order_categories');

// 2. AJAX: Get Articles and Subcategories for Selected Category
function cmg_ajax_get_advanced_order_data() {
    check_ajax_referer('cmg_adv_order_nonce');
    if (!current_user_can('edit_posts')) {
        wp_send_json_error(array('message' => 'Permission denied.'));
    }

    $cat_id = isset($_POST['cat_id']) ? intval($_POST['cat_id']) : 0;
    if (!$cat_id) {
        wp_send_json_error(array('message' => 'Invalid category ID.'));
    }

    $term = get_term($cat_id);
    if (!$term || is_wp_error($term)) {
        wp_send_json_error(array('message' => 'Category not found.'));
    }

    global $wpdb;

    // Articles in this category/subcategory
    $articles_raw = $wpdb->get_results($wpdb->prepare("
        SELECT p.ID, p.post_title, p.post_date, p.menu_order
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
        INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        WHERE tt.term_id = %d
        AND p.post_type IN ('docs', 'post')
        AND p.post_status = 'publish'
        ORDER BY p.menu_order ASC, p.post_date ASC, p.ID ASC
    ", $cat_id));

    $articles = array();
    if (!empty($articles_raw)) {
        foreach ($articles_raw as $art) {
            $articles[] = array(
                'id'             => $art->ID,
                'title'          => html_entity_decode($art->post_title),
                'date'           => $art->post_date,
                'date_timestamp' => strtotime($art->post_date),
                'date_formatted' => date_i18n('M j, Y', strtotime($art->post_date)),
                'order'          => intval($art->menu_order)
            );
        }
    }

    // Subcategories under this term (if any)
    $subcats_raw = get_terms(array(
        'taxonomy'   => $term->taxonomy,
        'parent'     => $cat_id,
        'hide_empty' => false,
        'orderby'    => 'term_id',
        'order'      => 'ASC'
    ));

    $subcategories = array();
    if (!empty($subcats_raw) && !is_wp_error($subcats_raw)) {
        foreach ($subcats_raw as $sc) {
            $order_val = get_term_meta($sc->term_id, '_cmgalaxy_sidebar_order', true);
            $subcategories[] = array(
                'id'    => $sc->term_id,
                'title' => html_entity_decode($sc->name),
                'slug'  => $sc->slug,
                'order' => ($order_val !== '' && $order_val !== false) ? intval($order_val) : 999
            );
        }
    }

    $saved_method = get_term_meta($cat_id, '_cmg_article_order_method', true);
    if (empty($saved_method)) {
        $saved_method = 'fcfs';
    }

    wp_send_json_success(array(
        'cat_id'        => $cat_id,
        'cat_name'      => html_entity_decode($term->name),
        'order_method'  => $saved_method,
        'articles'      => $articles,
        'subcategories' => $subcategories
    ));
}
add_action('wp_ajax_cmg_get_advanced_order_data', 'cmg_ajax_get_advanced_order_data');

// 3. AJAX: Save Advanced Order
function cmg_ajax_save_advanced_order() {
    check_ajax_referer('cmg_adv_order_nonce');
    if (!current_user_can('edit_posts')) {
        wp_send_json_error(array('message' => 'Permission denied.'));
    }

    $cat_id       = isset($_POST['cat_id']) ? intval($_POST['cat_id']) : 0;
    $scope        = isset($_POST['scope']) ? sanitize_text_field($_POST['scope']) : 'articles';
    $order_method = isset($_POST['order_method']) ? sanitize_text_field($_POST['order_method']) : 'custom';
    $ordered_ids  = isset($_POST['ordered_ids']) && is_array($_POST['ordered_ids']) ? array_map('intval', $_POST['ordered_ids']) : array();

    if (!$cat_id || empty($ordered_ids)) {
        wp_send_json_error(array('message' => 'Invalid parameters provided.'));
    }

    global $wpdb;

    if ($scope === 'articles') {
        // Save preferred order method on the term
        update_term_meta($cat_id, '_cmg_article_order_method', $order_method);

        // Update menu_order sequentially (1, 2, 3, 4...)
        $order_num = 1;
        foreach ($ordered_ids as $post_id) {
            $wpdb->update(
                $wpdb->posts,
                array('menu_order' => $order_num),
                array('ID' => $post_id)
            );
            clean_post_cache($post_id);
            $order_num += 1;
        }

        clean_term_cache($cat_id);
        wp_cache_flush();

        wp_send_json_success(array(
            'message' => 'Article order updated successfully as 1, 2, 3...! Applying everywhere...'
        ));
    } elseif ($scope === 'subcategories') {
        update_term_meta($cat_id, '_cmg_subcat_order_method', $order_method);

        $order_num = 1;
        foreach ($ordered_ids as $term_id) {
            update_term_meta($term_id, '_cmgalaxy_sidebar_order', $order_num);
            clean_term_cache($term_id);
            $order_num += 1;
        }

        clean_term_cache($cat_id);
        wp_cache_flush();

        wp_send_json_success(array(
            'message' => 'Subcategories order updated successfully! Applying everywhere...'
        ));
    }

    wp_send_json_error(array('message' => 'Unknown scope.'));
}
add_action('wp_ajax_cmg_save_advanced_order', 'cmg_ajax_save_advanced_order');

