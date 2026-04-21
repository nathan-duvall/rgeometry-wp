<?php
/**
 * ACF configuration and one-time JSON import.
 *
 * Field group definitions live in {theme}/acf-json/ so they track in Git.
 * ACF registers those groups in memory on every request, but the "Field
 * Groups" admin screen only sees groups that exist in the wp_posts table.
 * To make the two consistent, we import any JSON groups that don't yet have
 * a DB record on admin load. Idempotent. Runs once per group, then skips.
 *
 * @package RGeometry
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Bump rgeometry field groups to high priority so they render above
 * plugin-registered meta boxes (Rank Math, Yoast, etc.) on the edit screen.
 * The priority filter alone isn't enough when another plugin also uses
 * 'high' — tied priorities sort by registration order. The second filter
 * below runs after all plugins have registered and force-sorts rgeometry
 * groups to the top of the high-priority slot.
 */
add_filter( 'acf/input/meta_box_priority', 'rgeometry_acf_high_priority', 10, 2 );
function rgeometry_acf_high_priority( $priority, $field_group ) {
	if ( ! empty( $field_group['key'] ) && strpos( $field_group['key'], 'group_rgeometry_' ) === 0 ) {
		return 'high';
	}
	return $priority;
}

/**
 * Force-sort rgeometry field groups to the very top of the normal/high
 * priority slot on post edit screens, regardless of when other plugins
 * registered their meta boxes. Runs late so everyone else has finished.
 */
add_action( 'add_meta_boxes', 'rgeometry_reorder_meta_boxes', 9999, 2 );
function rgeometry_reorder_meta_boxes( $post_type, $post = null ) {
	global $wp_meta_boxes;
	if ( empty( $wp_meta_boxes[ $post_type ]['normal'] ) ) {
		return;
	}

	$rg_boxes = array();

	// Walk every priority slot in the 'normal' context, pull out rgeometry
	// boxes, and save them so we can prepend to 'high' afterwards.
	foreach ( array( 'high', 'core', 'default', 'low' ) as $priority ) {
		if ( empty( $wp_meta_boxes[ $post_type ]['normal'][ $priority ] ) ) continue;
		foreach ( $wp_meta_boxes[ $post_type ]['normal'][ $priority ] as $id => $box ) {
			if ( strpos( $id, 'acf-group_rgeometry_' ) === 0 ) {
				$rg_boxes[ $id ] = $box;
				unset( $wp_meta_boxes[ $post_type ]['normal'][ $priority ][ $id ] );
			}
		}
	}

	if ( empty( $rg_boxes ) ) {
		return;
	}

	// Prepend to the top of 'high' priority, preserving our own order.
	$existing_high = ! empty( $wp_meta_boxes[ $post_type ]['normal']['high'] )
		? $wp_meta_boxes[ $post_type ]['normal']['high']
		: array();
	$wp_meta_boxes[ $post_type ]['normal']['high'] = array_merge( $rg_boxes, $existing_high );
}

// Save field groups to the theme (ACF watches this folder and writes JSON
// whenever a group is edited or created in the admin).
add_filter( 'acf/settings/save_json', 'rgeometry_acf_json_save' );
function rgeometry_acf_json_save( $path ) {
	return RGEOMETRY_DIR . '/acf-json';
}

// Load field groups from the theme on boot.
add_filter( 'acf/settings/load_json', 'rgeometry_acf_json_load' );
function rgeometry_acf_json_load( $paths ) {
	unset( $paths[0] ); // drop ACF's default path so we're not loading twice
	$paths[] = RGEOMETRY_DIR . '/acf-json';
	return $paths;
}

/**
 * Import JSON field groups into the database on admin load so they appear in
 * ACF's Field Groups admin screen as first-class records, not "Sync Available"
 * placeholders. Also handles deprecation: JSON groups marked active:false get
 * their DB record deleted so old data from retired groups stops cluttering
 * the edit screen.
 *
 * On theme version bump (RGEOMETRY_VERSION changed from what's stored in the
 * `rgeometry_synced_version` option), existing RGeometry DB groups are first
 * deleted so the next pass re-imports from the updated JSON. Field-type
 * changes (e.g. select -> icon picker) only propagate to the DB that way.
 */
