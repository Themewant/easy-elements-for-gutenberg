<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function eelfg_create_block_image_comparison_block_init() {
	// Front-end + shared style handle so render.php can attach per-instance inline CSS.
	wp_register_style(
		'eelfg-image-comparison-style',
		plugins_url( 'build/style-index.css', __FILE__ ),
		array( 'eelfg-public-style' ),
		EELFG_VERSION
	);

	// Editor-only styles. Compiled from src/editor.scss.
	wp_register_style(
		'eelfg-image-comparison-editor-style',
		plugins_url( 'build/index.css', __FILE__ ),
		array( 'eelfg-image-comparison-style' ),
		EELFG_VERSION
	);

	register_block_type( __DIR__ . '/build', array(
		'style'        => 'eelfg-image-comparison-style',
		'editor_style' => 'eelfg-image-comparison-editor-style',
	) );
}
add_action( 'init', 'eelfg_create_block_image_comparison_block_init' );
