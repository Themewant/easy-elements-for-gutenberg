import { __ } from '@wordpress/i18n';
import { useEffect, useRef } from '@wordpress/element';
import { ServerSideRender } from '@wordpress/server-side-render';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	ToggleControl,
	CheckboxControl,
	TextControl,
	TextareaControl,
	BoxControl,
	Button,
	TabPanel,
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
const ICON_UP = SVG('M12 6.6l-6 6 1.4 1.4 4.6-4.6 4.6 4.6 1.4-1.4z');
const ICON_DOWN = SVG('M12 15.4l6-6-1.4-1.4-4.6 4.6-4.6-4.6-1.4 1.4z');
const ICON_TRASH = SVG('M9 3v1H4v2h16V4h-5V3H9zM6 7l1 13h10l1-13H6zm4 2h1v9h-1V9zm3 0h1v9h-1V9z');
const ICON_ADD = SVG('M11 5v6H5v2h6v6h2v-6h6v-2h-6V5z');

const LOGIN_TYPES = [
	{ label: __('Username', 'easy-elements-for-gutenberg'), value: 'username' },
	{ label: __('Password', 'easy-elements-for-gutenberg'), value: 'password' },
	{ label: __('Static Text', 'easy-elements-for-gutenberg'), value: 'static_text' },
];
const REGISTER_TYPES = [
	{ label: __('Username', 'easy-elements-for-gutenberg'), value: 'user_login' },
	{ label: __('Email', 'easy-elements-for-gutenberg'), value: 'user_email' },
	{ label: __('Password', 'easy-elements-for-gutenberg'), value: 'user_pass' },
	{ label: __('Confirm Password', 'easy-elements-for-gutenberg'), value: 'confirm_password' },
	{ label: __('First Name', 'easy-elements-for-gutenberg'), value: 'first_name' },
	{ label: __('Last Name', 'easy-elements-for-gutenberg'), value: 'last_name' },
	{ label: __('Display Name', 'easy-elements-for-gutenberg'), value: 'display_name' },
	{ label: __('Nice Name', 'easy-elements-for-gutenberg'), value: 'user_nicename' },
	{ label: __('Website', 'easy-elements-for-gutenberg'), value: 'user_url' },
	{ label: __('Description', 'easy-elements-for-gutenberg'), value: 'description' },
	{ label: __('Consent', 'easy-elements-for-gutenberg'), value: 'easyel_consent' },
	{ label: __('Custom', 'easy-elements-for-gutenberg'), value: 'custom' },
	{ label: __('Static Text', 'easy-elements-for-gutenberg'), value: 'static_text' },
];

