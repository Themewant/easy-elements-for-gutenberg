<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;

}
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Local template/iteration variables.

add_action( 'enqueue_block_editor_assets', 'eelfg_enqueue_block_styles' );
add_action( 'enqueue_block_assets', 'eelfg_enqueue_block_styles' );
function eelfg_enqueue_block_styles() {
	
    // if swier not existing
	wp_enqueue_style( 'swiper', EELFG_PL_URL . 'assets/lib/swiper/swiper-bundle.min.css', array(), EELFG_VERSION, 'all' ); 
	wp_enqueue_script( 'swiper', EELFG_PL_URL . 'assets/lib/swiper/swiper-bundle.min.js', array(),'12.0.3',false ); 

	// enqueue bootstrap grid
	wp_enqueue_style( 'eelfg-bootstrap-grid', EELFG_PL_URL . 'assets/lib/bootstrap/bootstrap-grid.min.css', array(), EELFG_VERSION, 'all' );

    // register plugin style if not registered	
	if (!wp_style_is('eelfg-public-style', 'registered')) {
		wp_register_style( 
			'eelfg-public-style', 
			EELFG_PL_URL . 'includes/public/assets/css/public.css', 
			array(), 
			EELFG_VERSION 
		);
	}
}

$eelfg_blocks_instance = \EELFG\Admin\Blocks::instance();
$eelfg_blocks = $eelfg_blocks_instance->get_blocks();

foreach ($eelfg_blocks as $eelfg_block) {
	if ($eelfg_block['status'] == 'disable') {
		continue;
	}
	if ($eelfg_block['isPro'] == true) {
		continue;
	}
	$file = __DIR__ . '/' . $eelfg_block['id'] . '/' . $eelfg_block['id'] . '.php';
	if (file_exists($file)) {
		require_once $file;
	}
}
