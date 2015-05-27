<?php
//main edit interface

function cub_get_children( $term, $depth = 0 ){

	
	$prefix = null;
	for( $i = 0; $i < $depth; $i++ ){
		$prefix .= '&nbsp;';
	}
	$term->name = $prefix . $term->name;


	$out_terms = array( $term );
	$terms = get_terms( $term->taxonomy, array( 'parent' => $term->term_id ) );
	if( !empty( $terms ) ){
		foreach( $terms as $term ){
			$out_terms = array_merge( $out_terms, cub_get_children( $term, ($depth+1) ) );
		}
		//$out_terms = array_merge( $out_terms, $terms )
	}
	//var_dump( $terms );

	return $out_terms;

}



global $wpdb;

$caldera_url_builer = Caldera_URL_Builder_Options::get_all();
// load post types
$post_type_args = array(
	'public' => true
);

$post_types = get_post_types( $post_type_args, 'objects' );

$caldera_url_builer['content_types'] = array();
$caldera_url_builer['archives'] = array();
$caldera_url_builer['taxonomies'] = array();
$caldera_url_builer['types'] = array(
	'Post Type'	=> array(),
	'Post Type Archive'	=> array(),
	'Taxonomy'	=> array(),
);	

foreach( $post_types as $post_type=>$post_object ){

	$caldera_url_builer['content_types'][ $post_type ] = (array) $post_object->rewrite;
	$caldera_url_builer['types']['Post Type'][ $post_type ] = $post_type;
	if( false !== $post_object->has_archive ){
		$caldera_url_builer['content_types'][ $post_type . '_archive' ] = (array) $post_object->rewrite;
		$caldera_url_builer['archives'][ $post_type . '_archive' ] = true;
		$caldera_url_builer['types']['Post Type Archive'][ $post_type . '_archive' ] = $post_type . '_archive';
	}
	// get taxos

	$taxonomies = get_object_taxonomies( $post_type );
	if( !empty( $taxonomies ) ){
		foreach ( $taxonomies as $taxonomy_name  ) {

			$caldera_url_builer['types']['Taxonomy'][ 'taxonomy_' . $taxonomy_name ] = $taxonomy_name;
			$caldera_url_builer['taxonomies']['taxonomy_' . $taxonomy_name] = true;
			$taxonomy = get_taxonomy( $taxonomy_name );
			$caldera_url_builer['content_types']['taxonomy_' . $taxonomy_name ] = array(
				'name'	=>	$taxonomy->name,
				'label'	=>	$taxonomy->label,
				'terms'	=>	array()
			);
			$caldera_url_builer['content_types'][ $post_type ]['taxonomies'][ $taxonomy_name ] = array(
				'name'	=>	$taxonomy->name,
				'label'	=>	$taxonomy->label,
				'terms'	=>	array()
			);

			$terms = get_terms( $taxonomy->name, array( 'parent' => 0 ) );
			if( !empty( $terms ) ){
				$terms_sorted = array();
				// parents only
				foreach( $terms as  $term ){
					$terms_sorted = array_merge( $terms_sorted, cub_get_children( $term ) );
				}

				$caldera_url_builer['content_types'][ $post_type ]['taxonomies'][ $taxonomy_name ]['terms'] = $terms_sorted;	
			}

			

		}
	}

	// get custom field keys
	//@todo in version 2.0
	/*$custom_field_keys = $wpdb->get_results( $wpdb->prepare( "SELECT 
	 	
	 	`" . $wpdb->postmeta . "`.`meta_key` AS `meta_key`, 
	 	`" . $wpdb->postmeta . "`.`meta_value` AS `meta_value`

		FROM `" . $wpdb->postmeta . "` 
		LEFT JOIN `" . $wpdb->posts . "` ON (`" . $wpdb->postmeta . "`.`post_id` = `" . $wpdb->posts . "`.`ID`)
		
		WHERE
		`" . $wpdb->posts . "`.`post_type` = %s
		AND
		SUBSTR(`" . $wpdb->postmeta . "`.`meta_key`,1,1) != '_'", $post_type ) );
	
	if( !empty( $custom_field_keys ) ){
		var_dump( $custom_field_keys );
		die;
		foreach( $custom_field_keys as $custom_fields ){

			$caldera_url_builer['content_types'][ $post_type ]['custom_fields'][ $custom_fields->meta_key ] = $custom_fields->meta_key;

		}

	}*/

}



?>
<div class="wrap" id="caldera-url-builder-main-canvas">
	<span class="wp-baldrick spinner" style="float: none; display: block;" data-target="#caldera-url-builder-main-canvas" data-callback="cub_canvas_init" data-type="json" data-request="#caldera-url-builder-live-config" data-event="click" data-template="#main-ui-template" data-autoload="true"></span>
</div>

<div class="clear"></div>

<input type="hidden" class="clear" autocomplete="off" id="caldera-url-builder-live-config" style="width:100%;" value="<?php echo esc_attr( json_encode($caldera_url_builer) ); ?>">

<script type="text/html" id="main-ui-template">
	<?php
	// pull in the join table card template
	include CUB_PATH . 'includes/templates/main-ui.php';
	?>	
</script>
<script type="text/html" id="license-modal-template">
	<?php
	// pull in the join table card template
	include CUB_PATH . 'includes/templates/license-modal.php';
	?>	
</script>
<script type="text/javascript">
	
	function cub_caldera_url_builder_license(el){

		var caldera_url_builder 	= jQuery(el),
			nonce	= jQuery('#cub_license_nonce'),
			refer	= jQuery('[name="_wp_http_referer"]'),
			action 	= jQuery('#caldera-url-builder-action'),
			key 	= jQuery("#caldera-url-builder-license_key");

		if( key.val().length === 0 ){
			key.focus();
			return false;
		}

		jQuery('#caldera-url-builder-license_key_loader').css('display', 'inline-block');
		caldera_url_builder.data({
			'code' : key.val(),
			'cub_license_nonce' : nonce.val(),
			'_wp_http_referer' : refer.val(),
			'license_action' : action.val()
		}); 

	}

</script>



