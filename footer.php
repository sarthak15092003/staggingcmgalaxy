<?php
/**
 * The template for displaying the footer.
 *
 * Renders the ElementsKit global footer template (ID 557) site-wide
 * using ElementsKit's own shortcode renderer for proper layout context.
 * Falls back to the theme's default footer if ElementsKit/Elementor is not active.
 *
 * @package HelloElementor
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * ElementsKit Global Footer Template ID.
 * This is the post ID of the "global-footer" ElementsKit template.
 * To verify: ElementsKit > Header & Footer > your footer row > check the post ID.
 */
$elementskit_footer_id = 557;

$footer_rendered = false;

// Method 1: Use ElementsKit's own shortcode — the correct and fully supported way.
// This ensures ElementsKit loads its own layout, styles, and scripts properly.
if ( shortcode_exists( 'elementskit_template' ) && $elementskit_footer_id ) {
	$footer_content = do_shortcode( '[elementskit_template id="' . absint( $elementskit_footer_id ) . '"]' );
	if ( ! empty( trim( $footer_content ) ) ) {
		echo '<footer id="site-footer" class="site-footer elementskit-footer">';
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- ElementsKit shortcode renders trusted builder content.
		echo $footer_content;
		echo '</footer>';
		$footer_rendered = true;
	}
}

// Method 2: Fallback — use Elementor's raw frontend renderer if shortcode isn't registered yet.
if ( ! $footer_rendered && did_action( 'elementor/loaded' ) && class_exists( '\Elementor\Plugin' ) ) {
	$elementor_instance = \Elementor\Plugin::instance();
	if ( isset( $elementor_instance->frontend ) ) {
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

// Method 3: Ultimate fallback — use Elementor Pro theme location or the theme's built-in footer.
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
