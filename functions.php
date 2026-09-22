<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'HELLO_ELEMENTOR_VERSION', '99.9.9' );

// Prevent WordPress from auto-updating and overwriting this custom theme
add_filter( 'site_transient_update_themes', function( $transient ) {
    if ( isset( $transient->response['hello-elementor'] ) ) {
        unset( $transient->response['hello-elementor'] );
    }
    return $transient;
} );
add_filter( 'auto_update_theme', '__return_false' );
define( 'EHP_THEME_SLUG', 'hello-elementor' );

define( 'HELLO_THEME_PATH', get_template_directory() );
define( 'HELLO_THEME_URL', get_template_directory_uri() );
define( 'HELLO_THEME_ASSETS_PATH', HELLO_THEME_PATH . '/assets/' );
define( 'HELLO_THEME_ASSETS_URL', HELLO_THEME_URL . '/assets/' );
define( 'HELLO_THEME_SCRIPTS_PATH', HELLO_THEME_ASSETS_PATH . 'js/' );
define( 'HELLO_THEME_SCRIPTS_URL', HELLO_THEME_ASSETS_URL . 'js/' );
define( 'HELLO_THEME_STYLE_PATH', HELLO_THEME_ASSETS_PATH . 'css/' );
define( 'HELLO_THEME_STYLE_URL', HELLO_THEME_ASSETS_URL . 'css/' );
define( 'HELLO_THEME_IMAGES_PATH', HELLO_THEME_ASSETS_PATH . 'images/' );
define( 'HELLO_THEME_IMAGES_URL', HELLO_THEME_ASSETS_URL . 'images/' );

if ( ! isset( $content_width ) ) {
	$content_width = 800; // Pixels.
}

if ( ! function_exists( 'hello_elementor_setup' ) ) {
	/**
	 * Set up theme support.
	 *
	 * @return void
	 */
	function hello_elementor_setup() {
		if ( is_admin() ) {
			hello_maybe_update_theme_version_in_db();
		}

		if ( apply_filters( 'hello_elementor_register_menus', true ) ) {
			register_nav_menus( [ 'menu-1' => esc_html__( 'Header', 'hello-elementor' ) ] );
			register_nav_menus( [ 'menu-2' => esc_html__( 'Footer', 'hello-elementor' ) ] );
		}

		if ( apply_filters( 'hello_elementor_post_type_support', true ) ) {
			add_post_type_support( 'page', 'excerpt' );
		}

		if ( apply_filters( 'hello_elementor_add_theme_support', true ) ) {
			add_theme_support( 'post-thumbnails' );
			add_theme_support( 'automatic-feed-links' );
			add_theme_support( 'title-tag' );
			add_theme_support(
				'html5',
				[
					'search-form',
					'comment-form',
					'comment-list',
					'gallery',
					'caption',
					'script',
					'style',
					'navigation-widgets',
				]
			);
			add_theme_support(
				'custom-logo',
				[
					'height'      => 100,
					'width'       => 350,
					'flex-height' => true,
					'flex-width'  => true,
				]
			);
			add_theme_support( 'align-wide' );
			add_theme_support( 'responsive-embeds' );

			/*
			 * Editor Styles
			 */
			add_theme_support( 'editor-styles' );
			add_editor_style( 'assets/css/editor-styles.css' );

			/*
			 * WooCommerce.
			 */
			if ( apply_filters( 'hello_elementor_add_woocommerce_support', true ) ) {
				// WooCommerce in general.
				add_theme_support( 'woocommerce' );
				// Enabling WooCommerce product gallery features (are off by default since WC 3.0.0).
				// zoom.
				add_theme_support( 'wc-product-gallery-zoom' );
				// lightbox.
				add_theme_support( 'wc-product-gallery-lightbox' );
				// swipe.
				add_theme_support( 'wc-product-gallery-slider' );
			}
		}
	}
}
add_action( 'after_setup_theme', 'hello_elementor_setup' );

function hello_maybe_update_theme_version_in_db() {
	$theme_version_option_name = 'hello_theme_version';
	// The theme version saved in the database.
	$hello_theme_db_version = get_option( $theme_version_option_name );

	// If the 'hello_theme_version' option does not exist in the DB, or the version needs to be updated, do the update.
	if ( ! $hello_theme_db_version || version_compare( $hello_theme_db_version, HELLO_ELEMENTOR_VERSION, '<' ) ) {
		update_option( $theme_version_option_name, HELLO_ELEMENTOR_VERSION );
	}
}

if ( ! function_exists( 'hello_elementor_display_header_footer' ) ) {
	/**
	 * Check whether to display header footer.
	 *
	 * @return bool
	 */
	function hello_elementor_display_header_footer() {
		$hello_elementor_header_footer = true;

		return apply_filters( 'hello_elementor_header_footer', $hello_elementor_header_footer );
	}
}

if ( ! function_exists( 'hello_elementor_scripts_styles' ) ) {
	/**
	 * Theme Scripts & Styles.
	 *
	 * @return void
	 */
	function hello_elementor_scripts_styles() {
		if ( apply_filters( 'hello_elementor_enqueue_style', true ) ) {
			wp_enqueue_style(
				'hello-elementor',
				HELLO_THEME_STYLE_URL . 'reset.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		if ( apply_filters( 'hello_elementor_enqueue_theme_style', true ) ) {
			wp_enqueue_style(
				'hello-elementor-theme-style',
				HELLO_THEME_STYLE_URL . 'theme.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		if ( hello_elementor_display_header_footer() ) {
			wp_enqueue_style(
				'hello-elementor-header-footer',
				HELLO_THEME_STYLE_URL . 'header-footer.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		wp_enqueue_style(
			'hello-elementor-main',
			HELLO_THEME_STYLE_URL . 'main.css',
			[ 'hello-elementor' ],
			filemtime( HELLO_THEME_STYLE_PATH . 'main.css' )
		);

		wp_enqueue_style(
			'cmg-google-font-onest',
			'https://fonts.googleapis.com/css2?family=Onest:wght@300;400;500;600;700;800;900&display=swap',
			[],
			null
		);

		// Enqueue Font Awesome for custom HTML icons (using v4 for backward compatibility with 'fa' classes)
		wp_enqueue_style(
			'font-awesome',
			'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css',
			[],
			'4.7.0'
		);
	}
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_scripts_styles' );

/* Enqueue Google Font Onest & define --primary-font */
add_action( 'wp_head', function() {
    ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Onest:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style id="cmg-primary-font-onest">
      :root {
        --primary-font: 'Onest', sans-serif;
        --e-global-typography-primary-font-family: 'Onest', sans-serif !important;
        --e-global-typography-secondary-font-family: 'Onest', sans-serif !important;
        --e-global-typography-text-font-family: 'Onest', sans-serif !important;
        --e-global-typography-accent-font-family: 'Onest', sans-serif !important;
      }
      body.single-post,
      body.single-post .site-main,
      body.single-post .cmg-blog-single-article,
      body.single-post .cmg-blog-header-wrapper,
      body.single-post .cmg-blog-content,
      body.single-post .cmg-blog-content p,
      body.single-post .cmg-blog-content h1,
      body.single-post .cmg-blog-content h2,
      body.single-post .cmg-blog-content h3,
      body.single-post .cmg-blog-content h4,
      body.single-post .cmg-blog-content h5,
      body.single-post .cmg-blog-content h6,
      body.single-post .cmg-blog-content li,
      body.single-post .cmg-blog-content span,
      body.single-post .cmg-blog-content a,
      body.single-post .cmg-blog-content blockquote,
      body.single-post .cmg-blog-content table,
      body.single-post .cmg-blog-content th,
      body.single-post .cmg-blog-content td,
      .cmg-blog-toc-sidebar,
      .cmg-floating-side-banner,
      .cmg-audit-banner-wrapper,
      .cmg-docy-toc-fab,
      .cmg-docy-sheet,
      .cmg-author-bio-container,
      .cmg-growth-banner-container,
      .cmg-related-articles-section {
        font-family: var(--primary-font, 'Onest', sans-serif) !important;
      }

      /* Article page only: H3 margin-top 20px */
      body.single-post .cmg-blog-content h3,
      body.single-post .cmg-blog-content h3.wp-block-heading,
      body.single-post .page-content.cmg-blog-content h3,
      body.single-post article.cmg-blog-single-article .cmg-blog-content h3,
      body.single-post article.cmg-blog-single-article .page-content h3,
      body.single-post h3.wp-block-heading {
        margin-top: 20px !important;
      }

      /* ==========================================================================
         ALL ARTICLE IMAGES BORDER RADIUS 20PX
         ========================================================================== */
      body.single-post .cmg-blog-featured-image-wrap img,
      body.single-post .cmg-blog-content img,
      body.single-post .cmg-blog-content figure img,
      body.single-post .cmg-blog-content .wp-block-image img,
      body.single-post .page-content img,
      body.single-post .entry-content img,
      body.single-post .cmg-related-thumb-wrap,
      body.single-post .cmg-related-thumb,
      body.single-post .cmg-side-banner-img {
        border-radius: 20px !important;
      }
      body.single-post .cmg-blog-featured-image-wrap,
      body.single-post .cmg-blog-content figure,
      body.single-post .cmg-blog-content .wp-block-image,
      body.single-post .cmg-related-thumb-wrap,
      body.single-post .cmg-side-banner-link {
        border-radius: 20px !important;
        overflow: hidden !important;
      }
      body.single-post .cmg-blog-author-avatar,
      body.single-post .cmg-blog-author-avatar img,
      body.single-post .cmg-author-avatar-img,
      body.single-post .cmg-author-avatar-badge,
      body.single-post .nav-rating-avatar,
      body.single-post .cmg-author-badge-avatar {
        border-radius: 50% !important;
      }
      
      /* Eradicate red #c36 on all buttons and links */
      [type="button"], [type="submit"], button {
        border-color: transparent !important;
      }
      [type="button"]:focus, [type="button"]:hover, [type="button"]:active,
      [type="submit"]:focus, [type="submit"]:hover, [type="submit"]:active,
      button:focus, button:hover, button:active {
        background-color: transparent;
        color: inherit;
        outline: none !important;
        box-shadow: none !important;
      }
      .cmg-toc-mobile-toggle-btn,
      .cmg-toc-mobile-toggle-btn:focus,
      .cmg-toc-mobile-toggle-btn:active,
      .cmg-blog-toc-header:hover .cmg-toc-mobile-toggle-btn,
      .cmg-blog-toc-header:focus .cmg-toc-mobile-toggle-btn {
              display: none !important;
            }
      .cmg-toc-mobile-toggle-btn:hover {
        background: #e2e8f0 !important;
        background-color: #e2e8f0 !important;
        color: #0f172a !important;
      }
      /* In-content text links only: keep header, nav, TOC, buttons and footer clean */
      .cmg-blog-content p a:not(.btn):not(.button):not(.cmg-share-btn),
      .cmg-blog-content li a:not(.btn):not(.button):not(.cmg-share-btn) {
        color: #2563eb !important;
        text-decoration: underline !important;
        text-decoration-color: rgba(37, 99, 235, 0.4) !important;
      }
      .cmg-blog-content p a:not(.btn):not(.button):not(.cmg-share-btn):hover,
      .cmg-blog-content li a:not(.btn):not(.button):not(.cmg-share-btn):hover {
        color: #1d4ed8 !important;
        text-decoration-color: #1d4ed8 !important;
      }
      /* Remove red tap / active highlight on click */
      *, *::before, *::after {
        -webkit-tap-highlight-color: transparent !important;
      }
      a:active,
      a:focus,
      a:focus-visible,
      button:active,
      button:focus,
      button:focus-visible {
        outline: none !important;
        -webkit-tap-highlight-color: transparent !important;
      }
      .cmg-blog-toc-sidebar *,
      .cmg-blog-toc-header,
      .cmg-toc-mobile-toggle-btn,
      .cmg-blog-toc-link,
      .cmg-docy-toc-fab,
      .cmg-docy-sheet,
      .cmg-docy-sheet-link {
        -webkit-tap-highlight-color: transparent !important;
        outline: none !important;
      }
      /* Ensure TOC links are ALWAYS black and bold when active/focused/visited */
      body.single-post .cmg-blog-toc-sidebar a.cmg-blog-toc-link,
      body.single-post .cmg-blog-toc-sidebar .cmg-blog-toc-link,
      body.single-post a.cmg-blog-toc-link {
        color: #475569 !important;
        font-weight: 400;
        text-decoration: none !important;
        background: transparent !important;
        border: none !important;
        outline: none !important;
      }
      body.single-post .cmg-blog-toc-sidebar a.cmg-blog-toc-link:hover,
      body.single-post a.cmg-blog-toc-link:hover {
        color: #000000 !important;
        background: transparent !important;
        border: none !important;
      }
      body.single-post .cmg-blog-toc-sidebar a.cmg-blog-toc-link:active,
      body.single-post .cmg-blog-toc-sidebar a.cmg-blog-toc-link:focus,
      body.single-post a.cmg-blog-toc-link:active,
      body.single-post a.cmg-blog-toc-link:focus,
      body.single-post .cmg-blog-toc-sidebar a.cmg-blog-toc-link:visited {
        color: #000000 !important;
        background: transparent !important;
        border: none !important;
        outline: none !important;
      }
      body.single-post .cmg-blog-toc-sidebar a.cmg-blog-toc-link.is-active,
      body.single-post .cmg-blog-toc-sidebar .cmg-blog-toc-link.is-active,
      body.single-post a.cmg-blog-toc-link.is-active {
        color: #000000 !important;
        font-weight: 700 !important;
        background: transparent !important;
        border: none !important;
      }
      body.single-post .cmg-blog-content,
      body.single-post .cmg-blog-content p,
      body.single-post .cmg-blog-content li,
      body.single-post .cmg-blog-content span,
      body.single-post .cmg-blog-content blockquote,
      body.single-post .cmg-blog-content td,
      body.single-post .cmg-blog-content th {
        font-size: 16px !important;
        line-height: 1.75 !important;
      }
    </style>
    <?php
}, 1 );


if ( ! function_exists( 'hello_elementor_register_elementor_locations' ) ) {
	/**
	 * Register Elementor Locations.
	 *
	 * @param ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $elementor_theme_manager theme manager.
	 *
	 * @return void
	 */
	function hello_elementor_register_elementor_locations( $elementor_theme_manager ) {
		if ( apply_filters( 'hello_elementor_register_elementor_locations', true ) ) {
			$elementor_theme_manager->register_all_core_location();
		}
	}
}
add_action( 'elementor/theme/register_locations', 'hello_elementor_register_elementor_locations' );

if ( ! function_exists( 'hello_elementor_content_width' ) ) {
	/**
	 * Set default content width.
	 *
	 * @return void
	 */
	function hello_elementor_content_width() {
		$GLOBALS['content_width'] = apply_filters( 'hello_elementor_content_width', 800 );
	}
}
add_action( 'after_setup_theme', 'hello_elementor_content_width', 0 );

if ( ! function_exists( 'hello_elementor_add_description_meta_tag' ) ) {
	/**
	 * Add description meta tag with excerpt text.
	 *
	 * @return void
	 */
	function hello_elementor_add_description_meta_tag() {
		if ( ! apply_filters( 'hello_elementor_description_meta_tag', true ) ) {
			return;
		}

		if ( ! is_singular() ) {
			return;
		}

		$post = get_queried_object();
		if ( empty( $post->post_excerpt ) ) {
			return;
		}

		echo '<meta name="description" content="' . esc_attr( wp_strip_all_tags( $post->post_excerpt ) ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'hello_elementor_add_description_meta_tag' );

// Settings page
require get_template_directory() . '/includes/settings-functions.php';

// Header & footer styling option, inside Elementor
require get_template_directory() . '/includes/elementor-functions.php';

if ( ! function_exists( 'hello_elementor_customizer' ) ) {
	// Customizer controls
	function hello_elementor_customizer() {
		if ( ! is_customize_preview() ) {
			return;
		}

		if ( ! hello_elementor_display_header_footer() ) {
			return;
		}

		require get_template_directory() . '/includes/customizer-functions.php';
	}
}
add_action( 'init', 'hello_elementor_customizer' );

if ( ! function_exists( 'hello_elementor_check_hide_title' ) ) {
	/**
	 * Check whether to display the page title.
	 *
	 * @param bool $val default value.
	 *
	 * @return bool
	 */
	function hello_elementor_check_hide_title( $val ) {
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			$current_doc = Elementor\Plugin::instance()->documents->get( get_the_ID() );
			if ( $current_doc && 'yes' === $current_doc->get_settings( 'hide_title' ) ) {
				$val = false;
			}
		}
		return $val;
	}
}
add_filter( 'hello_elementor_page_title', 'hello_elementor_check_hide_title' );

/**
 * BC:
 * In v2.7.0 the theme removed the `hello_elementor_body_open()` from `header.php` replacing it with `wp_body_open()`.
 * The following code prevents fatal errors in child themes that still use this function.
 */
if ( ! function_exists( 'hello_elementor_body_open' ) ) {
	function hello_elementor_body_open() {
		wp_body_open();
	}
}

require HELLO_THEME_PATH . '/theme.php';

HelloTheme\Theme::instance();


/**
 * CMGalaxy Lead Form ("Book A Demo") WordPress Shortcode
 * 
 * Usage:
 * [cmg_lead_form] or [book_a_demo_form]
 * [cmg_lead_form amplitude_api_key="YOUR_AMPLITUDE_API_KEY"]
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Server-Side AJAX Handler for CMGalaxy Lead Submissions
 */
if ( ! function_exists( 'cmg_handle_lead_submission' ) ) {
function cmg_handle_lead_submission() {
    $raw_input = file_get_contents( 'php://input' );
    $data = json_decode( $raw_input, true );

    if ( empty( $data ) || empty( $data['full_name'] ) || empty( $data['email_address'] ) ) {
        wp_send_json_error( array( 'message' => 'Missing required fields' ), 400 );
    }

    $payload = array(
        'full_name'     => sanitize_text_field( $data['full_name'] ),
        'email_address' => sanitize_email( $data['email_address'] ),
        'phone_number'  => sanitize_text_field( $data['phone_number'] ),
        'company_name'  => sanitize_text_field( $data['company_name'] ),
        'ad_spend'      => sanitize_text_field( $data['ad_spend'] ),
        'website'       => esc_url_raw( $data['website'] ),
        'utm_source'    => sanitize_text_field( isset( $data['utm_source'] ) ? $data['utm_source'] : '' ),
        'utm_medium'    => sanitize_text_field( isset( $data['utm_medium'] ) ? $data['utm_medium'] : '' ),
        'utm_campaign'  => sanitize_text_field( isset( $data['utm_campaign'] ) ? $data['utm_campaign'] : '' ),
        'page_url'      => esc_url_raw( isset( $data['page_url'] ) ? $data['page_url'] : '' ),
    );

    // Primary: Staging API (with fast 4s timeout)
    $response = wp_remote_post( 'https://staging-api.cmgalaxy.com/api/v2/event_emailer/cmgalaxy-enquiry/', array(
        'headers'     => array( 'Content-Type' => 'application/json' ),
        'body'        => json_encode( $payload ),
        'timeout'     => 4,
        'redirection' => 5,
        'sslverify'   => false,
    ) );

    // Fallback: Production API if staging server is down/timing out
    if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) >= 400 ) {
        $response = wp_remote_post( 'https://api.cmgalaxy.com/api/v2/event_emailer/cmgalaxy-enquiry/', array(
            'headers'     => array( 'Content-Type' => 'application/json' ),
            'body'        => json_encode( $payload ),
            'timeout'     => 10,
            'redirection' => 5,
            'sslverify'   => false,
        ) );
    }

    if ( is_wp_error( $response ) ) {
        wp_send_json_error( array( 'message' => $response->get_error_message() ), 500 );
    }

    $code = wp_remote_retrieve_response_code( $response );
    $body = json_decode( wp_remote_retrieve_body( $response ), true );

    if ( $code >= 200 && $code < 300 ) {
        wp_send_json_success( $body ? $body : array( 'message' => 'Enquiry submitted successfully' ) );
    } else {
        $error_msg = isset( $body['message'] ) ? $body['message'] : 'Submission failed';
        wp_send_json_error( array( 'message' => $error_msg ), $code );
    }
}
add_action( 'wp_ajax_cmg_lead_submit', 'cmg_handle_lead_submission' );
add_action( 'wp_ajax_nopriv_cmg_lead_submit', 'cmg_handle_lead_submission' );
}

if ( ! function_exists( 'cmg_lead_form_shortcode' ) ) {
function cmg_lead_form_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'ajax_url'           => admin_url( 'admin-ajax.php?action=cmg_lead_submit' ),
        'redirect_url'       => 'https://www.cmgalaxy.com/thank-you',
        'event_name'         => 'Book A Demo Sendmessage Clicked',
        'section_name'       => 'Book A Demo Form',
        'amplitude_api_key'  => '', // Optional: Pass your Amplitude API Key in shortcode
    ), $atts, 'cmg_lead_form' );

    ob_start();
    ?>
    <!-- INTL-TEL-INPUT & RECAPTCHA SCRIPTS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css">
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <style>
        .iti { 
            display: flex !important; 
            gap: 10px; 
            width: 100% !important; 
            align-items: stretch;
        }
        .iti__flag-container {
            position: relative !important;
            border-radius: 8px !important;
            border: 1px solid #dde3f0 !important;
            background-color: #ffffff !important;
            display: flex;
            align-items: center;
        }
        .iti__selected-flag {
            background-color: transparent !important;
        }
        .iti__selected-flag:hover, .iti__selected-flag:focus {
            background-color: transparent !important;
        }
        #phone-input { 
            flex: 1;
            width: 100% !important; 
            padding-top: 16px !important; 
            padding-bottom: 16px !important; 
            padding-right: 14px !important; 
            padding-left: 14px !important; 
            border-radius: 8px !important; 
            border: 1px solid #dde3f0 !important; 
            font-size: 15px !important; 
            box-sizing: border-box !important;
            background-color: #ffffff !important;
            height: auto !important;
            line-height: normal !important;
        }
        #phone-input:focus {
            border-color: #3a7dff !important;
            box-shadow: 0 0 0 1px rgba(58, 125, 255, 0.08) !important;
        }

        .lead-form-wrapper {
            max-width: 800px;
            margin: 40px auto;
            padding: 40px 48px;
            background: #ffffff;
            border-radius: 28px;
            box-shadow: 0 10px 30px rgba(15, 35, 52, 0.04);
            font-family: system-ui, -apple-system, sans-serif;
            box-sizing: border-box;
        }

        .lead-form-row { margin-bottom: 20px; }
        .lead-form-label { display: block; font-size: 14px; font-weight: 600; color: #1b2230; margin-bottom: 6px; }

        .lead-form-input, .lead-form-select {
            width: 100%;
            border-radius: 8px;
            border: 1px solid #dde3f0;
            padding: 16px 14px;
            font-size: 15px;
            outline: none;
            box-sizing: border-box;
            background-color: #ffffff;
        }

        .lead-form-select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%239aa3b5' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 20px center;
            background-size: 16px;
            padding-right: 50px;
            cursor: pointer;
        }

        .lead-form-input:focus, .lead-form-select:focus {
            border-color: #3a7dff;
            box-shadow: 0 0 0 1px rgba(58, 125, 255, 0.08);
        }

        #phone-input::placeholder { opacity: 0.6; }

        .lead-form-button-wrap { margin-top: 30px; }
        .lead-form-button {
            width: 100%; padding: 16px; border-radius: 999px; border: none;
            font-size: 17px; font-weight: 600; cursor: pointer;
            background: #1ec653; color: #ffffff; transition: background-color 0.2s ease, transform 0.15s ease;
        }
        .lead-form-button:hover { background: #17b047; transform: translateY(-1px); }
        .form-status { margin-top: 10px; font-size: 14px; }
        .form-status.success { color: #1a9c4b; }
        .form-status.error { color: #d63939; }

        @media (max-width: 767px) {
            .lead-form-wrapper { padding: 20px 20px; border-radius: 20px; }
        }
    </style>

    <div class="lead-form-wrapper">
        <form id="lead-form" data-wf-ignore="true" onsubmit="return false;">
            <div class="lead-form-row">
                <label class="lead-form-label" for="full_name">Full Name</label>
                <input id="full_name" type="text" class="lead-form-input" required>
            </div>
            <div class="lead-form-row">
                <label class="lead-form-label" for="email_address">Email Address</label>
                <input id="email_address" type="email" class="lead-form-input" required>
            </div>
            <div class="lead-form-row">
                <label class="lead-form-label" for="phone-input">Phone Number</label>
                <input id="phone-input" type="tel" class="lead-form-input" placeholder="" required pattern="[0-9]*"
                    inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            </div>
            <div class="lead-form-row">
                <label class="lead-form-label" for="company_name">Company Name</label>
                <input id="company_name" type="text" class="lead-form-input" required>
            </div>
            <div class="lead-form-row">
                <label class="lead-form-label" for="ad_spend">Ad Spend</label>
                <select id="ad_spend" class="lead-form-select" required>
                    <option value="">Typical ad spends</option>
                    <option>less than $100k / year</option>
                    <option>between $100k - $1M / year</option>
                    <option>between $1M - $10M / year</option>
                    <option>greater than $10M / year</option>
                </select>
            </div>
            <div class="lead-form-button-wrap">
                <button type="button" id="lead-submit-btn" class="lead-form-button">Send Message</button>
                <div id="form-status" class="form-status"></div>
            </div>
        </form>
    </div>

    <script>
        (function () {
            const AJAX_URL = "<?php echo esc_url( $atts['ajax_url'] ); ?>";
            const REDIRECT_URL = "<?php echo esc_url( $atts['redirect_url'] ); ?>";
            const EVENT_NAME = "<?php echo esc_js( $atts['event_name'] ); ?>";
            const SECTION_NAME = "<?php echo esc_js( $atts['section_name'] ); ?>";
            const AMPLITUDE_API_KEY = "<?php echo esc_js( $atts['amplitude_api_key'] ); ?>";

            const form = document.getElementById("lead-form");
            const statusEl = document.getElementById("form-status");
            const submitBtn = document.getElementById("lead-submit-btn");
            const phoneInput = document.getElementById("phone-input");

            /* Optional: Auto-load Amplitude SDK if API Key provided and not loaded */
            if (AMPLITUDE_API_KEY && (!window.amplitude || !window.amplitude.init)) {
                const s = document.createElement("script");
                s.src = "https://cdn.amplitude.com/libs/analytics-browser-2.11.1-min.js.gz";
                s.async = true;
                s.onload = function() {
                    if (window.amplitude && typeof window.amplitude.init === "function") {
                        window.amplitude.init(AMPLITUDE_API_KEY);
                    }
                };
                document.head.appendChild(s);
            }

            /* Helper function to trigger Amplitude events across all SDK versions */
            function trackAmplitudeEvent(name, props) {
                try {
                    if (window.amplitude) {
                        if (typeof window.amplitude.track === "function") {
                            window.amplitude.track(name, props);
                        } else if (typeof window.amplitude.logEvent === "function") {
                            window.amplitude.logEvent(name, props);
                        } else if (typeof window.amplitude.getInstance === "function") {
                            window.amplitude.getInstance().logEvent(name, props);
                        }
                    }
                } catch (e) {
                    console.warn("Amplitude tracking exception:", e);
                }
            }

            let iti = null;
            if (window.intlTelInput) {
                iti = window.intlTelInput(phoneInput, {
                    initialCountry: "auto",
                    geoIpLookup: function (callback) {
                        fetch("https://ipapi.co/json/")
                            .then(res => res.json())
                            .then(data => callback(data.country_code))
                            .catch(() => callback("in"));
                    },
                    separateDialCode: true,
                    autoPlaceholder: "off",
                    utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js"
                });
            }

            submitBtn.addEventListener("click", async function () {
                statusEl.textContent = "";
                statusEl.className = "form-status";
                phoneInput.style.borderColor = "#dde3f0";

                const fullName = document.getElementById("full_name").value.trim();
                const emailAddress = document.getElementById("email_address").value.trim();
                const phoneNumber = phoneInput.value.trim();
                const companyName = document.getElementById("company_name").value.trim();
                const adSpend = document.getElementById("ad_spend").value;

                if (!fullName) return showError("❌ Please enter your full name.", "full_name");
                document.getElementById("full_name").style.borderColor = "#dde3f0";

                if (!emailAddress) return showError("❌ Please enter your email address.", "email_address");
                document.getElementById("email_address").style.borderColor = "#dde3f0";

                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailAddress)) return showError("❌ Please enter a valid email address.", "email_address");

                if (!phoneNumber) return showError("❌ Please enter your phone number.", "phone-input");

                if (!companyName) return showError("❌ Please enter your company name.", "company_name");
                document.getElementById("company_name").style.borderColor = "#dde3f0";

                if (!adSpend) return showError("❌ Please select your ad spend.", "ad_spend");
                document.getElementById("ad_spend").style.borderColor = "#dde3f0";

                if (iti && !iti.isValidNumber()) {
                    const errorCode = iti.getValidationError();
                    const selectedCountryData = iti.getSelectedCountryData();
                    const fullCountryName = selectedCountryData.name || "selected country";
                    const countryName = fullCountryName.split('(')[0].trim();
                    const iso2 = selectedCountryData.iso2 ? selectedCountryData.iso2.toLowerCase() : '';

                    const phoneLengths = {
                        'af': 9, 'al': 9, 'dz': 9, 'ad': 6, 'ao': 9, 'ar': 10, 'am': 8, 'au': 9, 'at': 10, 'az': 9,
                        'bh': 8, 'bd': 10, 'by': 9, 'be': 9, 'bz': 7, 'bo': 8, 'ba': 8, 'bw': 8, 'br': 11, 'bn': 7,
                        'bg': 9, 'bf': 8, 'kh': 9, 'cm': 9, 'ca': 10, 'cl': 9, 'cn': 11, 'co': 10, 'cr': 8, 'hr': 9,
                        'cy': 8, 'cz': 9, 'dk': 8, 'ec': 9, 'eg': 10, 'sv': 8, 'ee': 8, 'et': 9, 'fi': 9, 'fr': 9,
                        'ge': 9, 'de': 10, 'gh': 9, 'gr': 10, 'gt': 8, 'hn': 8, 'hk': 8, 'hu': 9, 'is': 7, 'in': 10,
                        'id': 11, 'ir': 10, 'iq': 10, 'ie': 9, 'il': 9, 'it': 10, 'jm': 10, 'jp': 10, 'jo': 9, 'kz': 10,
                        'ke': 9, 'kw': 8, 'kg': 9, 'la': 9, 'lv': 8, 'lb': 8, 'ly': 9, 'lt': 8, 'lu': 9, 'mo': 8,
                        'mk': 8, 'my': 9, 'mv': 7, 'ml': 8, 'mt': 8, 'mx': 10, 'md': 8, 'mn': 8, 'me': 8, 'ma': 9,
                        'mz': 9, 'mm': 9, 'na': 9, 'np': 10, 'nl': 9, 'nz': 9, 'ni': 8, 'ng': 10, 'no': 8, 'om': 8,
                        'pk': 10, 'pa': 8, 'py': 9, 'pe': 9, 'ph': 10, 'pl': 9, 'pt': 9, 'qa': 8, 'ro': 10, 'ru': 10,
                        'sa': 9, 'sn': 9, 'rs': 9, 'sg': 8, 'sk': 9, 'si': 9, 'za': 9, 'kr': 10, 'es': 9, 'lk': 9,
                        'sd': 9, 'se': 9, 'ch': 9, 'sy': 9, 'tw': 9, 'tj': 9, 'tz': 9, 'th': 9, 'tn': 8, 'tr': 10,
                        'tm': 8, 'ug': 9, 'ua': 9, 'ae': 9, 'gb': 10, 'us': 10, 'uy': 8, 'uz': 9, 've': 10, 'vn': 9,
                        'ye': 9, 'zm': 9, 'zw': 9
                    };

                    let expectedLength = phoneLengths[iso2] || null;
                    let errorMessage = "Invalid phone number";

                    if (errorCode === 1) errorMessage = "Invalid country code";
                    else if (errorCode === 2) errorMessage = expectedLength ? `Number too short for ${countryName}. Expected ${expectedLength} digits.` : `Number too short for ${countryName}.`;
                    else if (errorCode === 3) errorMessage = expectedLength ? `Number too long for ${countryName}. Expected ${expectedLength} digits.` : `Number too long for ${countryName}.`;
                    else errorMessage = expectedLength ? `Invalid phone number for ${countryName}. Expected ${expectedLength} digits.` : `Invalid phone number for ${countryName}.`;

                    return showError("❌ " + errorMessage, "phone-input");
                }

                let utmData = {};
                try {
                    const stored = localStorage.getItem("utm_data");
                    if (stored) utmData = JSON.parse(stored);
                } catch (e) { utmData = {}; }

                const payload = {
                    full_name: fullName,
                    email_address: emailAddress,
                    phone_number: iti ? iti.getNumber() : phoneNumber,
                    company_name: companyName,
                    ad_spend: document.getElementById("ad_spend").selectedOptions[0].textContent,
                    website: window.location.hostname,
                    utm_source: utmData.utm_source || "",
                    utm_medium: utmData.utm_medium || "",
                    utm_campaign: utmData.utm_campaign || "",
                    page_url: window.location.href
                };

                submitBtn.disabled = true;
                submitBtn.textContent = "Sending...";

                try {
                    const response = await fetch(AJAX_URL, {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify(payload)
                    });

                    const resData = await response.json();

                    if (!response.ok || !resData.success) {
                        throw new Error(resData.data && resData.data.message ? resData.data.message : "API failed");
                    }

                    /* Track Amplitude Event on Success */
                    trackAmplitudeEvent(EVENT_NAME, {
                        initiated_at: document.title || window.location.pathname,
                        section_at: SECTION_NAME,
                        full_name: fullName,
                        email_address: emailAddress,
                        company_name: companyName,
                        ad_spend: payload.ad_spend
                    });

                    localStorage.removeItem('utm_data');
                    statusEl.textContent = "✅ Form submitted successfully!";
                    statusEl.className = "form-status success";
                    form.reset();

                    if (REDIRECT_URL) {
                        setTimeout(() => { window.location.href = REDIRECT_URL; }, 1000);
                    }

                } catch (err) {
                    console.error(err);
                    statusEl.textContent = "❌ Submission failed. Please try again.";
                    statusEl.className = "form-status error";
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.textContent = "Send Message";
                }
            });

            function showError(msg, fieldId) {
                statusEl.textContent = msg;
                statusEl.className = "form-status error";
                const target = document.getElementById(fieldId);
                if (target) {
                    target.style.borderColor = "#d63939";
                    target.focus();
                }
            }
        })();
    </script>
    <?php
    return ob_get_clean();
}
}
add_shortcode( 'cmg_lead_form', 'cmg_lead_form_shortcode' );
add_shortcode( 'book_a_demo_form', 'cmg_lead_form_shortcode' );


