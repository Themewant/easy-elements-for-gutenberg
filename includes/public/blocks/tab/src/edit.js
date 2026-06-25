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
	TabPanel,
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
const ICON_COLLAPSE = SVG('M5 11h14v2H5z'); // minus (shown when expanded — click to collapse)
const ICON_EXPAND = SVG('M11 11V5h2v6h6v2h-6v6h-2v-6H5v-2z'); // plus (shown when collapsed — click to expand)

const STATE_TABS = [
	{ name: 'normal', title: __('Normal', 'easy-elements-for-gutenberg') },
	{ name: 'hover', title: __('Hover', 'easy-elements-for-gutenberg') },
];

const POSITION_OPTIONS = [
	{ label: __('Left', 'easy-elements-for-gutenberg'), value: 'left' },
	{ label: __('Top', 'easy-elements-for-gutenberg'), value: 'top' },
	{ label: __('Right', 'easy-elements-for-gutenberg'), value: 'right' },
];

export default function Edit({ attributes, setAttributes, clientId }) {
	const { blockId, tabs, iconPosition, layoutDirection } = attributes;

	useEffect(() => {
		if (!blockId) {
			setAttributes({ blockId: 'eelfg-tab-' + clientId.slice(0, 6) });
		}
	}, [blockId, clientId, setAttributes]);

	const items = Array.isArray(tabs) ? tabs : [];

	// Editor-only: track which repeater items are collapsed (by index).
	// First item starts expanded, all others collapsed.
	const [collapsed, setCollapsed] = useState(() => {
		const init = {};
		items.forEach((_, i) => {
			if (i !== 0) init[i] = true;
		});
		return init;
	});
	const toggleCollapsed = (i) => setCollapsed((cur) => ({ ...cur, [i]: !cur[i] }));

	// Editor preview interactivity: the front-end view.js doesn't run inside the
	// ServerSideRender output, so wire up tab switching here. We delegate clicks
	// on a persistent wrapper (the SSR markup is replaced on every re-render).
	const previewRef = useRef(null);
	useEffect(() => {
		const root = previewRef.current;
		if (!root) {
			return undefined;
		}
		const onClick = (e) => {
			const li = e.target.closest('.eelfg-tab-titles li');
			if (!li || !root.contains(li)) {
				return;
			}
			const wrapper = li.closest('.eelfg-tabs-wrapper');
			if (!wrapper) {
				return;
			}
			const tabId = li.getAttribute('data-tab');
			wrapper.querySelectorAll('.eelfg-tab-titles li').forEach((t) => t.classList.remove('active'));
			li.classList.add('active');
			// Toggle the `active` class only and strip any inline styles so the
			// stylesheet is the single source of truth (avoids a stale inline
			// `display:block` keeping a previously-shown panel visible).
			wrapper.querySelectorAll('.eelfg-tab-content').forEach((c) => {
				c.classList.toggle('active', c.id === tabId);
				c.style.removeProperty('display');
				c.style.removeProperty('opacity');
				c.style.removeProperty('transform');
			});
		};
		root.addEventListener('click', onClick);
		return () => root.removeEventListener('click', onClick);
	}, []);

	const updateItem = (i, key, val) =>
		setAttributes({ tabs: items.map((it, idx) => (idx === i ? { ...it, [key]: val } : it)) });

	const addItem = () =>
		setAttributes({
			tabs: [
				...items,
				{
					tabTitle: __('New Tab', 'easy-elements-for-gutenberg'),
					iconType: 'icon',
					icon: '',
					image: {},
					contentTitle: '',
					contentDescription: '',
					readMoreText: '',
					readMoreUrl: '#',
					readMoreNewTab: false,
				},
			],
		});

	const removeItem = (i) => setAttributes({ tabs: items.filter((_, idx) => idx !== i) });

	const moveItem = (i, dir) => {
		const t = i + dir;
		if (t < 0 || t >= items.length) return;
		const next = items.slice();
		const [m] = next.splice(i, 1);
		next.splice(t, 0, m);
		setAttributes({ tabs: next });
	};

	const color = (label, key) => (
		<ColorPopover label={label} color={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />
	);
	const typo = (label, key) => (
		<TypographyControls label={label} attributes={attributes} setAttributes={setAttributes} attributeKey={key} />
	);
	const border = (label, key) => (
		<BorderControl label={label} value={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />
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

	return (
		<div {...useBlockProps()}>
			<InspectorControls>
				<PanelBody title={__('Tabs', 'easy-elements-for-gutenberg')} initialOpen={true}>
					{items.map((item, index) => { const isCollapsed = !!collapsed[index]; return (
						<div className="eelfg-tab-repeater-item" key={index}>
							<div className="eelfg-tab-repeater-head">
								<strong>{item.tabTitle || `#${index + 1}`}</strong>
								<div>
									<Button icon={isCollapsed ? ICON_EXPAND : ICON_COLLAPSE} label={isCollapsed ? __('Expand', 'easy-elements-for-gutenberg') : __('Collapse', 'easy-elements-for-gutenberg')} onClick={() => toggleCollapsed(index)} size="small" />
										<Button icon={ICON_UP} label={__('Move up', 'easy-elements-for-gutenberg')} onClick={() => moveItem(index, -1)} disabled={index === 0} size="small" />
									<Button icon={ICON_DOWN} label={__('Move down', 'easy-elements-for-gutenberg')} onClick={() => moveItem(index, 1)} disabled={index === items.length - 1} size="small" />
									<Button icon={ICON_TRASH} label={__('Remove', 'easy-elements-for-gutenberg')} onClick={() => removeItem(index)} isDestructive size="small" />
								</div>
							</div>
							{!isCollapsed && (<>
								<TextControl
								label={__('Tab Title', 'easy-elements-for-gutenberg')}
								value={item.tabTitle || ''}
								onChange={(v) => updateItem(index, 'tabTitle', v)}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
							<SelectControl
								label={__('Icon / Image', 'easy-elements-for-gutenberg')}
								value={item.iconType || 'icon'}
								options={[
									{ label: __('None', 'easy-elements-for-gutenberg'), value: 'none' },
									{ label: __('Icon', 'easy-elements-for-gutenberg'), value: 'icon' },
									{ label: __('Image', 'easy-elements-for-gutenberg'), value: 'image' },
								]}
								onChange={(v) => updateItem(index, 'iconType', v)}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
							{(item.iconType || 'icon') === 'icon' && (
								<IconPicker label={__('Icon', 'easy-elements-for-gutenberg')} value={item.icon || ''} onChange={(v) => updateItem(index, 'icon', v)} />
							)}
							{item.iconType === 'image' && (
								<MediaUploadCheck>
									<MediaUpload
										onSelect={(media) => updateItem(index, 'image', { id: media.id, url: media.url, alt: media.alt })}
										allowedTypes={['image']}
										value={item.image?.id}
										render={({ open }) => (
											<div style={{ marginBottom: '8px' }}>
												{item.image?.url && <img src={item.image.url} alt="" style={{ maxWidth: '100%', marginBottom: '6px' }} />}
												<Button variant="secondary" size="small" onClick={open}>
													{item.image?.url ? __('Replace', 'easy-elements-for-gutenberg') : __('Select Image', 'easy-elements-for-gutenberg')}
												</Button>
											</div>
										)}
									/>
								</MediaUploadCheck>
							)}
							<TextControl
								label={__('Content Title', 'easy-elements-for-gutenberg')}
								value={item.contentTitle || ''}
								onChange={(v) => updateItem(index, 'contentTitle', v)}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
							<TextareaControl
								label={__('Description', 'easy-elements-for-gutenberg')}
								value={item.contentDescription || ''}
								onChange={(v) => updateItem(index, 'contentDescription', v)}
								__nextHasNoMarginBottom
							/>
							<TextControl
								label={__('Button Text', 'easy-elements-for-gutenberg')}
								value={item.readMoreText || ''}
								onChange={(v) => updateItem(index, 'readMoreText', v)}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
							<TextControl
								label={__('Button URL', 'easy-elements-for-gutenberg')}
								value={item.readMoreUrl || ''}
								onChange={(v) => updateItem(index, 'readMoreUrl', v)}
								placeholder="https://your-link.com"
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
							<ToggleControl
								label={__('Open in new tab', 'easy-elements-for-gutenberg')}
								checked={!!item.readMoreNewTab}
								onChange={(v) => updateItem(index, 'readMoreNewTab', v)}
								__nextHasNoMarginBottom
							/>
								</>
								)}
						</div>
					)})}
					<Button variant="primary" onClick={addItem} icon={ICON_ADD}>{__('Add Tab', 'easy-elements-for-gutenberg')}</Button>
				</PanelBody>

				<PanelBody title={__('Tab Title Settings', 'easy-elements-for-gutenberg')} initialOpen={false}>
					<SelectControl
						label={__('Icon Position', 'easy-elements-for-gutenberg')}
						value={iconPosition}
						options={POSITION_OPTIONS}
						onChange={(v) => setAttributes({ iconPosition: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<SelectControl
						label={__('Layout', 'easy-elements-for-gutenberg')}
						value={layoutDirection}
						options={POSITION_OPTIONS}
						onChange={(v) => setAttributes({ layoutDirection: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</PanelBody>
			</InspectorControls>

			<InspectorControls group="styles">
				<PanelBody title={__('Tab Nav', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'titleTypography')}
					<TabPanel tabs={STATE_TABS}>
						{(tab) =>
							tab.name === 'normal' ? (
								<>
									{color(__('Text Color', 'easy-elements-for-gutenberg'), 'titleColor')}
									{color(__('Background Color', 'easy-elements-for-gutenberg'), 'titleBgColor')}
									{border(__('Border', 'easy-elements-for-gutenberg'), 'titleBorder')}
									{box(__('Border Radius', 'easy-elements-for-gutenberg'), 'titleBorderRadius')}
									{box(__('Padding', 'easy-elements-for-gutenberg'), 'titlePadding')}
									{box(__('Margin', 'easy-elements-for-gutenberg'), 'titleMargin')}
									{color(__('Icon Color', 'easy-elements-for-gutenberg'), 'tabIconColor')}
									{color(__('Icon / Image Bg Color', 'easy-elements-for-gutenberg'), 'tabIconBgColor')}
								</>
							) : (
								<>
									{color(__('Text Color', 'easy-elements-for-gutenberg'), 'titleActiveColor')}
									{color(__('Background Color', 'easy-elements-for-gutenberg'), 'titleActiveBgColor')}
									{color(__('Border Color', 'easy-elements-for-gutenberg'), 'titleActiveBorderColor')}
								</>
							)
						}
					</TabPanel>
				</PanelBody>

				<PanelBody title={__('Tab Nav Border & Spacing', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{num(__('Bottom Spacing (px)', 'easy-elements-for-gutenberg'), 'titleBottomSpacing')}
					{border(__('Bottom Border (top layout)', 'easy-elements-for-gutenberg'), 'navBorder')}
				</PanelBody>

				<PanelBody title={__('Tab Content Area', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Background Color', 'easy-elements-for-gutenberg'), 'contentBgColor')}
					{border(__('Border', 'easy-elements-for-gutenberg'), 'contentBorder')}
					{box(__('Border Radius', 'easy-elements-for-gutenberg'), 'contentBorderRadius')}
					{box(__('Padding', 'easy-elements-for-gutenberg'), 'contentPadding')}
					{box(__('Margin', 'easy-elements-for-gutenberg'), 'contentMargin')}
					<SelectControl
						label={__('Alignment', 'easy-elements-for-gutenberg')}
						value={attributes.descriptionAlignment}
						options={[
							{ label: __('Left', 'easy-elements-for-gutenberg'), value: 'left' },
							{ label: __('Center', 'easy-elements-for-gutenberg'), value: 'center' },
							{ label: __('Right', 'easy-elements-for-gutenberg'), value: 'right' },
							{ label: __('Justify', 'easy-elements-for-gutenberg'), value: 'justify' },
						]}
						onChange={(v) => setAttributes({ descriptionAlignment: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</PanelBody>

				<PanelBody title={__('Content Title', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'contentTitleTypography')}
					{color(__('Color', 'easy-elements-for-gutenberg'), 'contentTitleColor')}
					{box(__('Margin', 'easy-elements-for-gutenberg'), 'contentTitleMargin')}
				</PanelBody>

				<PanelBody title={__('Description', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'descTypography')}
					{color(__('Color', 'easy-elements-for-gutenberg'), 'descColor')}
					{box(__('Margin', 'easy-elements-for-gutenberg'), 'descMargin')}
				</PanelBody>

				<PanelBody title={__('Button', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'btnTypography')}
					<TabPanel tabs={STATE_TABS}>
						{(tab) =>
							tab.name === 'normal' ? (
								<>
									{color(__('Text Color', 'easy-elements-for-gutenberg'), 'btnColor')}
									{color(__('Background Color', 'easy-elements-for-gutenberg'), 'btnBgColor')}
								</>
							) : (
								<>
									{color(__('Text Color', 'easy-elements-for-gutenberg'), 'btnHoverColor')}
									{color(__('Background Color', 'easy-elements-for-gutenberg'), 'btnHoverBgColor')}
									{color(__('Border Color', 'easy-elements-for-gutenberg'), 'btnHoverBorderColor')}
								</>
							)
						}
					</TabPanel>
					<Divider />
					{border(__('Border', 'easy-elements-for-gutenberg'), 'btnBorder')}
					{box(__('Border Radius', 'easy-elements-for-gutenberg'), 'btnBorderRadius')}
					{box(__('Padding', 'easy-elements-for-gutenberg'), 'btnPadding')}
					{box(__('Margin', 'easy-elements-for-gutenberg'), 'btnMargin')}
				</PanelBody>
			</InspectorControls>

			<div ref={previewRef}>
				<ServerSideRender block="easy-elements-for-gutenberg/tab" attributes={attributes} httpMethod="POST" />
			</div>
		</div>
	);
}
