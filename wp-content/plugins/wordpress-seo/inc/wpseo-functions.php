<?php

function wpseo_get_value( $val, $postid = '' ) {
	if ( empty($postid) ) {
		global $post;
		if (isset($post))
			$postid = $post->ID;
		else 
			return false;
	}
	$custom = get_post_custom($postid);
	if (!empty($custom['_yoast_wpseo_'.$val][0]))
		return maybe_unserialize( $custom['_yoast_wpseo_'.$val][0] );
	else
		return false;
}

function wpseo_set_value( $meta, $val, $postid ) {
	update_post_meta( $postid, '_yoast_wpseo_'.$meta, $val );
}

function get_wpseo_options_arr() {
	$optarr = array('wpseo', 'wpseo_permalinks', 'wpseo_titles', 'wpseo_rss', 'wpseo_internallinks', 'wpseo_xml', 'wpseo_social');
	return apply_filters( 'wpseo_options', $optarr );
}

function get_wpseo_options() {
	$options = array();
	foreach( get_wpseo_options_arr() as $opt ) {
		$options = array_merge( $options, (array) get_option($opt) );
	}
	return $options;
}

function wpseo_replace_vars($string, $args, $omit = array() ) {
	
	$args = (array) $args;
	
	$string = strip_tags( $string );
	
	// Let's see if we can bail super early.
	if ( strpos( $string, '%%' ) === false )
		return trim( preg_replace('/\s+/u',' ', $string) );

	global $sep;
	if ( !isset( $sep ) || empty( $sep ) )
		$sep = '-';
		
	$simple_replacements = array(
		'%%sep%%'					=> $sep,
		'%%sitename%%'				=> get_bloginfo('name'),
		'%%sitedesc%%'				=> get_bloginfo('description'),
		'%%currenttime%%'			=> date('H:i'),
		'%%currentdate%%'			=> date('M jS Y'),
		'%%currentmonth%%'			=> date('F'),
		'%%currentyear%%'			=> date('Y'),
	);

	foreach ($simple_replacements as $var => $repl) {
		$string = str_replace($var, $repl, $string);
	}
	
	// Let's see if we can bail early.
	if ( strpos( $string, '%%' ) === false )
		return trim( preg_replace('/\s+/u',' ', $string) );

	global $wp_query;
	
	$defaults = array(
		'ID' => '',
		'name' => '',
		'post_author' => '',
		'post_content' => '',
		'post_date' => '',
		'post_content' => '',
		'post_excerpt' => '',
		'post_modified' => '',
		'post_title' => '',
		'taxonomy' => '',
		'term_id' => '',
	);
	
	if ( isset( $args['post_content'] ) )
		$args['post_content'] = wpseo_strip_shortcode( $args['post_content'] );
	if ( isset( $args['post_excerpt'] ) )
		$args['post_excerpt'] = wpseo_strip_shortcode( $args['post_excerpt'] );
		
	$r = (object) wp_parse_args($args, $defaults);

	// Only global $post on single's, otherwise some expressions will return wrong results.
	if ( is_singular() || ( is_front_page() && 'posts' != get_option('show_on_front') ) ) {
		global $post;
	}

	$pagenum = 0;
	$max_num_pages = 1;
	if ( !is_single() ) {
		$pagenum = get_query_var('paged');
		if ($pagenum === 0) 
			$pagenum = 1;

		if ( isset( $wp_query->max_num_pages ) && $wp_query->max_num_pages != '' && $wp_query->max_num_pages != 0 )
			$max_num_pages = $wp_query->max_num_pages;
	} else {
		$pagenum = get_query_var('page');
		$max_num_pages = substr_count( $post->post_content, '<!--nextpage-->' );
		if ( $max_num_pages >= 1 )
			$max_num_pages++;
	}
		
	// Let's do date first as it's a bit more work to get right.
	if ( $r->post_date != '' ) {
		$date = mysql2date( get_option('date_format'), $r->post_date );
	} else {
		if ( get_query_var('day') && get_query_var('day') != '' ) {
			$date = get_the_date();
		} else {
			if ( single_month_title(' ', false) && single_month_title(' ', false) != '' ) {
				$date = single_month_title(' ', false);
			} else if ( get_query_var('year') != '' ){
				$date = get_query_var('year');
			} else {
				$date = '';
			}
		}
	}
	
	$replacements = array(
		'%%date%%'					=> $date,
		'%%title%%'					=> stripslashes( $r->post_title ),
		'%%excerpt%%'				=> ( !empty($r->post_excerpt) ) ? strip_tags( $r->post_excerpt ) : substr( strip_shortcodes( strip_tags( $r->post_content ) ), 0, 155 ),
		'%%excerpt_only%%'			=> strip_tags( $r->post_excerpt ),
		'%%category%%'				=> wpseo_get_terms($r->ID, 'category'),
		'%%category_description%%'	=> !empty($r->taxonomy) ? trim(strip_tags(get_term_field( 'description', $r->term_id, $r->taxonomy ))) : '',
		'%%tag_description%%'		=> !empty($r->taxonomy) ? trim(strip_tags(get_term_field( 'description', $r->term_id, $r->taxonomy ))) : '',
		'%%term_description%%'		=> !empty($r->taxonomy) ? trim(strip_tags(get_term_field( 'description', $r->term_id, $r->taxonomy ))) : '',
		'%%term_title%%'			=> $r->name,
		'%%focuskw%%'				=> wpseo_get_value('focuskw', $r->ID),
		'%%tag%%'					=> wpseo_get_terms($r->ID, 'post_tag'),
		'%%modified%%'				=> mysql2date( get_option('date_format'), $r->post_modified ),
		'%%id%%'					=> $r->ID,
		'%%name%%'					=> get_the_author_meta('display_name', !empty($r->post_author) ? $r->post_author : get_query_var('author')),
		'%%userid%%'				=> !empty($r->post_author) ? $r->post_author : get_query_var('author'),
		'%%searchphrase%%'			=> esc_html(get_query_var('s')),
		'%%page%%'		 			=> ( $max_num_pages > 1) ? sprintf( $sep . ' ' . __('Page %d of %d','wordpress-seo'), $pagenum, $max_num_pages) : '', 
		'%%pagetotal%%'	 			=> $max_num_pages, 
		'%%pagenumber%%' 			=> $pagenum,
		'%%caption%%'				=> $r->post_excerpt,
	);
	
	foreach ($replacements as $var => $repl) {
		if ( !in_array($var, $omit) )
			$string = str_replace($var, $repl, $string);
	}
	
	if ( strpos( $string, '%%' ) === false ) {
		$string = preg_replace( '/\s+/u',' ', $string );
		return trim( $string );
	}

	if ( preg_match_all( '/%%cf_([^%]+)%%/u', $string, $matches, PREG_SET_ORDER ) ) {
		global $post;
		foreach ($matches as $match) {
			$string = str_replace( $match[0], get_post_meta( $post->ID, $match[1], true), $string );
		}
	}

	if ( preg_match_all( '/%%ct_desc_([^%]+)?%%/u', $string, $matches, PREG_SET_ORDER ) ) {
		global $post;
		foreach ($matches as $match) {
			$terms = get_the_terms( $post->ID, $match[1] );
			$string = str_replace( $match[0], get_term_field( 'description', $terms[0]->term_id, $match[1] ), $string );
		}
	}

	if ( preg_match_all( '/%%ct_([^%]+)%%(single%%)?/u', $string, $matches, PREG_SET_ORDER ) ) {
		global $post;
		foreach ($matches as $match) {
			$single = false;
			if ( isset($match[2]) && $match[2] == 'single%%' )
				$single = true;
			$ct_terms = wpseo_get_terms( $r->ID, $match[1], $single );

			$string = str_replace( $match[0], $ct_terms, $string );
		}
	}
	
	$string = preg_replace( '/\s+/u',' ', $string );
	return trim( $string );
}

