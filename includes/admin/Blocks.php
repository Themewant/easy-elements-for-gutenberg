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
            [
                'title'       => 'Button',
                'id'          => 'button',
                'description' => 'A flexible button with icon, gradient and full style controls.',
                'iconName'    => 'button.svg',
                'status'      => 'enable',
                "isPro"       => false,
            ],
            [
                'title'       => 'Info Box',
                'id'          => 'icon-box',
                'description' => 'Icon/info box with title, description, features, number and read-more button.',
                'iconName'    => 'info-box.svg',
                'status'      => 'enable',
                "isPro"       => false,
            ],
            [
                'title'       => 'Heading',
                'id'          => 'heading',
                'description' => 'Heading with sub-heading, highlight, separator, gradient text and watermark.',
                'iconName'    => 'heading.svg',
                'status'      => 'enable',
                "isPro"       => false,
            ],
            [
                'title'       => 'Team Member',
                'id'          => 'team-grid',
                'description' => 'Team member card with 5 skins, social icons, contact info and popup.',
                'iconName'    => 'social-icons.svg',
                'status'      => 'enable',
                "isPro"       => false,
            ],
            [
                'title'       => 'Testimonials Grid',
                'id'          => 'testimonials-grid',
                'description' => 'Grid of testimonials with 6 skins, ratings, quote icons and logos.',
                'iconName'    => 'social-icons.svg',
                'status'      => 'enable',
                "isPro"       => false,
            ],
            [
                'title'       => 'Category List',
                'id'          => 'category-list',
                'description' => 'A list or grid of taxonomy terms with icons and post counts.',
                'iconName'    => 'category.svg',
                'status'      => 'enable',
                "isPro"       => false,
            ],
            [
                'title'       => 'Feature List',
                'id'          => 'feature-list',
                'description' => 'A vertical list of features with icon/number/image, title and description.',
                'iconName'    => 'list.svg',
                'status'      => 'enable',
                "isPro"       => false,
            ],
            [
                'title'       => 'Counter',
                'id'          => 'counter',
                'description' => 'Animated number counter with prefix/suffix, icon, title and odometer mode.',
                'iconName'    => 'list.svg',
                'status'      => 'enable',
                "isPro"       => false,
            ],
            [
                'title'       => 'Tabs',
                'id'          => 'tab',
                'description' => 'Tabbed content with icon/image titles, content title, description and button.',
                'iconName'    => 'list.svg',
                'status'      => 'enable',
                "isPro"       => false,
            ],
        ];

        // Default-enabled IDs: any block we want available without the user toggling it on first.
        $default_enabled = [ 'layout-row', 'column', 'post-grid', 'gallery', 'faq', 'pricing-table', 'button', 'icon-box', 'heading', 'team-grid', 'testimonials-grid', 'category-list', 'feature-list', 'counter', 'tab' ];

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