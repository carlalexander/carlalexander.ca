<?php 

class WPSEO_Frontend {

	function __construct() {
		
		$options = get_wpseo_options();

		add_action( 'wp_head', array(&$this, 'head'), 1, 1 );
		remove_action( 'wp_head', 'rel_canonical' );

		add_filter( 'wp_title', array(&$this, 'title'), 10, 3 );
		add_filter( 'thematic_doctitle', array(&$this, 'force_wp_title') );
		add_filter( 'headway_title', array(&$this, 'force_wp_title') );
		
		add_action( 'wp',array(&$this,'page_redirect'), 99, 1 );

		add_action( 'login_head', array(&$this, 'noindex_page') );
		add_action( 'admin_head', array(&$this, 'noindex_page') );

		add_action( 'rss_head', array(&$this, 'noindex_feed') );
		add_action( 'rss2_head', array(&$this, 'noindex_feed') );
		add_action( 'commentsrss2_head', array(&$this, 'noindex_feed') );

		add_filter( 'loginout',array(&$this,'nofollow_link'));
		add_filter( 'register',array(&$this,'nofollow_link'));
		add_filter( 'comments_popup_link_attributes', array( &$this, 'echo_nofollow' ) );

		if ( isset($options['hidersdlink']) && $options['hidersdlink'] )
			remove_action('wp_head', 'rsd_link');

		if ( isset($options['hidewlwmanifest']) && $options['hidewlwmanifest'] )
			remove_action('wp_head', 'wlwmanifest_link');

		remove_action('wp_head', 'index_rel_link');
		remove_action('wp_head', 'start_post_rel_link');
		remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');

		if ( isset($options['hideshortlink']) && $options['hideshortlink'] )
			remove_action('wp_head', 'wp_shortlink_wp_head');
		if ( isset($options['hidefeedlinks']) && $options['hidefeedlinks'] ) {
			// TODO: add option to display just normal feed and hide comment feed.
			remove_action('wp_head', 'feed_links', 2);
			remove_action('wp_head', 'feed_links_extra', 3);
		}
		
		if ( ( isset($options['disabledate']) && $options['disabledate'] ) || 
			 ( isset($options['disableauthor']) && $options['disableauthor'] ) ||
			 ( isset($options['disablepostformats']) && $options['disablepostformats'] ) )
			add_action('wp', array(&$this, 'archive_redirect') );

		if (isset($options['redirectattachment']) && $options['redirectattachment'])
			add_action('template_redirect', array(&$this,'attachment_redirect'),1);


		if (isset($options['trailingslash']) && $options['trailingslash'])
			add_filter('user_trailingslashit', array(&$this, 'add_trailingslash') , 10, 2);

		if (isset($options['cleanpermalinks']) && $options['cleanpermalinks'])
			add_action('template_redirect',array(&$this,'clean_permalink'),1);	

		add_filter('the_content_feed', array(&$this, 'embed_rssfooter') );
		add_filter('the_excerpt_rss', array(&$this, 'embed_rssfooter_excerpt') );	
		
		if (isset($options['forcerewritetitle']) && $options['forcerewritetitle']) {
			add_action('get_header', array(&$this, 'force_rewrite_output_buffer') );
			add_action('wp_footer', array(&$this, 'flush_cache') );			
		}
	}

	function is_home_posts_page() {
		return ( is_home() && 'page' != get_option('show_on_front') );
	}
	
	function is_home_static_page() {
		return ( is_front_page() && 'page' == get_option('show_on_front') && is_page( get_option('page_on_front') ) );
	}
	
	function is_posts_page() {
		return ( is_home() && 'page' == get_option('show_on_front') );
	}
	
