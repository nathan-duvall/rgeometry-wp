<?php
/**
 * Theme setup: supports, menus, ACF guard.
 *
 * @package RGeometry
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'after_setup_theme', 'rgeometry_setup' );
function rgeometry_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'rgeometry' ),
		)
	);
}

/**
 * Surface a clear admin notice if ACF Pro is missing. The theme still loads
 * (so you don't get locked out) but editing fields won't work without it.
 */
add_action( 'admin_notices', 'rgeometry_require_acf_notice' );
function rgeometry_require_acf_notice() {
	if ( function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}
	echo '<div class="notice notice-error"><p><strong>RGeometry theme:</strong> Advanced Custom Fields Pro is required. Install and activate it to manage editable content.</p></div>';
}
