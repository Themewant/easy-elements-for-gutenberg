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
	Button,
	__experimentalDivider as Divider,
} from '@wordpress/components';

import ColorPopover from '../../custom-components/ColorPopover';
import IconPicker from '../../custom-components/IconPicker';
import BorderControl from '../../custom-components/BorderControl';
import BoxShadowControls from '../../custom-components/BoxShadowControls';
import BackgroundControl from '../../custom-components/BackgroundControl';

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

const PLATFORM_OPTIONS = [
	{ label: 'Facebook', value: 'facebook' },
	{ label: 'Twitter/X', value: 'twitter' },
	{ label: 'Instagram', value: 'instagram' },
	{ label: 'LinkedIn', value: 'linkedin' },
	{ label: 'YouTube', value: 'youtube' },
	{ label: 'TikTok', value: 'tiktok' },
	{ label: 'Pinterest', value: 'pinterest' },
	{ label: 'WhatsApp', value: 'whatsapp' },
	{ label: 'Telegram', value: 'telegram' },
	{ label: 'Snapchat', value: 'snapchat' },
	{ label: 'Reddit', value: 'reddit' },
	{ label: 'Discord', value: 'discord' },
	{ label: 'Spotify', value: 'spotify' },
	{ label: 'Email', value: 'email' },
	{ label: 'Copy Link', value: 'copy' },
];

export default function Edit({ attributes, setAttributes, clientId }) {
	const { blockId, platforms, layout, openNewTab } = attributes;

	useEffect(() => {
		if (!blockId) {
			setAttributes({ blockId: 'eelfg-soc-' + clientId.slice(0, 6) });
		}
	}, [blockId, clientId, setAttributes]);

	const items = Array.isArray(platforms) ? platforms : [];
	const update = (i, key, val) => setAttributes({ platforms: items.map((it, idx) => (idx === i ? { ...it, [key]: val } : it)) });
	const add = () => setAttributes({ platforms: [...items, { platform: 'facebook', customIcon: '' }] });
	const remove = (i) => setAttributes({ platforms: items.filter((_, idx) => idx !== i) });
	const move = (i, dir) => {
		const t = i + dir;
		if (t < 0 || t >= items.length) return;
		const next = items.slice();
		const [m] = next.splice(i, 1);
		next.splice(t, 0, m);
		setAttributes({ platforms: next });
	};

	const num = (label, key) => <TextControl label={label} type="number" value={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} __next40pxDefaultSize __nextHasNoMarginBottom />;

	return (
		<div {...useBlockProps()}>
			<InspectorControls>
				<PanelBody title={__('Social Share', 'easy-elements-for-gutenberg')} initialOpen={true}>
					{items.map((item, index) => (
						<div className="eelfg-soc-repeater-item" key={index}>
							<div className="eelfg-soc-repeater-head">
								<strong>{item.platform || `#${index + 1}`}</strong>
								<div>
									<Button icon={ICON_UP} label={__('Move up', 'easy-elements-for-gutenberg')} onClick={() => move(index, -1)} disabled={index === 0} size="small" />
									<Button icon={ICON_DOWN} label={__('Move down', 'easy-elements-for-gutenberg')} onClick={() => move(index, 1)} disabled={index === items.length - 1} size="small" />
									<Button icon={ICON_TRASH} label={__('Remove', 'easy-elements-for-gutenberg')} onClick={() => remove(index)} isDestructive size="small" />
								</div>
							</div>
							<SelectControl
								label={__('Platform', 'easy-elements-for-gutenberg')}
								value={item.platform || 'facebook'}
								options={PLATFORM_OPTIONS}
								onChange={(v) => update(index, 'platform', v)}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
							<IconPicker label={__('Custom Icon (optional)', 'easy-elements-for-gutenberg')} value={item.customIcon || ''} onChange={(v) => update(index, 'customIcon', v)} />
						</div>
					))}
					<Button variant="primary" onClick={add} icon={ICON_ADD}>{__('Add Platform', 'easy-elements-for-gutenberg')}</Button>
					<Divider />
					<SelectControl
						label={__('Layout', 'easy-elements-for-gutenberg')}
						value={layout}
						options={[
							{ label: __('Horizontal', 'easy-elements-for-gutenberg'), value: 'horizontal' },
							{ label: __('Vertical', 'easy-elements-for-gutenberg'), value: 'vertical' },
							{ label: __('Grid', 'easy-elements-for-gutenberg'), value: 'grid' },
						]}
						onChange={(v) => setAttributes({ layout: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<ToggleControl
						label={__('Open in New Tab', 'easy-elements-for-gutenberg')}
						checked={openNewTab}
						onChange={(v) => setAttributes({ openNewTab: v })}
						__nextHasNoMarginBottom
					/>
				</PanelBody>
			</InspectorControls>

			<InspectorControls group="styles">
				<PanelBody title={__('Buttons', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{num(__('Icon Size (px)', 'easy-elements-for-gutenberg'), 'iconSize')}
					<BackgroundControl
						label={__('Background', 'easy-elements-for-gutenberg')}
						colorValue={attributes.iconBgColor}
						gradientValue={attributes.iconBgGradient}
						onColorChange={(v) => setAttributes({ iconBgColor: v && typeof v === 'object' ? v.hex : v || '' })}
						onGradientChange={(v) => setAttributes({ iconBgGradient: v || '' })}
					/>
					<ColorPopover label={__('Icon Color', 'easy-elements-for-gutenberg')} color={attributes.iconColor} onChange={(v) => setAttributes({ iconColor: v })} />
					{num(__('Button Size (px)', 'easy-elements-for-gutenberg'), 'buttonSize')}
					{num(__('Button Spacing (px)', 'easy-elements-for-gutenberg'), 'buttonSpacing')}
					<Divider />
					<BoxControl label={__('Border Radius', 'easy-elements-for-gutenberg')} values={attributes.buttonRadius} onChange={(v) => setAttributes({ buttonRadius: v })} />
					<BorderControl label={__('Border', 'easy-elements-for-gutenberg')} value={attributes.buttonBorder} onChange={(v) => setAttributes({ buttonBorder: v })} />
					<BoxShadowControls label={__('Box Shadow', 'easy-elements-for-gutenberg')} value={attributes.buttonBoxShadow} onChange={(v) => setAttributes({ buttonBoxShadow: v })} />
				</PanelBody>
			</InspectorControls>

			<ServerSideRender block="easy-elements-for-gutenberg/social-share" attributes={attributes} httpMethod="POST" />
		</div>
	);
}
