<?php

function wpseo_set_option() {
	if ( ! current_user_can('manage_options') )
		die('-1');
	check_ajax_referer('wpseo-setoption');

	$option = $_POST['option'];
	if ( $option != 'page_comments' )
		die('-1');

	update_option( $option, 0 );
	die('1');
}
add_action('wp_ajax_wpseo_set_option', 'wpseo_set_option');

function wpseo_set_ignore() {
	if ( ! current_user_can('manage_options') )
		die('-1');
	check_ajax_referer('wpseo-ignore');

	$options = get_option('wpseo');
	$options['ignore_'.$_POST['option']] = 'ignore';
	update_option('wpseo', $options);
	die('1');
}
add_action('wp_ajax_wpseo_set_ignore', 'wpseo_set_ignore');

function wpseo_kill_blocking_files() {
	if ( ! current_user_can('manage_options') )
		die('-1');
	check_ajax_referer('wpseo-blocking-files');

	$message = 'There were no files to delete.';
	$options = get_option( 'wpseo' );
	if ( isset($options['blocking_files']) && is_array($options['blocking_files']) && count($options['blocking_files']) > 0 ) {
		$message = 'success';
		foreach ( $options['blocking_files'] as $k => $file ) {
			if ( ! @unlink( $file ) )
				$message = __( 'Some files could not be removed. Please remove them via FTP.', 'wordpress-seo' );
			else
				unset( $options['blocking_files'][$k] );
		}
		update_option( 'wpseo', $options );
	}

	die( $message );
}
add_action('wp_ajax_wpseo_kill_blocking_files', 'wpseo_kill_blocking_files');

function wpseo_get_suggest() {
	check_ajax_referer('wpseo-get-suggest');
	
	$term = urlencode( $_GET['term'] );
	$result = wp_remote_get( 'http://www.google.com/complete/search?output=toolbar&q='.$term );
	
	preg_match_all( '/suggestion data="([^"]+)"\/>/', $result['body'], $matches);
	
	echo json_encode( $matches[1] );
	die(); 
}
add_action('wp_ajax_wpseo_get_suggest', 'wpseo_get_suggest');