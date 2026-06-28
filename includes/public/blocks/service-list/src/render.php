<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Local template/iteration variables.

/**
 * Server-side render for the Service List block.
 *
 * Mirrors the markup of the Elementor "Service List" widget
 * (easy-elements/widgets/service-list) including its three skins. Element
 * classes use the "eelfg-" prefix.
 *
 * $attributes, $content and $block are provided by register_block_type().
 */

$H = '\EELFG\Frontend\Helper';

$unique_id = ! empty( $attributes['blockId'] ) ? $attributes['blockId'] : 'eelfg-svc-' . substr( md5( wp_json_encode( $attributes ) ), 0, 6 );

$skins      = [ 'skin1', 'skin2', 'skin3' ];
$skin       = isset( $attributes['skinStyle'] ) && in_array( $attributes['skinStyle'], $skins, true ) ? $attributes['skinStyle'] : 'skin1';
$media_type = isset( $attributes['mediaType'] ) ? $attributes['mediaType'] : 'icon';
$tags       = [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p' ];
$title_tag  = isset( $attributes['titleTag'] ) && in_array( $attributes['titleTag'], $tags, true ) ? $attributes['titleTag'] : 'h3';

$title    = isset( $attributes['title'] ) ? $attributes['title'] : '';
$desc     = isset( $attributes['description'] ) ? $attributes['description'] : '';
$number   = isset( $attributes['number'] ) ? $attributes['number'] : '';
$icon     = isset( $attributes['serviceIcon'] ) ? $attributes['serviceIcon'] : '';
$image    = isset( $attributes['serviceImage'] ) && is_array( $attributes['serviceImage'] ) ? $attributes['serviceImage'] : [];
$img_url  = ! empty( $image['url'] ) ? $image['url'] : '';

$readmore_type = isset( $attributes['readmoreType'] ) ? $attributes['readmoreType'] : 'readmore';
$readmore_text = isset( $attributes['readmoreText'] ) ? $attributes['readmoreText'] : '';
$readmore_icon = isset( $attributes['readmoreIcon'] ) ? $attributes['readmoreIcon'] : '';
$link          = isset( $attributes['readmoreLink'] ) ? $attributes['readmoreLink'] : '';
$target        = ! empty( $attributes['linkNewTab'] ) ? ' target="_blank"' : '';
$nofollow      = ! empty( $attributes['linkNofollow'] ) ? ' rel="nofollow"' : '';
$image_only_cls = ( 'image_only' === $media_type ) ? 'eelfg-image-only' : '';

$wrap_classes = 'eelfg-block eelfg-service-list-block-wrap eelfg-service-list-wrapper-' . $skin . ' ' . $unique_id;
$block_wrap_attr = get_block_wrapper_attributes( array( 'class' => $wrap_classes ) );
if ( empty( $block_wrap_attr ) ) {
	$block_wrap_attr = 'class="' . esc_attr( $wrap_classes ) . '"';
}

// ---------------------------------------------------------------------------
// Inline styles (scoped to this instance).
// ---------------------------------------------------------------------------
$selector     = '.eelfg-service-list-block-wrap.' . $unique_id;
$style_handle = 'eelfg-service-list-style';

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
$bg = function ( $colorKey, $gradKey ) use ( $attributes ) {
	if ( ! empty( $attributes[ $gradKey ] ) ) return [ 'background' => $attributes[ $gradKey ] ];
	if ( ! empty( $attributes[ $colorKey ] ) ) return [ 'background' => $attributes[ $colorKey ] ];
	return [];
};
$u = function ( $key ) use ( $attributes, $H ) {
	return ( isset( $attributes[ $key ] ) && '' !== $attributes[ $key ] ) ? $H::ensure_unit( $attributes[ $key ] ) : '';
};

// Layout.
$list = [];
if ( ! empty( $attributes['vAlign'] ) ) $list['align-items'] = $attributes['vAlign'];
$media = [];
if ( ! empty( $attributes['vAlign'] ) ) $media['align-items'] = $attributes['vAlign'];
if ( '' !== $u( 'mediaContentGap' ) ) $media['gap'] = $u( 'mediaContentGap' );

// Circle / icon wrap (circle + icon style both target the wrap).
$wrap = $bg( 'iconBgColor', 'iconBgGradient' );
$wrap = array_merge( $wrap, $dims( $attributes['iconPadding'] ?? [], 'padding' ), $dims( $attributes['iconBorderRadius'] ?? [], 'radius' ) );
$wrap = array_merge( $wrap, $bg( 'circleBgColor', 'circleBgGradient' ) );
if ( '' !== $u( 'circleSize' ) ) { $s = $u( 'circleSize' ); $wrap['max-width'] = $s; $wrap['min-width'] = $s; $wrap['max-height'] = $s; $wrap['min-height'] = $s; }
$wrap = array_merge( $wrap, $dims( $attributes['circleBorderRadius'] ?? [], 'radius' ), $dims( $attributes['circlePadding'] ?? [], 'padding' ) );
if ( ! empty( $attributes['circleBorder'] ) ) $wrap = array_merge( $wrap, $H::border_to_css_props( $attributes['circleBorder'] ) );

$icon_i   = ( ! empty( $attributes['iconColor'] ) ) ? [ 'color' => $attributes['iconColor'] ] : [];
$icon_svg = [];
if ( ! empty( $attributes['iconColor'] ) ) $icon_svg['fill'] = $attributes['iconColor'];
if ( '' !== $u( 'iconSize' ) ) { $icon_svg['width'] = $u( 'iconSize' ); $icon_svg['height'] = $u( 'iconSize' ); $icon_i['font-size'] = $u( 'iconSize' ); }

// Number.
$number_styles = $typo( $attributes['numberTypography'] ?? [] );
if ( ! empty( $attributes['numberColor'] ) ) $number_styles['color'] = $attributes['numberColor'];

// Title.
$title_styles = $typo( $attributes['titleTypography'] ?? [] );
if ( ! empty( $attributes['titleColor'] ) ) $title_styles['color'] = $attributes['titleColor'];
$title_styles = array_merge( $title_styles, $dims( $attributes['titleMargin'] ?? [], 'margin' ) );

// Description.
$desc_styles = $typo( $attributes['descTypography'] ?? [] );
if ( ! empty( $attributes['descColor'] ) ) $desc_styles['color'] = $attributes['descColor'];
$desc_styles = array_merge( $desc_styles, $dims( $attributes['descMargin'] ?? [], 'margin' ) );

// Read more (text).
$rm = $typo( $attributes['readmoreTypography'] ?? [] );
if ( ! empty( $attributes['readmoreColor'] ) ) $rm['color'] = $attributes['readmoreColor'];
$rm = array_merge( $rm, $bg( 'readmoreBgColor', 'readmoreBgGradient' ), $dims( $attributes['readmorePadding'] ?? [], 'padding' ), $dims( $attributes['readmoreBorderRadius'] ?? [], 'radius' ) );
if ( ! empty( $attributes['readmoreBorder'] ) ) $rm = array_merge( $rm, $H::border_to_css_props( $attributes['readmoreBorder'] ) );
$rm_hover = $bg( 'readmoreHoverBgColor', 'readmoreHoverBgGradient' );
if ( ! empty( $attributes['readmoreHoverColor'] ) ) $rm_hover['color'] = $attributes['readmoreHoverColor'];
if ( ! empty( $attributes['readmoreHoverBorderColor'] ) ) $rm_hover['border-color'] = $attributes['readmoreHoverBorderColor'];
$rm_icon_spacing = ( '' !== $u( 'readmoreIconSpacing' ) ) ? [ 'margin-left' => $u( 'readmoreIconSpacing' ) ] : [];

// Read more (only icon).
$oi_i   = ( ! empty( $attributes['readmoreIconColor'] ) ) ? [ 'color' => $attributes['readmoreIconColor'] ] : [];
$oi_svg = ( ! empty( $attributes['readmoreIconColor'] ) ) ? [ 'fill' => $attributes['readmoreIconColor'] ] : [];
$oi_span = $bg( 'readmoreIconBgColor', 'readmoreIconBgGradient' );
if ( '' !== $u( 'circleSizeOnlyIcon' ) ) { $s = $u( 'circleSizeOnlyIcon' ); $oi_span['width'] = $s; $oi_span['height'] = $s; $oi_span['line-height'] = $s; }
$oi_span = array_merge( $oi_span, $dims( $attributes['circleBorderRadiusOnlyIcon'] ?? [], 'radius' ) );
$oi_glyph = [];
if ( '' !== $u( 'circleIconSizeOnly' ) ) { $oi_glyph['font-size'] = $u( 'circleIconSizeOnly' ); }
$oi_glyph_svg = ( '' !== $u( 'circleIconSizeOnly' ) ) ? [ 'width' => $u( 'circleIconSizeOnly' ), 'height' => $u( 'circleIconSizeOnly' ) ] : [];

// Image.
$img_styles = ( '' !== $u( 'imageHeight' ) ) ? [ 'height' => $u( 'imageHeight' ) ] : [];
$img_styles = array_merge( $img_styles, $dims( $attributes['imageBorderRadius'] ?? [], 'radius' ) );

// Others (skin2).
$circle_hover = [];
if ( ! empty( $attributes['circleHoverColor'] ) ) { $circle_hover['background-color'] = $attributes['circleHoverColor']; $circle_hover['border-color'] = $attributes['circleHoverColor']; }
$line_normal = ! empty( $attributes['lineColor'] ) ? [ 'border-bottom-color' => $attributes['lineColor'] ] : [];
$line_hover  = ! empty( $attributes['lineColorHover'] ) ? [ 'border-bottom-color' => $attributes['lineColorHover'] ] : [];

wp_enqueue_style( $style_handle );
$H::add_custom_style( $style_handle, $selector, '', [
	'.eelfg-service-list'                       => $H::get_inline_styles( $list ),
	'.eelfg-service-media'                      => $H::get_inline_styles( $media ),
	'.eelfg-icon-img-wrap'                      => $H::get_inline_styles( $wrap ),
	'.eelfg-service-icon i'                     => $H::get_inline_styles( $icon_i ),
	'.eelfg-service-icon svg'                   => $H::get_inline_styles( $icon_svg ),
	'.eelfg--number'                            => $H::get_inline_styles( $number_styles ),
	'.eelfg-service-title'                      => $H::get_inline_styles( $title_styles ),
	'.eelfg-des'                                => $H::get_inline_styles( $desc_styles ),
	'.eelfg-readmore'                           => $H::get_inline_styles( $rm ),
	'.eelfg-readmore:hover'                     => $H::get_inline_styles( $rm_hover ),
	'.eelfg-readmore i, ' . $selector . ' .eelfg-readmore svg' => $H::get_inline_styles( $rm_icon_spacing ),
	'.eelfg-only-icon i'                        => $H::get_inline_styles( $oi_i ),
	'.eelfg-only-icon svg'                      => $H::get_inline_styles( $oi_svg ),
	'.eelfg-only-icon span'                     => $H::get_inline_styles( $oi_span ),
	'.eelfg-only-icon span i'                   => $H::get_inline_styles( $oi_glyph ),
	'.eelfg-only-icon span svg'                 => $H::get_inline_styles( $oi_glyph_svg ),
	'.eelfg-icon-img-wrap img'                  => $H::get_inline_styles( $img_styles ),
	'.eelfg-service-list:hover .eelfg-icon-img-wrap' => $H::get_inline_styles( $circle_hover ),
	'.eelfg-service-list-skin2:before'          => $H::get_inline_styles( $line_normal ),
	'.eelfg-service-list-skin2:after'           => $H::get_inline_styles( $line_hover ),
] );

// ---------------------------------------------------------------------------
// Markup helpers.
// ---------------------------------------------------------------------------
$svg = function ( $d ) {
	return '<svg viewBox="0 0 24 24" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="' . $d . '"></path></svg>';
};
$chevron_right = $svg( 'M8.6 5.4 7.2 6.8 12.4 12l-5.2 5.2 1.4 1.4L15.2 12z' );
$arrow_right   = $svg( 'M4 11v2h12l-5.5 5.5 1.4 1.4L20.3 12l-8.4-7.9-1.4 1.4L16 11z' );
$arrow_up_right = $svg( 'M7 7v2h7.6L6 17.6 7.4 19 16 10.4V18h2V7z' );
$render_icon = function ( $val ) {
	return ( ! empty( $val ) && 'none' !== $val ) ? '<i class="eelfg-icon ' . esc_attr( $val ) . '" aria-hidden="true"></i>' : '';
};

// Media block (shared across skins).
$media_html = '<div class="eelfg-icon-img-wrap ' . esc_attr( $image_only_cls ) . '">';
if ( 'number' === $media_type ) {
	$media_html .= '<span class="eelfg--number">' . esc_html( $number ) . '</span>';
} elseif ( 'icon' === $media_type && '' !== $icon ) {
	$media_html .= '<span class="eelfg-service-icon">' . $render_icon( $icon ) . '</span>';
} elseif ( ( 'image' === $media_type || 'image_only' === $media_type ) && '' !== $img_url ) {
	$media_html .= '<img class="eelfg-service-image" src="' . esc_url( $img_url ) . '" alt="' . esc_attr( $image['alt'] ?? $title ) . '">';
}
$media_html .= '</div>';

$title_html = ( '' !== $title ) ? '<' . tag_escape( $title_tag ) . ' class="eelfg-service-title">' . wp_kses_post( $title ) . '</' . tag_escape( $title_tag ) . '>' : '';

// Read more area.
$readmore_html = '';
if ( '' !== $link ) {
	$default_icon = ( 'skin3' === $skin ) ? $arrow_up_right : $arrow_right;
	if ( 'readmore' === $readmore_type && '' !== $readmore_text ) {
		$ric = ( '' !== $render_icon( $readmore_icon ) ) ? $render_icon( $readmore_icon ) : $chevron_right;
		$readmore_html = '<div class="eelfg-readmore-area"><a class="eelfg-readmore" href="' . esc_url( $link ) . '"' . $target . $nofollow . '>'
			. esc_html( $readmore_text ) . ' ' . $ric . '</a></div>';
	} else {
		$ric = ( '' !== $render_icon( $readmore_icon ) ) ? $render_icon( $readmore_icon ) : $default_icon;
		$readmore_html = '<div class="eelfg-readmore-area"><a class="eelfg-readmore eelfg-only-icon" href="' . esc_url( $link ) . '"' . $target . $nofollow . '>'
			. '<span class="screen-reader-text">' . esc_html__( 'Read more', 'easy-elements-for-gutenberg' ) . '</span>'
			. '<span>' . $ric . '</span></a></div>';
	}
}
?>
<div <?php echo wp_kses_post( $block_wrap_attr ); ?>>
	<div class="eelfg-service-list eelfg-service-list-<?php echo esc_attr( $skin ); ?>">
		<?php if ( 'skin2' === $skin ) : ?>
			<div class="eelfg-service-media">
				<?php echo $media_html . $title_html . $readmore_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
			<div class="eelfg-des"><?php echo wp_kses_post( $desc ); ?></div>
		<?php else : ?>
			<div class="eelfg-service-media">
				<?php echo $media_html . $title_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
			<div class="eelfg-service-des">
				<div class="eelfg-des"><?php echo wp_kses_post( $desc ); ?></div>
				<?php echo $readmore_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		<?php endif; ?>
	</div>
</div>
