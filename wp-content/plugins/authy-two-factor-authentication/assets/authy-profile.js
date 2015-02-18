(function($){
	var container = $('#authy_user');

	container.html( '<tr><th><label>' + window.Authy.th_text + '</label></th><td><a class="button thickbox" href="' + window.Authy.ajax + '&KeepThis=true&TB_iframe=true&height=380&width=450">' + window.Authy.button_text + '</a></td></tr>' );

	$( '.button', container ).on( 'click', function( ev ) {
		ev.preventDefault();
	} );
})(jQuery);