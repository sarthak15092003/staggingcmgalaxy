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

/* Shortcode [cmg_glossary] - Exact CMGalaxy.com Design */
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
          @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

          .cmg-glossary-container {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            max-width: 960px;
            margin: 0 auto;
            padding: 40px 20px;
            color: #111827;
          }

          .cmg-glossary-header {
            text-align: center;
            margin-bottom: 36px;
          }

          .cmg-glossary-title {
            font-size: 48px;
            font-weight: 800;
            color: #0b1f4f;
            margin: 0 0 12px 0;
            letter-spacing: -1px;
            line-height: 1.1;
          }

          .cmg-glossary-subtitle {
            font-size: 16px;
            color: #4b5563;
            max-width: 580px;
            margin: 0 auto;
            line-height: 1.5;
            font-weight: 400;
          }

          /* Search Box */
          .cmg-search-wrapper {
            position: relative;
            max-width: 760px;
            margin: 0 auto 32px auto;
          }

          .cmg-search-inner {
            display: flex;
            align-items: center;
            background: #ffffff;
            border: 1px solid #3A7DFF;
            border-radius: 50px;
            padding: 6px 8px 6px 20px;
            box-shadow: 0px 4px 20px rgba(58, 125, 255, 0.08);
            transition: all 0.25s ease;
          }

          .cmg-search-inner:focus-within {
            box-shadow: 0px 6px 24px rgba(58, 125, 255, 0.16);
            border-color: #2563eb;
          }

          .cmg-search-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
          }

          .cmg-search-icon svg {
            width: 20px;
            height: 20px;
          }

          .cmg-search-input {
            flex: 1;
            border: none;
            outline: none;
            background: transparent;
            font-size: 16px;
            color: #1f2937;
            font-family: inherit;
          }

          .cmg-search-input::placeholder {
            color: #9ca3af;
          }

          .cmg-search-button {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #3A7DFF;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s ease, transform 0.2s ease;
          }

          .cmg-search-button:hover {
            background: #2563eb;
            transform: scale(1.05);
          }

          .cmg-search-button svg {
            width: 18px;
            height: 18px;
            fill: #ffffff;
          }

          /* Alphabet Switch Bar */
          .cmg-alpha-bar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-bottom: 40px;
          }

          .cmg-alpha-btn {
            min-width: 34px;
            height: 34px;
            padding: 0 8px;
            border-radius: 8px;
            border: none;
            background: transparent;
            color: #1f2937;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            font-family: inherit;
          }

          .cmg-alpha-btn:hover {
            background: #f3f4f6;
            color: #3A7DFF;
          }

          .cmg-alpha-btn.active {
            background: #3A7DFF !important;
            color: #ffffff !important;
            box-shadow: 0 2px 8px rgba(58, 125, 255, 0.3);
          }

          /* Glossary List & Accordion Cards */
          .cmg-term-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
          }

          .cmg-term-card {
            background: #ffffff;
            border: 1px solid #f3f4f6;
            border-radius: 12px;
            padding: 20px 24px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            transition: all 0.25s ease;
            cursor: pointer;
          }

          .cmg-term-card:hover {
            border-color: #e5e7eb;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
          }

          .cmg-term-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
          }

          .cmg-term-left {
            display: flex;
            align-items: center;
            gap: 16px;
          }

          .cmg-term-letter-box {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: #eef2ff;
            color: #3A7DFF;
            font-size: 15px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
          }

          .cmg-term-title {
            font-size: 18px;
            font-weight: 700;
            color: #0b1f4f;
            margin: 0;
          }

          .cmg-term-chevron {
            width: 20px;
            height: 20px;
            color: #9ca3af;
            transition: transform 0.3s ease;
          }

          .cmg-term-card.open .cmg-term-chevron {
            transform: rotate(180deg);
          }

          .cmg-term-body {
            margin-top: 16px;
            padding-left: 52px;
            color: #4b5563;
            font-size: 15px;
            line-height: 1.6;
            display: none;
          }

          .cmg-term-card.open .cmg-term-body {
            display: block;
            animation: cmgFadeIn 0.3s ease;
          }

          .cmg-single-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 12px;
            font-size: 14px;
            font-weight: 600;
            color: #3A7DFF;
            text-decoration: none;
          }

          .cmg-single-link:hover {
            text-decoration: underline;
          }

          @keyframes cmgFadeIn {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
          }

          .cmg-no-results {
            text-align: center;
            padding: 40px;
            color: #6b7280;
            font-size: 15px;
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #f3f4f6;
          }

          @media (max-width: 640px) {
            .cmg-glossary-title { font-size: 32px; }
            .cmg-term-body { padding-left: 0; }
          }
        </style>

        <div class="cmg-glossary-container">
          <div class="cmg-glossary-header">
            <h1 class="cmg-glossary-title">CMGalaxy Glossary</h1>
            <p class="cmg-glossary-subtitle">A platform created to bring clarity, speed, and intelligence to every marketer's workflow.</p>
          </div>

          <!-- Search Box -->
          <div class="cmg-search-wrapper">
            <div class="cmg-search-inner">
              <span class="cmg-search-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="#3A7DFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                </svg>
              </span>
              <input type="text" id="cmg-search-input" class="cmg-search-input" placeholder="Search by term">
              <button type="button" id="cmg-search-submit" class="cmg-search-button">
                <svg viewBox="0 0 20 20">
                  <path d="M14.707 13.293a1 1 0 0 1 1.32-.083l.094.083 2.5 2.5a1 1 0 0 1-1.32 1.497l-.094-.083-2.5-2.5a1 1 0 0 1 0-1.414z"/>
                  <path d="M9 2a7 7 0 1 1 0 14A7 7 0 0 1 9 2zm0 2a5 5 0 1 0 0 10A5 5 0 0 0 9 4z"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Alphabet Switch Bar (ALL + A to Z) -->
          <div class="cmg-alpha-bar" id="cmg-alpha-bar"></div>

          <!-- Term Cards List -->
          <div class="cmg-term-list" id="cmg-term-list">
            <?php if (!empty($terms)) : ?>
              <?php foreach ($terms as $index => $item) : ?>
                <div class="cmg-term-card <?php echo $index === 0 ? 'open' : ''; ?>" data-term="<?php echo esc_attr($item['title']); ?>" data-letter="<?php echo esc_attr($item['letter']); ?>">
                  <div class="cmg-term-header">
                    <div class="cmg-term-left">
                      <div class="cmg-term-letter-box"><?php echo esc_html($item['letter']); ?></div>
                      <h3 class="cmg-term-title"><?php echo esc_html($item['title']); ?></h3>
                    </div>
                    <svg class="cmg-term-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                  </div>
                  <div class="cmg-term-body">
                    <div><?php echo $item['definition']; ?></div>
                    <a href="<?php echo esc_url($item['link']); ?>" class="cmg-single-link" target="_blank">
                      View full term page &rarr;
                    </a>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else : ?>
              <div class="cmg-no-results">No terms available. Add terms under CMG Glossary in WP Admin!</div>
            <?php endif; ?>
          </div>
        </div>

        <script>
        document.addEventListener("DOMContentLoaded", function () {
          let activeLetter = "A";
          let searchQuery = "";

          const alphaBar = document.getElementById("cmg-alpha-bar");
          const searchInput = document.getElementById("cmg-search-input");
          const cards = Array.from(document.querySelectorAll(".cmg-term-card"));

          const letters = ["ALL", ..."ABCDEFGHIJKLMNOPQRSTUVWXYZ".split("")];

          function renderAlphaBar() {
            if (!alphaBar) return;
            alphaBar.innerHTML = "";
            letters.forEach(letItem => {
              const btn = document.createElement("button");
              btn.type = "button";
              btn.className = "cmg-alpha-btn" + (letItem === activeLetter ? " active" : "");
              btn.textContent = letItem;

              btn.addEventListener("click", function () {
                activeLetter = letItem;
                document.querySelectorAll(".cmg-alpha-btn").forEach(b => b.classList.remove("active"));
                btn.classList.add("active");
                filterTerms();
              });

              alphaBar.appendChild(btn);
            });
          }

          function filterTerms() {
            let visibleCount = 0;

            cards.forEach(card => {
              const title = (card.getAttribute("data-term") || "").toLowerCase();
              const bodyEl = card.querySelector(".cmg-term-body");
              const bodyText = bodyEl ? bodyEl.textContent.toLowerCase() : "";
              const cardLetter = card.getAttribute("data-letter");

              const matchesSearch = searchQuery === "" || title.includes(searchQuery) || bodyText.includes(searchQuery);
              const matchesLetter = searchQuery !== "" ? true : (activeLetter === "ALL" || cardLetter === activeLetter);

              if (matchesSearch && matchesLetter) {
                card.style.display = "block";
                visibleCount++;
              } else {
                card.style.display = "none";
              }
            });

            let noRes = document.getElementById("cmg-no-results-box");
            const listContainer = document.getElementById("cmg-term-list");

            if (visibleCount === 0) {
              if (!noRes && listContainer) {
                noRes = document.createElement("div");
                noRes.id = "cmg-no-results-box";
                noRes.className = "cmg-no-results";
                noRes.textContent = "No glossary terms match your search.";
                listContainer.appendChild(noRes);
              }
              if (noRes) noRes.style.display = "block";
            } else {
              if (noRes) noRes.style.display = "none";
            }
          }

          if (searchInput) {
            searchInput.addEventListener("input", function (e) {
              searchQuery = e.target.value.toLowerCase().trim();
              filterTerms();
            });
          }

          cards.forEach(card => {
            card.addEventListener("click", function (e) {
              if (e.target.closest('.cmg-single-link')) return;
              card.classList.toggle("open");
            });
          });

          renderAlphaBar();
          filterTerms();
        });
        </script>
        <?php
        return ob_get_clean();
    }
}


