<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Local template/iteration variables.

/**
 * Server-side render for the Process Grid block.
 *
 * Mirrors the markup of the Elementor "Process Grid" widget
 * (easy-elements/widgets/process-grid). Element classes use the "eelfg-" prefix.
 *
 * $attributes, $content and $block are provided by register_block_type().
 */

$H = '\EELFG\Frontend\Helper';

$unique_id = ! empty( $attributes['blockId'] ) ? $attributes['blockId'] : 'eelfg-pg-' . substr( md5( wp_json_encode( $attributes ) ), 0, 6 );

$items     = isset( $attributes['items'] ) && is_array( $attributes['items'] ) ? $attributes['items'] : [];
$allowed   = [ 'top', 'left', 'right' ];
$direction = isset( $attributes['iconDirection'] ) && in_array( $attributes['iconDirection'], $allowed, true ) ? $attributes['iconDirection'] : 'top';
$tags      = [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p' ];
$title_tag = isset( $attributes['titleTag'] ) && in_array( $attributes['titleTag'], $tags, true ) ? $attributes['titleTag'] : 'h3';

$block_wrap_attr = get_block_wrapper_attributes( array( 'class' => 'eelfg-block eelfg-process-wrap ' . $unique_id ) );
if ( empty( $block_wrap_attr ) ) {
	$block_wrap_attr = 'class="eelfg-block eelfg-process-wrap ' . esc_attr( $unique_id ) . '"';
}

if ( empty( $items ) ) {
	echo '<div ' . wp_kses_post( $block_wrap_attr ) . '><p>' . esc_html__( 'Please add process items.', 'easy-elements-for-gutenberg' ) . '</p></div>';
	return;
}

// ---------------------------------------------------------------------------
// Inline styles (scoped to this instance).
// ---------------------------------------------------------------------------
$selector     = '.eelfg-process-wrap.' . $unique_id;
$style_handle = 'eelfg-process-grid-style';

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

// Item (space) + responsive columns.
$item = $dims( $attributes['itemPadding'] ?? [], 'padding' );
$cols_resp = [ 'desktop' => [], 'tablet' => [], 'mobile' => [] ];
$c_d = isset( $attributes['columns'] ) ? (int) $attributes['columns'] : 3;
$c_t = isset( $attributes['columnsTablet'] ) ? (int) $attributes['columnsTablet'] : 2;
$c_m = isset( $attributes['columnsMobile'] ) ? (int) $attributes['columnsMobile'] : 1;
if ( $c_d > 0 ) $cols_resp['desktop']['width'] = 'calc(100% / ' . $c_d . ')';
if ( $c_t > 0 ) $cols_resp['tablet']['width']  = 'calc(100% / ' . $c_t . ')';
if ( $c_m > 0 ) $cols_resp['mobile']['width']  = 'calc(100% / ' . $c_m . ')';

// Box.
$box = [];
if ( ! empty( $attributes['boxBgColor'] ) ) $box['background-color'] = $attributes['boxBgColor'];
if ( ! empty( $attributes['textAlign'] ) ) $box['text-align'] = $attributes['textAlign'];
$box = array_merge( $box, $dims( $attributes['boxPadding'] ?? [], 'padding' ), $dims( $attributes['boxRadius'] ?? [], 'radius' ), $shadow( $attributes['boxShadow'] ?? [] ) );
if ( ! empty( $attributes['boxBorder'] ) ) $box = array_merge( $box, $H::border_to_css_props( $attributes['boxBorder'] ) );

// Icon.
$icon = [];
if ( ! empty( $attributes['iconColor'] ) ) $icon['color'] = $attributes['iconColor'];
if ( ! empty( $attributes['iconBgColor'] ) ) $icon['background-color'] = $attributes['iconBgColor'];
if ( '' !== $u( 'iconSize' ) ) $icon['font-size'] = $u( 'iconSize' );
$icon = array_merge( $icon, $dims( $attributes['iconRadius'] ?? [], 'radius' ) );
if ( ! empty( $attributes['iconBorder'] ) ) $icon = array_merge( $icon, $H::border_to_css_props( $attributes['iconBorder'] ) );
$icon_svg = [];
if ( ! empty( $attributes['iconColor'] ) ) $icon_svg['fill'] = $attributes['iconColor'];
if ( '' !== $u( 'iconSize' ) ) { $icon_svg['width'] = $u( 'iconSize' ); $icon_svg['height'] = $u( 'iconSize' ); }

// Title.
$title = $typo( $attributes['titleTypography'] ?? [] );
if ( ! empty( $attributes['titleColor'] ) ) $title['color'] = $attributes['titleColor'];
$title = array_merge( $title, $dims( $attributes['titleMargin'] ?? [], 'margin' ) );

// Description.
$desc = $typo( $attributes['descTypography'] ?? [] );
if ( ! empty( $attributes['descColor'] ) ) $desc['color'] = $attributes['descColor'];

// Number (gradient text + opacity + position).
$number = $typo( $attributes['numberTypography'] ?? [] );
if ( ! empty( $attributes['numberColor'] ) ) {
	$number['background-image'] = 'linear-gradient(180deg, ' . $attributes['numberColor'] . ' 0%, rgba(255, 255, 255, 0.24) 100%)';
}
if ( '' !== ( $attributes['numberOpacity'] ?? '' ) ) $number['opacity'] = (float) $attributes['numberOpacity'];
if ( '' !== $u( 'numberOffsetY' ) ) $number['bottom'] = $u( 'numberOffsetY' );
if ( '' !== $u( 'numberOffsetX' ) ) $number['left'] = $u( 'numberOffsetX' );

$responsive_css = $H::generate_responsive_css( $selector . ' .eelfg-process-item', $cols_resp );

wp_enqueue_style( $style_handle );
$H::add_custom_style( $style_handle, $selector, $responsive_css, [
	'.eelfg-process-item'  => $H::get_inline_styles( $item ),
	'.eelfg-process-box'   => $H::get_inline_styles( $box ),
	'.eelfg-process-icon'  => $H::get_inline_styles( $icon ),
	'.eelfg-process-icon svg' => $H::get_inline_styles( $icon_svg ),
	'.eelfg-process-title' => $H::get_inline_styles( $title ),
	'.eelfg-process-desc'  => $H::get_inline_styles( $desc ),
	'.eelfg-process-number' => $H::get_inline_styles( $number ),
] );

$render_icon = function ( $val ) {
	return ( ! empty( $val ) && 'none' !== $val ) ? '<i class="eelfg-icon ' . esc_attr( $val ) . '" aria-hidden="true"></i>' : '';
};
$is_side = ( 'left' === $direction || 'right' === $direction );
?>
<div <?php echo wp_kses_post( $block_wrap_attr ); ?>>
	<div class="eelfg-process-grid">
		<?php
		foreach ( $items as $item_data ) :
			$icon_html = $render_icon( $item_data['icon'] ?? '' );
			$ttl       = isset( $item_data['title'] ) ? $item_data['title'] : '';
			$desc_txt  = isset( $item_data['description'] ) ? $item_data['description'] : '';
			$link      = isset( $item_data['link'] ) ? $item_data['link'] : '';
			$target    = ! empty( $item_data['linkNewTab'] ) ? ' target="_blank"' : '';
			$rel       = ! empty( $item_data['linkNofollow'] ) ? ' rel="nofollow"' : '';

			$num_type  = isset( $item_data['numberType'] ) ? $item_data['numberType'] : 'p_number';
			$num_html  = '';
			if ( 'p_number' === $num_type && '' !== ( $item_data['processNumber'] ?? '' ) ) {
				$num_html = '<span class="eelfg-process-number">' . esc_html( $item_data['processNumber'] ) . '</span>';
			} elseif ( 'p_icon' === $num_type && ! empty( $item_data['processIcon'] ) ) {
				$num_html = '<span class="eelfg-process-number">' . $render_icon( $item_data['processIcon'] ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
			<div class="eelfg-process-item">
				<div class="eelfg-process-box eelfg-process-dir-<?php echo esc_attr( $direction ); ?>">
					<?php if ( '' !== $link ) : ?>
						<a href="<?php echo esc_url( $link ); ?>"<?php echo $target . $rel; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> class="eelfg-process-link">
					<?php endif; ?>

					<?php if ( '' !== $icon_html ) : ?>
						<span class="eelfg-process-icon"><?php echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<?php endif; ?>

					<?php if ( $is_side ) : ?>
						<div class="eelfg-process-content">
					<?php endif; ?>

					<?php echo $num_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

					<?php if ( '' !== $ttl ) : ?>
						<<?php echo esc_attr( $title_tag ); ?> class="eelfg-process-title"><?php echo wp_kses_post( $ttl ); ?></<?php echo esc_attr( $title_tag ); ?>>
					<?php endif; ?>

					<?php if ( '' !== $desc_txt ) : ?>
						<div class="eelfg-process-desc"><?php echo wp_kses_post( $desc_txt ); ?></div>
					<?php endif; ?>

					<?php if ( $is_side ) : ?>
						</div>
					<?php endif; ?>

					<?php if ( '' !== $link ) : ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>
