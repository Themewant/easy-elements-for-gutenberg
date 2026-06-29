<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Local template/iteration variables.

/**
 * Server-side render for the Login | Register block.
 *
 * Mirrors the markup of the Elementor "Login | Register" widget
 * (easy-elements/widgets/login-register). Element classes use the "eelfg-" prefix.
 * AJAX submit is handled by EELFG\Public\Login_Register (login-register.php).
 *
 * $attributes, $content and $block are provided by register_block_type().
 */

$H = '\EELFG\Frontend\Helper';

$unique_id = ! empty( $attributes['blockId'] ) ? $attributes['blockId'] : 'eelfg-lr-' . substr( md5( wp_json_encode( $attributes ) ), 0, 6 );

$form_type   = isset( $attributes['formType'] ) ? $attributes['formType'] : 'login';
$is_editor   = ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || is_admin();
$show_label  = ! empty( $attributes['showLabel'] );
$show_icon   = ! empty( $attributes['showIcon'] );
$ajax_loader = ! empty( $attributes['showAjaxLoader'] );
$nonce       = wp_create_nonce( 'eelfg_lr_nonce' );
$ajaxurl     = admin_url( 'admin-ajax.php' );

$block_wrap_attr = get_block_wrapper_attributes( array( 'class' => 'eelfg-block eelfg-lr-block-wrap ' . $unique_id ) );
if ( empty( $block_wrap_attr ) ) {
	$block_wrap_attr = 'class="eelfg-block eelfg-lr-block-wrap ' . esc_attr( $unique_id ) . '"';
}

// ---------------------------------------------------------------------------
// Inline styles (scoped to this instance).
// ---------------------------------------------------------------------------
$selector     = '.eelfg-lr-block-wrap.' . $unique_id;
$style_handle = 'eelfg-login-register-style';

$typo = function ( $obj ) use ( $H ) {
	$out = [];
	if ( empty( $obj ) || ! is_array( $obj ) ) return $out;
	if ( ! empty( $obj['fontFamily'] ) ) $out['font-family'] = $obj['fontFamily'];
	if ( ! empty( $obj['fontSize'] ) ) $out['font-size'] = $H::ensure_unit( $obj['fontSize'] );
	if ( ! empty( $obj['fontWeight'] ) ) $out['font-weight'] = $obj['fontWeight'];
	if ( ! empty( $obj['fontStyle'] ) ) $out['font-style'] = $obj['fontStyle'];
	if ( ! empty( $obj['textTransform'] ) ) $out['text-transform'] = $obj['textTransform'];
	if ( ! empty( $obj['lineHeight'] ) ) $out['line-height'] = $obj['lineHeight'];
	if ( ! empty( $obj['letterSpacing'] ) ) $out['letter-spacing'] = $H::ensure_unit( $obj['letterSpacing'] );
	return $out;
};
$dims = function ( $obj, $type ) use ( $H ) {
	$out = [];
	if ( empty( $obj ) || ! is_array( $obj ) ) return $out;
	if ( 'padding' === $type ) {
		$map = [ 'top' => 'padding-top', 'right' => 'padding-right', 'bottom' => 'padding-bottom', 'left' => 'padding-left' ];
	} elseif ( 'margin' === $type ) {
		$map = [ 'top' => 'margin-top', 'right' => 'margin-right', 'bottom' => 'margin-bottom', 'left' => 'margin-left' ];
	} else {
		$map = [ 'top' => 'border-top-left-radius', 'right' => 'border-top-right-radius', 'bottom' => 'border-bottom-right-radius', 'left' => 'border-bottom-left-radius' ];
	}
	foreach ( $map as $side => $css_prop ) {
		if ( isset( $obj[ $side ] ) && '' !== $obj[ $side ] ) $out[ $css_prop ] = $H::ensure_unit( $obj[ $side ] );
	}
	return $out;
};
$shadow = function ( $obj ) use ( $H ) {
	if ( empty( $obj ) || ! is_array( $obj ) ) return [];
	$x = (int) ( $obj['x'] ?? 0 ); $y = (int) ( $obj['y'] ?? 0 ); $b = (int) ( $obj['b'] ?? 0 ); $s = (int) ( $obj['s'] ?? 0 );
	$c = $obj['c'] ?? '';
	$transparent = in_array( str_replace( ' ', '', (string) $c ), [ '', 'rgba(0,0,0,0)' ], true );
	if ( 0 === $x && 0 === $y && 0 === $b && 0 === $s && $transparent ) return [];
	return [ 'box-shadow' => $H::box_shadow_to_css( $obj ) ];
};
$u = function ( $key ) use ( $attributes, $H ) {
	return ( isset( $attributes[ $key ] ) && '' !== $attributes[ $key ] ) ? $H::ensure_unit( $attributes[ $key ] ) : '';
};

