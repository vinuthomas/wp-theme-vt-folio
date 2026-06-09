(function () {
    'use strict';

    var cfg = window.vtConsent || { cookieName: 'vt_consent', cookieDays: 365, geoEndpoint: '' };

    function setCookie(name, value, days) {
        var exp    = new Date(Date.now() + days * 864e5).toUTCString();
        var secure = window.location.protocol === 'https:' ? '; Secure' : '';
        document.cookie = name + '=' + encodeURIComponent(value)
            + '; expires=' + exp
            + '; path=/'
            + '; SameSite=Lax'
            + secure;
    }

    function dismissBanner(banner) {
        banner.classList.remove('vt-cookie-banner--visible');
        banner.addEventListener('transitionend', function () {
            banner.remove();
        }, { once: true });
    }

    function maybeLoadStats() {
        var src = cfg.statsSrc;
        if (!src || document.getElementById('jetpack-stats-js')) return;
        var s = document.createElement('script');
        s.id    = 'jetpack-stats-js';
        s.defer = true;
        s.src   = src;
        document.head.appendChild(s);
    }

    function handleConsent(value) {
        setCookie(cfg.cookieName, value, cfg.cookieDays);
        var banner = document.getElementById('vt-cookie-banner');
        if (banner) dismissBanner(banner);
        if (value === 'granted') maybeLoadStats();
        document.dispatchEvent(new CustomEvent('vt:consent', { detail: { value: value } }));
    }

    function getCookie(name) {
        var match = document.cookie.split('; ').find(function (row) {
            return row.indexOf(name + '=') === 0;
        });
        return match ? decodeURIComponent(match.split('=')[1]) : null;
    }

    function attachHandlers(banner) {
        var acceptBtn = document.getElementById('vt-consent-accept');
        var rejectBtn = document.getElementById('vt-consent-reject');

        if (acceptBtn) {
            acceptBtn.addEventListener('click', function () { handleConsent('granted'); });
        }
        if (rejectBtn) {
            rejectBtn.addEventListener('click', function () { handleConsent('denied'); });
        }

        function onKeyDown(e) {
            if (e.key === 'Escape') {
                handleConsent('denied');
                document.removeEventListener('keydown', onKeyDown);
            }
        }
        document.addEventListener('keydown', onKeyDown);
    }

    function showBanner(banner) {
        // Remove display:none, then let the browser paint the off-screen state
        // before adding the class that triggers the slide-up transition.
        banner.hidden = false;
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                banner.classList.add('vt-cookie-banner--visible');
            });
        });
        attachHandlers(banner);
    }

    function init() {
        var banner = document.getElementById('vt-cookie-banner');
        if (!banner) return;

        // Consent already recorded — silently remove (handles cached-page case).
        var stored = getCookie(cfg.cookieName);
        if (stored === 'granted' || stored === 'denied') {
            banner.remove();
            if (stored === 'granted') maybeLoadStats();
            return;
        }

        // Server inlined the geo result — use it directly, no round-trip needed.
        if (typeof cfg.isEU !== 'undefined') {
            if (cfg.isEU) {
                showBanner(banner);
            } else {
                banner.remove();
                maybeLoadStats();
            }
            return;
        }

        // Fallback: fetch geo endpoint (cached-HTML edge case or non-Cloudflare installs).
        if (!cfg.geoEndpoint) {
            showBanner(banner);
            return;
        }

        fetch(cfg.geoEndpoint, { credentials: 'omit' })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.eu) {
                    showBanner(banner);
                } else {
                    banner.remove();
                    maybeLoadStats();
                }
            })
            .catch(function () {
                banner.remove();
            });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
