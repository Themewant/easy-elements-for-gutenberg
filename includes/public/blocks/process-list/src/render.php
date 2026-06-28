<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Local template/iteration variables.

/**
 * Server-side render for the Process List block.
 *
 * Mirrors the markup of the Elementor "Process List" widget
 * (easy-elements/widgets/process-list). Element classes use the "eelfg-" prefix.
 *
 * $attributes, $content and $block are provided by register_block_type().
 */

$H = '\EELFG\Frontend\Helper';

$unique_id = ! empty( $attributes['blockId'] ) ? $attributes['blockId'] : 'eelfg-pl-' . substr( md5( wp_json_encode( $attributes ) ), 0, 6 );

$icon     = isset( $attributes['icon'] ) ? $attributes['icon'] : '';
$title    = isset( $attributes['title'] ) ? $attributes['title'] : '';
$desc     = isset( $attributes['description'] ) ? $attributes['description'] : '';
$number   = isset( $attributes['processNumber'] ) ? $attributes['processNumber'] : '';
$link     = isset( $attributes['link'] ) ? $attributes['link'] : '';
$target   = ! empty( $attributes['linkNewTab'] ) ? ' target="_blank"' : '';
$nofollow = ! empty( $attributes['linkNofollow'] ) ? ' rel="nofollow"' : '';

