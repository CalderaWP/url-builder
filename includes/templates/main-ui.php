<div class="caldera-url-builder-main-headercaldera">
		<h2>
		<?php _e( 'Caldera URL Builder', 'caldera-url-builder' ); ?> <span class="caldera-url-builder-version"><?php echo CUB_VER; ?></span>

		<button type="submit" class="add-new-h2 wp-baldrick" data-action="cub_save_config" data-active-class="none" data-load-element="#caldera-url-builder-save-indicator" data-before="cub_get_config_object" ><?php _e('Save Changes', 'caldera-url-builder') ; ?></button>

		<span class="add-new-h2 wp-baldrick" data-modal="license-caldera-url-builder" data-modal-height="210" data-modal-width="402" data-modal-buttons='<?php _e( 'Update License', 'caldera-url-builder' ); ?>|{"data-active-class" : "nope", "data-action":"cub_save_license","data-before":"cub_caldera_url_builder_license", "data-template": "#license-modal-template", "data-target" : "#license-caldera-url-builder_baldrickModalBody"}' data-modal-title="<?php _e('Caldera URL Builder License', 'caldera-url-builder') ; ?>" data-action="cub_get_caldera_url_builder_license" data-template="#license-modal-template" style="margin-right: 0px;"><?php _e('License', 'caldera-url-builder') ; ?></span>

		<span class="wp-baldrick" id="caldera-url-builder-test-rules" data-event="testlines" data-autoload="true" data-action="cub_test_rules" data-before="cub_get_config_object" data-callback="cub_rebuild_results" data-active-class="none" data-load-element="#scaldera-url-builder-save-indicator"></span>
		<span style="display: inline-block;" id="caldera-url-builder-save-indicator"><span style="float: none; margin: 0px 0px -2px;" class="spinner"></span></span>

	</h2>
			<div class="subsubsub caldera-url-builder-nav-tabs">
								
		</div>		
		<div class="clear"></div>

	<span class="wp-baldrick" id="caldera-url-builder-field-sync" data-event="refresh" data-target="#caldera-url-builder-main-canvas" data-callback="cub_canvas_init" data-type="json" data-request="#caldera-url-builder-live-config" data-template="#main-ui-template"></span>
</div>
<div class="caldera-url-builder-sub-headercaldera">
	<ul class="caldera-url-builder-sub-tabs caldera-url-builder-nav-tabs">
		<li class="{{#is _current_tab value="#caldera-url-builder-panel-routes"}}active {{/is}}caldera-url-builder-nav-tab"><a href="#caldera-url-builder-panel-routes"><?php _e('Rules', 'caldera-url-builder') ; ?></a></li>
		<li class="{{#is _current_tab value="#caldera-url-builder-panel-about"}}active {{/is}}caldera-url-builder-nav-tab"><a href="#caldera-url-builder-panel-about"><?php _e('About', 'caldera-url-builder') ; ?></a></li>
	</ul>
</div>

<form id="caldera-url-builder-main-form" action="?page=caldera_easy_rewrites" method="POST">
	<?php wp_nonce_field( 'caldera-url-builder', 'caldera-url-builder-setup' ); ?>
	<input type="hidden" value="caldera_url_builder" name="id" id="caldera_easy_rewrites-id">
	<input type="hidden" value="{{_current_tab}}" name="_current_tab" id="caldera-url-builder-active-tab">
	<input type="hidden" value="{{json content_types}}" name="content_types">

		<div id="caldera-url-builder-panel-routes" class="caldera-url-builder-editor-panel" {{#is _current_tab value="#caldera-url-builder-panel-routes"}}{{else}} style="display:none;" {{/is}}>
		<h4><?php _e('Rewrite Routes', 'caldera-url-builder') ; ?> <small class="description"><?php _e('Rules', 'caldera-url-builder') ; ?></small></h4>
		<?php
		// pull in the general settings template
		include CUB_PATH . 'includes/templates/routes-panel.php';
		?>
	</div>	<div id="caldera-url-builder-panel-about" class="caldera-url-builder-editor-panel" {{#is _current_tab value="#caldera-url-builder-panel-about"}}{{else}} style="display:none;" {{/is}}>
		<h4><?php _e('Caldera URL Builder', 'caldera-url-builder') ; ?> <small class="description"><?php _e('About', 'caldera-url-builder') ; ?></small></h4>
		<?php
		// pull in the general settings template
		include CUB_PATH . 'includes/templates/about-panel.php';
		?>
	</div>

	
	<div class="clear"></div>

</form>
{{#unless _current_tab}}
	{{#script}}
		jQuery(function($){
			$('.caldera-url-builder-nav-tab').first().trigger('click');
		});
	{{/script}}
{{/unless}}
