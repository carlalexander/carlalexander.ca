<!DOCTYPE html>

<html class="no-js" <?php language_attributes(); ?>>

	<head>

		<meta http-equiv="content-type" content="<?php bloginfo( 'html_type' ); ?>" charset="<?php bloginfo( 'charset' ); ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" >

		<link rel="profile" href="http://gmpg.org/xfn/11">

		<?php wp_head(); ?>

		<!-- Fathom - beautiful, simple website analytics -->
		<script src="https://cdn.usefathom.com/script.js" data-site="JWKMEKLG" defer></script>
		<!-- / Fathom -->

	</head>

	<body <?php body_class(); ?>>

		<?php
		if ( function_exists( 'wp_body_open' ) ) {
			wp_body_open();
		}
		?>

		<a class="skip-link button" href="#site-content"><?php _e( 'Skip to the content', 'mcluhan' ); ?></a>

		<header class="site-header group">

            <p class="site-logo"><a href="<?php echo esc_url( home_url() ); ?>" class="site-name"><img src="<?= get_stylesheet_directory_uri().'/assets/images/logo.png' ?>" /></a></p>

			<?php if ( is_singular() ) : ?>

				<p class="site-title"><a href="<?php echo esc_url( home_url() ); ?>" class="site-name"><?php bloginfo( 'name' ); ?></a></p>

			<?php else : ?>

				<h1 class="site-title"><a href="<?php echo esc_url( home_url() ); ?>" class="site-name"><?php bloginfo( 'name' ); ?></a></h1>

			<?php endif; ?>

			<?php if ( get_bloginfo( 'description' ) ) : ?>

				<p class="site-description"><?php bloginfo( 'description' ); ?></p>

			<?php endif; ?>

			<div class="nav-toggle">
				<div class="bar"></div>
				<div class="bar"></div>
			</div>

			<div class="menu-wrapper">

				<ul class="main-menu desktop">

					<?php

					if ( has_nav_menu( 'main-menu' ) ) {

						$main_menu_args = array(
							'container' 		=> '',
							'items_wrap' 		=> '%3$s',
							'theme_location' 	=> 'main-menu',
						);

						wp_nav_menu( $main_menu_args );

					} else {

						$fallback_args = array(
							'container' => '',
							'title_li' 	=> '',
						);

						wp_list_pages( $fallback_args );
					}
					?>
				</ul>

			</div><!-- .menu-wrapper -->

			<?php if ( has_nav_menu( 'social-menu' ) || ( ! get_theme_mod( 'mcluhan_hide_social' ) || is_customize_preview() ) ) : ?>

				<div class="social-menu desktop">

					<ul class="social-menu-inner">

						<li><a href="<?php echo esc_url( home_url( '?s=' ) ); ?>"></a></li>

						<?php

						$social_args = array(
							'theme_location'	=> 'social-menu',
							'container'			=> '',
							'container_class'	=> 'menu-social group',
							'items_wrap'		=> '%3$s',
							'menu_id'			=> 'menu-social-items',
							'menu_class'		=> 'menu-items',
							'depth'				=> 1,
							'link_before'		=> '<span class="screen-reader-text">',
							'link_after'		=> '</span>',
							'fallback_cb'		=> '',
						);

						wp_nav_menu( $social_args );

						?>

					</ul><!-- .social-menu-inner -->

				</div><!-- .social-menu -->

			<?php endif; ?>

		</header><!-- header -->

		<div class="mobile-menu-wrapper">

			<ul class="main-menu mobile">
				<?php
				if ( has_nav_menu( 'main-menu' ) ) {
					wp_nav_menu( $main_menu_args );
				} else {
					wp_list_pages( $fallback_args );
				}
				?>
				<li class="toggle-mobile-search-wrapper"><a href="#" class="toggle-mobile-search"><?php _e( 'Search', 'mcluhan' ); ?></a></li>
			</ul>

			<?php if ( has_nav_menu( 'social-menu' ) && ( ! get_theme_mod( 'mcluhan_hide_social' ) || is_customize_preview() ) ) : ?>

				<div class="social-menu mobile">

					<ul class="social-menu-inner">

						<?php wp_nav_menu( $social_args ); ?>

					</ul><!-- .social-menu-inner -->

				</div><!-- .social-menu -->

			<?php endif; ?>

		</div><!-- .mobile-menu-wrapper -->

		<div class="mobile-search">

			<div class="untoggle-mobile-search"></div>

			<?php get_search_form(); ?>

			<div class="mobile-results">

				<div class="results-wrapper"></div>

			</div>

		</div><!-- .mobile-search -->

		<div class="search-overlay">

			<?php get_search_form(); ?>

		</div><!-- .search-overlay -->

        <?php if (get_current_blog_id() === 1) : ?>
            <div id="headerbar-container" class="headerbar-wrapper">
                <div class="headerbar-content">
                    <?php if (mcluhan_promote_course()) : ?>
                        <p>Want to learn object-oriented programmming? <a href="/discover-object-oriented-programming/">Get my <span class="hidden-mobile">free</span> course</a></p>
                    <?php else : ?>
                        <p>Want to get more articles like this one? <a href="/newsletter/">Join my newsletter</a></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

		<main class="site-content" id="site-content">
