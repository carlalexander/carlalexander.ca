<?php

class WPSEO_Rewrite {

	function WPSEO_Rewrite() {
		$options = get_wpseo_options();
		
		add_filter('query_vars', array(&$this, 'query_vars') );

		if ( isset( $options['stripcategorybase']) && $options['stripcategorybase'] ) {
			add_filter( 'category_link', array(&$this, 'no_category_base'), 1000, 2 );
			add_filter( 'request', array(&$this, 'no_category_base_request') );
			add_filter( 'category_rewrite_rules', array(&$this, 'category_rewrite_rules') );
			
			add_action('created_category', array(&$this, 'flush_rules') );
			add_action('edited_category', array(&$this, 'flush_rules') );
			add_action('delete_category', array(&$this, 'flush_rules') );
		}
	}
	
	// FIXME: could use flush_rewrite_rules() instead.
	function flush_rules() {
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	}
	
	function no_category_base($catlink, $category_id) {
		$category = &get_category( $category_id );
		if ( is_wp_error( $category ) )
			return $category;
		$category_nicename = $category->slug;

		if ( $category->parent == $category_id ) // recursive recursion
			$category->parent = 0;
		elseif ($category->parent != 0 )
			$category_nicename = get_category_parents( $category->parent, false, '/', true ) . $category_nicename;

		$blog_prefix = '';
		if ( function_exists('is_multisite') && is_multisite() && !is_subdomain_install() && is_main_site() )
			$blog_prefix = 'blog/';

		$catlink = trailingslashit(get_option( 'home' )) . $blog_prefix . user_trailingslashit( $category_nicename, 'category' );
		return $catlink;
	}
		
	function query_vars( $query_vars ) {
		$options = get_wpseo_options();
		
		if ( isset($options['stripcategorybase']) && $options['stripcategorybase'] ) {
			$query_vars[] = 'wpseo_category_redirect';
		}
		
		return $query_vars;
	}
		
	function no_category_base_request( $query_vars ) {
		if( isset($query_vars['wpseo_category_redirect']) ) {
			$catlink = trailingslashit( get_option( 'home' ) ) . user_trailingslashit( $query_vars['wpseo_category_redirect'], 'category' );
			wp_redirect($catlink, 301);
			exit;
		}
		return $query_vars;
	}
	
	/**
	 * This function taken and only slightly adapted from WP No Category Base plugin by Saurabh Gupta
	 */
	function category_rewrite_rules( $rewrite ) {
		global $wp_rewrite;

		$category_rewrite = array();
		$categories = get_categories(array('hide_empty'=>false));

		$blog_prefix = '';
		if ( function_exists('is_multisite') && is_multisite() && !is_subdomain_install() && is_main_site() )
			$blog_prefix = 'blog/';

		foreach($categories as $category) {
			$category_nicename = $category->slug;
			if ( $category->parent == $category->cat_ID ) // recursive recursion
				$category->parent = 0;
			elseif ($category->parent != 0 )
				$category_nicename = get_category_parents( $category->parent, false, '/', true ) . $category_nicename;
			$category_rewrite[$blog_prefix.'('.$category_nicename.')/(?:feed/)?(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?category_name=$matches[1]&feed=$matches[2]';
			$category_rewrite[$blog_prefix.'('.$category_nicename.')/page/?([0-9]{1,})/?$'] = 'index.php?category_name=$matches[1]&paged=$matches[2]';
			$category_rewrite[$blog_prefix.'('.$category_nicename.')/?$'] = 'index.php?category_name=$matches[1]';
		}

		// Redirect support from Old Category Base
		$old_base = $wp_rewrite->get_category_permastruct();
		$old_base = str_replace( '%category%', '(.+)', $old_base );
		$old_base = trim($old_base, '/');
		$category_rewrite[$old_base.'$'] = 'index.php?wpseo_category_redirect=$matches[1]';

		return $category_rewrite;
	}		
}

$wpseo_rewrite = new WPSEO_Rewrite();
