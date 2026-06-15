<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function eelfg_create_block_layout_row_block_init() {
    wp_register_style(
        'eelfg-layout-row-style',
        plugins_url( 'build/style-index.css', __FILE__ ),
        array( 'eelfg-public-style' ),
        EELFG_VERSION
    );

    register_block_type( __DIR__ . '/build', array(
        'style'        => 'eelfg-layout-row-style',
        'editor_style' => 'eelfg-layout-row-style',
    ) );
}
add_action( 'init', 'eelfg_create_block_layout_row_block_init' );
