# VT Folio — Changelog

All notable changes to this theme are documented here.
Format follows [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).
Versioning follows [Semantic Versioning](https://semver.org/).

---

## [1.6.0] — 2026-05-31

### Added
- Block editor colour palette and font families now sync automatically with Customizer values via `wp_theme_json_data_theme` filter — changing accent colour or fonts in Customizer updates the block editor in real time
- Post Display: reading progress bar toggle (`vt_show_progress_bar`)
- Post Display: featured first post toggle (`vt_show_featured_post`)
- Post Display: footer credit text setting (`vt_footer_credit`, replaces hardcoded "All rights reserved.")

### Fixed
- Logo links (`a.site-logo`, `a.footer-logo`) now carry `aria-label` with the site name; both logo images marked decorative (`alt=""`). Previously the dark-mode logo had no accessible name, failing the Lighthouse discernible link text audit.

### Changed
- README installation instructions updated to highlight GitHub Releases zip as the primary install method

---

## [1.5.0] — 2026-05-31

### Added
- Social Links Customizer section (`Theme Appearance → Social Links`) — X/Twitter, LinkedIn, Instagram, SoundCloud, Mastodon URLs are fully configurable; links suppress rendering when left empty
- About Page Customizer section (`Theme Appearance → About Page`) — avatar email address is now a theme setting instead of hardcoded to the admin email
- `<link rel="me">` Mastodon verification tag in `<head>` is now driven by the Social Links Mastodon setting

### Changed
- About page avatar alt text now uses `get_bloginfo('name')` instead of a hardcoded name
- Footer social links and header `rel="me"` link read from Customizer settings with previous hardcoded URLs as defaults (no breaking change on update)

### Security
- **M-01 Fixed:** Added `JSON_HEX_TAG` flag to JSON-LD schema output (`functions.php`) to prevent `</script>` in post content from breaking out of the script block
- **L-01 Fixed:** Consent cookie now includes the `Secure` flag on HTTPS connections (`cookie-consent.js`)
- **L-02 Fixed:** `localStorage` access wrapped in `try/catch` to handle `SecurityError` in private browsing and restricted iframe environments (`header.php`, `theme.js`)
- **L-03 Fixed:** About page avatar email moved to Customizer; no longer references `admin_email` directly in template code
- **L-04 Fixed:** Hardcoded personal social URLs removed from templates; all moved to Customizer settings

---

## [1.4.0] — 2026-05-31

### Added
- Cookie consent banner for GDPR/UK GDPR compliance
  - EU/EEA visitor detection via Cloudflare `CF-IPCountry` header (free plan)
  - First-party consent cookie (`vt_consent`, 365-day expiry)
  - Jetpack Stats blocked until consent is granted
  - Extensible provider registry via `vt_consent_providers` filter
  - Customizer section: enable/disable, EU-only toggle, banner text, privacy policy URL
  - Testing mode: "Always show banner" Customizer checkbox bypasses geo and cookie
  - Useful filters: `vt_consent_country_code`, `vt_consent_eu_countries`, `vt_consent_default_eu`
- `inc/cookie-consent.php` — all consent PHP logic
- `assets/js/cookie-consent.js` — banner slide animation, cookie write, ESC key dismiss

---

## [1.3.6] and earlier

Theme developed and iterated prior to git history being tracked.
