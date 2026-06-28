<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function eelfg_create_block_process_list_block_init() {
	// Front-end + shared style handle so render.php can attach per-instance inline CSS.
	wp_register_style(
		'eelfg-process-list-style',
		plugins_url( 'build/style-index.css', __FILE__ ),
		array( 'eelfg-public-style' ),
		EELFG_VERSION
	);

	// Editor-only styles. Compiled from src/editor.scss.
	wp_register_style(
		'eelfg-process-list-editor-style',
		plugins_url( 'build/index.css', __FILE__ ),
		array( 'eelfg-process-list-style' ),
		EELFG_VERSION
	);

	register_block_type( __DIR__ . '/build', array(
		'style'        => 'eelfg-process-list-style',
		'editor_style' => 'eelfg-process-list-editor-style',
	) );
}
add_action( 'init', 'eelfg_create_block_process_list_block_init' );
