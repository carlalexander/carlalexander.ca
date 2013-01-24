<?php
/*
 * Theme Settings
 *
 * @package Oxygen
 * @subpackage Template
 */
	
add_action( 'admin_menu', 'oxygen_theme_admin_setup' );

function oxygen_theme_admin_setup() {
    
	global $theme_settings_page;
	
	/* Get the theme settings page name */
	$theme_settings_page = 'appearance_page_theme-settings';

	/* Get the theme prefix. */
	$prefix = hybrid_get_prefix();

	/* Create a settings meta box only on the theme settings page. */
	add_action( 'load-appearance_page_theme-settings', 'oxygen_theme_settings_meta_boxes' );

	/* Add a filter to validate/sanitize your settings. */
	add_filter( "sanitize_option_{$prefix}_theme_settings", 'oxygen_theme_validate_settings' );
	
	/* Enqueue scripts */
	add_action( 'admin_enqueue_scripts', 'oxygen_admin_scripts' );
	
}

/* Adds custom meta boxes to the theme settings page. */
function oxygen_theme_settings_meta_boxes() {

	/* Add a custom meta box. */
	add_meta_box(
		'oxygen-theme-meta-box',			// Name/ID
		__( 'General settings', 'oxygen' ),	// Label
		'oxygen_theme_meta_box',			// Callback function
		'appearance_page_theme-settings',		// Page to load on, leave as is
		'normal',					// Which meta box holder?
		'high'					// High/low within the meta box holder
	);

	/* Add additional add_meta_box() calls here. */
}

