<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Local template/iteration variables.

/**
 * Server-side render for the Search block.
 *
 * Mirrors the markup of the Elementor "Search" widget
 * (easy-elements/widgets/search). Element classes use the "eelfg-" prefix.
 *
 * $attributes, $content and $block are provided by register_block_type().
 */

$H = '\EELFG\Frontend\Helper';

$unique_id = ! empty( $attributes['blockId'] ) ? $attributes['blockId'] : 'eelfg-search-' . substr( md5( wp_json_encode( $attributes ) ), 0, 6 );

$style       = ( isset( $attributes['selectStyle'] ) && '2' === $attributes['selectStyle'] ) ? '2' : '1';
$placeholder = isset( $attributes['placeholder'] ) ? $attributes['placeholder'] : '';
$title       = isset( $attributes['searchTitle'] ) && '' !== $attributes['searchTitle'] ? $attributes['searchTitle'] : __( 'What are you looking for?', 'easy-elements-for-gutenberg' );
$open_icon   = isset( $attributes['openIcon'] ) ? $attributes['openIcon'] : '';
$close_icon  = isset( $attributes['closeIcon'] ) ? $attributes['closeIcon'] : '';

$block_wrap_attr = get_block_wrapper_attributes( array( 'class' => 'eelfg-block eelfg-search-block-wrap ' . $unique_id ) );
if ( empty( $block_wrap_attr ) ) {
	$block_wrap_attr = 'class="eelfg-block eelfg-search-block-wrap ' . esc_attr( $unique_id ) . '"';
}

// ---------------------------------------------------------------------------
// Inline styles (scoped to this instance).
// ---------------------------------------------------------------------------
$selector     = '.eelfg-search-block-wrap.' . $unique_id;
$style_handle = 'eelfg-search-style';

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
$u = function ( $key ) use ( $attributes, $H ) {
	return ( isset( $attributes[ $key ] ) && '' !== $attributes[ $key ] ) ? $H::ensure_unit( $attributes[ $key ] ) : '';
};

// Icons.
$icon_glyph = [];
if ( '' !== $u( 'iconSize' ) ) { $s = $u( 'iconSize' ); $icon_glyph['font-size'] = $s; $icon_glyph['width'] = $s; $icon_glyph['height'] = $s; }
$open_i  = $icon_glyph;
$open_svg = $icon_glyph;
if ( ! empty( $attributes['iconColor'] ) ) { $open_i['color'] = $attributes['iconColor']; $open_svg['fill'] = $attributes['iconColor']; }
$open_btn = ( '' !== $u( 'iconVerticalPosition' ) ) ? [ 'transform' => 'translateY(' . $u( 'iconVerticalPosition' ) . ')' ] : [];

// Style 2 submit icon position.
$submit2_pos = [];
$side = ( isset( $attributes['iconPositionSide'] ) && 'left' === $attributes['iconPositionSide'] ) ? 'left' : 'right';
if ( 'left' === $side ) {
	$submit2_pos['right'] = 'auto';
	$submit2_pos['left']  = ( '' !== $u( 'iconOffsetLeft' ) ) ? $u( 'iconOffsetLeft' ) : '15px';
} else {
	$submit2_pos['left'] = 'auto';
	if ( '' !== $u( 'iconOffsetRight' ) ) $submit2_pos['right'] = $u( 'iconOffsetRight' );
}

// Input field (shared).
$field = $typo( $attributes['inputTypography'] ?? [] );
if ( ! empty( $attributes['inputTextColor'] ) ) $field['color'] = $attributes['inputTextColor'];
if ( ! empty( $attributes['inputBgColor'] ) ) $field['background'] = $attributes['inputBgColor'];
$field = array_merge( $field, $dims( $attributes['inputPadding'] ?? [], 'padding' ) );
if ( '' !== $u( 'inputHeight' ) ) $field['height'] = $u( 'inputHeight' );
$field2 = [];
if ( '' !== $u( 'inputFieldWidth' ) ) $field2['width'] = $u( 'inputFieldWidth' );
$field2 = array_merge( $field2, $dims( $attributes['inputBorderRadius'] ?? [], 'radius' ) );
if ( ! empty( $attributes['inputBorderColor'] ) ) $field2['border-color'] = $attributes['inputBorderColor'];
$placeholder_styles = ! empty( $attributes['placeholderColor'] ) ? [ 'color' => $attributes['placeholderColor'] ] : [];

// Submit button.
$submit1 = [];
if ( ! empty( $attributes['submitIconColor'] ) ) $submit1['color'] = $attributes['submitIconColor'];
if ( ! empty( $attributes['submitBtnBg'] ) ) $submit1['background-color'] = $attributes['submitBtnBg'];
$submit1 = array_merge( $submit1, $dims( $attributes['submitPadding'] ?? [], 'padding' ) );
$submit1_svg = ! empty( $attributes['submitIconColor'] ) ? [ 'fill' => $attributes['submitIconColor'] ] : [];
$submit1_hover = [];
if ( ! empty( $attributes['submitIconHoverColor'] ) ) $submit1_hover['color'] = $attributes['submitIconHoverColor'];
if ( ! empty( $attributes['submitBtnHoverBg'] ) ) $submit1_hover['background-color'] = $attributes['submitBtnHoverBg'];
$submit2 = $submit2_pos;
if ( ! empty( $attributes['submitBtnBg'] ) ) $submit2['background-color'] = $attributes['submitBtnBg'];
$submit2 = array_merge( $submit2, $dims( $attributes['submitPadding'] ?? [], 'padding' ) );

