<?php 

class WPSEO_Breadcrumbs {

	function __construct() {
		$options = get_option("wpseo_internallinks");

		if (isset($options['trytheme']) && $options['trytheme']) {
			// Thesis
			add_action('thesis_hook_before_headline', array(&$this, 'breadcrumb_output'),10,1);

			// Hybrid
			remove_action( 'hybrid_before_content', 'hybrid_breadcrumb' );
			add_action( 'hybrid_before_content', array(&$this, 'breadcrumb_output'), 10, 1 );

			// Thematic
			add_action('thematic_belowheader', array(&$this, 'breadcrumb_output'),10,1);
						
			add_action('framework_hook_content_open', array(&$this, 'breadcrumb_output'),10,1);			
		}

		// If breadcrumbs are active (which they are otherwise this class wouldn't be instantiated), there's no reason
		// to have bbPress breadcrumbs as well.
		add_filter( 'bbp_get_breadcrumb', '__return_false' );
	}

	function breadcrumb_output() {
		$this->breadcrumb('<div id="wpseobreadcrumb">','</div>');
		return;
	}

	function bold_or_not($input) {
		$opt = get_option("wpseo_internallinks");
		if ( isset($opt['breadcrumbs-boldlast']) && $opt['breadcrumbs-boldlast'] ) {
			return '<strong>'.$input.'</strong>';
		} else {
			return $input;
		}
	}		
	
	function get_bc_title( $id_or_name, $type = 'post_type' ) {
		$bctitle = wpseo_get_value( 'bctitle', $id_or_name );
		return ( !empty($bctitle) ) ? $bctitle : strip_tags( get_the_title( $id_or_name ) );
	}
	
	function get_term_parents($term, $taxonomy) {
		$origterm = $term;
		$parents = array();
		while ($term->parent != 0) {
			$term = get_term($term->parent, $taxonomy);
			if ($term != $origterm)
				$parents[] = $term;
		}
		return $parents;
	}

	function get_menu_trail($menu = '', $parent = false, $trail = array()) {
		global $post, $wpdb;
		// No parent is set so we start by getting the parent of the current post
		if ( !$parent ) {
			$query = "SELECT meta_value FROM $wpdb->postmeta WHERE meta_key='_menu_item_menu_item_parent' AND post_id = (SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_menu_item_object_id' AND meta_value=$post->ID)";
			$result = $wpdb->get_results( $query );
			if( count($result) > 0 ) {
				$parent = $result[0]->meta_value;
			}
		// A parent is set and we want to get the grandparent.
		} else {
			$query = "SELECT meta_value FROM $wpdb->postmeta WHERE meta_key='_menu_item_menu_item_parent' AND post_id = $parent";
			$result = $wpdb->get_results( $query );
			if( count($result) > 0 ) {
				$parent = $result[0]->meta_value;
			} else {
				$parent = 0;
			}
		}
		// The parent is the root of the menu let's return the trail
		if ( $parent == 0) {
			return $trail;
		// There still are grandparents to discover
		} else {
			$trail[] = $parent;
			$temptrail = $this->get_menu_trail('', $parent, $trail);
			return $temptrail;
		}
	}

	function in_menu( $selectedmenu ) {
		global $wpdb, $post;
		$query = "SELECT * FROM $wpdb->term_relationships WHERE term_taxonomy_id = $selectedmenu AND object_id IN (SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_menu_item_object_id' AND meta_value=$post->ID)";
		$result = $wpdb->get_results( $query );
		if( count($result) > 0 ) {
			return true;
		} else {
			return false;
		}
	}

	function get_post_for_menunode( $node_id ) {
		global $wpdb;
		$query = "SELECT meta_value FROM $wpdb->postmeta WHERE meta_key='_menu_item_object_id' AND post_id = $node_id";
		$result = $wpdb->get_results( $query );
		if( count($result) > 0 ) {
			return $result[0]->meta_value;
		} else {
			return 0;
		}
	}
	
