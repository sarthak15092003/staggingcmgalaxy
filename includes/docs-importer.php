<?php
/**
 * CMGalaxy Docs API Importer & Live Sync Tool
 * Fetches and synchronizes categories and articles from https://docs.cmgalaxy.com/
 * into the local 'docs' post type and 'doc_category' taxonomy.
 *
 * @package HelloElementor
 */

if (!defined('ABSPATH')) {
    exit;
}

const CMG_DOCS_REMOTE_API = 'https://docs.cmgalaxy.com/wp-json/wp/v2';

/**
 * 1. Register Admin Page under Docs Menu
 */
function cmg_docs_importer_admin_menu() {
    add_submenu_page(
        'edit.php?post_type=docs',
        __('Sync Live Docs', 'hello-elementor'),
        __('Sync Live Docs', 'hello-elementor'),
        'manage_options',
        'docs-live-sync',
        'cmg_docs_importer_admin_page'
    );
}
add_action('admin_menu', 'cmg_docs_importer_admin_menu');

/**
 * 2. Admin Page Render
 */
function cmg_docs_importer_admin_page() {
    $local_docs_count = wp_count_posts('docs')->publish ?? 0;
    $local_cats_count = wp_count_terms(array('taxonomy' => 'doc_category', 'hide_empty' => false));
    ?>
    <div class="wrap" style="max-width: 900px; margin-top: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
        <div style="background: #ffffff; padding: 28px 32px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
            
            <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 20px; margin-bottom: 24px;">
                <div>
                    <h1 style="font-size: 24px; font-weight: 700; color: #0f172a; margin: 0 0 6px 0;">Sync Documentation from Live CMGalaxy</h1>
                    <p style="color: #64748b; font-size: 14.5px; margin: 0;">
                        Source: <a href="https://docs.cmgalaxy.com/" target="_blank" rel="noopener" style="color: #3b82f6; font-weight: 600; text-decoration: none;">https://docs.cmgalaxy.com/</a>
                    </p>
                </div>
                <div style="text-align: right;">
                    <span style="display: inline-block; padding: 6px 12px; background: #ecfdf5; color: #059669; border-radius: 999px; font-size: 13px; font-weight: 600;">REST API v2 Ready</span>
                </div>
            </div>

            <!-- Stats Grid -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 28px;">
                <div style="background: #f8fafc; padding: 20px; border-radius: 10px; border: 1px solid #e2e8f0;">
                    <div style="font-size: 13px; color: #64748b; text-transform: uppercase; font-weight: 600;">Local Docs Articles</div>
                    <div style="font-size: 32px; font-weight: 700; color: #0f172a; margin-top: 6px;" id="local-docs-count"><?php echo intval($local_docs_count); ?></div>
                </div>
                <div style="background: #f8fafc; padding: 20px; border-radius: 10px; border: 1px solid #e2e8f0;">
                    <div style="font-size: 13px; color: #64748b; text-transform: uppercase; font-weight: 600;">Local Doc Categories</div>
                    <div style="font-size: 32px; font-weight: 700; color: #0f172a; margin-top: 6px;" id="local-cats-count"><?php echo intval($local_cats_count); ?></div>
                </div>
            </div>

            <!-- Action Button & Progress -->
            <div style="margin-bottom: 28px;">
                <button type="button" id="start-sync-btn" class="button button-primary" style="padding: 10px 28px; height: auto; font-size: 15px; font-weight: 600; background: #3b82f6; border-color: #3b82f6; border-radius: 8px; cursor: pointer;">
                    Start Live Sync
                </button>
            </div>

            <!-- Progress Bar -->
            <div id="sync-progress-wrap" style="display: none; margin-bottom: 24px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px; font-weight: 600; color: #334155;">
                    <span id="sync-status-text">Synchronizing categories...</span>
                    <span id="sync-percentage-text">0%</span>
                </div>
                <div style="width: 100%; height: 12px; background: #e2e8f0; border-radius: 999px; overflow: hidden;">
                    <div id="sync-progress-bar" style="width: 0%; height: 100%; background: linear-gradient(90deg, #3b82f6, #10b981); transition: width 0.3s ease;"></div>
                </div>
            </div>

            <!-- Live Terminal Log -->
            <div id="sync-log-terminal" style="background: #0f172a; color: #38bdf8; font-family: monospace; font-size: 13px; padding: 16px; border-radius: 8px; max-height: 280px; overflow-y: auto; display: none; line-height: 1.6;">
                <div>[Ready] Click "Start Live Sync" to fetch all 133 categories and 1,148 articles from live docs.cmgalaxy.com...</div>
            </div>

        </div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        var isSyncing = false;
        var totalPostPages = 1;
        var currentPostPage = 1;
        var totalImportedDocs = 0;

        function logMessage(msg, isSuccess) {
            var color = isSuccess ? '#4ade80' : '#38bdf8';
            $('#sync-log-terminal').append('<div style="color: ' + color + ';">' + msg + '</div>');
            $('#sync-log-terminal').scrollTop($('#sync-log-terminal')[0].scrollHeight);
        }

        $('#start-sync-btn').on('click', function() {
            if (isSyncing) return;
            isSyncing = true;
            $(this).prop('disabled', true).text('Syncing in progress...');
            $('#sync-progress-wrap').show();
            $('#sync-log-terminal').show();
            logMessage('==> Step 1: Requesting categories from docs.cmgalaxy.com...', false);

            // Step 1: Sync Categories
            $.post(ajaxurl, {
                action: 'cmg_docs_sync_categories',
                nonce: '<?php echo wp_create_nonce("cmg_sync_docs_nonce"); ?>'
            }, function(res) {
                if (res.success) {
                    logMessage('✓ Imported/Updated ' + res.data.count + ' categories & subcategories successfully.', true);
                    $('#sync-progress-bar').css('width', '10%');
                    $('#sync-percentage-text').text('10%');
                    $('#local-cats-count').text(res.data.count);

                    // Step 2: Start Post Sync Batches
                    logMessage('==> Step 2: Starting articles batch fetch...', false);
                    syncPostsBatch(1);
                } else {
                    logMessage('✗ Error syncing categories: ' + (res.data.message || 'Unknown error'), false);
                    finishSync();
                }
            }).fail(function() {
                logMessage('✗ Failed to contact server for category sync.', false);
                finishSync();
            });
        });

        function syncPostsBatch(page) {
            $('#sync-status-text').text('Importing articles (Page ' + page + ')...');
            
            $.post(ajaxurl, {
                action: 'cmg_docs_sync_posts_batch',
                page: page,
                nonce: '<?php echo wp_create_nonce("cmg_sync_docs_nonce"); ?>'
            }, function(res) {
                if (res.success) {
                    totalPostPages = res.data.total_pages;
                    currentPostPage = res.data.current_page;
                    totalImportedDocs += res.data.batch_count;
                    $('#local-docs-count').text(totalImportedDocs);

                    var pct = Math.min(99, Math.round(10 + (currentPostPage / totalPostPages) * 90));
                    $('#sync-progress-bar').css('width', pct + '%');
                    $('#sync-percentage-text').text(pct + '%');
                    logMessage('✓ Page ' + currentPostPage + '/' + totalPostPages + ' processed (+ ' + res.data.batch_count + ' articles).', true);

                    if (currentPostPage < totalPostPages) {
                        syncPostsBatch(currentPostPage + 1);
                    } else {
                        $('#sync-progress-bar').css('width', '100%');
                        $('#sync-percentage-text').text('100%');
                        $('#sync-status-text').text('Sync completed successfully!');
                        logMessage('🎉 SUCCESS! All ' + totalImportedDocs + ' articles and categories synchronized from live docs!', true);
                        finishSync();
                    }
                } else {
                    logMessage('✗ Error processing page ' + page + ': ' + (res.data.message || 'Unknown'), false);
                    finishSync();
                }
            }).fail(function() {
                logMessage('✗ Request timeout or error on page ' + page + '. Retrying in 3s...', false);
                setTimeout(function() {
                    syncPostsBatch(page);
                }, 3000);
            });
        }

        function finishSync() {
            isSyncing = false;
            $('#start-sync-btn').prop('disabled', false).text('Run Live Sync Again');
        }
    });
    </script>
    <?php
}

