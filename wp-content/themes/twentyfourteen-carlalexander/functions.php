<?php

/**
 * Twenty Fourteen (carlalexander.ca) functions and definitions
 */

/**
 * Print HTML with meta information for the current post-date/time and author.
 */
function twentyfourteen_posted_on() {
	if ( is_sticky() && is_home() && ! is_paged() ) {
		echo '<span class="featured-post">' . __( 'Sticky', 'twentyfourteen' ) . '</span>';
	}

	// Set up and print post meta information.
	printf( '<span class="byline"><span class="author vcard"><a class="url fn n" href="%1$s" rel="author">%2$s</a></span></span>',
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		get_the_author()
	);
}