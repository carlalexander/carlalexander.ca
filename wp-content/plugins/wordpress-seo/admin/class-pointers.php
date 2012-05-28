<?php

class WPSEO_Pointers {

	function __construct() {
		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue' ) );
	}
	
	function enqueue() {
		$options = get_option('wpseo');
		if ( isset( $_GET['wpseo_restart_tour'] ) ) {
			unset( $options['ignore_tour'] );
			update_option( 'wpseo', $options );
		}
		if ( !isset($options['ignore_tour']) || !$options['ignore_tour'] ) {
			wp_enqueue_style( 'wp-pointer' ); 
			wp_enqueue_script( 'jquery-ui' ); 
			wp_enqueue_script( 'wp-pointer' ); 
			wp_enqueue_script( 'utils' );
			add_action( 'admin_print_footer_scripts', array( &$this, 'print_scripts' ), 99 );
			add_action( 'admin_head', array( &$this, 'admin_head' ) );
		}
	}

	function print_scripts() {
		global $pagenow, $current_user;
		
		$adminpages = array( 
			'wpseo_dashboard' => array(
					'content'  => '<h3>'.__( 'Dashboard', 'wordpress-seo' ).'</h3><p>'.__( 'This is the WordPress SEO Dashboard, here you can control some of the basic settings such as for which post types and taxonomies to show the WordPress SEO controls.', 'wordpress-seo' ).'</p><p><strong>'.__( 'Webmaster Tools', 'wordpress-seo' ).'</strong><br/>'.__( 'Underneath the General Settings, you can add the verification codes for the different Webmaster Tools programs, I highly encourage you to check out both Google and Bing\'s Webmaster Tools.', 'wordpress-seo' ).'</p><p><strong>'.__( 'About This Tour', 'wordpress-seo' ).'</strong><br/>'.__( 'Clicking Next below takes you to the next page of the tour. If you want to stop this tour, click "Close".', 'wordpress-seo' ).'</p>',
				'button2'  => __( 'Next', 'wordpress-seo' ),
				'function' => 'window.location="'.admin_url('admin.php?page=wpseo_titles').'";'
			),
			'wpseo_titles' => array(
				'content'  => "<h3>".__( "Title &amp; Description settings", 'wordpress-seo' )."</h3>"
							   ."<p>".__( "This is were you set the templates for your titles and descriptions of all the different types of pages on your blog, be it your homepage, posts, pages, category or tag archives, or even custom post type archives and custom posts: all of that is done from here.", 'wordpress-seo' )."</p>"
							   ."<p><strong>".__( "Templates", 'wordpress-seo' )."</strong><br/>"
							   .__( "The templates are built using variables, see <a href='#titleshelp'>the bottom of this page</a> for all the different variables available to you to use in these.", 'wordpress-seo' )."</p>"
							   ."<p><strong>".__( "Trouble?", 'wordpress-seo' )."</strong><br/>".__( "Be sure to check if your titles are displaying correctly once you've set this up. If you're experiencing trouble with how titles display, be sure to check the 'Force rewrite' checkbox on the left and check again, or follow the instructions on this page on how to modify your theme.", 'wordpress-seo' )."</p>",
				'button2'  => __( 'Next', 'wordpress-seo' ),
				'function' => 'window.location="'.admin_url('admin.php?page=wpseo_indexation').'";'
			),
			'wpseo_indexation' => array(
				'content'  => '<h3>'.__( 'Indexation settings', 'wordpress-seo' ).'</h3><p>'.__( 'There are options here to do a whole lot of things, feel free to read through them and set them appropriately, or skip them entirely: WordPress SEO will do the most important things by default.', 'wordpress-seo' ).'</p>',
				'button2'  => __( 'Next', 'wordpress-seo' ),
				'function' => 'window.location="'.admin_url('admin.php?page=wpseo_xml').'";'
			),
			'wpseo_xml' => array(
				'content'  => '<h3>'.__( 'XML Sitemaps', 'wordpress-seo' ).'</h3><p>'.__( 'I highly encourage you to check the box to enable XML Sitemaps. Once you do, an XML sitemap will be generated when you publish a new post, page or custom post and Google and Bing will be automatically notified.', 'wordpress-seo' ).'</p><p>'.__( 'Be sure to check whether post types or taxonomies are showing that search engines shouldn\'t be indexing, if so, check the box before them to hide them from the XML sitemaps.', 'wordpress-seo' ).'</p>',
				'button2'  => __( 'Next', 'wordpress-seo' ),
				'function' => 'window.location="'.admin_url('admin.php?page=wpseo_permalinks').'";'
			),
			'wpseo_permalinks' => array(
				'content'  => '<h3>'.__( 'Permalink Settings', 'wordpress-seo' ).'</h3><p>'.__( 'All of the options here are for advanced users only, if you don\'t know whether you should check any, don\'t touch them.', 'wordpress-seo' ).'</p>',
				'button2'  => __( 'Next', 'wordpress-seo' ),
				'function' => 'window.location="'.admin_url('admin.php?page=wpseo_internal-links').'";'
			),
			'wpseo_internal-links' => array(
				'content'  => '<h3>'.__( 'Breadcrumbs Settings', 'wordpress-seo' ).'</h3><p>'.sprintf(__( 'If your theme supports my breadcrumbs, as all Genesis and WooThemes themes as well as a couple of other ones do, you can change the settings for those here. If you want to modify your theme to support them, %sfollow these instructions%s.', 'wordpress-seo' ),'<a href="http://yoast.com/wordpress/breadcrumbs/">','</a>').'</p>',
				'button2'  => __( 'Next', 'wordpress-seo' ),
				'function' => 'window.location="'.admin_url('admin.php?page=wpseo_rss').'";'
			),
			'wpseo_rss' => array(
				'content'  => '<h3>'.__( 'RSS Settings', 'wordpress-seo' ).'</h3><p>'.__( 'This incredibly powerful function allows you to add content to the beginning and end of your posts in your RSS feed. This helps you gain links from people who steal your content!', 'wordpress-seo' ).'</p>',
				'button2'  => __( 'Next', 'wordpress-seo' ),
				'function' => 'window.location="'.admin_url('admin.php?page=wpseo_import').'";'
			),
			'wpseo_import' => array(
				'content'  => '<h3>'.__( 'Import &amp; Export', 'wordpress-seo' ).'</h3><p>'.__( 'Just switched over from another SEO plugin? Use the options here to switch your data over. If you were using some of my older plugins like Robots Meta &amp; RSS Footer, you can import the settings here too.', 'wordpress-seo' ).'</p><p>'.__( 'If you have multiple blogs and you\'re happy with how you\'ve configured this blog, you can export the settings and import them on another blog so you don\'t have to go through this process twice!', 'wordpress-seo' ).'</p>',
				'button2'  => __( 'Next', 'wordpress-seo' ),
				'function' => 'window.location="'.admin_url('admin.php?page=wpseo_files').'";'
			),
			'wpseo_files' => array(
				'content'  => '<h3>'.__( 'File Editor', 'wordpress-seo' ).'</h3><p>'.__( 'Here you can edit the .htaccess and robots.txt files, two of the most powerful files in your WordPress install. Only touch these files if you know what you\'re doing!', 'wordpress-seo' ).'</p><p><strong>'.__( 'Like this plugin?', 'wordpress-seo' ).'</strong><br/>'.sprintf(__( 'If you like this plugin, please %srate it 5 stars on WordPress.org%s and consider making a donation by clicking the button on the right!', 'wordpress-seo' ),'<a href="http://wordpress.org/extend/plugins/wordpress-seo/">','</a>').'</p>'.
				 '<p><strong>'.__('Newsletter','wordpress-seo').'</strong><br/>'.
				 __( 'If you would like to keep up to date regarding the WordPress SEO plugin and other plugins by Yoast, subscribe to the newsletter:', 'wordpress-seo').'</p>'.
				 '<form action="http://yoast.us1.list-manage.com/subscribe/post?u=ffa93edfe21752c921f860358&amp;id=972f1c9122" method="post" id="newsletter-form">'.
				 '<p>'.
				 '<label for="newsletter-name">'.__('Name','wordpress-seo').':</label><input style="color:#666" name="MMERGE9" value="'.$current_user->display_name.'" id="newsletter-name" placeholder="'.__('Name','wordpress-seo').'"/><br/>'.
				 '<label for="newsletter-email">'.__('Email','wordpress-seo').':</label><input style="color:#666" name="EMAIL" value="'.$current_user->user_email.'" id="newsletter-email" placeholder="'.__('Email','wordpress-seo').'"/><br/>'.
				 '<input type="hidden" name="group" value="2"/>'.
				 '<button type="submit" class="button-primary">'.__('Subscribe','wordpress-seo').'</button>'.
				 '</p></form>'.
				 '<p>'.__( 'The tour ends here, good luck!', 'wordpress-seo' ).'</p>',
			),
		);

		$page = '';
		if ( isset($_GET['page']) )
			$page = $_GET['page'];

		if ( 'admin.php' != $pagenow || !array_key_exists( $page, $adminpages ) ) {
			$id 			= 'toplevel_page_wpseo_dashboard';
			$content 		= '<h3>'.__( 'Congratulations!', 'wordpress-seo' ).'</h3>';
			$content 		.= '<p>'.__( 'You\'ve just installed WordPress SEO by Yoast! Click "Start Tour" to view a quick introduction of this plugins core functionality.', 'wordpress-seo' ).'</p>';
			$position_at 	= 'left top';
			$button2 		= __( "Start Tour", 'wordpress-seo' );
			$function 		= 'document.location="'.admin_url('admin.php?page=wpseo_dashboard').'";';
		} else {
			if ( '' != $page && in_array( $page, array_keys( $adminpages ) ) ) {
				$id 			= 'wpseo_content_top';
				$content 		= $adminpages[$page]['content'];
				$position_at 	= 'left bottom';
				$button2 		= $adminpages[$page]['button2'];
				$function 		= $adminpages[$page]['function'];
			}
		}

		$this->print_buttons( $id, $content, __( "Close", 'wordpress-seo' ), $position_at, $button2, $function );
	}
	
