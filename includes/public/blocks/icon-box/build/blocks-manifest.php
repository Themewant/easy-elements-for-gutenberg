<?php
// This file is generated. Do not modify it manually.
return array(
	'build' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'easy-elements-for-gutenberg/icon-box',
		'version' => '0.1.0',
		'title' => 'Info Box',
		'category' => 'easy-elements-for-gutenberg',
		'description' => 'An icon/info box with title, description, features, number and read-more button.',
		'keywords' => array(
			'box',
			'service',
			'icon',
			'icon-box',
			'info'
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
			'infoSkin' => array(
				'type' => 'string',
				'default' => 'default'
			),
			'iconType' => array(
				'type' => 'string',
				'default' => 'icon'
			),
			'icon' => array(
				'type' => 'string',
				'default' => ''
			),
			'iconImage' => array(
				'type' => 'object',
				'default' => array(
					
				)
			),
			'numberTitle' => array(
				'type' => 'string',
				'default' => ''
			),
			'numberGradient' => array(
				'type' => 'boolean',
				'default' => false
			),
			'title' => array(
				'type' => 'string',
				'default' => 'Manufacturing Industrial'
			),
			'titleTag' => array(
				'type' => 'string',
				'default' => 'h3'
			),
			'description' => array(
				'type' => 'string',
				'default' => 'Optimizing production and supply chain operations and generational transitions'
			),
			'features' => array(
				'type' => 'array',
				'default' => array(
					array(
						'icon' => '',
						'text' => 'Manufacturing Industrial'
					),
					array(
						'icon' => '',
						'text' => 'Supply Chain'
					)
				)
			),
			'linkUrl' => array(
				'type' => 'string',
				'default' => ''
			),
			'linkTarget' => array(
				'type' => 'boolean',
				'default' => false
			),
			'linkNofollow' => array(
				'type' => 'boolean',
				'default' => false
			),
			'enableBoxLink' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showReadMore' => array(
				'type' => 'boolean',
				'default' => false
			),
			'readMoreType' => array(
				'type' => 'string',
				'default' => 'read_text'
			),
			'readMoreText' => array(
				'type' => 'string',
				'default' => 'Read More'
			),
			'readMoreIcon' => array(
				'type' => 'string',
				'default' => ''
			),
			'readMoreTextIcon' => array(
				'type' => 'string',
				'default' => ''
			),
			'readMoreTextIconShow' => array(
				'type' => 'boolean',
				'default' => true
			),
			'readMoreAlignment' => array(
				'type' => 'string',
				'default' => ''
			),
			'buttonTextAlign' => array(
				'type' => 'string',
				'default' => ''
			),
			'iconDirection' => array(
				'type' => 'string',
				'default' => 'top'
			),
			'iconVerticalAlignment' => array(
				'type' => 'string',
				'default' => 'center'
			),
			'textAlign' => array(
				'type' => 'string',
				'default' => 'center'
			),
			'itemSpacing' => array(
				'type' => 'string',
				'default' => ''
			),
			'itemBgColor' => array(
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
			'itemBoxShadow' => array(
				'type' => 'object',
				'default' => array(
					'x' => 0,
					'y' => 0,
					'b' => 0,
					's' => 0,
					'c' => 'rgba(0, 0, 0, 0)'
				)
			),
			'itemBorderRadius' => array(
				'type' => 'object',
				'default' => array(
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => ''
				)
			),
			'itemPadding' => array(
				'type' => 'object'
			),
			'itemMargin' => array(
				'type' => 'object'
			),
			'itemHoverBgDirection' => array(
				'type' => 'string',
				'default' => 'default'
			),
			'itemHoverBgColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'itemHoverBorderColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'itemHoverBoxShadow' => array(
				'type' => 'object',
				'default' => array(
					'x' => 0,
					'y' => 0,
					'b' => 0,
					's' => 0,
					'c' => 'rgba(0, 0, 0, 0)'
				)
			),
			'imageSize' => array(
				'type' => 'string',
				'default' => ''
			),
			'imageBoxSize' => array(
				'type' => 'string',
				'default' => ''
			),
			'imageBgColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'imageBorder' => array(
				'type' => 'object',
				'default' => array(
					'width' => 0,
					'color' => '',
					'style' => 'solid'
				)
			),
			'imageBoxBorderRadius' => array(
				'type' => 'object',
				'default' => array(
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => ''
				)
			),
			'imageBorderRadius' => array(
				'type' => 'object',
				'default' => array(
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => ''
				)
			),
			'imagePadding' => array(
				'type' => 'object'
			),
			'imageMargin' => array(
				'type' => 'object'
			),
			'imageHoverBgColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'iconColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'iconBgColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'iconSize' => array(
				'type' => 'string',
				'default' => ''
			),
			'iconBoxSize' => array(
				'type' => 'string',
				'default' => ''
			),
			'iconBorder' => array(
				'type' => 'object',
				'default' => array(
					'width' => 0,
					'color' => '',
					'style' => 'solid'
				)
			),
			'iconBorderRadius' => array(
				'type' => 'object',
				'default' => array(
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => ''
				)
			),
			'iconMargin' => array(
				'type' => 'object'
			),
			'iconBoxShadow' => array(
				'type' => 'object',
				'default' => array(
					'x' => 0,
					'y' => 0,
					'b' => 0,
					's' => 0,
					'c' => 'rgba(0, 0, 0, 0)'
				)
			),
			'iconRotate' => array(
				'type' => 'string',
				'default' => ''
			),
			'gradientBorder' => array(
				'type' => 'boolean',
				'default' => false
			),
			'iconHoverColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'iconHoverBgColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'numberColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'numberBgColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'numberTypography' => array(
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
			'numberAlignment' => array(
				'type' => 'string',
				'default' => ''
			),
			'numberPadding' => array(
				'type' => 'object'
			),
			'numberMargin' => array(
				'type' => 'object'
			),
			'titleColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'titleTypography' => array(
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
			'titleMargin' => array(
				'type' => 'object'
			),
			'titleHoverColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'descColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'descTypography' => array(
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
			'descMargin' => array(
				'type' => 'object'
			),
			'descHoverColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'featureTextColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'featureIconColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'featureTextColorHover' => array(
				'type' => 'string',
				'default' => ''
			),
			'featureIconColorHover' => array(
				'type' => 'string',
				'default' => ''
			),
			'featureTypography' => array(
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
			'featureMargin' => array(
				'type' => 'object'
			),
			'featureIconGap' => array(
				'type' => 'string',
				'default' => ''
			),
			'featureIconSize' => array(
				'type' => 'string',
				'default' => ''
			),
			'readMoreIconColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'readMoreIconBgColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'readMoreIconPadding' => array(
				'type' => 'object'
			),
			'readMoreIconBorderRadius' => array(
				'type' => 'object',
				'default' => array(
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => ''
				)
			),
			'readMoreIconSize' => array(
				'type' => 'string',
				'default' => ''
			),
			'readMoreTextColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'readMoreTextBgColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'readMoreTextBorder' => array(
				'type' => 'object',
				'default' => array(
					'width' => 0,
					'color' => '',
					'style' => 'solid'
				)
			),
			'readMoreTextBorderRadius' => array(
				'type' => 'object',
				'default' => array(
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => ''
				)
			),
			'readMoreTextPadding' => array(
				'type' => 'object'
			),
			'readMoreTypography' => array(
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
			'readMoreMargin' => array(
				'type' => 'object'
			),
			'readMoreTextColorHover' => array(
				'type' => 'string',
				'default' => ''
			),
			'readMoreBgHover' => array(
				'type' => 'string',
				'default' => ''
			),
			'readMoreHoverBorderColor' => array(
				'type' => 'string',
				'default' => ''
			)
		)
	)
);
