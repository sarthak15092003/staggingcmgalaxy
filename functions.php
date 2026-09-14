<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'HELLO_ELEMENTOR_VERSION', '3.4.9' );
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
      .cmg-blog-content a, .cmg-blog-content a:visited {
        color: #2563eb !important;
        text-decoration: underline !important;
        text-decoration-color: rgba(37, 99, 235, 0.4) !important;
      }
      .cmg-blog-content a:hover, .cmg-blog-content a:focus, .cmg-blog-content a:active {
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
      body.single-post a:active,
      body.single-post a:focus {
        color: #2563eb !important;
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
      .cmg-blog-toc-link:active,
      .cmg-blog-toc-link:focus {
        color: #161c52 !important;
        background: rgba(22, 28, 82, 0.06) !important;
        border-left-color: #22c55e !important;
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
        .iti__flag-container, .iti__country-list { display: none !important; }
        .iti { display: block !important; width: 100% !important; }
        .iti__tel-input { width: 100% !important; }

        .lead-form-wrapper {
            max-width: 600px;
            margin: 40px auto;
            padding: 40px 48px;
            background: #ffffff;
            border-radius: 28px;
            box-shadow: 0 22px 60px rgba(15, 35, 52, 0.08);
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

        .lead-phone-row { display: flex; gap: 10px; align-items: center; }
        .country-dropdown { flex: 0 0 140px; position: relative; font-size: 14px; }
        .country-selected { display: flex; align-items: center; justify-content: space-between; padding: 16px 12px; border-radius: 8px; border: 1px solid #dde3f0; background: #fff; cursor: pointer; }
        .country-selected-left { display: flex; align-items: center; gap: 6px; overflow: hidden; }
        #countrySelectedFlag { display: inline-block; width: 20px; height: 14px; border-radius: 2px; flex-shrink: 0; object-fit: cover; }
        #countrySelectedLabel { font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px; color: #1b2230; white-space: nowrap; }
        .country-arrow { border-width: 6px 5px 0 5px; border-style: solid; border-color: #9aa3b5 transparent transparent transparent; flex-shrink: 0; }

        .country-options {
            position: absolute;
            top: 100%; left: 0; right: auto;
            width: 280px; margin-top: 4px;
            background: #fff; border-radius: 8px; border: 1px solid #dde3f0;
            max-height: 300px; overflow-y: auto; overflow-x: hidden; white-space: normal;
            box-shadow: 0 14px 30px rgba(15, 35, 52, 0.16); z-index: 9999; display: none;
        }
        .country-options.open { display: block; }
        .country-search { position: sticky; top: 0; background: #fff; padding: 10px; border-bottom: 1px solid #eee; }
        .country-search input { width: 100%; padding: 8px 10px; border: 1px solid #dde3f0; border-radius: 6px; font-size: 14px; outline: none; box-sizing: border-box; }
        .country-search input:focus { border-color: #3a7dff; }
        .country-option { display: flex; align-items: center; gap: 8px; padding: 8px 12px; cursor: pointer; font-size: 14px; white-space: normal; }
        .country-option img { width: 20px; height: 14px; flex-shrink: 0; border-radius: 2px; object-fit: cover; }
        .country-option span { flex: 1; }
        .country-option:hover { background: #f3f6ff; }
        .country-option.hidden { display: none; }
        .lead-phone-row .iti { flex: 1; }
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
            .lead-phone-row { flex-direction: row; gap: 10px; align-items: center; }
            .country-dropdown { flex: 0 0 110px; }
            .lead-phone-row .iti { flex: 1; }
            .lead-form-wrapper { padding: 20px 20px; border-radius: 20px; }
            .country-options { width: 260px; }
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
                <div class="lead-phone-row">
                    <div class="country-dropdown" id="countryDropdown">
                        <div class="country-selected" id="countrySelected">
                            <div class="country-selected-left">
                                <img id="countrySelectedFlag" class="country-flag" src="" alt="Country Flag">
                                <span id="countrySelectedLabel">IN +91</span>
                            </div>
                            <span class="country-arrow"></span>
                        </div>
                        <div class="country-options" id="countryOptions">
                            <div class="country-search">
                                <input type="text" id="countrySearchInput" placeholder="Search country...">
                            </div>
                            <div id="countryList"></div>
                        </div>
                        <input type="hidden" id="countryDialHidden" value="+91">
                    </div>
                    <input id="phone-input" type="tel" class="lead-form-input" placeholder="+91" required pattern="[0-9]*"
                        inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                </div>
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
            const dialHidden = document.getElementById("countryDialHidden");

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
                    phone_number: iti ? iti.getNumber() : (dialHidden.value + phoneNumber),
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

        document.addEventListener("DOMContentLoaded", function () {
            const phoneInput = document.getElementById("phone-input");
            const flagImg = document.getElementById("countrySelectedFlag");
            const label = document.getElementById("countrySelectedLabel");
            const dialHidden = document.getElementById("countryDialHidden");
            const countrySelected = document.getElementById("countrySelected");
            const countryOptions = document.getElementById("countryOptions");
            const countryList = document.getElementById("countryList");
            const searchInput = document.getElementById("countrySearchInput");

            if (!window.intlTelInputGlobals) return;

            const iti = window.intlTelInputGlobals.getInstance(phoneInput);
            const allCountries = window.intlTelInputGlobals.getCountryData();

            allCountries.forEach(country => {
                const option = document.createElement("div");
                option.className = "country-option";
                option.dataset.iso2 = country.iso2;
                option.dataset.dial = country.dialCode;
                option.dataset.name = country.name.toLowerCase();
                option.innerHTML = `
                    <img src="https://flagcdn.com/w20/${country.iso2}.png" alt="${country.name}">
                    <span>${country.name} (+${country.dialCode})</span>
                `;
                option.addEventListener("click", function () {
                    selectCountry(country);
                    countryOptions.classList.remove("open");
                    searchInput.value = "";
                    filterCountries("");
                });
                countryList.appendChild(option);
            });

            (async function setDefaultCountry() {
                let countryCode = "in";
                try {
                    const res = await fetch("https://ipapi.co/json/");
                    const data = await res.json();
                    if (data && data.country_code) countryCode = data.country_code.toLowerCase();
                } catch (err) { }
                const detected = allCountries.find(c => c.iso2 === countryCode);
                selectCountry(detected || allCountries.find(c => c.iso2 === "in"));
            })();

            countrySelected.addEventListener("click", function (e) {
                e.stopPropagation();
                countryOptions.classList.toggle("open");
                if (countryOptions.classList.contains("open")) {
                    setTimeout(() => searchInput.focus(), 100);
                }
            });

            searchInput.addEventListener("input", function (e) { filterCountries(e.target.value.toLowerCase()); });
            searchInput.addEventListener("click", function (e) { e.stopPropagation(); });

            function filterCountries(query) {
                const options = countryList.querySelectorAll(".country-option");
                options.forEach(option => {
                    const name = option.dataset.name;
                    const dial = option.dataset.dial;
                    const iso2 = option.dataset.iso2;
                    if (name.includes(query) || dial.includes(query) || iso2.includes(query)) option.classList.remove("hidden");
                    else option.classList.add("hidden");
                });
            }

            document.addEventListener("click", function () { countryOptions.classList.remove("open"); });
            countryOptions.addEventListener("click", function (e) { e.stopPropagation(); });

            function selectCountry(country) {
                flagImg.src = `https://flagcdn.com/w20/${country.iso2}.png`;
                label.textContent = `${country.iso2.toUpperCase()} +${country.dialCode}`;
                dialHidden.value = `+${country.dialCode}`;
                phoneInput.placeholder = `+${country.dialCode}`;
                if (iti) iti.setCountry(country.iso2);
            }
        });
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
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
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
        <div class="cmg-blog-header-wrapper" data-post-id="<?php echo esc_attr( $post_id ); ?>">
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
            margin-bottom: 24px !important;
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
              border-radius: 50%;
              overflow: hidden;
              background: #eef2ff;
              flex-shrink: 0;
              box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            }

            .cmg-blog-author-avatar img {
              width: 100%;
              height: 100%;
              object-fit: cover;
              border-radius: 50%;
              display: block;
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

        <script>
        (function() {
          function initCmgRatings() {
            var wraps = document.querySelectorAll(".cmg-blog-header-wrapper");
            wraps.forEach(function(wrap) {
              var postId = wrap.getAttribute("data-post-id") || "global";
              var starContainer = wrap.querySelector(".cmg-stars-list");
              if (!starContainer || starContainer.dataset.initialized) return;
              starContainer.dataset.initialized = "true";

              var stars = starContainer.querySelectorAll(".cmg-star");
              var scoreEl = wrap.querySelector(".cmg-rating-score");
              var textEl = wrap.querySelector(".cmg-rating-text");

              var storageKey = "cmg_post_rating_" + postId;
              var countKey = "cmg_post_rating_count_" + postId;
              var saved = localStorage.getItem(storageKey);
              var count = localStorage.getItem(countKey) || (saved ? "1" : "0");

              function updateDisplay(val) {
                stars.forEach(function(s) {
                  var idx = parseInt(s.getAttribute("data-index"), 10);
                  if (idx <= val) {
                    s.classList.add("active");
                  } else {
                    s.classList.remove("active");
                  }
                });
                if (scoreEl) {
                  scoreEl.textContent = (val > 0 ? parseFloat(val).toFixed(1) : "0.0") + " (" + (val > 0 ? count : "0") + ")";
                }
              }

              if (saved) {
                updateDisplay(parseFloat(saved));
              }

              stars.forEach(function(star) {
                star.addEventListener("mouseenter", function() {
                  var hoverIdx = parseInt(this.getAttribute("data-index"), 10);
                  stars.forEach(function(s) {
                    if (parseInt(s.getAttribute("data-index"), 10) <= hoverIdx) {
                      s.classList.add("hovered");
                    } else {
                      s.classList.remove("hovered");
                    }
                  });
                });

                star.addEventListener("click", function() {
                  var chosen = parseInt(this.getAttribute("data-index"), 10);
                  count = "1";
                  localStorage.setItem(storageKey, chosen);
                  localStorage.setItem(countKey, count);
                  updateDisplay(chosen);
                  if (textEl) {
                    textEl.textContent = "Thank you!";
                    setTimeout(function() { textEl.textContent = "Rating"; }, 3000);
                  }
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
        <div class="cmg-floating-side-banner" id="cmg-floating-side-banner">
          <style>
            .cmg-floating-side-banner {
              position: sticky;
              top: 110px;
              width: 160px;
              min-width: 160px;
              flex-shrink: 0;
              align-self: flex-start;
              margin-right: 0;
              background: transparent;
              border-radius: 12px;
              padding: 0;
              box-shadow: none;
              box-sizing: border-box;
              text-align: center;
              z-index: 20;
              opacity: 0;
              pointer-events: none;
              transform: translateY(20px);
              transition: opacity 0.4s ease, transform 0.4s ease;
              visibility: hidden;
            }

            .cmg-floating-side-banner.is-visible {
              opacity: 1;
              pointer-events: auto;
              transform: translateY(0);
              visibility: visible;
            }

            .cmg-side-banner-close {
              position: absolute;
              top: 6px;
              right: 6px;
              background: rgba(0, 0, 0, 0.45);
              border: none;
              color: rgba(255, 255, 255, 0.7);
              font-size: 15px;
              line-height: 1;
              cursor: pointer;
              width: 22px;
              height: 22px;
              border-radius: 50%;
              display: flex;
              align-items: center;
              justify-content: center;
              z-index: 5;
              transition: background 0.2s, color 0.2s;
              outline: none !important;
            }

            .cmg-side-banner-close:hover {
              background: rgba(0, 0, 0, 0.85);
              color: #ffffff;
            }

            .cmg-side-banner-link {
              display: block;
              text-decoration: none !important;
              border-radius: 12px;
              overflow: hidden;
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
              border-radius: 12px;
              box-shadow: 0 8px 24px rgba(9, 16, 43, 0.15);
            }

            /* Hide side banner on mobile/tablet - show as flex column on desktop */
            @media (max-width: 1024px) {
              .cmg-floating-side-banner {
                display: none !important;
                visibility: hidden !important;
                opacity: 0 !important;
                pointer-events: none !important;
              }
            }
          </style>

          <button type="button" class="cmg-side-banner-close" onclick="document.getElementById('cmg-floating-side-banner').style.display='none';" aria-label="Close">&times;</button>

          <a href="<?php echo esc_url( $audit_url ); ?>" id="blog-detail-sidebar-geo-audit" target="_blank" rel="noopener noreferrer" class="cmg-side-banner-link">
            <img src="<?php echo esc_url( $banner_img_url ); ?>" alt="Free GEO Audit - Rank Better in ChatGPT, Claude &amp; Perplexity" width="160" height="540" class="cmg-side-banner-img" loading="lazy" />
          </a>
        </div>

        <script>
        (function() {
          function initSideScrollBanner() {
            var banner = document.getElementById('cmg-floating-side-banner');
            if (!banner || banner.dataset.initialized) return;
            banner.dataset.initialized = 'true';

            var scrollThreshold = 1000; // Pixels scrolled before showing

            function handleScroll() {
              if (window.innerWidth <= 1024) {
                banner.classList.remove('is-visible');
                return;
              }
              if (window.scrollY > scrollThreshold) {
                banner.classList.add('is-visible');
              } else {
                banner.classList.remove('is-visible');
              }
            }

            window.addEventListener('scroll', handleScroll, { passive: true });
            handleScroll();
          }

          if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initSideScrollBanner);
          } else {
            initSideScrollBanner();
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
              border-radius: 50%;
              object-fit: cover;
              display: block;
              margin: 0 auto;
              box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
              border: 3px solid #ffffff;
              background: #f1f5f9;
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
              border-radius: 14px;
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
              font-size: 21px;
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
            /* Override Elementor parent container + WP content area - full width */
            .elementor-section, .e-con, .e-con-inner, .elementor-section-wrap,
            .elementor-container, .elementor-widget-container,
            .site-main, .hentry, article.post, .entry-content {
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
              padding: 0 0 0 12px;
              box-sizing: border-box;
              display: flex;
              align-items: flex-start;
              justify-content: flex-start;
              gap: 0;
            }

            /* Center blog content: 15px padding each side */
            .cmg-blog-content {
              flex: 1;
              min-width: 0;
              padding-left: 15px;
              padding-right: 15px;
              box-sizing: border-box;
            }

            /* Desktop left sticky sidebar */
            .cmg-blog-toc-sidebar {
              margin-left: 16px;
              width: 280px;
              min-width: 260px;
              position: sticky;
              top: 110px;
              max-height: calc(100vh - 140px);
              overflow-y: auto;
              box-sizing: border-box;
              z-index: 20;
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
              justify-content: space-between;
              margin-bottom: 12px;
              padding-bottom: 10px;
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
              justify-content: space-between;
              margin-bottom: 12px;
              padding-bottom: 10px;
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
              font-size: 13px !important;
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

            .cmg-blog-toc-list {
              list-style: none !important;
              margin: 0 !important;
              padding: 0 !important;
              display: flex;
              flex-direction: column;
              gap: 4px;
            }

            .cmg-blog-toc-item {
              list-style: none !important;
              margin: 0 !important;
              padding: 0 !important;
            }

            .cmg-blog-toc-link {
              display: block;
              padding: 7px 12px;
              font-size: 14px;
              line-height: 1.45;
              color: #475569 !important;
              text-decoration: none;
              border-left: 2.5px solid #e2e8f0;
              border-radius: 0 6px 6px 0;
              transition: all 0.2s ease;
              -webkit-tap-highlight-color: transparent !important;
              outline: none !important;
            }

            .cmg-blog-toc-link:hover {
              color: #0f172a !important;
              background: #f1f5f9 !important;
              border-left-color: #94a3b8 !important;
            }

            .cmg-blog-toc-link:active,
            .cmg-blog-toc-link:focus,
            .cmg-blog-toc-link:focus-visible {
              color: #161c52 !important;
              background: rgba(22, 28, 82, 0.06) !important;
              border-left-color: #22c55e !important;
              outline: none !important;
            }

            .cmg-blog-toc-link.is-active {
              color: #161c52 !important;
              font-weight: 600;
              background: rgba(22, 28, 82, 0.05) !important;
              border-left-color: #22c55e !important;
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
                border-left-color: #22c55e;
                background: #f0fdf4;
                color: #166534;
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


              // Active Scrollspy using IntersectionObserver
              const links = tocList.querySelectorAll('.cmg-blog-toc-link');
              const sheetLinks = sheetList ? sheetList.querySelectorAll('.cmg-docy-sheet-link') : [];

              if (window.IntersectionObserver) {
                const observer = new IntersectionObserver(function(entries) {
                  entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                      const id = entry.target.getAttribute('id');
                      links.forEach(function(link) {
                        if (link.getAttribute('href') === '#' + id) {
                          link.classList.add('is-active');
                        } else {
                          link.classList.remove('is-active');
                        }
                      });
                      sheetLinks.forEach(function(sLink) {
                        if (sLink.getAttribute('href') === '#' + id) {
                          sLink.classList.add('is-active');
                        } else {
                          sLink.classList.remove('is-active');
                        }
                      });
                    }
                  });
                }, {
                  rootMargin: '-100px 0px -60% 0px',
                  threshold: 0
                });

                headings.forEach(function(h2) {
                  observer.observe(h2);
                });
              }
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
