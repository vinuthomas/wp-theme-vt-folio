<?php get_header(); ?>

<div class="container">

    <h1 class="screen-reader-text"><?php bloginfo('name'); ?></h1>

    <?php if (have_posts()) : ?>

        <div class="posts-grid">
            <?php
            $count = 0;
            while (have_posts()) :
                the_post();
                $count++;
                get_template_part('template-parts/content', 'card', ['is_featured' => $count === 1]);
            endwhile;
            ?>
        </div>

        <?php
        the_posts_pagination([
            'mid_size'  => 2,
            'prev_text' => '<span aria-hidden="true">&larr;</span><span class="screen-reader-text">' . __('Previous page', 'vt-folio') . '</span>',
            'next_text' => '<span aria-hidden="true">&rarr;</span><span class="screen-reader-text">' . __('Next page', 'vt-folio') . '</span>',
        ]);
        ?>

    <?php else : ?>
        <?php get_template_part('template-parts/content', 'none'); ?>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
