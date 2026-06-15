<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Vars below are local to this included template, not true globals.

$eelfg_allowed_metas = isset($attributes['allowedMetas']) ? $attributes['allowedMetas'] : [];
$eelfg_meta_html = '';
// $eelfg_meta_position = isset($attributes['metaPosition']) ? $attributes['metaPosition'] : 'up_title';

if(empty($eelfg_allowed_metas)) {
    return;
}

if ( in_array( 'author', $eelfg_allowed_metas ) ) {
        $author_icon = '<i class="eelfg-meta-icon eelfg-icon-user-avatar"></i>';
        if($meta_style == '1') {
            $author_icon = '';
        }else if($meta_style == '2' || $meta_style == '3') {
            $author_id = get_the_author_meta('ID');
            $avatar_url = get_avatar_url($author_id, array(
                'size' => 150
            ));

            $author_icon = '<img src="'. esc_url( $avatar_url )  .'">';
        }
        $eelfg_meta_html .= '<span class="bldpost-meta">' . $author_icon . '<a href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . esc_html( $attributes['authorPrefix'] ) . ' ' . esc_html( get_the_author() ) . '</a></span>';
}

$eelfg_date_html = '';
if ( in_array( 'date', $eelfg_allowed_metas ) && empty($attributes['showDateOnTop'])) {
    $date_icon = '<i class="eelfg-meta-icon eelfg-icon-calendar-two"></i>';
    $date = get_the_date();
    if(in_array($meta_style, ['1', '2'])) {
        $date_icon = '';
    }
    if(in_array($meta_style, ['2', '3'])) {
        $date = \EELFG\Frontend\Helper::eelfg_time_ago();
    }
    if(isset($modified_date) && $modified_date == false) {
        $date = get_the_date();
    }
    $eelfg_date_html = '<span class="bldpost-meta">' . $date_icon . esc_html( $date ) . '</span>';
}

$eelfg_category_html = '';
if ( in_array( 'category', $eelfg_allowed_metas ) ) {
    $eelfg_categories = get_the_category();
    if ( ! empty($eelfg_categories) ) {
        $cat_links = [];
        foreach ($eelfg_categories as $eelfg_category) {
            if ( $eelfg_category->slug === 'uncategorized' ) {
                continue;
            }
            $cat_links[] = '<a href="' . esc_url(get_category_link($eelfg_category->term_id)) . '" class="eelfg-meta-cat">' . esc_html( $eelfg_category->name ) . '</a>';
        }
        if ( ! empty( $cat_links ) ) {
            $eelfg_category_html  = '<span class="bldpost-meta"><i class="eelfg-meta-icon eelfg-icon-notification-status"></i>';
            $eelfg_category_html .= implode( ', ', $cat_links );
            $eelfg_category_html .= '</span>';
        }
    }
}

if ( $meta_style == '3' ) {
    $eelfg_meta_html .= $eelfg_category_html . $eelfg_date_html;
} else {
    $eelfg_meta_html .= $eelfg_date_html . $eelfg_category_html;
}
if ( in_array( 'tag', $eelfg_allowed_metas ) ) {
    $eelfg_tags = get_the_tags();
    if ( $eelfg_tags ) {
        $eelfg_meta_html .= '<span class="bldpost-meta"><i class="eelfg-meta-icon eelfg-icon-tags"></i>';
        $tag_links = [];
        foreach ($eelfg_tags as $eelfg_tag) {
            $tag_links[] = '<a href="' . esc_url(get_tag_link($eelfg_tag->term_id)) . '">' . esc_html( $eelfg_tag->name ) . '</a>';
        }
        $eelfg_meta_html .= implode( ',&nbsp;', $tag_links );
        $eelfg_meta_html .= '</span>';
    }
}

if ( in_array( 'comments_count', $eelfg_allowed_metas ) ) {
    $eelfg_meta_html .= '<span class="bldpost-meta"><i class="eelfg-meta-icon eelfg-icon-chat"></i>';
    $eelfg_meta_html .= get_comments_number();
    $eelfg_meta_html .= '</span>';
}

// Allowed meta types may still produce no output for this post (e.g. tag/category have no terms,
// or only 'date' is allowed but showDateOnTop is on). Skip the wrapper so we don't ship empty markup.
if ( '' === trim( $eelfg_meta_html ) ) {
    return;
}
// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
?>
<div class="eelfg-post-metas eelfg-post-metas-style-<?php echo esc_attr( $meta_style ); ?> eelfg-post-meta-position-<?php echo esc_attr( $meta_position ); ?>">
    <?php
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML is escaped during generation
    echo wp_kses_post( $eelfg_meta_html );
    ?>
</div>