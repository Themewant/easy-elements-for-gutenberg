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
const ICON_ARROW_UP = SVG('M12 6.6l-6 6 1.4 1.4 4.6-4.6 4.6 4.6 1.4-1.4z');
const ICON_ARROW_DOWN = SVG('M12 15.4l6-6-1.4-1.4-4.6 4.6-4.6-4.6-1.4 1.4z');
const ICON_TRASH = SVG('M9 3v1H4v2h16V4h-5V3H9zM6 7l1 13h10l1-13H6zm4 2h1v9h-1V9zm3 0h1v9h-1V9z');
const ICON_ADD = SVG('M11 5v6H5v2h6v6h2v-6h6v-2h-6V5z');

const ALIGN_OPTIONS = [
	{ label: __('Left', 'easy-elements-for-gutenberg'), value: 'left' },
	{ label: __('Center', 'easy-elements-for-gutenberg'), value: 'center' },
	{ label: __('Right', 'easy-elements-for-gutenberg'), value: 'right' },
];

export default function Edit({ attributes, setAttributes, clientId }) {
	const {
		blockId,
		skinStyle,
		title,
		description,
		onSale,
		regularPrice,
		salePrice,
		price,
		currency,
		currencyPlacement,
		period,
		separator,
		headerAlignment,
		featuresDescription,
		features,
		featureIconStyle,
		featureTextAlignment,
		isFeatured,
		ribbonStyle,
		featuredText,
		ribbonAlignment,
		showButton,
		buttonText,
		buttonSubtext,
		buttonUrl,
		buttonTarget,
		buttonNofollow,
		buttonIcon,
		buttonIconPosition,
		buttonPosition,
		btnAlignment,
		buttonFullWidth,
	} = attributes;

	useEffect(() => {
		if (!blockId) {
			setAttributes({ blockId: 'eelfg-pricing-' + clientId.slice(0, 6) });
		}
	}, [blockId, clientId, setAttributes]);

	const items = Array.isArray(features) ? features : [];

	const updateFeature = (index, key, value) => {
		setAttributes({ features: items.map((it, i) => (i === index ? { ...it, [key]: value } : it)) });
	};
	const addFeature = () => {
		setAttributes({ features: [...items, { icon: '', text: __('New feature', 'easy-elements-for-gutenberg') }] });
	};
	const removeFeature = (index) => {
		setAttributes({ features: items.filter((_, i) => i !== index) });
	};
	const moveFeature = (index, dir) => {
		const target = index + dir;
		if (target < 0 || target >= items.length) return;
		const next = items.slice();
		const [moved] = next.splice(index, 1);
		next.splice(target, 0, moved);
		setAttributes({ features: next });
	};

	const color = (label, key) => (
		<ColorPopover label={label} color={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />
	);
	const typo = (label, key) => (
		<TypographyControls label={label} attributes={attributes} setAttributes={setAttributes} attributeKey={key} />
	);
	const border = (label, key) => (
		<BorderControl label={label} value={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />
	);
	const shadow = (label, key) => (
		<BoxShadowControls label={label} value={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />
	);
	const box = (label, key) => (
		<BoxControl label={label} values={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />
	);
	const num = (label, key) => (
		<TextControl
			label={label}
			type="number"
			value={attributes[key]}
			onChange={(v) => setAttributes({ [key]: v })}
			__next40pxDefaultSize
			__nextHasNoMarginBottom
		/>
	);

	return (
		<div {...useBlockProps()}>
			<InspectorControls>
				<PanelBody title={__('Layout', 'easy-elements-for-gutenberg')} initialOpen={true}>
					<SelectControl
						label={__('Skin', 'easy-elements-for-gutenberg')}
						value={skinStyle}
						options={[
							{ label: __('Skin 01', 'easy-elements-for-gutenberg'), value: 'skin1' },
						]}
						onChange={(v) => setAttributes({ skinStyle: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</PanelBody>

				<PanelBody title={__('Title & Price', 'easy-elements-for-gutenberg')} initialOpen={false}>
					<TextControl label={__('Title', 'easy-elements-for-gutenberg')} value={title} onChange={(v) => setAttributes({ title: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					<TextControl label={__('Description', 'easy-elements-for-gutenberg')} value={description} onChange={(v) => setAttributes({ description: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					<Divider />
					<ToggleControl label={__('On Sale?', 'easy-elements-for-gutenberg')} checked={onSale} onChange={(v) => setAttributes({ onSale: v })} __nextHasNoMarginBottom />
					{onSale ? (
						<>
							<TextControl label={__('Regular Price', 'easy-elements-for-gutenberg')} value={regularPrice} onChange={(v) => setAttributes({ regularPrice: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
							<TextControl label={__('Sale Price', 'easy-elements-for-gutenberg')} value={salePrice} onChange={(v) => setAttributes({ salePrice: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
						</>
					) : (
						<TextControl label={__('Price', 'easy-elements-for-gutenberg')} value={price} onChange={(v) => setAttributes({ price: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					)}
					<Divider />
					<TextControl label={__('Currency', 'easy-elements-for-gutenberg')} value={currency} onChange={(v) => setAttributes({ currency: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					<SelectControl
						label={__('Currency Position', 'easy-elements-for-gutenberg')}
						value={currencyPlacement}
						options={[
							{ label: __('Left', 'easy-elements-for-gutenberg'), value: 'left' },
							{ label: __('Right', 'easy-elements-for-gutenberg'), value: 'right' },
						]}
						onChange={(v) => setAttributes({ currencyPlacement: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<TextControl label={__('Period', 'easy-elements-for-gutenberg')} value={period} onChange={(v) => setAttributes({ period: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					<TextControl label={__('Separator', 'easy-elements-for-gutenberg')} value={separator} onChange={(v) => setAttributes({ separator: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					<SelectControl label={__('Alignment', 'easy-elements-for-gutenberg')} value={headerAlignment} options={ALIGN_OPTIONS} onChange={(v) => setAttributes({ headerAlignment: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
				</PanelBody>

				<PanelBody title={__('Features', 'easy-elements-for-gutenberg')} initialOpen={false}>
					<TextareaControl
						label={__('Features Description (top)', 'easy-elements-for-gutenberg')}
						value={featuresDescription}
						onChange={(v) => setAttributes({ featuresDescription: v })}
						__nextHasNoMarginBottom
					/>
					<Divider />
					{items.map((item, index) => (
						<div className="eelfg-pricing-repeater-item" key={index}>
							<div className="eelfg-pricing-repeater-head">
								<strong>#{index + 1}</strong>
								<div>
									<Button icon={ICON_ARROW_UP} label={__('Move up', 'easy-elements-for-gutenberg')} onClick={() => moveFeature(index, -1)} disabled={index === 0} size="small" />
									<Button icon={ICON_ARROW_DOWN} label={__('Move down', 'easy-elements-for-gutenberg')} onClick={() => moveFeature(index, 1)} disabled={index === items.length - 1} size="small" />
									<Button icon={ICON_TRASH} label={__('Remove', 'easy-elements-for-gutenberg')} onClick={() => removeFeature(index)} isDestructive size="small" />
								</div>
							</div>
							<TextControl
								label={__('Text', 'easy-elements-for-gutenberg')}
								value={item.text || ''}
								onChange={(v) => updateFeature(index, 'text', v)}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
							<IconPicker
								label={__('Icon', 'easy-elements-for-gutenberg')}
								value={item.icon || ''}
								onChange={(v) => updateFeature(index, 'icon', v)}
							/>
						</div>
					))}
					<Button variant="primary" onClick={addFeature} icon={ICON_ADD}>
						{__('Add Feature', 'easy-elements-for-gutenberg')}
					</Button>
					<Divider />
					<SelectControl
						label={__('Icon Style', 'easy-elements-for-gutenberg')}
						value={featureIconStyle}
						options={[
							{ label: __('Icon Only', 'easy-elements-for-gutenberg'), value: 'icon-only' },
							{ label: __('Icon with Background', 'easy-elements-for-gutenberg'), value: 'icon-bg' },
							{ label: __('Icon with Border', 'easy-elements-for-gutenberg'), value: 'icon-border' },
						]}
						onChange={(v) => setAttributes({ featureIconStyle: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<SelectControl label={__('Alignment', 'easy-elements-for-gutenberg')} value={featureTextAlignment} options={ALIGN_OPTIONS} onChange={(v) => setAttributes({ featureTextAlignment: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
				</PanelBody>

				<PanelBody title={__('Featured', 'easy-elements-for-gutenberg')} initialOpen={false}>
					<ToggleControl label={__('Featured', 'easy-elements-for-gutenberg')} checked={isFeatured} onChange={(v) => setAttributes({ isFeatured: v })} __nextHasNoMarginBottom />
					{isFeatured && (
						<>
							<SelectControl
								label={__('Ribbon Style', 'easy-elements-for-gutenberg')}
								value={ribbonStyle}
								options={[
									{ label: __('Style 1', 'easy-elements-for-gutenberg'), value: 'style1' },
									{ label: __('Style 2', 'easy-elements-for-gutenberg'), value: 'style2' },
								]}
								onChange={(v) => setAttributes({ ribbonStyle: v })}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
							<TextControl label={__('Featured Text', 'easy-elements-for-gutenberg')} value={featuredText} onChange={(v) => setAttributes({ featuredText: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
							{ribbonStyle === 'style2' && (
								<SelectControl
									label={__('Alignment', 'easy-elements-for-gutenberg')}
									value={ribbonAlignment}
									options={[
										{ label: __('Left', 'easy-elements-for-gutenberg'), value: 'left' },
										{ label: __('Right', 'easy-elements-for-gutenberg'), value: 'right' },
									]}
									onChange={(v) => setAttributes({ ribbonAlignment: v })}
									__next40pxDefaultSize
									__nextHasNoMarginBottom
								/>
							)}
						</>
					)}
				</PanelBody>

				<PanelBody title={__('Button', 'easy-elements-for-gutenberg')} initialOpen={false}>
					<ToggleControl label={__('Show Button', 'easy-elements-for-gutenberg')} checked={showButton} onChange={(v) => setAttributes({ showButton: v })} __nextHasNoMarginBottom />
					{showButton && (
						<>
							<TextControl label={__('Text', 'easy-elements-for-gutenberg')} value={buttonText} onChange={(v) => setAttributes({ buttonText: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
							<TextControl label={__('Bottom Text', 'easy-elements-for-gutenberg')} value={buttonSubtext} onChange={(v) => setAttributes({ buttonSubtext: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
							<TextControl label={__('Link URL', 'easy-elements-for-gutenberg')} type="url" value={buttonUrl} onChange={(v) => setAttributes({ buttonUrl: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
							<ToggleControl label={__('Open in new tab', 'easy-elements-for-gutenberg')} checked={buttonTarget} onChange={(v) => setAttributes({ buttonTarget: v })} __nextHasNoMarginBottom />
							<ToggleControl label={__('Add nofollow', 'easy-elements-for-gutenberg')} checked={buttonNofollow} onChange={(v) => setAttributes({ buttonNofollow: v })} __nextHasNoMarginBottom />
							<Divider />
							<IconPicker label={__('Icon', 'easy-elements-for-gutenberg')} value={buttonIcon} onChange={(v) => setAttributes({ buttonIcon: v })} />
							<SelectControl
								label={__('Icon Position', 'easy-elements-for-gutenberg')}
								value={buttonIconPosition}
								options={[
									{ label: __('Before', 'easy-elements-for-gutenberg'), value: 'before' },
									{ label: __('After', 'easy-elements-for-gutenberg'), value: 'after' },
								]}
								onChange={(v) => setAttributes({ buttonIconPosition: v })}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
							<SelectControl
								label={__('Button Position', 'easy-elements-for-gutenberg')}
								value={buttonPosition}
								options={[
									{ label: __('After Features', 'easy-elements-for-gutenberg'), value: 'after_features' },
									{ label: __('Before Features', 'easy-elements-for-gutenberg'), value: 'in_features' },
								]}
								onChange={(v) => setAttributes({ buttonPosition: v })}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
							<SelectControl label={__('Alignment', 'easy-elements-for-gutenberg')} value={btnAlignment} options={ALIGN_OPTIONS} onChange={(v) => setAttributes({ btnAlignment: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
							<ToggleControl label={__('Full Width Button', 'easy-elements-for-gutenberg')} checked={buttonFullWidth} onChange={(v) => setAttributes({ buttonFullWidth: v })} __nextHasNoMarginBottom />
						</>
					)}
				</PanelBody>
			</InspectorControls>

			<InspectorControls group="styles">
				<PanelBody title={__('Title', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Color', 'easy-elements-for-gutenberg'), 'titleColor')}
					{color(__('Highlight Color (span)', 'easy-elements-for-gutenberg'), 'titleHighlightColor')}
					{color(__('Background', 'easy-elements-for-gutenberg'), 'titleBgColor')}
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'titleTypography')}
					<Divider />
					{border(__('Border', 'easy-elements-for-gutenberg'), 'titleBorder')}
					<Divider />
					{box(__('Border Radius', 'easy-elements-for-gutenberg'), 'titleBorderRadius')}
					<Divider />
					{box(__('Padding', 'easy-elements-for-gutenberg'), 'titlePadding')}
					<Divider />
					{box(__('Margin', 'easy-elements-for-gutenberg'), 'titleMargin')}
				</PanelBody>

				<PanelBody title={__('Description', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Color', 'easy-elements-for-gutenberg'), 'descriptionColor')}
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'descriptionTypography')}
					<Divider />
					{border(__('Border', 'easy-elements-for-gutenberg'), 'descriptionBorder')}
					<Divider />
					{box(__('Padding', 'easy-elements-for-gutenberg'), 'descriptionPadding')}
					<Divider />
					{box(__('Margin', 'easy-elements-for-gutenberg'), 'descriptionMargin')}
				</PanelBody>

				<PanelBody title={__('Price', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Price Color', 'easy-elements-for-gutenberg'), 'priceColor')}
					{typo(__('Price Typography', 'easy-elements-for-gutenberg'), 'priceTypography')}
					<Divider />
					{box(__('Price Margin', 'easy-elements-for-gutenberg'), 'priceMargin')}
					{onSale && (
						<>
							<Divider />
							{color(__('Sale Price Color', 'easy-elements-for-gutenberg'), 'salePriceColor')}
							{typo(__('Sale Price Typography', 'easy-elements-for-gutenberg'), 'salePriceTypography')}
							{color(__('Regular (old) Price Color', 'easy-elements-for-gutenberg'), 'oldPriceColor')}
						</>
					)}
				</PanelBody>

				<PanelBody title={__('Period & Currency', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Period Color', 'easy-elements-for-gutenberg'), 'periodColor')}
					{typo(__('Period Typography', 'easy-elements-for-gutenberg'), 'periodTypography')}
					{box(__('Period Margin', 'easy-elements-for-gutenberg'), 'periodMargin')}
					<Divider />
					{color(__('Currency Color', 'easy-elements-for-gutenberg'), 'currencyColor')}
					{typo(__('Currency Typography', 'easy-elements-for-gutenberg'), 'currencyTypography')}
					{box(__('Currency Margin', 'easy-elements-for-gutenberg'), 'currencyMargin')}
					{num(__('Currency Vertical Position (px)', 'easy-elements-for-gutenberg'), 'currencyVerticalPosition')}
				</PanelBody>

				<PanelBody title={__('Features Description', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Color', 'easy-elements-for-gutenberg'), 'featuresDescriptionColor')}
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'featuresDescriptionTypography')}
					<Divider />
					{border(__('Border', 'easy-elements-for-gutenberg'), 'featuresDescriptionBorder')}
					<Divider />
					{box(__('Padding', 'easy-elements-for-gutenberg'), 'featuresDescriptionPadding')}
					<Divider />
					{box(__('Margin', 'easy-elements-for-gutenberg'), 'featuresDescriptionMargin')}
				</PanelBody>

				<PanelBody title={__('Features', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Text Color', 'easy-elements-for-gutenberg'), 'featuresTextColor')}
					{color(__('Icon Color', 'easy-elements-for-gutenberg'), 'featuresIconColor')}
					{typo(__('Text Typography', 'easy-elements-for-gutenberg'), 'featuresTextTypography')}
					<Divider />
					{border(__('Item Border', 'easy-elements-for-gutenberg'), 'featuresBorder')}
					<Divider />
					{box(__('Item Padding', 'easy-elements-for-gutenberg'), 'featuresPadding')}
					<Divider />
					{box(__('Item Margin', 'easy-elements-for-gutenberg'), 'featuresMargin')}
					<Divider />
					{num(__('Icon Gap (px)', 'easy-elements-for-gutenberg'), 'featuresIconGap')}
				</PanelBody>

				<PanelBody title={__('Feature Icon', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{num(__('Icon Size (px)', 'easy-elements-for-gutenberg'), 'featureIconSize')}
					{featureIconStyle === 'icon-bg' && color(__('Background', 'easy-elements-for-gutenberg'), 'featureIconBgColor')}
					{featureIconStyle === 'icon-border' && border(__('Border', 'easy-elements-for-gutenberg'), 'featureIconBorder')}
					{(featureIconStyle === 'icon-bg' || featureIconStyle === 'icon-border') && (
						<>
							<Divider />
							{box(__('Icon Padding', 'easy-elements-for-gutenberg'), 'featureIconPadding')}
							<Divider />
							{box(__('Icon Border Radius', 'easy-elements-for-gutenberg'), 'featureIconBorderRadius')}
						</>
					)}
				</PanelBody>

				{isFeatured && (
					<PanelBody title={__('Ribbon', 'easy-elements-for-gutenberg')} initialOpen={false}>
						{color(__('Text Color', 'easy-elements-for-gutenberg'), 'ribbonColor')}
						{color(__('Background Color', 'easy-elements-for-gutenberg'), 'ribbonBgColor')}
						{typo(__('Typography', 'easy-elements-for-gutenberg'), 'ribbonTypography')}
						{ribbonStyle === 'style1' && (
							<>
								<Divider />
								{box(__('Padding', 'easy-elements-for-gutenberg'), 'ribbonPadding')}
								<Divider />
								{box(__('Border Radius', 'easy-elements-for-gutenberg'), 'ribbonBorderRadius')}
							</>
						)}
					</PanelBody>
				)}

				<PanelBody title={__('Button', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Text Color', 'easy-elements-for-gutenberg'), 'buttonTextColor')}
					{color(__('Icon Color', 'easy-elements-for-gutenberg'), 'buttonIconColor')}
					{color(__('Background', 'easy-elements-for-gutenberg'), 'buttonBgColor')}
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'buttonTypography')}
					<Divider />
					{border(__('Border', 'easy-elements-for-gutenberg'), 'buttonBorder')}
					{shadow(__('Box Shadow', 'easy-elements-for-gutenberg'), 'buttonBoxShadow')}
					<Divider />
					{color(__('Text Color (Hover)', 'easy-elements-for-gutenberg'), 'buttonTextColorHover')}
					{color(__('Background (Hover)', 'easy-elements-for-gutenberg'), 'buttonBgColorHover')}
					{border(__('Border (Hover)', 'easy-elements-for-gutenberg'), 'buttonBorderHover')}
					{shadow(__('Box Shadow (Hover)', 'easy-elements-for-gutenberg'), 'buttonBoxShadowHover')}
					<Divider />
					{box(__('Border Radius', 'easy-elements-for-gutenberg'), 'buttonBorderRadius')}
					<Divider />
					{box(__('Padding', 'easy-elements-for-gutenberg'), 'buttonPadding')}
					<Divider />
					{box(__('Margin', 'easy-elements-for-gutenberg'), 'buttonMargin')}
					<Divider />
					{num(__('Icon Spacing (px)', 'easy-elements-for-gutenberg'), 'buttonIconSpacing')}
				</PanelBody>

				<PanelBody title={__('Button Subtext', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Color', 'easy-elements-for-gutenberg'), 'buttonSubtextColor')}
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'buttonSubtextTypography')}
					<Divider />
					{box(__('Margin', 'easy-elements-for-gutenberg'), 'buttonSubtextMargin')}
				</PanelBody>
			</InspectorControls>

			<ServerSideRender block="easy-elements-for-gutenberg/pricing-table" attributes={attributes} httpMethod="POST" />
		</div>
	);
}
