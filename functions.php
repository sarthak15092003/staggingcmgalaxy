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
	}
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_scripts_styles' );

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
            justify-content: space-between;
          }
          
          .wd-term-left {
            display: flex;
            align-items: center;
            gap: 12px;
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
                  <div class="wd-term-card" data-term="<?php echo esc_attr($item['title']); ?>" data-letter="<?php echo esc_attr($item['letter']); ?>">
                    <div class="wd-term-header">
                      <div class="wd-term-left">
                        <div class="wd-term-icon"><?php echo esc_html($item['letter']); ?></div>
                        <h3 class="wd-term-title" style="margin:0;"><a href="<?php echo esc_url($item['link']); ?>" class="wd-term-title-link"><?php echo esc_html($item['title']); ?></a></h3>
                      </div>
                      <svg class="wd-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                      </svg>
                    </div>
                    <div class="wd-term-definition">
                      <div><?php echo $item['definition']; ?></div>
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
              const defEl = card.querySelector(".wd-term-definition");
              const def = defEl ? defEl.textContent.toLowerCase() : "";
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
          
          cards.forEach(card => {
            card.addEventListener("click", (e) => {
              if (e.target.closest('.wd-term-title-link')) return;
              card.classList.toggle("open");
            });
          });

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

          .cmg-single-term-wrapper {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            max-width: 860px;
            margin: 40px auto;
            padding: 0 20px;
            color: #111827;
          }

          .cmg-single-breadcrumb {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 24px;
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
          }

          .cmg-single-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
            padding-bottom: 20px;
            border-bottom: 1px solid #f3f4f6;
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
          }

          .cmg-single-body {
            font-size: 18px;
            line-height: 1.7;
            color: #374151;
            margin-bottom: 32px;
          }

          .cmg-single-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 20px;
            border-top: 1px solid #f3f4f6;
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
              <div class="cmg-single-icon"><?php echo esc_html($letter); ?></div>
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
