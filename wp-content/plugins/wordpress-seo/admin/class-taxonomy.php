<?php

class WPSEO_Taxonomy {
	
	function WPSEO_Taxonomy() {
		$options = get_wpseo_options();
		
		if (is_admin() && isset($_GET['taxonomy']) && 
			( !isset($options['tax-hideeditbox-'.$_GET['taxonomy']]) || !$options['tax-hideeditbox-'.$_GET['taxonomy']]) )
			add_action($_GET['taxonomy'] . '_edit_form', array(&$this,'term_additions_form'), 10, 2 );
		
		add_action('edit_term', array(&$this,'update_term'), 10, 3 );
	}
	
	function form_row( $id, $label, $desc, $tax_meta, $type = 'text', $options = '' ) {
		$val = '';
		if ( isset($tax_meta[$id]) )
			$val = stripslashes($tax_meta[$id]);
		
		echo '<tr class="form-field">'."\n";
		echo "\t".'<th scope="row" valign="top"><label for="'.$id.'">'.$label.':</label></th>'."\n";
		echo "\t".'<td>'."\n";
		if ($type == 'text') {
?>
			<input name="<?php echo $id; ?>" id="<?php echo $id; ?>" type="text" value="<?php echo $val; ?>" size="40"/>
			<p class="description"><?php echo $desc; ?></p>
<?php	
		} else if ($type == 'checkbox') {
?>
			<input name="<?php echo $id; ?>" id="<?php echo $id; ?>" type="checkbox" <?php checked($val); ?>/>
<?php
		} else if ($type == 'select') {
?>
			<select name="<?php echo $id; ?>" id="<?php echo $id; ?>">
				<?php foreach ($options as $option => $label) {
					$sel = '';
					if ($option == $val)
						$sel = " selected='selected'";
					echo "<option".$sel." value='".$option."'>".$label."</option>";
				}?>
			</select>
<?php
		}
		echo "\t".'</td>'."\n";
		echo '</tr>'."\n";
	
	}
	
	function term_additions_form( $term, $taxonomy ) {
		$tax_meta = get_option('wpseo_taxonomy_meta');
		$options = get_wpseo_options();
		
		if ( isset($tax_meta[$taxonomy][$term->term_id]) )
			$tax_meta = $tax_meta[$taxonomy][$term->term_id];

		echo '<h3>'.__( 'Yoast WordPress SEO Settings', 'wordpress-seo' ).'</h3>';
		echo '<table class="form-table">';

		$this->form_row( 'wpseo_title', __( 'SEO Title', 'wordpress-seo' ), __( 'The SEO title is used on the archive page for this term.', 'wordpress-seo' ), $tax_meta );
		$this->form_row( 'wpseo_desc', __( 'SEO Description', 'wordpress-seo' ), __( 'The SEO description is used for the meta description on the archive page for this term.', 'wordpress-seo' ), $tax_meta );
		if ( isset($options['usemetakeywords']) && $options['usemetakeywords'] )
			$this->form_row( 'wpseo_metakey', __( 'Meta Keywords', 'wordpress-seo' ), __( 'Meta keywords used on the archive page for this term.', 'wordpress-seo' ), $tax_meta );
		$this->form_row( 'wpseo_canonical', __( 'Canonical', 'wordpress-seo' ), __( 'The canonical link is shown on the archive page for this term.', 'wordpress-seo' ), $tax_meta );
		$this->form_row( 'wpseo_bctitle', __( 'Breadcrumbs Title', 'wordpress-seo' ), sprintf(__( 'The Breadcrumbs title is used in the breadcrumbs where this %s appears.', 'wordpress-seo' ), $taxonomy), $tax_meta );

		$this->form_row( 'wpseo_noindex', sprintf(__( 'Noindex this %s', 'wordpress-seo' ), $taxonomy), '', $tax_meta, 'checkbox' );
		$this->form_row( 'wpseo_nofollow', sprintf(__( 'Nofollow this %s', 'wordpress-seo' ),$taxonomy), '', $tax_meta, 'checkbox' );

		$this->form_row( 'wpseo_sitemap_include', __( 'Include in sitemap?', 'wordpress-seo' ), '', $tax_meta, 'select', array(
			"-" => __("Auto detect", 'wordpress-seo' ),
			"always" => __("Always include", 'wordpress-seo' ),
			"never" => __("Never include", 'wordpress-seo' ),
		) );

		echo '</table>';
	}
	
	function update_term( $term_id, $tt_id, $taxonomy ) {
		$tax_meta = get_option( 'wpseo_taxonomy_meta' );

		foreach (array('title', 'desc', 'metakey', 'bctitle', 'canonical', 'sitemap_include') as $key) {
			if ( isset($_POST['wpseo_'.$key]) )
				$tax_meta[$taxonomy][$term_id]['wpseo_'.$key] 	= $_POST['wpseo_'.$key];
		}

		foreach (array('noindex', 'nofollow') as $key) {
			if ( isset($_POST['wpseo_'.$key]) )
				$tax_meta[$taxonomy][$term_id]['wpseo_'.$key] = true;
			else
				$tax_meta[$taxonomy][$term_id]['wpseo_'.$key] = false;			
		}

		update_option( 'wpseo_taxonomy_meta', $tax_meta );

		if ( defined('W3TC_DIR') ) {
			require_once W3TC_DIR . '/lib/W3/ObjectCache.php';
		    $w3_objectcache = & W3_ObjectCache::instance();

		    $w3_objectcache->flush();			
		}
	}
}
$wpseo_taxonomy = new WPSEO_Taxonomy();
