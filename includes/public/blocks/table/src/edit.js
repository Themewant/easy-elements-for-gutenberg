import { __ } from '@wordpress/i18n';
import { useEffect, useState, useRef } from '@wordpress/element';
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
const ICON_MINUS = SVG('M5 11h14v2H5z');
const ICON_PLUS = SVG('M11 11V5h2v6h6v2h-6v6h-2v-6H5v-2z');

const ALIGN_OPTIONS = [
	{ label: __('Default', 'easy-elements-for-gutenberg'), value: '' },
	{ label: __('Left', 'easy-elements-for-gutenberg'), value: 'left' },
	{ label: __('Center', 'easy-elements-for-gutenberg'), value: 'center' },
	{ label: __('Right', 'easy-elements-for-gutenberg'), value: 'right' },
	{ label: __('Justify', 'easy-elements-for-gutenberg'), value: 'justify' },
];
const VALIGN_OPTIONS = [
	{ label: __('Default', 'easy-elements-for-gutenberg'), value: '' },
	{ label: __('Top', 'easy-elements-for-gutenberg'), value: 'top' },
	{ label: __('Middle', 'easy-elements-for-gutenberg'), value: 'middle' },
	{ label: __('Bottom', 'easy-elements-for-gutenberg'), value: 'bottom' },
];
const DECORATION_OPTIONS = [
	{ label: __('Default', 'easy-elements-for-gutenberg'), value: '' },
	{ label: __('Underline', 'easy-elements-for-gutenberg'), value: 'underline' },
	{ label: __('Overline', 'easy-elements-for-gutenberg'), value: 'overline' },
	{ label: __('Line Through', 'easy-elements-for-gutenberg'), value: 'line-through' },
	{ label: __('None', 'easy-elements-for-gutenberg'), value: 'none' },
];

