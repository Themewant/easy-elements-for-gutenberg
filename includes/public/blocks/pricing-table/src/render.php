<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Local template/iteration variables.

/**
 * Server-side render for the Pricing Table block.
 *
 * Mirrors the markup produced by the Elementor "Pricing Table" widget
 * (easy-elements/widgets/pricing-table/pricing.php) so the shared CSS applies
 * identically on the front end. Element classes use this plugin's "eelfg-" prefix.
 *
 * $attributes, $content and $block are provided by register_block_type().
 */

$H = '\EELFG\Frontend\Helper';

$unique_id = ! empty( $attributes['blockId'] ) ? $attributes['blockId'] : 'eelfg-pricing-' . substr( md5( wp_json_encode( $attributes ) ), 0, 6 );

$skin_style   = isset( $attributes['skinStyle'] ) ? $attributes['skinStyle'] : 'skin1';
$title        = isset( $attributes['title'] ) ? $attributes['title'] : '';
$description  = isset( $attributes['description'] ) ? $attributes['description'] : '';
$on_sale      = ! empty( $attributes['onSale'] );
$currency     = isset( $attributes['currency'] ) ? $attributes['currency'] : '';
$currency_pos = isset( $attributes['currencyPlacement'] ) ? $attributes['currencyPlacement'] : 'left';
$period       = isset( $attributes['period'] ) ? $attributes['period'] : '';
$separator    = isset( $attributes['separator'] ) ? $attributes['separator'] : '';
$features     = isset( $attributes['features'] ) && is_array( $attributes['features'] ) ? $attributes['features'] : [];
$features_des = isset( $attributes['featuresDescription'] ) ? $attributes['featuresDescription'] : '';
$icon_style   = isset( $attributes['featureIconStyle'] ) ? $attributes['featureIconStyle'] : 'icon-only';
$is_featured  = ! empty( $attributes['isFeatured'] );
$ribbon_style = isset( $attributes['ribbonStyle'] ) ? $attributes['ribbonStyle'] : 'style1';
$featured_txt = isset( $attributes['featuredText'] ) ? $attributes['featuredText'] : '';
$ribbon_align = isset( $attributes['ribbonAlignment'] ) ? $attributes['ribbonAlignment'] : 'right';
$show_button  = ! empty( $attributes['showButton'] );
$button_pos   = isset( $attributes['buttonPosition'] ) ? $attributes['buttonPosition'] : 'after_features';

$block_wrap_attr = get_block_wrapper_attributes( array(
	'class' => 'eelfg-block eelfg-pricing-table-block-wrap ' . $unique_id,
) );

if ( empty( $block_wrap_attr ) ) {
	$block_wrap_attr = 'class="eelfg-block eelfg-pricing-table-block-wrap ' . esc_attr( $unique_id ) . '"';
}

// ---------------------------------------------------------------------------
// Inline styles (scoped to this block instance via $unique_id).
// ---------------------------------------------------------------------------
$selector     = '.eelfg-pricing-table-block-wrap.' . $unique_id;
$style_handle = 'eelfg-pricing-table-style';

/** Local helper: typography object -> CSS map. */
$typo = function ( $obj ) use ( $H ) {
	$out = [];
	if ( empty( $obj ) || ! is_array( $obj ) ) {
		return $out;
	}
	if ( ! empty( $obj['fontFamily'] ) ) $out['font-family'] = $obj['fontFamily'];
	if ( ! empty( $obj['fontSize'] ) ) $out['font-size'] = $H::ensure_unit( $obj['fontSize'] );
	if ( ! empty( $obj['fontWeight'] ) ) $out['font-weight'] = $obj['fontWeight'];
	if ( ! empty( $obj['fontStyle'] ) ) $out['font-style'] = $obj['fontStyle'];
	if ( ! empty( $obj['textTransform'] ) ) $out['text-transform'] = $obj['textTransform'];
	if ( ! empty( $obj['lineHeight'] ) ) $out['line-height'] = $obj['lineHeight'];
	if ( ! empty( $obj['letterSpacing'] ) ) $out['letter-spacing'] = $H::ensure_unit( $obj['letterSpacing'] );
	return $out;
};

