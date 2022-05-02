<?php
$photo    = $module->get_data();
$classes  = $module->get_classes();
$src      = $module->get_src();
$link     = $module->get_link();
$alt      = $module->get_alt();
$attrs    = $module->get_attributes();
$rel 	  = $module->get_rel();
$caption  = $module->get_caption();
$class    = '';
$width    = isset( $photo->width ) && ! empty( $photo->width ) ? $photo->width : false;
if ( 'hover' == $settings->show_caption ) {
	$class = ' pp-overlay-wrap';
}
?>
<div class="pp-photo-container">
	<div class="pp-photo<?php if ( ! empty( $settings->crop ) ) echo ' pp-photo-crop-' . $settings->crop ; ?> pp-photo-align-<?php echo $settings->align; ?> pp-photo-align-responsive-<?php echo $settings->align_responsive; ?>" itemscope itemtype="http://schema.org/ImageObject">
		<div class="pp-photo-content<?php echo $class; ?>">
			<div class="pp-photo-content-inner">
				<?php if ( ! empty( $link ) ) { ?>
				<a href="<?php echo $link; ?>" target="<?php echo $settings->link_target; ?>" itemprop="url"<?php echo $rel; ?>>
				<?php } ?>
					<img class="<?php echo $classes; ?>" src="<?php echo $src; ?>" alt="<?php echo $alt; ?>" itemprop="image" <?php echo $attrs; ?> />
					<div class="pp-overlay-bg"></div>
					<?php if ( ! empty( $caption ) && 'never' != $settings->show_caption ) { ?>
						<div class="pp-photo-caption pp-photo-caption-<?php echo $settings->show_caption; ?>" itemprop="caption"<?php echo $width ? ' style="max-width: ' . $width . 'px;"' : ''; ?>><?php echo $caption; ?></div>
					<?php } ?>
				<?php if ( ! empty( $link ) ) { ?>
				</a>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
