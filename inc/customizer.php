<?php

defined('ABSPATH') || exit;

/* ----------------------------------------------------------------
   Font definitions
   ---------------------------------------------------------------- */

const VT_HEADING_FONTS = [
    'Playfair Display'   => ['gf' => 'Playfair+Display:ital,wght@0,700;1,400',   'stack' => 'Georgia, serif'],
    'Merriweather'       => ['gf' => 'Merriweather:ital,wght@0,700;1,400',        'stack' => 'Georgia, serif'],
    'Lora'               => ['gf' => 'Lora:ital,wght@0,700;1,400',                'stack' => 'Georgia, serif'],
    'Cormorant Garamond' => ['gf' => 'Cormorant+Garamond:ital,wght@0,700;1,400',  'stack' => 'Georgia, serif'],
    'Libre Baskerville'  => ['gf' => 'Libre+Baskerville:ital,wght@0,700;1,400',   'stack' => 'Georgia, serif'],
    'DM Serif Display'   => ['gf' => 'DM+Serif+Display:ital,wght@0,400;1,400',    'stack' => 'Georgia, serif'],
    'EB Garamond'        => ['gf' => 'EB+Garamond:ital,wght@0,700;1,400',         'stack' => 'Georgia, serif'],
];

const VT_BODY_FONTS = [
    'Inter'         => ['gf' => 'Inter:wght@400;500;600',          'stack' => '-apple-system, BlinkMacSystemFont, sans-serif'],
    'Roboto'        => ['gf' => 'Roboto:wght@400;500;700',         'stack' => 'Arial, sans-serif'],
    'Open Sans'     => ['gf' => 'Open+Sans:wght@400;500;600',      'stack' => 'Arial, sans-serif'],
    'Source Sans 3' => ['gf' => 'Source+Sans+3:wght@400;500;600',  'stack' => 'Arial, sans-serif'],
    'Nunito'        => ['gf' => 'Nunito:wght@400;500;600',         'stack' => 'Arial, sans-serif'],
    'Lato'          => ['gf' => 'Lato:wght@400;700',               'stack' => 'Arial, sans-serif'],
    'IBM Plex Sans' => ['gf' => 'IBM+Plex+Sans:wght@400;500;600',  'stack' => 'Arial, sans-serif'],
    'DM Sans'       => ['gf' => 'DM+Sans:wght@400;500;600',        'stack' => 'Arial, sans-serif'],
];

const VT_LOGO_FONTS = [
    'Dancing Script' => ['gf' => 'Dancing+Script:wght@600', 'stack' => 'cursive'],
    'Pacifico'       => ['gf' => 'Pacifico',                 'stack' => 'cursive'],
    'Caveat'         => ['gf' => 'Caveat:wght@600',          'stack' => 'cursive'],
    'Satisfy'        => ['gf' => 'Satisfy',                  'stack' => 'cursive'],
    'Great Vibes'    => ['gf' => 'Great+Vibes',              'stack' => 'cursive'],
];

/* ----------------------------------------------------------------
   Google Fonts URL — called from vt_enqueue() in functions.php
   ---------------------------------------------------------------- */

function vt_google_fonts_url( bool $include_logo = false ): string {
    $h = vt_get_mod('vt_font_heading', 'Playfair Display');
    $b = vt_get_mod('vt_font_body',    'Inter');

    $families = [
        VT_HEADING_FONTS[$h]['gf'] ?? VT_HEADING_FONTS['Playfair Display']['gf'],
        VT_BODY_FONTS[$b]['gf']    ?? VT_BODY_FONTS['Inter']['gf'],
    ];

    // Logo font is only needed on About and 404 — skip it on every other page.
    if ( $include_logo ) {
        $l          = vt_get_mod('vt_font_logo', 'Dancing Script');
        $families[] = VT_LOGO_FONTS[$l]['gf'] ?? VT_LOGO_FONTS['Dancing Script']['gf'];
    }

    return 'https://fonts.googleapis.com/css2?family='
        . implode('&family=', array_unique($families))
        . '&display=swap';
}

/* ----------------------------------------------------------------
   Color helpers
   ---------------------------------------------------------------- */

function vt_hex_to_rgb(string $hex): array {
    $hex = ltrim($hex, '#');
    if (strlen($hex) === 3) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }
    return [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];
}