	function title( $title, $sep = '-', $seplocation = '', $postid = '' ) {
		if ( trim($sep) == '' )
			$sep = '-';
		$sep = ' '.$sep.' ';
		
		global $wp_query;

		if ( is_feed() )
			return $title;
			
		$options = get_wpseo_options();

		if ( $this->is_home_static_page() ) {
			global $post;
			$title = wpseo_get_value( 'title', $post->ID );
			if ( '' == $title )
				$title = wpseo_replace_vars($options['title-'.$post->post_type], (array) $post );
		} else if ( $this->is_home_posts_page() ) {
			if ( isset($options['title-home']) && $options['title-home'] != '' )
				$title = wpseo_replace_vars( $options['title-home'], array() );
			else {
				$title = get_bloginfo('name');
				if ( $wp_query->query_vars['paged'] )
					$title .= $sep.$wp_query->query_vars['paged'].'/'.$wp_query->max_num_pages;
				$title .= $sep.get_bloginfo('description');
			}
		} else if ( $this->is_posts_page() ) {
			$blogpage = get_post( get_option( 'page_for_posts' ) );
			$fixed_title = wpseo_get_value( 'title', $blogpage->ID );
			if ( $fixed_title ) { 
				$title = wpseo_replace_vars( $fixed_title, (array) $blogpage ); 
			} else {
				if (isset($options['title-'.$blogpage->post_type]) && !empty($options['title-'.$blogpage->post_type]) )
					$title = wpseo_replace_vars($options['title-'.$blogpage->post_type], (array) $blogpage );
				else {
					$title = get_bloginfo('name');
					if ( $wp_query->query_vars['paged'] )
						$title .= $sep.$wp_query->query_vars['paged'].'/'.$wp_query->max_num_pages;
					$title .= $sep.get_bloginfo('description');
				}
			}
		} else if ( is_singular() ) {
			global $post;
			if ( empty($post) ) {
				$post = $wp_query->get_queried_object();
			}
			$fixed_title = wpseo_get_value('title');
			if ( $fixed_title ) { 
				$title = $fixed_title; 
			} else {
				if (isset($options['title-'.$post->post_type]) && !empty($options['title-'.$post->post_type]) ) {
					$title = wpseo_replace_vars($options['title-'.$post->post_type], (array) $post );
				} else {
					$title = apply_filters('single_post_title', $title);
					$title = $title.$sep.get_bloginfo('name'); 
				}
			}
		} else if ( is_search() ) {
			if ( isset($options['title-search']) && !empty($options['title-search']) )
				$title = wpseo_replace_vars($options['title-search'], (array) $wp_query->get_queried_object() );	
			else {
				$title = __('Search for "').get_search_query().'"';
				
				if ( isset($wp_query->query_vars['paged']) && $wp_query->query_vars['paged'] )
					$title .= $sep.$wp_query->query_vars['paged'].'/'.$wp_query->max_num_pages;
				$title .= $sep.get_bloginfo('name'); 	
			}
		} else if ( is_category() || is_tag() || is_tax() ) {
			$term = $wp_query->get_queried_object();
			$title = trim( wpseo_get_term_meta( $term, $term->taxonomy, 'title' ) );
			if ( !$title || empty($title) ) {
				if ( isset($options['title-'.$term->taxonomy]) && !empty($options['title-'.$term->taxonomy]) ) {
					$title = wpseo_replace_vars($options['title-'.$term->taxonomy], (array) $term );
				} else {
					if ( is_category() )
						$title = single_cat_title('', false);
					else if ( is_tag() )
						$title = single_tag_title('', false);
					else if ( is_tax() ) {
						if ( function_exists('single_term_title') ) {
							$title = single_term_title('', false);
						} else {
							$term = $wp_query->get_queried_object();
							$title = $term->name;
						}
					} 

					if ( isset($wp_query->query_vars['paged']) && $wp_query->query_vars['paged'] )
						$title .= $sep.$wp_query->query_vars['paged'].'/'.$wp_query->max_num_pages;
					$title .= $sep.get_bloginfo('name'); 
				}
			}
		} else if ( is_author() ) {
			$author_id = get_query_var('author');
			$title = get_the_author_meta('wpseo_title', $author_id);
			if ( empty($title) ) {
				if ( isset($options['title-author']) && !empty($options['title-author']) )
					$title = wpseo_replace_vars($options['title-author'], array() );
				else {
					$title = get_the_author_meta('display_name', $author_id); 
					
					if ( isset($wp_query->query_vars['paged']) && $wp_query->query_vars['paged'] )
						$title .= $sep.$wp_query->query_vars['paged'].'/'.$wp_query->max_num_pages;
					$title .= $sep.get_bloginfo('name'); 		
				}
			}
		} else if ( function_exists('is_post_type_archive') && is_post_type_archive() ) {
			$post_type = get_post_type();
			if ( isset($options['title-ptarchive-'.$post_type]) && '' != $options['title-ptarchive-'.$post_type] ) {
				$title = $options['title-ptarchive-'.$post_type];
			} else {
				$post_type_obj = get_post_type_object( $post_type );
				$title = $post_type_obj->labels->menu_name.$sep.get_bloginfo('name');
			}
		} else if ( is_archive() ) {
		 	if ( isset($options['title-archive']) && !empty($options['title-archive']) )
				$title = wpseo_replace_vars($options['title-archive'], array('post_title' => $title) );
			else {
				if ( is_month() )
					$title = single_month_title(' ', false).' '.__('Archives'); 
				else if ( is_year() )
					$title = get_query_var('year').' '.__('Archives'); 
					
				if ( isset($wp_query->query_vars['paged']) && $wp_query->query_vars['paged'] )
					$title .= $sep.$wp_query->query_vars['paged'].'/'.$wp_query->max_num_pages;
				$title .= $sep.get_bloginfo('name');
			}
		} else if ( is_404() ) {
		 	if ( isset($options['title-404']) && !empty($options['title-404']) )
				$title = wpseo_replace_vars($options['title-404'], array('post_title' => $title) );
			else
				$title = __('Page not found').$sep.get_bloginfo('name');
		} 
		return esc_html( strip_tags( stripslashes( apply_filters( 'wpseo_title', $title ) ) ) );
	}
	