/** Local helper: dimensions object -> padding / margin / radius CSS map. */
$dims = function ( $obj, $type ) use ( $H ) {
	$out = [];
	if ( empty( $obj ) || ! is_array( $obj ) ) {
		return $out;
	}
	if ( 'padding' === $type ) {
		$map = [ 'top' => 'padding-top', 'right' => 'padding-right', 'bottom' => 'padding-bottom', 'left' => 'padding-left' ];
	} elseif ( 'margin' === $type ) {
		$map = [ 'top' => 'margin-top', 'right' => 'margin-right', 'bottom' => 'margin-bottom', 'left' => 'margin-left' ];
	} else {
		$map = [ 'top' => 'border-top-left-radius', 'right' => 'border-top-right-radius', 'bottom' => 'border-bottom-right-radius', 'left' => 'border-bottom-left-radius' ];
	}
	foreach ( $map as $side => $css_prop ) {
		if ( isset( $obj[ $side ] ) && '' !== $obj[ $side ] ) {
			$out[ $css_prop ] = $H::ensure_unit( $obj[ $side ] );
		}
	}
	return $out;
};

/** Local helper: box-shadow object -> CSS map (skips empty/transparent shadows). */
$shadow = function ( $obj ) use ( $H ) {
	if ( empty( $obj ) || ! is_array( $obj ) ) {
		return [];
	}
	$x = (int) ( $obj['x'] ?? 0 );
	$y = (int) ( $obj['y'] ?? 0 );
	$b = (int) ( $obj['b'] ?? 0 );
	$s = (int) ( $obj['s'] ?? 0 );
	$c = $obj['c'] ?? '';
	$transparent = in_array( str_replace( ' ', '', (string) $c ), [ '', 'rgba(0,0,0,0)' ], true );
	if ( 0 === $x && 0 === $y && 0 === $b && 0 === $s && $transparent ) {
		return [];
	}
	return [ 'box-shadow' => $H::box_shadow_to_css( $obj ) ];
};

$header_align = ! empty( $attributes['headerAlignment'] ) ? [ 'text-align' => $attributes['headerAlignment'] ] : [];

// ---- Title ----
$title_styles = $typo( $attributes['titleTypography'] ?? [] );
if ( ! empty( $attributes['titleColor'] ) ) $title_styles['color'] = $attributes['titleColor'];
if ( ! empty( $attributes['titleBgColor'] ) ) $title_styles['background-color'] = $attributes['titleBgColor'];
$title_styles = array_merge( $title_styles, $header_align, $dims( $attributes['titleBorderRadius'] ?? [], 'radius' ), $dims( $attributes['titlePadding'] ?? [], 'padding' ), $dims( $attributes['titleMargin'] ?? [], 'margin' ) );
if ( ! empty( $attributes['titleBorder'] ) ) $title_styles = array_merge( $title_styles, $H::border_to_css_props( $attributes['titleBorder'] ) );

// ---- Description ----
$desc_styles = $typo( $attributes['descriptionTypography'] ?? [] );
if ( ! empty( $attributes['descriptionColor'] ) ) $desc_styles['color'] = $attributes['descriptionColor'];
$desc_styles = array_merge( $desc_styles, $header_align, $dims( $attributes['descriptionPadding'] ?? [], 'padding' ), $dims( $attributes['descriptionMargin'] ?? [], 'margin' ) );
if ( ! empty( $attributes['descriptionBorder'] ) ) $desc_styles = array_merge( $desc_styles, $H::border_to_css_props( $attributes['descriptionBorder'] ) );

// ---- Price ----
$price_wrap_styles = array_merge( $header_align, $dims( $attributes['priceMargin'] ?? [], 'margin' ) );

$amount_styles = $typo( $attributes['priceTypography'] ?? [] );
if ( ! empty( $attributes['priceColor'] ) ) $amount_styles['color'] = $attributes['priceColor'];

