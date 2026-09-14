<?php
/**
 * Plugin Name: CMGalaxy Blog Ratings & IP Tracker
 * Plugin URI: https://www.cmgalaxy.com
 * Description: Captures and displays all blog article ratings, IP addresses, cross-device UUIDs, user agents, and syncs with CMGalaxy backend API.
 * Version: 1.0.0
 * Author: CMGalaxy
 * Author URI: https://www.cmgalaxy.com
 * License: GPLv2 or later
 * Text Domain: cmg-ratings
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 1. Create DB Table on activation
register_activation_hook( __FILE__, 'cmg_ratings_plugin_create_table' );
add_action( 'admin_init', 'cmg_ratings_plugin_create_table' );

function cmg_ratings_plugin_create_table() {
    global $wpdb;
    $table = $wpdb->prefix . 'cmg_blog_ratings';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        post_id bigint(20) unsigned NOT NULL DEFAULT 0,
        blog_title varchar(255) NOT NULL DEFAULT '',
        rating tinyint(1) unsigned NOT NULL DEFAULT 5,
        ip_address varchar(100) NOT NULL DEFAULT '',
        cross_device_id varchar(100) NOT NULL DEFAULT '',
        user_agent text,
        page_url text,
        created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY post_id (post_id),
        KEY blog_title (blog_title(191)),
        KEY created_at (created_at)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
}

// 2. IP Detection Helper
if ( ! function_exists( 'cmg_get_client_ip_address' ) ) {
    function cmg_get_client_ip_address() {
        $keys = array(
            'HTTP_CF_CONNECTING_IP',
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        );
        foreach ( $keys as $k ) {
            if ( ! empty( $_SERVER[ $k ] ) ) {
                $ips = explode( ',', $_SERVER[ $k ] );
                $ip = trim( $ips[0] );
                if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
                    return $ip;
                }
            }
        }
        return isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( $_SERVER['REMOTE_ADDR'] ) : '0.0.0.0';
    }
}

// 3. AJAX Endpoint to log ratings
add_action( 'wp_ajax_cmg_log_rating', 'cmg_plugin_handle_log_rating' );
add_action( 'wp_ajax_nopriv_cmg_log_rating', 'cmg_plugin_handle_log_rating' );

function cmg_plugin_handle_log_rating() {
    global $wpdb;
    $table = $wpdb->prefix . 'cmg_blog_ratings';

    $post_id         = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
    $blog_title      = isset( $_POST['blog_title'] ) ? sanitize_text_field( wp_unslash( $_POST['blog_title'] ) ) : '';
    $rating          = isset( $_POST['rating'] ) ? min( 5, max( 1, intval( $_POST['rating'] ) ) ) : 5;
    $cross_device_id = isset( $_POST['cross_device_id'] ) ? sanitize_text_field( wp_unslash( $_POST['cross_device_id'] ) ) : '';
    $page_url        = isset( $_POST['page_url'] ) ? esc_url_raw( wp_unslash( $_POST['page_url'] ) ) : '';
    $ip_address      = cmg_get_client_ip_address();
    $user_agent      = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ) : '';

    if ( ! $blog_title && $post_id ) {
        $blog_title = get_post_field( 'post_name', $post_id );
    }

    $inserted = $wpdb->insert(
        $table,
        array(
            'post_id'         => $post_id,
            'blog_title'      => $blog_title,
            'rating'          => $rating,
            'ip_address'      => $ip_address,
            'cross_device_id' => $cross_device_id,
            'user_agent'      => $user_agent,
            'page_url'        => $page_url,
            'created_at'      => current_time( 'mysql' ),
        ),
        array( '%d', '%s', '%d', '%s', '%s', '%s', '%s', '%s' )
    );

    if ( $inserted ) {
        wp_send_json_success( array( 'message' => 'Logged successfully', 'id' => $wpdb->insert_id ) );
    } else {
        wp_send_json_error( array( 'message' => 'Failed to log rating' ) );
    }
}

// 4. Admin Menu
add_action( 'admin_menu', 'cmg_ratings_plugin_add_admin_menu' );

function cmg_ratings_plugin_add_admin_menu() {
    add_menu_page(
        'Blog Ratings',
        'Blog Ratings',
        'manage_options',
        'cmg-blog-ratings',
        'cmg_ratings_plugin_render_dashboard',
        'dashicons-star-filled',
        27
    );
}

// 5. Handle CSV Export
add_action( 'admin_init', 'cmg_ratings_plugin_handle_csv_export' );

function cmg_ratings_plugin_handle_csv_export() {
    if ( ! isset( $_GET['page'] ) || $_GET['page'] !== 'cmg-blog-ratings' ) return;
    if ( ! isset( $_GET['action'] ) || $_GET['action'] !== 'export_csv' ) return;
    if ( ! current_user_can( 'manage_options' ) ) return;

    check_admin_referer( 'cmg_export_ratings_csv' );

    global $wpdb;
    $table = $wpdb->prefix . 'cmg_blog_ratings';
    $results = $wpdb->get_results( "SELECT * FROM $table ORDER BY id DESC", ARRAY_A );

    header( 'Content-Type: text/csv; charset=utf-8' );
    header( 'Content-Disposition: attachment; filename=cmg-blog-ratings-' . date( 'Y-m-d' ) . '.csv' );

    $output = fopen( 'php://output', 'w' );
    fputcsv( $output, array( 'ID', 'Post ID', 'Blog Title', 'Rating', 'IP Address', 'Cross Device ID', 'User Agent', 'Page URL', 'Date Submitted' ) );

    if ( ! empty( $results ) ) {
        foreach ( $results as $row ) {
            fputcsv( $output, $row );
        }
    }
    fclose( $output );
    exit;
}

// 6. Handle Delete Rating Action
add_action( 'admin_init', 'cmg_ratings_plugin_handle_delete' );

function cmg_ratings_plugin_handle_delete() {
    if ( ! isset( $_GET['page'] ) || $_GET['page'] !== 'cmg-blog-ratings' ) return;
    if ( ! isset( $_GET['action'] ) || $_GET['action'] !== 'delete_rating' ) return;
    if ( ! current_user_can( 'manage_options' ) ) return;

    $id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
    check_admin_referer( 'cmg_delete_rating_' . $id );

    if ( $id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'cmg_blog_ratings';
        $wpdb->delete( $table, array( 'id' => $id ), array( '%d' ) );
        wp_redirect( admin_url( 'admin.php?page=cmg-blog-ratings&deleted=1' ) );
        exit;
    }
}

// 7. Render Admin Dashboard
function cmg_ratings_plugin_render_dashboard() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    global $wpdb;
    $table = $wpdb->prefix . 'cmg_blog_ratings';

    // Filters
    $filter_rating = isset( $_GET['filter_rating'] ) ? intval( $_GET['filter_rating'] ) : 0;
    $search_query  = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';

    $where = array('1=1');
    $params = array();

    if ( $filter_rating > 0 && $filter_rating <= 5 ) {
        $where[] = "rating = %d";
        $params[] = $filter_rating;
    }

    if ( ! empty( $search_query ) ) {
        $where[] = "(blog_title LIKE %s OR ip_address LIKE %s OR cross_device_id LIKE %s)";
        $wildcard = '%' . $wpdb->esc_like( $search_query ) . '%';
        $params[] = $wildcard;
        $params[] = $wildcard;
        $params[] = $wildcard;
    }

    $where_sql = implode( ' AND ', $where );
    if ( ! empty( $params ) ) {
        $where_sql = $wpdb->prepare( $where_sql, $params );
    }

    // Pagination
    $per_page = 20;
    $current_page = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;
    $offset = ( $current_page - 1 ) * $per_page;

    $total_items = $wpdb->get_var( "SELECT COUNT(*) FROM $table WHERE $where_sql" );
    $ratings = $wpdb->get_results( "SELECT * FROM $table WHERE $where_sql ORDER BY id DESC LIMIT $offset, $per_page" );

    // Overall Stats
    $total_count   = $wpdb->get_var( "SELECT COUNT(*) FROM $table" );
    $avg_rating    = $wpdb->get_var( "SELECT AVG(rating) FROM $table" );
    $unique_ips    = $wpdb->get_var( "SELECT COUNT(DISTINCT ip_address) FROM $table" );
    $unique_devs   = $wpdb->get_var( "SELECT COUNT(DISTINCT cross_device_id) FROM $table" );
    $total_posts   = $wpdb->get_var( "SELECT COUNT(DISTINCT blog_title) FROM $table" );

    $export_url = wp_nonce_url( admin_url( 'admin.php?page=cmg-blog-ratings&action=export_csv' ), 'cmg_export_ratings_csv' );
    ?>
    <div class="wrap cmg-ratings-dashboard-wrap">
      <style>
        .cmg-ratings-dashboard-wrap {
          font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
          margin-top: 20px;
        }
        .cmg-ratings-header {
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-bottom: 24px;
          flex-wrap: wrap;
          gap: 12px;
        }
        .cmg-ratings-header h1 {
          font-size: 26px;
          font-weight: 700;
          color: #0f172a;
          margin: 0;
          display: flex;
          align-items: center;
          gap: 10px;
        }
        .cmg-stat-grid {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
          gap: 16px;
          margin-bottom: 24px;
        }
        .cmg-stat-card {
          background: #ffffff;
          padding: 20px;
          border-radius: 10px;
          border: 1px solid #e2e8f0;
          box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }
        .cmg-stat-title {
          font-size: 12px;
          font-weight: 700;
          text-transform: uppercase;
          color: #64748b;
          letter-spacing: 0.5px;
          margin-bottom: 6px;
        }
        .cmg-stat-value {
          font-size: 28px;
          font-weight: 700;
          color: #0f172a;
          display: flex;
          align-items: baseline;
          gap: 8px;
        }
        .cmg-stat-desc {
          font-size: 13px;
          color: #94a3b8;
          margin-top: 4px;
        }
        .cmg-filter-bar {
          background: #ffffff;
          padding: 16px 20px;
          border-radius: 8px;
          border: 1px solid #e2e8f0;
          margin-bottom: 20px;
          display: flex;
          align-items: center;
          justify-content: space-between;
          flex-wrap: wrap;
          gap: 12px;
        }
        .cmg-filter-group {
          display: flex;
          align-items: center;
          gap: 10px;
          flex-wrap: wrap;
        }
        .cmg-table-wrap {
          background: #ffffff;
          border-radius: 8px;
          border: 1px solid #e2e8f0;
          overflow-x: auto;
          box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }
        .cmg-ratings-table {
          width: 100%;
          border-collapse: collapse;
          text-align: left;
          font-size: 13px;
        }
        .cmg-ratings-table th {
          background: #f8fafc;
          color: #475569;
          font-weight: 600;
          padding: 12px 16px;
          border-bottom: 1px solid #e2e8f0;
          white-space: nowrap;
        }
        .cmg-ratings-table td {
          padding: 12px 16px;
          border-bottom: 1px solid #f1f5f9;
          color: #1e293b;
          vertical-align: middle;
        }
        .cmg-ratings-table tr:hover td {
          background: #f8fafc;
        }
        .cmg-stars-display {
          color: #f59e0b;
          font-size: 15px;
          letter-spacing: 1px;
          display: inline-flex;
          align-items: center;
          gap: 4px;
        }
        .cmg-badge-ip {
          background: #f1f5f9;
          color: #0f172a;
          padding: 3px 8px;
          border-radius: 4px;
          font-family: monospace;
          font-size: 12px;
          border: 1px solid #e2e8f0;
        }
        .cmg-badge-device {
          background: #f8fafc;
          color: #475569;
          padding: 3px 8px;
          border-radius: 4px;
          font-family: monospace;
          font-size: 11px;
          max-width: 140px;
          display: inline-block;
          overflow: hidden;
          text-overflow: ellipsis;
          white-space: nowrap;
        }
        .cmg-btn {
          display: inline-flex;
          align-items: center;
          gap: 6px;
          padding: 8px 14px;
          border-radius: 6px;
          font-size: 13px;
          font-weight: 600;
          text-decoration: none;
          cursor: pointer;
          border: 1px solid transparent;
          transition: all 0.2s ease;
        }
        .cmg-btn-primary {
          background: #2563eb;
          color: #ffffff !important;
        }
        .cmg-btn-primary:hover {
          background: #1d4ed8;
        }
        .cmg-btn-outline {
          background: #ffffff;
          color: #334155 !important;
          border-color: #cbd5e1;
        }
        .cmg-btn-outline:hover {
          background: #f8fafc;
          border-color: #94a3b8;
        }
        .cmg-btn-delete {
          color: #dc2626 !important;
          font-size: 12px;
          text-decoration: none;
        }
        .cmg-btn-delete:hover {
          text-decoration: underline;
        }
        .cmg-api-box {
          margin-top: 30px;
          background: #ffffff;
          padding: 24px;
          border-radius: 8px;
          border: 1px solid #e2e8f0;
        }
      </style>

      <?php if ( isset( $_GET['deleted'] ) ) : ?>
        <div class="notice notice-success is-dismissible"><p>Rating record deleted successfully.</p></div>
      <?php endif; ?>

      <div class="cmg-ratings-header">
        <h1>
          <span class="dashicons dashicons-star-filled" style="color: #f59e0b; font-size: 28px; width: 28px; height: 28px;"></span>
          Blog Ratings & Captured Data
        </h1>
        <div style="display: flex; gap: 10px;">
          <a href="<?php echo esc_url( $export_url ); ?>" class="cmg-btn cmg-btn-outline">
            <span class="dashicons dashicons-download" style="font-size: 16px; margin-top: 2px;"></span>
            Export CSV
          </a>
        </div>
      </div>

      <!-- Stats Overview Cards -->
      <div class="cmg-stat-grid">
        <div class="cmg-stat-card">
          <div class="cmg-stat-title">Total Ratings Submitted</div>
          <div class="cmg-stat-value"><?php echo number_format_i18n( intval( $total_count ) ); ?></div>
          <div class="cmg-stat-desc">Across all blog posts</div>
        </div>
        <div class="cmg-stat-card">
          <div class="cmg-stat-title">Overall Average Score</div>
          <div class="cmg-stat-value">
            <?php echo $avg_rating ? number_format( floatval( $avg_rating ), 1 ) : '0.0'; ?>
            <span style="color: #f59e0b; font-size: 18px;">★</span>
          </div>
          <div class="cmg-stat-desc">Out of 5.0 stars</div>
        </div>
        <div class="cmg-stat-card">
          <div class="cmg-stat-title">Unique IP Addresses</div>
          <div class="cmg-stat-value"><?php echo number_format_i18n( intval( $unique_ips ) ); ?></div>
          <div class="cmg-stat-desc">Distinct user IPs logged</div>
        </div>
        <div class="cmg-stat-card">
          <div class="cmg-stat-title">Unique Devices Tracked</div>
          <div class="cmg-stat-value"><?php echo number_format_i18n( intval( $unique_devs ) ); ?></div>
          <div class="cmg-stat-desc">Cross-device UUIDs</div>
        </div>
      </div>

      <!-- Filter & Search Bar -->
      <form method="get" action="">
        <input type="hidden" name="page" value="cmg-blog-ratings" />
        <div class="cmg-filter-bar">
          <div class="cmg-filter-group">
            <label for="filter_rating" style="font-weight: 600; font-size: 13px;">Filter Stars:</label>
            <select name="filter_rating" id="filter_rating" style="padding: 4px 8px; border-radius: 4px;">
              <option value="0">All Ratings</option>
              <option value="5" <?php selected( $filter_rating, 5 ); ?>>5 Stars ★★★★★</option>
              <option value="4" <?php selected( $filter_rating, 4 ); ?>>4 Stars ★★★★☆</option>
              <option value="3" <?php selected( $filter_rating, 3 ); ?>>3 Stars ★★★☆☆</option>
              <option value="2" <?php selected( $filter_rating, 2 ); ?>>2 Stars ★★☆☆☆</option>
              <option value="1" <?php selected( $filter_rating, 1 ); ?>>1 Star ★☆☆☆☆</option>
            </select>

            <input type="text" name="s" value="<?php echo esc_attr( $search_query ); ?>" placeholder="Search post, IP, device..." style="padding: 5px 10px; border-radius: 4px; min-width: 240px;" />
            <button type="submit" class="button button-primary">Filter</button>
            <?php if ( $filter_rating || $search_query ) : ?>
              <a href="<?php echo esc_url( admin_url( 'admin.php?page=cmg-blog-ratings' ) ); ?>" class="button">Clear</a>
            <?php endif; ?>
          </div>
          <div style="font-size: 13px; color: #64748b;">
            Showing <?php echo count( $ratings ); ?> of <?php echo intval( $total_items ); ?> records
          </div>
        </div>
      </form>

      <!-- Data Table -->
      <div class="cmg-table-wrap">
        <table class="cmg-ratings-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Post Title / Slug</th>
              <th>Rating</th>
              <th>IP Address</th>
              <th>Device UUID</th>
              <th>User Agent / Browser</th>
              <th>Date & Time</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if ( ! empty( $ratings ) ) : ?>
              <?php foreach ( $ratings as $r ) : ?>
                <tr>
                  <td style="color: #64748b;">#<?php echo esc_html( $r->id ); ?></td>
                  <td>
                    <?php if ( $r->post_id ) : ?>
                      <a href="<?php echo esc_url( get_permalink( $r->post_id ) ); ?>" target="_blank" style="font-weight: 600; text-decoration: none; color: #2563eb;">
                        <?php echo esc_html( get_the_title( $r->post_id ) ); ?>
                      </a>
                    <?php else : ?>
                      <span style="font-weight: 600;"><?php echo esc_html( $r->blog_title ? $r->blog_title : 'Blog Article' ); ?></span>
                    <?php endif; ?>
                    <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">
                      Slug: <?php echo esc_html( $r->blog_title ); ?>
                    </div>
                  </td>
                  <td>
                    <span class="cmg-stars-display">
                      <?php
                      $stars_html = '';
                      for ( $i = 1; $i <= 5; $i++ ) {
                          $stars_html .= ( $i <= $r->rating ) ? '★' : '☆';
                      }
                      echo $stars_html . ' ' . number_format( $r->rating, 1 );
                      ?>
                    </span>
                  </td>
                  <td>
                    <span class="cmg-badge-ip"><?php echo esc_html( $r->ip_address ? $r->ip_address : 'Unknown' ); ?></span>
                  </td>
                  <td>
                    <span class="cmg-badge-device" title="<?php echo esc_attr( $r->cross_device_id ); ?>">
                      <?php echo esc_html( $r->cross_device_id ? substr( $r->cross_device_id, 0, 14 ) . '...' : 'None' ); ?>
                    </span>
                  </td>
                  <td>
                    <span style="font-size: 12px; color: #475569;" title="<?php echo esc_attr( $r->user_agent ); ?>">
                      <?php
                      $ua = $r->user_agent;
                      $browser = 'Browser';
                      if ( strpos( $ua, 'Chrome' ) !== false && strpos( $ua, 'Edg' ) === false ) $browser = 'Chrome';
                      elseif ( strpos( $ua, 'Safari' ) !== false && strpos( $ua, 'Chrome' ) === false ) $browser = 'Safari';
                      elseif ( strpos( $ua, 'Firefox' ) !== false ) $browser = 'Firefox';
                      elseif ( strpos( $ua, 'Edg' ) !== false ) $browser = 'Edge';

                      $os = 'Desktop';
                      if ( strpos( $ua, 'Android' ) !== false ) $os = 'Android';
                      elseif ( strpos( $ua, 'iPhone' ) !== false || strpos( $ua, 'iPad' ) !== false ) $os = 'iOS';
                      elseif ( strpos( $ua, 'Windows' ) !== false ) $os = 'Windows';
                      elseif ( strpos( $ua, 'Mac' ) !== false ) $os = 'macOS';

                      echo esc_html( "$browser on $os" );
                      ?>
                    </span>
                  </td>
                  <td style="white-space: nowrap; color: #64748b; font-size: 12px;">
                    <?php echo esc_html( date( 'd M Y, H:i', strtotime( $r->created_at ) ) ); ?>
                  </td>
                  <td>
                    <?php
                    $delete_url = wp_nonce_url( admin_url( 'admin.php?page=cmg-blog-ratings&action=delete_rating&id=' . $r->id ), 'cmg_delete_rating_' . $r->id );
                    ?>
                    <a href="<?php echo esc_url( $delete_url ); ?>" class="cmg-btn-delete" onclick="return confirm('Delete this rating record?');">Delete</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else : ?>
              <tr>
                <td colspan="8" style="text-align: center; padding: 30px; color: #64748b;">
                  No ratings logged yet. When readers rate blog articles, their rating, IP address, device ID, and timestamp will appear here automatically!
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Live CMGalaxy Backend API Tester Box -->
      <div class="cmg-api-box">
        <h3 style="margin-top: 0; font-size: 16px; font-weight: 700; color: #0f172a;">
          <span class="dashicons dashicons-rest-api" style="margin-top: 2px;"></span>
          Query Live CMGalaxy Backend API (fetch_rating)
        </h3>
        <p style="font-size: 13px; color: #64748b; margin-bottom: 16px;">
          Fetch live aggregated ratings directly from <code>https://staging-api.cmgalaxy.com/api/v2/commonapis/fetch_rating/</code> for any article slug:
        </p>
        <div style="display: flex; gap: 10px; max-width: 600px;">
          <input type="text" id="cmg_test_slug" value="is-your-cac-high-because-youre-ignoring-creative-analysis" style="flex: 1; padding: 6px 10px;" />
          <button type="button" class="button button-primary" id="cmg_btn_test_api">Fetch Live API</button>
        </div>
        <div id="cmg_api_result" style="margin-top: 14px; font-family: monospace; font-size: 12px; background: #f8fafc; padding: 14px; border-radius: 6px; border: 1px solid #e2e8f0; display: none;"></div>
      </div>

      <script>
      document.getElementById('cmg_btn_test_api').addEventListener('click', function() {
        var slug = document.getElementById('cmg_test_slug').value.trim();
        var resBox = document.getElementById('cmg_api_result');
        resBox.style.display = 'block';
        resBox.innerHTML = 'Loading from https://staging-api.cmgalaxy.com...';

        fetch('https://staging-api.cmgalaxy.com/api/v2/commonapis/fetch_rating/', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            cross_device_id: '11111111-2222-3333-4444-555555555555',
            blog_title: slug
          })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
          resBox.innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
        })
        .catch(function(err) {
          resBox.innerHTML = '<span style="color:red;">Error: ' + err + '</span>';
        });
      });
      </script>
    </div>
    <?php
}
