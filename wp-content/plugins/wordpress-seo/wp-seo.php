<?php 
/*
Plugin Name: WordPress SEO
Version: 1.2.2
Plugin URI: http://yoast.com/wordpress/seo/#utm_source=wpadmin&utm_medium=plugin&utm_campaign=wpseoplugin
Description: The first true all-in-one SEO solution for WordPress, including on-page content analysis, XML sitemaps and much more.
Author: Joost de Valk
Author URI: http://yoast.com/
License: GPL v3

WordPress SEO Plugin
Copyright (C) 2008-2012, Joost de Valk - joost@yoast.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if ( !defined('WPSEO_URL') )
	define( 'WPSEO_URL', plugin_dir_url( __FILE__ ) );
if ( !defined('WPSEO_PATH') )
	define( 'WPSEO_PATH', plugin_dir_path( __FILE__ ) );
if ( !defined('WPSEO_BASENAME') )
	define( 'WPSEO_BASENAME', plugin_basename( __FILE__ ) );

load_plugin_textdomain( 'wordpress-seo', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

if ( version_compare(PHP_VERSION, '5.2', '<') ) {
	if ( is_admin() && (!defined('DOING_AJAX') || !DOING_AJAX) ) {
		require_once ABSPATH.'/wp-admin/includes/plugin.php';
		deactivate_plugins( __FILE__ );
	    wp_die( sprintf( __('WordPress SEO requires PHP 5.2 or higher, as does WordPress 3.2 and higher. The plugin has now disabled itself. For more info, %s$1see this post%s$2.', 'wordpress-seo'), '<a href="http://yoast.com/requires-php-52/">', '</a>') );
	} else {
		return;
	}
}

define( 'WPSEO_VERSION', '1.2.2' );

global $wp_version;

$pluginurl = plugin_dir_url( __FILE__ );
if ( preg_match( '/^https/', $pluginurl ) && !preg_match( '/^https/', get_bloginfo('url') ) )
	$pluginurl = preg_replace( '/^https/', 'http', $pluginurl );
define( 'WPSEO_FRONT_URL', $pluginurl );

require WPSEO_PATH.'inc/wpseo-functions.php';
require WPSEO_PATH.'inc/class-rewrite.php';
require WPSEO_PATH.'inc/class-sitemaps.php';

if ( !defined('DOING_AJAX') || !DOING_AJAX )
	require WPSEO_PATH.'inc/wpseo-non-ajax-functions.php';
	
$options = get_wpseo_options();

if ( is_admin() ) {
	require WPSEO_PATH.'admin/ajax.php';
	if ( !defined('DOING_AJAX') || !DOING_AJAX ) {
		require WPSEO_PATH.'admin/yst_plugin_tools.php';
		require WPSEO_PATH.'admin/class-config.php';
		require WPSEO_PATH.'admin/class-metabox.php';
		require WPSEO_PATH.'admin/class-taxonomy.php';
		if ( isset( $options['opengraph'] )  && $options['opengraph'] )
			require WPSEO_PATH.'admin/class-opengraph-admin.php';

		if ( version_compare( $wp_version, '3.2.1', '>') )
			require WPSEO_PATH.'admin/class-pointers.php';
	}
} else {
	require WPSEO_PATH.'frontend/class-frontend.php';
	if ( isset($options['breadcrumbs-enable']) && $options['breadcrumbs-enable'] )
		require WPSEO_PATH.'frontend/class-breadcrumbs.php';
	if ( isset( $options['opengraph'] )  && $options['opengraph'] )
		require WPSEO_PATH.'frontend/class-opengraph.php';
}

// Load all extra modules
if ( !defined('DOING_AJAX') || !DOING_AJAX )
	wpseo_load_plugins( WP_PLUGIN_DIR.'/wordpress-seo-modules/' );

// Let's act as though this is AIOSEO so plugins and themes that act differently for that will fix do it for this plugin as well.
if ( !class_exists('All_in_One_SEO_Pack') ) {
	class All_in_One_SEO_Pack {
		function All_in_One_SEO_Pack() {
			return true;
		}
	}
}

add_action( 'admin_init', 'wpseo_maybe_upgrade' );
register_activation_hook( __FILE__, 'wpseo_activate' );
register_deactivation_hook( __FILE__, 'wpseo_deactivate' );
