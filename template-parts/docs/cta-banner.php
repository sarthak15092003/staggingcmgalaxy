<?php
/**
 * CMGalaxy Docs Bottom CTA Banner Component
 * Same code, design, and color from docy (docy/index.php & live-style.css)
 */
?>
<section class="home-cta-banner">
    <style>
        .home-cta-banner {
            padding: 80px 0 80px 0 !important;
            clear: both;
        }

        .home-cta-banner .container {
            max-width: 1140px !important;
            margin-left: auto !important;
            margin-right: auto !important;
            padding-left: 15px !important;
            padding-right: 15px !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }

        .home-cta-banner__inner {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 40px;
            padding: 48px 56px;
            border-radius: 28px;
            background: linear-gradient(135deg, #070f2d 0%, #0a1642 55%, #0c225f 100%);
            color: #ffffff;
            overflow: hidden;
            box-sizing: border-box;
        }

        .home-cta-banner__inner::after {
            content: "";
            position: absolute;
            inset: -20%;
            z-index: 0;
            pointer-events: none;
        }

        .home-cta-banner__copy,
        .home-cta-banner__actions {
            position: relative;
            z-index: 1;
        }

        .home-cta-banner__copy {
            max-width: 460px !important;
        }

        .home-cta-banner__title {
            font-size: 28px !important;
            font-weight: 600 !important;
            line-height: 1.25 !important;
            color: #ffffff !important;
            margin: 0 0 16px 0 !important;
            letter-spacing: normal !important;
        }

        .home-cta-banner__title span {
            color: #3ddc97 !important;
        }

        .home-cta-banner__subtitle {
            font-size: 1.0625rem !important;
            color: #ffffff !important;
            margin: 0 !important;
            line-height: 1.5 !important;
        }

        .home-cta-banner__actions {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .home-cta-banner__primary,
        .home-cta-banner__secondary {
            height: 52px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: 999px !important;
            font-size: 15px !important;
            font-weight: 600 !important;
            text-decoration: none !important;
            box-sizing: border-box !important;
            line-height: 1 !important;
            transition: all 0.2s ease !important;
        }

        .home-cta-banner__primary {
            padding: 0 32px !important;
            background: #ffffff !important;
            background-color: #ffffff !important;
            background-image: none !important;
            color: #2d6cf8 !important;
        }

        .home-cta-banner__primary:hover,
        .home-cta-banner__primary:focus {
            background: #2d6cf8 !important;
            background-color: #2d6cf8 !important;
            background-image: none !important;
            color: #ffffff !important;
            transform: translateY(-2px) !important;
        }

        .home-cta-banner__secondary {
            padding: 0 28px !important;
            background-color: #34c759 !important;
            color: #ffffff !important;
            gap: 10px !important;
        }

        .home-cta-banner__secondary:hover,
        .home-cta-banner__secondary:focus {
            background-color: #28a745 !important;
            color: #ffffff !important;
            transform: translateY(-2px) !important;
        }

        .home-cta-banner__icon {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 24px !important;
            height: 24px !important;
            line-height: 1 !important;
        }

        .home-cta-banner__icon img {
            width: 20px !important;
            height: 20px !important;
            display: block !important;
        }

        @media (max-width: 991px) {
            .home-cta-banner {
                padding: 50px 0 60px 0 !important;
            }
            .home-cta-banner__inner {
                flex-direction: column !important;
                text-align: center !important;
                padding: 40px !important;
            }

            .home-cta-banner__copy {
                max-width: none !important;
            }

            .home-cta-banner__actions {
                justify-content: center !important;
            }
        }

        @media (max-width: 767px) {
            .home-cta-banner__subtitle {
                font-size: 16px !important;
            }
        }

        @media (max-width: 575px) {
            .home-cta-banner {
                padding: 36px 0 44px 0 !important;
            }
            .home-cta-banner__title {
                font-size: 24px !important;
            }

            .home-cta-banner__subtitle {
                font-size: 16px !important;
            }

            .home-cta-banner__inner {
                padding: 36px 28px !important;
                border-radius: 20px !important;
            }

            .home-cta-banner__primary,
            .home-cta-banner__secondary {
                width: 100% !important;
            }
        }
    </style>
    <div class="container">
        <div class="home-cta-banner__inner">
            <div class="home-cta-banner__copy">
                <h2 class="home-cta-banner__title">See How We Helped Businesses Like Yours <span>Grow 3x </span>Faster.</h2>
                <p class="home-cta-banner__subtitle">Let’s build a performance-driven ad strategy that works for your business.</p>
            </div>
            <div class="home-cta-banner__actions">
                <a class="home-cta-banner__primary" href="https://cmgalaxy.com/book-a-demo">Book a Demo</a>
                <a class="home-cta-banner__secondary" href="https://platform.cmgalaxy.com/sign-up">
                    <span class="home-cta-banner__icon" aria-hidden="true">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/message.svg'); ?>" alt="" width="24" height="24">
                    </span>
                    Try CMGalaxy
                </a>
            </div>
        </div>
    </div>
</section>
