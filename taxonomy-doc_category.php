<?php
/**
 * Taxonomy Template for Docs Categories (doc_category)
 * Exact 3-column layout matching Docy theme and CMGalaxy Knowledge Base.
 *
 * @package HelloElementor
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

get_header();

$current_term = get_queried_object();
$current_term_id = $current_term && isset($current_term->term_id) ? $current_term->term_id : 0;
$term_name = $current_term && isset($current_term->name) ? $current_term->name : 'Documentation';
$taxonomy_name = $current_term && isset($current_term->taxonomy) ? $current_term->taxonomy : 'doc_category';

// Calculate total articles count in this category
$articles_count = 0;
if ($current_term_id) {
    $count_query = new WP_Query(array(
        'post_type'      => array('docs', 'post'),
        'tax_query'      => array(
            array(
                'taxonomy'         => $taxonomy_name,
                'field'            => 'term_id',
                'terms'            => $current_term_id,
                'include_children' => true,
            )
        ),
        'posts_per_page' => 1,
    ));
    $articles_count = $count_query->found_posts;
}

$docs_home_url = get_post_type_archive_link('docs') ? get_post_type_archive_link('docs') : home_url('/docs/');
$sidebar_img_url = file_exists(get_template_directory() . '/assets/images/sidebarimg.png') 
    ? get_template_directory_uri() . '/assets/images/sidebarimg.png' 
    : 'https://docs.cmgalaxy.com/wp-content/themes/docy/assets/img/sidebarimg.png';
?>

<div class="docs-main-wrapper" style="padding-top: 28px; padding-bottom: 72px;">
    <div class="docs-container">
        
        <div class="docs-layout-row">
            
            <!-- 1. Left Modern Sidebar Navigation (20%) -->
            <div class="docs-col-sidebar category-left-sidebar-col">
                <?php get_template_part('template-parts/docs/sidebar-modern'); ?>
            </div>

            <!-- 2. Main Content Column (60% with Right Sidebar) -->
            <div class="docs-col-content category-main-col has-toc-sidebar">
                
                <!-- Breadcrumbs -->
                <nav aria-label="breadcrumb">
                    <ol class="custom-breadcrumb">
                        <li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
                        <li class="breadcrumb-separator">/</li>
                        <li class="active-crumb" aria-current="page"><?php echo esc_html($term_name); ?></li>
                    </ol>
                </nav>

                <!-- Category Header -->
                <h1 class="single-doc-title" style="font-size: 38px; font-weight: 700; color: #111827; line-height: 1.2; margin: 12px 0 14px 0;">
                    <?php echo esc_html($term_name); ?>
                </h1>

                <!-- Meta Line (Author & Articles Count) -->
                <div class="doc-author-meta-box" style="display: flex; align-items: center; gap: 8px; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid #e5e7eb;">
                    <div class="author-avatar" style="width: 20px; height: 20px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L3 12.5V21h8.5z" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16 8L2 22" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M17.5 15H9" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="author-info" style="font-size: 14px; color: #6b7280; line-height: 1.4;">
                        By &nbsp;<span style="color: #374151; font-weight: 600;">CMGalaxy</span> &nbsp;&middot;&nbsp; <?php echo intval($articles_count); ?> articles
                    </div>
                </div>

                <!-- Articles & Subcategories List -->
                <?php get_template_part('template-parts/docs/content-category-detail'); ?>

            </div>

            <!-- 3. Right Sidebar Sticky CTA Column (20%) -->
            <div class="docs-col-toc category-right-sidebar-col doc-sidebar">
                <div class="cat-3-right-sidebar" style="position: sticky; top: 90px;">
                    <div class="sidebar-widget">
                        <div class="random-image-container">
                            <a href="https://cmgalaxy.com/book-a-demo" target="_blank" rel="noopener noreferrer">
                                <img src="<?php echo esc_url($sidebar_img_url); ?>" alt="Book a Demo - CMGalaxy" style="width: 100%; height: auto; border-radius: 12px; display: block; box-shadow: 0 4px 16px rgba(0,0,0,0.06);" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<?php
get_footer();
