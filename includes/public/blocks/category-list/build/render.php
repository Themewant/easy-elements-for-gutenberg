<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Local template/iteration variables.

/**
 * Server-side render for the Category List block.
 *
 * Mirrors the markup of the Elementor "Category List" widget
 * (easy-elements/widgets/category-list). Element classes use the "eelfg-" prefix.
 */

$H = '\EELFG\Frontend\Helper';

$unique_id  = ! empty( $attributes['blockId'] ) ? $attributes['blockId'] : 'eelfg-cat-' . substr( md5( wp_json_encode( $attributes ) ), 0, 6 );
$taxonomy   = ! empty( $attributes['taxonomy'] ) ? $attributes['taxonomy'] : 'category';
$layout     = in_array( $attributes['layoutCategory'] ?? 'list', [ 'list', 'grid' ], true ) ? $attributes['layoutCategory'] : 'list';
$show_icon  = ! empty( $attributes['showIcon'] );
$show_count = ! empty( $attributes['showCount'] );
$cat_icon   = isset( $attributes['catIcon'] ) ? $attributes['catIcon'] : '';

$block_wrap_attr = get_block_wrapper_attributes( array(
	'class' => 'eelfg-block eelfg-cat-block-wrap ' . $unique_id,
) );
if ( empty( $block_wrap_attr ) ) {
	$block_wrap_attr = 'class="eelfg-block eelfg-cat-block-wrap ' . esc_attr( $unique_id ) . '"';
}

// ---------------------------------------------------------------------------
// Inline styles.
// ---------------------------------------------------------------------------
$selector     = '.eelfg-cat-block-wrap.' . $unique_id;
$style_handle = 'eelfg-category-list-style';

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
	$map = 'padding' === $type
		? [ 'top' => 'padding-top', 'right' => 'padding-right', 'bottom' => 'padding-bottom', 'left' => 'padding-left' ]
		: [ 'top' => 'border-top-left-radius', 'right' => 'border-top-right-radius', 'bottom' => 'border-bottom-right-radius', 'left' => 'border-bottom-left-radius' ];
	foreach ( $map as $side => $css_prop ) {
		if ( isset( $obj[ $side ] ) && '' !== $obj[ $side ] ) $out[ $css_prop ] = $H::ensure_unit( $obj[ $side ] );
	}
	return $out;
};
$bg = function ( $colorKey, $gradKey ) use ( $attributes ) {
	if ( ! empty( $attributes[ $gradKey ] ) ) return [ 'background' => $attributes[ $gradKey ] ];
	if ( ! empty( $attributes[ $colorKey ] ) ) return [ 'background' => $attributes[ $colorKey ] ];
	return [];
};
$u = function ( $key ) use ( $attributes, $H ) {
	return ( isset( $attributes[ $key ] ) && '' !== $attributes[ $key ] ) ? $H::ensure_unit( $attributes[ $key ] ) : '';
};

// Grid columns (responsive) + list/grid gap.
$col_css = '';
if ( 'grid' === $layout ) {
	$c_d = isset( $attributes['columns'] ) ? (int) $attributes['columns'] : 3;
	$c_t = isset( $attributes['columnsTablet'] ) && '' !== $attributes['columnsTablet'] ? (int) $attributes['columnsTablet'] : $c_d;
	$c_m = isset( $attributes['columnsMobile'] ) && '' !== $attributes['columnsMobile'] ? (int) $attributes['columnsMobile'] : 1;
	$col_css = $H::generate_responsive_css( $selector . ' .eelfg-cat-layout-grid', [
		'desktop' => [ 'grid-template-columns' => 'repeat(' . max( 1, $c_d ) . ', 1fr)' ],
		'tablet'  => [ 'grid-template-columns' => 'repeat(' . max( 1, $c_t ) . ', 1fr)' ],
		'mobile'  => [ 'grid-template-columns' => 'repeat(' . max( 1, $c_m ) . ', 1fr)' ],
	] );
}

$list_styles = ( '' !== $u( 'itemsGap' ) ) ? [ 'gap' => $u( 'itemsGap' ) ] : [];

$item = $bg( 'itemBgColor', 'itemBgGradient' );
$item = array_merge( $item, $dims( $attributes['itemPadding'] ?? [], 'padding' ), $dims( $attributes['itemRadius'] ?? [], 'radius' ) );
if ( ! empty( $attributes['itemBorder'] ) ) $item = array_merge( $item, $H::border_to_css_props( $attributes['itemBorder'] ) );
if ( '' !== $u( 'iconSpacing' ) ) $item['gap'] = $u( 'iconSpacing' );

$item_hover = $bg( 'itemBgHoverColor', 'itemBgHoverGradient' );
if ( ! empty( $attributes['itemBorderColorHover'] ) ) $item_hover['border-color'] = $attributes['itemBorderColorHover'];

