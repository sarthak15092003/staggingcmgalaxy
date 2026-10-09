<?php
/**
 * Single Doc Template for 'docs' Custom Post Type
 *
 * @package HelloElementor
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

get_header();

$current_doc_id = get_the_ID();
$terms = get_the_terms($current_doc_id, 'doc_category');
if (!$terms || is_wp_error($terms)) {
    $terms = get_the_terms($current_doc_id, 'category');
}
$first_term = ($terms && !is_wp_error($terms)) ? $terms[0] : null;
$docs_home_url = get_post_type_archive_link('docs') ? get_post_type_archive_link('docs') : home_url('/docs/');
?>

<div class="docs-main-wrapper" style="padding-top: 28px; padding-bottom: 72px;">
    <div class="docs-container">
        
        <div class="docs-layout-row">
            
            <!-- Left Modern Sidebar Navigation -->
            <div class="docs-col-sidebar category-left-sidebar-col">
                <?php get_template_part('template-parts/docs/sidebar-modern'); ?>
            </div>

            <!-- Main Doc Content Column -->
            <div class="docs-col-content">
                
                <!-- Breadcrumbs -->
                <nav aria-label="breadcrumb">
                    <ol class="custom-breadcrumb">
                        <li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
                        <li class="breadcrumb-separator">/</li>
                        <li><a href="<?php echo esc_url($docs_home_url); ?>">Docs</a></li>
                        <?php if ($first_term) : 
                            $term_link = get_term_link($first_term);
                            $term_link = is_wp_error($term_link) ? '#' : $term_link;
                        ?>
                            <li class="breadcrumb-separator">/</li>
                            <li><a href="<?php echo esc_url($term_link); ?>"><?php echo esc_html($first_term->name); ?></a></li>
                        <?php endif; ?>
                        <li class="breadcrumb-separator">/</li>
                        <li class="active-crumb" aria-current="page"><?php echo esc_html(wp_trim_words(get_the_title(), 6, '...')); ?></li>
                    </ol>
                </nav>

                <?php while (have_posts()) : the_post(); ?>
                    
                    <article id="post-<?php the_ID(); ?>" <?php post_class('doc-article-item'); ?>>
                        
                        <h1 class="single-doc-title"><?php the_title(); ?></h1>
                        
                        <!-- Author & Meta Box -->
                        <div class="doc-author-meta-box">
                            <div class="author-avatar">
                                <img src="https://docs.cmgalaxy.com/wp-content/uploads/2026/06/cropped-Group-1000004539-300x300-1.png" alt="CMGalaxy Logo" />
                            </div>
                            <div class="author-info">
                                Written by <strong style="color: #334155;"><?php echo get_the_author(); ?></strong> &bull; Updated <?php echo get_the_modified_date('M j, Y'); ?>
                            </div>
                        </div>

                        <!-- Featured Image if exists -->
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="doc-featured-image" style="margin-bottom: 24px;">
                                <?php the_post_thumbnail('large', array('style' => 'border-radius: 8px; max-width: 100%; height: auto;')); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Doc Body Content -->
                        <div class="doc-article-content">
                            <?php the_content(); ?>
                        </div>

                        <!-- Tags -->
                        <?php
                        $doc_tags = get_the_terms(get_the_ID(), 'doc_tag');
                        if (!$doc_tags || is_wp_error($doc_tags)) {
                            $doc_tags = get_the_tags();
                        }
                        if ($doc_tags && !is_wp_error($doc_tags)) : ?>
                            <div style="margin: 28px 0; display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                                <span style="font-size: 13.5px; font-weight: 600; color: #64748b;">Tags:</span>
                                <?php foreach ($doc_tags as $dtag) : ?>
                                    <span style="background: #f1f5f9; padding: 3px 10px; border-radius: 999px; font-size: 12.5px; color: #475569;">#<?php echo esc_html($dtag->name); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Feedback Box -->
                        <div class="cm-feedback-wrapper">
                            <div class="cm-feedback-top">
                                <h4 class="cm-feedback-title">Did this answer your question?</h4>
                                <div class="cm-feedback-buttons">
                                    <button type="button" class="cm-btn-vote" data-vote="like" data-post-id="<?php the_ID(); ?>">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path>
                                        </svg>
                                        Yes
                                    </button>
                                    <button type="button" class="cm-btn-vote" data-vote="dislike" data-post-id="<?php the_ID(); ?>">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M10 15v4a3 3 0 0 0 3 3l4-9V2H5.72a2 2 0 0 0-2 1.7l-1.38 9a2 2 0 0 0 2 2.3zm7-13h3a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2h-3"></path>
                                        </svg>
                                        No
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Related Articles -->
                        <?php
                        if ($first_term) {
                            $related_query = new WP_Query(array(
                                'post_type'      => array('docs', 'post'),
                                'tax_query'      => array(
                                    array(
                                        'taxonomy' => $first_term->taxonomy,
                                        'field'    => 'term_id',
                                        'terms'    => $first_term->term_id
                                    )
                                ),
                                'post__not_in'   => array(get_the_ID()),
                                'posts_per_page' => 4,
                            ));

                            if ($related_query->have_posts()) : ?>
                                <div style="margin-top: 40px; padding: 24px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px;">
                                    <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin: 0 0 16px 0;">Related Guides & Articles</h3>
                                    <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 10px;">
                                        <?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
                                            <li>
                                                <a href="<?php the_permalink(); ?>" style="color: #3b82f6; text-decoration: none; font-size: 14.5px; font-weight: 500; display: flex; align-items: center; justify-content: space-between;">
                                                    <span><?php the_title(); ?></span>
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 17L17 7M17 7H9M17 7V15"/></svg>
                                                </a>
                                            </li>
                                        <?php endwhile; wp_reset_postdata(); ?>
                                    </ul>
                                </div>
                            <?php endif;
                        }
                        ?>

                    </article>

                <?php endwhile; ?>

            </div>

            <!-- Right TOC Column (Sticky) -->
            <div class="docs-col-toc">
                <div class="doc-toc-wrap">
                    <div class="doc-toc-title">On this page</div>
                    <nav id="docy-toc"></nav>
                </div>
            </div>

        </div>

    </div>
</div>

<?php
get_footer();
