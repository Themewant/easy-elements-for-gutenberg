import { __ } from '@wordpress/i18n';
import { useEffect } from '@wordpress/element';
import { ServerSideRender } from '@wordpress/server-side-render';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	ToggleControl,
	TextControl,
	BoxControl,
	__experimentalDivider as Divider,
} from '@wordpress/components';

import ColorPopover from '../../custom-components/ColorPopover';
import IconPicker from '../../custom-components/IconPicker';
import TypographyControls from '../../custom-components/TypographyControls';
import BorderControl from '../../custom-components/BorderControl';
import BoxShadowControls from '../../custom-components/BoxShadowControls';

import './editor.scss';

const ALIGN_OPTIONS = [
	{ label: __('Left', 'easy-elements-for-gutenberg'), value: 'flex-start' },
	{ label: __('Center', 'easy-elements-for-gutenberg'), value: 'center' },
	{ label: __('Right', 'easy-elements-for-gutenberg'), value: 'flex-end' },
];

export default function Edit({ attributes, setAttributes, clientId }) {
	const {
		blockId,
		buttonText,
		buttonUrl,
		buttonTarget,
		buttonNofollow,
		buttonType,
		buttonIcon,
		iconPosition,
		showGradient,
		borderGradientButton,
	} = attributes;

	useEffect(() => {
		if (!blockId) {
			setAttributes({ blockId: 'eelfg-button-' + clientId.slice(0, 6) });
		}
	}, [blockId, clientId, setAttributes]);

	const color = (label, key) => (
		<ColorPopover label={label} color={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />
	);
	const typo = (label, key) => (
		<TypographyControls label={label} attributes={attributes} setAttributes={setAttributes} attributeKey={key} />
	);
	const border = (label, key) => (
		<BorderControl label={label} value={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />
	);
	const shadow = (label, key) => (
		<BoxShadowControls label={label} value={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />
	);
	const box = (label, key) => (
		<BoxControl label={label} values={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />
	);
	const num = (label, key) => (
		<TextControl
			label={label}
			type="number"
			value={attributes[key]}
			onChange={(v) => setAttributes({ [key]: v })}
			__next40pxDefaultSize
			__nextHasNoMarginBottom
		/>
	);

	const hasIcon = buttonIcon && buttonIcon !== 'none';

	return (
		<div {...useBlockProps()}>
			<InspectorControls>
				<PanelBody title={__('Button', 'easy-elements-for-gutenberg')} initialOpen={true}>
					<TextControl
						label={__('Button Text', 'easy-elements-for-gutenberg')}
						value={buttonText}
						onChange={(v) => setAttributes({ buttonText: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<TextControl
						label={__('Button URL', 'easy-elements-for-gutenberg')}
						type="url"
						value={buttonUrl}
						onChange={(v) => setAttributes({ buttonUrl: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<ToggleControl label={__('Open in new tab', 'easy-elements-for-gutenberg')} checked={buttonTarget} onChange={(v) => setAttributes({ buttonTarget: v })} __nextHasNoMarginBottom />
					<ToggleControl label={__('Add nofollow', 'easy-elements-for-gutenberg')} checked={buttonNofollow} onChange={(v) => setAttributes({ buttonNofollow: v })} __nextHasNoMarginBottom />
					<Divider />
					<SelectControl
						label={__('Button Type', 'easy-elements-for-gutenberg')}
						value={buttonType}
						options={[
							{ label: __('Primary', 'easy-elements-for-gutenberg'), value: 'primary' },
							{ label: __('Outline', 'easy-elements-for-gutenberg'), value: 'outline' },
							{ label: __('Icon', 'easy-elements-for-gutenberg'), value: 'icon_btn' },
						]}
						onChange={(v) => setAttributes({ buttonType: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<IconPicker label={__('Icon', 'easy-elements-for-gutenberg')} value={buttonIcon} onChange={(v) => setAttributes({ buttonIcon: v })} />
					{hasIcon && (
						<>
							<SelectControl
								label={__('Icon Position', 'easy-elements-for-gutenberg')}
								value={iconPosition}
								options={[
									{ label: __('Before Text', 'easy-elements-for-gutenberg'), value: 'before' },
									{ label: __('After Text', 'easy-elements-for-gutenberg'), value: 'after' },
								]}
								onChange={(v) => setAttributes({ iconPosition: v })}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
							{num(__('Icon Spacing (px)', 'easy-elements-for-gutenberg'), 'iconSpacing')}
						</>
					)}
					<Divider />
					{num(__('Minimum Width (px)', 'easy-elements-for-gutenberg'), 'minWidth')}
					<SelectControl
						label={__('Content Alignment', 'easy-elements-for-gutenberg')}
						value={attributes.buttonAlignment}
						options={ALIGN_OPTIONS}
						onChange={(v) => setAttributes({ buttonAlignment: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</PanelBody>

				<PanelBody title={__('Gradient', 'easy-elements-for-gutenberg')} initialOpen={false}>
					<ToggleControl label={__('Gradient Button', 'easy-elements-for-gutenberg')} checked={showGradient} onChange={(v) => setAttributes({ showGradient: v })} __nextHasNoMarginBottom />
					{showGradient && (
						<>
							{color(__('Gradient 1', 'easy-elements-for-gutenberg'), 'gradient1')}
							{color(__('Gradient 2', 'easy-elements-for-gutenberg'), 'gradient2')}
							{color(__('Gradient 3', 'easy-elements-for-gutenberg'), 'gradient3')}
						</>
					)}
					<Divider />
					<ToggleControl label={__('Border Gradient Button', 'easy-elements-for-gutenberg')} checked={borderGradientButton} onChange={(v) => setAttributes({ borderGradientButton: v })} __nextHasNoMarginBottom />
					{borderGradientButton && (
						<>
							{color(__('Border Gradient 1', 'easy-elements-for-gutenberg'), 'borderGradientColor1')}
							{color(__('Border Gradient 2', 'easy-elements-for-gutenberg'), 'borderGradientColor2')}
						</>
					)}
				</PanelBody>
			</InspectorControls>

			<InspectorControls group="styles">
				<PanelBody title={__('Button', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'buttonTypography')}
					<Divider />
					{color(__('Text Color', 'easy-elements-for-gutenberg'), 'textColor')}
					{color(__('Background', 'easy-elements-for-gutenberg'), 'bgColor')}
					{border(__('Border', 'easy-elements-for-gutenberg'), 'buttonBorder')}
					{shadow(__('Box Shadow', 'easy-elements-for-gutenberg'), 'buttonBoxShadow')}
					<Divider />
					{color(__('Text Color (Hover)', 'easy-elements-for-gutenberg'), 'textColorHover')}
					{color(__('Background (Hover)', 'easy-elements-for-gutenberg'), 'bgColorHover')}
					{border(__('Border (Hover)', 'easy-elements-for-gutenberg'), 'buttonBorderHover')}
					<Divider />
					{box(__('Border Radius', 'easy-elements-for-gutenberg'), 'buttonBorderRadius')}
					<Divider />
					{box(__('Padding', 'easy-elements-for-gutenberg'), 'buttonPadding')}
					<Divider />
					{box(__('Margin', 'easy-elements-for-gutenberg'), 'buttonMargin')}
				</PanelBody>

				{hasIcon && (
					<PanelBody title={__('Icon', 'easy-elements-for-gutenberg')} initialOpen={false}>
						{color(__('Color', 'easy-elements-for-gutenberg'), 'iconColor')}
						{color(__('Background', 'easy-elements-for-gutenberg'), 'iconBg')}
						{num(__('Size (px)', 'easy-elements-for-gutenberg'), 'iconSize')}
						{num(__('Box Width (px)', 'easy-elements-for-gutenberg'), 'iconBoxWidth')}
						{num(__('Box Height (px)', 'easy-elements-for-gutenberg'), 'iconBoxHeight')}
						{box(__('Box Border Radius', 'easy-elements-for-gutenberg'), 'iconBoxBorderRadius')}
						{num(__('Rotation (deg)', 'easy-elements-for-gutenberg'), 'iconRotation')}
						<Divider />
						{color(__('Color (Hover)', 'easy-elements-for-gutenberg'), 'iconColorHover')}
						{color(__('Background (Hover)', 'easy-elements-for-gutenberg'), 'iconBgHover')}
						{num(__('Rotation Hover (deg)', 'easy-elements-for-gutenberg'), 'iconRotationHover')}
					</PanelBody>
				)}
			</InspectorControls>

			<ServerSideRender block="easy-elements-for-gutenberg/button" attributes={attributes} httpMethod="POST" />
		</div>
	);
}
