/**
 * RGeometry — Front-end interactions
 *
 * Everything ported from the Lovable source lives here as vanilla JS so the
 * theme has no build step.
 *
 *   - ScrollReveal  (replaces Framer Motion useInView pattern)
 *   - Smooth scroll (replaces the scrollIntoView onClick handlers)
 *   - Navbar scroll state + mobile toggle
 *   - Projects carousel prev/next
 *   - Testimonials rotating carousel
 *   - Contact form AJAX submit
 */
(function () {
	'use strict';

	var REDUCED_MOTION = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	/* ---------- ScrollReveal ---------- */
	function applyRevealOptions(el) {
		var delay = el.getAttribute('data-reveal-delay');
		if (delay) el.style.setProperty('--rg-reveal-delay', parseFloat(delay) + 's');
		var y = el.getAttribute('data-reveal-y');
		if (y) el.style.transform = 'translateY(' + parseInt(y, 10) + 'px)';
	}
	function initReveal() {
		var targets = document.querySelectorAll('[data-reveal]');
		if (!targets.length) return;
		if (REDUCED_MOTION || !('IntersectionObserver' in window)) {
			Array.prototype.forEach.call(targets, function (el) { el.classList.add('is-visible'); });
			return;
		}
		Array.prototype.forEach.call(targets, applyRevealOptions);
		var io = new IntersectionObserver(function (entries, obs) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) {
					entry.target.classList.add('is-visible');
					obs.unobserve(entry.target);
				}
			});
		}, { root: null, rootMargin: '-15% 0px', threshold: 0 });
		Array.prototype.forEach.call(targets, function (el) { io.observe(el); });
	}

	/* ---------- Smooth scroll for in-page anchors ---------- */
	function initSmoothScroll() {
		document.addEventListener('click', function (e) {
			var link = e.target.closest('a[href^="#"]');
			if (!link) return;
			var href = link.getAttribute('href');
			if (!href || href === '#' || href === '#/') return;
			var target = document.getElementById(href.slice(1));
			if (!target) return;
			e.preventDefault();
			target.scrollIntoView({ behavior: 'smooth', block: 'start' });
			// Close mobile nav if open.
			var nav = document.querySelector('[data-nav]');
			var burger = document.querySelector('[data-nav-toggle]');
			var mobile = document.querySelector('[data-nav-mobile]');
			if (nav && burger && mobile) {
				burger.classList.remove('is-open');
				burger.setAttribute('aria-expanded', 'false');
				mobile.setAttribute('hidden', '');
			}
		});
	}

	/* ---------- Navbar ---------- */
	function initNavbar() {
		var nav = document.querySelector('[data-nav]');
		if (!nav) return;
		var onScroll = function () {
			if (window.scrollY > 80) nav.classList.add('is-scrolled');
			else nav.classList.remove('is-scrolled');
		};
		window.addEventListener('scroll', onScroll, { passive: true });
		onScroll();

		var burger = nav.querySelector('[data-nav-toggle]');
		var mobile = nav.querySelector('[data-nav-mobile]');
		if (burger && mobile) {
			burger.addEventListener('click', function () {
				var open = burger.classList.toggle('is-open');
				burger.setAttribute('aria-expanded', open ? 'true' : 'false');
				if (open) mobile.removeAttribute('hidden');
				else mobile.setAttribute('hidden', '');
			});
		}
	}

	/* ---------- Projects carousel ---------- */
	function initProjects() {
		var track = document.querySelector('[data-projects-track]');
		if (!track) return;
		var prev = document.querySelector('[data-projects-prev]');
		var next = document.querySelector('[data-projects-next]');
		function scrollBy(dir) {
			var amount = track.offsetWidth * 0.45;
			track.scrollBy({ left: dir === 'left' ? -amount : amount, behavior: 'smooth' });
		}
		if (prev) prev.addEventListener('click', function () { scrollBy('left'); });
		if (next) next.addEventListener('click', function () { scrollBy('right'); });
	}

	/* ---------- Testimonials rotating carousel ---------- */
	function initTestimonials() {
		var stage = document.querySelector('[data-testimonials]');
		if (!stage) return;
		var slides = stage.querySelectorAll('.rg-testimonial');
		var dots   = stage.querySelectorAll('.rg-testimonials__dot');
		if (!slides.length) return;
		var interval = parseInt(stage.getAttribute('data-testimonials-interval') || '5500', 10);
		var current = 0;
		var timer = null;

		function go(n) {
			current = (n + slides.length) % slides.length;
			slides.forEach(function (s, i) { s.classList.toggle('is-active', i === current); });
			dots.forEach(function (d, i) { d.classList.toggle('is-active', i === current); });
		}
		function start() {
			stop();
			if (slides.length < 2 || REDUCED_MOTION) return;
			timer = setInterval(function () { go(current + 1); }, interval);
		}
		function stop() { if (timer) { clearInterval(timer); timer = null; } }

		stage.querySelector('[data-testimonials-prev]').addEventListener('click', function () { go(current - 1); start(); });
		stage.querySelector('[data-testimonials-next]').addEventListener('click', function () { go(current + 1); start(); });
		dots.forEach(function (d, i) { d.addEventListener('click', function () { go(i); start(); }); });
		stage.addEventListener('mouseenter', stop);
		stage.addEventListener('mouseleave', start);
		start();
	}

	function boot() {
		initReveal();
		initSmoothScroll();
		initNavbar();
		initProjects();
		initTestimonials();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
