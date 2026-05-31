<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="https://gmpg.org/xfn/11">
<!-- Prevent flash of wrong theme -->
<script>
(function(){
    try {
        var t = localStorage.getItem('vt-theme') ||
                (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        document.documentElement.setAttribute('data-theme', t);
    } catch(e) {}
})();
</script>
<link rel="me" href="https://mastodon.online/@vinuthomas">
<?php
/*
 * Preload the above-fold logo image so the browser fetches it at high
 * priority during the initial HTML parse, before the stylesheet is applied.
 * Only the light-mode logo is preloaded because it is visible on first paint
 * for the majority of visitors; dark-mode is toggled client-side at runtime.
 */
$logo_url = esc_url( get_template_directory_uri() . '/assets/images/logo-dark.png' );
echo '<link rel="preload" as="image" href="' . $logo_url . '">' . "\n";
?>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main"><?php esc_html_e('Skip to content', 'vt-folio'); ?></a>

<div class="reading-progress" id="reading-progress" aria-hidden="true"></div>

<div class="site-wrapper">

<header class="site-header" role="banner">
    <div class="container">
        <div class="site-header__inner">

            <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo" rel="home">
                <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/logo-dark.png'); ?>"
                     alt="<?php echo esc_attr( get_bloginfo('name') ); ?>"
                     class="site-logo__img site-logo__img--light"
                     height="48" width="130">
                <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/logo-light.png'); ?>"
                     alt=""
                     class="site-logo__img site-logo__img--dark"
                     height="48" width="130">
            </a>

            <nav class="site-nav" role="navigation" aria-label="<?php esc_attr_e('Primary navigation', 'vt-folio'); ?>">
                <?php
                wp_nav_menu([
                    'theme_location' => 'primary',
                    'menu_class'     => 'nav-menu',
                    'menu_id'        => 'primary-menu',
                    'container'      => false,
                    'depth'          => 1,
                    'fallback_cb'    => false,
                ]);
                ?>
            </nav>

            <div class="header-actions">
                <button class="header-btn" id="search-toggle"
                        aria-expanded="false" aria-controls="search-overlay"
                        aria-label="<?php esc_attr_e('Open search', 'vt-folio'); ?>">
                    <svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                </button>

                <button class="header-btn" id="theme-toggle"
                        aria-label="<?php esc_attr_e('Switch to dark mode', 'vt-folio'); ?>"
                        aria-pressed="false">
                    <svg class="icon-sun" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
                    </svg>
                    <svg class="icon-moon" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/>
                    </svg>
                </button>

                <button class="menu-toggle header-btn" id="menu-toggle"
                        aria-expanded="false" aria-controls="primary-menu"
                        aria-label="<?php esc_attr_e('Open menu', 'vt-folio'); ?>">
                    <span aria-hidden="true"></span><span aria-hidden="true"></span><span aria-hidden="true"></span>
                </button>
            </div>

        </div>
    </div>

    <div class="search-overlay" id="search-overlay" aria-hidden="true" aria-label="<?php esc_attr_e('Site search', 'vt-folio'); ?>">
        <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="search-overlay__form">
            <label for="search-overlay-input" class="screen-reader-text"><?php esc_html_e('Search posts', 'vt-folio'); ?></label>
            <input type="search" name="s" id="search-overlay-input" class="search-overlay__input"
                   placeholder="<?php esc_attr_e('Search posts…', 'vt-folio'); ?>"
                   value="<?php echo esc_attr(get_search_query()); ?>"
                   autocomplete="off">
            <button type="submit" class="search-overlay__btn" aria-label="<?php esc_attr_e('Submit search', 'vt-folio'); ?>">
                <svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                </svg>
            </button>
            <button type="button" class="search-overlay__btn" id="search-close" aria-label="<?php esc_attr_e('Close search', 'vt-folio'); ?>">
                <svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </form>
    </div>
</header>

<main class="site-main" id="main" role="main">
