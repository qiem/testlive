<?php
FLBuilderModel::default_settings($settings, array(
	'post_slug' 		=> 'post',
) );
?>
<div class="fl-custom-query fl-loop-data-source" data-source="custom_query">
	<div id="fl-builder-settings-section-content" class="fl-builder-settings-section">
		<div class="fl-builder-settings-section-header">
			<button class="fl-builder-settings-title">
				<svg class="fl-symbol">
					<use xlink:href="#fl-down-caret"></use>
				</svg>
				<?php _e('Content', 'bb-powerpack'); ?>
			</button>
		</div>
		<div class="fl-builder-settings-section-content">
			<table class="fl-form-table">
				<?php
				FLBuilder::render_settings_field(
					'post_slug',
					array(
						'type'    => 'post-type',
						'label'   => __( 'Post Type', 'bb-powerpack' ),
						'preview'	=> array(
							'type'		=> 'none',
						),
					),
					$settings
				);
				?>
			</table>

			<table class="fl-form-table">
				<?php
				FLBuilder::render_settings_field(
					'post_count',
					array(
						'type'    => 'unit',
						'label'   => __( 'Total Number of Posts', 'bb-powerpack' ),
						'default' => '10',
						'slider'  => true,
						'help'    => __( 'Leave blank or add -1 for all posts.', 'bb-powerpack' ),
					),
					$settings
				);
				?>
			</table>

			<div id="fl-builder-settings-section-filter" class="fl-builder-settings-section">
				<h3 class="fl-builder-settings-title" style="padding: 8px 18px; background: #e7ebef;"><?php _e( 'Filter', 'bb-powerpack' ); ?></h3>
				<div class="fl-builder-settings-section-content">
					<?php foreach ( FLBuilderLoop::post_types() as $slug => $type ) : ?>
						<table class="fl-form-table fl-custom-query-filter pp-custom-query-<?php echo $slug; ?>-filter fl-loop-builder-filter fl-loop-builder-<?php echo $slug; ?>-filter" <?php if($slug == $settings->post_slug) echo 'style="display:table;"'; ?>>
						<?php

						// Taxonomies
						$taxonomies = FLBuilderLoop::taxonomies($slug);

						foreach ( $taxonomies as $tax_slug => $tax ) {

							FLBuilder::render_settings_field( 'tax_' . $slug . '_' . $tax_slug, array(
								'type'          => 'suggest',
								'action'        => 'fl_as_terms',
								'data'          => $tax_slug,
								'label'         => $tax->label,
								'help'          => sprintf( __('Enter a comma separated list of %s. Only posts with these %s will be shown.', 'bb-powerpack'), $tax->label, $tax->label ),
								'matching'      => true
							), $settings );
						}

						?>
						</table>
					<?php endforeach; ?>
				</div>
			</div>

			<div id="fl-builder-settings-section-location" class="fl-builder-settings-section">
				<h3 class="fl-builder-settings-title" style="padding: 8px 18px; background: #e7ebef;"><?php _e( 'Location', 'bb-powerpack' ); ?></h3>
				<div class="fl-builder-settings-section-content">
					<table class="fl-form-table">
					<?php
					FLBuilder::render_settings_field(
						'post_map_name',
						array(
							'type'        => 'text',
							'label'       => __( 'Location Name', 'bb-powerpack' ),
							'help'        => __( 'A browser based tooltip will be applied on marker.', 'bb-powerpack' ),
							'connections' => array( 'string' ),
						),
						$settings
					);
					FLBuilder::render_settings_field(
						'post_map_latitude',
						array(
							'type'        => 'text',
							'label'       => __( 'Latitude', 'bb-powerpack' ),
							'connections' => array( 'string' ),
						),
						$settings
					);
					FLBuilder::render_settings_field(
						'post_map_longitude',
						array(
							'type'        => 'text',
							'label'       => __( 'Longitude', 'bb-powerpack' ),
							'connections' => array( 'string' ),
						),
						$settings
					);
					FLBuilder::render_settings_field(
						'post_marker_point',
						array(
							'type'    => 'pp-switch',
							'label'   => __( 'Marker Icon', 'bb-powerpack' ),
							'default' => 'default',
							'options' => array(
								'default' => __( 'Default', 'bb-powerpack' ),
								'custom'  => __( 'Custom', 'bb-powerpack' ),
							),
							'toggle'  => array(
								'custom' => array(
									'fields' => array( 'post_marker_img' ),
								),
							),
						),
						$settings
					);
					FLBuilder::render_settings_field(
						'post_marker_img',
						array(
							'type'        => 'photo',
							'label'       => __( 'Custom Marker', 'bb-powerpack' ),
							'show_remove' => true,
							'connections' => array( 'photo' ),
						),
						$settings
					);
					FLBuilder::render_settings_field(
						'post_enable_info',
						array(
							'type'    => 'pp-switch',
							'label'   => __( 'Show Info Window', 'bb-powerpack' ),
							'default' => 'yes',
							'options' => array(
								'yes' => __( 'Yes', 'bb-powerpack' ),
								'no'  => __( 'No', 'bb-powerpack' ),
							),
							'toggle'  => array(
								'yes' => array(
									'fields' => array( 'post_info_window_text' ),
								),
							),
						),
						$settings
					);
					FLBuilder::render_settings_field(
						'post_info_window_text',
						array(
							'type'          => 'editor',
							'label'         => '',
							'default'       => __( 'IdeaBox Creations', 'bb-powerpack' ),
							'media_buttons' => false,
							'connections'   => array( 'string', 'html' ),
						),
						$settings
					);
					?>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
