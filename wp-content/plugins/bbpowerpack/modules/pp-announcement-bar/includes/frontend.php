<?php if ( FLBuilderModel::is_builder_active() ) { ?>
	<div class="pp-helper"><?php _e('Click here to edit the announcement-bar module. This text is only for editing and will disappear after you publish the changes.', 'bb-powerpack'); ?></div>
<?php }
$classes = '';
if( $settings->announcement_bar_position == 'bottom' ) {
	$classes = 'bottom';
}
else if( $settings->announcement_bar_position == 'top' ) {
	$classes = 'top';
}
?>
<div class="pp-announcement-bar-wrap pp-announcement-bar-<?php echo $classes; ?>">
	<div class="pp-announcement-bar-inner">
		<div class="pp-announcement-bar-content">
			<?php if( $settings->announcement_icon ) { ?>
				<div class="pp-announcement-bar-icon">
					<span class="pp-icon <?php echo $settings->announcement_icon; ?>"></span>
				</div>
			<?php } ?>
			<?php echo wpautop( $settings->announcement_content ); ?>
			<?php if ( trim($settings->announcement_link_text) != '' ) { ?>
			<div class="pp-announcement-bar-link">
				<a href="<?php echo $settings->announcement_link; ?>" target="<?php echo $settings->announcement_link_target; ?>"<?php echo $module->get_rel(); ?>>
					<?php echo $settings->announcement_link_text; ?>
				</a>
			</div>
			<?php } ?>
		</div>
		<div class="pp-announcement-bar-close-button" tabindex="0" aria-label="<?php _e( 'Close', 'bb-powerpack' ); ?>">
			<span class="fas fa-times pp-close-button" aria-hidden="true"></span>
		</div>
	</div>
</div>
