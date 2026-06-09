<?php

defined('ABSPATH') || exit;

/* ================================================================
   Cookie Consent — EU/EEA geo-gated via Cloudflare CF-IPCountry

   To add a provider (e.g. GA4), use the vt_consent_providers filter:

     add_filter('vt_consent_providers', function($providers) {
         $providers['ga4'] = [
             'label'    => 'Google Analytics 4',
             'category' => 'analytics',
             'disable'  => 'my_disable_ga4_fn',
         ];
         return $providers;
     });

   To force a country for local testing:
     add_filter('vt_consent_country_code', fn() => 'DE');
   ================================================================ */

const VT_CONSENT_COOKIE = 'vt_consent';
const VT_CONSENT_DAYS   = 365;

// EU 27 + EEA (IS/LI/NO) + UK GDPR (GB) + Swiss nFADP (CH)
const VT_EU_COUNTRIES = [
    'AT','BE','BG','CY','CZ','DE','DK','EE','ES','FI','FR','GR','HR',
    'HU','IE','IT','LT','LU','LV','MT','NL','PL','PT','RO','SE','SI','SK',
    'IS','LI','NO',
    'GB','CH',
];

/* ----------------------------------------------------------------
   Geo detection
   ---------------------------------------------------------------- */

function vt_consent_is_eu(): bool {
    $eu = apply_filters('vt_consent_eu_countries', VT_EU_COUNTRIES);

    // Manual override — useful for local testing or non-Cloudflare installs
    $override = apply_filters('vt_consent_country_code', null);
    if ($override !== null) {
        $code = strtoupper((string) $override);
        return $code === 'T1' || in_array($code, $eu, true);
    }

    if (!isset($_SERVER['HTTP_CF_IPCOUNTRY'])) {
        // Not behind Cloudflare; default controlled by filter (false = don't show)
        return (bool) apply_filters('vt_consent_default_eu', false);
    }

    $code = strtoupper(sanitize_text_field($_SERVER['HTTP_CF_IPCOUNTRY']));

    if ($code === 'T1') return true; // Tor exit node — conservative default
    return in_array($code, $eu, true);
}

/* ----------------------------------------------------------------
   Consent state
   ---------------------------------------------------------------- */

function vt_consent_value(): ?string {
    if (!isset($_COOKIE[VT_CONSENT_COOKIE])) return null;
    $v = $_COOKIE[VT_CONSENT_COOKIE];
    return in_array($v, ['granted', 'denied'], true) ? $v : null;
}

function vt_consent_granted(): bool {
    return vt_consent_value() === 'granted';
}

/* ----------------------------------------------------------------
   Provider registry

   Jetpack Stats is no longer listed here — it is handled client-side
   in cookie-consent.js (script injected dynamically after consent, or
   for non-EU visitors). Server-side providers (e.g. a GA4 server event)
   can still be added via the filter with a 'disable' callback.
   ---------------------------------------------------------------- */

function vt_consent_providers(): array {
    return apply_filters('vt_consent_providers', []);
}

/* ----------------------------------------------------------------
   Jetpack Stats — suppress the script tag at print time via
   script_loader_tag rather than wp_dequeue_script. Dequeue loses
   the priority race against Jetpack's Script Strategy API; filtering
   the tag fires after all enqueue/dequeue battles are settled.
   cookie-consent.js injects the script dynamically when appropriate.
   ---------------------------------------------------------------- */

add_filter('script_loader_tag', static function (string $tag, string $handle): string {
    return $handle === 'jetpack-stats' ? '' : $tag;
}, 10, 2);

/* ----------------------------------------------------------------
   Geo REST endpoint — /wp-json/vt/v1/geo
   Always dynamic: /wp-json/ is excluded from Jetpack HTML cache,
   WP Super Cache, W3TC, and most other cache engines by default.
   Returns { "eu": true|false } with no-store headers so Cloudflare
   and browsers never cache it either.
   ---------------------------------------------------------------- */

add_action('rest_api_init', function () {
    register_rest_route('vt/v1', '/geo', [
        'methods'             => 'GET',
        'callback'            => 'vt_consent_geo_rest',
        'permission_callback' => '__return_true',
    ]);
});

function vt_consent_geo_rest(): WP_REST_Response {
    $force_show = (bool) vt_get_mod('vt_consent_force_show', false);
    $geo_only   = vt_get_mod('vt_consent_geo_only', true);
    $eu         = $force_show || ($geo_only ? vt_consent_is_eu() : true);

    $response = new WP_REST_Response(['eu' => $eu], 200);
    $response->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    $response->header('Pragma', 'no-cache');
    return $response;
}

