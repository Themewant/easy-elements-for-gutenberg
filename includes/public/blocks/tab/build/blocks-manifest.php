<?php
// This file is generated. Do not modify it manually.
return array(
	'build' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'easy-elements-for-gutenberg/tab',
		'version' => '0.1.0',
		'title' => 'Tabs',
		'category' => 'easy-elements-for-gutenberg',
		'description' => 'Tabbed content with icon/image titles, content title, description and a button — horizontal or vertical layouts.',
		'keywords' => array(
			'tab',
			'tabs',
			'link',
			'click',
			'content'
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
		'viewScript' => 'file:./view.js',
		'render' => 'file:./render.php',
		'attributes' => array(
			'blockId' => array(
				'type' => 'string',
				'default' => ''
			),
			'tabs' => array(
				'type' => 'array',
				'default' => array(
					array(
						'tabTitle' => 'Our Company',
						'iconType' => 'icon',
						'icon' => '',
						'image' => array(
							
						),
						'contentTitle' => '',
						'contentDescription' => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using Content here, content here, making it look like readable English.',
						'readMoreText' => '',
						'readMoreUrl' => '#',
						'readMoreNewTab' => false
					),
					array(
						'tabTitle' => 'Our Mission',
						'iconType' => 'icon',
						'icon' => '',
						'image' => array(
							
						),
						'contentTitle' => '',
						'contentDescription' => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using Content here, content here, making it look like readable English.',
						'readMoreText' => '',
						'readMoreUrl' => '#',
						'readMoreNewTab' => false
					),
					array(
						'tabTitle' => 'Our Vision',
						'iconType' => 'icon',
						'icon' => '',
						'image' => array(
							
						),
						'contentTitle' => '',
						'contentDescription' => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using Content here, content here, making it look like readable English.',
						'readMoreText' => '',
						'readMoreUrl' => '#',
						'readMoreNewTab' => false
					)
				)
			),
			'iconPosition' => array(
				'type' => 'string',
				'default' => 'left'
			),
			'layoutDirection' => array(
				'type' => 'string',
				'default' => 'top'
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
			'titleColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'titleBgColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'titleBorder' => array(
				'type' => 'object',
				'default' => array(
					'width' => 0,
					'color' => '',
					'style' => 'solid'
				)
			),
			'titleBorderRadius' => array(
				'type' => 'object',
				'default' => array(
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => ''
				)
			),
			'titlePadding' => array(
				'type' => 'object'
			),
			'titleMargin' => array(
				'type' => 'object'
			),
			'tabIconColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'tabIconBgColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'titleActiveColor' => array(
				'type' => 'string',
				'default' => '#ffffff'
			),
			'titleActiveBgColor' => array(
				'type' => 'string',
				'default' => '#5933FF'
			),
			'titleActiveBorderColor' => array(
				'type' => 'string',
				'default' => '#5933FF'
			),
			'titleBottomSpacing' => array(
				'type' => 'string',
				'default' => ''
			),
			'navBorder' => array(
				'type' => 'object',
				'default' => array(
					'width' => 0,
					'color' => '',
					'style' => 'solid'
				)
			),
			'contentBgColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'contentBorder' => array(
				'type' => 'object',
				'default' => array(
					'width' => 0,
					'color' => '',
					'style' => 'solid'
				)
			),
			'contentBorderRadius' => array(
				'type' => 'object',
				'default' => array(
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => ''
				)
			),
			'contentPadding' => array(
				'type' => 'object'
			),
			'contentMargin' => array(
				'type' => 'object'
			),
			'descriptionAlignment' => array(
				'type' => 'string',
				'default' => 'center'
			),
			'contentTitleTypography' => array(
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
			'contentTitleColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'contentTitleMargin' => array(
				'type' => 'object'
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
			'descColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'descMargin' => array(
				'type' => 'object'
			),
			'btnTypography' => array(
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
			'btnColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'btnBgColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'btnHoverColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'btnHoverBgColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'btnHoverBorderColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'btnBorder' => array(
				'type' => 'object',
				'default' => array(
					'width' => 0,
					'color' => '',
					'style' => 'solid'
				)
			),
			'btnBorderRadius' => array(
				'type' => 'object',
				'default' => array(
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => ''
				)
			),
			'btnPadding' => array(
				'type' => 'object'
			),
			'btnMargin' => array(
				'type' => 'object'
			)
		)
	)
);
