<?php

/**
 * Twenty Fourteen (carlalexander.ca) functions and definitions
 */

/**
 * Add custom oEmbed providers.
 */
function twentyfourteen_add_oembed_providers() {
    wp_oembed_add_provider('#https?://speakerdeck.com/*/*#i', 'https://speakerdeck.com/oembed.json', true);
}
add_action('init', 'twentyfourteen_add_oembed_providers');

/**
 * Add a wrapper div to all oembeds with styling classes.
 *
 * @param string $html
 * @param object $data
 *
 * @return string
 */
function twentyfourteen_add_oembed_div($html, $data) {
    $class = 'oembed';

    if (!empty($data->provider_name)) {
        $class .= ' oembed-'.sanitize_title($data->provider_name);
    }

    return '<div class="'.$class.'">'.$html.'</div>';
}
add_filter('oembed_dataparse', 'twentyfourteen_add_oembed_div', 10, 2);

/**
 * Print HTML with meta information for the current post-date/time and author.
 */
function twentyfourteen_posted_on() {
	if ( is_sticky() && is_home() && ! is_paged() ) {
		echo '<span class="featured-post">' . __( 'Sticky', 'twentyfourteen' ) . '</span>';
	}

	// Set up and print post meta information.
	printf( '<span class="byline"><span class="author vcard">%1$s</span></span>',
		get_the_author()
	);
}
