<?php
// This file is generated. Do not modify it manually.
return array(
	'build' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'easy-elements-for-gutenberg/process-list',
		'version' => '0.1.0',
		'title' => 'Process List',
		'category' => 'easy-elements-for-gutenberg',
		'description' => 'A single process / step row with a big number, icon, title (with highlight) and description.',
		'keywords' => array(
			'process',
			'step',
			'icon box',
			'list',
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
			'icon' => array(
				'type' => 'string',
				'default' => 'eelfg-icon-favorite'
			),
			'title' => array(
				'type' => 'string',
				'default' => 'Initial Consultation'
			),
			'description' => array(
				'type' => 'string',
				'default' => 'We start by understanding your vision, needs, lifestyle, and budget to establish the foundation of your project.'
			),
			'link' => array(
				'type' => 'string',
				'default' => ''
			),
			'linkNewTab' => array(
				'type' => 'boolean',
				'default' => false
			),
			'linkNofollow' => array(
				'type' => 'boolean',
				'default' => false
			),
			'processNumber' => array(
				'type' => 'string',
				'default' => '01'
			),
			'titleTag' => array(
				'type' => 'string',
				'default' => 'h3'
			),
			'alignVertical' => array(
				'type' => 'string',
				'default' => 'center'
			),
			'itemGap' => array(
				'type' => 'string',
				'default' => '64'
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
			'iconPadding' => array(
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
			'iconBoxSize' => array(
				'type' => 'string',
				'default' => ''
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
			'titleSubColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'titleSubTypography' => array(
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
			'titleSubMargin' => array(
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
			)
		)
	)
);
