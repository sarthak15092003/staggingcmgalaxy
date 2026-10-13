<?php
/**
 * CMGALAXY Documentation Header Component
 * Exact header matching docs.cmgalaxy.com and Docy layout-cmgalaxy.
 *
 * @package HelloElementor
 */

if (!defined('ABSPATH')) {
    exit;
}

if (defined('CMG_DOCS_HEADER_RENDERED') || !empty($GLOBALS['cmg_docs_header_rendered'])) {
    return;
}
define('CMG_DOCS_HEADER_RENDERED', true);
$GLOBALS['cmg_docs_header_rendered'] = true;

$http_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
$request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
$is_https = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
$current_url = ($is_https ? "https://" : "http://") . $http_host . $request_uri;
$s_value = get_search_query() ? get_search_query() : '';

$post_obj = get_post();
$post_name = ($post_obj && isset($post_obj->post_name)) ? $post_obj->post_name : '';
$is_home_active = is_front_page() || is_home() || is_post_type_archive('docs') || is_page_template('page-templates/template-docs-home.php') || (is_page() && in_array($post_name, array('docs', 'kb', 'knowledge-base')));
$is_docs_active = is_singular('docs') || is_tax('doc_category') || is_tax('doc_tag') || is_tax('doc_dir') || is_post_type_archive('docs') || (is_page() && in_array($post_name, array('docs', 'kb', 'knowledge-base')));

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

$getting_started_term = get_term_by('slug', 'getting-started', 'doc_category');
if (!$getting_started_term) {
    $getting_started_term = get_term_by('slug', 'getting-started', 'category');
}
$getting_started_url = ($getting_started_term && !is_wp_error($getting_started_term))
    ? get_term_link($getting_started_term)
    : home_url('/getting-started/');

$docs_url = get_post_type_archive_link('docs') ? get_post_type_archive_link('docs') : home_url('/docs/');
$signin_url = add_query_arg('redirect_to', urlencode($current_url), home_url('/signin/'));

$logo_file = get_template_directory() . '/assets/images/kblogo.svg';
$logo_ver = file_exists($logo_file) ? filemtime($logo_file) : time();
$logo_url = file_exists($logo_file)
    ? add_query_arg('v', $logo_ver, get_template_directory_uri() . '/assets/images/kblogo.svg')
    : 'https://docs.cmgalaxy.com/wp-content/themes/docy/assets/img/kblogo.svg';

$logo_png_file = get_template_directory() . '/assets/images/kblogo.png';
$logo_png_ver  = file_exists($logo_png_file) ? filemtime($logo_png_file) : $logo_ver;
$logo_png_url  = file_exists($logo_png_file)
    ? add_query_arg('v', $logo_png_ver, get_template_directory_uri() . '/assets/images/kblogo.png')
    : $logo_url;

$lex_logo_url = file_exists(get_template_directory() . '/assets/images/lexlogo.svg')
    ? get_template_directory_uri() . '/assets/images/lexlogo.svg'
    : 'https://docs.cmgalaxy.com/wp-content/themes/docy/assets/img/lexlogo.svg';

$signin_icon_url = file_exists(get_template_directory() . '/assets/images/signin.svg')
    ? get_template_directory_uri() . '/assets/images/signin.svg'
    : 'https://docs.cmgalaxy.com/wp-content/themes/docy/assets/img/signin.svg';
?>