function vt_darken_hex(string $hex, float $amount): string {
    [$r, $g, $b] = vt_hex_to_rgb($hex);
    return sprintf('#%02x%02x%02x',
        max(0, (int) round($r * (1 - $amount))),
        max(0, (int) round($g * (1 - $amount))),
        max(0, (int) round($b * (1 - $amount)))
    );
}

function vt_lighten_hex(string $hex, float $amount): string {
    [$r, $g, $b] = vt_hex_to_rgb($hex);
    return sprintf('#%02x%02x%02x',
        min(255, (int) round($r + (255 - $r) * $amount)),
        min(255, (int) round($g + (255 - $g) * $amount)),
        min(255, (int) round($b + (255 - $b) * $amount))
    );
}

/* ----------------------------------------------------------------
   Inline CSS — overrides :root variables from customizer values
   ---------------------------------------------------------------- */

function vt_custom_css(): void {
    $accent     = vt_get_mod('vt_accent',              '#c8853a');
    $bg         = vt_get_mod('vt_bg',                  '#ffffff');
    $bg2        = vt_get_mod('vt_bg_secondary',        '#f7f6f4');
    $text1      = vt_get_mod('vt_text_primary',        '#1a1a1a');
    $text2      = vt_get_mod('vt_text_secondary',      '#555555');
    $dark_bg    = vt_get_mod('vt_dark_bg',             '#111111');
    $dark_bg2   = vt_get_mod('vt_dark_bg_secondary',   '#1c1c1c');
    $dark_text1 = vt_get_mod('vt_dark_text_primary',   '#f0ece6');
    $dark_text2 = vt_get_mod('vt_dark_text_secondary', '#b0a898');

    [$r, $g, $b]  = vt_hex_to_rgb($accent);
    $accent_hover = vt_darken_hex($accent, 0.17);
    $accent_light = "rgba($r,$g,$b,0.10)";
    $accent_text  = vt_darken_hex($accent, 0.08);
    $accent_dark  = vt_lighten_hex($accent, 0.25);

    $h_name  = vt_get_mod('vt_font_heading', 'Playfair Display');
    $b_name  = vt_get_mod('vt_font_body',    'Inter');
    $l_name  = vt_get_mod('vt_font_logo',    'Dancing Script');
    $h_stack = VT_HEADING_FONTS[$h_name]['stack'] ?? 'Georgia, serif';
    $b_stack = VT_BODY_FONTS[$b_name]['stack']    ?? 'sans-serif';
    $l_stack = VT_LOGO_FONTS[$l_name]['stack']    ?? 'cursive';

    $css  = ":root{";
    $css .= "--accent:{$accent};";
    $css .= "--accent-hover:{$accent_hover};";
    $css .= "--accent-text:{$accent_text};";
    $css .= "--accent-light:{$accent_light};";
    $css .= "--bg:{$bg};";
    $css .= "--bg-secondary:{$bg2};";
    $css .= "--bg-card:{$bg};";
    $css .= "--text-primary:{$text1};";
    $css .= "--text-secondary:{$text2};";
    $css .= "--font-heading:'{$h_name}',{$h_stack};";
    $css .= "--font-body:'{$b_name}',{$b_stack};";
    $css .= "--font-accent:'{$l_name}',{$l_stack};";
    $css .= "}";

    $css .= "[data-theme=\"dark\"]{";
    $css .= "--bg:{$dark_bg};";
    $css .= "--bg-secondary:{$dark_bg2};";
    $css .= "--bg-card:{$dark_bg2};";
    $css .= "--text-primary:{$dark_text1};";
    $css .= "--text-secondary:{$dark_text2};";
    $css .= "--accent-text:{$accent_dark};";
    $css .= "}";

    wp_add_inline_style('vt-style', $css);
}
add_action('wp_enqueue_scripts', 'vt_custom_css', 20);

/* ----------------------------------------------------------------
   Customizer registration
   ---------------------------------------------------------------- */

