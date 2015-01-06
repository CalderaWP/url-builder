<?php

	// Panel template for Rewite Routes
?>


{{#each rewrite}}
<div class="caldera-easy-rewrites-rule-wrapper" style="margin: 0px 0px 12px;">
	<input type="hidden" name="rewrite[{{_id}}][_id]" value="{{_id}}">
	<button type="button" class="button" data-remove-parent=".caldera-easy-rewrites-rule-wrapper" style="font-size: 17px; padding: 0px 9px; margin: 1px 4px 0px 0px;">&times;</button>
	{{#if content_type}}
		<input type="hidden" name="used_types[{{content_type}}]" value="{{content_type}}">
		<input type="hidden" name="rewrite[{{_id}}][content_type]" value="{{content_type}}">{{content_type}}
		<span class="caldera-easy-rewrites-segment" style="color: rgb(159, 159, 159);">:</span>
		<input type="text" name="rewrite[{{_id}}][slug]" value="{{#if slug}}{{slug}}{{else}}{{#find ../../../content_types content_type}}{{content_type}}{{/find}}{{/if}}" data-format="key">
		<span class="caldera-easy-rewrites-segment" style="color: rgb(159, 159, 159);">/</span> 
		{{#each segment}}
		<span class="caldera-easy-rewrites-rule-segment">
			<input type="hidden" name="rewrite[{{../_id}}][segment][{{_id}}][_id]" value="{{_id}}">
			<input type="text" name="rewrite[{{../_id}}][segment][{{_id}}][path]" value="{{path}}" data-format="key">
			<button style="display: inline; font-size: 17px; padding: 0px 5px; border-radius: 0px 4px 4px 0px; margin: 1px 2px 0px -6px;" type="button" class="button" data-remove-parent=".caldera-easy-rewrites-rule-segment">&times;</button>
			<span class="caldera-easy-rewrites-segment" style="color: rgb(159, 159, 159);">/</span> 
		</span>
		{{/each}}
		<button type="button" class="button wp-baldrick" data-request="cew_get_default_setting" type="button" data-script="add-segment" data-node="{{_id}}" style="font-size: 17px; padding: 0px 9px; margin: 1px 4px 0px 0px;">&plus;</button> 
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
