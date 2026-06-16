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
   Cache-Control header for anonymous front-end responses.

   HTML page views get `no-cache`: the browser may store the response
   but must revalidate before reuse. Unlike `no-store`, this keeps the
   page eligible for the back/forward cache (bfcache) — `no-store`
   disables bfcache in Chrome, forcing a full reload (and the dark-mode
   flash) on every back/forward navigation. Cloudflare edge-caches HTML
   separately for a week (purged on publish), so revalidation is cheap.

   Dynamic endpoints (feeds, *.php such as xmlrpc/comments) keep
   `no-store` so they are never stored anywhere. The Cloudflare Cache
   Rule excludes the same paths from edge caching.
   ---------------------------------------------------------------- */

add_action('send_headers', function (): void {
    if (is_user_logged_in()) {
        return;
    }
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    if (is_feed() || strpos($uri, '.php') !== false) {
        header('Cache-Control: no-store');
    } else {
        header('Cache-Control: no-cache');
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
   Open Graph + Twitter Card meta tags
   ---------------------------------------------------------------- */

function vt_og_tags(): void {
    // Bail if a dedicated SEO plugin or Jetpack is already providing OG tags
    if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || function_exists( 'jetpack_og_tags' ) ) return;

    $site_name = get_bloginfo( 'name' );
    $image     = '';
    $type      = 'website';

    if ( is_singular() ) {
        $post        = get_queried_object();
        $title       = get_the_title( $post );
        $description = wp_strip_all_tags( get_the_excerpt( $post ) ) ?: get_bloginfo( 'description' );
        $url         = get_permalink( $post );
        if ( is_singular( 'post' ) ) $type = 'article';

        if ( has_post_thumbnail( $post ) ) {
            $img = wp_get_attachment_image_src( get_post_thumbnail_id( $post ), 'vt-hero' );
            if ( $img ) $image = $img[0];
        }
    } else {
        $title       = wp_get_document_title();
        $description = get_bloginfo( 'description' );
        $url         = is_home() || is_front_page() ? home_url( '/' ) : get_pagenum_link();
    }

    $description = mb_substr( wp_strip_all_tags( $description ), 0, 160 );

    $x_url          = vt_get_mod( 'vt_social_x', 'https://x.com/vinuthomas' );
    $twitter_handle = '';
    if ( $x_url ) {
        $path = trim( parse_url( $x_url, PHP_URL_PATH ) ?? '', '/' );
        if ( $path && strpos( $path, '/' ) === false ) {
            $twitter_handle = '@' . $path;
        }
    }

    $out  = "\n";
    $out .= '<meta property="og:type" content="'        . esc_attr( $type )        . '">' . "\n";
    $out .= '<meta property="og:title" content="'       . esc_attr( $title )       . '">' . "\n";
    $out .= '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
    $out .= '<meta property="og:url" content="'         . esc_url( $url )          . '">' . "\n";
    $out .= '<meta property="og:site_name" content="'   . esc_attr( $site_name )   . '">' . "\n";
    if ( $image ) {
        $out .= '<meta property="og:image" content="'   . esc_url( $image )        . '">' . "\n";
    }
    $out .= '<meta name="twitter:card" content="summary_large_image">'                    . "\n";
    $out .= '<meta name="twitter:title" content="'       . esc_attr( $title )       . '">' . "\n";
    $out .= '<meta name="twitter:description" content="' . esc_attr( $description ) . '">' . "\n";
    if ( $image ) {
        $out .= '<meta name="twitter:image" content="'   . esc_url( $image )        . '">' . "\n";
    }
    if ( $twitter_handle ) {
        $out .= '<meta name="twitter:creator" content="' . esc_attr( $twitter_handle ) . '">' . "\n";
    }

    echo $out; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'wp_head', 'vt_og_tags', 2 );

/* ----------------------------------------------------------------
   Canonical <link> tag
   ---------------------------------------------------------------- */

function vt_canonical(): void {
    if ( defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION') ) return;

    // Prevent WP core's rel_canonical (singular-only) from duplicating ours.
    remove_action('wp_head', 'rel_canonical');

    if ( is_singular() ) {
        $url = get_permalink();
    } elseif ( is_home() || is_front_page() ) {
        $url = home_url('/');
    } else {
        $url = get_pagenum_link();
    }

    if ( $url ) {
        echo '<link rel="canonical" href="' . esc_url( $url ) . '">' . "\n";
    }
}
add_action('wp_head', 'vt_canonical', 3);

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
   Person JSON-LD schema on the homepage
   ---------------------------------------------------------------- */

function vt_person_schema(): void {
    if ( ! is_home() && ! is_front_page() ) return;

    $users = get_users([ 'role' => 'administrator', 'number' => 1 ]);
    if ( empty( $users ) ) return;
    $user = $users[0];

    $social_links = array_values( array_filter([
        vt_get_mod('vt_social_x',          'https://x.com/vinuthomas'),
        vt_get_mod('vt_social_linkedin',   'https://linkedin.com/in/vinuthomas'),
        vt_get_mod('vt_social_mastodon',   'https://mastodon.online/@vinuthomas'),
        vt_get_mod('vt_social_instagram',  'https://www.instagram.com/vinuthomas'),
        vt_get_mod('vt_social_soundcloud', 'https://soundcloud.com/vinuthomas'),
        vt_get_mod('vt_social_github',     ''),
        vt_get_mod('vt_social_bluesky',    ''),
        vt_get_mod('vt_social_youtube',    ''),
    ]));

    $schema = [
        '@context'    => 'https://schema.org',
        '@type'       => 'Person',
        'name'        => $user->display_name,
        'url'         => home_url('/'),
        'description' => $user->description ?: get_bloginfo('description'),
    ];

    if ( $social_links ) {
        $schema['sameAs'] = $social_links;
    }

    echo '<script type="application/ld+json">'
        . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG )
        . "</script>\n";
}
add_action('wp_head', 'vt_person_schema');

/* ----------------------------------------------------------------
   Breadcrumbs
   ---------------------------------------------------------------- */

function vt_breadcrumbs(): void {
    $items = [[ 'url' => home_url('/'), 'label' => __('Home', 'vt-folio') ]];

    if ( is_singular('post') ) {
        $cats = get_the_category();
        if ( $cats ) {
            $items[] = [ 'url' => get_category_link( $cats[0]->term_id ), 'label' => $cats[0]->name ];
        }
        $items[] = [ 'url' => '', 'label' => get_the_title() ];
    } elseif ( is_singular() ) {
        $items[] = [ 'url' => '', 'label' => get_the_title() ];
    } elseif ( is_category() ) {
        $items[] = [ 'url' => '', 'label' => single_cat_title('', false) ];
    } elseif ( is_tag() ) {
        $items[] = [ 'url' => '', 'label' => single_tag_title('', false) ];
    } elseif ( is_author() ) {
        $items[] = [ 'url' => '', 'label' => get_the_author() ];
    } elseif ( is_year() ) {
        $items[] = [ 'url' => '', 'label' => get_the_date('Y') ];
    } elseif ( is_month() ) {
        $items[] = [ 'url' => get_year_link( get_the_date('Y') ), 'label' => get_the_date('Y') ];
        $items[] = [ 'url' => '', 'label' => get_the_date('F') ];
    } elseif ( is_search() ) {
        /* translators: %s: search term */
        $items[] = [ 'url' => '', 'label' => sprintf( __('Search: %s', 'vt-folio'), get_search_query() ) ];
    } else {
        return;
    }

    if ( count($items) <= 1 ) return;

    $last = count($items) - 1;
    echo '<nav class="vt-breadcrumbs" aria-label="' . esc_attr__('Breadcrumb', 'vt-folio') . '">';
    echo '<ol class="vt-breadcrumbs__list">';
    foreach ( $items as $i => $item ) {
        echo '<li class="vt-breadcrumbs__item">';
        if ( $i === $last ) {
            echo '<span aria-current="page">' . esc_html( $item['label'] ) . '</span>';
        } else {
            echo '<a href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['label'] ) . '</a>';
        }
        echo '</li>';
    }
    echo '</ol></nav>';
}

/* ----------------------------------------------------------------
   BreadcrumbList JSON-LD schema
   ---------------------------------------------------------------- */

function vt_breadcrumb_schema(): void {
    if ( defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION') ) return;

    $items = [[ 'url' => home_url('/'), 'label' => __('Home', 'vt-folio') ]];

    if ( is_singular('post') ) {
        $cats = get_the_category();
        if ( $cats ) {
            $items[] = [ 'url' => get_category_link( $cats[0]->term_id ), 'label' => $cats[0]->name ];
        }
        $items[] = [ 'url' => get_permalink(), 'label' => get_the_title() ];
    } elseif ( is_singular() ) {
        $items[] = [ 'url' => get_permalink(), 'label' => get_the_title() ];
    } elseif ( is_category() ) {
        $items[] = [ 'url' => get_category_link( get_queried_object_id() ), 'label' => single_cat_title('', false) ];
    } elseif ( is_tag() ) {
        $items[] = [ 'url' => get_tag_link( get_queried_object_id() ), 'label' => single_tag_title('', false) ];
    } elseif ( is_author() ) {
        $items[] = [ 'url' => get_author_posts_url( get_queried_object_id() ), 'label' => get_the_author() ];
    } else {
        return;
    }

    if ( count($items) <= 1 ) return;

    $list_items = [];
    foreach ( $items as $pos => $item ) {
        $list_items[] = [
            '@type'    => 'ListItem',
            'position' => $pos + 1,
            'name'     => $item['label'],
            'item'     => $item['url'],
        ];
    }

    $schema = [
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $list_items,
    ];

    echo '<script type="application/ld+json">'
        . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG )
        . "</script>\n";
}
add_action('wp_head', 'vt_breadcrumb_schema');

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

