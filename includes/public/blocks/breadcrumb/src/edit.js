import { __ } from '@wordpress/i18n';
import { useEffect } from '@wordpress/element';
import { ServerSideRender } from '@wordpress/server-side-render';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	ToggleControl,
	TextControl,
	BoxControl,
	__experimentalDivider as Divider,
} from '@wordpress/components';

import ColorPopover from '../../custom-components/ColorPopover';
import IconPicker from '../../custom-components/IconPicker';
import TypographyControls from '../../custom-components/TypographyControls';

import './editor.scss';

export default function Edit({ attributes, setAttributes, clientId }) {
	const { blockId, showHomeIcon, showSeparatorIcon } = attributes;

	useEffect(() => {
		if (!blockId) {
			setAttributes({ blockId: 'eelfg-bc-' + clientId.slice(0, 6) });
		}
	}, [blockId, clientId, setAttributes]);

	const color = (label, key) => <ColorPopover label={label} color={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const typo = (label, key) => <TypographyControls label={label} attributes={attributes} setAttributes={setAttributes} attributeKey={key} />;
	const box = (label, key) => <BoxControl label={label} values={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const num = (label, key) => <TextControl label={label} type="number" value={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} __next40pxDefaultSize __nextHasNoMarginBottom />;

	return (
		<div {...useBlockProps()}>
			<InspectorControls>
				<PanelBody title={__('Content Settings', 'easy-elements-for-gutenberg')} initialOpen={true}>
					<ToggleControl label={__('Show Home Icon', 'easy-elements-for-gutenberg')} checked={showHomeIcon} onChange={(v) => setAttributes({ showHomeIcon: v })} __nextHasNoMarginBottom />
					{showHomeIcon && <IconPicker label={__('Home Icon', 'easy-elements-for-gutenberg')} value={attributes.homeIcon || ''} onChange={(v) => setAttributes({ homeIcon: v })} />}
					<TextControl label={__('Home Page Title', 'easy-elements-for-gutenberg')} value={attributes.homeTitle} onChange={(v) => setAttributes({ homeTitle: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					<ToggleControl label={__('Show Category Path', 'easy-elements-for-gutenberg')} checked={attributes.showCategoryPath} onChange={(v) => setAttributes({ showCategoryPath: v })} __nextHasNoMarginBottom />
					<ToggleControl label={__('Show Separator Icon', 'easy-elements-for-gutenberg')} checked={showSeparatorIcon} onChange={(v) => setAttributes({ showSeparatorIcon: v })} __nextHasNoMarginBottom />
					{showSeparatorIcon && <IconPicker label={__('Separator Icon', 'easy-elements-for-gutenberg')} value={attributes.separatorIcon || ''} onChange={(v) => setAttributes({ separatorIcon: v })} />}
				</PanelBody>
			</InspectorControls>

			<InspectorControls group="styles">
				<PanelBody title={__('Breadcrumb', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Text Color', 'easy-elements-for-gutenberg'), 'textColor')}
					{color(__('Active Color', 'easy-elements-for-gutenberg'), 'activeColor')}
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'textTypography')}
					<Divider />
					{color(__('Background (Normal)', 'easy-elements-for-gutenberg'), 'textBgColor')}
					{box(__('Padding (Normal)', 'easy-elements-for-gutenberg'), 'textPadding')}
					{box(__('Border Radius (Normal)', 'easy-elements-for-gutenberg'), 'textRadius')}
					<Divider />
					{color(__('Background (Active)', 'easy-elements-for-gutenberg'), 'textBgColorActive')}
					{box(__('Padding (Active)', 'easy-elements-for-gutenberg'), 'textPaddingActive')}
					{box(__('Border Radius (Active)', 'easy-elements-for-gutenberg'), 'textRadiusActive')}
				</PanelBody>

				{showHomeIcon && (
					<PanelBody title={__('Home Icon', 'easy-elements-for-gutenberg')} initialOpen={false}>
						{color(__('Color', 'easy-elements-for-gutenberg'), 'homeIconColor')}
						{color(__('Background Color', 'easy-elements-for-gutenberg'), 'homeIconBg')}
						{num(__('Size (px)', 'easy-elements-for-gutenberg'), 'homeIconSize')}
						{box(__('Border Radius', 'easy-elements-for-gutenberg'), 'homeIconRadius')}
						{box(__('Padding', 'easy-elements-for-gutenberg'), 'homeIconPadding')}
						{num(__('Vertical Position (px)', 'easy-elements-for-gutenberg'), 'homeIconPosY')}
						{num(__('Horizontal Position (px)', 'easy-elements-for-gutenberg'), 'homeIconPosX')}
					</PanelBody>
				)}

				<PanelBody title={__('Separator', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Color', 'easy-elements-for-gutenberg'), 'separatorColor')}
					{color(__('Background Color', 'easy-elements-for-gutenberg'), 'separatorBg')}
					{num(__('Size (px)', 'easy-elements-for-gutenberg'), 'separatorSize')}
					{box(__('Border Radius', 'easy-elements-for-gutenberg'), 'separatorRadius')}
					{box(__('Padding', 'easy-elements-for-gutenberg'), 'separatorPadding')}
					{box(__('Gap (Margin)', 'easy-elements-for-gutenberg'), 'separatorGap')}
					{num(__('Vertical Position (px)', 'easy-elements-for-gutenberg'), 'separatorPosY')}
					{num(__('Horizontal Position (px)', 'easy-elements-for-gutenberg'), 'separatorPosX')}
				</PanelBody>
			</InspectorControls>

			<ServerSideRender block="easy-elements-for-gutenberg/breadcrumb" attributes={attributes} httpMethod="POST" />
		</div>
	);
}
