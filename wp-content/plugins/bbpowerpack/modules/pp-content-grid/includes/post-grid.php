
<?php FLBuilderModel::default_settings($settings, array(
	'post_type' 		=> 'post',
	'show_image' 		=> 'yes',
	'show_author'		=> 'yes',
	'show_date'			=> 'yes',
	'show_categories'	=> 'no',
	'meta_separator'	=> ' | ',
	'show_content'		=> 'yes',
	'content_type'		=> 'excerpt',
	'content_length'	=> 300,
	'more_link_type'	=> 'box',
	'more_link_text'	=> __('Read More', 'bb-powerpack'),
	'post_grid_filters_display' => 'no',
	'post_grid_filters'	=> 'none',
	'post_taxonomies'	=> 'none',
	'image_thumb_crop'	=> '',
	'product_rating'	=> 'yes',
	'product_price'		=> 'yes',
	'product_button'	=> 'yes',
	'append_more'		=> 'yes'

));

$date_format = isset( $settings->date_format ) ? $settings->date_format : '';

if ( $settings->post_type == 'product' ) {
	global $post, $product;
}

$alternate_class = '';

if ( $count % 2 === 0 ) {
	$alternate_class = ' pp-post-2n';
}

$post_classes = join( ' ', get_post_class() );
$html_classes = "pp-content-post pp-content-grid-post$alternate_class pp-grid-{$settings->post_grid_style_select}";

do_action( 'pp_cg_before_render_post', $settings, $count, $html_classes );

