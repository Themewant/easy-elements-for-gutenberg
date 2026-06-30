<?php
namespace EELFG\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class Admin {
    public static function instance() {
        static $instance = null;
        if ( null === $instance ) {
            $instance = new self();
        }
        return $instance;
    }

    public function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10, 1 );
        add_action( 'enqueue_block_editor_assets', array( $this, 'editor_overrides' ) );
    }

    public function enqueue_scripts($hook) {
       

        $asset_file = include EELFG_PL_PATH . 'build/index.asset.php';

        $deps = array_map(function($dep) {
            return match($dep) {
                'react', 'react-dom', 'react-jsx-runtime' => 'wp-element',
                'wp-scripts' => 'wp-scripts',
                default => $dep,
            };
        }, $asset_file['dependencies']);

        wp_enqueue_style(
            'eelfg-admin-css',
            EELFG_PL_URL . 'build/style-index.css',
            [],
            $asset_file['version']
        );

        // Load the app on the main page and every Easy Elements submenu page.
        $our_pages = array_keys( \EELFG\Main::get_admin_pages() );
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only admin page detection, no data is processed.
        $current_page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
        if ( ! in_array( $current_page, $our_pages, true ) ) {
            return;
        }

        wp_enqueue_style( 'eelfg-admin-icons', EELFG_PL_URL . 'includes/admin/assets/icons/css/eelfg-icon.css', array(), EELFG_VERSION );

        wp_enqueue_script(
            'eelfg-admin-js',
            EELFG_PL_URL . 'build/index.js',
            $deps,
            $asset_file['version'],
            true
        );

        
        $blocks = \EELFG\Admin\Blocks::instance()->get_blocks();
        $is_pro_installed = class_exists( '\EELFG_LICENSE' );
        $is_pro_active = $is_pro_installed
            ? (bool) \EELFG_LICENSE::instance()->is_license_active()
            : false;

        $template_count = wp_count_posts( 'eelfg-template' );
        $total_templates = isset( $template_count->publish ) ? (int) $template_count->publish : 0;

        wp_localize_script( 'eelfg-admin-js', 'eelfg', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'siteUrl' => site_url(),
            'rest_url' => esc_url_raw(rest_url('easy-elements-for-gutenberg/v1/')),
            'nonce' => wp_create_nonce('wp_rest'),
            'blocks' => $blocks,
            'eelfgUrl' => EELFG_PL_URL,
            'eelfgPath' => EELFG_PL_PATH,
            'isProActive' => $is_pro_active,
            'isProInstalled' => $is_pro_installed,
            'templateCount' => $total_templates,
            'templateLimit' => $is_pro_active ? -1 : 3,
            'proUrl' => 'https://themewant.com/plugins/easy-elements-for-gutenberg/pricing',
            'colors' => \EELFG\Admin\Api::get_saved_colors(),
            'colorDefaults' => \EELFG\Admin\Api::get_color_defaults(),
            'layout' => \EELFG\Admin\Api::get_saved_layout(),
            'layoutDefaults' => \EELFG\Admin\Api::get_layout_defaults(),
            'builderTypes' => array_values( \EELFG\Extension\ThemeBuilder\Theme_Builder::get_template_types() ),
            'builderRules' => \EELFG\Extension\ThemeBuilder\Builder_Conditions::get_rules(),
            'license' => array(
                'key'    => (string) get_option( 'eelfg_license_key', '' ),
                'status' => (string) get_option( 'eelfg_license_status', '' ),
            ),
        ) );
    }

    public function editor_overrides() {
        $screen = get_current_screen();
        if ( ! $screen || ! in_array( $screen->post_type, array( 'eelfg-template', 'eelfg-builder' ), true ) ) {
            return;
        }

        // Send the editor "close" button back to the relevant dashboard submenu.
        $page = $screen->post_type === 'eelfg-builder' ? 'eelfg-theme-builder' : 'eelfg-templates';
        $template_page_url = admin_url( 'admin.php?page=' . $page );

        wp_add_inline_script( 'wp-edit-post', "
            (function() {
                var url = " . wp_json_encode( $template_page_url ) . ";
                function updateLink() {
                    var link = document.querySelector('a.edit-post-fullscreen-mode-close');
                    if (link && link.getAttribute('href') !== url) {
                        link.setAttribute('href', url);
                    }
                }
                var observer = new MutationObserver(updateLink);
                observer.observe(document.body, { childList: true, subtree: true });
                updateLink();
            })();
        " );
    }

}

