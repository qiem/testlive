<?php
$layout = 'slider';
$testimonials = $settings->testimonials;
$heading_tag = isset( $settings->heading_tag ) ? $settings->heading_tag : 'h2';
$title_tag = isset( $settings->title_tag ) ? $settings->title_tag : 'h3';
$subtitle_tag = isset( $settings->subtitle_tag ) ? $settings->subtitle_tag : 'h4';
$is_carousel = absint( $settings->min_slides ) > 1;

if ( isset( $settings->order ) ) {
	if( 'random' == $settings->order ) {
		shuffle( $testimonials );
	}

	if( 'desc' == $settings->order ) {
		krsort( $testimonials );
	}
}

$testimonials_class  = 'pp-testimonials-wrap';

if ( '' == $settings->heading ) {
	$testimonials_class .= ' pp-testimonials-no-heading';
}

if ( isset( $settings->layout ) ) {
	$layout = $settings->layout;
	$testimonials_class .= ' pp-testimonials-' . $layout;
	if ( 'grid' == $layout ) {
		$testimonials_class .= ' pp-testimonials-grid-' . $settings->grid_columns;

		if ( $settings->grid_columns_medium ) {
			$testimonials_class .= ' pp-testimonials-grid-md-' . $settings->grid_columns_medium;
		}
		if ( $settings->grid_columns_responsive ) {
			$testimonials_class .= ' pp-testimonials-grid-sm-' . $settings->grid_columns_responsive;
		}
	}
}
?>

