<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Local template/iteration variables.

/**
 * Server-side render for the Image Comparison block.
 *
 * Mirrors the markup of the Elementor "Image Comparison" widget
 * (easy-elements/widgets/image-comparison). Element classes use the "eelfg-" prefix.
 *
 * $attributes, $content and $block are provided by register_block_type().
 */

$H = '\EELFG\Frontend\Helper';

$unique_id = ! empty( $attributes['blockId'] ) ? $attributes['blockId'] : 'eelfg-cmp-' . substr( md5( wp_json_encode( $attributes ) ), 0, 6 );

$before = isset( $attributes['beforeImage'] ) && is_array( $attributes['beforeImage'] ) ? $attributes['beforeImage'] : [];
$after  = isset( $attributes['afterImage'] ) && is_array( $attributes['afterImage'] ) ? $attributes['afterImage'] : [];
$before_url = ! empty( $before['url'] ) ? $before['url'] : '';
$after_url  = ! empty( $after['url'] ) ? $after['url'] : '';

$orientation = ( isset( $attributes['orientation'] ) && 'vertical' === $attributes['orientation'] ) ? 'vertical' : 'horizontal';
$offset      = isset( $attributes['offset'] ) ? max( 0, min( 100, (int) $attributes['offset'] ) ) / 100 : 0.5;

$block_wrap_attr = get_block_wrapper_attributes( array( 'class' => 'eelfg-block eelfg-image-comparison-block-wrap ' . $unique_id ) );
if ( empty( $block_wrap_attr ) ) {
	$block_wrap_attr = 'class="eelfg-block eelfg-image-comparison-block-wrap ' . esc_attr( $unique_id ) . '"';
}

if ( '' === $before_url && '' === $after_url ) {
	echo '<div ' . wp_kses_post( $block_wrap_attr ) . '><p>' . esc_html__( 'Please select before and after images.', 'easy-elements-for-gutenberg' ) . '</p></div>';
	return;
}

// ---------------------------------------------------------------------------
// Inline styles (scoped to this instance).
// ---------------------------------------------------------------------------
$selector     = '.eelfg-image-comparison-block-wrap.' . $unique_id;
$style_handle = 'eelfg-image-comparison-style';

$dims = function ( $obj ) use ( $H ) {
	$out = [];
	if ( empty( $obj ) || ! is_array( $obj ) ) return $out;
	$map = [ 'top' => 'border-top-left-radius', 'right' => 'border-top-right-radius', 'bottom' => 'border-bottom-right-radius', 'left' => 'border-bottom-left-radius' ];
	foreach ( $map as $side => $css_prop ) {
		if ( isset( $obj[ $side ] ) && '' !== $obj[ $side ] ) $out[ $css_prop ] = $H::ensure_unit( $obj[ $side ] );
	}
	return $out;
};
$u = function ( $key ) use ( $attributes, $H ) {
	return ( isset( $attributes[ $key ] ) && '' !== $attributes[ $key ] ) ? $H::ensure_unit( $attributes[ $key ] ) : '';
};

$container = $dims( $attributes['containerRadius'] ?? [] );
if ( '' !== $u( 'height' ) ) $container['min-height'] = $u( 'height' );

$img = ( '' !== $u( 'height' ) ) ? [ 'height' => $u( 'height' ) ] : [];

wp_enqueue_style( $style_handle );
$H::add_custom_style( $style_handle, $selector, '', [
	'.eelfg-comparison-container'     => $H::get_inline_styles( $container ),
	'.eelfg-comparison-container img' => $H::get_inline_styles( $img ),
] );
?>
<div <?php echo wp_kses_post( $block_wrap_attr ); ?>>
	<div class="eelfg-comparison eelfg-comparison-<?php echo esc_attr( $orientation ); ?>">
		<div class="eelfg-comparison-container" data-offset="<?php echo esc_attr( $offset ); ?>" data-orientation="<?php echo esc_attr( $orientation ); ?>">
			<?php if ( '' !== $before_url ) : ?>
				<img class="eelfg-comparison-before" src="<?php echo esc_url( $before_url ); ?>" alt="<?php echo esc_attr( $before['alt'] ?? __( 'Before', 'easy-elements-for-gutenberg' ) ); ?>">
			<?php endif; ?>
			<?php if ( '' !== $after_url ) : ?>
				<img class="eelfg-comparison-after" src="<?php echo esc_url( $after_url ); ?>" alt="<?php echo esc_attr( $after['alt'] ?? __( 'After', 'easy-elements-for-gutenberg' ) ); ?>">
			<?php endif; ?>
			<div class="eelfg-comparison-handle">
				<span class="eelfg-comparison-left-arrow"></span>
				<span class="eelfg-comparison-right-arrow"></span>
			</div>
		</div>
	</div>
</div>
