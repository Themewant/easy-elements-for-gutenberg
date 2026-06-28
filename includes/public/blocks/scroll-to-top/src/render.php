<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Local template/iteration variables.

/**
 * Server-side render for the Scroll Top block.
 *
 * Mirrors the markup of the Elementor "Scroll Top" widget
 * (easy-elements/widgets/scroll-to-top). Element classes use the "eelfg-" prefix.
 *
 * $attributes, $content and $block are provided by register_block_type().
 */

$H = '\EELFG\Frontend\Helper';

$unique_id = ! empty( $attributes['blockId'] ) ? $attributes['blockId'] : 'eelfg-stt-' . substr( md5( wp_json_encode( $attributes ) ), 0, 6 );

$icon = isset( $attributes['scrollIcon'] ) ? $attributes['scrollIcon'] : '';

// In the editor, render the button inline + visible so it can be seen and styled
// (on the front end it is a fixed button that appears after scrolling).
$is_editor = ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || is_admin();

$block_wrap_attr = get_block_wrapper_attributes( array( 'class' => 'eelfg-block eelfg-scroll-top-block-wrap ' . $unique_id ) );
if ( empty( $block_wrap_attr ) ) {
	$block_wrap_attr = 'class="eelfg-block eelfg-scroll-top-block-wrap ' . esc_attr( $unique_id ) . '"';
}

// ---------------------------------------------------------------------------
// Inline styles (scoped to this instance).
// ---------------------------------------------------------------------------
$selector     = '.eelfg-scroll-top-block-wrap.' . $unique_id;
$style_handle = 'eelfg-scroll-to-top-style';

$btn = [];
if ( ! empty( $attributes['bgColor'] ) ) $btn['background-color'] = $attributes['bgColor'];
if ( ! empty( $attributes['color'] ) ) $btn['color'] = $attributes['color'];

$btn_svg = ! empty( $attributes['color'] ) ? [ 'fill' => $attributes['color'] ] : [];

wp_enqueue_style( $style_handle );
$H::add_custom_style( $style_handle, $selector, '', [
	'.eelfg-scroll-top'          => $H::get_inline_styles( $btn ),
	'.eelfg-scroll-top i'        => ! empty( $attributes['color'] ) ? 'color:' . $attributes['color'] : '',
	'.eelfg-scroll-top svg, ' . $selector . ' .eelfg-scroll-top svg path' => $H::get_inline_styles( $btn_svg ),
] );

$icon_html = ( ! empty( $icon ) && 'none' !== $icon )
	? '<i class="eelfg-icon ' . esc_attr( $icon ) . '" aria-hidden="true"></i>'
	: '<svg viewBox="0 0 24 24" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M12 4l8 8-1.4 1.4L13 7.8V20h-2V7.8L5.4 13.4 4 12z"></path></svg>';

$btn_classes = 'eelfg-scroll-top';
if ( $is_editor ) {
	$btn_classes .= ' eelfg-scroll-visible eelfg-scroll-editor';
}
?>
<div <?php echo wp_kses_post( $block_wrap_attr ); ?>>
	<div class="<?php echo esc_attr( $btn_classes ); ?>" role="button" tabindex="0" aria-label="<?php echo esc_attr__( 'Scroll to top', 'easy-elements-for-gutenberg' ); ?>">
		<?php echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
</div>
