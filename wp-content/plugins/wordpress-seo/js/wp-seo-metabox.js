function yst_clean( str, cleanalphanumeric ) { 
	if ( str == '' || str == undefined )
		return '';
	
	try {
		if ( cleanalphanumeric == true )
			str = str.replace(/[^a-zA-Z0-9\s]/, '');
		str = str.replace(/<\/?[^>]+>/gi, ''); 
		str = str.replace(/\[(.+?)\](.+?\[\/\\1\])?/, '');
	} catch(e) {}
	return str;
}

function ptest(str, p) {
	str = yst_clean( str, true );
	str = str.toLowerCase();
	var r = str.match(p);
	if (r != null)
		return '<span class="good">Yes ('+r.length+')</span>';
	else
		return '<span class="wrong">No</span>';
}

function testFocusKw() {
	// Retrieve focus keyword and trim
	var focuskw = jQuery.trim( jQuery('#yoast_wpseo_focuskw').val() );
	focuskw = focuskw.toLowerCase();
	
	var postname = jQuery('#editable-post-name-full').text();
	var url	= wpseo_permalink_template.replace('%postname%', postname).replace('http://','');

	var p = new RegExp("(^\|[ \n\r\t.,'\"\+!?:-]+)"+focuskw+"($\|[ \n\r\t.,'\"\+!?:-]+)",'gim');
	var p2 = new RegExp(focuskw.replace(/\s+/g,"[-_\\\//]"),'gim');
	if (focuskw != '') {
		var html = '<p>Your focus keyword was found in:<br/>';
		html += 'Article Heading: ' + ptest( jQuery('#title').val(), p ) + '<br/>';
		html += 'Page title: ' + ptest( jQuery('#wpseosnippet .title').text(), p ) + '<br/>';
		html += 'Page URL: ' + ptest( url, p2 ) + '<br/>';
		html += 'Content: ' + ptest( jQuery('#content').val(), p ) + '<br/>';
		html += 'Meta description: ' + ptest( jQuery('#yoast_wpseo_metadesc').val(), p );
		html += '</p>';
		jQuery('#focuskwresults').html(html);
	}
}

function updateTitle( force ) {
	if ( jQuery("#yoast_wpseo_title").val() ) {
		var title = jQuery("#yoast_wpseo_title").val();
	} else {
		var title = wpseo_title_template.replace('%%title%%', jQuery('#title').val() );
	}
	if ( title == '' )
		return;

	title = jQuery('<div />').html(title).text();

	if ( force ) 
		jQuery('#yoast_wpseo_title').val( title );

	title = yst_clean( title );
	title = jQuery.trim( title );

	if ( title.length > 70 ) {
		var space = title.lastIndexOf( " ", 67 );
		title = title.substring( 0, space ).concat( ' <strong>...</strong>' );
	}
	var len = 70 - title.length;
	if (len < 0)
		len = '<span class="wrong">'+len+'</span>';
	else
		len = '<span class="good">'+len+'</span>';

	title = boldKeywords( title, false );

	jQuery('#wpseosnippet .title').html( title );
	jQuery('#yoast_wpseo_title-length').html( len );
	testFocusKw();
}

function updateDesc( desc ) {
	var autogen 	= false;
	var desc 		= jQuery.trim( yst_clean( jQuery("#yoast_wpseo_metadesc").val() ) );
	var color 		= '#000';

	if ( desc == '' ) {
		if ( wpseo_metadesc_template != '' ) {
			var excerpt = yst_clean( jQuery("#excerpt").val() );
			desc = wpseo_metadesc_template.replace('%%excerpt_only%%', excerpt);
			desc = desc.replace('%%excerpt%%', excerpt);
		}

		desc = jQuery.trim ( desc );

		if ( desc == '' ) {
			desc = jQuery("#content").val();
			desc = yst_clean( desc );
			var focuskw = jQuery.trim( jQuery('#yoast_wpseo_focuskw').val() );
			if ( focuskw != '' ) {
				var descsearch = new RegExp( focuskw, 'gim');
				if ( desc.search(descsearch) != -1 ) {
					desc = desc.substr( desc.search(descsearch), wpseo_meta_desc_length );
				} else {
					desc = desc.substr( 0, wpseo_meta_desc_length );
				}
			} else {
				desc = desc.substr( 0, wpseo_meta_desc_length );
			}
			var color = "#888";
			autogen = true;			
		}
	}

	if ( !autogen )
		var len = wpseo_meta_desc_length - desc.length;
	else
		var len = wpseo_meta_desc_length;
		
	if (len < 0)
		len = '<span class="wrong">'+len+'</span>';
	else
		len = '<span class="good">'+len+'</span>';

	if ( autogen || desc.length > wpseo_meta_desc_length ) {
		var space = desc.lastIndexOf( " ", ( wpseo_meta_desc_length - 3 ) );
		desc = desc.substring( 0, space ).concat( ' <strong>...</strong>' );
	}

	desc = boldKeywords( desc, false );

	jQuery('#yoast_wpseo_metadesc-length').html(len);
	jQuery("#wpseosnippet .desc span.content").css( 'color', color );
	jQuery("#wpseosnippet .desc span.content").html( desc );
	testFocusKw();
}

function updateURL() {
	var name = jQuery('#editable-post-name-full').text();
	var url	= wpseo_permalink_template.replace('%postname%', name).replace('http://','');
	url = boldKeywords( url, true );
	jQuery("#wpseosnippet .url").html( url );
	testFocusKw();
}

