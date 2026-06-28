/**
 * Image Comparison — front-end behaviour.
 */
import { initComparison } from './comparison';

(function () {
	'use strict';

	function initAll() {
		var nodes = document.querySelectorAll('.eelfg-image-comparison-block-wrap .eelfg-comparison-container');
		Array.prototype.forEach.call(nodes, initComparison);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initAll);
	} else {
		initAll();
	}
})();
