# Vinu Thomas — WordPress Theme

A custom WordPress theme for a personal tech/AI blog. No build step — pure PHP and CSS.

## Features

- **Dark mode** — system-preference aware, toggled via header button, no flash on load
- **Responsive grid** — CSS Grid masonry-style listing with featured first post
- **Cookie consent** — GDPR/UK GDPR banner, EU-only via Cloudflare geo detection
- **Gutenberg-ready** — wide alignment, responsive embeds, block editor styles
- **Performance** — non-blocking Google Fonts, deferred JS, `sizes` attributes on all images
- **Article schema** — JSON-LD `BlogPosting` on every post (GEO/AEO optimised)
- **Customizer** — colours (light + dark), typography (Google Fonts), post display options
- **Gallery lightbox** — works with core and Jetpack tiled gallery blocks

## Requirements

- WordPress 6.3+
- PHP 8.1+
- Cloudflare (free plan) in front of the site for cookie consent geo detection

## Installation

Copy the `wordpress_theme/` folder into `wp-content/themes/vinu-thomas/` then activate via **Appearance → Themes**.

No npm, no compilation.

## Local development

Docker Compose is used for local development. The theme folder is symlinked into the container so edits are live instantly.

```bash
# Start
cd /path/to/vinuthomas-local && docker compose up -d

# Stop
docker compose down
```

Site runs at `http://localhost:8080`.

## Packaging a release

**Always bump the version in `style.css` first** (line 7). Use semantic versioning: patch for fixes, minor for new features.

```bash
rm -f ~/Desktop/vinu-thomas-theme.zip
cp -r /path/to/wordpress_theme /tmp/vinu-thomas
rm -f /tmp/vinu-thomas/CLAUDE.md
rm -rf /tmp/vinu-thomas/.claude
cd /tmp && zip -r ~/Desktop/vinu-thomas-theme.zip vinu-thomas --exclude "*/.DS_Store"
rm -rf /tmp/vinu-thomas
```

Verify before uploading:
```bash
unzip -l ~/Desktop/vinu-thomas-theme.zip | grep style.css
# should print: vinu-thomas/style.css
```

## File structure

```
style.css                    Theme header + all CSS
functions.php                Theme setup, enqueue, helpers
theme.json                   Block editor palette/typography
inc/
  customizer.php             All Customizer settings
  cookie-consent.php         GDPR cookie banner logic
header.php                   Sticky header, nav, dark-mode toggle, search
footer.php                   Footer nav, social links, copyright
home.php                     Blog listing (featured post + 3-col grid)
single.php                   Single post (narrow layout, reading time, author box)
page.php                     Generic page
page-about.php               About page template (two-column hero)
archive.php                  Category/tag/author/date archives
search.php                   Search results
404.php                      404 page
comments.php                 Comment list + form
searchform.php               Search form partial
template-parts/
  content-card.php           Post card partial
  content-none.php           No-posts placeholder
assets/
  js/theme.js                Dark mode, reading progress, mobile menu, lightbox
  js/cookie-consent.js       Cookie banner interactions
  images/                    Logo variants (light/dark)
```

## Customizer options

All options live under **Appearance → Customize → Theme Appearance**.

| Section | Settings |
|---|---|
| Colors — Light Mode | Accent, Background, Secondary Background, Primary Text, Secondary Text |
| Colors — Dark Mode | Background, Secondary Background, Primary Text, Secondary Text |
| Typography | Heading font, Body font, Logo/accent font (all Google Fonts) |
| Post Display | Show reading time, Show publish date |
| Cookie Consent | Enable banner, EU-only geo, Always show (testing), Banner text, Privacy policy URL |

## Cookie consent

The banner is shown only to EU/EEA visitors (detected via the Cloudflare `CF-IPCountry` request header — free plan, no configuration needed beyond having the DNS record proxied).

Consent is stored in a first-party cookie (`vt_consent`, 365 days). Jetpack Stats is blocked until consent is granted.

**Adding an analytics provider:**

```php
add_filter('vt_consent_providers', function($providers) {
    $providers['ga4'] = [
        'label'    => 'Google Analytics 4',
        'category' => 'analytics',
        'disable'  => function() {
            // dequeue or block the GA4 script here
        },
    ];
    return $providers;
});
```

**Testing locally** (forces the banner regardless of location or stored cookie):

Go to **Appearance → Customize → Theme Appearance → Cookie Consent** and enable **"Always show banner (testing mode)"**. Disable before deploying.

**Other useful filters:**

```php
// Override country detection (e.g. for non-Cloudflare installs)
add_filter('vt_consent_country_code', fn() => 'DE');

// Change the EU country list
add_filter('vt_consent_eu_countries', function($countries) {
    $countries[] = 'AU'; // add Australia
    return $countries;
});

// Show banner to all visitors regardless of location
add_filter('vt_consent_default_eu', '__return_true');
```

## Custom image sizes

| Slug | Dimensions | Used in |
|---|---|---|
| `vt-card` | 600×380 (cropped) | Post cards |
| `vt-featured` | 1200×700 (cropped) | Featured card |
| `vt-hero` | 1920×800 (cropped) | Single post hero |

## Registered menus

- `primary` — rendered in `header.php`
- `footer` — rendered in `footer.php` (only if assigned)

## About page

Create a WordPress page and assign it the **"About"** page template (`page-about.php`). The featured image becomes the left-column portrait.
