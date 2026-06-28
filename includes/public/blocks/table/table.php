<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function eelfg_create_block_table_block_init() {
	// Front-end + shared style handle so render.php can attach per-instance inline CSS.
	wp_register_style(
		'eelfg-table-style',
		plugins_url( 'build/style-index.css', __FILE__ ),
		array( 'eelfg-public-style' ),
		EELFG_VERSION
	);

	// Editor-only styles (sidebar repeater UI). Compiled from src/editor.scss.
	wp_register_style(
		'eelfg-table-editor-style',
		plugins_url( 'build/index.css', __FILE__ ),
		array( 'eelfg-table-style' ),
		EELFG_VERSION
	);

	register_block_type( __DIR__ . '/build', array(
		'style'        => 'eelfg-table-style',
		'editor_style' => 'eelfg-table-editor-style',
	) );
}
add_action( 'init', 'eelfg_create_block_table_block_init' );