/* Function for displaying the meta box. */
function oxygen_theme_meta_box() { ?>

	<table class="form-table">
	    
		<!-- Favicon upload -->
		<tr class="favicon_url">
			<th>
				<label for="<?php echo esc_attr( hybrid_settings_field_id( 'oxygen_favicon_url' ) ); ?>"><?php _e( 'Favicon:', 'oxygen' ); ?></label>
			</th>
			<td>
				<input type="text" id="<?php echo esc_attr( hybrid_settings_field_id( 'oxygen_favicon_url' ) ); ?>" name="<?php echo esc_attr( hybrid_settings_field_name( 'oxygen_favicon_url' ) ); ?>" value="<?php echo esc_url( hybrid_get_setting( 'oxygen_favicon_url' ) ); ?>" />
				<input id="oxygen_favicon_upload_button" class="button" type="button" value="Upload" />
				<br />
				<span class="description"><?php _e( 'Upload favicon image (recommended max size: 32x32).', 'oxygen' ); ?></span>
				
				<?php /* Display uploaded image */
				if ( hybrid_get_setting( 'oxygen_favicon_url' ) ) { ?>
                    <p><img src="<?php echo esc_url( hybrid_get_setting( 'oxygen_favicon_url' ) ); ?>" alt=""/></p>
				<?php } ?>
			</td>
		</tr>
		
		<!-- Logo upload -->
		<tr class="logo_url">
			<th>
				<label for="<?php echo esc_attr( hybrid_settings_field_id( 'oxygen_logo_url' ) ); ?>"><?php _e( 'Logo:', 'oxygen' ); ?></label>
			</th>
			<td>
				<input type="text" id="<?php echo esc_attr( hybrid_settings_field_id( 'oxygen_logo_url' ) ); ?>" name="<?php echo esc_attr( hybrid_settings_field_name( 'oxygen_logo_url' ) ); ?>" value="<?php echo esc_url( hybrid_get_setting( 'oxygen_logo_url' ) ); ?>" />
				<input id="oxygen_logo_upload_button" class="button" type="button" value="Upload" />
				<br />
				<span class="description"><?php _e( 'Upload logo image.', 'oxygen' ); ?></span>
				
				<?php /* Display uploaded image */
				if ( hybrid_get_setting( 'oxygen_logo_url' ) ) { ?>
                    <p><img src="<?php echo esc_url( hybrid_get_setting( 'oxygen_logo_url' ) ); ?>" alt=""/></p>
				<?php } ?>
			</td>
		</tr>		
		
		<!-- Title font family -->
		<tr>
			<th>
				<label for="<?php echo esc_attr( hybrid_settings_field_id( 'oxygen_font_family' ) ); ?>"><?php _e( 'Title font family:', 'oxygen' ); ?></label>
			</th>
			<td>
			    <select id="<?php echo esc_attr( hybrid_settings_field_id( 'oxygen_font_family' ) ); ?>" name="<?php echo hybrid_settings_field_name( 'oxygen_font_family' ); ?>">
				<option value="Abel" <?php selected( hybrid_get_setting( 'oxygen_font_family' ), 'Abel' ); ?>> <?php echo __( 'Abel', 'oxygen' ) ?> </option>				
				<option value="Oswald" <?php selected( hybrid_get_setting( 'oxygen_font_family' ), 'Oswald' ); ?>> <?php echo __( 'Oswald', 'oxygen' ) ?> </option>				
				<option value="Terminal Dosis" <?php selected( hybrid_get_setting( 'oxygen_font_family' ), 'Terminal Dosis' ); ?>> <?php echo __( 'Terminal Dosis', 'oxygen' ) ?> </option>
				<option value="Bitter" <?php selected( hybrid_get_setting( 'oxygen_font_family' ), 'Bitter' ); ?>> <?php echo __( 'Bitter', 'oxygen' ) ?> </option>
				<option value="Georgia" <?php selected( hybrid_get_setting( 'oxygen_font_family' ), 'Georgia' ); ?>> <?php echo __( 'Georgia', 'oxygen' ) ?> </option>
				<option value="Droid Serif" <?php selected( hybrid_get_setting( 'oxygen_font_family' ), 'Droid Serif' ); ?>> <?php echo __( 'Droid Serif', 'oxygen' ) ?> </option>				
				<option value="Helvetica" <?php selected( hybrid_get_setting( 'oxygen_font_family' ), 'Helvetica' ); ?>> <?php echo __( 'Helvetica', 'oxygen' ) ?> </option>
				<option value="Arial" <?php selected( hybrid_get_setting( 'oxygen_font_family' ), 'Arial' ); ?>> <?php echo __( 'Arial', 'oxygen' ) ?> </option>
				<option value="Verdana" <?php selected( hybrid_get_setting( 'oxygen_font_family' ), 'Verdana' ); ?>> <?php echo __( 'Verdana', 'oxygen' ) ?> </option>
				<option value="Droid Sans" <?php selected( hybrid_get_setting( 'oxygen_font_family' ), 'Droid Sans' ); ?>> <?php echo __( 'Droid Sans', 'oxygen' ) ?> </option>
			    </select>
				<span class="description"><?php _e( 'Choose a font for the titles.', 'oxygen' ); ?></span>
			</td>
		</tr>
		
		<!-- Font size -->
		<tr>
			<th>
			    <label for="<?php echo esc_attr( hybrid_settings_field_id( 'oxygen_font_size' ) ); ?>"><?php _e( 'Font size:', 'oxygen' ); ?></label>
			</th>
			<td>
			    <select id="<?php echo esc_attr( hybrid_settings_field_id( 'oxygen_font_size' ) ); ?>" name="<?php echo esc_attr( hybrid_settings_field_name( 'oxygen_font_size' ) ); ?>">
				<option value="16" <?php selected( hybrid_get_setting( 'oxygen_font_size' ), '16' ); ?>> <?php echo __( 'default', 'oxygen' ) ?> </option>
				<option value="17" <?php selected( hybrid_get_setting( 'oxygen_font_size' ), '17' ); ?>> <?php echo __( '17', 'oxygen' ) ?> </option>
				<option value="16" <?php selected( hybrid_get_setting( 'oxygen_font_size' ), '16' ); ?>> <?php echo __( '16', 'oxygen' ) ?> </option>
				<option value="15" <?php selected( hybrid_get_setting( 'oxygen_font_size' ), '15' ); ?>> <?php echo __( '15', 'oxygen' ) ?> </option>
				<option value="14" <?php selected( hybrid_get_setting( 'oxygen_font_size' ), '14' ); ?>> <?php echo __( '14', 'oxygen' ) ?> </option>				
				<option value="13" <?php selected( hybrid_get_setting( 'oxygen_font_size' ), '13' ); ?>> <?php echo __( '13', 'oxygen' ) ?> </option>
				<option value="12" <?php selected( hybrid_get_setting( 'oxygen_font_size' ), '12' ); ?>> <?php echo __( '12', 'oxygen' ) ?> </option>
			    </select>
			    <span class="description"><?php _e( 'The base font size in pixels.', 'oxygen' ); ?></span>
			</td>
		</tr>		
	    
		<!-- Link color -->
		<tr>
			<th>
				<label for="<?php echo esc_attr( hybrid_settings_field_id( 'oxygen_link_color' ) ); ?>"><?php _e( 'Link color:', 'oxygen' ); ?></label>
			</th>
			<td>
				<input type="text" id="<?php echo esc_attr( hybrid_settings_field_id( 'oxygen_link_color' ) ); ?>" name="<?php echo esc_attr( hybrid_settings_field_name( 'oxygen_link_color' ) ); ?>" size="8" value="<?php echo ( hybrid_get_setting( 'oxygen_link_color' ) ) ? esc_attr( hybrid_get_setting( 'oxygen_link_color' ) ) : '#0da4d3'; ?>" data-hex="true" />
				<div id="colorpicker_link_color"></div>
				<span class="description"><?php _e( 'Set the theme link color.', 'oxygen' ); ?></span>
			</td>
		</tr>
		
		<!-- Slider Timeout -->
		<tr>
			<th>
				<label for="<?php echo esc_attr( hybrid_settings_field_id( 'oxygen_slider_timeout' ) ); ?>"><?php _e( 'Slider Timeout:', 'oxygen' ); ?></label>
			</th>
			<td>
				<input type="text" id="<?php echo esc_attr( hybrid_settings_field_id( 'oxygen_slider_timeout' ) ); ?>" name="<?php echo esc_attr( hybrid_settings_field_name( 'oxygen_slider_timeout' ) ); ?>" value="<?php echo ( hybrid_get_setting( 'oxygen_slider_timeout' ) ) ? esc_attr( hybrid_get_setting( 'oxygen_slider_timeout' ) ) : '6000'; ?>" />
				<span class="description"><?php _e( 'The time interval between slides in milliseconds.', 'oxygen' ); ?></span>
			</td>
		</tr>
		
		<!-- Fancybox enable -->
		<tr>
			<th>
			    <label for="<?php echo esc_attr( hybrid_settings_field_id( 'oxygen_fancybox_enable' ) ); ?>"><?php _e( 'Fancybox:', 'oxygen' ); ?></label>
			</th>
			<td>
				<input class="checkbox" type="checkbox" <?php checked( hybrid_get_setting( 'oxygen_fancybox_enable' ), true ); ?> id="<?php echo esc_attr( hybrid_settings_field_id( 'oxygen_fancybox_enable' ) ); ?>" name="<?php echo esc_attr( hybrid_settings_field_name( 'oxygen_fancybox_enable' ) ); ?>" />
			    <span class="description"><?php _e( 'Check to enable the built-in <a href="http://fancybox.net/" target="_blank">Fancybox</a>.', 'oxygen' ); ?></span>
			</td>
		</tr>		

		<!-- Custom CSS -->
		<tr>
			<th>
				<label for="<?php echo esc_attr( hybrid_settings_field_id( 'oxygen_custom_css' ) ); ?>"><?php _e( 'Custom CSS:', 'oxygen' ); ?></label>
			</th>
			<td>
				<textarea id="<?php echo esc_attr( hybrid_settings_field_id( 'oxygen_custom_css' ) ); ?>" name="<?php echo esc_attr( hybrid_settings_field_name( 'oxygen_custom_css' ) ); ?>" cols="60" rows="8"><?php echo wp_htmledit_pre( stripslashes( hybrid_get_setting( 'oxygen_custom_css' ) ) ); ?></textarea>
				<span class="description"><?php _e( 'Add your custom CSS here. It would overwrite any default or custom theme settings.', 'oxygen' ); ?></span>
			</td>
		</tr>

		<!-- End custom form elements. -->
	</table><!-- .form-table --><?php
	
}

