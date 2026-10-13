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

<div class="docs-main-wrapper" style="padding-top: 28px; padding-bottom: 72px;">
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
                        <li class="breadcrumb-separator">/</li>
                        <li><a href="<?php echo esc_url($docs_home_url); ?>">Docs</a></li>
                        <?php if ($first_term) : 
                            $term_link = get_term_link($first_term);
                            $term_link = is_wp_error($term_link) ? '#' : $term_link;
                        ?>
                            <li class="breadcrumb-separator">/</li>
                            <li><a href="<?php echo esc_url($term_link); ?>"><?php echo esc_html($first_term->name); ?></a></li>
                        <?php endif; ?>
                        <li class="breadcrumb-separator">/</li>
                        <li class="active-crumb" aria-current="page"><?php echo esc_html(wp_trim_words(get_the_title(), 6, '...')); ?></li>
                    </ol>
                </nav>

                <?php while (have_posts()) : the_post(); ?>
                    
                    <article id="post-<?php the_ID(); ?>" <?php post_class('doc-article-item'); ?>>
                        
                        <h1 class="single-doc-title"><?php the_title(); ?></h1>
                        
                        <!-- Author & Meta Box -->
                        <div class="doc-author-meta-box">
                            <div class="author-info">
                                Written by <strong style="color: #334155; font-weight: 600;"><?php echo get_the_author(); ?></strong> &bull; Updated <?php echo get_the_modified_date('M j, Y'); ?>
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
                                'orderby'        => array('menu_order' => 'ASC', 'ID' => 'ASC'),
                                'order'          => 'ASC',
                            ));

                            if ($related_query->have_posts()) : ?>
                                <div style="margin-top: 40px; padding: 24px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px;">
                                    <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin: 0 0 16px 0;">Related Guides & Articles</h3>
                                    <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 10px;">
                                        <?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
                                            <li>
                                                <a href="<?php the_permalink(); ?>" style="color: #3b82f6; text-decoration: none; font-size: 14.5px; font-weight: 500; display: flex; align-items: center; justify-content: space-between;">
                                                    <span><?php the_title(); ?></span>
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 17L17 7M17 7H9M17 7V15"/></svg>
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

<?php
get_footer();