function wpseo_get_terms($id, $taxonomy, $return_single = false ) {
	// If we're on a specific tag, category or taxonomy page, return that and bail.
	if ( is_category() || is_tag() || is_tax() ) {
		global $wp_query;
		$term = $wp_query->get_queried_object();
		return $term->name;
	}
	
	if ( empty($id) || empty($taxonomy) )
		return '';
		
	$output = '';
	$terms = get_the_terms($id, $taxonomy);
	if ( $terms ) {
		foreach ($terms as $term) {
			if ( $return_single )
				return $term->name;
			$output .= $term->name.', ';
		}
		return rtrim( trim($output), ',' );
	}
	return '';
}

function wpseo_get_term_meta( $term, $taxonomy, $meta ) {
	if ( is_string( $term ) ) 
		$term = get_term_by('slug', $term, $taxonomy);

	if ( is_object( $term ) )
		$term = $term->term_id;
	else
		return false;
	
	$tax_meta = get_option( 'wpseo_taxonomy_meta' );
	if ( isset($tax_meta[$taxonomy][$term]) )
		$tax_meta = $tax_meta[$taxonomy][$term];
	else
		return false;
	
	return (isset($tax_meta['wpseo_'.$meta])) ? $tax_meta['wpseo_'.$meta] : false;
}