	function force_wp_title() {
		wp_reset_query();
		return wp_title('', 0);
	}
	
	function fix_generator($generator) {
		return preg_replace('/\s?'.get_bloginfo('version').'/','',$generator);
	}
	
	function promo() {
		echo "\n<!-- This site is optimized with the Yoast WordPress SEO plugin v".WPSEO_VERSION." - http://yoast.com/wordpress/seo/ -->\n";
	}
	
	function head() {
		$options = get_wpseo_options();

		global $wp_query;
		
		$this->promo();
		$this->metadesc();
		$this->metakeywords();
		$this->canonical();
		$this->adjacent_rel_links();
		$this->robots();
		
		if ( is_front_page() ) {
			if (!empty($options['googleverify'])) {
				$google_meta = $options['googleverify'];
				if ( strpos($google_meta, 'content') ) {
					preg_match('/content="([^"]+)"/', $google_meta, $match);
					$google_meta = $match[1];
				}
				echo "<meta name=\"google-site-verification\" content=\"$google_meta\" />\n";
			}
				
			if (!empty($options['msverify'])) {
				$bing_meta = $options['msverify'];
				if ( strpos($bing_meta, 'content') ) {
					preg_match('/content="([^"]+)"/', $bing_meta, $match);
					$bing_meta = $match[1];
				}								
				echo "<meta name=\"msvalidate.01\" content=\"$bing_meta\" />\n";
			}
			
			if (!empty($options['alexaverify'])) {
				echo "<meta name=\"alexaVerifyID\" content=\"".esc_attr($options['alexaverify'])."\" />\n";
			}	
		}

		do_action( 'wpseo_head' );
		
		echo "<!-- / Yoast WordPress SEO plugin. -->\n\n";
	}

