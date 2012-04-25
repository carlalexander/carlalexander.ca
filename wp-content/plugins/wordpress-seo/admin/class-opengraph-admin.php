<?php 

class WPSEO_Social_Admin {

	public function __construct() {
		$options = get_wpseo_options();

		add_action( 'wpseo_tab_header', array(&$this, 'tab_header'), 60 );
		add_action( 'wpseo_tab_content', array(&$this, 'tab_content') );

		add_filter( 'wpseo_save_metaboxes', array(&$this, 'save_meta_boxes' ), 10, 1 );
	}

	public function tab_header() {
		echo '<li class="wpseo-social"><a href="javascript:void(null);">'.__('Social', 'wordpress-seo' ).'</a></li>';
	}

	public function tab_content() {
		global $wpseo_metabox;
		
		$content = '';
		foreach( $this->get_meta_boxes() as $meta_box ) {
			$content .= $wpseo_metabox->do_meta_box( $meta_box );
		}
		$wpseo_metabox->do_tab( 'wpseo-social', __('Social', 'wordpress-seo' ), $content );
	}
	
	public function get_meta_boxes( ) {
		$mbs = array();
		$options = get_option('wpseo');
		$mbs['opengraph-description'] = array(
			"name" => "opengraph-description",
			"type" => "textarea",
			"std" => "",
			"richedit" => false,
			"title" => __("Facebook Description", 'wordpress-seo' ),
			"description" => __('If you don\'t want to use the meta description for sharing the post on Facebook but want another description there, write it here.', 'wordpress-seo' )
		);
		$mbs['google-plus-description'] = array(
			"name" => "google-plus-description",
			"type" => "textarea",
			"std" => "",
			"richedit" => false,
			"title" => __("Google+ Description", 'wordpress-seo' ),
			"description" => __('If you don\'t want to use the meta description for sharing the post on Google+ but want another description there, write it here.', 'wordpress-seo' )
		);
		return $mbs;
	}
	
	public function save_meta_boxes( $mbs ) {
		$mbs = array_merge( $mbs, $this->get_meta_boxes() );
		return $mbs;
	}
	
}

$wpseo_social = new WPSEO_Social_Admin();