/* ==========================================================================
   CMG SITE-WIDE TRACKING & AMPLITUDE ANALYTICS INTEGRATION
   ========================================================================== */

if (!function_exists('cmg_site_wide_analytics_head')) {
    function cmg_site_wide_analytics_head() {
        ?>
        <!-- Domain Verifications -->
        <meta name="p:domain_verify" content="5534472da58e08f50a0a9db68c170f9c"/>
        <meta name="facebook-domain-verification" content="q94uy9j71fnua1bu0mdk3seuk12az3" />

        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-PQ89VXN4');</script>

        <!-- Meta Pixel Code -->
        <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '1267004614826386');
        fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=1267004614826386&ev=PageView&noscript=1"/></noscript>

        <!-- MS Clarity -->
        <script type="text/javascript">
            (function(c,l,a,r,i,t,y){
                c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
                t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
                y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
            })(window, document, "clarity", "script", "sfpdpddt3k");
        </script>

        <!-- Amplitude Browser SDK & Session Replay -->
        <script src="https://cdn.amplitude.com/libs/analytics-browser-2.11.1-min.js.gz"></script>
        <script src="https://cdn.amplitude.com/libs/plugin-session-replay-browser-1.8.0-min.js.gz"></script>
        <script>
          document.addEventListener("DOMContentLoaded", function() {
            if (window.amplitude && window.sessionReplay) {
              try {
                const replay = window.sessionReplay.plugin({
                  sampleRate: 1,
                  maskAllInputs: false,
                  blockAllMedia: false
                });
                window.amplitude.add(replay);
              } catch(e) { console.warn("Amplitude Session Replay warning:", e); }

              window.amplitude.init(
                "3abb5a02f6d1968cb3a120c0bf9b94bb",
                {
                  instanceName: "default",
                  autocapture: { elementInteractions: false },
                  flushQueueSize: 1,
                  flushIntervalMillis: 1000,
                  defaultTracking: {
                    sessions: true,
                    pageViews: true,
                    formInteractions: true,
                    fileDownloads: true
                  }
                }
              );
            }
          });
        </script>

        <!-- CMGalaxy Pixel Tag -->
        <script src="https://cmg-backend.s3.eu-north-1.amazonaws.com/static/js/cmgalaxy.js" async></script>

        <!-- Finsweet Cookie Consent -->
        <script async src="https://cdn.jsdelivr.net/npm/@finsweet/cookie-consent@1/fs-cc.js" fs-cc-mode="opt-in"></script>

        <style>
          .header-2.fix { z-index: 10; }
          .header-2.fix.is-scrolled { z-index: 10; }
          body.modal-open .header-2.fix,
          body.modal-open .header-2.fix.is-scrolled { z-index: 5 !important; }
        </style>
        <?php
    }
}
add_action('wp_head', 'cmg_site_wide_analytics_head', 1);

if (!function_exists('cmg_site_wide_analytics_footer')) {
    function cmg_site_wide_analytics_footer() {
        ?>
        <!-- GTM Noscript -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PQ89VXN4" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>

        <!-- Cookie Consent Banner -->
        <style>
          .cmg-cookie-banner {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background-color: #fff;
            color: #444;
            padding: 16px 24px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 90%;
            display: none;
            align-items: center;
            gap: 16px;
            z-index: 9999;
            font-family: 'Segoe UI', sans-serif;
          }
          .cmg-cookie-banner.show { display: flex; }
          .cmg-cookie-icon {
            font-size: 24px;
            background-color: #f2f2f2;
            border-radius: 50%;
            padding: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
          }
          .cmg-cookie-text { flex: 1; font-size: 14px; line-height: 1.5; color: #484a61; font-weight: 400; }
          .cmg-cookie-btn { border: none; padding: 10px 18px; border-radius: 24px; cursor: pointer; font-weight: 500; font-size: 14px; transition: all 0.2s ease; }
          .cmg-accept-btn { background-color: #36D462; color: white; }
          .cmg-close-btn { background: none; border: none; font-size: 20px; color: #999; cursor: pointer; margin-left: 8px; }
          @media (max-width: 600px) {
            .cmg-cookie-banner { flex-direction: column; align-items: flex-start; text-align: left; padding: 16px; gap: 12px; left: 50%; transform: translateX(-50%); width: 95%; }
            .cmg-cookie-btn { width: 100%; text-align: center; }
            .cmg-close-btn { position: absolute; top: 8px; right: 8px; margin-left: 0; }
          }
        </style>
        <div class="cmg-cookie-banner" id="cmg-cookie-banner">
          <div class="cmg-cookie-icon">🍪</div>
          <div class="cmg-cookie-text">
            Our website uses cookies. By continuing navigating, we assume your permission to deploy cookies as detailed in our
            <a href="/privacy-policy" style="color: #3A7DFF; text-decoration: underline;">Privacy Policy</a>.
          </div>
          <button class="cmg-cookie-btn cmg-accept-btn" onclick="cmgAcceptCookies()">Accept cookies</button>
          <button class="cmg-close-btn" onclick="cmgDeclineCookies()">×</button>
        </div>

        <script>
          function setCookie(name, value, days) {
            let expires = "";
            if (days) {
              const date = new Date();
              date.setTime(date.getTime() + (days*24*60*60*1000));
              expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + value + expires + "; path=/";
          }

          function getCookie(name) {
            const nameEQ = name + "=";
            const ca = document.cookie.split(';');
            for(let i=0; i < ca.length; i++) {
              let c = ca[i];
              while (c.charAt(0) === ' ') c = c.substring(1);
              if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
          }

          function cmgAcceptCookies() {
            setCookie('cmgCookiesConsent', 'accepted', 365);
            const banner = document.getElementById('cmg-cookie-banner');
            if (banner) banner.classList.remove('show');
          }

          function cmgDeclineCookies() {
            setCookie('cmgCookiesConsent', 'declined', 365);
            const banner = document.getElementById('cmg-cookie-banner');
            if (banner) banner.classList.remove('show');
          }

          window.addEventListener('DOMContentLoaded', function () {
            const banner = document.getElementById('cmg-cookie-banner');
            const consent = getCookie('cmgCookiesConsent');
            if (!consent && banner) {
              banner.classList.add('show');
            }
          });
        </script>

        <!-- UTM Persistence & Page View Event -->
        <script>
        (function () {
          function getParam(name) {
            return new URLSearchParams(window.location.search).get(name);
          }

          function setFirstTouch(name, value) {
            if (!value) return;
            if (!localStorage.getItem("initial_" + name)) {
              localStorage.setItem("initial_" + name, value);
            }
          }

          const params = [
            "utm_source","utm_medium","utm_campaign",
            "utm_content","utm_term","utm_id",
            "twclid","wbraid"
          ];

          params.forEach(param => {
            const value = getParam(param);
            setFirstTouch(param, value);
          });

          function fireEvent() {
            if (!window.amplitude) {
              setTimeout(fireEvent, 300);
              return;
            }

            const eventData = {
              page_name: document.title || window.location.pathname,
              utm_source: getParam("utm_source") || localStorage.getItem("initial_utm_source") || "",
              utm_medium: getParam("utm_medium") || localStorage.getItem("initial_utm_medium") || "",
              utm_campaign: getParam("utm_campaign") || localStorage.getItem("initial_utm_campaign") || "",
              utm_content: getParam("utm_content") || localStorage.getItem("initial_utm_content") || "",
              utm_term: getParam("utm_term") || localStorage.getItem("initial_utm_term") || "",
              utm_id: getParam("utm_id") || localStorage.getItem("initial_utm_id") || "",
              twclid: getParam("twclid") || localStorage.getItem("initial_twclid") || "",
              wbraid: getParam("wbraid") || localStorage.getItem("initial_wbraid") || "",
              initiated_at: document.title || window.location.pathname,
              device_category: (function() {
                const ua = navigator.userAgent;
                if (/tablet|ipad|playbook|silk/i.test(ua)) return "tablet";
                if (/mobile|iphone|ipod|android/i.test(ua)) return "mobile";
                return "desktop";
              })(),
              os_clean: (function() {
                const ua = navigator.userAgent;
                if (/windows nt/i.test(ua)) return "Windows";
                if (/mac os x/i.test(ua) && !/iphone|ipad|ipod/i.test(ua)) return "macOS";
                if (/android/i.test(ua)) return "Android";
                if (/iphone|ipad|ipod/i.test(ua)) return "iOS";
                if (/linux/i.test(ua)) return "Linux";
                return "Unknown";
              })()
            };

            if (typeof window.amplitude.logEvent === 'function') {
              window.amplitude.logEvent("Staging - Website Page Viewed", eventData);
            } else if (typeof window.amplitude.track === 'function') {
              window.amplitude.track("Staging - Website Page Viewed", eventData);
            }

            if (window.amplitude && typeof window.amplitude.flush === 'function') {
              window.amplitude.flush();
            }

            console.log("✅ Amplitude Event Fired: Staging - Website Page Viewed", eventData);
          }

          fireEvent();
        })();
        </script>

        <!-- CTA Click Event Listener & Modal/Navbar Handlers -->
        <script>
        document.addEventListener("DOMContentLoaded", function () {
          const pageName = document.title || window.location.pathname;

          function getDeviceCategory() {
            const ua = navigator.userAgent;
            if (/tablet|ipad|playbook|silk/i.test(ua)) return "tablet";
            if (/mobile|iphone|ipod|android/i.test(ua)) return "mobile";
            return "desktop";
          }

          function getOSClean() {
            const ua = navigator.userAgent;
            if (/windows nt/i.test(ua)) return "Windows";
            if (/mac os x/i.test(ua) && !/iphone|ipad|ipod/i.test(ua)) return "macOS";
            if (/android/i.test(ua)) return "Android";
            if (/iphone|ipad|ipod/i.test(ua)) return "iOS";
            if (/linux/i.test(ua)) return "Linux";
            return "Unknown";
          }

          const deviceCategory = getDeviceCategory();
          const osClean = getOSClean();

          const sectionMap = {
            "btn-book-demo-omnichannel-hero": "Hero Section",
            "btn-start-a-free-trial-omnichannel": "Hero Section",
            "btn-try-cmgalaxy-omnichannel": "Feature List",
            "btn-book-demo-omnichannel": "Feature List",
            "btn-book-demo-omnichannel-footer": "Footer Section",

            "nav-home": "Nav Bar",
            "nav-features": "Nav Bar",
            "nav-omnichannel": "Nav Bar",
            "nav-ai-agent": "Nav Bar",
            "nav-full-funnel": "Nav Bar",
            "nav-integration": "Nav Bar",
            "nav-lex": "Nav Bar",
            "nav-about-us": "Nav Bar",
            "nav-blog": "Nav Bar",
            "nav-sign-in": "Nav Bar",
            "nav-sign-up-free": "Nav Bar",

            "footer-email-input": "Footer Section",
            "footer-email-submit": "Footer Section",
            "footer-omnichannel": "Footer Section",
            "footer-ai-agent": "Footer Section",
            "footer-full-funnel": "Footer Section",
            "footer-integration": "Footer Section",
            "footer-lex": "Footer Section",
            "footer-about-us": "Footer Section",
            "footer-privacy-policy": "Footer Section",
            "footer-terms": "Footer Section",
            "footer-blog": "Footer Section",
            "footer-social-linkedin": "Footer Section",
            "footer-social-facebook": "Footer Section",
            "footer-social-instagram": "Footer Section",
            "footer-social-youtube": "Footer Section",

            "home-start-free-trial": "Home Page Hero Section",
            "home-book-demo": "Home Page Hero Section",
            "home-hero-play-video": "Home Page Hero Section",
            "home-growth-banner-book-demo": "Growth Banner Section",
            "home-growth-banner-try-cmgalaxy": "Growth Banner Section",
            "proven-results-book-demo": "Proven Results Section",
            "proven-results-try-cmgalaxy": "Proven Results Section",
            "home-footer-cta-book-demo": "Footer Section",

            "aboutus-footer-book-demo": "About Us Footer Section",
            "aboutus-footer-try-cmgalaxy": "About Us Footer Section",
            "blog-footer-cta-book-demo": "Blog Footer Section",
            "blog-detail-banner-try-cmgalaxy": "Blog Detail CTA Banner",
            "blog-detail-banner-book-demo": "Blog Detail CTA Banner",
            "blog-detail-footer-book-demo": "Blog Detail Footer Section",

            "audit-hero-start-analysis-desktop": "Hero Section",
            "audit-hero-start-analysis-mobile": "Hero Section"
          };

          const eventNameMap = {
            "btn-book-demo-omnichannel-hero": "Staging - Website Book Demo Clicked",
            "btn-start-a-free-trial-omnichannel": "Staging - Website Start a Free Trial Clicked",
            "btn-try-cmgalaxy-omnichannel": "Staging - Website Try CM Galaxy Clicked",
            "btn-book-demo-omnichannel": "Staging - Website Book Demo Clicked",
            "btn-book-demo-omnichannel-footer": "Staging - Website Book Demo Clicked",

            "nav-home": "Staging - Nav Bar Home Clicked",
            "nav-features": "Staging - Nav Bar Features Clicked",
            "nav-omnichannel": "Staging - Nav Bar Omnichannel Clicked",
            "nav-ai-agent": "Staging - Nav Bar AI Agent Clicked",
            "nav-full-funnel": "Staging - Nav Bar Full Funnel Attribution Clicked",
            "nav-integration": "Staging - Nav Bar Integration Clicked",
            "nav-lex": "Staging - Nav Bar Lex Clicked",
            "nav-about-us": "Staging - Nav Bar About Us Clicked",
            "nav-blog": "Staging - Nav Bar Blog Clicked",
            "nav-sign-in": "Staging - Nav Bar Sign In Clicked",
            "nav-sign-up-free": "Staging - Nav Bar Sign Up Free Clicked",

            "footer-email-input": "Staging - Subscribe Email Entered",
            "footer-email-submit": "Staging - Footer Subscribe Email Clicked",
            "footer-omnichannel": "Staging - Footer Omnichannel Dashboard Clicked",
            "footer-ai-agent": "Staging - Footer Features AI Agent Clicked",
            "footer-full-funnel": "Staging - Footer Features Full Funnel Clicked",
            "footer-integration": "Staging - Footer Features Integration Clicked",
            "footer-lex": "Staging - Footer Features Lex Clicked",
            "footer-about-us": "Staging - Footer Company AboutUs Clicked",
            "footer-privacy-policy": "Staging - Footer Privacy Policy Clicked",
            "footer-terms": "Staging - Footer Terms and Conditions Clicked",
            "footer-blog": "Staging - Footer Company Blog Clicked",
            "footer-social-linkedin": "Staging - Footer Social LinkedIn Clicked",
            "footer-social-facebook": "Staging - Footer Social Facebook Clicked",
            "footer-social-instagram": "Staging - Footer Social Instagram Clicked",
            "footer-social-youtube": "Staging - Footer Social YouTube Clicked",

            "home-start-free-trial": "Staging - Home Start Free Trial Clicked",
            "home-book-demo": "Staging - Home Book Demo Clicked",
            "home-hero-play-video": "Staging - Home Page Hero Play Video Clicked",
            "home-growth-banner-book-demo": "Staging - Home Page Growth Banner Book Demo Clicked",
            "home-growth-banner-try-cmgalaxy": "Staging - Home Page Growth Banner Try CMGalaxy Clicked",
            "proven-results-book-demo": "Staging - Proven Results Book Demo Clicked",
            "proven-results-try-cmgalaxy": "Staging - Proven Results Try CMGalaxy Clicked",
            "home-footer-cta-book-demo": "Staging - Home Footer Cta Book Demo Clicked",

            "aboutus-footer-book-demo": "Staging - Aboutus Footer Cta Book Demo Clicked",
            "aboutus-footer-try-cmgalaxy": "Staging - Aboutus Footer Cta Try Cmgalaxy Clicked",
            "blog-footer-cta-book-demo": "Staging - Blog Footer Cta Book Demo Clicked",
            "blog-detail-banner-try-cmgalaxy": "Staging - Blog Detail Cta Banner Try CMGalaxy Clicked",
            "blog-detail-banner-book-demo": "Staging - Blog Detail Cta Banner Book Demo Clicked",
            "blog-detail-footer-book-demo": "Staging - Blog Detail Footer Cta Book Demo Clicked",

            "audit-hero-start-analysis-desktop": "Staging - Website Start Analysis Clicked desktop",
            "audit-hero-start-analysis-mobile": "Staging - Website Start Analysis Clicked mobile"
          };

          function logAmplitudeEvent(eventName, buttonId) {
            if (!window.amplitude) return;
            const props = {
              page_name: pageName,
              initiated_at: pageName,
              section_at: sectionMap[buttonId] || "Unknown Section",
              device_category: deviceCategory,
              os_clean: osClean
            };

            if (typeof window.amplitude.logEvent === 'function') {
              window.amplitude.logEvent(eventName, props);
            } else if (typeof window.amplitude.track === 'function') {
              window.amplitude.track(eventName, props);
            }

            if (window.amplitude && typeof window.amplitude.flush === 'function') {
              window.amplitude.flush();
            }

            console.log(`✅ Amplitude Event Fired: ${eventName}`, props);
          }

          // Document delegation for all CTA element IDs
          document.addEventListener("click", function (e) {
            const el = e.target.closest("[id]");
            if (!el || !el.id) return;
            if (eventNameMap[el.id]) {
              logAmplitudeEvent(eventNameMap[el.id], el.id);
            }
          });

          const footerEmailInput = document.getElementById("footer-email-input");
          if (footerEmailInput) {
            footerEmailInput.addEventListener("input", function () {
              logAmplitudeEvent("Subscribe Email Entered", "footer-email-input");
            });
          }

          // Modal Video Handler
          const body = document.body;
          const modal = document.querySelector(".modal-video");
          const openBtns = document.querySelectorAll(".open-video");
          const closeBtn = document.querySelector(".close-btn-video");

          if (modal) {
            openBtns.forEach(btn => {
              btn.addEventListener("click", () => {
                body.classList.add("modal-open");
                modal.classList.add("active");
                body.style.overflow = "hidden";
              });
            });

            function closeModal() {
              body.classList.remove("modal-open");
              modal.classList.remove("active");
              body.style.overflow = "";
            }

            if (closeBtn) closeBtn.addEventListener("click", closeModal);
            modal.addEventListener("click", (e) => { if (e.target === modal) closeModal(); });
            document.addEventListener("keydown", (e) => { if (e.key === "Escape") closeModal(); });
          }

          // Scrolled Navbar Handler
          const header = document.querySelector(".header-2");
          if (header) {
            function handleScroll() {
              if (window.scrollY > 10) { header.classList.add("is-scrolled"); }
              else { header.classList.remove("is-scrolled"); }
            }
            header.classList.remove("is-scrolled");
            handleScroll();
            window.addEventListener("scroll", handleScroll);
          }

          // Lazy load images, iframes & background images
          document.querySelectorAll("img").forEach(img => {
            if (!img.hasAttribute("loading")) img.setAttribute("loading", "lazy");
            img.setAttribute("decoding", "async");
          });
          document.querySelectorAll("iframe").forEach(iframe => {
            iframe.setAttribute("loading", "lazy");
          });

          const bgElements = document.querySelectorAll("[data-bg]");
          if ("IntersectionObserver" in window && bgElements.length > 0) {
            const observer = new IntersectionObserver(entries => {
              entries.forEach(entry => {
                if (entry.isIntersecting) {
                  const el = entry.target;
                  el.style.backgroundImage = "url('" + el.getAttribute("data-bg") + "')";
                  observer.unobserve(el);
                }
              });
            });
            bgElements.forEach(el => observer.observe(el));
          }
        });

        // Blog Click DataLayer Event
        document.addEventListener("mousedown", function (e) {
          const el = e.target.closest("[data-blog-title]");
          if (!el) return;
          const blogTitle = el.getAttribute("data-blog-title");
          if (!blogTitle) return;
          console.log("✅ Amplitude Event Fired: Staging - Blog Clicked", blogTitle);
          window.dataLayer = window.dataLayer || [];
          window.dataLayer.push({
            event: "Staging - Blog Clicked",
            blog_title: blogTitle
          });
        });
        </script>
        <?php
    }
}
add_action('wp_footer', 'cmg_site_wide_analytics_footer', 100);


/* ==========================================================================
   FORCE ELEMENTOR TEMPLATE TYPE TO 'wp-page' FOR ALL PAGES
   ========================================================================== */
if (!function_exists('cmg_fix_elementor_page_badges')) {
    function cmg_fix_elementor_page_badges() {
        if (!is_admin()) return;
        global $wpdb;
        // Update _elementor_template_type to 'wp-page' for all posts that are of type 'page'
        $wpdb->query("
            UPDATE {$wpdb->postmeta} pm
            JOIN {$wpdb->posts} p ON pm.post_id = p.ID
            SET pm.meta_value = 'wp-page'
            WHERE p.post_type = 'page' AND pm.meta_key = '_elementor_template_type' AND pm.meta_value != 'wp-page'
        ");
    }
}
add_action('admin_init', 'cmg_fix_elementor_page_badges');


/* ==========================================================================
   CMG GLOSSARY CUSTOM POST TYPE & DYNAMIC SHORTCODE [cmg_glossary]
   ========================================================================== */

if (!function_exists('cmg_register_glossary_cpt')) {
    function cmg_register_glossary_cpt() {
        $labels = array(
            'name'                  => _x('Glossary Terms', 'Post Type General Name', 'hello-elementor'),
            'singular_name'         => _x('Glossary Term', 'Post Type Singular Name', 'hello-elementor'),
            'menu_name'             => __('CMG Glossary', 'hello-elementor'),
            'name_admin_bar'        => __('Glossary Term', 'hello-elementor'),
            'archives'              => __('Glossary Archives', 'hello-elementor'),
            'all_items'             => __('All Glossary Terms', 'hello-elementor'),
            'add_new_item'          => __('Add New Glossary Term', 'hello-elementor'),
            'add_new'               => __('Add New', 'hello-elementor'),
            'new_item'              => __('New Glossary Term', 'hello-elementor'),
            'edit_item'             => __('Edit Glossary Term', 'hello-elementor'),
            'update_item'           => __('Update Glossary Term', 'hello-elementor'),
            'view_item'             => __('View Glossary Term', 'hello-elementor'),
            'search_items'          => __('Search Glossary Terms', 'hello-elementor'),
        );
        $args = array(
            'label'                 => __('Glossary Term', 'hello-elementor'),
            'description'           => __('CMGalaxy Glossary Terms & Definitions', 'hello-elementor'),
            'labels'                => $labels,
            'supports'              => array('title', 'editor', 'excerpt', 'custom-fields', 'revisions'),
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 20,
            'menu_icon'             => 'dashicons-book',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
            'show_in_rest'          => true,
            'rewrite'               => array('slug' => 'glossary', 'with_front' => false),
        );
        register_post_type('cmg_glossary', $args);
    }
}
add_action('init', 'cmg_register_glossary_cpt', 0);

/* Shortcode [cmg_glossary] - Exact User Requested UI */
if (!function_exists('cmg_glossary_shortcode')) {
    function cmg_glossary_shortcode($atts) {
        $args = array(
            'post_type'      => 'cmg_glossary',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
            'post_status'    => 'publish',
        );

        $query = new WP_Query($args);
        $terms = array();

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $title = get_the_title();
                $content = get_the_content();
                if (empty($content)) {
                    $content = get_the_excerpt();
                }
                $letter = strtoupper(substr($title, 0, 1));
                if (!preg_match('/[A-Z]/', $letter)) {
                    $letter = '#';
                }
                $terms[] = array(
                    'id'        => get_the_ID(),
                    'title'     => $title,
                    'definition'=> apply_filters('the_content', $content),
                    'link'      => get_permalink(),
                    'letter'    => $letter,
                );
            }
            wp_reset_postdata();
        }

        ob_start();
        ?>
        <style>
          .wd-glossary-wrapper {
            font-family: "Onest", -apple-system, BlinkMacSystemFont, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
          }
          
          .wd-glossary-app {
            background-color: transparent;
            border-radius: 12px;
            padding: 20px 0;
          }
          
          .wd-header-container {
            text-align: center;
            margin-bottom: 24px;
            padding-top: 24px;
          }
          
          .wd-breadcrumb {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 16px;
          }
          
          .wd-breadcrumb strong {
            color: #1f2937;
            font-weight: 600;
          }
          
          .wd-main-title {
            font-size: 54px;
            font-weight: 600;
            color: rgb(22, 28, 82);
            margin: 0 0 16px 0;
            letter-spacing: -0.5px;
          }
          
          .wd-subtitle {
            font-size: 16px;
            color: rgb(22, 28, 82);
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.5;
          }
          
          /* Outer shell */
          .stylish-search__shell {
            padding: 10px;
            border-radius: 999px;
            background: linear-gradient(135deg, rgba(79,140,255,0.08), rgba(61,220,151,0.08));
            margin-bottom: 32px;
          }
          
          /* Inner body */
          .stylish-search__body {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 22px;
            height: 57px;
            border-radius: 999px;
            border: 1px solid transparent;
            background: 
              linear-gradient(#ffffff, #ffffff) padding-box, 
              linear-gradient(135deg, #4f8cff, #3ddc97) border-box;
            position: relative;
          }
          
          /* Icon */
          .stylish-search__sparkle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
          }
          
          .stylish-search__sparkle img {
            width: 22px !important;
            height: 22px !important;
          }
          
          /* Input */
          .stylish-search__input,
          .stylish-search__input:focus,
          .stylish-search__input:active,
          .stylish-search__input:hover {
            flex: 1 !important;
            border: 0 !important;
            border-style: none !important;
            outline: none !important;
            box-shadow: none !important;
            font-size: 18px !important;
            background: transparent !important;
            color: #0b1f4f !important;
            height: 100% !important;
            margin: 0 !important;
            padding: 0 10px !important;
          }
          
          /* Button */
          .stylish-search__submit {
            position: absolute;
            right: 6px;
            top: 50%;
            transform: translateY(-50%);
            width: 48px;
            height: 48px;
            border-radius: 50%;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            background: 
              radial-gradient(36.82% 41.72% at 49.73% 100%, rgba(175, 244, 251, 0.45) 0%, rgba(58, 125, 255, 0) 100%), 
              radial-gradient(50.19% 67.5% at 49.73% 50%, #3A7DFF 0%, #3A7DFF 100%);
            background-blend-mode: plus-lighter, normal;
            box-shadow: 
              0px 1px 2px rgba(58, 125, 255, 0.08), 
              0px 10px 10px rgba(58, 125, 255, 0.21), 
              inset 0px 0px 6px rgba(255, 255, 255, 0.5);
            transition: all 0.25s ease;
          }
          
          .stylish-search__submit:hover {
            transform: translateY(-50%) scale(1.08);
          }
          
          .stylish-search__input::placeholder {
            color: rgba(11, 31, 79, 0.35);
            font-size: 15px;
          }
          
          @media (max-width: 767px) {
            .stylish-search__body {
              height: 50px;
              padding: 0 15px;
            }
            .stylish-search__input {
              font-size: 16px;
            }
            .wd-main-title {
              font-size: 36px;
            }
          }
          
          .wd-alphabet-filter {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            background-color: transparent;
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 24px;
            justify-content: center;
          }
          
          .wd-alpha-btn {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            background: transparent;
            font-size: 16px;
            font-weight: 600;
            color: rgb(22, 28, 82);
            cursor: pointer;
            border-radius: 4px;
            transition: all 0.2s;
          }
          
          .wd-alpha-btn:hover {
            background-color: #e5e7eb;
          }
          
          .wd-alpha-btn.active {
            background-color: #3a7dff;
            color: white;
          }
          
          .wd-glossary-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            background-color: #fff;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
          }
          
          .wd-term-card {
            border: 1px solid #f3f4f6;
            border-radius: 8px;
            padding: 16px;
            background-color: #fff;
            cursor: pointer;
            transition: border-color 0.2s;
          }
          
          .wd-term-card:hover {
            border-color: #e5e7eb;
          }
          
          .wd-term-header {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
          }
          
          .wd-term-left {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            width: 100%;
            text-align: center;
          }
          
          .wd-term-icon {
            width: 32px;
            height: 32px;
            background-color: rgba(58, 125, 255, 0.1);
            color: #3a7dff;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
          }
          
          .wd-term-title {
            font-weight: 700;
            font-size: 16px;
            color: rgb(22, 28, 82);
            margin: 0;
          }
          
          .wd-term-title-link {
            color: rgb(22, 28, 82);
            text-decoration: none;
            transition: color 0.2s ease;
          }
          
          .wd-term-title-link:hover {
            color: #3a7dff;
            text-decoration: underline;
          }
          
          .wd-chevron {
            color: #9ca3af;
            transition: transform 0.3s ease;
          }
          
          .wd-term-card.open .wd-chevron {
            transform: rotate(180deg);
          }
          
          .wd-term-definition {
            margin-top: 12px;
            font-size: 16px;
            font-weight: normal;
            line-height: 1.6;
            color: #4b5563;
            display: none;
            padding-left: 44px;
          }
          
          .wd-term-card.open .wd-term-definition {
            display: block;
            animation: wdFadeIn 0.3s ease;
          }

          .wd-single-page-link {
            display: inline-block;
            margin-top: 12px;
            font-size: 14px;
            font-weight: 600;
            color: #3a7dff;
            text-decoration: underline;
          }
          
          @keyframes wdFadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
          }

          .wd-no-results {
            text-align: center;
            padding: 32px;
            color: #6b7280;
            font-size: 14px;
          }
        </style>

        <div class="wd-glossary-wrapper">
          <div class="wd-header-container">
            <div class="wd-breadcrumb">Home / <strong>Glossary</strong></div>
            <h1 class="wd-main-title">CMGalaxy Glossary</h1>
            <p class="wd-subtitle">A platform created to bring clarity, speed, and intelligence <br>to every marketer's workflow.</p>
          </div>
          
          <div class="wd-glossary-app">
            <form onsubmit="event.preventDefault();">
              <div class="stylish-search stylish-search--banner">
                  <div class="stylish-search__shell">
                      <div class="stylish-search__body">
                          <span class="stylish-search__sparkle">
                              <img src="https://cdn.prod.website-files.com/67b5e5b07dee6e1ed91f0f5a/68997abd3f21edb670a1ae3f_Group%201000003734.svg" alt="icon">
                          </span>
                          <input type="text" id="wd-search-input" class="stylish-search__input" placeholder="Search by term">
                          <button type="submit" id="wd-search-btn" class="stylish-search__submit">
                              <svg width="24" height="24" viewBox="0 0 20 20" fill="none" style="width: 24px !important; height: 24px !important;">
                                  <path d="M14.707 13.293a1 1 0 0 1 1.32-.083l.094.083 2.5 2.5a1 1 0 0 1-1.32 1.497l-.094-.083-2.5-2.5a1 1 0 0 1 0-1.414z" fill="white"/>
                                  <path d="M9 2a7 7 0 1 1 0 14A7 7 0 0 1 9 2zm0 2a5 5 0 1 0 0 10A5 5 0 0 0 9 4z" fill="white"/>
                              </svg>
                          </button>
                      </div>
                  </div>
              </div>
            </form>
          
            <div class="wd-alphabet-filter" id="wd-alphabet-filter"></div>
          
            <div class="wd-glossary-list" id="wd-glossary-list">
              <?php if (!empty($terms)) : ?>
                <?php foreach ($terms as $index => $item) : ?>
                  <div class="wd-term-card" data-term="<?php echo esc_attr($item['title']); ?>" data-letter="<?php echo esc_attr($item['letter']); ?>" data-def="<?php echo esc_attr(wp_strip_all_tags($item['definition'])); ?>" onclick="window.location.href='<?php echo esc_url($item['link']); ?>';">
                    <div class="wd-term-header">
                      <div class="wd-term-left">
                        <h3 class="wd-term-title" style="margin:0;">
                          <a href="<?php echo esc_url($item['link']); ?>" class="wd-term-title-link" onclick="event.stopPropagation();"><?php echo esc_html($item['title']); ?></a>
                        </h3>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php else : ?>
                <div class="wd-no-results">No terms found. Add terms under CMG Glossary in WP Admin!</div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <script>
        document.addEventListener("DOMContentLoaded", () => {
          let currentFilter = "A";
          let currentSearch = "";
          
          const filterContainer = document.getElementById("wd-alphabet-filter");
          const searchInput = document.getElementById("wd-search-input");
          const cards = Array.from(document.querySelectorAll(".wd-term-card"));
          
          const alphabets = "ABCDEFGHIJKLMNOPQRSTUVWXYZ".split("");
          
          function renderFilters() {
            if (!filterContainer) return;
            filterContainer.innerHTML = "";
            alphabets.forEach(letter => {
              const btn = document.createElement("button");
              btn.className = "wd-alpha-btn";
              btn.textContent = letter;
              
              if (letter === currentFilter) {
                btn.classList.add("active");
              }
              
              btn.addEventListener("click", () => {
                if (currentFilter === letter) {
                  currentFilter = "";
                  btn.classList.remove("active");
                } else {
                  currentFilter = letter;
                  document.querySelectorAll(".wd-alpha-btn").forEach(b => b.classList.remove("active"));
                  btn.classList.add("active");
                }
                applyFilters();
              });
              
              filterContainer.appendChild(btn);
            });
          }
          
          function applyFilters() {
            let hasVisibleGlobal = false;
            
            cards.forEach(card => {
              const term = card.getAttribute("data-term").toLowerCase();
              const def = (card.getAttribute("data-def") || "").toLowerCase();
              const letter = card.getAttribute("data-letter");
              
              const matchesSearch = currentSearch === "" || term.includes(currentSearch) || def.includes(currentSearch);
              const matchesFilter = currentSearch !== "" ? true : (currentFilter === "" || letter === currentFilter);
              
              if (matchesSearch && matchesFilter) {
                card.style.display = "block";
                hasVisibleGlobal = true;
              } else {
                card.style.display = "none";
              }
            });
            
            let noResults = document.getElementById("wd-no-results");
            const listEl = document.getElementById("wd-glossary-list");

            if (!hasVisibleGlobal) {
              if (!noResults && listEl) {
                noResults = document.createElement("div");
                noResults.id = "wd-no-results";
                noResults.className = "wd-no-results";
                noResults.textContent = "No matching terms found.";
                listEl.appendChild(noResults);
              }
              if (noResults) noResults.style.display = "block";
            } else {
              if (noResults) noResults.style.display = "none";
            }
          }
          
          if (searchInput) {
            searchInput.addEventListener("input", (e) => {
              currentSearch = e.target.value.toLowerCase().trim();
              applyFilters();
            });
          }
          
          

          renderFilters();
          applyFilters();
        });
        </script>
        <?php
        return ob_get_clean();
    }
}


    add_shortcode('cmg_glossary', 'cmg_glossary_shortcode');
    add_shortcode('glossary', 'cmg_glossary_shortcode');
    add_shortcode('glossary_app', 'cmg_glossary_shortcode');
    add_shortcode('cmg_glossary_app', 'cmg_glossary_shortcode');
    add_shortcode('cmgalaxy_glossary', 'cmg_glossary_shortcode');


/* Automatic Flush Rewrite Rules for Glossary CPT Permalinks */
if (!function_exists('cmg_flush_glossary_rewrite_rules')) {
    function cmg_flush_glossary_rewrite_rules() {
        if (!get_option('cmg_glossary_flushed_rules_v2')) {
            flush_rewrite_rules();
            update_option('cmg_glossary_flushed_rules_v2', 1);
        }
    }
}
add_action('admin_init', 'cmg_flush_glossary_rewrite_rules');
add_action('init', function() { flush_rewrite_rules(false); }, 99);

/* Single Template for individual Glossary Term pages /glossary/term-slug/ */
if (!function_exists('cmg_glossary_single_template')) {
    function cmg_glossary_single_template($single_template) {
        global $post;
        if ($post && $post->post_type === 'cmg_glossary') {
            add_filter('the_content', 'cmg_render_single_glossary_content', 20);
        }
        return $single_template;
    }
}
add_filter('single_template', 'cmg_glossary_single_template');

if (!function_exists('cmg_render_single_glossary_content')) {
    function cmg_render_single_glossary_content($content) {
        if (!is_singular('cmg_glossary')) return $content;

        $title = get_the_title();
        $letter = strtoupper(substr($title, 0, 1));
        $glossary_url = home_url('/glossary/');

        ob_start();
        ?>
        <style>
          @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

          body.single-cmg_glossary .page-header,
          body.single-cmg_glossary .entry-header,
          body.single-cmg_glossary h1.entry-title,
          body.single-cmg_glossary header.entry-header {
            display: none !important;
          }

          .cmg-single-term-wrapper {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
            color: #111827;
          }

          .cmg-single-breadcrumb {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 24px;
            text-align: center;
          }

          .cmg-single-breadcrumb a {
            color: #3A7DFF;
            text-decoration: none;
            font-weight: 500;
          }

          .cmg-single-breadcrumb a:hover {
            text-decoration: underline;
          }

          .cmg-single-term-card {
            background: transparent;
            border: none;
            border-radius: 0;
            padding: 40px 0;
            box-shadow: none;
            text-align: center;
          }

          .cmg-single-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            margin-bottom: 24px;
            padding-bottom: 20px;
            border-bottom: 1px solid #f3f4f6;
            text-align: center;
          }

          .cmg-single-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: #eef2ff;
            color: #3A7DFF;
            font-size: 20px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
          }

          .cmg-single-title {
            font-size: 36px;
            font-weight: 800;
            color: #0b1f4f;
            margin: 0;
            letter-spacing: -0.5px;
            text-align: center;
          }

          .cmg-single-body {
            font-size: 18px;
            line-height: 1.7;
            color: #374151;
            margin-bottom: 32px;
            text-align: center;
          }

          .cmg-single-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            padding-top: 20px;
            border-top: 1px solid #f3f4f6;
            text-align: center;
          }

          .cmg-back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 50px;
            background: #3A7DFF;
            color: #ffffff;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.2s ease;
          }

          .cmg-back-btn:hover {
            background: #2563eb;
            color: #ffffff;
            transform: translateY(-1px);
          }
        </style>

        <div class="cmg-single-term-wrapper">
          <div class="cmg-single-breadcrumb">
            <a href="<?php echo esc_url(home_url('/')); ?>">Home</a> / 
            <a href="<?php echo esc_url($glossary_url); ?>">Glossary</a> / 
            <strong><?php echo esc_html($title); ?></strong>
          </div>

          <div class="cmg-single-term-card">
            <div class="cmg-single-header">
              <h1 class="cmg-single-title"><?php echo esc_html($title); ?></h1>
            </div>

            <div class="cmg-single-body">
              <?php echo $content; ?>
            </div>

            <div class="cmg-single-actions">
              <a href="<?php echo esc_url($glossary_url); ?>" class="cmg-back-btn">
                &larr; Back to Glossary Overview
              </a>
            </div>
          </div>
        </div>
        <?php
        return ob_get_clean();
    }
}