	function robots() {
		global $wp_query;
		
		$options = get_wpseo_options();
		
		$robots 			= array();
		$robots['index'] 	= 'index';
		$robots['follow'] 	= 'follow';
		$robots['other'] 	= array();
		
		if ( is_singular() ) {
			if ( wpseo_get_value('meta-robots-noindex') )
				$robots['index'] = 'noindex';
			if ( wpseo_get_value('meta-robots-nofollow') )
				$robots['follow'] = 'nofollow';
			if ( wpseo_get_value('meta-robots-adv') && wpseo_get_value('meta-robots-adv') != 'none' ) { 
				foreach ( explode( ',', wpseo_get_value('meta-robots-adv') ) as $r ) {
					$robots['other'][] = $r;
				}
			}
		} else {
			if ( is_search() ) {
				$robots['index']  = 'noindex';
				$robots['follow'] = 'follow';
			} else if ( is_tax() || is_tag() || is_category() ) {
				$term = $wp_query->get_queried_object();
				if ( ( is_category() 	&& isset( $options['noindexcat'] ) && $options['noindexcat'] ) || 
					 ( is_tag() 		&& isset( $options['noindextag'] ) && $options['noindextag']) ||
					 ( is_tax('post_format') && isset( $options['noindexpostformat'] ) && $options['noindexpostformat'] ) ) {
					$robots['index'] = 'noindex';
				} else {
					if ( wpseo_get_term_meta( $term, $term->taxonomy, 'noindex' ) )
						$robots['index'] = 'noindex';					
				}
				if ( wpseo_get_term_meta( $term, $term->taxonomy, 'nofollow' ) )
					$robots['follow'] = 'nofollow';
			} else if ( 
				(is_author() 	&& isset($options['noindexauthor']) && $options['noindexauthor']) || 
				(is_date() 		&& isset($options['noindexdate']) && $options['noindexdate']) || 
				(is_home() 		&& get_query_var('paged') > 1) )
			{
				$robots['index']  = 'noindex';
				$robots['follow'] = 'follow';
			}

			if ( $wp_query->query_vars['paged'] && $wp_query->query_vars['paged'] > 1 && isset($options['noindexsubpages']) && $options['noindexsubpages'] ) {
				$robots['index']  = 'noindex';
				$robots['follow'] = 'follow';
			}
		}
		
		foreach ( array('noodp','noydir') as $robot ) {
			if ( isset($options[$robot]) && $options[$robot] ) {
				$robots['other'][] = $robot;
			}
		}

		$robotsstr = $robots['index'].','.$robots['follow'];

		$robots['other'] = array_unique( $robots['other'] );
		foreach ($robots['other'] as $robot) {
			$robotsstr .= ','.$robot;
		}

		$robotsstr = preg_replace( '/^index,follow,?/', '', $robotsstr );
		
		if ($robotsstr != '') {
			echo '<meta name="robots" content="'.$robotsstr.'"/>'."\n";
		}
	}
	
	function canonical( $echo = true, $unpaged = false ) {
		$options = get_wpseo_options();
		
		$canonical = false;
		
		// Set decent canonicals for homepage, singulars and taxonomy pages
		if ( is_singular() ) {
			if ( wpseo_get_value('canonical') && wpseo_get_value('canonical') != '' ) { 
				$canonical = wpseo_get_value('canonical');
			} else {
				$canonical = get_permalink( get_queried_object() );
				// Fix paginated pages
				if ( get_query_var('page') > 1 ) {
					global $wp_rewrite;
					if ( !$wp_rewrite->using_permalinks() ) {
						$link = add_query_arg( 'page', get_query_var('page'), $link );
					} else {
						$link = user_trailingslashit( trailingslashit( $link ) . get_query_var( 'page' ) );
					}
				}
			}
		} else {
			if ( is_search() ) {
				$canonical = get_search_link();
			} else if ( is_front_page() ) {
				$canonical = home_url( '/' );
			} else if ( $this->is_posts_page() ) {
				$canonical = get_permalink( get_option( 'page_for_posts' ) );
			} else if ( is_tax() || is_tag() || is_category() ) {
				$term = get_queried_object();
				$canonical = wpseo_get_term_meta( $term, $term->taxonomy, 'canonical' );
				if ( !$canonical )
					$canonical = get_term_link( $term, $term->taxonomy );
			} else if ( function_exists('get_post_type_archive_link') && is_post_type_archive() ) {
				$canonical = get_post_type_archive_link( get_post_type() );
			} else if ( is_author() ) {
				$canonical = get_author_posts_url( get_query_var('author'), get_query_var('author_name') );
			} else if ( is_archive() ) {
				if ( is_date() ) {
					if ( is_day() ) {
						$canonical = get_day_link( get_query_var('year'), get_query_var('monthnum'), get_query_var('day') );
					} else if ( is_month() ) {
						$canonical = get_month_link( get_query_var('year'), get_query_var('monthnum') );
					} else if ( is_year() ) {
						$canonical = get_year_link( get_query_var('year') );
					}						
				}
			}
			
			if ( $canonical && $unpaged )
				return $canonical;
				
			if ( $canonical && get_query_var('paged') > 1 ) {
				global $wp_rewrite;
				if ( !$wp_rewrite->using_permalinks() ) {
					$canonical = add_query_arg( 'paged', get_query_var('paged'), $canonical );
				} else {
					$canonical = user_trailingslashit( trailingslashit( $canonical ) . trailingslashit( $wp_rewrite->pagination_base ) . get_query_var('paged') );
				}
			}
		}
		
		if ( $canonical && isset($options['force_transport']) && 'default' != $options['force_transport'] )
			$canonical = preg_replace( '/https?/', $options['force_transport'], $canonical );

		$canonical = apply_filters( 'wpseo_canonical', $canonical );
		
		if ( $canonical && !is_wp_error( $canonical ) ) {
			if ( $echo ) 
				echo '<link rel="canonical" href="' . esc_url( $canonical, null, 'other' ) . '" />'."\n";
			else
				return $canonical;
		}
	}
	
