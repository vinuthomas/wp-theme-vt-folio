<?php get_header(); ?>

<div class="container">

<?php while (have_posts()) : the_post(); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class('single-post'); ?>>

    <header class="single-post__header">
        <h1 class="single-post__title"><?php echo esc_html( get_the_title() ); ?></h1>
    </header>

    <?php if (has_post_thumbnail()) : ?>
    <div class="page-featured-image">
        <?php the_post_thumbnail('vt-hero', ['loading' => 'eager', 'alt' => '']); ?>
    </div>
    <?php endif; ?>

    <div class="entry-content">
        <?php the_content(); ?>
    </div>

    <?php
    if (comments_open() || get_comments_number()) {
        comments_template();
    }
    ?>

</article>

<?php endwhile; ?>

</div><!-- .container -->

<?php get_footer(); ?>