<!-- CMGALAXY Docs Complete Header -->
<header id="docy-header" class="cmgalaxy-doc-header-wrap header">
    <div class="cmgalaxy-doc-header-inner">
        
        <!-- Top Row: Logo, Search, Ask Lex, Profile/SignIn -->
        <div class="cmgalaxy-header-main">
            
            <!-- Logo Section -->
            <div class="cmgalaxy-logo-section">
                <a href="<?php echo esc_url($docs_url); ?>" class="cmgalaxy-logo-link" title="CMGalaxy Knowledge Base">
                    <picture class="cmgalaxy-logo-picture">
                        <source srcset="<?php echo esc_url($logo_url); ?>" type="image/svg+xml">
                        <img src="<?php echo esc_url($logo_png_url); ?>" 
                             alt="CMGALAXY Knowledge Base" 
                             class="cmgalaxy-logo-img"
                             width="160"
                             height="40"
                             loading="eager"
                             decoding="sync"
                             fetchpriority="high"
                             data-no-lazy="1"
                             data-skip-lazy="1"
                             onerror="if(!this.dataset.triedPng){this.dataset.triedPng='1';this.src='<?php echo esc_url($logo_png_url); ?>';}">
                    </picture>
                </a>
            </div>

            <!-- Search Section -->
            <div class="cmgalaxy-search-section">
                <div class="cmgalaxy-search-row">
                    <form action="<?php echo esc_url($docs_url); ?>" class="cmgalaxy-search-form" method="get" role="search">
                        <input type="hidden" name="post_type" value="docs">
                        <div class="search-input-wrapper">
                            <!-- Magnifying Glass Icon -->
                            <svg class="search-icon-left" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <input type="search" 
                                   placeholder="<?php esc_attr_e("Search ('/' to focus)", 'hello-elementor'); ?>" 
                                   name="s" 
                                   value="<?php echo esc_attr($s_value); ?>" 
                                   class="cmgalaxy-search-input" 
                                   id="cmgalaxy-search-input"
                                   autocomplete="off" 
                                   spellcheck="false">
                            <span class="cmgalaxy-search-loader" aria-hidden="true"></span>
                            <div class="cmgalaxy-search-suggestions" id="search-suggestions"></div>
                        </div>
                    </form>

                    <!-- Ask Lex Button -->
                    <a href="#" class="cmgalaxy-ask-lex-btn" id="cmg-ask-lex-btn" title="Ask Lex AI Assistant">
                        <img src="<?php echo esc_url($lex_logo_url); ?>" alt="" class="cmgalaxy-ask-lex-logo">
                        <span>Ask Lex</span>
                    </a>
                </div>
            </div>

            <!-- Right Actions: Sign In or User Profile -->
            <div class="cmgalaxy-actions">
                <?php if (is_user_logged_in()) : 
                    $current_user = wp_get_current_user();
                    $brand_name = get_user_meta($current_user->ID, 'brand_name', true);
                    $display_name = !empty($brand_name) ? $brand_name : (!empty($current_user->first_name) ? $current_user->first_name : $current_user->display_name);
                    $initial = strtoupper(substr($display_name, 0, 1));
                    $user_email = $current_user->user_email;
                ?>
                    <div class="cmg-user-profile-dropdown" id="cmgProfileDropdown">
                        <button type="button" class="cmg-profile-trigger" id="cmgProfileTrigger" aria-expanded="false" aria-haspopup="true">
                            <span class="cmg-avatar-circle"><?php echo esc_html($initial); ?></span>
                            <span class="cmg-user-name"><?php echo esc_html($display_name); ?></span>
                            <svg class="cmg-chevron-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </button>
                        <div class="cmg-dropdown-menu" id="cmgProfileDropdownMenu">
                            <div class="cmg-dropdown-header">
                                <div class="cmg-dropdown-user-name"><?php echo esc_html($display_name); ?></div>
                                <div class="cmg-dropdown-user-email"><?php echo esc_html($user_email); ?></div>
                            </div>
                            <div class="cmg-dropdown-divider"></div>
                            <a href="https://platform.cmgalaxy.com" target="_blank" rel="noopener noreferrer" class="cmg-dropdown-item">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="14" width="7" height="7"></rect>
                                    <rect x="3" y="14" width="7" height="7"></rect>
                                </svg>
                                Platform App
                            </a>
                            <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>" class="cmg-dropdown-item cmg-logout-item">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg>
                                Sign Out
                            </a>
                        </div>
                    </div>
                <?php else : ?>
                    <a href="<?php echo esc_url($signin_url); ?>" class="cmgalaxy-nav-link cmgalaxy-signin-btn">
                        <img src="<?php echo esc_url($signin_icon_url); ?>" alt="" class="cmgalaxy-signin-icon">
                        <span>Sign In</span>
                    </a>
                <?php endif; ?>
            </div>

        </div>

        <!-- Sub-Navigation Bar Row (Home and Get Started tabs matching docs.cmgalaxy.com) -->
        <div class="cmgalaxy-nav-menu">
            <ul class="cmgalaxy-main-nav">
                <li class="nav-item <?php echo !$is_get_started_active ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url($docs_url); ?>" class="nav-link">Home</a>
                </li>
                <li class="nav-item <?php echo $is_get_started_active ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url($getting_started_url); ?>" class="nav-link">Get Started</a>
                </li>
            </ul>
        </div>

    </div>