$html_classes .= " $post_classes";
?>
<div class="<?php echo $html_classes; ?>"<?php BB_PowerPack_Post_Helper::print_schema( ' itemscope itemtype="' . PPContentGridModule::schema_itemtype() . '"' ); ?> data-id="<?php echo $post_id; ?>">

	<?php PPContentGridModule::schema_meta(); ?>

	<?php if ( 'style-9' == $settings->post_grid_style_select ) {
		include $module_dir . 'includes/post-tile.php';
	} else { ?>

		<?php if ( $settings->more_link_type == 'box' && ('product' != $settings->post_type || 'download' != $settings->post_type ) ) { ?>
			<a class="pp-post-link" href="<?php echo $permalink; ?>" title="<?php the_title_attribute(); ?>"></a>
		<?php } ?>

		<?php if ( 'style-1' == $settings->post_grid_style_select ) { ?>

			<<?php echo $settings->title_tag; ?> class="pp-content-grid-title pp-post-title" itemprop="headline">
				<?php if( $settings->more_link_type == 'button' || $settings->more_link_type == 'title' || $settings->more_link_type == 'title_thumb' ) { ?>
					<a href="<?php echo $permalink; ?>" title="<?php the_title_attribute(); ?>">
				<?php } ?>
						<?php the_title(); ?>
				<?php if( $settings->more_link_type == 'button' || $settings->more_link_type == 'title' || $settings->more_link_type == 'title_thumb' ) { ?>
					</a>
				<?php } ?>
			</<?php echo $settings->title_tag; ?>>

			<div class="pp-content-post-meta pp-post-meta">
				<?php if($settings->show_author == 'yes' ) : ?>
					<span class="pp-content-post-author">
					<?php

					printf(
						_x( 'By %s', '%s stands for author name.', 'bb-powerpack' ),
						'<a href="' . get_author_posts_url( get_the_author_meta( 'ID' ) ) . '"><span>' . get_the_author_meta( 'display_name', get_the_author_meta( 'ID' ) ) . '</span></a>'
					);

					?>
					</span>
				<?php endif; ?>
				<?php if ( $settings->show_date == 'yes' ) : ?>
					<?php if ( $settings->show_author == 'yes' ) : ?>
						<span> <?php echo $settings->meta_separator; ?> </span>
					<?php endif; ?>
					<span class="pp-content-grid-date">
						<?php if ( pp_is_tribe_events_post( $post_id ) && function_exists( 'tribe_get_start_date' ) ) { ?>
							<?php echo tribe_get_start_date( null, false, $date_format ); ?>
						<?php } else { ?>
							<?php echo get_the_date( $date_format ); ?>
						<?php } ?>
					</span>
				<?php endif; ?>

			</div>

		<?php } ?>

		<?php if ( in_array( $settings->post_grid_style_select, array( 'default', 'style-2', 'style-3', 'style-5', 'style-8' ) ) ) {
			if ( isset( $settings->alternate_content ) && 'yes' === $settings->alternate_content ) { ?>
			<div class="pp-content-alternate-wrap">
		<?php } } ?>

		<?php if ( $settings->show_image == 'yes' ) : // Featured Image ?>
			<?php include $module_dir . 'includes/templates/post-image.php'; ?>
		<?php endif; ?>

		<div class="pp-content-grid-inner pp-content-body clearfix">
			<?php do_action( 'pp_cg_post_body_open', $post_id, $settings ); ?>

			<?php if('style-5' == $settings->post_grid_style_select && 'yes' == $settings->show_date) { ?>
			<div class="pp-content-post-date pp-post-meta">
				<?php if ( pp_is_tribe_events_post( $post_id ) && function_exists( 'tribe_get_start_date' ) ) { ?>
					<span class="pp-post-day"><?php echo tribe_get_start_date( null, false, 'd' ); ?></span>
					<span class="pp-post-month"><?php echo tribe_get_start_date( null, false, 'M' ); ?></span>
				<?php } else { ?>
					<span class="pp-post-day"><?php echo get_the_date('d'); ?></span>
					<span class="pp-post-month"><?php echo get_the_date('M'); ?></span>
				<?php } ?>
			</div>
			<?php } ?>

			<div class="pp-content-post-data">
				<?php if( 'style-1' != $settings->post_grid_style_select && 'style-4' != $settings->post_grid_style_select ) { ?>
					<<?php echo $settings->title_tag; ?> class="pp-content-grid-title pp-post-title" itemprop="headline">
						<?php if( $settings->more_link_type == 'button' || $settings->more_link_type == 'title' || $settings->more_link_type == 'title_thumb' ) { ?>
							<a href="<?php echo $permalink; ?>" title="<?php the_title_attribute(); ?>">
						<?php } ?>
								<?php the_title(); ?>
						<?php if( $settings->more_link_type == 'button' || $settings->more_link_type == 'title' || $settings->more_link_type == 'title_thumb' ) { ?>
							</a>
						<?php } ?>
					</<?php echo $settings->title_tag; ?>>
					<?php if( 'style-2' == $settings->post_grid_style_select ) { ?>
						<span class="pp-post-title-divider"></span>
					<?php } ?>
				<?php } ?>

				<?php if( ($settings->show_author == 'yes' || $settings->show_date == 'yes' || $settings->show_categories == 'yes')
						&& ('style-1' != $settings->post_grid_style_select) ) : ?>
				<div class="pp-content-post-meta pp-post-meta">
					<?php if( $settings->show_author == 'yes' ) : ?>
						<span class="pp-content-post-author">
						<?php

						printf(
							_x( 'By %s', '%s stands for author name.', 'bb-powerpack' ),
							'<a href="' . get_author_posts_url( get_the_author_meta( 'ID' ) ) . '"><span>' . get_the_author_meta( 'display_name', get_the_author_meta( 'ID' ) ) . '</span></a>'
						);

						?>
						</span>
					<?php endif; ?>

					<?php if($settings->show_date == 'yes' && 'style-5' != $settings->post_grid_style_select && 'style-6' != $settings->post_grid_style_select ) : ?>
						<?php if($settings->show_author == 'yes' ) : ?>
							<span> <?php echo $settings->meta_separator; ?> </span>
						<?php endif; ?>
						<span class="pp-content-grid-date">
							<?php if ( pp_is_tribe_events_post( $post_id ) && function_exists( 'tribe_get_start_date' ) ) { ?>
								<?php echo tribe_get_start_date( null, false, $date_format ); ?>
							<?php } else { ?>
								<?php echo get_the_date( $date_format ); ?>
							<?php } ?>
						</span>
					<?php endif; ?>

					<?php if( 'style-6' == $settings->post_grid_style_select || 'style-5' == $settings->post_grid_style_select ) : ?>
						<?php if($settings->show_author == 'yes' && $settings->show_categories == 'yes' && taxonomy_exists($settings->post_taxonomies) && !empty($terms_list) ) : ?>
							<span> <?php echo $settings->meta_separator; ?> </span>
						<?php endif; ?>
						<?php if($settings->show_categories == 'yes') { ?>
						<span class="pp-content-post-category">
							<?php if(taxonomy_exists($settings->post_taxonomies)) { ?>
								<?php $i = 1;
								foreach ($terms_list as $term):
									?>
								<?php if( $i == count($terms_list) ) { ?>
									<a href="<?php echo get_term_link($term); ?>" class="pp-post-meta-term"><?php echo $term->name; ?></a>
								<?php } else { ?>
									<a href="<?php echo get_term_link($term); ?>" class="pp-post-meta-term"><?php echo $term->name; ?></a> <?php echo ! empty( $settings->meta_separator ) ? $settings->meta_separator : '/'; ?>
								<?php } ?>
								<?php $i++; endforeach; ?>
							<?php } ?>
						</span>
						<?php } ?>
					<?php endif; ?>
				</div>
				<?php endif; ?>

				<?php if( $settings->post_type == 'product' && $settings->product_rating == 'yes' && class_exists( 'WooCommerce' ) ) { ?>
					<?php include $module_dir . 'includes/templates/product-rating.php'; ?>
				<?php } ?>

				<?php if ( 'tribe_events' == $settings->post_type && ( class_exists( 'Tribe__Events__Main' ) && class_exists( 'FLThemeBuilderLoader' ) ) ) { ?>
					<?php include $module_dir . 'includes/templates/event-content.php'; ?>
				<?php } ?>

				<?php do_action( 'pp_cg_before_post_content', $post_id, $settings ); ?>

				<?php if($settings->show_content == 'yes' || $settings->show_content == 'custom') : ?>
					<?php include $module_dir . 'includes/templates/post-content.php'; ?>
				<?php endif; ?>

				<?php do_action( 'pp_cg_after_post_content', $post_id, $settings ); ?>

				<?php if( $settings->more_link_text != '' && $settings->more_link_type == 'button' && 'product' != $settings->post_type && 'download' != $settings->post_type ) :
					include $module_dir . 'includes/templates/custom-button.php';
				endif; ?>

				<?php if( ( $settings->post_type == 'product' || $settings->post_type == 'download' ) && ( $settings->product_price == 'yes' || $settings->product_button == 'yes' ) ) { ?>
					<?php if( $settings->product_price == 'yes' ) { ?>
						<?php include $module_dir . 'includes/templates/product-price.php'; ?>
					<?php } ?>

					<?php if( $settings->more_link_text != '' && $settings->more_link_type == 'button' && ( 'product' == $settings->post_type || 'download' == $settings->post_type ) ) : ?>
						<?php if ( 'no' == $settings->product_button ) :
							include $module_dir . 'includes/templates/custom-button.php';
						endif; ?>
					<?php endif; ?>

					<?php if( $settings->product_button == 'yes' ) { ?>
						<?php include $module_dir . 'includes/templates/cart-button.php'; ?>
					<?php } ?>
				<?php } ?>

			</div>
			<?php if(($settings->show_categories == 'yes' && taxonomy_exists($settings->post_taxonomies) && !empty($terms_list)) && ('style-3' != $settings->post_grid_style_select && 'style-5' != $settings->post_grid_style_select && 'style-6' != $settings->post_grid_style_select) ) : ?>
				<?php include $module_dir . 'includes/templates/post-meta.php'; ?>
			<?php endif; ?>

			<?php do_action( 'pp_cg_post_body_close', $post_id, $settings ); ?>
		</div>

		<?php if ( in_array( $settings->post_grid_style_select, array( 'default', 'style-2', 'style-3', 'style-5', 'style-8' ) ) ) {
			if ( isset( $settings->alternate_content ) && 'yes' === $settings->alternate_content ) { ?>
			</div>
		<?php } } ?>
	<?php } ?>
</div>
