<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function eelfg_create_block_gallery_block_init() {
	// Register the block-specific style handle so render.php can attach the
	// per-instance inline CSS to it (matches the post-grid pattern).
	wp_register_style(
		'eelfg-gallery-style',
		plugins_url( 'build/style-index.css', __FILE__ ),
		array( 'eelfg-public-style' ),
		EELFG_VERSION
	);

	register_block_type( __DIR__ . '/build', array(
		'style'        => 'eelfg-gallery-style',
		'editor_style' => 'eelfg-gallery-style',
	) );
}
add_action( 'init', 'eelfg_create_block_gallery_block_init' );