/**
 * 3. AJAX Handler: Sync Categories
 */
function cmg_ajax_docs_sync_categories() {
    check_ajax_referer('cmg_sync_docs_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Permission denied.'));
    }

    $all_remote_cats = array();
    $page = 1;

    while (true) {
        $response = wp_remote_get(CMG_DOCS_REMOTE_API . '/categories?per_page=100&page=' . $page, array(
            'timeout' => 30,
            'headers' => array('User-Agent' => 'WordPress/CMGalaxy-Sync')
        ));

        if (is_wp_error($response)) {
            break;
        }

        $body = wp_remote_retrieve_body($response);
        $cats = json_decode($body, true);

        if (empty($cats) || !is_array($cats)) {
            break;
        }

        $all_remote_cats = array_merge($all_remote_cats, $cats);
        $total_pages = intval(wp_remote_retrieve_header($response, 'x-wp-totalpages') ?: 1);

        if ($page >= $total_pages) {
            break;
        }
        $page++;
    }

    if (empty($all_remote_cats)) {
        wp_send_json_error(array('message' => 'Could not fetch categories from remote API.'));
    }

    $mapping = get_option('_cmg_remote_cat_mapping', array());
    if (!is_array($mapping)) $mapping = array();

    // First Pass: Insert parent categories
    foreach ($all_remote_cats as $rcat) {
        if ($rcat['parent'] == 0) {
            $term = term_exists($rcat['slug'], 'doc_category');
            if (!$term) {
                $inserted = wp_insert_term(html_entity_decode($rcat['name']), 'doc_category', array(
                    'slug'        => $rcat['slug'],
                    'description' => $rcat['description'] ?? '',
                ));
                if (!is_wp_error($inserted)) {
                    $mapping[$rcat['id']] = $inserted['term_id'];
                }
            } else {
                $term_id = is_array($term) ? $term['term_id'] : $term;
                $mapping[$rcat['id']] = $term_id;
            }
        }
    }

    // Second Pass: Insert child subcategories
    foreach ($all_remote_cats as $rcat) {
        if ($rcat['parent'] > 0) {
            $parent_local_id = isset($mapping[$rcat['parent']]) ? $mapping[$rcat['parent']] : 0;
            $term = term_exists($rcat['slug'], 'doc_category');
            if (!$term) {
                $inserted = wp_insert_term(html_entity_decode($rcat['name']), 'doc_category', array(
                    'slug'        => $rcat['slug'],
                    'parent'      => $parent_local_id,
                    'description' => $rcat['description'] ?? '',
                ));
                if (!is_wp_error($inserted)) {
                    $mapping[$rcat['id']] = $inserted['term_id'];
                }
            } else {
                $term_id = is_array($term) ? $term['term_id'] : $term;
                wp_update_term($term_id, 'doc_category', array(
                    'parent' => $parent_local_id
                ));
                $mapping[$rcat['id']] = $term_id;
            }
        }
    }

    update_option('_cmg_remote_cat_mapping', $mapping);

    wp_send_json_success(array(
        'count'   => count($all_remote_cats),
        'mapping' => count($mapping)
    ));
}
add_action('wp_ajax_cmg_docs_sync_categories', 'cmg_ajax_docs_sync_categories');

