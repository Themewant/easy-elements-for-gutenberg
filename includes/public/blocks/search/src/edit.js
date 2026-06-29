import { __ } from '@wordpress/i18n';
import { useEffect, useRef } from '@wordpress/element';
import { ServerSideRender } from '@wordpress/server-side-render';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	TextControl,
	BoxControl,
	__experimentalDivider as Divider,
} from '@wordpress/components';

import ColorPopover from '../../custom-components/ColorPopover';
import IconPicker from '../../custom-components/IconPicker';
import TypographyControls from '../../custom-components/TypographyControls';

import './editor.scss';

export default function Edit({ attributes, setAttributes, clientId }) {
	const { blockId, selectStyle } = attributes;

	useEffect(() => {
		if (!blockId) {
			setAttributes({ blockId: 'eelfg-search-' + clientId.slice(0, 6) });
		}
	}, [blockId, clientId, setAttributes]);

	// Editor preview: open/close the popup lightbox (front-end view.js doesn't run
	// inside ServerSideRender). Delegated on a persistent wrapper.
	const previewRef = useRef(null);
	useEffect(() => {
		const root = previewRef.current;
		if (!root) return undefined;
		const onClick = (e) => {
			const open = e.target.closest('.eelfg-search-open-btn');
			const close = e.target.closest('.eelfg-search-close-btn, .eelfg-search-overlay');
			const box = root.querySelector('.eelfg-search-lightbox');
			if (!box) return;
			if (open && root.contains(open)) {
				e.preventDefault();
				box.classList.add('eelfg-lightbox');
			} else if (close && root.contains(close)) {
				e.preventDefault();
				box.classList.remove('eelfg-lightbox');
			}
		};
		root.addEventListener('click', onClick);
		return () => root.removeEventListener('click', onClick);
	}, []);

	const isPopup = selectStyle === '1';
	const isFields = selectStyle === '2';

	const color = (label, key) => <ColorPopover label={label} color={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const typo = (label, key) => <TypographyControls label={label} attributes={attributes} setAttributes={setAttributes} attributeKey={key} />;
	const box = (label, key) => <BoxControl label={label} values={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const num = (label, key) => <TextControl label={label} type="number" value={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} __next40pxDefaultSize __nextHasNoMarginBottom />;

	return (
		<div {...useBlockProps()}>
			<InspectorControls>
				<PanelBody title={__('Search Settings', 'easy-elements-for-gutenberg')} initialOpen={true}>
					<SelectControl
						label={__('Search Skin', 'easy-elements-for-gutenberg')}
						value={selectStyle}
						options={[
							{ label: __('Search Popup', 'easy-elements-for-gutenberg'), value: '1' },
							{ label: __('Search Fields', 'easy-elements-for-gutenberg'), value: '2' },
						]}
						onChange={(v) => setAttributes({ selectStyle: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					{isPopup && <TextControl label={__('Search Title', 'easy-elements-for-gutenberg')} value={attributes.searchTitle} onChange={(v) => setAttributes({ searchTitle: v })} __next40pxDefaultSize __nextHasNoMarginBottom />}
					<TextControl label={__('Search Placeholder', 'easy-elements-for-gutenberg')} value={attributes.placeholder} onChange={(v) => setAttributes({ placeholder: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					<IconPicker label={__('Search Icon', 'easy-elements-for-gutenberg')} value={attributes.openIcon || ''} onChange={(v) => setAttributes({ openIcon: v })} />
					{isPopup && <IconPicker label={__('Close Icon', 'easy-elements-for-gutenberg')} value={attributes.closeIcon || ''} onChange={(v) => setAttributes({ closeIcon: v })} />}
				</PanelBody>
			</InspectorControls>

			<InspectorControls group="styles">
				<PanelBody title={__('Search Icon', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{num(__('Icon Size (px)', 'easy-elements-for-gutenberg'), 'iconSize')}
					{color(__('Icon Color', 'easy-elements-for-gutenberg'), 'iconColor')}
					{isPopup && num(__('Icon Vertical Position (px)', 'easy-elements-for-gutenberg'), 'iconVerticalPosition')}
					{isFields && (
						<>
							<SelectControl
								label={__('Icon Position', 'easy-elements-for-gutenberg')}
								value={attributes.iconPositionSide}
								options={[
									{ label: __('Left', 'easy-elements-for-gutenberg'), value: 'left' },
									{ label: __('Right', 'easy-elements-for-gutenberg'), value: 'right' },
								]}
								onChange={(v) => setAttributes({ iconPositionSide: v })}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
							{attributes.iconPositionSide === 'right' && num(__('Offset From Right (px)', 'easy-elements-for-gutenberg'), 'iconOffsetRight')}
							{attributes.iconPositionSide === 'left' && num(__('Offset From Left (px)', 'easy-elements-for-gutenberg'), 'iconOffsetLeft')}
						</>
					)}
				</PanelBody>

				<PanelBody title={__('Input Field', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'inputTypography')}
					{color(__('Input Text Color', 'easy-elements-for-gutenberg'), 'inputTextColor')}
					{color(__('Placeholder Color', 'easy-elements-for-gutenberg'), 'placeholderColor')}
					{color(__('Input Background Color', 'easy-elements-for-gutenberg'), 'inputBgColor')}
					{box(__('Padding', 'easy-elements-for-gutenberg'), 'inputPadding')}
					{num(__('Height (px)', 'easy-elements-for-gutenberg'), 'inputHeight')}
					{isFields && num(__('Input Field Width (px)', 'easy-elements-for-gutenberg'), 'inputFieldWidth')}
					{isFields && box(__('Border Radius', 'easy-elements-for-gutenberg'), 'inputBorderRadius')}
					{isFields && color(__('Input Border Color', 'easy-elements-for-gutenberg'), 'inputBorderColor')}
					{isFields && color(__('Border Color (Focus)', 'easy-elements-for-gutenberg'), 'inputFocusBorderColor')}
				</PanelBody>

				<PanelBody title={__('Submit Button', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{isPopup && color(__('Submit Icon Color', 'easy-elements-for-gutenberg'), 'submitIconColor')}
					{isPopup && color(__('Submit Icon Hover Color', 'easy-elements-for-gutenberg'), 'submitIconHoverColor')}
					{color(__('Submit Icon Background', 'easy-elements-for-gutenberg'), 'submitBtnBg')}
					{isPopup && color(__('Submit Icon Hover Background', 'easy-elements-for-gutenberg'), 'submitBtnHoverBg')}
					{box(__('Padding', 'easy-elements-for-gutenberg'), 'submitPadding')}
				</PanelBody>

				{isPopup && (
					<PanelBody title={__('Search Popup', 'easy-elements-for-gutenberg')} initialOpen={false}>
						{color(__('Overlay Background', 'easy-elements-for-gutenberg'), 'overlayBg')}
						<Divider />
						{color(__('Title Color', 'easy-elements-for-gutenberg'), 'popupTitleColor')}
						{typo(__('Title Typography', 'easy-elements-for-gutenberg'), 'popupTitleTypography')}
						<Divider />
						{color(__('Close Icon Color', 'easy-elements-for-gutenberg'), 'closeIconColor')}
						{num(__('Close Icon Size (px)', 'easy-elements-for-gutenberg'), 'closeIconSize')}
					</PanelBody>
				)}
			</InspectorControls>

			<div ref={previewRef}>
				<ServerSideRender block="easy-elements-for-gutenberg/search" attributes={attributes} httpMethod="POST" />
			</div>
		</div>
	);
}