// Strip out the shortcodes with a filthy regex, because people don't properly register their shortcodes.
function wpseo_strip_shortcode( $text ) {
	return preg_replace( '|\[(.+?)\](.+?\[/\\1\])?|s', '', $text );
}

function wpseo_limit_words( $text, $limit = 30 ) {
	$explode = explode(' ',$text);
    $string  = '';	
	$i = 0;
    while ( $limit > $i ) {
        $string .= $explode[$i]." ";
		$i++;
    }
    return $string;
}

// This should work with Greek, Russian, Polish & French amongst other languages...
function wpseo_strtolower_utf8($string){ 
	$convert_to = array( 
	  "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", 
	  "v", "w", "x", "y", "z", "à", "á", "â", "ã", "ä", "å", "æ", "ç", "è", "é", "ê", "ë", "ì", "í", "î", "ï", 
	  "ð", "ñ", "ò", "ó", "ô", "õ", "ö", "ø", "ù", "ú", "û", "ü", "ý", "а", "б", "в", "г", "д", "е", "ё", "ж", 
	  "з", "и", "й", "к", "л", "м", "н", "о", "п", "р", "с", "т", "у", "ф", "х", "ц", "ч", "ш", "щ", "ъ", "ы", 
	  "ь", "э", "ю", "я", "ą", "ć", "ę", "ł", "ń", "ó", "ś", "ź", "ż" 
	); 
	$convert_from = array( 
	  "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", 
	  "V", "W", "X", "Y", "Z", "À", "Á", "Â", "Ã", "Ä", "Å", "Æ", "Ç", "È", "É", "Ê", "Ë", "Ì", "Í", "Î", "Ï", 
	  "Ð", "Ñ", "Ò", "Ó", "Ô", "Õ", "Ö", "Ø", "Ù", "Ú", "Û", "Ü", "Ý", "А", "Б", "В", "Г", "Д", "Е", "Ё", "Ж", 
	  "З", "И", "Й", "К", "Л", "М", "Н", "О", "П", "Р", "С", "Т", "У", "Ф", "Х", "Ц", "Ч", "Ш", "Щ", "Ъ", "Ъ", 
	  "Ь", "Э", "Ю", "Я", "Ą", "Ć", "Ę", "Ł", "Ń", "Ó", "Ś", "Ź", "Ż"
	); 

	return str_replace($convert_from, $convert_to, $string);
}

/**
 * Returns the stopwords for the current language
 *
 * @since 1.1.7
 *
 * @return array $stopwords array of stop words to check and / or remove from slug
 */
function wpseo_stopwords() {
	/* translators: this should be an array of stopwords for your language, separated by comma's. */
	return explode( ',', __( "a,about,above,after,again,against,all,am,an,and,any,are,aren't,as,at,be,because,been,before,being,below,between,both,but,by,can't,cannot,could,couldn't,did,didn't,do,does,doesn't,doing,don't,down,during,each,few,for,from,further,had,hadn't,has,hasn't,have,haven't,having,he,he'd,he'll,he's,her,here,here's,hers,herself,him,himself,his,how,how's,i,i'd,i'll,i'm,i've,if,in,into,is,isn't,it,it's,its,itself,let's,me,more,most,mustn't,my,myself,no,nor,not,of,off,on,once,only,or,other,ought,our,ours , ourselves,out,over,own,same,shan't,she,she'd,she'll,she's,should,shouldn't,so,some,such,than,that,that's,the,their,theirs,them,themselves,then,there,there's,these,they,they'd,they'll,they're,they've,this,those,through,to,too,under,until,up,very,was,wasn't,we,we'd,we'll,we're,we've,were,weren't,what,what's,when,when's,where,where's,which,while,who,who's,whom,why,why's,with,won't,would,wouldn't,you,you'd,you'll,you're,you've,your,yours,yourself,yourselves", "wordpress-seo" ) );
}

