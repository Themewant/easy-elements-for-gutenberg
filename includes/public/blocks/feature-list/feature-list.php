<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function eelfg_create_block_feature_list_block_init() {
	wp_register_style(
		'eelfg-feature-list-style',
		plugins_url( 'build/style-index.css', __FILE__ ),
		array( 'eelfg-public-style' ),
		EELFG_VERSION
	);

	wp_register_style(
		'eelfg-feature-list-editor-style',
		plugins_url( 'build/index.css', __FILE__ ),
		array( 'eelfg-feature-list-style' ),
		EELFG_VERSION
	);

	register_block_type( __DIR__ . '/build', array(
		'style'        => 'eelfg-feature-list-style',
		'editor_style' => 'eelfg-feature-list-editor-style',
	) );
}
add_action( 'init', 'eelfg_create_block_feature_list_block_init' );
