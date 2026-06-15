import { __ } from '@wordpress/i18n';
import { BaseControl, Tooltip } from '@wordpress/components';

const ImageRadioControl = ({
    label,
    options = [],
    value,
    onChange,
    help
}) => {

    const isLicenseActive = typeof easyElementsForGutenbergProData !== 'undefined' && easyElementsForGutenbergProData.is_license_active === '1';
    const proVersionUrl = 'https://themewant.com/downloads/easy-elements-for-gutenberg-pro'; // goto pro url if license not active when onclick pro option


    return (
        <>
            <BaseControl label={label} help={help} className="eshb-image-radio-control" __nextHasNoMarginBottom={true}>
                <div style={{
                    display: 'grid',
                    gridTemplateColumns: 'repeat(auto-fill, minmax(60px, 1fr))',
                    gap: '10px'
                }}>
                    {options.map((option, index) => (
                        <Tooltip text={option.label} key={index}>
                            <div
                                className={`eshb-image-radio-item ${value === option.value ? 'active' : ''}`}
                                onClick={() => {
                                    if (option.isPro && !isLicenseActive) {
                                        window.open(proVersionUrl, '_blank');
                                        return;
                                    }
                                    onChange(option.value)
                                }}
                                style={{
                                    border: value === option.value ? '2px solid var(--wp-admin-theme-color)' : '1px solid #e0e0e0',
                                    borderRadius: '4px',
                                    padding: '4px',
                                    cursor: 'pointer',
                                    backgroundColor: value === option.value ? 'rgba(var(--wp-admin-theme-color-rgb), 0.04)' : '#fff',
                                    transition: 'all 0.2s ease',
                                    display: 'flex',
                                    alignItems: 'center',
                                    justifyContent: 'center',
                                    position: 'relative'
                                }}
                            >
                                <img
                                    src={option.src}
                                    alt={option.label}
                                    style={{
                                        display: 'block',
                                        maxWidth: '100%',
                                        height: 'auto',
                                        borderRadius: '2px'
                                    }}
                                />
                                {!isLicenseActive && option.isPro && (
                                    <span style={{
                                        position: 'absolute',
                                        top: '4px',
                                        right: '4px',
                                        backgroundColor: '#ff0036',
                                        color: 'white',
                                        fontSize: '10px',
                                        fontWeight: 'bold',
                                        padding: '2px 3px',
                                        borderRadius: '3px',
                                        textTransform: 'uppercase',
                                        lineHeight: '1'
                                    }}>
                                        Pro
                                    </span>
                                )}
                            </div>
                        </Tooltip>
                    ))}
                </div>
            </BaseControl>
        </>
    );
};

export default ImageRadioControl;
