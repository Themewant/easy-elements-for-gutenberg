<?php
namespace EELFG\Extension;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class Category {
    public static function instance() {
        static $instance = null;
        if ( null === $instance ) {
            $instance = new self();
        }
        return $instance;
    }

    public function __construct() {
        add_action( 'category_edit_form_fields', array( $this, 'category_image_color_field' ) );
        add_action( 'category_add_form_fields', array( $this, 'category_image_color_field' ) );
        add_action( 'edited_category', array( $this, 'save_category_image_color' ) );
        add_action( 'created_category', array( $this, 'save_category_image_color' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'category_image_color_enqueue_scripts' ) );
        add_action( 'admin_footer', array( $this, 'category_image_color_inline_script' ) );
    }
    
    function category_image_color_enqueue_scripts( $hook ) {
        if ( $hook !== 'edit-tags.php' && $hook !== 'term.php' ) {
            return;
        }
        wp_enqueue_media();
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
    }

    function category_image_color_inline_script() {
        $screen = get_current_screen();
        if ( ! $screen || $screen->taxonomy !== 'category' ) {
            return;
        }
        ?>
        <script>
            jQuery(document).ready(function($) {
                var mediaUploader;

                $('.category-color-picker').wpColorPicker();

                $('.category_image_button').click(function(e) {
                    e.preventDefault();

                    if (mediaUploader) {
                        mediaUploader.open();
                        return;
                    }

                    mediaUploader = wp.media.frames.file_frame = wp.media({
                        title: '<?php esc_html_e( "Choose Image", "easy-elements-for-gutenberg" ); ?>',
                        button: {
                            text: '<?php esc_html_e( "Choose Image", "easy-elements-for-gutenberg" ); ?>'
                        },
                        multiple: false
                    });

                    mediaUploader.on('select', function() {
                        var attachment = mediaUploader.state().get('selection').first().toJSON();
                        $('#category_image').val(attachment.id);
                        $('#category_image_preview').html('<img src="' + attachment.url + '" style="max-width:100px;"/>');
                    });

                    mediaUploader.open();
                });

                $('.category_image_remove_button').click(function(e) {
                    e.preventDefault();
                    $('#category_image').val('');
                    $('#category_image_preview').html('');
                });
            });
        </script>
        <?php
    }

    function category_image_color_field( $term ) {
        $category_image_id = '';
        $category_color = '';

        // Check if $term is an object before accessing properties
        if ( is_object( $term ) ) {
            $category_image_id = get_term_meta( $term->term_id, 'category_image', true );
            $category_color = get_term_meta( $term->term_id, 'category_color', true );
        }

        $category_image_url = '';
        if ( $category_image_id ) {
            $category_image_url = wp_get_attachment_image_url( $category_image_id, 'thumbnail' );
        }
        ?>
        <tr class="form-field">
            <th scope="row" valign="top">
                <label for="category_image"><?php esc_html_e( 'Image', 'easy-elements-for-gutenberg' ); ?></label>
            </th>
            <td>
                <div id="category_image_preview">
                    <?php if ( $category_image_url ) : ?>
                        <img src="<?php echo esc_url( $category_image_url ); ?>" style="max-width: 100px;" />
                    <?php endif; ?>
                </div>
                <input type="hidden" id="category_image" name="category_image" value="<?php echo esc_attr( $category_image_id ); ?>" />
                <button type="button" class="button category_image_button"><?php esc_html_e( 'Upload Image', 'easy-elements-for-gutenberg' ); ?></button>
                <button type="button" class="button category_image_remove_button"><?php esc_html_e( 'Remove Image', 'easy-elements-for-gutenberg' ); ?></button>
            </td>
        </tr>

        <tr class="form-field">
            <th scope="row" valign="top">
                <label for="category_color"><?php esc_html_e( 'Color', 'easy-elements-for-gutenberg' ); ?></label>
            </th>
            <td>
                <input type="text" id="category_color" name="category_color" class="category-color-picker" value="<?php echo esc_attr( $category_color ); ?>" />
            </td>
        </tr>
        <?php
    }

    function save_category_image_color( $term_id ) {
        if ( ! current_user_can( 'manage_categories' ) ) {
            return;
        }

        $nonce = isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '';
        if ( ! wp_verify_nonce( $nonce, 'update-tag_' . $term_id ) && ! wp_verify_nonce( $nonce, 'add-tag' ) ) {
            return;
        }

        if ( isset( $_POST['category_image'] ) ) {
            update_term_meta( $term_id, 'category_image', sanitize_text_field( wp_unslash( $_POST['category_image'] ) ) );
        }
        if ( isset( $_POST['category_color'] ) ) {
            update_term_meta( $term_id, 'category_color', sanitize_hex_color( wp_unslash( $_POST['category_color'] ) ) );
        }
    }
}
