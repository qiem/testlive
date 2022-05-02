
<?php
$columns 		= isset( $settings->post_grid_count ) && is_array( $settings->post_grid_count ) ? $settings->post_grid_count : array();
$column_desktop = isset( $columns['desktop'] ) && ! empty( $columns['desktop'] ) && intval( $columns['desktop'] ) > 0 ? intval( $columns['desktop'] ) : 3;
$column_tablet  = isset( $columns['tablet'] ) && ! empty( $columns['tablet'] ) && intval( $columns['tablet'] ) > 0 ? intval( $columns['tablet'] ) : 2;
$column_mobile  = isset( $columns['mobile'] ) && ! empty( $columns['mobile'] ) && intval( $columns['mobile'] ) > 0 ? intval( $columns['mobile'] ) : 1;
$spacing		= isset( $settings->post_spacing ) ? intval( $settings->post_spacing ) : 0;
$space_desktop	= ( $column_desktop - 1 ) * $spacing;
$space_tablet 	= ( $column_tablet - 1 ) * $spacing;
$space_mobile 	= ( $column_mobile - 1 ) * $spacing;
$speed          = ! empty( $settings->transition_speed ) ? $settings->transition_speed * 1000 : 3000;
$slide_speed    = ( isset( $settings->slides_speed ) && ! empty( $settings->slides_speed ) ) ? $settings->slides_speed * 1000 : 1000;
$page_arg	 	= is_front_page() ? 'page' : 'paged';
$paged 			= get_query_var( $page_arg, 1 );
$breakpoints	= array(
	'mobile'		=> empty( $global_settings->responsive_breakpoint ) ? '768' : $global_settings->responsive_breakpoint,
	'tablet'		=> empty( $global_settings->medium_breakpoint ) ? '980' : $global_settings->medium_breakpoint,
);
$scrollTo		= apply_filters( 'pp_cg_scroll_to_grid_on_filter', true );
$js_fields 		= $module->get_fields_for_js( $module->form, $settings );
?>

var ppcg_<?php echo $id; ?> = '';

