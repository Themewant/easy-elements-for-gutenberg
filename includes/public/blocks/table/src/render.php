<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Local template/iteration variables.

/**
 * Server-side render for the Table block.
 *
 * Mirrors the markup of the Elementor "Table" widget
 * (easy-elements/widgets/table). Element classes use the "eelfg-" prefix.
 *
 * $attributes, $content and $block are provided by register_block_type().
 */

$H = '\EELFG\Frontend\Helper';

$unique_id = ! empty( $attributes['blockId'] ) ? $attributes['blockId'] : 'eelfg-table-' . substr( md5( wp_json_encode( $attributes ) ), 0, 6 );

$header = isset( $attributes['tableHeader'] ) && is_array( $attributes['tableHeader'] ) ? $attributes['tableHeader'] : [];
$body   = isset( $attributes['tableBody'] ) && is_array( $attributes['tableBody'] ) ? $attributes['tableBody'] : [];
$footer = isset( $attributes['tableFooter'] ) && is_array( $attributes['tableFooter'] ) ? $attributes['tableFooter'] : [];

$tooltip_align = isset( $attributes['tooltipAlign'] ) ? $attributes['tooltipAlign'] : 'top';

$block_wrap_attr = get_block_wrapper_attributes( array( 'class' => 'eelfg-block eelfg-table-block-wrap ' . $unique_id ) );
if ( empty( $block_wrap_attr ) ) {
	$block_wrap_attr = 'class="eelfg-block eelfg-table-block-wrap ' . esc_attr( $unique_id ) . '"';
}

if ( empty( $header ) && empty( $body ) && empty( $footer ) ) {
	echo '<div ' . wp_kses_post( $block_wrap_attr ) . '><p>' . esc_html__( 'Please add table cells.', 'easy-elements-for-gutenberg' ) . '</p></div>';
	return;
}

// ---------------------------------------------------------------------------
// Section-level inline styles (scoped to this instance).
// ---------------------------------------------------------------------------
$selector     = '.eelfg-table-block-wrap.' . $unique_id;
$style_handle = 'eelfg-table-style';

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
$u = function ( $key ) use ( $attributes, $H ) {
	return ( isset( $attributes[ $key ] ) && '' !== $attributes[ $key ] ) ? $H::ensure_unit( $attributes[ $key ] ) : '';
};

// General.
$valign_cells = ! empty( $attributes['verticalAlignTable'] ) ? [ 'vertical-align' => $attributes['verticalAlignTable'] ] : [];
$table_box    = array_merge( $dims( $attributes['tableMargin'] ?? [], 'margin' ), $dims( $attributes['tableRadius'] ?? [], 'radius' ) );
$cell_padding = $dims( $attributes['tablePadding'] ?? [], 'padding' );
$tbody_border = ! empty( $attributes['tableBorder'] ) ? $H::border_to_css_props( $attributes['tableBorder'] ) : [];

// Header.
$head = [];
if ( ! empty( $attributes['headerAlign'] ) ) $head['text-align'] = $attributes['headerAlign'];
if ( ! empty( $attributes['headerTextColor'] ) ) $head['color'] = $attributes['headerTextColor'];
if ( ! empty( $attributes['headerBgColor'] ) ) $head['background-color'] = $attributes['headerBgColor'];
$head = array_merge( $head, $typo( $attributes['headerTypography'] ?? [] ) );
$head_th = array_merge( $dims( $attributes['theadRadius'] ?? [], 'radius' ), $dims( $attributes['theadPadding'] ?? [], 'padding' ) );
if ( ! empty( $attributes['headBorder'] ) ) $head_th = array_merge( $head_th, $H::border_to_css_props( $attributes['headBorder'] ) );
$head_icon = [];
if ( ! empty( $attributes['headerIconColor'] ) ) $head_icon['color'] = $attributes['headerIconColor'];
if ( '' !== $u( 'headerIconSize' ) ) $head_icon['font-size'] = $u( 'headerIconSize' );
$head_icon = array_merge( $head_icon, $dims( $attributes['headerIconMargin'] ?? [], 'margin' ) );
$head_icon_svg = [];
if ( ! empty( $attributes['headerIconColor'] ) ) $head_icon_svg['fill'] = $attributes['headerIconColor'];
if ( '' !== $u( 'headerIconSize' ) ) { $head_icon_svg['width'] = $u( 'headerIconSize' ); $head_icon_svg['height'] = $u( 'headerIconSize' ); }
$head_icon_wrap = ( '' !== $u( 'headerIconYPos' ) ) ? [ 'top' => $u( 'headerIconYPos' ) ] : [];

