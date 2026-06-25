import { __ } from '@wordpress/i18n';
import { useEffect } from '@wordpress/element';
import { ServerSideRender } from '@wordpress/server-side-render';
import {
	useBlockProps,
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
} from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	ToggleControl,
	TextControl,
	TextareaControl,
	BoxControl,
	Button,
	__experimentalDivider as Divider,
} from '@wordpress/components';

import ColorPopover from '../../custom-components/ColorPopover';
import IconPicker from '../../custom-components/IconPicker';
import TypographyControls from '../../custom-components/TypographyControls';
import BorderControl from '../../custom-components/BorderControl';
import BackgroundControl from '../../custom-components/BackgroundControl';
import BoxShadowControls from '../../custom-components/BoxShadowControls';

import './editor.scss';

const ALIGN = [
	{ label: __('Left', 'easy-elements-for-gutenberg'), value: 'left' },
	{ label: __('Center', 'easy-elements-for-gutenberg'), value: 'center' },
	{ label: __('Right', 'easy-elements-for-gutenberg'), value: 'right' },
	{ label: __('Justify', 'easy-elements-for-gutenberg'), value: 'justify' },
];

const BLEND_MODES = ['', 'normal', 'multiply', 'screen', 'overlay', 'darken', 'lighten', 'color-dodge', 'color-burn', 'hard-light', 'soft-light', 'difference', 'exclusion', 'hue', 'saturation', 'color', 'luminosity'].map((v) => ({ label: v === '' ? __('Default', 'easy-elements-for-gutenberg') : v, value: v }));

