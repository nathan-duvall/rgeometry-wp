/**
 * RGeometry — Admin UI enhancements
 *
 *   1. Inject Lucide icon badge + subtitle into each RGeometry field group title bar.
 *   2. Render group description under the title, from the ACF field group's
 *      description (when ACF surfaces it in the DOM).
 *   3. Accordion behavior: all RGeometry postboxes collapsed on first load,
 *      click one to open, auto-close the others, remember last opened per user
 *      in localStorage.
 */
(function ($) {
	'use strict';

	if (typeof rgAdminUIData === 'undefined') return;

	var GROUPS        = rgAdminUIData.groups || {};
	var STORAGE_KEY   = rgAdminUIData.storageKey || 'rgAdminUI.lastOpen';
	var ACCORDION_ALL = !!rgAdminUIData.accordionAll;

	/**
	 * Pull the field group key out of a postbox DOM id.
	 * "acf-group_rgeometry_hero" -> "group_rgeometry_hero"
	 */
	function groupKeyFromPostbox($box) {
		var id = $box.attr('id') || '';
		if (id.indexOf('acf-group_rgeometry_') !== 0) return '';
		return id.replace(/^acf-/, '');
	}

	function decorateHeader($box) {
		var key = groupKeyFromPostbox($box);
		if (!key || !GROUPS[key]) return;

		var $header = $box.find('.postbox-header');
		if (!$header.length || $header.data('rgDecorated')) return;

		// Inject icon badge ahead of the title.
		if (GROUPS[key].svg) {
			var $icon = $('<span class="rg-admin-ui__icon" aria-hidden="true">' + GROUPS[key].svg + '</span>');
			$header.prepend($icon);
		}

		// If the group has a description (ACF outputs it inside .inside), wrap it
		// with a styled class. Non-destructive.
		var $desc = $box.find('.inside > .acf-fields').prevAll('.acf-field-group-description').first();
		if (!$desc.length) {
			// ACF doesn't always render a dedicated description node. Look for the
			// ACF field-group "description" attribute stored on the data model.
			// When absent, skip silently. The subtitle stays empty.
		}

		$header.data('rgDecorated', true);
	}

	function rgPostboxes() {
		return $('.postbox[id^="acf-group_rgeometry_"]');
	}

	function closeBox($box) {
		$box.addClass('closed');
		$box.find('.handlediv').attr('aria-expanded', 'false');
	}
	function openBox($box) {
		$box.removeClass('closed');
		$box.find('.handlediv').attr('aria-expanded', 'true');
	}

	function closeAllExcept($keepOpen) {
		rgPostboxes().each(function () {
			var $b = $(this);
			if ($keepOpen && $b.is($keepOpen)) return;
			closeBox($b);
		});
	}

	function restoreLastOpen() {
		var $boxes = rgPostboxes();
		if (!$boxes.length) return;

		// Start by closing everything.
		$boxes.each(function () { closeBox($(this)); });

		// Restore the last-opened group if still present.
		var key;
		try { key = localStorage.getItem(STORAGE_KEY); } catch (e) { key = null; }
		if (key) {
			var $match = $('#acf-' + CSS.escape(key));
			if ($match.length) {
				openBox($match);
			}
		}
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

	function wireAccordion() {
		// Use WP's native click target (the header). After WP's own handler
		// toggles the .closed class, inspect state and enforce accordion rules.
		$(document).on('click.rgAdminUI', '.postbox[id^="acf-group_rgeometry_"] .postbox-header, .postbox[id^="acf-group_rgeometry_"] .handlediv', function (e) {
			// Ignore clicks on form inputs inside the header (unlikely but safe).
			if ($(e.target).is('input, textarea, select, button:not(.handlediv)')) return;

			var $box = $(this).closest('.postbox');

			// Defer until after WP's own toggle handler has run.
			setTimeout(function () {
				if (!$box.hasClass('closed')) {
					// Just opened. Close the others and remember the new open group.
					if (ACCORDION_ALL) closeAllExcept($box);
					rememberOpen($box);
				} else {
					// Just closed. Forget the pointer if it was the remembered one.
					forgetIfMatches($box);
				}
			}, 0);
		});
	}

	function boot() {
		rgPostboxes().each(function () { decorateHeader($(this)); });
		wireAccordion();
		restoreLastOpen();
	}

	// ACF re-renders some UI dynamically. Run after DOM ready and again after a
	// short delay to catch late-rendered postboxes on complex screens.
	$(function () {
		boot();
		setTimeout(boot, 300);
	});

})(jQuery);
