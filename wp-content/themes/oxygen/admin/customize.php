<?php
/**
 * Add theme settings to the Theme Customizer.
 * 
 * @package Oxygen
 * @subpackage Functions
 * @since 0.3
 */

/* Register custom sections, settings, and controls. */
add_action( 'customize_register', 'oxygen_customize_register' );

/* Output settings CSS into the head. */
add_action( 'wp_head', 'oxygen_customize_css');

/* Enqueue Google fonts */
add_action( 'wp_enqueue_scripts', 'oxygen_google_fonts' );

/* Slider settings */
add_action( 'wp_footer', 'oxygen_slider_settings' );

/**
 * Register custom sections, settings, and controls.
 * 
 */
function oxygen_customize_register( $wp_customize ) {


	/* -------------- S E C T I O N S --------------- */

	/* Section: Typography */
	$wp_customize->add_section( 'oxygen_typography' , array(
		'title'      => __( 'Typography', 'oxygen' ),
		'priority'   => 30,
	) );

	/* Section: Miscellaneous */
	$wp_customize->add_section( 'oxygen_misc' , array(
		'title'      => __( 'Miscellaneous', 'oxygen' ),
		'priority'   => 190,
	) );	


	/* -------------- S E T T I N G S --------------- */

	/* Setting: Font Family */
	$wp_customize->add_setting( 'oxygen_font_family' , array(
		'default'     => 'Abel',
		'capability'  => 'edit_theme_options',
		'sanitize_callback' => 	'oxygen_font_family_sanitize'
	) );	

	/* Setting: Font Size */
	$wp_customize->add_setting( 'oxygen_font_size' , array(
		'default'     => '16',
		'capability'  => 'edit_theme_options',
		'sanitize_callback' => 	'oxygen_font_size_sanitize'
	) );	

	/* Setting: Link Color */
	$wp_customize->add_setting( 'oxygen_link_color' , array(
		'default'     => '#0da4d3',
		'capability'  => 'edit_theme_options',
		'sanitize_callback' => 	'oxygen_link_color_sanitize'
	) );

	/* Setting: Slider Timeout */
	$wp_customize->add_setting( 'oxygen_slider_timeout' , array(
		'default'     => '6000',
		'capability'  => 'edit_theme_options',
		'sanitize_callback' => 	'oxygen_slider_timeout_sanitize'
	) );

	/* Setting: Fancybox */
	$wp_customize->add_setting( 'oxygen_fancybox_enable' , array(
		'default'     => false,
		'capability'  => 'edit_theme_options',
		'sanitize_callback' => 	'oxygen_fancybox_enable_sanitize'
	) );		

	/* Setting: Custom CSS */
	$wp_customize->add_setting( 'oxygen_custom_css' , array(
		'default'     => hybrid_get_setting( 'oxygen_custom_css' ) ? hybrid_get_setting( 'oxygen_custom_css' ) : '',
		'capability'  => 'edit_theme_options',
		'sanitize_callback' => 	'oxygen_custom_css_sanitize'
	) );				


	/* -------------- C O N T R O L S --------------- */

	/* Control: Font Family */
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'oxygen_font_family', array(
		'label'     => __( 'Font Family', 'oxygen' ),
		'section'   => 'oxygen_typography',
		'settings'  => 'oxygen_font_family',
		'type'		=> 'select',
		'choices'	=> array (
			'Abel' 		=> 'Abel',
			'Oswald' 		=> 'Oswald',
			'Terminal Dosis' 	=> 'Terminal Dosis',
			'PT Serif' 		=> 'PT Serif',
			'Bitter'		=> 'Bitter',
			'Georgia' 		=> 'Georgia',
			'Droid Serif' 	=> 'Droid Serif',			
			'Helvetica' 	=> 'Helvetica',
			'Istok Web' 	=> 'Istok Web',
			'Arial' 		=> 'Arial',	
			'Verdana' 		=> 'Verdana',
			'Lucida Sans Unicode' => 'Lucida Sans Unicode',
			'Droid Sans' 	=> 'Droid Sans'
			),
		'priority'  => 1
	) ) );		

	/* Control: Font Size */
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'oxygen_font_size', array(
		'label'     => __( 'Font Size', 'oxygen' ),
		'section'   => 'oxygen_typography',
		'settings'  => 'oxygen_font_size',
		'type'		=> 'select',
		'choices'	=> array (
			'18' 		=> '18',
			'17' 		=> '17',
			'16' 		=> '16',
			'15' 		=> '15',
			'14' 		=> '14',
			'13' 		=> '13',
			'12' 		=> '12'
			),
		'priority'  => 1
	) ) );		

	/* Control: Link Color */
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'oxygen-link-color', array(
		'label'     => __( 'Link Color', 'oxygen' ),
		'section'   => 'colors',
		'settings'  => 'oxygen_link_color',
		'priority'  => 1
	) ) );

	/* Control: Slider Timeout */
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'oxygen-slider-timeout', array(
		'label'     => __( 'Slider Timeout', 'oxygen' ),
		'section'   => 'oxygen_misc',
		'settings'  => 'oxygen_slider_timeout',
		'priority'  => 1
	) ) );

	/* Control: Fancybox */
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'oxygen_fancybox_enable', array(
		'label'     => __( 'Fancybox', 'oxygen' ),
		'section'   => 'oxygen_misc',
		'settings'  => 'oxygen_fancybox_enable',
		'type' => 'checkbox',
		'priority'  => 1
	) ) );			

	/* Control: Custom CSS */
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'oxygen-custom-css', array(
		'label'     => __( 'Custom CSS', 'oxygen' ),
		'section'   => 'oxygen_misc',
		'settings'  => 'oxygen_custom_css',
		'priority'  => 1
	) ) );			

}