/* Ensure Archive Page (/glossary/) renders the Glossary UI Shortcode */
if (!function_exists('cmg_glossary_archive_template')) {
    function cmg_glossary_archive_template($template) {
        if (is_post_type_archive('cmg_glossary')) {
            $archive_file = locate_template('archive-cmg_glossary.php');
            if (!empty($archive_file)) {
                return $archive_file;
            }
        }
        return $template;
    }
}
add_filter('template_include', 'cmg_glossary_archive_template', 99);

/* Hide Hello Elementor default header title on single glossary term pages */
add_filter('hello_elementor_page_title', function($title) {
    if (is_singular('cmg_glossary')) {
        return false;
    }
    return $title;
});


/* ==========================================================================
   CMG BLOG SINGLE POST HEADER (Author, Date, Title, Reviews, Share)
   ========================================================================== */

/* ==========================================================================
   CMG AUTHOR DATA RESOLVER (Fully Dynamic from Edit Post / Custom Fields / WP Author)
   ========================================================================== */
if ( ! function_exists( 'cmg_get_post_author_data' ) ) {
    function cmg_get_post_author_data( $post_id = 0, $atts = array() ) {
        if ( ! $post_id ) {
            global $post;
            $post_id = ( $post && isset( $post->ID ) ) ? $post->ID : get_the_ID();
        }

        $author_id = $post_id ? get_post_field( 'post_author', $post_id ) : 0;

        // 1. Author Name
        $author_name = ! empty( $atts['author'] ) ? sanitize_text_field( $atts['author'] ) : ( ! empty( $atts['name'] ) ? sanitize_text_field( $atts['name'] ) : '' );
        
        // Check post meta keys from Edit Post
        if ( empty( $author_name ) && $post_id ) {
            $name_keys = array( 'author_name', 'author', 'cmg_author_name', 'post_author_name', 'author-name' );
            foreach ( $name_keys as $key ) {
                $val = get_post_meta( $post_id, $key, true );
                if ( ! empty( $val ) && is_string( $val ) ) {
                    $author_name = sanitize_text_field( $val );
                    break;
                }
            }
        }

        // Fallback to WP author profile
        if ( empty( $author_name ) && $author_id ) {
            $author_name = get_the_author_meta( 'display_name', $author_id );
            if ( empty( $author_name ) ) {
                $first = get_the_author_meta( 'first_name', $author_id );
                $last  = get_the_author_meta( 'last_name', $author_id );
                $author_name = trim( $first . ' ' . $last );
            }
            if ( empty( $author_name ) ) {
                $author_name = get_the_author_meta( 'nickname', $author_id );
            }
        }
        if ( empty( $author_name ) ) {
            $author_name = 'Author';
        }

        $is_versha = ( stripos( $author_name, 'versha' ) !== false );

        // 2. Author Avatar / Image (Custom field from Edit Post -> User Meta -> WordPress avatar)
        $avatar_url = ! empty( $atts['avatar'] ) ? esc_url( $atts['avatar'] ) : ( ! empty( $atts['image'] ) ? esc_url( $atts['image'] ) : '' );

        // Check post meta keys from Edit Post (can be image URL or media library attachment ID)
        if ( empty( $avatar_url ) && $post_id ) {
            $img_keys = array(
                'author_image', 'author_avatar', 'author_photo', 'author_picture',
                'author_img', 'author_pic', 'cmg_author_image', 'cmg_author_avatar',
                'author-image', 'author-avatar', 'user_image', 'user_avatar', 'image', 'avatar'
            );
            foreach ( $img_keys as $key ) {
                $val = get_post_meta( $post_id, $key, true );
                if ( ! empty( $val ) ) {
                    if ( is_numeric( $val ) ) {
                        $avatar_url = wp_get_attachment_image_url( (int) $val, 'full' );
                    } elseif ( is_string( $val ) ) {
                        $avatar_url = esc_url( $val );
                    }
                    if ( ! empty( $avatar_url ) ) {
                        break;
                    }
                }
            }
        }

        // Check user meta from WP user profile (profile_picture, simple_local_avatar, etc.)
        if ( empty( $avatar_url ) && $author_id ) {
            $user_img_keys = array( 'profile_picture', 'simple_local_avatar', 'user_avatar', 'author_image' );
            foreach ( $user_img_keys as $ukey ) {
                $uval = get_user_meta( $author_id, $ukey, true );
                if ( ! empty( $uval ) ) {
                    if ( is_numeric( $uval ) ) {
                        $avatar_url = wp_get_attachment_image_url( (int) $uval, 'full' );
                    } elseif ( is_array( $uval ) && ! empty( $uval['full'] ) ) {
                        $avatar_url = esc_url( $uval['full'] );
                    } elseif ( is_string( $uval ) ) {
                        $avatar_url = esc_url( $uval );
                    }
                    if ( ! empty( $avatar_url ) ) {
                        break;
                    }
                }
            }
        }

        // Check WordPress get_avatar_url
        if ( empty( $avatar_url ) && $author_id ) {
            $wp_avatar = get_avatar_url( $author_id, array( 'size' => 160 ) );
            if ( ! empty( $wp_avatar ) ) {
                $avatar_url = $wp_avatar;
            }
        }

        // Known avatar fallback only if author is specifically Versha Rawat
        if ( empty( $avatar_url ) && $is_versha ) {
            $avatar_url = 'https://cdn.prod.website-files.com/67b5e5b07dee6e1ed91f0f5a/68c7f03ced5fa62ff8419528_vesha.jpeg';
        }

        // Use high-resolution unclipped CMGalaxy brand icon for admin / CMGalaxy author
        $default_cmg_avatar = get_template_directory_uri() . '/assets/images/cmg-avatar-icon.png';
        if ( empty( $avatar_url ) || stripos( $avatar_url, 'Group-1000004539' ) !== false || ( ( stripos( $author_name, 'admin' ) !== false || stripos( $author_name, 'cmgalaxy' ) !== false ) && ! $is_versha ) ) {
            $avatar_url = $default_cmg_avatar;
        }

        // 3. Author Bio
        $author_bio = ! empty( $atts['bio'] ) ? wp_kses_post( $atts['bio'] ) : '';
        if ( empty( $author_bio ) && $post_id ) {
            $bio_keys = array( 'author_bio', 'author_description', 'author_desc', 'cmg_author_bio', 'cmg_author_description' );
            foreach ( $bio_keys as $bkey ) {
                $bval = get_post_meta( $post_id, $bkey, true );
                if ( ! empty( $bval ) && is_string( $bval ) ) {
                    $author_bio = wp_kses_post( $bval );
                    break;
                }
            }
        }
        if ( empty( $author_bio ) && $author_id ) {
            $wp_bio = get_the_author_meta( 'description', $author_id );
            if ( ! empty( $wp_bio ) ) {
                $author_bio = wp_kses_post( $wp_bio );
            }
        }
        if ( empty( $author_bio ) && $is_versha ) {
            $author_bio = 'Marketing technology content specialist with 4+ years of experience creating research-driven content for the EdTech and B2B SaaS space. Passionate about simplifying complex MarTech concepts through strategic storytelling, audience-focused writing, and data-backed insights.';
        }

        // 4. Author LinkedIn / URL
        $linkedin_url = ! empty( $atts['linkedin'] ) ? esc_url( $atts['linkedin'] ) : '';
        if ( empty( $linkedin_url ) && $post_id ) {
            $li_keys = array( 'author_linkedin', 'linkedin', 'author_url', 'cmg_author_linkedin' );
            foreach ( $li_keys as $lkey ) {
                $lval = get_post_meta( $post_id, $lkey, true );
                if ( ! empty( $lval ) && is_string( $lval ) ) {
                    $linkedin_url = esc_url( $lval );
                    break;
                }
            }
        }
        if ( empty( $linkedin_url ) && $author_id ) {
            $li_meta = get_user_meta( $author_id, 'linkedin', true );
            if ( ! empty( $li_meta ) ) {
                $linkedin_url = esc_url( $li_meta );
            } else {
                $u_url = get_the_author_meta( 'user_url', $author_id );
                if ( ! empty( $u_url ) ) {
                    $linkedin_url = esc_url( $u_url );
                }
            }
        }
        if ( empty( $linkedin_url ) && $is_versha ) {
            $linkedin_url = 'https://www.linkedin.com/in/versha-rawat/?originalSubdomain=in';
        }

        return array(
            'id'       => $author_id,
            'name'     => $author_name,
            'avatar'   => $avatar_url,
            'bio'      => $author_bio,
            'linkedin' => $linkedin_url,
        );
    }
}


if ( ! function_exists( 'cmg_render_blog_ratings_script' ) ) {
    function cmg_render_blog_ratings_script() {
        static $cmg_ratings_script_rendered = false;
        if ( $cmg_ratings_script_rendered ) {
            return '';
        }
        $cmg_ratings_script_rendered = true;
        ob_start();
        ?>
        <script>
        (function() {
          function initCmgRatings() {
            var wraps = document.querySelectorAll(".cmg-blog-header-wrapper, .cmg-blog-bottom-review-share-wrap");
            if (!wraps.length) return;

            var postId = wraps[0].getAttribute("data-post-id") || "1341";
            var blogSlug = wraps[0].getAttribute("data-blog-slug") || "";

            // Base URL matching book a demo form
            var apiBaseUrl = "https://staging-api.cmgalaxy.com";
            var apiFallbackUrl = "https://api.cmgalaxy.com";

            // Extract blog title / slug from URL or wrapper
            function getBlogTitleFromUrl(url) {
              url = url || window.location.href;
              try {
                var marker = "/blog/";
                var index = url.indexOf(marker);
                if (index !== -1) {
                  var after = url.substring(index + marker.length);
                  var clean = after.split(/[?#]/)[0].replace(/^\/+|\/+$/g, "");
                  var firstSegment = clean.split("/")[0];
                  if (firstSegment) return decodeURIComponent(firstSegment);
                }
                var wrap = document.querySelector("[data-blog-slug]");
                if (wrap && wrap.getAttribute("data-blog-slug")) {
                  return wrap.getAttribute("data-blog-slug");
                }
                var pathParts = window.location.pathname.replace(/^\/+|\/+$/g, "").split("/").filter(Boolean);
                if (pathParts.length) {
                  return decodeURIComponent(pathParts[pathParts.length - 1]);
                }
              } catch (e) {
                return null;
              }
              return null;
            }

            var blogTitleFromUrl = getBlogTitleFromUrl() || blogSlug || "is-your-cac-high-because-youre-ignoring-creative-analysis";

            // Generate or get cross-device UUID
            function getCrossDeviceId() {
              var id = localStorage.getItem("cross_device_id");
              if (!id || id.length < 32) {
                if (typeof crypto !== "undefined" && crypto.randomUUID) {
                  id = crypto.randomUUID();
                } else {
                  id = "xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g, function(c) {
                    var r = Math.random() * 16 | 0, v = c === "x" ? r : (r & 0x3 | 0x8);
                    return v.toString(16);
                  });
                }
                localStorage.setItem("cross_device_id", id);
              }
              return id;
            }

            var crossDeviceId = getCrossDeviceId();
            var totalReviews = 0;
            var currentAvgRating = 0.0;
            var userPreviousRating = 0;
            var userHasRated = false;

            function syncAllDisplays(userRating, avgRating, totalCount) {
              wraps.forEach(function(wrap) {
                var stars = wrap.querySelectorAll(".cmg-star");
                var scoreEl = wrap.querySelector(".cmg-rating-score");
                stars.forEach(function(s) {
                  var idx = parseInt(s.getAttribute("data-index") || s.getAttribute("data-value"), 10);
                  if (idx <= userRating) {
                    s.classList.add("active");
                    s.classList.add("filled");
                  } else {
                    s.classList.remove("active");
                    s.classList.remove("filled");
                  }
                });
                if (scoreEl) {
                  var displayAvg = (avgRating !== undefined && avgRating !== null && parseFloat(avgRating) > 0) ? parseFloat(avgRating).toFixed(1) : "0.0";
                  var displayCount = (totalCount !== undefined && totalCount !== null) ? totalCount : "0";
                  scoreEl.textContent = displayAvg + " (" + displayCount + ")";
                }
              });
            }

            // 1. Fetch ratings: check local WP DB first, sync with CMGalaxy API
            function fetchReviews(cId, bTitle) {
              if (!cId || !bTitle) return;

              var ajaxUrl = "<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>";
              if (ajaxUrl) {
                fetch(ajaxUrl + "?action=cmg_get_rating&blog_title=" + encodeURIComponent(bTitle) + "&cross_device_id=" + encodeURIComponent(cId) + "&post_id=" + encodeURIComponent(postId))
                  .then(function(r) { return r.json(); })
                  .then(function(res) {
                    if (res && res.success && res.data) {
                      var d = res.data;
                      if (d.total_rating > 0) {
                        totalReviews = parseInt(d.total_rating, 10);
                        currentAvgRating = parseFloat(d.avg_rating) || 0;
                        if (d.user_rating > 0) {
                          userPreviousRating = parseInt(d.user_rating, 10);
                          userHasRated = true;
                        }
                        syncAllDisplays(d.user_rating || Math.round(currentAvgRating), currentAvgRating.toFixed(1), totalReviews);
                        return;
                      }
                    }
                    fetchFromCMGAPI(cId, bTitle);
                  })
                  .catch(function(err) {
                    fetchFromCMGAPI(cId, bTitle);
                  });
              } else {
                fetchFromCMGAPI(cId, bTitle);
              }
            }

            function fetchFromCMGAPI(cId, bTitle) {
              fetch(apiBaseUrl + "/api/v2/commonapis/fetch_rating/", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                  cross_device_id: cId,
                  blog_title: bTitle
                })
              })
              .then(function(res) {
                if (!res.ok && res.status >= 500) {
                  return fetch(apiFallbackUrl + "/api/v2/commonapis/fetch_rating/", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                      cross_device_id: cId,
                      blog_title: bTitle
                    })
                  }).then(function(r) { return r.json(); });
                }
                return res.json();
              })
              .then(function(data) {
                if (data && data.success && data.data) {
                  var d = data.data;
                  var uRating = d.rating || 0;
                  if (uRating > 0) {
                    userPreviousRating = uRating;
                    userHasRated = true;
                  }
                  totalReviews = d.total_rating || 0;
                  currentAvgRating = typeof d.avg_rating === "number" ? d.avg_rating : parseFloat(d.avg_rating || 0);
                  syncAllDisplays(uRating || Math.round(currentAvgRating), currentAvgRating.toFixed(1), totalReviews);
                }
              })
              .catch(function(err) {
                console.error("fetchReviews error:", err);
              });
            }

            // Initial Fetch
            fetchReviews(crossDeviceId, blogTitleFromUrl);

            wraps.forEach(function(wrap) {
              var starContainer = wrap.querySelector(".cmg-stars-list");
              if (!starContainer || starContainer.dataset.initialized) return;
              starContainer.dataset.initialized = "true";

              var stars = starContainer.querySelectorAll(".cmg-star");

              stars.forEach(function(star) {
                star.addEventListener("mouseenter", function() {
                  var hoverIdx = parseInt(this.getAttribute("data-index") || this.getAttribute("data-value"), 10);
                  stars.forEach(function(s) {
                    var sIdx = parseInt(s.getAttribute("data-index") || s.getAttribute("data-value"), 10);
                    if (sIdx <= hoverIdx) {
                      s.classList.add("hovered");
                    } else {
                      s.classList.remove("hovered");
                    }
                  });
                });

                star.addEventListener("click", function() {
                  var rating = parseInt(this.getAttribute("data-index") || this.getAttribute("data-value"), 10);
                  if (!rating) return;

                  // Instant recalculation
                  var currentSum = currentAvgRating * totalReviews;
                  if (userHasRated && userPreviousRating > 0) {
                    currentSum = currentSum - userPreviousRating + rating;
                  } else {
                    totalReviews = totalReviews + 1;
                    currentSum = currentSum + rating;
                    userHasRated = true;
                  }
                  userPreviousRating = rating;
                  currentAvgRating = totalReviews > 0 ? (currentSum / totalReviews) : rating;
                  var displayAvg = currentAvgRating.toFixed(1);

                  // Immediate UI Feedback
                  syncAllDisplays(rating, displayAvg, totalReviews);

                  wraps.forEach(function(w) {
                    var tEl = w.querySelector(".cmg-rating-text");
                    if (tEl) {
                      tEl.textContent = "Thank you!";
                      setTimeout(function() { tEl.textContent = "Rating"; }, 3000);
                    }
                  });

                  // 1. Log to local WordPress DB
                  var ajaxUrl = "<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>";
                  if (ajaxUrl) {
                    var formData = new FormData();
                    formData.append("action", "cmg_log_rating");
                    formData.append("post_id", postId);
                    formData.append("blog_title", blogTitleFromUrl);
                    formData.append("rating", rating);
                    formData.append("cross_device_id", crossDeviceId);
                    formData.append("page_url", window.location.href);

                    fetch(ajaxUrl, {
                      method: "POST",
                      body: formData
                    })
                    .then(function(r) { return r.json(); })
                    .then(function(res) {
                      console.log("WP DB Log response:", res);
                    })
                    .catch(function(e) { console.error("WP DB Log error:", e); });
                  }

                  // 2. Send to CMGalaxy backend API
                  fetch(apiBaseUrl + "/api/v2/commonapis/save_rating/", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                      cross_device_id: crossDeviceId,
                      rating: rating,
                      blog_title: blogTitleFromUrl
                    })
                  })
                  .then(function(res) {
                    if (!res.ok && res.status >= 500) {
                      return fetch(apiFallbackUrl + "/api/v2/commonapis/save_rating/", {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({
                          cross_device_id: crossDeviceId,
                          rating: rating,
                          blog_title: blogTitleFromUrl
                        })
                      }).then(function(r) { return r.json(); });
                    }
                    return res.json();
                  })
                  .then(function(data) {
                    console.log("CMG API save response:", data);
                  })
                  .catch(function(err) {
                    console.error("CMG API save error:", err);
                  });
                });
              });

              starContainer.addEventListener("mouseleave", function() {
                stars.forEach(function(s) { s.classList.remove("hovered"); });
              });
            });
          }

          if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", initCmgRatings);
          } else {
            initCmgRatings();
          }
        })();
        </script>
        <?php
        return ob_get_clean();
    }
}