	/**
	 * Adds 'prev' and 'next' links to archives, as described in this Google blog post: http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html
	 *
	 * @since 1.0.3
	 */
	function adjacent_rel_links() {
		global $wp_query;

		if ( !is_singular() ) {
			$url = $this->canonical( false, true );

			if ( $url ) {
				$paged = get_query_var('paged');
				
				if ( 0 == $paged )
					$paged = 1;

				if ( $paged > 1 ) 
					$this->get_adjacent_rel_link( "prev", $url, $paged-1, true );

				if ( $paged < $wp_query->max_num_pages )
					$this->get_adjacent_rel_link( "next", $url, $paged+1, true );
			}
		} else {
			$numpages = substr_count( $wp_query->post->post_content, '<!--nextpage-->' ) + 1;
			if ( $numpages > 1 ) {		
				$page = get_query_var('page');
				if ( !$page )
					$page = 1;

				$url = get_permalink( $wp_query->post->ID );

				// If the current page is the frontpage, pagination should use /base/
				if ( $this->is_home_static_page() )
					$usebase = true;
				else
					$usebase = false;

				if ( $page > 1 )
					$this->get_adjacent_rel_link( "prev", $url, $page-1, $usebase, 'single_paged' );
				if ( $page < $numpages )
					$this->get_adjacent_rel_link( "next", $url, $page+1, $usebase, 'single_paged' );
			}
		}
	}

	/**
	 * Get adjacent pages link for archives
	 *
	 * @param string $rel Link relationship, prev or next.
	 * @param string $url the unpaginated URL of the current archive.
	 * @param string $page the page number to add on to $url for the $link tag.
	 * @param boolean $incl_pagination_base whether or not to include /page/ or not.
	 * @return string $link link element
	 *
	 * @since 1.0.2
	 */
	function get_adjacent_rel_link( $rel, $url, $page, $incl_pagination_base ) {
		global $wp_rewrite;
		if ( !$wp_rewrite->using_permalinks() ) {
			if ( $page > 1 )
				$url = add_query_arg( 'paged', $page, $url );
		} else {
			if ( $page > 1 ) {
				$base = '';
				if ( $incl_pagination_base )
					$base = trailingslashit( $wp_rewrite->pagination_base );
				$url = user_trailingslashit( trailingslashit( $url ) . $base . $page );
			}
		}
		$link = "<link rel=\"$rel\" href=\"$url\" />\n";
		echo apply_filters( $rel."_rel_link", $link );	
	}
	
	function metakeywords() {
		global $wp_query;
		
		$options = get_wpseo_options();
		if ( !isset( $options['usemetakeywords'] ) || !$options['usemetakeywords'] )
			return;

		$metakey = '';
		
		if ( is_singular() ) { 
			global $post;
			$metakey = wpseo_get_value('metakeywords');
			if ( !$metakey || empty( $metakey ) && isset( $options['metakey-'.$post->post_type] ) ) {
				$metakey = wpseo_replace_vars($options['metakey-'.$post->post_type], (array) $post );
			}
		} else {
			if ( $this->is_home_posts_page() && isset($options['metakey-home']) ) {
				$metakey = wpseo_replace_vars($options['metakey-home'], array() );
			} else if ( $this->is_home_static_page() ) {
				global $post;
				$metakey = wpseo_get_value('metakey');
				if ( ($metakey == '' || !$metakey) && isset($options['metakey-'.$post->post_type]) )
					$metakey = wpseo_replace_vars($options['metakey-'.$post->post_type], (array) $post );
			} else if ( is_category() || is_tag() || is_tax() ) {
				$term = $wp_query->get_queried_object();

				$metakey = wpseo_get_term_meta( $term, $term->taxonomy, 'metakey' );
				if ( !$metakey && isset($options['metakey-'.$term->taxonomy]))
					$metakey = wpseo_replace_vars($options['metakey-'.$term->taxonomy], (array) $term );
			} else if ( is_author() ) {
				$author_id = get_query_var('author');
				$metakey = get_the_author_meta('metakey', $author_id);
				if ( !$metakey && isset($options['metakey-author']) )
					$metakey = wpseo_replace_vars($options['metakey-author'], (array) $wp_query->get_queried_object() );
			} 
			
		}

		$metakey = trim( $metakey );
		if ( !empty( $metakey ) ) 
			echo '<meta name="keywords" content="'.esc_attr( strip_tags( stripslashes( $metakey ) ) ).'"/>'."\n";

	}
	
