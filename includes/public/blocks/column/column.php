<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function eelfg_create_block_column_block_init() {
    wp_register_style(
        'eelfg-column-style',
        plugins_url( 'build/style-index.css', __FILE__ ),
        array( 'eelfg-public-style' ),
        EELFG_VERSION
    );

    register_block_type( __DIR__ . '/build', array(
        'style'        => 'eelfg-column-style',
        'editor_style' => 'eelfg-column-style',
    ) );
}
add_action( 'init', 'eelfg_create_block_column_block_init' );