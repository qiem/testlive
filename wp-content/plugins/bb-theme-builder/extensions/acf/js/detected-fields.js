(function($) {
	$(document).on( '_initSettingsFormsComplete', function() {
		if ( FLBuilderConfig.select2Enabled ) {
			var dropdown   = $( this ).find( '#fl-field-name_custom select' );
			dropdown.select2({width:'100%'})
			.on('select2:open', function(e){
				$('#fl-field-name_custom select').attr('placeholder', FLBuilderStrings.placeholderSelect2);
			})
	}
		$('#fl-field-name_custom select').change(function(){
			str = $( this ).val();
			if ( '' !== str ) {
				text = this.options[this.selectedIndex].text;
				type = text.match( /\[(.*)\]$/ );
				$('#fl-field-name input[name=name]').val(str)
				$('select[name=type]').val(type[1]).trigger('change');
			}
		});
		acffix = $('.fl-builder-settings[data-form-id$=-acf] select[name=type]').val();
		if ( 'text' === acffix ) {
			setTimeout( function() {
				$('#fl-field-image_size').toggle();
			}, 20 );
		}
	});
})(jQuery);
