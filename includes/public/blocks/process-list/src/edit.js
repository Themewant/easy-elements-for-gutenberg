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
	__experimentalDivider as Divider,
} from '@wordpress/components';

import ColorPopover from '../../custom-components/ColorPopover';
import IconPicker from '../../custom-components/IconPicker';
import TypographyControls from '../../custom-components/TypographyControls';
import BorderControl from '../../custom-components/BorderControl';
import BoxShadowControls from '../../custom-components/BoxShadowControls';
import BackgroundControl from '../../custom-components/BackgroundControl';

import './editor.scss';

export default function Edit({ attributes, setAttributes, clientId }) {
	const { blockId, icon, title, description, link, processNumber, titleTag, alignVertical } = attributes;

	useEffect(() => {
		if (!blockId) {
			setAttributes({ blockId: 'eelfg-pl-' + clientId.slice(0, 6) });
		}
	}, [blockId, clientId, setAttributes]);

	const color = (label, key) => <ColorPopover label={label} color={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const typo = (label, key) => <TypographyControls label={label} attributes={attributes} setAttributes={setAttributes} attributeKey={key} />;
	const box = (label, key) => <BoxControl label={label} values={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const num = (label, key) => <TextControl label={label} type="number" value={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} __next40pxDefaultSize __nextHasNoMarginBottom />;

	return (
		<div {...useBlockProps()}>
			<InspectorControls>
				<PanelBody title={__('Process Settings', 'easy-elements-for-gutenberg')} initialOpen={true}>
					<IconPicker label={__('Icon', 'easy-elements-for-gutenberg')} value={icon || ''} onChange={(v) => setAttributes({ icon: v })} />
					<TextControl
						label={__('Title', 'easy-elements-for-gutenberg')}
						value={title}
						onChange={(v) => setAttributes({ title: v })}
						help={__('Wrap part of the title in <span> to use the Highlight Title styles.', 'easy-elements-for-gutenberg')}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<TextareaControl label={__('Description', 'easy-elements-for-gutenberg')} value={description} onChange={(v) => setAttributes({ description: v })} __nextHasNoMarginBottom />
					<TextControl label={__('Process Number', 'easy-elements-for-gutenberg')} value={processNumber} onChange={(v) => setAttributes({ processNumber: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					<TextControl label={__('Link', 'easy-elements-for-gutenberg')} value={link} onChange={(v) => setAttributes({ link: v })} placeholder="https://example.com" __next40pxDefaultSize __nextHasNoMarginBottom />
					{link && (
						<>
							<ToggleControl label={__('Open in new tab', 'easy-elements-for-gutenberg')} checked={!!attributes.linkNewTab} onChange={(v) => setAttributes({ linkNewTab: v })} __nextHasNoMarginBottom />
							<ToggleControl label={__('Add nofollow', 'easy-elements-for-gutenberg')} checked={!!attributes.linkNofollow} onChange={(v) => setAttributes({ linkNofollow: v })} __nextHasNoMarginBottom />
						</>
					)}
					<Divider />
					<SelectControl
						label={__('Title HTML Tag', 'easy-elements-for-gutenberg')}
						value={titleTag}
						options={['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p'].map((t) => ({ label: t.toUpperCase(), value: t }))}
						onChange={(v) => setAttributes({ titleTag: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<SelectControl
						label={__('Vertical Align', 'easy-elements-for-gutenberg')}
						value={alignVertical}
						options={[
							{ label: __('Top', 'easy-elements-for-gutenberg'), value: 'flex-start' },
							{ label: __('Center', 'easy-elements-for-gutenberg'), value: 'center' },
							{ label: __('Bottom', 'easy-elements-for-gutenberg'), value: 'flex-end' },
						]}
						onChange={(v) => setAttributes({ alignVertical: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					{num(__('Gap (px)', 'easy-elements-for-gutenberg'), 'itemGap')}
				</PanelBody>
			</InspectorControls>

			<InspectorControls group="styles">
				<PanelBody title={__('Icon', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Color', 'easy-elements-for-gutenberg'), 'iconColor')}
					<BackgroundControl
						label={__('Background', 'easy-elements-for-gutenberg')}
						colorValue={attributes.iconBgColor}
						gradientValue={attributes.iconBgGradient}
						onColorChange={(v) => setAttributes({ iconBgColor: v && typeof v === 'object' ? v.hex : v || '' })}
						onGradientChange={(v) => setAttributes({ iconBgGradient: v || '' })}
					/>
					{num(__('Icon Size (px)', 'easy-elements-for-gutenberg'), 'iconSize')}
					{num(__('Box Size (px)', 'easy-elements-for-gutenberg'), 'iconBoxSize')}
					<BorderControl label={__('Border', 'easy-elements-for-gutenberg')} value={attributes.iconBorder} onChange={(v) => setAttributes({ iconBorder: v })} />
					{box(__('Border Radius', 'easy-elements-for-gutenberg'), 'iconRadius')}
					{box(__('Padding', 'easy-elements-for-gutenberg'), 'iconPadding')}
					<BoxShadowControls label={__('Box Shadow', 'easy-elements-for-gutenberg')} value={attributes.iconBoxShadow} onChange={(v) => setAttributes({ iconBoxShadow: v })} />
				</PanelBody>

				<PanelBody title={__('Title', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Color', 'easy-elements-for-gutenberg'), 'titleColor')}
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'titleTypography')}
					{box(__('Margin', 'easy-elements-for-gutenberg'), 'titleMargin')}
				</PanelBody>

				<PanelBody title={__('Highlight Title', 'easy-elements-for-gutenberg')} initialOpen={false}>
					<p className="eelfg-pl-note">{__('Highlight options work only when part of the title is wrapped in a <span> tag.', 'easy-elements-for-gutenberg')}</p>
					{color(__('Color', 'easy-elements-for-gutenberg'), 'titleSubColor')}
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'titleSubTypography')}
					{box(__('Margin', 'easy-elements-for-gutenberg'), 'titleSubMargin')}
				</PanelBody>

				<PanelBody title={__('Description', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Color', 'easy-elements-for-gutenberg'), 'descColor')}
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'descTypography')}
				</PanelBody>

				<PanelBody title={__('Number', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Color', 'easy-elements-for-gutenberg'), 'numberColor')}
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'numberTypography')}
				</PanelBody>
			</InspectorControls>

			<ServerSideRender block="easy-elements-for-gutenberg/process-list" attributes={attributes} httpMethod="POST" />
		</div>
	);
}
