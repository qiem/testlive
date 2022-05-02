;(function($) {

	new PPPricingTable({
		id: '<?php echo $id; ?>',
		dualPricing: <?php echo ( 'yes' == $settings->dual_pricing ) ? 'true' : 'false'; ?>
	});

	var adjustHeights = function() {
		var spaceHeight = $('.fl-node-<?php echo $id; ?> .pp-pricing-table .pp-pricing-table-card .pp-pricing-table-price').outerHeight();
		$(".fl-node-<?php echo $id; ?> .pp-pricing-table .pp-pricing-table-matrix .pp-pricing-table-price").css('height', spaceHeight + 'px');

		$('.fl-node-<?php echo $id; ?> .pp-pricing-table-matrix .pp-pricing-table-features li').each(function() {
			var height = $(this).outerHeight();
			var index = $(this).index();

			$('.fl-node-<?php echo $id; ?> .pp-pricing-table-card .pp-pricing-table-features li.pp-pricing-table-item-' + (index+1)).css('height', height + 'px');
		});
	};

	$(document).ready( adjustHeights );

	$(window).on( 'resize', adjustHeights );

	$(document).on( 'pp_expandable_row_toggle', function( e, selector ) {
		if ( selector.parent().find( '.fl-node-<?php echo $id; ?>' ).length > 0 ) {
			adjustHeights();
		}
	} );

})(jQuery);