/**
 * Sanitize the "Font Family" setting.
 * 
 */
function oxygen_font_family_sanitize( $setting, $object ) {

	if ( 'oxygen_font_family' == $object->id )
		$setting = wp_filter_nohtml_kses( $setting );

	return $setting;
}

/**
 * Sanitize the "Font Size" setting.
 * 
 */
function oxygen_font_size_sanitize( $setting, $object ) {

	if ( 'oxygen_font_size' == $object->id )
		$setting = wp_filter_nohtml_kses( intval( $setting ) );

	return $setting;
}

/**
 * Sanitize the "Link Color" setting.
 * 
 */
function oxygen_link_color_sanitize( $setting, $object ) {

	if ( 'oxygen_link_color' == $object->id )
		$setting = wp_filter_nohtml_kses( $setting );

	return $setting;
}

/**
 * Sanitize the "Slider Timeout" setting.
 * 
 */
function oxygen_slider_timeout_sanitize( $setting, $object ) {

	if ( 'oxygen_slider_timeout' == $object->id )
		$setting = wp_filter_nohtml_kses( $setting );

	return $setting;
}

/**
 * Sanitize the "Fancybox" setting.
 * 
 */
function oxygen_fancybox_enable_sanitize( $setting, $object ) {

	if ( 'oxygen_fancybox_enable' == $object->id )
		$setting = wp_filter_nohtml_kses( $setting );

	return $setting;
}

/**
 * Sanitize the "Custom CSS" setting.
 * 
 */
function oxygen_custom_css_sanitize( $setting, $object ) {

	if ( 'oxygen_custom_css' == $object->id )
		$setting = wp_filter_nohtml_kses( $setting );

	return $setting;
}

/**
 * Output settings CSS into the head.
 * 
 */
