<?php
/**
 * Template Name: About
 */
get_header();
?>

<div class="page-about">

    <?php while (have_posts()) : the_post(); ?>

    <?php if (has_post_thumbnail()) : ?>
    <div class="about-hero">
        <div class="about-hero__image">
            <?php the_post_thumbnail('vt-hero', ['loading' => 'eager', 'fetchpriority' => 'high', 'alt' => get_the_title()]); ?>
        </div>
        <div class="about-hero__overlay">
            <div class="container">
                <div class="about-hero__identity">
                    <div class="about-hero__avatar">
                        <?php echo get_avatar(get_option('admin_email'), 160, '', 'Vinu Thomas'); ?>
                    </div>
                    <h1 class="about-hero__label">
                        <?php echo esc_html(has_excerpt() ? get_the_excerpt() : get_the_title()); ?>
                    </h1>
                </div>
            </div>
        </div>
    </div>
    <?php else : ?>
    <div class="container">
        <h1 class="about-hero__label about-hero__label--standalone">
            <?php echo esc_html(has_excerpt() ? get_the_excerpt() : get_the_title()); ?>
        </h1>
    </div>
    <?php endif; ?>

    <div class="container">
        <div class="about-body">
            <div class="about-body__content entry-content">
                <?php the_content(); ?>
            </div>
        </div>
    </div>

    <?php endwhile; ?>

</div>

<?php get_footer(); ?>
