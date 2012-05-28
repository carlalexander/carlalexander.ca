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
	$optarr = array('wpseo','wpseo_indexation', 'wpseo_permalinks', 'wpseo_titles', 'wpseo_rss', 'wpseo_internallinks', 'wpseo_xml', 'wpseo_social');
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
		return trim( preg_replace('/\s+/',' ', $string) );

	$simple_replacements = array(
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
		return trim( preg_replace('/\s+/',' ', $string) );

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
	$max_num_pages = 0;
	if ( !is_single() ) {
		$pagenum = get_query_var('paged');
		if ($pagenum === 0) {
			if ($wp_query->max_num_pages > 1)
				$pagenum = 1;
			else
				$pagenum = '';
		}
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
		'%%page%%'		 			=> ( $max_num_pages != 0 ) ? 'Page '.$pagenum.' of '.$max_num_pages : '', 
		'%%pagetotal%%'	 			=> ( $max_num_pages > 1 ) ? $max_num_pages : '', 
		'%%pagenumber%%' 			=> $pagenum,
		'%%caption%%'				=> $r->post_excerpt,
	);
	
	foreach ($replacements as $var => $repl) {
		if ( !in_array($var, $omit) )
			$string = str_replace($var, $repl, $string);
	}
	
	if ( strpos( $string, '%%' ) === false ) {
		$string = preg_replace( '/\s\s+/',' ', $string );
		return trim( $string );
	}

	if ( preg_match_all( '/%%cf_([^%]+)%%/', $string, $matches, PREG_SET_ORDER ) ) {
		global $post;
		foreach ($matches as $match) {
			$string = str_replace( $match[0], get_post_meta( $post->ID, $match[1], true), $string );
		}
	}
	
	$string = preg_replace( '/\s\s+/',' ', $string );
	return trim( $string );
}

function wpseo_get_terms($id, $taxonomy) {
	// If we're on a specific tag, category or taxonomy page, return that and bail.
	if ( is_category() || is_tag() || is_tax() ) {
		global $wp_query;
		$term = $wp_query->get_queried_object();
		return $term->name;
	}
	
	$output = '';
	$terms = get_the_terms($id, $taxonomy);
	if ( $terms ) {
		foreach ($terms as $term) {
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

	if ( isset( $_POST['post_title'] ) )
		return $slug;
		
	// Clean the slug of weirdness
	$clean_slug = sanitize_title( stripslashes( $_POST['post_title'] ) );

    // Turn it to an array and strip stopwords by comparing against an array of stopwords
    $clean_slug_array = array_diff ( split( " ", $clean_slug ), wpseo_stopwords() );

    // Turn the sanitized array into a string
    $clean_slug = join( "-", $clean_slug_array );

	return $clean_slug;
}
add_filter( 'name_save_pre', 'wpseo_remove_stopwords_from_slug', 0 );
