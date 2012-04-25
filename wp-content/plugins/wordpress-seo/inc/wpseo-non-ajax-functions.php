<?php

function wpseo_load_plugins( $path ) {
	$allowed_plugins = array('wpseo-local', 'wpseo-video', 'wpseo-news');
	
	$dir = @opendir( $path );
	if ($dir) {
		while (($entry = @readdir($dir)) !== false) {
			$full_dir_path = $path . "/" . $entry;
			if( in_array($entry, $allowed_plugins) && is_readable($full_dir_path) && is_dir($full_dir_path) ) {
				$module_dir = @opendir( $full_dir_path );
				if ($module_dir) {
					while (($module_entry = @readdir($module_dir)) !== false) {
						if (strrchr($module_entry, '.') === '.php') {
							require $full_dir_path . '/' . $module_entry;
						}
					}
				}
			}
		}
		@closedir($dir);
	}
}

function wpseo_get_country($country_code) {
	$country_arr = wpseo_get_country_arr();
	return $country_arr[$country_code];
}

function wpseo_get_country_arr(){
	$countries = array(
		'AF'=>'Afghanistan', 'AL'=>'Albania', 'DZ'=>'Algeria', 'AS'=>'American Samoa', 'AD'=>'Andorra', 'AO'=>'Angola', 'AI'=>'Anguilla', 'AQ'=>'Antarctica', 'AG'=>'Antigua And Barbuda', 'AR'=>'Argentina', 'AM'=>'Armenia', 'AW'=>'Aruba', 'AU'=>'Australia', 'AT'=>'Austria', 'AZ'=>'Azerbaijan', 'BS'=>'Bahamas', 'BH'=>'Bahrain', 'BD'=>'Bangladesh', 'BB'=>'Barbados', 'BY'=>'Belarus', 'BE'=>'Belgium', 'BZ'=>'Belize', 'BJ'=>'Benin', 'BM'=>'Bermuda', 'BT'=>'Bhutan', 'BO'=>'Bolivia', 'BA'=>'Bosnia And Herzegovina', 'BW'=>'Botswana', 'BV'=>'Bouvet Island', 'BR'=>'Brazil', 'IO'=>'British Indian Ocean Territory', 'BN'=>'Brunei', 'BG'=>'Bulgaria', 'BF'=>'Burkina Faso', 'BI'=>'Burundi', 'KH'=>'Cambodia', 'CM'=>'Cameroon', 'CA'=>'Canada', 'CV'=>'Cape Verde', 'KY'=>'Cayman Islands', 'CF'=>'Central African Republic', 'TD'=>'Chad', 'CL'=>'Chile', 'CN'=>'China', 'CX'=>'Christmas Island', 'CC'=>'Cocos (Keeling) Islands', 'CO'=>'Columbia', 'KM'=>'Comoros', 'CG'=>'Congo', 'CK'=>'Cook Islands', 'CR'=>'Costa Rica', 'CI'=>'Cote D\'Ivorie (Ivory Coast)', 'HR'=>'Croatia (Hrvatska)', 'CU'=>'Cuba', 'CY'=>'Cyprus', 'CZ'=>'Czech Republic', 'CD'=>'Democratic Republic Of Congo (Zaire)', 'DK'=>'Denmark', 'DJ'=>'Djibouti', 'DM'=>'Dominica', 'DO'=>'Dominican Republic', 'TP'=>'East Timor', 'EC'=>'Ecuador', 'EG'=>'Egypt', 'SV'=>'El Salvador', 'GQ'=>'Equatorial Guinea', 'ER'=>'Eritrea', 'EE'=>'Estonia', 'ET'=>'Ethiopia', 'FK'=>'Falkland Islands (Malvinas)', 'FO'=>'Faroe Islands', 'FJ'=>'Fiji', 'FI'=>'Finland', 'FR'=>'France', 'FX'=>'France, Metropolitan', 'GF'=>'French Guinea', 'PF'=>'French Polynesia', 'TF'=>'French Southern Territories', 'GA'=>'Gabon', 'GM'=>'Gambia', 'GE'=>'Georgia', 'DE'=>'Germany', 'GH'=>'Ghana', 'GI'=>'Gibraltar', 'GR'=>'Greece', 'GL'=>'Greenland', 'GD'=>'Grenada', 'GP'=>'Guadeloupe', 'GU'=>'Guam', 'GT'=>'Guatemala', 'GN'=>'Guinea', 'GW'=>'Guinea-Bissau', 'GY'=>'Guyana', 'HT'=>'Haiti', 'HM'=>'Heard And McDonald Islands', 'HN'=>'Honduras', 'HK'=>'Hong Kong', 'HU'=>'Hungary', 'IS'=>'Iceland', 'IN'=>'India', 'ID'=>'Indonesia', 'IR'=>'Iran', 'IQ'=>'Iraq', 'IE'=>'Ireland', 'IL'=>'Israel', 'IT'=>'Italy', 'JM'=>'Jamaica', 'JP'=>'Japan', 'JO'=>'Jordan', 'KZ'=>'Kazakhstan', 'KE'=>'Kenya', 'KI'=>'Kiribati', 'KW'=>'Kuwait', 'KG'=>'Kyrgyzstan', 'LA'=>'Laos', 'LV'=>'Latvia', 'LB'=>'Lebanon', 'LS'=>'Lesotho', 'LR'=>'Liberia', 'LY'=>'Libya', 'LI'=>'Liechtenstein', 'LT'=>'Lithuania', 'LU'=>'Luxembourg', 'MO'=>'Macau', 'MK'=>'Macedonia', 'MG'=>'Madagascar', 'MW'=>'Malawi', 'MY'=>'Malaysia', 'MV'=>'Maldives', 'ML'=>'Mali', 'MT'=>'Malta', 'MH'=>'Marshall Islands', 'MQ'=>'Martinique', 'MR'=>'Mauritania', 'MU'=>'Mauritius', 'YT'=>'Mayotte', 'MX'=>'Mexico', 'FM'=>'Micronesia', 'MD'=>'Moldova', 'MC'=>'Monaco', 'MN'=>'Mongolia', 'MS'=>'Montserrat', 'MA'=>'Morocco', 'MZ'=>'Mozambique', 'MM'=>'Myanmar (Burma)', 'NA'=>'Namibia', 'NR'=>'Nauru', 'NP'=>'Nepal', 'NL'=>'Netherlands', 'AN'=>'Netherlands Antilles', 'NC'=>'New Caledonia', 'NZ'=>'New Zealand', 'NI'=>'Nicaragua', 'NE'=>'Niger', 'NG'=>'Nigeria', 'NU'=>'Niue', 'NF'=>'Norfolk Island', 'KP'=>'North Korea', 'MP'=>'Northern Mariana Islands', 'NO'=>'Norway', 'OM'=>'Oman', 'PK'=>'Pakistan', 'PW'=>'Palau', 'PA'=>'Panama', 'PG'=>'Papua New Guinea', 'PY'=>'Paraguay', 'PE'=>'Peru', 'PH'=>'Philippines', 'PN'=>'Pitcairn', 'PL'=>'Poland', 'PT'=>'Portugal', 'PR'=>'Puerto Rico', 'QA'=>'Qatar', 'RE'=>'Reunion', 'RO'=>'Romania', 'RU'=>'Russia', 'RW'=>'Rwanda', 'SH'=>'Saint Helena', 'KN'=>'Saint Kitts And Nevis', 'LC'=>'Saint Lucia', 'PM'=>'Saint Pierre And Miquelon', 'VC'=>'Saint Vincent And The Grenadines', 'SM'=>'San Marino', 'ST'=>'Sao Tome And Principe', 'SA'=>'Saudi Arabia', 'SN'=>'Senegal', 'SC'=>'Seychelles', 'SL'=>'Sierra Leone', 'SG'=>'Singapore', 'SK'=>'Slovak Republic', 'SI'=>'Slovenia', 'SB'=>'Solomon Islands', 'SO'=>'Somalia', 'ZA'=>'South Africa', 'GS'=>'South Georgia And South Sandwich Islands', 'KR'=>'South Korea', 'ES'=>'Spain', 'LK'=>'Sri Lanka', 'SD'=>'Sudan', 'SR'=>'Suriname', 'SJ'=>'Svalbard And Jan Mayen', 'SZ'=>'Swaziland', 'SE'=>'Sweden', 'CH'=>'Switzerland', 'SY'=>'Syria', 'TW'=>'Taiwan', 'TJ'=>'Tajikistan', 'TZ'=>'Tanzania', 'TH'=>'Thailand', 'TG'=>'Togo', 'TK'=>'Tokelau', 'TO'=>'Tonga', 'TT'=>'Trinidad And Tobago', 'TN'=>'Tunisia', 'TR'=>'Turkey', 'TM'=>'Turkmenistan', 'TC'=>'Turks And Caicos Islands', 'TV'=>'Tuvalu', 'UG'=>'Uganda', 'UA'=>'Ukraine', 'AE'=>'United Arab Emirates', 'UK'=>'United Kingdom', 'US'=>'United States', 'UM'=>'United States Minor Outlying Islands', 'UY'=>'Uruguay', 'UZ'=>'Uzbekistan', 'VU'=>'Vanuatu', 'VA'=>'Vatican City (Holy See)', 'VE'=>'Venezuela', 'VN'=>'Vietnam', 'VG'=>'Virgin Islands (British)', 'VI'=>'Virgin Islands (US)', 'WF'=>'Wallis And Futuna Islands', 'EH'=>'Western Sahara', 'WS'=>'Western Samoa', 'YE'=>'Yemen', 'YU'=>'Yugoslavia', 'ZM'=>'Zambia', 'ZW'=>'Zimbabwe'
	);
	return $countries;
}

