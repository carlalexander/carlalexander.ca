(function($) {
    $(document).ready(function() {
        $(window).scroll(function () {

            if ($(window).scrollTop() > 400)
                $('#headerbar-container').animate({'height': '48px'}, 500);
            else
                $('#headerbar-container').stop(true).animate({'height': '0px'}, 500);
        });
    });
})(jQuery);
