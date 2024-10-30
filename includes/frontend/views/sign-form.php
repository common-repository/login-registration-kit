<?php
/**
 * $user_registration_kit_form_login_type
 * $user_registration_kit_form_login_shortcode
 * $user_registration_kit_form_login_remember_me
 * $user_registration_kit_form_login_hide_labels
 * $user_registration_kit_form_login_lost_password
 * $user_registration_kit_form_login_redirect
 * $user_registration_kit_captcha
 * $user_registration_kit_captcha_site_key
 * $user_registration_kit_captcha_secret_key
 * $both
 * $users_can_register
 * $unique_id
 */

$unique_id_sign      = uniqid( 'tk_lp_sign_id' );
$unique_id_lost_pass = uniqid( 'tk_lp_lost_pass_id' );

if ( esc_html( $user_registration_kit_form_login_type ) == 'simple' ) {
	?>

	<form id="sign-in<?php echo esc_attr( $unique_id ); ?>" class="tk-lp-form user-register-kit-sign tk-lp-tabs-form-content active" data-handler="lrk_sign_in_action">
		<?php do_action( 'login_form' ); ?>
		<div class="tk-lp-alert-cont"></div>
		<?php if ( $user_registration_kit_captcha == 'yes' ) { ?>
			<input class="lrk-sign-captcha-token simple-input" type="hidden" name="token">
		<?php } ?>
		<input type="hidden" value="<?php echo wp_create_nonce( 'user-registration-kit' ); ?>" name="_ajax_nonce" />
		<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $user_registration_kit_form_login_redirect ); ?>" />

		<div class="tk-lp-form-title"><?php echo esc_html__( 'Sign In', 'user-registration-kit' ); ?></div>
		<div class="tk-lp-form-item">
			<?php if ( esc_html( $user_registration_kit_form_login_hide_labels ) != 'yes' ) { ?>
				<label for="username<?php echo esc_attr( $unique_id_sign ); ?>" class="tk-lp-label"><?php echo esc_html__( 'Username or Email Address', 'user-registration-kit' ); ?></label>
			<?php } ?>
			<input class="tk-lp-input tk-lp-first-input" id="username<?php echo esc_attr( $unique_id_sign ); ?>" name="log" type="text" placeholder="<?php if ( esc_html( $user_registration_kit_form_login_hide_labels ) == 'yes' ) {
				echo esc_attr( 'Username or Email Address', 'user-registration-kit' );
			} ?>" />
		</div>
		<div class="tk-lp-form-item">
			<?php if ( esc_html( $user_registration_kit_form_login_hide_labels ) != 'yes' ) { ?>
				<label for="password<?php echo esc_attr( $unique_id_sign ); ?>" class="tk-lp-label"><?php echo esc_html__( 'Password', 'user-registration-kit' ); ?></label>
			<?php } ?>
			<input class="tk-lp-input" id="password<?php echo esc_attr( $unique_id_sign ); ?>" name="pwd" type="password" placeholder="<?php if ( esc_html( $user_registration_kit_form_login_hide_labels ) == 'yes' ) {
				echo esc_attr( 'Password', 'user-registration-kit' );
			} ?>" />
		</div>
		<?php if ( esc_html( $user_registration_kit_form_login_remember_me ) == 'yes' || esc_html( $user_registration_kit_form_login_lost_password ) == 'yes' ) { ?>
			<div class="tk-lp-form-item">
				<div class="tk-lp-remember">
					<?php if ( esc_html( $user_registration_kit_form_login_remember_me ) == 'yes' ) { ?>
						<label class="tk-lp-checkbox">
							<input type="checkbox" name="rememberme" value="forever" />
							<span class="tk-lp-control-indicator"></span>
							<?php echo esc_html__( 'Remember Me', 'user-registration-kit' ); ?>
						</label>
					<?php } ?>
					<?php if ( esc_html( $user_registration_kit_form_login_lost_password ) == 'yes' ) { ?>
						<a href="#" class="tk-lp-link-lost tk-lp-tabs-form-item" data-id="lost-password<?php echo esc_attr( $unique_id ); ?>"><?php echo esc_html__( 'Lost your password?', 'user-registration-kit' ); ?></a>
					<?php } ?>
				</div>
			</div>
		<?php } ?>
		<button type="button" class="submit-bttn tk-lp-button tk-lp-button--dark tk-lp-w-full"><?php echo esc_html__( 'Log In', 'user-registration-kit' ); ?></button>
		<?php if ( $users_can_register && $both ) { ?>
			<button type="button" class="tk-lp-button tk-lp-button--grey tk-lp-w-full tk-lp-tabs-form-item" data-id="sign-up<?php echo esc_attr( $unique_id ); ?>"><?php echo esc_html__( 'Create an Account', 'user-registration-kit' ); ?></button>
		<?php } ?>
	</form>
	<form id="lost-password<?php echo esc_attr( $unique_id ); ?>" class="tk-lp-form tk-lp-tabs-form-content user-register-kit-lost-password">
		<?php do_action( 'lostpassword_form' ); ?>
		<div class="tk-lp-form-title"><?php echo esc_html__( 'Lost Password', 'user-registration-kit' ); ?></div>
		<div class="tk-lp-alert-cont"></div>
		<div class="tk-lp-form-item">
			<label for="username<?php echo esc_attr( $unique_id_lost_pass ); ?>" class="tk-lp-label"><?php echo esc_html__( 'Email Address', 'user-registration-kit' ); ?></label>
			<input class="tk-lp-input tk-lp-first-input" id="username<?php echo esc_attr( $unique_id_lost_pass ); ?>" name="email" type="text">
		</div>
		<button type="button" class="tk-lp-submit-bttn tk-lp-button tk-lp-button--dark tk-lp-w-full"><?php echo esc_html__( 'Get New Password', 'user-registration-kit' ); ?></button>
		<?php if ( $users_can_register && $both ) { ?>
			<button type="button" class="tk-lp-button tk-lp-button--grey tk-lp-w-full tk-lp-tabs-form-item" data-id="sign-up<?php echo esc_attr( $unique_id ); ?>"><?php echo esc_html__( 'Create an Account', 'user-registration-kit' ); ?></button>
		<?php } else { ?>
			<button type="button" class="tk-lp-button tk-lp-button--grey tk-lp-w-full tk-lp-tabs-form-item" data-id="sign-in<?php echo esc_attr( $unique_id ); ?>"><?php echo esc_html__( 'Sign In', 'user-registration-kit' ); ?></button>
		<?php } ?>
	</form>
	<?php
} else {
	echo do_shortcode( wp_kses_post($user_registration_kit_form_login_shortcode) );
}
?>