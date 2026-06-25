<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Local template/iteration variables.

/**
 * Server-side render for the Team Member block.
 *
 * Mirrors the markup of the Elementor "Team Grid" widget
 * (easy-elements/widgets/team-grid) — 5 skins, social icons, contact info and
 * an optional popup. Element classes use this plugin's "eelfg-" prefix.
 */

$H = '\EELFG\Frontend\Helper';

$unique_id  = ! empty( $attributes['blockId'] ) ? $attributes['blockId'] : 'eelfg-team-' . substr( md5( wp_json_encode( $attributes ) ), 0, 6 );
$popup_id   = $unique_id . '-popup';

$skin       = isset( $attributes['teamSkin'] ) ? $attributes['teamSkin'] : 'default';
$overlay4   = isset( $attributes['skin4HoverOverlay'] ) ? $attributes['skin4HoverOverlay'] : 'overlay1';
$image      = isset( $attributes['image'] ) && is_array( $attributes['image'] ) ? $attributes['image'] : [];
$img_url    = ! empty( $image['url'] ) ? $image['url'] : '';
$allowed    = [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p' ];
$tag        = isset( $attributes['titleTag'] ) && in_array( $attributes['titleTag'], $allowed, true ) ? $attributes['titleTag'] : 'h4';
$name       = isset( $attributes['name'] ) ? $attributes['name'] : '';
$designation = isset( $attributes['designation'] ) ? $attributes['designation'] : '';
$details    = isset( $attributes['details'] ) ? $attributes['details'] : '';
$action     = isset( $attributes['actionType'] ) ? $attributes['actionType'] : 'link';
$link       = isset( $attributes['linkUrl'] ) ? $attributes['linkUrl'] : '';
$target     = ! empty( $attributes['linkTarget'] ) ? ' target="_blank"' : '';
$nofollow   = ! empty( $attributes['linkNofollow'] ) ? ' rel="nofollow"' : '';
$content_show = isset( $attributes['contentShow'] ) ? $attributes['contentShow'] : 'inside';
$show_social  = ! empty( $attributes['showSocialIcon'] );
$social_pos   = isset( $attributes['socialIconPosition'] ) ? $attributes['socialIconPosition'] : 'default';
$social_show  = isset( $attributes['socialIconShow'] ) ? $attributes['socialIconShow'] : 'dafault_show';
$social_hover_icon = isset( $attributes['socialHoverIcon'] ) ? $attributes['socialHoverIcon'] : '';
$social_links = isset( $attributes['socialLinks'] ) && is_array( $attributes['socialLinks'] ) ? $attributes['socialLinks'] : [];
$fetch      = isset( $attributes['fetchpriority'] ) ? $attributes['fetchpriority'] : '';

$show_contact = ! empty( $attributes['showContactInfo'] );
$email      = isset( $attributes['teamEmail'] ) ? $attributes['teamEmail'] : '';
$phone      = isset( $attributes['teamPhone'] ) ? $attributes['teamPhone'] : '';

$block_wrap_attr = get_block_wrapper_attributes( array(
	'class' => 'eelfg-block eelfg-team-grid-block-wrap ' . $unique_id . ' eelfg-team-wraps eelfg-team-grid eelfg-grid-layout ' . $skin,
) );
if ( empty( $block_wrap_attr ) ) {
	$block_wrap_attr = 'class="eelfg-block eelfg-team-grid-block-wrap ' . esc_attr( $unique_id ) . ' eelfg-team-wraps eelfg-team-grid eelfg-grid-layout ' . esc_attr( $skin ) . '"';
}

// ---------------------------------------------------------------------------
// Inline styles.
// ---------------------------------------------------------------------------
$selector     = '.eelfg-team-grid-block-wrap.' . $unique_id;
$style_handle = 'eelfg-team-grid-style';

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

// Team item.
$card = $bg( 'cardBgColor', 'cardBgGradient' );
$card = array_merge( $card, $dims( $attributes['itemPadding'] ?? [], 'padding' ), $dims( $attributes['itemBorderRadius'] ?? [], 'radius' ), $shadow( $attributes['teamBoxShadow'] ?? [] ) );
if ( ! empty( $attributes['teamBorder'] ) ) $card = array_merge( $card, $H::border_to_css_props( $attributes['teamBorder'] ) );
$card_hover = ! empty( $attributes['teamHoverBorderColor'] ) ? [ 'border-color' => $attributes['teamHoverBorderColor'] ] : [];
$content_align = ! empty( $attributes['teamContentAlignment'] ) ? [ 'text-align' => $attributes['teamContentAlignment'] ] : [];

// Image.
$img_box = [];
if ( '' !== $u( 'imageWidth' ) ) $img_box['max-width'] = $u( 'imageWidth' );
$img_box = array_merge( $img_box, $dims( $attributes['imageStyleRadius'] ?? [], 'radius' ) );
$img_el = ( '' !== $u( 'imageHeightStyle' ) ) ? [ 'height' => $u( 'imageHeightStyle' ) ] : [];
$img_area = $dims( $attributes['imagePadding'] ?? [], 'padding' );
$below = [];
if ( ! empty( $attributes['imageBelowBg'] ) ) $below['background-color'] = $attributes['imageBelowBg'];
if ( ! empty( $attributes['imageBelowHeight'] ) ) $below['height'] = $attributes['imageBelowHeight'] . '%';
if ( ! empty( $attributes['imageBelowPosition'] ) ) {
	if ( 'bottom' === $attributes['imageBelowPosition'] ) { $below['top'] = 'auto'; $below['bottom'] = '0'; } else { $below['top'] = '0'; $below['bottom'] = 'auto'; }
}
$below = array_merge( $below, $dims( $attributes['imageBelowRadius'] ?? [], 'radius' ) );
$overlay = $bg( 'imageOverlayColor', 'imageOverlayGradient' );

// Name & designation area.
$area = [];
if ( ! empty( $attributes['areaBgColor'] ) ) $area['background-color'] = $attributes['areaBgColor'];
$area = array_merge( $area, $dims( $attributes['wrapPadding'] ?? [], 'padding' ), $dims( $attributes['wrapMargin'] ?? [], 'margin' ), $dims( $attributes['areaBorderRadius'] ?? [], 'radius' ) );

// Skin4 overlay.
$ov = $bg( 'skin4OverlayColor', 'skin4OverlayGradient' );
if ( '' !== $u( 'skin4OverlayBlur' ) ) { $ov['backdrop-filter'] = 'blur(' . $u( 'skin4OverlayBlur' ) . ')'; $ov['-webkit-backdrop-filter'] = 'blur(' . $u( 'skin4OverlayBlur' ) . ')'; }
if ( ! empty( $attributes['skin4OverlayTextColor'] ) ) $ov['color'] = $attributes['skin4OverlayTextColor'];
$ov = array_merge( $ov, $dims( $attributes['skin4OverlayPadding'] ?? [], 'padding' ), $dims( $attributes['skin4OverlayBorderRadius'] ?? [], 'radius' ) );
$ov2 = ( '' !== $u( 'skin4Overlay2CircleSize' ) ) ? [ 'width' => $u( 'skin4Overlay2CircleSize' ) ] : [];

// Name / designation.
$name_styles = $typo( $attributes['nameTypography'] ?? [] );
if ( ! empty( $attributes['nameColor'] ) ) $name_styles['color'] = $attributes['nameColor'];
$name_styles = array_merge( $name_styles, $dims( $attributes['namePadding'] ?? [], 'padding' ) );
$deg_styles = $typo( $attributes['designationTypography'] ?? [] );
if ( ! empty( $attributes['designationColor'] ) ) $deg_styles['color'] = $attributes['designationColor'];

// Contact (skin5).
$contact_wrap = ( '' !== $u( 'contactGap' ) ) ? [ 'gap' => $u( 'contactGap' ) ] : [];
$contact_item = $typo( $attributes['contactTypography'] ?? [] );
if ( ! empty( $attributes['contactTextColor'] ) ) $contact_item['color'] = $attributes['contactTextColor'];
$contact_item = array_merge( $contact_item, $bg( 'contactItemBgColor', 'contactItemBgGradient' ), $dims( $attributes['contactItemRadius'] ?? [], 'radius' ), $dims( $attributes['contactItemPadding'] ?? [], 'padding' ) );
$contact_item_hover = [];
if ( ! empty( $attributes['contactTextHoverColor'] ) ) $contact_item_hover['color'] = $attributes['contactTextHoverColor'];
$contact_item_hover = array_merge( $contact_item_hover, $bg( 'contactItemBgHoverColor', 'contactItemBgHoverGradient' ) );
$contact_icon = $bg( 'contactIconBgColor', 'contactIconBgGradient' );
if ( ! empty( $attributes['contactIconColor'] ) ) $contact_icon['color'] = $attributes['contactIconColor'];
if ( '' !== $u( 'contactIconBoxSize' ) ) { $contact_icon['width'] = $u( 'contactIconBoxSize' ); $contact_icon['height'] = $u( 'contactIconBoxSize' ); }
$contact_icon = array_merge( $contact_icon, $dims( $attributes['contactIconRadius'] ?? [], 'radius' ) );
$contact_icon_glyph = ( '' !== $u( 'contactIconSize' ) ) ? [ 'font-size' => $u( 'contactIconSize' ) ] : [];
$contact_icon_fill = ! empty( $attributes['contactIconColor'] ) ? [ 'fill' => $attributes['contactIconColor'] ] : [];

// Description (default/skin1/skin2).
$tdesc = $typo( $attributes['teamDescriptionTypography'] ?? [] );
if ( ! empty( $attributes['teamDescriptionColor'] ) ) $tdesc['color'] = $attributes['teamDescriptionColor'];
$tdesc = array_merge( $tdesc, $dims( $attributes['teamDescriptionMargin'] ?? [], 'margin' ), $dims( $attributes['teamDescriptionPadding'] ?? [], 'padding' ) );

// Description (skin3 overlay).
$desc3 = $typo( $attributes['descTypography'] ?? [] );
if ( ! empty( $attributes['descColor'] ) ) $desc3['color'] = $attributes['descColor'];
$desc3_box = $bg( 'descBgColor', 'descBgGradient' );
$desc3_box = array_merge( $desc3_box, $dims( $attributes['descPadding'] ?? [], 'padding' ) );
$desc3_hover = ! empty( $attributes['descColorHover'] ) ? [ 'color' => $attributes['descColorHover'] ] : [];
$desc3_hover_bg = $bg( 'descBgHoverColor', 'descBgHoverGradient' );
if ( '' !== $u( 'descBgHoverOpacity' ) ) $desc3_hover_bg['opacity'] = $attributes['descBgHoverOpacity'];

// Social.
$soc = [];
if ( ! empty( $attributes['sIconColor'] ) ) $soc['color'] = $attributes['sIconColor'];
if ( ! empty( $attributes['sIconBgColor'] ) ) $soc['background'] = $attributes['sIconBgColor'];
$soc = array_merge( $soc, $typo( $attributes['sIconTypography'] ?? [] ), $dims( $attributes['sIconRadius'] ?? [], 'radius' ), $shadow( $attributes['socialBoxShadow'] ?? [] ) );
if ( '' !== $u( 'sIconButtonSize' ) ) { $soc['width'] = $u( 'sIconButtonSize' ); $soc['height'] = $u( 'sIconButtonSize' ); }
$soc_hover = [];
if ( ! empty( $attributes['sIconHoverColor'] ) ) $soc_hover['color'] = $attributes['sIconHoverColor'];
if ( ! empty( $attributes['sIconHoverBgColor'] ) ) $soc_hover['background'] = $attributes['sIconHoverBgColor'];
$soc_ul = ( '' !== $u( 'sIconGap' ) ) ? [ 'gap' => $u( 'sIconGap' ) ] : [];
$soc_area = $dims( $attributes['sIconAreaPadding'] ?? [], 'padding' );
if ( ! empty( $attributes['socialItemBorder'] ) ) $soc_area = array_merge( $soc_area, $H::border_to_css_props( $attributes['socialItemBorder'] ) );
$soc_align = ! empty( $attributes['teamSocialIconAlignment'] ) ? [ 'justify-content' => $attributes['teamSocialIconAlignment'] ] : [];
// Social position offsets.
$soc_pos = [];
if ( '' !== $u( 'sIconPosiTop' ) ) $soc_pos['top'] = $u( 'sIconPosiTop' );
if ( '' !== $u( 'sIconPosiBottom' ) ) $soc_pos['bottom'] = $u( 'sIconPosiBottom' );
if ( '' !== $u( 'sIconPosiLeft' ) ) $soc_pos['left'] = $u( 'sIconPosiLeft' );
if ( '' !== $u( 'sIconPosiRight' ) ) $soc_pos['right'] = $u( 'sIconPosiRight' );

// Popup.
$pop_content = ! empty( $attributes['popupBgColor'] ) ? [ 'background-color' => $attributes['popupBgColor'] ] : [];
$pop_name = $typo( $attributes['popupNameTypography'] ?? [] );
if ( ! empty( $attributes['popupNameColor'] ) ) $pop_name['color'] = $attributes['popupNameColor'];
$pop_deg = $typo( $attributes['popupDesignationTypography'] ?? [] );
if ( ! empty( $attributes['popupDesignationColor'] ) ) $pop_deg['color'] = $attributes['popupDesignationColor'];
$pop_det = $typo( $attributes['popupDetailsTypography'] ?? [] );
if ( ! empty( $attributes['popupDetailsColor'] ) ) $pop_det['color'] = $attributes['popupDetailsColor'];
$pop_close = ! empty( $attributes['popupCloseColor'] ) ? [ 'color' => $attributes['popupCloseColor'] ] : [];

$scale_off = ! empty( $attributes['disableImageScale'] ) ? [ 'transform' => 'none' ] : [];
$lift_off  = ! empty( $attributes['disableSocialLift'] ) ? [ 'transform' => 'none' ] : [];

wp_enqueue_style( $style_handle );
$H::add_custom_style( $style_handle, $selector, '', [
	'.eelfg-team-grid .eelfg-team-card'                  => $H::get_inline_styles( $card ),
	'.eelfg-team-grid .eelfg-team-card:hover'            => $H::get_inline_styles( $card_hover ),
	'.eelfg-team-grid .eelfg-team-card .eelfg-name-deg-wrap' => $H::get_inline_styles( array_merge( $area, $content_align ) ),
	'.eelfg-team-card .eelfg-team-img-box'               => $H::get_inline_styles( $img_box ),
	'.eelfg-team-card .eelfg-team-img-box img'           => $H::get_inline_styles( $img_el ),
	'.eelfg-team-card .eelfg-team-img-area'              => $H::get_inline_styles( $img_area ),
	'.eelfg-team-card .eelfg-team-img-area .eelfg-image-below-bg' => $H::get_inline_styles( $below ),
	'.eelfg-team-card .eelfg-image-overlay'              => $H::get_inline_styles( $overlay ),
	'.eelfg-team-card .eelfg-team-img:hover img, ' . $selector . ' .eelfg-team-card .eelfg-team-img-area:hover img' => $H::get_inline_styles( $scale_off ),
	'.eelfg-team-grid.skin4 .eelfg-team-hover-content, ' . $selector . ' .eelfg-team-grid.skin4 .eelfg-team-card .eelfg-team-hover-content.overlay2' => $H::get_inline_styles( $ov ),
	'.eelfg-team-grid.skin4 .eelfg-team-card .eelfg-team-hover-content.overlay2' => $H::get_inline_styles( $ov2 ),
	'.eelfg-name, ' . $selector . ' .eelfg-team-grid.skin4 .eelfg-team-hover-content.overlay2 .eelfg-name' => $H::get_inline_styles( $name_styles ),
	'.eelfg-designation, ' . $selector . ' .eelfg-team-grid.skin4 .eelfg-team-hover-content.overlay2 .eelfg-designation' => $H::get_inline_styles( $deg_styles ),
	'.eelfg-team-card.skin5 .eelfg-author-contact'       => $H::get_inline_styles( $contact_wrap ),
	'.eelfg-team-card.skin5 .eelfg-author-contact .eelfg-contact-item' => $H::get_inline_styles( $contact_item ),
	'.eelfg-team-card.skin5 .eelfg-author-contact .eelfg-contact-item:hover' => $H::get_inline_styles( $contact_item_hover ),
	'.eelfg-team-card.skin5 .eelfg-author-contact .eelfg-contact-item .eelfg-contact-icon' => $H::get_inline_styles( $contact_icon ),
	'.eelfg-team-card.skin5 .eelfg-author-contact .eelfg-contact-item .eelfg-contact-icon i, ' . $selector . ' .eelfg-team-card.skin5 .eelfg-contact-icon svg' => $H::get_inline_styles( array_merge( $contact_icon_glyph, $contact_icon_fill ) ),
	'.eelfg-team-description'                            => $H::get_inline_styles( $tdesc ),
	'.eelfg-image-content .eelfg-description'            => $H::get_inline_styles( $desc3 ),
	'.eelfg-image-content'                               => $H::get_inline_styles( $desc3_box ),
	'.eelfg-image-content:hover .eelfg-description'      => $H::get_inline_styles( $desc3_hover ),
	'.eelfg-image-content:hover::before'                 => $H::get_inline_styles( $desc3_hover_bg ),
	'.eelfg-team-social ul li a, ' . $selector . ' .eelfg-team-social .eelfg-team-social-hover a' => $H::get_inline_styles( $soc ),
	'.eelfg-team-social ul li a:hover, ' . $selector . ' .eelfg-team-social .eelfg-team-social-hover a:hover' => $H::get_inline_styles( $soc_hover ),
	'.eelfg-team-social ul'                              => $H::get_inline_styles( $soc_ul ),
	'.eelfg-team-social'                                 => $H::get_inline_styles( array_merge( $soc_area, $soc_pos ) ),
	'.eelfg-team-card .eelfg-team-social.default ul'     => $H::get_inline_styles( $soc_align ),
	'.eelfg-team-social .eelfg-team-social-hover a:hover, ' . $selector . ' .eelfg-team-social ul li a:hover' => $H::get_inline_styles( $lift_off ),
	'.eelfg-popup-content'                               => $H::get_inline_styles( $pop_content ),
	'.eelfg-popup-name .eelfg-name'                      => $H::get_inline_styles( $pop_name ),
	'.eelfg-popup-designation'                           => $H::get_inline_styles( $pop_deg ),
	'.eelfg-popup-details'                               => $H::get_inline_styles( $pop_det ),
	'.eelfg-popup-close'                                 => $H::get_inline_styles( $pop_close ),
] );

// ---------------------------------------------------------------------------
// Markup helpers.
// ---------------------------------------------------------------------------
$icon_i = function ( $val, $fallback ) {
	return ( ! empty( $val ) && 'none' !== $val ) ? '<i class="eelfg-icon ' . esc_attr( $val ) . '" aria-hidden="true"></i>' : $fallback;
};
$svg_link  = '<svg viewBox="0 0 24 24" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"><path d="M3.9 12a3 3 0 013-3h3v2H6.9a1 1 0 100 2h3v2h-3a3 3 0 01-3-3zm6 1h4v-2h-4v2zm4-4h3a3 3 0 010 6h-3v-2h3a1 1 0 100-2h-3V9z"/></svg>';
$svg_plus  = '<svg viewBox="0 0 24 24" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"><path d="M11 5v6H5v2h6v6h2v-6h6v-2h-6V5z"/></svg>';
$svg_mail  = '<svg viewBox="0 0 24 24" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"><path d="M3 5h18v14H3V5zm9 7L4 7v1l8 5 8-5V7l-8 5z"/></svg>';
$svg_phone = '<svg viewBox="0 0 24 24" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"><path d="M6.6 10.8a15 15 0 006.6 6.6l2.2-2.2a1 1 0 011-.24 11 11 0 003.4.55 1 1 0 011 1V20a1 1 0 01-1 1A17 17 0 013 4a1 1 0 011-1h3.5a1 1 0 011 1 11 11 0 00.55 3.4 1 1 0 01-.24 1l-2.2 2.4z"/></svg>';

// Open/close link wrapper.
$open_link = '';
$close_link = '';
if ( 'link' === $action && $link ) {
	$open_link = '<a href="' . esc_url( $link ) . '"' . $target . $nofollow . '>';
	$close_link = '</a>';
} elseif ( 'popup' === $action ) {
	$open_link = '<a href="#' . esc_attr( $popup_id ) . '" class="eelfg-popup-trigger" data-popup-id="' . esc_attr( $popup_id ) . '">';
	$close_link = '</a>';
}

$name_html = $name ? sprintf( '<%1$s class="eelfg-name">%2$s</%1$s>', tag_escape( $tag ), esc_html( $name ) ) : '';

// Social markup.
$social_html = '';
if ( $show_social && ! empty( $social_links ) ) {
	$pos_classes = $social_pos . ' ' . $social_show;
	ob_start();
	?>
	<div class="eelfg-team-social <?php echo esc_attr( $pos_classes ); ?>">
		<?php if ( 'hover_show' === $social_show && ! empty( $social_hover_icon ) ) : ?>
			<div class="eelfg-team-social-hover"><a href="#"><?php echo $icon_i( $social_hover_icon, $svg_plus ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></div>
		<?php endif; ?>
		<ul>
			<?php foreach ( $social_links as $s ) : $su = isset( $s['url'] ) ? $s['url'] : '#'; ?>
				<li><a href="<?php echo esc_url( $su ); ?>"><?php echo $icon_i( isset( $s['icon'] ) ? $s['icon'] : '', $svg_link ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php
	$social_html = ob_get_clean();
}
$social_default = ( $show_social && ! empty( $social_links ) && 'default' === $social_pos ) ? $social_html : '';
$social_positioned = ( $show_social && ! empty( $social_links ) && 'default' !== $social_pos ) ? $social_html : '';

// Image markup.
$img_alt = $img_url ? esc_attr( $image['alt'] ?? '' ) : esc_attr__( 'Team Image', 'easy-elements-for-gutenberg' );
$placeholder = EELFG_PL_URL . 'includes/public/assets/img/placeholder.png';
$img_src = $img_url ? $img_url : $placeholder;
ob_start();
?>
<div class="eelfg-team-img-box">
	<img class="eelfg-team-img" src="<?php echo esc_url( $img_src ); ?>" alt="<?php echo $img_alt; // phpcs:ignore ?>" loading="lazy" decoding="async" fetchpriority="<?php echo esc_attr( $fetch ); ?>">
	<div class="eelfg-image-below-bg"></div>
	<div class="eelfg-image-overlay"></div>
	<?php if ( in_array( $skin, [ 'skin3', 'skin5' ], true ) && $details ) : ?>
		<div class="eelfg-image-content <?php echo 'skin5' === $skin ? 'has-description' : ''; ?>">
			<div class="eelfg-description"><?php echo nl2br( esc_html( $details ) ); ?></div>
		</div>
	<?php endif; ?>
</div>
<?php
$img_box_html = ob_get_clean();

// Name-deg-wrap (with optional details + default social).
$build_name_wrap = function ( $extra_class = '', $with_details = true, $with_social = true ) use ( $name_html, $designation, $details, $social_default ) {
	ob_start();
	?>
	<div class="eelfg-name-deg-wrap <?php echo esc_attr( $extra_class ); ?>">
		<?php echo $name_html; // phpcs:ignore ?>
		<?php if ( $designation ) : ?><div class="eelfg-designation"><?php echo esc_html( $designation ); ?></div><?php endif; ?>
		<?php if ( $with_details && $details ) : ?><div class="eelfg-team-description"><?php echo nl2br( esc_html( $details ) ); ?></div><?php endif; ?>
		<?php if ( $with_social ) { echo $social_default; } // phpcs:ignore ?>
	</div>
	<?php
	return ob_get_clean();
};

ob_start();
?>
<div <?php echo wp_kses_post( $block_wrap_attr ); ?>>
	<div class="eelfg-grid-wrap">
		<?php if ( 'skin1' === $skin ) : ?>
			<div class="eelfg-grid-item">
				<div class="eelfg-team-card">
					<div class="eelfg-team-left">
						<?php echo $open_link; // phpcs:ignore ?>
						<div class="eelfg-team-img-area"><?php echo $img_box_html; // phpcs:ignore ?></div>
						<?php echo $close_link; // phpcs:ignore ?>
					</div>
					<div class="eelfg-team-right">
						<?php echo $build_name_wrap( '', true, false ); // phpcs:ignore ?>
						<?php echo $social_default; // phpcs:ignore ?>
					</div>
				</div>
			</div>
		<?php elseif ( 'skin3' === $skin ) : ?>
			<div class="eelfg-grid-item skin3">
				<div class="eelfg-team-card">
					<?php echo $open_link; // phpcs:ignore ?>
					<div class="eelfg-team-img-area"><?php echo $img_box_html; // phpcs:ignore ?></div>
					<div class="eelfg-team-deg-content">
						<div class="eelfg-name-deg-wrap">
							<?php echo $name_html; // phpcs:ignore ?>
							<?php if ( $designation ) : ?><div class="eelfg-designation"><?php echo esc_html( $designation ); ?></div><?php endif; ?>
						</div>
					</div>
					<?php echo $close_link; // phpcs:ignore ?>
					<div class="eelfg-social-media"><?php echo $social_default; // phpcs:ignore ?></div>
					<?php echo $social_positioned; // phpcs:ignore ?>
				</div>
			</div>
		<?php elseif ( 'skin4' === $skin ) : ?>
			<div class="eelfg-grid-item">
				<div class="eelfg-team-card">
					<?php echo $open_link; // phpcs:ignore ?>
					<div class="eelfg-team-img-area"><?php echo $img_box_html; // phpcs:ignore ?></div>
					<?php echo $close_link; // phpcs:ignore ?>
					<div class="eelfg-team-hover-content <?php echo esc_attr( $overlay4 ); ?>">
						<div class="eelfg-name-deg-wrap">
							<?php echo $name_html; // phpcs:ignore ?>
							<?php if ( $designation ) : ?><div class="eelfg-designation"><?php echo esc_html( $designation ); ?></div><?php endif; ?>
						</div>
						<?php echo ( $show_social && ! empty( $social_links ) ) ? $social_html : ''; // phpcs:ignore ?>
					</div>
				</div>
			</div>
		<?php elseif ( 'skin5' === $skin ) : ?>
			<div class="eelfg-grid-item">
				<div class="eelfg-team-card skin5">
					<div class="eelfg-team-img-area">
						<?php echo $open_link; // phpcs:ignore ?>
						<?php echo $img_box_html; // phpcs:ignore ?>
						<?php echo $close_link; // phpcs:ignore ?>
						<div class="eelfg-name-deg-wrap">
							<div class="eelfg-author-content">
								<?php echo $name_html; // phpcs:ignore ?>
								<?php if ( $designation ) : ?><div class="eelfg-designation"><?php echo esc_html( $designation ); ?></div><?php endif; ?>
							</div>
							<?php if ( $show_contact && ( $email || $phone ) ) : ?>
								<div class="eelfg-author-contact">
									<div class="eelfg-contact-inner">
										<?php if ( $email ) : ?>
											<div class="eelfg-team-email eelfg-contact-item">
												<div class="eelfg-contact-icon"><?php echo $icon_i( $attributes['teamEmailIcon'] ?? '', $svg_mail ); // phpcs:ignore ?></div>
												<?php echo esc_html( $email ); ?>
											</div>
										<?php endif; ?>
										<?php if ( $phone ) : ?>
											<div class="eelfg-team-phone eelfg-contact-item">
												<div class="eelfg-contact-icon"><?php echo $icon_i( $attributes['teamPhoneIcon'] ?? '', $svg_phone ); // phpcs:ignore ?></div>
												<?php echo esc_html( $phone ); ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		<?php else : // default + skin2 ?>
			<div class="eelfg-grid-item">
				<div class="eelfg-team-card">
					<div class="eelfg-team-img-area">
						<?php echo $open_link; // phpcs:ignore ?>
						<?php echo $img_box_html; // phpcs:ignore ?>
						<?php echo $close_link; // phpcs:ignore ?>
						<?php if ( 'inside' === $content_show ) { echo $build_name_wrap( 'inside', true, true ); } // phpcs:ignore ?>
					</div>
					<?php if ( 'inside' !== $content_show ) { echo $build_name_wrap( '', true, true ); } // phpcs:ignore ?>
					<?php echo $social_positioned; // phpcs:ignore ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( 'popup' === $action ) : ?>
			<div id="<?php echo esc_attr( $popup_id ); ?>" class="eelfg-popup-modal" style="display:none;">
				<div class="eelfg-popup-content">
					<span class="eelfg-popup-close">&times;</span>
					<div class="eelfg-popup-header">
						<?php if ( $name_html ) : ?><div class="eelfg-popup-name"><?php echo $name_html; // phpcs:ignore ?></div><?php endif; ?>
						<?php if ( $designation ) : ?><div class="eelfg-popup-designation"><?php echo esc_html( $designation ); ?></div><?php endif; ?>
					</div>
					<div class="eelfg-popup-details">
						<?php echo $details ? nl2br( esc_html( $details ) ) : '<p>' . esc_html__( 'No additional details available.', 'easy-elements-for-gutenberg' ) . '</p>'; ?>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>
<?php
echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Markup assembled from escaped parts above.