function vt_customizer(WP_Customize_Manager $wp_customize): void {

    /* — Panel ---------------------------------------------------- */
    $wp_customize->add_panel('vt_appearance', [
        'title'    => __('Theme Appearance', 'vt-folio'),
        'priority' => 25,
    ]);

    /* — Colors: Light Mode --------------------------------------- */
    $wp_customize->add_section('vt_colors_light', [
        'title'       => __('Colors — Light Mode', 'vt-folio'),
        'description' => __('Colors used when light mode is active.', 'vt-folio'),
        'panel'       => 'vt_appearance',
        'priority'    => 10,
    ]);

    $light_colors = [
        'vt_accent'         => ['default' => '#c8853a', 'label' => __('Accent',              'vt-folio')],
        'vt_bg'             => ['default' => '#ffffff', 'label' => __('Background',           'vt-folio')],
        'vt_bg_secondary'   => ['default' => '#f7f6f4', 'label' => __('Secondary Background', 'vt-folio')],
        'vt_text_primary'   => ['default' => '#1a1a1a', 'label' => __('Primary Text',         'vt-folio')],
        'vt_text_secondary' => ['default' => '#555555', 'label' => __('Secondary Text',       'vt-folio')],
    ];

    foreach ($light_colors as $id => $config) {
        $wp_customize->add_setting($id, [
            'default'           => $config['default'],
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'refresh',
        ]);
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, $id, [
            'label'   => $config['label'],
            'section' => 'vt_colors_light',
        ]));
    }

    /* — Colors: Dark Mode ---------------------------------------- */
    $wp_customize->add_section('vt_colors_dark', [
        'title'       => __('Colors — Dark Mode', 'vt-folio'),
        'description' => __('Colors used when dark mode is active.', 'vt-folio'),
        'panel'       => 'vt_appearance',
        'priority'    => 20,
    ]);

    $dark_colors = [
        'vt_dark_bg'            => ['default' => '#111111', 'label' => __('Background',           'vt-folio')],
        'vt_dark_bg_secondary'  => ['default' => '#1c1c1c', 'label' => __('Secondary Background', 'vt-folio')],
        'vt_dark_text_primary'  => ['default' => '#f0ece6', 'label' => __('Primary Text',         'vt-folio')],
        'vt_dark_text_secondary'=> ['default' => '#b0a898', 'label' => __('Secondary Text',       'vt-folio')],
    ];

    foreach ($dark_colors as $id => $config) {
        $wp_customize->add_setting($id, [
            'default'           => $config['default'],
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'refresh',
        ]);
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, $id, [
            'label'   => $config['label'],
            'section' => 'vt_colors_dark',
        ]));
    }

    /* — Typography ---------------------------------------------- */
    $wp_customize->add_section('vt_typography', [
        'title'       => __('Typography', 'vt-folio'),
        'description' => __('Google Fonts loaded on every page are updated automatically.', 'vt-folio'),
        'panel'       => 'vt_appearance',
        'priority'    => 30,
    ]);

    $font_groups = [
        'vt_font_heading' => [
            'label'   => __('Heading Font', 'vt-folio'),
            'default' => 'Playfair Display',
            'map'     => VT_HEADING_FONTS,
        ],
        'vt_font_body' => [
            'label'   => __('Body Font', 'vt-folio'),
            'default' => 'Inter',
            'map'     => VT_BODY_FONTS,
        ],
        'vt_font_logo' => [
            'label'   => __('Logo / Accent Font', 'vt-folio'),
            'default' => 'Dancing Script',
            'map'     => VT_LOGO_FONTS,
        ],
    ];

    foreach ($font_groups as $id => $config) {
        $choices = array_combine(array_keys($config['map']), array_keys($config['map']));
        $default = $config['default'];
        $wp_customize->add_setting($id, [
            'default'           => $default,
            'sanitize_callback' => fn($v) => array_key_exists($v, $choices) ? $v : $default,
            'transport'         => 'refresh',
        ]);
        $wp_customize->add_control($id, [
            'label'   => $config['label'],
            'section' => 'vt_typography',
            'type'    => 'select',
            'choices' => $choices,
        ]);
    }

    /* — Post Display -------------------------------------------- */
    $wp_customize->add_section('vt_display', [
        'title'    => __('Post Display', 'vt-folio'),
        'panel'    => 'vt_appearance',
        'priority' => 40,
    ]);

    $display_settings = [
        'vt_show_reading_time' => __('Show reading time', 'vt-folio'),
        'vt_show_date'         => __('Show publish date', 'vt-folio'),
    ];

    foreach ($display_settings as $id => $label) {
        $wp_customize->add_setting($id, [
            'default'           => true,
            'sanitize_callback' => 'rest_sanitize_boolean',
        ]);
        $wp_customize->add_control($id, [
            'label'   => $label,
            'section' => 'vt_display',
            'type'    => 'checkbox',
        ]);
    }

    $wp_customize->add_setting('vt_show_progress_bar', [
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ]);
    $wp_customize->add_control('vt_show_progress_bar', [
        'label'   => __('Show reading progress bar', 'vt-folio'),
        'section' => 'vt_display',
        'type'    => 'checkbox',
    ]);

    $wp_customize->add_setting('vt_show_featured_post', [
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ]);
    $wp_customize->add_control('vt_show_featured_post', [
        'label'       => __('Featured first post on blog listing', 'vt-folio'),
        'description' => __('Makes the first post full-width with a side-by-side image layout.', 'vt-folio'),
        'section'     => 'vt_display',
        'type'        => 'checkbox',
    ]);

    $wp_customize->add_setting('vt_footer_credit', [
        'default'           => __('All rights reserved.', 'vt-folio'),
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('vt_footer_credit', [
        'label'   => __('Footer credit text', 'vt-folio'),
        'section' => 'vt_display',
        'type'    => 'text',
    ]);

    /* — Social Links -------------------------------------------- */
    $wp_customize->add_section('vt_social', [
        'title'    => __('Social Links', 'vt-folio'),
        'panel'    => 'vt_appearance',
        'priority' => 45,
    ]);

    $vt_social_fields = [
        'vt_social_x'          => ['label' => __('X / Twitter URL', 'vt-folio'),  'default' => 'https://x.com/vinuthomas'],
        'vt_social_linkedin'   => ['label' => __('LinkedIn URL',    'vt-folio'),  'default' => 'https://linkedin.com/in/vinuthomas'],
        'vt_social_instagram'  => ['label' => __('Instagram URL',   'vt-folio'),  'default' => 'https://www.instagram.com/vinuthomas'],
        'vt_social_soundcloud' => ['label' => __('SoundCloud URL',  'vt-folio'),  'default' => 'https://soundcloud.com/vinuthomas'],
        'vt_social_mastodon'   => ['label' => __('Mastodon URL',    'vt-folio'),  'default' => 'https://mastodon.online/@vinuthomas'],
    ];

    foreach ($vt_social_fields as $id => $config) {
        $wp_customize->add_setting($id, [
            'default'           => $config['default'],
            'sanitize_callback' => 'esc_url_raw',
        ]);
        $wp_customize->add_control($id, [
            'label'   => $config['label'],
            'section' => 'vt_social',
            'type'    => 'url',
        ]);
    }

    /* — About Page ---------------------------------------------- */
    $wp_customize->add_section('vt_about', [
        'title'       => __('About Page', 'vt-folio'),
        'description' => __('Settings for the About page template.', 'vt-folio'),
        'panel'       => 'vt_appearance',
        'priority'    => 47,
    ]);

    $wp_customize->add_setting('vt_about_avatar_email', [
        'default'           => get_option('admin_email'),
        'sanitize_callback' => 'sanitize_email',
    ]);
    $wp_customize->add_control('vt_about_avatar_email', [
        'label'       => __('Avatar email address', 'vt-folio'),
        'description' => __('Email used to load the Gravatar on the About page. Leave blank to hide the avatar.', 'vt-folio'),
        'section'     => 'vt_about',
        'type'        => 'email',
    ]);

    /* — Cookie Consent ------------------------------------------ */
    $wp_customize->add_section('vt_consent', [
        'title'       => __('Cookie Consent', 'vt-folio'),
        'description' => __('GDPR/UK GDPR banner for EU/EEA visitors. Geo-detected via the Cloudflare CF-IPCountry header (free tier). Add analytics providers via the vt_consent_providers filter.', 'vt-folio'),
        'panel'       => 'vt_appearance',
        'priority'    => 50,
    ]);

    $wp_customize->add_setting('vt_consent_enabled', [
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ]);
    $wp_customize->add_control('vt_consent_enabled', [
        'label'   => __('Enable cookie consent banner', 'vt-folio'),
        'section' => 'vt_consent',
        'type'    => 'checkbox',
    ]);

    $wp_customize->add_setting('vt_consent_geo_only', [
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ]);
    $wp_customize->add_control('vt_consent_geo_only', [
        'label'       => __('Only show to EU/EEA visitors', 'vt-folio'),
        'description' => __('Requires Cloudflare in front of the site. Uncheck to show the banner to all visitors regardless of location.', 'vt-folio'),
        'section'     => 'vt_consent',
        'type'        => 'checkbox',
    ]);

    $wp_customize->add_setting('vt_consent_force_show', [
        'default'           => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ]);
    $wp_customize->add_control('vt_consent_force_show', [
        'label'       => __('Always show banner (testing mode)', 'vt-folio'),
        'description' => __('Bypasses geo detection and ignores any stored consent cookie. Turn off before going live.', 'vt-folio'),
        'section'     => 'vt_consent',
        'type'        => 'checkbox',
    ]);

    $wp_customize->add_setting('vt_consent_text', [
        'default'           => __('This site uses analytics cookies to understand how posts are being read. No personal data is sold or shared.', 'vt-folio'),
        'sanitize_callback' => 'sanitize_textarea_field',
    ]);
    $wp_customize->add_control('vt_consent_text', [
        'label'   => __('Banner message', 'vt-folio'),
        'section' => 'vt_consent',
        'type'    => 'textarea',
    ]);

    $wp_customize->add_setting('vt_consent_policy_url', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ]);
    $wp_customize->add_control('vt_consent_policy_url', [
        'label'       => __('Privacy policy URL', 'vt-folio'),
        'description' => __('Optional. Leave blank to use the WordPress Privacy Policy page if one is set.', 'vt-folio'),
        'section'     => 'vt_consent',
        'type'        => 'url',
    ]);
}
add_action('customize_register', 'vt_customizer');

