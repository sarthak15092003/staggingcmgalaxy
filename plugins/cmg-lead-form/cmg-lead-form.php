<?php
/**
 * Plugin Name: CMGalaxy Lead Form (Book A Demo)
 * Plugin URI: https://www.cmgalaxy.com/
 * Description: Standalone plugin providing the [cmg_lead_form] and [book_a_demo_form] shortcodes with Amplitude analytics and international phone input.
 * Version: 1.0.0
 * Author: CMGalaxy
 * License: GPLv2 or later
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
        .iti { display: block !important; width: 100% !important; }
        .iti input, .iti__tel-input { 
            width: 100% !important; 
            padding-top: 16px !important; 
            padding-bottom: 16px !important; 
            padding-right: 14px !important; 
            border-radius: 8px !important; 
            border: 1px solid #dde3f0 !important; 
            font-size: 15px !important; 
            box-sizing: border-box !important;
            background-color: #ffffff !important;
            height: auto !important;
            line-height: normal !important;
        }
        .iti input:focus {
            border-color: #3a7dff !important;
            box-shadow: 0 0 0 1px rgba(58, 125, 255, 0.08) !important;
        }

        .lead-form-wrapper {
            max-width: 800px;
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
