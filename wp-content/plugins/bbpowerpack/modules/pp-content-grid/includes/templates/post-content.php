<div class="pp-content-grid-content pp-post-content">
    <?php
	if ( $settings->show_content == 'custom' && isset( $settings->custom_content ) ) {
		$themer_parse_shortcodes = apply_filters( 'pp_cg_beaver_themer_parse_shortcodes', true, $settings );
		if ( is_callable( 'FLThemeBuilderFieldConnections::parse_shortcodes' ) && $themer_parse_shortcodes ) {
			echo FLThemeBuilderFieldConnections::parse_shortcodes( $settings->custom_content );
		} else {
			echo $settings->custom_content;
		}
	} else {
		if ( $settings->content_type == 'excerpt' ) :
			the_excerpt();
		endif;
		if ( $settings->content_type == 'content' ) :
			$more = '...';
			echo wp_trim_words( get_the_content(), $settings->content_length, apply_filters( 'pp_cg_content_limit_more', $more ) );
		endif;
		if ( $settings->content_type == 'full' ) :
			echo wpautop( get_the_content() );
		endif;
	}
    ?>
</div>
