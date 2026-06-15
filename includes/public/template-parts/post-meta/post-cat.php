<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Vars below are local to this included template, not true globals.

$eelfg_allowed_metas = isset($attributes['allowedMetas']) ? $attributes['allowedMetas'] : [];
$eelfg_meta_html = '';

if ( in_array( 'category', $eelfg_allowed_metas ) ) {
    $eelfg_categories = get_the_category();
    if ( ! empty($eelfg_categories) ) {
        $cat_links = '';
        foreach ($eelfg_categories as $eelfg_category) {
            if ( $eelfg_category->slug === 'uncategorized' ) {
                continue;
            }
            $cat_color = get_term_meta($eelfg_category->term_id, 'category_color', true);
            $dot_style = $cat_color ? 'background-color: ' . esc_attr( $cat_color ) : '';
            $cat_links = '<span class="eelfg-cat-dot" style="' . $dot_style . '"></span><a href="' . esc_url(get_category_link($eelfg_category->term_id)) . '">' . esc_html( $eelfg_category->name ) . '</a>';
            
            if ( ! empty( $cat_links ) ) {
                $eelfg_meta_html .= '<span class="bldpost-meta">';
                $eelfg_meta_html .= $cat_links;
                $eelfg_meta_html .= '</span>';
            }
        }

    }
}
// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
?>
<div class="eelfg-post-categories eelfg-post-categories-style-<?php echo esc_attr( $cat_style ); ?>">
    <?php 
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML is escaped during generation
    echo wp_kses_post( $eelfg_meta_html ); 
    ?>
</div>