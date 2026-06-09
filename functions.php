<?php

defined('ABSPATH') || exit;

require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/cookie-consent.php';

/* ----------------------------------------------------------------
   Theme setup
   ---------------------------------------------------------------- */

function vt_setup(): void {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('automatic-feed-links');
    add_theme_support('html5', ['search-form','comment-form','comment-list','gallery','caption','style','script']);
    add_theme_support('customize-selective-refresh-widgets');
    add_theme_support('align-wide');
    add_theme_support('responsive-embeds');
    add_theme_support('editor-styles');
    add_theme_support('wp-block-styles');
    add_theme_support('post-formats', ['gallery']);
    add_theme_support('custom-logo', [
        'height'      => 60,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ]);

    add_image_size('vt-card',     600, 380, true);
    add_image_size('vt-featured', 1200, 700, true);
    add_image_size('vt-hero',     1920, 800, true);

    register_nav_menus([
        'primary' => __('Primary Navigation', 'vt-folio'),
        'footer'  => __('Footer Navigation',  'vt-folio'),
    ]);

    add_editor_style('style.css');
}
add_action('after_setup_theme', 'vt_setup');

/* ----------------------------------------------------------------
   Scripts & styles
   ---------------------------------------------------------------- */

function vt_enqueue(): void {
    $ver = wp_get_theme()->get('Version');

    // Include the logo font only on pages that actually render it
    // (About page template and 404) to avoid a wasted font fetch everywhere else.
    $include_logo = is_404() || is_page_template('page-about.php');
    wp_enqueue_style('vt-fonts', vt_google_fonts_url( $include_logo ), [], null);

    wp_enqueue_style('vt-style', get_stylesheet_uri(), ['vt-fonts'], $ver);

    wp_enqueue_script('vt-theme', get_template_directory_uri() . '/assets/js/theme.js', [], $ver, true);

    if (is_singular() && comments_open()) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'vt_enqueue');

/* ----------------------------------------------------------------
   Remove wp-emoji — this site uses standard Unicode emoji and
   doesn't need WordPress's SVG polyfill loader (~6KB inline script
   + styles injected on every page).
   ---------------------------------------------------------------- */

add_action('init', function (): void {
    remove_action('wp_head',              'print_emoji_detection_script', 7);
    remove_action('wp_print_styles',      'print_emoji_styles');
    remove_action('admin_print_scripts',  'print_emoji_detection_script');
    remove_action('admin_print_styles',   'print_emoji_styles');
    remove_filter('the_content_feed',     'wp_staticize_emoji');
    remove_filter('comment_text_rss',     'wp_staticize_emoji');
    remove_filter('wp_mail',              'wp_staticize_emoji_for_email');
    add_filter('emoji_svg_url',           '__return_false');
});

/* ----------------------------------------------------------------
   Load per-block CSS separately so only the styles for blocks
   actually used on a given page are shipped.
   ---------------------------------------------------------------- */

add_filter('should_load_separate_core_block_assets', '__return_true');

/**
 * Inject preconnect hints for Google Fonts before wp_head() outputs anything
 * else. Two hints are required: one for the stylesheet host (fonts.googleapis.com)
 * and one crossorigin hint for the font-file CDN (fonts.gstatic.com). These
 * allow the browser to resolve DNS + complete TLS handshakes in parallel with
 * the HTML parse, saving ~300–500 ms on a cold first load.
 */
add_action('wp_head', function (): void {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}, 1); // priority 1 — fires before wp_enqueue_scripts outputs the <link> tags

/**
 * Load the Google Fonts stylesheet non-blocking via a preload swap.
 * A plain <link rel="stylesheet"> blocks rendering until the external CSS
 * and every @font-face woff2 it references are downloaded — that is the
 * three-step chain Lighthouse flags. Switching to rel="preload" + onload
 * breaks the chain: the browser fetches the CSS at high priority but does
 * not stall rendering while waiting for it. display=swap in the URL already
 * ensures text renders in fallback fonts until the custom fonts arrive.
 */