	function metadesc( $echo = true ) {
		if ( get_query_var('paged') && get_query_var('paged') > 1 )
			return;
			
		global $post, $wp_query;
		$options = get_wpseo_options();

		$metadesc = '';
		if (is_singular()) { 
			$metadesc = wpseo_get_value('metadesc');
			if ($metadesc == '' || !$metadesc) {
				if ( isset($options['metadesc-'.$post->post_type]) && $options['metadesc-'.$post->post_type] != '' )
					$metadesc = wpseo_replace_vars($options['metadesc-'.$post->post_type], (array) $post );
			}
		} else {
			if ( is_search() ) {
				$metadesc = '';
			} else if  ( $this->is_home_posts_page() && isset($options['metadesc-home']) ) {
				$metadesc = wpseo_replace_vars($options['metadesc-home'], array() );
			} else if  ( $this->is_posts_page() ) {
				$metadesc = wpseo_get_value('metadesc', get_option('page_for_posts') );
				if ( ($metadesc == '' || !$metadesc) && isset( $options['metadesc-'.$page->post_type] ) ) {
					$page = get_post( get_option('page_for_posts') );
					$metadesc = wpseo_replace_vars( $options['metadesc-'.$page->post_type], (array) $page );
				}
			} else if ( $this->is_home_static_page() ) {
				global $post;
				$metadesc = wpseo_get_value('metadesc');
				if ( ($metadesc == '' || !$metadesc) && isset($options['metadesc-'.$post->post_type]) )
					$metadesc = wpseo_replace_vars($options['metadesc-'.$post->post_type], (array) $post );
			} else if ( is_category() || is_tag() || is_tax() ) {
				$term = $wp_query->get_queried_object();

				$metadesc = wpseo_get_term_meta( $term, $term->taxonomy, 'desc' );
				if ( !$metadesc && isset($options['metadesc-'.$term->taxonomy]))
					$metadesc = wpseo_replace_vars($options['metadesc-'.$term->taxonomy], (array) $term );
			} else if ( is_author() ) {
				$author_id = get_query_var('author');
				$metadesc = get_the_author_meta('wpseo_metadesc', $author_id);
				if ( !$metadesc && isset($options['metadesc-author']))
					$metadesc = wpseo_replace_vars($options['metadesc-author'], (array) $wp_query->get_queried_object() );
			} else if ( function_exists('is_post_type_archive') && is_post_type_archive() ) {
				$post_type = get_post_type();
				if ( isset($options['metadesc-ptarchive-'.$post_type]) && '' != $options['metadesc-ptarchive-'.$post_type] ) {
					$metadesc = $options['metadesc-ptarchive-'.$post_type];
				} 
			}
		}
	
		$metadesc = apply_filters( 'wpseo_metadesc', trim( $metadesc ) );
		
		if ( $echo ) {
			if ( !empty( $metadesc ) )
				echo '<meta name="description" content="'.esc_attr( strip_tags( stripslashes( $metadesc ) ) ).'"/>'."\n";
			else if ( current_user_can('manage_options') && is_singular() )
				echo '<!-- '.__( 'Admin only notice: this page doesn\'t show a meta description because it doesn\'t have one, either write it for this page specifically or go into the SEO -> Titles menu and set up a template.', 'wordpress-seo' ).' -->'."\n";			
		} else {
			return $metadesc;
		}
		
	}

	function page_redirect( $input ) {
		if ( is_singular() ) {
			global $post;
			if ( !isset($post) )
				return;
			$redir = wpseo_get_value('redirect', $post->ID);
			if (!empty($redir)) {
				wp_redirect($redir, 301);
				exit;
			}
		}
	}
	
	function noindex_page() {
		$this->promo();
		echo '<meta name="robots" content="noindex" />'."\n";
	}

