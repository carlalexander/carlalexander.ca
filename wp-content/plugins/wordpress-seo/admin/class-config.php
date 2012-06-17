<?php
if ( ! class_exists( 'WPSEO_Admin' ) ) {
	
	class WPSEO_Admin extends Yoast_WPSEO_Plugin_Admin {

		var $hook 			= 'wordpress-seo';
		var $filename		= 'wordpress-seo/wp-seo.php';
		var $longname;
		var $shortname;
		var $currentoption 	= 'wpseo';
		var $ozhicon		= 'tag.png';
		
		function WPSEO_Admin() {
			$this->longname = __( 'WordPress SEO Configuration', 'wordpress-seo' );
			$this->shortname = __( 'SEO', 'wordpress-seo' );
			$this->multisite_defaults();
			add_action( 'init', array(&$this, 'init') );
		}
		
		function init() {
			if ( isset( $_GET['wpseo_reset_defaults']) ) {
				wpseo_reset_defaults();
				wp_redirect( admin_url('admin.php?page=wpseo_dashboard') );
			}
			
			if ( $this->grant_access() ) {
				add_action( 'admin_init', array(&$this, 'options_init') );
				add_action( 'admin_menu', array(&$this, 'register_settings_page') );
				add_action( 'network_admin_menu', array(&$this, 'register_network_settings_page') );

				add_filter( 'plugin_action_links', array(&$this, 'add_action_link'), 10, 2 );

				add_action( 'admin_print_scripts', array(&$this,'config_page_scripts'));
				add_action( 'admin_print_styles', array(&$this,'config_page_styles'));	
			}
				
			add_filter( 'ozh_adminmenu_icon', array(&$this, 'add_ozh_adminmenu_icon' ) );				
			
			add_action( 'show_user_profile', array(&$this,'wpseo_user_profile'));
			add_action( 'edit_user_profile', array(&$this,'wpseo_user_profile'));
			add_action( 'personal_options_update', array(&$this,'wpseo_process_user_option_update'));
			add_action( 'edit_user_profile_update', array(&$this,'wpseo_process_user_option_update'));

			add_filter( 'user_contactmethods', array(&$this,'add_google_plus_contactmethod'), 10, 1 );
			
			if ( '0' == get_option('blog_public') )
				add_action('admin_footer', array(&$this,'blog_public_warning'));				
		}

		function options_init() {
			register_setting( 'yoast_wpseo_options', 'wpseo' );
			register_setting( 'yoast_wpseo_permalinks_options', 'wpseo_permalinks' );
			register_setting( 'yoast_wpseo_titles_options', 'wpseo_titles' );
			register_setting( 'yoast_wpseo_rss_options', 'wpseo_rss' );
			register_setting( 'yoast_wpseo_internallinks_options', 'wpseo_internallinks' );
			register_setting( 'yoast_wpseo_xml_sitemap_options', 'wpseo_xml' );
			register_setting( 'yoast_wpseo_social_options', 'wpseo_social' );
			
			if ( function_exists('is_multisite') && is_multisite() )
				register_setting( 'yoast_wpseo_multisite_options', 'wpseo_multisite' );
		}
				
		function multisite_defaults() {
			$option = get_option('wpseo');
			if ( function_exists('is_multisite') && is_multisite() && !is_array($option) ) {
				$options = get_site_option('wpseo_ms');
				if ( is_array($options) && isset($options['defaultblog']) && !empty($options['defaultblog']) && $options['defaultblog'] != 0 ) {
					foreach ( get_wpseo_options_arr() as $option ) {
						update_option( $option, get_blog_option( $options['defaultblog'], $option) );
					}
				}
				$option['ms_defaults_set'] = true;
				update_option( 'wpseo', $option );
			}
		}
		
		function blog_public_warning() {
			if ( function_exists('is_network_admin') && is_network_admin() )
				return;
				
			$options = get_option('wpseo');
			if ( isset($options['ignore_blog_public_warning']) && $options['ignore_blog_public_warning'] == 'ignore' )
				return;
			echo "<div id='message' class='error'>";
			echo "<p><strong>".__( "Huge SEO Issue: You're blocking access to robots.", 'wordpress-seo' )."</strong> ".sprintf( __( "You must %sgo to your Privacy settings%s and set your blog visible to everyone.", 'wordpress-seo' ), "<a href='options-privacy.php'>", "</a>" )." <a href='javascript:wpseo_setIgnore(\"blog_public_warning\",\"message\",\"".wp_create_nonce('wpseo-ignore')."\");' class='button'>".__( "I know, don't bug me.", 'wordpress-seo' )."</a></p></div>";
		}
		
		function admin_sidebar() {
		?>
			<div class="postbox-container" style="width:25%;max-width:250px;">
				<div id="sidebar">	
						<?php
							$this->postbox('sitereview','<span class="promo">'.__('Improve your Site!','wordpress-seo').'</span>','<p>'.sprintf( __('Don\'t know where to start? Order a %1$swebsite review%2$s from Yoast!','wordpress-seo'), '<a href="http://yoast.com/hire-me/website-review/#utm_source=wpadmin&utm_medium=sidebanner&utm_term=link&utm_campaign=wpseoplugin">', '</a>').'</p>'.'<p><a class="button-primary" href="http://yoast.com/hire-me/website-review/#utm_source=wpadmin&utm_medium=sidebanner&utm_term=button&utm_campaign=wpseoplugin">'.__('Read more &raquo;','wordpress-seo').'</a></p>');
							$this->plugin_support();
							$this->postbox('donate','<span class="promo">'.__( 'Spread the Word!', 'wordpress-seo' ).'</span>','<p>'.__( 'Want to help make this plugin even better? All donations are used to improve this plugin, so donate $10, $20 or $50 now!', 'wordpress-seo' ).'</p><form action="https://www.paypal.com/cgi-bin/webscr" method="post">
							<input type="hidden" name="cmd" value="_s-xclick">
							<input type="hidden" name="hosted_button_id" value="83KQ269Q2SR82">
							<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit">
							</form>'
							.'<p>'.__('Or you could:','wordpress-seo').'</p>'
							.'<ul>'
							.'<li><a href="http://wordpress.org/extend/plugins/wordpress-seo/">'.__('Rate the plugin 5★ on WordPress.org','wordpress-seo').'</a></li>'
							.'<li><a href="http://yoast.com/wordpress/seo/#utm_source=wpadmin&utm_medium=sidebanner&utm_term=link&utm_campaign=wpseoplugin">'.__('Blog about it & link to the plugin page','wordpress-seo').'</a></li>'
							.'</ul>');
							$this->news(); 
						?>
					<br/><br/><br/>
				</div>
			</div>
		<?php
		}
		
		function admin_header($title, $expl = true, $form = true, $option = 'yoast_wpseo_options', $optionshort = 'wpseo', $contains_files = false) {
			?>
			<div class="wrap">
				<?php 
				if ( (isset($_GET['updated']) && $_GET['updated'] == 'true') || (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') ) {
					$msg = __('Settings updated', 'wordpress-seo' );

					if ( function_exists('w3tc_pgcache_flush') ) {
						w3tc_pgcache_flush();
						$msg .= __(' &amp; W3 Total Cache Page Cache flushed', 'wordpress-seo' );
					} else if (function_exists('wp_cache_clear_cache')) {
						wp_cache_clear_cache();
						$msg .= __(' &amp; WP Super Cache flushed', 'wordpress-seo' );
					}

					// flush rewrite rules if XML sitemap settings have been updated.
					if ( isset($_GET['page']) && 'wpseo_xml' == $_GET['page'] )
						flush_rewrite_rules();

					echo '<div id="message" style="width:94%;" class="message updated"><p><strong>'.$msg.'.</strong></p></div>';
				}  
				?>
				<a href="http://yoast.com/"><div id="yoast-icon" style="background: url(<?php echo WPSEO_URL; ?>images/wordpress-SEO-32x32.png) no-repeat;" class="icon32"><br /></div></a>
				<h2 id="wpseo-title"><?php _e("Yoast WordPress SEO: ", 'wordpress-seo' ); echo $title; ?></h2>
				<div id="wpseo_content_top" class="postbox-container" style="width:75%;">
					<div class="metabox-holder">	
						<div class="meta-box-sortables">
			<?php
			if ($form) {
				echo '<form action="'.admin_url('options.php').'" method="post" id="wpseo-conf"' . ($contains_files ? ' enctype="multipart/form-data"' : '') . '>';
				settings_fields($option); 
				$this->currentoption = $optionshort;
				// Set some of the ignore booleans here to prevent unsetting.
			}
			
		}
		
		function admin_footer($title, $submit = true) {
			if ($submit) {
			?>
							<div class="submit"><input type="submit" class="button-primary" name="submit" value="<?php _e("Save Settings", 'wordpress-seo'); ?>" /></div>
			<?php } ?>
							</form>
						</div>
					</div>
				</div>
				<?php $this->admin_sidebar(); ?>
			</div>				
			<?php
		}

		function replace_meta($old_metakey, $new_metakey, $replace = false) {
			global $wpdb;
			$oldies = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE meta_key = '$old_metakey'");
			foreach ($oldies as $old) {
				// Prevent inserting new meta values for posts that already have a value for that new meta key
				$check = $wpdb->get_var("SELECT count(*) FROM $wpdb->postmeta WHERE meta_key = '$new_metakey' AND post_id = ".$old->post_id);
				if ($check == 0)
					$wpdb->query("INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) VALUES (".$old->post_id.",'".$new_metakey."','".addslashes($old->meta_value)."')");
			}
			
			if ($replace) {
				$wpdb->query("DELETE FROM $wpdb->postmeta WHERE meta_key = '$old_metakey'");
			}
		}
		
		function delete_meta($metakey) {
			global $wpdb;
			$wpdb->query("DELETE FROM $wpdb->postmeta WHERE meta_key = '$metakey'");
		}
		
		function grant_access() {
			if ( !function_exists('is_multisite') || !is_multisite() )
				return true;
			
			$options = get_site_option('wpseo_ms');
			if ( !is_array( $options ) || !isset( $options['access'] ) )
				return true;
			
			if ( $options['access'] == 'superadmin' && !is_super_admin() )
				return false;
			
			return true;
		}
		
		function network_config_page() {
			$options = get_site_option('wpseo_ms');
			
			if ( isset( $_POST['wpseo_submit'] ) ) {
				foreach ( array('access', 'defaultblog') as $opt ) {
					$options[$opt] = $_POST['wpseo_ms'][$opt];
				}
				update_site_option('wpseo_ms', $options);
				echo '<div id="message" class="updated"><p>'.__('Settings Updated.', 'wordpress-seo' ).'</p></div>';
			}
			
			if ( isset( $_POST['wpseo_restore_blog'] ) ) {
				if ( isset( $_POST['wpseo_ms']['restoreblog'] ) && is_numeric( $_POST['wpseo_ms']['restoreblog'] ) ) {
					$blog = get_blog_details( $_POST['wpseo_ms']['restoreblog'] );
					if ( $blog ) {
						foreach ( get_wpseo_options_arr() as $option ) {
							$new_options = get_blog_option( $options['defaultblog'], $option );
							if ( count($new_options) > 0 )
								update_blog_option( $_POST['wpseo_ms']['restoreblog'], $option, $new_options );
						}
						echo '<div id="message" class="updated"><p>'.$blog->blogname.' '.__('restored to default SEO settings.', 'wordpress-seo' ).'</p></div>';
					}
				}
			}
			
			$this->admin_header('MultiSite Settings', false, false);
			
			$content = '<form method="post">';
			$content .= $this->select('access',__('Who should have access to the WordPress SEO settings', 'wordpress-seo' ), 
				array(
					'admin' => __( 'Site Admins (default)', 'wordpress-seo' ),
					'superadmin' => __( 'Super Admins only', 'wordpress-seo' )
				), 'wpseo_ms'
			);
			$content .= $this->textinput('defaultblog', __('New blogs get the SEO settings from this blog', 'wordpress-seo'), 'wpseo_ms' );
			$content .= '<p>'.__('Enter the Blog ID for the site whose settings you want to use as default for all sites that are added to your network. Leave empty for none.', 'wordpress-seo' ).'</p>';
			$content .= '<input type="submit" name="wpseo_submit" class="button-primary" value="'.__('Save MultiSite Settings', 'wordpress-seo' ).'"/>';
			$content .= '</form>';

			$this->postbox('wpseo_export',__('MultiSite Settings', 'wordpress-seo'),$content); 
			
			$content = '<form method="post">';
			$content .= '<p>'.__( 'Using this form you can reset a site to the default SEO settings.', 'wordpress-seo'  ).'</p>';
			$content .= $this->textinput( 'restoreblog', __('Blog ID', 'wordpress-seo'), 'wpseo_ms' );
			$content .= '<input type="submit" name="wpseo_restore_blog" value="'.__('Restore site to defaults', 'wordpress-seo' ).'" class="button"/>';
			$content .= '</form>';

			$this->postbox('wpseo_export',__('Restore site to default settings', 'wordpress-seo'),$content); 
			
			$this->admin_footer(__( 'Restore to Default', 'wordpress-seo' ), false);
		}
		
		function import_page() {
			$msg = '';
			if ( isset($_POST['import']) ) {
				global $wpdb;
				$msg 		= '';
				$replace 	= false;
				$deletekw	= false;
				
				if (isset($_POST['wpseo']['deleteolddata']) && $_POST['wpseo']['deleteolddata'] == 'on') {
					$replace = true;
				}
				if ( isset($_POST['wpseo']['importheadspace']) ) {
					$this->replace_meta('_headspace_description', '_yoast_wpseo_metadesc', $replace);
					$this->replace_meta('_headspace_keywords', '_yoast_wpseo_metakeywords', $replace);
					$this->replace_meta('_headspace_page_title', '_yoast_wpseo_title', $replace);
					$this->replace_meta('_headspace_noindex', '_yoast_wpseo_meta-robots-noindex', $replace);
					$this->replace_meta('_headspace_nofollow', '_yoast_wpseo_meta-robots-nofollow', $replace);

					$posts = $wpdb->get_results("SELECT ID FROM $wpdb->posts");
					foreach ($posts as $post) {
						$custom = get_post_custom($post->ID);
						$robotsmeta_adv = '';
						if (isset($custom['_headspace_noarchive'])) {
							$robotsmeta_adv .= 'noarchive,';
						}
						if (isset($custom['_headspace_noodp'])) {
							$robotsmeta_adv .= 'noodp,';
						}
						if (isset($custom['_headspace_noydir'])) {
							$robotsmeta_adv .= 'noydir';
						}
						$robotsmeta_adv = preg_replace('/,$/','',$robotsmeta_adv);
						wpseo_set_value('meta-robots-adv', $robotsmeta_adv, $post->ID);
						
						if ($replace) {
							foreach (array('noindex','nofollow','noarchive','noodp','noydir') as $meta) {
								delete_post_meta($post->ID, '_headspace_'.$meta);
							}
						}
					}
					$msg .= '<p>HeadSpace2 data successfully imported.</p>';
				} 
				if ( isset($_POST['wpseo']['importaioseo']) ) {
					$this->replace_meta('_aioseop_description', '_yoast_wpseo_metadesc', $replace);
					$this->replace_meta('_aioseop_keywords', '_yoast_wpseo_metakeywords', $replace);
					$this->replace_meta('_aioseop_title', '_yoast_wpseo_title', $replace);
					$msg .= '<p>'.__( 'All in One SEO data successfully imported.', 'wordpress-seo' ).'</p>';
				}
				if ( isset($_POST['wpseo']['importaioseoold']) ) {
					$this->replace_meta('description', '_yoast_wpseo_metadesc', $replace);
					$this->replace_meta('keywords', '_yoast_wpseo_metakeywords', $replace);
					$this->replace_meta('title', '_yoast_wpseo_title', $replace);
					$msg .= '<p>'.__('All in One SEO (Old version) data successfully imported.', 'wordpress-seo' ).'</p>';
				}
				if ( isset($_POST['wpseo']['importrobotsmeta']) ) {
					$posts = $wpdb->get_results("SELECT ID, robotsmeta FROM $wpdb->posts");
					foreach ($posts as $post) {
						if ( strpos($post->robotsmeta, 'noindex') !== false )
							wpseo_set_value('meta-robots-noindex', true, $post->ID);

						if ( strpos($post->robotsmeta, 'nofollow') !== false )
							wpseo_set_value('meta-robots-nofollow', true, $post->ID);
					}
					$msg .= '<p>'.__('Robots Meta values imported.', 'wordpress-seo' ).'</p>';
				}
				if ( isset($_POST['wpseo']['importrssfooter']) ) {
					$optold = get_option( 'RSSFooterOptions' );
					$optnew = get_option( 'wpseo_rss' );
					if ($optold['position'] == 'after') {
						if ( empty($optnew['rssafter']) )
							$optnew['rssafter'] = $optold['footerstring'];
					} else {
						if ( empty($optnew['rssbefore']) )
							$optnew['rssbefore'] = $optold['footerstring'];						
					}
					update_option( 'wpseo_rss', $optnew );
					$msg .= '<p>'.__('RSS Footer options imported successfully.', 'wordpress-seo' ).'</p>';
				}
				if ( isset($_POST['wpseo']['importbreadcrumbs']) ) {
					$optold = get_option( 'yoast_breadcrumbs' );
					$optnew = get_option( 'wpseo_internallinks' );

					if (is_array($optold)) {
						foreach ($optold as $opt => $val) {
							if (is_bool($val) && $val == true)
								$optnew['breadcrumbs-'.$opt] = 'on';
							else
								$optnew['breadcrumbs-'.$opt] = $val;
						}						
						update_option( 'wpseo_internallinks', $optnew );
						$msg .= '<p>'.__('Yoast Breadcrumbs options imported successfully.', 'wordpress-seo' ).'</p>';
					} else {
						$msg .= '<p>'.__('Yoast Breadcrumbs options could not be found', 'wordpress-seo' ).'</p>';
					}
				}
				if ($replace)
					$msg .= __(', and old data deleted', 'wordpress-seo' );
				if ($deletekw)
					$msg .= __(', and meta keywords data deleted', 'wordpress-seo' );
			}
			
			$this->admin_header('Import', false, false);
			if ($msg != '')
				echo '<div id="message" class="message updated" style="width:94%;">'.$msg.'</div>';
				
			$content = "<p>".__("No doubt you've used an SEO plugin before if this site isn't new. Let's make it easy on you, you can import the data below. If you want, you can import first, check if it was imported correctly, and then import &amp; delete. No duplicate data will be imported.", 'wordpress-seo' )."</p>";
			$content .= '<p>'.sprintf( __("If you've used another SEO plugin, try the %sSEO Data Transporter%s plugin to move your data into this plugin, it rocks!", 'wordpress-seo' ), "<a href='http://wordpress.org/extend/plugins/seo-data-transporter/'>", "</a>" ).'</p>';
			$content .= '<form action="" method="post">';
			$content .= $this->checkbox('importheadspace',__('Import from HeadSpace2?', 'wordpress-seo'));
			$content .= $this->checkbox('importaioseo',__('Import from All-in-One SEO?', 'wordpress-seo'));
			$content .= $this->checkbox('importaioseoold',__('Import from OLD All-in-One SEO?', 'wordpress-seo'));
			$content .= '<br/>';
			$content .= $this->checkbox('deleteolddata',__('Delete the old data after import? (recommended)', 'wordpress-seo'));
			$content .= '<input type="submit" class="button-primary" name="import" value="'.__( 'Import', 'wordpress-seo' ).'" />';
			$content .= '<br/><br/>';
			$content .= '<form action="" method="post">';
			$content .= '<h2>'.__( 'Import settings from other plugins', 'wordpress-seo' ).'</h2>';
			$content .= $this->checkbox('importrobotsmeta',__('Import from Robots Meta (by Yoast)?', 'wordpress-seo'));
			$content .= $this->checkbox('importrssfooter',__('Import from RSS Footer (by Yoast)?', 'wordpress-seo'));
			$content .= $this->checkbox('importbreadcrumbs',__('Import from Yoast Breadcrumbs?', 'wordpress-seo'));
			$content .= '<input type="submit" class="button-primary" name="import" value="'.__( 'Import', 'wordpress-seo' ).'" />';			
			$content .= '</form>';			
			
			$this->postbox('import',__('Import', 'wordpress-seo'),$content); 
			
			do_action('wpseo_import', $this);

			$content = '</form>';
			$content .= '<strong>'.__( 'Export', 'wordpress-seo' ).'</strong><br/>';
			$content .= '<form method="post">';
			$content .= '<p>'.__('Export your WordPress SEO settings here, to import them again later or to import them on another site.', 'wordpress-seo' ).'</p>';
			if ( phpversion() > 5.2 )
				$content .= $this->checkbox('include_taxonomy_meta', __('Include Taxonomy Metadata', 'wordpress-seo' ));
			$content .= '<input type="submit" class="button" name="wpseo_export" value="'.__('Export settings', 'wordpress-seo' ).'"/>';
			$content .= '</form>';
			if ( isset($_POST['wpseo_export']) ) {
				$include_taxonomy = false;
				if ( isset($_POST['wpseo']['include_taxonomy_meta']) )
					$include_taxonomy = true;
				$url = wpseo_export_settings( $include_taxonomy );
				if ($url) {
					$content .= '<script type="text/javascript">
						document.location = \''.$url.'\';
					</script>';
				} else {
					$content .= 'Error: '.$url;
				}
			}
			
			$content .= '<br class="clear"/><br/><strong>'.__( 'Import', 'wordpress-seo' ).'</strong><br/>';
			if ( !isset($_FILES['settings_import_file']) || empty($_FILES['settings_import_file']) ) {
				$content .= '<p>'.__('Import settings by locating <em>settings.zip</em> and clicking', 'wordpress-seo' ).' "'.__('Import settings', 'wordpress-seo' ).'":</p>';
				$content .= '<form method="post" enctype="multipart/form-data">';
				$content .= '<input type="file" name="settings_import_file"/>';
				$content .= '<input type="hidden" name="action" value="wp_handle_upload"/>';
				$content .= '<input type="submit" class="button" value="'.__('Import settings', 'wordpress-seo' ).'"/>';
				$content .= '</form>';
			} else {
				$file = wp_handle_upload($_FILES['settings_import_file']);
				
				if ( isset( $file['file'] ) && !is_wp_error($file) ) {
					require_once (ABSPATH . 'wp-admin/includes/class-pclzip.php');
					$zip = new PclZip( $file['file'] );
					$unzipped = $zip->extract( $p_path = WP_CONTENT_DIR.'/wpseo-import/' );
					if ( $unzipped[0]['stored_filename'] == 'settings.ini' ) {
						$options = parse_ini_file( WP_CONTENT_DIR.'/wpseo-import/settings.ini', true );
						foreach ($options as $name => $optgroup) {
							if ($name != 'wpseo_taxonomy_meta') {
								update_option($name, $optgroup);
							} else {
								update_option($name, json_decode( urldecode( $optgroup['wpseo_taxonomy_meta'] ), true ) );
							}
						}
						@unlink( WP_CONTENT_DIR.'/wpseo-import/' );
						
						$content .= '<p><strong>'.__('Settings successfully imported.', 'wordpress-seo' ).'</strong></p>';
					} else {
						$content .= '<p><strong>'.__('Settings could not be imported:', 'wordpress-seo' ).' '.__('Unzipping failed.', 'wordpress-seo' ).'</strong></p>';
					}
				} else {
					if ( is_wp_error($file) )
						$content .= '<p><strong>'.__('Settings could not be imported:', 'wordpress-seo' ).' '.$file['error'].'</strong></p>';
					else
						$content .= '<p><strong>'.__('Settings could not be imported:', 'wordpress-seo' ).' '.__('Upload failed.', 'wordpress-seo' ).'</strong></p>';
				}
			}
			$this->postbox('wpseo_export',__('Export & Import SEO Settings', 'wordpress-seo'),$content); 
			
			$this->admin_footer('Import', false);
		}

		function titles_page() {
			
			$options = get_option('wpseo_titles');
			
			?>
			<div class="wrap">
				<?php 
				
				if ( (isset($_GET['updated']) && $_GET['updated'] == 'true') || (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') ) {
					$msg = __('Settings updated', 'wordpress-seo' );

					if ( function_exists('w3tc_pgcache_flush') ) {
						w3tc_pgcache_flush();
						$msg .= __(' &amp; W3 Total Cache Page Cache flushed', 'wordpress-seo' );
					} else if (function_exists('wp_cache_clear_cache')) {
						wp_cache_clear_cache();
						$msg .= __(' &amp; WP Super Cache flushed', 'wordpress-seo' );
					}

					echo '<div id="message" style="width:94%;" class="message updated"><p><strong>'.$msg.'.</strong></p></div>';
				}
				
				?>
				<a href="http://yoast.com/"><div id="top yoast-icon" style="background: url(<?php echo WPSEO_URL; ?>images/wordpress-SEO-32x32.png) no-repeat;" class="icon32"><br /></div></a>
				<h2 id="wpseo-title"><?php _e("Yoast WordPress SEO: Titles &amp; Metas", 'wordpress-seo' ); ?></h2>
				<div id="wpseo_content_top" class="postbox-container" style="width:75%;">
					<div class="metabox-holder">	
						<div class="meta-box-sortables">
							
							<h2 class="nav-tab-wrapper" id="wpseo-tabs">
								<a class="nav-tab" id="general-tab" href="#top#general">General</a>
								<a class="nav-tab" id="home-tab" href="#top#home">Home</a>
								<a class="nav-tab" id="post_types-tab" href="#top#post_types">Post Types</a>
								<a class="nav-tab" id="taxonomies-tab" href="#top#taxonomies">Taxonomies</a>
								<a class="nav-tab" id="archives-tab" href="#top#archives">Other</a>
								<a class="nav-tab" id="template_help-tab" href="#top#template_help">Help</a>
							</h2>
							
							<div id="general" class="wpseotab">
			<?php
				echo '<form action="'.admin_url('options.php').'" method="post" id="wpseo-conf">';
				settings_fields('yoast_wpseo_titles_options'); 
				$this->currentoption = 'wpseo_titles';	
			
				echo '<h2>'.__('Title settings','wordpress-seo').'</h2>';
				echo $this->checkbox( 'forcerewritetitle', __('Force rewrite titles', 'wordpress-seo') );
				echo '<p class="desc">'.__('WordPress SEO has auto-detected whether it needs to force rewrite the titles for your pages, if you think it\'s wrong and you know what you\'re doing, you can change the setting here.','wordpress-seo').'</p>';
								
				echo '<h2>'.__('Sitewide <code>meta</code> settings','wordpress-seo').'</h2>';
				echo $this->checkbox( 'noindex-subpages', __('Noindex subpages of archives', 'wordpress-seo') );
				echo '<p class="desc">'.__('If you want to prevent /page/2/ and further of any archive to show up in the search results, enable this.').'</p>';
				
				echo $this->checkbox( 'usemetakeywords', __( 'Use <code>meta</code> keywords tag?', 'wordpress-seo' ) );
				echo '<p class="desc">'.__('I don\'t know why you\'d want to use meta keywords, but if you want to, check this box.').'</p>';
				
				echo $this->checkbox( 'noodp', __('Add <code>noodp</code> meta robots tag sitewide', 'wordpress-seo' ) );
				echo '<p class="desc">'.__('Prevents search engines from using the DMOZ description for pages from this site in the search results.', 'wordpress-seo').'</p>';
				
				echo $this->checkbox( 'noydir', __('Add <code>noydir</code> meta robots tag sitewide', 'wordpress-seo' ) );
				echo '<p class="desc">'.__('Prevents search engines from using the Yahoo! directory description for pages from this site in the search results.', 'wordpress-seo').'</p>';
			
				echo '<h2>'.__('Clean up the <code>&lt;head&gt;</code>', 'wordpress-seo' ).'</h2>';
				echo $this->checkbox('hide-rsdlink',__('Hide RSD Links','wordpress-seo'));
				echo $this->checkbox('hide-wlwmanifest',__('Hide WLW Manifest Links','wordpress-seo'));
				echo $this->checkbox('hide-shortlink',__('Hide Shortlink for posts','wordpress-seo'));
				echo $this->checkbox('hide-feedlinks',__('Hide RSS Links','wordpress-seo'));
			?>
				</div>
				<div id="home" class="wpseotab">
			<?php
			if ( 'page' != get_option('show_on_front') ) {
				echo '<h2>'.__('Homepage', 'wordpress-seo' ).'</h2>';
				echo $this->textinput('title-home',__('Title template', 'wordpress-seo' ));
				echo $this->textarea('metadesc-home',__('Meta description template', 'wordpress-seo' ), '', 'metadesc');
				if ( isset($options['usemetakeywords']) && $options['usemetakeywords'] )
					echo $this->textinput('metakey-home',__('Meta keywords template', 'wordpress-seo' ));
			} else {
				echo '<h2>'.__('Homepage &amp; Front page', 'wordpress-seo' ).'</h2>';
				echo '<p>'.sprintf( __('You can determine the title and description for the front page by %sediting the front page itself &raquo;%s', 'wordpress-seo' ), '<a href="'.get_edit_post_link( get_option('page_on_front') ).'">', '</a>') . '</p>';
				if ( is_numeric( get_option('page_for_posts') ) )
				echo '<p>' . sprintf( __('You can determine the title and description for the blog page by %sediting the blog page itself &raquo;%s', 'wordpress-seo' ), '<a href="'.get_edit_post_link( get_option('page_for_posts') ).'">', '</a>' ) . '</p>';
			}
			
			if ( 'page' != get_option('show_on_front') ) {
				echo '<h2>'.__('Author metadata', 'wordpress-seo' ).'</h2>';
				echo '<label class="select" for="">'.__('Author highlighting','wordpress-seo').':</label>';
				wp_dropdown_users( array( 'show_option_none' => "Don't show", 'name' => 'wpseo_titles[plus-author]', 'class' => 'select','selected' => isset($options['plus-author']) ? $options['plus-author'] : '' ) );
				echo '<p class="desc label">'.__('Choose the user that should be used for the <code>rel="author"</code> on the homepage. Make sure the user has filled out his/her Google+ profile link on their profile page.', 'wordpress-seo' ).'</p>';
			}
			
			?>
				</div>
				<div id="post_types" class="wpseotab">
			<?php
			foreach ( get_post_types( array('public' => true ), 'objects' ) as $posttype) {
				if (isset($options['redirectattachment']) && $options['redirectattachment'] && $posttype == 'attachment')
					continue;
				$name = $posttype->name;
				echo '<h4 id="'.$name.'">'.ucfirst($posttype->labels->name).'</h4>';
				echo $this->textinput('title-'.$name,__('Title template', 'wordpress-seo' ));
				echo $this->textarea('metadesc-'.$name,__('Meta description template', 'wordpress-seo' ), '', 'metadesc');
				if ( isset($options['usemetakeywords']) && $options['usemetakeywords'] )
					echo $this->textinput('metakey-'.$name,__('Meta keywords template', 'wordpress-seo' ));
				echo $this->checkbox('noindex-'.$name, '<code>noindex, follow</code>', __('Meta Robots','wordpress-seo') );
				echo $this->checkbox('hideeditbox-'.$name, __('Hide','wordpress-seo'), __('WordPress SEO Meta Box','wordpress-seo') );
				echo '<br/>';
			}

			echo '<h2>'.__('Custom Post Type Archives', 'wordpress-seo' ).'</h2>';
			echo '<p>'.__('Note: instead of templates these are the actual titles and meta descriptions for these custom post type archive pages.', 'wordpress-seo' ).'</p>';

			foreach ( get_post_types( array('public' => true, '_builtin' => false ), 'objects' ) as $pt ) {
				if ( !$pt->has_archive )
					continue;
				
				$name = $pt->name;
				
				echo '<h4>'.ucfirst($pt->labels->name).'</h4>';
				echo $this->textinput( 'title-ptarchive-' . $name, __('Title', 'wordpress-seo' ) );
				echo $this->textarea( 'metadesc-ptarchive-' . $name, __('Meta description', 'wordpress-seo' ), '', 'metadesc' );
				if ( isset($options['breadcrumbs-enable']) && $options['breadcrumbs-enable'] )
					echo $this->textinput( 'bctitle-ptarchive-' . $name, __('Breadcrumbs Title', 'wordpress-seo' ) );
				echo $this->checkbox('noindex-ptarchive-'.$name,'<code>noindex, follow</code>', __('Meta Robots','wordpress-seo') );
			}
			unset($pt, $post_type);			
			
			?>
				</div>
				<div id="taxonomies" class="wpseotab">
			<?php
			foreach ( get_taxonomies( array( 'public' => true ), 'objects' ) as $tax ) {
				echo '<h4>'.$tax->labels->name.'</h4>';
				echo $this->textinput( 'title-'.$tax->name, __('Title template', 'wordpress-seo' ) );
				echo $this->textarea( 'metadesc-'.$tax->name, __('Meta description template', 'wordpress-seo' ), '', 'metadesc' );
				if ( isset( $options['usemetakeywords']) && $options['usemetakeywords'] )
					echo $this->textinput( 'metakey-'.$tax->name, __('Meta keywords template', 'wordpress-seo' ) );
				echo $this->checkbox( 'noindex-'.$tax->name, '<code>noindex, follow</code>', __('Meta Robots','wordpress-seo') );
				echo $this->checkbox( 'tax-hideeditbox-'.$tax->name, __('Hide','wordpress-seo'), __('WordPress SEO Meta Box','wordpress-seo') );
				echo '<br/>';
			}
			
			?>
				</div>
				<div id="archives" class="wpseotab">
			<?php
				echo '<h4>'.__('Author Archives', 'wordpress-seo').'</h4>';
				echo $this->textinput('title-author',__('Title template', 'wordpress-seo' ));
				echo $this->textarea('metadesc-author',__('Meta description template', 'wordpress-seo' ), '', 'metadesc' );
				if ( isset($options['usemetakeywords']) && $options['usemetakeywords'] )
					echo $this->textinput('metakey-author',__('Meta keywords template', 'wordpress-seo' ));
				echo $this->checkbox('noindex-author','<code>noindex, follow</code>', __('Meta Robots','wordpress-seo') );
				echo $this->checkbox('disable-author',__('Disable the author archives', 'wordpress-seo'), '' );
				echo '<p class="desc label">'.__('If you\'re running a one author blog, the author archive will always look exactly the same as your homepage. And even though you may not link to it, others might, to do you harm. Disabling them here will make sure any link to those archives will be 301 redirected to the homepage.', 'wordpress-seo').'</p>';
				echo '<br/>';
				echo '<h4>'.__('Date Archives', 'wordpress-seo' ).'</h4>';
				echo $this->textinput('title-archive',__('Title template', 'wordpress-seo' ));
				echo $this->textarea('metadesc-archive',__('Meta description template', 'wordpress-seo' ), '', 'metadesc' );
				echo '<br/>';
				echo $this->checkbox('noindex-archive','<code>noindex, follow</code>', __('Meta Robots','wordpress-seo') );
				echo $this->checkbox('disable-date',__('Disable the date-based archives', 'wordpress-seo'), '' );
				echo '<p class="desc label">'.__('For the date based archives, the same applies: they probably look a lot like your homepage, and could thus be seen as duplicate content.', 'wordpress-seo').'</p>';

				echo '<h2>'.__('Special Pages', 'wordpress-seo' ).'</h2>';
				echo '<p>'.__('These pages will be noindex, followed by default, so they will never show up in search results.').'</p>';
				echo '<h4>'.__('Search pages', 'wordpress-seo' ).'</h4>';
				echo $this->textinput('title-search',__('Title template', 'wordpress-seo') );
				echo '<h4>'.__('404 pages', 'wordpress-seo' ).'</h4>';
				echo $this->textinput('title-404',__('Title template', 'wordpress-seo' ) );
				echo '<br class="clear"/>';
			?>
			</div>
			<div id="template_help" class="wpseotab">
			<?php
			$content = '
				<p>'.__( 'These tags can be included in templates and will be replaced by WordPress SEO by Yoast when a page is displayed.', 'wordpress-seo' ).'</p>
					<table class="yoast_help">
						<tr>
							<th>%%date%%</th>
							<td>'.__( 'Replaced with the date of the post/page', 'wordpress-seo' ).'</td>
						</tr>
						<tr class="alt">
							<th>%%title%%</th>
							<td>'.__('Replaced with the title of the post/page', 'wordpress-seo' ).'</td>
						</tr>
						<tr>
							<th>%%sitename%%</th>
							<td>'.__('The site\'s name', 'wordpress-seo' ).'</td>
						</tr>
						<tr class="alt">
							<th>%%sitedesc%%</th>
							<td>'.__('The site\'s tagline / description', 'wordpress-seo' ).'</td>
						</tr>
						<tr>
							<th>%%excerpt%%</th>
							<td>'.__('Replaced with the post/page excerpt (or auto-generated if it does not exist)', 'wordpress-seo' ).'</td>
						</tr>
						<tr class="alt">
							<th>%%excerpt_only%%</th>
							<td>'.__('Replaced with the post/page excerpt (without auto-generation)', 'wordpress-seo' ).'</td>
						</tr>
						<tr>
							<th>%%tag%%</th>
							<td>'.__('Replaced with the current tag/tags', 'wordpress-seo' ).'</td>
						</tr>
						<tr class="alt">
							<th>%%category%%</th>
							<td>'.__('Replaced with the post categories (comma separated)', 'wordpress-seo' ).'</td>
						</tr>
						<tr>
							<th>%%category_description%%</th>
							<td>'.__('Replaced with the category description', 'wordpress-seo' ).'</td>
						</tr>
						<tr class="alt">
							<th>%%tag_description%%</th>
							<td>'.__('Replaced with the tag description', 'wordpress-seo' ).'</td>
						</tr>
						<tr>
							<th>%%term_description%%</th>
							<td>'.__('Replaced with the term description', 'wordpress-seo' ).'</td>
						</tr>
						<tr class="alt">
							<th>%%term_title%%</th>
							<td>'.__('Replaced with the term name', 'wordpress-seo' ).'</td>
						</tr>
						<tr>
							<th>%%modified%%</th>
							<td>'.__('Replaced with the post/page modified time', 'wordpress-seo' ).'</td>
						</tr>
						<tr class="alt">
							<th>%%id%%</th>
							<td>'.__('Replaced with the post/page ID', 'wordpress-seo' ).'</td>
						</tr>
						<tr>
							<th>%%name%%</th>
							<td>'.__('Replaced with the post/page author\'s \'nicename\'', 'wordpress-seo' ).'</td>
						</tr>
						<tr class="alt">
							<th>%%userid%%</th>
							<td>'.__('Replaced with the post/page author\'s userid', 'wordpress-seo' ).'</td>
						</tr>
						<tr>
							<th>%%searchphrase%%</th>
							<td>'.__('Replaced with the current search phrase', 'wordpress-seo' ).'</td>
						</tr>
						<tr class="alt">
							<th>%%currenttime%%</th>
							<td>'.__('Replaced with the current time', 'wordpress-seo' ).'</td>
						</tr>
						<tr>
							<th>%%currentdate%%</th>
							<td>'.__('Replaced with the current date', 'wordpress-seo' ).'</td>
						</tr>
						<tr class="alt">
							<th>%%currentmonth%%</th>
							<td>'.__('Replaced with the current month', 'wordpress-seo' ).'</td>
						</tr>
						<tr>
							<th>%%currentyear%%</th>
							<td>'.__('Replaced with the current year', 'wordpress-seo' ).'</td>
						</tr>
						<tr class="alt">
							<th>%%page%%</th>
							<td>'.__('Replaced with the current page number (i.e. page 2 of 4)', 'wordpress-seo' ).'</td>
						</tr>
						<tr>
							<th>%%pagetotal%%</th>
							<td>'.__('Replaced with the current page total', 'wordpress-seo' ).'</td>
						</tr>
						<tr class="alt">
							<th>%%pagenumber%%</th>
							<td>'.__('Replaced with the current page number', 'wordpress-seo' ).'</td>
						</tr>
						<tr>
							<th>%%caption%%</th>
							<td>'.__('Attachment caption', 'wordpress-seo' ).'</td>
						</tr>
						<tr class="alt">
							<th>%%focuskw%%</th>
							<td>'.__('Replaced with the posts focus keyword', 'wordpress-seo' ).'</td>
						</tr>
						<tr>
							<th>%%cf_&lt;custom-field-name&gt;%%</th>
							<td>'.__('Replaced with a posts custom field value', 'wordpress-seo' ).'</td>
						</tr>
						<tr class="alt">
							<th>%%ct_&lt;custom-tax-name&gt;%%</th>
							<td>'.__('Replaced with a posts custom taxonomies, comma separated.', 'wordpress-seo' ).'</td>
						</tr>
						<tr>
							<th>%%ct_desc_&lt;custom-tax-name&gt;%%</th>
							<td>'.__('Replaced with a custom taxonomoies description', 'wordpress-seo' ).'</td>
						</tr>
						<tr class="alt">
							<th>%%sep%%</th>
							<td>'.__('The separator defined in your theme\'s <code>wp_title()</code> tag.', 'wordpress-seo' ).'</td>
						</tr>
					</table>';

			echo '<h2>'.__('Variables', 'wordpress-seo').'</h2>';
			echo $content;
			?>
			</div>
			<?php
			$this->admin_footer('Metas');
		}
				
		function settings_advice_page() {
			$this->admin_header(__( 'Settings Advice', 'wordpress-seo' ), false, true, 'yoast_wpseo_advice_options', 'wpseo_advice');
		}
		
		function permalinks_page() {
			if ( isset( $_GET['settings-updated'] ) ) {
				delete_option('rewrite_rules');
			}
			
			$this->admin_header(__( 'Permalinks', 'wordpress-seo' ), true, true, 'yoast_wpseo_permalinks_options', 'wpseo_permalinks');
			$content = $this->checkbox('stripcategorybase',__('Strip the category base (usually <code>/category/</code>) from the category URL.', 'wordpress-seo' ));
			$content .= $this->checkbox('trailingslash',__('Enforce a trailing slash on all category and tag URL\'s', 'wordpress-seo' ));
			$content .= '<p class="desc">'.__('If you choose a permalink for your posts with <code>.html</code>, or anything else but a / on the end, this will force WordPress to add a trailing slash to non-post pages nonetheless.', 'wordpress-seo').'</p>';

			$content .= $this->checkbox('redirectattachment',__('Redirect attachment URL\'s to parent post URL.', 'wordpress-seo' ));
			$content .= '<p class="desc">'.__('Attachments to posts are stored in the database as posts, this means they\'re accessible under their own URL\'s if you do not redirect them, enabling this will redirect them to the post they were attached to.', 'wordpress-seo').'</p>';

			$content .= $this->checkbox('cleanpermalinks',__('Redirect ugly URL\'s to clean permalinks. (Not recommended in many cases!)', 'wordpress-seo' ));
			$content .= '<p class="desc">'.__('People make mistakes in their links towards you sometimes, or unwanted parameters are added to the end of your URLs, this allows you to redirect them all away. Please note that while this is a feature that is actively maintained, it is known to break several plugins, and should for that reason be the first feature you disable when you encounter issues after installing this plugin.', 'wordpress-seo').'</p>';

			$this->postbox('permalinks',__('Permalink Settings', 'wordpress-seo'),$content); 
			
			$content = $this->select('force_transport', __( 'Force Transport', 'wordpress-seo' ), array('default' => __( 'Leave default', 'wordpress-seo' ), 'http' => __( 'Force http', 'wordpress-seo' ), 'https' => __( 'Force https', 'wordpress-seo' )));			
			$content .= '<p class="desc label">'.__('Force the canonical to either http or https, when your blog runs under both.', 'wordpress-seo').'</p>';

			$this->postbox('canonical',__('Canonical Settings', 'wordpress-seo'),$content); 

			$content = $this->checkbox('cleanpermalink-googlesitesearch',__('Prevent cleaning out Google Site Search URL\'s.', 'wordpress-seo' ));
			$content .= '<p class="desc">'.__('Google Site Search URL\'s look weird, and ugly, but if you\'re using Google Site Search, you probably do not want them cleaned out.', 'wordpress-seo').'</p>';

			$content .= $this->checkbox('cleanpermalink-googlecampaign',__('Prevent cleaning out Google Analytics Campaign Parameters.', 'wordpress-seo' ));
			$content .= '<p class="desc">'.__('If you use Google Analytics campaign parameters starting with <code>?utm_</code>, check this box. You shouldn\'t use these btw, you should instead use the hash tagged version instead.', 'wordpress-seo').'</p>';

			$content .= $this->textinput('cleanpermalink-extravars',__('Other variables not to clean', 'wordpress-seo' ));
			$content .= '<p class="desc">'.__('You might have extra variables you want to prevent from cleaning out, add them here, comma separarted.', 'wordpress-seo').'</p>';
			
			$this->postbox('cleanpermalinksdiv',__('Clean Permalink Settings', 'wordpress-seo'),$content); 
			
			$this->admin_footer('Permalinks');
		}
		
		function internallinks_page() {
			$this->admin_header(__('Internal Links','wordpress-seo'), false, true, 'yoast_wpseo_internallinks_options', 'wpseo_internallinks');

			$content = $this->checkbox('breadcrumbs-enable',__('Enable Breadcrumbs', 'wordpress-seo' ));
			$content .= '<br/>';
			$content .= $this->textinput('breadcrumbs-sep',__('Separator between breadcrumbs', 'wordpress-seo' ));
			$content .= $this->textinput('breadcrumbs-home',__('Anchor text for the Homepage', 'wordpress-seo' ));
			$content .= $this->textinput('breadcrumbs-prefix',__('Prefix for the breadcrumb path', 'wordpress-seo' ));
			$content .= $this->textinput('breadcrumbs-archiveprefix',__('Prefix for Archive breadcrumbs', 'wordpress-seo' ));
			$content .= $this->textinput('breadcrumbs-searchprefix',__('Prefix for Search Page breadcrumbs', 'wordpress-seo' ));
			$content .= $this->textinput('breadcrumbs-404crumb',__('Breadcrumb for 404 Page', 'wordpress-seo' ));
			$content .= $this->checkbox('breadcrumbs-blog-remove',__('Remove Blog page from Breadcrumbs', 'wordpress-seo' ));
			$content .= '<br/><br/>';
			$content .= '<strong>'.__('Taxonomy to show in breadcrumbs for:', 'wordpress-seo' ).'</strong><br/>';
			foreach ( get_post_types( array('public' => true ), 'objects' ) as $pt) {				
				$taxonomies = get_object_taxonomies( $pt->name, 'objects' );
				if (count($taxonomies) > 0) {
					$values = array(0 => __('None','wordpress-seo') );
					foreach ( $taxonomies as $tax ) {
						$values[$tax->name] = $tax->labels->singular_name;
					}
					$content .= $this->select('post_types-'.$pt->name.'-maintax', $pt->labels->name, $values);					
				}
			}
			$content .= '<br/>';
			
			$content .= '<strong>'.__('Post type archive to show in breadcrumbs for:', 'wordpress-seo' ).'</strong><br/>';
			foreach (get_taxonomies(array('public'=>true, '_builtin' => false), 'objects') as $tax ) {
				$values = array( '' => __( 'None', 'wordpress-seo' ) );
				if ( get_option('show_on_front') == 'page' )
					$values['post'] = __( 'Blog', 'wordpress-seo' );
				
				foreach (get_post_types( array('public' => true), 'objects' ) as $pt) {
					if ($pt->has_archive)
						$values[$pt->name] = $pt->labels->name;
				}
				$content .= $this->select('taxonomy-'.$tax->name.'-ptparent', $tax->labels->singular_name, $values);					
			}
			
			$content .= $this->checkbox('breadcrumbs-boldlast',__('Bold the last page in the breadcrumb', 'wordpress-seo' ));

			$content .= '<br class="clear"/>';
			$content .= '<h4>'.__('How to insert breadcrumbs in your theme', 'wordpress-seo' ).'</h4>';
			$content .= '<p>'.__('Usage of this breadcrumbs feature is explained <a href="http://yoast.com/wordpress/breadcrumbs/">here</a>. For the more code savvy, insert this in your theme:', 'wordpress-seo' ).'</p>';
			$content .= '<pre>&lt;?php if ( function_exists(&#x27;yoast_breadcrumb&#x27;) ) {
	yoast_breadcrumb(&#x27;&lt;p id=&quot;breadcrumbs&quot;&gt;&#x27;,&#x27;&lt;/p&gt;&#x27;);
} ?&gt;</pre>';
			$this->postbox('internallinks',__('Breadcrumbs Settings', 'wordpress-seo'),$content); 
			
			$this->admin_footer('Internal Links');
		}
				
		function files_page() {
			if ( isset($_POST['submitrobots']) ) {
				if (!current_user_can('manage_options')) die(__('You cannot edit the robots.txt file.', 'wordpress-seo'));
				
				check_admin_referer('wpseo-robotstxt');
				
				if (file_exists( get_home_path()."robots.txt") ) {
					$robots_file = get_home_path()."robots.txt";
					$robotsnew = stripslashes($_POST['robotsnew']);
					if (is_writable($robots_file)) {
						$f = fopen($robots_file, 'w+');
						fwrite($f, $robotsnew);
						fclose($f);
						$msg = __( 'Updated Robots.txt', 'wordpress-seo' );
					}
				} 
			}
			
			if ( isset($_POST['submithtaccess']) ) {
				if (!current_user_can('manage_options')) die(__('You cannot edit the .htaccess file.', 'wordpress-seo'));

				check_admin_referer('wpseo-htaccess');

				if (file_exists( get_home_path().".htaccess" ) ) {
					$htaccess_file = get_home_path().".htaccess";
					$htaccessnew = stripslashes($_POST['htaccessnew']);
					if (is_writeable($htaccess_file)) {
						$f = fopen($htaccess_file, 'w+');
						fwrite($f, $htaccessnew);
						fclose($f);
					}
				} 
			}

			$this->admin_header('Files', false, false);
			if (isset($msg) && !empty($msg)) {
				echo '<div id="message" style="width:94%;" class="updated fade"><p>'.$msg.'</p></div>';
			}

			if (file_exists( get_home_path()."robots.txt")) {
				$robots_file = get_home_path()."robots.txt";
				$f = fopen($robots_file, 'r');
				if (filesize($robots_file) > 0)
					$content = fread($f, filesize($robots_file));
				else
					$content = '';
				$robotstxtcontent = htmlspecialchars($content);

				if (!is_writable($robots_file)) {
					$content = "<p><em>".__("If your robots.txt were writable, you could edit it from here.", 'wordpress-seo')."</em></p>";
					$content .= '<textarea disabled="disabled" style="width: 90%;" rows="15" name="robotsnew">'.$robotstxtcontent.'</textarea><br/>';
				} else {
					$content = '<form action="" method="post" id="robotstxtform">';
					$content .= wp_nonce_field('wpseo-robotstxt', '_wpnonce', true, false);
					$content .= "<p>".__("Edit the content of your robots.txt:", 'wordpress-seo')."</p>";
					$content .= '<textarea style="width: 90%;" rows="15" name="robotsnew">'.$robotstxtcontent.'</textarea><br/>';
					$content .= '<div class="submit"><input class="button" type="submit" name="submitrobots" value="'.__("Save changes to Robots.txt", 'wordpress-seo').'" /></div>';
					$content .= '</form>';
				}
				$this->postbox('robotstxt',__('Robots.txt', 'wordpress-seo'),$content);
			}
			
			if (file_exists( get_home_path().".htaccess" )) {
				$htaccess_file = get_home_path()."/.htaccess";
				$f = fopen($htaccess_file, 'r');
				$contentht = fread($f, filesize($htaccess_file));
				$contentht = htmlspecialchars($contentht);

				if (!is_writable($htaccess_file)) {
					$content = "<p><em>".__("If your .htaccess were writable, you could edit it from here.", 'wordpress-seo')."</em></p>";
					$content .= '<textarea disabled="disabled" style="width: 90%;" rows="15" name="robotsnew">'.$contentht.'</textarea><br/>';
				} else {
					$content = '<form action="" method="post" id="htaccessform">';
					$content .= wp_nonce_field('wpseo-htaccess', '_wpnonce', true, false);
					$content .=  "<p>".__( 'Edit the content of your .htaccess:', 'wordpress-seo' )."</p>";
					$content .= '<textarea style="width: 90%;" rows="15" name="htaccessnew">'.$contentht.'</textarea><br/>';
					$content .= '<div class="submit"><input class="button" type="submit" name="submithtaccess" value="'.__('Save changes to .htaccess', 'wordpress-seo').'" /></div>';
					$content .= '</form>';
				}
				$this->postbox('htaccess',__('.htaccess file', 'wordpress-seo'),$content);
			}
			
			$this->admin_footer('', false);
		}
		
		function rss_page() {
			$options = get_wpseo_options();
			$this->admin_header('RSS', false, true, 'yoast_wpseo_rss_options', 'wpseo_rss');
			
			$content = '<p>'.__( "This feature is used to automatically add content to your RSS, more specifically, it's meant to add links back to your blog and your blog posts, so dumb scrapers will automatically add these links too, helping search engines identify you as the original source of the content.", 'wordpress-seo' ).'</p>';
			$rows = array();
			$rssbefore = '';
			if ( isset($options['rssbefore']) )
				$rssbefore = esc_html(stripslashes($options['rssbefore']));

			$rssafter = '';
			if ( isset($options['rssafter']) )
				$rssafter = esc_html(stripslashes($options['rssafter']));
			
			$rows[] = array(
				"id" => "rssbefore",
				"label" => __("Content to put before each post in the feed", 'wordpress-seo'),
				"desc" => __("(HTML allowed)", 'wordpress-seo'),
				"content" => '<textarea cols="50" rows="5" id="rssbefore" name="wpseo_rss[rssbefore]">'.$rssbefore.'</textarea>',
			);
			$rows[] = array(
				"id" => "rssafter",
				"label" => __("Content to put after each post", 'wordpress-seo'),
				"desc" => __("(HTML allowed)", 'wordpress-seo'),
				"content" => '<textarea cols="50" rows="5" id="rssafter" name="wpseo_rss[rssafter]">'.$rssafter.'</textarea>',
			);
			$rows[] = array(
				"label" => __('Explanation', 'wordpress-seo'),
				"content" => '<p>'.__('You can use the following variables within the content, they will be replaced by the value on the right.', 'wordpress-seo').'</p>'.
				'<table>'.
				'<tr><th><strong>%%AUTHORLINK%%</strong></th><td>'.__('A link to the archive for the post author, with the authors name as anchor text.', 'wordpress-seo').'</td></tr>'.
				'<tr><th><strong>%%POSTLINK%%</strong></th><td>'.__('A link to the post, with the title as anchor text.', 'wordpress-seo').'</td></tr>'.
				'<tr><th><strong>%%BLOGLINK%%</strong></th><td>'.__("A link to your site, with your site's name as anchor text.", 'wordpress-seo').'</td></tr>'.
				'<tr><th><strong>%%BLOGDESCLINK%%</strong></th><td>'.__("A link to your site, with your site's name and description as anchor text.", 'wordpress-seo').'</td></tr>'.
				'</table>'
			);
			$this->postbox('rssfootercontent',__('Content of your RSS Feed', 'wordpress-seo'),$content.$this->form_table($rows));
			
			$this->admin_footer('RSS');
		}
		
		function xml_sitemaps_page() {
			$this->admin_header('XML Sitemaps', false, true, 'yoast_wpseo_xml_sitemap_options', 'wpseo_xml');

			$options = get_option('wpseo_xml');

			$base = $GLOBALS['wp_rewrite']->using_index_permalinks() ? 'index.php/' : '';

			$content = $this->checkbox('enablexmlsitemap',__('Check this box to enable XML sitemap functionality.', 'wordpress-seo' ), false);
			$content .= '<div id="sitemapinfo">';
			if ( isset($options['enablexmlsitemap']) && $options['enablexmlsitemap'] )
				$content .= '<p>'.sprintf(__('You can find your XML Sitemap here: %sXML Sitemap%s', 'wordpress-seo' ), '<a target="_blank" class="button-secondary" href="'.home_url($base.'sitemap_index.xml').'">', '</a>').'<br/><br/>'.__( 'You do <strong>not</strong> need to generate the XML sitemap, nor will it take up time to generate after publishing a post.', 'wordpress-seo' ).'</p>';
			else
				$content .= '<p>'.__('Save your settings to activate XML Sitemaps.', 'wordpress-seo' ).'</p>';
			$content .= '<strong>'.__('General settings', 'wordpress-seo' ).'</strong><br/>';
			$content .= '<p>'.__('After content publication, the plugin automatically pings Google and Bing, do you need it to ping other search engines too? If so, check the box:', 'wordpress-seo' ).'</p>';
			$content .= $this->checkbox('xml_ping_yahoo', __("Ping Yahoo!", 'wordpress-seo' ), false);
			$content .= $this->checkbox('xml_ping_ask', __("Ping Ask.com", 'wordpress-seo' ), false);
			$content .= '<br/><strong>'.__('Exclude post types', 'wordpress-seo' ).'</strong><br/>';
			$content .= '<p>'.__('Please check the appropriate box below if there\'s a post type that you do <strong>NOT</strong> want to include in your sitemap:', 'wordpress-seo' ).'</p>';
			foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $pt) {
				$content .= $this->checkbox('post_types-'.$pt->name.'-not_in_sitemap', $pt->labels->name);
			}

			$content .= '<br/>';
			$content .= '<strong>'.__('Exclude taxonomies', 'wordpress-seo' ).'</strong><br/>';
			$content .= '<p>'.__('Please check the appropriate box below if there\'s a taxonomy that you do <strong>NOT</strong> want to include in your sitemap:', 'wordpress-seo' ).'</p>';
			foreach ( get_taxonomies( array( 'public' => true ), 'objects' ) as $tax ) {
				if ( isset( $tax->labels->name ) && trim($tax->labels->name) != '' )
					$content .= $this->checkbox('taxonomies-'.$tax->name.'-not_in_sitemap', $tax->labels->name);
			}
			
			$content .= '<br class="clear"/>';
			$content .= '</div>';

			$this->postbox('xmlsitemaps',__('XML Sitemap', 'wordpress-seo'),$content);
			
			do_action('wpseo_xmlsitemaps_config', $this);		
			
			$this->admin_footer('XML Sitemaps');
		}
		
		function config_page() {
			$options = get_option('wpseo');
		
			$this->admin_header('General', false, true, 'yoast_wpseo_options', 'wpseo' );
			
			echo $this->hidden('ignore_blog_public_warning');
			echo $this->hidden('ignore_tour');
			echo $this->hidden('ignore_page_comments');
			echo $this->hidden('ignore_permalink');
			echo $this->hidden('ms_defaults_set');
			echo $this->hidden('version');
			
			ksort($options);
			
			if ( isset($options['blocking_files']) && is_array($options['blocking_files']) && count($options['blocking_files']) > 0 ) {
				$options['blocking_files'] = array_unique( $options['blocking_files'] );
				echo '<p id="blocking_files" class="wrong">'
				.'<a href="javascript:wpseo_killBlockingFiles(\''.wp_create_nonce('wpseo-blocking-files').'\')" class="button fixit">'.__('Fix it.', 'wordpress-seo' ).'</a>'
				.__( 'The following file(s) is/are blocking your XML sitemaps from working properly:', 'wordpress-seo' ).'<br />';
				foreach($options['blocking_files'] as $file) {
					echo esc_html( $file ) . '<br/>';
				}
				echo __( 'Either delete them (this can be done with the "Fix it" button) or disable WP SEO XML sitemaps.', 'wordpress-seo' );
				echo '</p>';
			}
			
			if ( strpos( get_option('permalink_structure'), '%postname%' ) === false && !isset( $options['ignore_permalink'] )  )
				echo '<p id="wrong_permalink" class="wrong">'
				.'<a href="'.admin_url('options-permalink.php').'" class="button fixit">'.__('Fix it.', 'wordpress-seo' ).'</a>'
				.'<a href="javascript:wpseo_setIgnore(\'permalink\',\'wrong_permalink\',\''.wp_create_nonce('wpseo-ignore').'\');" class="button fixit">'.__('Ignore.', 'wordpress-seo' ).'</a>'
				.__('You do not have your postname in the URL of your posts and pages, it is highly recommended that you do. Consider setting your permalink structure to <strong>/%postname%/</strong>.', 'wordpress-seo' ).'</p>';

			if ( get_option('page_comments') && !isset( $options['ignore_page_comments'] ) )
				echo '<p id="wrong_page_comments" class="wrong">'
				.'<a href="javascript:setWPOption(\'page_comments\',\'0\',\'wrong_page_comments\',\''.wp_create_nonce('wpseo-setoption').'\');" class="button fixit">'.__('Fix it.', 'wordpress-seo' ).'</a>'
				.'<a href="javascript:wpseo_setIgnore(\'page_comments\',\'wrong_page_comments\',\''.wp_create_nonce('wpseo-ignore').'\');" class="button fixit">'.__('Ignore.', 'wordpress-seo' ).'</a>'
				.__('Paging comments is enabled, this is not needed in 999 out of 1000 cases, so the suggestion is to disable it, to do that, simply uncheck the box before "Break comments into pages..."', 'wordpress-seo' ).'</p>';

			echo '<h2>'.__('General','wordpress-seo').'</h2>';

			if ( isset( $options['ignore_tour'] ) && $options['ignore_tour'] ) {
				echo '<label class="select">'.__('Introduction Tour:','wordpress-seo').'</label><a class="button-secondary" href="'.admin_url('admin.php?page=wpseo_dashboard&wpseo_restart_tour').'">'.__('Start Tour', 'wordpress-seo' ).'</a>';
				echo '<p class="desc label">'.__('Take this tour to quickly learn about the use of this plugin.','wordpress-seo').'</p>';
			}

			echo '<label class="select">'.__('Default Settings:','wordpress-seo').'</label><a class="button-secondary" href="'.admin_url('admin.php?page=wpseo_dashboard&wpseo_reset_defaults').'">'.__('Reset Default Settings', 'wordpress-seo' ).'</a>';
			echo '<p class="desc label">'.__('If you want to restore a site to the default WordPress SEO settings, press this button.','wordpress-seo').'</p>';
			
			echo '<h2>'.__('Security','wordpress-seo').'</h2>';
			echo $this->checkbox('disableadvanced_meta', __('Disable the Advanced part of the WordPress SEO meta box', 'wordpress-seo' ));
			echo '<p class="desc">'.__('Unchecking this box allows authors and editors to redirect posts, noindex them and do other things you might not want if you don\'t trust your authors.','wordpress-seo').'</p>';
			
			echo '<h2>'.__('Webmaster Tools', 'wordpress-seo' ).'</h2>';
			echo '<p>'.__('You can use the boxes below to verify with the different Webmaster Tools, if your site is already verified, you can just forget about these. Enter the verify meta values for:', 'wordpress-seo' ).'</p>';
			echo $this->textinput('googleverify', '<a target="_blank" href="https://www.google.com/webmasters/tools/dashboard?hl=en&amp;siteUrl='.urlencode(get_bloginfo('url')).'%2F">'.__('Google Webmaster Tools', 'wordpress-seo').'</a>');
			echo $this->textinput('msverify','<a target="_blank" href="http://www.bing.com/webmaster/?rfp=1#/Dashboard/?url='.str_replace('http://','',get_bloginfo('url')).'">'.__('Bing Webmaster Tools', 'wordpress-seo').'</a>');
			echo $this->textinput('alexaverify','<a target="_blank" href="http://www.alexa.com/pro/subscription">'.__('Alexa Verification ID', 'wordpress-seo').'</a>');
							
			do_action('wpseo_dashboard', $this);
			
			$this->admin_footer('');
		}
		
		function social_page() {
			$options = get_option('wpseo_social');
			
			$fbconnect = '<p><strong>'.__('Facebook Insights and Admins', 'wordpress-seo').'</strong><br>';
			$fbconnect .= sprintf( __('To be able to access your <a href="%s">Facebook Insights</a> for your site, you need to specify a Facebook Admin. This can be a user, but if you have an app for your site, you could use that. For most people a user will be "good enough" though.', 'wordpress-seo' ), 'https://www.facebook.com/insights').'</p>';

			$error = false;
			$clearall = false;

			if ( isset( $_GET['delfbadmin'] ) ) {
				if ( wp_verify_nonce($_GET['nonce'], 'delfbadmin') != 1 )
					die("I don't think that's really nice of you!.");
				$id = $_GET['delfbadmin'];
				if ( isset( $options['fb_admins'][$id] ) ) {
					$fbadmin = $options['fb_admins'][$id]['name'];
					unset( $options['fb_admins'][$id] );
					update_option('wpseo_social', $options);
					add_settings_error('yoast_wpseo_social_options','success',sprintf(__('Successfully removed admin %s','wordpress-seo'), $fbadmin), 'updated');
					$error = true;
				} 
			}

			if ( isset( $_GET['fbclearall'] ) ) {
				if ( wp_verify_nonce($_GET['nonce'], 'fbclearall') != 1 )
					die("I don't think that's really nice of you!.");
				unset( $options['fb_admins'], $options['fbapps'], $options['fbadminapp'], $options['fbadminpage'] );
				update_option('wpseo_social', $options);
				add_settings_error('yoast_wpseo_social_options','success',sprintf(__('Successfully cleared all Facebook Data','wordpress-seo'), $fbadmin), 'updated');
			}
			
			if ( !isset( $options['fbconnectkey'] ) || empty( $options['fbconnectkey'] ) ) {
				$options['fbconnectkey'] = md5(get_bloginfo('url').rand());
				update_option('wpseo_social', $options);
			}

			if ( isset( $_GET['key'] ) && $_GET['key'] == $options['fbconnectkey'] ) {
				if ( isset( $_GET['userid'] ) ) {
					if ( !is_array($options['fb_admins']) ) 
						$options['fb_admins'] = array();
					$id = $_GET['userid'];
					$options['fb_admins'][$id]['name'] = urldecode($_GET['userrealname']);
					$options['fb_admins'][$id]['link'] = urldecode($_GET['link']);
					update_option('wpseo_social', $options);
					add_settings_error('yoast_wpseo_social_options','success',sprintf( __('Successfully added %s as a Facebook Admin!','wordpress-seo'), '<a href="'.$options['fb_admins'][$id]['link'].'">'.$options['fb_admins'][$id]['name'].'</a>') , 'updated');
				} else if ( isset( $_GET['apps'] ) ) {
					$apps = json_decode( stripslashes( $_GET['apps'] ) );
					$options['fbapps'] = array( '0' => __('Do not use a Facebook App as Admin','wordpress-seo') );
					foreach ($apps as $app) {
						$options['fbapps'][$app->app_id] = $app->display_name;
					}
					update_option('wpseo_social', $options);
					add_settings_error('yoast_wpseo_social_options','success', __('Successfully retrieved your apps from Facebook, now select an app to use as admin.','wordpress-seo') , 'updated');
				}
				$error = true;
			}

			$options = get_option('wpseo_social');
			
			if ( isset($options['fb_admins']) && is_array($options['fb_admins']) ) {
				foreach($options['fb_admins'] as $id => $admin) {
					$fbconnect .= '<input type="hidden" name="wpseo_social[fb_admins]['.$id.']" value="'.$admin.'"/>';
				}
				$clearall = true;
			}

			if ( isset($options['fbapps']) && is_array($options['fbapps']) ) {
				foreach($options['fbapps'] as $id => $page) {
					$fbconnect .= '<input type="hidden" name="wpseo_social[fbapps]['.$id.']" value="'.$page.'"/>';
				}
				$clearall = true;
			}
			
			$app_button_text = __('Use a Facebook App as Admin','wordpress-seo');
			if ( isset($options['fbapps']) && is_array($options['fbapps']) ) {
				$fbconnect .= '<p>'.__('Select an app to use as Facebook admin:', 'wordpress-seo' ).'</p>';
				$fbconnect .= '<select name="wpseo_social[fbadminapp]" id="fbadminapp">';
				
				if ( !isset($options['fbadminapp']) )
					$options['fbadminapp'] = 0;

				foreach($options['fbapps'] as $id => $app) {
					$sel = '';

					if ( $id == $options['fbadminapp'] )
						$sel = 'selected="selected"';
					$fbconnect .= '<option '.$sel.' value="'.$id.'">'.$app.'</option>';
				}
				$fbconnect .= '</select><div class="clear"></div><br/>';
				$app_button_text = __('Update Facebook Apps','wordpress-seo');
			}
			
			if ( !isset($options['fbadminapp']) || $options['fbadminapp'] == 0 ) {
				$button_text = __( 'Add Facebook Admin', 'wordpress-seo' );
				$primary = true;
				if ( isset($options['fb_admins']) && is_array($options['fb_admins']) && count($options['fb_admins']) > 0 ) {
					$fbconnect .= '<p>'.__( 'Currently connected Facebook admins:', 'wordpress-seo' ).'</p>';
					$fbconnect .= '<ul>';
					$nonce = wp_create_nonce('delfbadmin');
					
					foreach ( $options['fb_admins'] as $admin_id => $admin ) {
						$fbconnect .= '<li><a href="'.$admin['link'].'">'.$admin['name'].'</a> - <strong><a  href="'.admin_url('admin.php?page=wpseo_social&delfbadmin='.$admin_id.'&nonce='.$nonce).'">X</a></strong></li>';
						$fbconnect .= '<input type="hidden" name="wpseo_social[fb_admins]['.$admin_id.'][link]" value="'.$admin['link'].'"/>';
						$fbconnect .= '<input type="hidden" name="wpseo_social[fb_admins]['.$admin_id.'][name]" value="'.$admin['name'].'"/>';
					}
					$fbconnect .= '</ul>';
					$button_text = __( 'Add Another Facebook Admin', 'wordpress-seo' );
					$primary = false;
				}
				if ($primary)
					$but_primary = '-primary';
				$fbconnect .= '<p><a class="button'.$but_primary.'" href="https://yoast.com/fb-connect/?key='.$options['fbconnectkey'].'&redirect='.urlencode(admin_url('admin.php?page=wpseo_social')).'">'.$button_text.'</a></p>';	
			}

			$fbconnect .= '<a class="button" href="https://yoast.com/fb-connect/?key='.$options['fbconnectkey'].'&type=app&redirect='.urlencode(admin_url('admin.php?page=wpseo_social')).'">'.$app_button_text.'</a> ';
			if ($clearall) {
				$fbconnect .= '<a class="button" href="'.admin_url('admin.php?page=wpseo_social&nonce='.wp_create_nonce('fbclearall').'&fbclearall=true').'">'.__('Clear all Facebook Data', 'wordpress-seo').'</a> ';
			}
			$fbconnect .= '</p>';
			
			$this->admin_header(__('Social', 'wordpress-seo' ), false, true, 'yoast_wpseo_social_options', 'wpseo_social');

			if ( $error )
				settings_errors();
			
			echo '<h2>'.__('Facebook OpenGraph','wordpress-seo').'</h2>';
			echo $this->checkbox('opengraph', '<label for="opengraph">'.__('Add OpenGraph meta data', 'wordpress-seo').'</label>' );
			echo'<p class="desc">'.__('Add OpenGraph meta data to your site\'s <code>&lt;head&gt;</code> section. You can specify some of the ID\'s that are sometimes needed below:', 'wordpress-seo').'</p>';
			echo $fbconnect;
			echo '<h4>'.__( 'Frontpage settings', 'wordpress-seo' ).'</h4>';
			echo $this->textinput('og_frontpage_image', __('Image URL', 'wordpress-seo' ) );
			echo $this->textinput('og_frontpage_desc', __('Description', 'wordpress-seo' ) );
			echo '<p class="desc label">'.__('These are the image and description used in the OpenGraph meta tags on the frontpage of your site.','wordpress-seo').'</p>';
			echo '<h4>'.__( 'Default settings', 'wordpress-seo' ).'</h4>';
			echo $this->textinput('og_default_image', __('Image URL', 'wordpress-seo' ) );
			echo '<p class="desc label">'.__('This image is used if the post/page being shared does not contain any images.','wordpress-seo').'</p>';
						
			$this->admin_footer('');
		}
		
		function wpseo_user_profile($user) {
			if (!current_user_can('edit_users'))
				return;
				
			$options = get_wpseo_options();
			?>
				<h3 id="wordpress-seo"><?php _e( "WordPress SEO settings", 'wordpress-seo' ); ?></h3>
				<table class="form-table">
					<tr>
						<th><?php _e( "Title to use for Author page", 'wordpress-seo' ); ?></th>
						<td><input class="regular-text" type="text" name="wpseo_author_title" value="<?php echo esc_attr(get_the_author_meta('wpseo_title', $user->ID) ); ?>"/></td>
					</tr>
					<tr>
						<th><?php _e( "Meta description to use for Author page", 'wordpress-seo' ); ?></th>
						<td><textarea rows="3" cols="30" name="wpseo_author_metadesc"><?php echo esc_html(get_the_author_meta('wpseo_metadesc', $user->ID) ); ?></textarea></td>
					</tr>
			<?php 	if ( isset($options['usemetakeywords']) && $options['usemetakeywords'] ) {  ?>
					<tr>
						<th><?php _e( "Meta keywords to use for Author page", 'wordpress-seo' ); ?></th>
						<td><input class="regular-text" type="text" name="wpseo_author_metakey" value="<?php echo esc_attr(get_the_author_meta('wpseo_metakey', $user->ID) ); ?>"/></td>
					</tr>
			<?php } ?>
				</table>
				<br/><br/>
			<?php
		}
		
		function wpseo_process_user_option_update($user_id) {
			update_user_meta($user_id, 'wpseo_title', ( isset($_POST['wpseo_author_title']) ? $_POST['wpseo_author_title'] : '' ) );
			update_user_meta($user_id, 'wpseo_metadesc', ( isset($_POST['wpseo_author_metadesc']) ? $_POST['wpseo_author_metadesc'] : '' ) );
			update_user_meta($user_id, 'wpseo_metakey', ( isset($_POST['wpseo_author_metakey']) ? $_POST['wpseo_author_metakey'] : '' ) );
		}
		
		function add_google_plus_contactmethod( $contactmethods ) {
		  // Add Twitter
		  $contactmethods['googleplus'] = 'Google+';

		  return $contactmethods;
		}

	} // end class WPSEO_Admin
	$wpseo_admin = new WPSEO_Admin();
}
