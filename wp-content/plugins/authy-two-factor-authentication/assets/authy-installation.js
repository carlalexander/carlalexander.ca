jQuery(document).ready(function($) {
  $(".request-sms-link").on( 'click', function( ev ) {
    ev.preventDefault();

    var self = $(this);

    if (! self.hasClass('disable')) {
      self.addClass('disable');

      var username = self.data('username');
      var signature = self.data('signature');

      $.ajax({
        url:  AuthyAjax.ajaxurl,
        data: ({action : 'request_sms_ajax', username: username, signature: signature}),
        success: function(msg) {
          alert(msg);
          self.removeClass('disable');
        }
      });
    }
  });
});