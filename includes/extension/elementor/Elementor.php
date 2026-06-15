<?php
namespace EELFG\Extension\Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Elementor {
    public static function instance() {
        static $instance = null;
        if ( null === $instance ) {
            $instance = new self();
        }
        return $instance;
    }

    public function __construct() {
        add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
        add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
    }

    public function register_category( $elements_manager ) {
        $elements_manager->add_category(
            'eelfgst',
            array(
                'title' => 'eelfgst',
                'icon'  => 'eicon-posts-grid',
            )
        );
    }

    public function register_widgets( $widgets_manager ) {
        $widgets_manager->register( new \EELFG\Extension\Elementor\Template_Widget() );
    }
}