// Popup.
$overlay = ! empty( $attributes['overlayBg'] ) ? [ 'background' => $attributes['overlayBg'] ] : [];
$popup_title = $typo( $attributes['popupTitleTypography'] ?? [] );
if ( ! empty( $attributes['popupTitleColor'] ) ) $popup_title['color'] = $attributes['popupTitleColor'];
$close_styles = [];
if ( ! empty( $attributes['closeIconColor'] ) ) $close_styles['color'] = $attributes['closeIconColor'];
$close_svg = ! empty( $attributes['closeIconColor'] ) ? [ 'fill' => $attributes['closeIconColor'] ] : [];
$close_glyph = ( '' !== $u( 'closeIconSize' ) ) ? [ 'font-size' => $u( 'closeIconSize' ), 'width' => $u( 'closeIconSize' ), 'height' => $u( 'closeIconSize' ) ] : [];

wp_enqueue_style( $style_handle );
$H::add_custom_style( $style_handle, $selector, '', [
	'.eelfg-search-open-btn'                            => $H::get_inline_styles( $open_btn ),
	'.eelfg-search-open-btn i, ' . $selector . ' .eelfg-search-submit-btn i' => $H::get_inline_styles( $open_i ),
	'.eelfg-search-open-btn svg, ' . $selector . ' .eelfg-search-submit-btn svg' => $H::get_inline_styles( $open_svg ),
	'.eelfg-search-field'                               => $H::get_inline_styles( $field ),
	'.eelfg-search-style-2 .eelfg-search-field'         => $H::get_inline_styles( $field2 ),
	'.eelfg-search-field::placeholder'                  => $H::get_inline_styles( $placeholder_styles ),
	'.eelfg-search-style-2 .eelfg-search-field:focus'   => ! empty( $attributes['inputFocusBorderColor'] ) ? 'border-color:' . $attributes['inputFocusBorderColor'] : '',
	'.eelfg-search-content .eelfg-search-submit'        => $H::get_inline_styles( $submit1 ),
	'.eelfg-search-content .eelfg-search-submit svg'    => $H::get_inline_styles( $submit1_svg ),
	'.eelfg-search-content .eelfg-search-submit:hover'  => $H::get_inline_styles( $submit1_hover ),
	'.eelfg-search-style-2 .eelfg-search-submit-btn'    => $H::get_inline_styles( $submit2 ),
	'.eelfg-search-lightbox, ' . $selector . ' .eelfg-search-overlay' => $H::get_inline_styles( $overlay ),
	'.eelfg-search-title'                               => $H::get_inline_styles( $popup_title ),
	'.eelfg-search-close-btn i'                         => $H::get_inline_styles( array_merge( $close_styles, $close_glyph ) ),
	'.eelfg-search-close-btn svg'                       => $H::get_inline_styles( array_merge( $close_svg, $close_glyph ) ),
] );

// Icons / fallbacks.
$svg_search = '<svg viewBox="0 0 24 24" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M21 20l-5.6-5.6a7 7 0 10-1.4 1.4L20 21zM5 10.5a5.5 5.5 0 1111 0 5.5 5.5 0 01-11 0z"></path></svg>';
$svg_up     = '<svg viewBox="0 0 24 24" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M12 4l8 8-1.4 1.4L13 7.8V20h-2V7.8L5.4 13.4 4 12z"></path></svg>';
$render_icon = function ( $val, $fallback ) {
	return ( ! empty( $val ) && 'none' !== $val ) ? '<i class="eelfg-icon ' . esc_attr( $val ) . '" aria-hidden="true"></i>' : $fallback;
};
$action = esc_url( home_url( '/' ) );
?>
<div <?php echo wp_kses_post( $block_wrap_attr ); ?>>
	<?php if ( '2' === $style ) : ?>
		<div class="eelfg-search eelfg-search-style-2">
			<form role="search" method="get" class="eelfg-search-form" action="<?php echo $action; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
				<input type="search" class="eelfg-search-field" placeholder="<?php echo esc_attr( $placeholder ); ?>" value="" name="s" />
				<button type="submit" class="eelfg-search-submit-btn" aria-label="<?php esc_attr_e( 'Submit Search', 'easy-elements-for-gutenberg' ); ?>">
					<?php echo $render_icon( $open_icon, $svg_search ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
			</form>
		</div>
	<?php else : ?>
		<div class="eelfg-search eelfg-search-style-1">
			<a href="#" role="button" class="eelfg-search-open-btn" aria-label="<?php esc_attr_e( 'Open Search', 'easy-elements-for-gutenberg' ); ?>">
				<?php echo $render_icon( $open_icon, $svg_search ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
			<div class="eelfg-search-lightbox">
				<div class="eelfg-search-overlay">
					<a href="#" role="button" class="eelfg-search-close-btn" aria-label="<?php esc_attr_e( 'Close Search', 'easy-elements-for-gutenberg' ); ?>">
						<?php echo $render_icon( $close_icon, $svg_up ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
				</div>
				<div class="eelfg-search-content">
					<div class="eelfg-search-title"><?php echo esc_html( $title ); ?></div>
					<form role="search" method="get" class="eelfg-search-form" action="<?php echo $action; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
						<input type="search" class="eelfg-search-field" placeholder="<?php echo esc_attr( $placeholder ); ?>" value="" name="s" />
						<button type="submit" class="eelfg-search-submit" aria-label="<?php esc_attr_e( 'Submit Search', 'easy-elements-for-gutenberg' ); ?>">
							<?php echo $svg_search; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</button>
					</form>
				</div>
			</div>
		</div>
	<?php endif; ?>
</div>