function wpseo_flush_rules() {
	global $wpseo_rewrite;
	$wpseo_rewrite->flush_rules();
}

function wpseo_deactivate() {
	wpseo_flush_rules();
}
register_deactivation_hook(__FILE__,'wpseo_deactivate');

function wpseo_activate() {
	wpseo_flush_rules();
}
register_activation_hook( __FILE__, 'wpseo_activate' );

function wpseo_export_settings( $include_taxonomy ) {
    $content = "; ".__( "This is a settings export file for the WordPress SEO plugin by Yoast.com", 'wordpress-seo' )." - http://yoast.com/wordpress/seo/ \r\n"; 

	$optarr = get_wpseo_options_arr();
	
	foreach ($optarr as $optgroup) {
		$content .= "\n".'['.$optgroup.']'."\n";
		$options = get_option($optgroup);
		if (!is_array($options))
			continue;
	    foreach ($options as $key => $elem) { 
	        if( is_array($elem) ) { 
	            for($i=0;$i<count($elem);$i++)  { 
	                $content .= $key."[] = \"".$elem[$i]."\"\n"; 
	            } 
	        } 
	        else if($elem=="") 
				$content .= $key." = \n"; 
	        else 
				$content .= $key." = \"".$elem."\"\n"; 
	    }		
	}

	if ( $include_taxonomy ) {
		$content .= "\r\n\r\n[wpseo_taxonomy_meta]\r\n";
		$content .= "wpseo_taxonomy_meta = \"".urlencode( json_encode( get_option('wpseo_taxonomy_meta') ) )."\"";
	}

	$dir = wp_upload_dir();
	
    if ( !$handle = fopen( $dir['path'].'/settings.ini', 'w' ) )
        die();

    if ( !fwrite($handle, $content) ) 
        die();

    fclose($handle);

	require_once (ABSPATH . 'wp-admin/includes/class-pclzip.php');
	
	chdir( $dir['path'] );
	$zip = new PclZip('./settings.zip');
	if ($zip->create('./settings.ini') == 0)
	  	return false;
	
	return $dir['url'].'/settings.zip'; 
}

