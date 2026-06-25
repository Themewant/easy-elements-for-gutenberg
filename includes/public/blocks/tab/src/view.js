/**
 * Tabs — front-end behaviour.
 *
 * Behaviour ported from the Elementor widget script
 * (easy-elements/widgets/tab/js/tab.js). Rewritten as scoped, dependency-free
 * JS so it runs on any front-end page (the Elementor version hooked into
 * `elementor/frontend/init` and used jQuery).
 */
(function () {
	'use strict';

	var DURATION = 300;

	function slideOut(el) {
		return new Promise(function (resolve) {
			el.style.transition = 'opacity ' + DURATION + 'ms, transform ' + DURATION + 'ms';
			el.style.opacity = 0;
			el.style.transform = 'translateY(20px)';
			window.setTimeout(function () {
				el.style.display = 'none';
				resolve();
			}, DURATION);
		});
	}

	function slideIn(el) {
		return new Promise(function (resolve) {
			el.style.display = 'block';
			el.style.opacity = 0;
			el.style.transform = 'translateY(20px)';
			el.style.transition = 'opacity ' + DURATION + 'ms, transform ' + DURATION + 'ms';
			window.setTimeout(function () {
				el.style.opacity = 1;
				el.style.transform = 'translateY(0)';
			}, 10);
			window.setTimeout(resolve, DURATION);
		});
	}

	function initTabs(wrap) {
		if (wrap.dataset.eelfgTabInit === '1') {
			return;
		}
		wrap.dataset.eelfgTabInit = '1';

		var wrapper = wrap.querySelector('.eelfg-tabs-wrapper');
		if (!wrapper) {
			return;
		}

		var tabs = Array.prototype.slice.call(wrapper.querySelectorAll('.eelfg-tab-titles li'));
		var contents = Array.prototype.slice.call(wrapper.querySelectorAll('.eelfg-tab-content'));

		if (!tabs.length || !contents.length) {
			return;
		}

		tabs.forEach(function (tab) {
			tab.addEventListener('click', function () {
				var targetContent = wrapper.querySelector('#' + this.dataset.tab);
				if (!targetContent) {
					return;
				}

				var currentContent = contents.find(function (c) {
					return c.style.display !== 'none' && c.classList.contains('active');
				});

				if (currentContent === targetContent) {
					return;
				}

				tabs.forEach(function (t) {
					t.classList.remove('active');
				});
				this.classList.add('active');

				var swap = function () {
					contents.forEach(function (c) {
						c.classList.remove('active');
					});
					slideIn(targetContent).then(function () {
						targetContent.classList.add('active');
					});
				};

				if (currentContent) {
					slideOut(currentContent).then(swap);
				} else {
					swap();
				}
			});
		});

		// Initial state: honour the server-rendered active item, else the first.
		var activeIndex = tabs.findIndex(function (t) {
			return t.classList.contains('active');
		});
		if (activeIndex < 0) {
			activeIndex = 0;
		}

		tabs.forEach(function (t) {
			t.classList.remove('active');
		});
		contents.forEach(function (c) {
			c.style.display = 'none';
			c.style.opacity = 0;
			c.style.transform = 'translateY(20px)';
			c.classList.remove('active');
		});

		tabs[activeIndex].classList.add('active');
		if (contents[activeIndex]) {
			contents[activeIndex].style.display = 'block';
			contents[activeIndex].style.opacity = 1;
			contents[activeIndex].style.transform = 'translateY(0)';
			contents[activeIndex].classList.add('active');
		}
	}

	function initAll() {
		var wraps = document.querySelectorAll('.eelfg-tab-block-wrap');
		Array.prototype.forEach.call(wraps, initTabs);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initAll);
	} else {
		initAll();
	}
})();
