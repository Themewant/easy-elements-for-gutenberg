<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function eelfg_create_block_heading_block_init() {
	wp_register_style(
		'eelfg-heading-style',
		plugins_url( 'build/style-index.css', __FILE__ ),
		array( 'eelfg-public-style' ),
		EELFG_VERSION
	);

	// Editor-only styles (compiled from src/editor.scss).
	wp_register_style(
		'eelfg-heading-editor-style',
		plugins_url( 'build/index.css', __FILE__ ),
		array( 'eelfg-heading-style' ),
		EELFG_VERSION
	);

	register_block_type( __DIR__ . '/build', array(
		'style'        => 'eelfg-heading-style',
		'editor_style' => 'eelfg-heading-editor-style',
	) );
}
add_action( 'init', 'eelfg_create_block_heading_block_init' );
