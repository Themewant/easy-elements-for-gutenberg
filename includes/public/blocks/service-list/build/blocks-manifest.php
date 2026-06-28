<?php
// This file is generated. Do not modify it manually.
return array(
	'build' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'easy-elements-for-gutenberg/service-list',
		'version' => '0.1.0',
		'title' => 'Service List',
		'category' => 'easy-elements-for-gutenberg',
		'description' => 'A service item with icon / image / number, title, description and read-more — three skins.',
		'keywords' => array(
			'service',
			'icon box',
			'feature',
			'skin'
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
			'skinStyle' => array(
				'type' => 'string',
				'default' => 'skin1'
			),
			'mediaType' => array(
				'type' => 'string',
				'default' => 'icon'
			),
			'serviceIcon' => array(
				'type' => 'string',
				'default' => 'eelfg-icon-favorite'
			),
			'serviceImage' => array(
				'type' => 'object',
				'default' => array(
					
				)
			),
			'title' => array(
				'type' => 'string',
				'default' => 'Managed IT Services'
			),
			'number' => array(
				'type' => 'string',
				'default' => '1'
			),
			'titleTag' => array(
				'type' => 'string',
				'default' => 'h3'
			),
			'description' => array(
				'type' => 'string',
				'default' => 'Professional monitoring and management of your IT systems 24/7/365, ensuring optimal performance and security.'
			),
			'readmoreType' => array(
				'type' => 'string',
				'default' => 'readmore'
			),
			'readmoreText' => array(
				'type' => 'string',
				'default' => 'Read More'
			),
			'readmoreIcon' => array(
				'type' => 'string',
				'default' => ''
			),
			'readmoreIconSpacing' => array(
				'type' => 'string',
				'default' => ''
			),
			'readmoreLink' => array(
				'type' => 'string',
				'default' => '#'
			),
			'linkNewTab' => array(
				'type' => 'boolean',
				'default' => false
			),
			'linkNofollow' => array(
				'type' => 'boolean',
				'default' => false
			),
			'readmoreIconColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'readmoreIconBgColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'readmoreIconBgGradient' => array(
				'type' => 'string',
				'default' => ''
			),
			'circleSizeOnlyIcon' => array(
				'type' => 'string',
				'default' => '100'
			),
			'circleBorderRadiusOnlyIcon' => array(
				'type' => 'object',
				'default' => array(
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => ''
				)
			),
			'circleIconSizeOnly' => array(
				'type' => 'string',
				'default' => '25'
			),
			'vAlign' => array(
				'type' => 'string',
				'default' => ''
			),
			'mediaContentGap' => array(
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
			'iconBgGradient' => array(
				'type' => 'string',
				'default' => ''
			),
			'iconSize' => array(
				'type' => 'string',
				'default' => ''
			),
			'iconPadding' => array(
				'type' => 'object'
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
			'circleBgColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'circleBgGradient' => array(
				'type' => 'string',
				'default' => ''
			),
			'circleSize' => array(
				'type' => 'string',
				'default' => ''
			),
			'circleBorderRadius' => array(
				'type' => 'object',
				'default' => array(
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => ''
				)
			),
			'circlePadding' => array(
				'type' => 'object'
			),
			'circleBorder' => array(
				'type' => 'object',
				'default' => array(
					'width' => 0,
					'color' => '',
					'style' => 'solid'
				)
			),
			'numberColor' => array(
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
			'readmoreColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'readmoreTypography' => array(
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
			'readmoreBgColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'readmoreBgGradient' => array(
				'type' => 'string',
				'default' => ''
			),
			'readmorePadding' => array(
				'type' => 'object'
			),
			'readmoreBorderRadius' => array(
				'type' => 'object',
				'default' => array(
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => ''
				)
			),
			'readmoreBorder' => array(
				'type' => 'object',
				'default' => array(
					'width' => 0,
					'color' => '',
					'style' => 'solid'
				)
			),
			'readmoreHoverColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'readmoreHoverBgColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'readmoreHoverBgGradient' => array(
				'type' => 'string',
				'default' => ''
			),
			'readmoreHoverBorderColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'imageHeight' => array(
				'type' => 'string',
				'default' => ''
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
			'circleHoverColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'lineColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'lineColorHover' => array(
				'type' => 'string',
				'default' => ''
			)
		)
	)
);
