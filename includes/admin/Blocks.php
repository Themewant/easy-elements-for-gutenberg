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
                'description' => 'Animated number counter with prefix/suffix, icon, title and odometer.',
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
            [
                'title'       => 'Countdown',
                'id'          => 'countdown',
                'description' => 'Countdown timer to a target date with custom labels, separators and styling.',
                'iconName'    => 'list.svg',
                'status'      => 'enable',
                "isPro"       => false,
            ],
            [
                'title'       => 'Table',
                'id'          => 'table',
                'description' => 'Data table with header, body and footer cells, icons, images, tooltips and styling.',
                'iconName'    => 'list.svg',
                'status'      => 'enable',
                "isPro"       => false,
            ],
            [
                'title'       => 'Social Share',
                'id'          => 'social-share',
                'description' => 'Social share buttons for the current page with multiple platforms and layouts.',
                'iconName'    => 'social-icons.svg',
                'status'      => 'enable',
                "isPro"       => false,
            ],
            [
                'title'       => 'Social Icon',
                'id'          => 'social-icon',
                'description' => 'A row of linked social icons with per-icon or global colors and hover states.',
                'iconName'    => 'social-icons.svg',
                'status'      => 'enable',
                "isPro"       => false,
            ],
            [
                'title'       => 'Progress Bar',
                'id'          => 'progress',
                'description' => 'A progress / skill bar with title and percent, in two layout styles.',
                'iconName'    => 'list.svg',
                'status'      => 'enable',
                "isPro"       => false,
            ],
            [
                'title'       => 'Process Grid',
                'id'          => 'process-grid',
                'description' => 'A grid of process / service boxes with icon, title, description.',
                'iconName'    => 'grid.svg',
                'status'      => 'enable',
                "isPro"       => false,
            ],
            [
                'title'       => 'Process List',
                'id'          => 'process-list',
                'description' => 'A single process / step row with number, icon, title and description.',
                'iconName'    => 'list.svg',
                'status'      => 'enable',
                "isPro"       => false,
            ],
            [
                'title'       => 'Client Logo Grid',
                'id'          => 'clients-logo-grid',
                'description' => 'A responsive grid of client / partner logos with links and effects.',
                'iconName'    => 'grid.svg',
                'status'      => 'enable',
                "isPro"       => false,
            ],
            [
                'title'       => 'Image Comparison',
                'id'          => 'image-comparison',
                'description' => 'A before / after image comparison slider with a draggable handle.',
                'iconName'    => 'grid.svg',
                'status'      => 'enable',
                "isPro"       => false,
            ],
            [
                'title'       => 'Service List',
                'id'          => 'service-list',
                'description' => 'A service item with icon/image/number, title, description and — three skins.',
                'iconName'    => 'list.svg',
                'status'      => 'enable',
                "isPro"       => false,
            ],
            [
                'title'       => 'Scroll Top',
                'id'          => 'scroll-to-top',
                'description' => 'A floating scroll-to-top button that appears after scrolling.',
                'iconName'    => 'button.svg',
                'status'      => 'enable',
                "isPro"       => false,
            ],
            [
                'title'       => 'Offcanvas',
                'id'          => 'offcanvas',
                'description' => 'A toggle button that opens an off-canvas panel rendering a selected template.',
                'iconName'    => 'button.svg',
                'status'      => 'enable',
                "isPro"       => false,
            ],
            [
                'title'       => 'Search',
                'id'          => 'search',
                'description' => 'Site search with a popup lightbox skin or an inline search field skin.',
                'iconName'    => 'button.svg',
                'status'      => 'enable',
                "isPro"       => false,
            ],
            [
                'title'       => 'Login | Register',
                'id'          => 'login-register',
                'description' => 'AJAX login and registration forms with custom fields, captcha and styling.',
                'iconName'    => 'button.svg',
                'status'      => 'enable',
                "isPro"       => false,
            ],
            [
                'title'       => 'Breadcrumb',
                'id'          => 'breadcrumb',
                'description' => 'A dynamic breadcrumb trail for the current page.',
                'iconName'    => 'list.svg',
                'status'      => 'enable',
                "isPro"       => false,
            ],
        ];

        // Default-enabled IDs: any block we want available without the user toggling it on first.
        $default_enabled = [ 'layout-row', 'column', 'post-grid', 'gallery', 'faq', 'pricing-table', 'button', 'icon-box', 'heading', 'team-grid', 'testimonials-grid', 'category-list', 'feature-list', 'counter', 'tab', 'countdown', 'table', 'social-share', 'social-icon', 'progress', 'process-grid', 'process-list', 'clients-logo-grid', 'image-comparison', 'service-list', 'scroll-to-top', 'offcanvas', 'search', 'login-register', 'breadcrumb' ];

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