$sale_styles = $typo( $attributes['salePriceTypography'] ?? [] );
if ( ! empty( $attributes['salePriceColor'] ) ) $sale_styles['color'] = $attributes['salePriceColor'];

// ---- Period ----
$period_styles = $typo( $attributes['periodTypography'] ?? [] );
if ( ! empty( $attributes['periodColor'] ) ) $period_styles['color'] = $attributes['periodColor'];
$period_styles = array_merge( $period_styles, $dims( $attributes['periodMargin'] ?? [], 'margin' ) );

// ---- Currency ----
$currency_styles = $typo( $attributes['currencyTypography'] ?? [] );
if ( ! empty( $attributes['currencyColor'] ) ) $currency_styles['color'] = $attributes['currencyColor'];
$currency_styles = array_merge( $currency_styles, $dims( $attributes['currencyMargin'] ?? [], 'margin' ) );
if ( isset( $attributes['currencyVerticalPosition'] ) && '' !== $attributes['currencyVerticalPosition'] ) {
	$currency_styles['display']   = 'inline-block';
	$currency_styles['transform'] = 'translateY(' . $H::ensure_unit( $attributes['currencyVerticalPosition'] ) . ')';
}

// ---- Features description ----
$fdesc_styles = $typo( $attributes['featuresDescriptionTypography'] ?? [] );
if ( ! empty( $attributes['featuresDescriptionColor'] ) ) $fdesc_styles['color'] = $attributes['featuresDescriptionColor'];
$fdesc_styles = array_merge( $fdesc_styles, $dims( $attributes['featuresDescriptionPadding'] ?? [], 'padding' ), $dims( $attributes['featuresDescriptionMargin'] ?? [], 'margin' ) );
if ( ! empty( $attributes['featuresDescriptionBorder'] ) ) $fdesc_styles = array_merge( $fdesc_styles, $H::border_to_css_props( $attributes['featuresDescriptionBorder'] ) );

// ---- Features list ----
$features_text_styles = $typo( $attributes['featuresTextTypography'] ?? [] );
if ( ! empty( $attributes['featuresTextColor'] ) ) $features_text_styles['color'] = $attributes['featuresTextColor'];
$features_text_styles = array_merge( $features_text_styles, $dims( $attributes['featuresPadding'] ?? [], 'padding' ), $dims( $attributes['featuresMargin'] ?? [], 'margin' ) );
if ( ! empty( $attributes['featuresBorder'] ) ) $features_text_styles = array_merge( $features_text_styles, $H::border_to_css_props( $attributes['featuresBorder'] ) );
if ( isset( $attributes['featuresIconGap'] ) && '' !== $attributes['featuresIconGap'] ) {
	$features_text_styles['display']     = 'flex';
	$features_text_styles['align-items'] = 'center';
	$features_text_styles['gap']         = $H::ensure_unit( $attributes['featuresIconGap'] );
}

$features_icon_styles = [];
if ( ! empty( $attributes['featuresIconColor'] ) ) $features_icon_styles['color'] = $attributes['featuresIconColor'];
$features_icon_fill = [];
if ( ! empty( $attributes['featuresIconColor'] ) ) $features_icon_fill['fill'] = $attributes['featuresIconColor'];

// ---- Feature icon (box variants) ----
$feat_icon_size_svg = [];
$feat_icon_size_i   = [];
if ( ! empty( $attributes['featureIconSize'] ) ) {
	$fis = $H::ensure_unit( $attributes['featureIconSize'] );
	$feat_icon_size_svg = [ 'width' => $fis, 'height' => $fis ];
	$feat_icon_size_i   = [ 'font-size' => $fis ];
}
$feat_icon_box_styles = array_merge( $dims( $attributes['featureIconPadding'] ?? [], 'padding' ), $dims( $attributes['featureIconBorderRadius'] ?? [], 'radius' ) );
$feat_icon_bg_styles  = $feat_icon_box_styles;
if ( ! empty( $attributes['featureIconBgColor'] ) ) $feat_icon_bg_styles['background'] = $attributes['featureIconBgColor'];
$feat_icon_border_styles = $feat_icon_box_styles;
if ( ! empty( $attributes['featureIconBorder'] ) ) $feat_icon_border_styles = array_merge( $feat_icon_border_styles, $H::border_to_css_props( $attributes['featureIconBorder'] ) );

