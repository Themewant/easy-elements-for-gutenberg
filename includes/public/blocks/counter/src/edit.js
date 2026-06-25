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
	__experimentalDivider as Divider,
} from '@wordpress/components';

import ColorPopover from '../../custom-components/ColorPopover';
import IconPicker from '../../custom-components/IconPicker';
import TypographyControls from '../../custom-components/TypographyControls';
import BorderControl from '../../custom-components/BorderControl';
import BoxShadowControls from '../../custom-components/BoxShadowControls';

import './editor.scss';

const ALIGN3 = [
	{ label: __('Start', 'easy-elements-for-gutenberg'), value: 'flex-start' },
	{ label: __('Center', 'easy-elements-for-gutenberg'), value: 'center' },
	{ label: __('End', 'easy-elements-for-gutenberg'), value: 'flex-end' },
];

export default function Edit({ attributes, setAttributes, clientId }) {
	const { blockId, iconEnable, title, titlePosition } = attributes;

	useEffect(() => {
		if (!blockId) {
			setAttributes({ blockId: 'eelfg-cnt-' + clientId.slice(0, 6) });
		}
	}, [blockId, clientId, setAttributes]);

	const color = (label, key) => <ColorPopover label={label} color={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const typo = (label, key) => <TypographyControls label={label} attributes={attributes} setAttributes={setAttributes} attributeKey={key} />;
	const border = (label, key) => <BorderControl label={label} value={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const shadow = (label, key) => <BoxShadowControls label={label} value={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const box = (label, key) => <BoxControl label={label} values={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const num = (label, key) => <TextControl label={label} type="number" value={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} __next40pxDefaultSize __nextHasNoMarginBottom />;
	const tshadow = (label, key) => {
		const v = attributes[key] || {};
		const set = (patch) => setAttributes({ [key]: { ...v, ...patch } });
		return (
			<div style={{ marginBottom: '12px' }}>
				<strong style={{ display: 'block', marginBottom: '6px' }}>{label}</strong>
				<TextControl label={__('Offset X', 'easy-elements-for-gutenberg')} type="number" value={v.x ?? ''} onChange={(x) => set({ x })} __next40pxDefaultSize __nextHasNoMarginBottom />
				<TextControl label={__('Offset Y', 'easy-elements-for-gutenberg')} type="number" value={v.y ?? ''} onChange={(y) => set({ y })} __next40pxDefaultSize __nextHasNoMarginBottom />
				<TextControl label={__('Blur', 'easy-elements-for-gutenberg')} type="number" value={v.blur ?? ''} onChange={(blur) => set({ blur })} __next40pxDefaultSize __nextHasNoMarginBottom />
				<ColorPopover label={__('Shadow Color', 'easy-elements-for-gutenberg')} color={v.color} onChange={(c) => set({ color: c })} />
			</div>
		);
	};

	return (
		<div {...useBlockProps()}>
			<InspectorControls>
				<PanelBody title={__('Counter', 'easy-elements-for-gutenberg')} initialOpen={true}>
					{num(__('Ending Number', 'easy-elements-for-gutenberg'), 'number')}
					{num(__('Starting Number', 'easy-elements-for-gutenberg'), 'startNumber')}
					<TextControl label={__('Number Prefix', 'easy-elements-for-gutenberg')} value={attributes.prefix} onChange={(v) => setAttributes({ prefix: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					<TextControl label={__('Number Suffix', 'easy-elements-for-gutenberg')} value={attributes.suffix} onChange={(v) => setAttributes({ suffix: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					{num(__('Animation Duration (ms)', 'easy-elements-for-gutenberg'), 'duration')}
					<SelectControl
						label={__('Separator', 'easy-elements-for-gutenberg')}
						value={attributes.format}
						options={[
							{ label: __('Default', 'easy-elements-for-gutenberg'), value: 'default' },
							{ label: __('Comma', 'easy-elements-for-gutenberg'), value: 'comma' },
							{ label: __('Dot', 'easy-elements-for-gutenberg'), value: 'dot' },
							{ label: __('Space', 'easy-elements-for-gutenberg'), value: 'space' },
							{ label: __('Underline', 'easy-elements-for-gutenberg'), value: 'underline' },
						]}
						onChange={(v) => setAttributes({ format: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<SelectControl
						label={__('Animation Style', 'easy-elements-for-gutenberg')}
						value={attributes.animationType}
						options={[
							{ label: __('Counter', 'easy-elements-for-gutenberg'), value: 'counter' },
							{ label: __('Odometer', 'easy-elements-for-gutenberg'), value: 'odometer' },
						]}
						onChange={(v) => setAttributes({ animationType: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<Divider />
					<ToggleControl label={__('Show Icon', 'easy-elements-for-gutenberg')} checked={iconEnable} onChange={(v) => setAttributes({ iconEnable: v })} __nextHasNoMarginBottom />
					{iconEnable && <IconPicker label={__('Icon', 'easy-elements-for-gutenberg')} value={attributes.icon} onChange={(v) => setAttributes({ icon: v })} />}
					<Divider />
					<TextControl label={__('Title', 'easy-elements-for-gutenberg')} value={title} onChange={(v) => setAttributes({ title: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					<SelectControl label={__('Title Tag', 'easy-elements-for-gutenberg')} value={attributes.titleTag} options={['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'div', 'span'].map((t) => ({ label: t.toUpperCase(), value: t }))} onChange={(v) => setAttributes({ titleTag: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
				</PanelBody>
			</InspectorControls>

			<InspectorControls group="styles">
				<PanelBody title={__('Layout', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{title !== '' && (
						<SelectControl
							label={__('Title Position', 'easy-elements-for-gutenberg')}
							value={titlePosition}
							options={[
								{ label: __('Top', 'easy-elements-for-gutenberg'), value: 'top' },
								{ label: __('Bottom', 'easy-elements-for-gutenberg'), value: 'bottom' },
								{ label: __('Left', 'easy-elements-for-gutenberg'), value: 'left' },
								{ label: __('Right', 'easy-elements-for-gutenberg'), value: 'right' },
							]}
							onChange={(v) => setAttributes({ titlePosition: v })}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					)}
					{num(__('Content Gap (px)', 'easy-elements-for-gutenberg'), 'contentGap')}
					{title !== '' && (titlePosition === 'left' || titlePosition === 'right') && (
						<SelectControl label={__('Content Vertical Align', 'easy-elements-for-gutenberg')} value={attributes.contentVerticalAlign} options={[{ label: __('Default', 'easy-elements-for-gutenberg'), value: '' }, ...ALIGN3]} onChange={(v) => setAttributes({ contentVerticalAlign: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					)}
					{num(__('Prefix/Suffix Gap (px)', 'easy-elements-for-gutenberg'), 'subPreGap')}
					<Divider />
					{iconEnable && (
						<SelectControl
							label={__('Icon Position', 'easy-elements-for-gutenberg')}
							value={attributes.iconPosition}
							options={[
								{ label: __('Left', 'easy-elements-for-gutenberg'), value: 'left' },
								{ label: __('Top', 'easy-elements-for-gutenberg'), value: 'top' },
								{ label: __('Bottom', 'easy-elements-for-gutenberg'), value: 'bottom' },
								{ label: __('Right', 'easy-elements-for-gutenberg'), value: 'right' },
							]}
							onChange={(v) => setAttributes({ iconPosition: v })}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					)}
					{iconEnable && num(__('Icon Gap (px)', 'easy-elements-for-gutenberg'), 'iconGap')}
					<SelectControl label={__('Box Align (align-items)', 'easy-elements-for-gutenberg')} value={attributes.wrapAlign} options={[{ label: __('Default', 'easy-elements-for-gutenberg'), value: '' }, { label: __('Start', 'easy-elements-for-gutenberg'), value: 'start' }, { label: __('Center', 'easy-elements-for-gutenberg'), value: 'center' }, { label: __('End', 'easy-elements-for-gutenberg'), value: 'end' }]} onChange={(v) => setAttributes({ wrapAlign: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					<SelectControl label={__('Box Justify (justify-content)', 'easy-elements-for-gutenberg')} value={attributes.wrapJustify} options={[{ label: __('Default', 'easy-elements-for-gutenberg'), value: '' }, { label: __('Start', 'easy-elements-for-gutenberg'), value: 'flex-start' }, { label: __('Center', 'easy-elements-for-gutenberg'), value: 'center' }, { label: __('End', 'easy-elements-for-gutenberg'), value: 'flex-end' }, { label: __('Space Between', 'easy-elements-for-gutenberg'), value: 'space-between' }]} onChange={(v) => setAttributes({ wrapJustify: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
				</PanelBody>

				<PanelBody title={__('Number', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Color', 'easy-elements-for-gutenberg'), 'numberColor')}
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'numberTypography')}
					{num(__('Stroke Width (px)', 'easy-elements-for-gutenberg'), 'numberStrokeWidth')}
					{color(__('Stroke Color', 'easy-elements-for-gutenberg'), 'numberStrokeColor')}
					{tshadow(__('Text Shadow', 'easy-elements-for-gutenberg'), 'numberTextShadow')}
				</PanelBody>

				{attributes.prefix !== '' && (
					<PanelBody title={__('Prefix', 'easy-elements-for-gutenberg')} initialOpen={false}>
						{color(__('Color', 'easy-elements-for-gutenberg'), 'prefixColor')}
						{typo(__('Typography', 'easy-elements-for-gutenberg'), 'prefixTypography')}
						{tshadow(__('Text Shadow', 'easy-elements-for-gutenberg'), 'prefixTextShadow')}
					</PanelBody>
				)}

				{attributes.suffix !== '' && (
					<PanelBody title={__('Suffix', 'easy-elements-for-gutenberg')} initialOpen={false}>
						{color(__('Color', 'easy-elements-for-gutenberg'), 'suffixColor')}
						{typo(__('Typography', 'easy-elements-for-gutenberg'), 'suffixTypography')}
						{tshadow(__('Text Shadow', 'easy-elements-for-gutenberg'), 'suffixTextShadow')}
					</PanelBody>
				)}

				{title !== '' && (
					<PanelBody title={__('Title', 'easy-elements-for-gutenberg')} initialOpen={false}>
						{color(__('Color', 'easy-elements-for-gutenberg'), 'titleColor')}
						{typo(__('Typography', 'easy-elements-for-gutenberg'), 'titleTypography')}
						<SelectControl label={__('Text Alignment', 'easy-elements-for-gutenberg')} value={attributes.titleAlign} options={[{ label: __('Default', 'easy-elements-for-gutenberg'), value: '' }, { label: __('Left', 'easy-elements-for-gutenberg'), value: 'left' }, { label: __('Center', 'easy-elements-for-gutenberg'), value: 'center' }, { label: __('Right', 'easy-elements-for-gutenberg'), value: 'right' }, { label: __('Justify', 'easy-elements-for-gutenberg'), value: 'justify' }]} onChange={(v) => setAttributes({ titleAlign: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
						{tshadow(__('Text Shadow', 'easy-elements-for-gutenberg'), 'titleTextShadow')}
					</PanelBody>
				)}

				{iconEnable && (
					<PanelBody title={__('Icon', 'easy-elements-for-gutenberg')} initialOpen={false}>
						{color(__('Color', 'easy-elements-for-gutenberg'), 'iconColor')}
						{color(__('Background', 'easy-elements-for-gutenberg'), 'iconBgColor')}
						{num(__('Box Size (px)', 'easy-elements-for-gutenberg'), 'iconBoxSize')}
						{num(__('Icon Size (px)', 'easy-elements-for-gutenberg'), 'iconSize')}
						{box(__('Border Radius', 'easy-elements-for-gutenberg'), 'iconBorderRadius')}
						{box(__('Padding', 'easy-elements-for-gutenberg'), 'iconPadding')}
						{border(__('Border', 'easy-elements-for-gutenberg'), 'iconBorder')}
						{shadow(__('Box Shadow', 'easy-elements-for-gutenberg'), 'iconBoxShadow')}
					</PanelBody>
				)}
			</InspectorControls>

			<ServerSideRender block="easy-elements-for-gutenberg/counter" attributes={attributes} httpMethod="POST" />
		</div>
	);
}
