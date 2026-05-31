<?php
$is_featured = $args['is_featured'] ?? false;
$card_class  = $is_featured ? 'post-card post-card--featured' : 'post-card';
$img_size    = $is_featured ? 'vt-featured' : 'vt-card';
// Use the cached wrapper so repeated calls inside the post loop don't re-query the options table.
$show_date   = vt_get_mod('vt_show_date', true);
$show_rt     = vt_get_mod('vt_show_reading_time', true);
?>

<article id="post-<?php the_ID(); ?>" <?php post_class($card_class); ?> aria-label="<?php echo esc_attr( get_the_title() ); ?>">

    <?php if (has_post_thumbnail()) : ?>
    <div class="post-card__image">
        <a href="<?php echo esc_url( get_the_permalink() ); ?>" tabindex="-1" aria-hidden="true">
            <?php
            $sizes = $is_featured
                ? '(max-width: 1024px) 100vw, calc(50vw - 2rem)'
                : '(max-width: 640px) calc(100vw - 4rem), (max-width: 1024px) calc(50vw - 3rem), 373px';
            the_post_thumbnail($img_size, ['loading' => $is_featured ? 'eager' : 'lazy', 'alt' => '', 'sizes' => $sizes]);
            ?>
        </a>
    </div>
    <?php endif; ?>

    <div class="post-card__body">

        <div class="post-card__categories">
            <?php if (has_category()) : vt_the_categories('plain'); endif; ?>
        </div>

        <h2 class="post-card__title">
            <a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
        </h2>

        <p class="post-card__excerpt">
            <?php echo esc_html( wp_trim_words( get_the_excerpt(), 22, '&hellip;' ) ); ?>
        </p>

        <?php if ($show_date || $show_rt) : ?>
        <div class="post-card__meta">
            <?php if ($show_date) : ?>
            <span class="post-card__meta-item">
                <svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                </svg>
                <span class="screen-reader-text"><?php esc_html_e('Published', 'vt-folio'); ?></span>
                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date('M j, Y')); ?></time>
            </span>
            <?php endif; ?>

            <?php if ($show_rt) : ?>
            <span class="post-card__meta-item">
                <svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <?php echo esc_html(vt_reading_time(get_the_ID())); ?>
            </span>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div>

</article>
