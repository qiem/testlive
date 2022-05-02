<?php
	$has_featured_image = true;
	$has_fallback_image = isset( $settings->fallback_image ) && 'custom' == $settings->fallback_image && ! empty( $settings->fallback_image_custom );
	$using_dynamic_fallback = false;
	$featured_image_src = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), $settings->image_thumb_size );
	$featured_image_url = $featured_image_src ? $featured_image_src[0] : wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) );

	if ( empty( $featured_image_url ) ) {
		$has_featured_image = false;
		if ( ! $has_fallback_image && 'none' != $settings->fallback_image ) {
			$first_img = BB_PowerPack_Post_Helper::post_catch_image( get_the_content(), $settings->image_thumb_size );
			$featured_image_url = ( '' != $first_img['src'] ) ? $first_img['src'] : apply_filters( 'pp_cg_placeholder_img', $module_url . 'images/placeholder.jpg' );
			$settings->fallback_image = 'custom';
			$settings->fallback_image_custom = $first_img['id'];
			$settings->fallback_image_custom_src = $featured_image_url;
			$using_dynamic_fallback = true;
		} else {
			$featured_image_url = $settings->fallback_image_custom_src;
		}
	}
?>
<div class="pp-content-grid-image pp-post-image">
    <?php if ( ! empty( $featured_image_url ) ) { ?>
		<?php if ( 'style-9' == $settings->post_grid_style_select ) { ?>
			<div class="pp-post-featured-img" style="background-image: url('<?php echo $featured_image_url; ?>');">
				<a href="<?php echo $permalink; ?>" title="<?php the_title_attribute(); ?>"></a>
			</div>
		<?php } else { ?>
			<div class="pp-post-featured-img">
				<?php
					FLBuilder::render_module_html( 'photo', BB_PowerPack_Post_Helper::post_image_get_settings( get_the_ID(), $settings->image_thumb_crop, $settings, $has_featured_image ) );
					if ( $using_dynamic_fallback ) {
						$settings->fallback_image = 'default';
						$settings->fallback_image_custom = '';
						unset( $settings->fallback_image_custom_src );
					}
				?>
			</div>
		<?php } ?>
    <?php } ?>

	<?php if ( 'style-9' != $settings->post_grid_style_select ) { ?>
		<?php if(($settings->show_categories == 'yes' && taxonomy_exists($settings->post_taxonomies) && !empty($terms_list)) && ('style-3' == $settings->post_grid_style_select) ) : ?>
			<?php include $module_dir . 'includes/templates/post-meta.php'; ?>
		<?php endif; ?>

		<?php if( 'style-4' == $settings->post_grid_style_select ) { ?>
			<<?php echo $settings->title_tag; ?> class="pp-content-grid-title pp-post-title" itemprop="headline">
				<?php if( $settings->more_link_type == 'button' || $settings->more_link_type == 'title' || $settings->more_link_type == 'title_thumb' ) { ?>
					<a href="<?php echo $permalink; ?>">
				<?php } ?>
						<?php the_title(); ?>
				<?php if( $settings->more_link_type == 'button' || $settings->more_link_type == 'title' || $settings->more_link_type == 'title_thumb' ) { ?>
					</a>
				<?php } ?>
			</<?php echo $settings->title_tag; ?>>
		<?php } ?>

		<?php if('style-6' == $settings->post_grid_style_select && 'yes' == $settings->show_date) { ?>
		<div class="pp-content-post-date pp-post-meta">
			<?php if ( pp_is_tribe_events_post( $post_id ) && function_exists( 'tribe_get_start_date' ) ) { ?>
				<span class="pp-post-month"><?php echo tribe_get_start_date( null, false, 'M' ); ?></span>
				<span class="pp-post-day"><?php echo tribe_get_start_date( null, false, 'd' ); ?></span>
			<?php } else { ?>
				<span class="pp-post-month"><?php echo get_the_date('M'); ?></span>
				<span class="pp-post-day"><?php echo get_the_date('d'); ?></span>
			<?php } ?>
		</div>
		<?php } ?>
	<?php } ?>
</div>