function vt_async_google_fonts(string $tag, string $handle): string {
    if ('vt-fonts' !== $handle) return $tag;
    // Extract the href from the tag WordPress already built so the URL matches
    // exactly what was enqueued (which varies by page via $include_logo).
    preg_match("/href='([^']+)'/", $tag, $m);
    if (!$m) return $tag;
    $url = esc_url($m[1]);
    return '<link rel="preload" href="' . $url . '" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">' . "\n"
         . '<noscript><link rel="stylesheet" href="' . $url . '"></noscript>' . "\n";
}
add_filter('style_loader_tag', 'vt_async_google_fonts', 10, 2);

/**
 * Add defer to the theme JS tag. The script is already loaded in the footer
 * (in_footer: true), but `defer` ensures it doesn't block any remaining
 * render work and signals intent clearly to the browser. It is safe here
 * because theme.js guards all DOM queries with null checks.
 */
function vt_defer_theme_js(string $tag, string $handle): string {
    if ('vt-theme' === $handle) {
        return str_replace(' src=', ' defer src=', $tag);
    }
    return $tag;
}
add_filter('script_loader_tag', 'vt_defer_theme_js', 10, 2);

/* ----------------------------------------------------------------
   Helpers
   ---------------------------------------------------------------- */

/**
 * Cached wrapper around get_theme_mod(). Templates that call this inside
 * the post loop (content-card.php) or in branching blocks (single.php) avoid
 * redundant option lookups; WordPress already caches the option internally but
 * this ensures a single PHP function call per mod key per request.
 */
function vt_get_mod(string $key, mixed $default = false): mixed {
    static $cache = [];
    if (!array_key_exists($key, $cache)) {
        $cache[$key] = get_theme_mod($key, $default);
    }
    return $cache[$key];
}

function vt_reading_time(?int $post_id = null): string {
    $content    = get_post_field('post_content', $post_id ?? get_the_ID());
    $word_count = str_word_count(strip_tags($content));
    $minutes    = max(1, (int) ceil($word_count / 200));

    return $minutes === 1
        ? '1 min read'
        : "{$minutes} min read";
}

function vt_the_categories(string $style = 'plain'): void {
    $cats = get_the_category();
    if (empty($cats)) return;

    foreach ($cats as $cat) {
        if ($style === 'pill') {
            printf(
                '<a href="%s" class="single-post__category">%s</a>',
                esc_url(get_category_link($cat->term_id)),
                esc_html($cat->name)
            );
        } else {
            printf(
                '<a href="%s" class="post-card__category">%s</a>',
                esc_url(get_category_link($cat->term_id)),
                esc_html($cat->name)
            );
        }
    }
}

/* ----------------------------------------------------------------
   Cache-Control header — tell upstream proxies not to rely on the
   origin header (Jetpack sends max-age=172800). Cloudflare already
   ignores it via the Cache Rule, but this keeps the origin clean.
   ---------------------------------------------------------------- */

add_action('send_headers', function (): void {
    if (!is_user_logged_in()) {
        header('Cache-Control: no-store');
    }
});

/* ----------------------------------------------------------------
   Custom comment template
   ---------------------------------------------------------------- */

function vt_comment(WP_Comment $comment, array $args, int $depth): void {
    $author_name = get_comment_author($comment);
    ?>
    <li id="comment-<?php comment_ID(); ?>" <?php comment_class('comment', $comment); ?>>
        <article>
        <div class="comment-meta">
            <div class="comment-avatar"><?php echo get_avatar($comment, 44, '', $author_name); ?></div>
            <div>
                <span class="comment-author-name"><?php echo esc_html($author_name); ?></span>
                <time class="comment-date" datetime="<?php echo esc_attr( get_comment_date( 'c', $comment ) ); ?>">
                    <?php echo esc_html( get_comment_date( 'F j, Y', $comment ) ); ?>
                </time>
            </div>
        </div>
        <div class="comment-content"><?php comment_text(); ?></div>
        <footer class="reply">
            <?php
            comment_reply_link(array_merge($args, [
                'reply_text' => __('Reply', 'vt-folio'),
                'depth'      => $depth,
                'max_depth'  => $args['max_depth'] ?? 5,
            ]));
            ?>
        </footer>
        </article>
    <?php
    // li is closed by WordPress
}

