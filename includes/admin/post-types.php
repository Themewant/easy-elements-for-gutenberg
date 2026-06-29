<?php
namespace EELFG\Admin;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EELFG_Post_Types {
    public static function instance() {
        static $instance = null;
        if ( null === $instance ) {
            $instance = new self();
        }
        return $instance;
    }

    public function __construct() {
        add_action( 'init', array( $this, 'register_post_types' ) );
        add_shortcode( 'eelfg_template', array( $this, 'render_shortcode' ) );
    }

    public function register_post_types() {
        $labels = array(
            'name'                  => 'Templates',
            'singular_name'         => 'Template',
            'menu_name'             => 'Templates',
            'name_admin_bar'        => 'Template',
            'add_new'               => 'Add New',
            'add_new_item'          => 'Add New Template',
            'new_item'              => 'New Template',
            'edit_item'             => 'Edit Template',
            'view_item'             => 'View Template',
            'all_items'             => 'All Templates',
            'search_items'          => 'Search Templates',
            'not_found'             => 'No templates found.',
            'not_found_in_trash'    => 'No templates found in Trash.',
        );

        $args = array(
            'labels'             => $labels,
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => false,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'eelfg-template' ),
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'supports'           => array( 'title', 'editor', 'author' ),
            'show_in_rest'       => true,
            'rest_base'          => 'eelfg-templates',
        );

        register_post_type( 'eelfg-template', $args );
    }
    public function render_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'id' => 0,
        ), $atts, 'eelfg_template' );

        $id = absint( $atts['id'] );
        if ( ! $id ) {
            return '';
        }

        $post = get_post( $id );
        if ( ! $post || $post->post_type !== 'eelfg-template' || $post->post_status !== 'publish' ) {
            return '';
        }

        // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Applying WP core filter to render shortcodes/blocks in template content.
        return '<div class="eelfg-template-content">' . apply_filters( 'the_content', $post->post_content ) . '</div>';
    }
}

EELFG_Post_Types::instance();
