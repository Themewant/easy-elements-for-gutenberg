import { __ } from '@wordpress/i18n';
import { useEffect } from '@wordpress/element';
import { ServerSideRender } from '@wordpress/server-side-render';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
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
const ICON_UP = SVG('M12 6.6l-6 6 1.4 1.4 4.6-4.6 4.6 4.6 1.4-1.4z');
const ICON_DOWN = SVG('M12 15.4l6-6-1.4-1.4-4.6 4.6-4.6-4.6-1.4 1.4z');
const ICON_TRASH = SVG('M9 3v1H4v2h16V4h-5V3H9zM6 7l1 13h10l1-13H6zm4 2h1v9h-1V9zm3 0h1v9h-1V9z');
const ICON_ADD = SVG('M11 5v6H5v2h6v6h2v-6h6v-2h-6V5z');

const COLS = ['1', '2', '3', '4', '5', '6'].map((n) => ({ label: `${n} ${n === '1' ? 'Column' : 'Columns'}`, value: n }));

export default function Edit({ attributes, setAttributes, clientId }) {
	const { blockId, items, iconDirection, textAlign, titleTag } = attributes;

	useEffect(() => {
		if (!blockId) {
			setAttributes({ blockId: 'eelfg-pg-' + clientId.slice(0, 6) });
		}
	}, [blockId, clientId, setAttributes]);

	const list = Array.isArray(items) ? items : [];
	const update = (i, key, val) => setAttributes({ items: list.map((it, idx) => (idx === i ? { ...it, [key]: val } : it)) });
	const add = () =>
		setAttributes({
			items: [
				...list,
				{ icon: 'eelfg-icon-favorite', title: __('New Step', 'easy-elements-for-gutenberg'), description: '', link: '', linkNewTab: false, linkNofollow: false, numberType: 'p_number', processNumber: String(list.length + 1), processIcon: 'eelfg-icon-arrow-right' },
			],
		});
	const remove = (i) => setAttributes({ items: list.filter((_, idx) => idx !== i) });
	const move = (i, dir) => {
		const t = i + dir;
		if (t < 0 || t >= list.length) return;
		const next = list.slice();
		const [m] = next.splice(i, 1);
		next.splice(t, 0, m);
		setAttributes({ items: next });
	};

	const color = (label, key) => <ColorPopover label={label} color={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const typo = (label, key) => <TypographyControls label={label} attributes={attributes} setAttributes={setAttributes} attributeKey={key} />;
	const box = (label, key) => <BoxControl label={label} values={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const num = (label, key) => <TextControl label={label} type="number" value={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} __next40pxDefaultSize __nextHasNoMarginBottom />;

	return (
		<div {...useBlockProps()}>
			<InspectorControls>
				<PanelBody title={__('Process Grid', 'easy-elements-for-gutenberg')} initialOpen={true}>
					{list.map((item, index) => (
						<div className="eelfg-pg-repeater-item" key={index}>
							<div className="eelfg-pg-repeater-head">
								<strong>{item.title || `#${index + 1}`}</strong>
								<div>
									<Button icon={ICON_UP} label={__('Move up', 'easy-elements-for-gutenberg')} onClick={() => move(index, -1)} disabled={index === 0} size="small" />
									<Button icon={ICON_DOWN} label={__('Move down', 'easy-elements-for-gutenberg')} onClick={() => move(index, 1)} disabled={index === list.length - 1} size="small" />
									<Button icon={ICON_TRASH} label={__('Remove', 'easy-elements-for-gutenberg')} onClick={() => remove(index)} isDestructive size="small" />
								</div>
							</div>
							<IconPicker label={__('Icon', 'easy-elements-for-gutenberg')} value={item.icon || ''} onChange={(v) => update(index, 'icon', v)} />
							<TextControl label={__('Title', 'easy-elements-for-gutenberg')} value={item.title || ''} onChange={(v) => update(index, 'title', v)} __next40pxDefaultSize __nextHasNoMarginBottom />
							<TextareaControl label={__('Description', 'easy-elements-for-gutenberg')} value={item.description || ''} onChange={(v) => update(index, 'description', v)} __nextHasNoMarginBottom />
							<TextControl label={__('Link', 'easy-elements-for-gutenberg')} value={item.link || ''} onChange={(v) => update(index, 'link', v)} placeholder="https://example.com" __next40pxDefaultSize __nextHasNoMarginBottom />
							{item.link && (
								<>
									<ToggleControl label={__('Open in new tab', 'easy-elements-for-gutenberg')} checked={!!item.linkNewTab} onChange={(v) => update(index, 'linkNewTab', v)} __nextHasNoMarginBottom />
									<ToggleControl label={__('Add nofollow', 'easy-elements-for-gutenberg')} checked={!!item.linkNofollow} onChange={(v) => update(index, 'linkNofollow', v)} __nextHasNoMarginBottom />
								</>
							)}
							<SelectControl
								label={__('Process Number or Icon', 'easy-elements-for-gutenberg')}
								value={item.numberType || 'p_number'}
								options={[
									{ label: __('Process Number', 'easy-elements-for-gutenberg'), value: 'p_number' },
									{ label: __('Process Icon', 'easy-elements-for-gutenberg'), value: 'p_icon' },
								]}
								onChange={(v) => update(index, 'numberType', v)}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
							{(item.numberType || 'p_number') === 'p_number' ? (
								<TextControl label={__('Process Number', 'easy-elements-for-gutenberg')} value={item.processNumber || ''} onChange={(v) => update(index, 'processNumber', v)} __next40pxDefaultSize __nextHasNoMarginBottom />
							) : (
								<IconPicker label={__('Process Icon', 'easy-elements-for-gutenberg')} value={item.processIcon || ''} onChange={(v) => update(index, 'processIcon', v)} />
							)}
						</div>
					))}
					<Button variant="primary" onClick={add} icon={ICON_ADD}>{__('Add Item', 'easy-elements-for-gutenberg')}</Button>
					<Divider />
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
					<SelectControl
						label={__('Alignment', 'easy-elements-for-gutenberg')}
						value={textAlign}
						options={[
							{ label: __('Left', 'easy-elements-for-gutenberg'), value: 'left' },
							{ label: __('Center', 'easy-elements-for-gutenberg'), value: 'center' },
							{ label: __('Right', 'easy-elements-for-gutenberg'), value: 'right' },
						]}
						onChange={(v) => setAttributes({ textAlign: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<SelectControl
						label={__('Title HTML Tag', 'easy-elements-for-gutenberg')}
						value={titleTag}
						options={['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p'].map((t) => ({ label: t.toUpperCase(), value: t }))}
						onChange={(v) => setAttributes({ titleTag: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<SelectControl label={__('Columns', 'easy-elements-for-gutenberg')} value={attributes.columns} options={COLS} onChange={(v) => setAttributes({ columns: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					<SelectControl label={__('Columns (Tablet)', 'easy-elements-for-gutenberg')} value={attributes.columnsTablet} options={COLS} onChange={(v) => setAttributes({ columnsTablet: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					<SelectControl label={__('Columns (Mobile)', 'easy-elements-for-gutenberg')} value={attributes.columnsMobile} options={COLS} onChange={(v) => setAttributes({ columnsMobile: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					{box(__('Item Space', 'easy-elements-for-gutenberg'), 'itemPadding')}
				</PanelBody>
			</InspectorControls>

			<InspectorControls group="styles">
				<PanelBody title={__('Box', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Background Color', 'easy-elements-for-gutenberg'), 'boxBgColor')}
					{box(__('Padding', 'easy-elements-for-gutenberg'), 'boxPadding')}
					<BorderControl label={__('Border', 'easy-elements-for-gutenberg')} value={attributes.boxBorder} onChange={(v) => setAttributes({ boxBorder: v })} />
					{box(__('Border Radius', 'easy-elements-for-gutenberg'), 'boxRadius')}
					<BoxShadowControls label={__('Box Shadow', 'easy-elements-for-gutenberg')} value={attributes.boxShadow} onChange={(v) => setAttributes({ boxShadow: v })} />
				</PanelBody>

				<PanelBody title={__('Icon', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Color', 'easy-elements-for-gutenberg'), 'iconColor')}
					{color(__('Background', 'easy-elements-for-gutenberg'), 'iconBgColor')}
					{num(__('Size (px)', 'easy-elements-for-gutenberg'), 'iconSize')}
					<BorderControl label={__('Border', 'easy-elements-for-gutenberg')} value={attributes.iconBorder} onChange={(v) => setAttributes({ iconBorder: v })} />
					{box(__('Border Radius', 'easy-elements-for-gutenberg'), 'iconRadius')}
				</PanelBody>

				<PanelBody title={__('Title', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Color', 'easy-elements-for-gutenberg'), 'titleColor')}
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'titleTypography')}
					{box(__('Margin', 'easy-elements-for-gutenberg'), 'titleMargin')}
				</PanelBody>

				<PanelBody title={__('Description', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Color', 'easy-elements-for-gutenberg'), 'descColor')}
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'descTypography')}
				</PanelBody>

				<PanelBody title={__('Number', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Color', 'easy-elements-for-gutenberg'), 'numberColor')}
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'numberTypography')}
					{num(__('Opacity (0–1)', 'easy-elements-for-gutenberg'), 'numberOpacity')}
					{num(__('Vertical Position (px)', 'easy-elements-for-gutenberg'), 'numberOffsetY')}
					{num(__('Horizontal Position (px)', 'easy-elements-for-gutenberg'), 'numberOffsetX')}
				</PanelBody>
			</InspectorControls>

			<ServerSideRender block="easy-elements-for-gutenberg/process-grid" attributes={attributes} httpMethod="POST" />
		</div>
	);
}