if ( ! function_exists( 'cmg_render_blog_header' ) ) {
    function cmg_render_blog_header( $atts = array() ) {
        global $post;
        $GLOBALS['cmg_blog_header_already_rendered'] = true;

        $post_id = ( $post && isset( $post->ID ) ) ? $post->ID : 0;

        // Custom attributes or fallback to current post data
        $title = ! empty( $atts['title'] ) ? esc_html( $atts['title'] ) : ( $post_id ? get_the_title( $post_id ) : 'The Shift from Vanity Metrics to Value Metrics in Digital Marketing' );

        // Fully dynamic author data from Edit Post / User Profile
        $author_data = cmg_get_post_author_data( $post_id, $atts );
        $author_id   = $author_data['id'];
        $author_name = $author_data['name'];
        $author_role = ! empty( $atts['role'] ) ? esc_html( $atts['role'] ) : 'Author';
        $avatar_url  = $author_data['avatar'];

        // Date format: "30 Jul 26"
        $date = ! empty( $atts['date'] ) ? esc_html( $atts['date'] ) : ( $post_id ? get_the_date( 'd M y', $post_id ) : date( 'd M y' ) );

        // Permalinks & Sharing URLs
        $permalink = $post_id ? get_permalink( $post_id ) : ( isset( $_SERVER['HTTP_HOST'] ) ? ( ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http' ) . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ) : '' );
        $encoded_url = rawurlencode( $permalink );
        $encoded_title = rawurlencode( html_entity_decode( $title, ENT_QUOTES, 'UTF-8' ) );

        $fb_url = 'https://www.facebook.com/sharer/sharer.php?u=' . $encoded_url;
        $li_url = 'https://www.linkedin.com/sharing/share-offsite/?url=' . $encoded_url;
        $wa_url = 'https://api.whatsapp.com/send?text=' . $encoded_title . '%20' . $encoded_url;
        $x_url  = 'https://twitter.com/intent/tweet?url=' . $encoded_url . '&text=' . $encoded_title;

        ob_start();
        ?>
        <?php $post_slug = $post_id ? get_post_field( 'post_name', $post_id ) : ''; ?>
        <div class="cmg-blog-header-wrapper" data-post-id="<?php echo esc_attr( $post_id ); ?>" data-blog-slug="<?php echo esc_attr( $post_slug ); ?>">
          <style>
            body.single-post .page-header,
          body.single-post .entry-header,
          body.single-post h1.entry-title,
          body.single-post header.entry-header {
            display: none !important;
          }

          /* Remove second / duplicate featured image in post body */
          .cmg-blog-content .cmg-blog-featured-image-wrap,
          .cmg-blog-content > .wp-block-post-featured-image,
          .cmg-blog-content > figure:first-child.wp-block-image,
          .cmg-blog-content > p:first-child img[class*="wp-image-"] {
            display: none !important;
          }

          /* Bring bullet points, numbers & special list characters inside container */
          .cmg-blog-content ul,
          .cmg-blog-content ol,
          .page-content.cmg-blog-content ul,
          .page-content.cmg-blog-content ol,
          article.cmg-blog-single-article ul,
          article.cmg-blog-single-article ol,
          .single-post .page-content ul,
          .single-post .page-content ol {
            padding-left: 0 !important;
            margin-left: 0 !important;
            margin-top: 16px !important;
            margin-bottom: 0px !important;
            list-style-position: inside !important;
            box-sizing: border-box !important;
          }

          .cmg-blog-content ul li,
          .cmg-blog-content ol li,
          .page-content.cmg-blog-content ul li,
          .page-content.cmg-blog-content ol li,
          article.cmg-blog-single-article ul li,
          article.cmg-blog-single-article ol li,
          .single-post .page-content ul li,
          .single-post .page-content ol li {
            margin-bottom: 10px !important;
            line-height: 1.75 !important;
            padding-left: 4px !important;
          }

          /* Blockquotes and special characters inside container */
          .cmg-blog-content blockquote,
          article.cmg-blog-single-article blockquote,
          .single-post .page-content blockquote {
            padding: 16px 24px !important;
            margin: 24px 0 !important;
            border-left: 4px solid #3a7dff !important;
            background: #f8fafc !important;
            border-radius: 0 8px 8px 0 !important;
            box-sizing: border-box !important;
          }

          /* Article page only: H3 margin-top 20px */
          body.single-post .cmg-blog-content h3,
          body.single-post .cmg-blog-content h3.wp-block-heading,
          body.single-post .page-content.cmg-blog-content h3,
          body.single-post article.cmg-blog-single-article .cmg-blog-content h3,
          body.single-post article.cmg-blog-single-article .page-content h3,
          body.single-post h3.wp-block-heading {
            margin-top: 20px !important;
          }

          .cmg-blog-header-wrapper {
              font-family: var(--primary-font, 'Onest', sans-serif);
              max-width: 1100px;
              margin: 0 auto;
              padding: 24px 20px 20px 20px;
              box-sizing: border-box;
              color: #111827;
            }

            /* Author & Date Row */
            .cmg-blog-author-date-row {
              display: flex;
              justify-content: space-between;
              align-items: center;
              margin-bottom: 24px;
            }

            .cmg-blog-author-meta {
              display: flex;
              align-items: center;
              gap: 12px;
            }

            .cmg-blog-author-avatar {
              width: 44px;
              height: 44px;
              border-radius: 50% !important;
              overflow: hidden !important;
              background: #f3f4f6 !important;
              border: none !important;
              flex-shrink: 0;
              display: flex !important;
              align-items: center !important;
              justify-content: center !important;
              box-sizing: border-box !important;
            }

            .cmg-blog-author-avatar img {
              width: 100% !important;
              height: 100% !important;
              object-fit: contain !important;
              border-radius: 50% !important;
              display: block !important;
              padding: 2px !important;
              box-sizing: border-box !important;
            }

            .cmg-blog-author-info {
              display: flex;
              flex-direction: column;
              justify-content: center;
            }

            .cmg-blog-author-name {
              font-size: 15px;
              font-weight: 700;
              color: rgb(22, 28, 82);
              margin: 0;
              line-height: 1.25;
            }

            .cmg-blog-author-role {
              font-size: 13px;
              font-weight: 400;
              color: #6b7280;
              margin: 2px 0 0 0;
              line-height: 1.2;
            }

            .cmg-blog-post-date {
              font-size: 14px;
              font-weight: 500;
              color: #6b7280;
              text-align: right;
              white-space: nowrap;
            }

            /* Post Title */
            .cmg-blog-post-title {
              font-size: 42px;
              font-weight: 600;
              color: rgb(22, 28, 82);
              line-height: 1.24;
              letter-spacing: -0.5px;
              margin: 0 0 32px 0;
              word-wrap: break-word;
            }

            @media (max-width: 768px) {
              .cmg-blog-post-title {
                font-size: 28px;
                line-height: 1.3;
                margin-bottom: 24px;
              }
            }

            /* Reviews & Share Row */
            .cmg-blog-meta-action-row {
              display: flex;
              justify-content: space-between;
              align-items: flex-end;
              padding-top: 4px;
              padding-bottom: 24px;
              margin-bottom: 32px;
              border-bottom: 1px solid #f0f2f5;
            }

            @media (max-width: 640px) {
              .cmg-blog-meta-action-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
              }
            }

            /* Reviews Column */
            .cmg-blog-reviews-col {
              display: flex;
              flex-direction: column;
              gap: 6px;
            }

            .cmg-blog-reviews-label {
              font-size: 11px;
              font-weight: 700;
              color: #374151;
              letter-spacing: 0.8px;
              text-transform: uppercase;
              margin: 0;
            }

            .cmg-blog-rating-wrap {
              display: flex;
              align-items: center;
              gap: 4px;
            }

            .cmg-stars-list {
              display: inline-flex;
              align-items: center;
              gap: 3px;
              cursor: pointer;
            }

            .cmg-star {
              width: 18px;
              height: 18px;
              fill: none;
              stroke: #9ca3af;
              stroke-width: 1.6;
              transition: all 0.15s ease;
            }

            .cmg-star.active,
            .cmg-star.hovered {
              fill: #f59e0b;
              stroke: #f59e0b;
            }

            .cmg-rating-score {
              font-size: 14px;
              font-weight: 600;
              color: #1f2937;
              margin-left: 6px;
            }

            .cmg-rating-text {
              font-size: 14px;
              font-weight: 500;
              color: #3a7dff;
              text-decoration: none;
              cursor: pointer;
              margin-left: 2px;
            }

            .cmg-rating-text:hover {
              text-decoration: underline;
            }

            /* Share Column */
            .cmg-blog-share-col {
              display: flex;
              flex-direction: column;
              gap: 8px;
              align-items: flex-end;
            }

            @media (max-width: 640px) {
              .cmg-blog-share-col {
                align-items: flex-start;
              }
            }

            .cmg-blog-share-label {
              font-size: 13px;
              font-weight: 500;
              color: #4b5563;
              margin: 0;
              line-height: 1.2;
            }

            .cmg-blog-share-buttons {
              display: flex;
              align-items: center;
              gap: 8px;
            }

            .cmg-share-btn {
              width: 32px;
              height: 32px;
              border-radius: 50%;
              background: #f3f4f6;
              display: flex;
              align-items: center;
              justify-content: center;
              color: #4b5563;
              text-decoration: none;
              transition: all 0.2s ease;
              border: none;
              outline: none;
              cursor: pointer;
            }

            .cmg-share-btn svg {
              width: 15px;
              height: 15px;
              fill: currentColor;
              display: block;
            }

            .cmg-share-btn.wa svg {
              width: 16px;
              height: 16px;
            }

            .cmg-share-btn:hover {
              transform: translateY(-2px);
            }

            .cmg-share-btn.fb:hover {
              background: #1877f2;
              color: #ffffff;
            }

            .cmg-share-btn.li:hover {
              background: #0a66c2;
              color: #ffffff;
            }

            .cmg-share-btn.wa:hover {
              background: #25d366;
              color: #ffffff;
            }

            .cmg-share-btn.x:hover {
              background: #000000;
              color: #ffffff;
            }
          </style>

          <!-- Top Row: Author & Date -->
          <div class="cmg-blog-author-date-row">
            <div class="cmg-blog-author-meta">
              <div class="cmg-blog-author-avatar">
                <img src="<?php echo esc_url( $avatar_url ); ?>" alt="<?php echo esc_attr( $author_name ); ?>">
              </div>
              <div class="cmg-blog-author-info">
                <span class="cmg-blog-author-name"><?php echo $author_name; ?></span>
                <span class="cmg-blog-author-role"><?php echo $author_role; ?></span>
              </div>
            </div>
            <div class="cmg-blog-post-date">
              <?php echo $date; ?>
            </div>
          </div>

          <!-- Middle: Post Title -->
          <h1 class="cmg-blog-post-title"><?php echo $title; ?></h1>

          <!-- Bottom Row: Reviews & Share -->
          <div class="cmg-blog-meta-action-row">
            <!-- Reviews Column -->
            <div class="cmg-blog-reviews-col">
              <span class="cmg-blog-reviews-label">REVIEWS</span>
              <div class="cmg-blog-rating-wrap">
                <div class="cmg-stars-list" data-post-id="<?php echo esc_attr( $post_id ); ?>" title="Rate this article">
                  <svg class="cmg-star" data-index="1" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                  <svg class="cmg-star" data-index="2" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                  <svg class="cmg-star" data-index="3" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                  <svg class="cmg-star" data-index="4" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                  <svg class="cmg-star" data-index="5" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                </div>
                <span class="cmg-rating-score">0.0 (0)</span>
                <span class="cmg-rating-text">Rating</span>
              </div>
            </div>

            <!-- Social Share Column -->
            <div class="cmg-blog-share-col">
              <span class="cmg-blog-share-label">Share the post</span>
              <div class="cmg-blog-share-buttons">
                <!-- Facebook -->
                <a href="<?php echo esc_url( $fb_url ); ?>" target="_blank" rel="noopener noreferrer" class="cmg-share-btn fb" aria-label="Share on Facebook" title="Share on Facebook">
                  <svg viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </a>
                <!-- LinkedIn -->
                <a href="<?php echo esc_url( $li_url ); ?>" target="_blank" rel="noopener noreferrer" class="cmg-share-btn li" aria-label="Share on LinkedIn" title="Share on LinkedIn">
                  <svg viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                </a>
                <!-- WhatsApp -->
                <a href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener noreferrer" class="cmg-share-btn wa" aria-label="Share on WhatsApp" title="Share on WhatsApp">
                  <svg viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                </a>
                <!-- X (Twitter) -->
                <a href="<?php echo esc_url( $x_url ); ?>" target="_blank" rel="noopener noreferrer" class="cmg-share-btn x" aria-label="Share on X" title="Share on X">
                  <svg viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                </a>
              </div>
            </div>
          </div>
        </div>

        <?php echo cmg_render_blog_ratings_script(); ?>
        <?php
        return ob_get_clean();
    }
}

/* Register Shortcodes for Elementor / Gutenberg / Widgets */
if ( ! function_exists( 'cmg_blog_header_shortcode' ) ) {
    function cmg_blog_header_shortcode( $atts ) {
        return cmg_render_blog_header( (array) $atts );
    }
}
add_shortcode( 'cmg_blog_header', 'cmg_blog_header_shortcode' );
add_shortcode( 'blog_header', 'cmg_blog_header_shortcode' );
add_shortcode( 'cmg_post_header', 'cmg_blog_header_shortcode' );
add_shortcode( 'blog_meta', 'cmg_blog_header_shortcode' );

/* ==========================================================================
   CMG BLOG BOTTOM REVIEW & SHARE SECTION (After Content End)
   ========================================================================== */
if ( ! function_exists( 'cmg_render_blog_bottom_review_share' ) ) {
    function cmg_render_blog_bottom_review_share() {
        if ( ! is_singular( 'post' ) ) {
            return '';
        }

        $post_id   = get_the_ID();
        $permalink = get_permalink( $post_id );
        $title     = get_the_title( $post_id );

        // Social Share URLs
        $fb_url = 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode( $permalink );
        $li_url = 'https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode( $permalink );
        $wa_url = 'https://api.whatsapp.com/send?text=' . rawurlencode( $title . ' ' . $permalink );
        $x_url  = 'https://twitter.com/intent/tweet?url=' . rawurlencode( $permalink ) . '&text=' . rawurlencode( $title );

        ob_start();
        ?>
        <?php $post_slug = $post_id ? get_post_field( 'post_name', $post_id ) : ''; ?>
        <div class="cmg-blog-bottom-review-share-wrap" data-post-id="<?php echo esc_attr( $post_id ); ?>" data-blog-slug="<?php echo esc_attr( $post_slug ); ?>">
          <style>
            .cmg-blog-bottom-review-share-wrap {
              margin-top: 40px;
              margin-bottom: 24px;
              padding: 24px 0;
              border-top: 1px solid #e5e7eb;
              border-bottom: 1px solid #e5e7eb;
              font-family: var(--primary-font, 'Onest', sans-serif);
              box-sizing: border-box;
              clear: both;
            }

            .cmg-blog-bottom-review-share-wrap .cmg-blog-meta-action-row {
              margin-bottom: 0 !important;
              padding-bottom: 0 !important;
              border-bottom: none !important;
            }

            .cmg-blog-bottom-review-share-wrap .cmg-blog-share-buttons {
              display: flex;
              align-items: center;
              gap: 8px;
            }

            .cmg-blog-bottom-review-share-wrap .cmg-share-btn,
            .cmg-blog-content .cmg-blog-bottom-review-share-wrap a.cmg-share-btn {
              width: 32px !important;
              height: 32px !important;
              border-radius: 50% !important;
              background: #f3f4f6 !important;
              display: flex !important;
              align-items: center !important;
              justify-content: center !important;
              color: #4b5563 !important;
              text-decoration: none !important;
              transition: all 0.2s ease !important;
              border: none !important;
              outline: none !important;
              cursor: pointer !important;
              box-shadow: none !important;
            }

            .cmg-blog-bottom-review-share-wrap .cmg-share-btn svg,
            .cmg-blog-content .cmg-blog-bottom-review-share-wrap a.cmg-share-btn svg {
              width: 15px !important;
              height: 15px !important;
              fill: #4b5563 !important;
              color: #4b5563 !important;
              display: block !important;
            }

            .cmg-blog-bottom-review-share-wrap .cmg-share-btn.wa svg,
            .cmg-blog-content .cmg-blog-bottom-review-share-wrap a.cmg-share-btn.wa svg {
              width: 16px !important;
              height: 16px !important;
            }

            .cmg-blog-bottom-review-share-wrap .cmg-share-btn:hover,
            .cmg-blog-content .cmg-blog-bottom-review-share-wrap a.cmg-share-btn:hover {
              transform: translateY(-2px) !important;
            }

            .cmg-blog-bottom-review-share-wrap .cmg-share-btn.fb:hover,
            .cmg-blog-content .cmg-blog-bottom-review-share-wrap a.cmg-share-btn.fb:hover {
              background: #1877f2 !important;
              color: #ffffff !important;
            }
            .cmg-blog-bottom-review-share-wrap .cmg-share-btn.fb:hover svg,
            .cmg-blog-content .cmg-blog-bottom-review-share-wrap a.cmg-share-btn.fb:hover svg {
              fill: #ffffff !important;
            }

            .cmg-blog-bottom-review-share-wrap .cmg-share-btn.li:hover,
            .cmg-blog-content .cmg-blog-bottom-review-share-wrap a.cmg-share-btn.li:hover {
              background: #0a66c2 !important;
              color: #ffffff !important;
            }
            .cmg-blog-bottom-review-share-wrap .cmg-share-btn.li:hover svg,
            .cmg-blog-content .cmg-blog-bottom-review-share-wrap a.cmg-share-btn.li:hover svg {
              fill: #ffffff !important;
            }

            .cmg-blog-bottom-review-share-wrap .cmg-share-btn.wa:hover,
            .cmg-blog-content .cmg-blog-bottom-review-share-wrap a.cmg-share-btn.wa:hover {
              background: #25d366 !important;
              color: #ffffff !important;
            }
            .cmg-blog-bottom-review-share-wrap .cmg-share-btn.wa:hover svg,
            .cmg-blog-content .cmg-blog-bottom-review-share-wrap a.cmg-share-btn.wa:hover svg {
              fill: #ffffff !important;
            }

            .cmg-blog-bottom-review-share-wrap .cmg-share-btn.x:hover,
            .cmg-blog-content .cmg-blog-bottom-review-share-wrap a.cmg-share-btn.x:hover {
              background: #000000 !important;
              color: #ffffff !important;
            }
            .cmg-blog-bottom-review-share-wrap .cmg-share-btn.x:hover svg,
            .cmg-blog-content .cmg-blog-bottom-review-share-wrap a.cmg-share-btn.x:hover svg {
              fill: #ffffff !important;
            }
          </style>

          <div class="cmg-blog-meta-action-row">
            <!-- Reviews Column -->
            <div class="cmg-blog-reviews-col">
              <span class="cmg-blog-reviews-label">REVIEWS</span>
              <div class="cmg-blog-rating-wrap">
                <div class="cmg-stars-list" data-post-id="<?php echo esc_attr( $post_id ); ?>" title="Rate this article">
                  <svg class="cmg-star" data-index="1" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                  <svg class="cmg-star" data-index="2" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                  <svg class="cmg-star" data-index="3" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                  <svg class="cmg-star" data-index="4" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                  <svg class="cmg-star" data-index="5" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                </div>
                <span class="cmg-rating-score">0.0 (0)</span>
                <span class="cmg-rating-text">Rating</span>
              </div>
            </div>

            <!-- Social Share Column -->
            <div class="cmg-blog-share-col">
              <span class="cmg-blog-share-label">Share the post</span>
              <div class="cmg-blog-share-buttons">
                <!-- Facebook -->
                <a href="<?php echo esc_url( $fb_url ); ?>" target="_blank" rel="noopener noreferrer" class="cmg-share-btn fb" aria-label="Share on Facebook" title="Share on Facebook">
                  <svg viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </a>
                <!-- LinkedIn -->
                <a href="<?php echo esc_url( $li_url ); ?>" target="_blank" rel="noopener noreferrer" class="cmg-share-btn li" aria-label="Share on LinkedIn" title="Share on LinkedIn">
                  <svg viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                </a>
                <!-- WhatsApp -->
                <a href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener noreferrer" class="cmg-share-btn wa" aria-label="Share on WhatsApp" title="Share on WhatsApp">
                  <svg viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                </a>
                <!-- X (Twitter) -->
                <a href="<?php echo esc_url( $x_url ); ?>" target="_blank" rel="noopener noreferrer" class="cmg-share-btn x" aria-label="Share on X" title="Share on X">
                  <svg viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                </a>
              </div>
            </div>
          </div>
        </div>
        <?php echo cmg_render_blog_ratings_script(); ?>
        <?php
        return ob_get_clean();
    }
}
add_shortcode( 'cmg_bottom_review_share', 'cmg_render_blog_bottom_review_share' );
add_shortcode( 'cmg_review_share', 'cmg_render_blog_bottom_review_share' );


/* Hide Hello Elementor duplicate default title on single posts when our custom header is rendered */
add_filter( 'hello_elementor_page_title', function( $title ) {
    if ( is_singular( 'post' ) ) {
        return false;
    }
    return $title;
}, 20 );

/* Automatic injection fallback for single blog posts */
if ( ! function_exists( 'cmg_auto_inject_blog_header' ) ) {
    function cmg_auto_inject_blog_header( $content ) {
        if ( is_singular( 'post' ) && in_the_loop() && is_main_query() && ! is_admin() ) {
            if ( empty( $GLOBALS['cmg_blog_header_already_rendered'] ) ) {
                $header = cmg_render_blog_header();
                return $header . $content;
            }
        }
        return $content;
    }
}
add_filter( 'the_content', 'cmg_auto_inject_blog_header', 10 );

/* Remove duplicate 2nd featured image from post body content */
if ( ! function_exists( 'cmg_remove_duplicate_content_featured_image' ) ) {
    function cmg_remove_duplicate_content_featured_image( $content ) {
        if ( is_singular( 'post' ) && in_the_loop() && is_main_query() && ! is_admin() ) {
            $post_id = get_the_ID();
            $thumb_id = get_post_thumbnail_id( $post_id );
            $thumb_url = get_the_post_thumbnail_url( $post_id, 'full' );

            // 1. Remove duplicate cmg-blog-featured-image-wrap if present inside content
            $content = preg_replace(
                '#<div class="cmg-blog-featured-image-wrap"[^>]*>.*?</div>#is',
                '',
                $content
            );

            // 2. If content starts with an image matching the featured image or attachment ID
            if ( $thumb_id ) {
                $content = preg_replace(
                    '#<(figure|p|div)[^>]*>\s*<img[^>]*wp-image-' . $thumb_id . '[^>]*>\s*(?:<figcaption[^>]*>.*?</figcaption>\s*)?</\1>#is',
                    '',
                    $content,
                    1
                );
            }

            if ( $thumb_url ) {
                $filename = pathinfo( parse_url( $thumb_url, PHP_URL_PATH ), PATHINFO_FILENAME );
                if ( ! empty( $filename ) && strlen( $filename ) > 3 ) {
                    $escaped_fn = preg_quote( $filename, '#' );
                    $content = preg_replace(
                        '#<(figure|p|div)[^>]*>\s*<img[^>]*' . $escaped_fn . '[^>]*>\s*(?:<figcaption[^>]*>.*?</figcaption>\s*)?</\1>#is',
                        '',
                        $content,
                        1
                    );
                }
            }

            // 3. If the very first element in content is a standalone image tag or figure
            $content = preg_replace(
                '#^\s*<(figure|p|div)[^>]*class="[^"]*(?:wp-block-image|attachment-post-thumbnail|featured-image)[^"]*"[^>]*>\s*<img[^>]*>\s*(?:<figcaption[^>]*>.*?</figcaption>\s*)?</\1>#is',
                '',
                $content
            );
        }
        return $content;
    }
}
add_filter( 'the_content', 'cmg_remove_duplicate_content_featured_image', 20 );

/* ==========================================================================
   CMG BLOG TOP AUDIT BANNER ("Find What's Hurting Your Website. Instantly!")
   ========================================================================== */

if ( ! function_exists( 'cmg_render_blog_top_banner' ) ) {
    function cmg_render_blog_top_banner( $atts = array() ) {
        $audit_url = ! empty( $atts['url'] ) ? esc_url( $atts['url'] ) : esc_url( home_url( '/lex/' ) );
        $btn_text  = ! empty( $atts['btn_text'] ) ? esc_html( $atts['btn_text'] ) : 'Get My Free AI Audit &rarr;';

        ob_start();
        ?>
        <div class="cmg-audit-banner-wrapper">
          <style>
            .cmg-audit-banner-wrapper {
              max-width: 1100px;
              margin: 30px auto 20px auto;
              padding: 0 20px;
              box-sizing: border-box;
            }

            .cmg-audit-banner-inner {
              background: #0b112c;
              border-radius: 16px;
              padding: 26px 36px;
              display: flex;
              align-items: center;
              justify-content: space-between;
              gap: 24px;
              box-shadow: 0 10px 30px rgba(11, 17, 44, 0.12);
              border: 1px solid rgba(255, 255, 255, 0.05);
            }

            .cmg-audit-banner-text {
              display: flex;
              flex-direction: column;
              gap: 6px;
            }

            .cmg-audit-banner-heading {
              color: #ffffff;
              font-size: 25px;
              font-weight: 700;
              margin: 0;
              line-height: 1.3;
              letter-spacing: -0.3px;
              font-family: var(--primary-font, 'Onest', sans-serif);
            }

            .cmg-audit-highlight {
              color: #22c55e;
              font-weight: 700;
            }

            .cmg-audit-banner-subtext {
              color: #94a3b8;
              font-size: 14.5px;
              margin: 0;
              line-height: 1.4;
              font-family: var(--primary-font, 'Onest', sans-serif);
            }

            .cmg-audit-lex {
              color: #ffffff;
              font-weight: 600;
              display: inline-flex;
              align-items: center;
              gap: 4px;
            }

            .cmg-audit-sparkle {
              vertical-align: middle;
            }

            .cmg-audit-banner-action {
              flex-shrink: 0;
            }

            .cmg-audit-btn {
              display: inline-flex;
              align-items: center;
              justify-content: center;
              gap: 6px;
              background: #22c55e;
              color: #ffffff !important;
              font-size: 15px;
              font-weight: 600;
              padding: 13px 26px;
              border-radius: 999px;
              text-decoration: none !important;
              white-space: nowrap;
              transition: all 0.2s ease;
              box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35);
              font-family: var(--primary-font, 'Onest', sans-serif);
            }

            .cmg-audit-btn:hover {
              background: #16a34a;
              transform: translateY(-2px);
              box-shadow: 0 6px 20px rgba(34, 197, 94, 0.45);
              color: #ffffff !important;
            }

            @media (max-width: 860px) {
              .cmg-audit-banner-inner {
                flex-direction: column;
                align-items: flex-start;
                padding: 22px 24px;
                gap: 18px;
              }

              .cmg-audit-banner-heading {
                font-size: 21px;
              }

              .cmg-audit-banner-subtext {
                font-size: 13.5px;
              }

              .cmg-audit-banner-action {
                width: 100%;
              }

              .cmg-audit-btn {
                width: 100%;
                box-sizing: border-box;
              }
            }
          </style>

          <div class="cmg-audit-banner-inner">
            <div class="cmg-audit-banner-text">
              <h2 class="cmg-audit-banner-heading">
                Find What's Hurting Your Website. <span class="cmg-audit-highlight">Instantly!</span>
              </h2>
              <p class="cmg-audit-banner-subtext">
                Get a comprehensive report with actionable recommendations: powered by <span class="cmg-audit-lex"><svg class="cmg-audit-sparkle" viewBox="0 0 24 24" width="13" height="13" fill="#38bdf8"><path d="M12 0l2.5 9.5L24 12l-9.5 2.5L12 24l-2.5-9.5L0 12l9.5-2.5z"/></svg> Lex</span>
              </p>
            </div>
            <div class="cmg-audit-banner-action">
              <a href="<?php echo $audit_url; ?>" id="blog-detail-banner-try-cmgalaxy" class="cmg-audit-btn">
                <?php echo $btn_text; ?>
              </a>
            </div>
          </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

add_shortcode( 'cmg_blog_top_banner', 'cmg_render_blog_top_banner' );
add_shortcode( 'blog_top_banner', 'cmg_render_blog_top_banner' );
add_shortcode( 'audit_banner', 'cmg_render_blog_top_banner' );

/* ==========================================================================
   CMG FLOATING SIDE BANNER ("Rank Better in ChatGPT, Claude & Perplexity")
   Appears after user scrolls down ~400px on single blog posts
   ========================================================================== */

if ( ! function_exists( 'cmg_render_floating_side_banner' ) ) {
    function cmg_render_floating_side_banner() {
        if ( ! is_singular( 'post' ) ) {
            return '';
        }

        $audit_url = 'https://geo.cmgalaxy.com/';
        $banner_img_url = 'https://cdn.prod.website-files.com/69898e17b118c06479cfe8cb/698abdec0d5a26c786205421_audit%20straight.svg';

        ob_start();
        ?>
        <aside class="cmg-blog-right-sidebar" id="cmg-blog-right-sidebar" aria-label="Sidebar banner">
          <div class="cmg-floating-side-banner" id="cmg-floating-side-banner">
            <style>
              .cmg-blog-right-sidebar {
                width: 160px;
                min-width: 160px;
                max-width: 160px;
                flex-shrink: 0;
                align-self: stretch;
                margin: 0 !important;
                padding: 0 !important;
                box-sizing: border-box;
                position: relative;
              }

              .cmg-floating-side-banner {
                position: sticky;
                top: 110px;
                width: 160px;
                min-width: 160px;
                flex-shrink: 0;
                align-self: flex-start;
                margin: 0 !important;
                background: transparent;
                border-radius: 20px !important;
                padding: 0;
                box-shadow: none;
                box-sizing: border-box;
                text-align: center;
                z-index: 20;
                opacity: 0;
                visibility: hidden;
                pointer-events: none;
                transform: translateY(16px);
                transition: opacity 0.35s ease, transform 0.35s ease, visibility 0.35s ease;
              }

              .cmg-floating-side-banner.is-visible {
                opacity: 1 !important;
                visibility: visible !important;
                pointer-events: auto !important;
                transform: translateY(0) !important;
              }

              .cmg-floating-side-banner.is-hidden {
                display: none !important;
                opacity: 0 !important;
                visibility: hidden !important;
                pointer-events: none !important;
              }

              .cmg-side-banner-close {
                position: absolute;
                top: 8px;
                right: 8px;
                background: rgba(15, 23, 42, 0.6);
                border: none !important;
                color: #ffffff !important;
                font-size: 16px;
                line-height: 1;
                cursor: pointer;
                width: 24px;
                height: 24px;
                border-radius: 50% !important;
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 30;
                transition: background 0.2s, transform 0.15s;
                outline: none !important;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25) !important;
                padding: 0;
                -webkit-tap-highlight-color: transparent !important;
              }

              .cmg-side-banner-close:hover,
              .cmg-side-banner-close:focus {
                background: rgba(15, 23, 42, 0.95);
                color: #ffffff !important;
                transform: scale(1.1);
              }

              .cmg-side-banner-link {
                display: block;
                text-decoration: none !important;
                border-radius: 20px !important;
                overflow: hidden !important;
                transition: transform 0.25s ease, filter 0.25s ease;
                outline: none !important;
                line-height: 0;
              }

              .cmg-side-banner-link:hover {
                transform: translateY(-2px);
                filter: brightness(1.03);
              }

              .cmg-side-banner-img {
                width: 160px;
                height: 540px;
                max-width: 100%;
                height: auto;
                display: block;
                border-radius: 20px !important;
                box-shadow: 0 8px 24px rgba(9, 16, 43, 0.15);
              }

              /* Hide on mobile/tablet */
              @media (max-width: 1024px) {
                .cmg-blog-right-sidebar {
                  display: none !important;
                  visibility: hidden !important;
                  width: 0 !important;
                  height: 0 !important;
                  padding: 0 !important;
                  margin: 0 !important;
                }
                .cmg-floating-side-banner {
                  display: none !important;
                  visibility: hidden !important;
                  opacity: 0 !important;
                  pointer-events: none !important;
                }
              }
            </style>

            <button type="button" class="cmg-side-banner-close" id="cmg-side-banner-close-btn" onclick="cmgDismissSideBanner(event);" aria-label="Close side banner">&times;</button>

            <a href="<?php echo esc_url( $audit_url ); ?>" id="blog-detail-sidebar-geo-audit" target="_blank" rel="noopener noreferrer" class="cmg-side-banner-link">
              <img src="<?php echo esc_url( $banner_img_url ); ?>" alt="Free GEO Audit - Rank Better in ChatGPT, Claude &amp; Perplexity" width="160" height="540" class="cmg-side-banner-img" />
            </a>
          </div>
        </aside>

        <script>
        (function() {
          var isDismissed = false;
          var banner = document.getElementById('cmg-floating-side-banner');
          var closeBtn = document.getElementById('cmg-side-banner-close-btn');

          window.cmgDismissSideBanner = function(e) {
            if (e) {
              if (e.stopPropagation) e.stopPropagation();
              if (e.preventDefault) e.preventDefault();
            }
            isDismissed = true;
            if (banner) {
              banner.classList.remove('is-visible');
              banner.classList.add('is-hidden');
              banner.style.setProperty('display', 'none', 'important');
            }
          };

          if (closeBtn) {
            closeBtn.addEventListener('click', function(e) {
              window.cmgDismissSideBanner(e);
            });
          }

          function checkScroll() {
            if (isDismissed || !banner) return;
            var scrollY = window.pageYOffset || document.documentElement.scrollTop || 0;
            if (scrollY > 3000) {
              if (!banner.classList.contains('is-visible') && !banner.classList.contains('is-hidden')) {
                banner.classList.add('is-visible');
              }
            } else {
              if (banner.classList.contains('is-visible')) {
                banner.classList.remove('is-visible');
              }
            }
          }

          window.addEventListener('scroll', checkScroll, { passive: true });
          if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', checkScroll);
          } else {
            checkScroll();
          }
        })();
        </script>
        <?php
        return ob_get_clean();
    }
}
/* Removed: banner now rendered inline in single.php as sticky sidebar column
add_action( 'wp_footer', function() {
    if ( is_singular( 'post' ) && function_exists( 'cmg_render_floating_side_banner' ) ) {
        echo cmg_render_floating_side_banner();
    }
}, 30 );
*/

