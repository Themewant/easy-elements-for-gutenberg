<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Local template/iteration variables.

/**
 * Server-side render for the Testimonials Grid block.
 *
 * Mirrors the markup of the Elementor "Testimonials Grid" widget
 * (easy-elements/widgets/testimonials-grid) — 6 skins, ratings, quote icons,
 * logos and a view-all reveal. Element classes use this plugin's "eelfg-" prefix.
 */

$H = '\EELFG\Frontend\Helper';

$unique_id = ! empty( $attributes['blockId'] ) ? $attributes['blockId'] : 'eelfg-tstml-' . substr( md5( wp_json_encode( $attributes ) ), 0, 6 );

$skin        = isset( $attributes['testimonialsSkin'] ) ? preg_replace( '/[^a-z0-9_-]/i', '', (string) $attributes['testimonialsSkin'] ) : 'default';
if ( '' === $skin ) { $skin = 'default'; }
$items       = isset( $attributes['testimonials'] ) && is_array( $attributes['testimonials'] ) ? $attributes['testimonials'] : [];
$show_image  = ! empty( $attributes['showImage'] );
$show_rating = ! empty( $attributes['showRating'] );
$title_icon  = isset( $attributes['titleIcon'] ) ? $attributes['titleIcon'] : '';
$avatar_top  = ! empty( $attributes['avatarImageTop'] );
$show_more   = ! empty( $attributes['showLoadmore'] );
$more_text   = isset( $attributes['loadMoreText'] ) ? $attributes['loadMoreText'] : '';

if ( empty( $items ) ) {
	$wrap = get_block_wrapper_attributes( array( 'class' => 'eelfg-block eelfg-testimonial-block-wrap ' . $unique_id ) );
	echo '<div ' . wp_kses_post( $wrap ) . '><p>' . esc_html__( 'Please add testimonials.', 'easy-elements-for-gutenberg' ) . '</p></div>';
	return;
}

$has_more     = count( $items ) > 6 && 'skin3' === $skin && $show_more;
$overlay_cls  = $has_more ? ' eelfg-testifull-overlay' : '';

$block_wrap_attr = get_block_wrapper_attributes( array(
	'class' => 'eelfg-block eelfg-testimonial-block-wrap ' . $unique_id . ' eelfg-testimonial eelfg-grid-layout' . $overlay_cls,
) );
if ( empty( $block_wrap_attr ) ) {
	$block_wrap_attr = 'class="eelfg-block eelfg-testimonial-block-wrap ' . esc_attr( $unique_id ) . ' eelfg-testimonial eelfg-grid-layout' . esc_attr( $overlay_cls ) . '"';
}

// ---------------------------------------------------------------------------
// Inline styles.
// ---------------------------------------------------------------------------
$selector     = '.eelfg-testimonial-block-wrap.' . $unique_id;
$style_handle = 'eelfg-testimonials-grid-style';

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
$bg = function ( $colorKey, $gradKey ) use ( $attributes ) {
	if ( ! empty( $attributes[ $gradKey ] ) ) return [ 'background' => $attributes[ $gradKey ] ];
	if ( ! empty( $attributes[ $colorKey ] ) ) return [ 'background' => $attributes[ $colorKey ] ];
	return [];
};
$u = function ( $key ) use ( $attributes, $H ) {
	return ( isset( $attributes[ $key ] ) && '' !== $attributes[ $key ] ) ? $H::ensure_unit( $attributes[ $key ] ) : '';
};

// Columns (responsive) — not applied on default/skin4 (full-width).
$col_css = '';
if ( ! in_array( $skin, [ 'default', 'skin4' ], true ) ) {
	$c_d = isset( $attributes['columns'] ) ? (int) $attributes['columns'] : 3;
	$c_t = isset( $attributes['columnsTablet'] ) && '' !== $attributes['columnsTablet'] ? (int) $attributes['columnsTablet'] : $c_d;
	$c_m = isset( $attributes['columnsMobile'] ) && '' !== $attributes['columnsMobile'] ? (int) $attributes['columnsMobile'] : 2;
	$grid_resp = [
		'desktop' => [ 'width' => 'calc(100% / ' . max( 1, $c_d ) . ')' ],
		'tablet'  => [ 'width' => 'calc(100% / ' . max( 1, $c_t ) . ')' ],
		'mobile'  => [ 'width' => 'calc(100% / ' . max( 1, $c_m ) . ')' ],
	];
	$col_css = $H::generate_responsive_css( $selector . ' .eelfg-grid-item', $grid_resp );
}

