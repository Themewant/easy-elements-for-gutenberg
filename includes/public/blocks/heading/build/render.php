<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Local template/iteration variables.

/**
 * Server-side render for the Heading block.
 *
 * Mirrors the markup of the Elementor "Heading" widget
 * (easy-elements/widgets/heading) so the shared CSS applies on the front end.
 * Element classes use this plugin's "eelfg-" prefix. Animation features that
 * required external JS in the Elementor widget are intentionally omitted.
 */

$H = '\EELFG\Frontend\Helper';

$unique_id = ! empty( $attributes['blockId'] ) ? $attributes['blockId'] : 'eelfg-heading-' . substr( md5( wp_json_encode( $attributes ) ), 0, 6 );

$allowed_tags = [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span' ];
$tag          = isset( $attributes['titleTag'] ) && in_array( $attributes['titleTag'], $allowed_tags, true ) ? $attributes['titleTag'] : 'h2';

$title_raw   = isset( $attributes['title'] ) ? $attributes['title'] : '';
$description = isset( $attributes['description'] ) ? $attributes['description'] : '';
$sub_title   = isset( $attributes['subTitle'] ) ? $attributes['subTitle'] : '';
$sub_type    = isset( $attributes['subHeadingType'] ) ? $attributes['subHeadingType'] : 'none';
$sub_icon    = isset( $attributes['subHeadingIcon'] ) ? $attributes['subHeadingIcon'] : '';
$sub_image   = isset( $attributes['subHeadingImage'] ) && is_array( $attributes['subHeadingImage'] ) ? $attributes['subHeadingImage'] : [];
$icon_dir    = isset( $attributes['iconDirection'] ) ? $attributes['iconDirection'] : 'top';
$sub_grad    = ! empty( $attributes['showGradientBorder'] ) ? 'eelfg-sub-gradient-border' : '';

$show_border = ! empty( $attributes['showBorderTitle'] );
$border_pos  = isset( $attributes['borderPosition'] ) ? $attributes['borderPosition'] : 'eelfg-title-start';
$grad_title  = ! empty( $attributes['showGradientTitle'] ) ? ' eelfg-gradient-title' : '';
$image_fill_on = ! empty( $attributes['enableTitleImageFill'] ) && ! empty( $attributes['titleImageFill']['url'] );
$image_fill  = $image_fill_on ? ' eelfg-image-fill' : '';
$title_extra = $grad_title . $image_fill;
$watermark   = isset( $attributes['waterMark'] ) ? $attributes['waterMark'] : '';
$wm_class    = '' !== $watermark ? 'has-watermark' : '';

$sep_type    = isset( $attributes['separatorType'] ) ? $attributes['separatorType'] : 'none';
$sep_pos     = isset( $attributes['separatorPosition'] ) ? $attributes['separatorPosition'] : 'below';
$sep_icon    = isset( $attributes['selectIcon'] ) ? $attributes['selectIcon'] : '';
$sep_image   = isset( $attributes['sepImage'] ) && is_array( $attributes['sepImage'] ) ? $attributes['sepImage'] : [];

$link        = isset( $attributes['linkUrl'] ) ? $attributes['linkUrl'] : '';
$target      = ! empty( $attributes['linkTarget'] ) ? ' target="_blank"' : '';
$nofollow    = ! empty( $attributes['linkNofollow'] ) ? ' rel="nofollow"' : '';

// Highlight: {{text}} -> <span>text</span>.
$title = preg_replace_callback( '/\{\{(.*?)\}\}/', function ( $m ) {
	return '<span>' . esc_html( trim( $m[1] ) ) . '</span>';
}, $title_raw );
if ( '' !== $link ) {
	$title = '<a href="' . esc_url( $link ) . '"' . $target . $nofollow . '>' . $title . '</a>';
}

$wrap_class = trim( ( $show_border ? $border_pos . ' ' : '' ) . $wm_class );
$block_wrap_attr = get_block_wrapper_attributes( array(
	'class' => 'eelfg-block eelfg-heading-block-wrap ' . $unique_id,
) );
if ( empty( $block_wrap_attr ) ) {
	$block_wrap_attr = 'class="eelfg-block eelfg-heading-block-wrap ' . esc_attr( $unique_id ) . '"';
}

// ---------------------------------------------------------------------------
// Inline styles.
// ---------------------------------------------------------------------------
$selector     = '.eelfg-heading-block-wrap.' . $unique_id;
$style_handle = 'eelfg-heading-style';

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
$tshadow = function ( $obj ) use ( $H ) {
	if ( empty( $obj ) || ! is_array( $obj ) ) return [];
	$x = $obj['x'] ?? ''; $y = $obj['y'] ?? ''; $blur = $obj['blur'] ?? ''; $c = $obj['color'] ?? '';
	if ( '' === $x && '' === $y && '' === $blur && '' === $c ) return [];
	$val = $H::ensure_unit( '' === $x ? 0 : $x ) . ' ' . $H::ensure_unit( '' === $y ? 0 : $y ) . ' ' . $H::ensure_unit( '' === $blur ? 0 : $blur ) . ' ' . ( '' !== $c ? $c : 'rgba(0,0,0,0.3)' );
	return [ 'text-shadow' => $val ];
};
$shadow = function ( $obj ) use ( $H ) {
	if ( empty( $obj ) || ! is_array( $obj ) ) return [];
	$x = (int) ( $obj['x'] ?? 0 ); $y = (int) ( $obj['y'] ?? 0 ); $b = (int) ( $obj['b'] ?? 0 ); $s = (int) ( $obj['s'] ?? 0 );
	$c = $obj['c'] ?? '';
	$transparent = in_array( str_replace( ' ', '', (string) $c ), [ '', 'rgba(0,0,0,0)' ], true );
	if ( 0 === $x && 0 === $y && 0 === $b && 0 === $s && $transparent ) return [];
	return [ 'box-shadow' => $H::box_shadow_to_css( $obj ) ];
};

// Heading wrapper alignment.
$heading_styles = [];
if ( ! empty( $attributes['align'] ) ) $heading_styles['text-align'] = $attributes['align'];

// Title.
$title_styles = $typo( $attributes['titleTypography'] ?? [] );
if ( empty( $grad_title ) && ! empty( $attributes['titleColor'] ) ) $title_styles['color'] = $attributes['titleColor'];
if ( isset( $attributes['titleOpacity'] ) && '' !== $attributes['titleOpacity'] ) $title_styles['opacity'] = $attributes['titleOpacity'];
if ( isset( $attributes['titleStrokeWidth'] ) && '' !== $attributes['titleStrokeWidth'] ) $title_styles['-webkit-text-stroke-width'] = $H::ensure_unit( $attributes['titleStrokeWidth'] );
if ( ! empty( $attributes['titleStrokeColor'] ) ) $title_styles['-webkit-text-stroke-color'] = $attributes['titleStrokeColor'];
if ( ! empty( $attributes['titleBlendMode'] ) ) $title_styles['mix-blend-mode'] = $attributes['titleBlendMode'];
$title_styles = array_merge( $title_styles, $tshadow( $attributes['titleTextShadow'] ?? [] ), $dims( $attributes['titleMargin'] ?? [], 'margin' ), $dims( $attributes['titlePadding'] ?? [], 'padding' ) );

// Gradient text fill (background clipped to text via CSS).
$title_gradient = [];
if ( ! empty( $grad_title ) ) {
	if ( ! empty( $attributes['titleFillGradient'] ) ) {
		$title_gradient['background'] = $attributes['titleFillGradient'];
	} elseif ( ! empty( $attributes['titleFillColor'] ) ) {
		$title_gradient['background'] = $attributes['titleFillColor'];
	}
}

// Image fill (text mask).
$image_fill_styles = [];
if ( $image_fill_on ) {
	$image_fill_styles['background-image'] = 'url(' . esc_url( $attributes['titleImageFill']['url'] ) . ')';
}

// Border (side bar).
$border_styles = [];
if ( ! empty( $attributes['borderColor'] ) ) $border_styles['border-color'] = $attributes['borderColor'];
$border_styles = array_merge( $border_styles, $dims( $attributes['borderPadding'] ?? [], 'padding' ) );
$border_sel = '.eelfg-full-title-start, ' . $selector . ' .eelfg-title-start .eelfg-title, ' . $selector . ' .eelfg-full-title-end, ' . $selector . ' .eelfg-title-end .eelfg-title';

// Sub heading.
$sub_styles = $typo( $attributes['subTypography'] ?? [] );
$sub_span = [];
if ( ! empty( $attributes['subColor'] ) ) $sub_span['color'] = $attributes['subColor'];
$sub_span = array_merge( $sub_span, $typo( $attributes['subTypography'] ?? [] ), $dims( $attributes['subIconMargin'] ?? [], 'margin' ) );
$sub_box = [];
if ( ! empty( $attributes['subBgGradient'] ) ) {
	$sub_box['background'] = $attributes['subBgGradient'];
} elseif ( ! empty( $attributes['subBgColor'] ) ) {
	$sub_box['background'] = $attributes['subBgColor'];
}
$sub_box = array_merge( $sub_box, $dims( $attributes['subBorderRadius'] ?? [], 'radius' ), $dims( $attributes['subPadding'] ?? [], 'padding' ), $dims( $attributes['subMargin'] ?? [], 'margin' ) );
if ( ! empty( $attributes['subBorder'] ) ) $sub_box = array_merge( $sub_box, $H::border_to_css_props( $attributes['subBorder'] ) );
$sub_icon_color = [];
if ( ! empty( $attributes['iconColor'] ) ) $sub_icon_color = [ 'color' => $attributes['iconColor'], 'fill' => $attributes['iconColor'] ];
$sub_icon_size = ( ! empty( $attributes['subIconSize'] ) ) ? [ 'font-size' => $H::ensure_unit( $attributes['subIconSize'] ) ] : [];
$sub_img = [];
if ( ! empty( $attributes['subImageWidth'] ) ) $sub_img['width'] = $H::ensure_unit( $attributes['subImageWidth'] );
if ( ! empty( $attributes['subImageHeight'] ) ) $sub_img['height'] = $H::ensure_unit( $attributes['subImageHeight'] );
$sub_img = array_merge( $sub_img, $dims( $attributes['subImageRadius'] ?? [], 'radius' ) );
$sub_gap_lr = ( ! empty( $attributes['subIconGap'] ) ) ? [ 'gap' => $H::ensure_unit( $attributes['subIconGap'] ) ] : [];

// Gradient-border variables.
$grad_vars = [];
if ( ! empty( $sub_grad ) ) {
	if ( ! empty( $attributes['gradientColor1'] ) ) $grad_vars['--eelfg-grad-color-1'] = $attributes['gradientColor1'];
	if ( ! empty( $attributes['gradientColor2'] ) ) $grad_vars['--eelfg-grad-color-2'] = $attributes['gradientColor2'];
	if ( ! empty( $attributes['gradientColor3'] ) ) $grad_vars['--eelfg-grad-color-3'] = $attributes['gradientColor3'];
	if ( isset( $attributes['gradientBorderRadius'] ) && '' !== $attributes['gradientBorderRadius'] ) $grad_vars['--eelfg-grad-border-radius'] = $H::ensure_unit( $attributes['gradientBorderRadius'] );
}
$grad_pad = $dims( $attributes['subGradientBorderPadding'] ?? [], 'padding' );

// Highlight.
$hl = $typo( $attributes['highlightTypography'] ?? [] );
if ( ! empty( $attributes['highlightColor'] ) ) $hl['color'] = $attributes['highlightColor'];
if ( ! empty( $attributes['highlightBgGradient'] ) ) {
	$hl['background'] = $attributes['highlightBgGradient'];
} elseif ( ! empty( $attributes['highlightBgColor'] ) ) {
	$hl['background'] = $attributes['highlightBgColor'];
}
$hl = array_merge( $hl, $dims( $attributes['highlightPadding'] ?? [], 'padding' ), $dims( $attributes['highlightMargin'] ?? [], 'margin' ), $dims( $attributes['highlightBorderRadius'] ?? [], 'radius' ) );

// Description.
$desc = $typo( $attributes['descTypography'] ?? [] );
if ( ! empty( $attributes['descColor'] ) ) $desc['color'] = $attributes['descColor'];
$desc = array_merge( $desc, $dims( $attributes['descMargin'] ?? [], 'margin' ), $dims( $attributes['descPadding'] ?? [], 'padding' ) );

// Separator.
$sep_bar = [];
if ( ! empty( $attributes['solidColor'] ) ) { $sep_bar['color'] = $attributes['solidColor']; $sep_bar['background-color'] = $attributes['solidColor']; }
if ( ! empty( $attributes['separatorBarWidth'] ) ) $sep_bar['width'] = $H::ensure_unit( $attributes['separatorBarWidth'] );
if ( ! empty( $attributes['separatorBarHeight'] ) ) $sep_bar['height'] = $H::ensure_unit( $attributes['separatorBarHeight'] );
$sep_bar = array_merge( $sep_bar, $dims( $attributes['separatorMargin'] ?? [], 'margin' ) );
$sep_icon_styles = [];
if ( ! empty( $attributes['separatorIconColor'] ) ) $sep_icon_styles = [ 'color' => $attributes['separatorIconColor'], 'fill' => $attributes['separatorIconColor'] ];
$sep_icon_size_i = ( ! empty( $attributes['separatorIconSize'] ) ) ? [ 'font-size' => $H::ensure_unit( $attributes['separatorIconSize'] ) ] : [];
$sep_icon_size_svg = ( ! empty( $attributes['separatorIconSize'] ) ) ? [ 'width' => $H::ensure_unit( $attributes['separatorIconSize'] ), 'height' => $H::ensure_unit( $attributes['separatorIconSize'] ) ] : [];
$sep_img_styles = ( ! empty( $attributes['separatorImageWidth'] ) ) ? [ 'width' => $H::ensure_unit( $attributes['separatorImageWidth'] ), 'height' => 'auto' ] : [];

// Watermark.
$wm = $typo( $attributes['wmTypography'] ?? [] );
if ( ! empty( $attributes['wmColor'] ) ) $wm['color'] = $attributes['wmColor'];
if ( ! empty( $attributes['wmStrokeColor'] ) ) $wm['-webkit-text-stroke-color'] = $attributes['wmStrokeColor'];
if ( isset( $attributes['wmStrokeWidth'] ) && '' !== $attributes['wmStrokeWidth'] ) $wm['-webkit-text-stroke-width'] = $H::ensure_unit( $attributes['wmStrokeWidth'] );
if ( ! empty( $attributes['wmFontSize'] ) ) $wm['font-size'] = $H::ensure_unit( $attributes['wmFontSize'] );
if ( ! empty( $attributes['wmBgGradient'] ) ) {
	$wm['background'] = $attributes['wmBgGradient'];
} elseif ( ! empty( $attributes['wmBgColor'] ) ) {
	$wm['background'] = $attributes['wmBgColor'];
}
$wm = array_merge( $wm, $tshadow( $attributes['wmTextShadow'] ?? [] ), $shadow( $attributes['wmBoxShadow'] ?? [] ), $dims( $attributes['wmBorderRadius'] ?? [], 'radius' ), $dims( $attributes['wmPadding'] ?? [], 'padding' ) );
if ( ! empty( $attributes['wmBorder'] ) ) $wm = array_merge( $wm, $H::border_to_css_props( $attributes['wmBorder'] ) );
if ( ! empty( $attributes['wmBlendMode'] ) ) $wm['mix-blend-mode'] = $attributes['wmBlendMode'];
if ( ! empty( $attributes['wmTop'] ) ) { $wm['top'] = $H::ensure_unit( $attributes['wmTop'] ); $wm['bottom'] = 'auto'; }
if ( ! empty( $attributes['wmLeft'] ) ) { $wm['left'] = $H::ensure_unit( $attributes['wmLeft'] ); $wm['right'] = 'auto'; }
if ( isset( $attributes['wmZIndex'] ) && '' !== $attributes['wmZIndex'] ) $wm['z-index'] = $attributes['wmZIndex'];
if ( isset( $attributes['wmOpacity'] ) && '' !== $attributes['wmOpacity'] ) $wm['opacity'] = $attributes['wmOpacity'];
if ( isset( $attributes['wmRotation'] ) && '' !== $attributes['wmRotation'] ) $wm['transform'] = 'translateY(-50%) rotate(' . $attributes['wmRotation'] . 'deg)';

wp_enqueue_style( $style_handle );
$H::add_custom_style( $style_handle, $selector, '', [
	'.eelfg-heading'                              => $H::get_inline_styles( $heading_styles ),
	'.eelfg-heading .eelfg-title'                 => $H::get_inline_styles( $title_styles ),
	'.eelfg-heading .eelfg-title.eelfg-gradient-title, ' . $selector . ' .eelfg-heading .eelfg-title.eelfg-gradient-title div' => $H::get_inline_styles( $title_gradient ),
	'.eelfg-heading .eelfg-title.eelfg-image-fill, ' . $selector . ' .eelfg-heading .eelfg-title.eelfg-image-fill div' => $H::get_inline_styles( $image_fill_styles ),
	$border_sel                                   => $H::get_inline_styles( $border_styles ),
	'.eelfg-sub-heading'                          => $H::get_inline_styles( $sub_box ),
	'.eelfg-sub-heading.left, ' . $selector . ' .eelfg-sub-heading.right' => $H::get_inline_styles( $sub_gap_lr ),
	'.eelfg-sub-heading span'                     => $H::get_inline_styles( $sub_span ),
	'.eelfg-sub-heading i, ' . $selector . ' .eelfg-sub-heading svg' => $H::get_inline_styles( array_merge( $sub_icon_color, $sub_icon_size ) ),
	'.eelfg-sub-heading img'                      => $H::get_inline_styles( $sub_img ),
	'.eelfg-sub-gradient-border'                  => $H::get_inline_styles( $grad_pad ),
	'.eelfg-sub-gradient-border::before'          => $H::get_inline_styles( $grad_vars ),
	'.eelfg-heading .eelfg-title span'            => $H::get_inline_styles( $hl ),
	'.eelfg-heading .eelfg-description'           => $H::get_inline_styles( $desc ),
	'.eelfg-separator-icon'                       => $H::get_inline_styles( $sep_bar ),
	'.eelfg-separator-icon-wrap i'                => $H::get_inline_styles( array_merge( $sep_icon_styles, $sep_icon_size_i ) ),
	'.eelfg-separator-icon-wrap svg'              => $H::get_inline_styles( array_merge( $sep_icon_styles, $sep_icon_size_svg ) ),
	'.eelfg-separator-img'                        => $H::get_inline_styles( $sep_img_styles ),
	'.eelfg-watermark'                            => $H::get_inline_styles( $wm ),
] );

// ---------------------------------------------------------------------------
// Markup helpers.
// ---------------------------------------------------------------------------
$icon_heart = '<svg viewBox="0 0 24 24" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"><path d="M12 21s-7-4.6-9.3-8.4C1 9.6 2.4 6 5.8 6c2 0 3.3 1.3 4.2 2.6C10.9 7.3 12.2 6 14.2 6c3.4 0 4.8 3.6 3.1 6.6C19 16.4 12 21 12 21z"/></svg>';

$render_separator = function () use ( $attributes, $sep_type, $sep_icon, $sep_image, $icon_heart ) {
	if ( in_array( $sep_type, [ 'dotted', 'solid' ], true ) ) {
		echo '<div class="eelfg-separator-icon ' . esc_attr( $sep_type ) . '"></div>';
	} elseif ( 'icon' === $sep_type ) {
		echo '<div class="eelfg-separator-icon-wrap">';
		echo ( ! empty( $sep_icon ) && 'none' !== $sep_icon ) ? '<i class="eelfg-icon ' . esc_attr( $sep_icon ) . '" aria-hidden="true"></i>' : $icon_heart; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>';
	} elseif ( 'image' === $sep_type && ! empty( $sep_image['url'] ) ) {
		echo '<div class="eelfg-separator-icon-wrap"><img src="' . esc_url( $sep_image['url'] ) . '" alt="" class="eelfg-separator-img" loading="lazy" decoding="async" /></div>';
	}
};
$has_sep = 'none' !== $sep_type;

// Sub-heading icon/image markup.
ob_start();
if ( 'icon' === $sub_type && ! empty( $sub_icon ) && 'none' !== $sub_icon ) {
	echo '<i class="eelfg-icon ' . esc_attr( $sub_icon ) . '" aria-hidden="true"></i>';
} elseif ( 'image' === $sub_type && ! empty( $sub_image['url'] ) ) {
	echo '<img src="' . esc_url( $sub_image['url'] ) . '" alt="' . esc_attr( $sub_image['alt'] ?? '' ) . '">';
}
$sub_icon_markup = ob_get_clean();
?>
<div <?php echo wp_kses_post( $block_wrap_attr ); ?>>
	<div class="eelfg-heading <?php echo esc_attr( $wrap_class ); ?>">
		<?php
		if ( $has_sep && 'top' === $sep_pos ) {
			$render_separator();
		}

		if ( '' !== $sub_title ) :
			?>
			<div class="eelfg-sub-heading <?php echo esc_attr( $icon_dir ); ?> <?php echo esc_attr( $sub_grad ); ?>">
				<?php echo $sub_icon_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span><?php echo wp_kses_post( $sub_title ); ?></span>
			</div>
			<?php
		endif;

		if ( $has_sep && 'above' === $sep_pos ) {
			$render_separator();
		}

		if ( '' !== trim( (string) $title_raw ) ) {
			printf(
				'<%1$s class="eelfg-title%2$s">%3$s</%1$s>',
				tag_escape( $tag ),
				esc_attr( $title_extra ),
				wp_kses_post( $title )
			);
		}

		if ( $has_sep && 'below' === $sep_pos ) {
			$render_separator();
		}

		if ( '' !== $description ) {
			echo '<div class="eelfg-description">' . wp_kses_post( $description ) . '</div>';
		}

		if ( $has_sep && 'bottom' === $sep_pos ) {
			$render_separator();
		}

		if ( '' !== $watermark ) {
			echo '<div class="eelfg-watermark">' . esc_html( $watermark ) . '</div>';
		}
		?>
	</div>
</div>
