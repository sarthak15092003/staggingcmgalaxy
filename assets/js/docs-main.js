/**
 * CMGalaxy Docs & KB JavaScript
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        
        // 1. Sidebar Category Accordion Toggles
        $(document).on('click', '.section-header.expandable', function(e) {
            var $target = $(e.target);
            var isIconClick = $target.closest('.expand-icon').length > 0;
            var isLinkClick = $target.closest('a.section-title').length > 0;
            
            // If clicked on title link and NOT on the expand icon
            if (isLinkClick && !isIconClick) {
                // On single doc view, clicking category title also toggles
                if ($('body').hasClass('single-docs') || $('body').hasClass('single-post')) {
                    // allow toggle
                } else {
                    return;
                }
            }
            
            e.preventDefault();
            e.stopPropagation();
            
            var $header = $(this);
            var targetId = $header.data('target');
            if (!targetId) return;

            var $content = $('#' + targetId);
            if (!$content.length) return;

            var $icon = $header.find('.expand-icon');
            var isOpen = $header.hasClass('active') || $icon.hasClass('expanded');
            
            if (isOpen) {
                $content.stop(true, false).slideUp(200);
                $header.removeClass('active');
                $icon.removeClass('expanded');
            } else {
                $content.stop(true, false).slideDown(200);
                $header.addClass('active');
                $icon.addClass('expanded');
            }
        });

        // 2. Subcategory Item Accordion Toggles
        $(document).on('click', '.subsection-item.expandable-subcat', function(e) {
            var $target = $(e.target);
            var isIconClick = $target.closest('.expand-icon-subcat').length > 0;
            var isLinkClick = $target.closest('a.subsection-title').length > 0;
            
            // If clicked on title link and NOT on the expand icon
            if (isLinkClick && !isIconClick) {
                // In single doc view, clicking subcategory title toggles instead of reloading
                if ($('body').hasClass('single-docs') || $('body').hasClass('single-post')) {
                    // allow toggle
                } else {
                    return;
                }
            }
            
            e.preventDefault();
            e.stopPropagation();
            
            var $item = $(this);
            var targetId = $item.data('target');
            if (!targetId) return;

            var $subList = $('#' + targetId);
            if (!$subList.length) return;

            var $icon = $item.find('.expand-icon-subcat');
            var isOpen = $item.hasClass('expanded') || $icon.hasClass('expanded');
            
            if (isOpen) {
                $subList.stop(true, false).slideUp(200);
                $item.removeClass('expanded');
                $icon.removeClass('expanded');
            } else {
                $subList.stop(true, false).slideDown(200);
                $item.addClass('expanded');
                $icon.addClass('expanded');
            }
        });

        // 3. Dynamic Table of Contents (TOC) Generator & ScrollSpy (Docy exact match)
        var $tocContainer = $('#docy-toc, #docy-tocs-mobile');
        
        if ($tocContainer.length) {
            var $title = $('.single-doc-title, .post-title, article h1').first();
            var $contentArea = $('.doc-article-content, .blog_single_item, .editor-content, article').first();
            
            var $subHeadings = $contentArea.find('h2, h3, h4').filter(function() {
                var $h = $(this);
                if ($h.closest('.cm-feedback-wrapper, #cm-feedback-form, .related-guides, .related-articles-section, .cm-related-wrapper, footer, .tags-area').length > 0) {
                    return false;
                }
                var hid = ($h.attr('id') || '').toLowerCase();
                var htxt = $h.text().toLowerCase();
                if (hid.indexOf('feedback') !== -1 || htxt.indexOf('improve our product') !== -1 || htxt.indexOf('related guides') !== -1 || htxt.indexOf('related articles') !== -1) {
                    return false;
                }
                return true;
            });

            var allHeadings = [];
            if ($title.length) {
                if (!$title.attr('id')) {
                    $title.attr('id', 'doc-heading-title');
                }
                allHeadings.push($title[0]);
            }
            $subHeadings.each(function() {
                allHeadings.push(this);
            });
            var $headings = $(allHeadings);
            
            if ($headings.length > 0) {
                $('.docs-col-toc, .category-right-sidebar-col, .doc-sidebar').show();
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
                            
                            var headerHeight = 125;
                            var $header = $('.cmgalaxy-doc-header-wrap');
                            if ($header.length) {
                                headerHeight = $header.outerHeight() || 125;
                            }
                            var gap = 45; // Generous breathing room gap below sticky header
                            var offsetTop = Math.max(0, targetEl.offset().top - headerHeight - gap);
                            
                            $('html, body').stop().animate({
                                scrollTop: offsetTop
                            }, 350, function() {
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

                    var headerHeight = $('.cmgalaxy-doc-header-wrap').outerHeight() || 125;
                    var scrollPos = scrollTop + headerHeight + 55;
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
            }
        }

        // 4. CM Custom Tooltip for Sidebar (Exact Docy Match)
        (function() {
            var tt = document.getElementById('cm-global-sidebar-tooltip');
            if (!tt) {
                tt = document.createElement('div');
                tt.id = 'cm-global-sidebar-tooltip';
                tt.className = 'cm-custom-tooltip';
                document.body.appendChild(tt);
            }

            var hideTimer = null;

            document.addEventListener('mouseover', function(e) {
                if (window.innerWidth <= 1024) return;

                var link = e.target.closest('.cm-tooltip-target');
                if (!link) return;

                var text = link.getAttribute('data-cm-tooltip') || link.getAttribute('title');
                if (!text) return;

                if (hideTimer) { clearTimeout(hideTimer); hideTimer = null; }

                tt.textContent = text;
                tt.classList.add('cm-tooltip-visible');

                var rect = link.getBoundingClientRect();
                var sidebar = link.closest('.modern-sidebar') || link.closest('.docs-col-sidebar') || link.closest('.category-left-sidebar-col');
                var sidebarRight = sidebar ? sidebar.getBoundingClientRect().right : rect.right;

                tt.style.top = Math.max(10, (rect.top + (rect.height / 2) - (tt.offsetHeight / 2))) + 'px';
                tt.style.left = (sidebarRight - 15) + 'px';
            }, true);

            document.addEventListener('mouseout', function(e) {
                if (window.innerWidth <= 1024) return;

                var link = e.target.closest('.cm-tooltip-target');
                if (!link) return;

                if (e.relatedTarget && link.contains(e.relatedTarget)) return;

                hideTimer = setTimeout(function() {
                    tt.classList.remove('cm-tooltip-visible');
                }, 50);
            }, true);
        })();

        // 5. Docs Feedback Handler is managed interactively in the single-docs template (with reason prompts and DB capture)
    });

})(jQuery);
