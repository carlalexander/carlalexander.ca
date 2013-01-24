<?php
/**
 * @package Oxygen
 * @subpackage Functions
 * @version 0.3.6
 * @author Galin Simeonov
 * @link http://alienwp.com
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */

/* Load the core theme framework. */
require_once( trailingslashit( TEMPLATEPATH ) . 'library/hybrid.php' );
$theme = new Hybrid();

/* Do theme setup on the 'after_setup_theme' hook. */
add_action( 'after_setup_theme', 'oxygen_theme_setup' );

/**
 * Theme setup function.  This function adds support for theme features and defines the default theme
 * actions and filters.
 *
 */
function oxygen_theme_setup() {

	/* Get action/filter hook prefix. */
	$prefix = hybrid_get_prefix();

	/* Add theme support for core framework features. */
	add_theme_support( 'hybrid-core-menus', array( 'primary', 'secondary', 'subsidiary' ) );
	add_theme_support( 'hybrid-core-sidebars', array( 'primary', 'secondary', 'subsidiary', 'after-singular', 'header' ) );
	add_theme_support( 'hybrid-core-widgets' );
	add_theme_support( 'hybrid-core-theme-settings', array( 'about', 'footer' ) );
	add_theme_support( 'hybrid-core-meta-box-footer' );
	add_theme_support( 'hybrid-core-shortcodes' );
	add_theme_support( 'hybrid-core-drop-downs' );
	add_theme_support( 'hybrid-core-template-hierarchy' );

	/* Add theme support for framework extensions. */
	add_theme_support( 'loop-pagination' );
	add_theme_support( 'get-the-image' );
	add_theme_support( 'cleaner-gallery' );
	add_theme_support( 'breadcrumb-trail' );

	/* Add theme support for WordPress features. */
	add_theme_support( 'automatic-feed-links' );

	/* Embed width/height defaults. */
	add_filter( 'embed_defaults', 'oxygen_embed_defaults' );

	/* Filter the sidebar widgets. */
	add_filter( 'sidebars_widgets', 'oxygen_disable_sidebars' );
        
	/* Image sizes */
	add_action( 'init', 'oxygen_image_sizes' );

	/* Excerpt ending */
	add_filter( 'excerpt_more', 'oxygen_excerpt_more' );
 
	/* Custom excerpt length */
	add_filter( 'excerpt_length', 'oxygen_excerpt_length' );    
        
	/* Filter the pagination trail arguments. */
	add_filter( 'loop_pagination_args', 'oxygen_pagination_args' );
	
	/* Filter the comments arguments */
	add_filter( "{$prefix}_list_comments_args", 'oxygen_comments_args' );	
	
	/* Filter the commentform arguments */
	add_filter( 'comment_form_defaults', 'oxygen_commentform_args', 11, 1 );
	
	/* Enqueue scripts (and related stylesheets) */
	add_action( 'wp_enqueue_scripts', 'oxygen_scripts' );
	
	/* Enqueue Google fonts */
	add_action( 'wp_enqueue_scripts', 'oxygen_google_fonts' );
	
	/* Style settings */
	add_action( 'wp_head', 'oxygen_style_settings' );	
	
	/* Add the breadcrumb trail just after the container is open. */
	add_action( "{$prefix}_open_content", 'breadcrumb_trail' );
	
	/* Breadcrumb trail arguments. */
	add_filter( 'breadcrumb_trail_args', 'oxygen_breadcrumb_trail_args' );
	
	/* Add support for custom backgrounds */
	add_theme_support( 'custom-background' );
	
	/* Add theme settings */
	if ( is_admin() )
	    require_once( trailingslashit( TEMPLATEPATH ) . 'admin/functions-admin.php' );
	    
	/* Default footer settings */
	add_filter( "{$prefix}_default_theme_settings", 'oxygen_default_footer_settings' );
	
	/* Metaboxes */
	add_action( 'add_meta_boxes', 'oxygen_create_metabox' );
	add_action( 'save_post', 'oxygen_save_meta', 1, 2 );
	
	/* Slider settings */
	add_action( 'wp_footer', 'oxygen_slider_settings' );
	
}

/**
 * Disables sidebars if viewing a one-column page.
 *
 */
function oxygen_disable_sidebars( $sidebars_widgets ) {
	
	global $wp_query;
	
	    if ( is_page_template( 'page-template-fullwidth.php' ) ) {
		    $sidebars_widgets['primary'] = false;
			$sidebars_widgets['secondary'] = false;
	    }

	return $sidebars_widgets;
}

/**
 * Overwrites the default widths for embeds.  This is especially useful for making sure videos properly
 * expand the full width on video pages.  This function overwrites what the $content_width variable handles
 * with context-based widths.
 *
 */
