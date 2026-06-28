<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Local template/iteration variables.

/**
 * Server-side render for the Countdown block.
 *
 * Mirrors the markup of the Elementor "Countdown" widget
 * (easy-elements/widgets/countdown). Element classes use the "eelfg-" prefix.
 *
 * $attributes, $content and $block are provided by register_block_type().
 */

$H = '\EELFG\Frontend\Helper';

$unique_id = ! empty( $attributes['blockId'] ) ? $attributes['blockId'] : 'eelfg-cntdwn-' . substr( md5( wp_json_encode( $attributes ) ), 0, 6 );

// Target date (default: +1 day). Stored as "Y-m-d\TH:i" by the datetime-local control.
$target_raw = ! empty( $attributes['targetDate'] ) ? $attributes['targetDate'] : '';
$target_ts  = $target_raw ? strtotime( $target_raw ) : strtotime( '+1 day', current_time( 'timestamp', 1 ) );
if ( ! $target_ts ) {
	$target_ts = strtotime( '+1 day', current_time( 'timestamp', 1 ) );
}
$target_date = gmdate( 'Y-m-d H:i:s', $target_ts );

// Initial snapshot (so the numbers show before JS runs / in the SSR preview).
$now_ts   = current_time( 'timestamp' );
$distance = max( 0, $target_ts - $now_ts );
$i_days    = (int) floor( $distance / 86400 );
$i_hours   = (int) floor( ( $distance % 86400 ) / 3600 );
$i_minutes = (int) floor( ( $distance % 3600 ) / 60 );
$i_seconds = (int) ( $distance % 60 );

$allowed_sep = [ 'eelfg-cntdwn-space', 'eelfg-cntdwn-bullets', 'eelfg-cntdwn-dash' ];
$separator   = isset( $attributes['separator'] ) && in_array( $attributes['separator'], $allowed_sep, true ) ? $attributes['separator'] : 'eelfg-cntdwn-space';

$day_label     = isset( $attributes['dayLabel'] ) && '' !== $attributes['dayLabel'] ? $attributes['dayLabel'] : 'Days';
$hours_label   = isset( $attributes['hoursLabel'] ) && '' !== $attributes['hoursLabel'] ? $attributes['hoursLabel'] : 'Hours';
$minute_label  = isset( $attributes['minuteLabel'] ) && '' !== $attributes['minuteLabel'] ? $attributes['minuteLabel'] : 'Minutes';
$seconds_label = isset( $attributes['secondsLabel'] ) && '' !== $attributes['secondsLabel'] ? $attributes['secondsLabel'] : 'Seconds';

$block_wrap_attr = get_block_wrapper_attributes( array( 'class' => 'eelfg-block eelfg-countdown-block-wrap ' . $unique_id ) );
if ( empty( $block_wrap_attr ) ) {
	$block_wrap_attr = 'class="eelfg-block eelfg-countdown-block-wrap ' . esc_attr( $unique_id ) . '"';
}

// ---------------------------------------------------------------------------
// Inline styles (scoped to this instance via $unique_id).
// ---------------------------------------------------------------------------
$selector     = '.eelfg-countdown-block-wrap.' . $unique_id;
$style_handle = 'eelfg-countdown-style';

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
$shadow = function ( $obj ) use ( $H ) {
	if ( empty( $obj ) || ! is_array( $obj ) ) return [];
	$x = (int) ( $obj['x'] ?? 0 ); $y = (int) ( $obj['y'] ?? 0 ); $b = (int) ( $obj['b'] ?? 0 ); $s = (int) ( $obj['s'] ?? 0 );
	$c = $obj['c'] ?? '';
	$transparent = in_array( str_replace( ' ', '', (string) $c ), [ '', 'rgba(0,0,0,0)' ], true );
	if ( 0 === $x && 0 === $y && 0 === $b && 0 === $s && $transparent ) return [];
	return [ 'box-shadow' => $H::box_shadow_to_css( $obj ) ];
};
$u = function ( $key ) use ( $attributes, $H ) {
	return ( isset( $attributes[ $key ] ) && '' !== $attributes[ $key ] ) ? $H::ensure_unit( $attributes[ $key ] ) : '';
};

// Wrapper gap.
$cntdwn = [];
if ( '' !== $u( 'midGap' ) ) $cntdwn['gap'] = $u( 'midGap' );

// Item box.
$item = [];
if ( ! empty( $attributes['itemBgColor'] ) ) $item['background'] = $attributes['itemBgColor'];
$item = array_merge( $item, $shadow( $attributes['itemBoxShadow'] ?? [] ), $dims( $attributes['itemBorderRadius'] ?? [], 'radius' ), $dims( $attributes['itemPadding'] ?? [], 'padding' ) );
if ( ! empty( $attributes['itemBorder'] ) ) $item = array_merge( $item, $H::border_to_css_props( $attributes['itemBorder'] ) );

