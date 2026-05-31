# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A custom WordPress theme for vinuthomas.com — a personal tech/AI blog. No build step; pure PHP + CSS.

## Theme structure

```
style.css              — Theme header (required) + all CSS
functions.php          — Theme setup, Google Fonts enqueue, helpers
theme.json             — Gutenberg block editor palette/typography config
header.php             — Sticky header: Dancing Script logo, nav, dark-mode toggle, search
footer.php             — Footer: logo, footer nav, social links, copyright
home.php               — Blog listing: first post is featured (full-width), rest in 3-col grid
single.php             — Single post: centered narrow layout, reading time, author box, comments
page.php               — Generic page (same centered layout, no meta)
page-about.php         — Template Name: About — two-column hero (image + copy)
archive.php            — Category/tag/author/date archives
search.php             — Search results
404.php                — 404 with Dancing Script "404" in accent color
index.php              — Fallback (same as home.php)
comments.php           — Comment list + form; uses vt_comment() callback from functions.php
template-parts/
  content-card.php     — Post card partial (used by home/archive/search)
  content-none.php     — No-posts placeholder
assets/js/theme.js     — Dark mode toggle, reading progress bar, mobile menu
```

## Key design decisions

- **Dark mode**: CSS custom properties on `:root` vs `[data-theme="dark"]`. An inline `<script>` in `<head>` reads `localStorage.getItem('vt-theme')` and sets `data-theme` before CSS loads to prevent FOUC. The toggle button lives in the header and flips between `.icon-sun` / `.icon-moon` SVGs.
- **Masonry look**: CSS Grid with `align-items: start` on `.posts-grid` — cards have natural height variation from content. No JavaScript masonry library needed.
- **Featured card**: first post on the listing page gets `post-card--featured` class which makes it `grid-column: 1 / -1` with a side-by-side image+text layout.
- **Typography**: Playfair Display (headings), Inter (body), Dancing Script (logo + `about-hero__label`). Loaded from Google Fonts **non-blocking** via a `style_loader_tag` filter (`vt_async_google_fonts` in `functions.php`) that rewrites the `<link rel="stylesheet">` to `<link rel="preload" as="style" onload="...">`. This breaks the render-blocking chain Lighthouse flags.
- **Accent color**: `#c8853a` warm orange. Decorative use only (borders, icons, progress bar). **Never use `--accent` for text** — it only passes 3:1 contrast on white. Use `--accent-text` (`#8c5a1e`, 5.25:1) for small labels and `--accent-hover` for interactive text states. Nav active/hover state uses `--accent-text` for this reason.
- **Image `sizes` attributes**: hero (`single.php`) uses `(max-width: 1264px) calc(100vw - 4rem), 1168px`; regular cards use `(max-width: 640px) calc(100vw - 4rem), (max-width: 1024px) calc(50vw - 3rem), 373px`; featured card uses `(max-width: 1024px) 100vw, calc(50vw - 2rem)`. These must match the CSS grid breakpoints — update both together if layout changes.
- **Hero image upload size**: upload images at least 1200px wide for `vt-hero`. The registered size is 1920×800 but Jetpack CDN handles the resize; images smaller than the target fall back to the original dimensions.

## Helper functions (functions.php)

| Function | Purpose |
|---|---|
| `vt_reading_time(?int $post_id)` | Returns `"N min read"` from word count ÷ 200 |
| `vt_the_categories(string $style)` | Echoes category links; `'plain'` for cards, `'pill'` for single posts |
| `vt_comment(...)` | Custom `wp_list_comments` callback |
| `vt_async_google_fonts(string $tag, string $handle)` | `style_loader_tag` filter — rewrites the Google Fonts `<link>` to non-blocking preload |
| `vt_defer_theme_js(string $tag, string $handle)` | `script_loader_tag` filter — adds `defer` to `vt-theme` script tag |

## Customizer options

Under **Appearance → Customize → Post Display**:
- "Show reading time" (`vt_show_reading_time`, default: true)
- "Show publish date" (`vt_show_date`, default: true)

Both are checked in `content-card.php` and `single.php` via `get_theme_mod()`.

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

Create a WordPress page, then assign it the **"About"** page template (`Template Name: About` in `page-about.php`). The featured image becomes the left-column portrait.

## Installing the theme

Copy the entire `wordpress_theme/` folder into `wp-content/themes/` then activate via **Appearance → Themes**. No npm, no compilation.

## Packaging the theme for upload

WordPress matches themes by the folder name inside the zip — it must be `vinu-thomas/` or WordPress will treat it as a new theme instead of updating the existing one.

**Always delete the old zip first**, then build fresh from a temp copy:

**Step 1 — bump the version** in `style.css` (line 7, `Version: x.x.x`) before zipping. Use semantic versioning: patch (1.2.1 → 1.2.2) for fixes, minor (1.2.x → 1.3.0) for new features.

```bash
rm -f ~/Desktop/vinu-thomas-theme.zip
cp -r /Users/vinuthomas/code/wordpress_theme /tmp/vinu-thomas
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

## MCP server (production)

The `vinuthomas-wp` MCP server lives at `/Users/vinuthomas/code/vinuthomas.com-mcp/index.js` and connects to the **production** WordPress REST API at `https://www.vinuthomas.com/wp-json/wp/v2`.

It is registered in `~/.claude/settings.json` (global) and is available in all Claude Code sessions. Credentials (`WP_USER`, `WP_PASSWORD`) are stored as env vars in that config — never hardcode them.

**Available tools:**

| Tool | What it does |
|---|---|
| `get_posts` | List posts; filter by search, category, page |
| `get_post` | Full content of a single post by ID or slug |
| `get_categories` | All categories with post counts |
| `get_pages` | All published pages |
| `get_tags` | All tags with post counts |
| `get_site_info` | Site summary: latest post + categories |
| `create_tag` | Create a new tag (auth required) |
| `update_post_terms` | Set categories/tags on a post (auth required) |
| `delete_tag` | Permanently delete a tag (auth required) |

**To verify the server is working**, run:
```
node -e "fetch('https://www.vinuthomas.com/wp-json/wp/v2/posts?per_page=1&_fields=id,title').then(r=>r.json()).then(console.log)"
```

## Local development environment

Docker Compose setup lives at `/Users/vinuthomas/code/vinuthomas-local/`.

**Start the local site:**
```
cd /Users/vinuthomas/code/vinuthomas-local && docker compose up -d
```

Site runs at **http://localhost:8080**. MySQL exposed on host port 3307.

**Theme symlinking:** `vinuthomas-local/site/wp-content/themes/vinu-thomas` is a symlink to this `wordpress_theme/` folder — edits here are instantly live in the browser, no sync needed.

**WP-CLI** (run one-off commands):
```
docker compose --profile tools run --rm wpcli <command>
```

**Stop the site:**
```
docker compose down
```
