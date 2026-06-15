<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Local template variables.

$allowed_tags = [ 'div', 'section', 'article', 'main', 'header', 'footer', 'aside' ];
$tag          = isset( $attributes['htmlTag'] ) && in_array( $attributes['htmlTag'], $allowed_tags, true )
    ? $attributes['htmlTag']
    : 'div';

$content_width = isset( $attributes['contentWidth'] ) ? $attributes['contentWidth'] : 'boxed';
$vertical      = isset( $attributes['verticalAlign'] ) ? $attributes['verticalAlign'] : '';
$equal_height  = ! empty( $attributes['equalHeight'] );
$stretch       = ! empty( $attributes['stretchColumns'] );
$custom_class  = isset( $attributes['customClass'] ) ? trim( (string) $attributes['customClass'] ) : '';
$unique_id     = ! empty( $attributes['blockId'] )
    ? sanitize_html_class( $attributes['blockId'] )
    : 'eelfg-layout-row-' . wp_rand( 100, 99999 );

$selector = '.' . $unique_id;

$row_responsive = [ 'desktop' => [], 'tablet' => [], 'mobile' => [] ];

// Flexbox controls (D/T/M).
\EELFG\Frontend\Helper::add_responsive_vars( $attributes, $row_responsive, 'flexDirection',  'flex-direction' );
\EELFG\Frontend\Helper::add_responsive_vars( $attributes, $row_responsive, 'justifyContent', 'justify-content' );
\EELFG\Frontend\Helper::add_responsive_vars( $attributes, $row_responsive, 'alignItems',     'align-items' );
\EELFG\Frontend\Helper::add_responsive_vars( $attributes, $row_responsive, 'alignContent',   'align-content' );
\EELFG\Frontend\Helper::add_responsive_vars( $attributes, $row_responsive, 'flexWrap',       'flex-wrap' );
\EELFG\Frontend\Helper::add_responsive_vars( $attributes, $row_responsive, 'gap',            'gap' );
\EELFG\Frontend\Helper::add_responsive_vars( $attributes, $row_responsive, 'rowGap',         'row-gap' );
\EELFG\Frontend\Helper::add_responsive_vars( $attributes, $row_responsive, 'columnGap',      'column-gap' );
\EELFG\Frontend\Helper::add_responsive_vars( $attributes, $row_responsive, 'minHeight',      'min-height' );

// Padding / margin (object, D/T/M).
\EELFG\Frontend\Helper::add_responsive_vars( $attributes, $row_responsive, 'padding', '', [
    'top'    => 'padding-top',
    'right'  => 'padding-right',
    'bottom' => 'padding-bottom',
    'left'   => 'padding-left',
], true );
\EELFG\Frontend\Helper::add_responsive_vars( $attributes, $row_responsive, 'margin', '', [
    'top'    => 'margin-top',
    'right'  => 'margin-right',
    'bottom' => 'margin-bottom',
    'left'   => 'margin-left',
], true );

// Background.
if ( ! empty( $attributes['background'] ) ) {
    $row_responsive['desktop']['background-color'] = $attributes['background'];
}

$bg_image_url = ! empty( $attributes['backgroundImage']['url'] ) ? esc_url_raw( $attributes['backgroundImage']['url'] ) : '';
$bg_gradient  = ! empty( $attributes['backgroundGradient'] ) ? $attributes['backgroundGradient'] : '';

if ( $bg_image_url && $bg_gradient ) {
    $row_responsive['desktop']['background-image'] = $bg_gradient . ', url(' . $bg_image_url . ')';
} elseif ( $bg_image_url ) {
    $row_responsive['desktop']['background-image'] = 'url(' . $bg_image_url . ')';
} elseif ( $bg_gradient ) {
    $row_responsive['desktop']['background-image'] = $bg_gradient;
}

if ( $bg_image_url ) {
    if ( ! empty( $attributes['backgroundSize'] ) )       $row_responsive['desktop']['background-size']       = $attributes['backgroundSize'];
    if ( ! empty( $attributes['backgroundPosition'] ) )   $row_responsive['desktop']['background-position']   = $attributes['backgroundPosition'];
    if ( ! empty( $attributes['backgroundRepeat'] ) )     $row_responsive['desktop']['background-repeat']     = $attributes['backgroundRepeat'];
    if ( ! empty( $attributes['backgroundAttachment'] ) ) $row_responsive['desktop']['background-attachment'] = $attributes['backgroundAttachment'];
}

// Border.
if ( ! empty( $attributes['border'] ) ) {
    foreach ( \EELFG\Frontend\Helper::border_to_css_props( $attributes['border'] ) as $prop => $val ) {
        $row_responsive['desktop'][ $prop ] = $val;
    }
}

