<?php

/**
 * Backend Class for use in all Yoast plugins
 * Version 0.2.1
 */

if ( !class_exists('Yoast_WPSEO_Plugin_Admin') ) {
	class Yoast_WPSEO_Plugin_Admin {

		var $hook 		= '';
		var $filename	= '';
		var $longname	= '';
		var $shortname	= '';
		var $ozhicon	= '';
		var $optionname = '';
		var $homepage	= '';
		var $feed		= 'http://yoast.com/feed/';
		var $accesslvl	= 'manage_options';
		var $adminpages = array( 'wpseo_dashboard', 'wpseo_rss', 'wpseo_indexation', 'wpseo_files', 'wpseo_permalinks', 'wpseo_internal-links', 'wpseo_import', 'wpseo_titles', 'wpseo_xml', 'wpseo_social');
		
		function __construct() {
		}
		
		function add_ozh_adminmenu_icon( $hook ) {
			if ($hook == $this->hook) 
				return WPSEO_URL.$this->ozhicon;
			return $hook;
		}
		
		function config_page_styles() {
			global $pagenow;
			if ( $pagenow == 'admin.php' && isset($_GET['page']) && in_array($_GET['page'], $this->adminpages) ) {
				wp_enqueue_style('dashboard');
				wp_enqueue_style('thickbox');
				wp_enqueue_style('global');
				wp_enqueue_style('wp-admin');
				wp_enqueue_style('yoast-admin-css', WPSEO_URL . 'css/yst_plugin_tools.css', WPSEO_VERSION );
			}
		}

		function register_network_settings_page() {
			add_menu_page($this->longname, $this->shortname, 'delete_users', 'wpseo_dashboard', array(&$this,'network_config_page'), WPSEO_URL.'images/yoast-icon.png');
		}
		
		function register_settings_page() {
			add_menu_page($this->longname, $this->shortname, $this->accesslvl, 'wpseo_dashboard', array(&$this,'config_page'), WPSEO_URL.'images/yoast-icon.png');
			add_submenu_page('wpseo_dashboard',__( 'Titles', 'wordpress-seo' ),__( 'Titles', 'wordpress-seo' ),$this->accesslvl, 'wpseo_titles', array(&$this,'titles_page'));
			add_submenu_page('wpseo_dashboard',__( 'Indexation', 'wordpress-seo' ),__( 'Indexation', 'wordpress-seo' ),$this->accesslvl, 'wpseo_indexation', array(&$this,'indexation_page'));
			add_submenu_page('wpseo_dashboard',__( 'Social', 'wordpress-seo' ),__( 'Social', 'wordpress-seo' ),$this->accesslvl, 'wpseo_social', array(&$this,'social_page'));
			add_submenu_page('wpseo_dashboard',__( 'XML Sitemaps', 'wordpress-seo' ),__( 'XML Sitemaps', 'wordpress-seo' ),$this->accesslvl, 'wpseo_xml', array(&$this,'xml_sitemaps_page'));
			add_submenu_page('wpseo_dashboard',__( 'Permalinks', 'wordpress-seo' ),__( 'Permalinks', 'wordpress-seo' ),$this->accesslvl, 'wpseo_permalinks', array(&$this,'permalinks_page'));
			add_submenu_page('wpseo_dashboard',__( 'Internal Links', 'wordpress-seo' ),__( 'Internal Links', 'wordpress-seo' ),$this->accesslvl, 'wpseo_internal-links', array(&$this,'internallinks_page'));
			add_submenu_page('wpseo_dashboard',__( 'RSS', 'wordpress-seo' ),__( 'RSS', 'wordpress-seo' ),$this->accesslvl, 'wpseo_rss', array(&$this,'rss_page'));
			add_submenu_page('wpseo_dashboard',__( 'Import & Export', 'wordpress-seo' ),__( 'Import & Export', 'wordpress-seo' ),$this->accesslvl, 'wpseo_import', array(&$this,'import_page'));
			
			// Make sure on a multi site install only super admins can edit .htaccess and robots.txt
			if ( !function_exists('is_multisite') || !is_multisite() )
				add_submenu_page('wpseo_dashboard',__( 'Edit files', 'wordpress-seo' ),__( 'Edit files', 'wordpress-seo' ),$this->accesslvl, 'wpseo_files', array(&$this,'files_page'));
			else
				add_submenu_page('wpseo_dashboard',__( 'Edit files', 'wordpress-seo' ),__( 'Edit files', 'wordpress-seo' ),'delete_users', 'wpseo_files', array(&$this,'files_page'));
			
			global $submenu;
			if ( isset($submenu['wpseo_dashboard']) )
				$submenu['wpseo_dashboard'][0][0] = __( 'Dashboard', 'wordpress-seo' );
		}
		
		function plugin_options_url() {
			return admin_url( 'admin.php?page=wpseo_dashboard' );
		}
		
		/**
		 * Add a link to the settings page to the plugins list
		 */
		function add_action_link( $links, $file ) {
			static $this_plugin;
			if( empty($this_plugin) ) $this_plugin = $this->filename;
			if ( $file == $this_plugin ) {
				$settings_link = '<a href="' . $this->plugin_options_url() . '">' . __('Settings', 'wordpress-seo' ) . '</a>';
				array_unshift( $links, $settings_link );
			}
			return $links;
		}
		
		function config_page() {
			
		}
		
		function config_page_scripts() {
			global $pagenow;
			wp_enqueue_script( 'wpseo-admin-global-script', WPSEO_URL.'js/wp-seo-admin-global.js', array('jquery'), WPSEO_VERSION, true );

			if ( $pagenow == 'admin.php' && isset($_GET['page']) && in_array($_GET['page'], $this->adminpages) ) {
				wp_enqueue_script( 'wpseo-admin-script', WPSEO_URL.'js/wp-seo-admin.js', array('jquery'), WPSEO_VERSION, true );
				wp_enqueue_script( 'postbox' );
				wp_enqueue_script( 'dashboard' );
				wp_enqueue_script( 'thickbox' );
			}
		}

		/**
		 * Create a Checkbox input field
		 */
		function checkbox($id, $label, $label_left = false, $option = '') {
			if ( $option == '') {
				$options = get_wpseo_options();
				$option = !empty($option) ? $option : $this->currentoption;
			} else {
				if ( function_exists('is_network_admin') && is_network_admin() ) {
					$options = get_site_option($option);
				} else {
					$options = get_option($option);
				}
			}

			if (!isset($options[$id]))
				$options[$id] = false;
				
			$output_label = '<label for="'.$id.'">'.$label.'</label>';
			$output_input = '<input class="checkbox" type="checkbox" id="'.$id.'" name="'.$option.'['.$id.']"'. checked($options[$id],'on',false).'/> ';
			
			if( $label_left ) {
				$output = $output_label . $output_input;
			} else {
				$output = $output_input . $output_label;
			}
			return $output . '<br class="clear" />';
		}
		
		/**
		 * Create a Text input field
		 */
		function textinput($id, $label, $option = '') {
			if ( $option == '') {
				$options = get_wpseo_options();
				$option = !empty($option) ? $option : $this->currentoption;
			} else {
				if ( function_exists('is_network_admin') && is_network_admin() ) {
					$options = get_site_option($option);
				} else {
					$options = get_option($option);
				}
			}
			
			$val = '';
			if (isset($options[$id]))
				$val = htmlspecialchars($options[$id]);
			
			return '<label class="textinput" for="'.$id.'">'.$label.':</label><input class="textinput" type="text" id="'.$id.'" name="'.$option.'['.$id.']" value="'.$val.'"/>' . '<br class="clear" />';
		}
		
		/**
		 * Create a small textarea
		 */
		function textarea($id, $label, $option = '', $class = '') {
			if ( $option == '') {
				$options = get_wpseo_options();
				$option = !empty($option) ? $option : $this->currentoption;
			} else {
				if ( function_exists('is_network_admin') && is_network_admin() ) {
					$options = get_site_option($option);
				} else {
					$options = get_option($option);
				}
			}
			
			$val = '';
			if (isset($options[$id]))
				$val = esc_html($options[$id]);
			
			return '<label class="textinput" for="'.$id.'">'.$label.':</label><textarea class="textinput '.$class.'" id="'.$id.'" name="'.$option.'['.$id.']">' . $val . '</textarea>' . '<br class="clear" />';
		}
		
		/**
		 * Create a Hidden input field
		 */
		function hiddeninput($id, $option = '') {
			if ( $option == '') {
				$options = get_wpseo_options();
				$option = !empty($option) ? $option : $this->currentoption;
			} else {
				if ( function_exists('is_network_admin') && is_network_admin() ) {
					$options = get_site_option($option);
				} else {
					$options = get_option($option);
				}
			}
			
			$val = '';
			if (isset($options[$id]))
				$val = htmlspecialchars($options[$id]);
			return '<input class="hidden" type="hidden" id="'.$id.'" name="'.$option.'['.$id.']" value="'.$val.'"/>';
		}
		
		/**
		 * Create a Select Box
		 */
		function select($id, $label, $values, $option = '') {
			if ( $option == '') {
				$options = get_wpseo_options();
				$option = !empty($option) ? $option : $this->currentoption;
			} else {
				if ( function_exists('is_network_admin') && is_network_admin() ) {
					$options = get_site_option($option);
				} else {
					$options = get_option($option);
				}
			}
			
			$output = '<label class="select" for="'.$id.'">'.$label.':</label>';
			$output .= '<select class="select" name="'.$option.'['.$id.']" id="'.$id.'">';
			
			foreach($values as $value => $label) {
				$sel = '';
				if (isset($options[$id]) && $options[$id] == $value)
					$sel = 'selected="selected" ';

				if (!empty($label))
					$output .= '<option '.$sel.'value="'.$value.'">'.$label.'</option>';
			}
			$output .= '</select>';
			return $output . '<br class="clear"/>';
		}
		
		/**
		 * Create a File upload
		 */
		function file_upload($id, $label, $option = '') {
			$option = !empty($option) ? $option : $this->currentoption;
			$options = get_wpseo_options();
			
			$val = '';
			if (isset($options[$id]) && strtolower(gettype($options[$id])) == 'array') {
				$val = $options[$id]['url'];
			}
			$output = '<label class="select" for="'.$id.'">'.$label.':</label>';
			$output .= '<input type="file" value="' . $val . '" class="textinput" name="'.$option.'['.$id.']" id="'.$id.'"/>';
			
			// Need to save separate array items in hidden inputs, because empty file inputs type will be deleted by settings API.
			if(!empty($options[$id])) {
				$output .= '<input class="hidden" type="hidden" id="' . $id . '_file" name="wpseo_local[' . $id . '][file]" value="' . $options[$id]['file'] . '"/>'; 
				$output .= '<input class="hidden" type="hidden" id="' . $id . '_url" name="wpseo_local[' . $id . '][url]" value="' . $options[$id]['url'] . '"/>'; 
				$output .= '<input class="hidden" type="hidden" id="' . $id . '_type" name="wpseo_local[' . $id . '][type]" value="' . $options[$id]['type'] . '"/>'; 
			}
			$output .= '<br class="clear"/>';
			
			return $output;
		}
		
		/**
		 * Create a Radio input field
		 */
		function radio($id, $values, $label, $option = '') {
			if ( $option == '') {
				$options = get_wpseo_options();
				$option = !empty($option) ? $option : $this->currentoption;
			} else {
				if ( function_exists('is_network_admin') && is_network_admin() ) {
					$options = get_site_option($option);
				} else {
					$options = get_option($option);
				}
			}
			
			if (!isset($options[$id]))
				$options[$id] = false;

			$output = '<br/><label class="select">'.$label.':</label>'; 
			foreach($values as $key => $value) {
				$output .= '<input type="radio" class="radio" id="'.$id.'-' . $key . '" name="'.$option.'['.$id.']" value="'. $key.'" ' . ($options[$id] == $key ? ' checked="checked"' : '') . ' /> <label class="radio" for="'.$id.'-' . $key . '">'.$value.'</label>';
			}
			$output .= '<br/>';
			
			return $output;
		}
		
		/**
		 * Create a hidden input field
		 */
		function hidden($id, $option = '') {
			if ( $option == '') {
				$options = get_wpseo_options();
				$option = !empty($option) ? $option : $this->currentoption;
			} else {
				if ( function_exists('is_network_admin') && is_network_admin() ) {
					$options = get_site_option($option);
				} else {
					$options = get_option($option);
				}
			}

			if (!isset($options[$id]))
				$options[$id] = '';
			
			return '<input type="hidden" id="hidden_'.$id.'" name="'.$option.'['.$id.']" value="'.$options[$id].'"/>';
		}

		/**
		 * Create a potbox widget
		 */
		function postbox($id, $title, $content) {
		?>
			<div id="<?php echo $id; ?>" class="postbox">
				<div class="handlediv" title="Click to toggle"><br /></div>
				<h3 class="hndle"><span><?php echo $title; ?></span></h3>
				<div class="inside">
					<?php echo $content; ?>
				</div>
			</div>
		<?php
		}


		/**
		 * Create a form table from an array of rows
		 */
		function form_table($rows) {
			$content = '<table class="form-table">';
			foreach ($rows as $row) {
				$content .= '<tr><th valign="top" scrope="row">';
				if (isset($row['id']) && $row['id'] != '')
					$content .= '<label for="'.$row['id'].'">'.$row['label'].':</label>';
				else
					$content .= $row['label'];
				if (isset($row['desc']) && $row['desc'] != '')
					$content .= '<br/><small>'.$row['desc'].'</small>';
				$content .= '</th><td valign="top">';
				$content .= $row['content'];
				$content .= '</td></tr>'; 
			}
			$content .= '</table>';
			return $content;
		}

		/**
		 * Info box with link to the support forums.
		 */
		function plugin_support() {
			$content = '<p>'.__('If you are having problems with this plugin, please talk about them in the', 'wordpress-seo' ).' <a href="http://wordpress.org/tags/'.$this->hook.'">'.__("Support forums", 'wordpress-seo' ).'</a>.</p>';
			$content .= '<p>'.sprintf( __("If you're sure you've found a bug, or have a feature request, please submit it in the %1$sbug tracker%2$s.", "wordpress-seo"), "<a href='http://yoast.com/bugs/wordpress-seo/'>","</a>")."</p>";
			$this->postbox($this->hook.'support', __('Need support?', 'wordpress-seo' ), $content);
		}

		function text_limit( $text, $limit, $finish = '&hellip;') {
			if( strlen( $text ) > $limit ) {
		    	$text = substr( $text, 0, $limit );
				$text = substr( $text, 0, - ( strlen( strrchr( $text,' ') ) ) );
				$text .= $finish;
			}
			return $text;
		}

		function fetch_rss_items( $num ) {
			include_once(ABSPATH . WPINC . '/feed.php');
			$rss = fetch_feed( $this->feed );
			
			// Bail if feed doesn't work
			if ( is_wp_error($rss) )
				return false;
			
			$rss_items = $rss->get_items( 0, $rss->get_item_quantity( $num ) );
			
			// If the feed was erroneously 
			if ( !$rss_items ) {
				$md5 = md5( $this->feed );
				delete_transient( 'feed_' . $md5 );
				delete_transient( 'feed_mod_' . $md5 );
				$rss = fetch_feed( $this->feed );
				$rss_items = $rss->get_items( 0, $rss->get_item_quantity( $num ) );
			}
			
			return $rss_items;
		}
		
		/**
		 * Box with latest news from Yoast.com for sidebar
		 */
		function news() {
			$rss_items = $this->fetch_rss_items( 5 );
			
			$content = '<ul>';
			if ( !$rss_items ) {
			    $content .= '<li class="yoast">'.__( 'No news items, feed might be broken...', 'wordpress-seo' ).'</li>';
			} else {
			    foreach ( $rss_items as $item ) {
			    	$url = preg_replace( '/#.*/', '', esc_url( $item->get_permalink(), $protocolls=null, 'display' ) );
					$content .= '<li class="yoast">';
					$content .= '<a class="rsswidget" href="'.$url.'#utm_source=wpadmin&utm_medium=sidebarwidget&utm_term=newsitem&utm_campaign=wpseoplugin">'. esc_html( $item->get_title() ) .'</a> ';
					$content .= '</li>';
			    }
			}						
			$content .= '<li class="facebook"><a href="https://www.facebook.com/yoastcom">'.__( 'Like Yoast on Facebook', 'wordpress-seo' ).'</a></li>';
			$content .= '<li class="twitter"><a href="http://twitter.com/yoast">'.__( 'Follow Yoast on Twitter', 'wordpress-seo' ).'</a></li>';
			$content .= '<li class="googleplus"><a href="https://plus.google.com/115369062315673853712/posts">'.__( 'Circle Yoast on Google+', 'wordpress-seo' ).'</a></li>';
			$content .= '<li class="rss"><a href="'.$this->feed.'">'.__( 'Subscribe with RSS', 'wordpress-seo' ).'</a></li>';
			$content .= '<li class="email"><a href="http://yoast.com/wordpress-newsletter/">'.__( 'Subscribe by email', 'wordpress-seo' ).'</a></li>';
			$content .= '</ul>';
			$this->postbox('yoastlatest', __( 'Latest news from Yoast', 'wordpress-seo' ), $content);
		}

	}
}