	function breadcrumb($prefix = '', $suffix = '', $display = true) {
		$options = get_wpseo_options();

		global $wp_query, $post, $paged;

		$opt 		= get_option("wpseo_internallinks");
		$on_front 	= get_option('show_on_front');
		$blog_page 	= get_option('page_for_posts');
		$sep		= ( isset($opt['breadcrumbs-sep']) && $opt['breadcrumbs-sep'] != '' ) ? $opt['breadcrumbs-sep'] : '&raquo;';
		$home		= ( isset($opt['breadcrumbs-home']) && $opt['breadcrumbs-home'] != '' ) ? $opt['breadcrumbs-home'] : __('Home');
		$selmenu	= ( isset($opt['breadcrumbs-selectedmenu']) && $opt['breadcrumbs-selectedmenu'] != '' ) ? $opt['breadcrumbs-selectedmenu'] : 0;
		
		if ( "page" == $on_front && 'post' == get_post_type() ) {
			$homelink = '<a href="'.get_permalink(get_option('page_on_front')).'">'.$home.'</a>';
			$bloglink = $homelink;
			if ( $blog_page && ( !isset($opt['breadcrumbs-blog-remove']) || !$opt['breadcrumbs-blog-remove'] ) )
				$bloglink = $homelink.' '.$sep.' <a href="'.get_permalink($blog_page).'">'.$this->get_bc_title($blog_page).'</a>';
		} else {
			$homelink = '<a href="'.get_bloginfo('url').'">'.$home.'</a>';
			$bloglink = $homelink;
		}

 		if ( ( $on_front == "page" && is_front_page() ) || ( $on_front == "posts" && is_home() ) ) {
			$output = $this->bold_or_not($home);
		} else if ( $on_front == "page" && is_home() ) {
			$output = $homelink.' '.$sep.' '.$this->bold_or_not( $this->get_bc_title($blog_page) );
		} else if ( is_singular() ) {
			$output = $bloglink.' '.$sep.' ';

			if( isset($opt['breadcrumbs-menus']) && $opt['breadcrumbs-menus'] = 'on'){
				$use_menu = $this->in_menu( $selmenu );
			}
			if ( function_exists('bbp_body_class') && count( bbp_body_class( array() ) ) > 1 ) {
				remove_filter('bbp_get_breadcrumb','__return_false');
				$output .= bbp_get_breadcrumb( ' '.$sep.' ' );
				add_filter('bbp_get_breadcrumb','__return_false');
			} else if( isset( $use_menu ) && $use_menu ){
				$trail = $this->get_menu_trail();
				$trail = array_reverse ( $trail );
				$trailposts = array();
				for($t = 0; $t < count($trail); $t++){
					$trailposts[] = $this->get_post_for_menunode($trail[$t]);
				}
				for($t = 0; $t < count($trail); $t++){
					$bctitle = ( get_the_title( $trail[$t] ) == '' ) ? get_the_title( $trailposts[$t] ) : get_the_title( $trail[$t] );
					$output .= '<a href="' . get_permalink( $trailposts[$t] ) . '">' . $bctitle .'</a> ' . $sep . ' ';
				}
				$output .= $this->bold_or_not( $this->get_bc_title( $post->ID ) );
			} else {
				
				$post_type = get_post_type();
				if ( function_exists('get_post_type_archive_link') && get_post_type_archive_link( $post_type ) ) {
					if ( isset($options['bctitle-ptarchive-'.$post_type]) && '' != $options['bctitle-ptarchive-'.$post_type] ) {
						$archive_title = $options['bctitle-ptarchive-'.$post_type];
					} else {
						$post_type_obj = get_post_type_object( $post_type );
						$archive_title = $post_type_obj->labels->menu_name;
					}
					$output .= '<a href="'.get_post_type_archive_link( $post_type ).'">'.$archive_title.'</a> ' . $sep . ' ';
				}

				if ( 0 == $post->post_parent ) {
					if ( isset( $opt['post_types-'.$post->post_type.'-maintax'] ) && $opt['post_types-'.$post->post_type.'-maintax'] != '0' ) {
						$main_tax = $opt['post_types-'.$post->post_type.'-maintax'];
						$terms = wp_get_object_terms( $post->ID, $main_tax );
						if ( is_taxonomy_hierarchical($main_tax) && $terms[0]->parent != 0 ) {
							$parents = $this->get_term_parents($terms[0], $main_tax);
							$parents = array_reverse($parents);
							foreach($parents as $parent) {
								$bctitle = wpseo_get_term_meta( $parent, $main_tax, 'bctitle' );
								if (!$bctitle)
									$bctitle = $parent->name;
								$output .= '<a href="'.get_term_link( $parent, $main_tax ).'">'.$bctitle.'</a> '.$sep.' ';
							}
						}
						if ( count($terms) > 0 ) {
							$bctitle = wpseo_get_term_meta( $terms[0], $main_tax, 'bctitle' );
							if (!$bctitle)
								$bctitle = $terms[0]->name;
								$output .= '<a href="'.get_term_link($terms[0], $main_tax).'">'.$bctitle.'</a> '.$sep.' ';
						}
					}
					$output .= $this->bold_or_not( $this->get_bc_title( $post->ID ) );
				} else {
					if (isset($post->ancestors)) {
						if (is_array($post->ancestors))
							$ancestors = array_values($post->ancestors);
						else 
							$ancestors = array($post->ancestors);				
					} else {
						$ancestors = array($post->post_parent);
					}

					// Reverse the order so it's oldest to newest
					$ancestors = array_reverse($ancestors);

					foreach ( $ancestors as $ancestor ) {
						$output .= '<a href="'.get_permalink($ancestor).'">'.$this->get_bc_title( $ancestor ).'</a> '.$sep.' ';
					}

					$output .= $this->bold_or_not( $this->get_bc_title( $post->ID ) );
				}					
			}
		} else {
			if (! is_404() ) {
				$output = $bloglink.' '.$sep.' ';
			} else {
				$output = $homelink.' '.$sep.' ';
			}
			
			// echo '<pre>'.print_r($wp_query,1).'</pre>';
			
			if ( function_exists('is_post_type_archive') && is_post_type_archive() ) {
				$post_type = get_post_type();
				if ( isset($options['bctitle-ptarchive-'.$post_type]) && '' != $options['bctitle-ptarchive-'.$post_type] ) {
					$archive_title = $options['bctitle-ptarchive-'.$post_type];
				} else {
					$post_type_obj = get_post_type_object( $post_type );
					$archive_title = $post_type_obj->labels->menu_name;
				}
				$output .= $this->bold_or_not( $archive_title );
			} else if ( is_tax() || is_tag() || is_category() ) {
				$term = $wp_query->get_queried_object();

				if ( isset($options['taxonomy-'.$term->taxonomy.'-ptparent']) && $options['taxonomy-'.$term->taxonomy.'-ptparent'] != '' ) {
					$post_type = $options['taxonomy-'.$term->taxonomy.'-ptparent'];
					if ( 'post' == $post_type && get_option('show_on_front') == 'page' ) {
						$posts_page = get_option('page_for_posts');
						if ( $posts_page ) {
							$output .= '<a href="'.get_permalink( $posts_page ).'">'.$this->get_bc_title( $posts_page ).'</a> '.$sep.' ';
						}
					} else {
						if ( isset($options['bctitle-ptarchive-'.$post_type]) && '' != $options['bctitle-ptarchive-'.$post_type] ) {
							$archive_title = $options['bctitle-ptarchive-'.$post_type];
						} else {
							$post_type_obj = get_post_type_object( $post_type );
							$archive_title = $post_type_obj->labels->menu_name;
						}
						$output .= '<a href="'.get_post_type_archive_link( $post_type ).'">'.$archive_title.'</a> '.$sep.' ';
					}
				}
					
				if ( is_taxonomy_hierarchical($term->taxonomy) && $term->parent != 0 ) {
					$parents = $this->get_term_parents($term, $term->taxonomy);
					$parents = array_reverse( $parents );
					
					foreach($parents as $parent) {
						$bctitle = wpseo_get_term_meta( $parent, $term->taxonomy, 'bctitle' );
						if (!$bctitle)
							$bctitle = $parent->name;
						$output .= '<a href="'.get_term_link( $parent, $term->taxonomy ).'">'.$bctitle.'</a> '.$sep.' ';
					}
				}

				$bctitle = wpseo_get_term_meta( $term, $term->taxonomy, 'bctitle' );
				if (!$bctitle)
					$bctitle = $term->name;
				
				if ($paged)
					$output .= $this->bold_or_not('<a href="'.get_term_link( $term, $term->taxonomy ).'">'.$bctitle.'</a>');
				else
					$output .= $this->bold_or_not($bctitle);
			} else if ( is_date() ) { 
				if ( isset($opt['breadcrumbs-archiveprefix']) )
					$bc = $opt['breadcrumbs-archiveprefix'];
				else
					$bc = __('Archives for');
				if ( is_day() ) {
					global $wp_locale;
					$output .= '<a href="'.get_month_link( get_query_var('year'), get_query_var('monthnum') ).'">'.$wp_locale->get_month( get_query_var('monthnum') ).' '.get_query_var('year').'</a> '.$sep.' ';
					$output .= $this->bold_or_not( $bc." ".get_the_date() );
				} else if ( is_month() ) {
					$output .= $this->bold_or_not( $bc." ".single_month_title(' ',false) );
				} else if ( is_year() ) {
					$output .= $this->bold_or_not( $bc." ".get_query_var('year') );
				}
			} elseif ( is_author() ) {
				if ( isset($opt['breadcrumbs-archiveprefix']) )
					$bc = $opt['breadcrumbs-archiveprefix'];
				else
					$bc = __('Archives for');
				$user = $wp_query->get_queried_object();
				$output .= $this->bold_or_not($bc." ".$user->display_name);
			} elseif ( is_search() ) {
				if ( isset($opt['breadcrumbs-searchprefix']) && $opt['breadcrumbs-searchprefix'] != '' )
					$bc = $opt['breadcrumbs-searchprefix'];
				else
					$bc = __('You searched for');
				$output .= $this->bold_or_not($bc.' "'.stripslashes(strip_tags(get_search_query())).'"');
			} elseif ( isset( $wp_query->query_vars['bbp_topic_tag'] ) ) {
				remove_filter('bbp_get_breadcrumb','__return_false');
				$output .= bbp_get_breadcrumb( ' '.$sep.' ' );
				add_filter('bbp_get_breadcrumb','__return_false');
			} elseif ( is_404() ) {
				if ( isset($opt['breadcrumbs-404crumb']) && $opt['breadcrumbs-404crumb'] != '' )
					$crumb404 = $opt['breadcrumbs-404crumb'];
				else
					$crumb404 = __('Error 404: Page not found');
				$output .= $this->bold_or_not($crumb404);
			}
		}
		
		if ( isset($opt['breadcrumbs-prefix']) && $opt['breadcrumbs-prefix'] != "" ) {
			$output = $opt['breadcrumbs-prefix']." ".$output;
		}
		if ($display) {
			echo $prefix.$output.$suffix;
			return true;
		} else {
			return $prefix.$output.$suffix;
		}
	}
} 

if (!function_exists('yoast_breadcrumb')) {
	function yoast_breadcrumb($prefix = '', $suffix = '', $display = true) {
		$wpseo_bc = new WPSEO_Breadcrumbs();
		return $wpseo_bc->breadcrumb($prefix, $suffix, $display);
	}	
}