// ---- Ribbon ----
$ribbon_styles = $typo( $attributes['ribbonTypography'] ?? [] );
if ( ! empty( $attributes['ribbonColor'] ) ) $ribbon_styles['color'] = $attributes['ribbonColor'];
if ( ! empty( $attributes['ribbonBgColor'] ) ) $ribbon_styles['background'] = $attributes['ribbonBgColor'];
if ( 'style1' === $ribbon_style ) {
	$ribbon_styles = array_merge( $ribbon_styles, $dims( $attributes['ribbonPadding'] ?? [], 'padding' ), $dims( $attributes['ribbonBorderRadius'] ?? [], 'radius' ) );
}

// ---- Button ----
$button_styles = $typo( $attributes['buttonTypography'] ?? [] );
if ( ! empty( $attributes['buttonTextColor'] ) ) $button_styles['color'] = $attributes['buttonTextColor'];
if ( ! empty( $attributes['buttonBgColor'] ) ) $button_styles['background-color'] = $attributes['buttonBgColor'];
$button_styles = array_merge( $button_styles, $dims( $attributes['buttonBorderRadius'] ?? [], 'radius' ), $dims( $attributes['buttonPadding'] ?? [], 'padding' ), $dims( $attributes['buttonMargin'] ?? [], 'margin' ), $shadow( $attributes['buttonBoxShadow'] ?? [] ) );
if ( ! empty( $attributes['buttonBorder'] ) ) $button_styles = array_merge( $button_styles, $H::border_to_css_props( $attributes['buttonBorder'] ) );

$button_hover_styles = [];
if ( ! empty( $attributes['buttonTextColorHover'] ) ) $button_hover_styles['color'] = $attributes['buttonTextColorHover'];
if ( ! empty( $attributes['buttonBgColorHover'] ) ) $button_hover_styles['background-color'] = $attributes['buttonBgColorHover'];
$button_hover_styles = array_merge( $button_hover_styles, $shadow( $attributes['buttonBoxShadowHover'] ?? [] ) );
if ( ! empty( $attributes['buttonBorderHover'] ) ) $button_hover_styles = array_merge( $button_hover_styles, $H::border_to_css_props( $attributes['buttonBorderHover'] ) );

$button_icon_styles = [];
if ( ! empty( $attributes['buttonIconColor'] ) ) $button_icon_styles['color'] = $attributes['buttonIconColor'];
$button_icon_fill = [];
if ( ! empty( $attributes['buttonIconColor'] ) ) $button_icon_fill['fill'] = $attributes['buttonIconColor'];

$btn_icon_before = [];
$btn_icon_after  = [];
if ( isset( $attributes['buttonIconSpacing'] ) && '' !== $attributes['buttonIconSpacing'] ) {
	$bis = $H::ensure_unit( $attributes['buttonIconSpacing'] );
	$btn_icon_before = [ 'margin-right' => $bis ];
	$btn_icon_after  = [ 'margin-left' => $bis ];
}

// ---- Button subtext ----
$subtext_styles = $typo( $attributes['buttonSubtextTypography'] ?? [] );
if ( ! empty( $attributes['buttonSubtextColor'] ) ) $subtext_styles['color'] = $attributes['buttonSubtextColor'];
$subtext_styles = array_merge( $subtext_styles, $dims( $attributes['buttonSubtextMargin'] ?? [], 'margin' ) );

