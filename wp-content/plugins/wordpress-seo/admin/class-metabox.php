<?php

class WPSEO_Metabox {
	
	var $wpseo_meta_length = 156;
	var $wpseo_meta_length_reason = '';
	
	function __construct() {
		if ( !class_exists('TextStatistics') )
			require WPSEO_PATH."/admin/linkdex/TextStatistics.php";
		
		$options = get_wpseo_options();

		add_action( 'add_meta_boxes',                  array( $this, 'add_meta_box' ) );
		add_action( 'admin_print_styles-post-new.php', array( $this, 'enqueue'      ) );
		add_action( 'admin_print_styles-post.php',     array( $this, 'enqueue'      ) );
		add_action( 'admin_print_styles-edit.php',     array( $this, 'enqueue'      ) );

		add_action( 'admin_head', array( $this, 'script') );

		add_action( 'add_meta_boxes', array(&$this, 'add_custom_box') );

		add_action( 'wp_insert_post', array($this,'save_postdata') );
		
		if ( apply_filters('wpseo_use_page_analysis', true ) ) {
			add_action( 'admin_init', array(&$this, 'register_columns') );
			add_filter( 'request', array(&$this, 'column_sort_orderby') );
		
			add_action( 'restrict_manage_posts', array(&$this, 'posts_filter_dropdown') );
			add_action( 'post_submitbox_misc_actions', array( $this, 'publish_box' ) ); 
		}
	}

	public function register_columns() {
		$options = get_wpseo_options();
		
		foreach ( get_post_types( array('public' => true), 'names' ) as $pt ) {
			if ( isset($options['hideeditbox-'.$pt]) && $options['hideeditbox-'.$pt] )
				continue;
			add_filter( 'manage_'.$pt.'_posts_columns', array( $this, 'column_heading' ), 10, 1 );
			add_action( 'manage_'.$pt.'_posts_custom_column', array( $this, 'column_content' ), 10, 2 );
			add_action( 'manage_edit-'.$pt.'_sortable_columns', array( $this, 'column_sort' ), 10, 2 );
		}
	}
	
	public function publish_box() {
		$score = wpseo_get_value('linkdex');
		echo '<div class="misc-pub-section curtime misc-pub-section-last" style="height:0; padding:0; margin:0; border-top: 1px solid #DFDFDF"></div>';
		echo '<div class="misc-pub-section misc-yoast misc-pub-section-last">';

		if ( wpseo_get_value('meta-robots-noindex') == 1 ) {
			$score = 'noindex';
			$title = __('Post is set to noindex.','wordpress-seo');
		} else if ( $perc_score = wpseo_get_value('linkdex') ) {
			$score = wpseo_translate_score( round( $perc_score / 10 ) );
		} else {
			if ( isset( $_GET['post'] ) ) {
				$post_id = (int) $_GET['post'];
				$post = get_post( $post_id );
			} else {
				global $post;
			}

			$this->calculateResults( $post );
			$score = wpseo_get_value('linkdex');
			if ( !$score || empty( $score ) ) {
				$score = 'na';
				$title = __('No focus keyword set.','wordpress-seo');
			}
		}
		if ( !isset($title) )
			$title = ucfirst( $score );
		$result = '<div title="'.$title.'" alt="'.$title.'" class="wpseo_score_img '.$score.'"></div>';

		echo 'SEO: '.$result.' <a class="wpseo_tablink scroll" href="#wpseo_linkdex">Check</a>';
		
		echo '</div>';
	}
	
	public function add_custom_box() {
		$options = get_wpseo_options();

		foreach ( get_post_types() as $posttype ) {
			if ( in_array( $posttype, array('revision','nav_menu_item','attachment') ) )
				continue;
			if ( isset($options['hideeditbox-'.$posttype]) && $options['hideeditbox-'.$posttype] )
				continue;
			add_meta_box( 'wpseo_meta', __( 'WordPress SEO by Yoast', 'wordpress-seo' ), array( $this, 'meta_box' ), $posttype, 'normal', apply_filters( 'wpseo_metabox_prio', 'high' ) );
		}
	}
	
	public function script() {
		if ( isset( $_GET['post'] ) ) {
			$post_id = (int) $_GET['post'];
			$post = get_post( $post_id );
		} else {
			global $post;
		}

		if ( !isset($post) )
			return;
			
		$options = get_wpseo_options();
		
		$date = '';
		if ( $post->post_type == 'post' && apply_filters( 'wpseo_show_date_in_snippet', true, $post ) ) {
			$date = $this->get_post_date( $post );

			$this->wpseo_meta_length = $this->wpseo_meta_length - (strlen($date)+5);
			$this->wpseo_meta_length_reason = __( ' (because of date display)', 'wordpress-seo' );
		}
		
		$this->wpseo_meta_length_reason = apply_filters( 'wpseo_metadesc_length_reason', $this->wpseo_meta_length_reason, $post );
		$this->wpseo_meta_length = apply_filters('wpseo_metadesc_length', $this->wpseo_meta_length, $post );
		
		unset($date);

		$title_template = '';
		if ( isset( $options['title-'.$post->post_type] ) )
			$title_template = $options['title-'.$post->post_type];
			
		// If there's no title template set, use the default, otherwise title preview won't work.
		if ( $title_template == '' )
			$title_template = '%%title%% - %%sitename%%';
		$title_template = wpseo_replace_vars( $title_template, $post, array('%%title%%') );

		$metadesc_template = '';
		if ( isset( $options['metadesc-'.$post->post_type] ) )
			$metadesc_template = wpseo_replace_vars( $options['metadesc-'.$post->post_type], $post, array( '%%excerpt%%', '%%excerpt_only%%' ) );
		
		$sample_permalink = get_sample_permalink( $post->ID );
		$sample_permalink = str_replace('%page','%post',$sample_permalink[0]);
		?>
		<script type="text/javascript">
			var wpseo_lang ='<?php echo substr(get_locale(),0,2); ?>';
			var wpseo_meta_desc_length = '<?php echo $this->wpseo_meta_length; ?>';
			var wpseo_title_template = '<?php echo esc_attr($title_template); ?>';
			var wpseo_metadesc_template = '<?php echo esc_attr($metadesc_template); ?>';
			var wpseo_permalink_template = '<?php echo $sample_permalink; ?>';
			var wpseo_keyword_suggest_nonce = '<?php echo wp_create_nonce('wpseo-get-suggest'); ?>';
		</script>
		<?php
	}
	
	public function add_meta_box() {
		$options = get_wpseo_options();
		
		foreach ( get_post_types() as $posttype ) {
			if ( in_array( $posttype, array('revision','nav_menu_item','post_format','attachment') ) )
				continue;
			if ( isset($options['hideeditbox-'.$posttype]) && $options['hideeditbox-'.$posttype] )
				continue;
			add_meta_box( 'wpseo_meta', __( 'WordPress SEO by Yoast', 'wordpress-seo' ), array( $this, 'meta_box' ), $posttype, 'normal', 'high' );
		}
	}
	
	public function do_tab( $id, $heading, $content ) {
?>
	<div class="wpseotab <?php echo $id ?>">
		<h4 class="wpseo-heading"><?php echo $heading ?></h4>
		<table class="form-table">
			<?php echo $content ?>
		</table>
	</div>
<?php		
	}
	
