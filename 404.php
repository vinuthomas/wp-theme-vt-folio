<?php get_header(); ?>

<div class="container">
    <div class="error-404">
        <div class="error-404__number" aria-hidden="true">404</div>
        <h1 class="error-404__title"><?php esc_html_e( 'Well, this is embarrassing.', 'vinu-thomas' ); ?></h1>
        <p class="error-404__text">
            <?php esc_html_e( "This page never existed, was quietly removed, or got deleted during one of those 'aggressive clean-ups' we always regret by Tuesday. Either way — it's gone, and it's not coming back.", 'vinu-thomas' ); ?>
        </p>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn--primary">
            <?php esc_html_e( 'Back to home', 'vinu-thomas' ); ?>
        </a>
    </div>

    <?php
    // Pull keywords from the requested URL slug so we can surface related posts.
    $request_path = trim( wp_parse_url( esc_url_raw( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH ), '/' );
    $slug_parts   = preg_split( '/[-\/]+/', $request_path );
    $stop_words   = [
        'a', 'about', 'above', 'after', 'again', 'against', 'all', 'also', 'am', 'an', 'and', 'any', 'are',
        'as', 'at', 'be', 'because', 'been', 'before', 'being', 'below', 'between', 'both', 'but', 'by',
        'can', 'did', 'do', 'does', 'doing', 'down', 'during', 'each', 'few', 'for', 'from', 'get', 'got',
        'had', 'has', 'have', 'having', 'he', 'her', 'here', 'hers', 'him', 'his', 'how', 'i', 'if', 'in',
        'into', 'is', 'it', 'its', 'itself', 'just', 'me', 'more', 'most', 'my', 'no', 'not', 'now', 'of',
        'off', 'on', 'once', 'only', 'or', 'other', 'our', 'out', 'own', 's', 'same', 'she', 'should', 'so',
        'some', 'such', 'than', 'that', 'the', 'their', 'them', 'then', 'there', 'these', 'they', 'this',
        'those', 'through', 'to', 'too', 'under', 'until', 'up', 'us', 'very', 'was', 'we', 'were', 'what',
        'when', 'where', 'which', 'while', 'who', 'whom', 'why', 'will', 'with', 'you', 'your',
    ];
    $keywords     = array_filter( $slug_parts, fn( $w ) => strlen( $w ) > 2 && ! in_array( strtolower( $w ), $stop_words, true ) );

    if ( ! empty( $keywords ) ) :
        $suggestions = new WP_Query( [
            's'              => implode( ' ', $keywords ),
            'posts_per_page' => 3,
            'post_status'    => 'publish',
            'no_found_rows'  => true,
        ] );

        if ( $suggestions->have_posts() ) : ?>
        <div class="error-404__suggestions">
            <h2 class="error-404__suggestions-title"><?php esc_html_e( 'Did you mean one of these?', 'vinu-thomas' ); ?></h2>
            <div class="posts-grid">
                <?php while ( $suggestions->have_posts() ) : $suggestions->the_post(); ?>
                    <?php get_template_part( 'template-parts/content', 'card' ); ?>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
        <?php endif;
    endif; ?>

</div>

<?php get_footer(); ?>
