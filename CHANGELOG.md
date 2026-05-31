# VT Folio — Changelog

All notable changes to this theme are documented here.
Format follows [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).
Versioning follows [Semantic Versioning](https://semver.org/).

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
