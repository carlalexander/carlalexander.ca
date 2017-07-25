<?php

/**
 * Twenty Fourteen (carlalexander.ca) functions and definitions
 */

/**
 * Add custom favicon.
 */
function twentyfourteen_add_favicon() {
	echo '<link rel="Shortcut Icon" type="image/x-icon" href="'.get_bloginfo('stylesheet_directory').'/favicon.ico" />' . "\n";
}
add_action('wp_head', 'twentyfourteen_add_favicon');

/**
 * Enqueue the TwentyFourteen stylesheet.
 */
function twentyfourteen_parent_theme_enqueue_styles() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');

}
add_action('wp_enqueue_scripts', 'twentyfourteen_parent_theme_enqueue_styles');

/**
 * Enqueue the TwentyFourteen child theme scripts.
 */
function twentyfourteen_enqueue_scripts() {
    if (is_front_page() || is_single()) {
        wp_enqueue_script('child-script-headerbar', get_stylesheet_directory_uri() . '/js/headerbar.js', array('jquery'));
    }
}
add_action('wp_enqueue_scripts', 'twentyfourteen_enqueue_scripts');

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

/**
 * Remove separator on the homepage.
 *
 * @param string $title
 * @param string $separator
 *
 * @return string
 */
function twentyfourteen_homepage_title($title, $separator) {
    if (!is_home()) {
        return $title;
    }

    return str_ireplace(" $separator ", ' ', $title);
}
add_filter('wp_title', 'twentyfourteen_homepage_title', 99, 2);