	function noindex_feed() {
		echo '<xhtml:meta xmlns:xhtml="http://www.w3.org/1999/xhtml" name="robots" content="noindex" />'."\n";
	}

	function nofollow_link($output) {
		return str_replace('<a ','<a rel="nofollow" ',$output);
	}

	function echo_nofollow() {
		return ' rel="nofollow"';
	}

	function archive_redirect() {
		global $wp_query;
		$options = get_wpseo_options();
		if ( 
			 ( isset($options['disabledate']) && $options['disabledate'] && $wp_query->is_date ) || 
			 ( isset($options['disableauthor']) && $options['disableauthor'] && $wp_query->is_author ) ||
			 ( isset($options['disablepostformats']) && $options['disablepostformats'] && $wp_query->is_tax('post_format') ) 
		) {
			wp_redirect(get_bloginfo('url'),301);
			exit;
		}
	}

	function attachment_redirect() {
		global $post;
		if ( is_attachment() && isset($post->post_parent) && is_numeric($post->post_parent) ) {
			wp_redirect(get_permalink($post->post_parent), 301);
			exit;
		}
	}

	function add_trailingslash($url, $type) {
		// trailing slashes for everything except is_single()
		// Thanks to Mark Jaquith for this
		if ( 'single' === $type ) {
			return $url;
		} else {
			return trailingslashit($url);
		}
	}