	public function get_meta_boxes( $post_type = 'post' ) {
		global $post;
		
		$options = get_wpseo_options();

		$mbs = array();
		$mbs['snippetpreview'] = array(
			"name" => "snippetpreview",
			"type" => "snippetpreview",
			"title" => __("Snippet Preview", 'wordpress-seo' ),
		);
		$mbs['focuskw'] = array(
			"name" => "focuskw",
			"std" => "",
			"type" => "text",
			"title" => __("Focus Keyword", 'wordpress-seo' ),
			"description" => "<div class='alignright' style='width: 300px;'>"
			."<a class='preview button' id='wpseo_relatedkeywords' href='#wpseo_tag_suggestions'>".__('Find related keywords', 'wordpress-seo' )."</a> "
			."<p id='related_keywords_heading'>".__('Related keywords:', 'wordpress-seo' )."</p><div id='wpseo_tag_suggestions'></div></div><div id='focuskwresults'><p>".__("What is the main keyword or key phrase this page should be found for?", 'wordpress-seo' )."</p></div>",
			"autocomplete" => "off",
		);
		$mbs['title'] = array(
			"name" => "title",
			"std" => "",
			"type" => "text",
			"title" => __("SEO Title", 'wordpress-seo' ),
			"description" => '<div class="alignright" style="padding:5px;"><a class="button" href="#snippetpreview" id="wpseo_regen_title">'.__('Generate SEO title', 'wordpress-seo' ).'</a></div><p>'
				.sprintf(__("Title display in search engines is limited to 70 chars, %s chars left.", 'wordpress-seo' ), "<span id='yoast_wpseo_title-length'></span>")."<br/>"
				.sprintf(__("If the SEO Title is empty, the preview shows what the plugin generates based on your %stitle template%s.", 'wordpress-seo' ), "<a target='_blank' href='".admin_url('admin.php?page=wpseo_titles#'.$post_type)."'>", "</a>").'</p>',
		);
		$mbs['metadesc'] = array(
			"name" => "metadesc",
			"std" => "",
			"class" => "metadesc",
			"type" => "textarea",
			"title" => __("Meta Description", 'wordpress-seo' ),
			"rows" => 2,
			"richedit" => false,
			"description" => sprintf(__( "The <code>meta</code> description will be limited to %s chars%s, %s chars left.", 'wordpress-seo' ), $this->wpseo_meta_length, $this->wpseo_meta_length_reason, "<span id='yoast_wpseo_metadesc-length'></span>")." <div id='yoast_wpseo_metadesc_notice'></div><p>".sprintf(__( "If the meta description is empty, the preview shows what the plugin generates based on your %smeta description template%s.", 'wordpress-seo' ),"<a target='_blank' href='".admin_url('admin.php?page=wpseo_titles#'.$post_type)."'>", "</a>")."</p>"
		);
		if ( isset($options['usemetakeywords']) && $options['usemetakeywords'] ) {
			$mbs['metakeywords'] = array(
				"name" => "metakeywords",
				"std" => "",
				"class" => "metakeywords",
				"type" => "text",
				"title" => __("Meta Keywords", 'wordpress-seo' ),
				"description" => sprintf(__( "If you type something above it will override your %smeta keywords template%s.", 'wordpress-seo' ),"<a target='_blank' href='".admin_url('admin.php?page=wpseo_titles#'.$post_type)."'>","</a>")
			);
		}
		
		// Apply filters before entering the advanced section
		$mbs = apply_filters('wpseo_metabox_entries', $mbs);

		return $mbs;
	}
	
	function get_advanced_meta_boxes() {
		global $post;
		
		$post_type = '';
		if ( isset($post->post_type) )
			$post_type = $post->post_type;
		else if ( !isset($post->post_type) && isset( $_GET['post_type'] ) )
			$post_type = $_GET['post_type'];
			
		$options = get_wpseo_options();
		
		$mbs = array();
		
		$mbs['meta-robots-noindex'] = array(
			"name" => "meta-robots-noindex",
			"std" => "-",
			"title" => __("Meta Robots Index", 'wordpress-seo' ),
			"type" => "select",
			"options" => array(
				"0" => sprintf( __( "Default for post type, currently: %s", 'wordpress-seo'), ( isset( $options['noindex-' . $post_type ] ) && $options['noindex-' . $post_type ] ) ? 'noindex' : 'index' ),
				"2" => "index",
				"1" => "noindex",
			),
		);
		$mbs['meta-robots-nofollow'] = array(
			"name" => "meta-robots-nofollow",
			"std" => "follow",
			"title" => __("Meta Robots Follow", 'wordpress-seo' ),
			"type" => "radio",
			"options" => array(
				"0" => __("Follow", 'wordpress-seo' ),
				"1" => __("Nofollow", 'wordpress-seo' ),
			),
		);
		$mbs['meta-robots-adv'] = array(
			"name" => "meta-robots-adv",
			"std" => "none",
			"type" => "multiselect",
			"title" => __("Meta Robots Advanced", 'wordpress-seo' ),
			"description" => __("Advanced <code>meta</code> robots settings for this page.", 'wordpress-seo' ),
			"options" => array(
				"noodp" => __( "NO ODP", 'wordpress-seo' ),
				"noydir" => __( "NO YDIR", 'wordpress-seo' ),
				"noarchive" => __("No Archive", 'wordpress-seo' ),
				"nosnippet" => __("No Snippet", 'wordpress-seo' ),
			),
		);
		if (isset($options['breadcrumbs-enable']) && $options['breadcrumbs-enable']) {
			$mbs['bctitle'] = array(
				"name" => "bctitle",
				"std" => "",
				"type" => "text",
				"title" => __("Breadcrumbs title", 'wordpress-seo' ),
				"description" => __("Title to use for this page in breadcrumb paths", 'wordpress-seo' ),
			);
		}
		if (isset($options['enablexmlsitemap']) && $options['enablexmlsitemap']) {		
			$mbs['sitemap-include'] = array(
				"name" => "sitemap-include",
				"std" => "-",
				"type" => "select",
				"title" => __("Include in Sitemap", 'wordpress-seo' ),
				"description" => __("Should this page be in the XML Sitemap at all times, regardless of Robots Meta settings?", 'wordpress-seo' ),
				"options" => array(
					"-" => __("Auto detect", 'wordpress-seo' ),
					"always" => __("Always include", 'wordpress-seo' ),
					"never" => __("Never include", 'wordpress-seo' ),
				),
			);
			$mbs['sitemap-prio'] = array(
				"name" => "sitemap-prio",
				"std" => "-",
				"type" => "select",
				"title" => __("Sitemap Priority", 'wordpress-seo' ),
				"description" => __("The priority given to this page in the XML sitemap.", 'wordpress-seo' ),
				"options" => array(
					"-" => __("Automatic prioritization", 'wordpress-seo' ),
					"1" => __("1 - Highest priority", 'wordpress-seo' ),
					"0.9" => "0.9",
					"0.8" => "0.8 - ".__("Default for first tier pages", 'wordpress-seo' ),
					"0.7" => "0.7",
					"0.6" => "0.6 - ".__("Default for second tier pages and posts", 'wordpress-seo' ),
					"0.5" => "0.5 - ".__("Medium priority", 'wordpress-seo' ),
					"0.4" => "0.4",
					"0.3" => "0.3",
					"0.2" => "0.2",
					"0.1" => "0.1 - ".__("Lowest priority", 'wordpress-seo' ),
				),
			);
		}
		$mbs['canonical'] = array(
			"name" => "canonical",
			"std" => "",
			"type" => "text",
			"title" => __( "Canonical URL", 'wordpress-seo' ),
			"description" => sprintf(__( "The canonical URL that this page should point to, leave empty to default to permalink. %sCross domain canonical%s supported too.", 'wordpress-seo' ), "<a target='_blank' href='http://googlewebmastercentral.blogspot.com/2009/12/handling-legitimate-cross-domain.html'>", "</a>")
		);
		$mbs['redirect'] = array(
			"name" => "redirect",
			"std" => "",
			"type" => "text",
			"title" => __( "301 Redirect", 'wordpress-seo' ),
			"description" => __( "The URL that this page should redirect to.", 'wordpress-seo' )
		);
	
		// Apply filters for in advanced section
		$mbs = apply_filters('wpseo_metabox_entries_advanced', $mbs);

		return $mbs;
	}

