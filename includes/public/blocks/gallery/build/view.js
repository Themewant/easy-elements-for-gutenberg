/******/ (() => { // webpackBootstrap
/*!****************************************************!*\
  !*** ./includes/public/blocks/gallery/src/view.js ***!
  \****************************************************/
/**
 * Simple Gallery — front-end lightbox.
 *
 * Behaviour ported from the Elementor widget script
 * (easy-elements/widgets/gallery/js/simple-gallery.js). Rewritten as scoped,
 * dependency-free JS so it runs on any front-end page where the block appears
 * (the Elementor version hooked into `elementor/frontend/init`).
 */
(function () {
  'use strict';

  function initGallery(wrap) {
    if (wrap.dataset.eelfgGalleryInit === '1') {
      return;
    }
    wrap.dataset.eelfgGalleryInit = '1';
    var grid = wrap.querySelector('.eelfg-gallery-grid.eelfg-popup-enabled');
    var lightbox = wrap.querySelector('.eelfg-lightbox-gallery');
    if (!grid || !lightbox) {
      return;
    }
    var links = Array.prototype.slice.call(grid.querySelectorAll('.eelfg-popup-link'));
    var image = lightbox.querySelector('.eelfg-lightbox-image');
    var nextBtn = lightbox.querySelector('.eelfg-next');
    var prevBtn = lightbox.querySelector('.eelfg-prev');
    var closeBtn = lightbox.querySelector('.eelfg-close');
    if (!links.length || !image) {
      return;
    }
    var current = 0;
    function open(index) {
      current = index;
      image.setAttribute('src', links[current].getAttribute('href'));
      lightbox.classList.add('is-open');
    }
    function close() {
      lightbox.classList.remove('is-open');
    }
    function show(index) {
      current = (index + links.length) % links.length;
      image.setAttribute('src', links[current].getAttribute('href'));
    }
    links.forEach(function (link, index) {
      link.addEventListener('click', function (e) {
        e.preventDefault();
        open(index);
      });
    });
    if (nextBtn) {
      nextBtn.addEventListener('click', function () {
        show(current + 1);
      });
    }
    if (prevBtn) {
      prevBtn.addEventListener('click', function () {
        show(current - 1);
      });
    }
    if (closeBtn) {
      closeBtn.addEventListener('click', close);
    }
    lightbox.addEventListener('click', function (e) {
      if (e.target === lightbox || e.target === closeBtn) {
        close();
      }
    });
    document.addEventListener('keydown', function (e) {
      if (!lightbox.classList.contains('is-open')) {
        return;
      }
      if (e.key === 'ArrowRight') {
        show(current + 1);
      } else if (e.key === 'ArrowLeft') {
        show(current - 1);
      } else if (e.key === 'Escape') {
        close();
      }
    });
  }
  function initAll() {
    var wraps = document.querySelectorAll('.eelfg-gallery-block-wrap');
    Array.prototype.forEach.call(wraps, initGallery);
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
  } else {
    initAll();
  }
})();
/******/ })()
;
//# sourceMappingURL=view.js.map