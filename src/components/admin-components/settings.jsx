import { useState } from 'react';
import { Row, Col, ColorPicker, Button, InputNumber, notification } from 'antd';

const COLOR_FIELDS = [
    { key: 'primary', label: 'Primary', cssVar: '--eelfg-preset-color-primary', default: '#126bf0' },
    { key: 'secondary', label: 'Secondary', cssVar: '--eelfg-preset-color-secondary', default: '#5096ff' },
    { key: 'tertiary', label: 'Tertiary', cssVar: '--eelfg-preset-color-tertiary', default: '#f3f3f3' },
    { key: 'white', label: 'White', cssVar: '--eelfg-preset-color-white', default: '#ffffff' },
    { key: 'contrast_1', label: 'Contrast 1', cssVar: '--eelfg-preset-color-contrast-1', default: '#1e1e1e' },
    { key: 'contrast_2', label: 'Contrast 2', cssVar: '--eelfg-preset-color-contrast-2', default: '#11111194' },
    { key: 'border', label: 'Border', cssVar: '--eelfg-preset-color-border', default: '#8383831f' },
];

const toCssColor = (value) => {
    if (!value) return '';
    if (typeof value === 'string') return value;
    if (typeof value.toHexString === 'function') {
        const alpha = typeof value.toHsb === 'function' ? value.toHsb().a : 1;
        return alpha < 1 ? value.toHexString(true) : value.toHexString();
    }
    return String(value);
};

const applyColorsToRoot = (colors) => {
    const root = document.documentElement;
    COLOR_FIELDS.forEach(({ key, cssVar }) => {
        if (colors[key]) {
            root.style.setProperty(cssVar, colors[key]);
        }
    });
};

// Guarded: this runs at module-eval time, and the same bundle is also loaded in
// the block editor where the `eelfg` global is not localized.
const LAYOUT_DEFAULTS = (typeof eelfg !== 'undefined' && eelfg.layoutDefaults) || { container_width: '1200px' };

// Strip the unit suffix for the numeric input. Sanitize on save adds "px" back.
const parseContainerWidth = (value) => {
    if (value === '' || value == null) return '';
    const m = String(value).match(/^([0-9]*\.?[0-9]+)/);
    return m ? parseFloat(m[1]) : '';
};

