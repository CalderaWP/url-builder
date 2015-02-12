<?php

	// Panel template for Rewite Routes	

?>


{{#each rewrite}}
<div class="caldera-easy-rewrites-rule-wrapper">
	<input type="hidden" name="rewrite[{{_id}}][_id]" value="{{_id}}">
	<button type="button" class="button" data-remove-parent=".caldera-easy-rewrites-rule-wrapper" style="font-size: 17px; padding: 0px 9px; margin: 1px 4px 0px 0px;">&times;</button>
	{{#if content_type}}
		<input type="hidden" name="used_types[{{content_type}}]" value="{{content_type}}">
		<input type="hidden" name="rewrite[{{_id}}][content_type]" value="{{content_type}}">
		<span class="caldera-easy-rewrites-segment-slug">{{content_type}}</span>
		<span class="caldera-easy-rewrites-segment" style="color: rgb(159, 159, 159);">:</span>
		<input type="text" name="rewrite[{{_id}}][slug]" value="{{#if slug}}{{slug}}{{else}}{{content_type}}{{/if}}" data-format="key" data-sync="#preview-slug-{{_id}}">
		<span class="caldera-easy-rewrites-segment" style="color: rgb(159, 159, 159);">/</span> 
		{{#each segment}}
		<span class="caldera-easy-rewrites-rule-segment">
			
			<input type="hidden" name="rewrite[{{../_id}}][segment][{{_id}}][_id]" value="{{_id}}">

			<select data-live-sync="true" name="rewrite[{{../_id}}][segment][{{_id}}][type]" style="vertical-align: unset;">
				<option></option>
				{{#find ../../../content_types ../../content_type}}
					{{#if taxonomies}}
					<option value="taxonomy" {{#is ../../type value="taxonomy"}}selected="selected"{{/is}}><?php _e( 'Taxonomy', 'caldera-easy-rewrites' ); ?></option>
					{{/if}}
				{{/find}}
				<option value="static" {{#is type value="static"}}selected="selected"{{/is}}><?php _e( 'Static String', 'caldera-easy-rewrites' ); ?></option>
			</select>

			{{#is type value="taxonomy"}}


				{{#find ../../../../content_types ../../content_type}}

					<select data-live-sync="true" name="rewrite[{{../../../_id}}][segment][{{../../_id}}][taxonomy]" style="vertical-align: unset; margin-left: -5px;">
						<option value=""></option>
					{{#each taxonomies}}
						<option value="{{name}}" {{#is ../../taxonomy value="name"}}selected="selected"{{/is}}>{{label}}</option>
					{{/each}}
					</select>
					<?php /*
					{{#find taxonomies ../taxonomy}}
						{{#if terms}}
						<select data-live-sync="true" name="rewrite[{{../../../../../_id}}][segment][{{../../../../_id}}][default]" style="vertical-align: unset; margin-left: -5px;" placeholder="<?php _e( 'Default', 'caldera-easy-rewrites' ); ?>">
							<option value=""></option>
							{{#each terms}}
								<option value="{{slug}}" {{#is ../../../../default value="slug"}}selected="selected"{{/is}}>{{slug}}</option>
							{{/each}}
						</select>
						{{/if}}
					{{/find}} */ ?>
				{{/find}}

			{{/is}}

			{{#is type value="static"}}
				<input type="text" name="rewrite[{{../../_id}}][segment][{{_id}}][path]" value="{{path}}" data-sync="#preview-segment-{{_id}}" data-format="key" class="required">
			{{/is}}

			<button style="display: inline; font-size: 17px; padding: 0px 5px; border-radius: 0px 4px 4px 0px; margin: 1px 2px 0px -6px;" type="button" class="button" data-remove-parent=".caldera-easy-rewrites-rule-segment">&times;</button>
			<span class="caldera-easy-rewrites-segment" style="color: rgb(159, 159, 159);">/</span> 
		</span>
		{{/each}}
		<button type="button" class="button wp-baldrick" data-request="cew_get_default_setting" type="button" data-script="add-segment" data-node="{{_id}}" style="font-size: 17px; padding: 0px 9px; margin: 1px 4px 0px 0px;">&plus;</button> 

		<div class="caldera-easy-rewrites-segment-preview">
		<?php echo site_url( ); ?> <span class="caldera-easy-rewrites-segment" style="color: rgb(159, 159, 159);">/</span>
		<span id="preview-slug-{{_id}}">{{#if slug}}{{slug}}{{else}}{{content_type}}{{/if}}</span>
		<span class="caldera-easy-rewrites-segment" style="color: rgb(159, 159, 159);">/</span>
		{{#each segment}}
			<span id="preview-segment-{{_id}}">
			{{#is type value="taxonomy"}}<span class="caldera-easy-rewrite-variable">&lcub;{{taxonomy}}&rcub;</span>{{/is}}
			{{#is type value="static"}}{{path}}{{/is}}</span>
			<span class="caldera-easy-rewrites-segment" style="color: rgb(159, 159, 159);">/</span>
		{{/each}}
		</div>

	{{else}}
		<input type="hidden" name="new_node" value="true">
		<select data-live-sync="true" name="rewrite[{{_id}}][content_type]" style="vertical-align: unset;">
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
{{/each}}

<hr>
<button type="button" class="button wp-baldrick" data-request="cew_get_default_setting" type="button" data-add-node="rewrite"><?php _e( 'Add Rule', 'caldera-easy-rewrites' ); ?></button>