$tags      = [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p' ];
$title_tag = isset( $attributes['titleTag'] ) && in_array( $attributes['titleTag'], $tags, true ) ? $attributes['titleTag'] : 'h3';

$block_wrap_attr = get_block_wrapper_attributes( array( 'class' => 'eelfg-block eelfg-process-list-wrap ' . $unique_id ) );
if ( empty( $block_wrap_attr ) ) {
	$block_wrap_attr = 'class="eelfg-block eelfg-process-list-wrap ' . esc_attr( $unique_id ) . '"';
}

// ---------------------------------------------------------------------------
// Inline styles (scoped to this instance).
// ---------------------------------------------------------------------------
$selector     = '.eelfg-process-list-wrap.' . $unique_id;
$style_handle = 'eelfg-process-list-style';

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
$dims = function ( $obj, $type ) use ( $H ) {
	$out = [];
	if ( empty( $obj ) || ! is_array( $obj ) ) return $out;
	if ( 'padding' === $type ) {
		$map = [ 'top' => 'padding-top', 'right' => 'padding-right', 'bottom' => 'padding-bottom', 'left' => 'padding-left' ];
	} elseif ( 'margin' === $type ) {
		$map = [ 'top' => 'margin-top', 'right' => 'margin-right', 'bottom' => 'margin-bottom', 'left' => 'margin-left' ];
	} else {
		$map = [ 'top' => 'border-top-left-radius', 'right' => 'border-top-right-radius', 'bottom' => 'border-bottom-right-radius', 'left' => 'border-bottom-left-radius' ];
	}
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

// Row.
$row = [];
if ( ! empty( $attributes['alignVertical'] ) ) $row['align-items'] = $attributes['alignVertical'];
if ( '' !== $u( 'itemGap' ) ) $row['gap'] = $u( 'itemGap' );

// Icon.
$icon_styles = [];
if ( ! empty( $attributes['iconColor'] ) ) $icon_styles['color'] = $attributes['iconColor'];
$bg = ! empty( $attributes['iconBgGradient'] ) ? $attributes['iconBgGradient'] : ( ! empty( $attributes['iconBgColor'] ) ? $attributes['iconBgColor'] : '' );
if ( '' !== $bg ) $icon_styles['background'] = $bg;
if ( '' !== $u( 'iconBoxSize' ) ) { $b = $u( 'iconBoxSize' ); $icon_styles['width'] = $b; $icon_styles['min-width'] = $b; $icon_styles['height'] = $b; }
$icon_styles = array_merge( $icon_styles, $dims( $attributes['iconRadius'] ?? [], 'radius' ), $dims( $attributes['iconPadding'] ?? [], 'padding' ), $shadow( $attributes['iconBoxShadow'] ?? [] ) );
if ( ! empty( $attributes['iconBorder'] ) ) $icon_styles = array_merge( $icon_styles, $H::border_to_css_props( $attributes['iconBorder'] ) );
$icon_i   = ( '' !== $u( 'iconSize' ) ) ? [ 'font-size' => $u( 'iconSize' ) ] : [];
$icon_svg = [];
if ( ! empty( $attributes['iconColor'] ) ) $icon_svg['fill'] = $attributes['iconColor'];
if ( '' !== $u( 'iconSize' ) ) { $icon_svg['width'] = $u( 'iconSize' ); $icon_svg['height'] = $u( 'iconSize' ); }

// Title.
$title_styles = $typo( $attributes['titleTypography'] ?? [] );
if ( ! empty( $attributes['titleColor'] ) ) $title_styles['color'] = $attributes['titleColor'];
$title_styles = array_merge( $title_styles, $dims( $attributes['titleMargin'] ?? [], 'margin' ) );

// Highlight title (span).
$title_sub = $typo( $attributes['titleSubTypography'] ?? [] );
if ( ! empty( $attributes['titleSubColor'] ) ) $title_sub['color'] = $attributes['titleSubColor'];
$title_sub = array_merge( $title_sub, $dims( $attributes['titleSubMargin'] ?? [], 'margin' ) );

// Description.
$desc_styles = $typo( $attributes['descTypography'] ?? [] );
if ( ! empty( $attributes['descColor'] ) ) $desc_styles['color'] = $attributes['descColor'];

// Number.
$number_styles = $typo( $attributes['numberTypography'] ?? [] );
if ( ! empty( $attributes['numberColor'] ) ) $number_styles['color'] = $attributes['numberColor'];

wp_enqueue_style( $style_handle );
$H::add_custom_style( $style_handle, $selector, '', [
	'.eelfg-process-list'             => $H::get_inline_styles( $row ),
	'.eelfg-process-list-icon'        => $H::get_inline_styles( $icon_styles ),
	'.eelfg-process-list-icon i'      => $H::get_inline_styles( $icon_i ),
	'.eelfg-process-list-icon svg'    => $H::get_inline_styles( $icon_svg ),
	'.eelfg-process-list-title'       => $H::get_inline_styles( $title_styles ),
	'.eelfg-process-list-title span'  => $H::get_inline_styles( $title_sub ),
	'.eelfg-process-list-desc'        => $H::get_inline_styles( $desc_styles ),
	'.eelfg-pro-number'               => $H::get_inline_styles( $number_styles ),
] );

$icon_html = ( ! empty( $icon ) && 'none' !== $icon ) ? '<i class="eelfg-icon ' . esc_attr( $icon ) . '" aria-hidden="true"></i>' : '';
?>
<div <?php echo wp_kses_post( $block_wrap_attr ); ?>>
	<?php if ( '' !== $link ) : ?>
		<a href="<?php echo esc_url( $link ); ?>"<?php echo $target . $nofollow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> class="eelfg-process-list-link">
	<?php endif; ?>
	<div class="eelfg-process-list">
		<?php if ( '' !== $number ) : ?>
			<span class="eelfg-pro-number"><?php echo esc_html( $number ); ?></span>
		<?php endif; ?>
		<?php if ( '' !== $icon_html ) : ?>
			<span class="eelfg-process-list-icon"><?php echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
		<?php endif; ?>
		<div class="eelfg-process-list-content">
			<?php if ( '' !== $title ) : ?>
				<<?php echo esc_attr( $title_tag ); ?> class="eelfg-process-list-title"><?php echo wp_kses_post( $title ); ?></<?php echo esc_attr( $title_tag ); ?>>
			<?php endif; ?>
			<?php if ( '' !== $desc ) : ?>
				<div class="eelfg-process-list-desc"><?php echo wp_kses_post( $desc ); ?></div>
			<?php endif; ?>
		</div>
	</div>
	<?php if ( '' !== $link ) : ?>
		</a>
	<?php endif; ?>
</div>