	function admin_head() {
	?>
		<style type="text/css" media="screen">
			#pointer-primary, #tour-close {
				margin: 0 5px 0 0;
			}
		</style>
	<?php
	}
	
	function print_buttons( $id, $content, $button1, $position_at, $button2 = false, $button2_function = '' ) {
	?>
	<script type="text/javascript"> 
	//<![CDATA[ 
	jQuery(document).ready( function() { 
		jQuery('#<?php echo $id; ?>').pointer({ 
			content: '<?php echo addslashes( $content ); ?>', 
			buttons: function( event, t ) {
				button = jQuery('<a id="pointer-close" class="button-secondary">' + '<?php echo $button1; ?>' + '</a>');
				button.bind( 'click.pointer', function() {
					t.element.pointer('close');
				});
				return button;
			},
			position: {
				my: 'left bottom', 
				at: '<?php echo $position_at; ?>', 
				offset: '0 0'
			},
			arrow: {
				edge: 'left',
				align: 'top',
				offset: 10
			},
			close: function() { },
		}).pointer('open'); 
		<?php if ( $button2 ) { ?> 
		jQuery('#pointer-close').after('<a id="pointer-primary" class="button-primary">' + '<?php echo $button2; ?>' + '</a>');
		jQuery('#pointer-primary').click( function() {
			<?php echo $button2_function; ?>
		});
		jQuery('#pointer-close').click( function() {
			wpseo_setIgnore("tour","wp-pointer-0","<?php echo wp_create_nonce('wpseo-ignore'); ?>");
		});
		<?php } ?>
	}); 
	//]]> 
	</script>
	<?php
	}
}

$wpseo_pointers = new WPSEO_Pointers;
