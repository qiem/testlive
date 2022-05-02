<div class="pp-social-login">
	<?php if ( isset( $settings->separator ) && 'no' !== $settings->separator ) { ?>
	<div class="pp-login-form-sep">
		<span class="pp-login-form-sep-text"><?php echo $settings->separator_text; ?></span>
	</div>
	<?php } ?>
	<div class="pp-social-login-wrap pp-social-login--<?php echo $settings->social_button_type; ?> pp-social-login--layout-<?php echo $settings->social_button_layout; ?>">
		<?php if ( 'yes' === $settings->facebook_login ) { ?>
			<div class="pp-fb-login-button pp-social-login-button" id="pp-fb-login-button" tabindex="0" role="button">
				<span class="pp-social-login-icon">
					<svg xmlns="http://www.w3.org/2000/svg">
						<path d="M22.688 0H1.323C.589 0 0 .589 0 1.322v21.356C0 23.41.59 24 1.323 24h11.505v-9.289H9.693V11.09h3.124V8.422c0-3.1 1.89-4.789 4.658-4.789 1.322 0 2.467.1 2.8.145v3.244h-1.922c-1.5 0-1.801.711-1.801 1.767V11.1h3.59l-.466 3.622h-3.113V24h6.114c.734 0 1.323-.589 1.323-1.322V1.322A1.302 1.302 0 0 0 22.688 0z"></path>
					</svg>
				</span>
				<span class="pp-social-login-label"><?php _e( 'Facebook', 'bb-powerpack' ); ?></span>
			</div>
		<?php } ?>
		<?php if ( 'yes' === $settings->google_login ) { ?>
			<div class="pp-google-login-button pp-social-login-button" id="pp-google-login-button" tabindex="0" role="button">
				<span class="pp-social-login-icon">
					<svg xmlns="http://www.w3.org/2000/svg">
						<path d="M11.988,14.28 L11.988,9.816 L23.22,9.816 C23.388,10.572 23.52,11.28 23.52,12.276 C23.52,19.128 18.924,24 12,24 C5.376,24 -9.47390314e-15,18.624 -9.47390314e-15,12 C-9.47390314e-15,5.376 5.376,0 12,0 C15.24,0 17.952,1.188 20.028,3.132 L16.62,6.444 C15.756,5.628 14.244,4.668 12,4.668 C8.028,4.668 4.788,7.968 4.788,12.012 C4.788,16.056 8.028,19.356 12,19.356 C16.596,19.356 18.288,16.176 18.6,14.292 L11.988,14.292 L11.988,14.28 Z"></path>
					</svg>
				</span>
				<span class="pp-social-login-label"><?php _e( 'Google', 'bb-powerpack' ); ?></span>
			</div>
		<?php } ?>
	</div>
</div>