<?php
/**
 * Theme Settings options page.
 *
 * Global elements (header/footer branding, social links, business contact
 * info) live here so they stay consistent across pages if the site ever grows
 * past the one-pager.
 *
 * Registered as a parent page with three sub-pages so the admin sidebar
 * shows Header, Footer, and Business Info as distinct panels.
 *
 * @package RGeometry
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'acf/init', 'rgeometry_register_options_pages' );
function rgeometry_register_options_pages() {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	acf_add_options_page( array(
		'page_title' => 'Theme Settings',
		'menu_title' => 'Theme Settings',
		'menu_slug'  => 'rgeometry-theme-settings',
		'capability' => 'manage_options',
		'icon_url'   => 'dashicons-admin-customizer',
		'position'   => 60,
		'redirect'   => true, // parent redirects to first child
	) );

	acf_add_options_sub_page( array(
		'page_title'  => 'Header Settings',
		'menu_title'  => 'Header',
		'parent_slug' => 'rgeometry-theme-settings',
	) );

	acf_add_options_sub_page( array(
		'page_title'  => 'Footer Settings',
		'menu_title'  => 'Footer',
		'parent_slug' => 'rgeometry-theme-settings',
	) );

	acf_add_options_sub_page( array(
		'page_title'  => 'Business Info',
		'menu_title'  => 'Business Info',
		'parent_slug' => 'rgeometry-theme-settings',
	) );
}
