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
import BoxShadowControls from '../../custom-components/BoxShadowControls';

import './editor.scss';

const SVG = (path) => (
	<svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
		<path d={path} fill="currentColor" />
	</svg>
);
const ICON_ARROW_UP = SVG('M12 6.6l-6 6 1.4 1.4 4.6-4.6 4.6 4.6 1.4-1.4z');
const ICON_ARROW_DOWN = SVG('M12 15.4l6-6-1.4-1.4-4.6 4.6-4.6-4.6-1.4 1.4z');
const ICON_TRASH = SVG('M9 3v1H4v2h16V4h-5V3H9zM6 7l1 13h10l1-13H6zm4 2h1v9h-1V9zm3 0h1v9h-1V9z');
const ICON_ADD = SVG('M11 5v6H5v2h6v6h2v-6h6v-2h-6V5z');

const ALIGN = [
	{ label: __('Left', 'easy-elements-for-gutenberg'), value: 'left' },
	{ label: __('Center', 'easy-elements-for-gutenberg'), value: 'center' },
	{ label: __('Right', 'easy-elements-for-gutenberg'), value: 'right' },
];

export default function Edit({ attributes, setAttributes, clientId }) {
	const {
		infoSkin,
		iconType,
		icon,
		iconImage,
		numberTitle,
		numberGradient,
		title,
		titleTag,
		description,
		features,
		linkUrl,
		linkTarget,
		linkNofollow,
		enableBoxLink,
		showReadMore,
		readMoreType,
		readMoreText,
		readMoreIcon,
		readMoreTextIcon,
		readMoreTextIconShow,
		readMoreAlignment,
		buttonTextAlign,
		iconDirection,
		gradientBorder,
		blockId,
	} = attributes;

	useEffect(() => {
		if (!blockId) {
			setAttributes({ blockId: 'eelfg-icon-box-' + clientId.slice(0, 6) });
		}
	}, [blockId, clientId, setAttributes]);

	const items = Array.isArray(features) ? features : [];
	const updateFeature = (i, key, val) => setAttributes({ features: items.map((it, idx) => (idx === i ? { ...it, [key]: val } : it)) });
	const addFeature = () => setAttributes({ features: [...items, { icon: '', text: __('New feature', 'easy-elements-for-gutenberg') }] });
	const removeFeature = (i) => setAttributes({ features: items.filter((_, idx) => idx !== i) });
	const moveFeature = (i, dir) => {
		const t = i + dir;
		if (t < 0 || t >= items.length) return;
		const next = items.slice();
		const [m] = next.splice(i, 1);
		next.splice(t, 0, m);
		setAttributes({ features: next });
	};

	const color = (label, key) => <ColorPopover label={label} color={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const typo = (label, key) => <TypographyControls label={label} attributes={attributes} setAttributes={setAttributes} attributeKey={key} />;
	const border = (label, key) => <BorderControl label={label} value={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const shadow = (label, key) => <BoxShadowControls label={label} value={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const box = (label, key) => <BoxControl label={label} values={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const num = (label, key) => (
		<TextControl label={label} type="number" value={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
	);
	const isLR = iconDirection === 'left' || iconDirection === 'right';

	return (
		<div {...useBlockProps()}>
			<InspectorControls>
				<PanelBody title={__('Settings', 'easy-elements-for-gutenberg')} initialOpen={true}>
					<SelectControl
						label={__('Skin', 'easy-elements-for-gutenberg')}
						value={infoSkin}
						options={[
							{ label: __('Skin 01', 'easy-elements-for-gutenberg'), value: 'default' },
							{ label: __('Skin 02', 'easy-elements-for-gutenberg'), value: 'skin-2' },
						]}
						onChange={(v) => setAttributes({ infoSkin: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<SelectControl
						label={__('Type', 'easy-elements-for-gutenberg')}
						value={iconType}
						options={[
							{ label: __('Icon', 'easy-elements-for-gutenberg'), value: 'icon' },
							{ label: __('Image', 'easy-elements-for-gutenberg'), value: 'image' },
						]}
						onChange={(v) => setAttributes({ iconType: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					{iconType === 'icon' ? (
						<IconPicker label={__('Icon', 'easy-elements-for-gutenberg')} value={icon} onChange={(v) => setAttributes({ icon: v })} />
					) : (
						<MediaUploadCheck>
							<MediaUpload
								onSelect={(media) => setAttributes({ iconImage: { id: media.id, url: media.url, alt: media.alt } })}
								allowedTypes={['image']}
								value={iconImage?.id}
								render={({ open }) => (
									<div style={{ marginBottom: '12px' }}>
										{iconImage?.url && <img src={iconImage.url} alt="" style={{ maxWidth: '100%', marginBottom: '8px' }} />}
										<Button variant="secondary" onClick={open} style={{ width: '100%', justifyContent: 'center' }}>
											{iconImage?.url ? __('Replace Image', 'easy-elements-for-gutenberg') : __('Upload Image', 'easy-elements-for-gutenberg')}
										</Button>
									</div>
								)}
							/>
						</MediaUploadCheck>
					)}
					<Divider />
					<TextControl label={__('Number', 'easy-elements-for-gutenberg')} value={numberTitle} onChange={(v) => setAttributes({ numberTitle: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					{numberTitle !== '' && (
						<ToggleControl label={__('Number Gradient', 'easy-elements-for-gutenberg')} checked={numberGradient} onChange={(v) => setAttributes({ numberGradient: v })} __nextHasNoMarginBottom />
					)}
					<Divider />
					<TextControl label={__('Title', 'easy-elements-for-gutenberg')} value={title} onChange={(v) => setAttributes({ title: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					<SelectControl
						label={__('Title HTML Tag', 'easy-elements-for-gutenberg')}
						value={titleTag}
						options={['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p'].map((t) => ({ label: t.toUpperCase(), value: t }))}
						onChange={(v) => setAttributes({ titleTag: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<TextareaControl label={__('Description', 'easy-elements-for-gutenberg')} value={description} onChange={(v) => setAttributes({ description: v })} __nextHasNoMarginBottom />
				</PanelBody>

				{infoSkin === 'skin-2' && (
					<PanelBody title={__('Features', 'easy-elements-for-gutenberg')} initialOpen={false}>
						{items.map((item, index) => (
							<div className="eelfg-icon-box-repeater-item" key={index}>
								<div className="eelfg-icon-box-repeater-head">
									<strong>#{index + 1}</strong>
									<div>
										<Button icon={ICON_ARROW_UP} label={__('Move up', 'easy-elements-for-gutenberg')} onClick={() => moveFeature(index, -1)} disabled={index === 0} size="small" />
										<Button icon={ICON_ARROW_DOWN} label={__('Move down', 'easy-elements-for-gutenberg')} onClick={() => moveFeature(index, 1)} disabled={index === items.length - 1} size="small" />
										<Button icon={ICON_TRASH} label={__('Remove', 'easy-elements-for-gutenberg')} onClick={() => removeFeature(index)} isDestructive size="small" />
									</div>
								</div>
								<TextControl label={__('Text', 'easy-elements-for-gutenberg')} value={item.text || ''} onChange={(v) => updateFeature(index, 'text', v)} __next40pxDefaultSize __nextHasNoMarginBottom />
								<IconPicker label={__('Icon', 'easy-elements-for-gutenberg')} value={item.icon || ''} onChange={(v) => updateFeature(index, 'icon', v)} />
							</div>
						))}
						<Button variant="primary" onClick={addFeature} icon={ICON_ADD}>{__('Add Feature', 'easy-elements-for-gutenberg')}</Button>
					</PanelBody>
				)}

				<PanelBody title={__('Link', 'easy-elements-for-gutenberg')} initialOpen={false}>
					<TextControl label={__('Link URL', 'easy-elements-for-gutenberg')} type="url" value={linkUrl} onChange={(v) => setAttributes({ linkUrl: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					<ToggleControl label={__('Open in new tab', 'easy-elements-for-gutenberg')} checked={linkTarget} onChange={(v) => setAttributes({ linkTarget: v })} __nextHasNoMarginBottom />
					<ToggleControl label={__('Add nofollow', 'easy-elements-for-gutenberg')} checked={linkNofollow} onChange={(v) => setAttributes({ linkNofollow: v })} __nextHasNoMarginBottom />
					{linkUrl !== '' && (
						<ToggleControl label={__('Enable Full Box Link', 'easy-elements-for-gutenberg')} checked={enableBoxLink} onChange={(v) => setAttributes({ enableBoxLink: v })} __nextHasNoMarginBottom />
					)}
				</PanelBody>

				<PanelBody title={__('Read More', 'easy-elements-for-gutenberg')} initialOpen={false}>
					<ToggleControl label={__('Show Read More', 'easy-elements-for-gutenberg')} checked={showReadMore} onChange={(v) => setAttributes({ showReadMore: v })} __nextHasNoMarginBottom />
					{showReadMore && (
						<>
							<SelectControl
								label={__('Read More Type', 'easy-elements-for-gutenberg')}
								value={readMoreType}
								options={[
									{ label: __('Text', 'easy-elements-for-gutenberg'), value: 'read_text' },
									{ label: __('Icon', 'easy-elements-for-gutenberg'), value: 'read_icon' },
									{ label: __('Icon Hover to Text', 'easy-elements-for-gutenberg'), value: 'read_icon_to_text' },
								]}
								onChange={(v) => setAttributes({ readMoreType: v })}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
							{(readMoreType === 'read_text' || readMoreType === 'read_icon_to_text') && (
								<>
									<TextControl label={__('Read More Text', 'easy-elements-for-gutenberg')} value={readMoreText} onChange={(v) => setAttributes({ readMoreText: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
									<ToggleControl label={__('Show Icon Next to Text', 'easy-elements-for-gutenberg')} checked={readMoreTextIconShow} onChange={(v) => setAttributes({ readMoreTextIconShow: v })} __nextHasNoMarginBottom />
									{readMoreTextIconShow && (
										<IconPicker label={__('Text Button Icon', 'easy-elements-for-gutenberg')} value={readMoreTextIcon} onChange={(v) => setAttributes({ readMoreTextIcon: v })} />
									)}
								</>
							)}
							{readMoreType === 'read_icon' && (
								<IconPicker label={__('Read More Icon', 'easy-elements-for-gutenberg')} value={readMoreIcon} onChange={(v) => setAttributes({ readMoreIcon: v })} />
							)}
							<SelectControl
								label={__('Button Alignment', 'easy-elements-for-gutenberg')}
								value={readMoreAlignment}
								options={[
									{ label: __('Default', 'easy-elements-for-gutenberg'), value: '' },
									{ label: __('Left', 'easy-elements-for-gutenberg'), value: 'left' },
									{ label: __('Center', 'easy-elements-for-gutenberg'), value: 'center' },
									{ label: __('Right', 'easy-elements-for-gutenberg'), value: 'right' },
									{ label: __('Stretch', 'easy-elements-for-gutenberg'), value: 'stretch' },
								]}
								onChange={(v) => setAttributes({ readMoreAlignment: v })}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
							{infoSkin === 'skin-2' && (
								<SelectControl
									label={__('Skin-2 Button Align', 'easy-elements-for-gutenberg')}
									value={buttonTextAlign}
									options={[
										{ label: __('Default', 'easy-elements-for-gutenberg'), value: '' },
										{ label: __('Left', 'easy-elements-for-gutenberg'), value: 'flex-start' },
										{ label: __('Center', 'easy-elements-for-gutenberg'), value: 'center' },
										{ label: __('Right', 'easy-elements-for-gutenberg'), value: 'end' },
										{ label: __('Stretch', 'easy-elements-for-gutenberg'), value: 'stretch' },
									]}
									onChange={(v) => setAttributes({ buttonTextAlign: v })}
									__next40pxDefaultSize
									__nextHasNoMarginBottom
								/>
							)}
						</>
					)}
				</PanelBody>
			</InspectorControls>

			<InspectorControls group="styles">
				<PanelBody title={__('Item', 'easy-elements-for-gutenberg')} initialOpen={false}>
					<SelectControl
						label={__('Direction', 'easy-elements-for-gutenberg')}
						value={iconDirection}
						options={[
							{ label: __('Left', 'easy-elements-for-gutenberg'), value: 'left' },
							{ label: __('Top', 'easy-elements-for-gutenberg'), value: 'top' },
							{ label: __('Right', 'easy-elements-for-gutenberg'), value: 'right' },
						]}
						onChange={(v) => setAttributes({ iconDirection: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					{isLR && (
						<>
							<SelectControl
								label={__('Vertical Alignment', 'easy-elements-for-gutenberg')}
								value={attributes.iconVerticalAlignment}
								options={[
									{ label: __('Top', 'easy-elements-for-gutenberg'), value: 'flex-start' },
									{ label: __('Middle', 'easy-elements-for-gutenberg'), value: 'center' },
									{ label: __('Bottom', 'easy-elements-for-gutenberg'), value: 'flex-end' },
								]}
								onChange={(v) => setAttributes({ iconVerticalAlignment: v })}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
							{num(__('Item Spacing (px)', 'easy-elements-for-gutenberg'), 'itemSpacing')}
						</>
					)}
					<SelectControl label={__('Text Alignment', 'easy-elements-for-gutenberg')} value={attributes.textAlign} options={ALIGN} onChange={(v) => setAttributes({ textAlign: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					<Divider />
					{color(__('Background', 'easy-elements-for-gutenberg'), 'itemBgColor')}
					{border(__('Border', 'easy-elements-for-gutenberg'), 'itemBorder')}
					{shadow(__('Box Shadow', 'easy-elements-for-gutenberg'), 'itemBoxShadow')}
					{box(__('Border Radius', 'easy-elements-for-gutenberg'), 'itemBorderRadius')}
					{box(__('Padding', 'easy-elements-for-gutenberg'), 'itemPadding')}
					{box(__('Margin', 'easy-elements-for-gutenberg'), 'itemMargin')}
					<Divider />
					<SelectControl
						label={__('Hover Background Direction', 'easy-elements-for-gutenberg')}
						value={attributes.itemHoverBgDirection}
						options={[
							{ label: __('Default', 'easy-elements-for-gutenberg'), value: 'default' },
							{ label: __('Left', 'easy-elements-for-gutenberg'), value: 'left' },
							{ label: __('Right', 'easy-elements-for-gutenberg'), value: 'right' },
							{ label: __('Top', 'easy-elements-for-gutenberg'), value: 'top' },
							{ label: __('Bottom', 'easy-elements-for-gutenberg'), value: 'bottom' },
							{ label: __('Middle', 'easy-elements-for-gutenberg'), value: 'middle' },
						]}
						onChange={(v) => setAttributes({ itemHoverBgDirection: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					{color(__('Hover Background', 'easy-elements-for-gutenberg'), 'itemHoverBgColor')}
					{color(__('Hover Border Color', 'easy-elements-for-gutenberg'), 'itemHoverBorderColor')}
					{shadow(__('Hover Box Shadow', 'easy-elements-for-gutenberg'), 'itemHoverBoxShadow')}
				</PanelBody>

				{iconType === 'icon' && (
					<PanelBody title={__('Icon', 'easy-elements-for-gutenberg')} initialOpen={false}>
						{color(__('Color', 'easy-elements-for-gutenberg'), 'iconColor')}
						{color(__('Background', 'easy-elements-for-gutenberg'), 'iconBgColor')}
						{num(__('Icon Size (px)', 'easy-elements-for-gutenberg'), 'iconSize')}
						{num(__('Box Size (px)', 'easy-elements-for-gutenberg'), 'iconBoxSize')}
						{border(__('Border', 'easy-elements-for-gutenberg'), 'iconBorder')}
						{box(__('Border Radius', 'easy-elements-for-gutenberg'), 'iconBorderRadius')}
						{box(__('Margin', 'easy-elements-for-gutenberg'), 'iconMargin')}
						{shadow(__('Box Shadow', 'easy-elements-for-gutenberg'), 'iconBoxShadow')}
						{num(__('Rotate (deg)', 'easy-elements-for-gutenberg'), 'iconRotate')}
						<ToggleControl label={__('Gradient Border', 'easy-elements-for-gutenberg')} checked={gradientBorder} onChange={(v) => setAttributes({ gradientBorder: v })} __nextHasNoMarginBottom />
						<Divider />
						{color(__('Hover Color', 'easy-elements-for-gutenberg'), 'iconHoverColor')}
						{color(__('Hover Background', 'easy-elements-for-gutenberg'), 'iconHoverBgColor')}
					</PanelBody>
				)}

				{iconType === 'image' && (
					<PanelBody title={__('Image', 'easy-elements-for-gutenberg')} initialOpen={false}>
						{num(__('Image Size (px)', 'easy-elements-for-gutenberg'), 'imageSize')}
						{num(__('Box Size (px)', 'easy-elements-for-gutenberg'), 'imageBoxSize')}
						{color(__('Background', 'easy-elements-for-gutenberg'), 'imageBgColor')}
						{border(__('Border', 'easy-elements-for-gutenberg'), 'imageBorder')}
						{box(__('Box Border Radius', 'easy-elements-for-gutenberg'), 'imageBoxBorderRadius')}
						{box(__('Image Border Radius', 'easy-elements-for-gutenberg'), 'imageBorderRadius')}
						{box(__('Padding', 'easy-elements-for-gutenberg'), 'imagePadding')}
						{box(__('Margin', 'easy-elements-for-gutenberg'), 'imageMargin')}
						{color(__('Hover Background', 'easy-elements-for-gutenberg'), 'imageHoverBgColor')}
					</PanelBody>
				)}

				{numberTitle !== '' && (
					<PanelBody title={__('Number', 'easy-elements-for-gutenberg')} initialOpen={false}>
						{!numberGradient && color(__('Color', 'easy-elements-for-gutenberg'), 'numberColor')}
						{color(__('Background', 'easy-elements-for-gutenberg'), 'numberBgColor')}
						<SelectControl label={__('Alignment', 'easy-elements-for-gutenberg')} value={attributes.numberAlignment} options={[{ label: __('Default', 'easy-elements-for-gutenberg'), value: '' }, ...ALIGN]} onChange={(v) => setAttributes({ numberAlignment: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
						{typo(__('Typography', 'easy-elements-for-gutenberg'), 'numberTypography')}
						{box(__('Padding', 'easy-elements-for-gutenberg'), 'numberPadding')}
						{box(__('Margin', 'easy-elements-for-gutenberg'), 'numberMargin')}
					</PanelBody>
				)}

				<PanelBody title={__('Title', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Color', 'easy-elements-for-gutenberg'), 'titleColor')}
					{color(__('Hover Color', 'easy-elements-for-gutenberg'), 'titleHoverColor')}
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'titleTypography')}
					{box(__('Margin', 'easy-elements-for-gutenberg'), 'titleMargin')}
				</PanelBody>

				<PanelBody title={__('Description', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Color', 'easy-elements-for-gutenberg'), 'descColor')}
					{color(__('Hover Color', 'easy-elements-for-gutenberg'), 'descHoverColor')}
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'descTypography')}
					{box(__('Margin', 'easy-elements-for-gutenberg'), 'descMargin')}
				</PanelBody>

				{infoSkin === 'skin-2' && (
					<PanelBody title={__('Features', 'easy-elements-for-gutenberg')} initialOpen={false}>
						{color(__('Text Color', 'easy-elements-for-gutenberg'), 'featureTextColor')}
						{color(__('Icon Color', 'easy-elements-for-gutenberg'), 'featureIconColor')}
						{color(__('Text Color (Hover)', 'easy-elements-for-gutenberg'), 'featureTextColorHover')}
						{color(__('Icon Color (Hover)', 'easy-elements-for-gutenberg'), 'featureIconColorHover')}
						{typo(__('Typography', 'easy-elements-for-gutenberg'), 'featureTypography')}
						{box(__('Item Margin', 'easy-elements-for-gutenberg'), 'featureMargin')}
						{num(__('Icon Gap (px)', 'easy-elements-for-gutenberg'), 'featureIconGap')}
						{num(__('Icon Size (px)', 'easy-elements-for-gutenberg'), 'featureIconSize')}
					</PanelBody>
				)}

				{showReadMore && (
					<PanelBody title={__('Button', 'easy-elements-for-gutenberg')} initialOpen={false}>
						{(readMoreType === 'read_icon') && (
							<>
								{color(__('Icon Color', 'easy-elements-for-gutenberg'), 'readMoreIconColor')}
								{color(__('Icon Background', 'easy-elements-for-gutenberg'), 'readMoreIconBgColor')}
								{num(__('Icon Size (px)', 'easy-elements-for-gutenberg'), 'readMoreIconSize')}
								{box(__('Icon Padding', 'easy-elements-for-gutenberg'), 'readMoreIconPadding')}
								{box(__('Icon Border Radius', 'easy-elements-for-gutenberg'), 'readMoreIconBorderRadius')}
								<Divider />
							</>
						)}
						{(readMoreType === 'read_text' || readMoreType === 'read_icon_to_text') && (
							<>
								{color(__('Text Color', 'easy-elements-for-gutenberg'), 'readMoreTextColor')}
								{color(__('Text Background', 'easy-elements-for-gutenberg'), 'readMoreTextBgColor')}
								{typo(__('Typography', 'easy-elements-for-gutenberg'), 'readMoreTypography')}
								{border(__('Border', 'easy-elements-for-gutenberg'), 'readMoreTextBorder')}
								{box(__('Border Radius', 'easy-elements-for-gutenberg'), 'readMoreTextBorderRadius')}
								{box(__('Padding', 'easy-elements-for-gutenberg'), 'readMoreTextPadding')}
								<Divider />
								{color(__('Text Color (Hover)', 'easy-elements-for-gutenberg'), 'readMoreTextColorHover')}
								{color(__('Background (Hover)', 'easy-elements-for-gutenberg'), 'readMoreBgHover')}
								{color(__('Border Color (Hover)', 'easy-elements-for-gutenberg'), 'readMoreHoverBorderColor')}
								<Divider />
							</>
						)}
						{box(__('Button Margin', 'easy-elements-for-gutenberg'), 'readMoreMargin')}
					</PanelBody>
				)}
			</InspectorControls>

			<ServerSideRender block="easy-elements-for-gutenberg/icon-box" attributes={attributes} httpMethod="POST" />
		</div>
	);
}
