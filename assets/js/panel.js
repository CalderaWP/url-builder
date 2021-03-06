// General panel scripts .
function caldera_easy_rewrites_randomUUID() {
		var s = [], itoh = '0123456789ABCDEF';
		for (var i = 0; i <6; i++) s[i] = Math.floor(Math.random()*0x10);
		return s.join('');
}
var caldera_easy_rewrites_field_callbacks = [];
jQuery('document').ready(function($){
	// add row
	$('body').on('click', '.caldera-url-builder-add-group-row', function(){
		var clicked = $( this ),
			rowid = caldera_easy_rewrites_randomUUID(),
			template = $( '#' + clicked.data('rowtemplate')).html().replace(/{{id}}/g, rowid);
			if(clicked.data('field')){	
				var ref = clicked.data('field').split('-');
				template = template.replace(/\_\_i\_\_/g, ref[ref.length-2]);
			}
			//console.log(clicked.parent().parent().find('.groupitems').last());
			template = template.replace(/\_\_count\_\_/g, clicked.parent().parent().find('.groupitems').length);
			clicked.parent().before(template);

			for(var callback in caldera_easy_rewrites_field_callbacks){
				if( typeof window[caldera_easy_rewrites_field_callbacks[callback]] === 'function'){
					window[caldera_easy_rewrites_field_callbacks[callback]]();
				}
			}

	});
	$('body').on('click', '.caldera-url-builder-removeRow', function(){
		$(this).next().remove();
		$(this).remove();
		////console.log(this);
	});
	// tabs
	$('body').on('click', '.caldera-url-builder-metabox-config-nav li a, .caldera-url-builder-shortcode-config-nav li a, .caldera-url-builder-settings-config-nav li a, .caldera-url-builder-widget-config-nav li a', function(){
		$(this).parent().parent().find('.current').removeClass('current');
		$(this).parent().parent().parent().parent().find('.group').hide();
		$(''+$(this).attr('href')+'').show();
		$(this).parent().addClass('current');
		if($(this).data('tabset').length){
			$('#'+$(this).data('tabset')).val($(this).data('tabkey'));
		}
		return false;
	});

	// initcallbacks
	setInterval(function(){
		$('.caldera-url-builder-init-callback').each(function(k,v){
			var callback = $(this);
			if( typeof window[callback.data('init')] === 'function'){
				console.log(callback.data('init'));
				window[callback.data('init')]();
				callback.removeClass('caldera-url-builder-init-callback');
			}
		});
	}, 100);
});
