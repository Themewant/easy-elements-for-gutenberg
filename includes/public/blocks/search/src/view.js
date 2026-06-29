/**
 * Search — front-end behaviour (popup lightbox).
 *
 * Ported from the Elementor widget's jQuery script
 * (easy-elements/widgets/search/js/search.js). Rewritten as scoped,
 * dependency-free JS.
 */
(function () {
	'use strict';

	function init() {
		var wraps = document.querySelectorAll('.eelfg-search-block-wrap');

		Array.prototype.forEach.call(wraps, function (wrap) {
			if (wrap.dataset.eelfgSearchInit === '1') {
				return;
			}
			wrap.dataset.eelfgSearchInit = '1';

			var box = wrap.querySelector('.eelfg-search-lightbox');
			if (!box) {
				return;
			}
			var openBtn = wrap.querySelector('.eelfg-search-open-btn');
			var closers = wrap.querySelectorAll('.eelfg-search-close-btn, .eelfg-search-overlay');
			var field = box.querySelector('.eelfg-search-field');

			if (openBtn) {
				openBtn.addEventListener('click', function (e) {
					e.preventDefault();
					e.stopPropagation();
					box.classList.add('eelfg-lightbox');
					if (field) {
						window.setTimeout(function () { field.focus(); }, 400);
					}
				});
			}

			Array.prototype.forEach.call(closers, function (closer) {
				closer.addEventListener('click', function (e) {
					e.preventDefault();
					e.stopPropagation();
					box.classList.remove('eelfg-lightbox');
				});
			});

			// Clicking inside the content shouldn't close the popup.
			var content = box.querySelector('.eelfg-search-content');
			if (content) {
				content.addEventListener('click', function (e) {
					e.stopPropagation();
				});
			}

			document.addEventListener('keydown', function (e) {
				if (e.key === 'Escape') {
					box.classList.remove('eelfg-lightbox');
				}
			});
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
