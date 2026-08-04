<?php
/**
 * The template for displaying the footer.
 *
 * Renders the ElementsKit global footer template (ID 557) site-wide.
 * Falls back to the theme's default footer if Elementor is not active.
 *
 * @package HelloElementor
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * ElementsKit Global Footer Template ID.
 * This is the post ID of the "global-footer" ElementsKit template.
 * To find it: ElementsKit > Header & Footer > locate your footer template and note the ID.
 */
$elementskit_footer_id = 557;

$footer_rendered = false;

// Render the ElementsKit footer template if Elementor frontend is available.
if ( did_action( 'elementor/loaded' ) && class_exists( '\Elementor\Plugin' ) ) {
	$elementor_instance = \Elementor\Plugin::instance();
	if ( isset( $elementor_instance->frontend ) && $elementskit_footer_id ) {
		$footer_content = $elementor_instance->frontend->get_builder_content_for_display( $elementskit_footer_id, true );
		if ( ! empty( $footer_content ) ) {
			echo '<footer id="site-footer" class="site-footer elementskit-footer">';
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor renders trusted builder content.
			echo $footer_content;
			echo '</footer>';
			$footer_rendered = true;
		}
	}
}

// Fallback: use Elementor Pro theme location or the theme's built-in footer.
if ( ! $footer_rendered ) {
	if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) {
		if ( hello_elementor_display_header_footer() ) {
			if ( did_action( 'elementor/loaded' ) && function_exists( 'hello_header_footer_experiment_active' ) && hello_header_footer_experiment_active() ) {
				get_template_part( 'template-parts/dynamic-footer' );
			} else {
				get_template_part( 'template-parts/footer' );
			}
		}
	}
}
?>

<?php wp_footer(); ?>

</body>
</html>
