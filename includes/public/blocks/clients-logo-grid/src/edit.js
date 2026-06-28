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
	BoxControl,
	Button,
	TabPanel,
	__experimentalDivider as Divider,
} from '@wordpress/components';

import ColorPopover from '../../custom-components/ColorPopover';
import BorderControl from '../../custom-components/BorderControl';
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

const COLS = ['1', '2', '3', '4', '5', '6'].map((n) => ({ label: `${n} ${n === '1' ? 'Column' : 'Columns'}`, value: n }));

const STATE_TABS = [
	{ name: 'normal', title: __('Normal', 'easy-elements-for-gutenberg') },
	{ name: 'hover', title: __('Hover', 'easy-elements-for-gutenberg') },
];

export default function Edit({ attributes, setAttributes, clientId }) {
	const { blockId, logos, hoverSwap, grayscale } = attributes;

	useEffect(() => {
		if (!blockId) {
			setAttributes({ blockId: 'eelfg-clg-' + clientId.slice(0, 6) });
		}
	}, [blockId, clientId, setAttributes]);

	const items = Array.isArray(logos) ? logos : [];
	const update = (i, key, val) => setAttributes({ logos: items.map((it, idx) => (idx === i ? { ...it, [key]: val } : it)) });
	const add = () => setAttributes({ logos: [...items, { image: {}, link: '', linkNewTab: false, linkNofollow: false }] });
	const remove = (i) => setAttributes({ logos: items.filter((_, idx) => idx !== i) });
	const move = (i, dir) => {
		const t = i + dir;
		if (t < 0 || t >= items.length) return;
		const next = items.slice();
		const [m] = next.splice(i, 1);
		next.splice(t, 0, m);
		setAttributes({ logos: next });
	};

	const color = (label, key) => <ColorPopover label={label} color={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const box = (label, key) => <BoxControl label={label} values={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const num = (label, key) => <TextControl label={label} type="number" value={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} __next40pxDefaultSize __nextHasNoMarginBottom />;

	return (
		<div {...useBlockProps()}>
			<InspectorControls>
				<PanelBody title={__('Logo Settings', 'easy-elements-for-gutenberg')} initialOpen={true}>
					{items.map((item, index) => (
						<div className="eelfg-clg-repeater-item" key={index}>
							<div className="eelfg-clg-repeater-head">
								<strong>{__('Logo', 'easy-elements-for-gutenberg')} #{index + 1}</strong>
								<div>
									<Button icon={ICON_UP} label={__('Move up', 'easy-elements-for-gutenberg')} onClick={() => move(index, -1)} disabled={index === 0} size="small" />
									<Button icon={ICON_DOWN} label={__('Move down', 'easy-elements-for-gutenberg')} onClick={() => move(index, 1)} disabled={index === items.length - 1} size="small" />
									<Button icon={ICON_TRASH} label={__('Remove', 'easy-elements-for-gutenberg')} onClick={() => remove(index)} isDestructive size="small" />
								</div>
							</div>
							<MediaUploadCheck>
								<MediaUpload
									onSelect={(media) => update(index, 'image', { id: media.id, url: media.url, alt: media.alt })}
									allowedTypes={['image']}
									value={item.image?.id}
									render={({ open }) => (
										<div style={{ marginBottom: '8px' }}>
											{item.image?.url && <img src={item.image.url} alt="" style={{ maxWidth: '100%', marginBottom: '6px' }} />}
											<Button variant="secondary" size="small" onClick={open}>{item.image?.url ? __('Replace Logo', 'easy-elements-for-gutenberg') : __('Select Logo', 'easy-elements-for-gutenberg')}</Button>
										</div>
									)}
								/>
							</MediaUploadCheck>
							<TextControl label={__('Link', 'easy-elements-for-gutenberg')} value={item.link || ''} onChange={(v) => update(index, 'link', v)} placeholder="https://example.com" __next40pxDefaultSize __nextHasNoMarginBottom />
							{item.link && (
								<>
									<ToggleControl label={__('Open in new tab', 'easy-elements-for-gutenberg')} checked={!!item.linkNewTab} onChange={(v) => update(index, 'linkNewTab', v)} __nextHasNoMarginBottom />
									<ToggleControl label={__('Add nofollow', 'easy-elements-for-gutenberg')} checked={!!item.linkNofollow} onChange={(v) => update(index, 'linkNofollow', v)} __nextHasNoMarginBottom />
								</>
							)}
						</div>
					))}
					<Button variant="primary" onClick={add} icon={ICON_ADD}>{__('Add Logo', 'easy-elements-for-gutenberg')}</Button>
					<Divider />
					{num(__('Image Width (px)', 'easy-elements-for-gutenberg'), 'imageWidth')}
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
					<SelectControl label={__('Columns', 'easy-elements-for-gutenberg')} value={attributes.columns} options={COLS} onChange={(v) => setAttributes({ columns: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					<SelectControl label={__('Columns (Tablet)', 'easy-elements-for-gutenberg')} value={attributes.columnsTablet} options={COLS} onChange={(v) => setAttributes({ columnsTablet: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					<SelectControl label={__('Columns (Mobile)', 'easy-elements-for-gutenberg')} value={attributes.columnsMobile} options={COLS} onChange={(v) => setAttributes({ columnsMobile: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					<ToggleControl label={__('Image Hover Swap Effect', 'easy-elements-for-gutenberg')} checked={hoverSwap} onChange={(v) => setAttributes({ hoverSwap: v })} __nextHasNoMarginBottom />
				</PanelBody>
			</InspectorControls>

			<InspectorControls group="styles">
				<PanelBody title={__('Item', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{num(__('Item Max Width (px)', 'easy-elements-for-gutenberg'), 'itemWidth')}
					{num(__('Item Height (px)', 'easy-elements-for-gutenberg'), 'itemHeight')}
					{box(__('Item Space', 'easy-elements-for-gutenberg'), 'itemSpace')}
					{box(__('Padding', 'easy-elements-for-gutenberg'), 'itemPadding')}
					{box(__('Border Radius', 'easy-elements-for-gutenberg'), 'itemRadius')}
					{num(__('Transition (seconds)', 'easy-elements-for-gutenberg'), 'transition')}
					<Divider />
					<TabPanel tabs={STATE_TABS}>
						{(tab) =>
							tab.name === 'normal' ? (
								<>
									{color(__('Item Background', 'easy-elements-for-gutenberg'), 'itemBg')}
									<BorderControl label={__('Border', 'easy-elements-for-gutenberg')} value={attributes.itemBorder} onChange={(v) => setAttributes({ itemBorder: v })} />
									<BoxShadowControls label={__('Box Shadow', 'easy-elements-for-gutenberg')} value={attributes.itemBoxShadow} onChange={(v) => setAttributes({ itemBoxShadow: v })} />
									{num(__('Opacity (0–1)', 'easy-elements-for-gutenberg'), 'itemOpacity')}
									{!hoverSwap && num(__('Transform Scale (e.g. 1)', 'easy-elements-for-gutenberg'), 'itemScale')}
								</>
							) : (
								<>
									{color(__('Item Hover Background', 'easy-elements-for-gutenberg'), 'itemHoverBg')}
									<BorderControl label={__('Border', 'easy-elements-for-gutenberg')} value={attributes.itemHoverBorder} onChange={(v) => setAttributes({ itemHoverBorder: v })} />
									<BoxShadowControls label={__('Box Shadow', 'easy-elements-for-gutenberg')} value={attributes.itemHoverBoxShadow} onChange={(v) => setAttributes({ itemHoverBoxShadow: v })} />
									{num(__('Opacity (0–1)', 'easy-elements-for-gutenberg'), 'itemHoverOpacity')}
									{!hoverSwap && num(__('Transform Scale (e.g. 1.1)', 'easy-elements-for-gutenberg'), 'itemHoverScale')}
								</>
							)
						}
					</TabPanel>
				</PanelBody>

				<PanelBody title={__('Grayscale', 'easy-elements-for-gutenberg')} initialOpen={false}>
					<ToggleControl label={__('Image Grayscale', 'easy-elements-for-gutenberg')} checked={grayscale} onChange={(v) => setAttributes({ grayscale: v })} __nextHasNoMarginBottom />
					{grayscale && (
						<SelectControl
							label={__('Grayscale', 'easy-elements-for-gutenberg')}
							value={attributes.grayscaleOption}
							options={[
								{ label: __('Default Grayscale', 'easy-elements-for-gutenberg'), value: 'normal_grayscale' },
								{ label: __('Hover Grayscale', 'easy-elements-for-gutenberg'), value: 'hover_grayscale' },
								{ label: __('Hover to Default Image', 'easy-elements-for-gutenberg'), value: 'hover_to_default' },
							]}
							onChange={(v) => setAttributes({ grayscaleOption: v })}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					)}
				</PanelBody>
			</InspectorControls>

			<ServerSideRender block="easy-elements-for-gutenberg/clients-logo-grid" attributes={attributes} httpMethod="POST" />
		</div>
	);
}
