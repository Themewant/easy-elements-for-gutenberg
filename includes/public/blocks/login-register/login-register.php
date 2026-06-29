<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// AJAX login / registration handlers (registered on init for this block).
require_once __DIR__ . '/class-login-register.php';

function eelfg_create_block_login_register_block_init() {
	// Front-end + shared style handle so render.php can attach per-instance inline CSS.
	wp_register_style(
		'eelfg-login-register-style',
		plugins_url( 'build/style-index.css', __FILE__ ),
		array( 'eelfg-public-style' ),
		EELFG_VERSION
	);

	// Editor-only styles (sidebar repeater UI). Compiled from src/editor.scss.
	wp_register_style(
		'eelfg-login-register-editor-style',
		plugins_url( 'build/index.css', __FILE__ ),
		array( 'eelfg-login-register-style' ),
		EELFG_VERSION
	);

	register_block_type( __DIR__ . '/build', array(
		'style'        => 'eelfg-login-register-style',
		'editor_style' => 'eelfg-login-register-editor-style',
	) );
}
add_action( 'init', 'eelfg_create_block_login_register_block_init' );
