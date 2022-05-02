<?php
$photos = $module->get_photos();
$aria_label = isset( $settings->sr_text ) && ! empty( $settings->sr_text ) ? $settings->sr_text : __( 'Slider', 'bb-powerpack' );
$captions = array();
$count = 1;
?>
<div class="pp-image-carousel-wrapper">
	<?php
	if ( isset( $settings->thumb_position ) && 'above' == $settings->thumb_position ) {
		include $module->dir . 'includes/thumbnails.php';
	}
	?>
	<div class="pp-image-carousel swiper-container<?php echo ( $settings->carousel_type == 'slideshow') ? ' pp-image-carousel-slideshow' : ''; ?><?php echo ($settings->pagination_position && $settings->carousel_type != 'slideshow') ? ' pp-carousel-navigation-' . $settings->pagination_position : ''; ?>" role="region" aria-label="<?php echo $aria_label; ?>">
		<div class="swiper-wrapper">
			<?php foreach( $photos as $photo ) :
				$caption = htmlspecialchars( preg_replace( '/\"|\'/', '', $photo->caption ) );
				$caption = empty( trim( $caption ) ) ? sprintf( __( 'Slide %d', 'bb-powerpack' ), $count ) : $caption;
				$captions[] = $caption;
				?>
				<div class="pp-image-carousel-item<?php echo ( ( $settings->click_action != 'none' ) && !empty( $photo->link ) ) ? ' pp-image-carousel-link' : ''; ?> swiper-slide" role="group" aria-label="<?php echo $caption; ?>">
					<?php if( $settings->click_action != 'none' ) : ?>
							<?php $click_action_link = '#';
								$click_action_target = $settings->custom_link_target;
								$click_action_rel = ( '_blank' === $click_action_target ) ? ' rel="nofollow noopener"' : '';

								if ( $settings->click_action == 'custom-link' ) {
									if ( ! empty( $photo->cta_link ) ) {
										$click_action_link = $photo->cta_link;
									}
								}

								if ( $settings->click_action == 'lightbox' ) {
									$click_action_link = $photo->link;
								}

							?>
					<a href="<?php echo $click_action_link; ?>" target="<?php echo $click_action_target; ?>"<?php echo $click_action_rel; ?> data-caption="<?php echo $photo->caption; ?>">
					<?php endif; ?>

					<?php if ( ! isset( $settings->use_image_as ) || 'background' === $settings->use_image_as ) { ?>
					<div class="pp-carousel-image-container" style="background-image:url(<?php echo esc_url( $photo->src ); ?>)"></div>
					<?php } ?>
					<?php if ( isset( $settings->use_image_as ) && 'img' === $settings->use_image_as ) { ?>
					<div class="pp-carousel-image-container">
						<figure class="swiper-slide-inner">
							<img class="swiper-slide-image" src="<?php echo esc_url( $photo->src ); ?>" alt="<?php echo esc_attr( $photo->alt ); ?>" />
						</figure>
					</div>
					<?php } ?>

					<?php if( $settings->overlay != 'none' ) : ?>
						<!-- Overlay Wrapper -->
						<div class="pp-image-overlay <?php echo $settings->overlay_effects; ?>">

								<?php if( $settings->overlay == 'text' ) : ?>
									<div class="pp-caption">
										<?php echo $photo->caption; ?>
									</div>
								<?php endif; ?>

								<?php if( $settings->overlay == 'icon' ) : ?>
								<div class="pp-overlay-icon">
									<span class="<?php echo $settings->overlay_icon; ?>" aria-hidden="true"></span>
								</div>
								<?php endif; ?>

						</div> <!-- Overlay Wrapper Closed -->
					<?php endif; ?>

					<?php if( $settings->click_action != 'none' ) : ?>
					</a>
					<?php endif; ?>
				</div>
				<?php
				$count++;
			endforeach;
			?>
		</div>

		<?php if ( 1 < count( $photos ) ) : ?>
			<?php if ( $settings->slider_navigation == 'yes' ) { ?>
			<!-- navigation arrows -->
			<button class="pp-swiper-button pp-swiper-button-prev" aria-label="<?php echo isset( $settings->prev_nav_sr_text ) && ! empty( $settings->prev_nav_sr_text ) ? htmlspecialchars( $settings->prev_nav_sr_text ) : __( 'Previous slide', 'bb-powerpack' ); ?>" role="button" tabindex="0">
				<span class="fa fa-angle-left" aria-hidden="true"></span>
			</button>
			<button class="pp-swiper-button pp-swiper-button-next" aria-label="<?php echo isset( $settings->next_nav_sr_text ) && ! empty( $settings->next_nav_sr_text ) ? htmlspecialchars( $settings->next_nav_sr_text ) : __( 'Next slide', 'bb-powerpack' ); ?>" role="button" tabindex="0">
				<span class="fa fa-angle-right" aria-hidden="true"></span>
			</button>
			<?php } ?>

			<?php if ( $settings->pagination_type ) { ?>
			<!-- pagination -->
			<div class="swiper-pagination" data-captions="<?php echo htmlspecialchars( json_encode( $captions ) ); ?>"></div>
			<?php } ?>
		<?php endif; ?>
	</div>
	<?php
	if ( 1 < count( $photos ) ) {
		if ( isset( $settings->thumb_position ) && 'below' == $settings->thumb_position ) {
			include $module->dir . 'includes/thumbnails.php';
		}
		if ( ! isset( $settings->thumb_position ) ) {
			include $module->dir . 'includes/thumbnails.php';
		}
	}
	?>
</div>