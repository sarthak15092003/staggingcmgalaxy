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
        var $contentArea = $('.doc-article-content').first();
        if (!$contentArea.length) {
            $contentArea = $('.blog_single_item, .editor-content, article').first();
        }
        var $tocContainer = $('#docy-toc, #docy-tocs-mobile');
        
        if ($contentArea.length && $tocContainer.length) {
            // ONLY select headings in the main article body.
            // Explicitly filter out feedback box, related guides, or footer headings!
            var $headings = $contentArea.find('h2, h3').filter(function() {
                var $h = $(this);
                if ($h.closest('.cm-feedback-wrapper, #cm-feedback-form, .related-guides, .cm-related-wrapper, footer, .tags-area').length > 0) {
                    return false;
                }
                var hid = ($h.attr('id') || '').toLowerCase();
                var htxt = $h.text().toLowerCase();
                if (hid.indexOf('feedback') !== -1 || htxt.indexOf('improve our product') !== -1 || htxt.indexOf('related guides') !== -1) {
                    return false;
                }
                return true;
            });
            
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
                    
                    var isActiveFirst = (idx === 0) ? ' active' : '';
                    tocListHtml += '<li class="nav-item toc-' + hTag + isActiveFirst + '">';
                    tocListHtml += '<a class="nav-link' + isActiveFirst + '" href="#' + hId + '">' + cleanText + '</a>';
                    tocListHtml += '</li>';
                });
                
                tocListHtml += '</ul>';
                $tocContainer.html(tocListHtml);

                var isSmoothScrolling = false;

                function setActiveToc(id) {
                    if (!id) return;
                    $tocContainer.find('.nav-link').removeClass('active');
                    $tocContainer.find('li').removeClass('active');
                    
                    var $currentLink = $tocContainer.find('a[href="#' + id + '"]');
                    if ($currentLink.length) {
                        $currentLink.addClass('active');
                        $currentLink.closest('li').addClass('active');
                    }
                }

                // Smooth scroll on TOC link click
                $(document).on('click', '#docy-toc a, #docy-tocs-mobile a', function(e) {
                    var href = $(this).attr('href');
                    if (href && href.charAt(0) === '#') {
                        var targetEl = $(href);
                        if (targetEl.length) {
                            e.preventDefault();
                            var targetId = href.substring(1);
                            setActiveToc(targetId);
                            isSmoothScrolling = true;

                            // Close mobile TOC bottom popup sheet & overlay
                            $('#docy-tocs').hide();
                            $('#toc-overlay').hide();
                            
                            var offsetTop = targetEl.offset().top - 100;
                            $('html, body').stop().animate({
                                scrollTop: offsetTop
                            }, 300, function() {
                                setTimeout(function() {
                                    isSmoothScrolling = false;
                                    setActiveToc(targetId);
                                }, 60);
                            });
                        }
                    }
                });

                // ScrollSpy Highlighting
                function updateTocScrollSpy() {
                    if (isSmoothScrolling) return;
                    if (!$headings.length) return;
                    
                    var scrollTop = $(window).scrollTop();
                    var windowHeight = $(window).height();
                    var docHeight = $(document).height();

                    // If user is scrolled near bottom of page, activate last heading
                    if (scrollTop + windowHeight >= docHeight - 60) {
                        var lastId = $headings.last().attr('id');
                        setActiveToc(lastId);
                        return;
                    }

                    var scrollPos = scrollTop + 130;
                    var activeId = '';
                    
                    $headings.each(function() {
                        var headingTop = $(this).offset().top;
                        if (headingTop <= scrollPos) {
                            activeId = $(this).attr('id');
                        }
                    });

                    // If before first heading or near top, activate first heading
                    if (!activeId) {
                        activeId = $headings.first().attr('id');
                    }

                    setActiveToc(activeId);
                }

                $(window).on('scroll', updateTocScrollSpy);
                updateTocScrollSpy();
            } else {
                $('.docs-col-toc').hide();
            }
        }

        // 4. Docs Feedback Handler is managed interactively in the single-docs template (with reason prompts and DB capture)
    });

})(jQuery);
