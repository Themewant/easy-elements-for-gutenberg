<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Local template/iteration variables.

/**
 * Server-side render for the Progress Bar block.
 *
 * Mirrors the markup of the Elementor "Progress Bar" widget
 * (easy-elements/widgets/progress). Element classes use the "eelfg-" prefix.
 *
 * $attributes, $content and $block are provided by register_block_type().
 */

$H = '\EELFG\Frontend\Helper';

$unique_id = ! empty( $attributes['blockId'] ) ? $attributes['blockId'] : 'eelfg-pb-' . substr( md5( wp_json_encode( $attributes ) ), 0, 6 );

$style   = ( isset( $attributes['selectStyle'] ) && 'style2' === $attributes['selectStyle'] ) ? 'style2' : 'style1';
$title   = isset( $attributes['title'] ) ? $attributes['title'] : '';
$percent = isset( $attributes['percent'] ) ? max( 0, min( 100, (int) $attributes['percent'] ) ) : 0;

$block_wrap_attr = get_block_wrapper_attributes( array( 'class' => 'eelfg-block eelfg-progress-bar-block-wrap ' . $unique_id ) );
if ( empty( $block_wrap_attr ) ) {
	$block_wrap_attr = 'class="eelfg-block eelfg-progress-bar-block-wrap ' . esc_attr( $unique_id ) . '"';
}

// ---------------------------------------------------------------------------
// Inline styles (scoped to this instance).
// ---------------------------------------------------------------------------
$selector     = '.eelfg-progress-bar-block-wrap.' . $unique_id;
$style_handle = 'eelfg-progress-style';

$typo = function ( $obj ) use ( $H ) {
	$out = [];
	if ( empty( $obj ) || ! is_array( $obj ) ) return $out;
	if ( ! empty( $obj['fontFamily'] ) ) $out['font-family'] = $obj['fontFamily'];
	if ( ! empty( $obj['fontSize'] ) ) $out['font-size'] = $H::ensure_unit( $obj['fontSize'] );
	if ( ! empty( $obj['fontWeight'] ) ) $out['font-weight'] = $obj['fontWeight'];
	if ( ! empty( $obj['fontStyle'] ) ) $out['font-style'] = $obj['fontStyle'];
	if ( ! empty( $obj['textTransform'] ) ) $out['text-transform'] = $obj['textTransform'];
	if ( ! empty( $obj['lineHeight'] ) ) $out['line-height'] = $obj['lineHeight'];
	if ( ! empty( $obj['letterSpacing'] ) ) $out['letter-spacing'] = $H::ensure_unit( $obj['letterSpacing'] );
	return $out;
};
$u = function ( $key ) use ( $attributes, $H ) {
	return ( isset( $attributes[ $key ] ) && '' !== $attributes[ $key ] ) ? $H::ensure_unit( $attributes[ $key ] ) : '';
};

$track = [];
$fill  = [];
if ( 'style2' === $style ) {
	$bg = ! empty( $attributes['style2BgGradient'] ) ? $attributes['style2BgGradient'] : ( ! empty( $attributes['style2BgColor'] ) ? $attributes['style2BgColor'] : '' );
	if ( '' !== $bg ) $track['background'] = $bg;
} else {
	if ( ! empty( $attributes['progressColor'] ) ) $track['background'] = $attributes['progressColor'];
	if ( ! empty( $attributes['progressBarColor'] ) ) $fill['background'] = $attributes['progressBarColor'];
}
if ( '' !== $u( 'progressHeight' ) ) $track['height'] = $u( 'progressHeight' );
if ( '' !== $u( 'progressRadius' ) ) $track['border-radius'] = $u( 'progressRadius' );

$title_styles = $typo( $attributes['titleTypography'] ?? [] );
if ( ! empty( $attributes['titleColor'] ) ) $title_styles['color'] = $attributes['titleColor'];

$percent_styles = $typo( $attributes['percentTypography'] ?? [] );
if ( ! empty( $attributes['percentColor'] ) ) $percent_styles['color'] = $attributes['percentColor'];

wp_enqueue_style( $style_handle );
$H::add_custom_style( $style_handle, $selector, '', [
	'.eelfg-progress'      => $H::get_inline_styles( $track ),
	'.eelfg-progress-fill' => $H::get_inline_styles( $fill ),
	'.eelfg-pb-title'      => $H::get_inline_styles( $title_styles ),
	'.eelfg-pb-percent'    => $H::get_inline_styles( $percent_styles ),
] );
?>
<div <?php echo wp_kses_post( $block_wrap_attr ); ?>>
	<div class="eelfg-progress-bar eelfg-progress-<?php echo esc_attr( $style ); ?>">
		<?php if ( 'style2' === $style ) : ?>
			<div class="eelfg-progress-style2">
				<?php if ( '' !== $title ) : ?>
					<p class="eelfg-pb-title"><?php echo wp_kses_post( $title ); ?></p>
				<?php endif; ?>
				<div class="eelfg-progress">
					<div class="eelfg-progress-fill" role="progressbar" style="width: <?php echo esc_attr( $percent ); ?>%" data-width="<?php echo esc_attr( $percent ); ?>" aria-valuenow="<?php echo esc_attr( $percent ); ?>" aria-valuemin="0" aria-valuemax="100"></div>
					<span class="eelfg-pb-percent"><?php echo esc_html( $percent ); ?>%</span>
				</div>
			</div>
		<?php else : ?>
			<div class="eelfg-progress-top">
				<?php if ( '' !== $title ) : ?>
					<p class="eelfg-pb-title"><?php echo wp_kses_post( $title ); ?></p>
				<?php endif; ?>
				<span class="eelfg-pb-percent"><?php echo esc_html( $percent ); ?>%</span>
			</div>
			<div class="eelfg-progress">
				<div class="eelfg-progress-fill" role="progressbar" style="width: <?php echo esc_attr( $percent ); ?>%" data-width="<?php echo esc_attr( $percent ); ?>" aria-valuenow="<?php echo esc_attr( $percent ); ?>" aria-valuemin="0" aria-valuemax="100"></div>
			</div>
		<?php endif; ?>
	</div>
</div>
