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

    $order_a = get_term_meta( $a->term_id, '_cmgalaxy_sidebar_order', true );
    $order_b = get_term_meta( $b->term_id, '_cmgalaxy_sidebar_order', true );
    
    $val_a = ($order_a !== '') ? intval($order_a) : 9999;
    $val_b = ($order_b !== '') ? intval($order_b) : 9999;
    
    if ( $val_a == $val_b ) {
        return $a->term_id - $b->term_id;
    }
    return ( $val_a < $val_b ) ? -1 : 1;
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

    if ( $is_docs ) {
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
    
    // Get all doc categories
    $categories = get_terms(array(
        'taxonomy'   => array('doc_category', 'category'),
        'hide_empty' => false,
        'orderby'    => 'name',
        'order'      => 'ASC'
    ));

    $selected_cat_id = isset($_GET['cat_id']) ? intval($_GET['cat_id']) : 0;
    if (!$selected_cat_id && !empty($categories) && !is_wp_error($categories)) {
        // Default to Getting started or Onboarding if found
        foreach ($categories as $cat) {
            if ($cat->slug === 'getting-started' || $cat->slug === 'onboarding') {
                $selected_cat_id = $cat->term_id;
                break;
            }
        }
        if (!$selected_cat_id) {
            $selected_cat_id = $categories[0]->term_id;
        }
    }

    // Fetch posts in this category ordered by menu_order ASC, ID ASC (Docy exact order)
    $articles = array();
    if ($selected_cat_id) {
        $articles = $wpdb->get_results($wpdb->prepare("
            SELECT p.ID, p.post_title, p.post_name, p.menu_order, p.post_status
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
            INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            WHERE tt.term_id = %d
            AND p.post_type IN ('docs', 'post')
            AND p.post_status = 'publish'
            ORDER BY p.menu_order ASC, p.ID ASC
        ", $selected_cat_id));
    }
    ?>
    <div class="wrap" style="max-width: 1100px; margin-top: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
        <div style="background: #ffffff; padding: 28px 32px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
            
            <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 20px; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
                <div>
                    <h1 style="font-size: 24px; font-weight: 700; color: #0f172a; margin: 0 0 6px 0;">Article Order & Gated Access Manager</h1>
                    <p style="color: #64748b; font-size: 14.5px; margin: 0;">
                        Manage exact sidebar article ordering and customer paywall gating matching live Docy.
                    </p>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="button" id="cmg-sync-live-docy-btn" class="button button-primary" style="background: #2563eb; border-color: #2563eb; padding: 6px 18px; font-size: 13.5px; font-weight: 600; border-radius: 6px; cursor: pointer;">
                        ⚡ 1-Click Sync Order with Live Docy
                    </button>
                </div>
            </div>

            <!-- Category Selector Filter -->
            <form method="get" action="" style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px; background: #f8fafc; padding: 14px 18px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <input type="hidden" name="post_type" value="docs">
                <input type="hidden" name="page" value="cmgalaxy-docs-order-manager">
                
                <label for="cat_id" style="font-weight: 600; font-size: 14px; color: #334155;">Select Category:</label>
                <select name="cat_id" id="cat_id" onchange="this.form.submit()" style="padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 14px; min-width: 260px;">
                    <?php if (!empty($categories) && !is_wp_error($categories)) : ?>
                        <?php foreach ($categories as $cat) : ?>
                            <option value="<?php echo esc_attr($cat->term_id); ?>" <?php selected($selected_cat_id, $cat->term_id); ?>>
                                <?php echo esc_html($cat->name); ?> (<?php echo intval($cat->count); ?> articles)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </form>

            <div id="cmg-manager-notification" style="display: none; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; font-weight: 500;"></div>

            <!-- Articles Ordering Table -->
            <form id="cmg-order-form">
                <?php wp_nonce_field('cmg_bulk_order_nonce', 'order_nonce'); ?>
                <table class="wp-list-table widefat fixed striped posts" style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; box-shadow: none;">
                    <thead>
                        <tr>
                            <th style="width: 70px; text-align: center; font-weight: 600; color: #475569;">#</th>
                            <th style="width: 90px; font-weight: 600; color: #475569;">Order</th>
                            <th style="font-weight: 600; color: #475569;">Article Title</th>
                            <th style="width: 170px; text-align: center; font-weight: 600; color: #475569;">Paywall Gate</th>
                            <th style="width: 140px; text-align: center; font-weight: 600; color: #475569;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="cmg-articles-tbody">
                        <?php if (!empty($articles)) : ?>
                            <?php foreach ($articles as $index => $art) : 
                                $is_gated = cmg_is_post_restricted_to_logged_in($art->ID);
                                $preview_url = add_query_arg('preview_gate', '1', get_permalink($art->ID));
                                $edit_url = get_edit_post_link($art->ID);
                            ?>
                                <tr data-post-id="<?php echo esc_attr($art->ID); ?>">
                                    <td style="text-align: center; color: #94a3b8; font-weight: 600;">
                                        <?php echo intval($index + 1); ?>
                                    </td>
                                    <td>
                                        <input type="number" class="cmg-order-input" name="order[<?php echo esc_attr($art->ID); ?>]" value="<?php echo esc_attr($art->menu_order); ?>" style="width: 70px; padding: 4px 8px; border-radius: 4px; border: 1px solid #cbd5e1; text-align: center; font-weight: 600;">
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: #0f172a; font-size: 14px;">
                                            <a href="<?php echo esc_url($edit_url); ?>" style="color: #0f172a; text-decoration: none;">
                                                <?php echo esc_html($art->post_title); ?>
                                            </a>
                                        </div>
                                        <div style="font-size: 12px; color: #94a3b8; margin-top: 2px;">
                                            slug: <?php echo esc_html($art->post_name); ?>
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
                                <td colspan="5" style="text-align: center; padding: 32px; color: #64748b;">
                                    No articles found in this category.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <?php if (!empty($articles)) : ?>
                    <div style="margin-top: 24px; display: flex; justify-content: space-between; align-items: center;">
                        <button type="button" id="cmg-auto-number-btn" class="button" style="font-size: 13px;">
                            Auto-Assign (10, 20, 30...)
                        </button>
                        <button type="button" id="cmg-save-orders-btn" class="button button-primary" style="background: #10b981; border-color: #10b981; font-weight: 600; padding: 6px 24px; font-size: 14px; border-radius: 6px;">
                            💾 Save Order & Access Settings
                        </button>
                    </div>
                <?php endif; ?>
            </form>

        </div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        // Auto-assign numbers 10, 20, 30...
        $('#cmg-auto-number-btn').on('click', function(e) {
            e.preventDefault();
            var step = 10;
            var num = 10;
            $('#cmg-articles-tbody tr').each(function() {
                $(this).find('.cmg-order-input').val(num);
                num += step;
            });
        });

        // 1-Click Sync with Live Docy
        $('#cmg-sync-live-docy-btn').on('click', function(e) {
            e.preventDefault();
            var $btn = $(this);
            $btn.prop('disabled', true).text('Syncing with Live Docy...');

            $.post(ajaxurl, {
                action: 'cmg_sync_live_docy_order',
                _ajax_nonce: '<?php echo wp_create_nonce("cmg_sync_docy_nonce"); ?>'
            }, function(response) {
                $btn.prop('disabled', false).text('⚡ 1-Click Sync Order with Live Docy');
                var $note = $('#cmg-manager-notification');
                if (response.success) {
                    $note.css({'background': '#ecfdf5', 'color': '#065f46', 'border': '1px solid #a7f3d0'}).text(response.data.message).show();
                    setTimeout(function() { location.reload(); }, 1200);
                } else {
                    $note.css({'background': '#fef2f2', 'color': '#991b1b', 'border': '1px solid #fecaca'}).text(response.data.message || 'Error syncing').show();
                }
            }).fail(function() {
                $btn.prop('disabled', false).text('⚡ 1-Click Sync Order with Live Docy');
                alert('Connection error occurred.');
            });
        });

        // Save Orders and Gated Status
        $('#cmg-save-orders-btn').on('click', function(e) {
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
                $btn.prop('disabled', false).text('💾 Save Order & Access Settings');
                var $note = $('#cmg-manager-notification');
                if (response.success) {
                    $note.css({'background': '#ecfdf5', 'color': '#065f46', 'border': '1px solid #a7f3d0'}).text(response.data.message).show();
                    setTimeout(function() { $note.fadeOut(); }, 3000);
                } else {
                    $note.css({'background': '#fef2f2', 'color': '#991b1b', 'border': '1px solid #fecaca'}).text(response.data.message || 'Error saving').show();
                }
            }).fail(function() {
                $btn.prop('disabled', false).text('💾 Save Order & Access Settings');
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
