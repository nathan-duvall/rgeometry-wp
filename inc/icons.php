<?php
/**
 * Inline SVG icon registry.
 *
 * Icons stay as real inline SVG (not image uploads) so they animate and
 * inherit color cleanly. Editors pick from a known set via an ACF select.
 *
 * @package RGeometry
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return an inline SVG by name, or an empty string if the name isn't in the
 * registry. Safe to echo directly.
 *
 * @param string $name  Registry key.
 * @param string $class Optional extra CSS class.
 * @return string
 */
function rgeometry_icon( $name, $class = '' ) {
	$icons = rgeometry_icon_registry();
	if ( empty( $icons[ $name ] ) ) {
		return '';
	}
	$svg = $icons[ $name ];
	if ( $class ) {
		$svg = str_replace( '<svg ', '<svg class="' . esc_attr( $class ) . '" ', $svg );
	}
	return $svg;
}

/**
 * Choices array ready for an ACF select field.
 */
function rgeometry_icon_choices() {
	$labels = array(
		'home'        => 'Home (pitched roof)',
		'renovation'  => 'Renovation (overlapping squares)',
		'commercial'  => 'Commercial (building)',
		'chat'        => 'Chat bubble',
		'pencil'      => 'Pencil',
		'document'    => 'Document',
		'check'       => 'Checkmark circle',
		'quote'       => 'Quote mark',
		'chev-left'   => 'Chevron left',
		'chev-right'  => 'Chevron right',
		'map-pin'     => 'Map pin',
		'phone'       => 'Phone',
		'mail'        => 'Mail',
		'instagram'   => 'Instagram',
		'linkedin'    => 'LinkedIn',
	);
	return $labels;
}

/**
 * Raw SVG markup keyed by name. Stroke-width and viewBox match the reference
 * source at rgeometry.lovable.app.
 */
function rgeometry_icon_registry() {
	return array(
		'home' => '<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 24L24 8l18 16"/><path d="M10 22v16h28V22"/><path d="M20 38V28h8v10"/></svg>',

		'renovation' => '<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="8" y="8" width="20" height="20"/><path d="M28 20h12v20H20v-12"/><path d="M34 14v-2m0 0h-2m2 0h2m-2 0v2"/></svg>',

		'commercial' => '<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="8" y="16" width="32" height="24"/><path d="M8 16l16-8 16 8"/><path d="M18 40V28h12v12"/></svg>',

		'chat' => '<svg viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 28c6.627 0 12-5.373 12-12S22.627 4 16 4 4 9.373 4 16c0 2.214.6 4.29 1.646 6.07L4 28l5.93-1.646A11.94 11.94 0 0016 28z"/></svg>',

		'pencil' => '<svg viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 28l6-2 16-16-4-4L6 22z"/><path d="M20 8l4 4"/></svg>',

		'document' => '<svg viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="6" y="4" width="20" height="24" rx="2"/><path d="M10 10h12M10 14h12M10 18h8"/></svg>',

		'check' => '<svg viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 16l4 4 8-8"/><circle cx="16" cy="16" r="12"/></svg>',

		'quote' => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 21c3 0 7-1 7-8V5H3v8h4c0 2-1 4-4 4v4zm10 0c3 0 7-1 7-8V5h-7v8h4c0 2-1 4-4 4v4z"/></svg>',

		'chev-left' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>',

		'chev-right' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>',

		'map-pin' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>',

		'phone' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>',

		'mail' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>',

		'instagram' => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>',

		'linkedin' => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>',
	);
}