/* ----------------------------------------------------------------
   Miscellaneous
   ---------------------------------------------------------------- */

add_filter('excerpt_length', fn() => 22, 999);
add_filter('excerpt_more',   fn() => '&hellip;');

function vt_body_classes(array $classes): array {
    if (is_singular()) $classes[] = 'is-singular';
    return $classes;
}
add_filter('body_class', 'vt_body_classes');

/* ----------------------------------------------------------------
   Article JSON-LD schema (AEO/GEO)
   ---------------------------------------------------------------- */

function vt_article_schema(): void {
    if ( ! is_singular( 'post' ) ) return;

    $post      = get_queried_object();
    $author_id = $post->post_author;
    $author_url = get_the_author_meta( 'user_url', $author_id ) ?: get_home_url();

    $schema = [
        '@context'         => 'https://schema.org',
        '@type'            => 'BlogPosting',
        'headline'         => get_the_title( $post ),
        'description'      => wp_strip_all_tags( get_the_excerpt( $post ) ),
        'url'              => get_permalink( $post ),
        'datePublished'    => get_the_date( 'c', $post ),
        'dateModified'     => get_the_modified_date( 'c', $post ),
        'inLanguage'       => get_bloginfo( 'language' ),
        'wordCount'        => str_word_count( wp_strip_all_tags( $post->post_content ) ),
        'author'           => [
            '@type' => 'Person',
            'name'  => get_the_author_meta( 'display_name', $author_id ),
            'url'   => $author_url,
        ],
        'publisher'        => [
            '@type' => 'Person',
            'name'  => get_the_author_meta( 'display_name', $author_id ),
            'url'   => $author_url,
        ],
        'mainEntityOfPage' => [
            '@type' => 'WebPage',
            '@id'   => get_permalink( $post ),
        ],
    ];

    // Featured image
    if ( has_post_thumbnail( $post ) ) {
        $img = wp_get_attachment_image_src( get_post_thumbnail_id( $post ), 'vt-hero' );
        if ( $img ) {
            $schema['image'] = [
                '@type'  => 'ImageObject',
                'url'    => $img[0],
                'width'  => $img[1],
                'height' => $img[2],
            ];
        }
    }

    // Primary category as articleSection
    $cats = get_the_category( $post->ID );
    if ( $cats ) {
        $schema['articleSection'] = $cats[0]->name;
    }

    // Tags as keywords
    $tags = get_the_tags( $post->ID );
    if ( $tags ) {
        $schema['keywords'] = implode( ', ', wp_list_pluck( $tags, 'name' ) );
    }

    echo '<script type="application/ld+json">'
        . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG )
        . "</script>\n";
}
add_action( 'wp_head', 'vt_article_schema' );

/* ----------------------------------------------------------------
   Widget areas
   ---------------------------------------------------------------- */

function vt_widgets_init(): void {
    register_sidebar([
        'name'          => __('Single Post Sidebar', 'vt-folio'),
        'id'            => 'single-post-sidebar',
        'description'   => __('Appears beside single posts on wide screens (≥1380px).', 'vt-folio'),
        'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="sidebar-widget__title">',
        'after_title'   => '</h3>',
    ]);
}
add_action('widgets_init', 'vt_widgets_init');

/* ----------------------------------------------------------------
   Posts per page — apply Customizer value to main query
   ---------------------------------------------------------------- */

add_action('pre_get_posts', function (WP_Query $query): void {
    if (is_admin() || ! $query->is_main_query()) return;
    if (! $query->is_home() && ! $query->is_archive() && ! $query->is_search()) return;
    $ppp = (int) vt_get_mod('vt_posts_per_page', 10);
    if ($ppp > 0) $query->set('posts_per_page', $ppp);
});

