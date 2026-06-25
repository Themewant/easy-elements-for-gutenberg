/**
 * Testimonials Grid — "view all" reveal.
 *
 * Ported from the Elementor widget script
 * (easy-elements/widgets/testimonials-grid/js/testimonial.js). Rewritten as
 * scoped, dependency-free JS (the Elementor version used jQuery).
 */
(function () {
	'use strict';

	function init() {
		if (document.body.dataset.eelfgTstml === '1') {
			return;
		}
		document.body.dataset.eelfgTstml = '1';

		document.addEventListener('click', function (e) {
			var btn = e.target.closest('.eelfg-testimonial-more-btn');
			if (!btn) {
				return;
			}
			e.preventDefault();
			var wrap = btn.closest('.eelfg-testimonial');
			if (!wrap) {
				return;
			}
			wrap.querySelectorAll('.eelfg-hidden-testimonial').forEach(function (el) {
				el.classList.remove('eelfg-hidden-testimonial');
			});
			wrap.classList.remove('eelfg-testifull-overlay');
			btn.style.display = 'none';
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
