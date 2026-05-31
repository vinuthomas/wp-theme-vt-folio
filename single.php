<?php get_header(); ?>

<?php while (have_posts()) : the_post(); ?>

<?php
$vt_show_date = vt_get_mod('vt_show_date', true);
$vt_show_rt   = vt_get_mod('vt_show_reading_time', true);
$share_url    = rawurlencode(get_permalink());
$share_title  = rawurlencode(get_the_title());
?>

<div class="single-post-layout<?php echo get_post_format() === 'gallery' ? ' single-post-layout--full' : ''; ?>">

<!-- ── Left: floating social share bar ──────────────────────── -->
<aside class="social-share-bar" aria-label="<?php esc_attr_e('Share this post', 'vinu-thomas'); ?>">
    <span class="social-share-bar__label" aria-hidden="true"><?php esc_html_e('Share', 'vinu-thomas'); ?></span>

    <a href="<?php echo esc_url('https://www.facebook.com/sharer/sharer.php?u=' . $share_url); ?>"
       target="_blank" rel="noopener noreferrer"
       class="social-share-bar__btn"
       aria-label="<?php esc_attr_e('Share on Facebook (opens in new tab)', 'vinu-thomas'); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false" width="15" height="15">
            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
        </svg>
    </a>

    <a href="<?php echo esc_url('https://x.com/intent/post?url=' . $share_url . '&text=' . $share_title); ?>"
       target="_blank" rel="noopener noreferrer"
       class="social-share-bar__btn"
       aria-label="<?php esc_attr_e('Share on X (opens in new tab)', 'vinu-thomas'); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false" width="15" height="15">
            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
        </svg>
    </a>

    <a href="<?php echo esc_url('https://mastodon.social/share?text=' . $share_title . '%20' . $share_url); ?>"
       target="_blank" rel="noopener noreferrer"
       class="social-share-bar__btn"
       aria-label="<?php esc_attr_e('Share on Mastodon (opens in new tab)', 'vinu-thomas'); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false" width="15" height="15">
            <path d="M23.268 5.313c-.35-2.578-2.617-4.61-5.304-5.004C17.51.242 15.792 0 11.813 0h-.03c-3.98 0-4.835.242-5.288.309C3.882.692 1.496 2.518.917 5.127.64 6.412.61 7.837.661 9.143c.074 1.874.088 3.745.26 5.611.118 1.24.325 2.47.62 3.68.55 2.237 2.777 4.098 4.96 4.857 2.336.792 4.849.923 7.256.38.265-.061.527-.132.786-.213.585-.184 1.27-.39 1.774-.753a.057.057 0 0 0 .023-.043v-1.809a.052.052 0 0 0-.02-.041.053.053 0 0 0-.046-.01 20.282 20.282 0 0 1-4.709.545c-2.73 0-3.463-1.284-3.674-1.818a5.593 5.593 0 0 1-.319-1.433.053.053 0 0 1 .066-.054c1.517.363 3.072.546 4.632.546.376 0 .75 0 1.125-.01 1.57-.044 3.224-.124 4.768-.422.038-.008.077-.015.11-.024 2.435-.464 4.753-1.92 4.989-5.604.008-.145.03-1.52.03-1.67.002-.512.167-3.63-.024-5.545zm-3.748 9.195h-2.561V8.29c0-1.309-.55-1.976-1.67-1.976-1.23 0-1.846.79-1.846 2.35v3.403h-2.546V8.663c0-1.56-.617-2.35-1.848-2.35-1.112 0-1.668.668-1.67 1.977v6.218H4.822V8.102c0-1.31.337-2.35 1.011-3.12.696-.77 1.608-1.164 2.74-1.164 1.311 0 2.302.5 2.962 1.498l.638 1.06.638-1.06c.66-.999 1.65-1.498 2.96-1.498 1.13 0 2.043.395 2.74 1.164.675.77 1.012 1.81 1.012 3.12z"/>
        </svg>
    </a>

    <a href="<?php echo esc_url('https://www.linkedin.com/shareArticle?mini=true&url=' . $share_url); ?>"
       target="_blank" rel="noopener noreferrer"
       class="social-share-bar__btn"
       aria-label="<?php esc_attr_e('Share on LinkedIn (opens in new tab)', 'vinu-thomas'); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false" width="15" height="15">
            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
        </svg>
    </a>
</aside>

<!-- ── Centre: article ───────────────────────────────────────── -->
<div class="container">

