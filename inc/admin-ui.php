<?php
/**
 * Admin UI polish for the ACF edit experience.
 *
 * Two jobs:
 *   1. Inject a Lucide icon badge into each RGeometry field group's title bar
 *      and style the postbox headers so they don't feel like vanilla WP.
 *   2. Drive accordion behavior across rg- field groups: all collapsed on
 *      first visit, opening one auto-closes the others, last-opened group is
 *      remembered per-user via localStorage.
 *
 * Only fires on admin screens where RGeometry field groups render.
 *
 * @package RGeometry
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Map of ACF field group key -> Lucide icon name in our registry.
 * Edit here to swap icons across the whole admin.
 */
function rgeometry_admin_ui_icon_map() {
	return array(
		'group_rgeometry_hero'              => 'home',
		'group_rgeometry_services'          => 'briefcase',
		'group_rgeometry_projects'          => 'layers',
		'group_rgeometry_about'             => 'users',
		'group_rgeometry_process'           => 'git-branch',
		'group_rgeometry_testimonials'      => 'quote',
		'group_rgeometry_settings_header'   => 'menu',
		'group_rgeometry_settings_footer'   => 'mail',
		'group_rgeometry_settings_business' => 'info',
	);
}

/**
 * Hide the block editor content area on the static front page. The page's
 * content is driven entirely by ACF fields + front-page.php, so the editor
 * is just dead screen space sitting above the meaningful controls.
 *
 * Title, featured image, sidebar (page settings, Rank Math panel, etc.) all
 * stay visible. Other pages keep their editor untouched.
 */
add_action( 'admin_init', 'rgeometry_hide_editor_on_front_page' );
function rgeometry_hide_editor_on_front_page() {
	$post_id = 0;
	if ( isset( $_GET['post'] ) ) {
		$post_id = (int) $_GET['post'];
	} elseif ( isset( $_POST['post_ID'] ) ) {
		$post_id = (int) $_POST['post_ID'];
	}
	if ( ! $post_id ) {
		return;
	}

	$front_id = (int) get_option( 'page_on_front' );
	if ( $front_id && $post_id === $front_id ) {
		remove_post_type_support( 'page', 'editor' );
	}
}

add_action( 'admin_enqueue_scripts', 'rgeometry_admin_ui_enqueue' );
function rgeometry_admin_ui_enqueue( $hook ) {
	if ( ! rgeometry_admin_ui_should_load( $hook ) ) {
		return;
	}

	$ver = defined( 'RGEOMETRY_ASSET_VER' ) ? RGEOMETRY_ASSET_VER : '1';

	wp_enqueue_style(
		'rg-admin-ui',
		RGEOMETRY_URI . '/assets/css/admin-ui.css',
		array(),
		$ver
	);

	wp_enqueue_script(
		'rg-admin-ui',
		RGEOMETRY_URI . '/assets/js/admin-ui.js',
		array( 'jquery' ),
		$ver,
		true
	);

	// Build the per-group payload: icon SVG + metadata so the JS can render.
	$icon_map   = rgeometry_admin_ui_icon_map();
	$registry   = rgeometry_icon_registry();
	$groups_out = array();
	foreach ( $icon_map as $group_key => $icon_name ) {
		$groups_out[ $group_key ] = array(
			'icon'    => $icon_name,
			'svg'     => isset( $registry[ $icon_name ] ) ? $registry[ $icon_name ] : '',
		);
	}

	wp_localize_script( 'rg-admin-ui', 'rgAdminUIData', array(
		'groups'       => $groups_out,
		'storageKey'   => 'rgAdminUI.lastOpen.' . get_current_user_id(),
		'accordionAll' => true, // when true, opening one closes the others
	) );
}

/**
 * Only enqueue on screens that actually render RGeometry field groups:
 * - Page edit for the front page (Pages > Home)
 * - Theme Settings options sub-pages
 */
function rgeometry_admin_ui_should_load( $hook ) {
	$screen = get_current_screen();
	if ( ! $screen ) {
		return false;
	}

	// ACF options sub-pages register under toplevel_page_ and $_page_ slugs.
	if ( strpos( $screen->id, 'rgeometry-theme-settings' ) !== false ) {
		return true;
	}
	if ( strpos( $screen->id, 'acf-options-' ) !== false ) {
		return true;
	}

	// Page edit for the static front page.
	if ( in_array( $hook, array( 'post.php', 'post-new.php' ), true ) && $screen->post_type === 'page' ) {
		$front_id = (int) get_option( 'page_on_front' );
		$post_id  = isset( $_GET['post'] ) ? (int) $_GET['post'] : 0;
		if ( $front_id && $post_id === $front_id ) {
			return true;
		}
	}

	return false;
}