/* Auto populate initial Glossary Terms if empty */
if (!function_exists('cmg_auto_populate_glossary')) {
    function cmg_auto_populate_glossary() {
        if (!is_admin()) return;
        $count = wp_count_posts('cmg_glossary');
        if (isset($count->publish) && $count->publish > 0) return;

        $initial_terms = array(
            array('title' => 'A/B Testing', 'def' => 'A/B testing is a method used to compare two versions of the same marketing element. For example, a brand may test two headlines, two landing pages, or two ad creatives. One group sees version A, while another group sees version B. The version that gets better results, such as more clicks, leads, or sales, is treated as the winner.'),
            array('title' => 'Above the Fold', 'def' => 'Above the fold refers to the part of a webpage that is visible without scrolling. It is the first thing a visitor sees when they land on a page. Content placed above the fold tends to get more attention. Marketers use this area for key headlines, calls to action, and offers to capture interest before a user decides to scroll or leave.'),
            array('title' => 'Account-Based Marketing', 'def' => 'Account-Based Marketing, or ABM, is a focused marketing strategy where a business targets specific companies instead of a broad audience. It is commonly used in B2B marketing. Instead of running one general campaign for everyone, marketers create personalized messages for high-value accounts. This helps sales and marketing teams work together to win important customers.'),
            array('title' => 'Ad Attribution', 'def' => 'Ad attribution helps marketers understand which ad, channel, or campaign helped bring a customer. A person may see a social media ad, click a Google ad later, and then finally buy after visiting the website directly. Attribution shows which touchpoints played a role in the journey. This helps marketers spend money on channels that actually contribute to results.'),
            array('title' => 'Ad Exchange', 'def' => 'An ad exchange is an online marketplace where advertisers and publishers buy and sell digital ad space. Publishers offer available ad spaces from websites or apps, while advertisers bid to show their ads there. This buying and selling usually happens automatically in milliseconds. Ad exchanges help advertisers reach audiences and help publishers earn money from their digital content.'),
            array('title' => 'Ad Fatigue', 'def' => 'Ad fatigue happens when people see the same ad too many times and stop paying attention to it. They may ignore it, scroll past it, or even feel annoyed by the brand. This can reduce clicks and increase advertising costs. Marketers avoid ad fatigue by refreshing creatives, testing new messages, and controlling how often the same user sees an ad.'),
            array('title' => 'Ad Fraud', 'def' => 'Ad fraud is fake or dishonest activity that wastes advertising money. It can include fake clicks, fake impressions, bot traffic, or users who are not real potential customers. Advertisers pay for these actions but do not get real value. Detecting ad fraud is important because it helps brands protect their budget and make sure campaigns reach genuine people.'),
            array('title' => 'Ad Inventory', 'def' => 'Ad inventory means the total advertising space available on a website, app, or digital platform. For example, a news website may have banner ads, video ads, and sponsored article placements. These available spaces are called inventory. Publishers sell this inventory to advertisers, either directly or through automated ad platforms, to earn revenue from their audience.'),
            array('title' => 'Ad Network', 'def' => 'An ad network connects advertisers with publishers that have ad space to sell. Instead of advertisers contacting many websites one by one, an ad network brings multiple websites together. This makes it easier for brands to reach a large audience. For publishers, ad networks help fill empty ad spaces and generate revenue without managing every advertiser directly.'),
            array('title' => 'Ad Personalization', 'def' => 'Ad personalization means showing ads that are more relevant to a person based on their interests, behavior, location, or past interactions. For example, someone who visited a travel website may later see hotel or flight ads. Personalization can improve engagement because the message feels more useful. However, it must be done carefully with privacy and consent in mind.'),
            array('title' => 'Ad Server', 'def' => 'An ad server is a platform that stores, manages, and delivers digital ads. It decides which ad should appear, where it should appear, and when it should appear. It also tracks performance such as impressions, clicks, and conversions. Ad servers help advertisers and publishers manage campaigns, control delivery, and measure how well ads are performing.'),
            array('title' => 'Ad Spend', 'def' => 'Ad spend is the total amount of money a brand invests in paid advertising over a specific period. It can refer to spend on a single campaign, a channel like Meta or Google, or across all paid channels combined. Tracking ad spend helps marketers understand how budgets are being used and compare investment against the results it generates.'),
            array('title' => 'Ad Verification', 'def' => 'Ad verification is the process of checking whether ads are being shown correctly and safely. It helps advertisers confirm that ads appear on suitable websites, reach real users, and avoid fake traffic. It also checks things like viewability, brand safety, and fraud. Ad verification protects campaign quality and helps brands make sure their media spend is not wasted.'),
            array('title' => 'Affiliate Marketing', 'def' => 'Affiliate marketing is a performance-based marketing model where a brand pays partners, called affiliates, for bringing traffic, leads, or sales. Affiliates may use blogs, videos, social media, or review websites to promote products. They usually earn a commission when someone buys or signs up through their link. This model helps brands reach new audiences with lower upfront risk.'),
            array('title' => 'Agentic AI', 'def' => 'Agentic AI refers to artificial intelligence systems that can independently plan and complete multi-step tasks without needing a human to guide every action. In marketing, agentic AI may research audiences, write content, launch campaigns, or analyze results on its own. It goes beyond generating a single output and instead works through sequences of decisions to achieve a goal.'),
            array('title' => 'AI-Powered Marketing', 'def' => 'AI-powered marketing uses artificial intelligence to make marketing faster, smarter, and more personalized. It can help with writing ad copies, analyzing campaign data, recommending audiences, predicting customer behavior, and automating tasks. For non-technical marketers, AI works like a smart assistant that studies large amounts of data and suggests better decisions to improve campaign performance.'),
            array('title' => 'Answer Engine Optimization (AEO)', 'def' => 'Answer Engine Optimization, or AEO, is the practice of structuring content so that AI-powered tools and search engines can surface it as a direct answer to user questions. As more people get answers from AI assistants and featured snippets without clicking a website, AEO helps brands remain visible in these new discovery formats. Clear definitions, structured content, and concise explanations improve AEO performance.'),
            array('title' => 'App Tracking Transparency (ATT)', 'def' => 'App Tracking Transparency, or ATT, is a privacy framework introduced by Apple that requires apps to ask users for permission before tracking them across other apps and websites. When users opt out, advertisers receive less data about their behavior. ATT significantly reduced signal availability for mobile advertising and pushed marketers toward first-party data and server-side tracking solutions.'),
            array('title' => 'Audience Segmentation', 'def' => 'Audience segmentation means dividing a large audience into smaller groups based on shared qualities. These qualities can include age, location, interests, behavior, purchase history, or website activity. For example, a brand can show one message to first-time visitors and another to repeat customers. Segmentation helps marketers create more relevant campaigns and improve engagement, leads, and sales.'),
            array('title' => 'Average Order Value (AOV)', 'def' => 'Average order value, or AOV, is the average amount a customer spends in a single transaction. It is calculated by dividing total revenue by the number of orders. For example, if a store earns ₹1,00,000 from 200 orders, the AOV is ₹500. Brands track AOV to understand purchasing behavior and find opportunities to increase revenue per transaction through bundling, upselling, or minimum cart incentives.'),
            array('title' => 'Behavioral Targeting', 'def' => 'Behavioral targeting means showing ads based on what people do online. This may include pages they visit, products they view, videos they watch, or actions they take on a website. For example, someone who browses running shoes may later see sportswear ads. Behavioral targeting helps brands reach people based on interest, but it must follow privacy rules.'),
            array('title' => 'Blended ROAS', 'def' => 'Blended ROAS is a measure of total revenue divided by total ad spend across all channels combined. Unlike platform-reported ROAS, which only looks at one channel at a time, blended ROAS gives a business-level view of advertising efficiency. D2C brands use it to understand overall marketing health, since individual platforms often count the same sale multiple times due to attribution overlap.'),
            array('title' => 'Bounce Rate', 'def' => 'Bounce rate shows the percentage of people who visit a webpage and leave without taking further action. They may not click another page, fill a form, or interact with the website. A high bounce rate may mean the page is slow, confusing, not useful, or different from what the visitor expected. Marketers use it to improve website experience.'),
            array('title' => 'Brand Safety', 'def' => 'Brand safety means making sure ads do not appear next to harmful, offensive, misleading, or unsuitable content. For example, a family-focused brand may not want its ads shown beside violent or controversial articles. Brand safety tools help advertisers control where their ads appear. This protects reputation and ensures campaigns appear in environments that match the brand’s values.'),
            array('title' => 'Buyer Persona', 'def' => 'A buyer persona is a detailed description of an ideal customer. It includes information such as age, job role, goals, challenges, interests, and buying behavior. Marketers create personas to better understand who they are trying to reach. Having a clear buyer persona helps brands create more relevant content, campaigns, and offers that connect with the right audience.'),
            array('title' => 'Call to Action', 'def' => 'A call to action, or CTA, is the instruction that tells users what to do next. Common examples include “Buy Now,” “Book a Demo,” “Download Guide,” “Sign Up,” or “Contact Us.” A strong CTA makes the next step clear and easy. In digital marketing, CTAs are used in ads, emails, landing pages, websites, and social media posts.'),
            array('title' => 'Campaign Optimization', 'def' => 'Campaign optimization is the process of improving a marketing campaign while it is running. Marketers look at performance data such as clicks, leads, conversions, cost, and audience behavior. Based on this data, they make changes to improve results. This may include changing the budget, audience, ad copy, creative, landing page, or bidding strategy.'),
            array('title' => 'Cart Abandonment Rate', 'def' => 'Cart abandonment rate measures the percentage of shoppers who add items to their cart but leave without completing the purchase. For example, if 100 people add a product to cart and only 30 buy, the abandonment rate is 70%. A high rate may indicate friction in the checkout process, unexpected costs, or hesitation. Marketers use remarketing, email sequences, and checkout improvements to recover abandoned carts.'),
            array('title' => 'Channel Attribution', 'def' => 'Channel attribution helps marketers understand which marketing channels contribute to business results. These channels may include Google Ads, social media, email, organic search, display ads, or direct website visits. Since customers often interact with many channels before converting, attribution gives a clearer view of what is working. This helps businesses plan budgets and improve campaign strategy.'),
            array('title' => 'Checkout Abandonment', 'def' => 'Checkout abandonment happens when a user begins the checkout process by entering details such as shipping or payment information but does not complete the purchase. It is different from cart abandonment, which occurs earlier in the journey. Checkout abandonment often points to trust issues, payment failures, or a complicated checkout experience. Reducing it can significantly improve conversion rates.'),
            array('title' => 'Churn Rate', 'def' => 'Churn rate measures how many customers stop using a product or service over a specific period of time. For example, if a subscription business starts with 1,000 customers and loses 50, the churn rate is 5%. A high churn rate may indicate customer dissatisfaction or stronger competition. Marketers track churn because retaining customers is often less expensive than acquiring new ones.'),
            array('title' => 'Click-Through Rate', 'def' => 'Click-through rate, or CTR, shows how many people clicked an ad or link after seeing it. It is usually shown as a percentage. For example, if 1,000 people see an ad and 50 click it, the CTR is 5%. A higher CTR often means the message, design, or offer is interesting and relevant to the audience.'),
            array('title' => 'Cohort Analysis', 'def' => 'Cohort analysis is a way of studying groups of customers who share a common characteristic. For example, marketers might analyze all customers who made their first purchase in January. By studying how different groups behave over time, businesses can understand customer retention, purchasing patterns, and long-term value. Cohort analysis helps marketers improve customer experiences and campaign performance.'),
            array('title' => 'Connected TV (CTV)', 'def' => 'Connected TV, or CTV, refers to television content streamed through the internet instead of traditional cable or satellite services. Examples include smart TVs and streaming devices. Advertisers can show targeted ads on CTV platforms based on audience data. This allows brands to combine the broad reach of television with the targeting capabilities of digital advertising.'),
            array('title' => 'Consent Management Platform', 'def' => 'A Consent Management Platform, or CMP, helps websites collect and manage user permission for cookies and data tracking. When users see a cookie banner and choose what they allow, a CMP records that choice. This is important for privacy compliance. It helps brands respect user preferences while still collecting the data they are legally allowed to use.'),
            array('title' => 'Content Marketing', 'def' => 'Content marketing means creating useful content to attract and engage an audience. This can include blogs, videos, guides, case studies, newsletters, social posts, and infographics. Instead of only selling directly, content marketing educates or entertains people. Over time, it builds trust and helps customers understand why a brand, product, or service may be useful for them.'),
            array('title' => 'Contribution Margin', 'def' => 'Contribution margin is the revenue remaining after subtracting variable costs directly tied to a sale, such as product cost, shipping, and payment fees. It shows how much each order actually contributes toward fixed costs and profit. In D2C marketing, contribution margin helps brands understand whether campaigns are generating genuinely profitable orders, not just revenue.'),
            array('title' => 'Conversion API (CAPI)', 'def' => 'Conversion API, often called CAPI, is a technology that sends conversion data directly from a company\'s server to advertising platforms. It helps marketers measure campaign results more accurately, even when browser tracking is limited. CAPI has become more important as privacy changes reduce the effectiveness of traditional tracking methods. It helps businesses maintain reliable reporting and optimization.'),
            array('title' => 'Conversion Rate', 'def' => 'Conversion rate shows the percentage of visitors who complete a desired action. This action could be buying a product, filling a form, downloading a brochure, signing up, or booking a demo. For example, if 100 people visit a page and 10 submit a form, the conversion rate is 10%. It helps measure campaign and website effectiveness.'),
            array('title' => 'Conversion Rate Optimization (CRO)', 'def' => 'Conversion rate optimization, or CRO, is the process of improving a website or landing page to increase the percentage of visitors who take a desired action. This action could be a purchase, form submission, or signup. CRO involves testing layouts, copy, images, calls to action, and page speed. The goal is to get more value from existing traffic without increasing ad spend.'),
            array('title' => 'Conversion Tracking', 'def' => 'Conversion tracking is the process of measuring important actions users take after interacting with marketing campaigns. These actions may include purchases, form submissions, calls, app installs, or signups. It helps marketers understand which ads and channels are producing real results. Without conversion tracking, a campaign may get clicks but marketers may not know whether those clicks created business value.'),
            array('title' => 'Cookie', 'def' => 'A cookie is a small piece of data stored in a user’s browser when they visit a website. Cookies can remember login details, preferences, shopping carts, or browsing behavior. In marketing, cookies have often been used to understand users and show relevant ads. Due to privacy concerns, marketers are now focusing more on consent-based and first-party data.'),
            array('title' => 'Cookieless Tracking', 'def' => 'Cookieless tracking refers to methods of measuring user behavior and campaign performance without relying on third-party cookies. As browsers and devices restrict cookie-based tracking, marketers are turning to alternatives such as first-party data, server-side tracking, hashed emails, and privacy-safe APIs. Cookieless tracking helps businesses maintain measurement accuracy while respecting user privacy.'),
            array('title' => 'Cost Per Acquisition (CPA)', 'def' => 'CPA is the average cost of getting one lead, customer, signup, or sale. For example, if a company spends ₹10,000 on ads and gets 100 leads, the CPA is ₹100. A lower CPA usually means the campaign is more efficient. Marketers track CPA to understand whether advertising spend is producing valuable results.'),
            array('title' => 'Cost Per Click (CPC)', 'def' => 'CPC is the amount an advertiser pays when someone clicks on an ad. For example, if a campaign spends ₹1,000 and gets 200 clicks, the average CPC is ₹5. CPC is common in search, social, and display ads. It helps marketers understand how much they are paying to bring visitors to a website.'),
            array('title' => 'Cost Per Mille (CPM)', 'def' => 'Cost per mille, or CPM, means the cost of 1,000 ad impressions. An impression is counted when an ad is shown to a user. CPM is commonly used in awareness campaigns where the goal is to reach many people. For example, if the CPM is ₹200, the advertiser pays ₹200 for every 1,000 ad views.'),
            array('title' => 'Creative Analysis', 'def' => 'Creative analysis is the process of studying how ad creatives perform. It looks at images, videos, headlines, colors, messages, formats, and call-to-action buttons. The goal is to understand which creatives attract attention, get clicks, and drive conversions. Creative analysis helps marketers stop guessing and make better decisions based on real performance data.'),
            array('title' => 'Cross-Channel Marketing', 'def' => 'Cross-channel marketing means communicating with customers across multiple channels. These channels can include email, social media, websites, mobile apps, paid ads, and SMS. The goal is to create a consistent experience regardless of where customers interact with the brand. Cross-channel marketing helps businesses stay connected with customers throughout their buying journey.'),
            array('title' => 'Customer Acquisition Cost (CAC)', 'def' => 'Customer acquisition cost, or CAC, is the total cost of getting one new customer. It includes ad spend, marketing tools, creative production, sales efforts, and campaign management costs. For example, if a company spends ₹50,000 to get 100 customers, the CAC is ₹500. A high CAC may mean targeting, creatives, offers, or landing pages need improvement.'),
            array('title' => 'Customer Data Platform (CDP)', 'def' => 'A Customer Data Platform, or CDP, brings customer data from different sources into one place. These sources may include websites, apps, email campaigns, CRM systems, and purchase history. A CDP helps marketers create a clearer view of each customer. This makes it easier to personalize campaigns, understand behavior, and improve customer experience across channels.'),
            array('title' => 'Customer Journey', 'def' => 'The customer journey is the full path a person takes before, during, and after buying from a brand. It may start with seeing an ad, then visiting a website, reading reviews, comparing options, and finally making a purchase. Understanding the journey helps marketers deliver the right message at the right time and remove problems that stop conversions.'),
            array('title' => 'Customer Lifetime Value (CLV)', 'def' => 'Customer lifetime value, or CLV, is the estimated total revenue a business can earn from one customer over time. For example, a customer may make one purchase today and many more in the future. CLV helps brands understand which customers are most valuable. It also helps decide how much money can be spent on acquiring and retaining customers.'),
            array('title' => 'Customer Relationship Management (CRM)', 'def' => 'Customer Relationship Management, or CRM, is a system used to manage customer and lead information. It stores details such as names, emails, phone numbers, sales conversations, past purchases, and follow-up status. CRM tools help businesses organize relationships and communicate better. For marketers, CRM data is useful for lead nurturing, personalization, and sales support.'),
            array('title' => 'Data Clean Room', 'def' => 'A data clean room is a secure space where two or more companies can compare data without directly sharing private customer information. For example, a brand and a publisher may study campaign performance while protecting user identity. Data clean rooms are becoming important as privacy rules become stricter. They help marketers learn from data in a safer way.'),
            array('title' => 'Data-Driven Attribution (DDA)', 'def' => 'Data-driven attribution, or DDA, is an attribution model that uses machine learning to assign credit to different touchpoints based on their actual contribution to a conversion. Instead of following fixed rules like giving all credit to the first or last click, DDA analyzes patterns across many customer journeys. It is now the default model in Google Ads and Google Analytics 4.'),
            array('title' => 'Data-Driven Marketing', 'def' => 'Data-driven marketing means making marketing decisions based on real data instead of guesswork. This data can include customer behavior, ad performance, website visits, sales, and engagement. For example, if data shows that one audience converts better, marketers may spend more budget there. Data-driven marketing helps improve targeting, messaging, budget use, and overall campaign performance.'),
            array('title' => 'Demand Generation', 'def' => 'Demand generation is a marketing strategy focused on creating interest in a product, service, or brand. It is not only about collecting leads, but also about educating people and building awareness. Demand generation can include content marketing, webinars, ads, email campaigns, and events. The goal is to make potential customers understand the problem and consider the brand.'),
            array('title' => 'Demand-Side Platform (DSP)', 'def' => 'A Demand-Side Platform, or DSP, is a tool advertisers use to buy digital ad space automatically. It helps advertisers choose audiences, set budgets, place bids, and run ads across websites, apps, and video platforms. Instead of buying ads manually from each publisher, advertisers use a DSP to manage large-scale programmatic campaigns from one place.'),
            array('title' => 'Digital Experience Platform (DXP)', 'def' => 'A Digital Experience Platform, or DXP, helps brands manage digital experiences across websites, apps, customer portals, and other online touchpoints. It may include content management, personalization, analytics, and customer data features. A DXP helps businesses deliver smoother and more connected experiences. For customers, this means the brand feels more consistent and helpful across channels.'),
            array('title' => 'Display Advertising', 'def' => 'Display advertising refers to visual ads shown on websites, apps, or digital platforms. These ads can include banners, images, animations, videos, or rich media formats. Display ads are often used for brand awareness, remarketing, and product promotion. They usually appear beside website content, inside apps, or before and during videos depending on the platform.'),
            array('title' => 'Dynamic Creative Optimization (DCO)', 'def' => 'Dynamic Creative Optimization, or DCO, automatically changes ad creatives based on the user, context, or performance. For example, one person may see an ad with a discount, while another sees a product recommendation. DCO uses data to show more relevant creatives. It helps advertisers personalize campaigns at scale without manually creating every possible ad version.'),
            array('title' => 'Email Marketing', 'def' => 'Email marketing is the use of emails to communicate with customers, leads, or subscribers. Brands use it to share offers, newsletters, product updates, event invitations, and educational content. Good email marketing is personalized, useful, and timely. It helps businesses stay connected with their audience, nurture leads, bring users back, and encourage repeat purchases.'),
            array('title' => 'Engagement Rate', 'def' => 'Engagement rate shows how actively people interact with content or ads. Engagement can include likes, comments, shares, clicks, saves, reactions, or video views. A high engagement rate usually means the content is interesting or relevant to the audience. Marketers use this metric to understand whether people are paying attention and responding to the message.'),
            array('title' => 'Enhanced Conversions', 'def' => 'Enhanced conversions is a Google feature that improves the accuracy of conversion measurement by securely sending hashed first-party data, such as email addresses, from a brand\'s website to Google. This helps match conversions that may have been missed due to browser restrictions or ad blockers. It works alongside existing conversion tags and is particularly useful as third-party cookie tracking becomes less reliable.'),
            array('title' => 'Event Tracking', 'def' => 'Event tracking measures specific actions people take on a website or app. These actions can include button clicks, video plays, downloads, form submissions, scroll depth, or product views. Instead of only knowing that someone visited a page, event tracking shows what they did there. This helps marketers understand user behavior and improve website or campaign performance.'),
            array('title' => 'Exit Intent', 'def' => 'Exit intent is a behavioral signal detected when a user is about to leave a webpage. Tools that detect exit intent typically track mouse movement toward the browser\'s close button or address bar. Marketers use exit intent triggers to show popups, discount offers, or lead capture forms at the moment a user is about to leave. It is commonly used on product pages and landing pages to reduce drop-off.'),
            array('title' => 'First-Click Attribution', 'def' => 'First-click attribution gives all credit for a conversion to the very first touchpoint a customer interacted with. For example, if a user first discovered a brand through a social media ad and later converted after clicking a Google search ad, the social ad gets full credit. This model is useful for understanding which channels are best at driving initial awareness and new audience discovery.'),
            array('title' => 'First-Party Cookie', 'def' => 'A first-party cookie is a cookie set by the website a user is currently visiting. Unlike third-party cookies, which are placed by external domains, first-party cookies are created and controlled by the site owner. They are used to remember user preferences, login sessions, and on-site behavior. First-party cookies are not blocked by most browsers and remain a reliable tool for tracking within a single website.'),
            array('title' => 'First-Party Data', 'def' => 'First-party data is information a business collects directly from its own audience or customers. This can include website visits, form submissions, purchase history, email signups, app activity, and customer preferences. Since the data comes directly from people interacting with the brand, it is usually more reliable. It is becoming very important as marketers reduce dependence on third-party cookies.'),
            array('title' => 'Frequency Capping', 'def' => 'Frequency capping controls how many times the same person sees the same ad within a certain time period. For example, a campaign may limit an ad to three views per user per day. This helps prevent users from feeling annoyed. It also reduces wasted ad spend and helps brands avoid ad fatigue during digital campaigns.'),
            array('title' => 'Funnel', 'def' => 'A funnel is a simple way to describe the customer journey from awareness to conversion. At the top, many people may discover the brand. In the middle, some show interest and compare options. At the bottom, fewer people take action, such as buying or submitting a form. Marketers use funnels to improve each stage of the journey.'),
            array('title' => 'Generative AI', 'def' => 'Generative AI is a type of artificial intelligence that can create new content. It can write text, design images, generate videos, create ad copy, summarize reports, and suggest ideas. In marketing, generative AI helps teams save time and scale content production. However, human review is still important to make sure the output is accurate, original, and brand-safe.'),
            array('title' => 'Generative Engine Optimization (GEO)', 'def' => 'Generative Engine Optimization, or GEO, is the practice of optimizing content so that it is selected and cited by AI-powered search tools and generative engines. As tools like AI Overviews and AI assistants increasingly summarize information for users, GEO focuses on making content authoritative, well-structured, and easy for AI systems to reference. It is an evolution of traditional SEO for the AI search era.'),
            array('title' => 'Geo-Targeting', 'def' => 'Geo-targeting means showing ads or content to people based on their location. This location can be a country, city, state, area, or even a specific radius around a place. For example, a restaurant can show ads to people nearby. Geo-targeting helps marketers make campaigns more relevant by matching offers and messages to local audiences.'),
            array('title' => 'Google Analytics 4', 'def' => 'Google Analytics 4, or GA4, is Google’s analytics platform for measuring website and app activity. It uses event-based data, which means it tracks user actions such as clicks, page views, downloads, and purchases. GA4 helps marketers understand customer journeys across websites and apps, with privacy-focused measurement features and integrations with Google’s advertising tools.'),
            array('title' => 'Google Cookiepocalypse', 'def' => 'Google Cookiepocalypse refers to the major industry discussion around Google Chrome, third-party cookies, privacy, and digital advertising. For years, marketers expected Chrome to phase out third-party cookies, which would affect tracking and targeting. Google later shifted its approach toward user choice in Chrome rather than a full immediate cookie removal, keeping privacy and first-party data important topics for advertisers.'),
            array('title' => 'Hashed Email', 'def' => 'A hashed email is an email address that has been converted into a fixed string of characters using an encryption method such as SHA-256. The original email cannot be recovered from the hash, which makes it privacy-safe. Hashed emails are used to match customer records across platforms, enable server-side tracking, and power features like enhanced conversions and customer match audiences without exposing personal data.'),
            array('title' => 'Header Bidding', 'def' => 'Header bidding is a method publishers use to let multiple advertisers bid for ad space at the same time. Earlier, some advertisers got priority before others, which could limit revenue. Header bidding creates more competition by allowing many demand sources to compete together. This can help publishers earn more and help advertisers access better ad placements.'),
            array('title' => 'Heatmap', 'def' => 'A heatmap is a visual report that shows how users interact with a webpage. It can show where people click, how far they scroll, and which parts of a page get the most attention. Warmer areas usually show more activity. Marketers use heatmaps to improve landing pages, website layouts, forms, and call-to-action placement.'),
            array('title' => 'Holdout Test', 'def' => 'A holdout test is an experiment where a portion of the target audience is deliberately excluded from a campaign to measure its true impact. The group that does not see the campaign is called the holdout group. By comparing behavior between the exposed group and the holdout group, marketers can measure incrementality, which is the actual lift a campaign generated beyond what would have happened anyway.'),
            array('title' => 'Identity Resolution', 'def' => 'Identity resolution is the process of connecting different data points to understand that they belong to the same person or customer. For example, one person may visit a website on mobile, open an email on a laptop, and later buy through an app. Identity resolution helps brands create a more complete customer view while respecting privacy and consent rules.'),
            array('title' => 'Impression', 'def' => 'An impression is counted when an ad is displayed on a screen. It does not mean the person clicked the ad or even paid close attention to it. It simply means the ad was served and had the chance to be seen. Impressions are commonly used to measure reach, visibility, and awareness in digital advertising campaigns.'),
            array('title' => 'Incrementality Testing', 'def' => 'Incrementality testing measures the true uplift a marketing campaign generates. It answers the question of whether sales or conversions would have happened even without the campaign. By comparing an exposed group with a holdout group, marketers can identify how much of the reported ROAS or conversions is genuinely caused by advertising. It is considered more reliable than traditional attribution for understanding real campaign impact.'),
            array('title' => 'Influencer Marketing', 'def' => 'Influencer marketing is a strategy where brands work with people who have an audience on social media or digital platforms. These influencers promote products, services, or experiences through posts, videos, reviews, or stories. The goal is to use their trust and reach to influence buying decisions. Good influencer marketing feels authentic and matches the audience’s interests.'),
            array('title' => 'Intent Data', 'def' => 'Intent data shows signals that suggest a person or company may be interested in buying something. These signals can include searches, website visits, content downloads, product comparisons, or repeated visits to pricing pages. Marketers use intent data to identify people who may be closer to making a decision. This helps sales and marketing teams prioritize better opportunities.'),
            array('title' => 'Invalid Traffic', 'def' => 'Invalid traffic, or IVT, refers to ad traffic that does not come from genuine human interest. It can include bots, accidental clicks, fake impressions, repeated automated visits, or suspicious activity. Invalid traffic can waste ad budgets and make campaign reports look better than they really are. Detecting IVT helps advertisers focus on real users and reliable performance.'),
            array('title' => 'Keyword Targeting', 'def' => 'Keyword targeting means showing ads or creating content based on specific words people search for or read online. For example, a business selling CRM software may target keywords like “best CRM tool” or “sales management software.” Keyword targeting helps marketers reach people who are already interested in a topic, product, or solution.'),
            array('title' => 'Landing Page', 'def' => 'A landing page is the webpage users reach after clicking an ad, email link, or social media post. It is usually designed for one clear goal, such as getting a form submission, sale, signup, or demo booking. A good landing page has a clear message, simple design, strong headline, and easy call to action.'),
            array('title' => 'Last-Click Attribution', 'def' => 'Last-click attribution gives all credit for a conversion to the final touchpoint a customer interacted with before converting. For example, if a user clicked a Google search ad just before purchasing, that ad gets full credit regardless of earlier touchpoints like social media or email. It is simple to implement but can undervalue channels that play an important role earlier in the customer journey.'),
            array('title' => 'Lead Generation', 'def' => 'Lead generation is the process of attracting potential customers and collecting their contact details. This can happen through ads, landing pages, forms, webinars, gated content, or free consultations. A lead may not be ready to buy immediately, but they have shown interest. Businesses use lead generation to build a pipeline for sales and follow-up.'),
            array('title' => 'Lead Nurturing', 'def' => 'Lead nurturing is the process of building a relationship with potential customers over time. Instead of pushing for a sale immediately, brands share helpful emails, content, offers, or reminders. This keeps the brand top-of-mind and helps the lead move closer to buying. Lead nurturing is useful when customers need time, education, or trust before making a decision.'),
            array('title' => 'Lookalike Audience', 'def' => 'A lookalike audience is a group of people who are similar to a brand’s existing customers or website visitors. Advertising platforms study the behavior and traits of current customers, then find new people who match those patterns. This helps brands reach fresh audiences who are more likely to be interested in their products or services.'),
            array('title' => 'Marketing Automation', 'def' => 'Marketing automation uses software to manage repetitive marketing tasks automatically. This can include sending emails, following up with leads, scoring prospects, posting content, or triggering messages based on user actions. For example, when someone downloads a guide, they can automatically receive a follow-up email. Automation saves time and helps marketers communicate at the right moment.'),
            array('title' => 'Marketing Cloud', 'def' => 'A marketing cloud is a group of digital tools that helps businesses manage marketing activities from one place. It may include email marketing, customer data, campaign management, personalization, analytics, and automation. Large brands use marketing clouds to connect customer experiences across channels. For marketers, it helps manage complex campaigns more smoothly and consistently.'),
            array('title' => 'Marketing Data Silos', 'def' => 'Marketing data silos happen when customer or campaign data is stored in separate tools that do not connect well with each other. For example, email data may sit in one platform, ad data in another, and sales data in a CRM. This makes it hard to see the full customer journey. Breaking silos helps marketers make better decisions.'),
            array('title' => 'Marketing Intelligence', 'def' => 'Marketing intelligence is the process of collecting and analyzing information about customers, competitors, market trends, and campaign performance. It helps businesses make smarter marketing decisions based on data rather than assumptions. Marketing intelligence allows marketers to identify opportunities, spot risks, and improve campaign effectiveness.'),
            array('title' => 'Marketing Qualified Lead', 'def' => 'A Marketing Qualified Lead, or MQL, is a person who has shown enough interest to be considered more likely to become a customer. For example, they may download a brochure, attend a webinar, visit pricing pages, or engage with emails. MQLs are usually passed from marketing to sales for further follow-up after meeting certain criteria.'),
            array('title' => 'Martech', 'def' => 'Martech, or marketing technology, refers to the collection of software and tools used to manage and improve marketing activities. It includes platforms for analytics, advertising, automation, CRM, and customer data. Martech helps marketers plan campaigns, track performance, automate tasks, and deliver more personalized and data-driven customer experiences at scale.'),
            array('title' => 'Media Buying', 'def' => 'Media buying is the process of purchasing advertising space to promote a brand, product, or service. This can include buying ads on websites, apps, search engines, social media, video platforms, or other media channels. A media buyer decides where ads should appear, how much to spend, and which audience to target for the best results.'),
            array('title' => 'Media Efficiency Ratio (MER)', 'def' => 'Media efficiency ratio, or MER, is a business-level metric calculated by dividing total revenue by total ad spend across all paid channels. It is similar to blended ROAS but is often used alongside contribution margin to measure true marketing efficiency. MER helps brands avoid over-optimizing for platform-reported numbers and instead focus on overall business health.'),
            array('title' => 'Media Mix Modeling (MMM)', 'def' => 'Media Mix Modeling, often called MMM, is a method used to understand how different marketing channels contribute to business results. It analyzes data from channels such as TV, social media, search, email, and display advertising. MMM helps marketers understand which channels create the most value and how budgets should be allocated.'),
            array('title' => 'Meta Advantage+', 'def' => 'Meta Advantage+ is a suite of automated campaign tools from Meta that uses machine learning to optimize targeting, placements, creative, and bidding with minimal manual input. It includes Advantage+ Shopping Campaigns, which are designed for e-commerce brands. While it can improve efficiency, it also gives advertisers less control over where and to whom ads are shown, making attribution and measurement more complex.'),
            array('title' => 'Meta Pixel', 'def' => 'The Meta Pixel is a piece of tracking code placed on a website that records user actions such as page views, add-to-cart events, purchases, and signups. It sends this data to Meta to help optimize ad delivery, build audiences, and measure campaign performance. Due to iOS privacy changes and browser restrictions, its accuracy has declined, making it important to supplement with Conversion API for more reliable data.'),
            array('title' => 'Mobile Marketing', 'def' => 'Mobile marketing focuses on reaching people through mobile devices such as smartphones and tablets. It can include mobile ads, SMS campaigns, app notifications, mobile-friendly websites, location-based offers, and in-app advertising. Since many users browse, shop, and search from phones, mobile marketing helps brands stay visible where customers spend a large part of their digital time.'),
            array('title' => 'Multi-Touch Attribution', 'def' => 'Multi-touch attribution gives credit to multiple marketing touchpoints that help a customer convert. Instead of giving all credit to the first or last click, it looks at the full journey. For example, social ads, email, search ads, and website visits may all influence one sale. This helps marketers understand how channels work together.'),
            array('title' => 'Multivariate Testing', 'def' => 'Multivariate testing is a method of testing multiple variables on a webpage or ad simultaneously to find the best-performing combination. Unlike A/B testing, which compares two complete versions, multivariate testing can test different headlines, images, and buttons at the same time. It requires more traffic to produce statistically significant results but provides a more detailed view of how individual elements affect performance.'),
            array('title' => 'Native Advertising', 'def' => 'Native advertising is a paid ad format that matches the look and feel of the platform where it appears. Examples include sponsored articles, promoted social posts, and recommended content widgets. Unlike traditional banner ads, native ads are designed to feel more natural. The goal is to promote a message without disrupting the user experience too much.'),
            array('title' => 'New Customer ROAS (ncROAS)', 'def' => 'New customer ROAS, or ncROAS, measures return on ad spend generated specifically from first-time buyers, excluding repeat customers. It is increasingly used by D2C brands and is a specific optimization goal within Meta\'s Advantage+ Shopping Campaigns. Since acquiring new customers is more expensive and strategically different from retaining existing ones, ncROAS helps brands evaluate whether paid campaigns are actually expanding their customer base.'),
            array('title' => 'North Star Metric', 'def' => 'A north star metric is the single most important number a business tracks to measure meaningful progress. It reflects the core value a product or service delivers to customers. For example, an e-commerce brand might use monthly active buyers as its north star, while a SaaS company might use weekly active users. All other metrics and decisions are aligned to support growth in the north star metric.'),
            array('title' => 'Omnichannel Marketing', 'def' => 'Omnichannel marketing means creating a connected experience across multiple customer touchpoints. These may include websites, apps, email, social media, ads, stores, and customer support. The goal is to make the brand experience smooth no matter where the customer interacts. For example, a user may see an ad, visit the website, receive an email, and later purchase.'),
            array('title' => 'Order Tagging', 'def' => 'Order tagging is the practice of labeling orders in a platform like Shopify with specific attributes to track their source or characteristics. For example, orders can be tagged by the campaign, channel, or promotion that drove them. Tags make it easier to segment orders, analyze performance by source, and reconcile marketing data with actual revenue without relying only on pixel-based attribution.'),
            array('title' => 'Paid Media', 'def' => 'Paid media refers to any marketing placement a brand pays for. This includes search ads, social media ads, display ads, sponsored content, video ads, influencer promotions, and programmatic ads. Paid media helps brands reach audiences faster than relying only on organic methods. Marketers use it for awareness, lead generation, sales, app installs, and remarketing.'),
            array('title' => 'Performance Marketing', 'def' => 'Performance marketing is a type of marketing focused on measurable results. These results can include clicks, leads, sales, app installs, or signups. Marketers track metrics like CPA, ROAS, conversion rate, and CTR to understand performance. Unlike general awareness campaigns, performance marketing is strongly linked to clear business outcomes and continuous optimization.'),
            array('title' => 'Personalization', 'def' => 'Personalization means changing marketing messages, content, or offers based on user data. For example, an e-commerce website may recommend products based on past browsing or purchases. An email may address the user by name and show relevant offers. Personalization makes marketing feel more useful and can improve engagement, conversions, and customer satisfaction when done responsibly.'),
            array('title' => 'Post-Purchase Survey', 'def' => 'A post-purchase survey is a short questionnaire shown to customers after they complete a purchase. It typically asks how they heard about the brand. Since it collects responses directly from customers, it provides attribution data that is not affected by tracking limitations or cookie restrictions. Many D2C brands use post-purchase surveys alongside pixel data and analytics to get a more complete picture of which channels drive sales.'),
            array('title' => 'Predictive Analytics', 'def' => 'Predictive analytics uses data to estimate what may happen in the future. In marketing, it can help predict which customers may buy, leave, respond to offers, or become high-value customers. It does not guarantee the future, but it helps marketers make smarter decisions. Brands use predictive analytics to improve targeting, personalization, retention, and campaign planning.'),
            array('title' => 'Profit on Ad Spend (POAS)', 'def' => 'Profit on ad spend, or POAS, measures the profit generated for every rupee spent on advertising, rather than just revenue. Unlike ROAS, which only looks at revenue, POAS accounts for product costs, shipping, and other expenses. A campaign with a high ROAS may still be unprofitable if margins are thin. POAS gives a more accurate view of whether advertising is creating real business value.'),
            array('title' => 'Programmatic Advertising', 'def' => 'Programmatic advertising is the automated buying and selling of digital ads using technology. Instead of manually negotiating each ad placement, platforms use data and bidding systems to decide which ad should appear for which user. This happens very quickly. Programmatic advertising helps advertisers reach specific audiences and helps publishers sell ad space more efficiently.'),
            array('title' => 'Prompt Engineering', 'def' => 'Prompt engineering is the practice of designing clear, structured inputs to get better outputs from AI tools. In marketing, it applies to tasks like writing ad copy, generating campaign ideas, summarizing data, or creating content briefs using tools like ChatGPT or Claude. Better prompts lead to more accurate, relevant, and usable outputs, making it an increasingly valuable skill for marketing teams using AI in their workflows.'),
            array('title' => 'Propensity Modeling', 'def' => 'Propensity modeling uses statistical analysis to predict the likelihood that a specific customer will take a particular action, such as purchasing, churning, or responding to an offer. In marketing, it helps teams prioritize high-potential leads, personalize campaigns, and allocate budgets more efficiently. Instead of treating all customers the same, propensity models allow brands to focus effort where it is most likely to produce results.'),
            array('title' => 'Publisher', 'def' => 'A publisher is a website, app, media company, or digital platform that creates or hosts content and has an audience. Examples include news websites, blogs, online magazines, video platforms, and gaming apps. Publishers often earn revenue by selling ad space, subscriptions, sponsored content, or partnerships. In digital advertising, publishers provide the inventory advertisers buy.'),
            array('title' => 'Real-Time Bidding', 'def' => 'Real-Time Bidding, or RTB, is an automated auction used in digital advertising. When a user opens a webpage or app, advertisers instantly bid for the chance to show their ad. The most suitable winning bid gets the placement. This process happens in milliseconds. RTB helps advertisers buy impressions based on audience value and campaign goals.'),
            array('title' => 'Remarketing', 'def' => 'Remarketing means showing ads to people who have already interacted with a brand. For example, someone may visit a website, view a product, or add something to cart but not buy. Remarketing helps bring them back with relevant ads. Since these users already know the brand, they may be more likely to convert later.'),
            array('title' => 'Repeat Purchase Rate', 'def' => 'Repeat purchase rate measures the percentage of customers who make more than one purchase from a brand over a given period. It is a key indicator of customer loyalty and product satisfaction. A higher repeat purchase rate usually means lower long-term customer acquisition costs, since retained customers require less marketing investment than new ones. D2C brands track it closely as a driver of customer lifetime value.'),
            array('title' => 'Retail Media', 'def' => 'Retail media is advertising that appears on retailer-owned platforms, such as e-commerce websites, shopping apps, or marketplace search results. For example, a brand may pay to promote its product at the top of a retailer’s product page. Retail media is growing because retailers have valuable shopping data, which helps brands reach people close to purchase.'),
            array('title' => 'Return on Ad Spend', 'def' => 'Return on ad spend, or ROAS, measures how much revenue a brand earns for every rupee spent on advertising. For example, if a campaign spends ₹10,000 and generates ₹50,000 in sales, the ROAS is 5x. A higher ROAS usually means the campaign is more profitable. Marketers use ROAS to decide which campaigns to scale.'),
            array('title' => 'Revenue Reconciliation', 'def' => 'Revenue reconciliation is the process of matching revenue figures reported by advertising platforms, analytics tools, and e-commerce platforms to ensure they align with actual bank deposits or accounting records. Discrepancies are common because platforms count conversions differently, attribute the same order to multiple channels, or include cancelled and returned orders. Reconciliation is important for making reliable business decisions based on accurate revenue data.'),
            array('title' => 'Search Engine Marketing', 'def' => 'Search Engine Marketing, or SEM, usually refers to paid advertising on search engines like Google or Bing. These ads appear when people search for specific keywords. For example, a travel company may show ads when someone searches “best holiday packages.” SEM is useful because it reaches users who are already actively looking for something.'),
            array('title' => 'Search Engine Optimization', 'def' => 'Search Engine Optimization, or SEO, is the process of improving a website so it can appear higher in unpaid search results. SEO includes using relevant keywords, creating helpful content, improving website speed, and making pages easy for search engines to understand. Good SEO helps brands get more organic traffic from people searching for related topics.'),
            array('title' => 'Second-Party Data', 'def' => 'Second-party data is another company\'s first-party data that is shared through a trusted partnership. For example, an airline and a hotel chain may share customer insights to better understand travelers. The data is usually reliable because it comes directly from a known source. Marketers use second-party data to reach new audiences while maintaining better data quality than many traditional third-party data sources.'),
            array('title' => 'Server-Side Tracking', 'def' => 'Server-side tracking is a method of collecting website or app data through a server instead of relying only on the user’s browser. It can make tracking more reliable and give businesses better control over data sharing. Marketers use it to improve measurement, reduce data loss, and support privacy-focused tracking as browser rules become stricter.'),
            array('title' => 'Session Recording', 'def' => 'A session recording is a video replay of how a user navigated through a website, including mouse movements, clicks, scrolls, and pauses. Unlike heatmaps, which aggregate behavior across many users, session recordings show individual journeys. Marketers and CRO teams use them to identify usability problems, confusing layouts, broken elements, or points where users drop off before converting.'),
            array('title' => 'Shopify Attribution', 'def' => 'Shopify attribution refers to how the Shopify platform assigns credit for orders to traffic sources and marketing channels. Shopify uses its own last-click attribution model, which can produce different numbers than Meta Ads Manager, Google Ads, or GA4. Understanding how Shopify attributes orders is important for D2C brands because relying on any single platform\'s numbers without reconciliation can lead to inaccurate campaign decisions.'),
            array('title' => 'Signal Loss', 'def' => 'Signal loss refers to the reduction in data available to advertisers for targeting, measurement, and optimization. It is primarily caused by privacy changes such as Apple\'s ATT framework, the decline of third-party cookies, and browser restrictions. When signal is lost, platforms have less information to optimize campaigns, making attribution less accurate and audience targeting less precise. Addressing signal loss is one of the central challenges in modern performance marketing.'),
            array('title' => 'Social Listening', 'def' => 'Social listening means monitoring social media platforms to understand what people are saying about a brand, competitor, industry, or topic. It goes beyond counting likes and comments. It helps brands understand customer opinions, complaints, trends, and opportunities. Marketers use social listening to improve content, respond faster, manage reputation, and learn what the audience cares about.'),
            array('title' => 'Supply-Side Platform', 'def' => 'A Supply-Side Platform, or SSP, is a tool publishers use to sell digital ad space automatically. It connects publishers with ad exchanges, DSPs, and advertisers. SSPs help manage ad inventory, pricing rules, demand partners, and revenue opportunities. The main goal is to help publishers earn more from their available ad spaces while maintaining control.'),
            array('title' => 'Synthetic Data', 'def' => 'Synthetic data is artificially generated data that mirrors the statistical patterns of real customer data without containing actual personal information. It is used in marketing and analytics to train models, test campaigns, and fill gaps in data where privacy restrictions prevent the use of real user data. Synthetic data allows teams to continue analysis and testing while remaining compliant with privacy regulations.'),
            array('title' => 'Tag Management System', 'def' => 'A Tag Management System, or TMS, helps marketers manage website tracking codes from one place. These codes, called tags, may be used for analytics, ads, remarketing, or conversion tracking. Without a TMS, adding or changing tags may require developer support. A TMS makes it easier to update tracking and reduce errors on the website.'),
            array('title' => 'Target CPA', 'def' => 'Target CPA is a Smart Bidding strategy in Google Ads where the algorithm automatically adjusts bids to achieve a desired cost per acquisition. Advertisers set the CPA they want to hit, and Google\'s system optimizes in real time based on signals like device, location, time, and user behavior. It is useful for lead generation and conversion-focused campaigns where the goal is to control the cost of each result.'),
            array('title' => 'Target ROAS', 'def' => 'Target ROAS is a Smart Bidding strategy in Google Ads where the algorithm sets bids to achieve a specific return on ad spend. For example, setting a target ROAS of 400% means the system aims to generate ₹4 in revenue for every ₹1 spent. It is commonly used for e-commerce campaigns and requires sufficient conversion data to work effectively.'),
            array('title' => 'Third-Party Cookie', 'def' => 'A third-party cookie is a cookie placed by a domain other than the website a user is visiting. It has often been used for tracking users across different websites and showing targeted ads. Due to privacy concerns, many browsers and users now limit third-party cookies. This is why marketers are focusing more on first-party data and consent-based tracking.'),
            array('title' => 'TOFU / MOFU / BOFU', 'def' => 'TOFU, MOFU, and BOFU stand for top of funnel, middle of funnel, and bottom of funnel. They describe the three stages of the customer journey. TOFU content reaches people who are discovering a brand or problem for the first time. MOFU content engages people who are comparing options or learning more. BOFU content targets people who are close to making a decision. Marketers use these stages to match the right message and format to where a customer is in their journey.'),
            array('title' => 'Tracking Pixel', 'def' => 'A tracking pixel is a tiny piece of code added to a website, email, or ad to track user actions. It can help measure page visits, purchases, signups, email opens, or ad views. Users usually do not see the pixel. Marketers use tracking pixels to understand campaign performance, build remarketing audiences, and improve future advertising.'),
            array('title' => 'Unified Customer View', 'def' => 'A Unified Customer View combines customer information from different systems into one profile. Instead of looking at separate records from websites, CRM systems, and e-commerce platforms, businesses see one complete customer profile. This helps marketers better understand behavior, personalize experiences, and improve customer relationships.'),
            array('title' => 'UTM Parameters', 'def' => 'UTM parameters are small tags added to links to track where website traffic comes from. For example, they can show whether a visitor came from a Facebook ad, email newsletter, or Google campaign. UTM data appears in analytics tools and helps marketers compare campaign performance. They are useful for understanding which channels and messages drive traffic.'),
            array('title' => 'View-Through Attribution', 'def' => 'View-through attribution gives partial or full credit to an ad that a user saw but did not click, if they later converted through another channel. For example, if someone sees a display ad, does not click it, but visits the website directly two days later and purchases, view-through attribution would credit the display ad. It is commonly used in Meta and programmatic campaigns and can inflate reported performance if not evaluated carefully alongside other attribution methods.'),
            array('title' => 'Viewability', 'def' => 'Viewability measures whether an ad had a real chance to be seen by a user. An ad may load on a page, but if it appears below the screen and the user never scrolls, it may not be viewable. Viewability helps advertisers understand whether ads are actually visible, not just served. Higher viewability usually means better media quality.'),
            array('title' => 'Walled Garden', 'def' => 'A walled garden is a digital platform that keeps most of its data, users, and advertising tools within its own system. Examples include large search, social, or e-commerce platforms. Advertisers can run campaigns inside the platform, but may have limited access to raw data. Walled gardens offer scale and targeting, but can make independent measurement harder.'),
            array('title' => 'Web Analytics', 'def' => 'Web analytics is the process of collecting and studying website data. It shows how people find a website, which pages they visit, how long they stay, and what actions they take. Marketers use web analytics to understand user behavior, improve website performance, track campaigns, and make better business decisions based on real visitor activity.'),
            array('title' => 'Web Personalization', 'def' => 'Web personalization means changing website content based on who the visitor is or what they do. For example, a returning visitor may see product recommendations, while a new visitor may see an introductory offer. Personalization can be based on location, behavior, traffic source, or past interactions. It helps create a more relevant and useful website experience.'),
            array('title' => 'Yield Optimization', 'def' => 'Yield optimization is the process publishers use to earn more revenue from their ad inventory. It involves improving pricing, demand sources, ad placements, formats, and bidding strategies. The goal is not simply to show more ads, but to make each ad opportunity more valuable. Good yield optimization balances revenue growth with a positive user experience.'),
            array('title' => 'Zero Moment of Truth', 'def' => 'Zero Moment of Truth, or ZMOT, is the stage when a customer researches a product before making a buying decision. This may include reading reviews, comparing prices, watching videos, or searching online. It happens before the person contacts a brand or visits a store. Marketers focus on ZMOT by creating helpful content, reviews, and search-friendly information.'),
            array('title' => 'Zero-Click Search', 'def' => 'Zero-click search happens when a person gets the answer directly on a search results page without clicking any website. For example, Google may show a quick answer, map result, definition, or featured snippet. This can reduce website clicks but increase brand visibility. Marketers use SEO strategies to appear in these quick-answer areas when relevant.'),
            array('title' => 'Zero-Party Data', 'def' => 'Zero-party data is information that customers willingly share with a brand. This can include preferences, interests, survey answers, product choices, or communication preferences. For example, a user may tell a fashion brand their size and style preferences. Since the customer directly provides this data, it can be very useful for personalization and privacy-friendly marketing.')
        );

        foreach ($initial_terms as $item) {
            wp_insert_post(array(
                'post_title'   => $item['title'],
                'post_content' => $item['def'],
                'post_status'  => 'publish',
                'post_type'    => 'cmg_glossary',
            ));
        }
    }
}
add_action('admin_init', 'cmg_auto_populate_glossary');


/* Ensure Shortcodes are parsed in Elementor and WordPress content */
add_filter('the_content', 'do_shortcode', 11);
add_filter('widget_text', 'do_shortcode');
if (has_filter('elementor/widget/render_content')) {
    add_filter('elementor/widget/render_content', function($content, $widget) {
        return do_shortcode($content);
    }, 10, 2);
}

add_shortcode('cmg_glossary_app', 'cmg_glossary_shortcode');
add_shortcode('cmgalaxy_glossary', 'cmg_glossary_shortcode');
