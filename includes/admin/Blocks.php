<?php
namespace EELFG\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class Blocks {
    public static function instance() {
        static $instance = null;
        if ( null === $instance ) {
            $instance = new self();
        }
        return $instance;
    }

    public function __construct() {
        add_action( 'init', array( $this, 'register_blocks' ) );
    }

    public function get_blocks() {
        $blocks = [
            [
                'title'       => 'Post Grid',
                'id'        => 'post-grid',
                'description' => 'Post Grid Block',
                'iconName'        => 'grid.svg',
                'status'      => 'enable',
                "isPro"       => false,
            ],
             [
                'title'       => 'Row',
                'id'          => 'layout-row',
                'description' => 'Flexible row/section container holding columns.',
                'iconName'    => 'grid.svg',
                'status'      => 'enable',
                "isPro"       => false,
            ],
            [
                'title'       => 'Column',
                'id'          => 'column',
                'description' => 'Child column inside a eelfgst Row.',
                'iconName'    => 'grid.svg',
                'status'      => 'enable',
                "isPro"       => false,
            ],
            [
                'title'       => 'Simple Gallery',
                'id'          => 'gallery',
                'description' => 'Responsive image gallery with lightbox.',
                'iconName'    => 'grid.svg',
                'status'      => 'enable',
                "isPro"       => false,
            ],
            [
                'title'       => 'Accordion',
                'id'          => 'faq',
                'description' => 'FAQ accordion with collapsible icons and schema.',
                'iconName'    => 'list.svg',
                'status'      => 'enable',
                "isPro"       => false,
            ],
            [
                'title'       => 'Pricing Table',
                'id'          => 'pricing-table',
                'description' => 'Configurable pricing table with features, featured ribbon and CTA button.',
                'iconName'    => 'list.svg',
                'status'      => 'enable',
                "isPro"       => false,
            ],
        ];

        // Default-enabled IDs: any block we want available without the user toggling it on first.
        $default_enabled = [ 'layout-row', 'column', 'post-grid', 'gallery', 'faq', 'pricing-table' ];

        // Merge status from DB
        foreach ($blocks as &$block) {
            $default = in_array( $block['id'], $default_enabled, true ) ? 'enable' : 'disable';
            $block['status'] = get_option('eelfg_block_' . $block['id'], $default);
        }
        unset($block);

        $blocks = apply_filters('eelfg_blocks', $blocks);

        return $blocks;
    }

    public function register_blocks() {
    }
}   