</header>

<!-- Floating Lex Side Tab Trigger -->
<button id="lex-side-trigger" class="lex-side-trigger" aria-label="Open Lex Assistant">
    <svg width="12" height="20" viewBox="0 0 12 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M10 2l-8 10 8 10" stroke="#000000" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</button>

<!-- Lex Assistant Drawer -->
<div id="lex-drawer" class="lex-drawer" role="dialog" aria-modal="true" aria-label="Lex Assistant">
    <div class="lex-drawer-overlay" id="lex-drawer-overlay"></div>
    <div class="lex-drawer-panel">
        <button type="button" class="lex-drawer-expand" id="lex-drawer-expand" aria-label="Expand or Collapse Drawer" title="Expand or Collapse Drawer">
            <svg class="lex-icon-expand" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
            <svg class="lex-icon-collapse" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </button>
        <div class="lex-drawer-body">
            <div class="lex-drawer-content">
                <iframe id="lex-assistant-frame" src="<?php echo esc_url(add_query_arg(array('ajax_url' => admin_url('admin-ajax.php'), 'v' => filemtime(get_template_directory() . '/assets/html/lex-assistant.html')), get_template_directory_uri() . '/assets/html/lex-assistant.html')); ?>" style="width: 100%; height: 100%; border: none;" title="Lex Assistant"></iframe>
            </div>
        </div>
    </div>
</div>

<div id="cmgalaxy-search-backdrop" class="cmgalaxy-search-backdrop"></div>

<style>
/* Suppress marketing navbars whenever doc header is present */
.ekit-template-content-header,
.cm-navbar,
.cmgalaxy-subnav-bar {
    display: none !important;
    height: 0 !important;
    min-height: 0 !important;
    max-height: 0 !important;
    overflow: hidden !important;
    visibility: hidden !important;
    pointer-events: none !important;
    margin: 0 !important;
    padding: 0 !important;
    border: none !important;
}

:root {
    --cmg-adminbar-height: 0px;
    --cmg-header-height: 107px;
    --cmg-drawer-top: 107px;
}

body.admin-bar {
    --cmg-adminbar-height: 32px;
}

@media screen and (max-width: 782px) {
    body.admin-bar {
        --cmg-adminbar-height: 46px;
    }
}

/* ========================================================
   CMGALAXY DOCS HEADER STYLES (EXACT DOCY MATCH)
   ======================================================== */
.cmgalaxy-doc-header-wrap {
    width: 100% !important;
    background: #ffffff !important;
    border-bottom: 1px solid #e2e8f0 !important;
    position: sticky !important;
    top: var(--cmg-adminbar-height, 0px) !important;
    z-index: 99999 !important;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif !important;
}

.cmgalaxy-doc-header-inner {
    max-width: 1440px !important;
    margin: 0 auto !important;
    padding: 14px 30px 0 30px !important;
    box-sizing: border-box !important;
}

.cmgalaxy-header-main {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    width: 100% !important;
    padding-bottom: 12px !important;
    gap: 20px !important;
}

.cmgalaxy-logo-section {
    flex-shrink: 0 !important;
    min-width: 200px !important;
}

.cmgalaxy-logo-link {
    display: inline-flex !important;
    align-items: center !important;
    text-decoration: none !important;
}

.cmgalaxy-logo-picture {
    display: inline-block !important;
    line-height: 0 !important;
}

.cmgalaxy-logo-img {
    height: 40px !important;
    max-height: 44px !important;
    width: auto !important;
    display: block !important;
    object-fit: contain !important;
    visibility: visible !important;
    opacity: 1 !important;
}

.cmgalaxy-search-section {
    flex: 1 1 520px !important;
    max-width: 620px !important;
    transition: max-width 0.3s ease !important;
}

