<?php
// This file is generated. Do not modify it manually.
return array(
	'build' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'easy-elements-for-gutenberg/process-grid',
		'version' => '0.1.0',
		'title' => 'Process Grid',
		'category' => 'easy-elements-for-gutenberg',
		'description' => 'A grid of process / service boxes with icon, title, description, link and a large watermark step number or icon.',
		'keywords' => array(
			'process',
			'service',
			'icon box',
			'steps',
			'grid'
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
			'items' => array(
				'type' => 'array',
				'default' => array(
					array(
						'icon' => 'eelfg-icon-favorite',
						'title' => 'Manufacturing Industrial',
						'description' => 'Optimizing production and supply chain operations and generational transitions',
						'link' => '',
						'linkNewTab' => false,
						'linkNofollow' => false,
						'numberType' => 'p_number',
						'processNumber' => '1',
						'processIcon' => 'eelfg-icon-arrow-right'
					),
					array(
						'icon' => 'eelfg-icon-favorite',
						'title' => 'Professional Services',
						'description' => 'Growth strategies for knowledge-based businesses with strategic guidance throughout',
						'link' => '',
						'linkNewTab' => false,
						'linkNofollow' => false,
						'numberType' => 'p_number',
						'processNumber' => '2',
						'processIcon' => 'eelfg-icon-arrow-right'
					),
					array(
						'icon' => 'eelfg-icon-favorite',
						'title' => 'Technology & SaaS',
						'description' => 'Scaling strategies for rapid growth and market leadership expertise for companies facing challenges',
						'link' => '',
						'linkNewTab' => false,
						'linkNofollow' => false,
						'numberType' => 'p_number',
						'processNumber' => '3',
						'processIcon' => 'eelfg-icon-arrow-right'
					)
				)
			),
			'iconDirection' => array(
				'type' => 'string',
				'default' => 'top'
			),
			'textAlign' => array(
				'type' => 'string',
				'default' => 'center'
			),
			'titleTag' => array(
				'type' => 'string',
				'default' => 'h3'
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
			'itemPadding' => array(
				'type' => 'object'
			),
			'boxBgColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'boxPadding' => array(
				'type' => 'object'
			),
			'boxBorder' => array(
				'type' => 'object',
				'default' => array(
					'width' => 0,
					'color' => '',
					'style' => 'solid'
				)
			),
			'boxRadius' => array(
				'type' => 'object',
				'default' => array(
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => ''
				)
			),
			'boxShadow' => array(
				'type' => 'object',
				'default' => array(
					'x' => 0,
					'y' => 0,
					'b' => 0,
					's' => 0,
					'c' => 'rgba(0, 0, 0, 0)'
				)
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
			'numberOpacity' => array(
				'type' => 'string',
				'default' => ''
			),
			'numberOffsetY' => array(
				'type' => 'string',
				'default' => ''
			),
			'numberOffsetX' => array(
				'type' => 'string',
				'default' => ''
			)
		)
	)
);
