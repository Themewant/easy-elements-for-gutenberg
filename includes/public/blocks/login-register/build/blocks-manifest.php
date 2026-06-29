<?php
// This file is generated. Do not modify it manually.
return array(
	'build' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'easy-elements-for-gutenberg/login-register',
		'version' => '0.1.0',
		'title' => 'Login | Register',
		'category' => 'easy-elements-for-gutenberg',
		'description' => 'AJAX login and registration forms with custom fields, math captcha, messages and full styling.',
		'keywords' => array(
			'login',
			'register',
			'form',
			'user',
			'account'
		),
		'example' => array(
			
		),
		'supports' => array(
			'html' => false,
			'align' => array(
				'wide'
			),
			'spacing' => array(
				'margin' => array(
					'top',
					'bottom',
					'left',
					'right'
				)
			)
		),
		'textdomain' => 'easy-elements-for-gutenberg',
		'editorScript' => 'file:./index.js',
		'viewScript' => 'file:./view.js',
		'render' => 'file:./render.php',
		'attributes' => array(
			'blockId' => array(
				'type' => 'string',
				'default' => ''
			),
			'formType' => array(
				'type' => 'string',
				'default' => 'login'
			),
			'defaultForm' => array(
				'type' => 'string',
				'default' => 'login'
			),
			'redirectAfterLogin' => array(
				'type' => 'string',
				'default' => ''
			),
			'registerOptions' => array(
				'type' => 'array',
				'default' => array(
					'auto_login',
					'redirect'
				)
			),
			'redirectAfterRegister' => array(
				'type' => 'string',
				'default' => ''
			),
			'showPasswordResetLink' => array(
				'type' => 'boolean',
				'default' => true
			),
			'rememberMe' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showAjaxLoader' => array(
				'type' => 'boolean',
				'default' => true
			),
			'notifyAdminEmail' => array(
				'type' => 'boolean',
				'default' => true
			),
			'enableMathCaptcha' => array(
				'type' => 'boolean',
				'default' => false
			),
			'mathCaptchaError' => array(
				'type' => 'string',
				'default' => 'Incorrect answer to the math question.'
			),
			'loginFields' => array(
				'type' => 'array',
				'default' => array(
					array(
						'type' => 'username',
						'label' => 'Username',
						'placeholder' => 'Username',
						'icon' => '',
						'isRequired' => true,
						'staticText' => ''
					),
					array(
						'type' => 'password',
						'label' => 'Password',
						'placeholder' => 'Password',
						'icon' => '',
						'isRequired' => true,
						'staticText' => ''
					)
				)
			),
			'registerFields' => array(
				'type' => 'array',
				'default' => array(
					array(
						'type' => 'user_login',
						'label' => 'Username',
						'placeholder' => 'Username',
						'icon' => '',
						'isRequired' => true,
						'minLength' => '',
						'maxLength' => '',
						'width' => '',
						'metaKey' => '',
						'inputType' => 'text',
						'staticText' => ''
					),
					array(
						'type' => 'user_email',
						'label' => 'Email',
						'placeholder' => 'Email',
						'icon' => '',
						'isRequired' => true,
						'minLength' => '',
						'maxLength' => '',
						'width' => '',
						'metaKey' => '',
						'inputType' => 'text',
						'staticText' => ''
					),
					array(
						'type' => 'user_pass',
						'label' => 'Password',
						'placeholder' => 'Password',
						'icon' => '',
						'isRequired' => true,
						'minLength' => '8',
						'maxLength' => '',
						'width' => '',
						'metaKey' => '',
						'inputType' => 'text',
						'staticText' => ''
					)
				)
			),
			'showLabel' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showIcon' => array(
				'type' => 'boolean',
				'default' => false
			),
			'passwordResetText' => array(
				'type' => 'string',
				'default' => 'Forgot your password?'
			),
			'rememberLabelText' => array(
				'type' => 'string',
				'default' => 'Remember Me'
			),
			'submitButtonText' => array(
				'type' => 'string',
				'default' => 'Log In'
			),
			'registerButtonText' => array(
				'type' => 'string',
				'default' => 'Register'
			),
			'submitButtonIcon' => array(
				'type' => 'string',
				'default' => ''
			),
			'submitButtonIconPosition' => array(
				'type' => 'string',
				'default' => 'after'
			),
			'showSuccessMessage' => array(
				'type' => 'boolean',
				'default' => true
			),
			'loginSuccessMessage' => array(
				'type' => 'string',
				'default' => 'Login Success!'
			),
			'registerSuccessMessage' => array(
				'type' => 'string',
				'default' => 'Registration Success!'
			),
			'showErrorMessage' => array(
				'type' => 'boolean',
				'default' => true
			),
			'loginErrorMessage' => array(
				'type' => 'string',
				'default' => 'Login failed! Enter correct email & password.'
			),
			'registerErrorMessage' => array(
				'type' => 'string',
				'default' => 'Registration Failed!'
			),
			'alreadyLoggedInMessage' => array(
				'type' => 'string',
				'default' => 'You are already logged in. Goto Homepage'
			),
			'emptyErrorMessage' => array(
				'type' => 'string',
				'default' => 'This field is required.'
			),
			'confirmPassErrorMessage' => array(
				'type' => 'string',
				'default' => 'Confirm password not matched'
			),
			'minLengthErrorMessage' => array(
				'type' => 'string',
				'default' => 'Minimum {count} characters required.'
			),
			'maxLengthErrorMessage' => array(
				'type' => 'string',
				'default' => 'Maximum {count} characters required.'
			),
			'fieldInputColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'fieldPlaceholderColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'fieldBgColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'fieldTypography' => array(
				'type' => 'object',
				'default' => array(
					'fontFamily' => '',
					'fontSize' => '',
					'fontWeight' => '',
					'fontStyle' => '',
					'textTransform' => '',
					'lineHeight' => '',
					'letterSpacing' => ''
				)
			),
			'fieldPadding' => array(
				'type' => 'object'
			),
			'fieldMargin' => array(
				'type' => 'object'
			),
			'fieldRadius' => array(
				'type' => 'object',
				'default' => array(
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => ''
				)
			),
			'fieldBorder' => array(
				'type' => 'object',
				'default' => array(
					'width' => 0,
					'color' => '',
					'style' => 'solid'
				)
			),
			'fieldBoxShadow' => array(
				'type' => 'object',
				'default' => array(
					'x' => 0,
					'y' => 0,
					'b' => 0,
					's' => 0,
					'c' => 'rgba(0, 0, 0, 0)'
				)
			),
			'iconColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'iconSize' => array(
				'type' => 'string',
				'default' => ''
			),
			'labelColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'labelTypography' => array(
				'type' => 'object',
				'default' => array(
					'fontFamily' => '',
					'fontSize' => '',
					'fontWeight' => '',
					'fontStyle' => '',
					'textTransform' => '',
					'lineHeight' => '',
					'letterSpacing' => ''
				)
			),
			'labelMargin' => array(
				'type' => 'object'
			),
			'linksColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'linksHoverColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'linksTypography' => array(
				'type' => 'object',
				'default' => array(
					'fontFamily' => '',
					'fontSize' => '',
					'fontWeight' => '',
					'fontStyle' => '',
					'textTransform' => '',
					'lineHeight' => '',
					'letterSpacing' => ''
				)
			),
			'submitColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'submitBg' => array(
				'type' => 'string',
				'default' => ''
			),
			'submitPadding' => array(
				'type' => 'object'
			),
			'submitMargin' => array(
				'type' => 'object'
			),
			'submitWidth' => array(
				'type' => 'string',
				'default' => ''
			),
			'submitRadius' => array(
				'type' => 'object',
				'default' => array(
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => ''
				)
			),
			'submitBorder' => array(
				'type' => 'object',
				'default' => array(
					'width' => 0,
					'color' => '',
					'style' => 'solid'
				)
			),
			'submitBoxShadow' => array(
				'type' => 'object',
				'default' => array(
					'x' => 0,
					'y' => 0,
					'b' => 0,
					's' => 0,
					'c' => 'rgba(0, 0, 0, 0)'
				)
			),
			'submitIconSize' => array(
				'type' => 'string',
				'default' => ''
			),
			'submitHoverColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'submitHoverBg' => array(
				'type' => 'string',
				'default' => ''
			),
			'messageTypography' => array(
				'type' => 'object',
				'default' => array(
					'fontFamily' => '',
					'fontSize' => '',
					'fontWeight' => '',
					'fontStyle' => '',
					'textTransform' => '',
					'lineHeight' => '',
					'letterSpacing' => ''
				)
			),
			'msgPadding' => array(
				'type' => 'object'
			),
			'msgMargin' => array(
				'type' => 'object'
			),
			'successMessageColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'errorMessageColor' => array(
				'type' => 'string',
				'default' => ''
			)
		)
	)
);
