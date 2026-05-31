<?php get_header(); ?>

<div class="container">

    <header class="search-header">
        <?php $query = get_search_query(false); ?>
        <span class="archive-header__label" aria-hidden="true"><?php esc_html_e('Search results', 'vt-folio'); ?></span>
        <?php if ($query) : ?>
        <h1 class="archive-header__title">
            <span class="screen-reader-text"><?php esc_html_e('Search results for:', 'vt-folio'); ?> </span>&ldquo;<?php echo esc_html($query); ?>&rdquo;
        </h1>
        <?php else : ?>
        <h1 class="archive-header__title"><?php esc_html_e('Nothing found', 'vt-folio'); ?></h1>
        <?php endif; ?>
    </header>

    <?php if (have_posts()) : ?>

        <div class="posts-grid">
            <?php while (have_posts()) : the_post(); ?>
                <?php get_template_part('template-parts/content', 'card'); ?>
            <?php endwhile; ?>
        </div>

        <?php the_posts_pagination([
            'mid_size'  => 2,
            'prev_text' => '<span aria-hidden="true">&larr;</span><span class="screen-reader-text">' . __('Previous page', 'vt-folio') . '</span>',
            'next_text' => '<span aria-hidden="true">&rarr;</span><span class="screen-reader-text">' . __('Next page', 'vt-folio') . '</span>',
        ]); ?>

    <?php else : ?>
        <?php get_template_part('template-parts/content', 'none'); ?>
    <?php endif; ?>

</div>

<?php get_footer(); ?>