export default function Settings() {
    const initial = COLOR_FIELDS.reduce((acc, f) => {
        acc[f.key] = (eelfg.colors && eelfg.colors[f.key]) || f.default;
        return acc;
    }, {});

    const [colors, setColors] = useState(initial);
    const [saving, setSaving] = useState(false);

    const initialContainerWidth = parseContainerWidth(
        (eelfg.layout && eelfg.layout.container_width) || LAYOUT_DEFAULTS.container_width
    );
    const [containerWidth, setContainerWidth] = useState(initialContainerWidth);

    const applyContainerWidthToRoot = (px) => {
        if (px === '' || px == null) return;
        document.documentElement.style.setProperty('--eelfg-layout-row-max-width', `${px}px`);
    };

    const handleContainerWidthChange = (value) => {
        setContainerWidth(value);
        if (typeof value === 'number') applyContainerWidthToRoot(value);
    };

    const handleChange = (key) => (value) => {
        const next = toCssColor(value);
        setColors((prev) => {
            const updated = { ...prev, [key]: next };
            applyColorsToRoot(updated);
            return updated;
        });
    };

    const postJson = (path, body) =>
        fetch(eelfg.rest_url + path, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': eelfg.nonce,
            },
            body: JSON.stringify(body),
        }).then((res) => res.json());

    const handleSave = () => {
        setSaving(true);

        const containerWidthValue = (typeof containerWidth === 'number' && containerWidth > 0)
            ? `${containerWidth}px`
            : LAYOUT_DEFAULTS.container_width;

        Promise.allSettled([
            postJson('colors', { colors }),
            postJson('layout', { layout: { container_width: containerWidthValue } }),
        ])
            .then(([colorsRes, layoutRes]) => {
                const colorsOk = colorsRes.status === 'fulfilled' && colorsRes.value && colorsRes.value.status === 'success';
                const layoutOk = layoutRes.status === 'fulfilled' && layoutRes.value && layoutRes.value.status === 'success';

                if (colorsOk) eelfg.colors = colorsRes.value.colors || colors;
                if (layoutOk) eelfg.layout = layoutRes.value.layout || { container_width: containerWidthValue };

                if (colorsOk && layoutOk) {
                    notification.success({
                        message: 'Settings Saved',
                        description: 'Container width and color palette have been updated.',
                        duration: 2,
                    });
                } else {
                    notification.error({
                        message: 'Save Failed',
                        description: !colorsOk && !layoutOk
                            ? 'Could not save settings. Please try again.'
                            : `Could not save ${!colorsOk ? 'colors' : 'container width'}. Please try again.`,
                        duration: 2,
                    });
                }
            })
            .finally(() => setSaving(false));
    };

    const handleReset = () => {
        const defaults = COLOR_FIELDS.reduce((acc, f) => {
            acc[f.key] = f.default;
            return acc;
        }, {});
        setColors(defaults);
        applyColorsToRoot(defaults);

        const defaultWidth = parseContainerWidth(LAYOUT_DEFAULTS.container_width);
        setContainerWidth(defaultWidth);
        applyContainerWidthToRoot(defaultWidth);
    };

    return (
        <div className="eelfg-options-content">
            <h1 className="eelfg-options-title">Settings</h1>

            <h2 style={{ fontSize: 18, fontWeight: 600, marginTop: 0 }}>Content Container</h2>
            <p style={{ marginTop: 0, color: '#555' }}>
                Sets the boxed content max-width used by the eelfg Row block. Stored as
                the CSS variable <code>--eelfg-layout-row-max-width</code> on <code>:root</code>.
            </p>

            <Row gutter={[16, 16]} style={{ marginTop: 16 }}>
                <Col xs={24} sm={12} md={8}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: 12, padding: '10px 12px', background: '#f7f8fb', borderRadius: 8 }}>
                        <div style={{ display: 'flex', flexDirection: 'column', gap: 4, flex: 1 }}>
                            <strong>Container Width</strong>
                            <InputNumber
                                min={200}
                                max={3000}
                                step={10}
                                addonAfter="px"
                                value={containerWidth}
                                onChange={handleContainerWidthChange}
                                style={{ width: '100%' }}
                            />
                        </div>
                    </div>
                </Col>
            </Row>

            <h2 style={{ fontSize: 18, fontWeight: 600, marginTop: 32 }}>Color Palette</h2>
            <p style={{ marginTop: 0, color: '#555' }}>
                These colors are applied dynamically to the front-end CSS variables defined in <code>:root</code>.
            </p>

            <Row gutter={[16, 16]} style={{ marginTop: 16 }}>
                {COLOR_FIELDS.map((field) => (
                    <Col xs={24} sm={12} md={4} key={field.key}>
                        <div className="eelfg-color-field" style={{ display: 'flex', alignItems: 'center', gap: 12, padding: '10px 12px', background: '#f7f8fb', borderRadius: 8 }}>
                            <ColorPicker
                                value={colors[field.key]}
                                onChange={handleChange(field.key)}
                                format="hex"
                                showText
                            />
                            <div style={{ display: 'flex', flexDirection: 'column', lineHeight: 1.2 }}>
                                <strong>{field.label}</strong>
                            </div>
                        </div>
                    </Col>
                ))}
            </Row>

            <div style={{ marginTop: 24, display: 'flex', gap: 8 }}>
                <Button type="primary" onClick={handleSave} loading={saving}>Save Changes</Button>
                <Button onClick={handleReset} disabled={saving}>Reset to Defaults</Button>
            </div>
        </div>
    );
}
