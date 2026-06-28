import { __ } from '@wordpress/i18n';
import { useEffect, useRef } from '@wordpress/element';
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
	RangeControl,
	TextControl,
	BoxControl,
	Button,
	__experimentalDivider as Divider,
} from '@wordpress/components';

import { initComparison } from './comparison';

import './editor.scss';

export default function Edit({ attributes, setAttributes, clientId }) {
	const { blockId, beforeImage, afterImage, orientation, offset } = attributes;

	useEffect(() => {
		if (!blockId) {
			setAttributes({ blockId: 'eelfg-cmp-' + clientId.slice(0, 6) });
		}
	}, [blockId, clientId, setAttributes]);

	// Editor preview: the front-end view.js doesn't run inside ServerSideRender,
	// so initialise the slider here. A MutationObserver re-inits whenever the SSR
	// markup is replaced (e.g. when images / offset / orientation change).
	const previewRef = useRef(null);
	useEffect(() => {
		const root = previewRef.current;
		if (!root) return undefined;
		let timer;
		const run = () => {
			const c = root.querySelector('.eelfg-comparison-container');
			if (c) {
				delete c.dataset.eelfgCmpInit;
				initComparison(c);
			}
		};
		const schedule = () => {
			window.clearTimeout(timer);
			timer = window.setTimeout(run, 80);
		};
		const mo = new MutationObserver(schedule);
		mo.observe(root, { childList: true, subtree: true });
		schedule();
		return () => {
			mo.disconnect();
			window.clearTimeout(timer);
		};
	}, []);

	const imageControl = (label, key) => (
		<MediaUploadCheck>
			<MediaUpload
				onSelect={(media) => setAttributes({ [key]: { id: media.id, url: media.url, alt: media.alt } })}
				allowedTypes={['image']}
				value={attributes[key]?.id}
				render={({ open }) => (
					<div style={{ marginBottom: '12px' }}>
						<div style={{ marginBottom: '6px', fontWeight: 500 }}>{label}</div>
						{attributes[key]?.url && <img src={attributes[key].url} alt="" style={{ maxWidth: '100%', marginBottom: '6px' }} />}
						<Button variant="secondary" size="small" onClick={open}>{attributes[key]?.url ? __('Replace', 'easy-elements-for-gutenberg') : __('Select Image', 'easy-elements-for-gutenberg')}</Button>
					</div>
				)}
			/>
		</MediaUploadCheck>
	);

	return (
		<div {...useBlockProps()}>
			<InspectorControls>
				<PanelBody title={__('Images', 'easy-elements-for-gutenberg')} initialOpen={true}>
					{imageControl(__('Before Image', 'easy-elements-for-gutenberg'), 'beforeImage')}
					{imageControl(__('After Image', 'easy-elements-for-gutenberg'), 'afterImage')}
					<Divider />
					<SelectControl
						label={__('Layout', 'easy-elements-for-gutenberg')}
						value={orientation}
						options={[
							{ label: __('Horizontal', 'easy-elements-for-gutenberg'), value: 'horizontal' },
							{ label: __('Vertical', 'easy-elements-for-gutenberg'), value: 'vertical' },
						]}
						onChange={(v) => setAttributes({ orientation: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<RangeControl
						label={__('Default Offset (%)', 'easy-elements-for-gutenberg')}
						value={offset}
						onChange={(v) => setAttributes({ offset: v })}
						min={0}
						max={100}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</PanelBody>
			</InspectorControls>

			<InspectorControls group="styles">
				<PanelBody title={__('Styles', 'easy-elements-for-gutenberg')} initialOpen={false}>
					<TextControl
						label={__('Height (px)', 'easy-elements-for-gutenberg')}
						type="number"
						value={attributes.height}
						onChange={(v) => setAttributes({ height: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<BoxControl label={__('Container Radius', 'easy-elements-for-gutenberg')} values={attributes.containerRadius} onChange={(v) => setAttributes({ containerRadius: v })} />
				</PanelBody>
			</InspectorControls>

			<div ref={previewRef}>
				<ServerSideRender block="easy-elements-for-gutenberg/image-comparison" attributes={attributes} httpMethod="POST" />
			</div>
		</div>
	);
}
