<?php
// This file is generated. Do not modify it manually.
return array(
	'build' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'easy-elements-for-gutenberg/category-list',
		'version' => '0.1.0',
		'title' => 'Category List',
		'category' => 'easy-elements-for-gutenberg',
		'description' => 'A list or grid of taxonomy terms (categories) with icons and post counts.',
		'keywords' => array(
			'category',
			'categories',
			'taxonomy',
			'term',
			'list'
		),
		'example' => array(
			
		),
		'supports' => array(
			'html' => false,
			'align' => array(
				'wide',
				'full'
			),
			'spacing' => array(
				'padding' => array(
					'top',
					'bottom',
					'left',
					'right'
				),
				'margin' => array(
					'top',
					'bottom',
					'left',
					'right'
				)
			)
		),
		'textdomain' => 'easy-elements-for-gutenberg',
		'editorScript' => 'file:./index.js',
		'render' => 'file:./render.php',
		'attributes' => array(
			'blockId' => array(
				'type' => 'string',
				'default' => ''
			),
			'postType' => array(
				'type' => 'string',
				'default' => 'post'
			),
			'taxonomy' => array(
				'type' => 'string',
				'default' => 'category'
			),
			'number' => array(
				'type' => 'string',
				'default' => '0'
			),
			'orderby' => array(
				'type' => 'string',
				'default' => 'name'
			),
			'order' => array(
				'type' => 'string',
				'default' => 'ASC'
			),
			'hideEmpty' => array(
				'type' => 'boolean',
				'default' => false
			),
			'showCount' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showIcon' => array(
				'type' => 'boolean',
				'default' => true
			),
			'catIcon' => array(
				'type' => 'string',
				'default' => ''
			),
			'layoutCategory' => array(
				'type' => 'string',
				'default' => 'list'
			),
			'columns' => array(
				'type' => 'string',
				'default' => '3'
			),
			'columnsTablet' => array(
				'type' => 'string',
				'default' => '2'
			),
			'columnsMobile' => array(
				'type' => 'string',
				'default' => '1'
			),
			'itemsGap' => array(
				'type' => 'string',
				'default' => ''
			),
			'itemPadding' => array(
				'type' => 'object'
			),
			'itemRadius' => array(
				'type' => 'object',
				'default' => array(
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => ''
				)
			),
			'itemBgColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'itemBgGradient' => array(
				'type' => 'string',
				'default' => ''
			),
			'itemBorder' => array(
				'type' => 'object',
				'default' => array(
					'width' => 0,
					'color' => '',
					'style' => 'solid'
				)
			),
			'itemBgHoverColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'itemBgHoverGradient' => array(
				'type' => 'string',
				'default' => ''
			),
			'itemBorderColorHover' => array(
				'type' => 'string',
				'default' => ''
			),
			'iconSize' => array(
				'type' => 'string',
				'default' => ''
			),
			'iconColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'iconColorHover' => array(
				'type' => 'string',
				'default' => ''
			),
			'iconSpacing' => array(
				'type' => 'string',
				'default' => ''
			),
			'nameColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'nameColorHover' => array(
				'type' => 'string',
				'default' => ''
			),
			'nameTypography' => array(
				'type' => 'object',
				'default' => array(
					'fontFamily' => '',
					'fontSize' => '',
					'fontWeight' => '',
					'fontStyle' => '',
					'textTransform' => '',
					'lineHeight' => '',
					'letterSpacing' => ''
				)
			),
			'countColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'countTypography' => array(
				'type' => 'object',
				'default' => array(
					'fontFamily' => '',
					'fontSize' => '',
					'fontWeight' => '',
					'fontStyle' => '',
					'textTransform' => '',
					'lineHeight' => '',
					'letterSpacing' => ''
				)
			)
		)
	)
);