;(function($) {
	var left_arrow_svg = '<svg aria-hidden="true" data-prefix="fal" data-icon="angle-left" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 192 512" class="svg-inline--fa fa-angle-left fa-w-6 fa-2x"><path fill="currentColor" d="M25.1 247.5l117.8-116c4.7-4.7 12.3-4.7 17 0l7.1 7.1c4.7 4.7 4.7 12.3 0 17L64.7 256l102.2 100.4c4.7 4.7 4.7 12.3 0 17l-7.1 7.1c-4.7 4.7-12.3 4.7-17 0L25 264.5c-4.6-4.7-4.6-12.3.1-17z" class=""></path></svg>';
	var right_arrow_svg = '<svg aria-hidden="true" data-prefix="fal" data-icon="angle-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 192 512" class="svg-inline--fa fa-angle-right fa-w-6 fa-2x"><path fill="currentColor" d="M166.9 264.5l-117.8 116c-4.7 4.7-12.3 4.7-17 0l-7.1-7.1c-4.7-4.7-4.7-12.3 0-17L127.3 256 25.1 155.6c-4.7-4.7-4.7-12.3 0-17l7.1-7.1c4.7-4.7 12.3-4.7 17 0l117.8 116c4.6 4.7 4.6 12.3-.1 17z" class=""></path></svg>';

	var PPContentGridOptions = {
		id: '<?php echo $id ?>',
		layout: '<?php echo $settings->layout; ?>',
		style: '<?php echo $settings->post_grid_style_select; ?>',
		ajaxUrl: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
		siteUrl: '<?php echo site_url(); ?>',
		scrollTo: <?php echo $scrollTo ? 'true' : 'false'; ?>,
		perPage: '<?php echo $settings->posts_per_page; ?>',
		fields: <?php echo json_encode( $js_fields ); ?>,
		pagination: '<?php echo $settings->pagination; ?>',
		current_page: '<?php echo home_url( $_SERVER['REQUEST_URI'] ); ?>',
		page: '<?php echo $paged; ?>',
		is_tax: false,
		is_author: false,
		postSpacing: '<?php echo $settings->post_spacing; ?>',
		postColumns: {
			desktop: <?php echo $column_desktop; ?>,
			tablet: <?php echo $column_tablet ?>,
			mobile: <?php echo $column_mobile ?>,
		},
		matchHeight: '<?php echo $settings->match_height; ?>',
		<?php echo ( isset( $settings->post_grid_filters_display ) && 'yes' == $settings->post_grid_filters_display ) ? 'filters: true' : 'filters: false'; ?>,
		defaultFilter: '<?php echo isset( $settings->post_grid_filters_default ) && ! empty( $settings->post_grid_filters_default ) ? $settings->post_grid_filters_default : ''; ?>',
		<?php if ( isset( $settings->post_grid_filters ) && 'none' != $settings->post_grid_filters ) { ?>
			filterTax: '<?php echo $settings->post_grid_filters; ?>',
		<?php } ?>
		filterType: '<?php echo isset( $settings->post_grid_filters_type ) ? $settings->post_grid_filters_type : 'static'; ?>',
		<?php if ( 'grid' == $settings->layout && 'no' == $settings->match_height && 'style-9' != $settings->post_grid_style_select ) { ?>
		masonry: 'yes',
		<?php } ?>
		<?php if ( 'carousel' == $settings->layout ) { ?>
			carousel: {
				items: <?php echo $column_desktop; ?>,
				responsive: {
					0: {
						items: <?php echo $column_mobile; ?>,
					},
					<?php echo $breakpoints['mobile'] + 1; ?>: {
						items: <?php echo $column_tablet; ?>,
					},
					<?php echo $breakpoints['tablet'] + 1; ?>: {
						items: <?php echo $column_desktop; ?>,
					},
				},
			<?php if ( isset( $settings->slide_by ) && absint( $settings->slide_by ) ) : ?>
				slideBy: <?php echo absint( $settings->slide_by ); ?>,
			<?php endif; ?>
			<?php if ( isset( $settings->slider_pagination ) && 'no' === $settings->slider_pagination ) : ?>
				dots: false,
			<?php endif; ?>
			<?php if ( isset( $settings->auto_play ) ) : ?>
				<?php echo 'yes' === $settings->auto_play && ! FLBuilderModel::is_builder_active() ? 'autoplay: true' : 'autoplay: false'; ?>,
			<?php endif; ?>
				autoplayTimeout: <?php echo $speed ?>,
				autoplaySpeed: <?php echo $slide_speed ?>,
				navSpeed: <?php echo $slide_speed ?>,
				dotsSpeed: <?php echo $slide_speed ?>,
				<?php echo 'yes' === $settings->slider_navigation ? 'nav: true' : 'nav: false'; ?>,
				<?php echo 'yes' === $settings->stop_on_hover ? 'autoplayHoverPause: true' : 'autoplayHoverPause: false'; ?>,
				<?php echo 'yes' === $settings->lazy_load ? 'lazyLoad: true' : 'lazyLoad: false'; ?>,
				navText : [left_arrow_svg, right_arrow_svg],
				navContainer: '.fl-node-<?php echo $id; ?> .pp-carousel-nav',
				responsiveRefreshRate: 200,
				responsiveBaseWidth: window,
				loop: <?php echo isset( $settings->slide_loop ) && 'yes' === $settings->slide_loop ? 'true' : 'false'; ?>,
				center: <?php echo ( isset( $settings->slides_center_align ) && 'yes' == $settings->slides_center_align ) ? 'true' : 'false'; ?>,
				autoHeight: <?php echo isset( $settings->auto_height ) && 'yes' === $settings->auto_height ? 'true' : 'false'; ?>
			}
			<?php } // End if(). ?>
	};

	<?php if ( is_archive() || is_post_type_archive() ) { ?>
		PPContentGridOptions.is_archive = true;
	<?php } ?>

	<?php if ( is_tax() || is_category() ) { ?>
	PPContentGridOptions.is_tax = true;
	<?php } ?>

	<?php if ( isset( get_queried_object()->taxonomy ) && isset( get_queried_object()->slug ) ) { ?>
		PPContentGridOptions.current_tax = '<?php echo get_queried_object()->taxonomy; ?>';
		PPContentGridOptions.current_term = '<?php echo get_queried_object()->slug; ?>';
	<?php } ?>

	<?php if ( is_author() ) { ?>
	PPContentGridOptions.is_author = true;
	PPContentGridOptions.current_author = '<?php echo get_queried_object()->ID; ?>';
	<?php } ?>

	<?php if ( is_search() ) { ?>
	PPContentGridOptions.is_search = true;
	PPContentGridOptions.search_term = '<?php echo isset( $_GET['s'] ) ? (string) $_GET['s'] : ''; ?>';
	<?php } ?>

	<?php if ( isset( $_GET['orderby'] ) && ! empty( $_GET['orderby'] ) ) { ?>
	PPContentGridOptions.orderby = '<?php echo (string) $_GET['orderby']; ?>';
	<?php } ?>

	<?php if ( isset( $module->template_id ) ) { ?>
	PPContentGridOptions.template_id = '<?php echo $module->template_id; ?>';
	PPContentGridOptions.template_node_id = '<?php echo $module->template_node_id; ?>';
	<?php } ?>

	ppcg_<?php echo $id; ?> = new PPContentGrid( PPContentGridOptions );
	
	// expandable row fix.
	var state = 0;
	$(document).on('pp_expandable_row_toggle', function(e, selector) {
		if ( selector.is('.pp-er-open') && state === 0 && selector.parent().find( '.pp-content-post-grid' ).length > 0 ) {
			if ( 'undefined' !== typeof $.fn.isotope ) {
				selector.parent().find('.pp-content-post-grid').isotope('layout');
			}
			state = 1;
		}
	});

	// Tabs and Content Grid fix
	$(document).on('pp-tabs-switched', function(e, selector) {
		if ( selector.find('.pp-content-post-grid').length > 0 ) {
			var postsWrapper = selector.find('.pp-content-post-grid');
			ppcg_<?php echo $id; ?>._gridLayoutMatchHeight();
			if ( 'undefined' !== typeof $.fn.isotope ) {
				setTimeout(function() {
					if ( ! postsWrapper.hasClass('pp-isotope-initialized') ) {
						postsWrapper.isotope('layout');
						postsWrapper.addClass('pp-isotope-initialized');
					}
				}, 500);
			}
		}
	});

})(jQuery);
