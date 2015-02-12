<?php

$caldera_easy_rewrites = get_option( '_caldera_easy_rewrites' );
// load post types
$post_type_args = array(
	'rewrite'				=>	true,
);

$post_types = get_post_types( $post_type_args, 'objects' );

$caldera_easy_rewrites['content_types'] = array();

foreach( $post_types as $post_type=>$post_object ){
	
	$caldera_easy_rewrites['content_types'][ $post_type ] = (array) $post_object->rewrite;
	// get taxos
	$taxonomies = get_taxonomies( array( 'object_type' => array( $post_type ) ), 'objects' );
	if( !empty( $taxonomies ) ){
		foreach ( $taxonomies as $taxonom_name => $taxonomy) {
			$caldera_easy_rewrites['content_types'][ $post_type ]['taxonomies'][ $taxonom_name ] = array(
				'name'	=>	$taxonomy->name,
				'label'	=>	$taxonomy->label,
				'terms'	=>	get_terms( $taxonomy->name )
			);
		}
	}
}

?>
<div class="wrap" id="caldera-easy-rewrites-main-canvas">
	<span class="wp-baldrick spinner" style="float: none; display: block;" data-target="#caldera-easy-rewrites-main-canvas" data-callback="cew_canvas_init" data-type="json" data-request="#caldera-easy-rewrites-live-config" data-event="click" data-template="#main-ui-template" data-autoload="true"></span>
</div>

<div class="clear"></div>

<input type="hidden" class="clear" autocomplete="off" id="caldera-easy-rewrites-live-config" style="width:100%;" value="<?php echo esc_attr( json_encode($caldera_easy_rewrites) ); ?>">

<script type="text/html" id="main-ui-template">
	<?php
	// pull in the join table card template
	include CEW_PATH . 'includes/templates/main-ui.php';
	?>	
</script>





