---
name: wp-security-audit
description: Project-specific security checklist for this WordPress theme — load this as background context whenever reviewing security in this repo (including when /security-review runs here)
user-invocable: false
---

# WordPress theme security checklist (vinu-thomas)

This theme has no build step and ships as raw PHP directly into `wp-content/themes/`. Apply this checklist on top of general PHP/WordPress security review:

## Output escaping
- Every value echoed into HTML must be escaped at the point of output: `esc_html()`, `esc_attr()`, `esc_url()`, or `wp_kses()`/`wp_kses_post()` for rich content. Flag raw `echo $var` or `<?= $var ?>` on anything derived from post content, options, or user input.
- Template partials (`template-parts/*.php`) and callbacks (`vt_comment()` in `comments.php`) are common places escaping gets skipped on refactor — check these first.

## Cookie consent / GDPR invariant
- The `script_loader_tag` filter in `inc/cookie-consent.php` **must** call `vt_consent_granted()` before suppressing the Jetpack Stats script. If that filter ever unconditionally returns `''`, returning visitors with a granted cookie silently lose stats (this was the v1.8.4 regression — see `CLAUDE.md`).
- Any change to `inc/cookie-consent.php` or `assets/js/cookie-consent.js` should be checked against the "Stats load paths" table in `CLAUDE.md` — verify no path fires a tracking request before consent.
- The geo endpoint (`GET /wp-json/vt/v1/geo`) must never be cached (dynamic per-request) and must not leak IP or other PII in its response.

## WordPress-specific patterns
- Any form handling (comments, search, customizer settings) should rely on WordPress's built-in nonce/capability checks rather than custom validation — flag hand-rolled auth checks.
- Direct file access: PHP files that aren't templates loaded by WP's template hierarchy should guard with `defined('ABSPATH') || exit;` if added in `inc/`.
- Customizer settings (`inc/customizer.php`) that accept free text or URLs need a `sanitize_callback`; flag any `add_setting()` without one.
- No `$wpdb` raw queries exist today — if one is introduced, it must use `$wpdb->prepare()`.

## Reporting convention
- Past manual audits are saved as `security-reports/security-report-DDMMYYYY.md` (gitignored, never committed). If asked to produce a written report, follow this naming convention and location rather than inventing a new one.