wp_enqueue_style( $style_handle );
$H::add_custom_style( $style_handle, $selector, '', [
	'.eelfg-price-title'                              => $H::get_inline_styles( $title_styles ),
	'.eelfg-price-title span'                         => ! empty( $attributes['titleHighlightColor'] ) ? 'color:' . $attributes['titleHighlightColor'] : '',
	'.eelfg-subtitle-price'                           => $H::get_inline_styles( $desc_styles ),
	'.eelfg-price'                                    => $H::get_inline_styles( $price_wrap_styles ),
	'.eelfg-price .eelfg-amount'                      => $H::get_inline_styles( $amount_styles ),
	'.eelfg-price .eelfg-sale-price'                  => $H::get_inline_styles( $sale_styles ),
	'.eelfg-old-price'                                => ! empty( $attributes['oldPriceColor'] ) ? 'color:' . $attributes['oldPriceColor'] : '',
	'.eelfg-price .eelfg-period'                      => $H::get_inline_styles( $period_styles ),
	'.eelfg-currency'                                 => $H::get_inline_styles( $currency_styles ),
	'.eelfg-features-description'                     => $H::get_inline_styles( $fdesc_styles ),
	'ul.eelfg-features'                               => ! empty( $attributes['featureTextAlignment'] ) ? 'text-align:' . $attributes['featureTextAlignment'] : '',
	'.eelfg-features li'                              => $H::get_inline_styles( $features_text_styles ),
	'.eelfg-features li .feature-icon'                => $H::get_inline_styles( $features_icon_styles ),
	'.eelfg-features li svg path'                     => $H::get_inline_styles( $features_icon_fill ),
	'.eelfg-features svg.feature-icon'                => $H::get_inline_styles( $feat_icon_size_svg ),
	'.eelfg-features i.feature-icon'                  => $H::get_inline_styles( $feat_icon_size_i ),
	'.eelfg-features .feature-icon.icon-bg'           => $H::get_inline_styles( $feat_icon_bg_styles ),
	'.eelfg-features .feature-icon.icon-border'       => $H::get_inline_styles( $feat_icon_border_styles ),
	'.eelfg-ribbon'                                   => $H::get_inline_styles( $ribbon_styles ),
	'.eelfg-btn-part'                                 => ! empty( $attributes['btnAlignment'] ) ? 'text-align:' . $attributes['btnAlignment'] : '',
	'.eelfg-button'                                   => $H::get_inline_styles( $button_styles ),
	'.eelfg-button:hover'                             => $H::get_inline_styles( $button_hover_styles ),
	'.eelfg-button .eelfg-icon-before, .eelfg-button .eelfg-icon-after' => $H::get_inline_styles( $button_icon_styles ),
	'.eelfg-button .eelfg-icon-before svg path, .eelfg-button .eelfg-icon-after svg path' => $H::get_inline_styles( $button_icon_fill ),
	'.eelfg-button .eelfg-icon-before'                => $H::get_inline_styles( $btn_icon_before ),
	'.eelfg-button .eelfg-icon-after'                 => $H::get_inline_styles( $btn_icon_after ),
	'.eelfg-button-subtext'                           => $H::get_inline_styles( $subtext_styles ),
] );

// Default feature icon (checkmark) when none is selected.
$default_feature_icon = '<svg class="feature-icon ' . esc_attr( $icon_style ) . '" viewBox="0 0 24 24" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"><path d="M9 16.2l-3.5-3.5L4 14.2l5 5 11-11-1.5-1.5z"/></svg>';

/** Renders the price markup honouring currency placement. */
$render_price_value = function ( $value ) use ( $currency, $currency_pos ) {
	$currency_html = '<span class="eelfg-currency">' . esc_html( $currency ) . '</span>';
	if ( 'left' === $currency_pos ) {
		return $currency_html . esc_html( $value );
	}
	return esc_html( $value ) . $currency_html;
};

