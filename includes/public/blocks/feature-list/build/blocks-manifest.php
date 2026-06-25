<?php
// This file is generated. Do not modify it manually.
return array(
	'build' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'easy-elements-for-gutenberg/feature-list',
		'version' => '0.1.0',
		'title' => 'Feature List',
		'category' => 'easy-elements-for-gutenberg',
		'description' => 'A vertical list of features with icon, number or image, title, description and optional connector.',
		'keywords' => array(
			'feature',
			'list',
			'icon',
			'steps',
			'service'
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
			'features' => array(
				'type' => 'array',
				'default' => array(
					array(
						'iconType' => 'icon',
						'icon' => '',
						'number' => '01',
						'image' => array(
							
						),
						'title' => 'Lightning Fast',
						'desc' => 'Your site loads in seconds with our highly optimized structure.'
					),
					array(
						'iconType' => 'icon',
						'icon' => '',
						'number' => '02',
						'image' => array(
							
						),
						'title' => 'Smart & Flexible',
						'desc' => 'Build exactly what you envision with powerful styling controls.'
					),
					array(
						'iconType' => 'icon',
						'icon' => '',
						'number' => '03',
						'image' => array(
							
						),
						'title' => 'Reliable Support',
						'desc' => 'Our expert team is always ready to assist you with quick, friendly help.'
					)
				)
			),
			'feaDir' => array(
				'type' => 'string',
				'default' => 'left'
			),
			'feaVerticalAlign' => array(
				'type' => 'string',
				'default' => 'center'
			),
			'iconView' => array(
				'type' => 'string',
				'default' => 'stracked'
			),
			'iconShape' => array(
				'type' => 'string',
				'default' => 'rounded'
			),
			'titleTag' => array(
				'type' => 'string',
				'default' => 'h3'
			),
			'listBgColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'listBgGradient' => array(
				'type' => 'string',
				'default' => ''
			),
			'feaItemGap' => array(
				'type' => 'string',
				'default' => ''
			),
			'feaMiddleGap' => array(
				'type' => 'string',
				'default' => '20'
			),
			'feaConnector' => array(
				'type' => 'boolean',
				'default' => false
			),
			'feaConnectorLeft' => array(
				'type' => 'boolean',
				'default' => false
			),
			'feaConnectorType' => array(
				'type' => 'string',
				'default' => 'solid'
			),
			'feaConnectorWidth' => array(
				'type' => 'string',
				'default' => ''
			),
			'feaConnectorPositionX' => array(
				'type' => 'string',
				'default' => ''
			),
			'feaConnectorRightPositionX' => array(
				'type' => 'string',
				'default' => ''
			),
			'feaConnectorColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'feaListBorder' => array(
				'type' => 'object',
				'default' => array(
					'width' => 0,
					'color' => '',
					'style' => 'solid'
				)
			),
			'feaListBorderRadius' => array(
				'type' => 'object',
				'default' => array(
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => ''
				)
			),
			'feaListPadding' => array(
				'type' => 'object'
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
			'iconBoxSize' => array(
				'type' => 'string',
				'default' => ''
			),
			'iconAlignment' => array(
				'type' => 'string',
				'default' => 'center'
			),
			'iconShadow' => array(
				'type' => 'object',
				'default' => array(
					'x' => 0,
					'y' => 0,
					'b' => 0,
					's' => 0,
					'c' => 'rgba(0, 0, 0, 0)'
				)
			),
			'iconBorder' => array(
				'type' => 'object',
				'default' => array(
					'width' => 0,
					'color' => '',
					'style' => 'solid'
				)
			),
			'iconRadius' => array(
				'type' => 'object',
				'default' => array(
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => ''
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
			'titlePadding' => array(
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
			)
		)
	)
);
