<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function eelfg_create_block_countdown_block_init() {
	// Front-end + shared style handle so render.php can attach per-instance inline CSS.
	wp_register_style(
		'eelfg-countdown-style',
		plugins_url( 'build/style-index.css', __FILE__ ),
		array( 'eelfg-public-style' ),
		EELFG_VERSION
	);

	// Editor-only styles. Compiled from src/editor.scss.
	wp_register_style(
		'eelfg-countdown-editor-style',
		plugins_url( 'build/index.css', __FILE__ ),
		array( 'eelfg-countdown-style' ),
		EELFG_VERSION
	);

	register_block_type( __DIR__ . '/build', array(
		'style'        => 'eelfg-countdown-style',
		'editor_style' => 'eelfg-countdown-editor-style',
	) );
}
add_action( 'init', 'eelfg_create_block_countdown_block_init' );
