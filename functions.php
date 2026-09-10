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
 * ============================================================
 * CMGALAXY LEAD FORM ("BOOK A DEMO") SHORTCODE
 * Usage: [cmg_lead_form] or [book_a_demo_form]
 * ============================================================
 */
function cmg_lead_form_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'api_url'      => 'https://staging-api.cmgalaxy.com/api/v2/event_emailer/cmgalaxy-enquiry/',
        'redirect_url' => 'https://www.cmgalaxy.com/thank-you',
        'event_name'   => 'Book A Demo Sendmessage Clicked',
        'section_name' => 'Book A Demo Form',
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
            const API_URL = "<?php echo esc_url( $atts['api_url'] ); ?>";
            const REDIRECT_URL = "<?php echo esc_url( $atts['redirect_url'] ); ?>";
            const EVENT_NAME = "<?php echo esc_js( $atts['event_name'] ); ?>";
            const SECTION_NAME = "<?php echo esc_js( $atts['section_name'] ); ?>";

            const form = document.getElementById("lead-form");
            const statusEl = document.getElementById("form-status");
            const submitBtn = document.getElementById("lead-submit-btn");
            const phoneInput = document.getElementById("phone-input");
            const dialHidden = document.getElementById("countryDialHidden");

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

                if (!fullName) return showError("? Please enter your full name.", "full_name");
                document.getElementById("full_name").style.borderColor = "#dde3f0";

                if (!emailAddress) return showError("? Please enter your email address.", "email_address");
                document.getElementById("email_address").style.borderColor = "#dde3f0";

                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailAddress)) return showError("? Please enter a valid email address.", "email_address");

                if (!phoneNumber) return showError("? Please enter your phone number.", "phone-input");

                if (!companyName) return showError("? Please enter your company name.", "company_name");
                document.getElementById("company_name").style.borderColor = "#dde3f0";

                if (!adSpend) return showError("? Please select your ad spend.", "ad_spend");
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
                    else if (errorCode === 2) errorMessage = expectedLength ? Number too short for $countryName. Expected $expectedLength digits. : Number too short for $countryName.;
                    else if (errorCode === 3) errorMessage = expectedLength ? Number too long for $countryName. Expected $expectedLength digits. : Number too long for $countryName.;
                    else errorMessage = expectedLength ? Invalid phone number for $countryName. Expected $expectedLength digits. : Invalid phone number for $countryName.;

                    return showError("? " + errorMessage, "phone-input");
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
                submitBtn.textContent = "Sending�";

                try {
                    const response = await fetch(API_URL, {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify(payload)
                    });

                    if (!response.ok) throw new Error("API failed");

                    if (window.amplitude) {
                        amplitude.logEvent(EVENT_NAME, {
                            initiated_at: document.title || window.location.pathname,
                            section_at: SECTION_NAME
                        });
                    }

                    localStorage.removeItem('utm_data');
                    statusEl.textContent = "? Form submitted successfully!";
                    statusEl.className = "form-status success";
                    form.reset();

                    if (REDIRECT_URL) {
                        setTimeout(() => { window.location.href = REDIRECT_URL; }, 1000);
                    }

                } catch (err) {
                    console.error(err);
                    statusEl.textContent = "? Submission failed. Please try again.";
                    statusEl.className = "form-status error";
                }

                submitBtn.disabled = false;
                submitBtn.textContent = "Send Message";
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
add_shortcode( 'cmg_lead_form', 'cmg_lead_form_shortcode' );
add_shortcode( 'book_a_demo_form', 'cmg_lead_form_shortcode' );


/**
 * CMGalaxy Lead Form ("Book A Demo") WordPress Shortcode
 * 
 * Usage:
 * [cmg_lead_form] or [book_a_demo_form]
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function cmg_lead_form_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'api_url'      => 'https://api.cmgalaxy.com/api/v2/event_emailer/cmgalaxy-enquiry/',
        'redirect_url' => 'https://www.cmgalaxy.com/thank-you',
        'event_name'   => 'Book A Demo Sendmessage Clicked',
        'section_name' => 'Book A Demo Form',
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
            const API_URL = "<?php echo esc_url( $atts['api_url'] ); ?>";
            const REDIRECT_URL = "<?php echo esc_url( $atts['redirect_url'] ); ?>";
            const EVENT_NAME = "<?php echo esc_js( $atts['event_name'] ); ?>";
            const SECTION_NAME = "<?php echo esc_js( $atts['section_name'] ); ?>";

            const form = document.getElementById("lead-form");
            const statusEl = document.getElementById("form-status");
            const submitBtn = document.getElementById("lead-submit-btn");
            const phoneInput = document.getElementById("phone-input");
            const dialHidden = document.getElementById("countryDialHidden");

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

                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 15000); // 15s timeout

                try {
                    const response = await fetch(API_URL, {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify(payload),
                        signal: controller.signal
                    });

                    clearTimeout(timeoutId);

                    if (!response.ok) throw new Error("API failed with status " + response.status);

                    if (window.amplitude) {
                        amplitude.logEvent(EVENT_NAME, {
                            initiated_at: document.title || window.location.pathname,
                            section_at: SECTION_NAME
                        });
                    }

                    localStorage.removeItem('utm_data');
                    statusEl.textContent = "✅ Form submitted successfully!";
                    statusEl.className = "form-status success";
                    form.reset();

                    if (REDIRECT_URL) {
                        setTimeout(() => { window.location.href = REDIRECT_URL; }, 1000);
                    }

                } catch (err) {
                    clearTimeout(timeoutId);
                    console.error(err);
                    if (err.name === 'AbortError') {
                        statusEl.textContent = "❌ Request timed out. Please try again.";
                    } else {
                        statusEl.textContent = "❌ Submission failed. Please try again.";
                    }
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
add_shortcode( 'cmg_lead_form', 'cmg_lead_form_shortcode' );
add_shortcode( 'book_a_demo_form', 'cmg_lead_form_shortcode' );
