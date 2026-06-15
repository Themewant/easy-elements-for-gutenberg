<?php
namespace EELFG\Extension;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( '\EELFG\Extension\Video_Post' ) ) {

	class Video_Post {

		public function __construct() {

			// Meta box
			add_action( 'add_meta_boxes', [ $this, 'register_meta_box' ] );
			add_action( 'save_post', [ $this, 'save_featured_meta' ] );
		}

		/* ================= Meta Box ================= */

		public function register_meta_box() {
			add_meta_box(
				'eelfg_video_post',
				'Video Url',
				[ $this, 'render_meta_box' ],
				'post',
				'side'
			);
		}

		public function render_meta_box( $post ) {
			wp_nonce_field( 'eelfg_video_nonce', 'eelfg_video_nonce' );
			$video_url = get_post_meta( $post->ID, '_video_url', true );
			?>
			<label>
				<input type="text" name="_video_url" value="<?php echo esc_attr( $video_url ); ?>" />
			</label>
			<?php
		}

		public function save_featured_meta( $post_id ) {

			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			// Verify nonce
			if ( ! isset( $_POST['eelfg_video_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['eelfg_video_nonce'] ) ), 'eelfg_video_nonce' ) ) {
				return;
			}

			if ( isset( $_POST['_video_url'] ) ) {
				$video_url = sanitize_text_field( wp_unslash( $_POST['_video_url'] ) );
				update_post_meta( $post_id, '_video_url', $video_url );
			} else {
				update_post_meta( $post_id, '_video_url', '' );
			}
		}
	}
}

