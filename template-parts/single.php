<?php
/**
 * The template for displaying singular post-types: posts, pages and user-defined custom post types.
 *
 * @package HelloElementor
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

while ( have_posts() ) :
	the_post();
	?>

<main id="content" <?php post_class( 'site-main' ); ?>>

	<?php if ( is_singular( 'post' ) ) : ?>
		<article class="cmg-blog-single-article">
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="cmg-blog-featured-image-wrap" style="max-width: 1100px; margin: 30px auto 10px auto; padding: 0 20px; box-sizing: border-box;">
					<?php the_post_thumbnail( 'full', array( 'style' => 'width: 100%; height: auto; border-radius: 20px; display: block; object-fit: cover;' ) ); ?>
				</div>
			<?php endif; ?>

			<?php
			if ( function_exists( 'cmg_render_blog_header' ) ) {
				echo cmg_render_blog_header();
			}
			?>

			<div class="cmg-blog-layout-wrapper">
				<?php
				if ( function_exists( 'cmg_render_blog_toc_sidebar' ) ) {
					echo cmg_render_blog_toc_sidebar();
				}
				?>

				<div class="page-content cmg-blog-content" style="flex: 1 1 0%; min-width: 0; max-width: 880px; font-size: 18px; line-height: 1.75; color: #374151; box-sizing: border-box;">
					<?php the_content(); ?>

					<?php wp_link_pages(); ?>

					<?php if ( has_tag() ) : ?>
					<div class="post-tags" style="margin-top: 30px;">
						<?php the_tags( '<span class="tag-links">' . esc_html__( 'Tagged ', 'hello-elementor' ), ', ', '</span>' ); ?>
					</div>
					<?php endif; ?>

					<?php
					if ( function_exists( 'cmg_render_blog_bottom_review_share' ) ) {
						echo cmg_render_blog_bottom_review_share();
					}
					?>
				</div>

				<?php
				/* Side CTA banner - 3rd flex column (right sticky sidebar) */
				if ( function_exists( 'cmg_render_floating_side_banner' ) ) {
					echo cmg_render_floating_side_banner();
				}
				?>
			</div>

			<?php
			if ( function_exists( 'cmg_render_blog_author_bio' ) ) {
				echo cmg_render_blog_author_bio();
			}
			if ( function_exists( 'cmg_render_blog_bottom_growth_banner' ) ) {
				echo cmg_render_blog_bottom_growth_banner();
			}
			if ( function_exists( 'cmg_render_blog_related_articles' ) ) {
				echo cmg_render_blog_related_articles();
			}
			?>

			
		</article>
	<?php else : ?>
		<?php if ( apply_filters( 'hello_elementor_page_title', true ) ) : ?>
			<div class="page-header">
				<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
			</div>
		<?php endif; ?>

		<div class="page-content">
			<?php the_content(); ?>

			<?php wp_link_pages(); ?>

			<?php if ( has_tag() ) : ?>
			<div class="post-tags">
				<?php the_tags( '<span class="tag-links">' . esc_html__( 'Tagged ', 'hello-elementor' ), ', ', '</span>' ); ?>
			</div>
			<?php endif; ?>
		</div>

		<?php comments_template(); ?>
	<?php endif; ?>

</main>

	<?php
endwhile;

// Build timestamp: 2026-09-16 10:10
