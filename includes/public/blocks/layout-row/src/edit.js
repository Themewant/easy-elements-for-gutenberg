import { __ } from '@wordpress/i18n';
import { useEffect, useMemo, useCallback } from '@wordpress/element';
import { createBlock } from '@wordpress/blocks';
import { useSelect, useDispatch } from '@wordpress/data';
import {
    useBlockProps,
    useInnerBlocksProps,
    InspectorControls,
    BlockControls,
    store as blockEditorStore,
} from '@wordpress/block-editor';
import {
    PanelBody,
    __experimentalDivider as Divider,
    SelectControl,
    ToggleControl,
    TextControl,
    BoxControl,
    ToolbarGroup,
    ToolbarButton,
    Button,
    __experimentalUnitControl as UnitControl,
    __experimentalNumberControl as NumberControl,
    __experimentalToggleGroupControl as ToggleGroupControl,
    __experimentalToggleGroupControlOptionIcon as ToggleGroupControlOptionIcon,
} from '@wordpress/components';

import {
    iconDirRow, iconDirRowRev, iconDirCol, iconDirColRev,
    iconJustifyStart, iconJustifyCenter, iconJustifyEnd,
    iconJustifyBetween, iconJustifyAround, iconJustifyEvenly,
    iconAlignStretch, iconAlignTop, iconAlignMiddle, iconAlignBottom, iconAlignBaseline,
    iconWrapNo, iconWrap, iconWrapRev,
} from './flexbox-icons';

import BackgroundControl from '../../custom-components/BackgroundControl';
import BorderControl from '../../custom-components/BorderControl';
import BoxShadowControls from '../../custom-components/BoxShadowControls';
import ColorPopover from '../../custom-components/ColorPopover';
import ResponsiveWrapper from '../../custom-components/ResponsiveWrapper';

import { LAYOUT_PRESETS, presetTemplate, getPresetById } from './presets';
import { buildRowEditorCss } from './style-utils';

import './editor.scss';

const ALLOWED_BLOCKS = ['easy-elements-for-gutenberg/column'];

const getKey = (base, device) =>
    device === 'desktop' ? base : `${base}${device.charAt(0).toUpperCase() + device.slice(1)}`;

const presetIcon = (preset) => {
    const total = preset.columns.reduce((a, b) => a + b, 0);
    return (
        <span className="eelfg-preset-icon" aria-hidden="true">
            {preset.columns.map((w, i) => (
                <span
                    key={i}
                    className="eelfg-preset-icon__col"
                    style={{ flex: `${(w / total) * 100} 0 0` }}
                />
            ))}
        </span>
    );
};

