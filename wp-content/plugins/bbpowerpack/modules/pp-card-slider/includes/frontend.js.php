var pp_card_<?php echo $id; ?>;
<?php
if ( 'yes' === $settings->autoplay ) {
	$autoplay_speed = ( '' !== $settings->autoplay_speed ) ? $settings->autoplay_speed : 2000;
} else {
	$autoplay_speed = 9999999;
}

?>
;(function($) {
	pp_card_<?php echo $id; ?> = new PPCardSlider( {
		id: '<?php echo $id; ?>',
		loop: <?php echo 'yes' === $settings->infinite_loop ? 'true' : 'false'; ?>,
		effect: 'fade',
		speed: <?php echo '' !== $settings->slide_speed ? $settings->slide_speed : 1000; ?>,
		grabCursor: <?php echo 'yes' === $settings->grab_cursor ? 'true' : 'false'; ?>,
		direction: '<?php echo $settings->slide_direction; ?>',
		autoplay: {
			delay: <?php echo $autoplay_speed; ?>,
			disableOnInteraction: <?php echo 'yes' === $settings->pause_interaction ? 'true' : 'false'; ?>
		},
		pagination: {
			el: '.swiper-pagination',
			type: 'bullets',
			clickable: true,
		},
		keyboard: {
			enabled: <?php echo ( 'yes' === $settings->keyboard_nav ) ? 'true' : 'false'; ?>,
			onlyInViewport: false,
		},
		responsive: <?php echo $global_settings->responsive_breakpoint; ?>,
	} );

})(jQuery);
