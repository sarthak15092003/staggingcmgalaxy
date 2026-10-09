<?php
/**
 * Docs Search Hero Banner Component
 */

$banner_title = isset($args['title']) ? $args['title'] : 'CMGalaxy Documentation & Help Center';
$banner_subtitle = isset($args['subtitle']) ? $args['subtitle'] : 'Your CMGalaxy questions, answered. Learn how to connect your platforms, read your attribution data, analyse creatives, and turn insights into better ROAS.';
?>

<div class="docs-hero-banner">
    <div class="docs-container">
        <h1 class="docs-hero-title"><?php echo esc_html($banner_title); ?></h1>
        <p class="docs-hero-subtitle"><?php echo esc_html($banner_subtitle); ?></p>
        
        <div class="docs-search-box">
            <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                <span class="docs-search-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </span>
                <input type="search" class="docs-search-input" placeholder="Search documentation, guides, troubleshooting..." value="<?php echo get_search_query(); ?>" name="s" />
                <input type="hidden" name="post_type" value="docs" />
            </form>
        </div>
    </div>
</div>
