import { useState } from 'react';
import { Alert, Button, Input, Space, Tag, Typography, notification } from 'antd';

const { Title, Paragraph, Text } = Typography;

const readInitial = () => {
    const data = (typeof eelfg !== 'undefined' && eelfg.license) ? eelfg.license : {};
    return {
        key: data.key || '',
        status: data.status || '',
    };
};

const callLicenseApi = (path, { method = 'POST', body } = {}) => {
    const opts = {
        method,
        headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': eelfg.nonce,
        },
    };
    if (body) opts.body = JSON.stringify(body);

    return fetch(eelfg.rest_url + 'license/' + path, opts).then(async (res) => {
        const data = await res.json().catch(() => ({}));
        if (!res.ok) {
            const message = data && data.message ? data.message : 'Request failed.';
            const error = new Error(message);
            error.payload = data;
            throw error;
        }
        return data;
    });
};

export default function License() {
    const initial = readInitial();
    const [licenseKey, setLicenseKey] = useState(initial.key);
    const [status, setStatus] = useState(initial.status);
    const [activating, setActivating] = useState(false);
    const [deactivating, setDeactivating] = useState(false);
    const [checking, setChecking] = useState(false);

    const isActive = status === 'valid';

    const syncLocal = (next) => {
        if (eelfg && eelfg.license) {
            eelfg.license = { ...eelfg.license, ...next };
        }
    };

    const handleActivate = () => {
        if (!licenseKey.trim()) {
            notification.warning({
                message: 'License Key Required',
                description: 'Please enter your license key.',
                duration: 2,
            });
            return;
        }
        setActivating(true);
        callLicenseApi('activate', { body: { license_key: licenseKey.trim() } })
            .then((data) => {
                setStatus(data.status || 'valid');
                syncLocal({ key: licenseKey.trim(), status: data.status || 'valid' });
                notification.success({
                    message: 'License Activated',
                    description: data.message || 'Your license is now active.',
                    duration: 2,
                });
            })
            .catch((err) => {
                setStatus('');
                setLicenseKey('');
                syncLocal({ status: '', key: '' });
                notification.error({
                    message: 'Activation Failed',
                    description: err.message,
                    duration: 3,
                });
            })
            .finally(() => setActivating(false));
    };

    const handleDeactivate = () => {
        setDeactivating(true);
        callLicenseApi('deactivate')
            .then((data) => {
                setStatus('');
                setLicenseKey('');
                syncLocal({ status: '', key: '' });
                notification.success({
                    message: 'License Deactivated',
                    description: data.message || 'License has been deactivated.',
                    duration: 2,
                });
            })
            .catch((err) => {
                notification.error({
                    message: 'Deactivation Failed',
                    description: err.message,
                    duration: 3,
                });
            })
            .finally(() => setDeactivating(false));
    };

    const handleCheck = () => {
        setChecking(true);
        callLicenseApi('check?force=1', { method: 'GET' })
            .then((data) => {
                const next = data.status || 'invalid';
                setStatus(next === 'valid' ? 'valid' : '');
                syncLocal({ status: next === 'valid' ? 'valid' : '' });
                notification.info({
                    message: 'License Status',
                    description: next === 'valid' ? 'Your license is valid.' : 'License is not valid.',
                    duration: 2,
                });
            })
            .catch((err) => {
                notification.error({
                    message: 'Check Failed',
                    description: err.message,
                    duration: 3,
                });
            })
            .finally(() => setChecking(false));
    };

    return (
        <div className="eelfg-options-content">
            <Title level={3} style={{ marginTop: 0 }}>License</Title>
            <Paragraph type="secondary" style={{ marginTop: 0 }}>
                Activate your license to receive updates and access premium features.
            </Paragraph>

            <div style={{ margin: '12px 0 20px' }}>
                <Text strong style={{ marginRight: 8 }}>Status:</Text>
                {isActive
                    ? <Tag color="green">Active</Tag>
                    : <Tag color="red">Inactive</Tag>}
            </div>

            {isActive && (
                <Alert
                    type="success"
                    showIcon
                    message="Your license is active on this site."
                    style={{ marginBottom: 16 }}
                />
            )}

            <div style={{ maxWidth: 520 }}>
                <label htmlFor="eelfg-license-key" style={{ display: 'block', marginBottom: 6, fontWeight: 600 }}>
                    License Key
                </label>
                <Input.Password
                    id="eelfg-license-key"
                    value={licenseKey}
                    onChange={(e) => setLicenseKey(e.target.value)}
                    placeholder="Enter your license key"
                    disabled={isActive || activating}
                    autoComplete="off"
                />
            </div>

            <Space style={{ marginTop: 16 }}>
                {!isActive && (
                    <Button type="primary" onClick={handleActivate} loading={activating}>
                        Activate License
                    </Button>
                )}
                {isActive && (
                    <Button danger onClick={handleDeactivate} loading={deactivating}>
                        Deactivate License
                    </Button>
                )}
                <Button onClick={handleCheck} loading={checking} disabled={!licenseKey.trim() && !isActive}>
                    Check Status
                </Button>
            </Space>
        </div>
    );
}
