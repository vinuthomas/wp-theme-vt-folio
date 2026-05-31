(function () {
    'use strict';

    /* ── Dark mode ─────────────────────────────────────────────── */

    var html     = document.documentElement;
    var toggle   = document.getElementById('theme-toggle');
    var iconSun  = toggle && toggle.querySelector('.icon-sun');
    var iconMoon = toggle && toggle.querySelector('.icon-moon');

    function applyTheme(theme) {
        html.setAttribute('data-theme', theme);
        if (!toggle) return;
        var isDark = theme === 'dark';
        // Update aria-pressed to reflect current state:
        // pressed = dark mode is active
        toggle.setAttribute('aria-pressed', String(isDark));
        // Update label to describe the *action* (what clicking will do next)
        toggle.setAttribute('aria-label', isDark ? 'Switch to light mode' : 'Switch to dark mode');
        if (iconSun && iconMoon) {
            iconSun.style.display  = isDark ? 'none' : '';
            iconMoon.style.display = isDark ? '' : 'none';
        }
    }

    // header.php already sets data-theme inline to prevent FOUC;
    // here we just sync the icon state and ARIA on DOMContentLoaded.
    var currentTheme = html.getAttribute('data-theme') || 'light';
    applyTheme(currentTheme);

    if (toggle) {
        toggle.addEventListener('click', function () {
            var next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            applyTheme(next);
            localStorage.setItem('vt-theme', next);
        });
    }

    /* ── Reading progress bar ───────────────────────────────────── */

    var progressBar = document.getElementById('reading-progress');
    var isSingle    = document.body.classList.contains('single');

    if (progressBar && isSingle) {
        var article = document.querySelector('.entry-content');

        if (article) {
            // Cache article geometry so the scroll handler never triggers
            // a forced synchronous layout (getBoundingClientRect / offsetHeight
            // inside a scroll listener causes a layout read-after-write each
            // frame).  We invalidate the cache on resize.
            var articleTop    = 0;
            var articleHeight = 0;

            function cacheArticleGeometry() {
                articleTop    = article.getBoundingClientRect().top + window.scrollY;
                articleHeight = article.offsetHeight;
            }
            cacheArticleGeometry();

            window.addEventListener('resize', cacheArticleGeometry, { passive: true });

            document.addEventListener('scroll', function () {
                if (articleHeight === 0) return;
                var scrolled = window.scrollY - articleTop;
                // scaleX(0..1) matches the CSS change from width to transform:scaleX
                var ratio = Math.min(1, Math.max(0, scrolled / articleHeight));
                progressBar.style.transform = 'scaleX(' + ratio + ')';
            }, { passive: true });
        }
    }

    /* ── Search overlay ────────────────────────────────────────── */

    var searchToggle  = document.getElementById('search-toggle');
    var searchOverlay = document.getElementById('search-overlay');
    var searchClose   = document.getElementById('search-close');
    var searchInput   = searchOverlay && searchOverlay.querySelector('.search-overlay__input');

    // Returns all interactive elements inside the overlay that can receive focus
    function getFocusableInOverlay() {
        return Array.prototype.slice.call(
            searchOverlay.querySelectorAll(
                'a[href], button:not([disabled]), input:not([disabled]), [tabindex]:not([tabindex="-1"])'
            )
        );
    }

    function openSearch() {
        searchOverlay.classList.add('is-open');
        searchOverlay.setAttribute('aria-hidden', 'false');
        searchToggle.setAttribute('aria-expanded', 'true');
        // Update toggle label to reflect its new action
        searchToggle.setAttribute('aria-label', 'Close search');
        if (searchInput) { searchInput.focus(); searchInput.select(); }
    }

    function closeSearch() {
        searchOverlay.classList.remove('is-open');
        searchOverlay.setAttribute('aria-hidden', 'true');
        searchToggle.setAttribute('aria-expanded', 'false');
        searchToggle.setAttribute('aria-label', 'Open search');
        // Return focus to the toggle button that opened the overlay
        searchToggle.focus();
    }

    if (searchToggle && searchOverlay) {
        searchToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            searchOverlay.classList.contains('is-open') ? closeSearch() : openSearch();
        });

        if (searchClose) {
            searchClose.addEventListener('click', function () {
                closeSearch();
            });
        }

        // Trap focus inside overlay when it is open (Tab / Shift+Tab cycle)
        searchOverlay.addEventListener('keydown', function (e) {
            if (e.key !== 'Tab') return;
            var focusable = getFocusableInOverlay();
            if (!focusable.length) return;
            var first = focusable[0];
            var last  = focusable[focusable.length - 1];
            if (e.shiftKey) {
                if (document.activeElement === first) {
                    e.preventDefault();
                    last.focus();
                }
            } else {
                if (document.activeElement === last) {
                    e.preventDefault();
                    first.focus();
                }
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && searchOverlay.classList.contains('is-open')) {
                closeSearch();
            }
        });

        document.addEventListener('click', function (e) {
            if (searchOverlay.classList.contains('is-open') &&
                !searchOverlay.contains(e.target) &&
                !searchToggle.contains(e.target)) {
                closeSearch();
            }
        });
    }

    /* ── Mobile menu ────────────────────────────────────────────── */

    var menuToggle = document.getElementById('menu-toggle');
    var navMenu    = document.querySelector('.nav-menu');

    function openMenu() {
        menuToggle.setAttribute('aria-expanded', 'true');
        menuToggle.setAttribute('aria-label', 'Close menu');
        navMenu.classList.add('is-open');
        // Move focus to the first menu item for keyboard users
        var firstLink = navMenu.querySelector('a');
        if (firstLink) { firstLink.focus(); }
    }

    function closeMenu() {
        menuToggle.setAttribute('aria-expanded', 'false');
        menuToggle.setAttribute('aria-label', 'Open menu');
        navMenu.classList.remove('is-open');
        // Return focus to the toggle button
        menuToggle.focus();
    }

    if (menuToggle && navMenu) {
        menuToggle.addEventListener('click', function () {
            if (this.getAttribute('aria-expanded') === 'true') {
                closeMenu();
            } else {
                openMenu();
            }
        });

        // Close menu on Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && menuToggle.getAttribute('aria-expanded') === 'true') {
                closeMenu();
            }
        });

        // Close menu on outside click
        document.addEventListener('click', function (e) {
            if (menuToggle.getAttribute('aria-expanded') === 'true' &&
                !menuToggle.contains(e.target) && !navMenu.contains(e.target)) {
                closeMenu();
            }
        });
    }


    /* ── Gallery lightbox ───────────────────────────────────────── */

    var lbEl = document.createElement('div');
    lbEl.className = 'vt-lightbox';
    lbEl.setAttribute('role', 'dialog');
    lbEl.setAttribute('aria-modal', 'true');
    lbEl.setAttribute('aria-label', 'Image viewer');
    lbEl.setAttribute('aria-hidden', 'true');
    lbEl.innerHTML =
        '<button class="vt-lightbox__close" aria-label="Close">' +
            '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M18 6L6 18M6 6l12 12"/></svg>' +
        '</button>' +
        '<button class="vt-lightbox__nav vt-lightbox__nav--prev" aria-label="Previous image">' +
            '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg>' +
        '</button>' +
        '<div class="vt-lightbox__inner">' +
            '<img class="vt-lightbox__img" src="" alt="" />' +
            '<p class="vt-lightbox__caption"></p>' +
        '</div>' +
        '<button class="vt-lightbox__nav vt-lightbox__nav--next" aria-label="Next image">' +
            '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M9 18l6-6-6-6"/></svg>' +
        '</button>';
    document.body.appendChild(lbEl);

    var lbImg      = lbEl.querySelector('.vt-lightbox__img');
    var lbCap      = lbEl.querySelector('.vt-lightbox__caption');
    var lbCloseBtn = lbEl.querySelector('.vt-lightbox__close');
    var lbPrevBtn  = lbEl.querySelector('.vt-lightbox__nav--prev');
    var lbNextBtn  = lbEl.querySelector('.vt-lightbox__nav--next');

    var lbGroup    = [];
    var lbIndex    = 0;
    var lbTrigger  = null;

    function lbGetFullUrl(img) {
        var a = img.parentElement;
        if (a && a.tagName === 'A' && a.href) return a.href;
        var srcset = img.getAttribute('srcset') || '';
        if (srcset) {
            var best = srcset.split(',').reduce(function (acc, entry) {
                var parts = entry.trim().split(/\s+/);
                var w = parseInt(parts[1]) || 0;
                return w > acc.w ? { url: parts[0], w: w } : acc;
            }, { url: '', w: 0 });
            if (best.url) return best.url;
        }
        return img.src;
    }

    function lbGetCaption(img) {
        var fig = img.parentElement;
        while (fig && fig.tagName !== 'FIGURE') { fig = fig.parentElement; }
        var cap = fig && fig.querySelector('figcaption');
        return cap ? cap.textContent.trim() : (img.alt ? img.alt.trim() : '');
    }

    function lbShow(index) {
        lbIndex = index;
        var item = lbGroup[index];
        lbImg.alt = item.alt;
        lbImg.src = item.url;
        lbCap.textContent = item.caption;
        lbCap.hidden = !item.caption;
        lbPrevBtn.hidden = lbGroup.length < 2;
        lbNextBtn.hidden = lbGroup.length < 2;
    }

    function lbOpen(group, index, trigger) {
        lbGroup   = group;
        lbTrigger = trigger;
        lbShow(index);
        lbEl.classList.add('is-open');
        lbEl.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        lbCloseBtn.focus();
    }

    function lbClose() {
        lbEl.classList.remove('is-open');
        lbEl.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        if (lbTrigger) { lbTrigger.focus(); }
    }

    lbCloseBtn.addEventListener('click', lbClose);
    lbPrevBtn.addEventListener('click', function () {
        lbShow((lbIndex - 1 + lbGroup.length) % lbGroup.length);
    });
    lbNextBtn.addEventListener('click', function () {
        lbShow((lbIndex + 1) % lbGroup.length);
    });
    lbEl.addEventListener('click', function (e) {
        if (e.target === lbEl) { lbClose(); }
    });
    document.addEventListener('keydown', function (e) {
        if (!lbEl.classList.contains('is-open')) { return; }
        if (e.key === 'Escape')     { lbClose(); }
        if (e.key === 'ArrowLeft'  && lbGroup.length > 1) { lbShow((lbIndex - 1 + lbGroup.length) % lbGroup.length); }
        if (e.key === 'ArrowRight' && lbGroup.length > 1) { lbShow((lbIndex + 1) % lbGroup.length); }
    });

    // Wire up Gutenberg gallery blocks
    Array.prototype.forEach.call(
        document.querySelectorAll('.entry-content .wp-block-gallery'),
        function (gallery) {
            var imgs  = gallery.querySelectorAll('figure img');
            var group = Array.prototype.map.call(imgs, function (img) {
                return { url: lbGetFullUrl(img), alt: img.alt || '', caption: lbGetCaption(img) };
            });
            Array.prototype.forEach.call(imgs, function (img, i) {
                img.addEventListener('click', function (e) {
                    e.preventDefault();
                    lbOpen(group, i, img);
                });
            });
        }
    );

    // Wire up Jetpack Tiled Gallery blocks
    Array.prototype.forEach.call(
        document.querySelectorAll('.entry-content .wp-block-jetpack-tiled-gallery'),
        function (gallery) {
            var imgs  = gallery.querySelectorAll('.tiled-gallery__item img');
            var group = Array.prototype.map.call(imgs, function (img) {
                var url = img.getAttribute('data-url') || lbGetFullUrl(img);
                return { url: url, alt: img.alt || '', caption: lbGetCaption(img) };
            });
            Array.prototype.forEach.call(imgs, function (img, i) {
                img.addEventListener('click', function (e) {
                    e.preventDefault();
                    lbOpen(group, i, img);
                });
            });
        }
    );

    // Wire up individual images linked to a media file
    Array.prototype.forEach.call(
        document.querySelectorAll('.entry-content .wp-block-image a'),
        function (a) {
            if (!/\.(jpe?g|png|gif|webp|avif)(\?|$)/i.test(a.href)) { return; }
            var img = a.querySelector('img');
            if (!img) { return; }
            var group = [{ url: a.href, alt: img.alt || '', caption: lbGetCaption(img) }];
            a.addEventListener('click', function (e) {
                e.preventDefault();
                lbOpen(group, 0, img);
            });
        }
    );

}());
