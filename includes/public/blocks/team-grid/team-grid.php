<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function eelfg_create_block_team_grid_block_init() {
	wp_register_style(
		'eelfg-team-grid-style',
		plugins_url( 'build/style-index.css', __FILE__ ),
		array( 'eelfg-public-style' ),
		EELFG_VERSION
	);

	// Editor-only styles (compiled from src/editor.scss).
	wp_register_style(
		'eelfg-team-grid-editor-style',
		plugins_url( 'build/index.css', __FILE__ ),
		array( 'eelfg-team-grid-style' ),
		EELFG_VERSION
	);

	register_block_type( __DIR__ . '/build', array(
		'style'        => 'eelfg-team-grid-style',
		'editor_style' => 'eelfg-team-grid-editor-style',
	) );
}
add_action( 'init', 'eelfg_create_block_team_grid_block_init' );
