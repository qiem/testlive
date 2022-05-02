;(function($) {
	
	FLBuilder.registerModuleHelper( 'pp-google-map', {
		init: function() {
			var form = $('.fl-builder-settings');
			var self = this;
			
			self._toggleOverlayFields();
			self._toggleFilterFields();

			form.find('#fl-field-map_source').on('change', function() {
				self._toggleOverlayFields();
			});

			form.find('#fl-field-post_slug').on('change', function() {
				self._toggleFilterFields();
			});
		},

		_toggleOverlayFields: function() {
			var form  = $('.fl-builder-settings');
			var field = form.find('select[name="map_source"]');

			if ( ( '' === field.val() || 'acf' !== field.val() ) || 'no' === form.find('select[name="acf_enable_info"]').val() ) {
				form.find('#fl-field-acf_info_window_text').hide();
			} else {
				form.find('#fl-field-acf_info_window_text').show();
			}

			if ( ( '' === field.val() || 'acf_options_page' !== field.val() ) || 'no' === form.find('select[name="acf_options_enable_info"]').val() ) {
				form.find('#fl-field-acf_options_info_window_text').hide();
			} else {
				form.find('#fl-field-acf_options_info_window_text').show();
			}
		},

		_toggleFilterFields: function() {
			var form  = $('.fl-builder-settings');
			var postType = form.find('#fl-field-post_slug select').val();
			var section = $('.pp-custom-query-' + postType + '-filter');

			form.find('.fl-custom-query-filter').hide();

			if ( section.length > 0 ) {
				section.show();
			}
		}
	});
})(jQuery);