	function meta_box() {
		if ( isset( $_GET['post'] ) ) {
			$post_id = (int) $_GET['post'];
			$post = get_post( $post_id );
		} else {
			global $post;
		}

		$options = get_wpseo_options();
		
?>
	<div class="wpseo-metabox-tabs-div">
		<ul class="wpseo-metabox-tabs" id="wpseo-metabox-tabs">
			<li class="general"><a class="wpseo_tablink" href="#wpseo_general"><?php _e( "General", 'wordpress-seo' ); ?></a></li>
			<li id="linkdex" class="linkdex"><a class="wpseo_tablink" href="#wpseo_linkdex"><?php _e( "Page Analysis", 'wordpress-seo' ); ?></a></li>
			<li class="advanced"><a class="wpseo_tablink" href="#wpseo_advanced"><?php _e( "Advanced", 'wordpress-seo' ); ?></a></li>
			<?php do_action('wpseo_tab_header'); ?>
		</ul>
<?php		
		$content = '';
		foreach( $this->get_meta_boxes($post->post_type) as $meta_box) {
			$content .= $this->do_meta_box( $meta_box );
		}
		$this->do_tab( 'general', __( 'General', 'wordpress-seo' ), $content );

		$this->do_tab( 'linkdex', __( 'Page Analysis', 'wordpress-seo' ), $this->linkdex_output( $post ) );
		
		if ( current_user_can('edit_users') || ! isset($options['disableadvanced_meta']) || !$options['disableadvanced_meta'] ) {
			$content = '';
			foreach( $this->get_advanced_meta_boxes() as $meta_box ) {
				$content .= $this->do_meta_box( $meta_box );
			}
			$this->do_tab( 'advanced', __( 'Advanced', 'wordpress-seo' ), $content );
		}
		
		do_action('wpseo_tab_content');
		
		echo '</div>';
	}

	function do_meta_box( $meta_box ) {
		if ( isset( $_GET['post'] ) ) {
			$post_id = (int) $_GET['post'];
			$post = get_post( $post_id );
		} else {
			global $post;
		}

		$content = '';

		if (!isset($meta_box['name'])) {
			$meta_box['name'] = '';
		} else {
			$meta_box_value = wpseo_get_value($meta_box['name']);
		}
	
		$class = '';
		if ( !empty( $meta_box['class'] ) )
			$class = ' '.$meta_box['class'];

		$placeholder = '';
		if ( isset( $meta_box['placeholder'] ) && !empty( $meta_box['placeholder'] ) )
			$placeholder = $meta_box['placeholder'];
			
		if( ( !isset($meta_box_value) || empty($meta_box_value) ) && isset($meta_box['std']) )  
			$meta_box_value = $meta_box['std'];  

		$content .= '<tr>';
		$content .= '<th scope="row"><label for="yoast_wpseo_'.$meta_box['name'].'">'.$meta_box['title'].':</label></th>';  
		$content .= '<td>';		

		switch($meta_box['type']) { 
			case "snippetpreview":
				$content .= $this->snippet();
				break;
			case "text":
				$ac = '';
				if ( isset( $meta_box['autocomplete']) && $meta_box['autocomplete'] == 'off' )
					$ac = 'autocomplete="off" ';
				$content .= '<input type="text" placeholder="'.$placeholder.'" id="yoast_wpseo_'.$meta_box['name'].'" '.$ac.'name="yoast_wpseo_'.$meta_box['name'].'" value="'.esc_attr($meta_box_value).'" class="large-text"/><br />';  
				break;
			case "textarea":
				$rows = 5;
				if (isset($meta_box['rows']))
					$rows = $meta_box['rows'];
				if (!isset($meta_box['richedit']) || $meta_box['richedit'] == true) {
					$content .= '<div class="editor_container">';
					wp_tiny_mce( true, array( "editor_selector" => $meta_box['name'].'_class' ) );
					$content .= '<textarea class="large-text '.$meta_box['name'].'_class" rows="'.$rows.'" id="yoast_wpseo_'.$meta_box['name'].'" name="yoast_wpseo_'.$meta_box['name'].'">'.esc_html($meta_box_value).'</textarea>';
					$content .= '</div>';
				} else {
					$content .= '<textarea class="large-text" rows="3" id="yoast_wpseo_'.$meta_box['name'].'" name="yoast_wpseo_'.$meta_box['name'].'">'.esc_html($meta_box_value).'</textarea>';
				}
				break;
			case "select":
				$content .= '<select name="yoast_wpseo_'.$meta_box['name'].'" id="yoast_wpseo_'.$meta_box['name'].'" class="yoast'.$class.'">';
				foreach ($meta_box['options'] as $val => $option) {
					$selected = '';
					if ($meta_box_value == $val)
						$selected = 'selected="selected"';
					$content .= '<option '.$selected.' value="'.esc_attr($val).'">'.$option.'</option>';
				}
				$content .= '</select>';
				break;
			case "multiselect":
				$selectedarr = explode(',',$meta_box_value);
				$meta_box['options'] = array('none' => 'None') + $meta_box['options'];
				$content .= '<select multiple="multiple" size="'.count($meta_box['options']).'" style="height: '.(count($meta_box['options'])*16).'px;" name="yoast_wpseo_'.$meta_box['name'].'[]" id="yoast_wpseo_'.$meta_box['name'].'" class="yoast'.$class.'">';
				foreach ($meta_box['options'] as $val => $option) {
					$selected = '';
					if (in_array($val, $selectedarr))
						$selected = 'selected="selected"';
					$content .= '<option '.$selected.' value="'.esc_attr($val).'">'.$option.'</option>';
				}
				$content .= '</select>';
				break;
			case "checkbox":
				$checked = '';
				if ($meta_box_value != 'off')
					$checked = 'checked="checked"';
				$content .= '<input type="checkbox" id="yoast_wpseo_'.$meta_box['name'].'" name="yoast_wpseo_'.$meta_box['name'].'" '.$checked.' class="yoast'.$class.'"/> '.esc_html($meta_box['expl']).'<br />';
				break;
			case "radio":
				if ($meta_box_value == '')
					$meta_box_value = $meta_box['std'];
				foreach ($meta_box['options'] as $val => $option) {
					$selected = '';
					if ($meta_box_value == $val)
						$selected = 'checked="checked"';
					$content .= '<input type="radio" '.$selected.' id="yoast_wpseo_'.$meta_box['name'].'_'.$val.'" name="yoast_wpseo_'.$meta_box['name'].'" value="'.esc_attr($val).'"/> <label for="yoast_wpseo_'.$meta_box['name'].'_'.$val.'">'.$option.'</label> ';
				}
				break;
			case "divtext":
				$content .= '<p>' . $meta_box['description'] . '</p>';
		}
		
		if ( isset($meta_box['description']) )
			$content .= '<p>'.$meta_box['description'].'</p>';
	
		$content .= '</td>';  
		$content .= '</tr>';	
		
		return $content;
	}
	
	function get_post_date( $post ) {
		if ( isset($post->post_date) && $post->post_status == 'publish' )
			$date = date('j M Y', strtotime($post->post_date) );
		else 
			$date = date('j M Y');
		return $date;
	}
	