// Field input box (applies to the plain control and the icon-mode inner wrap).
$box_sel = '.eelfg-authentication-form-field:not(.eelfg-authentication-form-submit-field):not(.eelfg-authentication-form-cta-field):not(.eelfg-authentication-form-checkbox-field) .eelfg-form-control, '
	. $selector . ' .eelfg-input-has-icon .eelfg-authentication-form-field:not(.eelfg-authentication-form-submit-field):not(.eelfg-authentication-form-cta-field):not(.eelfg-authentication-form-checkbox-field) .eelfg-authentication-form-field-inner';

$box_styles = [];
if ( ! empty( $attributes['fieldBgColor'] ) ) $box_styles['background-color'] = $attributes['fieldBgColor'];
$box_styles = array_merge( $box_styles, $dims( $attributes['fieldPadding'] ?? [], 'padding' ), $dims( $attributes['fieldMargin'] ?? [], 'margin' ), $dims( $attributes['fieldRadius'] ?? [], 'radius' ), $shadow( $attributes['fieldBoxShadow'] ?? [] ) );
if ( ! empty( $attributes['fieldBorder'] ) ) $box_styles = array_merge( $box_styles, $H::border_to_css_props( $attributes['fieldBorder'] ) );

$control = $typo( $attributes['fieldTypography'] ?? [] );
if ( ! empty( $attributes['fieldInputColor'] ) ) $control['color'] = $attributes['fieldInputColor'];

$placeholder = ! empty( $attributes['fieldPlaceholderColor'] ) ? [ 'color' => $attributes['fieldPlaceholderColor'] ] : [];

$icon_i = [];
$icon_svg = [];
if ( ! empty( $attributes['iconColor'] ) ) { $icon_i['color'] = $attributes['iconColor']; $icon_svg['fill'] = $attributes['iconColor']; }
if ( '' !== $u( 'iconSize' ) ) { $icon_i['font-size'] = $u( 'iconSize' ); $icon_svg['width'] = $u( 'iconSize' ); $icon_svg['height'] = $u( 'iconSize' ); }

// Label.
$label_styles = $typo( $attributes['labelTypography'] ?? [] );
if ( ! empty( $attributes['labelColor'] ) ) $label_styles['color'] = $attributes['labelColor'];
$label_styles = array_merge( $label_styles, $dims( $attributes['labelMargin'] ?? [], 'margin' ) );

// Links.
$links = $typo( $attributes['linksTypography'] ?? [] );
if ( ! empty( $attributes['linksColor'] ) ) $links['color'] = $attributes['linksColor'];
$links_hover = ! empty( $attributes['linksHoverColor'] ) ? [ 'color' => $attributes['linksHoverColor'] ] : [];

// Submit button.
$submit = [];
if ( ! empty( $attributes['submitColor'] ) ) $submit['color'] = $attributes['submitColor'];
if ( ! empty( $attributes['submitBg'] ) ) $submit['background'] = $attributes['submitBg'];
$submit = array_merge( $submit, $dims( $attributes['submitPadding'] ?? [], 'padding' ), $dims( $attributes['submitMargin'] ?? [], 'margin' ), $dims( $attributes['submitRadius'] ?? [], 'radius' ), $shadow( $attributes['submitBoxShadow'] ?? [] ) );
if ( ! empty( $attributes['submitBorder'] ) ) $submit = array_merge( $submit, $H::border_to_css_props( $attributes['submitBorder'] ) );
$submit_field = ( '' !== $u( 'submitWidth' ) ) ? [ 'width' => ( (float) $attributes['submitWidth'] ) . '%' ] : [];
$submit_hover = [];
if ( ! empty( $attributes['submitHoverColor'] ) ) $submit_hover['color'] = $attributes['submitHoverColor'];
if ( ! empty( $attributes['submitHoverBg'] ) ) $submit_hover['background'] = $attributes['submitHoverBg'];
$submit_icon = ( '' !== $u( 'submitIconSize' ) ) ? [ 'font-size' => $u( 'submitIconSize' ), 'width' => $u( 'submitIconSize' ), 'height' => $u( 'submitIconSize' ) ] : [];