/**
 * Adds an SEO admin bar menu with several options. If the current user is an admin he can also go straight to several settings menu's from here.
 */
function wpseo_admin_bar_menu() {
	// If the current user can't write posts, this is all of no use, so let's not output an admin menu
	if ( !current_user_can('edit_posts') )
		return;
		
	global $wp_admin_bar, $wpseo_front, $post;

	if ( is_object($wpseo_front) ) {
		$url = $wpseo_front->canonical( false );
	} else {
		$url = '';
	}
	
	if ( isset($post) && is_object($post) ) {
		$focuskw 	= wpseo_get_value('focuskw', $post->ID);
	} else {
		$focuskw = '';
	}

	$wp_admin_bar->add_menu( array( 'id' => 'wpseo-menu', 'title' => __( 'SEO', 'wordpress-seo' ), 'href' => get_admin_url('admin.php?page=wpseo_dashboard'), ) );

	$wp_admin_bar->add_menu( array( 'parent' => 'wpseo-menu', 'id' => 'wpseo-kwresearch', 'title' => __( 'Keyword Research', 'wordpress-seo' ), '#', ) );

	$wp_admin_bar->add_menu( array( 'parent' => 'wpseo-kwresearch', 'id' => 'wpseo-adwordsexternal', 'title' => __( 'AdWords External' ), 'href' => 'https://adwords.google.com/select/KeywordToolExternal', 'meta' => array('target' => '_blank') ) );
	$wp_admin_bar->add_menu( array( 'parent' => 'wpseo-kwresearch', 'id' => 'wpseo-googleinsights', 'title' => __( 'Google Insights' ), 'href' => 'http://www.google.com/insights/search/#q='.urlencode($focuskw).'&cmpt=q', 'meta' => array('target' => '_blank') ) );
	$wp_admin_bar->add_menu( array( 'parent' => 'wpseo-kwresearch', 'id' => 'wpseo-wordtracker', 'title' => __( 'SEO Book' ), 'href' => 'http://tools.seobook.com/keyword-tools/seobook/?keyword='.urlencode($focuskw), 'meta' => array('target' => '_blank') ) );

	if ( !is_admin() ) {
		$cleanurl = preg_replace('/^https?%3A%2F%2F/','', urlencode($url));
		$wp_admin_bar->add_menu( array( 'parent' => 'wpseo-menu', 'id' => 'wpseo-analysis', 'title' => __( 'Analyze this page', 'wordpress-seo'  ), '#', ) );
		$wp_admin_bar->add_menu( array( 'parent' => 'wpseo-analysis', 'id' => 'wpseo-inlinks-y', 'title' => __( 'Check Inlinks (Yahoo!)', 'wordpress-seo'  ), 'href' => 'https://siteexplorer.search.yahoo.com/search?p='.$cleanurl.'&bwm=i&bwmo=d&bwmf=u', 'meta' => array('target' => '_blank') ) );
		$wp_admin_bar->add_menu( array( 'parent' => 'wpseo-analysis', 'id' => 'wpseo-inlinks-ose', 'title' => __( 'Check Inlinks (OSE)', 'wordpress-seo'  ), 'href' => 'http://www.opensiteexplorer.org/'.str_replace('/','%252F',preg_replace('/^https?:\/\//','',$url)).'/a!links', 'meta' => array('target' => '_blank') ) );	
		$wp_admin_bar->add_menu( array( 'parent' => 'wpseo-analysis', 'id' => 'wpseo-kwdensity', 'title' => __( 'Check Keyword Density', 'wordpress-seo'  ), 'href' => 'http://tools.davidnaylor.co.uk/keyworddensity/index.php?url='.$url.'&keyword='.urlencode($focuskw), 'meta' => array('target' => '_blank') ) );
		$wp_admin_bar->add_menu( array( 'parent' => 'wpseo-analysis', 'id' => 'wpseo-cache', 'title' => __( 'Check Google Cache', 'wordpress-seo'  ), 'href' => 'http://webcache.googleusercontent.com/search?strip=1&q=cache:'.$url, 'meta' => array('target' => '_blank') ) );
		$wp_admin_bar->add_menu( array( 'parent' => 'wpseo-analysis', 'id' => 'wpseo-header', 'title' => __( 'Check Headers', 'wordpress-seo'  ), 'href' => 'http://quixapp.com/headers/?r='.urlencode($url), 'meta' => array('target' => '_blank') ) );
	}

	$admin_menu = false;
	if ( function_exists('is_multisite') && is_multisite() ) {
		$options = get_site_option('wpseo_ms');
		if ( is_array( $options ) && isset( $options['access'] ) && $options['access'] == 'superadmin' ) {
			if ( is_super_admin() )
				$admin_menu = true;
			else
				$admin_menu = false;
		} else {
			if ( current_user_can('manage_options') )
				$admin_menu = true;
			else
				$admin_menu = false;
		}
	} else {
		if ( current_user_can('manage_options') )
			$admin_menu = true;
	}
	
	if ( $admin_menu ) {
		$wp_admin_bar->add_menu( array( 'parent' => 'wpseo-menu', 'id' => 'wpseo-settings', 'title' => __( 'SEO Settings', 'wordpress-seo'  ), 'href' => admin_url('admin.php?page=wpseo_titles'), ) );

		$wp_admin_bar->add_menu( array( 'parent' => 'wpseo-settings', 'id' => 'wpseo-titles', 'title' => __( 'Titles', 'wordpress-seo'  ), 'href' => admin_url('admin.php?page=wpseo_titles'), ) );
		$wp_admin_bar->add_menu( array( 'parent' => 'wpseo-settings', 'id' => 'wpseo-social', 'title' => __( 'Social', 'wordpress-seo'  ), 'href' => admin_url('admin.php?page=wpseo_social'), ) );
		$wp_admin_bar->add_menu( array( 'parent' => 'wpseo-settings', 'id' => 'wpseo-indexation', 'title' => __( 'Indexation', 'wordpress-seo'  ), 'href' => admin_url('admin.php?page=wpseo_indexation'), ) );
		$wp_admin_bar->add_menu( array( 'parent' => 'wpseo-settings', 'id' => 'wpseo-xml', 'title' => __( 'XML Sitemaps', 'wordpress-seo'  ), 'href' => admin_url('admin.php?page=wpseo_xml'), ) );
		$wp_admin_bar->add_menu( array( 'parent' => 'wpseo-settings', 'id' => 'wpseo-permalinks', 'title' => __( 'Permalinks', 'wordpress-seo'  ), 'href' => admin_url('admin.php?page=wpseo_permalinks'), ) );
		$wp_admin_bar->add_menu( array( 'parent' => 'wpseo-settings', 'id' => 'wpseo-internal-links', 'title' => __( 'Internal Links', 'wordpress-seo'  ), 'href' => admin_url('admin.php?page=wpseo_internal-links'), ) );
		$wp_admin_bar->add_menu( array( 'parent' => 'wpseo-settings', 'id' => 'wpseo-rss', 'title' => __( 'RSS', 'wordpress-seo'  ), 'href' => admin_url('admin.php?page=wpseo_rss'), ) );	
	}	
}
add_action( 'admin_bar_menu', 'wpseo_admin_bar_menu', 95 );

