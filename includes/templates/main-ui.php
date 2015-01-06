<div class="caldera-easy-rewrites-main-headerwordpress">
		<h2>
		<?php _e( 'Caldera Easy Rewrites', 'caldera-easy-rewrites' ); ?> <span class="caldera-easy-rewrites-version"><?php echo CEW_VER; ?></span>
		<button style="cursor:pointer;" class="add-new-h2 wp-baldrick" id="caldera-easy-rewrites-rebuild-rules" data-event="click" data-action="cew_rebuild_rules" data-active-class="none" data-load-element="#caldera-easy-rewrites-save-indicator"><?php _e('Rebuild Rules', 'caldera-easy-rewrites') ; ?></button>
		<span style="position: absolute; top: 8px;" id="caldera-easy-rewrites-save-indicator"><span style="float: none; margin: 16px 0px -5px 10px;" class="spinner"></span></span>

	</h2>
			<div class="subsubsub caldera-easy-rewrites-nav-tabs">
								
		</div>		
		<div class="clear"></div>

	<span class="wp-baldrick" id="caldera-easy-rewrites-field-sync" data-event="refresh" data-target="#caldera-easy-rewrites-main-canvas" data-callback="cew_canvas_init" data-type="json" data-request="#caldera-easy-rewrites-live-config" data-template="#main-ui-template"></span>	
</div>
<div class="caldera-easy-rewrites-sub-headerwordpress">
	<h2 class="caldera-easy-rewrites-sub-tabs caldera-easy-rewrites-nav-tabs nav-tab-wrapper">
				<a class="{{#is _current_tab value="#caldera-easy-rewrites-panel-routes"}}nav-tab-active {{/is}}caldera-easy-rewrites-nav-tab nav-tab" href="#caldera-easy-rewrites-panel-routes"><?php _e('Rules', 'caldera-easy-rewrites') ; ?></a>
		<a class="{{#is _current_tab value="#caldera-easy-rewrites-panel-about"}}nav-tab-active {{/is}}caldera-easy-rewrites-nav-tab nav-tab" href="#caldera-easy-rewrites-panel-about"><?php _e('About', 'caldera-easy-rewrites') ; ?></a>

	</h2>
</div>

<form id="caldera-easy-rewrites-main-form" action="?page=caldera_easy_rewrites" method="POST">
	<?php wp_nonce_field( 'caldera-easy-rewrites', 'caldera-easy-rewrites-setup' ); ?>
	<input type="hidden" value="caldera_easy_rewrites" name="id" id="caldera_easy_rewrites-id">
	<input type="hidden" value="{{_current_tab}}" name="_current_tab" id="caldera-easy-rewrites-active-tab">
	<input type="hidden" value="{{json content_types}}" name="content_types">

		<div id="caldera-easy-rewrites-panel-routes" class="caldera-easy-rewrites-editor-panel" {{#is _current_tab value="#caldera-easy-rewrites-panel-routes"}}{{else}} style="display:none;" {{/is}}>		
		<h4><?php _e('Rewite Routes', 'caldera-easy-rewrites') ; ?> <small class="description"><?php _e('Rules', 'caldera-easy-rewrites') ; ?></small></h4>
		<?php
		// pull in the general settings template
		include CEW_PATH . 'includes/templates/routes-panel.php';
		?>
	</div>	<div id="caldera-easy-rewrites-panel-about" class="caldera-easy-rewrites-editor-panel" {{#is _current_tab value="#caldera-easy-rewrites-panel-about"}}{{else}} style="display:none;" {{/is}}>		
		<h4><?php _e('Caldera Easy Rewrites', 'caldera-easy-rewrites') ; ?> <small class="description"><?php _e('About', 'caldera-easy-rewrites') ; ?></small></h4>
		<?php
		// pull in the general settings template
		include CEW_PATH . 'includes/templates/about-panel.php';
		?>
	</div>

	
	<div class="clear"></div>
	<div class="caldera-easy-rewrites-footer-bar">
		<button type="submit" class="button button-primary wp-baldrick" data-action="cew_save_config" data-active-class="none" data-load-element="#caldera-easy-rewrites-save-indicator" data-callback="cew_rebuild_trigger" data-before="cew_get_config_object" ><?php _e('Save Changes', 'caldera-easy-rewrites') ; ?></button>
	</div>	

</form>
{{#unless _current_tab}}
	{{#script}}
		jQuery(function($){
			$('.caldera-easy-rewrites-nav-tab').first().trigger('click');
		});
	{{/script}}
{{/unless}}