$align       = ! empty( $attributes['testimonialsAlignment'] ) ? $attributes['testimonialsAlignment'] : '';
$align_styles = $align ? [ 'text-align' => $align ] : [];

$item_pad = $dims( $attributes['itemPadding'] ?? [], 'padding' );
$inner = $bg( 'bgColor', 'bgGradient' );
$inner = array_merge( $inner, $dims( $attributes['itemBorderRadius'] ?? [], 'radius' ), $dims( $attributes['itemInnerPadding'] ?? [], 'padding' ), $shadow( $attributes['itemBoxShadow'] ?? [] ) );
if ( ! empty( $attributes['itemBorder'] ) ) $inner = array_merge( $inner, $H::border_to_css_props( $attributes['itemBorder'] ) );
if ( '' !== $u( 'wrapperGap' ) ) $inner['gap'] = $u( 'wrapperGap' );

$name_styles = $typo( $attributes['nameTypography'] ?? [] );
if ( ! empty( $attributes['nameColor'] ) ) $name_styles['color'] = $attributes['nameColor'];
$name_margin = $dims( $attributes['nameMargin'] ?? [], 'margin' );

$deg_styles = $typo( $attributes['designationTypography'] ?? [] );
if ( ! empty( $attributes['designationColor'] ) ) $deg_styles['color'] = $attributes['designationColor'];

$desc_styles = $typo( $attributes['descriptionTypography'] ?? [] );
if ( ! empty( $attributes['descriptionColor'] ) ) $desc_styles['color'] = $attributes['descriptionColor'];
$desc_styles = array_merge( $desc_styles, $dims( $attributes['descriptionMargin'] ?? [], 'margin' ) );
if ( '' !== $u( 'minHeight' ) ) $desc_styles['min-height'] = $u( 'minHeight' );
if ( '' !== $u( 'maxWidth' ) ) $desc_styles['max-width'] = $u( 'maxWidth' );

$rating_styles = [];
if ( ! empty( $attributes['ratingColor'] ) ) $rating_styles['color'] = $attributes['ratingColor'];
if ( '' !== $u( 'ratingSize' ) ) $rating_styles['font-size'] = $u( 'ratingSize' );

$author_wrap = ! empty( $attributes['authorMetaAlignment'] ) ? [ 'align-items' => $attributes['authorMetaAlignment'] ] : [];
$author_style4 = ! empty( $attributes['authorMetaAlignmentStyle4'] ) ? [ 'text-align' => $attributes['authorMetaAlignmentStyle4'] ] : [];
$picture_margin = $dims( $attributes['authorMetaGap'] ?? [], 'margin' );
$img_styles = [];
if ( '' !== $u( 'authorImageSize' ) ) { $img_styles['width'] = $u( 'authorImageSize' ) . ' !important'; $img_styles['height'] = $u( 'authorImageSize' ) . ' !important'; $img_styles['object-fit'] = 'cover'; }
$img_styles = array_merge( $img_styles, $dims( $attributes['authorImageBorderRadius'] ?? [], 'radius' ) );
$logo_styles = ( '' !== $u( 'logoHeight' ) ) ? [ 'height' => $u( 'logoHeight' ), 'width' => 'auto' ] : [];

$quote_styles = [];
if ( ! empty( $attributes['quoteIconColor'] ) ) $quote_styles['fill'] = $attributes['quoteIconColor'];
$quote_size = ( '' !== $u( 'quoteIconSize' ) ) ? [ 'width' => $u( 'quoteIconSize' ), 'height' => $u( 'quoteIconSize' ) ] : [];

// View-all button.
$more = $typo( $attributes['loadMoreTypography'] ?? [] );
if ( ! empty( $attributes['loadMoreColor'] ) ) $more['color'] = $attributes['loadMoreColor'];
$more = array_merge( $more, $bg( 'loadMoreBgColor', 'loadMoreBgGradient' ), $dims( $attributes['loadmorePadding'] ?? [], 'padding' ), $dims( $attributes['loadmoreBorderRadius'] ?? [], 'radius' ) );
if ( ! empty( $attributes['loadMoreBorder'] ) ) $more = array_merge( $more, $H::border_to_css_props( $attributes['loadMoreBorder'] ) );
$more_hover = [];
if ( ! empty( $attributes['loadMoreHoverColor'] ) ) $more_hover['color'] = $attributes['loadMoreHoverColor'];
$more_hover = array_merge( $more_hover, $bg( 'loadMoreHoverBgColor', 'loadMoreHoverBgGradient' ) );
if ( ! empty( $attributes['loadMoreHoverBorderColor'] ) ) $more_hover['border-color'] = $attributes['loadMoreHoverBorderColor'];

