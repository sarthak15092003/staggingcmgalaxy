/**
 * CMGalaxy Docs & KB JavaScript
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        
        // 1. Sidebar Category Accordion Toggles
        $(document).on('click', '.section-header.expandable', function(e) {
            var isIconClick = $(e.target).closest('.expand-icon').length > 0;
            // If clicked on title link and NOT on the expand icon
            if (!isIconClick && $(e.target).closest('a.section-title').length) {
                // On single doc view, clicking category title also toggles
                if ($('body').hasClass('single-docs') || $('body').hasClass('single-post')) {
                    // allow toggle
                } else {
                    return;
                }
            }
            
            e.preventDefault();
            var $header = $(this);
            var targetId = $header.data('target');
            var $content = $('#' + targetId);
            var $icon = $header.find('.expand-icon');
            
            var isCurrentlyOpen = $content.is(':visible');
            if (isCurrentlyOpen) {
                $content.slideUp(200);
                $header.removeClass('active');
                $icon.removeClass('expanded');
            } else {
                $content.slideDown(200);
                $header.addClass('active');
                $icon.addClass('expanded');
            }
        });

        // 2. Subcategory Item Accordion Toggles
        $(document).on('click', '.subsection-item.expandable-subcat', function(e) {
            var isIconClick = $(e.target).closest('.expand-icon-subcat').length > 0;
            // If clicked on title link and NOT on the expand icon
            if (!isIconClick && $(e.target).closest('a.subsection-title').length) {
                // In single doc view, clicking subcategory title toggles instead of reloading
                if ($('body').hasClass('single-docs') || $('body').hasClass('single-post')) {
                    // allow toggle
                } else {
                    return;
                }
            }
            
            e.preventDefault();
            var $item = $(this);
            var targetId = $item.data('target');
            var $subList = $('#' + targetId);
            var $icon = $item.find('.expand-icon-subcat');
            
            var isCurrentlyOpen = $subList.is(':visible');
            if (isCurrentlyOpen) {
                $subList.slideUp(200);
                $item.removeClass('expanded');
                $icon.removeClass('expanded');
            } else {
                $subList.slideDown(200);
                $item.addClass('expanded');
                $icon.addClass('expanded');
            }
        });

        // Dedicated click handler for subcategory expand icon
        $(document).on('click', '.expand-icon-subcat', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var $item = $(this).closest('.subsection-item.expandable-subcat');
            if ($item.length) {
                var targetId = $item.data('target');
                var $subList = $('#' + targetId);
                var $icon = $(this);
                
                var isCurrentlyOpen = $subList.is(':visible');
                if (isCurrentlyOpen) {
                    $subList.slideUp(200);
                    $item.removeClass('expanded');
                    $icon.removeClass('expanded');
                } else {
                    $subList.slideDown(200);
                    $item.addClass('expanded');
                    $icon.addClass('expanded');
                }
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

        // 4. Docs Feedback Handler is managed interactively in the single-docs template (with reason prompts and DB capture)
    });

})(jQuery);
