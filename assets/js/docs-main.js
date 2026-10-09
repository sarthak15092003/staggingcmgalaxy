/**
 * CMGalaxy Docs & KB JavaScript
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        
        // 1. Sidebar Category Accordion Toggles
        $(document).on('click', '.section-header.expandable', function(e) {
            // Prevent triggering if clicked directly on category title link
            if ($(e.target).is('a.section-title')) {
                return;
            }
            e.preventDefault();
            var $header = $(this);
            var targetId = $header.data('target');
            var $content = $('#' + targetId);
            
            $header.toggleClass('active');
            $header.find('.expand-icon').toggleClass('expanded');
            $content.slideToggle(200);
        });

        // 2. Subcategory Item Accordion Toggles
        $(document).on('click', '.subsection-item.expandable-subcat', function(e) {
            if ($(e.target).is('a.subsection-title') || $(e.target).closest('a').length) {
                // allow link navigation
                return;
            }
            e.preventDefault();
            var targetId = $(this).data('target');
            var $subList = $('#' + targetId);
            var $icon = $(this).find('.expand-icon-subcat');
            
            $subList.slideToggle(200);
            if ($icon.length) {
                var isExpanded = $subList.is(':visible');
                $icon.css('transform', isExpanded ? 'rotate(270deg)' : 'rotate(180deg)');
            }
        });

        // 3. Dynamic Table of Contents (TOC) Generator & ScrollSpy
        var $contentArea = $('.doc-article-content, .blog_single_item, .editor-content, article').first();
        var $tocContainer = $('#docy-toc, #docy-tocs-mobile');
        
        if ($contentArea.length && $tocContainer.length) {
            var $headings = $contentArea.find('h2, h3');
            
            if ($headings.length > 0) {
                var tocListHtml = '<ul class="nav flex-column">';
                var emojiRegex = /[\u{1F300}-\u{1F9FF}\u{2600}-\u{26FF}\u{2700}-\u{27BF}\u{1F600}-\u{1F64F}\u{1F680}-\u{1F6FF}\u{1F1E0}-\u{1F1FF}\u{1FA00}-\u{1FA6F}\u{1FA70}-\u{1FAFF}\u{2300}-\u{23FF}\u{200D}\u{FE0F}]/gu;
                
                $headings.each(function(idx) {
                    var $h = $(this);
                    var hId = $h.attr('id');
                    if (!hId) {
                        hId = 'doc-heading-' + (idx + 1);
                        $h.attr('id', hId);
                    }
                    var hTag = this.tagName.toLowerCase();
                    var rawText = $h.text().trim();
                    var cleanText = rawText.replace(emojiRegex, '').replace(/\s+/g, ' ').replace(/^[\s\-–—:]+|[\s\-–—:]+$/g, '').trim();
                    
                    if (!cleanText) {
                        cleanText = rawText;
                    }
                    
                    tocListHtml += '<li class="nav-item toc-' + hTag + '">';
                    tocListHtml += '<a class="nav-link" href="#' + hId + '">' + cleanText + '</a>';
                    tocListHtml += '</li>';
                });
                
                tocListHtml += '</ul>';
                $tocContainer.html(tocListHtml);

                // Smooth scroll on TOC link click
                $(document).on('click', '#docy-toc a, #docy-tocs-mobile a', function(e) {
                    var href = $(this).attr('href');
                    if (href && href.charAt(0) === '#') {
                        var targetEl = $(href);
                        if (targetEl.length) {
                            e.preventDefault();
                            var offsetTop = targetEl.offset().top - 100;
                            $('html, body').stop().animate({
                                scrollTop: offsetTop
                            }, 300);
                        }
                    }
                });

                // ScrollSpy Highlighting
                $(window).on('scroll', function() {
                    var scrollPos = $(window).scrollTop() + 140;
                    var activeId = '';
                    
                    $headings.each(function() {
                        if (scrollPos >= $(this).offset().top) {
                            activeId = $(this).attr('id');
                        }
                    });

                    if (activeId) {
                        $tocContainer.find('.nav-link').removeClass('active');
                        $tocContainer.find('li').removeClass('active');
                        
                        var $currentLink = $tocContainer.find('a[href="#' + activeId + '"]');
                        if ($currentLink.length) {
                            $currentLink.addClass('active');
                            $currentLink.closest('li').addClass('active');
                        }
                    }
                });
            } else {
                $('.docs-col-toc').hide();
            }
        }

        // 4. Docs Feedback Handler
        $(document).on('click', '.cm-btn-vote', function() {
            var $btn = $(this);
            var voteType = $btn.data('vote');
            var postId = $btn.data('post-id');
            
            $('.cm-btn-vote').removeClass('active');
            $btn.addClass('active');

            // Send feedback
            if (typeof cmg_docs_ajax !== 'undefined') {
                $.post(cmg_docs_ajax.ajax_url, {
                    action: 'cm_submit_feedback',
                    post_id: postId,
                    vote: voteType,
                    nonce: cmg_docs_ajax.nonce
                }, function(res) {
                    $('.cm-feedback-top').html('<p style="color: #10b981; font-weight: 500; margin: 0;">Thank you for your feedback!</p>');
                });
            } else {
                $('.cm-feedback-top').html('<p style="color: #10b981; font-weight: 500; margin: 0;">Thank you for your feedback!</p>');
            }
        });

    });

})(jQuery);