wp_enqueue_style( $style_handle );
$H::add_custom_style( $style_handle, $selector, $col_css, [
	'.eelfg-grid-item'                          => $H::get_inline_styles( $item_pad ),
	'.eelfg-tstml-inner'                        => $H::get_inline_styles( $inner ),
	'.eelfg-tstml-inner'                        => $H::get_inline_styles( $inner ),
	'.eelfg-tstml-inner, ' . $selector . ' .eelfg-tstml-inner .eel-description' => $H::get_inline_styles( $align_styles ),
	'.eelfg-tstml-inner .eelfg-name'            => $H::get_inline_styles( $name_styles ),
	'.eelfg-tstml-inner .eelfg-author-wrap'     => $H::get_inline_styles( array_merge( $name_margin, $author_wrap ) ),
	'.eelfg-tstml-inner .eelfg-designation'     => $H::get_inline_styles( $deg_styles ),
	'.eelfg-tstml-inner .eelfg-description'     => $H::get_inline_styles( $desc_styles ),
	'.eelfg-rating span.star'                   => $H::get_inline_styles( $rating_styles ),
	'.eelfg-tstml-inner.skin4 .eelfg-author'    => $H::get_inline_styles( $author_style4 ),
	'.eelfg-author-wrap .eelfg-picture'         => $H::get_inline_styles( $picture_margin ),
	'.eelfg-author-wrap .eelfg-picture img, ' . $selector . ' .eelfg-picture img' => $H::get_inline_styles( $img_styles ),
	'.eelfg-company-logo img'                   => $H::get_inline_styles( $logo_styles ),
	'.eelfg-quote svg, ' . $selector . ' .eelfg-quote svg path' => $H::get_inline_styles( $quote_styles ),
	'.eelfg-quote svg'                          => $H::get_inline_styles( $quote_size ),
	'.eelfg-testimonial-more-btn'               => $H::get_inline_styles( $more ),
	'.eelfg-testimonial-more-btn:hover'         => $H::get_inline_styles( $more_hover ),
] );

// ---------------------------------------------------------------------------
// Markup helpers.
// ---------------------------------------------------------------------------
$placeholder = EELFG_PL_URL . 'includes/public/assets/img/placeholder.png';
$svg_quote = '<svg viewBox="0 0 24 24" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"><path d="M9 7H5a2 2 0 00-2 2v4a2 2 0 002 2h2v-2H5V9h4V7zm10 0h-4a2 2 0 00-2 2v4a2 2 0 002 2h2v-2h-2V9h4V7z"/></svg>';
$icon_i = function ( $val, $fallback ) {
	return ( ! empty( $val ) && 'none' !== $val ) ? '<i class="eelfg-icon ' . esc_attr( $val ) . '" aria-hidden="true"></i>' : $fallback;
};

$render_picture = function ( $item ) use ( $show_image, $placeholder ) {
	if ( ! $show_image ) return '';
	$src = ! empty( $item['image']['url'] ) ? $item['image']['url'] : $placeholder;
	$alt = ! empty( $item['image']['alt'] ) ? $item['image']['alt'] : '';
	return '<div class="eelfg-picture"><img src="' . esc_url( $src ) . '" alt="' . esc_attr( $alt ) . '" loading="lazy" decoding="async"></div>';
};
$render_logo = function ( $item ) {
	if ( empty( $item['logo']['url'] ) ) return '';
	return '<div class="eelfg-company-logo"><img src="' . esc_url( $item['logo']['url'] ) . '" alt="' . esc_attr( $item['logo']['alt'] ?? '' ) . '" loading="lazy" decoding="async"></div>';
};
$render_rating = function ( $item ) use ( $show_rating ) {
	if ( ! $show_rating || empty( $item['rating'] ) ) return '';
	$r = (int) $item['rating'];
	$out = '<div class="eelfg-rating" aria-label="Rating: ' . esc_attr( $r ) . ' out of 5">';
	for ( $i = 1; $i <= 5; $i++ ) {
		$out .= '<span class="star' . ( $i <= $r ? ' filled' : '' ) . '">' . ( $i <= $r ? '★' : '☆' ) . '</span>';
	}
	return $out . '</div>';
};
$render_name = function ( $item, $with_title_icon = false ) use ( $title_icon, $icon_i ) {
	if ( empty( $item['name'] ) ) return '';
	$tail = ( $with_title_icon && ! empty( $title_icon ) && 'none' !== $title_icon ) ? '<span class="eelfg-title-icon">' . $icon_i( $title_icon, '' ) . '</span>' : '';
	return '<div class="eelfg-name">' . esc_html( $item['name'] ) . $tail . '</div>';
};
$render_desig = function ( $item ) {
	return ! empty( $item['designation'] ) ? '<em class="eelfg-designation">' . esc_html( $item['designation'] ) . '</em>' : '';
};
$render_desc = function ( $item ) {
	return ! empty( $item['description'] ) ? '<div class="eelfg-description">' . esc_html( $item['description'] ) . '</div>' : '';
};
$render_quote = function ( $item ) use ( $icon_i, $svg_quote ) {
	if ( empty( $item['quoteIcon'] ) || 'none' === $item['quoteIcon'] ) return '';
	return '<div class="eelfg-quote" aria-hidden="true">' . $icon_i( $item['quoteIcon'], $svg_quote ) . '</div>';
};

