<?php

global $wpdb;

$caldera_url_builer = Caldera_URL_Builder_Options::get_all();
// load post types
$post_type_args = array(
	'public' => true
);

$post_types = get_post_types( $post_type_args, 'objects' );

$caldera_url_builer['content_types'] = array();

foreach( $post_types as $post_type=>$post_object ){

	$caldera_url_builer['content_types'][ $post_type ] = (array) $post_object->rewrite;
	// get taxos

	$taxonomies = get_object_taxonomies( $post_type );
	if( !empty( $taxonomies ) ){
		foreach ( $taxonomies as $taxonomy_name  ) {

			$taxonomy = get_taxonomy( $taxonomy_name );

			$caldera_url_builer['content_types'][ $post_type ]['taxonomies'][ $taxonomy_name ] = array(
				'name'	=>	$taxonomy->name,
				'label'	=>	$taxonomy->label,
				'terms'	=>	get_terms( $taxonomy->name )
			);
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