	function snippet() {
		if ( isset( $_GET['post'] ) ) {
			$post_id = (int) $_GET['post'];
			$post = get_post( $post_id );
		} else {
			global $post;
		}
		
		$options = get_wpseo_options();
		
		// TODO: make this configurable per post type.
		$date = '';
		if ( $post->post_type == 'post' )
			$date = $this->get_post_date( $post );
		
		$title = wpseo_get_value('title');
		$desc = wpseo_get_value('metadesc');

		$slug = $post->post_name;
		if (empty($slug))
			$slug = sanitize_title($title);

		if ( !empty($date) )
			$datestr = '<span style="color: #666;">'.$date.'</span> – ';
		else
			$datestr = '';
		$content = '<div id="wpseosnippet">
			<a class="title" href="#">'.$title.'</a><br/>
		<a href="#" style="font-size: 13px; color: #282; line-height: 15px;" class="url">'.str_replace('http://','',get_bloginfo('url')).'/'.$slug.'/</a> - <a href="#" class="util">Cached</a>
			<p class="desc" style="font-size: 13px; color: #000; line-height: 15px;">'.$datestr.'<span class="content">'.$desc.'</span></p>
		</div>';

		$content = apply_filters( 'wpseo_snippet', $content, $post, compact( 'title', 'desc', 'date', 'slug' ) );

		return $content;
	}

	function save_postdata( $post_id ) {  
		
		if ( $post_id == null )
			return;

		if ( wp_is_post_revision( $post_id ) )
			return;
		
		clean_post_cache( $post_id );
		$post = get_post( $post_id );
		
		$metaboxes = array_merge( $this->get_meta_boxes( $post->post_type ), $this->get_advanced_meta_boxes() );
		
		$metaboxes = apply_filters( 'wpseo_save_metaboxes', $metaboxes );
		
		foreach( $metaboxes as $meta_box ) {  
			if ( !isset($meta_box['name']) )
				continue;

			if ( 'checkbox' == $meta_box['type'] ) {
				if ( isset( $_POST['yoast_wpseo_'.$meta_box['name']] ) )
					$data = 'on';
				else
					$data = 'off';
			} else if ( 'multiselect' == $meta_box['type'] ) {
				if ( isset( $_POST['yoast_wpseo_'.$meta_box['name']] ) ) {
					if (is_array($_POST['yoast_wpseo_'.$meta_box['name']]))
						$data = implode( ",", $_POST['yoast_wpseo_'.$meta_box['name']] );
					else
						$data = $_POST['yoast_wpseo_'.$meta_box['name']];
				} else {
					continue;
				}
			} else {
				if ( isset($_POST['yoast_wpseo_'.$meta_box['name']]) )
					$data = $_POST['yoast_wpseo_'.$meta_box['name']];  
				else 
					continue;
			}

			$option = '_yoast_wpseo_'.$meta_box['name'];
			$oldval = get_post_meta($post_id, $option, true);

			update_post_meta($post_id, $option, $data, $oldval);  
		}  
		
		$this->calculateResults( $post );

		do_action('wpseo_saved_postdata');
	}