function oxygen_customize_css() { ?>

	<style type="text/css">

		/* Font size. */
		<?php if ( get_theme_mod( 'oxygen_font_size' ) ) { // legacy setting ?>
			html { font-size: <?php echo get_theme_mod( 'oxygen_font_size' ); ?>px; }
		<?php } elseif ( hybrid_get_setting( 'oxygen_font_size' ) ) { ?>
			html { font-size: <?php echo hybrid_get_setting( 'oxygen_font_size' ); ?>px; }
		<?php } ?>

		/* Font family. */
		<?php if ( get_theme_mod( 'oxygen_font_family' ) ) { // legacy setting ?>
			h1, h2, h3, h4, h5, h6, dl dt, blockquote, blockquote blockquote blockquote, #site-title, #menu-primary li a { font-family: '<?php echo get_theme_mod( 'oxygen_font_family' ); ?>', serif; }
		<?php } elseif ( hybrid_get_setting( 'oxygen_font_family' ) ) { ?>
			h1, h2, h3, h4, h5, h6, dl dt, blockquote, blockquote blockquote blockquote, #site-title, #menu-primary li a { font-family: '<?php echo hybrid_get_setting( 'oxygen_font_family' ); ?>', serif; }
		<?php } ?>

		/* Link color. */
		<?php if ( get_theme_mod( 'oxygen_link_color' ) ) { // legacy setting ?>
			a, a:visited, .page-template-front .hfeed-more .hentry .entry-title a:hover, .entry-title a, .entry-title a:visited { color: <?php echo get_theme_mod( 'oxygen_link_color' ); ?>; }
			.read-more, .read-more:visited, .pagination a:hover, .comment-navigation a:hover, #respond #submit, .button, a.button, #subscribe #subbutton, .wpcf7-submit, #loginform .button-primary { background-color: <?php echo get_theme_mod( 'oxygen_link_color' ); ?>; }
		<?php } elseif ( hybrid_get_setting( 'oxygen_link_color' ) ) { ?>
			a, a:visited, .page-template-front .hfeed-more .hentry .entry-title a:hover, .entry-title a, .entry-title a:visited { color: <?php echo hybrid_get_setting( 'oxygen_link_color' ); ?>; }
			.read-more, .read-more:visited, .pagination a:hover, .comment-navigation a:hover, #respond #submit, .button, a.button, #subscribe #subbutton, .wpcf7-submit, #loginform .button-primary { background-color: <?php echo hybrid_get_setting( 'oxygen_link_color' ); ?>; }
		<?php } ?>
		a:hover, a:focus { color: #000; }
		.read-more:hover, #respond #submit:hover, .button:hover, a.button:hover, #subscribe #subbutton:hover, .wpcf7-submit:hover, #loginform .button-primary:hover { background-color: #111; }

		/* Custom CSS. */
		<?php if ( get_theme_mod( 'oxygen_custom_css' ) ) { // legacy setting
			echo get_theme_mod( 'oxygen_custom_css' ) . "\n"; 
		} else {
			echo hybrid_get_setting( 'oxygen_custom_css' ) . "\n";
		} ?>
	
	</style>	

<?php }

/**
 * Enqueue Google fonts.
 *
 */
function oxygen_google_fonts() {
	
	if ( get_theme_mod( 'oxygen_font_family' ) ) {
		
		switch ( get_theme_mod( 'oxygen_font_family' ) ) {
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

	} elseif ( hybrid_get_setting( 'oxygen_font_family' ) ) {

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
 * Slider settings
 *
 */
function oxygen_slider_settings() {

	if ( get_theme_mod( 'oxygen_slider_timeout' ) ) {
		$timeout = get_theme_mod( 'oxygen_slider_timeout' );
	} elseif ( hybrid_get_setting( 'oxygen_slider_timeout' ) ) {
		$timeout = hybrid_get_setting( 'oxygen_slider_timeout' );
	} else {
		$timeout = '6000';
	}

	$settings = array( 'timeout' => $timeout );
	wp_localize_script( 'oxygen_footer_scripts', 'slider_settings', $settings );
	wp_localize_script( 'oxygen_footer_scripts_light', 'slider_settings', $settings );
}

?>