function boldKeywords( str, url ) {
	focuskw = jQuery.trim( jQuery('#yoast_wpseo_focuskw').val() );

	if ( focuskw == '' ) 
		return str;
		
	if ( focuskw.search(' ') != -1 ) {
		var keywords 	= focuskw.split(' ');
	} else {
		var keywords	= new Array( focuskw );
	}
	for (var i=0;i<keywords.length;i++) {
		var kw		= yst_clean( keywords[i] );
		if ( url ) {
			var kw 	= kw.replace(' ','-').toLowerCase();
			kwregex = new RegExp( "([-/])("+kw+")([-/])?" );
		} else {
			kwregex = new RegExp( "(^\|[ \n\r\t.,'\"\+!?:-]+)("+kw+")($\|[ \n\r\t.,'\"\+!?:-]+)", 'gim' );
		}
		str 	= str.replace( kwregex, "$1<strong>$2</strong>$3" );
	}
	return str;
}

function updateSnippet() {
	updateURL();
	updateTitle();
	updateDesc();
}

jQuery(document).ready(function(){	
	// Tabs, based on code by Pete Mall - https://github.com/PeteMall/Metabox-Tabs
	jQuery('.wpseo-metabox-tabs li a').each(function(i) {
		var thisTab = jQuery(this).parent().attr('class').replace(/active /, '');

		if ( 'active' != jQuery(this).attr('class') )
			jQuery('div.' + thisTab).hide();

		jQuery('div.' + thisTab).addClass('wpseo-tab-content');

		jQuery(this).click(function(){
			// hide all child content
			jQuery(this).parent().parent().parent().children('div').hide();

			// remove all active tabs
			jQuery(this).parent().parent('ul').find('li.active').removeClass('active');

			// show selected content
			jQuery(this).parent().parent().parent().find('div.'+thisTab).show();
			jQuery(this).parent().parent().parent().find('li.'+thisTab).addClass('active');
		});
	});

	jQuery('.wpseo-heading').hide();
	jQuery('.wpseo-metabox-tabs').show();
	// End Tabs code
	
	jQuery('#related_keywords_heading').hide();
	
	var cache = {}, lastXhr;
	jQuery('#yoast_wpseo_focuskw').autocomplete({
		minLength: 3,
		source: function( request, response ) {
			var term = request.term;
			if ( term in cache ) {
				response( cache[ term ] );
				return;
			}
			request._ajax_nonce = wpseo_keyword_suggest_nonce;
			request.action = 'wpseo_get_suggest';
			
			lastXhr = jQuery.getJSON( ajaxurl, request, function( data, status, xhr ) {
				cache[ term ] = data;
				if ( xhr === lastXhr ) {
					console.log(data);
					response( data );
				}
			});
		}
	});
	
	jQuery('#yoast_wpseo_title').keyup( function() {
		updateTitle();		
	});
	jQuery('#yoast_wpseo_metadesc').keyup( function() {
		updateDesc();
	});
	jQuery('#excerpt').keyup( function() {
		updateDesc();
	});
	
	jQuery('#yoast_wpseo_title').live('change', function() {
		updateTitle();
	});
	jQuery('#yoast_wpseo_metadesc').live('change', function() {
		updateDesc();
	});
	jQuery('#yoast_wpseo_focuskw').live('change', function() {
		jQuery('#wpseo_relatedkeywords').show();
		jQuery('#wpseo_tag_suggestions').hide();
		jQuery('#related_keywords_heading').hide();
	});
	jQuery('#excerpt').live('change', function() {
		updateDesc();
	});
	jQuery('#content').live('change', function() {
		updateDesc();
	});
	jQuery('#tinymce').live('change', function() {
		updateDesc();
	});
	jQuery('#titlewrap #title').live('change', function() {
		updateTitle();
	});
	jQuery('#wpseo_regen_title').click(function() {
		updateTitle(1);
		return false;
	});

	jQuery('#wpseo_relatedkeywords').click(function() {
		if (jQuery('#yoast_wpseo_focuskw').val() == '')
			return false;
		jQuery.getJSON("http://boss.yahooapis.com/ysearch/web/v1/"+jQuery('#yoast_wpseo_focuskw').val()+"?"
			+"appid=NTPCcr7V34Gspq8myEAxcQZs2w.WLOE2a2z.p.1WjSc_u5XQn9xnf8n_N9oOCOs-"
			+"&lang="+wpseo_lang
			+"&format=json"
			+"&count=50"
			+"&view=keyterms"
			+"&callback=?",
			function (data) {
				var keywords = new Array();
				
				if ( data['ysearchresponse']['resultset_web'] != undefined ) {
					jQuery.each(data['ysearchresponse']['resultset_web'], function(i,item) {
						if ( item['keyterms']['terms'] != undefined ) {
							jQuery.each(item['keyterms']['terms'], function(i,kw) {
								key = kw.toLowerCase();

								if ( key != undefined ) {
									if (keywords[key] == undefined)
										keywords[key] = 1;
									else
										keywords[key]++;
								}
							});
						}
					});

					var result = '<p class="clear">';
					for (key in keywords) {
						if (keywords[key] > 5)
							result += '<span class="wpseo_yahoo_kw">' + key + '</span>';
					}
					result += '</p>';
					jQuery('#wpseo_tag_suggestions').html( result );
					jQuery('#related_keywords_heading').show();
				}
			});	
		jQuery(this).hide();
		return false;
	});
	
	updateSnippet();
});