/** Renders the CTA button. */
$render_button = function () use ( $attributes, $H ) {
	if ( empty( $attributes['showButton'] ) ) {
		return;
	}
	$full_width = ! empty( $attributes['buttonFullWidth'] ) ? 'eelfg--full-btn' : '';
	$url        = ! empty( $attributes['buttonUrl'] ) ? $attributes['buttonUrl'] : '#';
	$target     = ! empty( $attributes['buttonTarget'] ) ? ' target="_blank"' : '';
	$nofollow   = ! empty( $attributes['buttonNofollow'] ) ? ' rel="nofollow"' : '';
	$icon       = isset( $attributes['buttonIcon'] ) ? $attributes['buttonIcon'] : '';
	$icon_pos   = isset( $attributes['buttonIconPosition'] ) ? $attributes['buttonIconPosition'] : 'after';
	$text       = isset( $attributes['buttonText'] ) ? $attributes['buttonText'] : '';
	$subtext    = isset( $attributes['buttonSubtext'] ) ? $attributes['buttonSubtext'] : '';
	$icon_html  = ( ! empty( $icon ) && 'none' !== $icon ) ? '<i class="eelfg-icon ' . esc_attr( $icon ) . '" aria-hidden="true"></i>' : '';
	?>
	<div class="eelfg-btn-part">
		<a href="<?php echo esc_url( $url ); ?>"<?php echo $target . $nofollow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static attribute strings. ?> class="eelfg-button <?php echo esc_attr( $full_width ); ?>">
			<?php if ( 'before' === $icon_pos && $icon_html ) : ?>
				<span class="eelfg-icon eelfg-icon-before"><?php echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Icon markup escaped above. ?></span>
			<?php endif; ?>
			<?php echo esc_html( $text ); ?>
			<?php if ( 'after' === $icon_pos && $icon_html ) : ?>
				<span class="eelfg-icon eelfg-icon-after"><?php echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Icon markup escaped above. ?></span>
			<?php endif; ?>
		</a>
		<?php if ( ! empty( $subtext ) ) : ?>
			<div class="eelfg-button-subtext"><?php echo esc_html( $subtext ); ?></div>
		<?php endif; ?>
	</div>
	<?php
};

$wrap_classes = 'eelfg-pricing-table eelfg--' . $skin_style;
if ( $is_featured ) {
	$wrap_classes .= ' featured ' . $ribbon_style;
}
?>
<div <?php echo wp_kses_post( $block_wrap_attr ); ?>>
	<div class="<?php echo esc_attr( $wrap_classes ); ?>">
		<?php if ( $is_featured ) : ?>
			<div class="eelfg-ribbon eelfg-<?php echo esc_attr( $ribbon_align ); ?>">
				<?php echo esc_html( $featured_txt ); ?>
			</div>
		<?php endif; ?>

		<h3 class="eelfg-price-title"><?php echo wp_kses_post( $title ); ?></h3>
		<div class="eelfg-subtitle-price"><?php echo wp_kses_post( $description ); ?></div>

		<div class="eelfg-price">
			<?php if ( $on_sale ) : ?>
				<span class="eelfg-old-price"><?php echo $render_price_value( $attributes['regularPrice'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from esc_html in closure. ?></span>
				<span class="eelfg-sale-price"><?php echo $render_price_value( $attributes['salePrice'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from esc_html in closure. ?></span>
			<?php else : ?>
				<span class="eelfg-amount"><?php echo $render_price_value( $attributes['price'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from esc_html in closure. ?></span>
			<?php endif; ?>
			<span class="eelfg-period"><?php echo esc_html( $separator . $period ); ?></span>
		</div>

		<?php if ( 'in_features' === $button_pos ) { $render_button(); } ?>

		<?php if ( ! empty( $features_des ) ) : ?>
			<div class="eelfg-features-description"><?php echo wp_kses_post( $features_des ); ?></div>
		<?php endif; ?>

		<ul class="eelfg-features">
			<?php foreach ( $features as $feature ) : ?>
				<li class="<?php echo esc_attr( $icon_style ); ?>">
					<?php
					$f_icon = isset( $feature['icon'] ) ? $feature['icon'] : '';
					if ( ! empty( $f_icon ) && 'none' !== $f_icon ) {
						echo '<i class="eelfg-icon ' . esc_attr( $f_icon ) . ' feature-icon ' . esc_attr( $icon_style ) . '" aria-hidden="true"></i>';
					} else {
						echo $default_feature_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static inline SVG.
					}
					?>
					<span class="eelfg-feature-text"><?php echo esc_html( isset( $feature['text'] ) ? $feature['text'] : '' ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php if ( 'after_features' === $button_pos ) { $render_button(); } ?>
	</div>
</div>
