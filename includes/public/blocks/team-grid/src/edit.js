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

const SVG = (path) => (
	<svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
		<path d={path} fill="currentColor" />
	</svg>
);
const ICON_UP = SVG('M12 6.6l-6 6 1.4 1.4 4.6-4.6 4.6 4.6 1.4-1.4z');
const ICON_DOWN = SVG('M12 15.4l6-6-1.4-1.4-4.6 4.6-4.6-4.6-1.4 1.4z');
const ICON_TRASH = SVG('M9 3v1H4v2h16V4h-5V3H9zM6 7l1 13h10l1-13H6zm4 2h1v9h-1V9zm3 0h1v9h-1V9z');
const ICON_ADD = SVG('M11 5v6H5v2h6v6h2v-6h6v-2h-6V5z');

const ALIGN = [
	{ label: __('Left', 'easy-elements-for-gutenberg'), value: 'left' },
	{ label: __('Center', 'easy-elements-for-gutenberg'), value: 'center' },
	{ label: __('Right', 'easy-elements-for-gutenberg'), value: 'right' },
];

export default function Edit({ attributes, setAttributes, clientId }) {
	const {
		blockId,
		teamSkin,
		actionType,
		showSocialIcon,
		socialIconPosition,
		socialIconShow,
		socialHoverIcon,
		socialLinks,
		showContactInfo,
		contentShow,
	} = attributes;

	useEffect(() => {
		if (!blockId) {
			setAttributes({ blockId: 'eelfg-team-' + clientId.slice(0, 6) });
		}
	}, [blockId, clientId, setAttributes]);

	const items = Array.isArray(socialLinks) ? socialLinks : [];
	const updateItem = (i, key, val) => setAttributes({ socialLinks: items.map((it, idx) => (idx === i ? { ...it, [key]: val } : it)) });
	const addItem = () => setAttributes({ socialLinks: [...items, { url: '#', icon: '' }] });
	const removeItem = (i) => setAttributes({ socialLinks: items.filter((_, idx) => idx !== i) });
	const moveItem = (i, dir) => {
		const t = i + dir;
		if (t < 0 || t >= items.length) return;
		const next = items.slice();
		const [m] = next.splice(i, 1);
		next.splice(t, 0, m);
		setAttributes({ socialLinks: next });
	};

	const color = (label, key) => <ColorPopover label={label} color={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const typo = (label, key) => <TypographyControls label={label} attributes={attributes} setAttributes={setAttributes} attributeKey={key} />;
	const border = (label, key) => <BorderControl label={label} value={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const shadow = (label, key) => <BoxShadowControls label={label} value={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const box = (label, key) => <BoxControl label={label} values={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const num = (label, key) => <TextControl label={label} type="number" value={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} __next40pxDefaultSize __nextHasNoMarginBottom />;
	const bg = (label, c, g) => (
		<BackgroundControl
			label={label}
			colorValue={attributes[c]}
			gradientValue={attributes[g]}
			onColorChange={(v) => setAttributes({ [c]: v && typeof v === 'object' ? v.hex : v || '' })}
			onGradientChange={(v) => setAttributes({ [g]: v || '' })}
		/>
	);

	const isContentSkin = teamSkin === 'default' || teamSkin === 'skin2';

	return (
		<div {...useBlockProps()}>
			<InspectorControls>
				<PanelBody title={__('Team Settings', 'easy-elements-for-gutenberg')} initialOpen={true}>
					<SelectControl
						label={__('Skin Type', 'easy-elements-for-gutenberg')}
						value={teamSkin}
						options={[
							{ label: __('Default', 'easy-elements-for-gutenberg'), value: 'default' },
							{ label: __('Skin 01', 'easy-elements-for-gutenberg'), value: 'skin1' },
							{ label: __('Skin 02', 'easy-elements-for-gutenberg'), value: 'skin2' },
							{ label: __('Skin 03', 'easy-elements-for-gutenberg'), value: 'skin3' },
							{ label: __('Skin 04 (Hover Overlay)', 'easy-elements-for-gutenberg'), value: 'skin4' },
							{ label: __('Skin 05', 'easy-elements-for-gutenberg'), value: 'skin5' },
						]}
						onChange={(v) => setAttributes({ teamSkin: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					{teamSkin === 'skin4' && (
						<SelectControl
							label={__('Hover Overlay Style', 'easy-elements-for-gutenberg')}
							value={attributes.skin4HoverOverlay}
							options={[
								{ label: __('Hover Overlay 1', 'easy-elements-for-gutenberg'), value: 'overlay1' },
								{ label: __('Hover Overlay 2', 'easy-elements-for-gutenberg'), value: 'overlay2' },
							]}
							onChange={(v) => setAttributes({ skin4HoverOverlay: v })}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					)}
					<MediaUploadCheck>
						<MediaUpload
							onSelect={(media) => setAttributes({ image: { id: media.id, url: media.url, alt: media.alt } })}
							allowedTypes={['image']}
							value={attributes.image?.id}
							render={({ open }) => (
								<div style={{ margin: '8px 0' }}>
									{attributes.image?.url && <img src={attributes.image.url} alt="" style={{ maxWidth: '100%', marginBottom: '8px' }} />}
									<Button variant="secondary" onClick={open} style={{ width: '100%', justifyContent: 'center' }}>{attributes.image?.url ? __('Replace Image', 'easy-elements-for-gutenberg') : __('Select Image', 'easy-elements-for-gutenberg')}</Button>
								</div>
							)}
						/>
					</MediaUploadCheck>
					<TextControl label={__('Name', 'easy-elements-for-gutenberg')} value={attributes.name} onChange={(v) => setAttributes({ name: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					<SelectControl label={__('Title HTML Tag', 'easy-elements-for-gutenberg')} value={attributes.titleTag} options={['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p'].map((t) => ({ label: t.toUpperCase(), value: t }))} onChange={(v) => setAttributes({ titleTag: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					<TextControl label={__('Designation', 'easy-elements-for-gutenberg')} value={attributes.designation} onChange={(v) => setAttributes({ designation: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					<TextareaControl label={__('Description', 'easy-elements-for-gutenberg')} value={attributes.details} onChange={(v) => setAttributes({ details: v })} __nextHasNoMarginBottom />
					{isContentSkin && (
						<SelectControl
							label={__('Content Show', 'easy-elements-for-gutenberg')}
							value={contentShow}
							options={[
								{ label: __('Inside Image', 'easy-elements-for-gutenberg'), value: 'inside' },
								{ label: __('Normal', 'easy-elements-for-gutenberg'), value: 'normal' },
							]}
							onChange={(v) => setAttributes({ contentShow: v })}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					)}
					<SelectControl
						label={__('Image Fetch Priority', 'easy-elements-for-gutenberg')}
						value={attributes.fetchpriority}
						options={[
							{ label: __('Default', 'easy-elements-for-gutenberg'), value: '' },
							{ label: __('High', 'easy-elements-for-gutenberg'), value: 'high' },
							{ label: __('Low', 'easy-elements-for-gutenberg'), value: 'low' },
						]}
						onChange={(v) => setAttributes({ fetchpriority: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<Divider />
					{bg(__('Image Overlay', 'easy-elements-for-gutenberg'), 'imageOverlayColor', 'imageOverlayGradient')}
					<ToggleControl label={__('Disable Image Scale on Hover', 'easy-elements-for-gutenberg')} checked={attributes.disableImageScale} onChange={(v) => setAttributes({ disableImageScale: v })} __nextHasNoMarginBottom />
					<ToggleControl label={__('Disable Social Icon Lift on Hover', 'easy-elements-for-gutenberg')} checked={attributes.disableSocialLift} onChange={(v) => setAttributes({ disableSocialLift: v })} __nextHasNoMarginBottom />
					<ToggleControl label={__('Show Social Icon', 'easy-elements-for-gutenberg')} checked={showSocialIcon} onChange={(v) => setAttributes({ showSocialIcon: v })} __nextHasNoMarginBottom />
				</PanelBody>

				{teamSkin === 'skin5' && (
					<PanelBody title={__('Contact Info', 'easy-elements-for-gutenberg')} initialOpen={false}>
						<ToggleControl label={__('Show Contact Info', 'easy-elements-for-gutenberg')} checked={showContactInfo} onChange={(v) => setAttributes({ showContactInfo: v })} __nextHasNoMarginBottom />
						{showContactInfo && (
							<>
								<TextControl label={__('Email', 'easy-elements-for-gutenberg')} value={attributes.teamEmail} onChange={(v) => setAttributes({ teamEmail: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
								<TextControl label={__('Phone', 'easy-elements-for-gutenberg')} value={attributes.teamPhone} onChange={(v) => setAttributes({ teamPhone: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
								<IconPicker label={__('Email Icon', 'easy-elements-for-gutenberg')} value={attributes.teamEmailIcon} onChange={(v) => setAttributes({ teamEmailIcon: v })} />
								<IconPicker label={__('Phone Icon', 'easy-elements-for-gutenberg')} value={attributes.teamPhoneIcon} onChange={(v) => setAttributes({ teamPhoneIcon: v })} />
							</>
						)}
					</PanelBody>
				)}

				<PanelBody title={__('Action', 'easy-elements-for-gutenberg')} initialOpen={false}>
					<SelectControl
						label={__('Action Type', 'easy-elements-for-gutenberg')}
						value={actionType}
						options={[
							{ label: __('Link', 'easy-elements-for-gutenberg'), value: 'link' },
							{ label: __('Popup', 'easy-elements-for-gutenberg'), value: 'popup' },
						]}
						onChange={(v) => setAttributes({ actionType: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					{actionType === 'link' && (
						<>
							<TextControl label={__('Link URL', 'easy-elements-for-gutenberg')} type="url" value={attributes.linkUrl} onChange={(v) => setAttributes({ linkUrl: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
							<ToggleControl label={__('Open in new tab', 'easy-elements-for-gutenberg')} checked={attributes.linkTarget} onChange={(v) => setAttributes({ linkTarget: v })} __nextHasNoMarginBottom />
							<ToggleControl label={__('Add nofollow', 'easy-elements-for-gutenberg')} checked={attributes.linkNofollow} onChange={(v) => setAttributes({ linkNofollow: v })} __nextHasNoMarginBottom />
						</>
					)}
				</PanelBody>

				{showSocialIcon && (
					<PanelBody title={__('Social Settings', 'easy-elements-for-gutenberg')} initialOpen={false}>
						<SelectControl
							label={__('Icon Position', 'easy-elements-for-gutenberg')}
							value={socialIconPosition}
							options={[
								{ label: __('Default', 'easy-elements-for-gutenberg'), value: 'default' },
								{ label: __('Top Left', 'easy-elements-for-gutenberg'), value: 'posi_left' },
								{ label: __('Top Right', 'easy-elements-for-gutenberg'), value: 'posi_right' },
								{ label: __('Bottom Left', 'easy-elements-for-gutenberg'), value: 'posi_botttom_left' },
								{ label: __('Bottom Right', 'easy-elements-for-gutenberg'), value: 'posi_botttom_right' },
							]}
							onChange={(v) => setAttributes({ socialIconPosition: v })}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
						{socialIconPosition !== 'default' && (
							<>
								<SelectControl
									label={__('Icon Show', 'easy-elements-for-gutenberg')}
									value={socialIconShow}
									options={[
										{ label: __('Default Show', 'easy-elements-for-gutenberg'), value: 'dafault_show' },
										{ label: __('Icon Hover Show', 'easy-elements-for-gutenberg'), value: 'hover_show' },
									]}
									onChange={(v) => setAttributes({ socialIconShow: v })}
									__next40pxDefaultSize
									__nextHasNoMarginBottom
								/>
								{socialIconShow === 'hover_show' && <IconPicker label={__('Hover Icon', 'easy-elements-for-gutenberg')} value={socialHoverIcon} onChange={(v) => setAttributes({ socialHoverIcon: v })} />}
								{(socialIconPosition === 'posi_left' || socialIconPosition === 'posi_right') && num(__('Top Position (px)', 'easy-elements-for-gutenberg'), 'sIconPosiTop')}
								{(socialIconPosition === 'posi_botttom_left' || socialIconPosition === 'posi_botttom_right') && num(__('Bottom Position (px)', 'easy-elements-for-gutenberg'), 'sIconPosiBottom')}
								{(socialIconPosition === 'posi_left' || socialIconPosition === 'posi_botttom_left') && num(__('Left Position (px)', 'easy-elements-for-gutenberg'), 'sIconPosiLeft')}
								{(socialIconPosition === 'posi_right' || socialIconPosition === 'posi_botttom_right') && num(__('Right Position (px)', 'easy-elements-for-gutenberg'), 'sIconPosiRight')}
							</>
						)}
						<Divider />
						{items.map((item, index) => (
							<div className="eelfg-team-grid-repeater-item" key={index}>
								<div className="eelfg-team-grid-repeater-head">
									<strong>#{index + 1}</strong>
									<div>
										<Button icon={ICON_UP} label={__('Move up', 'easy-elements-for-gutenberg')} onClick={() => moveItem(index, -1)} disabled={index === 0} size="small" />
										<Button icon={ICON_DOWN} label={__('Move down', 'easy-elements-for-gutenberg')} onClick={() => moveItem(index, 1)} disabled={index === items.length - 1} size="small" />
										<Button icon={ICON_TRASH} label={__('Remove', 'easy-elements-for-gutenberg')} onClick={() => removeItem(index)} isDestructive size="small" />
									</div>
								</div>
								<TextControl label={__('Link URL', 'easy-elements-for-gutenberg')} value={item.url || ''} onChange={(v) => updateItem(index, 'url', v)} __next40pxDefaultSize __nextHasNoMarginBottom />
								<IconPicker label={__('Icon', 'easy-elements-for-gutenberg')} value={item.icon || ''} onChange={(v) => updateItem(index, 'icon', v)} />
							</div>
						))}
						<Button variant="primary" onClick={addItem} icon={ICON_ADD}>{__('Add Social Link', 'easy-elements-for-gutenberg')}</Button>
					</PanelBody>
				)}
			</InspectorControls>

			<InspectorControls group="styles">
				<PanelBody title={__('Team Item', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{bg(__('Background', 'easy-elements-for-gutenberg'), 'cardBgColor', 'cardBgGradient')}
					{box(__('Padding', 'easy-elements-for-gutenberg'), 'itemPadding')}
					{box(__('Border Radius', 'easy-elements-for-gutenberg'), 'itemBorderRadius')}
					{border(__('Border', 'easy-elements-for-gutenberg'), 'teamBorder')}
					{color(__('Hover Border Color', 'easy-elements-for-gutenberg'), 'teamHoverBorderColor')}
					{shadow(__('Box Shadow', 'easy-elements-for-gutenberg'), 'teamBoxShadow')}
					<SelectControl label={__('Alignment', 'easy-elements-for-gutenberg')} value={attributes.teamContentAlignment} options={[{ label: __('Default', 'easy-elements-for-gutenberg'), value: '' }, ...ALIGN]} onChange={(v) => setAttributes({ teamContentAlignment: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
				</PanelBody>

				<PanelBody title={__('Image', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{num(__('Image Width (px)', 'easy-elements-for-gutenberg'), 'imageWidth')}
					{num(__('Image Height (px)', 'easy-elements-for-gutenberg'), 'imageHeightStyle')}
					{box(__('Padding', 'easy-elements-for-gutenberg'), 'imagePadding')}
					{box(__('Border Radius', 'easy-elements-for-gutenberg'), 'imageStyleRadius')}
					<Divider />
					{color(__('Below Background Color', 'easy-elements-for-gutenberg'), 'imageBelowBg')}
					{num(__('Below BG Height (%)', 'easy-elements-for-gutenberg'), 'imageBelowHeight')}
					<SelectControl label={__('Below BG Position', 'easy-elements-for-gutenberg')} value={attributes.imageBelowPosition} options={[{ label: __('Top', 'easy-elements-for-gutenberg'), value: 'top' }, { label: __('Bottom', 'easy-elements-for-gutenberg'), value: 'bottom' }]} onChange={(v) => setAttributes({ imageBelowPosition: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					{box(__('Below BG Border Radius', 'easy-elements-for-gutenberg'), 'imageBelowRadius')}
				</PanelBody>

				<PanelBody title={__('Name & Designation Area', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Background Color', 'easy-elements-for-gutenberg'), 'areaBgColor')}
					{box(__('Padding', 'easy-elements-for-gutenberg'), 'wrapPadding')}
					{box(__('Margin', 'easy-elements-for-gutenberg'), 'wrapMargin')}
					{box(__('Border Radius', 'easy-elements-for-gutenberg'), 'areaBorderRadius')}
				</PanelBody>

				{teamSkin === 'skin4' && (
					<PanelBody title={__('Hover Overlay', 'easy-elements-for-gutenberg')} initialOpen={false}>
						{bg(__('Background', 'easy-elements-for-gutenberg'), 'skin4OverlayColor', 'skin4OverlayGradient')}
						{num(__('Backdrop Blur (px)', 'easy-elements-for-gutenberg'), 'skin4OverlayBlur')}
						{color(__('Text Color', 'easy-elements-for-gutenberg'), 'skin4OverlayTextColor')}
						{attributes.skin4HoverOverlay === 'overlay2' && num(__('Overlay 2 Circle Size (px)', 'easy-elements-for-gutenberg'), 'skin4Overlay2CircleSize')}
						{box(__('Padding', 'easy-elements-for-gutenberg'), 'skin4OverlayPadding')}
						{box(__('Border Radius', 'easy-elements-for-gutenberg'), 'skin4OverlayBorderRadius')}
						{num(__('Transition Duration (s)', 'easy-elements-for-gutenberg'), 'skin4OverlayTransition')}
					</PanelBody>
				)}

				<PanelBody title={__('Name', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Color', 'easy-elements-for-gutenberg'), 'nameColor')}
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'nameTypography')}
					{box(__('Padding', 'easy-elements-for-gutenberg'), 'namePadding')}
				</PanelBody>

				<PanelBody title={__('Designation', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Color', 'easy-elements-for-gutenberg'), 'designationColor')}
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'designationTypography')}
				</PanelBody>

				{teamSkin === 'skin5' && showContactInfo && (
					<PanelBody title={__('Contact Info', 'easy-elements-for-gutenberg')} initialOpen={false}>
						{num(__('Items Gap (px)', 'easy-elements-for-gutenberg'), 'contactGap')}
						<Divider />
						{color(__('Text Color', 'easy-elements-for-gutenberg'), 'contactTextColor')}
						{bg(__('Item Background', 'easy-elements-for-gutenberg'), 'contactItemBgColor', 'contactItemBgGradient')}
						{color(__('Text Color (Hover)', 'easy-elements-for-gutenberg'), 'contactTextHoverColor')}
						{bg(__('Item Background (Hover)', 'easy-elements-for-gutenberg'), 'contactItemBgHoverColor', 'contactItemBgHoverGradient')}
						{typo(__('Typography', 'easy-elements-for-gutenberg'), 'contactTypography')}
						{box(__('Item Border Radius', 'easy-elements-for-gutenberg'), 'contactItemRadius')}
						{box(__('Item Padding', 'easy-elements-for-gutenberg'), 'contactItemPadding')}
						<Divider />
						{color(__('Icon Color', 'easy-elements-for-gutenberg'), 'contactIconColor')}
						{bg(__('Icon Background', 'easy-elements-for-gutenberg'), 'contactIconBgColor', 'contactIconBgGradient')}
						{num(__('Icon Size (px)', 'easy-elements-for-gutenberg'), 'contactIconSize')}
						{num(__('Icon Box Size (px)', 'easy-elements-for-gutenberg'), 'contactIconBoxSize')}
						{box(__('Icon Border Radius', 'easy-elements-for-gutenberg'), 'contactIconRadius')}
					</PanelBody>
				)}

				{(teamSkin === 'default' || teamSkin === 'skin1' || teamSkin === 'skin2') && (
					<PanelBody title={__('Description', 'easy-elements-for-gutenberg')} initialOpen={false}>
						{color(__('Color', 'easy-elements-for-gutenberg'), 'teamDescriptionColor')}
						{typo(__('Typography', 'easy-elements-for-gutenberg'), 'teamDescriptionTypography')}
						{box(__('Margin', 'easy-elements-for-gutenberg'), 'teamDescriptionMargin')}
						{box(__('Padding', 'easy-elements-for-gutenberg'), 'teamDescriptionPadding')}
					</PanelBody>
				)}

				{teamSkin === 'skin3' && (
					<PanelBody title={__('Description (Overlay)', 'easy-elements-for-gutenberg')} initialOpen={false}>
						{color(__('Color', 'easy-elements-for-gutenberg'), 'descColor')}
						{bg(__('Background', 'easy-elements-for-gutenberg'), 'descBgColor', 'descBgGradient')}
						{color(__('Color (Hover)', 'easy-elements-for-gutenberg'), 'descColorHover')}
						{bg(__('Hover Background', 'easy-elements-for-gutenberg'), 'descBgHoverColor', 'descBgHoverGradient')}
						<TextControl label={__('Hover BG Opacity (0-1)', 'easy-elements-for-gutenberg')} type="number" step="0.1" value={attributes.descBgHoverOpacity} onChange={(v) => setAttributes({ descBgHoverOpacity: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
						{typo(__('Typography', 'easy-elements-for-gutenberg'), 'descTypography')}
						{box(__('Padding', 'easy-elements-for-gutenberg'), 'descPadding')}
					</PanelBody>
				)}

				{showSocialIcon && (
					<PanelBody title={__('Social Icon', 'easy-elements-for-gutenberg')} initialOpen={false}>
						{color(__('Color', 'easy-elements-for-gutenberg'), 'sIconColor')}
						{color(__('Background', 'easy-elements-for-gutenberg'), 'sIconBgColor')}
						{color(__('Color (Hover)', 'easy-elements-for-gutenberg'), 'sIconHoverColor')}
						{color(__('Background (Hover)', 'easy-elements-for-gutenberg'), 'sIconHoverBgColor')}
						{typo(__('Typography', 'easy-elements-for-gutenberg'), 'sIconTypography')}
						{num(__('Icon Gap (px)', 'easy-elements-for-gutenberg'), 'sIconGap')}
						{num(__('Button Size (px)', 'easy-elements-for-gutenberg'), 'sIconButtonSize')}
						{box(__('Border Radius', 'easy-elements-for-gutenberg'), 'sIconRadius')}
						{box(__('Area Padding', 'easy-elements-for-gutenberg'), 'sIconAreaPadding')}
						{socialIconPosition === 'default' && border(__('Area Border', 'easy-elements-for-gutenberg'), 'socialItemBorder')}
						{socialIconPosition === 'default' && (
							<SelectControl label={__('Alignment', 'easy-elements-for-gutenberg')} value={attributes.teamSocialIconAlignment} options={[{ label: __('Default', 'easy-elements-for-gutenberg'), value: '' }, { label: __('Left', 'easy-elements-for-gutenberg'), value: 'flex-start' }, { label: __('Center', 'easy-elements-for-gutenberg'), value: 'center' }, { label: __('Right', 'easy-elements-for-gutenberg'), value: 'flex-end' }]} onChange={(v) => setAttributes({ teamSocialIconAlignment: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
						)}
						{shadow(__('Box Shadow', 'easy-elements-for-gutenberg'), 'socialBoxShadow')}
					</PanelBody>
				)}

				{actionType === 'popup' && (
					<PanelBody title={__('Popup Style', 'easy-elements-for-gutenberg')} initialOpen={false}>
						{color(__('Background Color', 'easy-elements-for-gutenberg'), 'popupBgColor')}
						{color(__('Name Color', 'easy-elements-for-gutenberg'), 'popupNameColor')}
						{typo(__('Name Typography', 'easy-elements-for-gutenberg'), 'popupNameTypography')}
						{color(__('Designation Color', 'easy-elements-for-gutenberg'), 'popupDesignationColor')}
						{typo(__('Designation Typography', 'easy-elements-for-gutenberg'), 'popupDesignationTypography')}
						{color(__('Details Color', 'easy-elements-for-gutenberg'), 'popupDetailsColor')}
						{typo(__('Details Typography', 'easy-elements-for-gutenberg'), 'popupDetailsTypography')}
						{color(__('Close Icon Color', 'easy-elements-for-gutenberg'), 'popupCloseColor')}
					</PanelBody>
				)}
			</InspectorControls>

			<ServerSideRender block="easy-elements-for-gutenberg/team-grid" attributes={attributes} httpMethod="POST" />
		</div>
	);
}
