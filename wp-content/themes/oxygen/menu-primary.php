<?php
/**
 * Primary Menu Template
 *
 * Displays the Primary Menu if it has active menu items.
 *
 * @package Oxygen
 * @subpackage Template
 */

if ( has_nav_menu( 'primary' ) ) : ?>

	<?php do_atomic( 'before_menu_primary' ); // oxygen_before_menu_primary ?>

	<div id="menu-primary" class="site-navigation menu-container" role="navigation">

		<span class="menu-toggle"><?php _e( 'Menu', 'oxygen' ); ?></span>

		<?php do_atomic( 'open_menu_primary' ); // oxygen_open_menu_primary ?>

		<?php wp_nav_menu( array( 'theme_location' => 'primary', 'container_class' => 'menu', 'menu_class' => 'nav-menu', 'menu_id' => 'menu-primary-items', 'fallback_cb' => '' ) ); ?>

		<?php do_atomic( 'close_menu_primary' ); // oxygen_close_menu_primary ?>

	</div><!-- #menu-primary .menu-container -->

	<?php do_atomic( 'after_menu_primary' ); // oxygen_after_menu_primary ?>

<?php endif; ?>