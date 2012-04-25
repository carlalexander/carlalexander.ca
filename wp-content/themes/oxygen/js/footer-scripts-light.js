/* <![CDATA[ */

var jqu = jQuery.noConflict();

jqu( function () {
	
	/* Remove a class from the body tag if JavaScript is enabled */
	jqu( 'body' ).removeClass( 'no-js' );
	
	/* Masonry */
	var $container = jqu( '.hfeed-more');
	var width = $container.width();
	$container.imagesLoaded( function() {
		$container.masonry( {
			temSelector: '.hentry',
			columnWidth: width * 0.4787234042553191,
			gutterWidth: width * 0.0425531914893617,
			isResizable: true,
		} );
	} ); 		
	
	/* Cycle */
	jqu( '#featured-content' ).cycle( {
		slideExpr: '.featured-post',
		fx: 'fade',
		speed: 500,
		timeout: slider_settings.timeout,
		cleartypeNoBg: true,
		pager: '#slide-thumbs',
		slideResize:   true,
		containerResize: false,
		width: '100%',
		fit: 1,
		prev: '#slider-prev',
		next: '#slider-next',
		pagerAnchorBuilder: function( idx, slide ) { 
			// return selector string for existing anchor 
			return '#slide-thumbs li:eq(' + idx + ') a'; 
    	}
	} );
	
	/* FitVids */
	jqu( ".entry-content" ).fitVids();	
	
} );

/* ]]> */