/* Validate theme settings. */
function oxygen_theme_validate_settings( $input ) {
    
	$input['oxygen_favicon_url'] = esc_url_raw( $input['oxygen_favicon_url'] );
	$input['oxygen_logo_url'] = esc_url_raw( $input['oxygen_logo_url'] );
	$input['oxygen_font_family'] = wp_filter_nohtml_kses( $input['oxygen_font_family'] );
	$input['oxygen_font_size'] = wp_filter_nohtml_kses( $input['oxygen_font_size'] );
    $input['oxygen_link_color'] = wp_filter_nohtml_kses( $input['oxygen_link_color'] );
	$input['oxygen_slider_timeout'] = wp_filter_nohtml_kses( intval( $input['oxygen_slider_timeout'] ) );
    $input['oxygen_custom_css'] = wp_filter_nohtml_kses( $input['oxygen_custom_css'] );
	$input['oxygen_fancybox_enable'] = ( isset( $input['oxygen_fancybox_enable'] ) ? 1 : 0 );	

    /* Return the array of theme settings. */
    return $input;
}

/* Enqueue scripts (and related stylesheets) */
function oxygen_admin_scripts( $hook_suffix ) {
    
    global $theme_settings_page;
	
    if ( $theme_settings_page == $hook_suffix ) {
	    
	    /* Enqueue Scripts */
	    wp_enqueue_script( 'oxygen_functions-admin', get_template_directory_uri() . '/admin/functions-admin.js', array( 'jquery', 'media-upload', 'thickbox', 'farbtastic' ), '1.0', false );

		/* Localize script strings */
		wp_localize_script( 'oxygen_functions-admin', 'js_text', array( 'insert_into_post' => __( 'Use this Image', 'oxygen' ) ) );		    
	    
	    /* Enqueue Styles */
	    wp_enqueue_style( 'functions-admin', get_template_directory_uri() . '/admin/functions-admin.css', false, 1.0, 'screen' );
	    wp_enqueue_style( 'farbtastic' );
    }
}

?>