<div class="<?php echo $testimonials_class; ?>">
	<?php if ( '4' == $settings->testimonial_layout ) { ?>
		<div class="layout-4-container<?php echo ( 'slider' == $layout && $is_carousel ) ? ' carousel-enabled' : ''; ?>">
	<?php } ?>
	<?php if ( '' != $settings->heading ) { ?>
		<<?php echo $heading_tag; ?> class="pp-testimonials-heading"><?php echo $settings->heading; ?></<?php echo $heading_tag; ?>>
	<?php } ?>

	<div class="pp-testimonials">
		<?php
		$testimonial_layout = $settings->testimonial_layout;

		$number_testimonials = count( $testimonials );

		$classes = '';
		if ( 'slider' == $layout ) {
			$classes = $is_carousel ? ' carousel-enabled' : '';
			echo '<div class="owl-carousel owl-theme' . ( 'no' === $settings->adaptive_height ? ' owl-height' : '' ) . '">';
		} else {
			$classes = '';
		}

		switch ( $testimonial_layout ) {
			case '1':
			foreach( $testimonials as $testimonial ) :

				if ( ! is_object( $testimonial ) ) {
					continue;
				}

				?>
				<div class="pp-testimonial layout-1<?php echo $classes; ?>">
					<?php if ( $testimonial->photo ) { ?>
						<div class="pp-testimonials-image">
							<img src="<?php echo $testimonial->photo_src; ?>" alt="<?php echo $module->get_alt($testimonial); ?>" />
						</div>
					<?php } ?>
					<div class="pp-content-wrapper">
						<?php if ( $settings->show_arrow == 'yes' ) { ?><div class="pp-arrow-top"></div><?php } ?>
						<?php if ( $testimonial->testimonial ) { ?>
							<div class="pp-testimonials-content"><?php echo $testimonial->testimonial; ?></div>
						<?php } ?>
						<?php if ( $testimonial->title || $testimonial->subtitle ) { ?>
							<div class="pp-title-wrapper">
								<?php if( $testimonial->title ) { ?>
									<<?php echo $title_tag; ?> class="pp-testimonials-title"><?php echo $testimonial->title; ?></<?php echo $title_tag; ?>>
								<?php } ?>
								<?php if ( $testimonial->subtitle ) { ?>
									<<?php echo $subtitle_tag; ?> class="pp-testimonials-subtitle"><?php echo $testimonial->subtitle; ?></<?php echo $subtitle_tag; ?>>
								<?php } ?>
							</div>
						<?php } ?>
					</div>
				</div>
			<?php endforeach;
			break;

			case '2':
			foreach( $testimonials as $testimonial ) :

				if ( ! is_object( $testimonial ) ) {
					continue;
				}

				?>
				<div class="pp-testimonial layout-2<?php echo $classes; ?>">
					<?php if ( $testimonial->testimonial ) { ?>
						<div class="pp-content-wrapper">
							<div class="pp-testimonials-content"><?php echo $testimonial->testimonial; ?></div>
							<?php if ( $settings->show_arrow == 'yes' ) { ?><div class="pp-arrow-bottom"></div><?php } ?>
						</div>
					<?php } ?>
					<div class="pp-vertical-align">
						<?php if ( $testimonial->photo ) { ?>
							<div class="pp-testimonials-image">
								<img src="<?php echo $testimonial->photo_src; ?>" alt="<?php echo $module->get_alt($testimonial); ?>" />
							</div>
						<?php } ?>
						<?php if ( $testimonial->title || $testimonial->subtitle ) { ?>
							<div class="pp-title-wrapper">
								<?php if ( $testimonial->title ) { ?>
									<<?php echo $title_tag; ?> class="pp-testimonials-title"><?php echo $testimonial->title; ?></<?php echo $title_tag; ?>>
								<?php } ?>
								<?php if ( $testimonial->subtitle ) { ?>
									<<?php echo $subtitle_tag; ?> class="pp-testimonials-subtitle"><?php echo $testimonial->subtitle; ?></<?php echo $subtitle_tag; ?>>
								<?php } ?>
							</div>
						<?php } ?>
					</div>
				</div>
			<?php endforeach;
			break;

			case '3':
			foreach( $testimonials as $testimonial ) :

				if ( ! is_object( $testimonial ) ) {
					continue;
				}

				?>
				<div class="pp-testimonial layout-3<?php echo $classes; ?> clearfix">
					<?php if ( $testimonial->photo ) { ?>
						<div class="pp-testimonials-image">
							<img src="<?php echo $testimonial->photo_src; ?>" alt="<?php echo $module->get_alt($testimonial); ?>" />
						</div>
					<?php } ?>
					<div class="layout-3-content pp-content-wrapper">
						<?php if ( $settings->show_arrow == 'yes' ) { ?><div class="pp-arrow-left"></div><?php } ?>
						<?php if ( $testimonial->testimonial ) { ?>
							<div class="pp-testimonials-content"><?php echo $testimonial->testimonial; ?></div>
						<?php } ?>
						<?php if ( $testimonial->title || $testimonial->subtitle ) { ?>
							<div class="pp-title-wrapper">
								<?php if ( $testimonial->title ) { ?>
									<<?php echo $title_tag; ?> class="pp-testimonials-title"><?php echo $testimonial->title; ?></<?php echo $title_tag; ?>>
								<?php } ?>
								<?php if ( $testimonial->subtitle ) { ?>
									<<?php echo $subtitle_tag; ?> class="pp-testimonials-subtitle"><?php echo $testimonial->subtitle; ?></<?php echo $subtitle_tag; ?>>
								<?php } ?>
							</div>
						<?php } ?>
					</div>
				</div>
			<?php endforeach;
			break;

			case '4':
			foreach( $testimonials as $testimonial ) :

				if ( ! is_object( $testimonial ) ) {
					continue;
				}

				?>
				<div class="pp-testimonial layout-4<?php echo $classes; ?><?php echo ! $testimonial->photo ? ' no-image-inner' : ''; ?>">
					<?php if ( $testimonial->photo ) { ?>
						<div class="pp-testimonials-image">
							<img src="<?php echo $testimonial->photo_src; ?>" alt="<?php echo $module->get_alt($testimonial); ?>" />
						</div>
					<?php } ?>
					<div class="layout-4-content">
						<?php if ( $testimonial->testimonial ) { ?>
							<div class="pp-testimonials-content"><?php echo $testimonial->testimonial; ?></div>
						<?php } ?>
						<?php if ( $testimonial->title || $testimonial->subtitle ) { ?>
							<div class="pp-title-wrapper">
								<?php if ( $testimonial->title ) { ?>
									<<?php echo $title_tag; ?> class="pp-testimonials-title"><?php echo $testimonial->title; ?></<?php echo $title_tag; ?>>
								<?php } ?>
								<?php if ( $testimonial->subtitle ) { ?>
									<<?php echo $subtitle_tag; ?> class="pp-testimonials-subtitle"><?php echo $testimonial->subtitle; ?></<?php echo $subtitle_tag; ?>>
								<?php } ?>
							</div>
						<?php } ?>
					</div>
				</div>
			<?php endforeach;
			break;

			case '5':
			foreach( $testimonials as $testimonial ) :

				if ( ! is_object( $testimonial ) ) {
					continue;
				}

				?>
				<div class="pp-testimonial layout-5<?php echo $classes; ?>">
					<div class="pp-vertical-align">
						<?php if ( $testimonial->title || $testimonial->subtitle ) { ?>
							<div class="pp-title-wrapper">
								<?php if ( $testimonial->title ) { ?>
									<<?php echo $title_tag; ?> class="pp-testimonials-title"><?php echo $testimonial->title; ?></<?php echo $title_tag; ?>>
								<?php } ?>
								<?php if ( $testimonial->subtitle ) { ?>
									<<?php echo $subtitle_tag; ?> class="pp-testimonials-subtitle"><?php echo $testimonial->subtitle; ?></<?php echo $subtitle_tag; ?>>
								<?php } ?>
							</div>
						<?php } ?>
					</div>
					<?php if ( $testimonial->testimonial ) { ?>
						<div class="pp-content-wrapper">
							<?php if ( $settings->show_arrow == 'yes' ) { ?><div class="pp-arrow-top"></div><?php } ?>
							<div class="pp-testimonials-content"><?php echo $testimonial->testimonial; ?></div>
						</div>
					<?php } ?>
				</div>
			<?php endforeach;
			break;

			default:
			foreach( $testimonials as $testimonial ) :

				if ( ! is_object( $testimonial ) ) {
					continue;
				}

				?>
				<div class="pp-testimonial layout-1<?php echo $classes; ?>">
					<?php if ( $testimonial->photo ) { ?>
						<div class="pp-testimonials-image">
							<img src="<?php echo $testimonial->photo_src; ?>" alt="<?php echo $module->get_alt($testimonial); ?>" />
						</div>
					<?php } ?>
					<?php if ( $testimonial->testimonial ) { ?>
						<div class="pp-testimonials-content"><?php echo $testimonial->testimonial; ?></div>
					<?php } ?>
					<?php if ( $testimonial->title || $testimonial->subtitle ) { ?>
						<div class="pp-title-wrapper">
							<?php if ( $testimonial->title ) { ?>
								<<?php echo $title_tag; ?> class="pp-testimonials-title"><?php echo $testimonial->title; ?></<?php echo $title_tag; ?>>
							<?php } ?>
							<?php if ( $testimonial->subtitle ) { ?>
								<<?php echo $subtitle_tag; ?> class="pp-testimonials-subtitle"><?php echo $testimonial->subtitle; ?></<?php echo $subtitle_tag; ?>>
							<?php } ?>
						</div>
					<?php } ?>
				</div>
			<?php endforeach;
			break;
		}

		if ( 'slider' == $layout ) {
			echo '</div>';
		}
		?>
	</div><!-- /.pp-testimonials -->
	<?php if( $settings->testimonial_layout == '4' ) { ?>
	</div>
	<?php } ?>
</div>
