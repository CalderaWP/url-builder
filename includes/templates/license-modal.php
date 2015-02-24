<?php
/**
 * Licensing modal template
 *
 * @package   Caldera_URL_Builder
 * @author    David Cramer <david@digilab.co.za>
 * @license   GPL-2.0+
 * @link
 * @copyright 2014 David Cramer <david@digilab.co.za>
 */

global $cub_licensing_output;

?>
	<div class="caldera-url-builder-config-group">
		<label style="width: 105px;"><?php _e('License Key', 'caldera-url-builder'); ?><span id="caldera-url-builder-license_key_loader" class="spinner" style="background-position: center center;"></span></label>
		<input type="text" name="license_key" id="caldera-url-builder-license_key" value="{{data/key}}" autocomplete="off" style="width: 265px;">		
		{{#if data/message}}
			{{#is data/success value=false}}
			<div class="notice notice-error">
				<p>{{data/message}}</p>
			</div>
			{{/is}}
		{{else}}
			{{#if data/license}}
				{{#is data/license value="activated"}}
					<div class="notice updated">
						<p><?php _e('License is active and expires: ', 'caldera-url-builder'); ?>{{data/expires}}</p>
						<input id="caldera-url-builder-action" value="deactivate" type="hidden"> 
					</div>
					{{#script}}
					jQuery('button[data-action="cub_save_license"]').html('<?php _e('Remove License', 'caldera-url-builder'); ?>');
					{{/script}}					
				{{else}}
					<div class="notice notice-warning">
						<p><?php _e('License has been removed', 'caldera-url-builder'); ?></p>
						<input id="caldera-url-builder-action" value="activate" type="hidden"> 
					</div>
					{{#script}}
					jQuery('button[data-action="cub_save_license"]').html('<?php _e('Activate License', 'caldera-url-builder'); ?>');
					{{/script}}					
				{{/is}}
			{{/if}}
			{{#if data/status}}
				{{#is data/status value="1"}}
					<div class="notice updated">
						<p><?php _e('License is active', 'caldera-url-builder'); ?></p>
						<input id="caldera-url-builder-action" value="deactivate" type="hidden">
					</div>
					{{#script}}
					jQuery('button[data-action="cub_save_license"]').html('<?php _e('Remove License', 'caldera-url-builder'); ?>');
					{{/script}}
				{{else}}
					<p><?php _e( 'Get a license now at', 'caldera-url-builder' ); ?> <a href="https://calderawp.com/downloads/caldera-url-builder/" target="_blank">CalderaWP</a></p>
					<input id="caldera-url-builder-action" value="activate" type="hidden">
					{{#script}}
					jQuery('button[data-action="cub_save_license"]').html('<?php _e('Activate License', 'caldera-url-builder'); ?>');
					{{/script}}

				{{/is}}
			{{/if}}

			{{#is success value=false}}
				<div class="notice notice-error">
					<p><?php _e('Invalid License Key', 'caldera-url-builder'); ?></p>
					<input id="caldera-url-builder-action" value="activate" type="hidden"> 
				</div>			
			{{/is}}

		{{/if}}
		
		<?php echo $cub_licensing_output->nonce_field(); ?>
	</div>