<?php 
/*
Plugin Name: WordPress SEO
Version: 1.1.9
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

define( 'WPSEO_URL', plugin_dir_url(__FILE__) );
define( 'WPSEO_PATH', plugin_dir_path(__FILE__) );
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

define( 'WPSEO_VERSION', '1.1.9' );

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

function wpseo_maybe_upgrade() {
	$options = get_option( 'wpseo' );
	$current_version = isset($options['version']) ? $options['version'] : 0;

	if ( version_compare( $current_version, WPSEO_VERSION, '==' ) )
		return;

	// <= 0.3.5: flush rewrite rules for new XML sitemaps
	if ( $current_version == 0 ) {
		flush_rewrite_rules();
	}

	if ( version_compare( $current_version, '0.4.2', '<' ) ) {
		$xml_opt = array();
		// Move XML Sitemap settings from general array to XML specific array, general settings first
		foreach ( array('enablexmlsitemap', 'xml_include_images', 'xml_ping_google', 'xml_ping_bing', 'xml_ping_yahoo', 'xml_ping_ask', 'xmlnews_posttypes') as $opt ) {
			if ( isset( $options[$opt] ) ) {
				$xml_opt[$opt] = $options[$opt];
				unset( $options[$opt] );
			}
		}
		// Per post type settings
		foreach ( get_post_types() as $post_type ) {
			if ( in_array( $post_type, array('revision','nav_menu_item','attachment') ) ) 
				continue;

			if ( isset( $options['post_types-'.$post_type.'-not_in_sitemap'] ) ) {
				$xml_opt['post_types-'.$post_type.'-not_in_sitemap'] = $options['post_types-'.$post_type.'-not_in_sitemap'];
				unset( $options['post_types-'.$post_type.'-not_in_sitemap'] );
			}
		}
		// Per taxonomy settings
		foreach ( get_taxonomies() as $taxonomy ) {
			if ( in_array( $taxonomy, array('nav_menu','link_category','post_format') ) )
				continue;

			if ( isset( $options['taxonomies-'.$taxonomy.'-not_in_sitemap'] ) ) {
				$xml_opt['taxonomies-'.$taxonomy.'-not_in_sitemap'] = $options['taxonomies-'.$taxonomy.'-not_in_sitemap'];
				unset( $options['taxonomies-'.$taxonomy.'-not_in_sitemap'] );
			}
		}
		if ( get_option('wpseo_xml') === false )
			update_option( 'wpseo_xml', $xml_opt );
		unset( $xml_opt );

		// Clean up other no longer used settings
		unset( $options['wpseodir'], $options['wpseourl'] );
	}

	if ( version_compare( $current_version, '1.0.2.2', '<' ) ) {
		$opt = (array) get_option( 'wpseo_indexation' );		
		unset( $opt['hideindexrel'], $opt['hidestartrel'], $opt['hideprevnextpostlink'], $opt['hidewpgenerator'] );
		update_option( 'wpseo_indexation', $opt );
	}

	if ( version_compare( $current_version, '1.0.4', '<' ) ) {
		$opt = (array) get_option( 'wpseo_indexation' );
		$newopt = array(
			'opengraph' => $opt['opengraph'],
			'fb_pageid' => $opt['fb_pageid'],
			'fb_adminid' => $opt['fb_adminid'],
			'fb_appid' => $opt['fb_appid'],
		);
		update_option('wpseo_social', $newopt);
		unset($opt['opengraph'], $opt['fb_pageid'], $opt['fb_adminid'], $opt['fb_appid']);
		update_option('wpseo_indexation', $opt);
	}
	
	$options['version'] = WPSEO_VERSION;
	update_option( 'wpseo', $options );
}
add_action( 'admin_init', 'wpseo_maybe_upgrade' );