// Separator position + color (pseudo-elements).
$sep_pos = ( '' !== $u( 'separatorPositionX' ) ) ? [ 'left' => $u( 'separatorPositionX' ) ] : [];
$sep_col = ! empty( $attributes['separatorColor'] ) ? [ 'background' => $attributes['separatorColor'] ] : [];

// Label display (under number vs inline) + alignment.
$span = [ 'display' => ! empty( $attributes['labelUnderNumber'] ) ? 'block' : 'inline-block' ];
if ( ! empty( $attributes['labelUnderNumber'] ) && ! empty( $attributes['contentAlign'] ) ) {
	$span['text-align'] = $attributes['contentAlign'];
}

// Per-unit number + label styles.
$units = [ 'days', 'hours', 'minutes', 'seconds' ];
$unit_attr_map = [
	'days'    => [ 'typo' => 'daysTypography', 'color' => 'daysColor', 'lcolor' => 'daysLabelColor', 'ltypo' => 'daysLabelTypography' ],
	'hours'   => [ 'typo' => 'hoursTypography', 'color' => 'hoursColor', 'lcolor' => 'hoursLabelColor', 'ltypo' => 'hoursLabelTypography' ],
	'minutes' => [ 'typo' => 'minutesTypography', 'color' => 'minutesColor', 'lcolor' => 'minutesLabelColor', 'ltypo' => 'minutesLabelTypography' ],
	'seconds' => [ 'typo' => 'secondsTypography', 'color' => 'secondsColor', 'lcolor' => 'secondsLabelColor', 'ltypo' => 'secondsLabelTypography' ],
];

$sub_styles = [
	'.eelfg-cntdwn'        => $H::get_inline_styles( $cntdwn ),
	'.eelfg-cntdwn-item'   => $H::get_inline_styles( $item ),
	'.eelfg-cntdwn-item span' => $H::get_inline_styles( $span ),
];

if ( $sep_pos ) {
	$sub_styles['.eelfg-cntdwn-bullets::before, ' . $selector . ' .eelfg-cntdwn-bullets::after, ' . $selector . ' .eelfg-cntdwn-dash::before'] = $H::get_inline_styles( $sep_pos );
}
if ( $sep_col ) {
	$sub_styles['.eelfg-cntdwn-bullets::before, ' . $selector . ' .eelfg-cntdwn-bullets::after, ' . $selector . ' .eelfg-cntdwn-dash::before '] = $H::get_inline_styles( $sep_col );
}

foreach ( $units as $unit ) {
	$m = $unit_attr_map[ $unit ];
	$num_styles = $typo( $attributes[ $m['typo'] ] ?? [] );
	if ( ! empty( $attributes[ $m['color'] ] ) ) $num_styles['color'] = $attributes[ $m['color'] ];
	$label_styles = $typo( $attributes[ $m['ltypo'] ] ?? [] );
	if ( ! empty( $attributes[ $m['lcolor'] ] ) ) $label_styles['color'] = $attributes[ $m['lcolor'] ];
	$sub_styles[ '.eelfg-cntdwn-' . $unit ]            = $H::get_inline_styles( $num_styles );
	$sub_styles[ '.eelfg-cntdwn-' . $unit . '-label' ] = $H::get_inline_styles( $label_styles );
}

wp_enqueue_style( $style_handle );
$H::add_custom_style( $style_handle, $selector, '', $sub_styles );
?>
<div <?php echo wp_kses_post( $block_wrap_attr ); ?>>
	<div class="eelfg-cntdwn" data-target="<?php echo esc_attr( $target_date ); ?>">
		<div class="eelfg-cntdwn-item <?php echo esc_attr( $separator ); ?>"><span class="eelfg-cntdwn-days"><?php echo esc_html( $i_days ); ?></span> <span class="eelfg-cntdwn-days-label"><?php echo wp_kses_post( $day_label ); ?></span></div>
		<div class="eelfg-cntdwn-item <?php echo esc_attr( $separator ); ?>"><span class="eelfg-cntdwn-hours"><?php echo esc_html( $i_hours ); ?></span> <span class="eelfg-cntdwn-hours-label"><?php echo wp_kses_post( $hours_label ); ?></span></div>
		<div class="eelfg-cntdwn-item <?php echo esc_attr( $separator ); ?>"><span class="eelfg-cntdwn-minutes"><?php echo esc_html( $i_minutes ); ?></span> <span class="eelfg-cntdwn-minutes-label"><?php echo wp_kses_post( $minute_label ); ?></span></div>
		<div class="eelfg-cntdwn-item <?php echo esc_attr( $separator ); ?>"><span class="eelfg-cntdwn-seconds"><?php echo esc_html( $i_seconds ); ?></span> <span class="eelfg-cntdwn-seconds-label"><?php echo wp_kses_post( $seconds_label ); ?></span></div>
	</div>
</div>