	public function enqueue() {
		$color = get_user_meta( get_current_user_id(), 'admin_color', true );
		if ( '' == $color ) 
			$color = 'fresh';
		
		global $pagenow;
		if ( $pagenow == 'edit.php' ) {
			wp_enqueue_style( 'edit-page', WPSEO_URL.'css/edit-page.css', WPSEO_VERSION );
		} else {
			wp_enqueue_style( 'metabox-tabs', WPSEO_URL.'css/metabox-tabs.css', WPSEO_VERSION );
			wp_enqueue_style( "metabox-$color", WPSEO_URL.'css/metabox-'.$color.'.css', WPSEO_VERSION );

			wp_enqueue_script( 'jquery-ui-autocomplete', WPSEO_URL.'js/jquery-ui-autocomplete.min.js', array( 'jquery', 'jquery-ui-core' ), WPSEO_VERSION, true );		
			wp_enqueue_script( 'wp-seo-metabox', WPSEO_URL.'js/wp-seo-metabox.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-autocomplete' ), WPSEO_VERSION, true );
		}
	}

	function posts_filter_dropdown() {
		echo '<select name="seo_filter">';
		echo '<option value="">All SEO Scores</option>';
		foreach ( array(
					'na' => 'SEO: No Focus Keyword',
					'bad' => 'SEO: Bad',
					'poor' => 'SEO: Poor',
					'ok' => 'SEO: OK',
					'good' => 'SEO: Good',
					'noindex' => 'SEO: Post Noindexed',
					) as $val => $text ) {
			$sel = '';
			if ( isset($_GET['seo_filter']) && $_GET['seo_filter'] == $val )
				$sel = 'selected ';
			echo '<option '.$sel.'value="'.$val.'">'.$text.'</option>';
		}
		echo '</select>';
	}
	
	function column_heading( $columns ) {
		return array_merge( $columns, array('wpseo-score' => 'SEO', 'wpseo-title' => 'SEO Title', 'wpseo-metadesc' => 'Meta Desc.', 'wpseo-focuskw' => 'Focus KW') );
	}

	function column_content( $column_name, $id ) {
		if ( $column_name == 'wpseo-score' ) {
			if ( wpseo_get_value('meta-robots-noindex', $id) == 1 ) {
				$score = 'noindex';
				$title = __('Post is set to noindex.','wordpress-seo');
				if ( wpseo_get_value('meta-robots-noindex', $id) !== 0 )
					update_post_meta( $id, '_yoast_wpseo_linkdex', 0 );
			} else if ( $score = wpseo_get_value('linkdex', $id) ) {
				$score = wpseo_translate_score( round( $score / 10 ) );
				$title = $score;
			} else {
				$this->calculateResults( get_post( $id ) );
				$score = wpseo_get_value('linkdex', $id );
				if ( !$score || empty( $score ) ) {
					$score = 'na';
					$title = __('Focus keyword not set.','wordpress-seo');
				} else {
					$score = wpseo_translate_score( $score );
					$title = $score;
				}
			}
			
			echo '<div title="'.$title.'" alt="'.$title.'" class="wpseo_score_img '.$score.'"></div>';
		}
		if ( $column_name == 'wpseo-title' ) {
			echo $this->page_title( $id );
		}
		if ( $column_name == 'wpseo-metadesc' ) {
			echo wpseo_get_value( 'metadesc', $id );
		}
		if ( $column_name == 'wpseo-focuskw' ) {
			$focuskw = wpseo_get_value( 'focuskw', $id );
			echo $focuskw;
		}
	}
	
	function column_sort( $columns ) {
		$columns['wpseo-score'] = 'wpseo-score';
		$columns['wpseo-metadesc'] = 'wpseo-metadesc';
		$columns['wpseo-focuskw'] = 'wpseo-focuskw';
		return $columns;
	}
	
	function column_sort_orderby( $vars ) {
		if ( isset( $_GET['seo_filter'] ) ) {
			switch ( $_GET['seo_filter'] ) {
				case 'noindex':
					$low = false;
					$noindex = true;
					break;
				case 'na':
					$low = 0;
					$high = 0;
					break;
				case 'bad':
					$low = 1;
					$high = 34;
					break;
				case 'poor':
					$low = 35;
					$high = 54;
					break;
				case 'ok':
					$low = 55;
					$high = 74;
					break;
				case 'good':
					$low = 75;
					$high = 100;
					break;
				default:
					$low = false;
					$noindex = false;
					break;
			}
			if ( $low !== false ) {
				$vars = array_merge( 
						$vars, 
						array(
							'meta_query' => array(
								'relation' => 'AND',
								array(
									'key' => '_yoast_wpseo_meta-robots-noindex',
									'value' => 1,
									'compare' => '!='
								),
								array(
									'key' => '_yoast_wpseo_linkdex',
									'value' => array( $low, $high ),
									'type' => 'numeric',
									'compare' => 'BETWEEN'
					    		)
							)
						)
					);
			} else if ( $noindex ) {
				$vars = array_merge( 
						$vars, 
						array(
							'meta_query' => array(
								'relation' => 'AND',
								array(
									'key' => '_yoast_wpseo_meta-robots-noindex',
									'value' => 1,
									'compare' => '='
								),
							)
						)
					);				
			}
		}
		if ( isset( $vars['orderby'] ) && 'wpseo-score' == $vars['orderby'] ) {
			$vars = array_merge( $vars, array(
				'meta_key' => '_yoast_wpseo_linkdex',
				'orderby' => 'meta_value_num'
			) );
		}
		if ( isset( $vars['orderby'] ) && 'wpseo-metadesc' == $vars['orderby'] ) {
			$vars = array_merge( $vars, array(
				'meta_key' => '_yoast_wpseo_metadesc',
				'orderby' => 'meta_value'
			) );
		}
		if ( isset( $vars['orderby'] ) && 'wpseo-focuskw' == $vars['orderby'] ) {
			$vars = array_merge( $vars, array(
				'meta_key' => '_yoast_wpseo_focuskw',
				'orderby' => 'meta_value'
			) );
		}

		return $vars;
	}
	
	function page_title( $postid ) {
		$fixed_title = wpseo_get_value('title', $postid );
		if ($fixed_title) {
			return $fixed_title;
		} else {
			$post = get_post( $postid );
			$options = get_wpseo_options();
			if ( isset($options['title-'.$post->post_type]) && !empty($options['title-'.$post->post_type]) )
				return wpseo_replace_vars($options['title-'.$post->post_type], (array) $post );				
			else
				return wpseo_replace_vars('%%title%%', (array) $post );			
		}
	}

	function aasort( &$array, $key ) {
	    $sorter = array();
	    $ret = array();
	    reset($array);
	    foreach ($array as $ii => $va) {
	        $sorter[$ii]=$va[$key];
	    }
	    asort($sorter);
	    foreach ($sorter as $ii => $va) {
	        $ret[$ii]=$array[$ii];
	    }
	    $array=$ret;
	}

	function linkdex_output( $post ) {
		
		if ( isset( $_GET['post'] ) ) {
			$post_id = (int) $_GET['post'];
			$post = get_post( $post_id );
		} else if ( is_int( $post->ID ) ) {
			$post = get_post( $post->ID );
		}
				
		$results = $this->calculateResults( $post );

		if ( is_wp_error( $results ) ) {
			$error = $results->get_error_messages();
			return '<div class="wpseo_msg"><p><strong>'.$error[0].'</strong></p></div>';
		}
			
		$overall = 0;
		$overall_max = 0;

		$output = '<table class="wpseoanalysis">';	
		
		$perc_score = wpseo_get_value('linkdex');
		
		foreach ($results as $result) {
			$score = wpseo_translate_score( $result['val'] );
			$output .= '<tr><td class="score"><div class="wpseo_score_img '.$score.'"></div></td><td>'.$result['msg'].'</td></tr>';
		}
		$output .= '</table>';
		$output .= '<hr/>';
		$output .= '<p style="font-size: 13px;"><a href="http://yoast.com/out/linkdex/"><img class="alignleft" style="margin: 0 10px 5px 0;" src="'.WPSEO_URL.'images/linkdex-logo.png" alt="Linkdex"/></a>'.sprintf(__( 'This page analysis brought to you by the collaboration of Yoast and %sLinkdex%s. Linkdex is an SEO suite that helps you optimize your site and offers you all the SEO tools you\'ll need. Yoast uses %sLinkdex%s and highly recommends you do too!', 'wordpress-seo' ),'<a href="http://yoast.com/out/linkdex/">','</a>', '<a href="http://yoast.com/out/linkdex/">','</a>').'</p>';

		if ( WP_DEBUG )
			$output .= '<p><small>('.$perc_score.'%)</small></p>';
		
		$output = '<div class="wpseo_msg"><p>'.__('To update this page analysis, save as draft or update and check this tab again', 'wordpress-seo' ).'.</p></div>'.$output;
	
		unset( $results, $job );

		return $output;
	}

	function calculateResults( $post ) {		
		$options = get_wpseo_options();

		if ( !class_exists('DOMDocument') ) {
			$result = new WP_Error('no-domdocument', sprintf(__("Your hosting environment does not support PHP's %sDocument Object Model%s.", 'wordpress-seo' ), '<a href="http://php.net/manual/en/book.dom.php">','</a>').' '.__("To enjoy all the benefits of the page analysis feature, you'll need to (get your host to) install it.", 'wordpress-seo' ) );
			return $result;
		}
		
		if ( !wpseo_get_value( 'focuskw', $post->ID ) ) {
			$result = new WP_Error('no-focuskw', sprintf( __( 'No focus keyword was set for this %s. If you do not set a focus keyword, no score can be calculated.', 'wordpress-seo' ), $post->post_type ) );
			
			update_post_meta( $post->ID, '_yoast_wpseo_linkdex', 0 );  
			
			return $result;
		}
	
		$results	= '';
		$job 		= array();

		$sampleurl = get_sample_permalink( $post );
		$job["pageUrl"] = preg_replace( '/%(post|page)name%/', $sampleurl[1], $sampleurl[0] );
		$job["pageSlug"] = urldecode( $post->post_name );
		$job["keyword"]	= trim( wpseo_get_value('focuskw') );
		$job["keyword_folded"] = $this->strip_separators_and_fold( $job["keyword"] );

		$dom = new domDocument; 
		$dom->strictErrorChecking = false; 
		$dom->preserveWhiteSpace = false; 
		@$dom->loadHTML($post->post_content);
		$xpath = new DOMXPath($dom);

		$statistics = new TextStatistics;
		
		// Keyword
		$this->ScoreKeyword($job, $results);
		
		// Title
		if ( wpseo_get_value('title') ) {
			$title = wpseo_get_value('title');
		} else {
			if ( isset( $options['title-'.$post->post_type] ) && $options['title-'.$post->post_type] != '' )
				$title_template = $options['title-'.$post->post_type];
			else
				$title_template = '%%title%% - %%sitename%%';
			$title = wpseo_replace_vars($title_template, (array) $post );		
		}
		$this->ScoreTitle($job, $results, $title, $statistics);
		unset($title);

		// Meta description
		$description = '';
		if ( wpseo_get_value('metadesc') ) {
			$description = wpseo_get_value('metadesc');
		} else {
			if ( isset( $options['metadesc-'.$post->post_type] ) && !empty( $options['metadesc-'.$post->post_type] ) )
				$description = wpseo_replace_vars( $options['metadesc-'.$post->post_type], (array) $post );
		}

		$meta_length = apply_filters('wpseo_metadesc_length', 156, $post );
		
		$this->ScoreDescription($job, $results, $description, $meta_length, $statistics);
		unset($description);
	
		// Body
		$body 	= $this->GetBody( $post );	
		$firstp = $this->GetFirstParagraph( $post );
		$this->ScoreBody($job, $results, $body, $firstp, $statistics);
		unset($body);
		unset($firstp);

		// URL
		$this->ScoreUrl($job, $results, $statistics);	

		// Headings
		$headings = $this->GetHeadings($post->post_content);
		$this->ScoreHeadings($job, $results, $headings);
		unset($headings);

		// Images
		$alts = $this->GetImagesAltText($post->post_content);
		$imgs = $this->GetImageCount($dom, $xpath);
		$this->ScoreImagesAltText($job, $results, $alts, $imgs);
		unset($alts, $imgs);

		// Anchors
		$anchors 	= $this->GetAnchorTexts($dom, $xpath);
		$count 		= $this->GetAnchorCount($dom, $xpath);
		$this->ScoreAnchorTexts($job, $results, $anchors, $count);
		unset($anchors, $count, $dom);

		$this->aasort( $results, 'val' );
		
		$overall = 0;
		$overall_max = 0;

		foreach ($results as $result) {
			$overall += $result['val'];
			$overall_max += 9;
		}
		
		$score = round( ( $overall / $overall_max ) * 100 );
		
		update_post_meta( $post->ID, '_yoast_wpseo_linkdex', $score );  
		
		return $results;
	}
	
	function SaveScoreResult(&$results, $scoreValue, $scoreUrlStatusMessage) {
		$score = array(
			'val' => $scoreValue,
			'msg' => $scoreUrlStatusMessage
		);
		$results[] = $score;
	}

	function strip_separators_and_fold($inputString, $removeOptionalCharacters=false) {
		$keywordCharactersAlwaysReplacedBySpace = array(",", "'", "\"", "?", "’", "“", "”", "|","/");
		$keywordCharactersRemovedOrReplaced = array("_","-");
		$keywordWordsRemoved = array(" a ", " in ", " an ", " on ", " for ", " the ", " and ");

		// lower
		$inputString = wpseo_strtolower_utf8($inputString);

		// default characters replaced by space
		$inputString = str_replace($keywordCharactersAlwaysReplacedBySpace, ' ', $inputString);

		// standardise whitespace
		$inputString = preg_replace('/\s+/',' ',$inputString);

		// deal with the separators that can be either removed or replaced by space
		if ($removeOptionalCharacters) {
			// remove word separators with a space
			$inputString = str_replace($keywordWordsRemoved, ' ', $inputString);

			$inputString = str_replace($keywordCharactersRemovedOrReplaced, '', $inputString);				
		} else {
			$inputString = str_replace($keywordCharactersRemovedOrReplaced, ' ', $inputString);		
		}
		
		// standardise whitespace again
		$inputString = preg_replace('/\s+/',' ',$inputString);

		return trim( $inputString );
	}
	
	function ScoreKeyword($job, &$results) {
		$keywordStopWord = __("The keyword for this page contains one or more %sstop words%s, consider removing them. Found '%s'.", 'wordpress-seo' );
	
		if ( wpseo_stopwords_check( $job["keyword"] ) !== false )
			$this->SaveScoreResult( $results, 5, sprintf( $keywordStopWord,"<a href=\"http://en.wikipedia.org/wiki/Stop_words\">", "</a>", wpseo_stopwords_check( $job["keyword"] ) ) );			
	}
	
	function ScoreUrl($job, &$results, $statistics) {
		$urlGood 		= __("The keyword / phrase appears in the URL for this page.", 'wordpress-seo' );
		$urlMedium 		= __("The keyword / phrase does not appear in the URL for this page. If you decide to rename the URL be sure to check the old URL 301 redirects to the new one!", 'wordpress-seo' );
		$urlStopWords	= __("The slug for this page contains one or more <a href=\"http://en.wikipedia.org/wiki/Stop_words\">stop words</a>, consider removing them.", 'wordpress-seo' );
		$longSlug		= __("The slug for this page is a bit long, consider shortening it.", 'wordpress-seo' );
		
		$needle 	= $this->strip_separators_and_fold( $job["keyword"] );
		$haystack1 	= $this->strip_separators_and_fold( $job["pageUrl"], true );
		$haystack2 	= $this->strip_separators_and_fold( $job["pageUrl"], false );

		if (strrpos($haystack1,$needle) || strrpos($haystack2,$needle))
			$this->SaveScoreResult( $results, 9, $urlGood );
		else
			$this->SaveScoreResult( $results, 6, $urlMedium );	

		// Check for Stop Words in the slug
		if ( wpseo_stopwords_check( $job["pageSlug"], true ) !== false )
			$this->SaveScoreResult( $results, 5, $urlStopWords );

		// Check if the slug isn't too long relative to the length of the keyword
		if ( ( $statistics->text_length( $job["keyword"] ) + 20 ) < $statistics->text_length( $job["pageSlug"] ) && 40 < $statistics->text_length( $job["pageSlug"] ) )
			$this->SaveScoreResult( $results, 5, $longSlug );
	}

	function ScoreTitle($job, &$results, $title, $statistics) {		
		$scoreTitleMinLength 		 = 40;
		$scoreTitleMaxLength 		 = 70;
		$scoreTitleKeywordLimit		 = 0;

		$scoreTitleMissing 			 = __("Please create a page title.", 'wordpress-seo' );
		$scoreTitleCorrectLength 	 = __("The page title is more than 40 characters and less than the recommended 70 character limit.", 'wordpress-seo' );
		$scoreTitleTooShort 		 = __("The page title contains %d characters, which is less than the recommended minimum of 40 characters. Use the space to add keyword variations or create compelling call-to-action copy.", 'wordpress-seo' );
		$scoreTitleTooLong 			 = __("The page title contains %d characters, which is more than the viewable limit of 70 characters; some words will not be visible to users in your listing.", 'wordpress-seo' );
		$scoreTitleKeywordMissing 	 = __("The keyword / phrase %s does not appear in the page title.", 'wordpress-seo' );
		$scoreTitleKeywordBeginning  = __("The page title contains keyword / phrase, at the beginning which is considered to improve rankings.", 'wordpress-seo' );
		$scoreTitleKeywordEnd 		 = __("The page title contains keyword / phrase, but it does not appear at the beginning; try and move it to the beginning.", 'wordpress-seo' );
		$scoreTitleKeywordIn 		 = __("The page title contains keyword / phrase.", 'wordpress-seo' );

		if ( $title == "" ) {
			$this->SaveScoreResult($results, 1, $scoreTitleMissing);
		} else {	
			$length = $statistics->text_length( $title );
			if ($length < $scoreTitleMinLength)
				$this->SaveScoreResult( $results, 6, sprintf($scoreTitleTooShort, $length) );
			else if ($length > $scoreTitleMaxLength)
				$this->SaveScoreResult( $results, 6, sprintf($scoreTitleTooLong, $length) );
			else
				$this->SaveScoreResult( $results, 9, $scoreTitleCorrectLength );

			// TODO MA Keyword/Title matching is exact match with separators removed, but should extend to distributed match
			$needle_position = stripos( $title, $job["keyword_folded"] );

			if ( $needle_position === false ) {
				$needle_position = stripos( $title, $job["keyword"] );
			}
			
			if ( $needle_position === false )
				$this->SaveScoreResult( $results, 2, sprintf( $scoreTitleKeywordMissing, $job["keyword_folded"] ) );
			else if ( $needle_position <= $scoreTitleKeywordLimit )
				$this->SaveScoreResult( $results, 9, $scoreTitleKeywordBeginning );
			else
				$this->SaveScoreResult( $results, 6, $scoreTitleKeywordEnd );
		}
	}

	function ScoreAnchorTexts($job, &$results, $anchor_texts, $count) {
		$scoreNoLinks 					= __("No outbound links appear in this page, consider adding some as appropriate.", 'wordpress-seo' );
		$scoreKeywordInOutboundLink		= __("You're linking to another page with the keyword you want this page to rank for, consider changing that if you truly want this page to rank.", 'wordpress-seo' );
		$scoreLinksDofollow				= __("This page has %s outbound link(s).", 'wordpress-seo' );
		$scoreLinksNofollow				= __("This page has %s outbound link(s), all nofollowed.", 'wordpress-seo' );
		$scoreLinks						= __("This page has %s nofollowed link(s) and %s normal outbound link(s).", 'wordpress-seo' );

		
		if ( $count['external']['nofollow'] == 0 && $count['external']['dofollow'] == 0 ) {
			$this->SaveScoreResult( $results, 6, $scoreNoLinks );
		} else {
			$found = false;
			foreach ($anchor_texts as $anchor_text) {
				if ( wpseo_strtolower_utf8( $anchor_text ) == $job["keyword_folded"] )
					$found = true;
			}
			if ( $found )
				$this->SaveScoreResult($results, 2, $scoreKeywordInOutboundLink);

			if ( $count['external']['nofollow'] == 0 && $count['external']['dofollow'] > 0  ) {
				$this->SaveScoreResult($results, 9, sprintf( $scoreLinksDofollow, $count['external']['dofollow'] ) );
			} else if ( $count['external']['nofollow'] > 0 && $count['external']['dofollow'] == 0  ) {
				$this->SaveScoreResult($results, 7, sprintf( $scoreLinksNofollow, $count['external']['nofollow'] ) );
			} else {
				$this->SaveScoreResult($results, 8, sprintf( $scoreLinks, $count['external']['nofollow'], $count['external']['dofollow'] ) );
			}
		}

	}

	function GetAnchorTexts(&$dom, &$xpath) {
		$query 			= "//a|//A";
		$dom_objects 	= $xpath->query($query);
		$anchor_texts	= array();
		foreach ($dom_objects as $dom_object) {
			if ( $dom_object->attributes->getNamedItem('href') ) {
				$href = $dom_object->attributes->getNamedItem('href')->textContent;
				if ( substr( $href, 0, 4 ) == 'http' )
					$anchor_texts['external'] = $dom_object->textContent;
			}
		}
		unset($dom_objects);
		return $anchor_texts;
	}

	function GetAnchorCount(&$dom, &$xpath) {
		$query 			= "//a|//A";
		$dom_objects 	= $xpath->query($query);
		$count = array( 
			'total' => 0,
			'internal' => array( 'nofollow' => 0, 'dofollow' => 0 ), 
			'external' => array( 'nofollow' => 0, 'dofollow' => 0 ), 
			'other' => array( 'nofollow' => 0, 'dofollow' => 0 ) 
		);
		
		foreach ($dom_objects as $dom_object) {
			$count['total']++;
			if ( $dom_object->attributes->getNamedItem('href') ) {
				$href 	= $dom_object->attributes->getNamedItem('href')->textContent;
				$wpurl	= get_bloginfo('url'); 
				if ( substr( $href, 0, 1 ) == "/" || substr( $href, 0, strlen( $wpurl ) ) == $wpurl )
					$type = "internal";
				else if ( substr( $href, 0, 4 ) == 'http' )
					$type = "external";
				else
					$type = "other";
				if ( $dom_object->attributes->getNamedItem('rel') ) {
					$link_rel = $dom_object->attributes->getNamedItem('rel')->textContent;
					if ( stripos($link_rel, 'nofollow') !== false )
						$count[$type]['nofollow']++;
					else
						$count[$type]['dofollow']++;
				} else {
					$count[$type]['dofollow']++;
				}
			}
		}
		return $count;
	}
	
	function ScoreImagesAltText($job, &$results, $alts, $imgcount) {
		$scoreImagesNoImages 			= __("No images appear in this page, consider adding some as appropriate.", 'wordpress-seo' );
		$scoreImagesNoAlt			 	= __("The images on this page are missing alt tags.", 'wordpress-seo' );
		$scoreImagesAltKeywordIn		= __("The images on this page contain alt tags with the target keyword / phrase.", 'wordpress-seo' );
		$scoreImagesAltKeywordMissing 	= __("The images on this page do not have alt tags containing your keyword / phrase.", 'wordpress-seo' );

		if ( $imgcount == 0 ) {
			$this->SaveScoreResult($results,3,$scoreImagesNoImages);
		} else if ( count($alts) == 0 && $imgcount != 0 ) {
			$this->SaveScoreResult($results,5,$scoreImagesNoAlt);
		} else {
			$found=false;
			foreach ($alts as $alt) {
				$haystack1=$this->strip_separators_and_fold($alt,true);
				$haystack2=$this->strip_separators_and_fold($alt,false);
				if (strrpos($haystack1,$job["keyword_folded"])!==false)
					$found=true;
				else if (strrpos($haystack2,$job["keyword_folded"])!==false)
					$found=true;
			}
			if ($found)
				$this->SaveScoreResult($results,9,$scoreImagesAltKeywordIn);				
			else 
				$this->SaveScoreResult($results,5,$scoreImagesAltKeywordMissing);
		}

	}

	function GetImagesAltText($postcontent) {
		preg_match_all( '/<img [^>]+ alt=(["\'])([^\\1]+)\\1[^>]+>/im', $postcontent, $matches );
		$alts = array();
		foreach ( $matches[2] as $alt ) {
			$alts[] = wpseo_strtolower_utf8( $alt );
		}
		return $alts;
	}

	function GetImageCount(&$dom, &$xpath) {
		$query 			= "//img|//IMG";
		$dom_objects 	= $xpath->query($query);
		$count 			= 0;
		foreach ($dom_objects as $dom_object)
			$count++;
		return $count;
	}
	
	function ScoreHeadings($job, &$results, $headings) {
		$scoreHeadingsNone				= __("No heading tags appear in the copy.", 'wordpress-seo' );
		$scoreHeadingsKeywordIn			= __("Keyword / keyphrase appears in %s (out of %s) headings in the copy. While not a major ranking factor, this is beneficial.", 'wordpress-seo' );
		$scoreHeadingsKeywordMissing	= __("You have not used your keyword / keyphrase in any heading in your copy.", 'wordpress-seo' );

		$headingCount = count( $headings );
		if ( $headingCount == 0 )
			$this->SaveScoreResult( $results, 7, $scoreHeadingsNone );
		else {
			$found = 0;
			foreach ($headings as $heading) {
				$haystack1 = $this->strip_separators_and_fold( $heading , true );
				$haystack2 = $this->strip_separators_and_fold( $heading , false );

				if ( strrpos( $haystack1, $job["keyword_folded"]) !== false )
					$found++;
				else if ( strrpos( $haystack2, $job["keyword_folded"]) !== false )
					$found++;
			}
			if ( $found )
				$this->SaveScoreResult($results,9, sprintf( $scoreHeadingsKeywordIn, $found, $headingCount ) );
			else 
				$this->SaveScoreResult($results,3,$scoreHeadingsKeywordMissing);
		}
	}

	// Currently just returns an array of the text content
	function GetHeadings( $postcontent ) {
		preg_match_all('/<h([1-6])([^>]+)?>(.*)?<\/h\\1>/i', $postcontent, $matches);
		$headings = array();
		foreach ($matches[3] as $heading) {
			$headings[] = wpseo_strtolower_utf8( $heading );
		}
		return $headings;
	}	
	
	function ScoreDescription($job, &$results, $description, $maxlength = 155, $statistics) {
		$scoreDescriptionMinLength = 120;
		$scoreDescriptionCorrectLength	= __("In the specified meta description, consider: How does it compare to the competition? Could it be made more appealing?", 'wordpress-seo' );
		$scoreDescriptionTooShort 		= __("The meta description is under 120 characters, however up to %s characters are available. %s", 'wordpress-seo' );
		$scoreDescriptionTooLong		= __("The specified meta description is over %s characters, reducing it will ensure the entire description is visible. %s", 'wordpress-seo' );
		$scoreDescriptionMissing		= __("No meta description has been specified, search engines will display copy from the page instead.", 'wordpress-seo' );
		$scoreDescriptionKeywordIn		= __("The meta description contains the primary keyword / phrase.", 'wordpress-seo' );
		$scoreDescriptionKeywordMissing	= __("A meta description has been specified, but it does not contain the target keyword / phrase.", 'wordpress-seo' );

		$metaShorter					= '';
		if ($maxlength != 155)
			$metaShorter				= __("The available space is shorter than the usual 155 characters because Google will also include the publication date in the snippet.", 'wordpress-seo' );
		
		if ( $description == "" ) {
			$this->SaveScoreResult($results,1,$scoreDescriptionMissing);
		} else {
			$length = $statistics->text_length( $description );
			
			if ($length < $scoreDescriptionMinLength)
				$this->SaveScoreResult( $results, 6, sprintf($scoreDescriptionTooShort, $maxlength, $metaShorter) );
			else if ($length <= $maxlength)
				$this->SaveScoreResult( $results, 9, $scoreDescriptionCorrectLength);
			else
				$this->SaveScoreResult( $results, 6, sprintf($scoreDescriptionTooLong, $maxlength, $metaShorter) );

			// TODO MA Keyword/Title matching is exact match with separators removed, but should extend to distributed match
			$haystack1 = $this->strip_separators_and_fold($description,true);
			$haystack2 = $this->strip_separators_and_fold($description,false);
			if (strrpos($haystack1,$job["keyword_folded"])===false && strrpos($haystack2,$job["keyword_folded"])===false)
				$this->SaveScoreResult($results,3,$scoreDescriptionKeywordMissing);
			else 
				$this->SaveScoreResult($results,9,$scoreDescriptionKeywordIn);	
		}
	}

	function ScoreBody($job, &$results, $body, $firstp, $statistics) {		
		$scoreBodyGoodLimit 	= 300;
		$scoreBodyOKLimit 		= 250;
		$scoreBodyPoorLimit 	= 200;
		$scoreBodyBadLimit 		= 100;

		$scoreBodyGoodLength 	= __("There are %d words contained in the body copy, this is greater than the 300 word recommended minimum.", 'wordpress-seo' );
		$scoreBodyPoorLength 	= __("There are %d words contained in the body copy, this is below the 300 word recommended minimum. Add more useful content on this topic for readers.", 'wordpress-seo' );
		$scoreBodyBadLength 	= __("There are %d words contained in the body copy. This is far too low and should be increased.", 'wordpress-seo' );

		$scoreKeywordDensityLow 	= __("The keyword density is %s%%, which is a bit low, the keyword was found %s times.", 'wordpress-seo' );
		$scoreKeywordDensityHigh 	= __("The keyword density is %s%%, which is over the advised 4.5%% maximum, the keyword was found %s times.", 'wordpress-seo' );
		$scoreKeywordDensityGood 	= __("The keyword density is %s%%, which is great, the keyword was found %s times.", 'wordpress-seo' );

		$scoreFirstParagraphLow		= __("The keyword doesn't appear in the first paragraph of the copy, make sure the topic is clear immediately.", 'wordpress-seo' );
		$scoreFirstParagraphHigh	= __("The keyword appears in the first paragraph of the copy.", 'wordpress-seo' );

		$fleschurl					= '<a href="http://en.wikipedia.org/wiki/Flesch-Kincaid_readability_test#Flesch_Reading_Ease">'.__('Flesch Reading Ease', 'wordpress-seo' ).'</a>';
		$scoreFlesch				= __("The copy scores %s in the %s test, which is considered %s to read. %s", 'wordpress-seo' );
		
		// Copy length check
		$wordCount = $statistics->word_count( $body );
		
		if ( $wordCount < $scoreBodyBadLimit )
			$this->SaveScoreResult( $results, -10, sprintf( $scoreBodyBadLength, $wordCount ) );
		else if ( $wordCount < $scoreBodyPoorLimit )
			$this->SaveScoreResult( $results, 3, sprintf( $scoreBodyPoorLength, $wordCount ) );
		else if ( $wordCount < $scoreBodyOKLimit )
			$this->SaveScoreResult( $results, 5, sprintf( $scoreBodyPoorLength, $wordCount ) );
		else if ( $wordCount < $scoreBodyGoodLimit )
			$this->SaveScoreResult( $results, 7, sprintf( $scoreBodyPoorLength, $wordCount ) );
		else
			$this->SaveScoreResult( $results, 9, sprintf( $scoreBodyGoodLength, $wordCount ) );

		$body = wpseo_strtolower_utf8( $body );
		
		// Keyword Density check
		if ( $wordCount > 0 ) {
			$keywordCount 		= preg_match_all("/".preg_quote($job["keyword"])."/msiU", $body, $res);
			$keywordWordCount 	= str_word_count( $job["keyword"] );
			$keywordDensity 	= number_format( ( ($keywordCount / ($wordCount - (($keywordCount -1) * $keywordWordCount))) * 100 ) , 2 );
		}

		if ( $keywordDensity < 1 ) {
			$this->SaveScoreResult( $results, 4, sprintf( $scoreKeywordDensityLow, $keywordDensity, $keywordCount ) );		
		} else if ( $keywordDensity > 4.5 ) {
			$this->SaveScoreResult( $results, -50, sprintf( $scoreKeywordDensityHigh, $keywordDensity, $keywordCount ) );		
		} else {
			$this->SaveScoreResult( $results, 9, sprintf( $scoreKeywordDensityGood, $keywordDensity, $keywordCount ) );		
		}

		$firstp = wpseo_strtolower_utf8( $firstp );
		
		// First Paragraph Test
		if ( stripos( $firstp, $job["keyword"] ) === false && stripos( $firstp, $job["keyword_folded"] ) === false ) {
			$this->SaveScoreResult( $results, 3, $scoreFirstParagraphLow );
		} else {
			$this->SaveScoreResult( $results, 9, $scoreFirstParagraphHigh );		
		}

		$lang = get_bloginfo('language');
		if ( substr($lang, 0, 2) == 'en' && $wordCount > 100 ) {
			// Flesch Reading Ease check
			$flesch = $statistics->flesch_kincaid_reading_ease($body);

			$note = '';
			if ( $flesch >= 90 ) {
				$level = __('very easy','wordpress-seo');
				$score = 9;
			} else if ( $flesch >= 80 ) {
				$level = __('easy','wordpress-seo');
				$score = 9;
			} else if ( $flesch >= 70 ) {
				$level = __('fairly easy','wordpress-seo');
				$score = 8;
			} else if ( $flesch >= 60 ) {
				$level = __('OK','wordpress-seo');
				$score = 7;
			} else if ( $flesch >= 50 ) {
				$level = __('fairly difficult','wordpress-seo');
				$note = __('Try to make shorter sentences to improve readability.', 'wordpress-seo' );
				$score = 6;
			} else if ( $flesch >= 30 ) {
				$level = __('difficult','wordpress-seo');
				$note = __('Try to make shorter sentences, using less difficult words to improve readability.', 'wordpress-seo' );
				$score = 5;
			} else if ( $flesch >= 0 ) {
				$level = __('very difficult','wordpress-seo');
				$note = __('Try to make shorter sentences, using less difficult words to improve readability.', 'wordpress-seo' );
				$score = 4;
			}
			$this->SaveScoreResult( $results, $score, sprintf( $scoreFlesch, $flesch, $fleschurl, $level, $note ) );	
		}
	}

	function GetBody( $post ) {		
		// Strip shortcodes, for obvious reasons
		$origHtml = wpseo_strip_shortcode( $post->post_content );
		if ( trim( $origHtml ) == '' )
			return '';

		$htmdata2 = preg_replace( "/\n|\r/"," ",$origHtml );
		if ( $htmdata2 == null )
			$htmdata2 = $origHtml;
		else
			unset( $origHtml );

		$htmdata3 = preg_replace( "/<(\x20*script|script).*?(\/>|\/script>)/", "", $htmdata2 );
		if ( $htmdata3 == null)
			$htmdata3 = $htmdata2;
		else
			unset( $htmdata2 );

		$htmdata4 = preg_replace( "/<!--.*?-->/", "", $htmdata3 );
		if ( $htmdata4 == null )
			$htmdata4 = $htmdata3;
		else
			unset( $htmdata3 );

		$htmdata5 = preg_replace( "/<(\x20*style|style).*?(\/>|\/style>)/", "", $htmdata4 );
		if ( $htmdata5 == null)
			$htmdata5 = $htmdata4;
		else
			unset( $htmdata4 );			

		return $htmdata5;
	}

	function GetFirstParagraph( $post ) {
		// To determine the first paragraph we first need to autop the content, then match the first paragraph and return.		
		$res = preg_match( '/<p>(.*)<\/p>/', wpautop( $post->post_content ), $matches );
		if ( $res )
			return $matches[1];
		return false;
	}
}
$wpseo_metabox = new WPSEO_Metabox();