function oxygen_embed_defaults( $args ) {
	
	$args['width'] = 470;
		
	if ( is_page_template( 'page-template-fullwidth.php' ) )
		$args['width'] = 940;

	return $args;
}

/**
 * Excerpt ending 
 *
 */
function oxygen_excerpt_more( $more ) {	
	return '...';
}

/**
 *  Custom excerpt lengths 
 *
 */
function oxygen_excerpt_length( $length ) {
	return 25;
}

/**
 * Enqueue scripts (and related stylesheets)
 *
 */
function oxygen_scripts() {
	
	if ( !is_admin() ) {
		
		/* Enqueue Scripts */	
		wp_enqueue_script( 'oxygen_imagesloaded', get_template_directory_uri() . '/js/jquery.imagesloaded.js', array( 'jquery' ), '1.0', true );	
		wp_enqueue_script( 'oxygen_masonry', get_template_directory_uri() . '/js/jquery.masonry.min.js', array( 'jquery' ), '1.0', true );	
		wp_enqueue_script( 'oxygen_cycle', get_template_directory_uri() . '/js/cycle/jquery.cycle.min.js', array( 'jquery' ), '1.0', true );		
		wp_enqueue_script( 'oxygen_fitvids', get_template_directory_uri() . '/js/fitvids/jquery.fitvids.js', array( 'jquery' ), '1.0', true );	
		wp_enqueue_script( 'oxygen_footer_scripts', get_template_directory_uri() . '/js/footer-scripts.js', array( 'jquery', 'oxygen_imagesloaded', 'oxygen_masonry', 'oxygen_cycle', 'oxygen_fancybox', 'oxygen_fitvids' ), '1.0', true );
		
		/* Enqueue Fancybox if enabled. */	
		if ( hybrid_get_setting( 'oxygen_fancybox_enable' ) ) {
			wp_enqueue_script( 'oxygen_fancybox', get_template_directory_uri() . '/js/fancybox/jquery.fancybox-1.3.4.pack.js', array( 'jquery' ), '1.0', true );
			wp_enqueue_style( 'fancybox-stylesheet', get_template_directory_uri() . '/js/fancybox/jquery.fancybox-1.3.4.css', false, 1.0, 'screen' );
			wp_enqueue_script( 'oxygen_footer_scripts', get_template_directory_uri() . '/js/footer-scripts.js', array( 'jquery', 'oxygen_imagesloaded', 'oxygen_masonry', 'oxygen_cycle', 'oxygen_fancybox', 'oxygen_fitvids' ), '1.0', true );
		} else {
			wp_enqueue_script( 'oxygen_footer_scripts_light', get_template_directory_uri() . '/js/footer-scripts-light.js', array( 'jquery', 'oxygen_imagesloaded', 'oxygen_masonry', 'oxygen_cycle', 'oxygen_fitvids' ), '1.0', true );
		}
				
	}
}

/**
 * Pagination args 
 *
 */
function oxygen_pagination_args( $args ) {
	
	$args['prev_text'] = __( '&larr; Previous', 'oxygen' );
	$args['next_text'] = __( 'Next &rarr;', 'oxygen' );

	return $args;
}

/**
 *  Image sizes
 *
 */
function oxygen_image_sizes() {
	
	add_image_size( 'archive-thumbnail', 470, 140, true );
	add_image_size( 'single-thumbnail', 470, 260, true );
	add_image_size( 'featured-thumbnail', 750, 380, true );
	add_image_size( 'slider-nav-thumbnail', 110, 70, true );
}

/**
 *  Unregister Hybrid widgets
 *
 */
function oxygen_unregister_widgets() {
	
	unregister_widget( 'Hybrid_Widget_Search' );
	register_widget( 'WP_Widget_Search' );	
}

/**
 * Custom comments arguments
 * 
 */
function oxygen_comments_args( $args ) {
	
	$args['avatar_size'] = 40;
	return $args;
}

/**
 *  Custom comment form arguments
 * 
 */