export default function Edit({ attributes, setAttributes, clientId }) {
	const { blockId, tableHeader, tableBody, tableFooter } = attributes;

	useEffect(() => {
		if (!blockId) {
			setAttributes({ blockId: 'eelfg-table-' + clientId.slice(0, 6) });
		}
	}, [blockId, clientId, setAttributes]);

	// Editor preview tooltip interactivity (front-end view.js doesn't run in SSR).
	const previewRef = useRef(null);
	useEffect(() => {
		const root = previewRef.current;
		if (!root) return undefined;
		const over = (e) => {
			const tip = e.target.closest('.eelfg-tbl-tooltip');
			if (tip && root.contains(tip)) tip.classList.add('show');
		};
		const out = (e) => {
			const tip = e.target.closest('.eelfg-tbl-tooltip');
			if (tip) tip.classList.remove('show');
		};
		root.addEventListener('mouseover', over);
		root.addEventListener('mouseout', out);
		return () => {
			root.removeEventListener('mouseover', over);
			root.removeEventListener('mouseout', out);
		};
	}, []);

	const color = (label, key) => <ColorPopover label={label} color={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const typo = (label, key) => <TypographyControls label={label} attributes={attributes} setAttributes={setAttributes} attributeKey={key} />;
	const border = (label, key) => <BorderControl label={label} value={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const box = (label, key) => <BoxControl label={label} values={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const num = (label, key) => <TextControl label={label} type="number" value={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} __next40pxDefaultSize __nextHasNoMarginBottom />;

	// ---- Generic repeater management -------------------------------------------
	const makeRepeater = (attrKey, newItem) => {
		const items = Array.isArray(attributes[attrKey]) ? attributes[attrKey] : [];
		const update = (i, key, val) => setAttributes({ [attrKey]: items.map((it, idx) => (idx === i ? { ...it, [key]: val } : it)) });
		const add = () => setAttributes({ [attrKey]: [...items, { ...newItem }] });
		const remove = (i) => setAttributes({ [attrKey]: items.filter((_, idx) => idx !== i) });
		const move = (i, dir) => {
			const t = i + dir;
			if (t < 0 || t >= items.length) return;
			const next = items.slice();
			const [m] = next.splice(i, 1);
			next.splice(t, 0, m);
			setAttributes({ [attrKey]: next });
		};
		return { items, update, add, remove, move };
	};

	const [collapsed, setCollapsed] = useState({});
	const cKey = (section, i) => `${section}-${i}`;
	const isCollapsed = (section, i) => !!collapsed[cKey(section, i)];
	const toggleCollapsed = (section, i) => setCollapsed((cur) => ({ ...cur, [cKey(section, i)]: !cur[cKey(section, i)] }));

	const ItemHead = ({ section, index, label, rep }) => (
		<div className="eelfg-tbl-repeater-head">
			<strong>{label || `#${index + 1}`}</strong>
			<div>
				<Button icon={isCollapsed(section, index) ? ICON_PLUS : ICON_MINUS} label={isCollapsed(section, index) ? __('Expand', 'easy-elements-for-gutenberg') : __('Collapse', 'easy-elements-for-gutenberg')} onClick={() => toggleCollapsed(section, index)} size="small" />
				<Button icon={ICON_UP} label={__('Move up', 'easy-elements-for-gutenberg')} onClick={() => rep.move(index, -1)} disabled={index === 0} size="small" />
				<Button icon={ICON_DOWN} label={__('Move down', 'easy-elements-for-gutenberg')} onClick={() => rep.move(index, 1)} disabled={index === rep.items.length - 1} size="small" />
				<Button icon={ICON_TRASH} label={__('Remove', 'easy-elements-for-gutenberg')} onClick={() => rep.remove(index)} isDestructive size="small" />
			</div>
		</div>
	);

	const cellAdvanced = (item, index, update, opts = {}) => (
		<>
			<ToggleControl label={__('Advance Settings', 'easy-elements-for-gutenberg')} checked={!!item.advance} onChange={(v) => update(index, 'advance', v)} __nextHasNoMarginBottom />
			{item.advance && (
				<>
					<ToggleControl label={__('colSpan', 'easy-elements-for-gutenberg')} checked={!!item.colspan} onChange={(v) => update(index, 'colspan', v)} __nextHasNoMarginBottom />
					{item.colspan && <TextControl label={__('colSpan Number', 'easy-elements-for-gutenberg')} type="number" value={item.colspanNumber || ''} onChange={(v) => update(index, 'colspanNumber', v)} __next40pxDefaultSize __nextHasNoMarginBottom />}
					{opts.rowspan && (
						<>
							<ToggleControl label={__('rowSpan', 'easy-elements-for-gutenberg')} checked={!!item.rowspan} onChange={(v) => update(index, 'rowspan', v)} __nextHasNoMarginBottom />
							{item.rowspan && <TextControl label={__('rowSpan Number', 'easy-elements-for-gutenberg')} type="number" value={item.rowspanNumber || ''} onChange={(v) => update(index, 'rowspanNumber', v)} __next40pxDefaultSize __nextHasNoMarginBottom />}
						</>
					)}
					{opts.flex && (
						<>
							<ToggleControl label={__('Data Flex', 'easy-elements-for-gutenberg')} checked={!!item.dataFlex} onChange={(v) => update(index, 'dataFlex', v)} __nextHasNoMarginBottom />
							{item.dataFlex && (
								<>
									<SelectControl label={__('Flex Alignment', 'easy-elements-for-gutenberg')} value={item.flexAlign || ''} options={[{ label: __('Left', 'easy-elements-for-gutenberg'), value: 'start' }, { label: __('Center', 'easy-elements-for-gutenberg'), value: 'center' }, { label: __('Right', 'easy-elements-for-gutenberg'), value: 'end' }, { label: __('Justify', 'easy-elements-for-gutenberg'), value: 'space-between' }]} onChange={(v) => update(index, 'flexAlign', v)} __next40pxDefaultSize __nextHasNoMarginBottom />
									<TextControl label={__('Middle Gap (px)', 'easy-elements-for-gutenberg')} type="number" value={item.flexGap || ''} onChange={(v) => update(index, 'flexGap', v)} __next40pxDefaultSize __nextHasNoMarginBottom />
								</>
							)}
						</>
					)}
					{!(opts.flex && item.dataFlex) && <SelectControl label={__('Alignment', 'easy-elements-for-gutenberg')} value={item.align || ''} options={ALIGN_OPTIONS} onChange={(v) => update(index, 'align', v)} __next40pxDefaultSize __nextHasNoMarginBottom />}
					<SelectControl label={__('Vertical Alignment', 'easy-elements-for-gutenberg')} value={item.verticalAlign || ''} options={VALIGN_OPTIONS} onChange={(v) => update(index, 'verticalAlign', v)} __next40pxDefaultSize __nextHasNoMarginBottom />
					<TextControl label={__('Custom Width (e.g. 30% or 200px)', 'easy-elements-for-gutenberg')} value={item.width || ''} onChange={(v) => update(index, 'width', v)} __next40pxDefaultSize __nextHasNoMarginBottom />
					<SelectControl label={__('Decoration', 'easy-elements-for-gutenberg')} value={item.decoration || ''} options={DECORATION_OPTIONS} onChange={(v) => update(index, 'decoration', v)} __next40pxDefaultSize __nextHasNoMarginBottom />
					{opts.colors && (
						<>
							<ColorPopover label={__('Background Color', 'easy-elements-for-gutenberg')} color={item.bgColor || ''} onChange={(v) => update(index, 'bgColor', v)} />
							<ColorPopover label={__('Text Color', 'easy-elements-for-gutenberg')} color={item.textColor || ''} onChange={(v) => update(index, 'textColor', v)} />
							<ColorPopover label={__('Icon Color', 'easy-elements-for-gutenberg')} color={item.iconColor || ''} onChange={(v) => update(index, 'iconColor', v)} />
						</>
					)}
				</>
			)}
		</>
	);

	const cellTooltip = (item, index, update) => (
		<>
			<ToggleControl label={__('Tooltip', 'easy-elements-for-gutenberg')} checked={!!item.tooltip} onChange={(v) => update(index, 'tooltip', v)} __nextHasNoMarginBottom />
			{item.tooltip && (
				<>
					<IconPicker label={__('Tooltip Icon', 'easy-elements-for-gutenberg')} value={item.tooltipIcon || ''} onChange={(v) => update(index, 'tooltipIcon', v)} />
					<TextareaControl label={__('Tooltip Description', 'easy-elements-for-gutenberg')} value={item.tooltipDesc || ''} onChange={(v) => update(index, 'tooltipDesc', v)} __nextHasNoMarginBottom />
				</>
			)}
		</>
	);

	const headerRep = makeRepeater('tableHeader', { text: __('Table Header', 'easy-elements-for-gutenberg') });
	const bodyRep = makeRepeater('tableBody', { text: __('Table Data', 'easy-elements-for-gutenberg'), type: 'icon' });
	const footerRep = makeRepeater('tableFooter', { text: __('Table Footer', 'easy-elements-for-gutenberg') });

	return (
		<div {...useBlockProps()}>
			<InspectorControls>
				<PanelBody title={__('Table Header', 'easy-elements-for-gutenberg')} initialOpen={true}>
					{headerRep.items.map((item, index) => (
						<div className="eelfg-tbl-repeater-item" key={index}>
							<ItemHead section="header" index={index} label={item.text} rep={headerRep} />
							{!isCollapsed('header', index) && (
								<>
									<TextControl label={__('Text', 'easy-elements-for-gutenberg')} value={item.text || ''} onChange={(v) => headerRep.update(index, 'text', v)} __next40pxDefaultSize __nextHasNoMarginBottom />
									<ToggleControl label={__('Icon', 'easy-elements-for-gutenberg')} checked={!!item.headerIcon} onChange={(v) => headerRep.update(index, 'headerIcon', v)} __nextHasNoMarginBottom />
									{item.headerIcon && <IconPicker label={__('Icon', 'easy-elements-for-gutenberg')} value={item.headIcon || ''} onChange={(v) => headerRep.update(index, 'headIcon', v)} />}
									{cellAdvanced(item, index, headerRep.update)}
									{cellTooltip(item, index, headerRep.update)}
								</>
							)}
						</div>
					))}
					<Button variant="primary" onClick={headerRep.add} icon={ICON_ADD}>{__('Add Header Cell', 'easy-elements-for-gutenberg')}</Button>
				</PanelBody>

				<PanelBody title={__('Table Body', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{bodyRep.items.map((item, index) => (
						<div className="eelfg-tbl-repeater-item" key={index}>
							<ItemHead section="body" index={index} label={item.text} rep={bodyRep} />
							{!isCollapsed('body', index) && (
								<>
									<ToggleControl label={__('New Row (start a new row at this cell)', 'easy-elements-for-gutenberg')} checked={!!item.row} onChange={(v) => bodyRep.update(index, 'row', v)} __nextHasNoMarginBottom />
									<SelectControl label={__('Select Type', 'easy-elements-for-gutenberg')} value={item.type || 'icon'} options={[{ label: __('Icon', 'easy-elements-for-gutenberg'), value: 'icon' }, { label: __('Image', 'easy-elements-for-gutenberg'), value: 'image' }]} onChange={(v) => bodyRep.update(index, 'type', v)} __next40pxDefaultSize __nextHasNoMarginBottom />
									{(item.type || 'icon') === 'icon' && <IconPicker label={__('Icon', 'easy-elements-for-gutenberg')} value={item.icon || ''} onChange={(v) => bodyRep.update(index, 'icon', v)} />}
									{item.type === 'image' && (
										<MediaUploadCheck>
											<MediaUpload
												onSelect={(media) => bodyRep.update(index, 'image', { id: media.id, url: media.url, alt: media.alt })}
												allowedTypes={['image']}
												value={item.image?.id}
												render={({ open }) => (
													<div style={{ marginBottom: '8px' }}>
														{item.image?.url && <img src={item.image.url} alt="" style={{ maxWidth: '100%', marginBottom: '6px' }} />}
														<Button variant="secondary" size="small" onClick={open}>{item.image?.url ? __('Replace', 'easy-elements-for-gutenberg') : __('Select Image', 'easy-elements-for-gutenberg')}</Button>
													</div>
												)}
											/>
										</MediaUploadCheck>
									)}
									<TextareaControl label={__('Text', 'easy-elements-for-gutenberg')} value={item.text || ''} onChange={(v) => bodyRep.update(index, 'text', v)} __nextHasNoMarginBottom />
									{cellAdvanced(item, index, bodyRep.update, { rowspan: true, flex: true, colors: true })}
									{cellTooltip(item, index, bodyRep.update)}
								</>
							)}
						</div>
					))}
					<Button variant="primary" onClick={bodyRep.add} icon={ICON_ADD}>{__('Add Body Cell', 'easy-elements-for-gutenberg')}</Button>
				</PanelBody>

				<PanelBody title={__('Table Footer', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{footerRep.items.map((item, index) => (
						<div className="eelfg-tbl-repeater-item" key={index}>
							<ItemHead section="footer" index={index} label={item.text} rep={footerRep} />
							{!isCollapsed('footer', index) && (
								<>
									<TextControl label={__('Text', 'easy-elements-for-gutenberg')} value={item.text || ''} onChange={(v) => footerRep.update(index, 'text', v)} __next40pxDefaultSize __nextHasNoMarginBottom />
									{cellAdvanced(item, index, footerRep.update)}
									{cellTooltip(item, index, footerRep.update)}
								</>
							)}
						</div>
					))}
					<Button variant="primary" onClick={footerRep.add} icon={ICON_ADD}>{__('Add Footer Cell', 'easy-elements-for-gutenberg')}</Button>
				</PanelBody>
			</InspectorControls>

			<InspectorControls group="styles">
				<PanelBody title={__('General', 'easy-elements-for-gutenberg')} initialOpen={false}>
					<SelectControl label={__('Vertical Alignment', 'easy-elements-for-gutenberg')} value={attributes.verticalAlignTable} options={VALIGN_OPTIONS} onChange={(v) => setAttributes({ verticalAlignTable: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					{box(__('Table Margin', 'easy-elements-for-gutenberg'), 'tableMargin')}
					{box(__('Cell Padding', 'easy-elements-for-gutenberg'), 'tablePadding')}
					{border(__('Table Border', 'easy-elements-for-gutenberg'), 'tableBorder')}
					{box(__('Table Border Radius', 'easy-elements-for-gutenberg'), 'tableRadius')}
				</PanelBody>

				<PanelBody title={__('Table Header', 'easy-elements-for-gutenberg')} initialOpen={false}>
					<SelectControl label={__('Alignment', 'easy-elements-for-gutenberg')} value={attributes.headerAlign} options={ALIGN_OPTIONS} onChange={(v) => setAttributes({ headerAlign: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					{color(__('Text Color', 'easy-elements-for-gutenberg'), 'headerTextColor')}
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'headerTypography')}
					{color(__('Background Color', 'easy-elements-for-gutenberg'), 'headerBgColor')}
					{border(__('Border', 'easy-elements-for-gutenberg'), 'headBorder')}
					{box(__('Border Radius', 'easy-elements-for-gutenberg'), 'theadRadius')}
					{box(__('Padding', 'easy-elements-for-gutenberg'), 'theadPadding')}
					<Divider />
					{color(__('Icon Color', 'easy-elements-for-gutenberg'), 'headerIconColor')}
					{num(__('Icon Size (px)', 'easy-elements-for-gutenberg'), 'headerIconSize')}
					{num(__('Icon Vertical Position (px)', 'easy-elements-for-gutenberg'), 'headerIconYPos')}
					{box(__('Icon Margin', 'easy-elements-for-gutenberg'), 'headerIconMargin')}
				</PanelBody>

				<PanelBody title={__('Table Body', 'easy-elements-for-gutenberg')} initialOpen={false}>
					<SelectControl label={__('Alignment', 'easy-elements-for-gutenberg')} value={attributes.bodyAlign} options={ALIGN_OPTIONS} onChange={(v) => setAttributes({ bodyAlign: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					{color(__('Text Color', 'easy-elements-for-gutenberg'), 'bodyTextColor')}
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'bodyTypography')}
					{color(__('Background Color', 'easy-elements-for-gutenberg'), 'bodyBgColor')}
					<ToggleControl label={__('Striped Background', 'easy-elements-for-gutenberg')} checked={attributes.stripedBg} onChange={(v) => setAttributes({ stripedBg: v })} __nextHasNoMarginBottom />
					{attributes.stripedBg && color(__('Secondary Background Color', 'easy-elements-for-gutenberg'), 'stripedBgColor')}
					<Divider />
					{color(__('Icon Color', 'easy-elements-for-gutenberg'), 'bodyIconColor')}
					{num(__('Icon Size (px)', 'easy-elements-for-gutenberg'), 'bodyIconSize')}
					{box(__('Icon Margin', 'easy-elements-for-gutenberg'), 'bodyIconGap')}
					<Divider />
					{box(__('Border Radius', 'easy-elements-for-gutenberg'), 'tbodyRadius')}
					{box(__('Padding', 'easy-elements-for-gutenberg'), 'tbodyPadding')}
					{box(__('Margin', 'easy-elements-for-gutenberg'), 'tbodyMargin')}
					{border(__('Border', 'easy-elements-for-gutenberg'), 'bodyBorder')}
					<Divider />
					{color(__('Tooltip Icon Color', 'easy-elements-for-gutenberg'), 'tooltipIconColor')}
					{num(__('Tooltip Icon Size (px)', 'easy-elements-for-gutenberg'), 'tooltipIconSize')}
					{box(__('Tooltip Icon Margin', 'easy-elements-for-gutenberg'), 'tooltipIconMargin')}
					<SelectControl label={__('Tooltip Align', 'easy-elements-for-gutenberg')} value={attributes.tooltipAlign} options={[{ label: __('Left', 'easy-elements-for-gutenberg'), value: 'left' }, { label: __('Top', 'easy-elements-for-gutenberg'), value: 'top' }, { label: __('Right', 'easy-elements-for-gutenberg'), value: 'right' }, { label: __('Bottom', 'easy-elements-for-gutenberg'), value: 'bottom' }]} onChange={(v) => setAttributes({ tooltipAlign: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					<Divider />
					{num(__('Image Size (px)', 'easy-elements-for-gutenberg'), 'imgSize')}
					{box(__('Image Border Radius', 'easy-elements-for-gutenberg'), 'imgRadius')}
				</PanelBody>

				<PanelBody title={__('Table Footer', 'easy-elements-for-gutenberg')} initialOpen={false}>
					<SelectControl label={__('Alignment', 'easy-elements-for-gutenberg')} value={attributes.footerAlign} options={ALIGN_OPTIONS} onChange={(v) => setAttributes({ footerAlign: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					{color(__('Text Color', 'easy-elements-for-gutenberg'), 'footerTextColor')}
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'footerTypography')}
					{color(__('Background Color', 'easy-elements-for-gutenberg'), 'footerBgColor')}
					{box(__('Border Radius', 'easy-elements-for-gutenberg'), 'tfootRadius')}
					{box(__('Padding', 'easy-elements-for-gutenberg'), 'tfootPadding')}
					{border(__('Border', 'easy-elements-for-gutenberg'), 'footBorder')}
				</PanelBody>
			</InspectorControls>

			<div ref={previewRef}>
				<ServerSideRender block="easy-elements-for-gutenberg/table" attributes={attributes} httpMethod="POST" />
			</div>
		</div>
	);
}