/* Shortcodes */
add_shortcode( 'cmg_side_banner', 'cmg_render_floating_side_banner' );
add_shortcode( 'cmg_floating_banner', 'cmg_render_floating_side_banner' );
add_shortcode( 'side_banner', 'cmg_render_floating_side_banner' );

/* ==========================================================================
   CMG BLOG AUTHOR BIO BOX (Fully Dynamic from Post / Author Profile)
   ========================================================================== */

if ( ! function_exists( 'cmg_render_blog_author_bio' ) ) {
    function cmg_render_blog_author_bio( $atts = array() ) {
        if ( ! is_singular( 'post' ) && empty( $atts['force'] ) ) {
            return '';
        }

        static $bio_rendered = false;
        if ( $bio_rendered && empty( $atts['force'] ) ) {
            return '';
        }
        if ( ! empty( $GLOBALS['cmg_blog_author_bio_already_rendered'] ) && empty( $atts['force'] ) ) {
            return '';
        }
        $bio_rendered = true;
        $GLOBALS['cmg_blog_author_bio_already_rendered'] = true;

        global $post;
        $post_id   = ( $post && isset( $post->ID ) ) ? $post->ID : get_the_ID();

        // Fully dynamic author data from Edit Post / Custom Fields / User Profile
        $author_data  = cmg_get_post_author_data( $post_id, $atts );
        $author_name  = $author_data['name'];
        $avatar_url   = $author_data['avatar'];
        $author_bio   = $author_data['bio'];
        $linkedin_url = $author_data['linkedin'];

        ob_start();
        ?>
        <div class="cmg-author-bio-container">
          <style>
            .cmg-author-bio-container {
              position: relative;
              max-width: 1100px;
              margin: 60px auto 40px auto;
              padding: 0 20px;
              box-sizing: border-box;
              text-align: center;
              font-family: var(--primary-font, 'Onest', sans-serif);
            }

            .cmg-author-bio-line {
              position: absolute;
              top: 45px;
              left: 20px;
              right: 20px;
              height: 1px;
              background: #e5e7eb;
              z-index: 1;
            }

            .cmg-author-avatar-badge {
              position: relative;
              display: inline-block;
              background: #ffffff;
              padding: 0 16px;
              z-index: 2;
              border-radius: 50%;
            }

            .cmg-author-avatar-img {
              width: 72px;
              height: 72px;
              border-radius: 50% !important;
              object-fit: contain !important;
              display: block !important;
              margin: 0 auto;
              border: none !important;
              background: #f3f4f6 !important;
              padding: 4px !important;
              box-sizing: border-box !important;
            }

            .cmg-author-bio-name {
              font-size: 20px;
              font-weight: 700;
              color: #111827;
              margin: 16px 0 10px 0;
              line-height: 1.3;
              letter-spacing: -0.2px;
            }

            .cmg-author-bio-text {
              font-size: 15px;
              line-height: 1.65;
              color: #4b5563;
              max-width: 820px;
              margin: 0 auto 16px auto;
              font-weight: 400;
            }

            .cmg-author-social-wrap {
              display: flex;
              align-items: center;
              justify-content: center;
              gap: 12px;
              margin-top: 6px;
            }

            .cmg-author-li-link {
              display: inline-flex;
              align-items: center;
              justify-content: center;
              width: 32px;
              height: 32px;
              border-radius: 50%;
              background: #f1f5f9;
              color: #0a66c2;
              text-decoration: none;
              transition: all 0.2s ease;
              box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            }

            .cmg-author-li-link:hover {
              background: #0a66c2;
              color: #ffffff;
              transform: translateY(-2px);
              box-shadow: 0 4px 12px rgba(10, 102, 194, 0.3);
            }

            .cmg-author-li-link svg {
              width: 15px;
              height: 15px;
              fill: currentColor;
              transition: fill 0.2s ease;
            }

            @media (max-width: 640px) {
              .cmg-author-bio-container {
                margin: 45px auto 30px auto;
              }

              .cmg-author-bio-text {
                font-size: 14px;
                line-height: 1.6;
              }
            }
          </style>

          <div class="cmg-author-bio-line"></div>

          <?php if ( ! empty( $avatar_url ) ) : ?>
          <div class="cmg-author-avatar-badge">
            <img src="<?php echo esc_url( $avatar_url ); ?>" alt="<?php echo esc_attr( $author_name ); ?>" class="cmg-author-avatar-img" loading="lazy" />
          </div>
          <?php endif; ?>

          <?php if ( ! empty( $author_name ) ) : ?>
          <h3 class="cmg-author-bio-name"><?php echo esc_html( $author_name ); ?></h3>
          <?php endif; ?>

          <?php if ( ! empty( $author_bio ) ) : ?>
          <p class="cmg-author-bio-text"><?php echo $author_bio; ?></p>
          <?php endif; ?>

          <?php if ( ! empty( $linkedin_url ) ) : ?>
          <div class="cmg-author-social-wrap">
            <a href="<?php echo esc_url( $linkedin_url ); ?>" target="_blank" rel="noopener noreferrer" class="cmg-author-li-link" aria-label="<?php echo esc_attr( $author_name ); ?> Profile">
              <svg viewBox="0 0 24 24">
                <path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/>
              </svg>
            </a>
          </div>
          <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}

add_shortcode( 'cmg_author_bio', 'cmg_render_blog_author_bio' );
add_shortcode( 'cmg_user_banner', 'cmg_render_blog_author_bio' );
add_shortcode( 'author_bio', 'cmg_render_blog_author_bio' );

/* ==========================================================================
   CMG BLOG BOTTOM GROWTH BANNER ("See How We Helped Businesses Like Yours Grow 3x Faster.")
   ========================================================================== */

if ( ! function_exists( 'cmg_render_blog_bottom_growth_banner' ) ) {
    function cmg_render_blog_bottom_growth_banner( $atts = array() ) {
        if ( ! is_singular( 'post' ) && empty( $atts['force'] ) ) {
            return '';
        }

        static $growth_rendered = false;
        if ( $growth_rendered && empty( $atts['force'] ) ) {
            return '';
        }
        if ( ! empty( $GLOBALS['cmg_blog_bottom_banner_already_rendered'] ) && empty( $atts['force'] ) ) {
            return '';
        }
        $growth_rendered = true;
        $GLOBALS['cmg_blog_bottom_banner_already_rendered'] = true;

        $try_url  = ! empty( $atts['try_url'] ) ? esc_url( $atts['try_url'] ) : 'https://app.cmgalaxy.com';
        $demo_url = ! empty( $atts['demo_url'] ) ? esc_url( $atts['demo_url'] ) : 'https://www.cmgalaxy.com/demo';

        ob_start();
        ?>
        <div class="cmg-bottom-growth-banner-wrap">
          <style>
            .cmg-bottom-growth-banner-wrap {
              max-width: 1100px;
              margin: 50px auto 70px auto;
              padding: 0 20px;
              box-sizing: border-box;
              font-family: var(--primary-font, 'Onest', sans-serif);
            }

            .cmg-bottom-growth-card {
              background: #0c1538;
              background: linear-gradient(135deg, #0e173e 0%, #0a112c 100%);
              border-radius: 24px;
              padding: 48px 56px;
              display: flex;
              align-items: center;
              justify-content: space-between;
              gap: 36px;
              box-shadow: none;
              position: relative;
              overflow: hidden;
              box-sizing: border-box;
            }

            /* Subtle decorative background glow */
            .cmg-bottom-growth-card::before {
              content: "";
              position: absolute;
              top: -60px;
              right: 15%;
              width: 260px;
              height: 260px;
              background: radial-gradient(circle, rgba(56, 189, 248, 0.1) 0%, rgba(56, 189, 248, 0) 70%);
              border-radius: 50%;
              pointer-events: none;
            }

            .cmg-bottom-growth-text {
              flex: 1 1 auto;
              z-index: 2;
            }

            .cmg-bottom-growth-heading {
              font-size: 38px;
              line-height: 1.22;
              font-weight: 700;
              color: #ffffff;
              margin: 0 0 14px 0;
              letter-spacing: -0.5px;
            }

            .cmg-bottom-growth-highlight {
              color: #22c55e;
            }

            .cmg-bottom-growth-subtext {
              font-size: 16px;
              line-height: 1.55;
              color: #cbd5e1;
              margin: 0;
              max-width: 520px;
              font-weight: 400;
            }

            .cmg-bottom-growth-actions {
              display: flex;
              align-items: center;
              gap: 16px;
              flex-shrink: 0;
              z-index: 2;
            }

            .cmg-growth-btn-try {
              display: inline-flex;
              align-items: center;
              justify-content: center;
              background: #ffffff;
              color: #0c1538;
              font-size: 15px;
              font-weight: 600;
              padding: 14px 30px;
              border-radius: 9999px;
              text-decoration: none;
              white-space: nowrap;
              transition: all 0.2s ease;
              box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
            }

            .cmg-growth-btn-try:hover {
              background: #f8fafc;
              color: #0c1538;
              transform: translateY(-2px);
              box-shadow: 0 8px 22px rgba(0, 0, 0, 0.25);
            }

            .cmg-growth-btn-demo {
              display: inline-flex;
              align-items: center;
              justify-content: center;
              background: #22c55e;
              color: #ffffff;
              font-size: 15px;
              font-weight: 600;
              padding: 14px 30px;
              border-radius: 9999px;
              text-decoration: none;
              white-space: nowrap;
              transition: all 0.2s ease;
              box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35);
            }

            .cmg-growth-btn-demo:hover {
              background: #16a34a;
              color: #ffffff;
              transform: translateY(-2px);
              box-shadow: 0 8px 22px rgba(34, 197, 94, 0.45);
            }

            @media (max-width: 960px) {
              .cmg-bottom-growth-card {
                flex-direction: column;
                align-items: flex-start;
                padding: 38px 32px;
                gap: 28px;
              }

              .cmg-bottom-growth-heading {
                font-size: 30px;
              }

              .cmg-bottom-growth-subtext {
                font-size: 15px;
              }

              .cmg-bottom-growth-actions {
                width: 100%;
                display: flex;
                flex-direction: row;
                gap: 12px;
                flex-wrap: wrap;
              }

              .cmg-growth-btn-try,
              .cmg-growth-btn-demo {
                flex: 1 1 auto;
                text-align: center;
                justify-content: center;
                min-width: 140px;
                padding: 13px 20px;
              }
            }

            @media (max-width: 580px) {
              .cmg-bottom-growth-banner-wrap {
                margin: 40px auto 50px auto;
                padding: 0 16px;
              }

              .cmg-bottom-growth-card {
                padding: 28px 22px;
                border-radius: 18px;
                gap: 22px;
              }

              .cmg-bottom-growth-heading {
                font-size: 24px;
                line-height: 1.25;
              }

              .cmg-bottom-growth-actions {
                flex-direction: column;
                width: 100%;
                gap: 10px;
              }

              .cmg-growth-btn-try,
              .cmg-growth-btn-demo {
                width: 100%;
                box-sizing: border-box;
              }
            }
          </style>

          <div class="cmg-bottom-growth-card">
            <div class="cmg-bottom-growth-text">
              <h2 class="cmg-bottom-growth-heading">
                See How We Helped<br>
                Businesses Like Yours<br>
                <span class="cmg-bottom-growth-highlight">Grow 3x</span> Faster.
              </h2>
              <p class="cmg-bottom-growth-subtext">
                Let’s build a performance-driven ad strategy that works for your business.
              </p>
            </div>
            <div class="cmg-bottom-growth-actions">
              <a href="<?php echo $try_url; ?>" id="blog-detail-banner-try-cmgalaxy" class="cmg-growth-btn-try">
                Try CMGalaxy
              </a>
              <a href="<?php echo $demo_url; ?>" id="blog-detail-footer-book-demo" class="cmg-growth-btn-demo">
                Book a demo
              </a>
            </div>
          </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

add_shortcode( 'cmg_bottom_banner', 'cmg_render_blog_bottom_growth_banner' );
add_shortcode( 'cmg_growth_banner', 'cmg_render_blog_bottom_growth_banner' );
add_shortcode( 'bottom_banner', 'cmg_render_blog_bottom_growth_banner' );

/* ==========================================================================
   DISABLE COMMENTS / "LEAVE A REPLY" ON SINGLE BLOG POSTS
   ========================================================================== */
add_filter( 'comments_open', function( $open, $post_id = null ) {
    if ( is_singular( 'post' ) || ( $post_id && get_post_type( $post_id ) === 'post' ) ) {
        return false;
    }
    return $open;
}, 50, 2 );

add_filter( 'pings_open', function( $open, $post_id = null ) {
    if ( is_singular( 'post' ) || ( $post_id && get_post_type( $post_id ) === 'post' ) ) {
        return false;
    }
    return $open;
}, 50, 2 );

add_action( 'wp_head', function() {
    if ( is_singular( 'post' ) ) {
        echo '<style id="cmg-hide-comments">
            body.single-post #comments,
            body.single-post #respond,
            body.single-post .comment-respond,
            body.single-post .comments-area,
            body.single-post .comments-wrapper,
            body.single-post .entry-comments {
                display: none !important;
            }
        </style>';
    }
}, 50 );

/* ==========================================================================
   CMG BLOG RELATED ARTICLES SECTION
   ========================================================================== */

if ( ! function_exists( 'cmg_render_blog_related_articles' ) ) {
    function cmg_render_blog_related_articles( $atts = array() ) {
        if ( ! is_singular( 'post' ) && empty( $atts['force'] ) ) {
            return '';
        }

        static $related_rendered = false;
        if ( $related_rendered && empty( $atts['force'] ) ) {
            return '';
        }
        if ( ! empty( $GLOBALS['cmg_blog_related_articles_already_rendered'] ) && empty( $atts['force'] ) ) {
            return '';
        }
        $related_rendered = true;
        $GLOBALS['cmg_blog_related_articles_already_rendered'] = true;

        global $post;
        $current_id = ( $post && isset( $post->ID ) ) ? $post->ID : get_the_ID();

        $articles = array();

        // 1. Query related posts from WordPress
        $categories = $current_id ? wp_get_post_categories( $current_id ) : array();
        $query_args = array(
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'posts_per_page' => 3,
            'post__not_in'   => $current_id ? array( $current_id ) : array(),
            'orderby'        => 'date',
            'order'          => 'DESC',
        );
        if ( ! empty( $categories ) ) {
            $query_args['category__in'] = $categories;
        }

        $query = new WP_Query( $query_args );

        // Fallback to recent posts if no category posts found
        if ( ! $query->have_posts() || $query->post_count < 3 ) {
            $fallback_args = array(
                'post_type'      => 'post',
                'post_status'    => 'publish',
                'posts_per_page' => 3,
                'post__not_in'   => $current_id ? array( $current_id ) : array(),
                'orderby'        => 'date',
                'order'          => 'DESC',
            );
            $query = new WP_Query( $fallback_args );
        }

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $pid   = get_the_ID();
                $thumb = get_the_post_thumbnail_url( $pid, 'large' );
                $articles[] = array(
                    'title'     => get_the_title(),
                    'permalink' => get_permalink(),
                    'date'      => get_the_date( 'F j, Y' ),
                    'image'     => $thumb,
                );
            }
            wp_reset_postdata();
        }

        // Curated fallback articles from the production blog to guarantee 3 complete cards
        $curated_fallbacks = array(
            array(
                'title'     => 'Ideal Customer Profile Bucketing: Do You Really Know Your Audience?',
                'permalink' => home_url( '/blog/ideal-customer-profile-bucketing-do-you-really-know-your-audience' ),
                'date'      => 'August 24, 2026',
                'image'     => 'https://cdn.prod.website-files.com/67b5e5b17dee6e1ed91f1014/6a8c212b7eab4bd21b88c912_Feature%20image24.jpg',
            ),
            array(
                'title'     => 'The Shift from Vanity Metrics to Value Metrics in Digital Marketing',
                'permalink' => home_url( '/blog/the-shift-from-vanity-metrics-to-value-metrics-in-digital-marketing' ),
                'date'      => 'July 30, 2026',
                'image'     => 'https://cdn.prod.website-files.com/67b5e5b17dee6e1ed91f1014/6a6ae2469c4c58ed4776a782_Feature%20Image.jpg%20(5).jpeg',
            ),
            array(
                'title'     => 'The Marketer’s Time Trap: How Marketing Automation Frees You to Focus on Strategy',
                'permalink' => home_url( '/blog/the-marketers-time-trap-how-marketing-automation-frees-you-to-focus-on-strategy' ),
                'date'      => 'July 27, 2026',
                'image'     => 'https://cdn.prod.website-files.com/67b5e5b17dee6e1ed91f1014/6a8276f7a6a4220b22a6aa87_Feature%20Image%20(6).jpg',
            ),
        );

        // Fill up to 3 cards if WP has fewer published posts
        $fallback_idx = 0;
        while ( count( $articles ) < 3 && $fallback_idx < count( $curated_fallbacks ) ) {
            $fb = $curated_fallbacks[ $fallback_idx ];
            if ( ! $current_id || stripos( get_the_title( $current_id ), substr( $fb['title'], 0, 15 ) ) === false ) {
                $articles[] = $fb;
            }
            $fallback_idx++;
        }

        // Fill images for any WP posts missing a featured image
        foreach ( $articles as $k => $art ) {
            if ( empty( $art['image'] ) ) {
                $articles[$k]['image'] = $curated_fallbacks[$k % 3]['image'];
            }
        }

        $view_all_url = home_url( '/blog/' );

        ob_start();
        ?>
        <div class="cmg-related-articles-section">
          <style>
            .cmg-related-articles-section {
              max-width: 1280px;
              margin: 70px auto 40px auto;
              padding: 0 20px;
              box-sizing: border-box;
              font-family: var(--primary-font, 'Onest', sans-serif);
            }

            .cmg-related-header {
              display: flex;
              align-items: center;
              justify-content: space-between;
              margin-bottom: 36px;
              gap: 20px;
            }

            .cmg-related-heading {
              font-size: 42px;
              font-weight: 700;
              color: #161c52;
              margin: 0;
              line-height: 1.2;
              letter-spacing: -0.5px;
            }

            .cmg-related-view-all-btn {
              display: inline-flex;
              align-items: center;
              justify-content: center;
              padding: 11px 26px;
              border: 1.5px solid #161c52;
              border-radius: 9999px;
              background: transparent;
              color: #161c52;
              font-size: 14.5px;
              font-weight: 600;
              text-decoration: none;
              white-space: nowrap;
              transition: all 0.25s ease;
            }

            .cmg-related-view-all-btn:hover {
              background: #161c52;
              color: #ffffff;
              transform: translateY(-2px);
              box-shadow: 0 4px 14px rgba(22, 28, 82, 0.2);
            }

            .cmg-related-grid {
              display: grid;
              grid-template-columns: repeat(3, 1fr);
              gap: 36px;
            }

            .cmg-related-card {
              display: flex;
              flex-direction: column;
              text-decoration: none;
              color: inherit;
              border-radius: 16px;
              transition: transform 0.25s ease;
            }

            .cmg-related-card:hover {
              transform: translateY(-4px);
            }

            .cmg-related-thumb-wrap {
              width: 100%;
              aspect-ratio: 16 / 9;
              border-radius: 20px;
              overflow: hidden;
              background: #e2e8f0;
              margin-bottom: 16px;
              box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            }

            .cmg-related-thumb {
              width: 100%;
              height: 100%;
              object-fit: cover;
              display: block;
              transition: transform 0.35s ease;
            }

            .cmg-related-card:hover .cmg-related-thumb {
              transform: scale(1.04);
            }

            .cmg-related-date {
              font-size: 14px;
              font-weight: 500;
              color: #64748b;
              margin: 0 0 10px 0;
            }

            .cmg-related-title {
              font-size: 18px;
              font-weight: 700;
              color: #0f172a;
              line-height: 1.36;
              margin: 0;
              display: -webkit-box;
              -webkit-line-clamp: 3;
              -webkit-box-orient: vertical;
              overflow: hidden;
              transition: color 0.2s ease;
            }

            .cmg-related-card:hover .cmg-related-title {
              color: #2563eb;
            }

            @media (max-width: 900px) {
              .cmg-related-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 24px;
              }

              .cmg-related-heading {
                font-size: 30px;
              }
            }

            @media (max-width: 600px) {
              .cmg-related-articles-section {
                margin: 50px auto 30px auto;
              }

              .cmg-related-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
                margin-bottom: 24px;
              }

              .cmg-related-heading {
                font-size: 26px;
              }

              .cmg-related-view-all-btn {
                width: 100%;
                box-sizing: border-box;
                text-align: center;
              }

              .cmg-related-grid {
                grid-template-columns: 1fr;
                gap: 24px;
              }
            }
          </style>

          <div class="cmg-related-header">
            <h2 class="cmg-related-heading">Related Articles</h2>
            <a href="<?php echo esc_url( $view_all_url ); ?>" class="cmg-related-view-all-btn" id="blog-detail-related-view-all">
              View All Articles
            </a>
          </div>

          <div class="cmg-related-grid">
            <?php foreach ( $articles as $item ) : ?>
            <a href="<?php echo esc_url( $item['permalink'] ); ?>" class="cmg-related-card">
              <div class="cmg-related-thumb-wrap">
                <img src="<?php echo esc_url( $item['image'] ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>" class="cmg-related-thumb" loading="lazy" />
              </div>
              <p class="cmg-related-date"><?php echo esc_html( $item['date'] ); ?></p>
              <h3 class="cmg-related-title"><?php echo esc_html( $item['title'] ); ?></h3>
            </a>
            <?php endforeach; ?>
          </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

add_shortcode( 'cmg_related_articles', 'cmg_render_blog_related_articles' );
add_shortcode( 'related_articles', 'cmg_render_blog_related_articles' );
add_shortcode( 'cmg_blog_related', 'cmg_render_blog_related_articles' );

/* ==========================================================================
   CMG BLOG TABLE OF CONTENTS (Desktop: Left Sticky Sidebar | Mobile: Docy-Style Bottom TOC & Button)
   ========================================================================== */

if ( ! function_exists( 'cmg_render_blog_toc_sidebar' ) ) {
    function cmg_render_blog_toc_sidebar() {
        if ( ! is_singular( 'post' ) ) {
            return '';
        }

        ob_start();
        ?>
        <aside class="cmg-blog-toc-sidebar" id="cmg-blog-toc-sidebar" aria-label="Table of contents">
          <style>
            /* Override Elementor parent container + WP content area - scoped to blog single article and wrapper only */
            body.single-post #content.site-main,
            body.single-post article.cmg-blog-single-article,
            body.single-post .entry-content {
              max-width: 100% !important;
              width: 100% !important;
              padding-left: 0 !important;
              padding-right: 0 !important;
            }

            /* Prevent horizontal scroll from vw trick */
            html, body { overflow-x: clip !important; }

            /* Desktop 3-column flex layout */
            .cmg-blog-layout-wrapper {
              width: 100vw;
              max-width: 100vw;
              position: relative;
              left: 50%;
              margin-left: -50vw;
              margin-right: -50vw;
              padding: 0 24px 0 24px;
              box-sizing: border-box;
              display: flex;
              align-items: flex-start;
              justify-content: space-between;
              gap: 0;
            }

            /* Center blog content container, text remains left-aligned */
            .cmg-blog-content {
              flex: 1 1 auto !important;
              min-width: 0 !important;
              max-width: 960px !important;
              margin: 0 !important;
              padding-left: 20px !important;
              padding-right: 20px !important;
              box-sizing: border-box !important;
              text-align: left !important;
            }

            /* Desktop left sticky sidebar - reduced width & left aligned */
            .cmg-blog-toc-sidebar {
              margin-left: 0 !important;
              width: 220px !important;
              min-width: 190px !important;
              max-width: 230px !important;
              position: sticky;
              top: 110px;
              max-height: calc(100vh - 140px);
              overflow-y: auto;
              box-sizing: border-box;
              z-index: 20;
              text-align: left;
              font-family: var(--primary-font, 'Onest', sans-serif);
            }

            /* Custom slim scrollbar for TOC */
            .cmg-blog-toc-sidebar::-webkit-scrollbar {
              width: 4px;
            }
            .cmg-blog-toc-sidebar::-webkit-scrollbar-thumb {
              background: #cbd5e1;
              border-radius: 4px;
            }

            .cmg-blog-toc-inner {
              padding-right: 8px;
            }

            .cmg-blog-toc-header {
              display: flex;
              align-items: center;
              justify-content: flex-start;
              gap: 8px;
              text-align: left;
              margin-bottom: 8px;
              padding-bottom: 6px;
              border-bottom: 1px solid #e5e7eb;
            }

            .cmg-blog-toc-title-wrap {
              display: flex;
              align-items: center;
              gap: 8px;
            }

            .cmg-blog-toc-title {
              font-size: 13px;
              font-weight: 700;
              text-transform: uppercase;
              letter-spacing: 0.8px;
              color: #64748b;
            }

            .cmg-toc-icon {
              color: #2563eb;
            }

            .cmg-toc-count-pill {
              display: none;
              background: #e2e8f0;
              color: #475569;
              font-size: 11px;
              font-weight: 600;
              padding: 2px 8px;
              border-radius: 999px;
            }

            .cmg-toc-mobile-toggle-btn {
              display: inline-flex !important;
              align-items: center;
              gap: 5px;
              background: #f1f5f9 !important;
              background-color: #f1f5f9 !important;
              color: #475569 !important;
              font-size: 11.5px;
              font-weight: 600;
              padding: 4px 10px;
              border-radius: 999px;
              border: 1px solid #e2e8f0 !important;
              cursor: pointer;
              transition: all 0.2s ease;
              outline: none !important;
              box-shadow: none !important;
            }

            .cmg-toc-mobile-toggle-btn:hover,
            .cmg-toc-mobile-toggle-btn:focus,
            .cmg-toc-mobile-toggle-btn:active,
            .cmg-blog-toc-header:hover .cmg-toc-mobile-toggle-btn {
              background: #e2e8f0 !important;
              background-color: #e2e8f0 !important;
              color: #0f172a !important;
              outline: none !important;
              box-shadow: none !important;
            }

            .cmg-toc-mobile-toggle-btn svg {
              transition: transform 0.25s ease;
            }

            .cmg-blog-toc-header {
              display: flex;
              align-items: center;
              justify-content: flex-start;
              gap: 8px;
              text-align: left;
              margin-bottom: 8px;
              padding-bottom: 6px;
              border-bottom: 1px solid #e5e7eb;
              cursor: pointer;
              user-select: none;
            }

            .cmg-blog-toc-header:hover .cmg-toc-mobile-toggle-btn {
              background: #e2e8f0 !important;
            }

            .cmg-blog-toc-header.is-expanded .cmg-toc-mobile-toggle-btn svg {
              transform: rotate(180deg);
            }

            /* Nav container & 2-title blur preview */
            .cmg-blog-toc-nav {
              position: relative;
              transition: all 0.3s ease;
            }

            /* When collapsed: show first 2 titles clearly, hide rest (no blur) */
            .cmg-blog-toc-nav.is-collapsed .cmg-blog-toc-item:nth-child(1),
            .cmg-blog-toc-nav.is-collapsed .cmg-blog-toc-item:nth-child(2) {
              display: block !important;
              filter: none !important;
              opacity: 1 !important;
              max-height: none !important;
              pointer-events: auto !important;
            }

            .cmg-blog-toc-nav.is-collapsed .cmg-blog-toc-item:nth-child(n+3) {
              display: none !important;
            }

            /* When expanded: show all items sharp & interactive */
            .cmg-blog-toc-nav:not(.is-collapsed) .cmg-blog-toc-item {
              display: block !important;
              max-height: none !important;
              filter: none !important;
              opacity: 1 !important;
              pointer-events: auto !important;
              user-select: auto !important;
              transition: filter 0.25s ease, opacity 0.25s ease;
            }

            /* Show more in text form at bottom - left aligned */
            .cmg-toc-show-more-wrap {
              margin-top: 6px !important;
              padding-top: 0 !important;
              margin-bottom: 0 !important;
              padding-bottom: 0 !important;
              display: flex !important;
              justify-content: flex-start !important;
              align-items: center !important;
              width: 100% !important;
              text-align: left !important;
            }

            .cmg-toc-show-more-btn {
              background: transparent !important;
              background-color: transparent !important;
              border: none !important;
              box-shadow: none !important;
              outline: none !important;
              padding: 3px 6px !important;
              color: #2563eb !important;
              font-size: 12px !important;
              line-height: 1.2 !important;
              font-weight: 600 !important;
              cursor: pointer !important;
              display: inline-flex !important;
              justify-content: flex-start !important;
              align-items: center !important;
              margin: 0 !important;
              gap: 5px !important;
              text-decoration: none !important;
              font-family: inherit !important;
              border-radius: 4px !important;
              transition: color 0.2s ease !important;
              -webkit-tap-highlight-color: transparent !important;
            }

            .cmg-toc-show-more-btn:hover,
            .cmg-toc-show-more-btn:focus,
            .cmg-toc-show-more-btn:active {
              color: #1d4ed8 !important;
              background: transparent !important;
              background-color: transparent !important;
              text-decoration: underline !important;
              box-shadow: none !important;
              outline: none !important;
            }

            .cmg-toc-show-more-btn svg {
              transition: transform 0.25s ease;
            }

            .cmg-blog-toc-list,
            ul.cmg-blog-toc-list,
            article.cmg-blog-single-article ul.cmg-blog-toc-list,
            .single-post ul.cmg-blog-toc-list {
              list-style: none !important;
              margin: 0 !important;
              margin-top: 0 !important;
              margin-bottom: 0 !important;
              padding: 0 !important;
              padding-left: 0 !important;
              display: flex;
              flex-direction: column;
              gap: 0 !important;
            }

            .cmg-blog-toc-item,
            li.cmg-blog-toc-item,
            article.cmg-blog-single-article ul.cmg-blog-toc-list li,
            .single-post ul.cmg-blog-toc-list li {
              list-style: none !important;
              margin: 0 !important;
              margin-top: 0 !important;
              margin-bottom: 6px !important;
              padding: 0 !important;
              list-style: none !important;
            }

            .cmg-blog-toc-link {
              display: block;
              padding: 3px 8px !important;
              font-size: 12px;
              line-height: 1.35;
              color: #475569 !important;
              text-decoration: none;
              border: none !important;
              border-left: none !important;
              border-right: none !important;
              background: transparent !important;
              transition: all 0.2s ease;
              -webkit-tap-highlight-color: transparent !important;
              outline: none !important;
            }

            .cmg-blog-toc-link:hover {
              color: #000000 !important;
              background: transparent !important;
              border: none !important;
              border-left: none !important;
              border-right: none !important;
            }

            .cmg-blog-toc-link:active,
            .cmg-blog-toc-link:focus,
            .cmg-blog-toc-link:focus-visible,
            .cmg-blog-toc-link.is-active {
              color: #000000 !important;
              font-weight: 700 !important;
              background: transparent !important;
              border: none !important;
              border-left: none !important;
              border-right: none !important;
              outline: none !important;
            }

            .cmg-blog-content h2 {
              scroll-margin-top: 110px;
            }

            /* Floating button & bottom sheet hidden on desktop */
            .cmg-docy-toc-fab {
              display: none;
            }
            .cmg-docy-sheet-overlay, .cmg-docy-sheet {
              display: none;
            }

            /* ==========================================================================
               MOBILE RESPONSIVE BREAKPOINT (<= 1024px)
               - Only top CTA banner visible
               - Side CTA banner hidden
               - TOC moves to bottom with toggle button (Docy style)
               - Floating Docy-style action button at bottom for instant TOC access
               ========================================================================== */
            @media (max-width: 1024px) {
              /* Reset desktop full-width breakout to standard responsive container */
              .cmg-blog-layout-wrapper {
                display: flex !important;
                flex-direction: column !important;
                width: 100% !important;
                max-width: 100% !important;
                left: 0 !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
                padding: 0 16px !important;
                gap: 0 !important;
                box-sizing: border-box !important;
              }

              /* Main blog content: first in order, full width */
              .cmg-blog-content {
                order: 1 !important;
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 !important;
                box-sizing: border-box !important;
                font-size: 16px !important;
                line-height: 1.7 !important;
              }

              /* Side banner: strictly hidden on mobile/tablet */
              .cmg-floating-side-banner {
                display: none !important;
                visibility: hidden !important;
                opacity: 0 !important;
                pointer-events: none !important;
              }

              /* TOC sidebar moves to BOTTOM of article */
              .cmg-blog-toc-sidebar {
                order: 2 !important;
                position: static !important;
                width: 100% !important;
                min-width: 0 !important;
                max-width: 100% !important;
                margin: 36px 0 24px 0 !important;
                max-height: none !important;
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 14px;
                padding: 0 !important;
                box-sizing: border-box !important;
                box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
                overflow: hidden !important;
              }

              .cmg-blog-toc-inner {
                padding: 14px 18px !important;
              }

              .cmg-blog-toc-header {
                display: flex !important;
                align-items: center !important;
                justify-content: space-between !important;
                margin-bottom: 12px !important;
                padding-bottom: 10px !important;
                border-bottom: 1px solid #e2e8f0 !important;
                cursor: pointer;
                user-select: none;
              }

              .cmg-toc-count-pill {
                display: inline-block !important;
              }

              .cmg-toc-mobile-toggle-btn {
                display: none !important;
              }

              .cmg-toc-mobile-toggle-btn svg {
                transition: transform 0.25s ease;
              }

              .cmg-blog-toc-header.is-expanded .cmg-toc-mobile-toggle-btn svg {
                transform: rotate(180deg);
              }

              .cmg-blog-toc-link {
                padding: 10px 14px;
                font-size: 15px;
                background: #ffffff;
                border: 1px solid #edf2f7;
                border-left: 3px solid #cbd5e1;
                border-radius: 8px;
                margin-bottom: 3px;
              }

              .cmg-blog-toc-link.is-active {
                border-left: none !important;
                border-right: none !important;
                background: transparent !important;
                color: #000000 !important;
                font-weight: 700 !important;
              }

              /* Docy-Style Floating Action Button at Bottom */
              .cmg-docy-toc-fab {
                display: inline-flex !important;
                position: fixed;
                bottom: 22px;
                right: 18px;
                z-index: 9999;
                align-items: center;
                gap: 8px;
                background: #09102b;
                color: #ffffff;
                border: 1px solid rgba(255, 255, 255, 0.2);
                border-radius: 999px;
                padding: 10px 18px;
                font-size: 13.5px;
                font-weight: 600;
                box-shadow: 0 8px 24px rgba(9, 16, 43, 0.4), 0 2px 6px rgba(0, 0, 0, 0.15);
                cursor: pointer;
                backdrop-filter: blur(8px);
                -webkit-backdrop-filter: blur(8px);
                transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
                font-family: var(--primary-font, 'Onest', sans-serif);
              }

              .cmg-docy-toc-fab:active {
                transform: scale(0.96);
              }

              .cmg-docy-fab-icon {
                color: #22c55e;
              }

              .cmg-docy-fab-badge {
                background: rgba(34, 197, 94, 0.25);
                color: #22c55e;
                font-size: 11px;
                font-weight: 700;
                padding: 1px 7px;
                border-radius: 999px;
              }

              /* Docy-Style Bottom Sheet Drawer & Overlay */
              .cmg-docy-sheet-overlay {
                display: block !important;
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.6);
                backdrop-filter: blur(4px);
                -webkit-backdrop-filter: blur(4px);
                z-index: 99998;
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.3s ease;
              }

              .cmg-docy-sheet-overlay.is-active {
                opacity: 1;
                pointer-events: auto;
              }

              .cmg-docy-sheet {
                display: flex !important;
                flex-direction: column;
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                max-height: 80vh;
                background: #ffffff;
                border-radius: 20px 20px 0 0;
                z-index: 99999;
                box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.25);
                transform: translateY(100%);
                transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
                font-family: var(--primary-font, 'Onest', sans-serif);
                box-sizing: border-box;
                overflow: hidden;
              }

              .cmg-docy-sheet.is-active {
                transform: translateY(0);
              }

              .cmg-docy-sheet-handle {
                width: 40px;
                height: 4.5px;
                background: #cbd5e1;
                border-radius: 999px;
                margin: 10px auto 4px auto;
                flex-shrink: 0;
              }

              .cmg-docy-sheet-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 12px 20px 14px 20px;
                border-bottom: 1px solid #e2e8f0;
                flex-shrink: 0;
              }

              .cmg-docy-sheet-title {
                font-size: 15px;
                font-weight: 700;
                color: #0f172a;
                display: flex;
                align-items: center;
                gap: 8px;
              }

              .cmg-docy-sheet-close {
                background: #f1f5f9;
                border: none;
                color: #64748b;
                width: 32px;
                height: 32px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 20px;
                cursor: pointer;
                line-height: 1;
                transition: background 0.2s, color 0.2s;
              }

              .cmg-docy-sheet-close:hover {
                background: #e2e8f0;
                color: #0f172a;
              }

              .cmg-docy-sheet-body {
                padding: 14px 18px 32px 18px;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
                max-height: calc(80vh - 80px);
                box-sizing: border-box;
              }

              .cmg-docy-sheet-list {
                list-style: none !important;
                margin: 0 !important;
                padding: 0 !important;
                display: flex;
                flex-direction: column;
                gap: 6px;
              }

              .cmg-docy-sheet-link {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 11px 14px;
                font-size: 14.5px;
                line-height: 1.4;
                color: #334155;
                text-decoration: none;
                border-radius: 10px;
                background: #f8fafc;
                border: 1px solid #edf2f7;
                transition: all 0.2s ease;
              }

              .cmg-docy-sheet-link {
                -webkit-tap-highlight-color: transparent !important;
                outline: none !important;
              }
              .cmg-docy-sheet-link:hover, .cmg-docy-sheet-link.is-active,
              .cmg-docy-sheet-link:active, .cmg-docy-sheet-link:focus {
                color: #0f172a !important;
                background: #eef2ff !important;
                border-color: #c7d2fe !important;
                font-weight: 600;
                outline: none !important;
              }
            }
          </style>

          <div class="cmg-blog-toc-inner">
            <div class="cmg-blog-toc-header" id="cmg-blog-toc-header">
              <div class="cmg-blog-toc-title-wrap">
                <svg class="cmg-toc-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="8" y1="6" x2="21" y2="6"></line>
                  <line x1="8" y1="12" x2="21" y2="12"></line>
                  <line x1="8" y1="18" x2="21" y2="18"></line>
                  <line x1="3" y1="6" x2="3.01" y2="6"></line>
                  <line x1="3" y1="12" x2="3.01" y2="12"></line>
                  <line x1="3" y1="18" x2="3.01" y2="18"></line>
                </svg>
                <span class="cmg-blog-toc-title">Table of Contents</span>
                <span class="cmg-toc-count-pill" id="cmg-toc-count-pill">0</span>
              </div>
            </div>

            <nav class="cmg-blog-toc-nav is-collapsed" id="cmg-blog-toc-nav">
              <ul class="cmg-blog-toc-list" id="cmg-blog-toc-list">
                <!-- Dynamically generated from article H2 elements -->
              </ul>
              <div class="cmg-toc-show-more-wrap" id="cmg-toc-show-more-wrap">
                <button type="button" class="cmg-toc-show-more-btn" id="cmg-toc-show-more-btn" aria-label="Show more table of contents headings">
                  <span class="cmg-toc-show-more-text" id="cmg-toc-show-more-text">Show more</span>
                  <svg class="cmg-toc-show-more-icon" id="cmg-toc-show-more-icon" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="6 9 12 15 18 9"></polyline>
                  </svg>
                </button>
              </div>
            </nav>
          </div>

          <!-- Docy-Style Mobile Floating Action Button -->
          <button type="button" class="cmg-docy-toc-fab" id="cmg-docy-toc-fab" aria-label="Open Table of Contents">
            <svg class="cmg-docy-fab-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="8" y1="6" x2="21" y2="6"></line>
              <line x1="8" y1="12" x2="21" y2="12"></line>
              <line x1="8" y1="18" x2="21" y2="18"></line>
              <line x1="3" y1="6" x2="3.01" y2="6"></line>
              <line x1="3" y1="12" x2="3.01" y2="12"></line>
              <line x1="3" y1="18" x2="3.01" y2="18"></line>
            </svg>
            <span>Table of Contents</span>
            <span class="cmg-docy-fab-badge" id="cmg-docy-fab-badge">0</span>
          </button>

          <!-- Docy-Style Mobile Bottom Sheet Drawer -->
          <div class="cmg-docy-sheet-overlay" id="cmg-docy-sheet-overlay"></div>
          <div class="cmg-docy-sheet" id="cmg-docy-sheet" role="dialog" aria-modal="true" aria-label="Table of Contents">
            <div class="cmg-docy-sheet-handle"></div>
            <div class="cmg-docy-sheet-header">
              <div class="cmg-docy-sheet-title">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="8" y1="6" x2="21" y2="6"></line>
                  <line x1="8" y1="12" x2="21" y2="12"></line>
                  <line x1="8" y1="18" x2="21" y2="18"></line>
                  <line x1="3" y1="6" x2="3.01" y2="6"></line>
                  <line x1="3" y1="12" x2="3.01" y2="12"></line>
                  <line x1="3" y1="18" x2="3.01" y2="18"></line>
                </svg>
                <span>Table of Contents</span>
              </div>
              <button type="button" class="cmg-docy-sheet-close" id="cmg-docy-sheet-close" aria-label="Close">&times;</button>
            </div>
            <div class="cmg-docy-sheet-body">
              <ul class="cmg-docy-sheet-list" id="cmg-docy-sheet-list">
                <!-- Dynamically populated -->
              </ul>
            </div>
          </div>

          <script>
          (function() {
            function initTOC() {
              const content = document.querySelector('.cmg-blog-content');
              const tocList = document.getElementById('cmg-blog-toc-list');
              const tocSidebar = document.getElementById('cmg-blog-toc-sidebar');
              const tocHeader = document.getElementById('cmg-blog-toc-header');
// Top toggle button removed
              const tocNav = document.getElementById('cmg-blog-toc-nav');
              const countPill = document.getElementById('cmg-toc-count-pill');
              const fab = document.getElementById('cmg-docy-toc-fab');
              const fabBadge = document.getElementById('cmg-docy-fab-badge');
              const sheet = document.getElementById('cmg-docy-sheet');
              const sheetOverlay = document.getElementById('cmg-docy-sheet-overlay');
              const sheetClose = document.getElementById('cmg-docy-sheet-close');
              const sheetList = document.getElementById('cmg-docy-sheet-list');

              if (!content || !tocList || !tocSidebar) return;

              // Extract all H2 headings inside .cmg-blog-content
              const headings = content.querySelectorAll('h2');
              if (headings.length === 0) {
                tocSidebar.style.display = 'none';
                if (fab) fab.style.display = 'none';
                return;
              }

              // Update counts
              if (countPill) countPill.textContent = headings.length;
              if (fabBadge) fabBadge.textContent = headings.length;

              tocList.innerHTML = '';
              if (sheetList) sheetList.innerHTML = '';

              headings.forEach(function(h2, index) {
                let id = h2.getAttribute('id');
                if (!id) {
                  const slug = h2.textContent.toLowerCase().trim()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/(^-|-$)/g, '');
                  id = slug || ('section-' + (index + 1));
                  if (document.getElementById(id)) {
                    id += '-' + (index + 1);
                  }
                  h2.setAttribute('id', id);
                }

                h2.style.scrollMarginTop = '110px';

                // In-page list item
                const li = document.createElement('li');
                li.className = 'cmg-blog-toc-item';
                const a = document.createElement('a');
                a.href = '#' + id;
                a.className = 'cmg-blog-toc-link';
                a.textContent = h2.textContent.trim();

                a.addEventListener('click', function(e) {
                  e.preventDefault();
                  links.forEach(function(l) { l.classList.remove('is-active'); });
                  a.classList.add('is-active');
                  a.blur();
                  const target = document.getElementById(id);
                  if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    if (window.history && window.history.pushState) {
                      window.history.pushState(null, '', '#' + id);
                    }
                  }
                });

                li.appendChild(a);
                tocList.appendChild(li);

                // Bottom sheet list item (Docy style)
                if (sheetList) {
                  const sLi = document.createElement('li');
                  const sA = document.createElement('a');
                  sA.href = '#' + id;
                  sA.className = 'cmg-docy-sheet-link';
                  sA.innerHTML = '<span style="color:#2563eb;font-weight:700;">' + (index + 1) + '.</span> <span>' + h2.textContent.trim() + '</span>';

                  sA.addEventListener('click', function(e) {
                    e.preventDefault();
                    closeSheet();
                    const target = document.getElementById(id);
                    if (target) {
                      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                      if (window.history && window.history.pushState) {
                        window.history.pushState(null, '', '#' + id);
                      }
                    }
                  });

                  sLi.appendChild(sA);
                  sheetList.appendChild(sLi);
                }
              });

              // Bottom "Show more" button elements
              const showMoreWrap = document.getElementById('cmg-toc-show-more-wrap');
              const showMoreBtn = document.getElementById('cmg-toc-show-more-btn');
              const showMoreText = document.getElementById('cmg-toc-show-more-text');
              const showMoreIcon = document.getElementById('cmg-toc-show-more-icon');

              // Hide "Show more" button if 2 or fewer headings
              if (headings.length <= 2) {
                if (showMoreWrap) showMoreWrap.style.display = 'none';
                
                if (tocNav) tocNav.classList.remove('is-collapsed');
              }

              // Toggle between 2-titles (blur) and all-titles (expanded)
              function toggleInPageTOC() {
                const isCollapsed = tocNav.classList.toggle('is-collapsed');
                if (showMoreText) {
                  showMoreText.textContent = isCollapsed ? 'Show more' : 'Show less';
                }
                if (showMoreIcon) {
                  showMoreIcon.style.transform = isCollapsed ? 'rotate(0deg)' : 'rotate(180deg)';
                }
                if (tocHeader) {
                  tocHeader.classList.toggle('is-expanded', !isCollapsed);
                  tocHeader.classList.toggle('is-collapsed', isCollapsed);
                }

              }

              if (showMoreBtn) {
                showMoreBtn.addEventListener('click', function(e) {
                  e.preventDefault();
                  e.stopPropagation();
                  toggleInPageTOC();
                });
              }

// Only bottom show more button handles toggle

              // Title header remains completely untouched and standard

              // Docy-style Bottom Sheet Controls
              function openSheet() {
                if (sheet && sheetOverlay) {
                  sheet.classList.add('is-active');
                  sheetOverlay.classList.add('is-active');
                  document.body.style.overflow = 'hidden';
                }
              }

              function closeSheet() {
                if (sheet && sheetOverlay) {
                  sheet.classList.remove('is-active');
                  sheetOverlay.classList.remove('is-active');
                  document.body.style.overflow = '';
                }
              }

              if (fab) {
                fab.addEventListener('click', openSheet);
              }
              if (sheetClose) {
                sheetClose.addEventListener('click', closeSheet);
              }
              if (sheetOverlay) {
                sheetOverlay.addEventListener('click', closeSheet);
              }

              document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeSheet();
              });

              // Resize handler: close sheet if resized to desktop
              window.addEventListener('resize', function() {
                if (window.innerWidth > 1024) {
                  closeSheet();
                }
              });

              // Initial state: by default collapsed / hidden on both desktop & mobile
              if (tocNav) {
                tocNav.classList.add('is-collapsed');
              }
              if (tocHeader) {
                tocHeader.classList.add('is-collapsed');
                tocHeader.classList.remove('is-expanded');
              }


              // Active Scrollspy: highlight bold black as sections come into view
              const links = tocList.querySelectorAll('.cmg-blog-toc-link');
              const sheetLinks = sheetList ? sheetList.querySelectorAll('.cmg-docy-sheet-link') : [];

              function updateActiveSection() {
                let currentId = '';
                const scrollPos = window.scrollY || window.pageYOffset;
                const offset = 140; // Account for sticky header offset

                headings.forEach(function(h2) {
                  const rect = h2.getBoundingClientRect();
                  if (rect.top <= offset) {
                    currentId = h2.getAttribute('id');
                  }
                });

                if (!currentId && headings.length > 0 && scrollPos < 400) {
                  currentId = headings[0].getAttribute('id');
                }

                if (currentId) {
                  links.forEach(function(link) {
                    if (link.getAttribute('href') === '#' + currentId) {
                      link.classList.add('is-active');
                    } else {
                      link.classList.remove('is-active');
                    }
                  });
                  sheetLinks.forEach(function(sLink) {
                    if (sLink.getAttribute('href') === '#' + currentId) {
                      sLink.classList.add('is-active');
                    } else {
                      sLink.classList.remove('is-active');
                    }
                  });
                }
              }

              let isTicking = false;
              window.addEventListener('scroll', function() {
                if (!isTicking) {
                  window.requestAnimationFrame(function() {
                    updateActiveSection();
                    isTicking = false;
                  });
                  isTicking = true;
                }
              }, { passive: true });

              // Run immediately so first section is bold on load
              updateActiveSection();
            }

            if (document.readyState === 'loading') {
              document.addEventListener('DOMContentLoaded', initTOC);
            } else {
              initTOC();
            }
          })();
          </script>
        </aside>
        <?php
        return ob_get_clean();
    }
}

