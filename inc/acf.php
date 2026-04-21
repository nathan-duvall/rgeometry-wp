<?php
/**
 * ACF configuration: point local JSON at the theme's acf-json folder so field
 * groups live in version control alongside the templates.
 *
 * @package RGeometry
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Save field groups to the theme (ACF watches this folder and auto-syncs on admin load).
add_filter( 'acf/settings/save_json', 'rgeometry_acf_json_save' );
function rgeometry_acf_json_save( $path ) {
	return RGEOMETRY_DIR . '/acf-json';
}

// Load field groups from the theme on boot.
add_filter( 'acf/settings/load_json', 'rgeometry_acf_json_load' );
function rgeometry_acf_json_load( $paths ) {
	// Drop the parent's default path so we're not loading from two places.
	unset( $paths[0] );
	$paths[] = RGEOMETRY_DIR . '/acf-json';
	return $paths;
}