.cmgalaxy-search-row {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
}

.cmgalaxy-search-form {
    flex: 1 1 auto !important;
    margin: 0 !important;
    padding: 0 !important;
}

.search-input-wrapper {
    position: relative !important;
    width: 100% !important;
}

.search-icon-left {
    position: absolute !important;
    left: 14px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    color: #94a3b8 !important;
    pointer-events: none !important;
}

.cmgalaxy-search-input {
    width: 100% !important;
    height: 42px !important;
    line-height: 42px !important;
    padding: 0 40px 0 38px !important;
    background: #f8fafc !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 999px !important;
    font-size: 14px !important;
    color: #1e293b !important;
    outline: none !important;
    box-sizing: border-box !important;
    transition: all 0.2s ease !important;
    box-shadow: inset 0 1px 2px rgba(0,0,0,0.03) !important;
}

.cmgalaxy-search-input:focus {
    background: #ffffff !important;
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
}

.cmgalaxy-search-loader {
    position: absolute !important;
    right: 14px !important;
    top: 50% !important;
    width: 16px !important;
    height: 16px !important;
    margin-top: -8px !important;
    border: 2px solid #cbd5e1 !important;
    border-top-color: #3b82f6 !important;
    border-radius: 50% !important;
    opacity: 0 !important;
    visibility: hidden !important;
    pointer-events: none !important;
}

.cmgalaxy-search-loader.active {
    opacity: 1 !important;
    visibility: visible !important;
    animation: cmgSearchSpin 0.75s linear infinite !important;
}

@keyframes cmgSearchSpin {
    to { transform: rotate(360deg); }
}

.cmgalaxy-ask-lex-btn {
    height: 42px !important;
    background: #ffffff !important;
    color: #334155 !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 999px !important;
    padding: 0 16px !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    font-size: 14px !important;
    font-weight: 600 !important;
    text-decoration: none !important;
    flex-shrink: 0 !important;
    transition: all 0.2s ease !important;
    box-shadow: 0 1px 2px rgba(0,0,0,0.04) !important;
}

.cmgalaxy-ask-lex-btn:hover {
    border-color: #10b981 !important;
    color: #047857 !important;
    box-shadow: 0 3px 10px rgba(16, 185, 129, 0.2) !important;
    transform: translateY(-1px) !important;
}

.cmgalaxy-ask-lex-logo {
    width: 18px !important;
    height: 18px !important;
    display: block !important;
}

.cmgalaxy-actions {
    flex-shrink: 0 !important;
    min-width: 140px !important;
    display: flex !important;
    justify-content: flex-end !important;
    align-items: center !important;
}

.cmgalaxy-signin-btn {
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    color: #475569 !important;
    font-size: 14px !important;
    font-weight: 600 !important;
    text-decoration: none !important;
    padding: 8px 14px !important;
    border-radius: 8px !important;
    transition: color 0.15s ease, background 0.15s ease !important;
}

.cmgalaxy-signin-btn:hover {
    color: #0f172a !important;
    background: #f8fafc !important;
}

.cmgalaxy-signin-icon {
    width: 20px !important;
    height: 20px !important;
    display: block !important;
}

/* User Profile Trigger & Dropdown */
.cmg-user-profile-dropdown {
    position: relative !important;
}

.cmg-profile-trigger {
    background: #ffffff !important;
    color: #334155 !important;
    height: 40px !important;
    padding: 0 14px 0 8px !important;
    border-radius: 999px !important;
    border: 1px solid #e2e8f0 !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    cursor: pointer !important;
    font-size: 14px !important;
    font-weight: 500 !important;
    transition: all 0.2s ease !important;
}

.cmg-profile-trigger:hover {
    border-color: #cbd5e1 !important;
    background: #f8fafc !important;
}

.cmg-avatar-circle {
    width: 26px !important;
    height: 26px !important;
    border-radius: 50% !important;
    background: #059669 !important;
    color: #ffffff !important;
    font-size: 13px !important;
    font-weight: 700 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.cmg-dropdown-menu {
    position: absolute !important;
    top: calc(100% + 8px) !important;
    right: 0 !important;
    min-width: 220px !important;
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 12px !important;
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.15) !important;
    padding: 8px 0 !important;
    z-index: 99999 !important;
    display: none !important;
}

