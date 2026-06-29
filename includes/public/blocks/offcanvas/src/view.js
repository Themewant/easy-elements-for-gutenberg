/**
 * Offcanvas — front-end behaviour.
 *
 * Ported from the Elementor widget's jQuery script
 * (easy-elements/widgets/offcanvas/js/offcanvas.js). Rewritten as scoped,
 * dependency-free JS.
 */
(function () {
	'use strict';

	var ACTIVE = 'eelfg-active';
	var BODY_ACTIVE = 'eelfg-offcanvas-active';

	function closePanel(panel) {
		panel.classList.remove(ACTIVE);
		if (!document.querySelector('.eelfg-offcanvas.' + ACTIVE)) {
			document.body.classList.remove(BODY_ACTIVE);
		}
	}

	function openPanel(panel) {
		// Close any other open panels first.
		Array.prototype.forEach.call(document.querySelectorAll('.eelfg-offcanvas.' + ACTIVE), function (other) {
			if (other !== panel) {
				other.classList.remove(ACTIVE);
			}
		});
		panel.classList.add(ACTIVE);
		document.body.classList.add(BODY_ACTIVE);
	}

	function onToggleClick(e) {
		var toggle = e.target.closest('.eelfg-offcanvas-toggle');
		if (!toggle) {
			return;
		}
		e.preventDefault();
		e.stopPropagation();
		var target = toggle.getAttribute('data-target');
		var panel = target ? document.querySelector(target) : null;
		if (!panel) {
			return;
		}
		if (panel.classList.contains(ACTIVE)) {
			closePanel(panel);
		} else {
			openPanel(panel);
		}
	}

	function init() {
		document.addEventListener('click', onToggleClick);

		// Click inside the panel shouldn't close it; click on the overlay / outside should.
		document.addEventListener('click', function (e) {
			if (e.target.closest('.eelfg-offcanvas-toggle')) {
				return;
			}
			var insidePanel = e.target.closest('.eelfg-offcanvas-panel');
			var open = document.querySelectorAll('.eelfg-offcanvas.' + ACTIVE);
			Array.prototype.forEach.call(open, function (panel) {
				// If the click is inside this panel's content, keep it open.
				if (insidePanel && panel.contains(insidePanel)) {
					return;
				}
				closePanel(panel);
			});
		});

		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') {
				Array.prototype.forEach.call(document.querySelectorAll('.eelfg-offcanvas.' + ACTIVE), closePanel);
			}
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