function oxygen_commentform_args( $args ) {
	
	global $user_identity;

	/* Get the current commenter. */
	$commenter = wp_get_current_commenter();

	/* Create the required <span> and <input> element class. */
	$req = ( ( get_option( 'require_name_email' ) ) ? ' <span class="required">' . __( '*', 'oxygen' ) . '</span> ' : '' );
	$input_class = ( ( get_option( 'require_name_email' ) ) ? ' req' : '' );
	
	
	$fields = array(
		'author' => '<p class="form-author' . $input_class . '"><input type="text" class="text-input" name="author" id="author" value="' . esc_attr( $commenter['comment_author'] ) . '" size="40" /><label for="author">' . __( 'Name', 'oxygen' ) . $req . '</label></p>',
		'email' => '<p class="form-email' . $input_class . '"><input type="text" class="text-input" name="email" id="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="40" /><label for="email">' . __( 'Email', 'oxygen' ) . $req . '</label></p>',
		'url' => '<p class="form-url"><input type="text" class="text-input" name="url" id="url" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="40" /><label for="url">' . __( 'Website', 'oxygen' ) . '</label></p>'
	);
	
	$args = array(
		'fields' => apply_filters( 'comment_form_default_fields', $fields ),
		'comment_field' => '<p class="form-textarea req"><!--<label for="comment">' . __( 'Comment', 'oxygen' ) . '</label>--><textarea name="comment" id="comment" cols="60" rows="10"></textarea></p>',
		'must_log_in' => '<p class="alert">' . sprintf( __( 'You must be <a href="%1$s" title="Log in">logged in</a> to post a comment.', 'oxygen' ), wp_login_url( get_permalink() ) ) . '</p><!-- .alert -->',
		'logged_in_as' => '<p class="log-in-out">' . sprintf( __( 'Logged in as <a href="%1$s" title="%2$s">%2$s</a>.', 'oxygen' ), admin_url( 'profile.php' ), esc_attr( $user_identity ) ) . ' <a href="' . wp_logout_url( get_permalink() ) . '" title="' . esc_attr__( 'Log out of this account', 'oxygen' ) . '">' . __( 'Log out &rarr;', 'oxygen' ) . '</a></p><!-- .log-in-out -->',
		'comment_notes_before' => '',
		'comment_notes_after' => '',
		'id_form' => 'commentform',
		'id_submit' => 'submit',
		'title_reply' => __( 'Leave a Reply', 'oxygen' ),
		'title_reply_to' => __( 'Leave a Reply to %s', 'oxygen' ),
		'cancel_reply_link' => __( 'Click here to cancel reply.', 'oxygen' ),
		'label_submit' => __( 'Post Comment &rarr;', 'oxygen' ),
	);
	
	return $args;
}

/**
 * Breadcrumb trail arguments.
 *
 */
function oxygen_breadcrumb_trail_args( $args ) {

	$args['before'] = '';
	$args['separator'] = '&nbsp; / &nbsp;';
	$args['front_page'] = false;
	
	return $args;
}

/**
 * Default footer settings
 *
 */
function oxygen_default_footer_settings( $settings ) {
    
    $settings['footer_insert'] = '<p class="copyright">' . __( 'Copyright &#169; [the-year] [site-link]', 'oxygen' ) . '</p>' . "\n\n" . '<p class="credit">' . __( 'Powered by [wp-link] and [theme-link]', 'oxygen' ) . '</p>';
    
    return $settings;
}

/**
 * Metaboxes
 *
 */
function oxygen_create_metabox() {
    add_meta_box( 'oxygen_metabox', __( 'Location', 'oxygen' ), 'oxygen_metabox', 'post', 'side', 'low' );            
}
             
function oxygen_metabox() {
	
	global $post;
	
	/* Retrieve metadata values if they already exist. */
	$oxygen_post_location = get_post_meta( $post->ID, '_oxygen_post_location', true ); ?>	
	
	<p><label><input type="radio" name="oxygen_post_location" value="featured" <?php echo esc_attr( $oxygen_post_location ) == 'featured' ? 'checked="checked"' : '' ?> /> <?php echo __( 'Featured', 'oxygen' ) ?></label></p>
	<p><label><input type="radio" name="oxygen_post_location" value="primary" <?php echo esc_attr( $oxygen_post_location ) == 'primary' ? 'checked="checked"' : '' ?> /> <?php echo __( 'Primary', 'oxygen' ) ?></label></p>
	<p><label><input type="radio" name="oxygen_post_location" value="secondary" <?php echo esc_attr( $oxygen_post_location ) == 'secondary' ? 'checked="checked"' : '' ?> /> <?php echo __( 'Secondary', 'oxygen' ) ?></label></p>
	<p><label><input type="radio" name="oxygen_post_location" value="no-display" <?php echo esc_attr( $oxygen_post_location ) == 'no-display' ? 'checked="checked"' : '' ?> /> <?php echo __( 'Do not display', 'oxygen' ) ?></label></p>	
		
	<span class="description"><?php _e( 'Post location on the home page', 'oxygen' ); ?>
	<?php           
}

/* Save post metadata. */
function oxygen_save_meta( $post_id, $post ) {
	if ( isset( $_POST['oxygen_post_location'] ) ) {
		update_post_meta( $post_id, '_oxygen_post_location', strip_tags( $_POST['oxygen_post_location'] ) );
	}
}

/**
 * Google fonts
 *
 */