	public function clean_permalink( $headers ) {
		if ( is_robots() || get_query_var('sitemap') )
			return;

		global $wp_query;
		
		$options = get_wpseo_options();
	
		// Recreate current URL
		$cururl = 'http';
		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
			$cururl .= "s";
		}
		$cururl .= "://";
		if ($_SERVER["SERVER_PORT"] != "80")
			$cururl .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		else
			$cururl .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];

		$properurl = '';
		
		if ( is_singular() ) {
			global $post;
			if ( empty($post) )
				$post = $wp_query->get_queried_object();

			$properurl = get_permalink($post->ID);
			
			$page = get_query_var('page');
			if ( $page && $page != 1 ) {
				$post = get_post($post->ID);
				$page_count = substr_count($post->post_content, '<!--nextpage-->');
				if ( $page > ($page_count+1) )
					$properurl = user_trailingslashit( trailingslashit( $properurl ) . ( $page_count + 1 ) );
				else
					$properurl = user_trailingslashit( trailingslashit( $properurl ) . $page );
			}
				
			// Fix reply to comment links, whoever decided this should be a GET variable?
			$result = preg_match('/(\?replytocom=[^&]+)/', $_SERVER["REQUEST_URI"], $matches);
			if ( $result )
				$properurl .= str_replace('?replytocom=','#comment-',$matches[0]);

			// Prevent cleaning out posts & page previews for people capable of viewing them
			if ( isset($_GET['preview']) && isset($_GET['preview_nonce']) && current_user_can('edit_post') )
				$properurl = '';
		} else if ( is_front_page() ) {
			if ( $this->is_home_posts_page() ) {
				$properurl = get_bloginfo('url').'/';
			} elseif ( $this->is_home_static_page() ) {
				global $post;
			 	$properurl = get_permalink( $post->ID );
			}
		} else if ( is_category() || is_tag() || is_tax() ) {
			$term = $wp_query->get_queried_object();
			if ( is_feed() )
				$properurl = get_term_feed_link( $term, $term->taxonomy );
			else
				$properurl = get_term_link( $term, $term->taxonomy );
		} else if ( is_search() ) {
			$s = preg_replace( '/(%20|\+)/', ' ', get_search_query() );
			$properurl = get_bloginfo('url').'/?s=' . rawurlencode( $s );
		} else if ( is_404() ) {
			if ( function_exists('is_multisite') && is_multisite() && !is_subdomain_install() && is_main_site() ) {
				if ($cururl == get_bloginfo('url').'/blog/' || $cururl == get_bloginfo('url').'/blog' ) {
					if ( $this->is_home_static_page() )
						$properurl = get_permalink( get_option('page_for_posts') );
					else
						$properurl = get_bloginfo('url').'/';
				}
			}
		}
		
		if ( !empty($properurl) && $wp_query->query_vars['paged'] != 0 && $wp_query->post_count != 0 ) {
			if ( is_search() ) {
				$properurl = get_bloginfo('url').'/page/' . $wp_query->query_vars['paged'] . '/?s=' . rawurlencode( $s );
			} else {
				$properurl = user_trailingslashit( trailingslashit($properurl). 'page/' . $wp_query->query_vars['paged'] );
			}
		}
		
		// Prevent cleaning out the WP Subscription managers interface for everyone
		foreach (array('wp-subscription-manager') as $get) {
			if ( isset($_GET[$get]) ) {
				$properurl = '';
			}		
		}		
		
		// Allow plugins to register their own variables not to clean
		$whitelisted_extravars = apply_filters( 'wpseo_whitelist_permalink_vars', array() );

		if (isset($options['cleanpermalink-googlesitesearch']) && $options['cleanpermalink-googlesitesearch']) {
			// Prevent cleaning out Google Site searches
			$whitelisted_extravars = array_merge( $whitelisted_extravars, array('q','cx','debug','cof','ie','sa') );
		}

		if (isset($options['cleanpermalink-googlecampaign']) && $options['cleanpermalink-googlecampaign']) {
			// Prevent cleaning out Google Analytics campaign variables
			$whitelisted_extravars = array_merge( $whitelisted_extravars, array('utm_campaign','utm_medium','utm_source','utm_content','utm_term') );
		}

		if ( isset($options['cleanpermalink-extravars']) && strlen($options['cleanpermalink-extravars']) > 0 ) {
			$whitelisted_extravars = array_merge( $whitelisted_extravars, explode( ',', $options['cleanpermalink-extravars'] ) );
		}
		
		foreach ( $whitelisted_extravars as $get ) {
			if ( isset($_GET[ trim( $get ) ]) ) {
				$properurl = '';
			}		
		}
		
		if ( !empty($properurl) && $cururl != $properurl ) {	
			wp_redirect($properurl, 301);
			exit;
		}
	}
	
	function rss_replace_vars($temp) {
		global $post;
		
		$authorlink   = '<a rel="author" href="'.get_author_posts_url( $post->post_author ).'">'.get_the_author().'</a>';
		$postlink 	  = '<a href="'.get_permalink().'">'.get_the_title()."</a>";
		$bloglink 	  = '<a href="'.get_bloginfo('url').'">'.get_bloginfo('name').'</a>';
		$blogdesclink = '<a href="'.get_bloginfo('url').'">'.get_bloginfo('name').' - '.get_bloginfo('description').'</a>';

		$temp = stripslashes($temp);
		$temp = str_replace("%%AUTHORLINK%%", $authorlink, $temp);
		$temp = str_replace("%%POSTLINK%%", $postlink, $temp);
		$temp = str_replace("%%BLOGLINK%%", $bloglink, $temp);		
		$temp = str_replace("%%BLOGDESCLINK%%", $blogdesclink, $temp);					
		return $temp;
	}

	function embed_rssfooter($content) {
		if(is_feed()) {
			$options  = get_wpseo_options();

			if ( isset($options['rssbefore']) && !empty($options['rssbefore']) ) {
				$content = "<p>" . $this->rss_replace_vars($options['rssbefore']) . "</p>" . $content;
			} 
			if ( isset($options['rssafter']) && !empty($options['rssafter']) ) {
				$content .= "<p>" . $this->rss_replace_vars($options['rssafter']). "</p>";
			} 
		}
		return $content;
	}

	function embed_rssfooter_excerpt($content) {
		if(is_feed()) {
			$options  = get_wpseo_options();

			if ( isset($options['rssbefore']) && !empty($options['rssbefore']) ) {
				$content = "<p>".$this->rss_replace_vars($options['rssbefore']) . "</p><p>" . $content ."</p>";
			} 
			if ( isset($options['rssafter']) && !empty($options['rssafter']) ) {
				$content = "<p>".$content."</p><p>".$this->rss_replace_vars($options['rssafter'])."</p>";
			} 
		}
		return $content;
	}
	
	function flush_cache() {
		global $wp_query, $post, $wpseo_ob;

		if ( !$wpseo_ob )
			return;
			
		$content = ob_get_contents();
		$title = $this->title( '' );
		
		$content = preg_replace('/<title>(.*)<\/title>/','<title>'.$title.'</title>', $content);
		ob_end_clean();
		echo $content;
	}
	
	function force_rewrite_output_buffer() {
		global $wpseo_ob;
		$wpseo_ob = true;
		ob_start();
	}
}

$wpseo_front = new WPSEO_Frontend;