export default function Edit({ attributes, setAttributes, clientId }) {
    const {
        blockId,
        htmlTag,
        preset,
        contentWidth,
        customClass,
        verticalAlign,
        equalHeight,
        stretchColumns,
    } = attributes;

    useEffect(() => {
        if (!blockId) {
            setAttributes({ blockId: 'eelfg-layout-row-' + Math.random().toString(36).slice(2, 8) });
        }
    }, [blockId, setAttributes]);

    const { innerBlocks, hasChildren } = useSelect(
        (select) => {
            const blocks = select(blockEditorStore).getBlocks(clientId);
            return { innerBlocks: blocks, hasChildren: blocks.length > 0 };
        },
        [clientId]
    );

    const { replaceInnerBlocks, updateBlockAttributes } = useDispatch(blockEditorStore);

    const Tag = ['div', 'section', 'article', 'main', 'header', 'footer', 'aside'].includes(htmlTag)
        ? htmlTag
        : 'div';

    const editorCss = useMemo(
        () => buildRowEditorCss(attributes, innerBlocks.length),
        [attributes, innerBlocks.length]
    );

    const wrapperClasses = [
        'eelfg-block',
        'eelfg-layout-row',
        blockId,
        `is-content-${contentWidth}`,
        verticalAlign ? `is-valign-${verticalAlign}` : '',
        equalHeight ? 'is-equal-height' : '',
        stretchColumns ? 'is-stretch' : '',
        customClass,
    ]
        .filter(Boolean)
        .join(' ');

    const blockProps = useBlockProps({ className: wrapperClasses });

    const innerBlocksProps = useInnerBlocksProps(
        { className: 'eelfg-layout-row__inner' },
        {
            allowedBlocks: ALLOWED_BLOCKS,
            orientation: 'horizontal',
            template: presetTemplate(preset),
            templateLock: false,
            renderAppender: false,
        }
    );

    const applyPreset = useCallback(
        (newPresetId) => {
            const next = getPresetById(newPresetId);
            const widths = next.columns;
            const existing = innerBlocks || [];

            const merged = widths.map((w, idx) => {
                const widthStr = `${parseFloat(w).toFixed(2)}%`;
                if (existing[idx]) {
                    const reused = existing[idx];
                    return {
                        ...reused,
                        attributes: { ...reused.attributes, width: widthStr },
                    };
                }
                return createBlock('easy-elements-for-gutenberg/column', { width: widthStr });
            });

            replaceInnerBlocks(clientId, merged, false);
            setAttributes({ preset: newPresetId, columns: widths.length });
        },
        [clientId, innerBlocks, replaceInnerBlocks, setAttributes]
    );

    const addColumn = useCallback(() => {
        const count = (innerBlocks || []).length + 1;
        const evenWidth = `${(100 / count).toFixed(2)}%`;
        (innerBlocks || []).forEach((b) => updateBlockAttributes(b.clientId, { width: evenWidth }));
        const next = [
            ...(innerBlocks || []),
            createBlock('easy-elements-for-gutenberg/column', { width: evenWidth }),
        ];
        replaceInnerBlocks(clientId, next, false);
        setAttributes({ columns: count, preset: '' });
    }, [clientId, innerBlocks, replaceInnerBlocks, updateBlockAttributes, setAttributes]);

    const removeLastColumn = useCallback(() => {
        if ((innerBlocks || []).length <= 1) return;
        const remaining = innerBlocks.slice(0, -1);
        const evenWidth = `${(100 / remaining.length).toFixed(2)}%`;
        remaining.forEach((b) => updateBlockAttributes(b.clientId, { width: evenWidth }));
        replaceInnerBlocks(clientId, remaining, false);
        setAttributes({ columns: remaining.length, preset: '' });
    }, [clientId, innerBlocks, replaceInnerBlocks, updateBlockAttributes, setAttributes]);

    return (
        <>
            <style>{editorCss}</style>

            <BlockControls>
                <ToolbarGroup>
                    <ToolbarButton icon="plus-alt2" label={__('Add Column', 'eelfg')} onClick={addColumn} />
                    <ToolbarButton
                        icon="minus"
                        label={__('Remove Column', 'eelfg')}
                        onClick={removeLastColumn}
                        disabled={(innerBlocks || []).length <= 1}
                    />
                </ToolbarGroup>
            </BlockControls>

            <InspectorControls>
                <PanelBody title={__('Layout', 'eelfg')} initialOpen={true}>
                    <div className="eelfg-preset-grid">
                        {LAYOUT_PRESETS.map((p) => (
                            <Button
                                key={p.id}
                                className={`eelfg-preset-btn ${preset === p.id ? 'is-active' : ''}`}
                                onClick={() => applyPreset(p.id)}
                                label={p.label}
                            >
                                {presetIcon(p)}
                                <span className="eelfg-preset-btn__label">{p.label}</span>
                            </Button>
                        ))}
                    </div>
                    <Divider />

                    <SelectControl
                        label={__('HTML Tag', 'eelfg')}
                        value={htmlTag}
                        options={[
                            { label: 'div', value: 'div' },
                            { label: 'section', value: 'section' },
                            { label: 'article', value: 'article' },
                            { label: 'main', value: 'main' },
                            { label: 'header', value: 'header' },
                            { label: 'footer', value: 'footer' },
                            { label: 'aside', value: 'aside' },
                        ]}
                        onChange={(value) => setAttributes({ htmlTag: value })}
                        __next40pxDefaultSize
                        __nextHasNoMarginBottom
                    />
                    <Divider />

                    <SelectControl
                        label={__('Content Width', 'eelfg')}
                        value={contentWidth}
                        options={[
                            { label: __('Boxed', 'eelfg'), value: 'boxed' },
                            { label: __('Full Width', 'eelfg'), value: 'full' },
                        ]}
                        onChange={(value) => setAttributes({ contentWidth: value })}
                        __next40pxDefaultSize
                        __nextHasNoMarginBottom
                    />

                    {contentWidth === 'boxed' && (
                        <ResponsiveWrapper label={__('Max Width', 'eelfg')}>
                            {(device) => (
                                <UnitControl
                                    value={attributes[getKey('maxWidth', device)]}
                                    onChange={(v) => setAttributes({ [getKey('maxWidth', device)]: v })}
                                    __next40pxDefaultSize
                                />
                            )}
                        </ResponsiveWrapper>
                    )}

                    <ResponsiveWrapper label={__('Min Height', 'eelfg')}>
                        {(device) => (
                            <UnitControl
                                value={attributes[getKey('minHeight', device)]}
                                onChange={(v) => setAttributes({ [getKey('minHeight', device)]: v })}
                                __next40pxDefaultSize
                            />
                        )}
                    </ResponsiveWrapper>
                </PanelBody>

                <PanelBody title={__('Flexbox', 'eelfg')} initialOpen={false}>
                    <ResponsiveWrapper label={__('Direction', 'eelfg')}>
                        {(device) => (
                            <ToggleGroupControl
                                isBlock
                                isDeselectable
                                value={attributes[getKey('flexDirection', device)]}
                                onChange={(v) => setAttributes({ [getKey('flexDirection', device)]: v ?? '' })}
                                __next40pxDefaultSize
                                __nextHasNoMarginBottom
                            >
                                <ToggleGroupControlOptionIcon value="row"            icon={iconDirRow}    label={__('Row', 'eelfg')} />
                                <ToggleGroupControlOptionIcon value="row-reverse"    icon={iconDirRowRev} label={__('Row reverse', 'eelfg')} />
                                <ToggleGroupControlOptionIcon value="column"         icon={iconDirCol}    label={__('Column', 'eelfg')} />
                                <ToggleGroupControlOptionIcon value="column-reverse" icon={iconDirColRev} label={__('Column reverse', 'eelfg')} />
                            </ToggleGroupControl>
                        )}
                    </ResponsiveWrapper>

                    <ResponsiveWrapper label={__('Justify Content', 'eelfg')}>
                        {(device) => (
                            <ToggleGroupControl
                                isBlock
                                isDeselectable
                                value={attributes[getKey('justifyContent', device)]}
                                onChange={(v) => setAttributes({ [getKey('justifyContent', device)]: v ?? '' })}
                                __next40pxDefaultSize
                                __nextHasNoMarginBottom
                            >
                                <ToggleGroupControlOptionIcon value="flex-start"    icon={iconJustifyStart}   label={__('Start', 'eelfg')} />
                                <ToggleGroupControlOptionIcon value="center"        icon={iconJustifyCenter}  label={__('Center', 'eelfg')} />
                                <ToggleGroupControlOptionIcon value="flex-end"      icon={iconJustifyEnd}     label={__('End', 'eelfg')} />
                                <ToggleGroupControlOptionIcon value="space-between" icon={iconJustifyBetween} label={__('Space between', 'eelfg')} />
                                <ToggleGroupControlOptionIcon value="space-around"  icon={iconJustifyAround}  label={__('Space around', 'eelfg')} />
                                <ToggleGroupControlOptionIcon value="space-evenly"  icon={iconJustifyEvenly}  label={__('Space evenly', 'eelfg')} />
                            </ToggleGroupControl>
                        )}
                    </ResponsiveWrapper>

                    <ResponsiveWrapper label={__('Align Items', 'eelfg')}>
                        {(device) => (
                            <ToggleGroupControl
                                isBlock
                                isDeselectable
                                value={attributes[getKey('alignItems', device)]}
                                onChange={(v) => setAttributes({ [getKey('alignItems', device)]: v ?? '' })}
                                __next40pxDefaultSize
                                __nextHasNoMarginBottom
                            >
                                <ToggleGroupControlOptionIcon value="stretch"    icon={iconAlignStretch}  label={__('Stretch', 'eelfg')} />
                                <ToggleGroupControlOptionIcon value="flex-start" icon={iconAlignTop}      label={__('Top', 'eelfg')} />
                                <ToggleGroupControlOptionIcon value="center"     icon={iconAlignMiddle}   label={__('Middle', 'eelfg')} />
                                <ToggleGroupControlOptionIcon value="flex-end"   icon={iconAlignBottom}   label={__('Bottom', 'eelfg')} />
                                <ToggleGroupControlOptionIcon value="baseline"   icon={iconAlignBaseline} label={__('Baseline', 'eelfg')} />
                            </ToggleGroupControl>
                        )}
                    </ResponsiveWrapper>

                    <ResponsiveWrapper label={__('Align Content', 'eelfg')}>
                        {(device) => (
                            <ToggleGroupControl
                                isBlock
                                isDeselectable
                                value={attributes[getKey('alignContent', device)]}
                                onChange={(v) => setAttributes({ [getKey('alignContent', device)]: v ?? '' })}
                                __next40pxDefaultSize
                                __nextHasNoMarginBottom
                            >
                                <ToggleGroupControlOptionIcon value="stretch"       icon={iconAlignStretch}   label={__('Stretch', 'eelfg')} />
                                <ToggleGroupControlOptionIcon value="flex-start"    icon={iconAlignTop}       label={__('Top', 'eelfg')} />
                                <ToggleGroupControlOptionIcon value="center"        icon={iconAlignMiddle}    label={__('Middle', 'eelfg')} />
                                <ToggleGroupControlOptionIcon value="flex-end"      icon={iconAlignBottom}    label={__('Bottom', 'eelfg')} />
                                <ToggleGroupControlOptionIcon value="space-between" icon={iconJustifyBetween} label={__('Space between', 'eelfg')} />
                                <ToggleGroupControlOptionIcon value="space-around"  icon={iconJustifyAround}  label={__('Space around', 'eelfg')} />
                            </ToggleGroupControl>
                        )}
                    </ResponsiveWrapper>

                    <ResponsiveWrapper label={__('Wrap', 'eelfg')}>
                        {(device) => (
                            <ToggleGroupControl
                                isBlock
                                isDeselectable
                                value={attributes[getKey('flexWrap', device)]}
                                onChange={(v) => setAttributes({ [getKey('flexWrap', device)]: v ?? '' })}
                                __next40pxDefaultSize
                                __nextHasNoMarginBottom
                            >
                                <ToggleGroupControlOptionIcon value="nowrap"       icon={iconWrapNo}  label={__('No wrap', 'eelfg')} />
                                <ToggleGroupControlOptionIcon value="wrap"         icon={iconWrap}    label={__('Wrap', 'eelfg')} />
                                <ToggleGroupControlOptionIcon value="wrap-reverse" icon={iconWrapRev} label={__('Wrap reverse', 'eelfg')} />
                            </ToggleGroupControl>
                        )}
                    </ResponsiveWrapper>

                    <ResponsiveWrapper label={__('Gap', 'eelfg')}>
                        {(device) => (
                            <UnitControl
                                value={attributes[getKey('gap', device)]}
                                onChange={(v) => setAttributes({ [getKey('gap', device)]: v })}
                                __next40pxDefaultSize
                            />
                        )}
                    </ResponsiveWrapper>

                    <ResponsiveWrapper label={__('Row Gap', 'eelfg')}>
                        {(device) => (
                            <UnitControl
                                value={attributes[getKey('rowGap', device)]}
                                onChange={(v) => setAttributes({ [getKey('rowGap', device)]: v })}
                                __next40pxDefaultSize
                            />
                        )}
                    </ResponsiveWrapper>

                    <ResponsiveWrapper label={__('Column Gap', 'eelfg')}>
                        {(device) => (
                            <UnitControl
                                value={attributes[getKey('columnGap', device)]}
                                onChange={(v) => setAttributes({ [getKey('columnGap', device)]: v })}
                                __next40pxDefaultSize
                            />
                        )}
                    </ResponsiveWrapper>
                </PanelBody>

                <PanelBody title={__('Advanced Layout', 'eelfg')} initialOpen={false}>
                    <SelectControl
                        label={__('Vertical Align', 'eelfg')}
                        value={verticalAlign}
                        options={[
                            { label: __('Default', 'eelfg'), value: '' },
                            { label: __('Top', 'eelfg'), value: 'top' },
                            { label: __('Middle', 'eelfg'), value: 'middle' },
                            { label: __('Bottom', 'eelfg'), value: 'bottom' },
                        ]}
                        onChange={(v) => setAttributes({ verticalAlign: v })}
                        __next40pxDefaultSize
                        __nextHasNoMarginBottom
                    />
                    <Divider />
                    <ToggleControl
                        label={__('Equal Height Columns', 'eelfg')}
                        checked={equalHeight}
                        onChange={(v) => setAttributes({ equalHeight: v })}
                        __nextHasNoMarginBottom
                    />
                    <Divider />
                    <ToggleControl
                        label={__('Stretch Columns', 'eelfg')}
                        checked={stretchColumns}
                        onChange={(v) => setAttributes({ stretchColumns: v })}
                        __nextHasNoMarginBottom
                    />
                    <Divider />
                    <TextControl
                        label={__('Custom CSS Class', 'eelfg')}
                        value={customClass}
                        onChange={(v) => setAttributes({ customClass: v })}
                        __next40pxDefaultSize
                        __nextHasNoMarginBottom
                    />
                </PanelBody>
            </InspectorControls>

            <InspectorControls group="styles">
                <PanelBody title={__('Background', 'eelfg')} initialOpen={false}>
                    <BackgroundControl
                        label={__('Background', 'eelfg')}
                        colorValue={attributes.background}
                        gradientValue={attributes.backgroundGradient}
                        onColorChange={(v) => {
                            const hex = v && typeof v === 'object' ? v.hex : v;
                            setAttributes({ background: hex || '' });
                        }}
                        onGradientChange={(v) => setAttributes({ backgroundGradient: v || '' })}
                    />
                    <Divider />
                    <TextControl
                        label={__('Background Image URL', 'eelfg')}
                        value={attributes.backgroundImage?.url || ''}
                        onChange={(v) =>
                            setAttributes({
                                backgroundImage: { ...attributes.backgroundImage, url: v },
                            })
                        }
                        __next40pxDefaultSize
                        __nextHasNoMarginBottom
                    />
                    {attributes.backgroundImage?.url && (
                        <>
                            <Divider />
                            <SelectControl
                                label={__('Size', 'eelfg')}
                                value={attributes.backgroundSize}
                                options={[
                                    { label: 'auto', value: 'auto' },
                                    { label: 'cover', value: 'cover' },
                                    { label: 'contain', value: 'contain' },
                                ]}
                                onChange={(v) => setAttributes({ backgroundSize: v })}
                                __next40pxDefaultSize
                                __nextHasNoMarginBottom
                            />
                            <SelectControl
                                label={__('Repeat', 'eelfg')}
                                value={attributes.backgroundRepeat}
                                options={[
                                    { label: 'no-repeat', value: 'no-repeat' },
                                    { label: 'repeat', value: 'repeat' },
                                    { label: 'repeat-x', value: 'repeat-x' },
                                    { label: 'repeat-y', value: 'repeat-y' },
                                ]}
                                onChange={(v) => setAttributes({ backgroundRepeat: v })}
                                __next40pxDefaultSize
                                __nextHasNoMarginBottom
                            />
                            <SelectControl
                                label={__('Position', 'eelfg')}
                                value={attributes.backgroundPosition}
                                options={[
                                    { label: 'center center', value: 'center center' },
                                    { label: 'top left', value: 'top left' },
                                    { label: 'top center', value: 'top center' },
                                    { label: 'top right', value: 'top right' },
                                    { label: 'center left', value: 'center left' },
                                    { label: 'center right', value: 'center right' },
                                    { label: 'bottom left', value: 'bottom left' },
                                    { label: 'bottom center', value: 'bottom center' },
                                    { label: 'bottom right', value: 'bottom right' },
                                ]}
                                onChange={(v) => setAttributes({ backgroundPosition: v })}
                                __next40pxDefaultSize
                                __nextHasNoMarginBottom
                            />
                            <SelectControl
                                label={__('Attachment', 'eelfg')}
                                value={attributes.backgroundAttachment}
                                options={[
                                    { label: 'scroll', value: 'scroll' },
                                    { label: 'fixed', value: 'fixed' },
                                ]}
                                onChange={(v) => setAttributes({ backgroundAttachment: v })}
                                __next40pxDefaultSize
                                __nextHasNoMarginBottom
                            />
                        </>
                    )}
                </PanelBody>

                <PanelBody title={__('Border', 'eelfg')} initialOpen={false}>
                    <BorderControl
                        label={__('Border', 'eelfg')}
                        value={attributes.border}
                        onChange={(v) => setAttributes({ border: v })}
                    />
                    <Divider />
                    <BoxControl
                        label={__('Border Radius', 'eelfg')}
                        values={attributes.borderRadius}
                        onChange={(v) => setAttributes({ borderRadius: v })}
                    />
                    <Divider />
                    <BoxShadowControls
                        label={__('Box Shadow', 'eelfg')}
                        value={attributes.boxShadow}
                        onChange={(v) => setAttributes({ boxShadow: v })}
                    />
                </PanelBody>

                <PanelBody title={__('Spacing', 'eelfg')} initialOpen={false}>
                    <ResponsiveWrapper label={__('Padding', 'eelfg')}>
                        {(device) => (
                            <BoxControl
                                values={attributes[getKey('padding', device)]}
                                onChange={(v) => setAttributes({ [getKey('padding', device)]: v })}
                            />
                        )}
                    </ResponsiveWrapper>
                    <Divider />
                    <ResponsiveWrapper label={__('Margin', 'eelfg')}>
                        {(device) => (
                            <BoxControl
                                values={attributes[getKey('margin', device)]}
                                onChange={(v) => setAttributes({ [getKey('margin', device)]: v })}
                            />
                        )}
                    </ResponsiveWrapper>
                </PanelBody>

                <PanelBody title={__('Position & Z-Index', 'eelfg')} initialOpen={false}>
                    <SelectControl
                        label={__('Position', 'eelfg')}
                        value={attributes.position}
                        options={[
                            { label: __('Default', 'eelfg'), value: '' },
                            { label: 'static', value: 'static' },
                            { label: 'relative', value: 'relative' },
                            { label: 'absolute', value: 'absolute' },
                            { label: 'fixed', value: 'fixed' },
                            { label: 'sticky', value: 'sticky' },
                        ]}
                        onChange={(v) => setAttributes({ position: v })}
                        __next40pxDefaultSize
                        __nextHasNoMarginBottom
                    />
                    <Divider />
                    <SelectControl
                        label={__('Overflow', 'eelfg')}
                        value={attributes.overflow}
                        options={[
                            { label: __('Default', 'eelfg'), value: '' },
                            { label: 'visible', value: 'visible' },
                            { label: 'hidden', value: 'hidden' },
                            { label: 'auto', value: 'auto' },
                            { label: 'scroll', value: 'scroll' },
                        ]}
                        onChange={(v) => setAttributes({ overflow: v })}
                        __next40pxDefaultSize
                        __nextHasNoMarginBottom
                    />
                    <Divider />
                    <TextControl
                        label={__('Z-Index', 'eelfg')}
                        value={attributes.zIndex}
                        onChange={(v) => setAttributes({ zIndex: v })}
                        type="number"
                        __next40pxDefaultSize
                        __nextHasNoMarginBottom
                    />
                </PanelBody>
            </InspectorControls>

            <Tag {...blockProps}>
                {hasChildren ? (
                    <div {...innerBlocksProps} />
                ) : (
                    <div className="eelfg-layout-row__layout-picker">
                        <div className="eelfg-layout-row__layout-picker__title">
                            {__('Chose a Layout', 'eelfg')}
                        </div>
                        <div className="eelfg-layout-row__layout-picker__grid">
                            {LAYOUT_PRESETS.map((p) => (
                                <button
                                    type="button"
                                    key={p.id}
                                    className="eelfg-layout-row__layout-picker__item"
                                    onClick={() => applyPreset(p.id)}
                                    aria-label={p.label}
                                    title={p.label}
                                >
                                    {presetIcon(p)}
                                </button>
                            ))}
                        </div>
                    </div>
                )}
            </Tag>
        </>
    );
}
