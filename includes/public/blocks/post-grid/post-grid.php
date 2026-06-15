<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function eelfg_create_block_post_grid_block_init() {
	// Register block-specific styles manually to be sure
	wp_register_style(
		'eelfg-post-grid-style',
		plugins_url( 'build/style-index.css', __FILE__ ),
		array('eelfg-public-style'),
		EELFG_VERSION
	);

	register_block_type( __DIR__ . '/build', array(
		'style'         => 'eelfg-post-grid-style',
		'editor_style'  => 'eelfg-post-grid-style',
	) );
}
add_action( 'init', 'eelfg_create_block_post_grid_block_init' );
