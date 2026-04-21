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
 * Import JSON field groups that don't already exist in the database. Runs on
 * admin_init so field groups appear in ACF's Field Groups admin screen as
 * first-class records, not just "Sync Available" placeholders.
 */
add_action( 'admin_init', 'rgeometry_acf_sync_local_json' );
function rgeometry_acf_sync_local_json() {
	if ( ! function_exists( 'acf_get_local_json_files' ) || ! function_exists( 'acf_update_field_group' ) ) {
		return;
	}

	// Only run for users who could import via the admin UI anyway.
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$files = acf_get_local_json_files( 'acf-field-group' );
	if ( empty( $files ) ) {
		return;
	}

	// Groups already imported into wp_posts (keyed by ACF key).
	$db_groups = acf_get_field_groups();
	$db_keys   = array();
	foreach ( $db_groups as $g ) {
		// Skip groups that are only in JSON (local === 'json'). We want real DB records.
		if ( ! empty( $g['ID'] ) && $g['ID'] > 0 ) {
			$db_keys[] = $g['key'];
		}
	}

	foreach ( array_keys( $files ) as $key ) {
		if ( in_array( $key, $db_keys, true ) ) {
			continue; // already in DB
		}

		$group = acf_get_local_field_group( $key );
		if ( ! $group ) {
			continue;
		}

		// Pull the full fields list (including sub-fields) for this group, then import.
		$fields = acf_get_fields( $group );
		$group  = acf_update_field_group( $group );

		if ( $fields ) {
			foreach ( $fields as $field ) {
				$field['parent'] = $group['ID'];
				rgeometry_acf_import_field( $field );
			}
		}
	}
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