.cmg-dropdown-menu.show {
    display: block !important;
}

.cmg-dropdown-header {
    padding: 10px 16px 8px 16px !important;
}

.cmg-dropdown-user-name {
    font-size: 14px !important;
    font-weight: 600 !important;
    color: #0f172a !important;
}

.cmg-dropdown-user-email {
    font-size: 12px !important;
    color: #64748b !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.cmg-dropdown-divider {
    height: 1px !important;
    background: #f1f5f9 !important;
    margin: 6px 0 !important;
}

.cmg-dropdown-item {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    padding: 8px 16px !important;
    font-size: 13.5px !important;
    font-weight: 500 !important;
    color: #334155 !important;
    text-decoration: none !important;
    transition: background 0.15s ease !important;
}

.cmg-dropdown-item:hover {
    background: #f8fafc !important;
    color: #1d4ed8 !important;
}

.cmg-dropdown-item.cmg-logout-item:hover {
    background: #fef2f2 !important;
    color: #dc2626 !important;
}

/* Sub-Navigation Bar */
.cmgalaxy-nav-menu {
    border: none !important;
    border-top: none !important;
    padding: 0 !important;
    margin: 0 !important;
}

.cmgalaxy-main-nav {
    display: flex !important;
    align-items: center !important;
    list-style: none !important;
    margin: 0 !important;
    padding: 0 !important;
    gap: 32px !important;
}

.cmgalaxy-main-nav .nav-item {
    margin: 0 !important;
    padding: 0 !important;
}

.cmgalaxy-main-nav .nav-link {
    display: inline-block !important;
    padding: 10px 0 !important;
    color: #64748b !important;
    font-size: 14px !important;
    font-weight: 500 !important;
    text-decoration: none !important;
    border-bottom: 2px solid transparent !important;
    transition: color 0.15s ease, border-color 0.15s ease !important;
}

.cmgalaxy-main-nav .nav-link:hover,
.cmgalaxy-main-nav .nav-item.active .nav-link {
    color: #0f172a !important;
    border-bottom-color: #3b82f6 !important;
    font-weight: 600 !important;
}

/* Search Suggestions Dropdown */
.cmgalaxy-search-suggestions {
    position: absolute !important;
    top: calc(100% + 8px) !important;
    left: 0 !important;
    right: 0 !important;
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 12px !important;
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.1) !important;
    z-index: 10002 !important;
    max-height: 440px !important;
    overflow-y: auto !important;
    display: none !important;
    text-align: left !important;
}

.suggestion-item {
    display: block !important;
    padding: 10px 16px !important;
    text-decoration: none !important;
    border-bottom: 1px solid #f8fafc !important;
    transition: background 0.15s ease !important;
}

.suggestion-item:hover {
    background: #f8fafc !important;
}

.suggestion-title {
    font-size: 14px !important;
    font-weight: 600 !important;
    color: #0f172a !important;
    margin-bottom: 2px !important;
}

.suggestion-meta {
    font-size: 12px !important;
    color: #64748b !important;
}

.ask-ai-suggestion {
    border-top: 1px solid #f1f5f9 !important;
    background: #f8fafc !important;
}

.ask-ai-item {
    padding: 12px 16px !important;
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    cursor: pointer !important;
}

.ask-ai-item:hover {
    background: #eff6ff !important;
}

.ask-ai-text {
    font-size: 14px !important;
    font-weight: 600 !important;
    color: #1d4ed8 !important;
}

.search-highlight {
    background: #fef08a !important;
    color: #854d0e !important;
    padding: 0 2px !important;
    border-radius: 2px !important;
}

/* Floating Lex Trigger Button */
.lex-side-trigger {
    position: fixed !important;
    right: -18px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    z-index: 99999 !important;
    background: #ffffff !important;
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 50% !important;
    width: 36px !important;
    height: 36px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: flex-start !important;
    padding-left: 6px !important;
    cursor: pointer !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

.lex-side-trigger:hover {
    right: -10px !important;
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12) !important;
}

