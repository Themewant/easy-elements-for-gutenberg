<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function eelfg_create_block_process_grid_block_init() {
	// Front-end + shared style handle so render.php can attach per-instance inline CSS.
	wp_register_style(
		'eelfg-process-grid-style',
		plugins_url( 'build/style-index.css', __FILE__ ),
		array( 'eelfg-public-style' ),
		EELFG_VERSION
	);

	// Editor-only styles (sidebar repeater UI). Compiled from src/editor.scss.
	wp_register_style(
		'eelfg-process-grid-editor-style',
		plugins_url( 'build/index.css', __FILE__ ),
		array( 'eelfg-process-grid-style' ),
		EELFG_VERSION
	);

	register_block_type( __DIR__ . '/build', array(
		'style'        => 'eelfg-process-grid-style',
		'editor_style' => 'eelfg-process-grid-editor-style',
	) );
}
add_action( 'init', 'eelfg_create_block_process_grid_block_init' );
