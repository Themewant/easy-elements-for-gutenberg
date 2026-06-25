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
const COLS = ['1', '2', '3', '4', '5', '6'].map((v) => ({ label: `${v} ${v === '1' ? __('Column', 'easy-elements-for-gutenberg') : __('Columns', 'easy-elements-for-gutenberg')}`, value: v }));
const RATINGS = ['1', '2', '3', '4', '5'].map((v) => ({ label: '★'.repeat(Number(v)) + '☆'.repeat(5 - Number(v)), value: v }));

export default function Edit({ attributes, setAttributes, clientId }) {
	const { blockId, testimonialsSkin, testimonials, showImage, showRating } = attributes;

	useEffect(() => {
		if (!blockId) {
			setAttributes({ blockId: 'eelfg-tstml-' + clientId.slice(0, 6) });
		}
	}, [blockId, clientId, setAttributes]);

	const items = Array.isArray(testimonials) ? testimonials : [];
	const updateItem = (i, key, val) => setAttributes({ testimonials: items.map((it, idx) => (idx === i ? { ...it, [key]: val } : it)) });
	const addItem = () => setAttributes({ testimonials: [...items, { image: {}, name: __('New Name', 'easy-elements-for-gutenberg'), designation: '', description: '', quoteIcon: '', showQuoteIconSkin1: false, rating: '5', logo: {} }] });
	const removeItem = (i) => setAttributes({ testimonials: items.filter((_, idx) => idx !== i) });
	const moveItem = (i, dir) => {
		const t = i + dir;
		if (t < 0 || t >= items.length) return;
		const next = items.slice();
		const [m] = next.splice(i, 1);
		next.splice(t, 0, m);
		setAttributes({ testimonials: next });
	};

	const color = (label, key) => <ColorPopover label={label} color={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const typo = (label, key) => <TypographyControls label={label} attributes={attributes} setAttributes={setAttributes} attributeKey={key} />;
	const border = (label, key) => <BorderControl label={label} value={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const shadow = (label, key) => <BoxShadowControls label={label} value={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const box = (label, key) => <BoxControl label={label} values={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const num = (label, key) => <TextControl label={label} type="number" value={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} __next40pxDefaultSize __nextHasNoMarginBottom />;
	const bg = (label, c, g) => (
		<BackgroundControl label={label} colorValue={attributes[c]} gradientValue={attributes[g]} onColorChange={(v) => setAttributes({ [c]: v && typeof v === 'object' ? v.hex : v || '' })} onGradientChange={(v) => setAttributes({ [g]: v || '' })} />
	);
	const mediaField = (label, item, index, key) => (
		<MediaUploadCheck>
			<MediaUpload
				onSelect={(media) => updateItem(index, key, { id: media.id, url: media.url, alt: media.alt })}
				allowedTypes={['image']}
				value={item[key]?.id}
				render={({ open }) => (
					<div style={{ marginBottom: '8px' }}>
						<span style={{ display: 'block', marginBottom: '4px', fontSize: '11px', textTransform: 'uppercase' }}>{label}</span>
						{item[key]?.url && <img src={item[key].url} alt="" style={{ maxWidth: '100%', marginBottom: '6px' }} />}
						<Button variant="secondary" size="small" onClick={open}>{item[key]?.url ? __('Replace', 'easy-elements-for-gutenberg') : __('Select', 'easy-elements-for-gutenberg')}</Button>
					</div>
				)}
			/>
		</MediaUploadCheck>
	);

	const noColumns = testimonialsSkin === 'default' || testimonialsSkin === 'skin4';

	return (
		<div {...useBlockProps()}>
			<InspectorControls>
				<PanelBody title={__('Layout Settings', 'easy-elements-for-gutenberg')} initialOpen={true}>
					<SelectControl
						label={__('Skin Type', 'easy-elements-for-gutenberg')}
						value={testimonialsSkin}
						options={[
							{ label: __('Default', 'easy-elements-for-gutenberg'), value: 'default' },
							{ label: __('Skin 01', 'easy-elements-for-gutenberg'), value: 'skin1' },
							{ label: __('Skin 02', 'easy-elements-for-gutenberg'), value: 'skin2' },
							{ label: __('Skin 03', 'easy-elements-for-gutenberg'), value: 'skin3' },
							{ label: __('Skin 04', 'easy-elements-for-gutenberg'), value: 'skin4' },
							{ label: __('Skin 05', 'easy-elements-for-gutenberg'), value: 'skin5' },
							{ label: __('Skin 06', 'easy-elements-for-gutenberg'), value: 'skin6' },
						]}
						onChange={(v) => setAttributes({ testimonialsSkin: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					{testimonialsSkin === 'skin2' && (
						<ToggleControl label={__('Avatar Image Top', 'easy-elements-for-gutenberg')} checked={attributes.avatarImageTop} onChange={(v) => setAttributes({ avatarImageTop: v })} __nextHasNoMarginBottom />
					)}
					{testimonialsSkin === 'skin3' && (
						<>
							<ToggleControl label={__('View All Button', 'easy-elements-for-gutenberg')} help={__('Works after 6 items.', 'easy-elements-for-gutenberg')} checked={attributes.showLoadmore} onChange={(v) => setAttributes({ showLoadmore: v })} __nextHasNoMarginBottom />
							{attributes.showLoadmore && <TextControl label={__('Button Text', 'easy-elements-for-gutenberg')} value={attributes.loadMoreText} onChange={(v) => setAttributes({ loadMoreText: v })} __next40pxDefaultSize __nextHasNoMarginBottom />}
						</>
					)}
				</PanelBody>

				<PanelBody title={__('Testimonials', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{items.map((item, index) => (
						<div className="eelfg-tstml-repeater-item" key={index}>
							<div className="eelfg-tstml-repeater-head">
								<strong>{item.name || `#${index + 1}`}</strong>
								<div>
									<Button icon={ICON_UP} label={__('Move up', 'easy-elements-for-gutenberg')} onClick={() => moveItem(index, -1)} disabled={index === 0} size="small" />
									<Button icon={ICON_DOWN} label={__('Move down', 'easy-elements-for-gutenberg')} onClick={() => moveItem(index, 1)} disabled={index === items.length - 1} size="small" />
									<Button icon={ICON_TRASH} label={__('Remove', 'easy-elements-for-gutenberg')} onClick={() => removeItem(index)} isDestructive size="small" />
								</div>
							</div>
							{mediaField(__('Picture', 'easy-elements-for-gutenberg'), item, index, 'image')}
							<TextControl label={__('Name', 'easy-elements-for-gutenberg')} value={item.name || ''} onChange={(v) => updateItem(index, 'name', v)} __next40pxDefaultSize __nextHasNoMarginBottom />
							<TextControl label={__('Designation', 'easy-elements-for-gutenberg')} value={item.designation || ''} onChange={(v) => updateItem(index, 'designation', v)} __next40pxDefaultSize __nextHasNoMarginBottom />
							<TextareaControl label={__('Description', 'easy-elements-for-gutenberg')} value={item.description || ''} onChange={(v) => updateItem(index, 'description', v)} __nextHasNoMarginBottom />
							<SelectControl label={__('Rating', 'easy-elements-for-gutenberg')} value={item.rating || '5'} options={RATINGS} onChange={(v) => updateItem(index, 'rating', v)} __next40pxDefaultSize __nextHasNoMarginBottom />
							<IconPicker label={__('Quote Icon', 'easy-elements-for-gutenberg')} value={item.quoteIcon || ''} onChange={(v) => updateItem(index, 'quoteIcon', v)} />
							<ToggleControl label={__('Show Quote Icon on Skin 01', 'easy-elements-for-gutenberg')} checked={!!item.showQuoteIconSkin1} onChange={(v) => updateItem(index, 'showQuoteIconSkin1', v)} __nextHasNoMarginBottom />
							{mediaField(__('Logo', 'easy-elements-for-gutenberg'), item, index, 'logo')}
						</div>
					))}
					<Button variant="primary" onClick={addItem} icon={ICON_ADD}>{__('Add Testimonial', 'easy-elements-for-gutenberg')}</Button>
					<Divider />
					<ToggleControl label={__('Show Image', 'easy-elements-for-gutenberg')} checked={showImage} onChange={(v) => setAttributes({ showImage: v })} __nextHasNoMarginBottom />
					{!noColumns && (
						<>
							<SelectControl label={__('Columns', 'easy-elements-for-gutenberg')} value={attributes.columns} options={COLS} onChange={(v) => setAttributes({ columns: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
							<SelectControl label={__('Columns (Tablet)', 'easy-elements-for-gutenberg')} value={attributes.columnsTablet} options={COLS} onChange={(v) => setAttributes({ columnsTablet: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
							<SelectControl label={__('Columns (Mobile)', 'easy-elements-for-gutenberg')} value={attributes.columnsMobile} options={COLS} onChange={(v) => setAttributes({ columnsMobile: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
						</>
					)}
					{box(__('Item Padding', 'easy-elements-for-gutenberg'), 'itemPadding')}
					{testimonialsSkin !== 'default' && num(__('Logo Height (px)', 'easy-elements-for-gutenberg'), 'logoHeight')}
					<SelectControl label={__('Alignment', 'easy-elements-for-gutenberg')} value={attributes.testimonialsAlignment} options={[{ label: __('Default', 'easy-elements-for-gutenberg'), value: '' }, ...ALIGN]} onChange={(v) => setAttributes({ testimonialsAlignment: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					{!['skin1', 'skin2', 'skin4'].includes(testimonialsSkin) && (
						<>
							<ToggleControl label={__('Show Rating', 'easy-elements-for-gutenberg')} checked={showRating} onChange={(v) => setAttributes({ showRating: v })} __nextHasNoMarginBottom />
							{showRating && color(__('Rating Color', 'easy-elements-for-gutenberg'), 'ratingColor')}
							{showRating && num(__('Rating Size (px)', 'easy-elements-for-gutenberg'), 'ratingSize')}
						</>
					)}
					{testimonialsSkin === 'skin1' && <IconPicker label={__('Title Icon', 'easy-elements-for-gutenberg')} value={attributes.titleIcon} onChange={(v) => setAttributes({ titleIcon: v })} />}
				</PanelBody>
			</InspectorControls>

			<InspectorControls group="styles">
				<PanelBody title={__('Item', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{bg(__('Background', 'easy-elements-for-gutenberg'), 'bgColor', 'bgGradient')}
					{border(__('Border', 'easy-elements-for-gutenberg'), 'itemBorder')}
					{box(__('Border Radius', 'easy-elements-for-gutenberg'), 'itemBorderRadius')}
					{shadow(__('Box Shadow', 'easy-elements-for-gutenberg'), 'itemBoxShadow')}
					{box(__('Padding', 'easy-elements-for-gutenberg'), 'itemInnerPadding')}
					{testimonialsSkin === 'default' && num(__('Gap (px)', 'easy-elements-for-gutenberg'), 'wrapperGap')}
				</PanelBody>

				<PanelBody title={__('Name', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Color', 'easy-elements-for-gutenberg'), 'nameColor')}
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'nameTypography')}
					{box(__('Margin', 'easy-elements-for-gutenberg'), 'nameMargin')}
				</PanelBody>

				<PanelBody title={__('Designation', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Color', 'easy-elements-for-gutenberg'), 'designationColor')}
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'designationTypography')}
				</PanelBody>

				<PanelBody title={__('Description', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Color', 'easy-elements-for-gutenberg'), 'descriptionColor')}
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'descriptionTypography')}
					{box(__('Margin', 'easy-elements-for-gutenberg'), 'descriptionMargin')}
					{num(__('Min Height (px)', 'easy-elements-for-gutenberg'), 'minHeight')}
					{num(__('Max Width (px)', 'easy-elements-for-gutenberg'), 'maxWidth')}
				</PanelBody>

				{showImage && (
					<PanelBody title={__('Author Image', 'easy-elements-for-gutenberg')} initialOpen={false}>
						<SelectControl label={__('Author Info Alignment', 'easy-elements-for-gutenberg')} value={attributes.authorMetaAlignment} options={[{ label: __('Start', 'easy-elements-for-gutenberg'), value: 'flex-start' }, { label: __('Center', 'easy-elements-for-gutenberg'), value: 'center' }, { label: __('End', 'easy-elements-for-gutenberg'), value: 'flex-end' }]} onChange={(v) => setAttributes({ authorMetaAlignment: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
						{testimonialsSkin === 'skin4' && (
							<SelectControl label={__('Skin 4 Alignment', 'easy-elements-for-gutenberg')} value={attributes.authorMetaAlignmentStyle4} options={ALIGN} onChange={(v) => setAttributes({ authorMetaAlignmentStyle4: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
						)}
						{box(__('Author Info Gap (margin)', 'easy-elements-for-gutenberg'), 'authorMetaGap')}
						{num(__('Image Size (px)', 'easy-elements-for-gutenberg'), 'authorImageSize')}
						{box(__('Image Border Radius', 'easy-elements-for-gutenberg'), 'authorImageBorderRadius')}
					</PanelBody>
				)}

				{(testimonialsSkin === 'default' || testimonialsSkin === 'skin1') && (
					<PanelBody title={__('Quote Icon', 'easy-elements-for-gutenberg')} initialOpen={false}>
						{color(__('Color', 'easy-elements-for-gutenberg'), 'quoteIconColor')}
						{num(__('Size (px)', 'easy-elements-for-gutenberg'), 'quoteIconSize')}
					</PanelBody>
				)}

				{testimonialsSkin === 'skin3' && attributes.showLoadmore && (
					<PanelBody title={__('View All Button', 'easy-elements-for-gutenberg')} initialOpen={false}>
						{typo(__('Typography', 'easy-elements-for-gutenberg'), 'loadMoreTypography')}
						{color(__('Color', 'easy-elements-for-gutenberg'), 'loadMoreColor')}
						{bg(__('Background', 'easy-elements-for-gutenberg'), 'loadMoreBgColor', 'loadMoreBgGradient')}
						{box(__('Padding', 'easy-elements-for-gutenberg'), 'loadmorePadding')}
						{box(__('Border Radius', 'easy-elements-for-gutenberg'), 'loadmoreBorderRadius')}
						{border(__('Border', 'easy-elements-for-gutenberg'), 'loadMoreBorder')}
						<Divider />
						{color(__('Hover Color', 'easy-elements-for-gutenberg'), 'loadMoreHoverColor')}
						{bg(__('Hover Background', 'easy-elements-for-gutenberg'), 'loadMoreHoverBgColor', 'loadMoreHoverBgGradient')}
						{color(__('Hover Border Color', 'easy-elements-for-gutenberg'), 'loadMoreHoverBorderColor')}
					</PanelBody>
				)}
			</InspectorControls>

			<ServerSideRender block="easy-elements-for-gutenberg/testimonials-grid" attributes={attributes} httpMethod="POST" />
		</div>
	);
}