export default function Edit({ attributes, setAttributes, clientId }) {
	const { blockId, formType } = attributes;

	useEffect(() => {
		if (!blockId) {
			setAttributes({ blockId: 'eelfg-lr-' + clientId.slice(0, 6) });
		}
	}, [blockId, clientId, setAttributes]);

	// Editor preview: make the "both" form switcher work (view.js doesn't run in SSR).
	const previewRef = useRef(null);
	useEffect(() => {
		const root = previewRef.current;
		if (!root) return undefined;
		const onClick = (e) => {
			const sw = e.target.closest('.eelfg-form-switcher');
			if (!sw || !root.contains(sw)) return;
			e.preventDefault();
			const wrap = sw.closest('.eelfg-authentication-toggle-wrap');
			if (wrap) wrap.setAttribute('data-active-form', sw.getAttribute('data-switch-to'));
		};
		root.addEventListener('click', onClick);
		return () => root.removeEventListener('click', onClick);
	}, []);

	const isLogin = formType === 'login' || formType === 'both';
	const isRegister = formType === 'register' || formType === 'both';

	const color = (label, key) => <ColorPopover label={label} color={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const typo = (label, key) => <TypographyControls label={label} attributes={attributes} setAttributes={setAttributes} attributeKey={key} />;
	const box = (label, key) => <BoxControl label={label} values={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const border = (label, key) => <BorderControl label={label} value={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const shadow = (label, key) => <BoxShadowControls label={label} value={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} />;
	const num = (label, key) => <TextControl label={label} type="number" value={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} __next40pxDefaultSize __nextHasNoMarginBottom />;
	const txt = (label, key) => <TextControl label={label} value={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} __next40pxDefaultSize __nextHasNoMarginBottom />;
	const area = (label, key) => <TextareaControl label={label} value={attributes[key]} onChange={(v) => setAttributes({ [key]: v })} __nextHasNoMarginBottom />;

	// Generic repeater for login/register fields.
	const fieldRepeater = (attrKey, types, isRegisterRep) => {
		const items = Array.isArray(attributes[attrKey]) ? attributes[attrKey] : [];
		const update = (i, key, val) => setAttributes({ [attrKey]: items.map((it, idx) => (idx === i ? { ...it, [key]: val } : it)) });
		const add = () => setAttributes({ [attrKey]: [...items, { type: types[0].value, label: '', placeholder: '', icon: '', isRequired: true, staticText: '', metaKey: '', minLength: '', maxLength: '', width: '' }] });
		const remove = (i) => setAttributes({ [attrKey]: items.filter((_, idx) => idx !== i) });
		const move = (i, dir) => {
			const t = i + dir;
			if (t < 0 || t >= items.length) return;
			const next = items.slice();
			const [m] = next.splice(i, 1);
			next.splice(t, 0, m);
			setAttributes({ [attrKey]: next });
		};
		return (
			<>
				{items.map((item, index) => (
					<div className="eelfg-lr-repeater-item" key={index}>
						<div className="eelfg-lr-repeater-head">
							<strong>{item.type || `#${index + 1}`}</strong>
							<div>
								<Button icon={ICON_UP} label={__('Move up', 'easy-elements-for-gutenberg')} onClick={() => move(index, -1)} disabled={index === 0} size="small" />
								<Button icon={ICON_DOWN} label={__('Move down', 'easy-elements-for-gutenberg')} onClick={() => move(index, 1)} disabled={index === items.length - 1} size="small" />
								<Button icon={ICON_TRASH} label={__('Remove', 'easy-elements-for-gutenberg')} onClick={() => remove(index)} isDestructive size="small" />
							</div>
						</div>
						<SelectControl label={__('Type', 'easy-elements-for-gutenberg')} value={item.type} options={types} onChange={(v) => update(index, 'type', v)} __next40pxDefaultSize __nextHasNoMarginBottom />
						{item.type === 'static_text' ? (
							<TextareaControl label={__('Static Text', 'easy-elements-for-gutenberg')} value={item.staticText || ''} onChange={(v) => update(index, 'staticText', v)} __nextHasNoMarginBottom />
						) : (
							<>
								{isRegisterRep && item.type === 'custom' && <TextControl label={__('Meta Key', 'easy-elements-for-gutenberg')} value={item.metaKey || ''} onChange={(v) => update(index, 'metaKey', v)} __next40pxDefaultSize __nextHasNoMarginBottom />}
								<TextControl label={__('Label', 'easy-elements-for-gutenberg')} value={item.label || ''} onChange={(v) => update(index, 'label', v)} __next40pxDefaultSize __nextHasNoMarginBottom />
								<TextControl label={__('Placeholder', 'easy-elements-for-gutenberg')} value={item.placeholder || ''} onChange={(v) => update(index, 'placeholder', v)} __next40pxDefaultSize __nextHasNoMarginBottom />
								<IconPicker label={__('Icon', 'easy-elements-for-gutenberg')} value={item.icon || ''} onChange={(v) => update(index, 'icon', v)} />
								{isRegisterRep && (
									<>
										<TextControl label={__('Min Length', 'easy-elements-for-gutenberg')} type="number" value={item.minLength || ''} onChange={(v) => update(index, 'minLength', v)} __next40pxDefaultSize __nextHasNoMarginBottom />
										<TextControl label={__('Max Length', 'easy-elements-for-gutenberg')} type="number" value={item.maxLength || ''} onChange={(v) => update(index, 'maxLength', v)} __next40pxDefaultSize __nextHasNoMarginBottom />
										<TextControl label={__('Width (e.g. 50%)', 'easy-elements-for-gutenberg')} value={item.width || ''} onChange={(v) => update(index, 'width', v)} __next40pxDefaultSize __nextHasNoMarginBottom />
									</>
								)}
								<ToggleControl label={__('Required', 'easy-elements-for-gutenberg')} checked={!!item.isRequired} onChange={(v) => update(index, 'isRequired', v)} __nextHasNoMarginBottom />
							</>
						)}
					</div>
				))}
				<Button variant="primary" onClick={add} icon={ICON_ADD}>{__('Add Field', 'easy-elements-for-gutenberg')}</Button>
			</>
		);
	};

	const regOpt = (label, value) => (
		<CheckboxControl
			label={label}
			checked={(attributes.registerOptions || []).includes(value)}
			onChange={(checked) => {
				const cur = attributes.registerOptions || [];
				setAttributes({ registerOptions: checked ? [...new Set([...cur, value])] : cur.filter((v) => v !== value) });
			}}
			__nextHasNoMarginBottom
		/>
	);

	const STATE_TABS = [
		{ name: 'normal', title: __('Normal', 'easy-elements-for-gutenberg') },
		{ name: 'hover', title: __('Hover', 'easy-elements-for-gutenberg') },
	];

	return (
		<div {...useBlockProps()}>
			<InspectorControls>
				<PanelBody title={__('Form', 'easy-elements-for-gutenberg')} initialOpen={true}>
					<SelectControl
						label={__('Type', 'easy-elements-for-gutenberg')}
						value={formType}
						options={[
							{ label: __('Login', 'easy-elements-for-gutenberg'), value: 'login' },
							{ label: __('Register', 'easy-elements-for-gutenberg'), value: 'register' },
							{ label: __('Both', 'easy-elements-for-gutenberg'), value: 'both' },
						]}
						onChange={(v) => setAttributes({ formType: v })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					{formType === 'both' && (
						<SelectControl
							label={__('Show First', 'easy-elements-for-gutenberg')}
							value={attributes.defaultForm}
							options={[
								{ label: __('Login', 'easy-elements-for-gutenberg'), value: 'login' },
								{ label: __('Register', 'easy-elements-for-gutenberg'), value: 'register' },
							]}
							onChange={(v) => setAttributes({ defaultForm: v })}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					)}
					{isLogin && txt(__('Redirect Link After Login', 'easy-elements-for-gutenberg'), 'redirectAfterLogin')}
					{isLogin && <ToggleControl label={__('Show Password Reset Link', 'easy-elements-for-gutenberg')} checked={attributes.showPasswordResetLink} onChange={(v) => setAttributes({ showPasswordResetLink: v })} __nextHasNoMarginBottom />}
					{isLogin && <ToggleControl label={__('Show Remember Me', 'easy-elements-for-gutenberg')} checked={attributes.rememberMe} onChange={(v) => setAttributes({ rememberMe: v })} __nextHasNoMarginBottom />}
					<ToggleControl label={__('Show Ajax Loader', 'easy-elements-for-gutenberg')} checked={attributes.showAjaxLoader} onChange={(v) => setAttributes({ showAjaxLoader: v })} __nextHasNoMarginBottom />
					{isRegister && (
						<>
							<Divider />
							<p className="eelfg-lr-note">{__('Register Actions', 'easy-elements-for-gutenberg')}</p>
							{regOpt(__('Notify user by email', 'easy-elements-for-gutenberg'), 'notify_email')}
							{regOpt(__('Auto login', 'easy-elements-for-gutenberg'), 'auto_login')}
							{regOpt(__('Redirect', 'easy-elements-for-gutenberg'), 'redirect')}
							{(attributes.registerOptions || []).includes('redirect') && txt(__('Redirect Link After Registration', 'easy-elements-for-gutenberg'), 'redirectAfterRegister')}
							<ToggleControl label={__('Send Admin Notification', 'easy-elements-for-gutenberg')} checked={attributes.notifyAdminEmail} onChange={(v) => setAttributes({ notifyAdminEmail: v })} __nextHasNoMarginBottom />
							<Divider />
							<ToggleControl label={__('Enable Math Captcha', 'easy-elements-for-gutenberg')} checked={attributes.enableMathCaptcha} onChange={(v) => setAttributes({ enableMathCaptcha: v })} __nextHasNoMarginBottom />
							{attributes.enableMathCaptcha && area(__('Captcha Error Message', 'easy-elements-for-gutenberg'), 'mathCaptchaError')}
						</>
					)}
				</PanelBody>

				<PanelBody title={__('Fields', 'easy-elements-for-gutenberg')} initialOpen={false}>
					<ToggleControl label={__('Show Label', 'easy-elements-for-gutenberg')} checked={attributes.showLabel} onChange={(v) => setAttributes({ showLabel: v })} disabled={attributes.showIcon} __nextHasNoMarginBottom />
					<ToggleControl label={__('Show Icon', 'easy-elements-for-gutenberg')} checked={attributes.showIcon} onChange={(v) => setAttributes({ showIcon: v })} __nextHasNoMarginBottom />
					<Divider />
					{isLogin && (
						<>
							<p className="eelfg-lr-note">{__('Login Fields', 'easy-elements-for-gutenberg')}</p>
							{fieldRepeater('loginFields', LOGIN_TYPES, false)}
							<Divider />
							{attributes.showPasswordResetLink && txt(__('Password Reset Text', 'easy-elements-for-gutenberg'), 'passwordResetText')}
							{attributes.rememberMe && txt(__('Remember Text', 'easy-elements-for-gutenberg'), 'rememberLabelText')}
						</>
					)}
					{isRegister && (
						<>
							<Divider />
							<p className="eelfg-lr-note">{__('Register Fields', 'easy-elements-for-gutenberg')}</p>
							{fieldRepeater('registerFields', REGISTER_TYPES, true)}
						</>
					)}
				</PanelBody>

				<PanelBody title={__('Submit', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{isLogin && txt(__('Login Button Text', 'easy-elements-for-gutenberg'), 'submitButtonText')}
					{isRegister && txt(__('Register Button Text', 'easy-elements-for-gutenberg'), 'registerButtonText')}
					<IconPicker label={__('Icon', 'easy-elements-for-gutenberg')} value={attributes.submitButtonIcon || ''} onChange={(v) => setAttributes({ submitButtonIcon: v })} />
					<SelectControl label={__('Icon Position', 'easy-elements-for-gutenberg')} value={attributes.submitButtonIconPosition} options={[{ label: __('Before Text', 'easy-elements-for-gutenberg'), value: 'before' }, { label: __('After Text', 'easy-elements-for-gutenberg'), value: 'after' }]} onChange={(v) => setAttributes({ submitButtonIconPosition: v })} __next40pxDefaultSize __nextHasNoMarginBottom />
				</PanelBody>

				<PanelBody title={__('Messages', 'easy-elements-for-gutenberg')} initialOpen={false}>
					<ToggleControl label={__('Show Success Message', 'easy-elements-for-gutenberg')} checked={attributes.showSuccessMessage} onChange={(v) => setAttributes({ showSuccessMessage: v })} __nextHasNoMarginBottom />
					{isLogin && area(__('Login Success Message', 'easy-elements-for-gutenberg'), 'loginSuccessMessage')}
					{isRegister && area(__('Register Success Message', 'easy-elements-for-gutenberg'), 'registerSuccessMessage')}
					<ToggleControl label={__('Show Failed Message', 'easy-elements-for-gutenberg')} checked={attributes.showErrorMessage} onChange={(v) => setAttributes({ showErrorMessage: v })} __nextHasNoMarginBottom />
					{isLogin && area(__('Login Failed Message', 'easy-elements-for-gutenberg'), 'loginErrorMessage')}
					{isRegister && area(__('Register Failed Message', 'easy-elements-for-gutenberg'), 'registerErrorMessage')}
					{area(__('Already Logged In Message', 'easy-elements-for-gutenberg'), 'alreadyLoggedInMessage')}
					{area(__('Empty Field Message', 'easy-elements-for-gutenberg'), 'emptyErrorMessage')}
					{isRegister && area(__('Confirm Password Message', 'easy-elements-for-gutenberg'), 'confirmPassErrorMessage')}
					{isRegister && area(__('Min Characters Message', 'easy-elements-for-gutenberg'), 'minLengthErrorMessage')}
					{isRegister && area(__('Max Characters Message', 'easy-elements-for-gutenberg'), 'maxLengthErrorMessage')}
				</PanelBody>
			</InspectorControls>

			<InspectorControls group="styles">
				<PanelBody title={__('Fields', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Text Color', 'easy-elements-for-gutenberg'), 'fieldInputColor')}
					{color(__('Placeholder Color', 'easy-elements-for-gutenberg'), 'fieldPlaceholderColor')}
					{color(__('Background Color', 'easy-elements-for-gutenberg'), 'fieldBgColor')}
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'fieldTypography')}
					{box(__('Padding', 'easy-elements-for-gutenberg'), 'fieldPadding')}
					{box(__('Margin', 'easy-elements-for-gutenberg'), 'fieldMargin')}
					{box(__('Border Radius', 'easy-elements-for-gutenberg'), 'fieldRadius')}
					{border(__('Border', 'easy-elements-for-gutenberg'), 'fieldBorder')}
					{shadow(__('Box Shadow', 'easy-elements-for-gutenberg'), 'fieldBoxShadow')}
					{attributes.showIcon && (
						<>
							<Divider />
							{color(__('Icon Color', 'easy-elements-for-gutenberg'), 'iconColor')}
							{num(__('Icon Size (px)', 'easy-elements-for-gutenberg'), 'iconSize')}
						</>
					)}
				</PanelBody>

				{attributes.showLabel && (
					<PanelBody title={__('Label', 'easy-elements-for-gutenberg')} initialOpen={false}>
						{color(__('Color', 'easy-elements-for-gutenberg'), 'labelColor')}
						{typo(__('Typography', 'easy-elements-for-gutenberg'), 'labelTypography')}
						{box(__('Margin', 'easy-elements-for-gutenberg'), 'labelMargin')}
					</PanelBody>
				)}

				<PanelBody title={__('Links', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{color(__('Color', 'easy-elements-for-gutenberg'), 'linksColor')}
					{color(__('Hover Color', 'easy-elements-for-gutenberg'), 'linksHoverColor')}
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'linksTypography')}
				</PanelBody>

				<PanelBody title={__('Submit', 'easy-elements-for-gutenberg')} initialOpen={false}>
					<TabPanel tabs={STATE_TABS}>
						{(tab) =>
							tab.name === 'normal' ? (
								<>
									{color(__('Color', 'easy-elements-for-gutenberg'), 'submitColor')}
									{color(__('Background', 'easy-elements-for-gutenberg'), 'submitBg')}
								</>
							) : (
								<>
									{color(__('Color', 'easy-elements-for-gutenberg'), 'submitHoverColor')}
									{color(__('Background', 'easy-elements-for-gutenberg'), 'submitHoverBg')}
								</>
							)
						}
					</TabPanel>
					<Divider />
					{box(__('Padding', 'easy-elements-for-gutenberg'), 'submitPadding')}
					{box(__('Margin', 'easy-elements-for-gutenberg'), 'submitMargin')}
					{num(__('Width (%)', 'easy-elements-for-gutenberg'), 'submitWidth')}
					{box(__('Border Radius', 'easy-elements-for-gutenberg'), 'submitRadius')}
					{border(__('Border', 'easy-elements-for-gutenberg'), 'submitBorder')}
					{shadow(__('Box Shadow', 'easy-elements-for-gutenberg'), 'submitBoxShadow')}
					{num(__('Icon Size (px)', 'easy-elements-for-gutenberg'), 'submitIconSize')}
				</PanelBody>

				<PanelBody title={__('Message', 'easy-elements-for-gutenberg')} initialOpen={false}>
					{typo(__('Typography', 'easy-elements-for-gutenberg'), 'messageTypography')}
					{box(__('Padding', 'easy-elements-for-gutenberg'), 'msgPadding')}
					{box(__('Margin', 'easy-elements-for-gutenberg'), 'msgMargin')}
					{color(__('Success Color', 'easy-elements-for-gutenberg'), 'successMessageColor')}
					{color(__('Failed Color', 'easy-elements-for-gutenberg'), 'errorMessageColor')}
				</PanelBody>
			</InspectorControls>

			<div ref={previewRef}>
				<ServerSideRender block="easy-elements-for-gutenberg/login-register" attributes={attributes} httpMethod="POST" />
			</div>
		</div>
	);
}