add_shortcode( 'cmg_blog_toc', 'cmg_render_blog_toc_sidebar' );
add_shortcode( 'blog_toc', 'cmg_render_blog_toc_sidebar' );
add_shortcode( 'jump-to-nav', 'cmg_render_blog_toc_sidebar' );
add_shortcode( 'jump_to_nav', 'cmg_render_blog_toc_sidebar' );
add_shortcode( 'jump_nav', 'cmg_render_blog_toc_sidebar' );



/* ==========================================================================
   CMG BLOG RATINGS & IP TRACKER DASHBOARD (Integrated for WP Admin)
   ========================================================================== */
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

    // One-time cleanup of test entries
    $wpdb->query( "DELETE FROM $table WHERE post_id = 2026" );

    $post_id         = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 1341;
    $blog_title      = isset( $_POST['blog_title'] ) ? sanitize_text_field( wp_unslash( $_POST['blog_title'] ) ) : '';
    $rating          = isset( $_POST['rating'] ) ? min( 5, max( 1, intval( $_POST['rating'] ) ) ) : 5;
    $cross_device_id = isset( $_POST['cross_device_id'] ) ? sanitize_text_field( wp_unslash( $_POST['cross_device_id'] ) ) : '';
    $page_url        = isset( $_POST['page_url'] ) ? esc_url_raw( wp_unslash( $_POST['page_url'] ) ) : '';
    $ip_address      = cmg_get_client_ip_address();
    $user_agent      = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ) : '';

    if ( ! $blog_title && $post_id ) {
        $blog_title = get_post_field( 'post_name', $post_id );
    }

    // Check if this device already rated this article - if so, update instead of duplicating
    $existing_id = 0;
    if ( $cross_device_id && $blog_title ) {
        $existing_id = $wpdb->get_var( $wpdb->prepare(
            "SELECT id FROM $table WHERE blog_title = %s AND cross_device_id = %s LIMIT 1",
            $blog_title,
            $cross_device_id
        ) );
    }

    if ( $existing_id ) {
        $wpdb->update(
            $table,
            array(
                'rating'     => $rating,
                'ip_address' => $ip_address,
                'user_agent' => $user_agent,
                'page_url'   => $page_url,
                'created_at' => current_time( 'mysql' ),
            ),
            array( 'id' => $existing_id ),
            array( '%d', '%s', '%s', '%s', '%s' ),
            array( '%d' )
        );
        $record_id = $existing_id;
    } else {
        $wpdb->insert(
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
        $record_id = $wpdb->insert_id;
    }

    if ( $record_id ) {
        wp_send_json_success( array( 'message' => 'Logged successfully', 'id' => $record_id ) );
    } else {
        wp_send_json_error( array( 'message' => 'Failed to log rating' ) );
    }
}

