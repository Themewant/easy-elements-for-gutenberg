<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function eelfg_create_block_faq_block_init() {
	// Register the block-specific style handle so render.php can attach the
	// per-instance inline CSS to it (matches the post-grid / gallery pattern).
	wp_register_style(
		'eelfg-faq-style',
		plugins_url( 'build/style-index.css', __FILE__ ),
		array( 'eelfg-public-style' ),
		EELFG_VERSION
	);

	// Editor-only styles (inline editing UI: accordion toggle, item controls).
	// Compiled from src/editor.scss. Without this the editor markup is unstyled.
	wp_register_style(
		'eelfg-faq-editor-style',
		plugins_url( 'build/index.css', __FILE__ ),
		array( 'eelfg-faq-style' ),
		EELFG_VERSION
	);

	register_block_type( __DIR__ . '/build', array(
		'style'        => 'eelfg-faq-style',
		'editor_style' => 'eelfg-faq-editor-style',
	) );
}
add_action( 'init', 'eelfg_create_block_faq_block_init' );
