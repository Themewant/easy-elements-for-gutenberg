import { __ } from '@wordpress/i18n';
import { SelectControl } from '@wordpress/components';

const TextAlignControl = ({
    attributes,
    setAttributes,
    attributeKey,
    label = __('Text Align', 'easy-elements-for-gutenberg'),
}) => {
    const value = attributes[attributeKey] || '';

    return (
        <SelectControl
            value={value}
            options={[
                { label: __('Default', 'easy-elements-for-gutenberg'), value: '' },
                { label: __('Left', 'easy-elements-for-gutenberg'), value: 'left' },
                { label: __('Center', 'easy-elements-for-gutenberg'), value: 'center' },
                { label: __('Right', 'easy-elements-for-gutenberg'), value: 'right' },
                { label: __('Justify', 'easy-elements-for-gutenberg'), value: 'justify' },
            ]}
            onChange={(newValue) => setAttributes({ [attributeKey]: newValue })}
            __nextHasNoMarginBottom={true}
        />
    );
};

export default TextAlignControl;
