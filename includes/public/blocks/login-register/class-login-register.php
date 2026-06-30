<?php
/**
 * AJAX handlers for the Login | Register block (login + registration).
 *
 * Ported from easy-elements/widgets/login-register/class.login-register.php.
 * Actions: eelfg_login / eelfg_register. Nonce action: eelfg_lr_nonce.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'EELFG_Login_Register' ) ) :

class EELFG_Login_Register {

	public function __construct() {
		add_action( 'wp_ajax_eelfg_login', [ $this, 'handle_login' ] );
		add_action( 'wp_ajax_nopriv_eelfg_login', [ $this, 'handle_login' ] );
		add_action( 'wp_ajax_eelfg_register', [ $this, 'handle_register' ] );
		add_action( 'wp_ajax_nopriv_eelfg_register', [ $this, 'handle_register' ] );
	}

	private function verify_nonce() {
		$nonce = ! empty( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		return $nonce && wp_verify_nonce( $nonce, 'eelfg_lr_nonce' );
	}

	public function handle_login() {
		if ( ! $this->verify_nonce() ) {
			return wp_send_json_error( [ 'msg' => 'Security check failed!' ] );
		}
		if ( is_user_logged_in() ) {
			return wp_send_json_error( [ 'msg' => 'You are already logged in.' ] );
		}

		// Nonce is verified above via $this->verify_nonce(); the sniff cannot trace cross-method verification.
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$user_login = ! empty( $_POST['user'] ) ? sanitize_user( wp_unslash( $_POST['user'] ) ) : '';
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		$user_pass  = ! empty( $_POST['pwd'] ) ? wp_unslash( $_POST['pwd'] ) : '';
		$remember   = ! empty( $_POST['remember'] );

		$user = wp_signon( [
			'user_login'    => $user_login,
			'user_password' => $user_pass,
			'remember'      => $remember,
		], is_ssl() );

		// phpcs:enable WordPress.Security.NonceVerification.Missing

		if ( is_wp_error( $user ) ) {
			return wp_send_json_error( [ 'msg' => 'Invalid username or password.' ] );
		}

		wp_set_current_user( $user->ID );
		return wp_send_json_success();
	}

	public function handle_register() {
		if ( ! $this->verify_nonce() ) {
			return wp_send_json_error( [ 'msg' => 'Security check failed!' ] );
		}
		if ( is_user_logged_in() ) {
			return wp_send_json_error( [ 'msg' => 'You are already logged in.' ] );
		}
		if ( ! get_option( 'users_can_register' ) ) {
			return wp_send_json_error( [ 'msg' => 'User registration is currently disabled.' ] );
		}

		// Nonce is verified above via $this->verify_nonce(); the sniff cannot trace cross-method verification.
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$custom_meta = ! empty( $_POST['custom_meta'] )
			? map_deep( wp_unslash( $_POST['custom_meta'] ), 'sanitize_text_field' )
			: [];

		$easyel_consent = ! empty( $_POST['easyel_consent'] ) ? 'yes' : 'no';
		$default_role   = get_option( 'default_role', 'subscriber' );

		$user_data = [
			'user_login'    => ! empty( $_POST['user_login'] ) ? sanitize_user( wp_unslash( $_POST['user_login'] ), true ) : '',
			'user_email'    => ! empty( $_POST['user_email'] ) ? sanitize_email( wp_unslash( $_POST['user_email'] ) ) : '',
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			'user_pass'     => ! empty( $_POST['user_pass'] ) ? wp_unslash( $_POST['user_pass'] ) : '',
			'role'          => $default_role,
			'first_name'    => ! empty( $_POST['first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['first_name'] ) ) : '',
			'last_name'     => ! empty( $_POST['last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['last_name'] ) ) : '',
			'display_name'  => ! empty( $_POST['display_name'] ) ? sanitize_text_field( wp_unslash( $_POST['display_name'] ) ) : '',
			'user_nicename' => ! empty( $_POST['user_nicename'] ) ? sanitize_text_field( wp_unslash( $_POST['user_nicename'] ) ) : '',
			'nickname'      => ! empty( $_POST['nickname'] ) ? sanitize_text_field( wp_unslash( $_POST['nickname'] ) ) : '',
			'user_url'      => ! empty( $_POST['user_url'] ) ? esc_url_raw( wp_unslash( $_POST['user_url'] ) ) : '',
			'description'   => ! empty( $_POST['description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['description'] ) ) : '',
		];

		$auto_login         = ( isset( $_POST['auto_login'] ) && 'yes' === $_POST['auto_login'] ) ? 'yes' : 'no';
		$send_new_user_email = ( isset( $_POST['send_new_user_email'] ) && 'yes' === $_POST['send_new_user_email'] ) ? 'yes' : 'no';
		$notify_admin_email  = ( isset( $_POST['notify_admin_email'] ) && 'yes' === $_POST['notify_admin_email'] ) ? 'yes' : 'no';

		if ( empty( $user_data['user_login'] ) ) {
			return wp_send_json_error( [ 'msg' => 'Username is required.' ] );
		}
		if ( ! validate_username( $user_data['user_login'] ) ) {
			return wp_send_json_error( [ 'msg' => 'Invalid username.' ] );
		}
		if ( username_exists( $user_data['user_login'] ) ) {
			return wp_send_json_error( [ 'msg' => 'Username already exists.' ] );
		}
		if ( empty( $user_data['user_email'] ) || ! is_email( $user_data['user_email'] ) ) {
			return wp_send_json_error( [ 'msg' => 'Invalid email address.' ] );
		}
		if ( email_exists( $user_data['user_email'] ) ) {
			return wp_send_json_error( [ 'msg' => 'Email already exists.' ] );
		}
		if ( empty( $user_data['user_pass'] ) ) {
			return wp_send_json_error( [ 'msg' => 'Password is required.' ] );
		}
		if ( strlen( $user_data['user_pass'] ) < 8 ) {
			return wp_send_json_error( [ 'msg' => 'Password must be at least 8 characters.' ] );
		}

		if ( isset( $_POST['confirm_password'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$confirm_password = wp_unslash( $_POST['confirm_password'] );
			if ( '' !== $confirm_password && ! hash_equals( $user_data['user_pass'], (string) $confirm_password ) ) {
				return wp_send_json_error( [ 'msg' => 'Confirm password does not match.' ] );
			}
		}

		if ( isset( $_POST['eel_math_captcha_hash'] ) ) {
			$captcha_answer = isset( $_POST['eel_math_captcha'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['eel_math_captcha'] ) ) ) : '';
			$captcha_hash   = sanitize_text_field( wp_unslash( $_POST['eel_math_captcha_hash'] ) );
			if ( '' === $captcha_answer || ! hash_equals( wp_hash( $captcha_answer ), $captcha_hash ) ) {
				$captcha_error = ! empty( $_POST['math_captcha_error_msg'] ) ? sanitize_text_field( wp_unslash( $_POST['math_captcha_error_msg'] ) ) : 'Incorrect answer to the math question.';
				return wp_send_json_error( [ 'msg' => $captcha_error ] );
			}
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		$user_data         = apply_filters( 'eelfg/login-register/new-user-data', $user_data );
		$user_data['role'] = $default_role;

		$user_id = wp_insert_user( $user_data );
		if ( is_wp_error( $user_id ) ) {
			return wp_send_json_error( [ 'msg' => $user_id->get_error_message() ] );
		}

		if ( is_array( $custom_meta ) ) {
			foreach ( $custom_meta as $key => $value ) {
				if ( $this->is_meta_key_allowed( $key ) ) {
					update_user_meta( $user_id, $key, $value );
				}
			}
		}
		update_user_meta( $user_id, 'easyel_consent', $easyel_consent );

		remove_action( 'register_new_user', 'wp_send_new_user_notifications' );
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core hook fired intentionally.
		do_action( 'register_new_user', $user_id );

		$scope = [];
		if ( 'yes' === $send_new_user_email ) { $scope[] = 'user'; }
		if ( 'yes' === $notify_admin_email ) { $scope[] = 'admin'; }
		if ( ! empty( $scope ) ) {
			wp_new_user_notification( $user_id, null, count( $scope ) === 2 ? 'both' : $scope[0] );
		}

		if ( 'yes' === $auto_login ) {
			wp_set_current_user( $user_id );
			wp_set_auth_cookie( $user_id, true, is_ssl() );
		}

		return wp_send_json_success( [ 'msg' => 'User created successfully' ] );
	}

	private function is_meta_key_allowed( $key ) {
		if ( ! is_string( $key ) || '' === $key ) {
			return false;
		}
		if ( function_exists( 'is_protected_meta' ) && is_protected_meta( $key, 'user' ) ) {
			return false;
		}
		$blocked = [ 'wp_capabilities', 'wp_user_level', 'session_tokens', 'role', 'roles', 'default_password_nag', 'user_status' ];
		if ( in_array( strtolower( $key ), $blocked, true ) ) {
			return false;
		}
		if ( preg_match( '/(^|_)(capabilities|user_level)$/i', $key ) ) {
			return false;
		}
		if ( ! preg_match( '/^[A-Za-z0-9_]+$/', $key ) ) {
			return false;
		}
		return true;
	}
}

new EELFG_Login_Register();

endif;