// 3b. Endpoint to fetch ratings from WP Database
add_action( 'wp_ajax_cmg_get_rating', 'cmg_plugin_handle_get_rating' );
add_action( 'wp_ajax_nopriv_cmg_get_rating', 'cmg_plugin_handle_get_rating' );

function cmg_plugin_handle_get_rating() {
    global $wpdb;
    $table = $wpdb->prefix . 'cmg_blog_ratings';

    $blog_title      = isset( $_REQUEST['blog_title'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['blog_title'] ) ) : '';
    $post_id         = isset( $_REQUEST['post_id'] ) ? absint( $_REQUEST['post_id'] ) : 0;
    $cross_device_id = isset( $_REQUEST['cross_device_id'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['cross_device_id'] ) ) : '';

    $where = array();
    $params = array();

    if ( $blog_title ) {
        $where[] = "blog_title = %s";
        $params[] = $blog_title;
    } elseif ( $post_id ) {
        $where[] = "post_id = %d";
        $params[] = $post_id;
    } else {
        wp_send_json_error( array( 'message' => 'Missing identifier' ) );
    }

    $where_sql = $wpdb->prepare( implode( ' AND ', $where ), $params );

    $stats = $wpdb->get_row( "SELECT COUNT(*) as total_rating, AVG(rating) as avg_rating, SUM(rating) as total_rating_sum FROM $table WHERE $where_sql" );

    $user_rating = 0;
    if ( $cross_device_id ) {
        $user_row = $wpdb->get_row( $wpdb->prepare( "SELECT rating FROM $table WHERE $where_sql AND cross_device_id = %s ORDER BY id DESC LIMIT 1", $cross_device_id ) );
        if ( $user_row ) {
            $user_rating = intval( $user_row->rating );
        }
    }

    $count = $stats ? intval( $stats->total_rating ) : 0;
    $avg   = ( $stats && $stats->avg_rating ) ? round( floatval( $stats->avg_rating ), 1 ) : 0.0;

    wp_send_json_success( array(
        'total_rating' => $count,
        'avg_rating'   => $avg,
        'user_rating'  => $user_rating,
        'blog_title'   => $blog_title
    ) );
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
                    <?php
                    $p_obj = null;
                    if ( $r->post_id && $r->post_id != 2026 ) {
                        $p_obj = get_post( $r->post_id );
                    }
                    if ( ! $p_obj && $r->blog_title ) {
                        $p_obj = get_page_by_path( $r->blog_title, OBJECT, 'post' );
                    }
                    $p_title = $p_obj ? $p_obj->post_title : ( $r->blog_title ? ucwords( str_replace( '-', ' ', $r->blog_title ) ) : 'Blog Article' );
                    $p_link  = $p_obj ? get_permalink( $p_obj->ID ) : ( $r->page_url ? $r->page_url : '#' );
                    ?>
                    <a href="<?php echo esc_url( $p_link ); ?>" target="_blank" style="font-weight: 600; text-decoration: none; color: #2563eb; font-size: 14px;">
                      <?php echo esc_html( $p_title ); ?>
                    </a>
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

// Build timestamp: 2026-09-21 09:40

/**
 * CMG: Permanent SVG Protection System
 * Prevents WordPress and plugins (like SVG Support, Safe SVG, Elementor)
 * from stripping clipPath, mask, filter, gradients, and casing on ALL future uploads.
 */
class CMG_SVG_Protector {
    private static $raw_svg_cache = [];
    private static $last_raw_svg = null;

    public static function init() {
        // 1. Ensure SVG mime types and file types are always recognized at highest priority
        add_filter('upload_mimes', [__CLASS__, 'allow_svg_mimes'], PHP_INT_MAX);
        add_filter('wp_check_filetype_and_ext', [__CLASS__, 'check_filetype_and_ext'], PHP_INT_MAX, 4);

        // 2. Enable unfiltered SVG uploads across Elementor, Gutenkit, and Bodhi SVG Support
        add_filter('pre_option_elementor_unfiltered_files_upload', '__return_true');
        add_filter('elementor/files/svg/allow_unfiltered_upload', '__return_true');
        add_filter('elementor/files/svg/sanitizer/is_safe', '__return_true');
        add_filter('elementor/files/svg/validate', '__return_true');
        add_filter('pre_option_gutenkit_unfiltered_files_upload', '__return_true');
        add_filter('gutenkit/libs/unfiltered_file_support/allow_unfiltered_upload', '__return_true');
        add_filter('bodhi_svgs_disable_sanitization', '__return_true');
        add_filter('bodhi_svgs_advanced_mode', '__return_true');

        // 3. Prefilter (Priority 1): Capture untouched raw SVG content and neutralize other plugins' prefilters
        add_filter('wp_handle_upload_prefilter', [__CLASS__, 'on_upload_prefilter'], 1);
        add_filter('wp_handle_sideload_prefilter', [__CLASS__, 'on_upload_prefilter'], 1);

        // 4. Prefilter Cleanup (Highest Priority): Ensure error is 0 and pristine file is preserved
        add_filter('wp_handle_upload_prefilter', [__CLASS__, 'on_upload_prefilter_cleanup'], PHP_INT_MAX);
        add_filter('wp_handle_sideload_prefilter', [__CLASS__, 'on_upload_prefilter_cleanup'], PHP_INT_MAX);

        // 5. Post-upload: Restore original content, guarantee viewBox & xmlns
        add_filter('wp_handle_upload', [__CLASS__, 'on_upload_complete'], 9999);
        add_filter('wp_handle_sideload', [__CLASS__, 'on_upload_complete'], 9999);

        // 6. Dynamically unhook aggressive sanitizers (Gutenkit, Elementor, SVG Support, Safe SVG)
        add_action('admin_init', [__CLASS__, 'remove_plugin_sanitizers'], 999);
        add_action('wp_loaded', [__CLASS__, 'remove_plugin_sanitizers'], 999);

        // 7. Whitelist SVG tags for plugins that support filtering
        add_filter('svg_allowed_tags', [__CLASS__, 'whitelist_tags']);
        add_filter('svg_allowed_attributes', [__CLASS__, 'whitelist_attributes']);
        add_filter('elementor/files/svg/allowed_elements', [__CLASS__, 'whitelist_tags']);
        add_filter('elementor/files/svg/allowed_attributes', [__CLASS__, 'whitelist_attributes']);

        // 8. Fix SVG thumbnails in WordPress Media Library & Elementor
        add_action('admin_head', [__CLASS__, 'admin_svg_styles']);
        add_action('elementor/editor/after_enqueue_styles', [__CLASS__, 'admin_svg_styles']);

        // 9. Calculate and save accurate SVG dimensions for WordPress & Elementor
        add_filter('wp_generate_attachment_metadata', [__CLASS__, 'generate_svg_metadata'], 10, 2);
    }

    public static function allow_svg_mimes($mimes) {
        $mimes['svg']  = 'image/svg+xml';
        $mimes['svgz'] = 'image/svg+xml';
        return $mimes;
    }

    public static function check_filetype_and_ext($data, $file, $filename, $mimes) {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if ($ext === 'svg' || $ext === 'svgz') {
            $data['ext']  = 'svg';
            $data['type'] = 'image/svg+xml';
            $data['proper_filename'] = $filename;
        }
        return $data;
    }

    public static function remove_plugin_sanitizers() {
        global $wp_filter;
        foreach (['wp_handle_upload_prefilter', 'wp_handle_sideload_prefilter'] as $hook_name) {
            if (isset($wp_filter[$hook_name]) && is_object($wp_filter[$hook_name]) && isset($wp_filter[$hook_name]->callbacks)) {
                foreach ($wp_filter[$hook_name]->callbacks as $priority => $callbacks) {
                    foreach ($callbacks as $id => $cb) {
                        $callable = $cb['function'] ?? null;
                        $callable_name = '';
                        if (is_string($callable)) {
                            $callable_name = $callable;
                        } elseif (is_array($callable)) {
                            $class = is_object($callable[0]) ? get_class($callable[0]) : (string)$callable[0];
                            $method = (string)($callable[1] ?? '');
                            $callable_name = $class . '::' . $method;
                        }
                        if (!empty($callable_name) && stripos($callable_name, 'CMG_SVG_Protector') === false) {
                            if (stripos($callable_name, 'bodhi') !== false ||
                                stripos($callable_name, 'sanitize') !== false ||
                                stripos($callable_name, 'svg_handler') !== false ||
                                stripos($callable_name, 'check_svg') !== false ||
                                stripos($callable_name, 'gutenkit') !== false ||
                                stripos($callable_name, 'check_files_formate') !== false ||
                                stripos($callable_name, 'elementor') !== false) {
                                unset($wp_filter[$hook_name]->callbacks[$priority][$id]);
                            }
                        }
                    }
                }
            }
        }
    }

    public static function on_upload_prefilter($file) {
        if (empty($file['name']) || empty($file['tmp_name'])) {
            return $file;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext === 'svg' || $ext === 'svgz') {
            // Save pristine raw content before any plugin touches it
            if (file_exists($file['tmp_name'])) {
                $raw_content = file_get_contents($file['tmp_name']);
                if (!empty($raw_content)) {
                    self::$raw_svg_cache[$file['name']] = $raw_content;
                    self::$last_raw_svg = $raw_content;
                }
            }

            // Immediately clear any other callbacks on the upload prefilter
            global $wp_filter;
            foreach (['wp_handle_upload_prefilter', 'wp_handle_sideload_prefilter'] as $hook_name) {
                if (isset($wp_filter[$hook_name]) && is_object($wp_filter[$hook_name])) {
                    foreach ($wp_filter[$hook_name]->callbacks as $priority => $callbacks) {
                        foreach ($callbacks as $id => $cb) {
                            if (strpos((string)$id, 'CMG_SVG_Protector') === false) {
                                unset($wp_filter[$hook_name]->callbacks[$priority][$id]);
                            }
                        }
                    }
                }
            }
        }

        return $file;
    }

    public static function on_upload_prefilter_cleanup($file) {
        $ext = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
        if ($ext === 'svg' || $ext === 'svgz') {
            // Clear any error set by plugins
            $file['error'] = 0;
            unset($file['error']);

            // If anything emptied or damaged the temp file, restore our pristine copy
            if (!empty(self::$last_raw_svg) && !empty($file['tmp_name'])) {
                if (!file_exists($file['tmp_name']) || filesize($file['tmp_name']) === 0) {
                    file_put_contents($file['tmp_name'], self::$last_raw_svg);
                }
            }
        }
        return $file;
    }

    public static function on_upload_complete($upload) {
        if (empty($upload['file'])) {
            return $upload;
        }

        $filepath = $upload['file'];
        $ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));

        if ($ext === 'svg' && file_exists($filepath)) {
            $filename = basename($filepath);
            $base_name_clean = preg_replace('/-\d+(\.svg)$/i', '$1', $filename);

            // Restore pristine content if altered by any sanitizer (support renamed uploads like -1.svg)
            $raw = self::$raw_svg_cache[$filename] ?? self::$raw_svg_cache[$base_name_clean] ?? self::$last_raw_svg;
            if (!empty($raw)) {
                file_put_contents($filepath, $raw);
            }

            $content = file_get_contents($filepath);

            // 1. Guarantee viewBox casing
            if (strpos($content, 'viewbox=') !== false) {
                $content = preg_replace('/\bviewbox\b/i', 'viewBox', $content);
            }

            // 2. Guarantee xmlns="http://www.w3.org/2000/svg" exists (essential for <img> tag rendering)
            if (stripos($content, 'xmlns=') === false) {
                $content = preg_replace('/<svg\b/i', '<svg xmlns="http://www.w3.org/2000/svg"', $content, 1);
            }

            // 3. Guarantee viewBox exists so SVG can scale responsively
            if (!preg_match('/\bviewBox\s*=/i', $content)) {
                if (preg_match('/width=["\']([0-9.]+)(?:px)?["\']/i', $content, $wm) &&
                    preg_match('/height=["\']([0-9.]+)(?:px)?["\']/i', $content, $hm)) {
                    $content = preg_replace('/<svg\b/i', '<svg viewBox="0 0 ' . $wm[1] . ' ' . $hm[1] . '"', $content, 1);
                }
            }

            file_put_contents($filepath, $content);
        }

        return $upload;
    }

    public static function admin_svg_styles() {
        echo '<style id="cmg-svg-admin-media-fix">
            .thumbnail img[src*=".svg"],
            .attachment img[src*=".svg"],
            .attachment-preview img[src*=".svg"],
            .media-frame .attachment .thumbnail .centered img[src*=".svg"],
            .media-frame-content .attachments-browser .attachment-preview img[src*=".svg"],
            .media-modal .attachment-preview img[src*=".svg"] {
                width: 100% !important;
                height: 100% !important;
                max-width: 100% !important;
                max-height: 100% !important;
                object-fit: contain !important;
                position: static !important;
                transform: none !important;
                display: block !important;
            }
            .media-sidebar .attachment-details .thumbnail img[src*=".svg"],
            .attachment-info .thumbnail img[src*=".svg"] {
                width: auto !important;
                height: auto !important;
                max-width: 120px !important;
                max-height: 120px !important;
                object-fit: contain !important;
                margin: 0 auto !important;
            }
            .elementor-control-media-area .elementor-control-media__preview {
                background-size: contain !important;
                background-repeat: no-repeat !important;
                background-position: center !important;
            }
        </style>';
    }

    public static function generate_svg_metadata($metadata, $attachment_id) {
        $mime = get_post_mime_type($attachment_id);
        if ($mime === 'image/svg+xml') {
            $file = get_attached_file($attachment_id);
            if (file_exists($file)) {
                $content = file_get_contents($file);
                if (empty($metadata['width']) || empty($metadata['height'])) {
                    if (preg_match('/\bviewBox=["\']\s*0\s+0\s+([0-9.]+)\s+([0-9.]+)\s*["\']/i', $content, $m)) {
                        $metadata['width'] = round(floatval($m[1]));
                        $metadata['height'] = round(floatval($m[2]));
                    } elseif (preg_match('/width=["\']([0-9.]+)(?:px)?["\']/i', $content, $wm) &&
                              preg_match('/height=["\']([0-9.]+)(?:px)?["\']/i', $content, $hm)) {
                        $metadata['width'] = round(floatval($wm[1]));
                        $metadata['height'] = round(floatval($hm[1]));
                    }
                }
            }
        }
        return $metadata;
    }

    public static function whitelist_tags($tags) {
        if (!is_array($tags)) {
            $tags = [];
        }
        return array_unique(array_merge($tags, [
            'svg', 'g', 'path', 'defs', 'clipPath', 'mask', 'filter', 
            'feFlood', 'feColorMatrix', 'feOffset', 'feGaussianBlur', 
            'feComposite', 'feBlend', 'rect', 'circle', 'line', 'polygon', 
            'polyline', 'style', 'text', 'tspan', 'linearGradient', 'radialGradient', 'stop'
        ]));
    }

    public static function whitelist_attributes($attributes) {
        if (!is_array($attributes)) {
            $attributes = [];
        }
        return array_unique(array_merge($attributes, [
            'clip-path', 'mask', 'filter', 'filterUnits', 'color-interpolation-filters',
            'stdDeviation', 'dx', 'dy', 'in', 'in2', 'operator', 'values', 'result', 
            'flood-opacity', 'viewBox', 'maskUnits', 'fill', 'stroke', 'stroke-width',
            'stroke-linecap', 'stroke-dasharray', 'opacity', 'width', 'height', 'x', 'y'
        ]));
    }
}
CMG_SVG_Protector::init();

// Auto-repair broken Group-178969.svg in uploads directory if needed
add_action('init', function() {
    $theme_clean_svg = get_stylesheet_directory() . '/assets/images/67eca388d05621cf04a778bf_Group-178969.svg';
    if (!file_exists($theme_clean_svg)) {
        return;
    }

    $upload_info = wp_upload_dir();
    $target_dir = $upload_info['basedir'] . '/2026/09';
    $target_file = $target_dir . '/67eca388d05621cf04a778bf_Group-178969.svg';

    if (file_exists($target_file)) {
        if (filesize($target_file) < 108000) {
            @copy($theme_clean_svg, $target_file);
        }
    } else if (is_dir($target_dir) && is_writable($target_dir)) {
        @copy($theme_clean_svg, $target_file);
    }
});


/* ==========================================================================
   CMGALAXY BRAND ANALYSIS REPORT SHORTCODE
   Shortcode: [cmg_brand_report], [brand_report_form], [brand_analysis_report]
   ========================================================================== */
function cmg_render_brand_report_form( $atts = [] ) {
    ob_start();
    ?>
    <style>
    /* =========================================================
       CMGALAXY BRAND ANALYSIS REPORT
       DESKTOP WIDTH FIX | TABLET + MOBILE RESPONSIVE
       ========================================================= */

    /* MAIN REPORT FORM WRAPPER */
    .report-form-wrapper {
      display: block !important;
      width: 704px !important;
      max-width: calc(100vw - 48px) !important;
      min-width: 0 !important;
      margin-left: auto !important;
      margin-right: auto !important;
      margin-top: 0 !important;
      margin-bottom: 0 !important;
      padding: 0 !important;
      box-sizing: border-box !important;
      overflow: visible !important;
    }

    /* FORM */
    .report-form {
      display: flex !important;
      flex-direction: column !important;
      align-items: stretch !important;
      width: 100% !important;
      max-width: 100% !important;
      min-width: 0 !important;
      margin: 0 !important;
      padding: 0 !important;
      box-sizing: border-box !important;
      overflow: visible !important;
    }

    /* BOX SIZING */
    .report-form,
    .report-form *,
    .report-form-wrapper,
    .report-form-wrapper * {
      box-sizing: border-box !important;
    }

    /* INPUTS */
    .report-input,
    .report-form input.report-input,
    .report-form .w-input {
      display: block !important;
      width: 100% !important;
      max-width: 100% !important;
      min-width: 0 !important;
      height: 52px !important;
      margin-top: 0 !important;
      margin-right: 0 !important;
      margin-bottom: 24px !important;
      margin-left: 0 !important;
      padding-top: 0 !important;
      padding-right: 16px !important;
      padding-bottom: 0 !important;
      padding-left: 16px !important;
      border: 1px solid #6f7285 !important;
      border-radius: 14px !important;
      background-color: #ffffff !important;
      color: #20244f !important;
      font-family: inherit !important;
      font-size: 16px !important;
      font-weight: 400 !important;
      line-height: normal !important;
      outline: none !important;
      appearance: none !important;
      -webkit-appearance: none !important;
      box-sizing: border-box !important;
      overflow: hidden !important;
      transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
    }

    /* PLACEHOLDER */
    .report-input::placeholder {
      color: #9294a1 !important;
      opacity: 1 !important;
    }

    /* INPUT FOCUS */
    .report-input:focus,
    .report-form .w-input:focus {
      border-color: #315eea !important;
      box-shadow: 0 0 0 2px rgba(49, 94, 234, 0.08) !important;
    }

    /* SUBMIT BUTTON */
    .report-submit,
    .report-form button.report-submit,
    .report-form input[type="submit"].report-submit {
      display: block !important;
      width: 332px !important;
      max-width: 100% !important;
      min-width: 0 !important;
      height: 50px !important;
      margin-top: 1px !important;
      margin-right: auto !important;
      margin-bottom: 28px !important;
      margin-left: auto !important;
      padding-top: 0 !important;
      padding-right: 20px !important;
      padding-bottom: 0 !important;
      padding-left: 20px !important;
      border: none !important;
      border-radius: 30px !important;
      background-color: #2fc653 !important;
      color: #ffffff !important;
      font-family: inherit !important;
      font-size: 16px !important;
      font-weight: 500 !important;
      line-height: 50px !important;
      text-align: center !important;
      white-space: nowrap !important;
      cursor: pointer !important;
      appearance: none !important;
      -webkit-appearance: none !important;
      box-sizing: border-box !important;
      transition: background-color 0.25s ease, transform 0.2s ease, box-shadow 0.25s ease !important;
    }

    /* BUTTON HOVER (GREEN -> BLUE) */
    .report-submit:hover,
    .report-form button.report-submit:hover {
      background-color: #2f64e8 !important;
      transform: translateY(-1px) !important;
      box-shadow: 0 5px 14px rgba(47, 100, 232, 0.18) !important;
    }

    /* BUTTON ACTIVE */
    .report-submit:active {
      transform: translateY(0) !important;
      box-shadow: none !important;
    }

    /* LOADING */
    .report-submit.loading {
      opacity: 0.75 !important;
      cursor: wait !important;
      transform: none !important;
      box-shadow: none !important;
    }

    .report-submit:disabled {
      cursor: wait !important;
    }

    /* DISCLAIMER */
    .report-disclaimer {
      display: block !important;
      width: 100% !important;
      max-width: 100% !important;
      min-width: 0 !important;
      margin: 0 !important;
      padding: 0 !important;
      color: #a1a3b0 !important;
      font-family: inherit !important;
      font-size: 14px !important;
      font-weight: 400 !important;
      line-height: 1.5 !important;
      text-align: center !important;
      overflow-wrap: break-word !important;
      word-wrap: break-word !important;
      box-sizing: border-box !important;
    }

    /* SUCCESS & ERROR */
    .report-success {
      display: none;
      width: 100% !important;
      max-width: 100% !important;
      min-width: 0 !important;
      margin: 14px 0 0 0 !important;
      padding: 0 10px !important;
      color: #2a9d4b !important;
      font-family: inherit !important;
      font-size: 14px !important;
      font-weight: 400 !important;
      line-height: 1.5 !important;
      text-align: center !important;
      overflow-wrap: anywhere !important;
      word-break: break-word !important;
      box-sizing: border-box !important;
    }

    .report-error {
      display: none;
      width: 100% !important;
      max-width: 100% !important;
      min-width: 0 !important;
      margin: 14px 0 0 0 !important;
      padding: 0 10px !important;
      color: #d33b3b !important;
      font-family: inherit !important;
      font-size: 14px !important;
      font-weight: 400 !important;
      line-height: 1.5 !important;
      text-align: center !important;
      overflow-wrap: anywhere !important;
      word-break: break-word !important;
      box-sizing: border-box !important;
    }

    /* RESPONSIVE MEDIA QUERIES */
    @media screen and (min-width: 1200px) {
      .report-form-wrapper {
        width: 704px !important;
        max-width: 704px !important;
        margin-left: auto !important;
        margin-right: auto !important;
      }
      .report-input,
      .report-form .w-input {
        width: 704px !important;
        max-width: 704px !important;
      }
    }

    @media screen and (min-width: 992px) and (max-width: 1199px) {
      .report-form-wrapper {
        width: 704px !important;
        max-width: calc(100vw - 48px) !important;
        margin-left: auto !important;
        margin-right: auto !important;
      }
      .report-input,
      .report-form .w-input {
        width: 100% !important;
        max-width: 100% !important;
      }
    }

    @media screen and (min-width: 768px) and (max-width: 991px) {
      .report-form-wrapper {
        width: 100% !important;
        max-width: 704px !important;
        margin-left: auto !important;
        margin-right: auto !important;
        padding-left: 24px !important;
        padding-right: 24px !important;
      }
      .report-form,
      .report-input,
      .report-form .w-input {
        width: 100% !important;
        max-width: 100% !important;
      }
      .report-submit {
        width: 332px !important;
        max-width: 100% !important;
      }
    }

    @media screen and (max-width: 767px) {
      .report-form-wrapper {
        display: block !important;
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        padding-left: 20px !important;
        padding-right: 20px !important;
        box-sizing: border-box !important;
        overflow: visible !important;
      }
      .report-form,
      .report-input,
      .report-form .w-input {
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
      }
      .report-form {
        align-items: stretch !important;
      }
      .report-input,
      .report-form .w-input {
        height: 48px !important;
        margin-bottom: 18px !important;
        padding-left: 14px !important;
        padding-right: 14px !important;
        border-radius: 12px !important;
        font-size: 16px !important;
      }
      .report-submit {
        width: 100% !important;
        max-width: 332px !important;
        min-width: 0 !important;
        height: 48px !important;
        margin-top: 2px !important;
        margin-left: auto !important;
        margin-right: auto !important;
        margin-bottom: 24px !important;
        font-size: 16px !important;
        line-height: 48px !important;
      }
      .report-disclaimer {
        width: 100% !important;
        max-width: 100% !important;
        padding-left: 4px !important;
        padding-right: 4px !important;
        font-size: 13px !important;
        line-height: 1.5 !important;
      }
      .report-success,
      .report-error {
        width: 100% !important;
        max-width: 100% !important;
        font-size: 13px !important;
      }
    }

    @media screen and (max-width: 479px) {
      .report-form-wrapper {
        width: 100% !important;
        max-width: 100% !important;
        padding-left: 16px !important;
        padding-right: 16px !important;
      }
      .report-input,
      .report-form .w-input {
        width: 100% !important;
        max-width: 100% !important;
        height: 46px !important;
        margin-bottom: 16px !important;
        padding-left: 13px !important;
        padding-right: 13px !important;
        border-radius: 11px !important;
        font-size: 15px !important;
      }
      .report-submit {
        width: 100% !important;
        max-width: 100% !important;
        height: 46px !important;
        margin-top: 2px !important;
        margin-bottom: 22px !important;
        padding-left: 10px !important;
        padding-right: 10px !important;
        font-size: 15px !important;
        line-height: 46px !important;
      }
      .report-disclaimer,
      .report-success,
      .report-error {
        font-size: 12px !important;
        line-height: 1.45 !important;
      }
      .report-success,
      .report-error {
        padding-left: 5px !important;
        padding-right: 5px !important;
      }
    }

    @media screen and (max-width: 359px) {
      .report-form-wrapper {
        padding-left: 12px !important;
        padding-right: 12px !important;
      }
      .report-input,
      .report-form .w-input {
        height: 44px !important;
        font-size: 14px !important;
      }
      .report-submit {
        height: 44px !important;
        font-size: 14px !important;
        line-height: 44px !important;
      }
      .report-disclaimer {
        font-size: 11px !important;
      }
    }
    </style>

    <div class="report-form-wrapper">
      <form id="brand-report-form" class="report-form" novalidate>
        <input
          type="text"
          id="report-full-name"
          name="fullName"
          class="report-input"
          placeholder="Full Name"
          maxlength="256"
          autocomplete="name"
          required
        >
        <input
          type="email"
          id="report-work-email"
          name="workEmail"
          class="report-input"
          placeholder="Work Email"
          maxlength="256"
          autocomplete="email"
          required
        >
        <button
          type="submit"
          id="report-submit"
          class="report-submit"
        >
          Download the Report
        </button>
        <p class="report-disclaimer">
          No calls. No follow-up unless you ask for one.
        </p>
        <p id="report-success" class="report-success">
          Thank you! Your report is downloading.
        </p>
        <p id="report-error" class="report-error"></p>
      </form>
    </div>

    <script>
    (function () {
      "use strict";

      const API_URL = "https://staging-api.cmgalaxy.com/api/v2/onboarding/milestone_lead_magnet/";
      const REPORT_PDF_URL = "https://gtm.cmgalaxy.com/BrandAnalysisReport.pdf";

      function initBrandReportForm() {
        const forms = document.querySelectorAll('.report-form');
        if (!forms.length) return;

        forms.forEach(function(form) {
          if (form.dataset.cmgReportInit === "true") return;
          form.dataset.cmgReportInit = "true";

          const fullNameInput = form.querySelector('input[name="fullName"]');
          const workEmailInput = form.querySelector('input[name="workEmail"]');
          const submitButton = form.querySelector('.report-submit');
          const successMessage = form.querySelector('.report-success');
          const errorMessage = form.querySelector('.report-error');

          if (!fullNameInput || !workEmailInput || !submitButton) return;

          form.addEventListener("submit", async function (event) {
            event.preventDefault();

            if (successMessage) successMessage.style.display = "none";
            if (errorMessage) {
              errorMessage.style.display = "none";
              errorMessage.textContent = "";
            }

            const fullName = fullNameInput.value.trim();
            const workEmail = workEmailInput.value.trim();

            if (!fullName) {
              if (errorMessage) {
                errorMessage.textContent = "Please enter your full name.";
                errorMessage.style.display = "block";
              }
              fullNameInput.focus();
              return;
            }

            if (!workEmail) {
              if (errorMessage) {
                errorMessage.textContent = "Please enter your work email.";
                errorMessage.style.display = "block";
              }
              workEmailInput.focus();
              return;
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(workEmail)) {
              if (errorMessage) {
                errorMessage.textContent = "Please enter a valid work email.";
                errorMessage.style.display = "block";
              }
              workEmailInput.focus();
              return;
            }

            const originalButtonText = submitButton.textContent;
            submitButton.disabled = true;
            submitButton.classList.add("loading");
            submitButton.textContent = "Please wait...";

            const payload = {
              fullName: fullName,
              workEmail: workEmail
            };

            try {
              const response = await fetch(API_URL, {
                method: "POST",
                mode: "cors",
                headers: {
                  "Content-Type": "application/json",
                  "Accept": "application/json"
                },
                body: JSON.stringify(payload)
              });

              const contentType = response.headers.get("content-type") || "";
              let responseData = null;

              if (contentType.includes("application/json")) {
                try {
                  responseData = await response.json();
                } catch (err) {
                  responseData = null;
                }
              } else {
                try {
                  responseData = await response.text();
                } catch (err) {
                  responseData = null;
                }
              }

              if (!response.ok) {
                let apiMessage = "Unable to submit your details.";
                if (responseData && typeof responseData === "object") {
                  apiMessage = responseData.message || responseData.error || responseData.detail || responseData.non_field_errors || apiMessage;
                } else if (typeof responseData === "string" && responseData.trim()) {
                  apiMessage = responseData.trim();
                }
                throw new Error("API " + response.status + ": " + apiMessage);
              }

              if (successMessage) {
                successMessage.textContent = "Thank you! Your report is downloading.";
                successMessage.style.display = "block";
              }

              submitButton.textContent = "Downloading...";

              const downloadLink = document.createElement("a");
              downloadLink.href = REPORT_PDF_URL;
              downloadLink.target = "_blank";
              downloadLink.rel = "noopener";
              downloadLink.download = "BrandAnalysisReport.pdf";
              document.body.appendChild(downloadLink);
              downloadLink.click();
              document.body.removeChild(downloadLink);

              submitButton.textContent = "Report Downloaded";
              submitButton.classList.remove("loading");

            } catch (error) {
              console.error("CMGalaxy Report Error:", error);
              if (errorMessage) {
                errorMessage.textContent = error.message || "Something went wrong. Please try again.";
                errorMessage.style.display = "block";
              }
              submitButton.disabled = false;
              submitButton.classList.remove("loading");
              submitButton.textContent = originalButtonText;
            }
          });
        });
      }

      if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initBrandReportForm);
      } else {
        initBrandReportForm();
      }
    })();
    </script>
    <?php
    return ob_get_clean();
}