// Border radius.
$radius = isset( $attributes['borderRadius'] ) ? $attributes['borderRadius'] : [];
if ( ! empty( $radius['top'] ) )    $row_responsive['desktop']['border-top-left-radius']     = \EELFG\Frontend\Helper::ensure_unit( $radius['top'] );
if ( ! empty( $radius['right'] ) )  $row_responsive['desktop']['border-top-right-radius']    = \EELFG\Frontend\Helper::ensure_unit( $radius['right'] );
if ( ! empty( $radius['bottom'] ) ) $row_responsive['desktop']['border-bottom-right-radius'] = \EELFG\Frontend\Helper::ensure_unit( $radius['bottom'] );
if ( ! empty( $radius['left'] ) )   $row_responsive['desktop']['border-bottom-left-radius']  = \EELFG\Frontend\Helper::ensure_unit( $radius['left'] );

// Box shadow.
if ( ! empty( $attributes['boxShadow'] ) && ! empty( $attributes['boxShadow']['c'] ) && $attributes['boxShadow']['c'] !== 'rgba(0,0,0,0)' ) {
    $row_responsive['desktop']['box-shadow'] = \EELFG\Frontend\Helper::box_shadow_to_css( $attributes['boxShadow'] );
}

// Advanced.
if ( ! empty( $attributes['overflow'] ) ) $row_responsive['desktop']['overflow'] = $attributes['overflow'];
if ( ! empty( $attributes['position'] ) ) $row_responsive['desktop']['position'] = $attributes['position'];
if ( $attributes['zIndex'] !== '' && $attributes['zIndex'] !== null ) {
    $z = trim( (string) $attributes['zIndex'] );
    if ( $z !== '' ) $row_responsive['desktop']['z-index'] = (int) $z;
}

// CSS custom props for the column-width calc — columns read these via cascade and
// compute their own width as `calc(W% - (cols-1) * gap * W / 100)` so non-zero gap
// never pushes the row to overflow. See column/src/render.php.
$col_count = ( isset( $block ) && isset( $block->parsed_block['innerBlocks'] ) )
    ? count( $block->parsed_block['innerBlocks'] )
    : 0;
if ( $col_count > 0 ) {
    $row_responsive['desktop']['--bp-cols'] = $col_count;
}
foreach ( [ '' => 'desktop', 'Tablet' => 'tablet', 'Mobile' => 'mobile' ] as $sfx => $dev ) {
    // Prefer explicit columnGap, fall back to the gap shorthand.
    foreach ( [ 'columnGap', 'gap' ] as $base ) {
        $val = isset( $attributes[ $base . $sfx ] ) ? trim( (string) $attributes[ $base . $sfx ] ) : '';
        if ( $val !== '' ) {
            $row_responsive[ $dev ]['--bp-gap'] = \EELFG\Frontend\Helper::ensure_unit( $val );
            break;
        }
    }
}

// Max-width for boxed mode. Write the CSS variable on the row wrapper — the SCSS
// in style.scss already reads it on the inner via var(--eelfg-layout-row-max-width).
// Setting it as a direct max-width inline would lose a specificity fight with the
// SCSS .is-content-boxed > .__inner rule, so use the variable.
if ( $content_width === 'boxed' ) {
    \EELFG\Frontend\Helper::add_responsive_vars( $attributes, $row_responsive, 'maxWidth', '--eelfg-layout-row-max-width' );
}

// Compile CSS.
$style_handle = 'eelfg-layout-row-style';
$css  = \EELFG\Frontend\Helper::generate_responsive_css( $selector, $row_responsive );

wp_enqueue_style( $style_handle );
\EELFG\Frontend\Helper::add_custom_style( $style_handle, $selector, $css, [] );

$classes = [
    'eelfg-block',
    'eelfg-layout-row',
    $unique_id,
    'is-content-' . sanitize_html_class( $content_width ),
];
if ( $vertical )     $classes[] = 'is-valign-' . sanitize_html_class( $vertical );
if ( $equal_height ) $classes[] = 'is-equal-height';
if ( $stretch )      $classes[] = 'is-stretch';
if ( $custom_class ) {
    foreach ( explode( ' ', $custom_class ) as $c ) {
        $c = sanitize_html_class( $c );
        if ( $c ) $classes[] = $c;
    }
}

$wrapper_attrs = get_block_wrapper_attributes( [ 'class' => implode( ' ', $classes ) ] );

// Output.
printf(
    '<%1$s %2$s><div class="eelfg-layout-row__inner">%3$s</div></%1$s>',
    tag_escape( $tag ),
    $wrapper_attrs, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from get_block_wrapper_attributes()
    $content        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $content is pre-rendered inner blocks HTML
);