export default function Edit({ attributes, setAttributes, clientId }) {
	const {
		titleTag,
		linkUrl,
		linkTarget,
		linkNofollow,
		showBorderTitle,
		borderPosition,
		showGradientTitle,
		subHeadingType,
		subHeadingIcon,
		subHeadingImage,
		iconDirection,
		showGradientBorder,
		separatorType,
		separatorPosition,
		selectIcon,
		sepImage,
		blockId,
	} = attributes;

	useEffect(() => {
		if (!blockId) {
			setAttributes({ blockId: 'eelfg-heading-' + clientId.slice(0, 6) });
		}
	}, [blockId, clientId, setAttributes]);

	const color = (label, key) => <ColorPopover label={label} color={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const typo = (label, key) => <TypographyControls label={label} attributes={attributes} setAttributes={setAttributes} attributeKey={key} />;
	const border = (label, key) => <BorderControl label={label} value={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const box = (label, key) => <BoxControl label={label} values={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const num = (label, key) => (
		<TextControl label={label} type="number" value={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
	);
	const shadow = (label, key) => <BoxShadowControls label={label} value={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const bg = (label, colorKey, gradKey) => (
		<BackgroundControl
			label={label}
			colorValue={attributes[colorKey]}
			gradientValue={attributes[gradKey]}
			onColorChange={(v) => setAttributes({ [colorKey]: v && typeof v === 'object' ? v.hex : v || '' })}
			onGradientChange={(v) => setAttributes({ [gradKey]: v || '' })}
		/>
	);
	const tshadow = (label, key) => {
		const v = attributes[key] || {};
		const set = (patch) => setAttributes({ [key]: { ...v, ...patch } });
		return (
			<div style={{ marginBottom: '12px' }}>
				<strong style={{ display: 'block', marginBottom: '6px' }}>{label}</strong>
				<TextControl label={__('Offset X', 'easy-elements-for-gutenberg')} type="number" value={v.x ?? ''} onChange={(x) => set({ x })} __next40pxDefaultSize __nextHasNoMarginBottom />
				<TextControl label={__('Offset Y', 'easy-elements-for-gutenberg')} type="number" value={v.y ?? ''} onChange={(y) => set({ y })} __next40pxDefaultSize __nextHasNoMarginBottom />
				<TextControl label={__('Blur', 'easy-elements-for-gutenberg')} type="number" value={v.blur ?? ''} onChange={(blur) => set({ blur })} __next40pxDefaultSize __nextHasNoMarginBottom />
				<ColorPopover label={__('Shadow Color', 'easy-elements-for-gutenberg')} color={v.color} onChange={(color) => set({ color })} />
			</div>
		);
	};

	return (
		<div {...useBlockProps()}>
			<InspectorControls>
				<PanelBody title={__('Heading', 'easy-elements-for-gutenberg')} initialOpen={true}>
					<TextareaControl
						label={__('Heading Text', 'easy-elements-for-gutenberg')}
						help={__('Wrap part of the text in {{ }} to highlight it. Example: Heading {{Here}}.', 'easy-elements-for-gutenberg')}
						value={attributes.title}
						onChange={(v) => setAttributes({ title: v })}
						__nextHasNoMarginBottom
					/>
					<SelectControl
						label={__('HTML Tag', 'easy-elements-for-gutenberg')}
						value={titleTag}
						options={['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span'].map((t) => ({ label: t.toUpperCase(), value: t }))}
						onChange={(v) => setAttributes({ titleTag: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<SelectControl label={__('Alignment', 'easy-elements-for-gutenberg')} value={attributes.align} options={[{ label: __('Default', 'easy-elements-for-gutenberg'), value: '' }, ...ALIGN]} onChange={(v) => setAttributes({ align: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					<Divider />
					<ToggleControl label={__('Enable Gradient Text', 'easy-elements-for-gutenberg')} checked={showGradientTitle} onChange={(v) => setAttributes({ showGradientTitle: v })} __nextHasNoMarginBottom />
					<ToggleControl label={__('Show Title Side Border', 'easy-elements-for-gutenberg')} checked={showBorderTitle} onChange={(v) => setAttributes({ showBorderTitle: v })} __nextHasNoMarginBottom />
					{showBorderTitle && (
						<>
							<SelectControl
								label={__('Border Position', 'easy-elements-for-gutenberg')}
								value={borderPosition}
								options={[
									{ label: __('Left of Title (text width)', 'easy-elements-for-gutenberg'), value: 'eelfg-title-start' },
									{ label: __('Right of Title (text width)', 'easy-elements-for-gutenberg'), value: 'eelfg-title-end' },
									{ label: __('Left of Title (full width)', 'easy-elements-for-gutenberg'), value: 'eelfg-full-title-start' },
									{ label: __('Right of Title (full width)', 'easy-elements-for-gutenberg'), value: 'eelfg-full-title-end' },
								]}
								onChange={(v) => setAttributes({ borderPosition: v })}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
							{color(__('Border Color', 'easy-elements-for-gutenberg'), 'borderColor')}
							{box(__('Border Padding', 'easy-elements-for-gutenberg'), 'borderPadding')}
						</>
					)}
					<Divider />
					<TextControl label={__('Link URL', 'easy-elements-for-gutenberg')} type="url" value={linkUrl} onChange={(v) => setAttributes({ linkUrl: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					{linkUrl !== '' && (
						<>
							<ToggleControl label={__('Open in new tab', 'easy-elements-for-gutenberg')} checked={linkTarget} onChange={(v) => setAttributes({ linkTarget: v })} __nextHasNoMarginBottom />
							<ToggleControl label={__('Add nofollow', 'easy-elements-for-gutenberg')} checked={linkNofollow} onChange={(v) => setAttributes({ linkNofollow: v })} __nextHasNoMarginBottom />
						</>
					)}
					<Divider />
					<TextareaControl label={__('Description', 'easy-elements-for-gutenberg')} value={attributes.description} onChange={(v) => setAttributes({ description: v })} __nextHasNoMarginBottom />
				</PanelBody>

				<PanelBody title={__('Sub Heading', 'easy-elements-for-gutenberg')} initialOpen={false}>
					<TextareaControl label={__('Sub Heading', 'easy-elements-for-gutenberg')} value={attributes.subTitle} onChange={(v) => setAttributes({ subTitle: v })} __nextHasNoMarginBottom />
					<SelectControl
						label={__('Sub Heading Type', 'easy-elements-for-gutenberg')}
						value={subHeadingType}
						options={[
							{ label: __('None', 'easy-elements-for-gutenberg'), value: 'none' },
							{ label: __('Icon', 'easy-elements-for-gutenberg'), value: 'icon' },
							{ label: __('Image', 'easy-elements-for-gutenberg'), value: 'image' },
						]}
						onChange={(v) => setAttributes({ subHeadingType: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					{subHeadingType === 'icon' && <IconPicker label={__('Icon', 'easy-elements-for-gutenberg')} value={subHeadingIcon} onChange={(v) => setAttributes({ subHeadingIcon: v })} />}
					{subHeadingType === 'image' && (
						<MediaUploadCheck>
							<MediaUpload
								onSelect={(media) => setAttributes({ subHeadingImage: { id: media.id, url: media.url, alt: media.alt } })}
								allowedTypes={['image']}
								value={subHeadingImage?.id}
								render={({ open }) => (
									<div style={{ marginBottom: '12px' }}>
										{subHeadingImage?.url && <img src={subHeadingImage.url} alt="" style={{ maxWidth: '100%', marginBottom: '8px' }} />}
										<Button variant="secondary" onClick={open} style={{ width: '100%', justifyContent: 'center' }}>{subHeadingImage?.url ? __('Replace Image', 'easy-elements-for-gutenberg') : __('Upload Image', 'easy-elements-for-gutenberg')}</Button>
									</div>
								)}
							/>
						</MediaUploadCheck>
					)}
					{subHeadingType !== 'none' && (
						<SelectControl
							label={__('Icon / Image Position', 'easy-elements-for-gutenberg')}
							value={iconDirection}
							options={[
								{ label: __('Left of Text', 'easy-elements-for-gutenberg'), value: 'left' },
								{ label: __('Above Text', 'easy-elements-for-gutenberg'), value: 'top' },
								{ label: __('Right of Text', 'easy-elements-for-gutenberg'), value: 'right' },
							]}
							onChange={(v) => setAttributes({ iconDirection: v })}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					)}
					<Divider />
					<ToggleControl label={__('Show Gradient Border', 'easy-elements-for-gutenberg')} checked={showGradientBorder} onChange={(v) => setAttributes({ showGradientBorder: v })} __nextHasNoMarginBottom />
					{showGradientBorder && (
						<>
							{color(__('Gradient Color 1', 'easy-elements-for-gutenberg'), 'gradientColor1')}
							{color(__('Gradient Color 2', 'easy-elements-for-gutenberg'), 'gradientColor2')}
							{color(__('Gradient Color 3', 'easy-elements-for-gutenberg'), 'gradientColor3')}
							{num(__('Border Radius (px)', 'easy-elements-for-gutenberg'), 'gradientBorderRadius')}
							{box(__('Gradient Border Padding', 'easy-elements-for-gutenberg'), 'subGradientBorderPadding')}
						</>
					)}
				</PanelBody>

				<PanelBody title={__('Separator', 'easy-elements-for-gutenberg')} initialOpen={false}>
					<SelectControl
						label={__('Separator Type', 'easy-elements-for-gutenberg')}
						value={separatorType}
						options={[
							{ label: __('None', 'easy-elements-for-gutenberg'), value: 'none' },
							{ label: __('Dotted', 'easy-elements-for-gutenberg'), value: 'dotted' },
							{ label: __('Solid', 'easy-elements-for-gutenberg'), value: 'solid' },
							{ label: __('Icon', 'easy-elements-for-gutenberg'), value: 'icon' },
							{ label: __('Image', 'easy-elements-for-gutenberg'), value: 'image' },
						]}
						onChange={(v) => setAttributes({ separatorType: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					{separatorType !== 'none' && (
						<SelectControl
							label={__('Position', 'easy-elements-for-gutenberg')}
							value={separatorPosition}
							options={[
								{ label: __('Top of Widget', 'easy-elements-for-gutenberg'), value: 'top' },
								{ label: __('Above Title', 'easy-elements-for-gutenberg'), value: 'above' },
								{ label: __('Below Title', 'easy-elements-for-gutenberg'), value: 'below' },
								{ label: __('Bottom of Widget', 'easy-elements-for-gutenberg'), value: 'bottom' },
							]}
							onChange={(v) => setAttributes({ separatorPosition: v })}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					)}
					{(separatorType === 'solid' || separatorType === 'dotted') && (
						<>
							{color(__('Separator Color', 'easy-elements-for-gutenberg'), 'solidColor')}
							{num(__('Width (px)', 'easy-elements-for-gutenberg'), 'separatorBarWidth')}
							{separatorType === 'solid' && num(__('Height (px)', 'easy-elements-for-gutenberg'), 'separatorBarHeight')}
						</>
					)}
					{separatorType === 'icon' && (
						<>
							<IconPicker label={__('Select Icon', 'easy-elements-for-gutenberg')} value={selectIcon} onChange={(v) => setAttributes({ selectIcon: v })} />
							{color(__('Icon Color', 'easy-elements-for-gutenberg'), 'separatorIconColor')}
							{num(__('Icon Size (px)', 'easy-elements-for-gutenberg'), 'separatorIconSize')}
						</>
					)}
					{separatorType === 'image' && (
						<>
							<MediaUploadCheck>
								<MediaUpload
									onSelect={(media) => setAttributes({ sepImage: { id: media.id, url: media.url, alt: media.alt } })}
									allowedTypes={['image']}
									value={sepImage?.id}
									render={({ open }) => (
										<div style={{ marginBottom: '12px' }}>
											{sepImage?.url && <img src={sepImage.url} alt="" style={{ maxWidth: '100%', marginBottom: '8px' }} />}
											<Button variant="secondary" onClick={open} style={{ width: '100%', justifyContent: 'center' }}>{sepImage?.url ? __('Replace Image', 'easy-elements-for-gutenberg') : __('Upload Image', 'easy-elements-for-gutenberg')}</Button>
										</div>
									)}
								/>
							</MediaUploadCheck>
							{num(__('Image Width (px)', 'easy-elements-for-gutenberg'), 'separatorImageWidth')}
						</>
					)}
					{separatorType !== 'none' && box(__('Margin', 'easy-elements-for-gutenberg'), 'separatorMargin')}
				</PanelBody>

				<PanelBody title={__('Watermark', 'easy-elements-for-gutenberg')} initialOpen={false}>
					<TextareaControl label={__('Watermark Text', 'easy-elements-for-gutenberg')} value={attributes.waterMark} onChange={(v) => setAttributes({ waterMark: v })} __nextHasNoMarginBottom />
				</PanelBody>
			</InspectorControls>

			<InspectorControls group="styles">
				<PanelBody title={__('Heading', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{!showGradientTitle && color(__('Color', 'easy-elements-for-gutenberg'), 'titleColor')}
					{showGradientTitle && bg(__('Text Gradient / Color', 'easy-elements-for-gutenberg'), 'titleFillColor', 'titleFillGradient')}
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'titleTypography')}
					<TextControl label={__('Opacity (0-1)', 'easy-elements-for-gutenberg')} type="number" step="0.01" value={attributes.titleOpacity} onChange={(v) => setAttributes({ titleOpacity: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					{num(__('Stroke Width (px)', 'easy-elements-for-gutenberg'), 'titleStrokeWidth')}
					{color(__('Stroke Color', 'easy-elements-for-gutenberg'), 'titleStrokeColor')}
					{tshadow(__('Text Shadow', 'easy-elements-for-gutenberg'), 'titleTextShadow')}
					<SelectControl label={__('Blend Mode', 'easy-elements-for-gutenberg')} value={attributes.titleBlendMode} options={BLEND_MODES} onChange={(v) => setAttributes({ titleBlendMode: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					{box(__('Margin', 'easy-elements-for-gutenberg'), 'titleMargin')}
					{box(__('Padding', 'easy-elements-for-gutenberg'), 'titlePadding')}
					<Divider />
					<ToggleControl label={__('Enable Image Fill (Text Mask)', 'easy-elements-for-gutenberg')} checked={attributes.enableTitleImageFill} onChange={(v) => setAttributes({ enableTitleImageFill: v })} __nextHasNoMarginBottom />
					{attributes.enableTitleImageFill && (
						<MediaUploadCheck>
							<MediaUpload
								onSelect={(media) => setAttributes({ titleImageFill: { id: media.id, url: media.url, alt: media.alt } })}
								allowedTypes={['image']}
								value={attributes.titleImageFill?.id}
								render={({ open }) => (
									<div style={{ marginTop: '8px' }}>
										{attributes.titleImageFill?.url && <img src={attributes.titleImageFill.url} alt="" style={{ maxWidth: '100%', marginBottom: '8px' }} />}
										<Button variant="secondary" onClick={open} style={{ width: '100%', justifyContent: 'center' }}>{attributes.titleImageFill?.url ? __('Replace Image', 'easy-elements-for-gutenberg') : __('Select Image', 'easy-elements-for-gutenberg')}</Button>
									</div>
								)}
							/>
						</MediaUploadCheck>
					)}
				</PanelBody>

				<PanelBody title={__('Sub Heading', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Color', 'easy-elements-for-gutenberg'), 'subColor')}
					{bg(__('Background', 'easy-elements-for-gutenberg'), 'subBgColor', 'subBgGradient')}
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'subTypography')}
					{box(__('Text Spacing (margin)', 'easy-elements-for-gutenberg'), 'subIconMargin')}
					{subHeadingType !== 'none' && (
						<>
							{color(__('Icon Color', 'easy-elements-for-gutenberg'), 'iconColor')}
							{num(__('Icon/Image to Text Gap (px)', 'easy-elements-for-gutenberg'), 'subIconGap')}
							{subHeadingType === 'icon' && num(__('Icon Size (px)', 'easy-elements-for-gutenberg'), 'subIconSize')}
							{subHeadingType === 'image' && (
								<>
									{num(__('Image Width (px)', 'easy-elements-for-gutenberg'), 'subImageWidth')}
									{num(__('Image Height (px)', 'easy-elements-for-gutenberg'), 'subImageHeight')}
									{box(__('Image Border Radius', 'easy-elements-for-gutenberg'), 'subImageRadius')}
								</>
							)}
						</>
					)}
					<Divider />
					{border(__('Border', 'easy-elements-for-gutenberg'), 'subBorder')}
					{box(__('Border Radius', 'easy-elements-for-gutenberg'), 'subBorderRadius')}
					{box(__('Padding', 'easy-elements-for-gutenberg'), 'subPadding')}
					{box(__('Margin', 'easy-elements-for-gutenberg'), 'subMargin')}
				</PanelBody>

				<PanelBody title={__('Highlight', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Color', 'easy-elements-for-gutenberg'), 'highlightColor')}
					{bg(__('Background', 'easy-elements-for-gutenberg'), 'highlightBgColor', 'highlightBgGradient')}
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'highlightTypography')}
					{box(__('Padding', 'easy-elements-for-gutenberg'), 'highlightPadding')}
					{box(__('Margin', 'easy-elements-for-gutenberg'), 'highlightMargin')}
					{box(__('Border Radius', 'easy-elements-for-gutenberg'), 'highlightBorderRadius')}
				</PanelBody>

				<PanelBody title={__('Description', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Color', 'easy-elements-for-gutenberg'), 'descColor')}
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'descTypography')}
					{box(__('Margin', 'easy-elements-for-gutenberg'), 'descMargin')}
					{box(__('Padding', 'easy-elements-for-gutenberg'), 'descPadding')}
				</PanelBody>

				{attributes.waterMark !== '' && (
					<PanelBody title={__('Watermark', 'easy-elements-for-gutenberg')} initialOpen={false}>
						{color(__('Text Color', 'easy-elements-for-gutenberg'), 'wmColor')}
						{color(__('Stroke Color', 'easy-elements-for-gutenberg'), 'wmStrokeColor')}
						{num(__('Stroke Width (px)', 'easy-elements-for-gutenberg'), 'wmStrokeWidth')}
						{num(__('Font Size (px)', 'easy-elements-for-gutenberg'), 'wmFontSize')}
						{typo(__('Typography', 'easy-elements-for-gutenberg'), 'wmTypography')}
						{tshadow(__('Text Shadow', 'easy-elements-for-gutenberg'), 'wmTextShadow')}
						<Divider />
						{bg(__('Background', 'easy-elements-for-gutenberg'), 'wmBgColor', 'wmBgGradient')}
						{border(__('Border', 'easy-elements-for-gutenberg'), 'wmBorder')}
						{box(__('Border Radius', 'easy-elements-for-gutenberg'), 'wmBorderRadius')}
						{box(__('Padding', 'easy-elements-for-gutenberg'), 'wmPadding')}
						{shadow(__('Box Shadow', 'easy-elements-for-gutenberg'), 'wmBoxShadow')}
						<Divider />
						{num(__('Top / Bottom (px)', 'easy-elements-for-gutenberg'), 'wmTop')}
						{num(__('Left / Right (px)', 'easy-elements-for-gutenberg'), 'wmLeft')}
						{num(__('Rotation (deg)', 'easy-elements-for-gutenberg'), 'wmRotation')}
						<TextControl label={__('Opacity (0-1)', 'easy-elements-for-gutenberg')} type="number" step="0.01" value={attributes.wmOpacity} onChange={(v) => setAttributes({ wmOpacity: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
						{num(__('Z-Index', 'easy-elements-for-gutenberg'), 'wmZIndex')}
						<SelectControl label={__('Blend Mode', 'easy-elements-for-gutenberg')} value={attributes.wmBlendMode} options={BLEND_MODES} onChange={(v) => setAttributes({ wmBlendMode: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					</PanelBody>
				)}
			</InspectorControls>

			<ServerSideRender block="easy-elements-for-gutenberg/heading" attributes={attributes} httpMethod="POST" />
		</div>
	);
}
