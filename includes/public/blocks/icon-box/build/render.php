<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Local template/iteration variables.

/**
 * Server-side render for the Info Box block.
 *
 * Mirrors the markup of the Elementor "Info Box" widget
 * (easy-elements/widgets/icon-box) so the shared CSS applies on the front end.
 * Element classes use this plugin's "eelfg-" prefix.
 */

$H = '\EELFG\Frontend\Helper';

$unique_id = ! empty( $attributes['blockId'] ) ? $attributes['blockId'] : 'eelfg-icon-box-' . substr( md5( wp_json_encode( $attributes ) ), 0, 6 );

$skin        = isset( $attributes['infoSkin'] ) ? $attributes['infoSkin'] : 'default';
$icon_type   = isset( $attributes['iconType'] ) ? $attributes['iconType'] : 'icon';
$icon        = isset( $attributes['icon'] ) ? $attributes['icon'] : '';
$icon_image  = isset( $attributes['iconImage'] ) && is_array( $attributes['iconImage'] ) ? $attributes['iconImage'] : [];
$number      = isset( $attributes['numberTitle'] ) ? $attributes['numberTitle'] : '';
$num_grad    = ! empty( $attributes['numberGradient'] );
$title       = isset( $attributes['title'] ) ? $attributes['title'] : '';
$allowed_tags = [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p' ];
$title_tag   = isset( $attributes['titleTag'] ) && in_array( $attributes['titleTag'], $allowed_tags, true ) ? $attributes['titleTag'] : 'h3';
$description = isset( $attributes['description'] ) ? $attributes['description'] : '';
$features    = isset( $attributes['features'] ) && is_array( $attributes['features'] ) ? $attributes['features'] : [];

$direction   = isset( $attributes['iconDirection'] ) ? $attributes['iconDirection'] : 'top';
$hover_dir   = isset( $attributes['itemHoverBgDirection'] ) ? $attributes['itemHoverBgDirection'] : 'default';
$grad_border = ! empty( $attributes['gradientBorder'] ) ? 'eelfg-gradient-border' : '';

$link        = ! empty( $attributes['linkUrl'] ) ? $attributes['linkUrl'] : '';
$target      = ! empty( $attributes['linkTarget'] ) ? ' target="_blank"' : '';
$nofollow    = ! empty( $attributes['linkNofollow'] ) ? ' rel="nofollow"' : '';
$box_link    = $link && ! empty( $attributes['enableBoxLink'] );
$show_more   = ! empty( $attributes['showReadMore'] );
$more_type   = isset( $attributes['readMoreType'] ) ? $attributes['readMoreType'] : 'read_text';
$more_text   = isset( $attributes['readMoreText'] ) ? $attributes['readMoreText'] : '';
$more_icon   = isset( $attributes['readMoreIcon'] ) ? $attributes['readMoreIcon'] : '';
$more_t_icon = isset( $attributes['readMoreTextIcon'] ) ? $attributes['readMoreTextIcon'] : '';
$more_t_show = ! empty( $attributes['readMoreTextIconShow'] );
$btn_align2  = isset( $attributes['buttonTextAlign'] ) ? $attributes['buttonTextAlign'] : '';

$wrap_classes = [ 'eelfg-block', 'eelfg-icon-box-block-wrap', 'eelfg-icon-box-wraps', $unique_id ];
if ( ! empty( $attributes['readMoreAlignment'] ) ) {
	$wrap_classes[] = 'eelfg-btn-align-' . $attributes['readMoreAlignment'];
}
$block_wrap_attr = get_block_wrapper_attributes( array( 'class' => implode( ' ', $wrap_classes ) ) );
if ( empty( $block_wrap_attr ) ) {
	$block_wrap_attr = 'class="' . esc_attr( implode( ' ', $wrap_classes ) ) . '"';
}

// ---------------------------------------------------------------------------
// Inline styles (scoped to this block instance via $unique_id).
// ---------------------------------------------------------------------------
$selector     = '.eelfg-icon-box-block-wrap.' . $unique_id;
$style_handle = 'eelfg-icon-box-style';

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

// Item
$item = [];
if ( ! empty( $attributes['itemBgColor'] ) ) $item['background'] = $attributes['itemBgColor'];
if ( ! empty( $attributes['textAlign'] ) ) $item['text-align'] = $attributes['textAlign'];
$item = array_merge( $item, $dims( $attributes['itemBorderRadius'] ?? [], 'radius' ), $dims( $attributes['itemPadding'] ?? [], 'padding' ), $dims( $attributes['itemMargin'] ?? [], 'margin' ), $shadow( $attributes['itemBoxShadow'] ?? [] ) );
if ( ! empty( $attributes['itemBorder'] ) ) $item = array_merge( $item, $H::border_to_css_props( $attributes['itemBorder'] ) );

$item_flex = [];
if ( in_array( $direction, [ 'left', 'right' ], true ) ) {
	if ( ! empty( $attributes['iconVerticalAlignment'] ) ) $item_flex['align-items'] = $attributes['iconVerticalAlignment'];
	if ( isset( $attributes['itemSpacing'] ) && '' !== $attributes['itemSpacing'] ) $item_flex['gap'] = $H::ensure_unit( $attributes['itemSpacing'] );
}

$item_hover_before = [];
if ( ! empty( $attributes['itemHoverBgColor'] ) ) $item_hover_before['background'] = $attributes['itemHoverBgColor'];
$item_hover = [];
if ( ! empty( $attributes['itemHoverBorderColor'] ) ) $item_hover['border-color'] = $attributes['itemHoverBorderColor'];
$item_hover = array_merge( $item_hover, $shadow( $attributes['itemHoverBoxShadow'] ?? [] ) );

// Icon
$icon_styles = [];
if ( ! empty( $attributes['iconColor'] ) ) $icon_styles['color'] = $attributes['iconColor'];
if ( ! empty( $attributes['iconBgColor'] ) ) $icon_styles['background-color'] = $attributes['iconBgColor'];
if ( ! empty( $attributes['iconSize'] ) ) $icon_styles['font-size'] = $H::ensure_unit( $attributes['iconSize'] );
if ( ! empty( $attributes['iconBoxSize'] ) ) { $b = $H::ensure_unit( $attributes['iconBoxSize'] ); $icon_styles['min-width'] = $b; $icon_styles['height'] = $b; }
$icon_styles = array_merge( $icon_styles, $dims( $attributes['iconBorderRadius'] ?? [], 'radius' ), $dims( $attributes['iconMargin'] ?? [], 'margin' ), $shadow( $attributes['iconBoxShadow'] ?? [] ) );
if ( ! empty( $attributes['iconBorder'] ) ) $icon_styles = array_merge( $icon_styles, $H::border_to_css_props( $attributes['iconBorder'] ) );
$icon_color_fill = ! empty( $attributes['iconColor'] ) ? [ 'fill' => $attributes['iconColor'] ] : [];
$icon_rotate = ( isset( $attributes['iconRotate'] ) && '' !== $attributes['iconRotate'] ) ? [ 'transform' => 'rotate(' . $attributes['iconRotate'] . 'deg)' ] : [];
$icon_hover = ! empty( $attributes['iconHoverColor'] ) ? [ 'color' => $attributes['iconHoverColor'] ] : [];
$icon_hover_fill = ! empty( $attributes['iconHoverColor'] ) ? [ 'fill' => $attributes['iconHoverColor'] ] : [];
$icon_hover_bg = ! empty( $attributes['iconHoverBgColor'] ) ? [ 'background-color' => $attributes['iconHoverBgColor'] ] : [];

// Image
$image_box = [];
if ( ! empty( $attributes['imageBgColor'] ) ) $image_box['background-color'] = $attributes['imageBgColor'];
if ( ! empty( $attributes['imageBoxSize'] ) ) { $b = $H::ensure_unit( $attributes['imageBoxSize'] ); $image_box['min-width'] = $b; $image_box['height'] = $b; }
$image_box = array_merge( $image_box, $dims( $attributes['imageBoxBorderRadius'] ?? [], 'radius' ), $dims( $attributes['imagePadding'] ?? [], 'padding' ), $dims( $attributes['imageMargin'] ?? [], 'margin' ) );
if ( ! empty( $attributes['imageBorder'] ) ) $image_box = array_merge( $image_box, $H::border_to_css_props( $attributes['imageBorder'] ) );
$image_img = [];
if ( ! empty( $attributes['imageSize'] ) ) { $b = $H::ensure_unit( $attributes['imageSize'] ); $image_img['width'] = $b; $image_img['height'] = $b; }
$image_img = array_merge( $image_img, $dims( $attributes['imageBorderRadius'] ?? [], 'radius' ) );
$image_hover_box = ! empty( $attributes['imageHoverBgColor'] ) ? [ 'background-color' => $attributes['imageHoverBgColor'] ] : [];

// Number
$number_styles = $typo( $attributes['numberTypography'] ?? [] );
if ( ! $num_grad && ! empty( $attributes['numberColor'] ) ) $number_styles['color'] = $attributes['numberColor'];
if ( ! empty( $attributes['numberBgColor'] ) ) $number_styles['background'] = $attributes['numberBgColor'];
if ( ! empty( $attributes['numberAlignment'] ) ) $number_styles['text-align'] = $attributes['numberAlignment'];
$number_styles = array_merge( $number_styles, $dims( $attributes['numberPadding'] ?? [], 'padding' ), $dims( $attributes['numberMargin'] ?? [], 'margin' ) );

// Title
$title_styles = $typo( $attributes['titleTypography'] ?? [] );
if ( ! empty( $attributes['titleColor'] ) ) $title_styles['color'] = $attributes['titleColor'];
$title_styles = array_merge( $title_styles, $dims( $attributes['titleMargin'] ?? [], 'margin' ) );
$title_hover = ! empty( $attributes['titleHoverColor'] ) ? [ 'color' => $attributes['titleHoverColor'] ] : [];

// Description
$desc_styles = $typo( $attributes['descTypography'] ?? [] );
if ( ! empty( $attributes['descColor'] ) ) $desc_styles['color'] = $attributes['descColor'];
$desc_styles = array_merge( $desc_styles, $dims( $attributes['descMargin'] ?? [], 'margin' ) );
$desc_hover = ! empty( $attributes['descHoverColor'] ) ? [ 'color' => $attributes['descHoverColor'] ] : [];

// Features
$feat_text = $typo( $attributes['featureTypography'] ?? [] );
if ( ! empty( $attributes['featureTextColor'] ) ) $feat_text['color'] = $attributes['featureTextColor'];
$feat_item = $dims( $attributes['featureMargin'] ?? [], 'margin' );
$feat_icon = ! empty( $attributes['featureIconColor'] ) ? [ 'color' => $attributes['featureIconColor'] ] : [];
$feat_icon_fill = ! empty( $attributes['featureIconColor'] ) ? [ 'fill' => $attributes['featureIconColor'] ] : [];
$feat_icon_gap = ( isset( $attributes['featureIconGap'] ) && '' !== $attributes['featureIconGap'] ) ? [ 'margin-right' => $H::ensure_unit( $attributes['featureIconGap'] ) ] : [];
$feat_icon_size_i = ( ! empty( $attributes['featureIconSize'] ) ) ? [ 'font-size' => $H::ensure_unit( $attributes['featureIconSize'] ) ] : [];
$feat_icon_size_svg = ( ! empty( $attributes['featureIconSize'] ) ) ? [ 'width' => $H::ensure_unit( $attributes['featureIconSize'] ), 'height' => $H::ensure_unit( $attributes['featureIconSize'] ) ] : [];
$feat_text_hover = ! empty( $attributes['featureTextColorHover'] ) ? [ 'color' => $attributes['featureTextColorHover'] ] : [];
$feat_icon_hover = ! empty( $attributes['featureIconColorHover'] ) ? [ 'color' => $attributes['featureIconColorHover'], 'fill' => $attributes['featureIconColorHover'] ] : [];

// Button
$rm_icon = [];
if ( ! empty( $attributes['readMoreIconColor'] ) ) $rm_icon['color'] = $attributes['readMoreIconColor'];
$rm_icon_fill = ! empty( $attributes['readMoreIconColor'] ) ? [ 'fill' => $attributes['readMoreIconColor'] ] : [];
$rm_icon_box = [];
if ( ! empty( $attributes['readMoreIconBgColor'] ) ) $rm_icon_box['background-color'] = $attributes['readMoreIconBgColor'];
$rm_icon_box = array_merge( $rm_icon_box, $dims( $attributes['readMoreIconPadding'] ?? [], 'padding' ), $dims( $attributes['readMoreIconBorderRadius'] ?? [], 'radius' ) );
$rm_icon_size_svg = ( ! empty( $attributes['readMoreIconSize'] ) ) ? [ 'width' => $H::ensure_unit( $attributes['readMoreIconSize'] ), 'height' => $H::ensure_unit( $attributes['readMoreIconSize'] ) ] : [];
$rm_icon_size_i = ( ! empty( $attributes['readMoreIconSize'] ) ) ? [ 'font-size' => $H::ensure_unit( $attributes['readMoreIconSize'] ) ] : [];

$rm_text = $typo( $attributes['readMoreTypography'] ?? [] );
if ( ! empty( $attributes['readMoreTextColor'] ) ) $rm_text['color'] = $attributes['readMoreTextColor'];
if ( ! empty( $attributes['readMoreTextBgColor'] ) ) $rm_text['background-color'] = $attributes['readMoreTextBgColor'];
$rm_text = array_merge( $rm_text, $dims( $attributes['readMoreTextBorderRadius'] ?? [], 'radius' ), $dims( $attributes['readMoreTextPadding'] ?? [], 'padding' ) );
if ( ! empty( $attributes['readMoreTextBorder'] ) ) $rm_text = array_merge( $rm_text, $H::border_to_css_props( $attributes['readMoreTextBorder'] ) );
$rm_wrap = $dims( $attributes['readMoreMargin'] ?? [], 'margin' );
$rm_text_hover = [];
if ( ! empty( $attributes['readMoreTextColorHover'] ) ) $rm_text_hover['color'] = $attributes['readMoreTextColorHover'];
if ( ! empty( $attributes['readMoreBgHover'] ) ) $rm_text_hover['background'] = $attributes['readMoreBgHover'];
if ( ! empty( $attributes['readMoreHoverBorderColor'] ) ) $rm_text_hover['border-color'] = $attributes['readMoreHoverBorderColor'];

wp_enqueue_style( $style_handle );
$H::add_custom_style( $style_handle, $selector, '', [
	'.eelfg-icon-box'                              => $H::get_inline_styles( $item ),
	'.eelfg-icon-box.right, ' . $selector . ' .eelfg-icon-box.left' => $H::get_inline_styles( $item_flex ),
	'.eelfg-icon-box:hover::before'               => $H::get_inline_styles( $item_hover_before ),
	'.eelfg-icon-box:hover'                       => $H::get_inline_styles( $item_hover ),
	'.eelfg-icon-box .eelfg-icon'                 => $H::get_inline_styles( $icon_styles ),
	'.eelfg-icon-box .eelfg-icon svg, ' . $selector . ' .eelfg-icon-box .eelfg-icon svg path' => $H::get_inline_styles( $icon_color_fill ),
	'.eelfg-icon-box .eelfg-icon svg, ' . $selector . ' .eelfg-icon-box .eelfg-icon i' => $H::get_inline_styles( $icon_rotate ),
	'.eelfg-icon-box:hover .eelfg-icon'           => $H::get_inline_styles( array_merge( $icon_hover, $icon_hover_bg ) ),
	'.eelfg-icon-box:hover .eelfg-icon svg path'  => $H::get_inline_styles( $icon_hover_fill ),
	'.eelfg-icon-box .eelfg-icon.eelfg-icon-image' => $H::get_inline_styles( $image_box ),
	'.eelfg-icon-box .eelfg-icon.eelfg-icon-image img' => $H::get_inline_styles( $image_img ),
	'.eelfg-icon-box:hover .eelfg-icon.eelfg-icon-image' => $H::get_inline_styles( $image_hover_box ),
	'.eelfg-pro-number'                           => $H::get_inline_styles( $number_styles ),
	'.eelfg-icon-box-title'                       => $H::get_inline_styles( $title_styles ),
	'.eelfg-icon-box:hover .eelfg-icon-box-title' => $H::get_inline_styles( $title_hover ),
	'.eelfg-icon-box-description'                 => $H::get_inline_styles( $desc_styles ),
	'.eelfg-icon-box:hover .eelfg-icon-box-description' => $H::get_inline_styles( $desc_hover ),
	'.eelfg-icon-box .eelfg-feature-item span'    => $H::get_inline_styles( $feat_text ),
	'.eelfg-icon-box .eelfg-feature-item'         => $H::get_inline_styles( $feat_item ),
	'.eelfg-icon-box .eelfg-feature-item i'       => $H::get_inline_styles( array_merge( $feat_icon, $feat_icon_size_i, $feat_icon_gap ) ),
	'.eelfg-icon-box .eelfg-feature-item svg'     => $H::get_inline_styles( array_merge( $feat_icon_fill, $feat_icon_size_svg, $feat_icon_gap ) ),
	'.eelfg-icon-box:hover .eelfg-feature-item span' => $H::get_inline_styles( $feat_text_hover ),
	'.eelfg-icon-box:hover .eelfg-feature-item i, ' . $selector . ' .eelfg-icon-box:hover .eelfg-feature-item svg path' => $H::get_inline_styles( $feat_icon_hover ),
	'.eelfg-read-more-icon'                       => $H::get_inline_styles( array_merge( $rm_icon, $rm_icon_box ) ),
	'.eelfg-read-more-icon svg, ' . $selector . ' .eelfg-read-more-icon svg path' => $H::get_inline_styles( $rm_icon_fill ),
	'.eelfg-read-more-icon svg'                   => $H::get_inline_styles( $rm_icon_size_svg ),
	'.eelfg-read-more-icon i'                     => $H::get_inline_styles( $rm_icon_size_i ),
	'.eelfg-read-more-text'                       => $H::get_inline_styles( $rm_text ),
	'.eelfg-read-more'                            => $H::get_inline_styles( $rm_wrap ),
	'.eelfg-read-more-text:hover'                 => $H::get_inline_styles( $rm_text_hover ),
] );

// ---------------------------------------------------------------------------
// Default icons (used when no custom icon is selected).
// ---------------------------------------------------------------------------
$icon_star  = '<svg viewBox="0 0 24 24" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"><path d="M12 2l3 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.9 21l1.2-6.8-5-4.9 6.9-1z"/></svg>';
$icon_check = '<svg viewBox="0 0 24 24" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"><path d="M9 16.2l-3.5-3.5L4 14.2l5 5 11-11-1.5-1.5z"/></svg>';
$icon_arrow = '<svg viewBox="0 0 24 24" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"><path d="M7 17l9.2-9.2M9 7h8v8"/></svg>';

$render_icon_i = function ( $val, $fallback_svg ) {
	if ( ! empty( $val ) && 'none' !== $val ) {
		return '<i class="eelfg-icon ' . esc_attr( $val ) . '" aria-hidden="true"></i>';
	}
	return $fallback_svg;
};

// Build the icon / image markup.
ob_start();
if ( 'image' === $icon_type && ! empty( $icon_image['url'] ) ) {
	echo '<span class="eelfg-icon eelfg-icon-image"><img src="' . esc_url( $icon_image['url'] ) . '" alt="' . esc_attr( $icon_image['alt'] ?? '' ) . '"></span>';
} elseif ( 'icon' === $icon_type ) {
	echo '<span class="eelfg-icon ' . esc_attr( $grad_border ) . '">' . $render_icon_i( $icon, $icon_star ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in closure / static SVG.
}
$icon_markup = ob_get_clean();

$number_markup = '';
if ( '' !== $number ) {
	$number_markup = '<div class="eelfg-pro-number ' . ( $num_grad ? 'eelfg-gradient-yes' : '' ) . '">' . esc_html( $number ) . '</div>';
}

$title_markup = '';
if ( '' !== $title ) {
	$title_markup = '<' . tag_escape( $title_tag ) . ' class="eelfg-icon-box-title">' . wp_kses_post( $title ) . '</' . tag_escape( $title_tag ) . '>';
}

$desc_markup = '';
if ( '' !== $description ) {
	$desc_markup = '<div class="eelfg-icon-box-description">' . wp_kses_post( $description ) . '</div>';
}

// Read-more markup.
$read_more_markup = '';
if ( $show_more ) {
	ob_start();
	?>
	<div class="eelfg-read-more <?php echo esc_attr( $btn_align2 ); ?>">
		<?php if ( $link ) : ?>
			<a class="eelfg-read-more-link" href="<?php echo esc_url( $link ); ?>"<?php echo $target . $nofollow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static. ?>>
		<?php endif; ?>
		<?php if ( 'read_icon' === $more_type ) : ?>
			<span class="eelfg-read-more-icon"><?php echo $render_icon_i( $more_icon, $icon_arrow ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
		<?php elseif ( 'read_text' === $more_type && '' !== $more_text ) : ?>
			<span class="eelfg-read-more-text">
				<?php echo esc_html( $more_text ); ?>
				<?php if ( $more_t_show ) : ?>
					<span class="eelfg-read-more-text-icon"><?php echo $render_icon_i( $more_t_icon, $icon_arrow ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<?php endif; ?>
			</span>
		<?php elseif ( 'read_icon_to_text' === $more_type && '' !== $more_text ) : ?>
			<span class="eelfg-read-more-text eelfg-icon-to-text">
				<span class="eelfg-text"><?php echo esc_html( $more_text ); ?></span>
				<?php if ( $more_t_show ) : ?>
					<span class="eelfg-read-more-text-icon"><?php echo $render_icon_i( $more_t_icon, $icon_arrow ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<?php endif; ?>
			</span>
		<?php endif; ?>
		<?php if ( $link ) : ?></a><?php endif; ?>
	</div>
	<?php
	$read_more_markup = ob_get_clean();
}

$box_link_markup = $box_link ? '<a class="eelfg-box-link" href="' . esc_url( $link ) . '"' . $target . $nofollow . '></a>' : '';
$is_lr = in_array( $direction, [ 'left', 'right' ], true );
$box_classes = 'eelfg-icon-box ' . esc_attr( $direction ) . ' eelfg-bf-' . esc_attr( $hover_dir ) . ' eelfg-info-' . esc_attr( $skin );
?>
<div <?php echo wp_kses_post( $block_wrap_attr ); ?>>
	<?php if ( 'skin-2' === $skin ) : ?>
		<div class="<?php echo esc_attr( $box_classes ); ?>">
			<?php echo $box_link_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<div class="eelfg-info-heading">
				<?php echo $icon_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo $number_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php if ( $is_lr ) : ?><div class="eelfg-title-content-wrap"><?php endif; ?>
				<?php echo $title_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php if ( $is_lr ) : ?></div><?php endif; ?>
			</div>
			<div class="eelfg-info-content">
				<?php echo $desc_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php foreach ( $features as $f ) : ?>
					<div class="eelfg-feature-item">
						<?php echo $render_icon_i( isset( $f['icon'] ) ? $f['icon'] : '', $icon_check ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span><?php echo esc_html( isset( $f['text'] ) ? $f['text'] : '' ); ?></span>
					</div>
				<?php endforeach; ?>
				<?php echo $read_more_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
	<?php else : ?>
		<div class="<?php echo esc_attr( $box_classes ); ?>">
			<?php echo $box_link_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo $icon_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo $number_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php if ( $is_lr ) : ?><div class="eelfg-title-content-wrap"><?php endif; ?>
			<?php echo $title_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo $desc_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo $read_more_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php if ( $is_lr ) : ?></div><?php endif; ?>
		</div>
	<?php endif; ?>
</div>
