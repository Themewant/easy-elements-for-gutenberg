<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Local template/iteration variables.

/**
 * Server-side render for the Client Logo Grid block.
 *
 * Mirrors the markup of the Elementor "Client Logo Grid" widget
 * (easy-elements/widgets/clients-logo-grid). Element classes use the "eelfg-" prefix.
 *
 * $attributes, $content and $block are provided by register_block_type().
 */

$H = '\EELFG\Frontend\Helper';

$unique_id = ! empty( $attributes['blockId'] ) ? $attributes['blockId'] : 'eelfg-clg-' . substr( md5( wp_json_encode( $attributes ) ), 0, 6 );

$logos = isset( $attributes['logos'] ) && is_array( $attributes['logos'] ) ? $attributes['logos'] : [];
$swap  = ! empty( $attributes['hoverSwap'] );
$fetch = isset( $attributes['fetchpriority'] ) ? $attributes['fetchpriority'] : '';
$gray  = ! empty( $attributes['grayscale'] ) ? ( $attributes['grayscaleOption'] ?? 'normal_grayscale' ) : '';

$gray_class_map = [
	'normal_grayscale' => 'eelfg-normal-grayscale',
	'hover_grayscale'  => 'eelfg-hover-grayscale',
	'hover_to_default' => 'eelfg-hover-to-default',
];
$gray_class = ( '' !== $gray && isset( $gray_class_map[ $gray ] ) ) ? $gray_class_map[ $gray ] : '';

$block_wrap_attr = get_block_wrapper_attributes( array( 'class' => 'eelfg-block eelfg-clients-logo-wrap ' . $unique_id ) );
if ( empty( $block_wrap_attr ) ) {
	$block_wrap_attr = 'class="eelfg-block eelfg-clients-logo-wrap ' . esc_attr( $unique_id ) . '"';
}

if ( empty( $logos ) ) {
	echo '<div ' . wp_kses_post( $block_wrap_attr ) . '><p>' . esc_html__( 'Please add client logos.', 'easy-elements-for-gutenberg' ) . '</p></div>';
	return;
}

// ---------------------------------------------------------------------------
// Inline styles (scoped to this instance).
// ---------------------------------------------------------------------------
$selector     = '.eelfg-clients-logo-wrap.' . $unique_id;
$style_handle = 'eelfg-clients-logo-grid-style';

$dims = function ( $obj, $type ) use ( $H ) {
	$out = [];
	if ( empty( $obj ) || ! is_array( $obj ) ) return $out;
	$map = 'padding' === $type
		? [ 'top' => 'padding-top', 'right' => 'padding-right', 'bottom' => 'padding-bottom', 'left' => 'padding-left' ]
		: [ 'top' => 'border-top-left-radius', 'right' => 'border-top-right-radius', 'bottom' => 'border-bottom-right-radius', 'left' => 'border-bottom-left-radius' ];
	foreach ( $map as $side => $css_prop ) {
		if ( isset( $obj[ $side ] ) && '' !== $obj[ $side ] ) $out[ $css_prop ] = $H::ensure_unit( $obj[ $side ] );
	}
	return $out;
};
$shadow = function ( $obj ) use ( $H ) {
	if ( empty( $obj ) || ! is_array( $obj ) ) return [];
	$x = (int) ( $obj['x'] ?? 0 ); $y = (int) ( $obj['y'] ?? 0 ); $b = (int) ( $obj['b'] ?? 0 ); $s = (int) ( $obj['s'] ?? 0 );
	$c = $obj['c'] ?? '';
	$transparent = in_array( str_replace( ' ', '', (string) $c ), [ '', 'rgba(0,0,0,0)' ], true );
	if ( 0 === $x && 0 === $y && 0 === $b && 0 === $s && $transparent ) return [];
	return [ 'box-shadow' => $H::box_shadow_to_css( $obj ) ];
};
$u = function ( $key ) use ( $attributes, $H ) {
	return ( isset( $attributes[ $key ] ) && '' !== $attributes[ $key ] ) ? $H::ensure_unit( $attributes[ $key ] ) : '';
};

// Item space + responsive columns.
$grid_item = $dims( $attributes['itemSpace'] ?? [], 'padding' );
$cols_resp = [ 'desktop' => [], 'tablet' => [], 'mobile' => [] ];
$c_d = isset( $attributes['columns'] ) ? (int) $attributes['columns'] : 4;
$c_t = isset( $attributes['columnsTablet'] ) ? (int) $attributes['columnsTablet'] : 3;
$c_m = isset( $attributes['columnsMobile'] ) ? (int) $attributes['columnsMobile'] : 2;
if ( $c_d > 0 ) $cols_resp['desktop']['width'] = 'calc(100% / ' . $c_d . ')';
if ( $c_t > 0 ) $cols_resp['tablet']['width']  = 'calc(100% / ' . $c_t . ')';
if ( $c_m > 0 ) $cols_resp['mobile']['width']  = 'calc(100% / ' . $c_m . ')';

