<?php
$enable_ajax = 'yes' === $settings->form_ajax ? 'true' : 'false';
$shortcode = '[gravityform id="' . absint( $settings->select_form_field ) . '" title="' . $settings->title_field . '" description="' . $settings->description_field . '" ajax="' . $enable_ajax . '" tabindex="' . $settings->form_tab_index . '"]';
?>
<div class="pp-gf-content">
	<div class="pp-gf-inner">
	<?php if ( 'yes' === $settings->form_custom_title_desc ) { ?>
		<h3 class="form-title"><?php echo $settings->custom_title; ?></h3>
		<p class="form-description"><?php echo $settings->custom_description; ?></p>
	<?php } ?>
	<?php
	if ( ! empty( $settings->select_form_field ) ) {
		// if ( is_callable( 'GFCommon::gform_do_shortcode' ) && class_exists( 'GFFormDisplay' ) && ! wp_doing_ajax() ) {
		// 	echo GFCommon::gform_do_shortcode( $shortcode );
		// } else {
		// 	echo do_shortcode( $shortcode );
		// }
		echo do_shortcode( $shortcode );
	}
	?>
	</div>
</div>
