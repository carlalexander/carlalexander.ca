<?php

$extra_classes = '';

if ( ! get_the_title() ) {
	$extra_classes = ' no-title';
}

?>

<li <?php post_class( 'post-preview' . $extra_classes ); ?> id="post-<?php the_ID(); ?>">

	<?php

    $isLink = has_post_format('link');
	$title_args = array();

	if ( is_sticky() ) {
		$title_args = array(
			'before' => __( 'Sticky post:', 'mcluhan' ) . ' ',
		);
	}

	?>

	<a href="<?= $isLink ? mcluhan_get_first_url() : get_the_permalink(); ?>" title="<?php the_title_attribute( $title_args ); ?>">
        <?php
        if ($isLink) {
            echo sprintf('<span style="display: flex"><img src="%s" alt="External link" title="External link" />', get_stylesheet_directory_uri() . '/assets/images/external.svg');
        }

		$sticky = is_sticky() ? '<div class="sticky-arrow"></div>'  : '';
		the_title( '<h2 class="title">' . $sticky . '<span>', '</span></h2>' );

		// Check setting for the order of month and day
		$format_setting = get_theme_mod( 'mcluhan_preview_date_format' );
		$date_format = ( $format_setting && 'month-day' == $format_setting ) ? 'M j' : 'j M';

		$date = date_i18n( $date_format, get_the_time( 'U' ) );

		// Check setting for outputting date in lowercase
		if ( get_theme_mod( 'mcluhan_preview_date_lowercase' ) ) {
			$date = strtolower( $date );
		}

        if ($isLink) {
            echo '</span>';
        }

		// Output date
        if (!is_search()) {
            echo '<time>' . $date . '</time>';
        }

		?>
	</a>

</li>
