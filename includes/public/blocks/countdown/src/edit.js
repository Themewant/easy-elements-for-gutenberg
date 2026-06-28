import { __ } from '@wordpress/i18n';
import { useEffect, useRef } from '@wordpress/element';
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
import TypographyControls from '../../custom-components/TypographyControls';
import BorderControl from '../../custom-components/BorderControl';
import BoxShadowControls from '../../custom-components/BoxShadowControls';

import './editor.scss';

export default function Edit({ attributes, setAttributes, clientId }) {
	const {
		blockId,
		dayLabel,
		hoursLabel,
		minuteLabel,
		secondsLabel,
		targetDate,
		separator,
		labelUnderNumber,
		contentAlign,
	} = attributes;

	useEffect(() => {
		if (!blockId) {
			setAttributes({ blockId: 'eelfg-cntdwn-' + clientId.slice(0, 6) });
		}
	}, [blockId, clientId, setAttributes]);

	// Editor preview ticking: the front-end view.js doesn't run inside the
	// ServerSideRender output, so drive the countdown here. The interval reads
	// the target + elements fresh from the DOM each tick, so it survives the
	// SSR markup being replaced on every re-render.
	const previewRef = useRef(null);
	useEffect(() => {
		const tick = () => {
			const root = previewRef.current;
			if (!root) return;
			const cd = root.querySelector('.eelfg-cntdwn[data-target]');
			if (!cd) return;
			const target = new Date(cd.dataset.target).getTime();
			if (isNaN(target)) return;
			const distance = Math.max(0, target - Date.now());
			const sec = 1000, min = sec * 60, hr = min * 60, day = hr * 24;
			const set = (sel, val) => {
				const el = cd.querySelector(sel);
				if (el) el.textContent = val;
			};
			set('.eelfg-cntdwn-days', Math.floor(distance / day));
			set('.eelfg-cntdwn-hours', Math.floor((distance % day) / hr));
			set('.eelfg-cntdwn-minutes', Math.floor((distance % hr) / min));
			set('.eelfg-cntdwn-seconds', Math.floor((distance % min) / sec));
		};
		tick();
		const id = setInterval(tick, 1000);
		return () => clearInterval(id);
	}, []);

	const color = (label, key) => (
		<ColorPopover label={label} color={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />
	);
	const typo = (label, key) => (
		<TypographyControls label={label} attributes={attributes} setAttributes={setAttributes} attributeKey={key} />
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

	const isSeparator = separator === 'eelfg-cntdwn-bullets' || separator === 'eelfg-cntdwn-dash';

	const unitSection = (titleLabel, typoKey, colorKey, labelColorKey, labelTypoKey) => (
		<PanelBody title={titleLabel} initialOpen={false}>
			{typo(__('Number Typography', 'easy-elements-for-gutenberg'), typoKey)}
			{color(__('Number Color', 'easy-elements-for-gutenberg'), colorKey)}
			<Divider />
			{color(__('Label Color', 'easy-elements-for-gutenberg'), labelColorKey)}
			{typo(__('Label Typography', 'easy-elements-for-gutenberg'), labelTypoKey)}
		</PanelBody>
	);

	return (
		<div {...useBlockProps()}>
			<InspectorControls>
				<PanelBody title={__('Countdown', 'easy-elements-for-gutenberg')} initialOpen={true}>
					<TextControl
						label={__('Target Date', 'easy-elements-for-gutenberg')}
						type="datetime-local"
						value={targetDate}
						onChange={(v) => setAttributes({ targetDate: v })}
						help={__('Leave empty to default to 24 hours from now.', 'easy-elements-for-gutenberg')}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<Divider />
					<TextControl label={__('Days Label', 'easy-elements-for-gutenberg')} value={dayLabel} onChange={(v) => setAttributes({ dayLabel: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					<TextControl label={__('Hours Label', 'easy-elements-for-gutenberg')} value={hoursLabel} onChange={(v) => setAttributes({ hoursLabel: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					<TextControl label={__('Minutes Label', 'easy-elements-for-gutenberg')} value={minuteLabel} onChange={(v) => setAttributes({ minuteLabel: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					<TextControl label={__('Seconds Label', 'easy-elements-for-gutenberg')} value={secondsLabel} onChange={(v) => setAttributes({ secondsLabel: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					<Divider />
					<SelectControl
						label={__('Separator', 'easy-elements-for-gutenberg')}
						value={separator}
						options={[
							{ label: __('Space', 'easy-elements-for-gutenberg'), value: 'eelfg-cntdwn-space' },
							{ label: __('Bullets', 'easy-elements-for-gutenberg'), value: 'eelfg-cntdwn-bullets' },
							{ label: __('Dash', 'easy-elements-for-gutenberg'), value: 'eelfg-cntdwn-dash' },
						]}
						onChange={(v) => setAttributes({ separator: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<ToggleControl
						label={__('Display Label Under Number', 'easy-elements-for-gutenberg')}
						checked={labelUnderNumber}
						onChange={(v) => setAttributes({ labelUnderNumber: v })}
						__nextHasNoMarginBottom
					/>
					{labelUnderNumber && (
						<SelectControl
							label={__('Content Alignment', 'easy-elements-for-gutenberg')}
							value={contentAlign}
							options={[
								{ label: __('Left', 'easy-elements-for-gutenberg'), value: 'left' },
								{ label: __('Center', 'easy-elements-for-gutenberg'), value: 'center' },
								{ label: __('Right', 'easy-elements-for-gutenberg'), value: 'right' },
							]}
							onChange={(v) => setAttributes({ contentAlign: v })}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					)}
				</PanelBody>
			</InspectorControls>

			<InspectorControls group="styles">
				<PanelBody title={__('Countdown', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{num(__('Mid Gap (px)', 'easy-elements-for-gutenberg'), 'midGap')}
					{isSeparator && num(__('Separator Position X (px)', 'easy-elements-for-gutenberg'), 'separatorPositionX')}
					{isSeparator && color(__('Separator Color', 'easy-elements-for-gutenberg'), 'separatorColor')}
					<Divider />
					{color(__('Item Background', 'easy-elements-for-gutenberg'), 'itemBgColor')}
					<BorderControl label={__('Item Border', 'easy-elements-for-gutenberg')} value={attributes.itemBorder} onChange={(v) => setAttributes({ itemBorder: v })} />
					<BoxShadowControls label={__('Item Box Shadow', 'easy-elements-for-gutenberg')} value={attributes.itemBoxShadow} onChange={(v) => setAttributes({ itemBoxShadow: v })} />
					{box(__('Item Border Radius', 'easy-elements-for-gutenberg'), 'itemBorderRadius')}
					{box(__('Item Padding', 'easy-elements-for-gutenberg'), 'itemPadding')}
				</PanelBody>

				{unitSection(__('Days', 'easy-elements-for-gutenberg'), 'daysTypography', 'daysColor', 'daysLabelColor', 'daysLabelTypography')}
				{unitSection(__('Hours', 'easy-elements-for-gutenberg'), 'hoursTypography', 'hoursColor', 'hoursLabelColor', 'hoursLabelTypography')}
				{unitSection(__('Minutes', 'easy-elements-for-gutenberg'), 'minutesTypography', 'minutesColor', 'minutesLabelColor', 'minutesLabelTypography')}
				{unitSection(__('Seconds', 'easy-elements-for-gutenberg'), 'secondsTypography', 'secondsColor', 'secondsLabelColor', 'secondsLabelTypography')}
			</InspectorControls>

			<div ref={previewRef}>
				<ServerSideRender block="easy-elements-for-gutenberg/countdown" attributes={attributes} httpMethod="POST" />
			</div>
		</div>
	);
}
