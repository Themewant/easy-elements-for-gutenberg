<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function eelfg_create_block_button_block_init() {
	// Register the block-specific style handle so render.php can attach the
	// per-instance inline CSS to it (matches the post-grid / gallery pattern).
	wp_register_style(
		'eelfg-button-style',
		plugins_url( 'build/style-index.css', __FILE__ ),
		array( 'eelfg-public-style' ),
		EELFG_VERSION
	);

	// Editor-only styles (compiled from src/editor.scss).
	wp_register_style(
		'eelfg-button-editor-style',
		plugins_url( 'build/index.css', __FILE__ ),
		array( 'eelfg-button-style' ),
		EELFG_VERSION
	);

	register_block_type( __DIR__ . '/build', array(
		'style'        => 'eelfg-button-style',
		'editor_style' => 'eelfg-button-editor-style',
	) );
}
add_action( 'init', 'eelfg_create_block_button_block_init' );
