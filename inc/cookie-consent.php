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
   ---------------------------------------------------------------- */

function vt_consent_providers(): array {
    return apply_filters('vt_consent_providers', [
        'jetpack-stats' => [
            'label'    => 'Jetpack Stats',
            'category' => 'analytics',
            'disable'  => 'vt_consent_disable_jetpack_stats',
        ],
    ]);
}

function vt_consent_disable_jetpack_stats(): void {
    // Filter used by Jetpack 11+
    add_filter('jetpack_is_stats_enabled', '__return_false');

    // Classic stats module: remove the shutdown hook that fires the server-side ping.
    // Jetpack registers stats_shutdown at plugins_loaded; by the time init runs it's
    // already on the shutdown queue, so this remove_action lands correctly.
    remove_action('shutdown', 'stats_shutdown');

    // Dequeue any stats JS enqueued by the module
    add_action('wp_enqueue_scripts', static function () {
        wp_dequeue_script('jetpack-stats');
    }, 99);
}

/* ----------------------------------------------------------------
   Bootstrap — runs at init
   ---------------------------------------------------------------- */

function vt_consent_init(): void {
    if (!vt_get_mod('vt_consent_enabled', true)) return;

    $force_show = (bool) vt_get_mod('vt_consent_force_show', false);

    $geo_only = vt_get_mod('vt_consent_geo_only', true);
    $in_scope = $force_show || ($geo_only ? vt_consent_is_eu() : true);

    if (!$in_scope) return;

    // In force-show mode ignore any stored cookie so the banner always appears
    $consent = $force_show ? null : vt_consent_value();

    // Block all providers while consent is absent or denied
    if ($consent !== 'granted') {
        foreach (vt_consent_providers() as $provider) {
            if (!empty($provider['disable']) && is_callable($provider['disable'])) {
                call_user_func($provider['disable']);
            }
        }
    }

    // Show banner when no decision has been recorded (or force-show overrides it)
    if ($consent === null) {
        add_action('wp_enqueue_scripts', 'vt_consent_enqueue');
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

    wp_localize_script('vt-cookie-consent', 'vtConsent', [
        'cookieName' => VT_CONSENT_COOKIE,
        'cookieDays' => VT_CONSENT_DAYS,
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
         aria-live="polite">
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