/**
 * 4. AJAX Handler: Sync Posts Batch
 */
function cmg_ajax_docs_sync_posts_batch() {
    check_ajax_referer('cmg_sync_docs_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Permission denied.'));
    }

    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $mapping = get_option('_cmg_remote_cat_mapping', array());

    $url = CMG_DOCS_REMOTE_API . '/posts?per_page=50&page=' . $page;
    $response = wp_remote_get($url, array(
        'timeout' => 45,
        'headers' => array('User-Agent' => 'WordPress/CMGalaxy-Sync')
    ));

    if (is_wp_error($response)) {
        wp_send_json_error(array('message' => $response->get_error_message()));
    }

    $total_pages = intval(wp_remote_retrieve_header($response, 'x-wp-totalpages') ?: 1);
    $body = wp_remote_retrieve_body($response);
    $posts = json_decode($body, true);

    if (empty($posts) || !is_array($posts)) {
        wp_send_json_success(array(
            'current_page' => $page,
            'total_pages'  => $total_pages,
            'batch_count'  => 0,
        ));
    }

    $batch_count = 0;

    foreach ($posts as $p) {
        $title   = isset($p['title']['rendered']) ? html_entity_decode($p['title']['rendered']) : '';
        $content = isset($p['content']['rendered']) ? $p['content']['rendered'] : '';
        $excerpt = isset($p['excerpt']['rendered']) ? $p['excerpt']['rendered'] : '';
        $slug    = isset($p['slug']) ? sanitize_title($p['slug']) : '';
        $date    = isset($p['date']) ? $p['date'] : '';

        if (empty($title) && empty($slug)) continue;

        // Map remote categories to local doc_category terms
        $assigned_term_ids = array();
        if (!empty($p['categories'])) {
            foreach ($p['categories'] as $rcat_id) {
                if (isset($mapping[$rcat_id])) {
                    $assigned_term_ids[] = intval($mapping[$rcat_id]);
                }
            }
        }

        // Check if doc exists
        $existing = get_page_by_path($slug, OBJECT, 'docs');
        if (!$existing) {
            $existing = get_page_by_path($slug, OBJECT, 'post');
        }

        if ($existing) {
            $post_id = $existing->ID;
            wp_update_post(array(
                'ID'           => $post_id,
                'post_title'   => $title,
                'post_content' => $content,
                'post_excerpt' => $excerpt,
                'post_type'    => 'docs',
                'post_status'  => 'publish'
            ));
        } else {
            $post_id = wp_insert_post(array(
                'post_title'   => $title,
                'post_name'    => $slug,
                'post_content' => $content,
                'post_excerpt' => $excerpt,
                'post_type'    => 'docs',
                'post_status'  => 'publish',
                'post_date'    => $date,
            ));
        }

        if (!empty($assigned_term_ids) && !is_wp_error($post_id)) {
            wp_set_object_terms($post_id, $assigned_term_ids, 'doc_category');
            wp_set_object_terms($post_id, $assigned_term_ids, 'category');
        }

        $batch_count++;
    }

    wp_send_json_success(array(
        'current_page' => $page,
        'total_pages'  => $total_pages,
        'batch_count'  => $batch_count,
    ));
}
add_action('wp_ajax_cmg_docs_sync_posts_batch', 'cmg_ajax_docs_sync_posts_batch');