/* ----------------------------------------------------------------
   Sync Customizer values into the block editor palette + typography
   wp_theme_json_data_theme fires whenever WP resolves theme.json,
   including the block editor and frontend block styles.
   ---------------------------------------------------------------- */

add_filter('wp_theme_json_data_theme', function (WP_Theme_JSON_Data $theme_json): WP_Theme_JSON_Data {
    $accent = vt_get_mod('vt_accent',       '#c8853a');
    $bg     = vt_get_mod('vt_bg',           '#ffffff');
    $bg2    = vt_get_mod('vt_bg_secondary', '#f7f6f4');
    $text   = vt_get_mod('vt_text_primary', '#1a1a1a');

    $h_name  = vt_get_mod('vt_font_heading', 'Playfair Display');
    $b_name  = vt_get_mod('vt_font_body',    'Inter');
    $h_stack = VT_HEADING_FONTS[$h_name]['stack'] ?? 'Georgia, serif';
    $b_stack = VT_BODY_FONTS[$b_name]['stack']    ?? 'sans-serif';

    $theme_json->update_with([
        'version'  => 2,
        'settings' => [
            'color' => [
                'palette' => [
                    ['name' => 'Accent',     'slug' => 'accent',     'color' => $accent],
                    ['name' => 'Background', 'slug' => 'background', 'color' => $bg],
                    ['name' => 'Surface',    'slug' => 'surface',    'color' => $bg2],
                    ['name' => 'Text',       'slug' => 'text',       'color' => $text],
                    ['name' => 'Muted',      'slug' => 'muted',      'color' => '#888888'],
                ],
            ],
            'typography' => [
                'fontFamilies' => [
                    [
                        'fontFamily' => "'{$h_name}', {$h_stack}",
                        'name'       => $h_name,
                        'slug'       => 'heading',
                    ],
                    [
                        'fontFamily' => "'{$b_name}', {$b_stack}",
                        'name'       => $b_name,
                        'slug'       => 'body',
                    ],
                ],
            ],
        ],
        'styles' => [
            'color' => [
                'background' => $bg,
                'text'       => $text,
            ],
            'typography' => [
                'fontFamily' => "'{$b_name}', {$b_stack}",
            ],
        ],
    ]);

    return $theme_json;
});
