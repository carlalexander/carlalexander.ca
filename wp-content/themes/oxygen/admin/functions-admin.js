jQuery(document).ready(function($) {
              
    // Upload image         
    $('#oxygen_favicon_upload_button, #oxygen_logo_upload_button').click(function() {
        formfield = $(this).prev().attr('id');
        tb_show('', 'media-upload.php?post_id=0&type=image&TB_iframe=true');
        return false;
    });

    // Insert the image url into the input field
    window.send_to_editor = function(html) {       
        fileurl = $('img', html).attr('src');  
        $('#' + formfield).val(fileurl);     
        tb_remove();
    } 

    // Colorpicker
    jQuery('#colorpicker_link_color').farbtastic('#oxygen_theme_settings-oxygen_link_color');
    
    jQuery('#oxygen_theme_settings-oxygen_link_color').blur( function() {
            jQuery('#colorpicker_link_color').hide();
    });
    
    jQuery('#oxygen_theme_settings-oxygen_link_color').focus( function() {
            jQuery('#colorpicker_link_color').show();
    });
             
});