// Messages.
$msg = $typo( $attributes['messageTypography'] ?? [] );
$msg = array_merge( $msg, $dims( $attributes['msgPadding'] ?? [], 'padding' ), $dims( $attributes['msgMargin'] ?? [], 'margin' ) );

wp_enqueue_style( $style_handle );
$H::add_custom_style( $style_handle, $selector, '', [
	$box_sel                                       => $H::get_inline_styles( $box_styles ),
	'.eelfg-form-control'                          => $H::get_inline_styles( $control ),
	'.eelfg-form-control::placeholder'             => $H::get_inline_styles( $placeholder ),
	'.eelfg-form-field-icon i'                     => $H::get_inline_styles( $icon_i ),
	'.eelfg-form-field-icon svg'                   => $H::get_inline_styles( $icon_svg ),
	'.eelfg-authentication-form-label'             => $H::get_inline_styles( $label_styles ),
	'.eelfg-authentication-form a'                 => $H::get_inline_styles( $links ),
	'.eelfg-authentication-form a:hover'           => $H::get_inline_styles( $links_hover ),
	'.eelfg-submit-button'                         => $H::get_inline_styles( $submit ),
	'.eelfg-submit-button:hover'                   => $H::get_inline_styles( $submit_hover ),
	'.eelfg-authentication-form-submit-field'      => $H::get_inline_styles( $submit_field ),
	'.eelfg-submit-button i, ' . $selector . ' .eelfg-submit-button svg' => $H::get_inline_styles( $submit_icon ),
	'.eelfg-form-status'                           => $H::get_inline_styles( $msg ),
	'.eelfg-form-status .eelfg-form-success-msg'   => ! empty( $attributes['successMessageColor'] ) ? 'color:' . $attributes['successMessageColor'] : '',
	'.eelfg-form-status .eelfg-form-error-msg'     => ! empty( $attributes['errorMessageColor'] ) ? 'color:' . $attributes['errorMessageColor'] : '',
] );

// ---------------------------------------------------------------------------
// Markup helpers.
// ---------------------------------------------------------------------------
$render_icon = function ( $val ) {
	return ( ! empty( $val ) && 'none' !== $val ) ? '<i class="eelfg-icon ' . esc_attr( $val ) . '" aria-hidden="true"></i>' : '';
};

$loader_html = $ajax_loader ? '<span class="eelfg-form-ajax-loader"><span class="eelfg-spinner"></span></span>' : '';

$submit_btn = function ( $text, $icon, $position ) use ( $render_icon, $loader_html ) {
	$icon_html = $render_icon( $icon );
	$out  = '<div class="eelfg-authentication-form-field eelfg-authentication-form-submit-field"><div class="eelfg-authentication-form-field-inner"><div class="eelfg-form-control-wrap">';
	$out .= '<button type="submit" class="eelfg-submit-button">';
	if ( '' !== $icon_html && 'before' === $position ) {
		$out .= '<span class="eelfg-form-btn-icon">' . $icon_html . '</span>';
	}
	$out .= '<span class="eelfg-form-btn-text">' . esc_html( $text ) . '</span>';
	if ( '' !== $icon_html && 'after' === $position ) {
		$out .= '<span class="eelfg-form-btn-icon">' . $icon_html . '</span>';
	}
	$out .= $loader_html;
	$out .= '</button></div></div></div>';
	return $out;
};

$status_html = function ( $error_msg, $success_msg, $show_error, $show_success ) {
	$out = '<div class="eelfg-form-status">';
	if ( $show_error ) {
		$out .= '<p class="eelfg-form-error-msg">' . esc_html( $error_msg ) . '</p>';
	}
	if ( $show_success ) {
		$out .= '<p class="eelfg-form-success-msg">' . esc_html( $success_msg ) . '</p>';
	}
	$out .= '</div>';
	return $out;
};

$a = $attributes;
$icon_cls = $show_icon ? 'eelfg-input-has-icon' : '';

