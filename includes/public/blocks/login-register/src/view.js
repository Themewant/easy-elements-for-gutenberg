/**
 * Login | Register — front-end behaviour.
 *
 * Ported from the Elementor widget script
 * (easy-elements/widgets/login-register/js/login-register.js). Rewritten as
 * scoped, dependency-free JS. AJAX endpoint + nonce are read from data
 * attributes on each form (data-ajaxurl / data-nonce).
 */
(function () {
	'use strict';

	function post(url, data) {
		var body = new URLSearchParams();
		Object.keys(data).forEach(function (k) {
			var v = data[k];
			if (v !== null && typeof v === 'object') {
				// Flatten one level for custom_meta[key].
				Object.keys(v).forEach(function (sub) {
					body.append(k + '[' + sub + ']', v[sub]);
				});
			} else {
				body.append(k, v == null ? '' : v);
			}
		});
		return fetch(url, {
			method: 'POST',
			credentials: 'same-origin',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: body.toString(),
		}).then(function (r) { return r.json(); });
	}

	function showMsg(form, type, text) {
		var el = form.querySelector('.eel-form-status .eel-form-' + type + '-msg, .eelfg-form-status .eelfg-form-' + type + '-msg');
		if (!el) return;
		if (text) el.textContent = text;
		el.style.display = 'block';
	}
	function hideMsgs(form) {
		form.querySelectorAll('.eelfg-form-status p').forEach(function (p) { p.style.display = 'none'; });
	}
	function loader(form, on) {
		var l = form.querySelector('.eelfg-form-ajax-loader');
		if (l) l.classList.toggle('show', !!on);
	}

	function handleLogin(form) {
		hideMsgs(form);
		loader(form, true);
		var redirect = (form.querySelector('input[name="login_redirect_link"]') || {}).value || '';
		var data = {
			action: 'eelfg_login',
			nonce: form.getAttribute('data-nonce'),
			user: (form.querySelector('#eelfg_username') || {}).value || '',
			pwd: (form.querySelector('#eelfg_password') || {}).value || '',
			remember: form.querySelector('#eel_rememberme') && form.querySelector('#eel_rememberme').checked ? 1 : 0,
		};
		post(form.getAttribute('data-ajaxurl'), data).then(function (res) {
			loader(form, false);
			if (res && res.success) {
				showMsg(form, 'success');
				window.setTimeout(function () { if (redirect) window.location.replace(redirect); }, 1500);
			} else {
				showMsg(form, 'error', res && res.data && res.data.msg ? res.data.msg : '');
			}
		}).catch(function () {
			loader(form, false);
			showMsg(form, 'error');
		});
	}

	function validateRegister(form) {
		var emptyMsg = (form.querySelector('input[name="empty_error_msg"]') || {}).value || 'This field is required.';
		var minMsg = (form.querySelector('input[name="min_length_error_msg"]') || {}).value || '';
		var maxMsg = (form.querySelector('input[name="max_length_error_msg"]') || {}).value || '';
		var confirmMsg = (form.querySelector('input[name="confirm_pass_error_msg"]') || {}).value || '';
		var ok = true;

		form.querySelectorAll('.eelfg-error-msg').forEach(function (e) { e.textContent = ''; e.style.display = 'none'; });
		form.querySelectorAll('.input-error').forEach(function (e) { e.classList.remove('input-error'); });

		form.querySelectorAll('input, textarea, select').forEach(function (input) {
			var name = input.getAttribute('name');
			var type = input.getAttribute('type');
			if (!name || type === 'hidden') return;
			var id = input.id || name;
			var errEl = form.querySelector('.eelfg-error-msg[data-error-for="' + id + '"]');
			var required = input.required || input.hasAttribute('required');
			var value = (input.value || '').trim();

			var fail = function (msg) {
				ok = false;
				input.classList.add('input-error');
				if (errEl) { errEl.textContent = msg; errEl.style.display = 'block'; }
			};

			if (type === 'checkbox') {
				if (required && !input.checked) { fail(emptyMsg); }
				return;
			}
			if (required && value === '') { fail(emptyMsg); return; }
			var min = input.getAttribute('min');
			var max = input.getAttribute('max');
			if (min && value.length > 0 && value.length < parseInt(min, 10)) { fail(minMsg.replace('{count}', min)); return; }
			if (max && value.length > parseInt(max, 10)) { fail(maxMsg.replace('{count}', max)); return; }
		});

		var pass = (form.querySelector('input[name="user_pass"]') || {}).value;
		var confirmField = form.querySelector('input[name="confirm_password"]');
		if (confirmField && pass !== confirmField.value) {
			confirmField.classList.add('input-error');
			var ce = form.querySelector('.eelfg-error-msg[data-error-for="eelfg_confirm_password"]');
			if (ce) { ce.textContent = confirmMsg; ce.style.display = 'block'; }
			ok = false;
		}
		return ok;
	}

	function handleRegister(form) {
		if (!validateRegister(form)) {
			return;
		}
		hideMsgs(form);
		loader(form, true);

		var redirect = (form.querySelector('input[name="register_redirect_link"]') || {}).value || '';
		var doRedirect = ((form.querySelector('input[name="redirect_after_registration"]') || {}).value || 'no') === 'yes';

		var data = { action: 'eelfg_register', nonce: form.getAttribute('data-nonce') };
		form.querySelectorAll('input, textarea, select').forEach(function (input) {
			var name = input.getAttribute('name');
			if (!name) return;
			if (input.getAttribute('type') === 'checkbox') {
				data[name] = input.checked ? input.value || 'yes' : '';
				return;
			}
			var m = name.match(/^custom_meta\[([^\]]+)\]$/);
			if (m) {
				if (!data.custom_meta) data.custom_meta = {};
				data.custom_meta[m[1]] = input.value;
			} else {
				data[name] = input.value;
			}
		});

		post(form.getAttribute('data-ajaxurl'), data).then(function (res) {
			loader(form, false);
			if (res && res.success) {
				showMsg(form, 'success');
				if (doRedirect && redirect) {
					window.setTimeout(function () { window.location.replace(redirect); }, 1500);
				}
			} else {
				showMsg(form, 'error', res && res.data && res.data.msg ? res.data.msg : '');
			}
		}).catch(function () {
			loader(form, false);
			showMsg(form, 'error');
		});
	}

	function init() {
		document.addEventListener('submit', function (e) {
			var login = e.target.closest('.eelfg-login-form');
			var register = e.target.closest('.eelfg-register-form');
			if (login) { e.preventDefault(); handleLogin(login); }
			else if (register) { e.preventDefault(); handleRegister(register); }
		});

		document.addEventListener('click', function (e) {
			var sw = e.target.closest('.eelfg-authentication-toggle-wrap .eelfg-form-switcher');
			if (!sw) return;
			e.preventDefault();
			var wrap = sw.closest('.eelfg-authentication-toggle-wrap');
			if (wrap) wrap.setAttribute('data-active-form', sw.getAttribute('data-switch-to'));
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
