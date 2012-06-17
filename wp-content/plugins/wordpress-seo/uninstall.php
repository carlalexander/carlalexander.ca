<?php

if( !defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') )
    exit();

foreach ( array('wpseo', 'wpseo_indexation', 'wpseo_permalinks', 'wpseo_titles', 'wpseo_rss', 'wpseo_internallinks', 'wpseo_xml', 'wpseo_social') as $option) {
	delete_option( $option );
}
