/**
 * RGeometry Icon Picker — admin-side JS.
 *
 * Renders a modal grid of icons grouped by category, with search. One modal
 * instance is shared across all picker fields on the page (singleton).
 * Opened by clicking any [data-rg-iconpicker-open], writes its selection back
 * to the corresponding hidden input + updates the preview.
 */
(function ($) {
	'use strict';

	if (typeof rgIconPickerData === 'undefined') return;

	var icons  = rgIconPickerData.icons  || [];
	var labels = rgIconPickerData.labels || {};

	// Active field reference (set on open, cleared on close).
	var activeField = null;

	function getCategories() {
		var cats = {};
		icons.forEach(function (ic) {
			if (!cats[ic.category]) cats[ic.category] = [];
			cats[ic.category].push(ic);
		});
		return cats;
	}

	function escapeHtml(s) {
		return String(s).replace(/[&<>"']/g, function (c) {
			return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
		});
	}

	function renderGrid(filter, currentValue) {
		var q = (filter || '').toLowerCase().trim();
		var categories = getCategories();
		var html = '';
		var anyMatch = false;

		Object.keys(categories).forEach(function (cat) {
			var list = categories[cat].filter(function (ic) {
				if (!q) return true;
				var hay = (ic.key + ' ' + ic.label + ' ' + ic.keywords + ' ' + ic.category).toLowerCase();
				return hay.indexOf(q) !== -1;
			});
			if (!list.length) return;
			anyMatch = true;
			html += '<div class="rg-iconpicker-category">';
			html += '<div class="rg-iconpicker-category__title">' + escapeHtml(cat) + '</div>';
			html += '<div class="rg-iconpicker-grid">';
			list.forEach(function (ic) {
				var sel = ic.key === currentValue ? ' is-selected' : '';
				html += '<button type="button" class="rg-iconpicker-grid__item' + sel + '" data-key="' + escapeHtml(ic.key) + '" title="' + escapeHtml(ic.label) + '">';
				html += '<span class="rg-iconpicker-grid__icon">' + ic.svg + '</span>';
				html += '<span class="rg-iconpicker-grid__label">' + escapeHtml(ic.label) + '</span>';
				html += '</button>';
			});
			html += '</div></div>';
		});

		if (!anyMatch) {
			html = '<div class="rg-iconpicker-modal__empty">' + escapeHtml(labels.empty || 'No icons match.') + '</div>';
		}
		return html;
	}

	function ensureModal() {
		var $modal = $('#rg-iconpicker-modal');
		if ($modal.length) return $modal;

		$modal = $(
			'<div id="rg-iconpicker-modal" class="rg-iconpicker-modal" hidden role="dialog" aria-modal="true">' +
				'<div class="rg-iconpicker-modal__box">' +
					'<div class="rg-iconpicker-modal__head">' +
						'<h2 class="rg-iconpicker-modal__title">' + escapeHtml(labels.title || 'Choose an icon') + '</h2>' +
						'<button type="button" class="rg-iconpicker-modal__close" aria-label="' + escapeHtml(labels.close || 'Close') + '">×</button>' +
					'</div>' +
					'<div class="rg-iconpicker-modal__search">' +
						'<input type="text" placeholder="' + escapeHtml(labels.searchPlace || 'Search…') + '" aria-label="' + escapeHtml(labels.searchLabel || 'Search icons') + '" />' +
					'</div>' +
					'<div class="rg-iconpicker-modal__body"></div>' +
				'</div>' +
			'</div>'
		);
		$('body').append($modal);

		$modal.on('click', function (e) {
			if (e.target === this) closeModal();
		});
		$modal.find('.rg-iconpicker-modal__close').on('click', closeModal);

		$modal.on('input', '.rg-iconpicker-modal__search input', function () {
			var q = $(this).val();
			var cur = activeField ? activeField.find('[data-rg-iconpicker-input]').val() : '';
			$modal.find('.rg-iconpicker-modal__body').html(renderGrid(q, cur));
		});

		$modal.on('click', '.rg-iconpicker-grid__item', function () {
			var key = $(this).data('key');
			var ic = icons.find(function (i) { return i.key === key; });
			if (!ic || !activeField) return;
			activeField.find('[data-rg-iconpicker-input]').val(ic.key).trigger('change');
			activeField.find('[data-rg-iconpicker-preview]').html(ic.svg);
			activeField.find('[data-rg-iconpicker-label]').text(ic.label);

			// Add a clear button if one isn't already there.
			if (!activeField.find('[data-rg-iconpicker-clear]').length) {
				$('<button type="button" class="rg-iconpicker__clear" data-rg-iconpicker-clear aria-label="Clear icon">×</button>')
					.appendTo(activeField.find('.rg-iconpicker__btn'));
			}
			closeModal();
		});

		$(document).on('keydown.rgiconpicker', function (e) {
			if (e.key === 'Escape' && !$modal.prop('hidden')) closeModal();
		});

		return $modal;
	}

	function openModal($field) {
		activeField = $field;
		var $modal = ensureModal();
		var currentValue = $field.find('[data-rg-iconpicker-input]').val() || '';
		$modal.find('.rg-iconpicker-modal__search input').val('');
		$modal.find('.rg-iconpicker-modal__body').html(renderGrid('', currentValue));
		$modal.prop('hidden', false);
		setTimeout(function () { $modal.find('.rg-iconpicker-modal__search input').trigger('focus'); }, 0);
	}

	function closeModal() {
		var $modal = $('#rg-iconpicker-modal');
		$modal.prop('hidden', true);
		activeField = null;
	}

	$(document).on('click', '[data-rg-iconpicker-open]', function (e) {
		// Don't open when the inner clear button was the click target.
		if ($(e.target).closest('[data-rg-iconpicker-clear]').length) return;
		e.preventDefault();
		openModal($(this).closest('[data-rg-iconpicker]'));
	});

	$(document).on('click', '[data-rg-iconpicker-clear]', function (e) {
		e.preventDefault();
		e.stopPropagation();
		var $field = $(this).closest('[data-rg-iconpicker]');
		$field.find('[data-rg-iconpicker-input]').val('').trigger('change');
		$field.find('[data-rg-iconpicker-preview]').html('<span class="rg-iconpicker__placeholder" aria-hidden="true">?</span>');
		$field.find('[data-rg-iconpicker-label]').text(labels.choose || 'Choose icon…');
		$(this).remove();
	});

})(jQuery);
