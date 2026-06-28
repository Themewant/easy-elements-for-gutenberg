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
	TabPanel,
	__experimentalDivider as Divider,
} from '@wordpress/components';

import ColorPopover from '../../custom-components/ColorPopover';
import IconPicker from '../../custom-components/IconPicker';
import TypographyControls from '../../custom-components/TypographyControls';
import BorderControl from '../../custom-components/BorderControl';
import BackgroundControl from '../../custom-components/BackgroundControl';

import './editor.scss';

const STATE_TABS = [
	{ name: 'normal', title: __('Normal', 'easy-elements-for-gutenberg') },
	{ name: 'hover', title: __('Hover', 'easy-elements-for-gutenberg') },
];

export default function Edit({ attributes, setAttributes, clientId }) {
	const { blockId, skinStyle, mediaType, readmoreType, title, number, description } = attributes;

	useEffect(() => {
		if (!blockId) {
			setAttributes({ blockId: 'eelfg-svc-' + clientId.slice(0, 6) });
		}
	}, [blockId, clientId, setAttributes]);

	const color = (label, key) => <ColorPopover label={label} color={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const typo = (label, key) => <TypographyControls label={label} attributes={attributes} setAttributes={setAttributes} attributeKey={key} />;
	const box = (label, key) => <BoxControl label={label} values={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const border = (label, key) => <BorderControl label={label} value={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const num = (label, key) => <TextControl label={label} type="number" value={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} __next40pxDefaultSize __nextHasNoMarginBottom />;
	const bg = (label, cKey, gKey) => (
		<BackgroundControl
			label={label}
			colorValue={attributes[cKey]}
			gradientValue={attributes[gKey]}
			onColorChange={(v) => setAttributes({ [cKey]: v && typeof v === 'object' ? v.hex : v || '' })}
			onGradientChange={(v) => setAttributes({ [gKey]: v || '' })}
		/>
	);

	const isReadmoreText = readmoreType === 'readmore';

	return (
		<div {...useBlockProps()}>
			<InspectorControls>
				<PanelBody title={__('Content', 'easy-elements-for-gutenberg')} initialOpen={true}>
					<SelectControl
						label={__('Skin', 'easy-elements-for-gutenberg')}
						value={skinStyle}
						options={[
							{ label: __('Skin 1', 'easy-elements-for-gutenberg'), value: 'skin1' },
							{ label: __('Skin 2', 'easy-elements-for-gutenberg'), value: 'skin2' },
							{ label: __('Skin 3', 'easy-elements-for-gutenberg'), value: 'skin3' },
						]}
						onChange={(v) => setAttributes({ skinStyle: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<SelectControl
						label={__('Media Type', 'easy-elements-for-gutenberg')}
						value={mediaType}
						options={[
							{ label: __('Icon Box', 'easy-elements-for-gutenberg'), value: 'icon' },
							{ label: __('Image Box', 'easy-elements-for-gutenberg'), value: 'image' },
							{ label: __('Only Image', 'easy-elements-for-gutenberg'), value: 'image_only' },
							{ label: __('Number', 'easy-elements-for-gutenberg'), value: 'number' },
						]}
						onChange={(v) => setAttributes({ mediaType: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					{mediaType === 'icon' && <IconPicker label={__('Icon', 'easy-elements-for-gutenberg')} value={attributes.serviceIcon || ''} onChange={(v) => setAttributes({ serviceIcon: v })} />}
					{(mediaType === 'image' || mediaType === 'image_only') && (
						<MediaUploadCheck>
							<MediaUpload
								onSelect={(media) => setAttributes({ serviceImage: { id: media.id, url: media.url, alt: media.alt } })}
								allowedTypes={['image']}
								value={attributes.serviceImage?.id}
								render={({ open }) => (
									<div style={{ marginBottom: '8px' }}>
										{attributes.serviceImage?.url && <img src={attributes.serviceImage.url} alt="" style={{ maxWidth: '100%', marginBottom: '6px' }} />}
										<Button variant="secondary" size="small" onClick={open}>{attributes.serviceImage?.url ? __('Replace Image', 'easy-elements-for-gutenberg') : __('Select Image', 'easy-elements-for-gutenberg')}</Button>
									</div>
								)}
							/>
						</MediaUploadCheck>
					)}
					{mediaType === 'number' && <TextControl label={__('Number', 'easy-elements-for-gutenberg')} value={number} onChange={(v) => setAttributes({ number: v })} __next40pxDefaultSize __nextHasNoMarginBottom />}
					<TextControl label={__('Title', 'easy-elements-for-gutenberg')} value={title} onChange={(v) => setAttributes({ title: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					<SelectControl
						label={__('Title HTML Tag', 'easy-elements-for-gutenberg')}
						value={attributes.titleTag}
						options={['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p'].map((t) => ({ label: t.toUpperCase(), value: t }))}
						onChange={(v) => setAttributes({ titleTag: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<TextareaControl label={__('Description', 'easy-elements-for-gutenberg')} value={description} onChange={(v) => setAttributes({ description: v })} __nextHasNoMarginBottom />
					<Divider />
					<SelectControl
						label={__('Read More Type', 'easy-elements-for-gutenberg')}
						value={readmoreType}
						options={[
							{ label: __('Read More', 'easy-elements-for-gutenberg'), value: 'readmore' },
							{ label: __('Only Icon', 'easy-elements-for-gutenberg'), value: 'icon' },
							{ label: __('Magnetic Hover', 'easy-elements-for-gutenberg'), value: 'magnetic_hover' },
						]}
						onChange={(v) => setAttributes({ readmoreType: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					{isReadmoreText && <TextControl label={__('Read More Text', 'easy-elements-for-gutenberg')} value={attributes.readmoreText} onChange={(v) => setAttributes({ readmoreText: v })} __next40pxDefaultSize __nextHasNoMarginBottom />}
					<IconPicker label={__('Read More Icon', 'easy-elements-for-gutenberg')} value={attributes.readmoreIcon || ''} onChange={(v) => setAttributes({ readmoreIcon: v })} />
					{readmoreType !== 'icon' && num(__('Icon Spacing (px)', 'easy-elements-for-gutenberg'), 'readmoreIconSpacing')}
					<TextControl label={__('Read More URL', 'easy-elements-for-gutenberg')} value={attributes.readmoreLink} onChange={(v) => setAttributes({ readmoreLink: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					<ToggleControl label={__('Open in new tab', 'easy-elements-for-gutenberg')} checked={!!attributes.linkNewTab} onChange={(v) => setAttributes({ linkNewTab: v })} __nextHasNoMarginBottom />
					<ToggleControl label={__('Add nofollow', 'easy-elements-for-gutenberg')} checked={!!attributes.linkNofollow} onChange={(v) => setAttributes({ linkNofollow: v })} __nextHasNoMarginBottom />

					{readmoreType === 'icon' && (
						<>
							<Divider />
							{color(__('Icon Color', 'easy-elements-for-gutenberg'), 'readmoreIconColor')}
							{bg(__('Icon Background', 'easy-elements-for-gutenberg'), 'readmoreIconBgColor', 'readmoreIconBgGradient')}
							{num(__('Circle Size (px)', 'easy-elements-for-gutenberg'), 'circleSizeOnlyIcon')}
							{box(__('Circle Border Radius', 'easy-elements-for-gutenberg'), 'circleBorderRadiusOnlyIcon')}
							{num(__('Circle Icon Size (px)', 'easy-elements-for-gutenberg'), 'circleIconSizeOnly')}
						</>
					)}
					<Divider />
					<SelectControl
						label={__('Vertical Alignment', 'easy-elements-for-gutenberg')}
						value={attributes.vAlign}
						options={[
							{ label: __('Default', 'easy-elements-for-gutenberg'), value: '' },
							{ label: __('Top', 'easy-elements-for-gutenberg'), value: 'flex-start' },
							{ label: __('Center', 'easy-elements-for-gutenberg'), value: 'center' },
							{ label: __('Bottom', 'easy-elements-for-gutenberg'), value: 'flex-end' },
						]}
						onChange={(v) => setAttributes({ vAlign: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					{num(__('Media & Content Gap (px)', 'easy-elements-for-gutenberg'), 'mediaContentGap')}
				</PanelBody>
			</InspectorControls>

			<InspectorControls group="styles">
				{mediaType === 'icon' && (
					<PanelBody title={__('Icon', 'easy-elements-for-gutenberg')} initialOpen={false}>
						{color(__('Icon Color', 'easy-elements-for-gutenberg'), 'iconColor')}
						{bg(__('Background', 'easy-elements-for-gutenberg'), 'iconBgColor', 'iconBgGradient')}
						{num(__('Icon Size (px)', 'easy-elements-for-gutenberg'), 'iconSize')}
						{box(__('Icon Padding', 'easy-elements-for-gutenberg'), 'iconPadding')}
						{box(__('Border Radius', 'easy-elements-for-gutenberg'), 'iconBorderRadius')}
					</PanelBody>
				)}

				<PanelBody title={__('Circle', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{bg(__('Background', 'easy-elements-for-gutenberg'), 'circleBgColor', 'circleBgGradient')}
					{num(__('Circle Size (px)', 'easy-elements-for-gutenberg'), 'circleSize')}
					{box(__('Border Radius', 'easy-elements-for-gutenberg'), 'circleBorderRadius')}
					{box(__('Padding', 'easy-elements-for-gutenberg'), 'circlePadding')}
					{border(__('Border', 'easy-elements-for-gutenberg'), 'circleBorder')}
				</PanelBody>

				{mediaType === 'number' && (
					<PanelBody title={__('Number', 'easy-elements-for-gutenberg')} initialOpen={false}>
						{color(__('Number Color', 'easy-elements-for-gutenberg'), 'numberColor')}
						{typo(__('Typography', 'easy-elements-for-gutenberg'), 'numberTypography')}
					</PanelBody>
				)}

				<PanelBody title={__('Title', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Color', 'easy-elements-for-gutenberg'), 'titleColor')}
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'titleTypography')}
					{box(__('Margin', 'easy-elements-for-gutenberg'), 'titleMargin')}
				</PanelBody>

				<PanelBody title={__('Description', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Color', 'easy-elements-for-gutenberg'), 'descColor')}
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'descTypography')}
					{box(__('Margin', 'easy-elements-for-gutenberg'), 'descMargin')}
				</PanelBody>

				{readmoreType !== 'icon' && (
					<PanelBody title={__('Read More', 'easy-elements-for-gutenberg')} initialOpen={false}>
						{typo(__('Typography', 'easy-elements-for-gutenberg'), 'readmoreTypography')}
						<TabPanel tabs={STATE_TABS}>
							{(tab) =>
								tab.name === 'normal' ? (
									<>
										{color(__('Color', 'easy-elements-for-gutenberg'), 'readmoreColor')}
										{bg(__('Background', 'easy-elements-for-gutenberg'), 'readmoreBgColor', 'readmoreBgGradient')}
										{box(__('Padding', 'easy-elements-for-gutenberg'), 'readmorePadding')}
										{box(__('Border Radius', 'easy-elements-for-gutenberg'), 'readmoreBorderRadius')}
										{border(__('Border', 'easy-elements-for-gutenberg'), 'readmoreBorder')}
									</>
								) : (
									<>
										{color(__('Color', 'easy-elements-for-gutenberg'), 'readmoreHoverColor')}
										{bg(__('Background', 'easy-elements-for-gutenberg'), 'readmoreHoverBgColor', 'readmoreHoverBgGradient')}
										{color(__('Border Color', 'easy-elements-for-gutenberg'), 'readmoreHoverBorderColor')}
									</>
								)
							}
						</TabPanel>
					</PanelBody>
				)}

				{(mediaType === 'image' || mediaType === 'image_only') && (
					<PanelBody title={__('Image', 'easy-elements-for-gutenberg')} initialOpen={false}>
						{num(__('Image Height (px)', 'easy-elements-for-gutenberg'), 'imageHeight')}
						{box(__('Border Radius', 'easy-elements-for-gutenberg'), 'imageBorderRadius')}
					</PanelBody>
				)}

				{skinStyle === 'skin2' && (
					<PanelBody title={__('Others', 'easy-elements-for-gutenberg')} initialOpen={false}>
						{color(__('Circle Hover Bg Color', 'easy-elements-for-gutenberg'), 'circleHoverColor')}
						{color(__('Line Color Normal', 'easy-elements-for-gutenberg'), 'lineColor')}
						{color(__('Line Color Hover', 'easy-elements-for-gutenberg'), 'lineColorHover')}
					</PanelBody>
				)}
			</InspectorControls>

			<ServerSideRender block="easy-elements-for-gutenberg/service-list" attributes={attributes} httpMethod="POST" />
		</div>
	);
}