$icon_styles = [];
if ( '' !== $u( 'iconSize' ) ) $icon_styles['font-size'] = $u( 'iconSize' );
if ( ! empty( $attributes['iconColor'] ) ) $icon_styles['color'] = $attributes['iconColor'];
$icon_fill = ! empty( $attributes['iconColor'] ) ? [ 'fill' => $attributes['iconColor'] ] : [];
$icon_hover = ! empty( $attributes['iconColorHover'] ) ? [ 'color' => $attributes['iconColorHover'] ] : [];
$icon_hover_fill = ! empty( $attributes['iconColorHover'] ) ? [ 'fill' => $attributes['iconColorHover'] ] : [];

$name_styles = $typo( $attributes['nameTypography'] ?? [] );
if ( ! empty( $attributes['nameColor'] ) ) $name_styles['color'] = $attributes['nameColor'];
$name_hover = ! empty( $attributes['nameColorHover'] ) ? [ 'color' => $attributes['nameColorHover'] ] : [];

$count_styles = $typo( $attributes['countTypography'] ?? [] );
if ( ! empty( $attributes['countColor'] ) ) $count_styles['color'] = $attributes['countColor'];

wp_enqueue_style( $style_handle );
$H::add_custom_style( $style_handle, $selector, $col_css, [
	'.eelfg-cat-list'                            => $H::get_inline_styles( $list_styles ),
	'.eelfg-cat-item'                            => $H::get_inline_styles( $item ),
	'.eelfg-cat-item:hover'                      => $H::get_inline_styles( $item_hover ),
	'.eelfg-cat-icon'                            => $H::get_inline_styles( $icon_styles ),
	'.eelfg-cat-icon svg, ' . $selector . ' .eelfg-cat-icon svg path' => $H::get_inline_styles( $icon_fill ),
	'.eelfg-cat-item:hover .eelfg-cat-icon'      => $H::get_inline_styles( $icon_hover ),
	'.eelfg-cat-item:hover .eelfg-cat-icon svg, ' . $selector . ' .eelfg-cat-item:hover .eelfg-cat-icon svg path' => $H::get_inline_styles( $icon_hover_fill ),
	'.eelfg-cat-name'                            => $H::get_inline_styles( $name_styles ),
	'.eelfg-cat-item:hover .eelfg-cat-name'      => $H::get_inline_styles( $name_hover ),
	'.eelfg-cat-count'                           => $H::get_inline_styles( $count_styles ),
] );

// ---------------------------------------------------------------------------
// Query.
// ---------------------------------------------------------------------------
if ( empty( $taxonomy ) || ! taxonomy_exists( $taxonomy ) ) {
	echo '<div ' . wp_kses_post( $block_wrap_attr ) . '><p>' . esc_html__( 'Select a valid taxonomy.', 'easy-elements-for-gutenberg' ) . '</p></div>';
	return;
}

$terms = get_terms( array(
	'taxonomy'   => $taxonomy,
	'hide_empty' => ! empty( $attributes['hideEmpty'] ),
	'orderby'    => isset( $attributes['orderby'] ) ? $attributes['orderby'] : 'name',
	'order'      => ( isset( $attributes['order'] ) && 'DESC' === $attributes['order'] ) ? 'DESC' : 'ASC',
	'number'     => isset( $attributes['number'] ) ? absint( $attributes['number'] ) : 0,
) );

if ( empty( $terms ) || is_wp_error( $terms ) ) {
	echo '<div ' . wp_kses_post( $block_wrap_attr ) . '><p>' . esc_html__( 'No terms found.', 'easy-elements-for-gutenberg' ) . '</p></div>';
	return;
}

$icon_folder = '<svg viewBox="0 0 24 24" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"><path d="M10 4H4a2 2 0 00-2 2v12a2 2 0 002 2h16a2 2 0 002-2V8a2 2 0 00-2-2h-8l-2-2z"/></svg>';
$icon_html = '';
if ( $show_icon ) {
	$icon_html = ( ! empty( $cat_icon ) && 'none' !== $cat_icon ) ? '<i class="eelfg-icon ' . esc_attr( $cat_icon ) . '" aria-hidden="true"></i>' : $icon_folder;
}
?>
<div <?php echo wp_kses_post( $block_wrap_attr ); ?>>
	<div class="eelfg-cat-list eelfg-cat-layout-<?php echo esc_attr( $layout ); ?>">
		<?php
		foreach ( $terms as $term ) :
			$term_link = get_term_link( $term );
			if ( is_wp_error( $term_link ) ) {
				continue;
			}
			?>
			<a class="eelfg-cat-item" href="<?php echo esc_url( $term_link ); ?>">
				<?php if ( $show_icon ) : ?>
					<span class="eelfg-cat-icon"><?php echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above / static SVG. ?></span>
				<?php endif; ?>
				<span class="eelfg-cat-name"><?php echo esc_html( $term->name ); ?></span>
				<?php if ( $show_count ) : ?>
					<span class="eelfg-cat-count">(<?php echo esc_html( $term->count ); ?>)</span>
				<?php endif; ?>
			</a>
		<?php endforeach; ?>
	</div>
</div>
