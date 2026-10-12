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
    if (is_admin() || !is_singular() || is_user_logged_in()) {
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
 * 7. Load Docs Importer & Sync Tool
 */
require_once get_template_directory() . '/includes/docs-importer.php';
