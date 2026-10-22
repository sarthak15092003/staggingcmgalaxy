<?php
/**
 * CMGalaxy Docs Bottom CTA Banner Component
 * Matches exact mobile & desktop design from docs.cmgalaxy.com
 */

$demo_url = 'https://cmgalaxy.com/book-a-demo';
$try_url  = 'https://app.cmgalaxy.com/';
?>

<section class="cmg-docs-growth-banner-area">
    <div class="cmg-docs-growth-card">
        <div class="cmg-docs-growth-content">
            <h2 class="cmg-docs-growth-heading">
                See How We Helped<br>
                Businesses Like<br>
                Yours <span class="cmg-docs-growth-green">Grow 3x</span> Faster.
            </h2>
            <p class="cmg-docs-growth-subtext">
                Let’s build a performance-driven ad strategy that works for your business.
            </p>
            <div class="cmg-docs-growth-buttons">
                <a href="<?php echo esc_url($demo_url); ?>" class="cmg-docs-btn-demo" target="_blank" rel="noopener noreferrer">
                    Book a Demo
                </a>
                <a href="<?php echo esc_url($try_url); ?>" class="cmg-docs-btn-try" target="_blank" rel="noopener noreferrer">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                    </svg>
                    <span>Try CMGalaxy</span>
                </a>
            </div>
        </div>
    </div>
</section>

<style>
.cmg-docs-growth-banner-area {
    max-width: 1140px;
    margin: 40px auto 60px auto;
    padding: 0 20px;
    box-sizing: border-box;
    clear: both;
}

.cmg-docs-growth-card {
    background: #0B1930;
    background: linear-gradient(145deg, #0D1C36 0%, #071328 100%);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 24px;
    padding: 48px 32px;
    text-align: center;
    position: relative;
    overflow: hidden;
    box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.35);
    box-sizing: border-box;
}

.cmg-docs-growth-card::before {
    content: "";
    position: absolute;
    top: -80px;
    right: 20%;
    width: 280px;
    height: 280px;
    background: radial-gradient(circle, rgba(34, 197, 94, 0.12) 0%, rgba(34, 197, 94, 0) 70%);
    border-radius: 50%;
    pointer-events: none;
}

.cmg-docs-growth-content {
    position: relative;
    z-index: 2;
    max-width: 680px;
    margin: 0 auto;
}

.cmg-docs-growth-heading {
    font-size: 32px !important;
    line-height: 1.25 !important;
    font-weight: 600 !important;
    color: #ffffff !important;
    margin: 0 0 16px 0 !important;
    letter-spacing: -0.02em !important;
}

.cmg-docs-growth-green {
    color: #22c55e !important;
    font-weight: 600 !important;
}

.cmg-docs-growth-subtext {
    font-size: 15px !important;
    line-height: 1.55 !important;
    color: #94a3b8 !important;
    margin: 0 auto 30px auto !important;
    max-width: 480px !important;
    font-weight: 400 !important;
}

.cmg-docs-growth-buttons {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 14px;
    margin: 0 auto;
}

.cmg-docs-btn-demo {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    background: #ffffff !important;
    color: #0f172a !important;
    font-size: 15px !important;
    font-weight: 600 !important;
    padding: 13px 30px !important;
    border-radius: 9999px !important;
    text-decoration: none !important;
    white-space: nowrap !important;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12) !important;
    transition: all 0.2s ease !important;
    box-sizing: border-box !important;
}

.cmg-docs-btn-demo:hover {
    background: #f8fafc !important;
    color: #0f172a !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2) !important;
}

.cmg-docs-btn-try {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
    background: #22c55e !important;
    color: #ffffff !important;
    font-size: 15px !important;
    font-weight: 600 !important;
    padding: 13px 30px !important;
    border-radius: 9999px !important;
    text-decoration: none !important;
    white-space: nowrap !important;
    box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35) !important;
    transition: all 0.2s ease !important;
    box-sizing: border-box !important;
}

.cmg-docs-btn-try:hover {
    background: #16a34a !important;
    color: #ffffff !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 8px 22px rgba(34, 197, 94, 0.45) !important;
}

@media (max-width: 640px) {
    .cmg-docs-growth-banner-area {
        margin: 28px auto 40px auto !important;
        padding: 0 16px !important;
    }

    .cmg-docs-growth-card {
        padding: 38px 20px 34px !important;
        border-radius: 20px !important;
    }

    .cmg-docs-growth-heading {
        font-size: 26px !important;
        line-height: 1.25 !important;
    }

    .cmg-docs-growth-subtext {
        font-size: 14.5px !important;
        margin-bottom: 26px !important;
    }

    .cmg-docs-growth-buttons {
        flex-direction: column !important;
        width: 100% !important;
        max-width: 320px !important;
        gap: 12px !important;
    }

    .cmg-docs-btn-demo,
    .cmg-docs-btn-try {
        width: 100% !important;
        padding: 13px 20px !important;
        box-sizing: border-box !important;
    }
}
</style>
