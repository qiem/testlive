<div class="<?php echo $module->get_classname(); ?>">
	<a <?php echo $module->get_attributes(); ?>>
		<?php if ( ! empty( $settings->icon ) && ( 'before' == $settings->icon_position || ! isset( $settings->icon_position ) ) && $settings->display_icon == 'yes' ) : ?>
		<i class="pp-button-icon pp-button-icon-before <?php echo $settings->icon; ?>"></i>
		<?php endif; ?>
		<span class="pp-button-text"><?php echo $settings->text; ?></span>
		<?php if ( ! empty( $settings->icon ) && 'after' == $settings->icon_position && $settings->display_icon == 'yes' ) : ?>
		<i class="pp-button-icon pp-button-icon-after <?php echo $settings->icon; ?>"></i>
		<?php endif; ?>
	</a>
</div>
