<?php
/**
 * Front-end asset enqueues: Google Fonts, theme CSS, ScrollReveal JS.
 *
 * @package RGeometry
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_enqueue_scripts', 'rgeometry_enqueue_assets' );
function rgeometry_enqueue_assets() {
	// Google Fonts: DM Sans (body) + Playfair Display (display).
	wp_enqueue_style(
		'rgeometry-google-fonts',
		'https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&display=swap',
		array(),
		null
	);

	// Theme stylesheet (compiled tokens + components).
	wp_enqueue_style(
		'rgeometry-theme',
		RGEOMETRY_URI . '/assets/css/theme.css',
		array( 'rgeometry-google-fonts' ),
		RGEOMETRY_ASSET_VER
	);

	// ScrollReveal (vanilla IntersectionObserver) + nav/testimonials/projects logic.
	wp_enqueue_script(
		'rgeometry-reveal',
		RGEOMETRY_URI . '/assets/js/reveal.js',
		array(),
		RGEOMETRY_ASSET_VER,
		true
	);
}

/**
 * Preconnect hints matching the Lovable source's <head>. Marginal perf win,
 * but keeps the network waterfall consistent with the reference.
 */
add_action( 'wp_head', 'rgeometry_preconnect', 1 );
function rgeometry_preconnect() {
	echo "\n" . '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}
