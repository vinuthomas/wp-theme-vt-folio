<?php get_header(); ?>

<div class="container">

    <?php vt_breadcrumbs(); ?>

    <header class="archive-header">
        <span class="archive-header__label" aria-hidden="true">
            <?php
            if (is_category())    esc_html_e('Category',  'vt-folio');
            elseif (is_tag())     esc_html_e('Tag',        'vt-folio');
            elseif (is_author())  esc_html_e('Author',     'vt-folio');
            elseif (is_year())    esc_html_e('Year',       'vt-folio');
            elseif (is_month())   esc_html_e('Month',      'vt-folio');
            else                  esc_html_e('Archive',    'vt-folio');
            ?>
        </span>
        <h1 class="archive-header__title"><?php echo wp_kses_post( get_the_archive_title() ); ?></h1>
        <?php if (get_the_archive_description()) : ?>
        <p class="archive-header__description"><?php echo wp_kses_post(get_the_archive_description()); ?></p>
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
