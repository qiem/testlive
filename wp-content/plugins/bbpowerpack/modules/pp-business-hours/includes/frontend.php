<div class="pp-business-hours-content clearfix" itemscope itemtype="http://schema.org/LocalBusiness">
	<meta itemprop="name" content="<?php echo get_bloginfo('name'); ?>" />
	<?php
	// Fetch logo from theme or filter.
	$image = '';
	if ( class_exists( 'FLTheme' ) && 'image' == FLTheme::get_setting( 'fl-logo-type' ) ) {
		$image = FLTheme::get_setting( 'fl-logo-image' );
	} elseif ( function_exists( 'has_custom_logo' ) && has_custom_logo() ) {
		$custom_logo_id = get_theme_mod( 'custom_logo' );
		$logo           = wp_get_attachment_image_src( $custom_logo_id, 'full' );
		$image          = $logo[0];
	}
	$image = apply_filters( 'pp_business_hours_publisher_image_url', $image );
	if ( $image ) {
		echo '<div itemprop="image" itemscope itemtype="https://schema.org/ImageObject">';
		echo '<meta itemprop="url" content="' . $image . '">';
		echo '</div>';
	}
	?>

	<?php
	$rows = count($settings->business_hours_rows);

	for ($i=0; $i < count($settings->business_hours_rows); $i++) :

		if(!is_object($settings->business_hours_rows[$i])) continue;

		$bhRow = $settings->business_hours_rows[$i];
		$status = '';
		$highlight = '';

		if ( $bhRow->status == 'close' ) {
			$status = ' pp-closed';
		}
		if ( $bhRow->highlight == 'yes' ) {
			$highlight = ' pp-highlight-row';
		}

		$title = $module->get_day_translation( $bhRow->title, $bhRow->day_format );

		if ( $bhRow->hours_type == 'range' ) {
			$title = $module->get_day_translation( $bhRow->start_day, $bhRow->day_format );
			$title .= ' - ';
			$title .= $module->get_day_translation( $bhRow->end_day, $bhRow->day_format );
		}

		$opening_hours = '';
		$closing_hours = '';

		?>
		<div itemprop="openingHoursSpecification" itemscope="itemscope" itemtype="https://schema.org/OpeningHoursSpecification" class="pp-bh-row clearfix pp-bh-row-<?php echo $i; ?><?php echo $status; ?><?php echo $highlight; ?>">
			<div class="pp-bh-title">
				<?php if ( $bhRow->hours_type == 'day' ) { ?>
					<link itemprop="dayOfWeek" href="http://schema.org/<?php echo $bhRow->title; ?>" /><?php echo $title; ?>
				<?php } else { ?>
					<?php echo $title; ?>
				<?php } ?>
			</div>
			<div class="pp-bh-timing">
				<?php if ( $bhRow->status == 'close' ) {
					echo $bhRow->status_text;
				} else {
					if ( is_object( $bhRow->start_time ) ) {
						$opening_hours = $bhRow->start_time->hours . ':' . $bhRow->start_time->minutes . ' ' . $bhRow->start_time->day_period;
						$closing_hours = $bhRow->end_time->hours . ':' . $bhRow->end_time->minutes . ' ' . $bhRow->end_time->day_period;
					}
					if ( is_array( $bhRow->start_time ) ) {
						$opening_hours = $bhRow->start_time['hours'] . ':' . $bhRow->start_time['minutes'] . '&nbsp;' . $bhRow->start_time['day_period'];
						$closing_hours = $bhRow->end_time['hours'] . ':' . $bhRow->end_time['minutes'] . '&nbsp;' . $bhRow->end_time['day_period'];
					}

					$opening_time = '';
					$closing_time = '';
					if ( isset( $settings->hours_24_format ) && 'yes' === $settings->hours_24_format ) {
						$opening_time = date( "G:i", strtotime( $opening_hours ) );
						$closing_time = date( "G:i", strtotime( $closing_hours ) );
					} else {
						$opening_time = date( "g:i A", strtotime( $opening_hours ) );
						$closing_time = date( "g:i A", strtotime( $closing_hours ) );
					}
				
					if ( $bhRow->hours_type == 'day' ) {
						echo '<time itemprop="opens" content="'.$opening_time.'">' . $opening_time . '</time>';
						echo ' - ';
						echo '<time itemprop="closes" content="'.$closing_time.'">' . $closing_time . '</time>';
					} else {
						$datetime 	= array();
						$start_day 	= 0;
						$end_day 	= 0;
						
						foreach ( pp_long_day_format() as $day => $label ) {
							if ( $day == $bhRow->start_day ) {
								$start_day = 1;
							}
							if ( ! $start_day ) {
								continue;
							}
							if ( $end_day ) {
								break;
							}
							if ( $day == $bhRow->end_day ) {
								$end_day = 1;
							}
							$datetime[] = substr( $day, 0, 2 );
						}

						$datetime_str = implode(',', $datetime);
						$datetime_str .= ' ';
						$datetime_str .= $opening_time;
						$datetime_str .= '-';
						$datetime_str .= $closing_time;

						echo '<time itemprop="openingHours" datetime="' . $datetime_str . '">';
						echo $opening_time;
						echo ' - ';
						echo $closing_time;
						echo '</time>';
					}
				} ?>
			</div>
		</div>
		<?php
	endfor; ?>
</div>
