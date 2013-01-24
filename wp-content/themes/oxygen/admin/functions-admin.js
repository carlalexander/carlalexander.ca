jQuery(document).ready(function($) {

    var formfield;
              
    // Upload image         
    $('#oxygen_favicon_upload_button, #oxygen_logo_upload_button').click(function() {
        formfield = $(this).prev().attr('id');
        tbframe_interval = setInterval(function() {
            $('#TB_iframeContent').contents().find('.savesend input[type="submit"]').val(js_text.insert_into_post);
        }, 500);
        tb_show('', 'media-upload.php?post_id=0&type=image&TB_iframe=true');
        return false;
    });

    // Insert the image url into the input field
    window.original_send_to_editor = window.send_to_editor;
    window.send_to_editor = function(html) {  
        if (formfield) {      
            fileurl = $('img', html).attr('src');  
            $('#' + formfield).val(fileurl);     
            tb_remove();
            formfield = '';
        } else {
            window.original_send_to_editor(html);
        }
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