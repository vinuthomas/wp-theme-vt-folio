</main><!-- #main -->

<footer class="site-footer" role="contentinfo">
    <div class="container">
        <div class="site-footer__inner">

            <a href="<?php echo esc_url(home_url('/')); ?>"
               class="footer-logo site-logo"
               aria-label="<?php echo esc_attr( get_bloginfo('name') ); ?>">
                <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/logo-dark.png'); ?>"
                     alt=""
                     class="site-logo__img site-logo__img--light"
                     height="28" width="78">
                <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/logo-light.png'); ?>"
                     alt=""
                     class="site-logo__img site-logo__img--dark"
                     height="28" width="78">
                <span class="site-logo__text" aria-hidden="true"><?php echo esc_html(vt_get_mod('vt_logo_text', 'Vinu Thomas')); ?></span>
            </a>

            <?php if (has_nav_menu('footer')) : ?>
            <nav class="footer-nav" aria-label="<?php esc_attr_e('Footer navigation', 'vt-folio'); ?>">
                <?php
                wp_nav_menu([
                    'theme_location' => 'footer',
                    'container'      => false,
                    'depth'          => 1,
                    'fallback_cb'    => false,
                ]);
                ?>
            </nav>
            <?php endif; ?>

            <?php
            $vt_url_x          = vt_get_mod('vt_social_x',          'https://x.com/vinuthomas');
            $vt_url_linkedin   = vt_get_mod('vt_social_linkedin',   'https://linkedin.com/in/vinuthomas');
            $vt_url_instagram  = vt_get_mod('vt_social_instagram',  'https://www.instagram.com/vinuthomas');
            $vt_url_soundcloud = vt_get_mod('vt_social_soundcloud', 'https://soundcloud.com/vinuthomas');
            $vt_url_mastodon   = vt_get_mod('vt_social_mastodon',   'https://mastodon.online/@vinuthomas');
            $vt_url_youtube    = vt_get_mod('vt_social_youtube',    '');
            $vt_url_github     = vt_get_mod('vt_social_github',     '');
            $vt_url_bluesky    = vt_get_mod('vt_social_bluesky',    '');
            ?>
            <div class="footer-social">
                <?php if ($vt_url_x) : ?>
                <a href="<?php echo esc_url($vt_url_x); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('X / Twitter (opens in new tab)', 'vt-folio'); ?>">
                    <svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="16" height="16">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                </a>
                <?php endif; ?>
                <?php if ($vt_url_linkedin) : ?>
                <a href="<?php echo esc_url($vt_url_linkedin); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('LinkedIn (opens in new tab)', 'vt-folio'); ?>">
                    <svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="16" height="16">
                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                    </svg>
                </a>
                <?php endif; ?>
                <?php if ($vt_url_instagram) : ?>
                <a href="<?php echo esc_url($vt_url_instagram); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Instagram (opens in new tab)', 'vt-folio'); ?>">
                    <svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="16" height="16">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                </a>
                <?php endif; ?>
                <?php if ($vt_url_soundcloud) : ?>
                <a href="<?php echo esc_url($vt_url_soundcloud); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('SoundCloud (opens in new tab)', 'vt-folio'); ?>">
                    <svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="16" height="16">
                        <path d="M11.56 8.87V17h8.76c1.49 0 2.68-1.18 2.68-2.68 0-1.35-1.01-2.47-2.32-2.65.11-.38.17-.77.17-1.18 0-2.37-1.92-4.29-4.29-4.29-1.38 0-2.6.65-3.4 1.67zM9.98 10.01C9.84 9.69 9.79 9.44 9.79 9.18c0-1.5 1.21-2.72 2.72-2.72.41 0 .79.09 1.13.25C12.73 5.08 11.2 4 9.44 4 7.01 4 5.04 5.97 5.04 8.4c0 .44.06.87.18 1.27C3.97 9.95 3 11.04 3 12.37 3 13.82 4.18 15 5.63 15h4.35v-4.99z"/>
                    </svg>
                </a>
                <?php endif; ?>
                <?php if ($vt_url_mastodon) : ?>
                <a href="<?php echo esc_url($vt_url_mastodon); ?>" target="_blank" rel="noopener noreferrer me" aria-label="<?php esc_attr_e('Mastodon (opens in new tab)', 'vt-folio'); ?>">
                    <svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="16" height="16">
                        <path d="M23.268 5.313c-.35-2.578-2.617-4.61-5.304-5.004C17.51.242 15.792 0 11.813 0h-.03c-3.98 0-4.835.242-5.288.309C3.882.692 1.496 2.518.917 5.127.64 6.412.61 7.837.661 9.143c.074 1.874.088 3.745.26 5.611.118 1.24.325 2.47.62 3.68.55 2.237 2.777 4.098 4.96 4.857 2.336.792 4.849.923 7.256.38.265-.061.527-.132.786-.213.585-.184 1.27-.39 1.774-.753a.057.057 0 0 0 .023-.043v-1.809a.052.052 0 0 0-.02-.041.053.053 0 0 0-.046-.01 20.282 20.282 0 0 1-4.709.545c-2.73 0-3.463-1.284-3.674-1.818a5.593 5.593 0 0 1-.319-1.433.053.053 0 0 1 .066-.054c1.517.363 3.072.546 4.632.546.376 0 .75 0 1.125-.01 1.57-.044 3.224-.124 4.768-.422.038-.008.077-.015.11-.024 2.435-.464 4.753-1.92 4.989-5.604.008-.145.03-1.52.03-1.67.002-.512.167-3.63-.024-5.545zm-3.748 9.195h-2.561V8.29c0-1.309-.55-1.976-1.67-1.976-1.23 0-1.846.79-1.846 2.35v3.403h-2.546V8.663c0-1.56-.617-2.35-1.848-2.35-1.112 0-1.668.668-1.67 1.977v6.218H4.822V8.102c0-1.31.337-2.35 1.011-3.12.696-.77 1.608-1.164 2.74-1.164 1.311 0 2.302.5 2.962 1.498l.638 1.06.638-1.06c.66-.999 1.65-1.498 2.96-1.498 1.13 0 2.043.395 2.74 1.164.675.77 1.012 1.81 1.012 3.12z"/>
                    </svg>
                </a>
                <?php endif; ?>
                <?php if ($vt_url_youtube) : ?>
                <a href="<?php echo esc_url($vt_url_youtube); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('YouTube (opens in new tab)', 'vt-folio'); ?>">
                    <svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="16" height="16">
                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                    </svg>
                </a>
                <?php endif; ?>
                <?php if ($vt_url_github) : ?>
                <a href="<?php echo esc_url($vt_url_github); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('GitHub (opens in new tab)', 'vt-folio'); ?>">
                    <svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="16" height="16">
                        <path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/>
                    </svg>
                </a>
                <?php endif; ?>
                <?php if ($vt_url_bluesky) : ?>
                <a href="<?php echo esc_url($vt_url_bluesky); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Bluesky (opens in new tab)', 'vt-folio'); ?>">
                    <svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 568 501" fill="currentColor" width="16" height="16">
                        <path d="M123.121 33.664C188.971 82.553 258.7 181.68 284 234.873c25.3-53.192 95.028-152.32 160.879-201.21C491.866-1.611 568-28.906 568 57.947c0 17.346-9.945 145.713-15.778 166.555-20.275 72.453-94.155 90.933-159.763 79.748C507.295 323.689 528.034 395.054 468.889 462c-100.199 102.94-143.946-25.801-154.824-58.464-1.967-5.78-2.879-8.766-2.879-8.766.014 2.568.014 2.568 0 5.126.014 2.558.014 2.558 0 5.126-.879-3.062-1.867-6.07-2.88-8.766-10.878 32.663-54.625 161.404-154.824 58.464C94.966 395.054 115.705 323.689 230.641 304.25c-65.608 11.185-139.488-7.295-159.763-79.748C64.945 203.66 55 75.293 55 57.947 55-28.906 131.134-1.611 123.121 33.664z"/>
                    </svg>
                </a>
                <?php endif; ?>
                <a href="<?php echo esc_url(get_feed_link()); ?>" aria-label="<?php esc_attr_e('RSS feed', 'vt-folio'); ?>">
                    <svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="16" height="16">
                        <path d="M6.18 15.64a2.18 2.18 0 0 1 2.18 2.18C8.36 19.01 7.38 20 6.18 20C4.98 20 4 19.01 4 17.82a2.18 2.18 0 0 1 2.18-2.18M4 4.44A15.56 15.56 0 0 1 19.56 20h-2.83A12.73 12.73 0 0 0 4 7.27V4.44m0 5.66a9.9 9.9 0 0 1 9.9 9.9h-2.83A7.07 7.07 0 0 0 4 12.93V10.1z"/>
                    </svg>
                </a>
            </div>

            <p class="footer-copy">
                &copy; <?php echo esc_html(gmdate('Y')); ?> <?php echo esc_html(get_bloginfo('name')); ?>.
                <?php
                $credit = vt_get_mod('vt_footer_credit', __('All rights reserved.', 'vt-folio'));
                if ($credit) echo esc_html($credit);
                ?>
            </p>

        </div>
    </div>
</footer>

<button id="vt-back-top" aria-label="<?php esc_attr_e('Back to top', 'vt-folio'); ?>" hidden>
    <svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5"/>
    </svg>
</button>

</div><!-- .site-wrapper -->

<?php wp_footer(); ?>
</body>
</html>