<article id="post-<?php the_ID(); ?>" <?php post_class('single-post'); ?>>

    <?php if (has_post_thumbnail()) : ?>
    <div class="single-post__hero">
        <?php the_post_thumbnail('vt-hero', [
            'loading' => 'eager',
            'alt'     => '',
            'sizes'   => '(max-width: 1264px) calc(100vw - 4rem), 1168px',
        ]); ?>
        <header class="single-post__header">
            <?php if (has_category()) : ?>
            <div class="single-post__categories">
                <?php vt_the_categories('pill'); ?>
            </div>
            <?php endif; ?>
            <h1 class="single-post__title"><?php echo esc_html( get_the_title() ); ?></h1>
            <div class="single-post__meta">
                <?php if ($vt_show_date) : ?>
                <span class="single-post__meta-item">
                    <svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                    </svg>
                    <span class="screen-reader-text"><?php esc_html_e('Published', 'vinu-thomas'); ?></span>
                    <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date('F j, Y')); ?></time>
                </span>
                <?php endif; ?>
                <?php if ($vt_show_rt) : ?>
                <span class="single-post__meta-item">
                    <svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <?php echo esc_html(vt_reading_time()); ?>
                </span>
                <?php endif; ?>
            </div>
        </header>
    </div>
    <?php else : ?>
    <header class="single-post__header">
        <?php if (has_category()) : ?>
        <div class="single-post__categories">
            <?php vt_the_categories('pill'); ?>
        </div>
        <?php endif; ?>
        <h1 class="single-post__title"><?php echo esc_html( get_the_title() ); ?></h1>
        <div class="single-post__meta">
            <?php if ($vt_show_date) : ?>
            <span class="single-post__meta-item">
                <svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                </svg>
                <span class="screen-reader-text"><?php esc_html_e('Published', 'vinu-thomas'); ?></span>
                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date('F j, Y')); ?></time>
            </span>
            <?php endif; ?>
            <?php if ($vt_show_rt) : ?>
            <span class="single-post__meta-item">
                <svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <?php echo esc_html(vt_reading_time()); ?>
            </span>
            <?php endif; ?>
        </div>
    </header>
    <?php endif; ?>

    <div class="entry-content">
        <?php the_content(); ?>
    </div>

    <?php
    $tags = get_the_tags();
    if ($tags) :
    ?>
    <div class="post-tags">
        <?php foreach ($tags as $tag) : ?>
        <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="post-tag">
            #<?php echo esc_html($tag->name); ?>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <nav class="post-navigation" aria-label="<?php esc_attr_e('Post navigation', 'vinu-thomas'); ?>">
        <?php
        $prev = get_previous_post();
        $next = get_next_post();
        ?>
        <?php if ($prev) : ?>
        <div class="post-nav-item post-nav-item--prev">
            <span class="post-nav-label" aria-hidden="true">&larr; <?php esc_html_e('Previous', 'vinu-thomas'); ?></span>
            <a href="<?php echo esc_url(get_permalink($prev)); ?>" class="post-nav-title"
               aria-label="<?php echo esc_attr( sprintf( __('Previous post: %s', 'vinu-thomas'), get_the_title($prev) ) ); ?>">
                <?php echo esc_html(get_the_title($prev)); ?>
            </a>
        </div>
        <?php else : ?>
        <div></div>
        <?php endif; ?>

        <?php if ($next) : ?>
        <div class="post-nav-item post-nav-item--next">
            <span class="post-nav-label" aria-hidden="true"><?php esc_html_e('Next', 'vinu-thomas'); ?> &rarr;</span>
            <a href="<?php echo esc_url(get_permalink($next)); ?>" class="post-nav-title"
               aria-label="<?php echo esc_attr( sprintf( __('Next post: %s', 'vinu-thomas'), get_the_title($next) ) ); ?>">
                <?php echo esc_html(get_the_title($next)); ?>
            </a>
        </div>
        <?php endif; ?>
    </nav>

    <?php
    $author_bio = get_the_author_meta('description');
    if ($author_bio) :
    ?>
    <div class="author-box">
        <div class="author-box__avatar">
            <?php echo get_avatar(get_the_author_meta('user_email'), 72, '', get_the_author(), ['class' => '']); ?>
        </div>
        <div>
            <h3 class="author-box__name"><?php echo esc_html( get_the_author() ); ?></h3>
            <p class="author-box__bio"><?php echo esc_html($author_bio); ?></p>
        </div>
    </div>
    <?php endif; ?>

    <?php
    if (comments_open() || get_comments_number()) {
        comments_template();
    }
    ?>

</article>

</div><!-- .container -->

<!-- ── Right: widget sidebar ────────────────────────────────── -->
<aside class="single-sidebar" aria-label="<?php esc_attr_e('Post sidebar', 'vinu-thomas'); ?>" role="complementary">
    <?php if (is_active_sidebar('single-post-sidebar')) : ?>
        <?php dynamic_sidebar('single-post-sidebar'); ?>
    <?php endif; ?>
</aside>

</div><!-- .single-post-layout -->

<?php endwhile; ?>

<?php get_footer(); ?>
