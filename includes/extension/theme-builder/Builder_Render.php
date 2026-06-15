<?php
namespace EELFG\Extension\ThemeBuilder;

/**
 * Theme Builder — frontend rendering.
 *
 * Replaces the active (classic) theme's header and footer with the matching
 * builder template. The replacement leans on a WordPress core detail: both
 * get_header() and get_footer() fire their action and then call
 * locate_template( [...], true, $load_once = true ) — i.e. require_once. So if
 * we render our own markup inside the hook and then pre-require the theme's
 * own header.php / footer.php into a discarded buffer, core's subsequent
 * require_once becomes a no-op and the theme part never visibly renders.
 *
 * Resolution is location-agnostic and reads from the template-type registry,
 * so wiring a future type into get_header-like output is a small addition.
 *
 * @package EasyElementsForGutenberg
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Builder_Render {

	/**
	 * Resolved post id per location for the current request. null = not resolved
	 * yet, false = resolved to "nothing matches".
	 *
	 * @var array<string,int|false|null>
	 */
	private $resolved = array();

	/**
	 * Pre-rendered HTML per location. We render in the get_header/get_footer hook
	 * (before wp_head() runs) so block styles enqueue in time for the document
	 * <head>, then the template wrapper just echoes the cached markup.
	 *
	 * @var array<string,string>
	 */
	private $output = array();

	public static function instance() {
		static $instance = null;
		if ( null === $instance ) {
			$instance = new self();
		}
		return $instance;
	}

	public function __construct() {
		// Classic themes: take over get_header() / get_footer().
		add_action( 'get_header', array( $this, 'maybe_override_header' ) );
		add_action( 'get_footer', array( $this, 'maybe_override_footer' ) );

		// Block (FSE) themes: replace the header/footer template-part blocks.
		add_filter( 'pre_render_block', array( $this, 'maybe_replace_template_part' ), 10, 2 );

		add_shortcode( 'eelfg_builder', array( $this, 'shortcode' ) );
	}

	/**
	 * Block-theme replacement: swap the matching builder template in for the
	 * theme's `core/template-part` header/footer.
	 *
	 * Block themes render header/footer as template parts (area/slug =
	 * header|footer) rather than calling get_header()/get_footer(). Returning a
	 * non-null value from pre_render_block short-circuits the part's render.
	 *
	 * @param string|null $pre   Pre-rendered content, or null to render normally.
	 * @param array       $block Parsed block.
	 * @return string|null
	 */
	public function maybe_replace_template_part( $pre, $block ) {
		// Leave the editor / REST previews showing the real theme parts.
		if ( is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
			return $pre;
		}

		if ( empty( $block['blockName'] ) || 'core/template-part' !== $block['blockName'] ) {
			return $pre;
		}

		$attrs = isset( $block['attrs'] ) ? $block['attrs'] : array();
		$area  = isset( $attrs['area'] ) ? $attrs['area'] : '';
		$slug  = isset( $attrs['slug'] ) ? $attrs['slug'] : '';

		$type = '';
		if ( 'header' === $area || 'header' === $slug ) {
			$type = 'header';
		} elseif ( 'footer' === $area || 'footer' === $slug ) {
			$type = 'footer';
		}

		if ( ! $type || ! \EELFG\Extension\ThemeBuilder\Theme_Builder::is_valid_type( $type ) ) {
			return $pre;
		}

		$post_id = $this->get_location_post( $type );
		if ( ! $post_id ) {
			return $pre;
		}

		return $this->render_post( $post_id, $type );
	}

	/**
	 * Resolve the best-matching builder post id for a location, cached per request.
	 *
	 * @param string $type Template type slug (e.g. header, footer).
	 * @return int|false
	 */
	public function get_location_post( $type ) {
		if ( array_key_exists( $type, $this->resolved ) && null !== $this->resolved[ $type ] ) {
			return $this->resolved[ $type ];
		}

		$this->resolved[ $type ] = $this->find_matching_post( $type );
		return $this->resolved[ $type ];
	}

	/**
	 * Find the most recently modified published builder post of $type whose
	 * display conditions match the current request.
	 *
	 * @return int|false
	 */
	private function find_matching_post( $type ) {
		if ( ! \EELFG\Extension\ThemeBuilder\Theme_Builder::is_valid_type( $type ) ) {
			return false;
		}

		$query = new \WP_Query(
			array(
				'post_type'      => \EELFG\Extension\ThemeBuilder\Theme_Builder::POST_TYPE,
				'post_status'    => 'publish',
				'posts_per_page' => 100,
				'orderby'        => 'modified',
				'order'          => 'DESC',
				'no_found_rows'  => true,
				'fields'         => 'ids',
				// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Filtering a small template post type by its type meta to resolve the active template.
				'meta_query'     => array(
					array(
						'key'   => \EELFG\Extension\ThemeBuilder\Theme_Builder::META_TYPE,
						'value' => $type,
					),
				),
			)
		);

		foreach ( $query->posts as $post_id ) {
			$conditions = \EELFG\Extension\ThemeBuilder\Theme_Builder::get_post_conditions( $post_id );
			if ( \EELFG\Extension\ThemeBuilder\Builder_Conditions::matches_current_request( $conditions ) ) {
				return (int) $post_id;
			}
		}

		return false;
	}

	public function maybe_override_header() {
		if ( is_admin() || ! $this->get_location_post( 'header' ) ) {
			return;
		}

		// Render now (before wp_head) so block styles enqueue for the <head>.
		$this->output['header'] = $this->render_location( 'header' );

		require EELFG_PL_PATH . 'includes/theme-builder/templates/header.php';

		// Swallow the theme's own header.php so core's require_once no-ops it.
		ob_start();
		locate_template( array( 'header.php' ), true, true );
		ob_get_clean();
	}

	public function maybe_override_footer() {
		if ( is_admin() || ! $this->get_location_post( 'footer' ) ) {
			return;
		}

		$this->output['footer'] = $this->render_location( 'footer' );

		require EELFG_PL_PATH . 'includes/theme-builder/templates/footer.php';

		ob_start();
		locate_template( array( 'footer.php' ), true, true );
		ob_get_clean();
	}

	/**
	 * Return the pre-rendered HTML for a location (used by the template wrappers).
	 */
	public function get_output( $type ) {
		return isset( $this->output[ $type ] ) ? $this->output[ $type ] : '';
	}

	/**
	 * Render the matching builder template for a location, wrapped for styling.
	 *
	 * @param string $type Template type slug.
	 */
	public function render_location( $type ) {
		$post_id = $this->get_location_post( $type );
		if ( ! $post_id ) {
			return '';
		}

		return $this->render_post( $post_id, $type );
	}

	/**
	 * Render a builder post's stored block content for the frontend.
	 *
	 * @param int    $post_id Builder post id.
	 * @param string $type    Location type, used for the wrapper class/attr.
	 */
	public function render_post( $post_id, $type = '' ) {
		$post = get_post( $post_id );
		if ( ! $post || \EELFG\Extension\ThemeBuilder\Theme_Builder::POST_TYPE !== $post->post_type ) {
			return '';
		}

		$content = $post->post_content;
		$content = do_blocks( $content );
		$content = do_shortcode( $content );

		$type = $type ? $type : \EELFG\Extension\ThemeBuilder\Theme_Builder::get_post_type_slug( $post_id );

		$wrapper_class = 'eelfg-builder-location eelfg-builder-' . sanitize_html_class( $type );

		return sprintf(
			'<div class="%1$s" data-eelfg-builder-type="%2$s" data-eelfg-builder-id="%3$d">%4$s</div>',
			esc_attr( $wrapper_class ),
			esc_attr( $type ),
			(int) $post_id,
			$content
		);
	}

	/**
	 * [eelfg_builder id="123"] — render a builder template anywhere.
	 */
	public function shortcode( $atts ) {
		$atts = shortcode_atts( array( 'id' => 0 ), $atts, 'eelfg_builder' );
		$id   = (int) $atts['id'];
		if ( ! $id ) {
			return '';
		}
		return $this->render_post( $id );
	}
}