add_shortcode( 'cmg_brand_report', 'cmg_render_brand_report_form' );
add_shortcode( 'brand_report_form', 'cmg_render_brand_report_form' );
add_shortcode( 'brand_analysis_report', 'cmg_render_brand_report_form' );
add_shortcode( 'cmg_report_form', 'cmg_render_brand_report_form' );
if ( ! function_exists( 'cmg_enqueue_custom_styles' ) ) {
    function cmg_enqueue_custom_styles() {
        wp_enqueue_style(
            'cmg-custom-css',
            get_template_directory_uri() . '/css/custom.css',
            array(),
            time()
        );
    }
    add_action( 'wp_enqueue_scripts', 'cmg_enqueue_custom_styles', 999 );
    add_action( 'elementor/preview/enqueue_styles', 'cmg_enqueue_custom_styles', 999 );
}

/**
 * Automatically cascade font-size, font-family, and styles from ANY custom class (e.g. .cta, .cta1)
 * directly down to Elementor buttons and inner text, so you NEVER need to write the word "elementor".
 */
add_action( 'wp_head', function() {
    ?>
    <style id="cmg-button-auto-cascade">
    .elementor-widget-button[class] .elementor-button,
    .elementor-widget-button[class] .elementor-button-text,
    .elementor-widget-button[class] .elementor-button-content-wrapper {
        font-size: inherit;
        line-height: inherit;
    }
    .elementor-widget-button.cta *,
    .elementor-widget-button.cta1 *,
    .cta .elementor-button,
    .cta .elementor-button-text,
    .cta1 .elementor-button,
    .cta1 .elementor-button-text {
        font-size: inherit !important;
    }
    </style>
    <?php
}, 99 );

/**
 * Forward custom classes from Elementor Button Widget wrapper directly to inner <a> tag
 * Ensures any class entered in Elementor (e.g. cta, cta1) appears directly on <a class="elementor-button ...">
 */
function cmg_forward_elementor_button_classes( $content ) {
    if ( empty( $content ) || strpos( $content, 'elementor-widget-button' ) === false ) {
        return $content;
    }
    return preg_replace_callback(
        '/(<div[^>]*class=["\'][^"\']*elementor-widget-button[^"\']*["\'][^>]*>)([\s\S]*?)(?=<\/div>)/i',
        function( $match ) {
            $div_tag = $match[1];
            $inner   = $match[2];
            if ( preg_match( '/class=["\']([^"\']+)["\']/', $div_tag, $m ) ) {
                $classes = array_filter( explode( ' ', $m[1] ), function( $c ) {
                    return ! empty( $c ) && strpos( $c, 'elementor-' ) !== 0 && strpos( $c, 'e-' ) !== 0 && strpos( $c, 'wp-' ) !== 0 && strpos( $c, 'wor-' ) !== 0;
                } );
                if ( ! empty( $classes ) ) {
                    $class_str = ' ' . esc_attr( implode( ' ', $classes ) );
                    $inner = preg_replace( '/(<a\s+[^>]*class=["\']elementor-button\b)/i', '$1' . $class_str, $inner, 1 );
                }
            }
            return $div_tag . $inner;
        },
        $content
    );
}
add_filter( 'the_content', 'cmg_forward_elementor_button_classes', 99 );
add_filter( 'elementor/frontend/the_content', 'cmg_forward_elementor_button_classes', 99 );

add_filter( 'elementor/widget/render_content', function( $content, $widget ) {
    if ( 'button' === $widget->get_name() ) {
        $classes = $widget->get_settings( '_css_classes' );
        if ( ! empty( $classes ) ) {
            $classes_clean = esc_attr( trim( $classes ) );
            $content = preg_replace(
                '/(<a\s+[^>]*class=["\']elementor-button\b)/i',
                '$1 ' . $classes_clean,
                $content,
                1
            );
        }
    }
    return $content;
}, 10, 2 );

add_action( 'wp_footer', function() {
    ?>
    <script>
    (function() {
        function forwardButtonClasses() {
            var widgets = document.querySelectorAll('.elementor-widget-button');
            for (var i = 0; i < widgets.length; i++) {
                var w = widgets[i];
                var btn = w.querySelector('.elementor-button');
                if (!btn) continue;
                var classList = w.className.split(/\s+/);
                for (var j = 0; j < classList.length; j++) {
                    var c = classList[j];
                    if (c && !c.startsWith('elementor-') && !c.startsWith('e-') && !c.startsWith('wp-') && !c.startsWith('wor-') && !btn.classList.contains(c)) {
                        btn.classList.add(c);
                    }
                }
            }
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', forwardButtonClasses);
        } else {
            forwardButtonClasses();
        }
        window.addEventListener('load', forwardButtonClasses);
    })();
    </script>
    <?php
}, 999 );

/**
 * Neutralize unscoped global 'svg { width: 100%; height: auto; }' in video-thumbnail HTML widgets
 */
add_filter( 'elementor/widget/render_content', function( $widget_content, $widget ) {
    if ( is_string( $widget_content ) && strpos( $widget_content, 'video-thumbnail' ) !== false && strpos( $widget_content, 'svg' ) !== false ) {
        $widget_content = preg_replace( '/(\b)svg\s*\{\s*width:\s*100%;\s*height:\s*auto;\s*\}/i', '$1.video-thumbnail svg { width: 100%; height: auto; }', $widget_content );
    }
    return $widget_content;
}, 10, 2 );

add_filter( 'the_content', function( $content ) {
    if ( is_string( $content ) && strpos( $content, 'video-thumbnail' ) !== false && strpos( $content, 'svg' ) !== false ) {
        $content = preg_replace( '/(\b)svg\s*\{\s*width:\s*100%;\s*height:\s*auto;\s*\}/i', '$1.video-thumbnail svg { width: 100%; height: auto; }', $content );
    }
    return $content;
}, 999 );

/**
 * REST API Endpoint to import articles from https://cmgalaxy.com/blog
 * Accessible via:
 * GET or POST https://y9xt93xns6.onrocket.site/wp-json/cmg/v1/import-articles?key=cmg_import_2026
 */
add_action( 'rest_api_init', function() {
    register_rest_route( 'cmg/v1', '/import-articles', [
        'methods'             => [ 'GET', 'POST' ],
        'callback'            => 'cmg_import_articles_handler',
        'permission_callback' => '__return_true',
    ] );

    register_rest_route( 'cmg/v1', '/update-articles-meta', [
        'methods'             => [ 'GET', 'POST' ],
        'callback'            => 'cmg_update_articles_meta_handler',
        'permission_callback' => '__return_true',
    ] );
} );

function cmg_update_articles_meta_handler( WP_REST_Request $request ) {
    $secret = 'cmg_import_2026';
    if ( $request->get_param( 'key' ) !== $secret ) {
        return new WP_REST_Response( [ 'error' => 'Unauthorized' ], 403 );
    }

    $items = $request->get_json_params();
    $results = [];

    if ( ! empty( $items ) && is_array( $items ) ) {
        foreach ( $items as $item ) {
            $post = null;
            if ( ! empty( $item['id'] ) ) {
                $post = get_post( intval( $item['id'] ) );
            }
            if ( ! $post ) {
                $slug = sanitize_title( $item['slug'] ?? '' );
                if ( empty( $slug ) ) continue;
                $post = cmg_find_post_by_slug( $slug );
            }
            if ( ! $post ) continue;

            $update_data = [
                'ID'          => $post->ID,
                'post_author' => 1,
            ];

            if ( ! empty( $item['title'] ) ) {
                $update_data['post_title'] = wp_strip_all_tags( $item['title'] );
            }

            if ( ! empty( $item['post_date'] ) ) {
                $update_data['post_date']     = $item['post_date'];
                $update_data['post_date_gmt'] = get_gmt_from_date( $item['post_date'] );
            }

            if ( ! empty( $item['meta_description'] ) ) {
                $update_data['post_excerpt'] = sanitize_textarea_field( $item['meta_description'] );
            }

            wp_update_post( $update_data );

            if ( ! empty( $item['meta_description'] ) ) {
                update_post_meta( $post->ID, '_yoast_wpseo_metadesc', sanitize_textarea_field( $item['meta_description'] ) );
            }

            if ( ! empty( $item['author'] ) ) {
                update_post_meta( $post->ID, 'cmg_author_name', sanitize_text_field( $item['author'] ) );
            }

            if ( ! empty( $item['reading_time'] ) ) {
                update_post_meta( $post->ID, 'cmg_reading_time', sanitize_text_field( $item['reading_time'] ) );
            }

            $results[] = [
                'id'        => $post->ID,
                'slug'      => $slug,
                'post_date' => $item['post_date'] ?? '',
                'author'    => $item['author'] ?? '',
            ];
        }
    }

    // Inspect & update Elementor Blog Page (ID 1339)
    $page_id = 1339;
    $raw_meta = get_post_meta( $page_id, '_elementor_data', true );
    $widget_settings = null;

    if ( ! empty( $raw_meta ) ) {
        $elements = json_decode( $raw_meta, true );
        if ( is_array( $elements ) ) {
            $updated = false;
            $updater = function( &$items ) use ( &$updater, &$updated, &$widget_settings ) {
                foreach ( $items as &$it ) {
                    if ( isset( $it['widgetType'] ) && $it['widgetType'] === 'elementskit-blog-posts' ) {
                        $widget_settings = $it['settings'] ?? [];
                        // Set posts count to 20 so all articles are displayed
                        $it['settings']['ekit_blog_posts_num'] = 20;
                        $it['settings']['ekit_blog_posts_order_by'] = 'date';
                        $it['settings']['ekit_blog_posts_sort'] = 'desc';
                        $it['settings']['ekit_blog_posts_meta'] = 'yes';
                        $it['settings']['ekit_blog_posts_meta_select'] = [ 'date', 'author' ];
                        $updated = true;
                    }
                    if ( isset( $it['widgetType'] ) && $it['widgetType'] === 'html' ) {
                        if ( isset( $it['settings']['html'] ) && strpos( $it['settings']['html'], 'cmgalaxy-search' ) !== false ) {
                            $it['settings']['html'] = '<div class="cmgalaxy-search-wrap">' . "\n" .
                                '    <!-- LEFT IMAGE ICON -->' . "\n" .
                                '    <div class="cmgalaxy-search-icon">' . "\n" .
                                '        <img decoding="async" src="https://y9xt93xns6.onrocket.site/wp-content/uploads/2026/09/lex-logo.png" alt="icon">' . "\n" .
                                '    </div>' . "\n\n" .
                                '    <!-- INPUT -->' . "\n" .
                                '    <input type="search" class="cmgalaxy-search-input" placeholder="Search articles, insights, or topics...">' . "\n\n" .
                                '    <!-- RIGHT BUTTON -->' . "\n" .
                                '    <button type="button" class="cmgalaxy-search-btn" aria-label="Search">' . "\n" .
                                '        <svg width="18" height="18" viewBox="0 0 20 20" fill="none">' . "\n" .
                                '            <path d="M14.707 13.293a1 1 0 0 1 1.32-.083l.094.083 2.5 2.5a1 1 0 0 1-1.32 1.497l-.094-.083-2.5-2.5a1 1 0 0 1 0-1.414z" fill="white"/>' . "\n" .
                                '            <path d="M9 2a7 7 0 1 1 0 14A7 7 0 0 1 9 2zm0 2a5 5 0 1 0 0 10A5 5 0 0 0 9 4z" fill="white"/>' . "\n" .
                                '        </svg>' . "\n" .
                                '    </button>' . "\n" .
                                '</div>';
                            $updated = true;
                        }
                    }
                    if ( ! empty( $it['elements'] ) ) {
                        $updater( $it['elements'] );
                    }
                }
            };
            $updater( $elements );

            if ( $updated ) {
                update_post_meta( $page_id, '_elementor_data', wp_slash( json_encode( $elements ) ) );
                if ( class_exists( '\Elementor\Plugin' ) ) {
                    \Elementor\Plugin::$instance->files_manager->clear_cache();
                }
            }
        }
    }

    return new WP_REST_Response( [
        'success'               => true,
        'updated_articles'      => count( $results ),
        'articles'              => $results,
        'prior_widget_settings' => $widget_settings,
    ], 200 );
}

/**
 * Filter Elementor HTML widget output to ensure bare CSS code is never printed as text
 */
add_filter( 'elementor/widget/render_content', 'cmg_filter_elementor_widget_render_content', 999, 2 );
function cmg_filter_elementor_widget_render_content( $content, $widget ) {
    if ( strpos( $content, 'cmgalaxy-search' ) !== false ) {
        // Strip any unescaped raw CSS leaked into HTML text
        if ( strpos( $content, '.cmgalaxy-search-wrap{' ) !== false || strpos( $content, '.cmgalaxy-search-wrap {' ) !== false ) {
            $content = preg_replace( '/\s*\.cmgalaxy-search-wrap\s*\{.*$/s', '</div>', $content );
        }
        // Ensure input and button with SVG icon are always properly intact
        if ( strpos( $content, '<input' ) === false || strpos( $content, '<path' ) === false ) {
            $search_bar = '<div class="cmgalaxy-search-wrap">
    <div class="cmgalaxy-search-icon">
        <img decoding="async" src="https://y9xt93xns6.onrocket.site/wp-content/uploads/2026/09/lex-logo.png" alt="icon">
    </div>
    <input type="search" class="cmgalaxy-search-input" placeholder="Search articles, insights, or topics...">
    <button type="button" class="cmgalaxy-search-btn" aria-label="Search">
        <svg width="18" height="18" viewBox="0 0 20 20" fill="none">
            <path d="M14.707 13.293a1 1 0 0 1 1.32-.083l.094.083 2.5 2.5a1 1 0 0 1-1.32 1.497l-.094-.083-2.5-2.5a1 1 0 0 1 0-1.414z" fill="white"/>
            <path d="M9 2a7 7 0 1 1 0 14A7 7 0 0 1 9 2zm0 2a5 5 0 1 0 0 10A5 5 0 0 0 9 4z" fill="white"/>
        </svg>
    </button>
</div>';
            $content = preg_replace( '/<div class="cmgalaxy-search-wrap">.*?<\/div>\s*(?:<\/div>)?/s', $search_bar, $content );
        }
    }
    return $content;
}

/**
 * Add instant client-side article search interactivity
 */
add_action( 'wp_footer', 'cmg_blog_instant_search_script', 999 );
function cmg_blog_instant_search_script() {
    if ( ! is_page( [ 1339, 'blog', 'blog-2' ] ) ) return;
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var searchInput = document.querySelector('.cmgalaxy-search-wrap input');
        if (!searchInput) return;

        function filterPosts(query) {
            query = (query || '').toLowerCase().trim();
            var items = document.querySelectorAll('.elementor-widget-elementskit-blog-posts .post-item');
            items.forEach(function(item) {
                var titleEl = item.querySelector('.entry-title');
                var text = titleEl ? titleEl.textContent.toLowerCase() : '';
                if (!query || text.indexOf(query) !== -1) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        searchInput.addEventListener('input', function() {
            filterPosts(this.value);
        });

        // Quick tags
        document.querySelectorAll('.elementor-widget-text-editor').forEach(function(editor) {
            if (editor.textContent && editor.textContent.indexOf('Recent searches') !== -1) {
                var html = editor.innerHTML;
                var tags = ['Marketing Automation', 'GA4', 'AI Marketing', 'Data Insights'];
                tags.forEach(function(tag) {
                    html = html.replace(tag, '<span class="cmg-search-tag" style="cursor:pointer;color:#3b79ff;text-decoration:underline;">' + tag + '</span>');
                });
                editor.innerHTML = html;
                editor.querySelectorAll('.cmg-search-tag').forEach(function(span) {
                    span.addEventListener('click', function() {
                        searchInput.value = this.textContent.trim();
                        filterPosts(searchInput.value);
                    });
                });
            }
        });
    });
    </script>
    <?php
}

/**
 * Ensure Blog Posts widget displays all articles arranged by date DESC (latest first)
 */
add_filter( 'elementskit/widgets/blog_posts/query_args', 'cmg_override_blog_query_args', 999 );
add_filter( 'elementskit_blog_posts_query_args', 'cmg_override_blog_query_args', 999 );
function cmg_override_blog_query_args( $args ) {
    $args['posts_per_page'] = 20;
    $args['orderby'] = 'date';
    $args['order'] = 'DESC';
    return $args;
}

function cmg_find_post_by_slug( $slug ) {
    $posts = get_posts( [
        'name'        => sanitize_title( $slug ),
        'post_type'   => 'post',
        'post_status' => 'any',
        'numberposts' => 1,
    ] );
    return ! empty( $posts ) ? $posts[0] : null;
}

function cmg_attach_featured_image( $img_url, $post_id, $title = '' ) {
    if ( empty( $img_url ) ) return false;

    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $attach_id = media_sideload_image( $img_url, $post_id, $title, 'id' );
    if ( ! is_wp_error( $attach_id ) && is_numeric( $attach_id ) ) {
        set_post_thumbnail( $post_id, (int) $attach_id );
        return (int) $attach_id;
    }

    $tmp = download_url( $img_url, 30 );
    if ( is_wp_error( $tmp ) ) {
        return false;
    }

    $path_info = pathinfo( parse_url( $img_url, PHP_URL_PATH ) );
    $ext = ! empty( $path_info['extension'] ) ? $path_info['extension'] : 'webp';
    $file_array = [
        'name'     => sanitize_file_name( ( ! empty( $path_info['filename'] ) ? $path_info['filename'] : 'featured' ) . '.' . $ext ),
        'tmp_name' => $tmp,
    ];

    $attach_id = media_handle_sideload( $file_array, $post_id, $title );
    if ( is_wp_error( $attach_id ) ) {
        @unlink( $tmp );
        return false;
    }

    set_post_thumbnail( $post_id, (int) $attach_id );
    return (int) $attach_id;
}

function cmg_scrape_webflow_articles() {
    $blog_url = 'https://cmgalaxy.com/blog';
    $response = wp_remote_get( $blog_url, [ 'timeout' => 30, 'sslverify' => false ] );
    if ( is_wp_error( $response ) ) {
        return [];
    }

    $html = wp_remote_retrieve_body( $response );
    if ( empty( $html ) ) {
        return [];
    }

    preg_match_all( '/href="(\/blog\/[^"]+)"/', $html, $matches );
    if ( empty( $matches[1] ) ) {
        return [];
    }

    $slugs = [];
    foreach ( array_unique( $matches[1] ) as $path ) {
        if ( $path === '/blog' || $path === '/blog/' ) continue;
        $slug = trim( str_replace( [ '/blog/', '/blog' ], '', $path ), '/' );
        if ( ! empty( $slug ) && ! in_array( $slug, $slugs ) ) {
            $slugs[] = $slug;
        }
    }

    $articles = [];
    foreach ( $slugs as $slug ) {
        $art_url = 'https://cmgalaxy.com/blog/' . $slug;
        $art_res = wp_remote_get( $art_url, [ 'timeout' => 30, 'sslverify' => false ] );
        if ( is_wp_error( $art_res ) ) continue;
        $art_html = wp_remote_retrieve_body( $art_res );
        if ( empty( $art_html ) ) continue;

        $title = '';
        if ( preg_match( '/<meta\s+property=["\']og:title["\']\s+content=["\']([^"\']+)["\']/i', $art_html, $m ) ) {
            $title = $m[1];
        } elseif ( preg_match( '/<title>([^<]+)<\/title>/i', $art_html, $m ) ) {
            $title = $m[1];
        }
        $title = preg_replace( '/\s*\|\s*CMGalaxy.*$/i', '', $title );
        $title = html_entity_decode( $title, ENT_QUOTES | ENT_HTML5, 'UTF-8' );

        $featured_img = '';
        if ( preg_match( '/<meta[^>]+(?:property=["\']og:image["\'][^>]+content=["\']([^"\']+)["\']|content=["\']([^"\']+)["\'][^>]+property=["\']og:image["\'])/i', $art_html, $m ) ) {
            $featured_img = ! empty( $m[1] ) ? $m[1] : ( ! empty( $m[2] ) ? $m[2] : '' );
        }

        $content = '';
        if ( preg_match( '/<div[^>]*class=["\'][^"\']*w-richtext[^"\']*["\'][^>]*>(.*?)<\/div>/is', $art_html, $m ) ) {
            $content = $m[1];
        }

        $articles[] = [
            'title'          => $title,
            'slug'           => $slug,
            'featured_image' => $featured_img,
            'content'        => $content,
        ];
    }

    return $articles;
}

function cmg_import_articles_handler( WP_REST_Request $request ) {
    $secret = 'cmg_import_2026';
    $provided_key = $request->get_param( 'key' );
    if ( $provided_key !== $secret ) {
        return new WP_REST_Response( [ 'error' => 'Unauthorized. Invalid or missing key.' ], 403 );
    }

    if ( function_exists( 'set_time_limit' ) ) {
        @set_time_limit( 300 );
    }

    $articles = $request->get_json_params();

    if ( empty( $articles ) || ! is_array( $articles ) ) {
        $param_articles = $request->get_param( 'articles' );
        if ( ! empty( $param_articles ) && is_array( $param_articles ) ) {
            $articles = $param_articles;
        }
    }

    if ( empty( $articles ) ) {
        $articles = cmg_scrape_webflow_articles();
    }

    if ( empty( $articles ) ) {
        return new WP_REST_Response( [ 'error' => 'No articles found or provided to import.' ], 400 );
    }

    $results = [];

    foreach ( $articles as $article ) {
        $title = isset( $article['title'] ) ? sanitize_text_field( $article['title'] ) : '';
        $slug = isset( $article['slug'] ) ? sanitize_title( $article['slug'] ) : '';
        $content = isset( $article['content'] ) ? $article['content'] : '';
        $featured_img = isset( $article['featured_image'] ) ? esc_url_raw( $article['featured_image'] ) : '';

        if ( empty( $title ) && empty( $slug ) ) {
            continue;
        }

        $existing_post = cmg_find_post_by_slug( $slug );
        if ( ! $existing_post && ! empty( $title ) ) {
            $posts_by_title = get_posts( [
                'title'       => $title,
                'post_type'   => 'post',
                'post_status' => 'any',
                'numberposts' => 1,
            ] );
            if ( ! empty( $posts_by_title ) ) {
                $existing_post = $posts_by_title[0];
            }
        }

        if ( $existing_post ) {
            $post_id = $existing_post->ID;
            $status = 'already_exists';

            if ( ! has_post_thumbnail( $post_id ) && ! empty( $featured_img ) ) {
                $thumb_id = cmg_attach_featured_image( $featured_img, $post_id, $title );
                if ( $thumb_id ) {
                    $status .= '_thumbnail_added';
                }
            }
        } else {
            $post_data = [
                'post_title'   => $title,
                'post_name'    => $slug,
                'post_content' => $content,
                'post_status'  => 'publish',
                'post_type'    => 'post',
            ];

            $post_id = wp_insert_post( $post_data, true );

            if ( is_wp_error( $post_id ) ) {
                $results[] = [
                    'title'  => $title,
                    'slug'   => $slug,
                    'status' => 'error',
                    'error'  => $post_id->get_error_message(),
                ];
                continue;
            }

            $status = 'imported';

            if ( ! empty( $featured_img ) ) {
                $thumb_id = cmg_attach_featured_image( $featured_img, $post_id, $title );
                if ( $thumb_id ) {
                    $status .= '_with_thumbnail';
                } else {
                    $status .= '_thumbnail_failed';
                }
            }
        }

        $results[] = [
            'id'     => $post_id,
            'title'  => $title,
            'slug'   => $slug,
            'status' => $status,
            'url'    => get_permalink( $post_id ),
        ];
    }

    return new WP_REST_Response( [
        'success' => true,
        'count'   => count( $results ),
        'results' => $results,
    ], 200 );
}



