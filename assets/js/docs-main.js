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
            
            $('.docs-col-toc, .category-right-sidebar-col, .doc-sidebar').show();

            var $subHeadings = $contentArea.find('h2, h3, h4').filter(function() {
                var $h = $(this);
                if ($h.closest('.cm-feedback-wrapper, #cm-feedback-form, .related-guides, .related-articles-section, .cm-related-wrapper, footer, .tags-area, .cmg-upgrade-card, .cmg-modal-overlay').length > 0) {
                    return false;
                }
                var hid = ($h.attr('id') || '').toLowerCase();
                var htxt = $h.text().toLowerCase();
                if (hid.indexOf('feedback') !== -1 || htxt.indexOf('improve our product') !== -1 || htxt.indexOf('related guides') !== -1 || htxt.indexOf('related articles') !== -1 || htxt.indexOf('access everything') !== -1) {
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
                var scrollTimeout = null;
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
                            
                            var headerHeight = 110;
                            var $header = $('.cmgalaxy-doc-header-wrap');
                            if ($header.length) {
                                headerHeight = $header.outerHeight() || 110;
                            }
                            var gap = 30; // Clean breathing room below sticky header
                            var offsetTop = Math.max(0, Math.round(targetEl.offset().top - headerHeight - gap));
                            
                            if (scrollTimeout) {
                                clearTimeout(scrollTimeout);
                            }

                            // Perform buttery smooth scroll
                            if ('scrollBehavior' in document.documentElement.style) {
                                window.scrollTo({
                                    top: offsetTop,
                                    behavior: 'smooth'
                                });
                            } else {
                                $('html, body').stop().animate({
                                    scrollTop: offsetTop
                                }, 400);
                            }

                            if (window.history && window.history.pushState) {
                                window.history.pushState(null, '', '#' + targetId);
                            }

                            // Keep scrollspy locked during scroll transition to prevent active item flickering/jerking
                            scrollTimeout = setTimeout(function() {
                                isSmoothScrolling = false;
                                setActiveToc(targetId);
                            }, 600);
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

                    var headerHeight = $('.cmgalaxy-doc-header-wrap').outerHeight() || 110;
                    var scrollPos = scrollTop + headerHeight + 50;
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

            function handleMouseOver(e) {
                if (window.innerWidth <= 1024) return;

                var item = e.target.closest('.sidebar-subcat-post-item, .cm-tooltip-target, .subsection-item, .section-header');
                if (!item) return;

                var targetEl = item.matches('.cm-tooltip-target') ? item : (item.querySelector('.cm-tooltip-target') || item.querySelector('a') || item);
                var text = targetEl.getAttribute('data-cm-tooltip') || targetEl.getAttribute('title') || item.getAttribute('data-cm-tooltip') || (targetEl.querySelector('span') ? targetEl.querySelector('span').textContent.trim() : targetEl.textContent.trim());
                if (!text) return;

                if (hideTimer) { clearTimeout(hideTimer); hideTimer = null; }

                tt.textContent = text;
                tt.classList.add('cm-tooltip-visible');

                var rect = item.getBoundingClientRect();
                var sidebar = item.closest('.modern-sidebar') || item.closest('.docs-col-sidebar') || item.closest('.category-left-sidebar-col');
                var sidebarRight = sidebar ? sidebar.getBoundingClientRect().right : (rect.right + 20);

                tt.style.top = Math.max(10, (rect.top + (rect.height / 2) - (tt.offsetHeight / 2))) + 'px';
                tt.style.left = (sidebarRight - 15) + 'px';
            }

            function handleMouseOut(e) {
                if (window.innerWidth <= 1024) return;

                var item = e.target.closest('.sidebar-subcat-post-item, .cm-tooltip-target, .subsection-item, .section-header');
                if (!item) return;

                if (e.relatedTarget && item.contains(e.relatedTarget)) return;

                hideTimer = setTimeout(function() {
                    tt.classList.remove('cm-tooltip-visible');
                }, 50);
            }

            document.addEventListener('mouseover', handleMouseOver, true);
            document.addEventListener('mouseout', handleMouseOut, true);
        })();

        // 5. Docs Feedback Handler is managed interactively in the single-docs template (with reason prompts and DB capture)
    });

})(jQuery);
