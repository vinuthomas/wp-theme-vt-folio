# Changelog

All notable changes to this theme are documented here.
Format follows [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).
Versioning follows [Semantic Versioning](https://semver.org/).

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