// Logo box (normal).
$logo_box = [];
if ( '' !== $u( 'itemWidth' ) ) $logo_box['max-width'] = $u( 'itemWidth' );
if ( '' !== $u( 'itemHeight' ) ) $logo_box['height'] = $u( 'itemHeight' );
$logo_box = array_merge( $logo_box, $dims( $attributes['itemPadding'] ?? [], 'padding' ), $dims( $attributes['itemRadius'] ?? [], 'radius' ), $shadow( $attributes['itemBoxShadow'] ?? [] ) );
if ( ! empty( $attributes['itemBg'] ) ) $logo_box['background-color'] = $attributes['itemBg'];
if ( ! empty( $attributes['itemBorder'] ) ) $logo_box = array_merge( $logo_box, $H::border_to_css_props( $attributes['itemBorder'] ) );
if ( '' !== $u( 'transition' ) ) $logo_box['transition'] = 'all ' . ( (float) $attributes['transition'] ) . 's ease';

// Logo box (hover).
$logo_hover = $shadow( $attributes['itemHoverBoxShadow'] ?? [] );
if ( ! empty( $attributes['itemHoverBg'] ) ) $logo_hover['background-color'] = $attributes['itemHoverBg'];
if ( ! empty( $attributes['itemHoverBorder'] ) ) $logo_hover = array_merge( $logo_hover, $H::border_to_css_props( $attributes['itemHoverBorder'] ) );

// Image.
$img = [];
if ( '' !== $u( 'imageWidth' ) ) $img['width'] = $u( 'imageWidth' );
if ( '' !== $u( 'itemHeight' ) ) $img['height'] = '100%';
if ( '' !== $u( 'transition' ) ) $img['transition'] = 'all ' . ( (float) $attributes['transition'] ) . 's ease';

$img_inner = [];
if ( '' !== ( $attributes['itemOpacity'] ?? '' ) ) $img_inner['opacity'] = (float) $attributes['itemOpacity'];
if ( ! $swap && '' !== ( $attributes['itemScale'] ?? '' ) ) $img_inner['transform'] = 'scale(' . (float) $attributes['itemScale'] . ')';

$img_inner_hover = [];
if ( '' !== ( $attributes['itemHoverOpacity'] ?? '' ) ) $img_inner_hover['opacity'] = (float) $attributes['itemHoverOpacity'];
if ( ! $swap && '' !== ( $attributes['itemHoverScale'] ?? '' ) ) $img_inner_hover['transform'] = 'scale(' . (float) $attributes['itemHoverScale'] . ')';

$responsive_css = $H::generate_responsive_css( $selector . ' .eelfg-grid-item', $cols_resp );

wp_enqueue_style( $style_handle );
$H::add_custom_style( $style_handle, $selector, $responsive_css, [
	'.eelfg-grid-item'            => $H::get_inline_styles( $grid_item ),
	'.eelfg-logo-img'             => $H::get_inline_styles( $logo_box ),
	'.eelfg-logo-img:hover'       => $H::get_inline_styles( $logo_hover ),
	'.eelfg-grid-img'             => $H::get_inline_styles( $img ),
	'.eelfg-logo-img img'         => $H::get_inline_styles( $img_inner ),
	'.eelfg-logo-img:hover img'   => $H::get_inline_styles( $img_inner_hover ),
] );
?>
<div <?php echo wp_kses_post( $block_wrap_attr ); ?>>
	<div class="eelfg-clients-logo eelfg-grid-layout">
		<div class="eelfg-grid-wrap">
			<?php
			foreach ( $logos as $item ) :
				$image = isset( $item['image'] ) && is_array( $item['image'] ) ? $item['image'] : [];
				$url   = ! empty( $image['url'] ) ? $image['url'] : '';
				$alt   = ! empty( $image['alt'] ) ? $image['alt'] : '';

				$link     = isset( $item['link'] ) ? $item['link'] : '';
				$target   = ! empty( $item['linkNewTab'] ) ? ' target="_blank"' : '';
				$rel      = ! empty( $item['linkNofollow'] ) ? ' rel="nofollow"' : '';

				$box_class = trim( 'eelfg-logo-img ' . $gray_class );
				?>
				<div class="eelfg-grid-item">
					<div class="<?php echo esc_attr( $box_class ); ?>">
						<?php if ( '' !== $link ) : ?>
							<a href="<?php echo esc_url( $link ); ?>"<?php echo $target . $rel; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<?php endif; ?>

						<?php if ( '' !== $url ) : ?>
							<?php if ( $swap ) : ?>
								<img class="eelfg-grid-img eelfg-logo-img-hover" src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy" decoding="async" fetchpriority="<?php echo esc_attr( $fetch ); ?>">
								<img class="eelfg-grid-img eelfg-logo-img-normal" src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy" decoding="async" fetchpriority="<?php echo esc_attr( $fetch ); ?>">
							<?php else : ?>
								<img class="eelfg-grid-img" src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy" decoding="async" fetchpriority="<?php echo esc_attr( $fetch ); ?>">
							<?php endif; ?>
						<?php else : ?>
							<span class="eelfg-logo-placeholder"><?php esc_html_e( 'Logo', 'easy-elements-for-gutenberg' ); ?></span>
						<?php endif; ?>

						<?php if ( '' !== $link ) : ?>
							</a>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
