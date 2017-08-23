<?php
/**
 * Fix network admin URL to include the "/wp/" base
 *
 * @see https://core.trac.wordpress.org/ticket/23221
 */
add_filter( 'network_site_url', function( $url, $path, $scheme ){
    $urls_to_fix = array(
        '/wp-admin/network/',
        '/wp-login.php',
        '/wp-activate.php',
        '/wp-signup.php',
    );

    foreach( $urls_to_fix as $maybe_fix_url ) {
        $fixed_wp_url = '/wp' . $maybe_fix_url;
        if ( false !== stripos( $url, $maybe_fix_url )
            && false === stripos( $url, $fixed_wp_url ) ) {
            $url = str_replace( $maybe_fix_url, $fixed_wp_url, $url );
        }
    }

    return $url;
}, 10, 3 );