// Body.
$bd = [];
if ( ! empty( $attributes['bodyAlign'] ) ) $bd['text-align'] = $attributes['bodyAlign'];
if ( ! empty( $attributes['bodyTextColor'] ) ) $bd['color'] = $attributes['bodyTextColor'];
if ( ! empty( $attributes['bodyBgColor'] ) ) $bd['background-color'] = $attributes['bodyBgColor'];
$bd = array_merge( $bd, $typo( $attributes['bodyTypography'] ?? [] ) );
$bd_striped = ( ! empty( $attributes['stripedBg'] ) && ! empty( $attributes['stripedBgColor'] ) ) ? [ 'background-color' => $attributes['stripedBgColor'] ] : [];
$bd_icon = [];
if ( ! empty( $attributes['bodyIconColor'] ) ) $bd_icon['color'] = $attributes['bodyIconColor'];
if ( '' !== $u( 'bodyIconSize' ) ) $bd_icon['font-size'] = $u( 'bodyIconSize' );
$bd_icon = array_merge( $bd_icon, $dims( $attributes['bodyIconGap'] ?? [], 'padding' ) );
$bd_icon_svg = [];
if ( ! empty( $attributes['bodyIconColor'] ) ) $bd_icon_svg['fill'] = $attributes['bodyIconColor'];
if ( '' !== $u( 'bodyIconSize' ) ) { $bd_icon_svg['width'] = $u( 'bodyIconSize' ); $bd_icon_svg['height'] = $u( 'bodyIconSize' ); }
$bd_td = array_merge( $dims( $attributes['tbodyRadius'] ?? [], 'radius' ), $dims( $attributes['tbodyPadding'] ?? [], 'padding' ), $dims( $attributes['tbodyMargin'] ?? [], 'margin' ) );
if ( ! empty( $attributes['bodyBorder'] ) ) $bd_td = array_merge( $bd_td, $H::border_to_css_props( $attributes['bodyBorder'] ) );
$tip_icon = [];
if ( ! empty( $attributes['tooltipIconColor'] ) ) $tip_icon['color'] = $attributes['tooltipIconColor'];
if ( '' !== $u( 'tooltipIconSize' ) ) $tip_icon['font-size'] = $u( 'tooltipIconSize' );
$tip_icon = array_merge( $tip_icon, $dims( $attributes['tooltipIconMargin'] ?? [], 'margin' ) );
$tip_icon_svg = [];
if ( ! empty( $attributes['tooltipIconColor'] ) ) $tip_icon_svg['fill'] = $attributes['tooltipIconColor'];
if ( '' !== $u( 'tooltipIconSize' ) ) { $tip_icon_svg['width'] = $u( 'tooltipIconSize' ); $tip_icon_svg['height'] = $u( 'tooltipIconSize' ); }
$img = ( '' !== $u( 'imgSize' ) ) ? [ 'max-width' => $u( 'imgSize' ), 'height' => $u( 'imgSize' ) ] : [];
$img = array_merge( $img, $dims( $attributes['imgRadius'] ?? [], 'radius' ) );

// Footer.
$ft = [];
if ( ! empty( $attributes['footerAlign'] ) ) $ft['text-align'] = $attributes['footerAlign'];
if ( ! empty( $attributes['footerTextColor'] ) ) $ft['color'] = $attributes['footerTextColor'];
if ( ! empty( $attributes['footerBgColor'] ) ) $ft['background-color'] = $attributes['footerBgColor'];
$ft = array_merge( $ft, $typo( $attributes['footerTypography'] ?? [] ) );
$ft_th = array_merge( $dims( $attributes['tfootRadius'] ?? [], 'radius' ), $dims( $attributes['tfootPadding'] ?? [], 'padding' ) );
if ( ! empty( $attributes['footBorder'] ) ) $ft_th = array_merge( $ft_th, $H::border_to_css_props( $attributes['footBorder'] ) );

