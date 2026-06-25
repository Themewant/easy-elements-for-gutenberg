import { __ } from '@wordpress/i18n';
import { useEffect } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
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
import BackgroundControl from '../../custom-components/BackgroundControl';

import './editor.scss';

const COLS = ['1', '2', '3', '4', '5', '6'].map((v) => ({ label: v, value: v }));

export default function Edit({ attributes, setAttributes, clientId }) {
	const { blockId, postType, taxonomy, layoutCategory, showIcon, showCount } = attributes;

	useEffect(() => {
		if (!blockId) {
			setAttributes({ blockId: 'eelfg-cat-' + clientId.slice(0, 6) });
		}
	}, [blockId, clientId, setAttributes]);

	const { postTypes, taxonomies } = useSelect((select) => {
		const core = select('core');
		return {
			postTypes: core.getPostTypes({ per_page: -1 }) || [],
			taxonomies: core.getTaxonomies({ per_page: -1 }) || [],
		};
	}, []);

	const postTypeOptions = (postTypes || [])
		.filter((pt) => pt.viewable && !['attachment', 'wp_block', 'wp_template', 'wp_template_part', 'wp_navigation'].includes(pt.slug))
		.map((pt) => ({ label: pt.name, value: pt.slug }));

	const taxOptions = (taxonomies || [])
		.filter((tax) => Array.isArray(tax.types) && tax.types.includes(postType))
		.map((tax) => ({ label: tax.name, value: tax.slug }));

	// Keep taxonomy valid for the chosen post type.
	useEffect(() => {
		if (taxOptions.length && !taxOptions.some((t) => t.value === taxonomy)) {
			setAttributes({ taxonomy: taxOptions[0].value });
		}
	}, [postType, taxonomies]); // eslint-disable-line react-hooks/exhaustive-deps

	const color = (label, key) => <ColorPopover label={label} color={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const typo = (label, key) => <TypographyControls label={label} attributes={attributes} setAttributes={setAttributes} attributeKey={key} />;
	const border = (label, key) => <BorderControl label={label} value={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const box = (label, key) => <BoxControl label={label} values={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const num = (label, key) => <TextControl label={label} type="number" value={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} __next40pxDefaultSize __nextHasNoMarginBottom />;
	const bg = (label, c, g) => (
		<BackgroundControl label={label} colorValue={attributes[c]} gradientValue={attributes[g]} onColorChange={(v) => setAttributes({ [c]: v && typeof v === 'object' ? v.hex : v || '' })} onGradientChange={(v) => setAttributes({ [g]: v || '' })} />
	);

	return (
		<div {...useBlockProps()}>
			<InspectorControls>
				<PanelBody title={__('Query', 'easy-elements-for-gutenberg')} initialOpen={true}>
					<SelectControl
						label={__('Post Type', 'easy-elements-for-gutenberg')}
						value={postType}
						options={postTypeOptions.length ? postTypeOptions : [{ label: __('Post', 'easy-elements-for-gutenberg'), value: 'post' }]}
						onChange={(v) => setAttributes({ postType: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<SelectControl
						label={__('Taxonomy', 'easy-elements-for-gutenberg')}
						value={taxonomy}
						options={taxOptions.length ? taxOptions : [{ label: __('Categories', 'easy-elements-for-gutenberg'), value: 'category' }]}
						onChange={(v) => setAttributes({ taxonomy: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<TextControl label={__('Number of Items', 'easy-elements-for-gutenberg')} help={__('Set 0 to show all.', 'easy-elements-for-gutenberg')} type="number" value={attributes.number} onChange={(v) => setAttributes({ number: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
					<SelectControl
						label={__('Order By', 'easy-elements-for-gutenberg')}
						value={attributes.orderby}
						options={[
							{ label: __('Name', 'easy-elements-for-gutenberg'), value: 'name' },
							{ label: __('Count', 'easy-elements-for-gutenberg'), value: 'count' },
							{ label: __('Slug', 'easy-elements-for-gutenberg'), value: 'slug' },
							{ label: __('ID', 'easy-elements-for-gutenberg'), value: 'term_id' },
						]}
						onChange={(v) => setAttributes({ orderby: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<SelectControl
						label={__('Order', 'easy-elements-for-gutenberg')}
						value={attributes.order}
						options={[
							{ label: __('Ascending', 'easy-elements-for-gutenberg'), value: 'ASC' },
							{ label: __('Descending', 'easy-elements-for-gutenberg'), value: 'DESC' },
						]}
						onChange={(v) => setAttributes({ order: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<ToggleControl label={__('Hide Empty', 'easy-elements-for-gutenberg')} checked={attributes.hideEmpty} onChange={(v) => setAttributes({ hideEmpty: v })} __nextHasNoMarginBottom />
					<ToggleControl label={__('Show Count', 'easy-elements-for-gutenberg')} checked={showCount} onChange={(v) => setAttributes({ showCount: v })} __nextHasNoMarginBottom />
					<ToggleControl label={__('Show Icon', 'easy-elements-for-gutenberg')} checked={showIcon} onChange={(v) => setAttributes({ showIcon: v })} __nextHasNoMarginBottom />
					{showIcon && <IconPicker label={__('Icon', 'easy-elements-for-gutenberg')} value={attributes.catIcon} onChange={(v) => setAttributes({ catIcon: v })} />}
				</PanelBody>

				<PanelBody title={__('Layout', 'easy-elements-for-gutenberg')} initialOpen={false}>
					<SelectControl
						label={__('Layout', 'easy-elements-for-gutenberg')}
						value={layoutCategory}
						options={[
							{ label: __('List', 'easy-elements-for-gutenberg'), value: 'list' },
							{ label: __('Grid', 'easy-elements-for-gutenberg'), value: 'grid' },
						]}
						onChange={(v) => setAttributes({ layoutCategory: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					{layoutCategory === 'grid' && (
						<>
							<SelectControl label={__('Columns', 'easy-elements-for-gutenberg')} value={attributes.columns} options={COLS} onChange={(v) => setAttributes({ columns: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
							<SelectControl label={__('Columns (Tablet)', 'easy-elements-for-gutenberg')} value={attributes.columnsTablet} options={COLS} onChange={(v) => setAttributes({ columnsTablet: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
							<SelectControl label={__('Columns (Mobile)', 'easy-elements-for-gutenberg')} value={attributes.columnsMobile} options={COLS} onChange={(v) => setAttributes({ columnsMobile: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
						</>
					)}
					{num(__('Items Gap (px)', 'easy-elements-for-gutenberg'), 'itemsGap')}
				</PanelBody>
			</InspectorControls>

			<InspectorControls group="styles">
				<PanelBody title={__('Items', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{box(__('Padding', 'easy-elements-for-gutenberg'), 'itemPadding')}
					{box(__('Border Radius', 'easy-elements-for-gutenberg'), 'itemRadius')}
					{bg(__('Background', 'easy-elements-for-gutenberg'), 'itemBgColor', 'itemBgGradient')}
					{border(__('Border', 'easy-elements-for-gutenberg'), 'itemBorder')}
					<Divider />
					{bg(__('Background (Hover)', 'easy-elements-for-gutenberg'), 'itemBgHoverColor', 'itemBgHoverGradient')}
					{color(__('Border Color (Hover)', 'easy-elements-for-gutenberg'), 'itemBorderColorHover')}
				</PanelBody>

				{showIcon && (
					<PanelBody title={__('Icon', 'easy-elements-for-gutenberg')} initialOpen={false}>
						{num(__('Size (px)', 'easy-elements-for-gutenberg'), 'iconSize')}
						{color(__('Color', 'easy-elements-for-gutenberg'), 'iconColor')}
						{color(__('Hover Color', 'easy-elements-for-gutenberg'), 'iconColorHover')}
						{num(__('Spacing (px)', 'easy-elements-for-gutenberg'), 'iconSpacing')}
					</PanelBody>
				)}

				<PanelBody title={__('Title', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Color', 'easy-elements-for-gutenberg'), 'nameColor')}
					{color(__('Hover Color', 'easy-elements-for-gutenberg'), 'nameColorHover')}
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'nameTypography')}
				</PanelBody>

				{showCount && (
					<PanelBody title={__('Count', 'easy-elements-for-gutenberg')} initialOpen={false}>
						{color(__('Color', 'easy-elements-for-gutenberg'), 'countColor')}
						{typo(__('Typography', 'easy-elements-for-gutenberg'), 'countTypography')}
					</PanelBody>
				)}
			</InspectorControls>

			<ServerSideRender block="easy-elements-for-gutenberg/category-list" attributes={attributes} httpMethod="POST" />
		</div>
	);
}
