<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function eelfg_create_block_pricing_table_block_init() {
	// Register the block-specific style handle so render.php can attach the
	// per-instance inline CSS to it (matches the post-grid / gallery pattern).
	wp_register_style(
		'eelfg-pricing-table-style',
		plugins_url( 'build/style-index.css', __FILE__ ),
		array( 'eelfg-public-style' ),
		EELFG_VERSION
	);

	// Editor-only styles (compiled from src/editor.scss).
	wp_register_style(
		'eelfg-pricing-table-editor-style',
		plugins_url( 'build/index.css', __FILE__ ),
		array( 'eelfg-pricing-table-style' ),
		EELFG_VERSION
	);

	register_block_type( __DIR__ . '/build', array(
		'style'        => 'eelfg-pricing-table-style',
		'editor_style' => 'eelfg-pricing-table-editor-style',
	) );
}
add_action( 'init', 'eelfg_create_block_pricing_table_block_init' );
