import { __ } from '@wordpress/i18n';
import { useEffect, useRef } from '@wordpress/element';
import { ServerSideRender } from '@wordpress/server-side-render';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	TextControl,
	RangeControl,
	__experimentalDivider as Divider,
} from '@wordpress/components';

import ColorPopover from '../../custom-components/ColorPopover';
import TypographyControls from '../../custom-components/TypographyControls';
import BackgroundControl from '../../custom-components/BackgroundControl';

import './editor.scss';

export default function Edit({ attributes, setAttributes, clientId }) {
	const { blockId, selectStyle, title, percent } = attributes;

	useEffect(() => {
		if (!blockId) {
			setAttributes({ blockId: 'eelfg-pb-' + clientId.slice(0, 6) });
		}
	}, [blockId, clientId, setAttributes]);

	// Editor preview: the front-end view.js doesn't run inside ServerSideRender,
	// so animate the fill here too. A MutationObserver re-runs the animation each
	// time the SSR markup is replaced (e.g. when percent/style changes).
	const previewRef = useRef(null);
	useEffect(() => {
		const root = previewRef.current;
		if (!root) return undefined;
		let timer;
		let raf;
		const animate = () => {
			const fill = root.querySelector('.eelfg-progress-fill');
			if (!fill) return;
			const target = (fill.getAttribute('data-width') || '0') + '%';
			fill.style.transition = 'none';
			fill.style.width = '0%';
			void fill.offsetWidth; // force reflow
			fill.style.transition = 'width 1.5s ease';
			raf = window.requestAnimationFrame(() => {
				fill.style.width = target;
			});
		};
		const schedule = () => {
			window.clearTimeout(timer);
			timer = window.setTimeout(animate, 60);
		};
		const mo = new MutationObserver(schedule);
		mo.observe(root, { childList: true, subtree: true });
		schedule();
		return () => {
			mo.disconnect();
			window.clearTimeout(timer);
			window.cancelAnimationFrame(raf);
		};
	}, []);

	const color = (label, key) => <ColorPopover label={label} color={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const typo = (label, key) => <TypographyControls label={label} attributes={attributes} setAttributes={setAttributes} attributeKey={key} />;
	const num = (label, key) => <TextControl label={label} type="number" value={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} __next40pxDefaultSize __nextHasNoMarginBottom />;

	return (
		<div {...useBlockProps()}>
			<InspectorControls>
				<PanelBody title={__('Progress Bar', 'easy-elements-for-gutenberg')} initialOpen={true}>
					<SelectControl
						label={__('Select Style', 'easy-elements-for-gutenberg')}
						value={selectStyle}
						options={[
							{ label: __('Style 1', 'easy-elements-for-gutenberg'), value: 'style1' },
							{ label: __('Style 2', 'easy-elements-for-gutenberg'), value: 'style2' },
						]}
						onChange={(v) => setAttributes({ selectStyle: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<TextControl
						label={__('Title', 'easy-elements-for-gutenberg')}
						value={title}
						onChange={(v) => setAttributes({ title: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<RangeControl
						label={__('Percent', 'easy-elements-for-gutenberg')}
						value={percent}
						onChange={(v) => setAttributes({ percent: v })}
						min={0}
						max={100}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</PanelBody>
			</InspectorControls>

			<InspectorControls group="styles">
				<PanelBody title={__('Progress', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{selectStyle === 'style1' && (
						<>
							{color(__('Progress Color (track)', 'easy-elements-for-gutenberg'), 'progressColor')}
							{color(__('Progress Bar Color (fill)', 'easy-elements-for-gutenberg'), 'progressBarColor')}
						</>
					)}
					{selectStyle === 'style2' && (
						<BackgroundControl
							label={__('Background', 'easy-elements-for-gutenberg')}
							colorValue={attributes.style2BgColor}
							gradientValue={attributes.style2BgGradient}
							onColorChange={(v) => setAttributes({ style2BgColor: v && typeof v === 'object' ? v.hex : v || '' })}
							onGradientChange={(v) => setAttributes({ style2BgGradient: v || '' })}
						/>
					)}
					<Divider />
					{num(__('Height (px)', 'easy-elements-for-gutenberg'), 'progressHeight')}
					{num(__('Border Radius (px)', 'easy-elements-for-gutenberg'), 'progressRadius')}
				</PanelBody>

				<PanelBody title={__('Title', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Color', 'easy-elements-for-gutenberg'), 'titleColor')}
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'titleTypography')}
				</PanelBody>

				<PanelBody title={__('Percent', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Color', 'easy-elements-for-gutenberg'), 'percentColor')}
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'percentTypography')}
				</PanelBody>
			</InspectorControls>

			<div ref={previewRef}>
				<ServerSideRender block="easy-elements-for-gutenberg/progress" attributes={attributes} httpMethod="POST" />
			</div>
		</div>
	);
}
