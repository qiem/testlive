<?php if ( 'slider' == $settings->layout ) { ?>

	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .bx-wrapper .bx-pager a {
		<?php if ( $settings->dot_color ) { ?>background: <?php echo pp_get_color_value( $settings->dot_color ); ?>;<?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .bx-wrapper .bx-pager a {
		opacity: 0.5;
	}
	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .bx-wrapper .bx-pager a.active {
		<?php if ( $settings->active_dot_color ) { ?>background: <?php echo pp_get_color_value( $settings->active_dot_color ); ?>;<?php } ?>
		opacity: 1;
	}
	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .fa:hover,
	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .fa {
		<?php if ( $settings->arrow_color ) { ?>color: <?php echo pp_get_color_value( $settings->arrow_color ); ?>;<?php } ?>
	}
	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .pp-arrow-wrapper {
		<?php if ( $settings->arrow_alignment ) { ?>text-align: <?php echo $settings->arrow_alignment; ?>;<?php } ?>
	}

	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .owl-theme .owl-dots .owl-dot span {
		opacity: 1;
		<?php if ( ! empty( $settings->dot_color ) ) { ?>
		background: <?php echo pp_get_color_value( $settings->dot_color ); ?>;
		<?php } ?>
		box-shadow: none;
	}

	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .owl-theme .owl-dots .owl-dot.active span,
	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .owl-theme .owl-dots .owl-dot:hover span {
		<?php if ( ! empty( $settings->active_dot_color ) ) { ?>
		background: <?php echo pp_get_color_value( $settings->active_dot_color ); ?>;
		<?php } ?>
		opacity: 1;
		box-shadow: none;
	}

	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .owl-theme .owl-nav {
		text-align: <?php echo $settings->arrow_alignment; ?>;
	}

	.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .owl-theme .owl-nav button svg {
		<?php if ( isset( $settings->arrow_size ) && '' !== $settings->arrow_size ) { ?>
			width: <?php echo $settings->arrow_size; ?>px;
			height: <?php echo $settings->arrow_size; ?>px;
		<?php } ?>
		<?php if ( isset( $settings->arrow_color ) && ! empty( $settings->arrow_color ) ) { ?>
			color: <?php echo pp_get_color_value( $settings->arrow_color ); ?>;
		<?php } ?>
		<?php if ( isset( $settings->arrow_bg_color ) && ! empty( $settings->arrow_bg_color ) ) { ?>
			background: <?php echo pp_get_color_value( $settings->arrow_bg_color ); ?>;
		<?php } ?>
	}

<?php } ?>
<?php
FLBuilderCSS::responsive_rule( array(
	'settings'	=> $settings,
	'setting_name'	=> 'spacing',
	'selector'	=> ".fl-node-$id .pp-testimonials-grid .pp-testimonials",
	'prop'		=> 'grid-gap',
	'unit'		=> 'px',
) );
?>

.fl-node-<?php echo $id; ?> .pp-testimonial {
	<?php if ( isset( $settings->full_box_bg ) && ! empty( $settings->full_box_bg ) ) { ?>
	background: <?php echo pp_get_color_value( $settings->full_box_bg ); ?>;
	<?php } ?>
}
<?php
	// Box - Border
	FLBuilderCSS::border_field_rule( array(
		'settings' 		=> $settings,
		'setting_name' 	=> 'full_box_border',
		'selector' 		=> ".fl-node-$id .pp-testimonial",
	) );

	// Box - Padding
	FLBuilderCSS::dimension_field_rule( array(
		'settings' 		=> $settings,
		'setting_name' 	=> 'full_box_padding',
		'selector' 		=> ".fl-node-$id .pp-testimonial",
		'unit'			=> 'px',
		'props'			=> array(
			'padding-top' 		=> 'full_box_padding_top',
			'padding-right' 	=> 'full_box_padding_right',
			'padding-bottom' 	=> 'full_box_padding_bottom',
			'padding-left' 		=> 'full_box_padding_left',
		),
	) );
?>

<?php if ( $settings->layout_4_content_bg || ( isset( $settings->box_border['width'] ) && 0 != $settings->box_border['width']['top'] ) ) { ?>
	.fl-node-<?php echo $id; ?> .pp-testimonials .pp-content-wrapper {
		padding: 20px;
	}
<?php } ?>

<?php if ( $settings->testimonial_layout == '1' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-testimonial.layout-1 .pp-content-wrapper {
		<?php if ( $settings->layout_4_content_bg ) { ?>background: <?php echo pp_get_color_value( $settings->layout_4_content_bg ); ?>;<?php } ?>
	}
	<?php if ( $settings->show_arrow == 'yes' ) { ?>
		.fl-node-<?php echo $id; ?> .pp-testimonial.layout-1 .pp-arrow-top {
			<?php if ( $settings->layout_4_content_bg ) { ?>border-bottom-color: <?php echo pp_get_color_value( $settings->layout_4_content_bg ); ?>;<?php } ?>
		}
	<?php } ?>
<?php } ?>
<?php if ( $settings->testimonial_layout == '2' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-testimonial.layout-2 .pp-content-wrapper {
		<?php if ( $settings->layout_4_content_bg ) { ?>background: <?php echo pp_get_color_value( $settings->layout_4_content_bg ); ?>;<?php } ?>
	}
	<?php if ( $settings->show_arrow == 'yes' ) { ?>
		.fl-node-<?php echo $id; ?> .pp-testimonial.layout-2 .pp-arrow-bottom {
			<?php if ( $settings->layout_4_content_bg ) { ?>border-top-color: <?php echo pp_get_color_value( $settings->layout_4_content_bg ); ?>;<?php } ?>
		}
	<?php } ?>
<?php } ?>
<?php if ( $settings->testimonial_layout == '3' ) {
	$wd = $settings->image_size + 30; ?>
	.fl-node-<?php echo $id; ?> .pp-testimonial.layout-3 .pp-content-wrapper {
		width: calc(100% - <?php echo $wd; ?>px);
		<?php if ( $settings->layout_4_content_bg ) { ?>background: <?php echo pp_get_color_value( $settings->layout_4_content_bg ); ?>;<?php } ?>
	}
	<?php if ( $settings->show_arrow == 'yes' ) { ?>
		.fl-node-<?php echo $id; ?> .pp-testimonial.layout-3 .pp-arrow-left {
			<?php if ( $settings->layout_4_content_bg ) { ?>border-right-color: <?php echo pp_get_color_value( $settings->layout_4_content_bg ); ?>;<?php } ?>
		}
	<?php } ?>
	.fl-node-<?php echo $id; ?> .pp-testimonials .layout-3 .pp-testimonials-image {
		max-height: <?php echo $settings->image_size; ?>px;
		max-width: <?php echo $settings->image_size; ?>px;
	}

<?php } ?>
<?php if ( $settings->testimonial_layout == '4' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-testimonial.layout-4 .layout-4-content {
		<?php if ( $settings->layout_4_content_bg ) { ?>background: <?php echo pp_get_color_value( $settings->layout_4_content_bg ); ?>;<?php } ?>
	}
<?php } ?>
<?php if ( $settings->testimonial_layout == '5' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-testimonial.layout-5 .pp-content-wrapper {
		<?php if ( $settings->layout_4_content_bg ) { ?>background: <?php echo pp_get_color_value( $settings->layout_4_content_bg ); ?>;<?php } ?>
	}
	<?php if ( $settings->show_arrow == 'yes' ) { ?>
		.fl-node-<?php echo $id; ?> .pp-testimonial.layout-5 .pp-arrow-top {
			<?php if ( $settings->layout_4_content_bg ) { ?>border-bottom-color: <?php echo pp_get_color_value( $settings->layout_4_content_bg ); ?>;<?php } ?>
		}
	<?php } ?>
<?php } ?>

<?php
	// Content Box - Border
	FLBuilderCSS::border_field_rule( array(
		'settings' 		=> $settings,
		'setting_name' 	=> 'box_border',
		'selector' 		=> ".fl-node-$id .pp-testimonial.layout-1 .pp-content-wrapper, .fl-node-$id .pp-testimonial.layout-2 .pp-content-wrapper, .fl-node-$id .pp-testimonial.layout-3 .pp-content-wrapper, .fl-node-$id .pp-testimonial.layout-4 .layout-4-content, .fl-node-$id .pp-testimonial.layout-5 .pp-content-wrapper",
	) );

	// Content Box - Padding
	FLBuilderCSS::dimension_field_rule( array(
		'settings' 		=> $settings,
		'setting_name' 	=> 'box_padding',
		'selector' 		=> ".fl-node-$id .pp-testimonial.layout-1 .pp-content-wrapper, .fl-node-$id .pp-testimonial.layout-2 .pp-content-wrapper, .fl-node-$id .pp-testimonial.layout-3 .pp-content-wrapper, .fl-node-$id .pp-testimonial.layout-4 .layout-4-content, .fl-node-$id .pp-testimonial.layout-5 .pp-content-wrapper",
		'unit'			=> 'px',
		'props'			=> array(
			'padding-top' 		=> 'box_padding_top',
			'padding-right' 	=> 'box_padding_right',
			'padding-bottom' 	=> 'box_padding_bottom',
			'padding-left' 		=> 'box_padding_left',
		),
	) );
?>

.fl-node-<?php echo $id; ?> .pp-testimonials-wrap .pp-testimonials-heading {
	<?php if ( $settings->heading_color ) { ?>color: #<?php echo $settings->heading_color; ?>;<?php } ?>
}
<?php
	// Heading Typography
	FLBuilderCSS::typography_field_rule( array(
		'settings'		=> $settings,
		'setting_name' 	=> 'heading_typography',
		'selector' 		=> ".fl-node-$id .pp-testimonials-wrap .pp-testimonials-heading",
	) );
?>
.fl-node-<?php echo $id; ?> .pp-testimonial .pp-title-wrapper h3.pp-testimonials-title {
	<?php if ( $settings->title_color ) { ?>color: #<?php echo $settings->title_color; ?>;<?php } ?>
	margin-top: <?php echo $settings->title_margin['top']; ?>px;
	margin-bottom: <?php echo $settings->title_margin['bottom']; ?>px;
}
<?php
	// Title Typography
	FLBuilderCSS::typography_field_rule( array(
		'settings'		=> $settings,
		'setting_name' 	=> 'title_typography',
		'selector' 		=> ".fl-node-$id .pp-testimonial .pp-title-wrapper h3.pp-testimonials-title",
	) );
?>
.fl-node-<?php echo $id; ?> .pp-testimonial .pp-title-wrapper h4.pp-testimonials-subtitle {
	<?php if ( $settings->subtitle_color ) { ?>color: #<?php echo $settings->subtitle_color; ?>;<?php } ?>
	margin-top: <?php echo $settings->subtitle_margin['top']; ?>px;
	margin-bottom: <?php echo $settings->subtitle_margin['bottom']; ?>px;
}
<?php
	// Sub Title Typography
	FLBuilderCSS::typography_field_rule( array(
		'settings'		=> $settings,
		'setting_name' 	=> 'subtitle_typography',
		'selector' 		=> ".fl-node-$id .pp-testimonial .pp-title-wrapper h4.pp-testimonials-subtitle",
	) );
?>
.fl-node-<?php echo $id; ?> .pp-testimonial .pp-testimonials-content {
	<?php if ( $settings->text_color ) { ?>color: #<?php echo $settings->text_color; ?><?php } ?>;
	margin-top: <?php echo $settings->content_margin['top']; ?>px;
	margin-bottom: <?php echo $settings->content_margin['bottom']; ?>px;
}
<?php
	// text Typography
	FLBuilderCSS::typography_field_rule( array(
		'settings'		=> $settings,
		'setting_name' 	=> 'text_typography',
		'selector' 		=> ".fl-node-$id .pp-testimonial .pp-testimonials-content",
	) );
?>
.fl-node-<?php echo $id; ?> .pp-testimonial .pp-testimonials-image img {
	max-height: <?php echo $settings->image_size; ?>px;
	max-width: <?php echo $settings->image_size; ?>px;
}

<?php
	// Image - Border
	FLBuilderCSS::border_field_rule( array(
		'settings' 		=> $settings,
		'setting_name' 	=> 'image_border',
		'selector' 		=> ".fl-node-$id .pp-testimonial .pp-testimonials-image img",
	) );
?>

.fl-node-<?php echo $id; ?>.pp-masonry-grid .pp-testimonials {
	<?php if ( isset( $settings->grid_columns ) && ! empty( $settings->grid_columns ) ) { ?>
	column-count: <?php echo $settings->grid_columns; ?>;
	<?php } ?>
}

<?php if ( isset( $settings->padding_top ) && '' !== $settings->padding_top ) { ?>
.fl-node-<?php echo $id; ?> .pp-testimonials .layout-4 {
	padding-top: <?php echo $settings->padding_top; ?>px;
}
<?php } ?>

@media only screen and ( max-width: <?php echo $global_settings->medium_breakpoint; ?>px ) {
	.fl-node-<?php echo $id; ?> .pp-testimonials-grid-md-1 .pp-testimonials {
		grid-template-columns: repeat(1,1fr);
		display: grid;
	}

	.fl-node-<?php echo $id; ?> .pp-testimonials-grid-md-2 .pp-testimonials {
		grid-template-columns: repeat(2,1fr);
		display: grid;
	}

	.fl-node-<?php echo $id; ?> .pp-testimonials-grid-md-3 .pp-testimonials {
		grid-template-columns: repeat(3,1fr);
		display: grid;
	}

	.fl-node-<?php echo $id; ?> .pp-testimonials-grid-md-4 .pp-testimonials {
		grid-template-columns: repeat(4,1fr);
		display: grid;
	}

	.fl-node-<?php echo $id; ?> .pp-testimonials-grid-md-5 .pp-testimonials {
		grid-template-columns: repeat(5,1fr);
		display: grid;
	}

	.fl-node-<?php echo $id; ?> .pp-testimonials-grid-md-6 .pp-testimonials {
		grid-template-columns: repeat(6,1fr);
		display: grid;
	}
	.fl-node-<?php echo $id; ?>.pp-masonry-grid .pp-testimonials {
		<?php if ( isset( $settings->grid_columns_medium ) && ! empty( $settings->grid_columns_medium ) ) { ?>
		column-count: <?php echo $settings->grid_columns_medium; ?>;
		<?php } ?>
	}
}

@media only screen and ( max-width: <?php echo $global_settings->responsive_breakpoint; ?>px ) {
	.fl-node-<?php echo $id; ?> .pp-testimonials-grid-sm-1 .pp-testimonials {
		grid-template-columns: repeat(1,1fr);
		display: grid;
	}

	.fl-node-<?php echo $id; ?> .pp-testimonials-grid-sm-2 .pp-testimonials {
		grid-template-columns: repeat(2,1fr);
		display: grid;
	}

	.fl-node-<?php echo $id; ?> .pp-testimonials-grid-sm-3 .pp-testimonials {
		grid-template-columns: repeat(3,1fr);
		display: grid;
	}

	.fl-node-<?php echo $id; ?> .pp-testimonials-grid-sm-4 .pp-testimonials {
		grid-template-columns: repeat(4,1fr);
		display: grid;
	}

	.fl-node-<?php echo $id; ?> .pp-testimonials-grid-sm-5 .pp-testimonials {
		grid-template-columns: repeat(5,1fr);
		display: grid;
	}

	.fl-node-<?php echo $id; ?> .pp-testimonials-grid-sm-6 .pp-testimonials {
		grid-template-columns: repeat(6,1fr);
		display: grid;
	}

	.fl-node-<?php echo $id; ?>.pp-masonry-grid .pp-testimonials {
		<?php if ( isset( $settings->grid_columns_responsive ) && ! empty( $settings->grid_columns_responsive ) ) { ?>
		column-count: <?php echo $settings->grid_columns_responsive; ?>;
		<?php } ?>
	}
}
