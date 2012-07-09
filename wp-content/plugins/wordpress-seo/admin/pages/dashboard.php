<?php
/**
 * @package Admin
 */

global $wpseo_admin_pages;

$options = get_option( 'wpseo' );

$wpseo_admin_pages->admin_header( 'General', true, 'yoast_wpseo_options', 'wpseo' );

echo $wpseo_admin_pages->hidden( 'ignore_blog_public_warning' );
echo $wpseo_admin_pages->hidden( 'ignore_tour' );
echo $wpseo_admin_pages->hidden( 'ignore_page_comments' );
echo $wpseo_admin_pages->hidden( 'ignore_permalink' );
echo $wpseo_admin_pages->hidden( 'ms_defaults_set' );
echo $wpseo_admin_pages->hidden( 'version' );

if ( isset( $options[ 'blocking_files' ] ) && is_array( $options[ 'blocking_files' ] ) && count( $options[ 'blocking_files' ] ) > 0 ) {
	$options[ 'blocking_files' ] = array_unique( $options[ 'blocking_files' ] );
	echo '<p id="blocking_files" class="wrong">'
		. '<a href="javascript:wpseo_killBlockingFiles(\'' . wp_create_nonce( 'wpseo-blocking-files' ) . '\')" class="button fixit">' . __( 'Fix it.', 'wordpress-seo' ) . '</a>'
		. __( 'The following file(s) is/are blocking your XML sitemaps from working properly:', 'wordpress-seo' ) . '<br />';
	foreach ( $options[ 'blocking_files' ] as $file ) {
		echo esc_html( $file ) . '<br/>';
	}
	echo __( 'Either delete them (this can be done with the "Fix it" button) or disable WP SEO XML sitemaps.', 'wordpress-seo' );
	echo '</p>';
}

if ( strpos( get_option( 'permalink_structure' ), '%postname%' ) === false && !isset( $options[ 'ignore_permalink' ] ) )
	echo '<p id="wrong_permalink" class="wrong">'
		. '<a href="' . admin_url( 'options-permalink.php' ) . '" class="button fixit">' . __( 'Fix it.', 'wordpress-seo' ) . '</a>'
		. '<a href="javascript:wpseo_setIgnore(\'permalink\',\'wrong_permalink\',\'' . wp_create_nonce( 'wpseo-ignore' ) . '\');" class="button fixit">' . __( 'Ignore.', 'wordpress-seo' ) . '</a>'
		. __( 'You do not have your postname in the URL of your posts and pages, it is highly recommended that you do. Consider setting your permalink structure to <strong>/%postname%/</strong>.', 'wordpress-seo' ) . '</p>';

if ( get_option( 'page_comments' ) && !isset( $options[ 'ignore_page_comments' ] ) )
	echo '<p id="wrong_page_comments" class="wrong">'
		. '<a href="javascript:setWPOption(\'page_comments\',\'0\',\'wrong_page_comments\',\'' . wp_create_nonce( 'wpseo-setoption' ) . '\');" class="button fixit">' . __( 'Fix it.', 'wordpress-seo' ) . '</a>'
		. '<a href="javascript:wpseo_setIgnore(\'page_comments\',\'wrong_page_comments\',\'' . wp_create_nonce( 'wpseo-ignore' ) . '\');" class="button fixit">' . __( 'Ignore.', 'wordpress-seo' ) . '</a>'
		. __( 'Paging comments is enabled, this is not needed in 999 out of 1000 cases, so the suggestion is to disable it, to do that, simply uncheck the box before "Break comments into pages..."', 'wordpress-seo' ) . '</p>';

echo '<h2>' . __( 'General', 'wordpress-seo' ) . '</h2>';

if ( isset( $options[ 'ignore_tour' ] ) && $options[ 'ignore_tour' ] ) {
	echo '<label class="select">' . __( 'Introduction Tour:', 'wordpress-seo' ) . '</label><a class="button-secondary" href="' . admin_url( 'admin.php?page=wpseo_dashboard&wpseo_restart_tour' ) . '">' . __( 'Start Tour', 'wordpress-seo' ) . '</a>';
	echo '<p class="desc label">' . __( 'Take this tour to quickly learn about the use of this plugin.', 'wordpress-seo' ) . '</p>';
}

echo '<label class="select">' . __( 'Default Settings:', 'wordpress-seo' ) . '</label><a class="button-secondary" href="' . admin_url( 'admin.php?page=wpseo_dashboard&wpseo_reset_defaults' ) . '">' . __( 'Reset Default Settings', 'wordpress-seo' ) . '</a>';
echo '<p class="desc label">' . __( 'If you want to restore a site to the default WordPress SEO settings, press this button.', 'wordpress-seo' ) . '</p>';

echo '<h2>' . __( 'Security', 'wordpress-seo' ) . '</h2>';
echo $wpseo_admin_pages->checkbox( 'disableadvanced_meta', __( 'Disable the Advanced part of the WordPress SEO meta box', 'wordpress-seo' ) );
echo '<p class="desc">' . __( 'Unchecking this box allows authors and editors to redirect posts, noindex them and do other things you might not want if you don\'t trust your authors.', 'wordpress-seo' ) . '</p>';

echo '<h2>' . __( 'Webmaster Tools', 'wordpress-seo' ) . '</h2>';
echo '<p>' . __( 'You can use the boxes below to verify with the different Webmaster Tools, if your site is already verified, you can just forget about these. Enter the verify meta values for:', 'wordpress-seo' ) . '</p>';
echo $wpseo_admin_pages->textinput( 'googleverify', '<a target="_blank" href="https://www.google.com/webmasters/tools/dashboard?hl=en&amp;siteUrl=' . urlencode( get_bloginfo( 'url' ) ) . '%2F">' . __( 'Google Webmaster Tools', 'wordpress-seo' ) . '</a>' );
echo $wpseo_admin_pages->textinput( 'msverify', '<a target="_blank" href="http://www.bing.com/webmaster/?rfp=1#/Dashboard/?url=' . str_replace( 'http://', '', get_bloginfo( 'url' ) ) . '">' . __( 'Bing Webmaster Tools', 'wordpress-seo' ) . '</a>' );
echo $wpseo_admin_pages->textinput( 'alexaverify', '<a target="_blank" href="http://www.alexa.com/pro/subscription">' . __( 'Alexa Verification ID', 'wordpress-seo' ) . '</a>' );

do_action( 'wpseo_dashboard' );

$wpseo_admin_pages->admin_footer();