/** Render the login form. */
$render_login = function ( $is_toggle ) use ( $a, $show_label, $show_icon, $icon_cls, $render_icon, $submit_btn, $status_html, $nonce, $ajaxurl ) {
	$fields = ! empty( $a['loginFields'] ) && is_array( $a['loginFields'] ) ? $a['loginFields'] : [];
	$redirect = ! empty( $a['redirectAfterLogin'] ) ? $a['redirectAfterLogin'] : home_url();
	ob_start();
	?>
	<form method="post" class="eelfg-authentication-form eelfg-login-form <?php echo esc_attr( $icon_cls ); ?>" data-ajaxurl="<?php echo esc_url( $ajaxurl ); ?>" data-nonce="<?php echo esc_attr( $nonce ); ?>" <?php echo $is_toggle ? 'data-form-name="login"' : ''; ?>>
		<input type="hidden" name="login_redirect_link" value="<?php echo esc_url( $redirect ); ?>">
		<input type="hidden" name="empty_error_msg" value="<?php echo esc_attr( $a['emptyErrorMessage'] ?? '' ); ?>">
		<?php
		foreach ( $fields as $field ) {
			$type = ! empty( $field['type'] ) ? $field['type'] : 'username';
			$id   = 'eelfg_' . $type;
			$label = $field['label'] ?? '';
			$ph    = $field['placeholder'] ?? '';
			$icon  = $field['icon'] ?? '';
			$req   = ! empty( $field['isRequired'] ) ? 'required' : '';
			if ( 'static_text' === $type && ! empty( $field['staticText'] ) ) {
				echo '<div class="eelfg-authentication-form-field eelfg-authentication-form-static-field"><div class="eelfg-authentication-form-field-inner"><div class="eelfg-form-control-wrap">' . wp_kses_post( $field['staticText'] ) . '</div></div></div>';
				continue;
			}
			echo '<div class="eelfg-authentication-form-field"><div class="eelfg-authentication-form-field-inner">';
			if ( $show_icon && '' !== $render_icon( $icon ) ) {
				echo '<span class="eelfg-form-field-icon eelfg-user-icon">' . $render_icon( $icon ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			echo '<div class="eelfg-form-control-wrap">';
			if ( $show_label && '' !== $label ) {
				echo '<label for="' . esc_attr( $id ) . '" class="eelfg-authentication-form-label">' . esc_html( $label ) . '</label>';
			}
			echo '<input type="' . esc_attr( 'password' === $type ? 'password' : 'text' ) . '" name="' . esc_attr( $type ) . '" class="eelfg-form-control" id="' . esc_attr( $id ) . '" placeholder="' . esc_attr( $ph ) . '" ' . esc_attr( $req ) . ' />';
			echo '</div></div><div class="eelfg-error-msg" data-error-for="' . esc_attr( $id ) . '"></div></div>';
		}

		if ( ! empty( $a['rememberMe'] ) || ! empty( $a['showPasswordResetLink'] ) ) {
			echo '<div class="eelfg-authentication-form-field eelfg-authentication-form-checkbox-field"><div class="eelfg-authentication-form-field-inner"><div class="eelfg-form-control-wrap">';
			if ( ! empty( $a['rememberMe'] ) ) {
				echo '<label class="eelfg-authentication-form-label"><input type="checkbox" name="eel_rememberme" id="eel_rememberme" value="forever" /> ' . esc_html( $a['rememberLabelText'] ?? 'Remember Me' ) . '</label>';
			}
			if ( ! empty( $a['showPasswordResetLink'] ) ) {
				echo '<a href="' . esc_url( wp_lostpassword_url() ) . '" target="_blank">' . esc_html( $a['passwordResetText'] ?? '' ) . '</a>';
			}
			echo '</div></div></div>';
		}

		echo $submit_btn( $a['submitButtonText'] ?? 'Log In', $a['submitButtonIcon'] ?? '', $a['submitButtonIconPosition'] ?? 'after' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( $is_toggle ) {
			echo '<div class="eelfg-authentication-form-field eelfg-authentication-form-cta-field"><div class="eelfg-authentication-form-field-inner"><div class="eelfg-form-control-wrap eelfg-form-switcher" data-switch-to="register">'
				. esc_html__( "Don't have an account?", 'easy-elements-for-gutenberg' ) . ' <span class="eelfg-switch-link">' . esc_html__( 'Sign Up', 'easy-elements-for-gutenberg' ) . '</span></div></div></div>';
		}

		echo $status_html( $a['loginErrorMessage'] ?? '', $a['loginSuccessMessage'] ?? '', ! empty( $a['showErrorMessage'] ), ! empty( $a['showSuccessMessage'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
	</form>
	<?php
	return ob_get_clean();
};

/** Render the register form. */
$render_register = function ( $is_toggle ) use ( $a, $show_label, $show_icon, $icon_cls, $render_icon, $submit_btn, $status_html, $nonce, $ajaxurl ) {
	$fields  = ! empty( $a['registerFields'] ) && is_array( $a['registerFields'] ) ? $a['registerFields'] : [];
	$options = ! empty( $a['registerOptions'] ) && is_array( $a['registerOptions'] ) ? $a['registerOptions'] : [];
	$auto_login = in_array( 'auto_login', $options, true ) ? 'yes' : 'no';
	$notify     = in_array( 'notify_email', $options, true ) ? 'yes' : 'no';
	$redirect_on = in_array( 'redirect', $options, true ) ? 'yes' : 'no';
	$redirect = ! empty( $a['redirectAfterRegister'] ) ? $a['redirectAfterRegister'] : home_url();
	$notify_admin = ! empty( $a['notifyAdminEmail'] ) ? 'yes' : 'no';
	ob_start();
	?>
	<form method="post" class="eelfg-authentication-form eelfg-register-form <?php echo esc_attr( $icon_cls ); ?>" data-ajaxurl="<?php echo esc_url( $ajaxurl ); ?>" data-nonce="<?php echo esc_attr( $nonce ); ?>" <?php echo $is_toggle ? 'data-form-name="register"' : ''; ?>>
		<input type="hidden" name="register_redirect_link" value="<?php echo esc_url( $redirect ); ?>">
		<input type="hidden" name="redirect_after_registration" value="<?php echo esc_attr( $redirect_on ); ?>">
		<input type="hidden" name="auto_login" value="<?php echo esc_attr( $auto_login ); ?>">
		<input type="hidden" name="send_new_user_email" value="<?php echo esc_attr( $notify ); ?>">
		<input type="hidden" name="notify_admin_email" value="<?php echo esc_attr( $notify_admin ); ?>">
		<input type="hidden" name="empty_error_msg" value="<?php echo esc_attr( $a['emptyErrorMessage'] ?? '' ); ?>">
		<input type="hidden" name="min_length_error_msg" value="<?php echo esc_attr( $a['minLengthErrorMessage'] ?? '' ); ?>">
		<input type="hidden" name="max_length_error_msg" value="<?php echo esc_attr( $a['maxLengthErrorMessage'] ?? '' ); ?>">
		<input type="hidden" name="confirm_pass_error_msg" value="<?php echo esc_attr( $a['confirmPassErrorMessage'] ?? '' ); ?>">
		<?php
		foreach ( $fields as $field ) {
			$type   = ! empty( $field['type'] ) ? $field['type'] : 'user_login';
			$meta   = $field['metaKey'] ?? '';
			$id     = 'eelfg_' . $type;
			$label  = $field['label'] ?? '';
			$ph     = $field['placeholder'] ?? '';
			$icon   = $field['icon'] ?? '';
			$req    = ! empty( $field['isRequired'] ) ? 'required' : '';
			$min    = ( '' !== ( $field['minLength'] ?? '' ) ) ? $field['minLength'] : '';
			$max    = ( '' !== ( $field['maxLength'] ?? '' ) ) ? $field['maxLength'] : '';
			$width  = ! empty( $field['width'] ) ? $field['width'] : '';
			$wstyle = $width ? ' style="width:' . esc_attr( $width ) . ';"' : '';

			if ( 'custom' === $type && '' !== $meta ) {
				$type = 'custom_meta[' . $meta . ']';
				$id   = 'eelfg_' . $meta;
			}

			if ( 'easyel_consent' === $type ) {
				echo '<div class="eelfg-authentication-form-field eelfg-authentication-form-checkbox-field"' . $wstyle . '><div class="eelfg-authentication-form-field-inner"><div class="eelfg-form-control-wrap">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo '<label class="eelfg-authentication-form-label"><input type="checkbox" name="' . esc_attr( $type ) . '" id="' . esc_attr( $id ) . '" ' . esc_attr( $req ) . '/> ' . esc_html( $label ) . '</label>';
				echo '</div></div><div class="eelfg-error-msg" data-error-for="' . esc_attr( $id ) . '"></div></div>';
				continue;
			}
			if ( 'static_text' === $type && ! empty( $field['staticText'] ) ) {
				echo '<div class="eelfg-authentication-form-field eelfg-authentication-form-static-field"' . $wstyle . '><div class="eelfg-authentication-form-field-inner"><div class="eelfg-form-control-wrap">' . wp_kses_post( $field['staticText'] ) . '</div></div></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				continue;
			}

			$input_type = in_array( $type, [ 'user_pass', 'confirm_password' ], true ) ? 'password' : ( 'user_email' === $type ? 'email' : 'text' );
			echo '<div class="eelfg-authentication-form-field"' . $wstyle . '><div class="eelfg-authentication-form-field-inner">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			if ( $show_icon && '' !== $render_icon( $icon ) ) {
				echo '<span class="eelfg-form-field-icon eelfg-user-icon">' . $render_icon( $icon ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			echo '<div class="eelfg-form-control-wrap">';
			if ( $show_label && '' !== $label ) {
				echo '<label for="' . esc_attr( $id ) . '" class="eelfg-authentication-form-label">' . esc_html( $label ) . '</label>';
			}
			echo '<input type="' . esc_attr( $input_type ) . '" name="' . esc_attr( $type ) . '" class="eelfg-form-control" id="' . esc_attr( $id ) . '" placeholder="' . esc_attr( $ph ) . '" ' . esc_attr( $req ) . ( '' !== $min ? ' min="' . esc_attr( $min ) . '"' : '' ) . ( '' !== $max ? ' max="' . esc_attr( $max ) . '"' : '' ) . ' />';
			echo '</div></div><div class="eelfg-error-msg" data-error-for="' . esc_attr( $id ) . '"></div></div>';
		}

		// Math captcha.
		if ( ! empty( $a['enableMathCaptcha'] ) ) {
			$ca = wp_rand( 1, 9 );
			$cb = wp_rand( 1, 9 );
			$hash = wp_hash( (string) ( $ca + $cb ) );
			$cap_err = ! empty( $a['mathCaptchaError'] ) ? $a['mathCaptchaError'] : 'Incorrect answer to the math question.';
			echo '<div class="eelfg-authentication-form-field eelfg-authentication-form-captcha-field"><div class="eelfg-authentication-form-field-inner"><div class="eelfg-form-control-wrap">';
			echo '<div class="eelfg-math-captcha-question"><span class="eelfg-math-captcha-equation">' . esc_html( $ca . ' + ' . $cb . ' equals?' ) . '</span></div>';
			echo '<input type="text" name="eel_math_captcha" class="eelfg-form-control" id="eel_math_captcha" inputmode="numeric" autocomplete="off" required />';
			echo '<input type="hidden" name="eel_math_captcha_hash" value="' . esc_attr( $hash ) . '">';
			echo '<input type="hidden" name="math_captcha_error_msg" value="' . esc_attr( $cap_err ) . '">';
			echo '</div></div><div class="eelfg-error-msg" data-error-for="eel_math_captcha"></div></div>';
		}

		echo $submit_btn( $a['registerButtonText'] ?? 'Register', $a['submitButtonIcon'] ?? '', $a['submitButtonIconPosition'] ?? 'after' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( $is_toggle ) {
			echo '<div class="eelfg-authentication-form-field eelfg-authentication-form-cta-field"><div class="eelfg-authentication-form-field-inner"><div class="eelfg-form-control-wrap eelfg-form-switcher" data-switch-to="login">'
				. esc_html__( 'Already have an account?', 'easy-elements-for-gutenberg' ) . ' <span class="eelfg-switch-link">' . esc_html__( 'Login', 'easy-elements-for-gutenberg' ) . '</span></div></div></div>';
		}

		echo $status_html( $a['registerErrorMessage'] ?? '', $a['registerSuccessMessage'] ?? '', ! empty( $a['showErrorMessage'] ), ! empty( $a['showSuccessMessage'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
	</form>
	<?php
	return ob_get_clean();
};
?>
<div <?php echo wp_kses_post( $block_wrap_attr ); ?>>
	<?php
	// On the front end, show a notice when already logged in (skipped in the editor
	// so the form can still be previewed / styled).
	if ( is_user_logged_in() && ! $is_editor ) {
		echo '<a href="' . esc_url( home_url() ) . '">' . esc_html( $attributes['alreadyLoggedInMessage'] ?? 'You are already logged in.' ) . '</a>';
	} elseif ( 'login' === $form_type ) {
		echo $render_login( false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} elseif ( 'register' === $form_type ) {
		echo $render_register( false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} else {
		$default_form = ( ! empty( $attributes['defaultForm'] ) && 'register' === $attributes['defaultForm'] ) ? 'register' : 'login';
		echo '<div class="eelfg-authentication-toggle-wrap" data-active-form="' . esc_attr( $default_form ) . '">';
		echo $render_login( true );    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $render_register( true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>';
	}
	?>
</div>
