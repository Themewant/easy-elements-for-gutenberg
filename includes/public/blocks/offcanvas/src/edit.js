import { __ } from '@wordpress/i18n';
import { useEffect, useState, useRef } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { ServerSideRender } from '@wordpress/server-side-render';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	ToggleControl,
	TextControl,
	BoxControl,
	Button,
	Notice,
	__experimentalDivider as Divider,
} from '@wordpress/components';

import ColorPopover from '../../custom-components/ColorPopover';
import IconPicker from '../../custom-components/IconPicker';
import TypographyControls from '../../custom-components/TypographyControls';

import './editor.scss';

export default function Edit({ attributes, setAttributes, clientId }) {
	const { blockId, offcanvasLayout, menuText } = attributes;

	useEffect(() => {
		if (!blockId) {
			setAttributes({ blockId: 'eelfg-oc-' + clientId.slice(0, 6) });
		}
	}, [blockId, clientId, setAttributes]);

	// Editor preview: the front-end view.js doesn't run inside ServerSideRender,
	// so wire up the open/close toggle here. Delegated on a persistent wrapper so
	// it survives the SSR markup being replaced.
	const previewRef = useRef(null);
	useEffect(() => {
		const root = previewRef.current;
		if (!root) return undefined;
		const doc = root.ownerDocument || document;
		const syncBody = () => {
			const anyOpen = !!root.querySelector('.eelfg-offcanvas.eelfg-active');
			doc.body.classList.toggle('eelfg-offcanvas-active', anyOpen);
		};
		const onClick = (e) => {
			const toggle = e.target.closest('.eelfg-offcanvas-toggle');
			if (!toggle || !root.contains(toggle)) return;
			e.preventDefault();
			e.stopPropagation();
			const target = toggle.getAttribute('data-target');
			const panel = target ? root.querySelector(target) : null;
			if (!panel) return;
			panel.classList.toggle('eelfg-active');
			syncBody();
		};
		root.addEventListener('click', onClick);
		return () => root.removeEventListener('click', onClick);
	}, []);

	const [templates, setTemplates] = useState([]);
	useEffect(() => {
		apiFetch({ path: '/wp/v2/eelfg-templates?per_page=100&status=publish&_fields=id,title' })
			.then((posts) => {
				setTemplates(
					(posts || []).map((p) => ({
						label: (p.title && p.title.rendered) || `#${p.id}`,
						value: String(p.id),
					}))
				);
			})
			.catch(() => setTemplates([]));
	}, []);

	const templateOptions = [{ label: __('— Select Template —', 'easy-elements-for-gutenberg'), value: '' }, ...templates];

	const color = (label, key) => <ColorPopover label={label} color={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const num = (label, key) => <TextControl label={label} type="number" value={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} __next40pxDefaultSize __nextHasNoMarginBottom />;

	const isClassic = offcanvasLayout === 'classic';

	return (
		<div {...useBlockProps()}>
			<InspectorControls>
				<PanelBody title={__('Offcanvas Settings', 'easy-elements-for-gutenberg')} initialOpen={true}>
					<SelectControl
						label={__('Offcanvas Layout', 'easy-elements-for-gutenberg')}
						value={offcanvasLayout}
						options={[
							{ label: __('Classic', 'easy-elements-for-gutenberg'), value: 'classic' },
							{ label: __('Modern', 'easy-elements-for-gutenberg'), value: 'modern' },
						]}
						onChange={(v) => setAttributes({ offcanvasLayout: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<TextControl label={__('Canvas Menu Text', 'easy-elements-for-gutenberg')} value={menuText} onChange={(v) => setAttributes({ menuText: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					<IconPicker label={__('Menu Icon', 'easy-elements-for-gutenberg')} value={attributes.btnIcon || ''} onChange={(v) => setAttributes({ btnIcon: v })} />
					{isClassic && (
						<SelectControl
							label={__('Open Position', 'easy-elements-for-gutenberg')}
							value={attributes.positionOffcanvas}
							options={[
								{ label: __('Left', 'easy-elements-for-gutenberg'), value: 'eelfg-offcanvas-left' },
								{ label: __('Right', 'easy-elements-for-gutenberg'), value: 'eelfg-offcanvas-right' },
							]}
							onChange={(v) => setAttributes({ positionOffcanvas: v })}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					)}
					{num(__('Canvas Width (px)', 'easy-elements-for-gutenberg'), 'offcanvasWidth')}
					<Divider />
					<SelectControl
						label={__('Select Template', 'easy-elements-for-gutenberg')}
						value={attributes.contentTemplate}
						options={templateOptions}
						onChange={(v) => setAttributes({ contentTemplate: v })}
						help={__('Pick an Easy Elements Template to show inside the panel.', 'easy-elements-for-gutenberg')}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					{!templates.length && (
						<Notice status="warning" isDismissible={false}>
							{__('No templates found. Create one under the Templates post type first.', 'easy-elements-for-gutenberg')}
						</Notice>
					)}
					<IconPicker label={__('Close Icon', 'easy-elements-for-gutenberg')} value={attributes.closeIcon || ''} onChange={(v) => setAttributes({ closeIcon: v })} />
					<ToggleControl label={__('Enable Blur', 'easy-elements-for-gutenberg')} checked={!!attributes.needBlur} onChange={(v) => setAttributes({ needBlur: v })} __nextHasNoMarginBottom />
				</PanelBody>
			</InspectorControls>

			<InspectorControls group="styles">
				<PanelBody title={__('Opener Icon', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{menuText !== '' && color(__('Text Color', 'easy-elements-for-gutenberg'), 'openerTextColor')}
					{menuText !== '' && <TypographyControls label={__('Typography', 'easy-elements-for-gutenberg')} attributes={attributes} setAttributes={setAttributes} attributeKey="openerTextTypography" />}
					{isClassic ? (
						<>
							{color(__('Hamburger Color', 'easy-elements-for-gutenberg'), 'openerIconColor')}
							{num(__('Hamburger Size (px)', 'easy-elements-for-gutenberg'), 'openerIconSize')}
						</>
					) : (
						<>
							{color(__('Hamburger Color', 'easy-elements-for-gutenberg'), 'openerHamburgerColor')}
							{num(__('Hamburger Size (px)', 'easy-elements-for-gutenberg'), 'openerIconSizeModern')}
						</>
					)}
				</PanelBody>

				<PanelBody title={__('Closing Icon', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{isClassic ? (
						<>
							{color(__('Icon Color', 'easy-elements-for-gutenberg'), 'closingIconColor')}
							{num(__('Icon Size (px)', 'easy-elements-for-gutenberg'), 'closingIconSize')}
						</>
					) : (
						color(__('Icon Color', 'easy-elements-for-gutenberg'), 'closingIconModernColor')
					)}
				</PanelBody>

				<PanelBody title={__('Offcanvas Item', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Background', 'easy-elements-for-gutenberg'), 'offcanvasBg')}
					<BoxControl label={__('Padding', 'easy-elements-for-gutenberg')} values={attributes.offcanvasPadding} onChange={(v) => setAttributes({ offcanvasPadding: v })} />
				</PanelBody>
			</InspectorControls>

			<div ref={previewRef}>
				<ServerSideRender block="easy-elements-for-gutenberg/offcanvas" attributes={attributes} httpMethod="POST" />
			</div>
		</div>
	);
}
