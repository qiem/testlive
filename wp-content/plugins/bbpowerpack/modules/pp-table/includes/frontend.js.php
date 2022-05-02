(function($) {

	var isTouch = function() {
		var prefixes = ' -webkit- -moz- -o- -ms- '.split(' ');
		var mq = function(query) {
			return window.matchMedia(query).matches;
		}
		
		if (('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch) {
			return true;
		}
		
		// include the 'heartz' as a way to have a non matching MQ to help terminate the join
		// https://git.io/vznFH
		var query = ['(', prefixes.join('touch-enabled),('), 'heartz', ')'].join('');
		return mq(query);
	};

	$(".fl-node-<?php echo $id; ?> table.pp-table-content tbody tr:nth-child(odd)").addClass("odd");
	$(".fl-node-<?php echo $id; ?> table.pp-table-content tbody tr:nth-child(even)").addClass("even");

	$(".fl-node-<?php echo $id; ?> table.pp-table-content").attr('data-tablesaw-mode', '<?php echo $settings->scrollable; ?>');

	<?php if ( $settings->scrollable == 'swipe' && $settings->custom_breakpoint > 0 ) { ?>
	if ( $(window).width() >= <?php echo $settings->custom_breakpoint; ?> && ! isTouch() ) {
		$(".fl-node-<?php echo $id; ?> table.pp-table-content").removeAttr('data-tablesaw-mode');
	}
	<?php } ?>

	$( document ).trigger( "enhance.tablesaw" );

	$(document).on('pp-tabs-switched', function(e, selector) {
		if ( selector.find('.pp-table-content').length > 0 ) {
			$( window ).trigger( "resize" );
		}
	});

})(jQuery);