$sub = [
	'.eelfg-table-body td, ' . $selector . ' .eelfg-table-body th' => $H::get_inline_styles( $valign_cells ),
	'.eelfg-table'                          => $H::get_inline_styles( $table_box ),
	'.eelfg-table-header th, ' . $selector . ' .eelfg-table-body td' => $H::get_inline_styles( $cell_padding ),
	'.eelfg-table-body'                     => $H::get_inline_styles( array_merge( $tbody_border, $bd ) ),

	'.eelfg-table-header'                   => $H::get_inline_styles( $head ),
	'.eelfg-table-header th'                => $H::get_inline_styles( $head_th ),
	'.eelfg-header-icon'                    => $H::get_inline_styles( $head_icon_wrap ),
	'.eelfg-header-icon i'                  => $H::get_inline_styles( $head_icon ),
	'.eelfg-header-icon svg'                => $H::get_inline_styles( $head_icon_svg ),

	'.eelfg-table-body tr:nth-of-type(2n)'  => $H::get_inline_styles( $bd_striped ),
	'.eelfg-table-body td i'                => $H::get_inline_styles( $bd_icon ),
	'.eelfg-table-body td svg'              => $H::get_inline_styles( $bd_icon_svg ),
	'.eelfg-table-body td'                  => $H::get_inline_styles( $bd_td ),
	'.eelfg-tbl-tooltip i'                  => $H::get_inline_styles( $tip_icon ),
	'.eelfg-tbl-tooltip svg'                => $H::get_inline_styles( $tip_icon_svg ),
	'.eelfg-table-image'                    => $H::get_inline_styles( $img ),

	'.eelfg-table-footer'                   => $H::get_inline_styles( $ft ),
	'.eelfg-table-footer th'                => $H::get_inline_styles( $ft_th ),
];

wp_enqueue_style( $style_handle );
$H::add_custom_style( $style_handle, $selector, '', $sub );

// ---------------------------------------------------------------------------
// Per-cell helpers.
// ---------------------------------------------------------------------------
$default_tip_icon = '<svg viewBox="0 0 24 24" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 15h-2v-2h2v2zm0-3.6h-2c0-2 2.5-2.2 2.5-3.9A1.5 1.5 0 0012 8a1.6 1.6 0 00-1.6 1.5H8.4A3.6 3.6 0 0112 6a3.5 3.5 0 013.5 3.5c0 2-2.5 2.3-2.5 3.9z"/></svg>';

$render_icon = function ( $val ) {
	return ( ! empty( $val ) && 'none' !== $val ) ? '<i class="eelfg-icon ' . esc_attr( $val ) . '" aria-hidden="true"></i>' : '';
};

// Build the per-cell inline style attribute from "advance" settings.
$cell_style = function ( $item ) use ( $H ) {
	$adv = ! empty( $item['advance'] );
	$styles = [];
	if ( $adv ) {
		if ( ! empty( $item['width'] ) ) $styles[] = 'width:' . $item['width'];
		if ( empty( $item['dataFlex'] ) && ! empty( $item['align'] ) ) $styles[] = 'text-align:' . $item['align'];
		if ( ! empty( $item['verticalAlign'] ) ) $styles[] = 'vertical-align:' . $item['verticalAlign'];
		if ( ! empty( $item['decoration'] ) ) $styles[] = 'text-decoration:' . $item['decoration'];
		if ( ! empty( $item['bgColor'] ) ) $styles[] = 'background-color:' . $item['bgColor'];
		if ( ! empty( $item['textColor'] ) ) $styles[] = 'color:' . $item['textColor'];
		if ( ! empty( $item['dataFlex'] ) ) {
			if ( ! empty( $item['flexAlign'] ) ) $styles[] = 'justify-content:' . $item['flexAlign'];
			if ( '' !== ( $item['flexGap'] ?? '' ) ) $styles[] = 'gap:' . $H::ensure_unit( $item['flexGap'] );
		}
	}
	return implode( ';', $styles );
};