/**
 * Cleans stopwords out of the slug, if the slug hasn't been set yet.
 *
 * @since 1.1.7
 *
 * @param string $slug if this isn't empty, the function will return an unaltered slug.
 * @return string $clean_slug cleaned slug
 */
function wpseo_remove_stopwords_from_slug( $slug ) {
    // Don't to change an existing slug
	if ( $slug ) 
		return $slug;
	
	if ( !isset( $_POST['post_title'] ) )
		return $slug;
		
	// Lowercase the slug and strip slashes
	$clean_slug = strtolower( stripslashes( $_POST['post_title'] ) );

	// Remove all weird HTML entities
	$clean_slug = remove_accents( $_POST['post_title'] );

    // Turn it to an array and strip stopwords by comparing against an array of stopwords
    $clean_slug_array = array_diff ( split( " ", $clean_slug ), wpseo_stopwords() );

    // Turn the sanitized array into a string
    $clean_slug = join( "-", $clean_slug_array );

	return $clean_slug;
}
add_filter( 'name_save_pre', 'wpseo_remove_stopwords_from_slug', 0 );

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
			'opengraph' => isset( $opt['opengraph'] ) ? $opt['opengraph'] : '',
			'fb_adminid' => isset( $opt['fb_adminid'] ) ? $opt['fb_adminid'] : '',
			'fb_appid' => isset( $opt['fb_appid'] ) ? $opt['fb_appid'] : '',
		);
		update_option('wpseo_social', $newopt);
		unset($opt['opengraph'], $opt['fb_pageid'], $opt['fb_adminid'], $opt['fb_appid']);
		update_option('wpseo_indexation', $opt);
	}
	
	if ( version_compare( $current_version, '1.2', '<' ) ) {
		$opt = get_option( 'wpseo_indexation' );
		$metaopt = get_option('wpseo_titles');

		$metaopt['noindex-author'] 			= isset( $opt['noindexauthor'] ) 		? $opt['noindexauthor'] 		: '';
		$metaopt['disable-author'] 			= isset( $opt['disableauthor'] ) 		? $opt['disableauthor'] 		: '';
		$metaopt['noindex-archive'] 		= isset( $opt['noindexdate'] ) 			? $opt['noindexdate'] 			: '';
		$metaopt['noindex-category'] 		= isset( $opt['noindexcat'] ) 			? $opt['noindexcat'] 			: '';
		$metaopt['noindex-post_tag'] 		= isset( $opt['noindextag'] ) 			? $opt['noindextag'] 			: '';
		$metaopt['noindex-post_format'] 	= isset( $opt['noindexpostformat'] ) 	? $opt['noindexpostformat'] 	: '';
		$metaopt['noindex-subpages']		= isset( $opt['noindexsubpages'] ) 		? $opt['noindexsubpages'] 		: '';
		$metaopt['hide-rsdlink']			= isset( $opt['hidersdlink'] ) 			? $opt['hidersdlink'] 			: '';
		$metaopt['hide-feedlinks']			= isset( $opt['hidefeedlinks'] ) 		? $opt['hidefeedlinks'] 		: '';
		$metaopt['hide-wlwmanifest']		= isset( $opt['hidewlwmanifest'] ) 		? $opt['hidewlwmanifest'] 		: '';
		$metaopt['hide-shortlink']			= isset( $opt['hideshortlink'] ) 		? $opt['hideshortlink'] 		: '';
		   
		update_option('wpseo_titles', $metaopt);

		delete_option('wpseo_indexation');
	}
	
	wpseo_title_test();
	
	$options['version'] = WPSEO_VERSION;
	update_option( 'wpseo', $options );
}