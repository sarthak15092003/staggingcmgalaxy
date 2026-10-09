<?php
/**
 * Exact CMGalaxy / Docy Search Banner Component
 */

$fallback_bg = get_template_directory_uri() . '/assets/images/indexbg.svg';
$placeholder = 'Search documentation, guides, troubleshooting...';
?>

<section class="banner-bg">
    <div class="doc_banner_area search-banner-light banner_creative1 sbnr-global" style="background-image: url('<?php echo esc_url($fallback_bg); ?>'); background-size: cover; background-position: center; background-repeat: no-repeat; padding: 60px 0 50px; position: relative;">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px; text-align: center;">
            <div class="doc_banner_content">
                <h1 class="banner-support-title" style="color: #161C52; font-weight: 600; font-size: 40px; line-height: 1.25; margin-bottom: 24px;">
                    From Setup to Scale<br> Your CMGalaxy Guide, Start to Finish
                </h1>

                <form id="ajax-search-form" action="<?php echo esc_url(home_url('/')); ?>" method="get" class="header_search_form" style="max-width: 680px; margin: 0 auto;">
                    <div class="header_search_form_info">
                        <div class="stylish-search stylish-search--banner">
                            <div class="stylish-search__shell search-input-wrapper" style="position: relative; display: flex; align-items: center; gap: 8px; padding: 8px; border-radius: 999px; background: linear-gradient(135deg, rgba(58, 125, 255, 0.12), rgba(61, 220, 151, 0.12)); box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
                                <div class="stylish-search__body" style="height: 57px; flex: 1; display: flex; align-items: center; gap: 12px; padding: 0 20px; border-radius: 999px; background: #ffffff; border: 1px solid transparent; position: relative;">
                                    <span class="stylish-search__sparkle" aria-hidden="true" style="display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/lexlogo.svg'); ?>" alt="Lex Logo" width="24" height="24" loading="lazy" />
                                    </span>
                                    <input type="search" name="s" id="searchInput" class="stylish-search__input" placeholder="<?php echo esc_attr($placeholder); ?>" autocomplete="off" value="<?php echo get_search_query(); ?>" style="flex: 1; border: none !important; outline: none !important; font-size: 16px; color: #0b1f4f; background: transparent; padding: 0;" />
                                    <button type="submit" class="stylish-search__submit" aria-label="Search">
                                        <span class="stylish-search__submit-icon" aria-hidden="true">
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M14.707 13.293a1 1 0 0 1 1.32-.083l.094.083 2.5 2.5a1 1 0 0 1-1.32 1.497l-.094-.083-2.5-2.5a1 1 0 0 1 0-1.414z" fill="#ffffff" />
                                                <path d="M9 2a7 7 0 1 1 0 14A7 7 0 0 1 9 2zm0 2a5 5 0 1 0 0 10A5 5 0 0 0 9 4z" fill="#ffffff" />
                                            </svg>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</section>
