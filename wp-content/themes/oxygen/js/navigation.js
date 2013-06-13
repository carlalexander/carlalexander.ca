/**
 * Handles toggling the navigation menu for small screens.
 */

( function ( $ ) {

    var nav, button, menu;

    button = $( ".menu-toggle" );
        if ( ! button ) 
            return;    

    button.click( function() {
    
        nav = $(this).closest( ".site-navigation" );
        if ( ! nav ) 
            return;
        
        menu = $( "ul:first", nav );
        if ( ! menu.length || ! menu.children().length ) {
            button.hide();
            return;
        }

        menu.toggleClass( 'nav-menu' );

        if ( ! $(this).hasClass( 'toggled-on' ) ) {
            $(this).removeClass( 'toggled-on' );
            menu.removeClass( 'toggled-on' );
        } else {
            menu.addClass( 'toggled-on' );
            $(this).addClass( 'toggled-on' );
        }

    } );

} ) ( jQuery );