add_action( 'admin_init', 'rgeometry_acf_sync_local_json' );
function rgeometry_acf_sync_local_json() {
	if ( ! function_exists( 'acf_get_local_json_files' ) || ! function_exists( 'acf_import_field_group' ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$stored_version  = get_option( 'rgeometry_synced_version' );
	$is_version_bump = defined( 'RGEOMETRY_VERSION' ) && $stored_version !== RGEOMETRY_VERSION;

	$files = acf_get_local_json_files( 'acf-field-group' );
	if ( empty( $files ) ) {
		if ( $is_version_bump ) update_option( 'rgeometry_synced_version', RGEOMETRY_VERSION );
		return;
	}

	foreach ( $files as $key => $file_path ) {
		// Read the JSON file directly. acf_get_local_field_group() would
		// return the group metadata without its fields array (ACF stores
		// groups and fields separately in its local registry), so importing
		// from it results in a group with zero fields.
		if ( ! is_readable( $file_path ) ) {
			continue;
		}
		$json = json_decode( file_get_contents( $file_path ), true );
		if ( ! is_array( $json ) || empty( $json['key'] ) ) {
			continue;
		}

		$is_deprecated = isset( $json['active'] ) && $json['active'] === false;
		$existing      = rgeometry_acf_find_db_group_id( $key );

		if ( $is_deprecated ) {
			if ( $existing && function_exists( 'acf_delete_field_group' ) ) {
				acf_delete_field_group( $existing );
			}
			continue;
		}

		// Version bump: delete the old DB record so the import creates a
		// fresh one from the JSON definition.
		if ( $is_version_bump && $existing && function_exists( 'acf_delete_field_group' ) ) {
			acf_delete_field_group( $existing );
			$existing = 0;
		}

		if ( $existing ) {
			continue; // already in DB, not a version bump, nothing to do
		}

		// Use ACF's own import (same path the admin "Import JSON" button uses).
		// The JSON payload carries the nested fields/sub_fields/layouts.
		acf_import_field_group( $json );
	}

	if ( $is_version_bump ) {
		update_option( 'rgeometry_synced_version', RGEOMETRY_VERSION );
	}
}

/**
 * Direct wp_posts lookup for an ACF field group by key. Bypasses ACF's in-
 * memory cache, which lags after acf_delete_field_group() inside the same
 * request.
 *
 * @param string $key  ACF field-group key (stored as post_name).
 * @return int  Post ID if found, 0 otherwise.
 */
function rgeometry_acf_find_db_group_id( $key ) {
	$posts = get_posts( array(
		'post_type'              => 'acf-field-group',
		'name'                   => $key,
		'posts_per_page'         => 1,
		'post_status'            => 'any',
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
	) );
	return $posts ? (int) $posts[0]->ID : 0;
}

/**
 * Recursively import a field (and its sub-fields and layouts) into the database.
 */
function rgeometry_acf_import_field( $field ) {
	if ( ! function_exists( 'acf_update_field' ) ) {
		return;
	}
	$sub_fields = ! empty( $field['sub_fields'] ) ? $field['sub_fields'] : array();
	$layouts    = ! empty( $field['layouts'] )    ? $field['layouts']    : array();
	unset( $field['sub_fields'], $field['layouts'] );

	$imported = acf_update_field( $field );
	if ( ! $imported ) {
		return;
	}

	foreach ( $sub_fields as $sub ) {
		$sub['parent'] = $imported['ID'];
		rgeometry_acf_import_field( $sub );
	}
	foreach ( $layouts as $layout ) {
		if ( ! empty( $layout['sub_fields'] ) ) {
			foreach ( $layout['sub_fields'] as $sub ) {
				$sub['parent'] = $imported['ID'];
				rgeometry_acf_import_field( $sub );
			}
		}
	}
}