$cell_attrs = function ( $item ) {
	$adv  = ! empty( $item['advance'] );
	$out  = '';
	if ( $adv && ! empty( $item['colspan'] ) && '' !== ( $item['colspanNumber'] ?? '' ) ) {
		$out .= ' colspan="' . esc_attr( (int) $item['colspanNumber'] ) . '"';
	}
	if ( $adv && ! empty( $item['rowspan'] ) && '' !== ( $item['rowspanNumber'] ?? '' ) ) {
		$out .= ' rowspan="' . esc_attr( (int) $item['rowspanNumber'] ) . '"';
	}
	return $out;
};

$tooltip_html = function ( $item ) use ( $render_icon, $default_tip_icon, $tooltip_align ) {
	if ( empty( $item['tooltip'] ) || '' === ( $item['tooltipDesc'] ?? '' ) ) {
		return '';
	}
	$icon = $render_icon( $item['tooltipIcon'] ?? '' );
	if ( '' === $icon ) {
		$icon = $default_tip_icon;
	}
	return '<span class="eelfg-tbl-tooltip" data-placement="' . esc_attr( $tooltip_align ) . '">'
		. $icon // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		. '<span class="eelfg-tbl-tooltip-content">' . esc_html( $item['tooltipDesc'] ) . '</span>'
		. '</span>';
};
?>
<div <?php echo wp_kses_post( $block_wrap_attr ); ?>>
	<table class="eelfg-table">
		<?php if ( ! empty( $header ) ) : ?>
			<thead class="eelfg-table-header">
				<tr>
					<?php
					foreach ( $header as $item ) {
						$style = $cell_style( $item );
						$icon  = ( ! empty( $item['headerIcon'] ) ) ? $render_icon( $item['headIcon'] ?? '' ) : '';
						echo '<th class="eelfg-th"' . $cell_attrs( $item ) . ( $style ? ' style="' . esc_attr( $style ) . '"' : '' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						if ( '' !== $icon ) {
							echo '<span class="eelfg-header-icon">' . $icon . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						echo wp_kses_post( $item['text'] ?? '' );
						echo $tooltip_html( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo '</th>';
					}
					?>
				</tr>
			</thead>
		<?php endif; ?>

		<?php if ( ! empty( $body ) ) : ?>
			<tbody class="eelfg-table-body">
				<tr>
					<?php
					foreach ( $body as $index => $item ) {
						if ( $index > 0 && ! empty( $item['row'] ) ) {
							echo '</tr><tr>';
						}
						$type      = isset( $item['type'] ) ? $item['type'] : 'icon';
						$flex      = ! empty( $item['dataFlex'] ) ? ' eelfg-data-flex' : '';
						$style     = $cell_style( $item );
						$icon_color = ( ! empty( $item['advance'] ) && ! empty( $item['iconColor'] ) ) ? ' style="color:' . esc_attr( $item['iconColor'] ) . '"' : '';

						echo '<td class="eelfg-td' . esc_attr( $flex ) . '"' . $cell_attrs( $item ) . ( $style ? ' style="' . esc_attr( $style ) . '"' : '' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

						if ( 'image' === $type && ! empty( $item['image']['url'] ) ) {
							echo '<img src="' . esc_url( $item['image']['url'] ) . '" class="eelfg-table-image" alt="' . esc_attr( $item['image']['alt'] ?? '' ) . '">';
						} elseif ( 'icon' === $type ) {
							$ic = $render_icon( $item['icon'] ?? '' );
							if ( '' !== $ic ) {
								echo '<span' . $icon_color . '>' . $ic . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
						}

						echo wp_kses_post( $item['text'] ?? '' );
						echo $tooltip_html( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo '</td>';
					}
					?>
				</tr>
			</tbody>
		<?php endif; ?>

		<?php if ( ! empty( $footer ) ) : ?>
			<tfoot class="eelfg-table-footer">
				<tr>
					<?php
					foreach ( $footer as $item ) {
						$style = $cell_style( $item );
						echo '<th class="eelfg-tf"' . $cell_attrs( $item ) . ( $style ? ' style="' . esc_attr( $style ) . '"' : '' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo wp_kses_post( $item['text'] ?? '' );
						echo $tooltip_html( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo '</th>';
					}
					?>
				</tr>
			</tfoot>
		<?php endif; ?>
	</table>
</div>