/** Render one testimonial's inner-wrap for the active skin. */
$render_inner = function ( $item ) use ( $skin, $avatar_top, $render_picture, $render_logo, $render_rating, $render_name, $render_desig, $render_desc, $render_quote ) {
	$picture = $render_picture( $item );
	$logo = $render_logo( $item );
	$rating = $render_rating( $item );
	$desc = $render_desc( $item );
	$quote = $render_quote( $item );
	$name = $render_name( $item, 'skin1' === $skin );
	$desig = $render_desig( $item );

	if ( 'skin1' === $skin ) {
		$q = ( ! empty( $item['showQuoteIconSkin1'] ) ) ? $quote : '';
		return '<div class="eelfg-tstml-inner skin1">' . $logo . $q . $desc
			. '<div class="eelfg-author-wrap">' . $picture . '<div class="eelfg-author">' . $name . $desig . '</div></div></div>';
	}
	if ( 'skin2' === $skin ) {
		$top = $avatar_top ? ' eelfg-avatar-image-top' : '';
		return '<div class="eelfg-tstml-inner skin2' . $top . '">' . $logo
			. '<div class="eelfg-picture-des-wrap">' . $picture
			. '<div class="eelfg-description-wrap">' . $desc . '<div class="eelfg-author-wrap"><div class="eelfg-author">' . $name . $desig . '</div></div></div></div></div>';
	}
	if ( 'skin3' === $skin ) {
		return '<div class="eelfg-tstml-inner skin3">' . $logo . $desc
			. '<div class="eelfg-author-wrap">' . $picture . '<div class="eelfg-author">' . $name . $desig . '</div>' . $rating . '</div></div>';
	}
	if ( 'skin4' === $skin ) {
		return '<div class="eelfg-tstml-inner skin4">' . $logo
			. '<div class="eelfg-picture-des-wrap">' . $picture
			. '<div class="eelfg-description-wrap">' . $desc . '<div class="eelfg-author-wrap"><div class="eelfg-author">' . $name . $desig . '</div></div></div></div></div>';
	}
	if ( 'skin5' === $skin ) {
		return '<div class="eelfg-tstml-inner skin5">' . $logo . $desc
			. '<div class="eelfg-author-wrap">' . $picture . '<div class="eelfg-author">' . $name . '</div>' . $rating
			. '<div class="eelfg-rating">' . $desig . '</div></div></div>';
	}
	if ( 'skin6' === $skin ) {
		return '<div class="eelfg-tstml-inner skin6">' . $logo . $rating . $desc
			. '<div class="eelfg-author-wrap">' . $picture . '<div class="eelfg-author">' . $name . $desig . '</div></div></div>';
	}
	// default
	return '<div class="eelfg-tstml-inner">'
		. '<div class="eelfg-author-wrap">' . $picture . '<div class="eelfg-author">' . $name . $desig . $rating . '</div></div>'
		. $desc . $quote . '</div>';
};
?>
<div <?php echo wp_kses_post( $block_wrap_attr ); ?>>
	<div class="eelfg-grid-wrap">
		<?php
		$count = 0;
		foreach ( $items as $item ) :
			$count++;
			$classes = 'not-hidden';
			if ( $count > 6 && $has_more ) {
				$classes .= ' eelfg-hidden-testimonial';
			}
			?>
			<div class="eelfg-grid-item <?php echo esc_attr( $classes ); ?> eelfg-testimonials--<?php echo esc_attr( $skin ); ?>">
				<div class="eelfg-testimonial-item">
					<?php echo $render_inner( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Assembled from escaped parts. ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<?php if ( $has_more ) : ?>
		<div class="eelfg-testimonial-more-btn"><?php echo esc_html( $more_text ); ?></div>
	<?php endif; ?>
</div>
