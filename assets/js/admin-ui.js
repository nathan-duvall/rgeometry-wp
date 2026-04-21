/**
 * RGeometry — Admin UI enhancements
 *
 *   1. Inject Lucide icon badge into each RGeometry field group's title bar.
 *   2. Make the entire header row a click target (not just the chevron button).
 *   3. Accordion behavior on screens with multiple rg- groups (Home edit):
 *      - First visit: all collapsed
 *      - Open one, auto-close the others
 *      - Remember last opened per-user via localStorage
 *   4. On screens with a single rg- group (Theme Settings sub-pages):
 *      - Leave the group open, no accordion, no storage
 */
(function ($) {
	'use strict';

	if (typeof rgAdminUIData === 'undefined') return;

	var GROUPS        = rgAdminUIData.groups || {};
	var STORAGE_KEY   = rgAdminUIData.storageKey || 'rgAdminUI.lastOpen';
	var ACCORDION_ALL = !!rgAdminUIData.accordionAll;

	function rgPostboxes() {
		return $('.postbox[id^="acf-group_rgeometry_"]');
	}
	function hasMultipleGroups() {
		return rgPostboxes().length > 1;
	}

	function groupKeyFromPostbox($box) {
		var id = $box.attr('id') || '';
		if (id.indexOf('acf-group_rgeometry_') !== 0) return '';
		return id.replace(/^acf-/, '');
	}

	function decorateHeader($box) {
		var key = groupKeyFromPostbox($box);
		if (!key || !GROUPS[key]) return;

		var $header = $box.find('> .postbox-header');
		if (!$header.length || $header.data('rgDecorated')) return;

		if (GROUPS[key].svg) {
			var $icon = $('<span class="rg-admin-ui__icon" aria-hidden="true">' + GROUPS[key].svg + '</span>');
			$header.prepend($icon);
		}
		$header.data('rgDecorated', true);
	}

	function closeBox($box) {
		$box.addClass('closed');
		$box.find('> .postbox-header .handlediv').attr('aria-expanded', 'false');
	}
	function openBox($box) {
		$box.removeClass('closed');
		$box.find('> .postbox-header .handlediv').attr('aria-expanded', 'true');
	}

	function closeAllExcept($keep) {
		rgPostboxes().each(function () {
			var $b = $(this);
			if ($keep && $b.is($keep)) return;
			closeBox($b);
		});
	}

	function rememberOpen($box) {
		var key = groupKeyFromPostbox($box);
		if (!key) return;
		try { localStorage.setItem(STORAGE_KEY, key); } catch (e) {}
	}
	function forgetIfMatches($box) {
		var key = groupKeyFromPostbox($box);
		if (!key) return;
		try {
			if (localStorage.getItem(STORAGE_KEY) === key) {
				localStorage.removeItem(STORAGE_KEY);
			}
		} catch (e) {}
	}

	function restoreState() {
		var $boxes = rgPostboxes();
		if (!$boxes.length) return;

		// Single group (Theme Settings sub-pages): leave open, skip accordion.
		if (!hasMultipleGroups()) {
			$boxes.each(function () { openBox($(this)); });
			return;
		}

		// Multiple groups: collapse all, then restore last-opened if any.
		$boxes.each(function () { closeBox($(this)); });
		var key;
		try { key = localStorage.getItem(STORAGE_KEY); } catch (e) { key = null; }
		if (key) {
			var $match = $('#acf-' + CSS.escape(key));
			if ($match.length) openBox($match);
		}
	}

	function wireClicks() {
		// Full-header click = toggle. Exclude native buttons/inputs/links
		// (gear menu, order arrows, chevron) so those keep their own behavior.
		$(document).on('click.rgAdminUI', '.postbox[id^="acf-group_rgeometry_"] > .postbox-header', function (e) {
			if ($(e.target).closest('.handle-actions, .handlediv, button, a, input, select, textarea').length) return;

			var $box = $(this).closest('.postbox');
			var wasClosed = $box.hasClass('closed');
			if (wasClosed) openBox($box); else closeBox($box);

			if (!wasClosed) {
				forgetIfMatches($box);
				return;
			}
			if (hasMultipleGroups() && ACCORDION_ALL) closeAllExcept($box);
			rememberOpen($box);
		});

		// WP's own chevron button toggles natively. Run accordion logic after.
		$(document).on('click.rgAdminUI', '.postbox[id^="acf-group_rgeometry_"] > .postbox-header .handlediv', function () {
			var $box = $(this).closest('.postbox');
			setTimeout(function () {
				if ($box.hasClass('closed')) {
					forgetIfMatches($box);
				} else {
					if (hasMultipleGroups() && ACCORDION_ALL) closeAllExcept($box);
					rememberOpen($box);
				}
			}, 0);
		});
	}

	function boot() {
		rgPostboxes().each(function () { decorateHeader($(this)); });
		wireClicks();
		restoreState();
	}

	$(function () {
		boot();
		setTimeout(boot, 300); // catch any late-rendered ACF postboxes
	});

})(jQuery);
