<?php
/**
 * Single Doc Template for 'docs' Custom Post Type
 *
 * @package HelloElementor
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

get_header('docs');

get_template_part('template-parts/docs/header-docy');

$current_doc_id = get_the_ID();
$terms = get_the_terms($current_doc_id, 'doc_category');
if (!$terms || is_wp_error($terms)) {
    $terms = get_the_terms($current_doc_id, 'category');
}
$first_term = ($terms && !is_wp_error($terms)) ? $terms[0] : null;
$docs_home_url = get_post_type_archive_link('docs') ? get_post_type_archive_link('docs') : home_url('/docs/');
?>

<div class="docs-main-wrapper" style="padding-top: 0; padding-bottom: 72px;">
    <div class="docs-container">
        
        <div class="docs-layout-row">
            
            <!-- Left Modern Sidebar Navigation -->
            <div class="docs-col-sidebar category-left-sidebar-col">
                <?php get_template_part('template-parts/docs/sidebar-modern'); ?>
            </div>

            <!-- Main Doc Content Column -->
            <div class="docs-col-content category-main-col has-toc-sidebar">
                
                <!-- Breadcrumbs -->
                <nav aria-label="breadcrumb">
                    <ol class="custom-breadcrumb">
                        <li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
                        <?php if ($first_term) : 
                            $term_link = get_term_link($first_term);
                            $term_link = is_wp_error($term_link) ? '#' : $term_link;
                        ?>
                            <li class="breadcrumb-separator">/</li>
                            <li><a href="<?php echo esc_url($term_link); ?>"><?php echo esc_html($first_term->name); ?></a></li>
                        <?php endif; ?>
                        <li class="breadcrumb-separator">/</li>
                        <li class="active-crumb" aria-current="page" title="<?php echo esc_attr(get_the_title()); ?>"><?php echo esc_html(wp_trim_words(get_the_title(), 3, '...')); ?></li>
                    </ol>
                </nav>

                <?php while (have_posts()) : the_post(); ?>
                    
                    <article id="post-<?php the_ID(); ?>" <?php post_class('doc-article-item'); ?>>
                        
                        <h1 class="single-doc-title"><?php the_title(); ?></h1>
                        
                        <!-- Author & Meta Box -->
                        <?php
                        $author_name = get_the_author();
                        if (empty($author_name) || stripos($author_name, 'admin') !== false || stripos($author_name, 'cmgalaxy') !== false) {
                            $author_name = 'CMGalaxy';
                        }
                        $brand_icon_url = 'https://y9xt93xns6.onrocket.site/wp-content/uploads/2026/10/cropped-Group-1000004539-300x300-1.webp';
                        ?>
                        <div class="doc-author-meta-box" style="display: flex; align-items: center; gap: 8px; margin-bottom: 24px;">
                            <div class="author-avatar" style="width: 18px; height: 18px; flex-shrink: 0; display: inline-flex; align-items: center; justify-content: center;">
                                <img src="<?php echo esc_url($brand_icon_url); ?>" alt="<?php echo esc_attr($author_name); ?>" style="width: 18px; height: 18px; object-fit: contain; display: block;" />
                            </div>
                            <div class="author-info" style="font-size: 14px; color: #64748b; line-height: 1.4;">
                                Written by <span class="author-name" style="color: #334155; font-weight: 500;"><?php echo esc_html($author_name); ?></span>
                            </div>
                        </div>

                        <!-- Doc Body Content -->
                        <div class="doc-article-content">
                            <?php 
                            $is_preview_gate = (isset($_GET['preview_gate']) && $_GET['preview_gate'] === '1') || (isset($_GET['gated']) && $_GET['gated'] === '1');
                            $is_post_gated = function_exists('cmg_is_post_restricted_to_logged_in') && cmg_is_post_restricted_to_logged_in(get_the_ID());
                            $is_restricted_post = $is_post_gated && (!is_user_logged_in() || $is_preview_gate);

                            // Admin preview notification banner (only when explicitly previewing gate via URL)
                            if ($is_preview_gate && current_user_can('edit_posts')) : ?>
                                <div class="cmg-admin-preview-banner" style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 12px 18px; margin-bottom: 24px; font-size: 14px; color: #1e40af; display: flex; align-items: center; justify-content: space-between; gap: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                                    <span><strong>Admin Preview Active:</strong> You are currently previewing the Gated Paywall Gate as a non-logged-in visitor.</span>
                                    <a href="<?php echo esc_url(remove_query_arg(array('preview_gate', 'gated'))); ?>" style="background: #2563eb; color: #ffffff; padding: 6px 14px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 500;">Exit Preview</a>
                                </div>
                            <?php endif; ?>

                            <?php 
                            if ($is_restricted_post) {
                                if (function_exists('cmg_render_paywall_gate')) {
                                    echo cmg_render_paywall_gate(get_the_ID());
                                } else {
                                    get_template_part('template-parts/docs/modal-upgrade');
                                }
                            } else {
                                $doc_content = get_the_content();
                                $doc_content = preg_replace('/<figure\b[^>]*>.*?<\/figure>/is', '', $doc_content);
                                $doc_content = preg_replace('/<img\b[^>]*\/?>/is', '', $doc_content);
                                $doc_content = preg_replace('/[\x{1F300}-\x{1F9FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}\x{1F600}-\x{1F64F}\x{1F680}-\x{1F6FF}\x{1F1E0}-\x{1F1FF}\x{1FA00}-\x{1FA6F}\x{1FA70}-\x{1FAFF}\x{2300}-\x{23FF}\x{200D}\x{FE0F}]/u', '', $doc_content);
                                echo apply_filters('the_content', $doc_content);
                            }
                            ?>
                        </div>

                        <!-- Tags -->
                        <?php
                        $doc_tags = get_the_terms(get_the_ID(), 'doc_tag');
                        if (!$doc_tags || is_wp_error($doc_tags)) {
                            $doc_tags = get_the_tags();
                        }
                        if ($doc_tags && !is_wp_error($doc_tags)) : ?>
                            <div style="margin: 28px 0; display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                                <span style="font-size: 13.5px; font-weight: 600; color: #64748b;">Tags:</span>
                                <?php foreach ($doc_tags as $dtag) : ?>
                                    <span style="background: #f1f5f9; padding: 3px 10px; border-radius: 999px; font-size: 12.5px; color: #475569;">#<?php echo esc_html($dtag->name); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Like / Dislike Feedback Box (Imported from Docy) -->
                        <div class="cm-feedback-wrapper">
                            <div class="cm-feedback-top">
                                <h4 class="cm-feedback-title">Was this page helpful?</h4>
                                <div class="cm-feedback-buttons">
                                    <button class="cm-btn-vote" id="cm-vote-yes">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path></svg>
                                        Like
                                    </button>
                                    <button class="cm-btn-vote" id="cm-vote-no">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 15v4a3 3 0 0 0 3 3l4-9V2H5.72a2 2 0 0 0-2 1.7l-1.38 9a2 2 0 0 0 2 2.3zm7-13h3a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2h-3"></path></svg>
                                        Dislike
                                    </button>
                                </div>
                            </div>

                            <div class="cm-feedback-login-prompt" id="cm-feedback-login-prompt" style="display: none; padding: 18px 16px; text-align: center; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 10px; margin-top: 16px;">
                                <p style="margin: 0; color: #475569; font-size: 14px; line-height: 1.5;">
                                    Please <a href="<?php echo esc_url( add_query_arg('redirect_to', urlencode(get_permalink()), home_url('/signin/')) ); ?>" style="color: #3b82f6; font-weight: 600; text-decoration: underline;">Sign In</a> or <a href="<?php echo esc_url( 'https://cmgalaxy.com/book-a-demo' ); ?>" target="_blank" rel="noopener noreferrer" style="color: #3b82f6; font-weight: 600; text-decoration: underline;">Sign Up</a> to leave your feedback.
                                </p>
                            </div>
                            
                            <div class="cm-feedback-form-area" id="cm-feedback-form" style="display: none;">
                                <h3 class="cm-feedback-form-title" id="cm-feedback-title" style="margin-top: 0px;">How can we improve our product?</h3>
                                
                                <div class="cm-feedback-options" id="cm-options-yes" style="display: none;">
                                    <label class="cm-feedback-option">
                                        <input type="radio" name="cm_feedback_reason_yes" class="cm-feedback-radio" value="The guide worked as expected">
                                        The guide worked as expected
                                    </label>
                                    <label class="cm-feedback-option">
                                        <input type="radio" name="cm_feedback_reason_yes" class="cm-feedback-radio" value="It was easy to find the information I needed">
                                        It was easy to find the information I needed
                                    </label>
                                    <label class="cm-feedback-option">
                                        <input type="radio" name="cm_feedback_reason_yes" class="cm-feedback-radio" value="It was easy to understand the product and features">
                                        It was easy to understand the product and features
                                    </label>
                                    <label class="cm-feedback-option">
                                        <input type="radio" name="cm_feedback_reason_yes" class="cm-feedback-radio" value="The documentation is up to date">
                                        The documentation is up to date
                                    </label>
                                    <label class="cm-feedback-option">
                                        <input type="radio" name="cm_feedback_reason_yes" class="cm-feedback-radio" value="Something else">
                                        Something else
                                    </label>
                                </div>

                                <div class="cm-feedback-options" id="cm-options-no" style="display: none;">
                                    <label class="cm-feedback-option">
                                        <input type="radio" name="cm_feedback_reason_no" class="cm-feedback-radio" value="Help me get started faster">
                                        Help me get started faster
                                    </label>
                                    <label class="cm-feedback-option">
                                        <input type="radio" name="cm_feedback_reason_no" class="cm-feedback-radio" value="Make it easier to find what I'm looking for">
                                        Make it easier to find what I'm looking for
                                    </label>
                                    <label class="cm-feedback-option">
                                        <input type="radio" name="cm_feedback_reason_no" class="cm-feedback-radio" value="Make it easy to understand the product and features">
                                        Make it easy to understand the product and features
                                    </label>
                                    <label class="cm-feedback-option">
                                        <input type="radio" name="cm_feedback_reason_no" class="cm-feedback-radio" value="Update this documentation">
                                        Update this documentation
                                    </label>
                                    <label class="cm-feedback-option">
                                        <input type="radio" name="cm_feedback_reason_no" class="cm-feedback-radio" value="Something else">
                                        Something else
                                    </label>
                                </div>

                                <div class="cm-feedback-custom-text-wrap" id="cm-custom-text-wrap" style="display: none; margin-top: 14px; margin-bottom: 14px;">
                                    <textarea id="cm-feedback-custom-text" placeholder="Please specify your feedback..." style="width: 100%; min-height: 80px; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; font-family: inherit; box-sizing: border-box; resize: vertical; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#3B82F6'" onblur="this.style.borderColor='#d1d5db'"></textarea>
                                </div>

                                <div class="cm-feedback-actions">
                                    <button type="button" class="cm-btn-cancel" id="cm-feedback-cancel">Cancel</button>
                                    <button type="button" class="cm-btn-submit" id="cm-feedback-submit">Submit feedback</button>
                                </div>
                            </div>

                            <div class="cm-feedback-success-area" id="cm-feedback-success" style="display: none; padding: 20px 16px; text-align: center; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; margin-top: 16px;">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-bottom: 8px; display: inline-block;">
                                    <circle cx="12" cy="12" r="10" fill="#22C55E"/>
                                    <path d="M8 12L11 15L16 9" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <div id="cm-feedback-success-text" style="color: #15803d; font-weight: 600; font-size: 15px; line-height: 1.4;">Thank you for your feedback!</div>
                            </div>
                        </div>

                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const isLoggedIn = <?php echo is_user_logged_in() ? 'true' : 'false'; ?>;
                            const btnYes = document.getElementById('cm-vote-yes');
                            const btnNo = document.getElementById('cm-vote-no');
                            const formArea = document.getElementById('cm-feedback-form');
                            const loginPrompt = document.getElementById('cm-feedback-login-prompt');
                            const btnCancel = document.getElementById('cm-feedback-cancel');
                            const btnSubmit = document.getElementById('cm-feedback-submit');
                            const title = document.getElementById('cm-feedback-title');
                            const optionsYes = document.getElementById('cm-options-yes');
                            const optionsNo = document.getElementById('cm-options-no');
                            const customWrap = document.getElementById('cm-custom-text-wrap');
                            const customTextarea = document.getElementById('cm-feedback-custom-text');
                            const successArea = document.getElementById('cm-feedback-success');
                            const successText = document.getElementById('cm-feedback-success-text');
                            
                            function updateSubmitButtonState() {
                                if (!btnSubmit) return;
                                const selected = document.querySelector('.cm-feedback-radio:checked');
                                if (!selected) {
                                    btnSubmit.classList.remove('active');
                                    btnSubmit.disabled = true;
                                    return;
                                }

                                if (selected.value === 'Something else') {
                                    const textVal = customTextarea ? customTextarea.value.trim() : '';
                                    if (textVal.length > 0) {
                                        btnSubmit.classList.add('active');
                                        btnSubmit.disabled = false;
                                    } else {
                                        btnSubmit.classList.remove('active');
                                        btnSubmit.disabled = true;
                                    }
                                } else {
                                    btnSubmit.classList.add('active');
                                    btnSubmit.disabled = false;
                                }
                            }

                            function resetCustomText() {
                                if (customWrap) customWrap.style.display = 'none';
                                if (customTextarea) customTextarea.value = '';
                                updateSubmitButtonState();
                            }

                            function hideSuccess() {
                                if (successArea) successArea.style.display = 'none';
                            }
                            
                            function showForm(vote) {
                                hideSuccess();

                                if (vote === 'like' || vote === 'yes') {
                                    btnYes.classList.add('active');
                                    btnNo.classList.remove('active');
                                } else {
                                    btnNo.classList.add('active');
                                    btnYes.classList.remove('active');
                                }

                                if (!isLoggedIn) {
                                    if (formArea) formArea.style.display = 'none';
                                    if (loginPrompt) loginPrompt.style.display = 'block';
                                    return;
                                }

                                if (loginPrompt) loginPrompt.style.display = 'none';
                                if (formArea) formArea.style.display = 'block';
                                
                                document.querySelectorAll('.cm-feedback-radio').forEach(r => r.checked = false);
                                resetCustomText();
                                updateSubmitButtonState();
                                
                                if (vote === 'like' || vote === 'yes') {
                                    title.innerText = 'Great! What worked best for you?';
                                    optionsYes.style.display = 'flex';
                                    optionsNo.style.display = 'none';
                                } else {
                                    title.innerText = 'How can we improve our product?';
                                    optionsNo.style.display = 'flex';
                                    optionsYes.style.display = 'none';
                                }
                            }

                            document.querySelectorAll('.cm-feedback-radio').forEach(radio => {
                                radio.addEventListener('change', function() {
                                    if (this.checked && this.value === 'Something else') {
                                        if (customWrap) customWrap.style.display = 'block';
                                        if (customTextarea) customTextarea.focus();
                                    } else {
                                        if (customWrap) customWrap.style.display = 'none';
                                    }
                                    updateSubmitButtonState();
                                });
                            });

                            if (customTextarea) {
                                customTextarea.addEventListener('input', updateSubmitButtonState);
                                customTextarea.addEventListener('keyup', updateSubmitButtonState);
                            }
                            
                            if (btnYes) btnYes.addEventListener('click', () => showForm('like'));
                            if (btnNo) btnNo.addEventListener('click', () => showForm('dislike'));
                            
                            if (btnCancel) {
                                btnCancel.addEventListener('click', () => {
                                    if (formArea) formArea.style.display = 'none';
                                    if (loginPrompt) loginPrompt.style.display = 'none';
                                    btnYes.classList.remove('active');
                                    btnNo.classList.remove('active');
                                    document.querySelectorAll('.cm-feedback-radio').forEach(r => r.checked = false);
                                    resetCustomText();
                                    updateSubmitButtonState();
                                    hideSuccess();
                                });
                            }
                            
                            if (btnSubmit) {
                                btnSubmit.addEventListener('click', () => {
                                    const selected = document.querySelector('.cm-feedback-radio:checked');
                                    if (!selected) {
                                        return;
                                    }
                                    if (selected.value === 'Something else' && customTextarea && !customTextarea.value.trim()) {
                                        customTextarea.focus();
                                        return;
                                    }
                                    
                                    const vote = btnYes.classList.contains('active') ? 'like' : 'dislike';
                                    let reason = selected.value;
                                    if (reason === 'Something else' && customTextarea) {
                                        const customVal = customTextarea.value.trim();
                                        if (customVal) {
                                            reason = 'Something else: ' + customVal;
                                        }
                                    }
                                    const postId = <?php echo get_the_ID(); ?>;
                                    const postTitle = <?php echo json_encode(get_the_title()); ?>;
                                    const userId = <?php echo get_current_user_id(); ?>;
                                    const userName = <?php $cu = wp_get_current_user(); echo json_encode( $cu->exists() ? ( $cu->display_name ? $cu->display_name : $cu->user_login ) : '' ); ?>;
                                    const userEmail = <?php $cu = wp_get_current_user(); echo json_encode( $cu->exists() ? $cu->user_email : '' ); ?>;
                                    
                                    const formData = new FormData();
                                    formData.append('action', 'cm_submit_feedback');
                                    formData.append('post_id', postId);
                                    formData.append('post_title', postTitle);
                                    formData.append('user_id', userId);
                                    formData.append('user_name', userName);
                                    formData.append('user_email', userEmail);
                                    formData.append('vote', vote);
                                    formData.append('reason', reason);
                                    
                                    // Disable submit button while saving
                                    const originalText = btnSubmit.innerText;
                                    btnSubmit.innerText = 'Saving...';
                                    btnSubmit.disabled = true;

                                    fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                                        method: 'POST',
                                        body: formData
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if(data.success) {
                                            const thankMsg = (vote === 'dislike' || vote === 'no')
                                                ? 'Thank you for your feedback! We will get back to you.'
                                                : 'Thank you for your feedback!';
                                                
                                            if (formArea) formArea.style.display = 'none';
                                            if (successText) successText.innerText = thankMsg;
                                            if (successArea) successArea.style.display = 'block';
                                        } else {
                                            alert(data.data || 'Something went wrong. Please try again.');
                                        }
                                    })
                                    .catch(error => {
                                        alert('Error saving feedback.');
                                        console.error(error);
                                    })
                                    .finally(() => {
                                        btnSubmit.innerText = originalText;
                                        btnSubmit.disabled = false;
                                        btnYes.classList.remove('active');
                                        btnNo.classList.remove('active');
                                        document.querySelectorAll('.cm-feedback-radio').forEach(r => r.checked = false);
                                        resetCustomText();
                                    });
                                });
                            }
                        });
                        </script>

                        <!-- Related Articles -->
                        <?php
                        if ($first_term) {
                            $related_query = new WP_Query(array(
                                'post_type'      => array('docs', 'post'),
                                'tax_query'      => array(
                                    array(
                                        'taxonomy' => $first_term->taxonomy,
                                        'field'    => 'term_id',
                                        'terms'    => $first_term->term_id
                                    )
                                ),
                                'post__not_in'   => array(get_the_ID()),
                                'posts_per_page' => 4,
                                'orderby'        => array('menu_order' => 'ASC', 'date' => 'ASC', 'ID' => 'ASC'),
                                'order'          => 'ASC',
                            ));

                            if ($related_query->have_posts()) : ?>
                                <div class="related-articles-section" style="margin-top: 3.0rem; margin-bottom: 1.5rem;">
                                    <h5 class="fw-semibold mb-3" id="related-articles" style="color: #484A61 !important; font-size: 20px !important; font-weight: 600 !important; margin: 0 0 16px 0;">Related Articles</h5>
                                    <ul class="list-unstyled mb-0" style="border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; padding: 0; margin: 0; list-style: none;">
                                        <?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
                                            <li style="border-bottom: 1px solid #e5e7eb; margin: 0; padding: 0; list-style: none;">
                                                <a href="<?php the_permalink(); ?>" class="d-flex justify-content-between align-items-center related-article-link">
                                                    <span><?php the_title(); ?></span>
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="related-article-icon">
                                                        <path d="M7 17L17 7M17 7H9M17 7V15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </a>
                                            </li>
                                        <?php endwhile; wp_reset_postdata(); ?>
                                    </ul>
                                </div>
                            <?php endif;
                        }
                        ?>

                    </article>

                <?php endwhile; ?>

            </div>

            <!-- Right TOC Column (Sticky) -->
            <div class="docs-col-toc category-right-sidebar-col doc-sidebar">
                <div class="doc-toc-wrap">
                    <div class="doc-toc-title">On this page</div>
                    <nav id="docy-toc"></nav>
                </div>
            </div>

        </div>

    </div>
</div>

<!-- Docy Mobile Floating Bottom Action Bar & Sheets (Mobile View) -->
<div class="sc-jtXEFf jrBzsJ" id="cm-bottom-action-bar">
    <div class="sc-eldieg eYVFtH">
        <div class="overlay" id="toc-overlay"></div>
        <button class="sc-kiIyQV fqmceZ table_content" aria-expanded="false" aria-controls="docy-tocs">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 6h16M4 12h16M4 18h16" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
            <span>On this Page</span>
        </button>
        <aside class="bottom_table_content" id="docy-tocs" aria-hidden="true">
            <button class="close-toc" aria-label="Close Table of Contents">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
            <h6 class="toc-title mb-3">On this Page</h6>
            <nav class="nav-sidebar doc-nav" id="docy-tocs-mobile"></nav>
        </aside>
        <button class="sc-kiIyQV fqmceZ table_share_btn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path>
                <polyline points="16 6 12 2 8 6"></polyline>
                <line x1="12" y1="2" x2="12" y2="15"></line>
            </svg>
            <span>Share</span>
        </button>
        <div class="docy-modal-content" id="share-modal" aria-hidden="true">
            <button class="close docy-close" aria-label="Close Share Modal">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
            <div class="docy-share-wrap">
                <p class="share-modal-title">Share</p>
                <div class="social-links" style="display: flex; align-items: center; justify-content: center; gap: 14px; flex-wrap: nowrap;">
                    <!-- Copy Link Button -->
                    <button type="button" class="share-social-icon cm-copy-action-btn" data-url="<?php the_permalink(); ?>" title="Copy link" style="width: 46px; height: 46px; border-radius: 50%; background: #f1f5f9; border: 1px solid #e2e8f0; display: inline-flex; align-items: center; justify-content: center; color: #3b82f6; cursor: pointer; padding: 0; flex-shrink: 0;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                        </svg>
                    </button>
                    <!-- WhatsApp -->
                    <a href="https://api.whatsapp.com/send?text=<?php echo urlencode(get_the_title() . ' ' . get_permalink()); ?>" target="_blank" rel="noopener noreferrer" class="share-social-icon" title="WhatsApp" style="width: 46px; height: 46px; border-radius: 50%; background: #25D366; display: inline-flex; align-items: center; justify-content: center; color: #ffffff; text-decoration: none; flex-shrink: 0;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                        </svg>
                    </a>
                    <!-- Facebook -->
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" target="_blank" rel="noopener noreferrer" class="share-social-icon" title="Facebook" style="width: 46px; height: 46px; border-radius: 50%; background: #1877F2; display: inline-flex; align-items: center; justify-content: center; color: #ffffff; text-decoration: none; flex-shrink: 0;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                    <!-- LinkedIn -->
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode(get_permalink()); ?>" target="_blank" rel="noopener noreferrer" class="share-social-icon" title="LinkedIn" style="width: 46px; height: 46px; border-radius: 50%; background: #0A66C2; display: inline-flex; align-items: center; justify-content: center; color: #ffffff; text-decoration: none; flex-shrink: 0;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                        </svg>
                    </a>
                    <!-- Twitter/X -->
                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" target="_blank" rel="noopener noreferrer" class="share-social-icon" title="X" style="width: 46px; height: 46px; border-radius: 50%; background: #000000; display: inline-flex; align-items: center; justify-content: center; color: #ffffff; text-decoration: none; flex-shrink: 0;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                    </a>
                </div>
                <div class="cm-copy-toast" style="display: none; color: #10b981; font-size: 13px; text-align: center; margin-top: 16px; font-weight: 500;">Link copied to clipboard!</div>
            </div>
        </div>
    </div>
</div>

<script>
(function($) {
    'use strict';
    $(document).ready(function() {
        var tocVisible = false;
        var shareModalVisible = false;

        // Toggle Table of Contents sheet
        $('.table_content').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var $toc = $('#docy-tocs');
            var $overlay = $('#toc-overlay');

            if ($toc.is(':visible')) {
                $toc.hide();
                $overlay.hide();
                tocVisible = false;
            } else {
                $('#share-modal').hide();
                shareModalVisible = false;
                $toc.css({'display': 'flex'}).show();
                $overlay.show();
                tocVisible = true;
            }
        });

        // Close TOC when clicking close button
        $('.close-toc').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $('#docy-tocs').hide();
            $('#toc-overlay').hide();
            tocVisible = false;
        });

        // Automatically close mobile TOC sheet when any item link is clicked
        $(document).on('click', '#docy-tocs a, #docy-tocs-mobile a, .bottom_table_content a', function() {
            $('#docy-tocs').hide();
            $('#toc-overlay').hide();
            tocVisible = false;
        });

        // Toggle Share Modal
        $('.table_share_btn').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var $modal = $('#share-modal');
            var $overlay = $('#toc-overlay');

            if (shareModalVisible) {
                $modal.hide();
                $overlay.hide();
                shareModalVisible = false;
            } else {
                $('#docy-tocs').hide();
                tocVisible = false;
                $modal.show();
                $overlay.show();
                shareModalVisible = true;
            }
        });

        // Close Share Modal
        $('.docy-close').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $('#share-modal').hide();
            $('#toc-overlay').hide();
            shareModalVisible = false;
        });

        // Close when clicking outside overlay
        $('#toc-overlay').on('click', function(e) {
            e.preventDefault();
            $('#docy-tocs').hide();
            $('#share-modal').hide();
            $(this).hide();
            tocVisible = false;
            shareModalVisible = false;
        });

        // Copy link action button
        $(document).on('click', '.cm-copy-action-btn', function(e) {
            e.preventDefault();
            var url = $(this).attr('data-url') || window.location.href;
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(url);
            } else {
                var $temp = $('<input>');
                $('body').append($temp);
                $temp.val(url).select();
                document.execCommand("copy");
                $temp.remove();
            }
            var $toast = $('#share-modal .cm-copy-toast');
            if ($toast.length) {
                $toast.stop().fadeIn(200).delay(1800).fadeOut(300);
            }
        });

        // Footer observer to hide mobile action bar near page bottom
        function checkFooterHideBar() {
            var $bar = $('#cm-bottom-action-bar');
            var $footer = $('footer, .footer_area, .doc_footer_top, .footer-content, .footer-copyright, #colophon').first();
            if (!$bar.length || !$footer.length) return;

            var footerRect = $footer[0].getBoundingClientRect();
            var vh = window.innerHeight || document.documentElement.clientHeight;
            if (footerRect.top <= vh + 15) {
                $bar.addClass('hidden-by-footer');
            } else {
                $bar.removeClass('hidden-by-footer');
            }
        }

        window.addEventListener('scroll', checkFooterHideBar, { passive: true });
        window.addEventListener('resize', checkFooterHideBar, { passive: true });
        checkFooterHideBar();
    });
})(jQuery);
</script>

<!-- CMGalaxy Bottom CTA Banner -->
<?php get_template_part('template-parts/docs/cta-banner'); ?>

<?php
get_footer();
