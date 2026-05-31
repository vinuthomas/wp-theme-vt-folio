(function () {
    'use strict';

    var cfg = window.vtConsent || { cookieName: 'vt_consent', cookieDays: 365 };

    function setCookie(name, value, days) {
        var exp = new Date(Date.now() + days * 864e5).toUTCString();
        document.cookie = name + '=' + encodeURIComponent(value)
            + '; expires=' + exp
            + '; path=/'
            + '; SameSite=Lax';
    }

    function dismissBanner(banner) {
        banner.classList.remove('vt-cookie-banner--visible');
        banner.addEventListener('transitionend', function () {
            banner.remove();
        }, { once: true });
    }

    function handleConsent(value) {
        setCookie(cfg.cookieName, value, cfg.cookieDays);
        var banner = document.getElementById('vt-cookie-banner');
        if (banner) dismissBanner(banner);
        // Notify any listeners (e.g. a future GA4 provider that loads dynamically)
        document.dispatchEvent(new CustomEvent('vt:consent', { detail: { value: value } }));
    }

    function init() {
        var banner = document.getElementById('vt-cookie-banner');
        if (!banner) return;

        // Double rAF ensures the browser has painted the initial off-screen state
        // before adding the class that triggers the slide-up transition.
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                banner.classList.add('vt-cookie-banner--visible');
            });
        });

        var acceptBtn = document.getElementById('vt-consent-accept');
        var rejectBtn = document.getElementById('vt-consent-reject');

        if (acceptBtn) {
            acceptBtn.addEventListener('click', function () { handleConsent('granted'); });
        }
        if (rejectBtn) {
            rejectBtn.addEventListener('click', function () { handleConsent('denied'); });
        }

        // ESC key = reject (keyboard accessibility)
        function onKeyDown(e) {
            if (e.key === 'Escape') {
                handleConsent('denied');
                document.removeEventListener('keydown', onKeyDown);
            }
        }
        document.addEventListener('keydown', onKeyDown);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