function oxygen_google_fonts() {
	
	if ( hybrid_get_setting( 'oxygen_font_family' ) ) {
		
		switch ( hybrid_get_setting( 'oxygen_font_family' ) ) {
			case 'Abel':
				wp_enqueue_style( 'font-abel', 'http://fonts.googleapis.com/css?family=Abel', false, 1.0, 'screen' );
				break;
			case 'Oswald':
				wp_enqueue_style( 'font-oswald', 'http://fonts.googleapis.com/css?family=Oswald', false, 1.0, 'screen' );
				break;
			case 'Terminal Dosis':
				wp_enqueue_style( 'font-terminal-dosis', 'http://fonts.googleapis.com/css?family=Terminal+Dosis', false, 1.0, 'screen' );
				break;
			case 'Droid Serif':
				wp_enqueue_style( 'font-droid-serif', 'http://fonts.googleapis.com/css?family=Droid+Serif:400,400italic', false, 1.0, 'screen' );
				break;			
			case 'Istok Web':
				wp_enqueue_style( 'font-istok-web', 'http://fonts.googleapis.com/css?family=Istok+Web', false, 1.0, 'screen' );
				break;
			case 'Droid Sans':
				wp_enqueue_style( 'font-droid-sans', 'http://fonts.googleapis.com/css?family=Droid+Sans', false, 1.0, 'screen' );
				break;				
			case 'Bitter':
				wp_enqueue_style( 'font-bitter', 'http://fonts.googleapis.com/css?family=Bitter', false, 1.0, 'screen' );
				break;
		}
	} else {
		wp_enqueue_style( 'font-abel', 'http://fonts.googleapis.com/css?family=Abel', false, 1.0, 'screen' );
	}	
}

/**
 * Style settings
 *
 */
function oxygen_style_settings() {
	
	echo "\n<!-- Style settings -->\n";
	echo "<style type=\"text/css\" media=\"all\">\n";
	
	if ( hybrid_get_setting( 'oxygen_font_size' ) )
		echo 'html { font-size: ' . hybrid_get_setting( 'oxygen_font_size' ) . "px; }\n";
	
	if ( hybrid_get_setting( 'oxygen_font_family' ) )
		echo 'h1, h2, h3, h4, h5, h6, dl dt, blockquote, blockquote blockquote blockquote, #site-title, #menu-primary li a { font-family: ' . hybrid_get_setting( 'oxygen_font_family' ) . ", sans-serif; }\n";
	
	if ( hybrid_get_setting( 'oxygen_link_color' ) ) {
		echo 'a, a:visited, .page-template-front .hfeed-more .hentry .entry-title a:hover, .entry-title a, .entry-title a:visited { color: ' . hybrid_get_setting( 'oxygen_link_color' ) . "; }\n";
		echo 'a:hover, .comment-meta a, .comment-meta a:visited, .page-template-front .hentry .entry-title a:hover, .archive .hentry .entry-title a:hover, .search .hentry .entry-title a:hover { border-color: ' . hybrid_get_setting( 'oxygen_link_color' ) . "; }\n";
		echo '.read-more, .read-more:visited, .pagination a:hover, .comment-navigation a:hover, #respond #submit, .button, a.button, #subscribe #subbutton, .wpcf7-submit, #loginform .button-primary { background-color: ' . hybrid_get_setting( 'oxygen_link_color' ) . "; }\n";		
		echo "a:hover, a:focus { color: #000; }\n";
		echo ".read-more:hover, #respond #submit:hover, .button:hover, a.button:hover, #subscribe #subbutton:hover, .wpcf7-submit:hover, #loginform .button-primary:hover { background-color: #111; }\n";		
	}	
	if ( hybrid_get_setting( 'oxygen_custom_css' ) )
		echo hybrid_get_setting( 'oxygen_custom_css' ) . "\n";
	
	echo "</style>\n";

}

/**
 * Slider settings
 *
 */
function oxygen_slider_settings() {
	
	$timeout = hybrid_get_setting( 'oxygen_slider_timeout' ) ? hybrid_get_setting( 'oxygen_slider_timeout' ) : '6000';
	$settings = array( 'timeout' => $timeout );
	wp_localize_script( 'oxygen_footer_scripts', 'slider_settings', $settings );
	wp_localize_script( 'oxygen_footer_scripts_light', 'slider_settings', $settings );
}

/**
 * Oxygen site title.
 * 
 */
function oxygen_site_title() {
	
	if ( hybrid_get_setting( 'oxygen_logo_url' ) ) {	
	
		$tag = ( is_front_page() ) ? 'h1' : 'div';	
			
		echo '<' . $tag . ' id="site-title">' . "\n";
			echo '<a href="' . get_home_url() . '" title="' . get_bloginfo( 'name' ) . '" rel="Home">' . "\n";
				echo '<img class="logo" src="' . esc_url( hybrid_get_setting( 'oxygen_logo_url' ) ) . '" alt="' . get_bloginfo( 'name' ) . '" />' . "\n";
			echo '</a>' . "\n";
		echo '</' . $tag . '>' . "\n";
	
	} else {
	
		hybrid_site_title();
	
	}
}

?>