jQuery(document).ready(function () {
	jQuery("#disableexplanation").change(function() {
		if (jQuery("#disableexplanation").is(':checked')) {
			jQuery("p.desc").css("display","none");
		} else {
			jQuery("p.desc").css("display","block");
		}
	}).change();
	jQuery("#enablexmlsitemap").change(function() {
		if (jQuery("#enablexmlsitemap").is(':checked')) {
			jQuery("#sitemapinfo").css("display","block");
		} else {
			jQuery("#sitemapinfo").css("display","none");
		}
	}).change();
	jQuery("#cleanpermalinks").change(function() {
		if (jQuery("#cleanpermalinks").is(':checked')) {
			jQuery("#cleanpermalinksdiv").css("display","block");
		} else {
			jQuery("#cleanpermalinksdiv").css("display","none");
		}
	}).change();		
});

function wpseo_exportSettings() {
	jQuery.post(ajaxurl, { 
			action: 'wpseo_export_settings', 
		}, function(data) { 
			if (data) {
				jQuery('#exportbutton').attr('href', data);
				jQuery('#exportbutton').text('Download export file');
			}
		}
	);
}

function setWPOption( option, newval, hide, nonce ) {
	jQuery.post(ajaxurl, { 
			action: 'wpseo_set_option', 
			option: option,
			newval: newval,
			_wpnonce: nonce 
		}, function(data) {
			if (data)
				jQuery('#'+hide).hide();
		}
	);
}

function wpseo_killBlockingFiles( nonce ) {
	jQuery.post( ajaxurl, {
		action: 'wpseo_kill_blocking_files',
		_ajax_nonce: nonce
	}, function(data) {
		if (data == 'success')
			jQuery('#blocking_files').hide();
		else
			jQuery('#block_files').html(data);
	});
}