/* Floating Lex Side Tab Trigger */
body.lex-drawer-open .lex-side-trigger {
    opacity: 0 !important;
    visibility: hidden !important;
    pointer-events: none !important;
    transform: translateY(-50%) translateX(100%) !important;
}

/* Lex Drawer: side companion panel below the sticky header (matches docs.cmgalaxy.com) */
.lex-drawer {
    display: none !important;
    position: fixed !important;
    top: var(--cmg-drawer-top, calc(var(--cmg-adminbar-height, 0px) + var(--cmg-header-height, 107px))) !important;
    right: 0 !important;
    bottom: 0 !important;
    width: 420px !important;
    max-width: 95vw !important;
    height: calc(100vh - var(--cmg-drawer-top, calc(var(--cmg-adminbar-height, 0px) + var(--cmg-header-height, 107px)))) !important;
    z-index: 99990 !important;
    pointer-events: auto !important;
    transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

.lex-drawer.open {
    display: block !important;
}

.lex-drawer.expanded {
    width: 900px !important;
    max-width: 95vw !important;
}

/* Reflow documentation content when Lex drawer is open so no text is cut off */
@media screen and (min-width: 992px) {
    body.lex-drawer-open .docs-main-wrapper,
    body.lex-drawer-open .cmgalaxy-category-content-wrap,
    body.lex-drawer-open .site-main {
        margin-right: 420px !important;
        transition: margin-right 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }

    body.lex-drawer-open.lex-expanded .docs-main-wrapper,
    body.lex-drawer-open.lex-expanded .cmgalaxy-category-content-wrap,
    body.lex-drawer-open.lex-expanded .site-main {
        margin-right: 900px !important;
    }

    body.lex-drawer-open .docs-col-toc {
        display: none !important;
    }

    body.lex-drawer-open .docs-col-content {
        flex: 0 0 80% !important;
        max-width: 80% !important;
        width: 80% !important;
    }
}

/* No dark/blurred backdrop overlay - documentation stays 100% visible and readable */
.lex-drawer-overlay {
    display: none !important;
}

/* Drawer Sliding Panel */
.lex-drawer-panel {
    position: relative !important;
    width: 100% !important;
    height: 100% !important;
    display: flex !important;
    flex-direction: column !important;
    animation: lexSlideIn 0.32s cubic-bezier(0.4, 0, 0.2, 1) forwards !important;
    pointer-events: auto !important;
}

.lex-drawer.expanded .lex-drawer-panel {
    width: 100% !important;
}

.lex-drawer-body {
    position: relative !important;
    width: 100% !important;
    height: 100% !important;
    background: #ffffff !important;
    box-shadow: -8px 0 24px rgba(15, 23, 42, 0.08) !important;
    border-left: 1px solid #e2e8f0 !important;
    overflow: hidden !important;
}

.lex-drawer.closing .lex-drawer-panel {
    animation: lexSlideOut 0.25s cubic-bezier(0.4, 0, 0.2, 1) forwards !important;
}

.lex-drawer.lex-no-animation .lex-drawer-panel {
    animation: none !important;
    transition: none !important;
}

@keyframes lexSlideIn {
    from { transform: translateX(100%); }
    to { transform: translateX(0); }
}

@keyframes lexSlideOut {
    from { transform: translateX(0); }
    to { transform: translateX(100%); }
}

/* Clean border edge - expand/close is handled in header icons */
.lex-drawer-expand {
    display: none !important;
}

.lex-side-trigger {
    position: fixed !important;
    right: -18px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    z-index: 99999 !important;
    background: #ffffff !important;
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 50% !important;
    width: 36px !important;
    height: 36px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: flex-start !important;
    padding-left: 6px !important;
    cursor: pointer !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

.lex-side-trigger:hover {
    right: -10px !important;
    background: #ffffff !important;
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12) !important;
}

.lex-side-trigger svg {
    transition: transform 0.3s ease !important;
}

.lex-side-trigger:hover svg {
    transform: translateX(-2px) !important;
}

body.lex-drawer-open .lex-side-trigger {
    opacity: 0 !important;
    visibility: hidden !important;
    pointer-events: none !important;
    transform: translateY(-50%) translateX(100%) !important;
}

.lex-drawer-content {
    width: 100% !important;
    height: 100% !important;
}

@media (max-width: 900px) {
    .cmgalaxy-header-main {
        flex-wrap: wrap !important;
    }
    .cmgalaxy-search-section {
        order: 3 !important;
        max-width: 100% !important;
        flex-basis: 100% !important;
    }
    .cmgalaxy-main-nav {
        gap: 16px !important;
        overflow-x: auto !important;
        white-space: nowrap !important;
    }
}
</style>

<script>
(function($) {
    'use strict';
    $(document).ready(function() {
        var ajaxUrl = "<?php echo admin_url('admin-ajax.php'); ?>";
        var lexLogo = "<?php echo esc_js($lex_logo_url); ?>";
        var searchTimer = null;
        var $searchInput = $('#cmgalaxy-search-input');
        var $suggestions = $('#search-suggestions');
        var $loader = $('.cmgalaxy-search-loader');
        var $drawer = $('#lex-drawer');

        // Shortcut '/' to focus search input
        $(document).on('keydown', function(e) {
            if (e.key === '/' && !$(e.target).is('input, textarea')) {
                e.preventDefault();
                $searchInput.focus();
            }
        });

        // Live Search Input Listener
        $searchInput.on('input', function() {
            var q = $(this).val().trim();
            clearTimeout(searchTimer);

            if (q.length < 2) {
                $suggestions.hide().empty();
                $loader.removeClass('active');
                return;
            }

            $loader.addClass('active');

            searchTimer = setTimeout(function() {
                $.ajax({
                    url: ajaxUrl,
                    type: 'POST',
                    data: { action: 'lex_live_search', query: q },
                    success: function(res) {
                        $loader.removeClass('active');
                        if (res.success && res.data && res.data.results) {
                            renderSuggestions(q, res.data.results);
                        }
                    },
                    error: function() {
                        $loader.removeClass('active');
                    }
                });
            }, 180);
        });

        function renderSuggestions(q, results) {
            $suggestions.empty();
            if (results.length > 0) {
                results.forEach(function(r) {
                    var escaped = q.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, '\\$&');
                    var titleH = r.title.replace(new RegExp('(' + escaped + ')', 'gi'), '<mark class="search-highlight">$1</mark>');
                    var catText = r.category ? r.category : 'Documentation';
                    $suggestions.append(
                        '<a href="' + r.url + '" class="suggestion-item">' +
                            '<div class="suggestion-title">' + titleH + '</div>' +
                            '<div class="suggestion-meta">' + catText + '</div>' +
                        '</a>'
                    );
                });
            }

            // Ask Lex Item at bottom
            $suggestions.append(
                '<div class="ask-ai-suggestion">' +
                    '<div class="ask-ai-item" id="cmg-suggest-ask-lex">' +
                        '<img src="' + lexLogo + '" style="width:20px; height:20px;">' +
                        '<div>' +
                            '<div class="ask-ai-text">Ask Lex: <em>"' + q + '"</em></div>' +
                            '<div class="suggestion-meta" style="font-size:12px; color:#64748b;">Get an AI-powered answer instantly</div>' +
                        '</div>' +
                    '</div>' +
                '</div>'
            );

            $suggestions.show();
        }

        // Close suggestions on outside click
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.search-input-wrapper').length) {
                $suggestions.hide();
            }
        });

        function updateDrawerPosition() {
            var $h = $('.cmgalaxy-doc-header-wrap');
            if ($h.length) {
                var rect = $h[0].getBoundingClientRect();
                var bottom = Math.round(rect.bottom);
                if (bottom > 40) {
                    document.documentElement.style.setProperty('--cmg-drawer-top', bottom + 'px');
                    var drawer = document.getElementById('lex-drawer');
                    if (drawer) {
                        drawer.style.top = bottom + 'px';
                        drawer.style.height = 'calc(100vh - ' + bottom + 'px)';
                    }
                }
            }
        }
        updateDrawerPosition();
        $(window).on('resize scroll', updateDrawerPosition);

        // Open Drawer on Ask Lex Click
        function openDrawer(query) {
            updateDrawerPosition();
            $drawer.removeClass('closing').addClass('open');
            $('body').addClass('lex-drawer-open');
            $suggestions.hide();
            localStorage.setItem('lex_drawer_state', 'open');

            if (query) {
                var iframe = document.getElementById('lex-assistant-frame');
                if (iframe && iframe.contentWindow) {
                    setTimeout(function() {
                        iframe.contentWindow.postMessage({ type: 'lex_start_search', query: query }, '*');
                    }, 400);
                }
            }
            syncLexExpandedState();
        }

        function closeDrawer() {
            $drawer.addClass('closing');
            localStorage.setItem('lex_drawer_state', 'closed');
            setTimeout(function() {
                $drawer.removeClass('open closing expanded');
                $('body').removeClass('lex-drawer-open lex-expanded');
                syncLexExpandedState(false);
            }, 260);
        }

        function toggleLexExpand() {
            $drawer.toggleClass('expanded');
            $('body').toggleClass('lex-expanded', $drawer.hasClass('expanded'));
            var isExpanded = $drawer.hasClass('expanded');
            localStorage.setItem('lex_drawer_expanded', isExpanded ? 'true' : 'false');
            syncLexExpandedState(isExpanded);
        }

        function syncLexExpandedState(isExpanded) {
            var iframe = document.getElementById('lex-assistant-frame');
            if (iframe && iframe.contentWindow) {
                if (typeof isExpanded === 'undefined') {
                    isExpanded = $drawer.hasClass('expanded');
                }
                iframe.contentWindow.postMessage({
                    type: 'lex_expanded_state',
                    expanded: isExpanded
                }, '*');
            }
        }

        // Expose helper functions globally for iframe and parent window
        window.openLexDrawer = openDrawer;
        window.closeLexDrawer = closeDrawer;
        window.toggleLexExpand = toggleLexExpand;
        window.syncLexExpandedState = syncLexExpandedState;

        // Restore drawer expanded & open state if needed
        function restoreLexDrawerState() {
            var state = localStorage.getItem('lex_drawer_state');
            var expanded = localStorage.getItem('lex_drawer_expanded') === 'true';
            if (expanded && $drawer.length) {
                $drawer.addClass('expanded');
                $('body').addClass('lex-expanded');
            }
            if (state === 'open' && $drawer.length) {
                updateDrawerPosition();
                $drawer.addClass('lex-no-animation open');
                $('body').addClass('lex-drawer-open');
                setTimeout(function() {
                    $drawer.removeClass('lex-no-animation');
                    syncLexExpandedState();
                }, 300);
            }
        }
        restoreLexDrawerState();

        // Listen for internal events from Lex Assistant iframe (accept both string and object)
        window.addEventListener('message', function(event) {
            if (!event.data) return;
            if (event.data === 'close-lex' || (typeof event.data === 'object' && event.data.type === 'close-lex')) {
                closeDrawer();
            }
            if (event.data === 'expand-lex' || (typeof event.data === 'object' && event.data.type === 'expand-lex')) {
                toggleLexExpand();
            }
        });

        // Toggle drawer on Ask Lex or side trigger
        $('#cmg-ask-lex-btn, #lex-side-trigger').on('click', function(e) {
            e.preventDefault();
            if ($drawer.hasClass('open')) {
                closeDrawer();
            } else {
                var q = $searchInput.val().trim();
                openDrawer(q);
            }
        });

        $(document).on('click', '#cmg-suggest-ask-lex', function() {
            var q = $searchInput.val().trim();
            openDrawer(q);
        });

        // Close on Escape key press
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $drawer.hasClass('open')) {
                e.preventDefault();
                closeDrawer();
            }
        });

        // User Profile Dropdown Toggle
        var $trigger = $('#cmgProfileTrigger');
        var $menu = $('#cmgProfileDropdownMenu');
        if ($trigger.length && $menu.length) {
            $trigger.on('click', function(e) {
                e.stopPropagation();
                $menu.toggleClass('show');
            });
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#cmgProfileDropdown').length) {
                    $menu.removeClass('show');
                }
            });
        }
    });
})(jQuery);
</script>
