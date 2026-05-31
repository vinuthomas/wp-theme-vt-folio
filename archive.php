<?php get_header(); ?>

<div class="container">

    <header class="archive-header">
        <span class="archive-header__label" aria-hidden="true">
            <?php
            if (is_category())    esc_html_e('Category',  'vinu-thomas');
            elseif (is_tag())     esc_html_e('Tag',        'vinu-thomas');
            elseif (is_author())  esc_html_e('Author',     'vinu-thomas');
            elseif (is_year())    esc_html_e('Year',       'vinu-thomas');
            elseif (is_month())   esc_html_e('Month',      'vinu-thomas');
            else                  esc_html_e('Archive',    'vinu-thomas');
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
            'prev_text' => '<span aria-hidden="true">&larr;</span><span class="screen-reader-text">' . __('Previous page', 'vinu-thomas') . '</span>',
            'next_text' => '<span aria-hidden="true">&rarr;</span><span class="screen-reader-text">' . __('Next page', 'vinu-thomas') . '</span>',
        ]); ?>

    <?php else : ?>
        <?php get_template_part('template-parts/content', 'none'); ?>
    <?php endif; ?>

</div>

<?php get_footer(); ?>
