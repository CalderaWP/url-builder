var caldera_easy_rewrites_canvas = false,
	cew_get_config_object,
	cew_record_change,
	cew_canvas_init,
	cew_get_default_setting,
	init_magic_tags,
	cew_rebuild_magics,
	config_object = {},
	magic_tags = [],
	cew_rebuild_results,
	content_types,
	cew_initial_load = true;

jQuery( function($){


	cew_rebuild_results = function( obj ){

		var result = obj.data, clashes = [];

		for( var type in result.data ){
			
			clashes = [];

			$( '.preview-type-' + type ).removeClass('warning').removeClass('danger').removeClass('success').find('.notice').remove();
			

			if( typeof result.data[ type ]['warning'] !== 'undefined' ){

				// error - no posts
				$( '.preview-type-' + type ).addClass('warning').append('<span class="notice">' + result.data[ type ]['warning'] + '</span>');

			}else{

				if( result.data[ type ].length > 1){
					// error
					$( '.preview-type-' + type ).addClass('danger').append('<span class="notice">' + $('#rewrite-notice-error').html() + '</span>');					
					for( var p = 0; p < result.data[ type ].length; p++ ){

						// whats the clash?
						if( result.data[ type ][p]['attachment'] && type !== 'attachment' ){
							clashes.push( 'Attachments' );
						}else if( result.data[ type ][p]['pagename'] && type !== 'page' ){
							clashes.push( 'Page' );
						}else if( result.data[ type ][p]['name'] && type !== 'post' ){
							clashes.push( 'Post' );
						}else if( result.data[ type ][p]['post_type'] && type !== result.data[ type ][p]['post_type'] ){
							clashes.push( result.data[ type ][p]['post_type'] );
						}

					}
					var last = false
					if( clashes.length > 1 ){
						last = clashes.pop();
					}
					$( '.preview-type-' + type + ' .notice').append( clashes.join(', ') );
					if( last ){
						$( '.preview-type-' + type + ' .notice' ).append( ' & ' + last );
					}
				}else{
					// tis good
					$( '.preview-type-' + type ).addClass('success').append('<span class="notice">' + $('#rewrite-notice-success').html() + '</span>');
				}

			}
		}
		if( false === cew_initial_load ){
			caldera_easy_rewrites_canvas = 'tested';			
		}
		cew_initial_load = false;
	}


	init_magic_tags = function(){
		//init magic tags
		var magicfields = jQuery('.magic-tag-enabled');

		magicfields.each(function(k,v){
			var input = jQuery(v);
			
			if(input.hasClass('magic-tag-init-bound')){
				var currentwrapper = input.parent().find('.magic-tag-init');
				if(!input.is(':visible')){
					currentwrapper.hide();
				}else{
					currentwrapper.show();
				}
				return;			
			}
			var magictag = jQuery('<span class="dashicons dashicons-editor-code magic-tag-init"></span>'),
				wrapper = jQuery('<span style="position:relative;display:inline-block; width:100%;"></span>');

			if(input.is('input')){
				magictag.css('borderBottom', 'none');
			}

			if(input.hasClass('caldera-easy-rewrites-conditional-value-field')){
				wrapper.width('auto');
			}

			//input.wrap(wrapper);
			magictag.insertAfter(input);
			input.addClass('magic-tag-init-bound');
			if(!input.is(':visible')){
				magictag.hide();
			}else{
				magictag.show();
			}
		});

	}

	// internal function declarationas
	cew_get_config_object = function(el, e){

		e.preventDefault();
		// new sync first
		$('#caldera_easy_rewrites-id').trigger('change');
		var clicked 	= $(el),
			config 		= $('#caldera-easy-rewrites-live-config').val(),
			required 	= $('.required'),
			clean		= true;

		for( var input = 0; input < required.length; input++ ){
			if( required[input].value.length <= 0 && $( required[input] ).is(':visible') ){
				$( required[input] ).addClass('caldera-easy-rewrites-input-error').focus();
				clean = false;
			}else{
				$( required[input] ).removeClass('caldera-easy-rewrites-input-error');
			}
		}
		if( clean ){
			caldera_easy_rewrites_canvas = config;
		}
		clicked.data( 'config', config );
		return clean;
	}

	cew_record_change = function(){
		// hook and rebuild the fields list
		jQuery(document).trigger('record_change');
		jQuery('#caldera_easy_rewrites-id').trigger('change');
		jQuery('#caldera-easy-rewrites-field-sync').trigger('refresh');

	}
	
	cew_canvas_init = function(){

		if( !caldera_easy_rewrites_canvas ){
			// bind changes
			jQuery('#caldera-easy-rewrites-main-canvas').on('keydown keyup change','input, select, textarea', function(e) {
				config_object = jQuery('#caldera-easy-rewrites-main-form').formJSON(); // perhaps load into memory to keep it live.
				jQuery('#caldera-easy-rewrites-live-config').val( JSON.stringify( config_object ) ).trigger('change');
			});

			caldera_easy_rewrites_canvas = jQuery('#caldera-easy-rewrites-live-config').val();
			config_object = JSON.parse( caldera_easy_rewrites_canvas ); // perhaps load into memory to keep it live.
		}
		if( $('.color-field').length ){
			$('.color-field').wpColorPicker({
				change: function(obj){
					$('#caldera_easy_rewrites-id').trigger('change');
				}
			});
		}
		if( $('.caldera-easy-rewrites-group-wrapper').length ){
			$( ".caldera-easy-rewrites-group-wrapper" ).sortable({
				handle: ".dashicons-sort",
				update: function(){
					jQuery('#caldera_easy_rewrites-id').trigger('change');
				}
			});
			$( ".caldera-easy-rewrites-fields-list" ).sortable({
				handle: ".dashicons-sort",
				update: function(){
					jQuery('#caldera_easy_rewrites-id').trigger('change');
				}
			});
		}
		// live change init
		$('[data-init-change]').trigger('change');
		// rebuild tags
		cew_rebuild_magics();
		jQuery(document).trigger('canvas_init');
		jQuery( '#caldera-easy-rewrites-test-rules').trigger('testlines');
	}
	cew_get_default_setting = function(obj){

		var id = 'node_' + Math.round(Math.random() * 99887766) + '_' + Math.round(Math.random() * 99887766),
			new_object = {},
			config_object = JSON.parse( jQuery('#caldera-easy-rewrites-live-config').val() ), // perhaps load into memory to keep it live.
			trigger = ( obj.trigger ? obj.trigger : obj.params.trigger ),
			sub_id = ( trigger.data('group') ? trigger.data('group') : 'node_' + Math.round(Math.random() * 99887766) + '_' + Math.round(Math.random() * 99887766) ),
			nodes;

		
		// add simple node
		if( trigger.data('addNode') ){
			// new node? add one
			var newnode = { "_id" : id };

			nodes = trigger.data('addNode').split('.');
			
			for( var n = nodes.length-1; n >= 0; n--){
				if( n > 0 ){
					var newobj = newnode,
						nid = 'node_' + Math.round(Math.random() * 99887766) + '_' + Math.round(Math.random() * 99887766);

					newnode = {"_id" : n > 1 ? nid : id };
					newnode[nodes[n]] 			= {};
					newnode[nodes[n]][nid] 		= newobj;
					newnode[nodes[n]][nid]._id 	= nid;

				}else{

					if( !config_object[nodes[n]] ){
						config_object[nodes[n]] = {};
					}
					config_object[nodes[n]][id] = newnode;
				}

			}

		}
		// remove simple node (all)
		if( trigger.data('removeNode') ){
			// new node? add one
			if( config_object[trigger.data('removeNode')] ){
				delete config_object[trigger.data('removeNode')];
			}

		}



		switch( trigger.data('script') ){
			case "add-segment":
				// add to core object
				if( !config_object.rewrite[trigger.data('node')].segment ){
					config_object.rewrite[trigger.data('node')].segment = {};
				}
				config_object.rewrite[trigger.data('node')].segment[id] = { "_id" : id };

				break;
			case "add-field-node":
				// add to core object
				if( !config_object[trigger.data('slug')][trigger.data('group')].field ){
					config_object[trigger.data('slug')][trigger.data('group')].field = {};
				}
				config_object[trigger.data('slug')][trigger.data('group')].field[id] = { "_id": id, 'name': 'new field', 'slug': 'new_field' };
				config_object.open_field = id;
				break;				
		}


		jQuery('#caldera-easy-rewrites-live-config').val( JSON.stringify( config_object ) );
		jQuery('#caldera-easy-rewrites-field-sync').trigger('refresh');
		cew_record_change();
	}
	// sutocomplete category
	$.widget( "custom.catcomplete", $.ui.autocomplete, {
		_create: function() {
			this._super();
			this.widget().menu( "option", "items", "> :not(.ui-autocomplete-category)" );
		},
		_renderMenu: function( ul, items ) {
			var that = this,
			currentCategory = "";
			$.each( items, function( index, item ) {
				var li;
				if ( item.category != currentCategory ) {
					ul.append( "<li class='ui-autocomplete-category'>" + item.category + "</li>" );
					currentCategory = item.category;
				}
				li = that._renderItemData( ul, item );
				if ( item.category ) {
					li.attr( "aria-label", item.category + " : " + item.label );
				}
			});
		}
	});
	cew_rebuild_magics = function(){

		function split( val ) {
			return val.split( / \s*/ );
		}
		function extractLast( term ) {
			return split( term ).pop();
		}
		$( ".magic-tag-enabled" ).bind( "keydown", function( event ) {
			if ( event.keyCode === $.ui.keyCode.TAB && $( this ).catcomplete( "instance" ).menu.active ) {
				event.preventDefault();
			}
		}).catcomplete({
			minLength: 0,
			source: function( request, response ) {
				// delegate back to autocomplete, but extract the last term
				magic_tags = [];
				var category = '';
				// Search form fields
				if( config_object.search_form && config_object.form_fields ){
					// set internal tags
					var system_tags = [
						'autocomplete_item',
					];					
					category = $('#caldera-easy-rewrites-label-tags').text();
					for( f = 0; f < system_tags.length; f++ ){
						magic_tags.push( { label: '{' + system_tags[f] + '}', category: category }  );
					}							
				}
				
				response( $.ui.autocomplete.filter( magic_tags, extractLast( request.term ) ) );
			},
			focus: function() {
				// prevent value inserted on focus
				return false;
			},
			select: function( event, ui ) {
				var terms = split( this.value );
				// remove the current input
				terms.pop();
				// add the selected item
				terms.push( ui.item.value );
				// add placeholder to get the comma-and-space at the end
				//terms.push( "" );
				this.value = terms.join( " " );
				return false;
			}
		});
	}	

	// trash 
	$(document).on('click', '.caldera-easy-rewrites-card-actions .confirm a', function(e){
		e.preventDefault();
		var parent = $(this).closest('.caldera-easy-rewrites-card-content');
			actions = parent.find('.row-actions');

		actions.slideToggle(300);
	});

	// bind slugs
	$(document).on('keyup change', '[data-format="slug"]', function(e){

		var input = $(this);

		if( input.data('master') && input.prop('required') && this.value.length <= 0 && e.type === "change" ){
			this.value = $(input.data('master')).val().replace(/[^a-z0-9]/gi, '_').toLowerCase();
			if( this.value.length ){
				input.trigger('change');
			}
			return;
		}

		this.value = this.value.replace(/[^a-z0-9]/gi, '_').toLowerCase();
	});
	// bind keys
	$(document).on('keyup change', '[data-format="key"]', function(e){

		var input = $(this),
			parent = input.closest('.caldera-easy-rewrites-rule-segment');

		if( e.key && e.key === '/' && this.value.length > 1 ){
			input.val( this.value.substr(0,this.value.length-1) ).trigger('change');
			if( !parent.next().length ){
				parent.closest('.caldera-easy-rewrites-rule-wrapper').find('.add-new-segment').trigger('click');
			}
			return;
		}

		if( input.data('master') && input.prop('required') && this.value.length <= 0 && e.type === "change" ){
			this.value = $(input.data('master')).val().replace(/[^a-z0-9_%@.]/gi, '-').toLowerCase();
			if( this.value.length ){
				input.trigger('change');
			}
			return;
		}

		this.value = this.value.replace(/[^a-z0-9_%@.]/gi, '-').toLowerCase();
	});
		
	// bind label update
	$(document).on('keyup change', '[data-sync]', function(){
		var input = $(this),
			syncs = $(input.data('sync'));
		
		syncs.each(function(){
			var sync = $(this);

			if( sync.is('input') ){
				sync.val( input.val() ).trigger('change');
			}else{
				sync.text(input.val());
			}
		});
	});
	// bind toggles
	$(document).on('click', '[data-toggle]', function(){
		
		var toggle = $(this).data('toggle'),
			target = $(toggle);
		
		target.each(function(){
			var tog = $(this);
			if( tog.is(':checkbox') || tog.is(':radio') ){
				if( tog.prop('checked') ){
					tog.prop('checked', false);
				}else{
					tog.prop('checked', true);
				}
				cew_record_change();
			}else{
				tog.toggle();
			}
		});

	});	

	// bind tabs
	$(document).on('click', '.caldera-easy-rewrites-nav-tabs a', function(e){
		
		e.preventDefault();
		var clicked 	= $(this),
			tab_id 		= clicked.attr('href'),
			required 	= $('[required]'),
			clean		= true;

		for( var input = 0; input < required.length; input++ ){
			if( required[input].value.length <= 0 && $( required[input] ).is(':visible') ){
				$( required[input] ).addClass('caldera-easy-rewrites-input-error');
				clean = false;
			}else{
				$( required[input] ).removeClass('caldera-easy-rewrites-input-error');
			}
		}
		if( !clean ){
			return;
		}
		$('.caldera-easy-rewrites-nav-tabs .current').removeClass('current');
		$('.caldera-easy-rewrites-nav-tabs .active').removeClass('active');
		$('.caldera-easy-rewrites-nav-tabs .nav-tab-active').removeClass('nav-tab-active');
		if( clicked.parent().is('li') ){
			clicked.parent().addClass('active');			
		}else if( clicked.parent().is('div') ){
			clicked.addClass('current');			
		}else{			
			clicked.addClass('nav-tab-active');
		}
		

		$('.caldera-easy-rewrites-editor-panel').hide();
		$( tab_id ).show();
		

		jQuery('#caldera-easy-rewrites-active-tab').val(tab_id).trigger('change');

	});

	// row remover global neeto
	$(document).on('click', '[data-remove-parent]', function(e){
		var clicked = $(this),
			parent = clicked.closest(clicked.data('removeParent'));
		if( clicked.data('confirm') ){
			if( !confirm(clicked.data('confirm')) ){
				return;
			}
		}
		parent.remove();
		cew_record_change();		
	});
	
	// init tags
	$('body').on('click', '.magic-tag-init', function(e){
		var clicked = $(this),
			input = clicked.prev();

		input.focus().trigger('init.magic');

	});
	
	// initialize live sync rebuild
	$(document).on('change', '[data-live-sync]', function(e){
		cew_record_change();
		
	});

	// initialise baldrick triggers
	$('.wp-baldrick').baldrick({
		request     : ajaxurl,
		method      : 'POST'
	});


	window.onbeforeunload = function(e) {

		if( caldera_easy_rewrites_canvas && caldera_easy_rewrites_canvas !== jQuery('#caldera-easy-rewrites-live-config').val() ){
			return true;
		}
	};


});