function wpseo_stopwords_check( $haystack, $checkingUrl = false ) {
	// TODO: Make it possible to internationalize this
	$stopWords = array("a","about","above","after","again","against","all","am","an","and","any","are","aren't","as","at","be","because","been","before","being","below","between","both","but","by","can't","cannot","could","couldn't","did","didn't","do","does","doesn't","doing","don't","down","during","each","few","for","from","further","had","hadn't","has","hasn't","have","haven't","having","he","he'd","he'll","he's","her","here","here's","hers","herself","him","himself","his","how","how's","i","i'd","i'll","i'm","i've","if","in","into","is","isn't","it","it's","its","itself","let's","me","more","most","mustn't","my","myself","no","nor","not","of","off","on","once","only","or","other","ought","our","ours "," ourselves","out","over","own","same","shan't","she","she'd","she'll","she's","should","shouldn't","so","some","such","than","that","that's","the","their","theirs","them","themselves","then","there","there's","these","they","they'd","they'll","they're","they've","this","those","through","to","too","under","until","up","very","was","wasn't","we","we'd","we'll","we're","we've","were","weren't","what","what's","when","when's","where","where's","which","while","who","who's","whom","why","why's","with","won't","would","wouldn't","you","you'd","you'll","you're","you've","your","yours","yourself","yourselves");
	
	foreach ( $stopWords as $stopWord ) {
		// If checking a URL remove the single quotes
		if ( $checkingUrl )
			$stopWord = str_replace( "'", "", $stopWord );

		// Check whether the stopword appears as a whole word
		$res = preg_match( "/\b".$stopWord."\b/i", $haystack, $match );
		if ( $res > 0 )
			return $stopWord;
	}
	
	return false;
}


