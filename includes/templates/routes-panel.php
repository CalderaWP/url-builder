<?php

	// Panel template for Rewrite Routes	

?>


{{#each rewrite}}
<div class="caldera-url-builder-rule-wrapper">
	<input type="hidden" name="rewrite[{{_id}}][_id]" value="{{_id}}">
	<button type="button" class="button" data-remove-parent=".caldera-url-builder-rule-wrapper" style="font-size: 17px; padding: 0px 9px; margin: 1px 4px 0px 0px;">&times;</button>
	{{#if content_type}}
		<input type="hidden" name="used_types[{{content_type}}]" value="{{content_type}}">
		<input type="hidden" name="rewrite[{{_id}}][content_type]" value="{{content_type}}">
		<span class="caldera-url-builder-segment-slug">{{#find @root/archives content_type}}<span style="color: rgb(143, 143, 143);" class="dashicons dashicons-portfolio"></span>{{else}}<span style="color: rgb(143, 143, 143);" class="dashicons dashicons-admin-post"></span>{{/find}} {{content_type}}</span>
		<span class="caldera-url-builder-segment" style="color: rgb(159, 159, 159);">:</span>
		<input type="hidden" name="rewrite[{{_id}}][slug]" value="{{#if slug}}{{slug}}{{else}}{{content_type}}{{/if}}" data-format="key" data-sync="#preview-slug-{{_id}}" required>
		<span class="caldera-url-builder-segment" style="color: rgb(159, 159, 159);">/</span>
		<span class="caldera-url-builder-segment-list">
			{{#each segment}}
			<span class="caldera-url-builder-rule-segment">
				
				<input type="hidden" name="rewrite[{{../_id}}][segment][{{_id}}][_id]" value="{{_id}}">

				<select data-live-sync="true" name="rewrite[{{../_id}}][segment][{{_id}}][type]" style="vertical-align: unset;" class="required">
					<option></option>
					{{#find ../../../content_types ../../content_type}}
						{{#if taxonomies}}
						<option value="taxonomy" {{#is ../../type value="taxonomy"}}selected="selected"{{/is}}><?php _e( 'Taxonomy', 'caldera-url-builder' ); ?></option>
						{{/if}}
					{{/find}}
					<option value="static" {{#is type value="static"}}selected="selected"{{/is}}><?php _e( 'Static String', 'caldera-url-builder' ); ?></option>
				</select>

				{{#is type value="taxonomy"}}


					{{#find ../../../../content_types ../../content_type}}

						<select data-live-sync="true" name="rewrite[{{../../../_id}}][segment][{{../../_id}}][taxonomy]" style="vertical-align: unset; margin-left: -5px;" class="required">
							<option value=""></option>
						{{#each taxonomies}}
							<option value="{{name}}" {{#is ../../taxonomy value="name"}}selected="selected"{{/is}}>{{label}}</option>
						{{/each}}
						</select>
						
						{{#find taxonomies ../taxonomy}}
							
							<select data-live-sync="true" name="rewrite[{{../../../../_id}}][segment][{{../../../_id}}][default]" style="vertical-align: unset; margin-left: -5px;" placeholder="<?php _e( 'Default', 'caldera-url-builder' ); ?>">
								<option value="" disabled><?php _e( 'Select Default', 'caldera-url-builder' ); ?></option>
								{{#each terms}}
									<option value="{{slug}}" {{#is ../../../default value="slug"}}selected="selected"{{/is}}>{{name}}</option>
								{{/each}}
							</select>
							
						{{/find}}
					{{/find}}

				{{/is}}

				{{#is type value="static"}}
					<input type="text" name="rewrite[{{../../_id}}][segment][{{_id}}][path]" value="{{path}}" data-sync="#preview-segment-{{_id}}" data-live-sync="true" data-format="key" class="required">
				{{/is}}

				<button dlass="caldera-url-builder-remove-segment" style="display: inline; font-size: 17px; padding: 0px 5px; border-radius: 0px 4px 4px 0px; margin: 1px 2px 0px -6px;" type="button" class="button" data-remove-parent=".caldera-url-builder-rule-segment">&times;</button>
				<span class="caldera-url-builder-segment" style="color: rgb(159, 159, 159);">/</span>
			</span>
			{{/each}}
		</span>
		<button type="button" {{#unless segment}}data-autoload="true"{{/unless}} class="button add-new-segment wp-baldrick" data-request="cub_get_default_setting" type="button" data-script="add-segment" data-node="{{_id}}" style="font-size: 17px; padding: 0px 9px; margin: 1px 4px 0px 0px;">&plus;</button>


		<div class="caldera-url-builder-segment-preview preview-type-{{content_type}}">
		<input type="hidden" name="rewrite[{{_id}}][pass]" value="" class="route-pass-flag">
		<?php echo site_url( ); ?> <span class="caldera-url-builder-segment" style="color: rgb(159, 159, 159);">/</span>
		<?php /*<span id="preview-slug-{{_id}}">{{#if slug}}{{slug}}{{else}}{{content_type}}{{/if}}</span>
		<span class="caldera-url-builder-segment" style="color: rgb(159, 159, 159);">/</span> */ ?>
		{{#each segment}}
			<span id="preview-segment-{{_id}}">
			{{#is type value="taxonomy"}}<span class="caldera-easy-rewrite-variable">&lcub;{{taxonomy}}&rcub;</span>{{/is}}
			{{#is type value="static"}}{{path}}{{/is}}</span>
			<span class="caldera-url-builder-segment" style="color: rgb(159, 159, 159);">/</span>
		{{/each}}
		{{#find @root/archives content_type}}
			{{else}}
			<span class="caldera-easy-rewrite-variable">&lcub;{{content_type}}_slug&rcub;</span>
			<span class="caldera-url-builder-segment" style="color: rgb(159, 159, 159);">/</span>
		{{/find}}
		</div>

	{{else}}
		<input type="hidden" name="new_node" value="true">
		<select class="caldera-easy-rewrite-new-rule-select required" data-live-sync="true" data-script="add-segment" name="rewrite[{{_id}}][content_type]" style="vertical-align: unset;">
		<option></option>
		{{#each ../../content_types}}
			{{#find ../../../used_types @key}}
			<option value="{{@key}}" disabled="disabled">{{@key}}</option>
			{{else}}
			<option value="{{@key}}" {{#is ../content_type value="@key"}}selected="selected"{{/is}}>{{@key}}</option>
			{{/find}}
		{{/each}}
		</select>
	{{/if}}	
</div>
<div></div>
{{/each}}

<hr>
<button id="caldera-easy-rewrite-add-rule-button" type="button" class="button wp-baldrick" data-request="cub_get_default_setting" type="button" data-add-node="rewrite"><?php _e( 'Add Rule', 'caldera-url-builder' ); ?></button>

<div id="rewrite-notice-error" style="display:none;"><?php _e('Rewrite clashes with: ', 'caldera-url-builder'); ?></div>
<div id="rewrite-notice-success" style="display:none;"><?php _e('Looks good!', 'caldera-url-builder'); ?></div>
<div id="rewrite-notice-warning" style="display:none;"><?php _e('Looks good, but will clash with "attachments".', 'caldera-url-builder'); ?></div>

{{#script}}
if( jQuery('.caldera-easy-rewrite-new-rule-select').length ){
	jQuery('#caldera-easy-rewrite-add-rule-button').prop('disabled', 'true').remove();
}
{{/script}}