/* ----------------------------------------------------------------
   Bootstrap — runs at init
   ---------------------------------------------------------------- */

function vt_consent_init(): void {
    if (!vt_get_mod('vt_consent_enabled', true)) return;

    $force_show = (bool) vt_get_mod('vt_consent_force_show', false);
    $consent    = $force_show ? null : vt_consent_value();

    // Run any server-side provider disable callbacks (e.g. GA4 server events).
    // Jetpack Stats is handled client-side and no longer appears in providers().
    $geo_only = vt_get_mod('vt_consent_geo_only', true);
    $in_scope = $force_show || ($geo_only ? vt_consent_is_eu() : true);

    if ($in_scope && $consent !== 'granted') {
        foreach (vt_consent_providers() as $provider) {
            if (!empty($provider['disable']) && is_callable($provider['disable'])) {
                call_user_func($provider['disable']);
            }
        }
    }

    // Always render the banner HTML when no decision is recorded.
    // Geo-gating is handled client-side via the /wp-json/vt/v1/geo endpoint
    // so it works correctly even when this page is served from an HTML cache.
    if ($consent === null) {
        add_action('wp_enqueue_scripts', 'vt_consent_enqueue', 50);
        add_action('wp_footer', 'vt_consent_render_banner', 100);
    }
}
add_action('init', 'vt_consent_init');

/* ----------------------------------------------------------------
   Assets
   ---------------------------------------------------------------- */

function vt_consent_enqueue(): void {
    $ver = wp_get_theme()->get('Version');

    wp_enqueue_script(
        'vt-cookie-consent',
        get_template_directory_uri() . '/assets/js/cookie-consent.js',
        [],
        $ver,
        true
    );

    // Jetpack Stats uses a weekly-versioned URL: stats.wp.com/e-{ISO year}{ISO week}.js
    // Derived from the date rather than reading wp_scripts->registered, which is
    // unreliable because Jetpack's Script Strategy API enqueues after our priority 50.
    $stats_src = 'https://stats.wp.com/e-' . gmdate( 'oW' ) . '.js';

    $force_show = (bool) vt_get_mod('vt_consent_force_show', false);
    $geo_only   = (bool) vt_get_mod('vt_consent_geo_only', true);
    $is_eu      = $force_show || ($geo_only ? vt_consent_is_eu() : true);

    wp_localize_script('vt-cookie-consent', 'vtConsent', [
        'cookieName'  => VT_CONSENT_COOKIE,
        'cookieDays'  => VT_CONSENT_DAYS,
        'isEU'        => $is_eu,
        'geoEndpoint' => rest_url('vt/v1/geo'), // fallback for cached-HTML edge case
        'statsSrc'    => $stats_src,
    ]);
}

/* ----------------------------------------------------------------
   Banner HTML
   ---------------------------------------------------------------- */

function vt_consent_render_banner(): void {
    $text = vt_get_mod(
        'vt_consent_text',
        __('This site uses analytics cookies to understand how posts are being read. No personal data is sold or shared.', 'vt-folio')
    );

    $policy_url = vt_get_mod('vt_consent_policy_url', '');
    if (empty($policy_url) && function_exists('get_privacy_policy_url')) {
        $policy_url = get_privacy_policy_url();
    }
    ?>
    <div id="vt-cookie-banner"
         class="vt-cookie-banner"
         role="region"
         aria-label="<?php esc_attr_e('Cookie consent', 'vt-folio'); ?>"
         aria-live="polite"
         hidden>
        <div class="vt-cookie-banner__inner">
            <p class="vt-cookie-banner__text">
                <?php echo esc_html($text); ?>
                <?php if (!empty($policy_url)) : ?>
                    <a href="<?php echo esc_url($policy_url); ?>"
                       class="vt-cookie-banner__policy"
                       target="_blank"
                       rel="noopener noreferrer"><?php esc_html_e('Privacy policy', 'vt-folio'); ?></a>
                <?php endif; ?>
            </p>
            <div class="vt-cookie-banner__actions">
                <button id="vt-consent-reject"
                        class="vt-cookie-btn vt-cookie-btn--ghost"
                        type="button">
                    <?php esc_html_e('Reject non-essential', 'vt-folio'); ?>
                </button>
                <button id="vt-consent-accept"
                        class="vt-cookie-btn vt-cookie-btn--primary"
                        type="button">
                    <?php esc_html_e('Accept all', 'vt-folio'); ?>
                </button>
            </div>
        </div>
    </div>
    <?php
}
