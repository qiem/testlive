<div class="pp-logos-content clearfix">
    <div class="pp-logos-wrapper clearfix">
		<?php
		for ( $i = 0; $i < count( $settings->logos_grid ); $i++ ) {

			if ( ! is_object( $settings->logos_grid[ $i ] ) ) {
				continue;
			}

			$logos_grid = $settings->logos_grid[ $i ];
			$alt = $logos_grid->upload_logo_title;

			if ( empty( $alt ) ) {
				$alt = get_post_meta( $logos_grid->upload_logo_grid, '_wp_attachment_image_alt', true );
				if ( empty( $alt ) && isset( $logos_grid->upload_logo_grid_src ) ) {
					$alt = $logos_grid->upload_logo_grid_src;
				}
			}

			$img_attrs = array(
				'class' => 'logo-image',
				'src' => $logos_grid->upload_logo_grid_src,
				'alt' => $alt,
				'data-no-lazy' => 1,
			);

			$img_attrs = apply_filters( 'pp_logo_image_html_attrs', $img_attrs, $logos_grid, $settings );

			$img_attrs_str = '';

			foreach ( $img_attrs as $key => $value ) {
				$img_attrs_str .= ' ' . $key . '=' . '"' . $value . '"';
			}

		?>
		<div class="pp-logo pp-logo-<?php echo $i; ?>">
        <?php if ( $logos_grid->upload_logo_link != '' ) { ?>
            <a href="<?php echo $logos_grid->upload_logo_link; ?>" target="<?php echo $settings->upload_logo_link_target; ?>"<?php echo ( '_blank' === $settings->upload_logo_link_target && ( ! isset( $settings->link_nofollow ) || 'yes' === $settings->link_nofollow ) ) ? ' rel="nofollow noopener"' : ''; ?>>
        <?php } ?>
            <div class="pp-logo-inner">
                <div class="pp-logo-inner-wrap">
                    <?php if( $logos_grid->upload_logo_grid ) { ?>
						<div class="logo-image-wrapper">
							<img <?php echo trim( $img_attrs_str ); ?> />
						</div>
                    <?php } ?>
                    <?php if ( $logos_grid->upload_logo_title ) { ?>
                        <div class="title-wrapper">
                            <p class="logo-title">
                                <?php echo $logos_grid->upload_logo_title; ?>
                            </p>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <?php if ( $logos_grid->upload_logo_link != '' ) { ?>
                </a>
            <?php } ?>
		</div>
		<?php } ?>
	</div>
    <div class="logo-slider-next"></div>
	<div class="logo-slider-prev"></div>
</div>
