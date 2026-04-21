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
 * Import JSON field groups into the database on admin load so they appear in
 * ACF's Field Groups admin screen as first-class records, not "Sync Available"
 * placeholders. Also handles deprecation: JSON groups marked active:false get
 * their DB record deleted so old data from retired groups stops cluttering
 * the edit screen.
 *
 * On theme version bump (RGEOMETRY_VERSION changed from what's stored in the
 * `rgeometry_synced_version` option), all RGeometry field groups are first
 * deleted so the next pass re-imports from the updated JSON. Field-type
 * changes (e.g. select -> icon picker) only propagate to the DB that way.
 */
add_action( 'admin_init', 'rgeometry_acf_sync_local_json' );
function rgeometry_acf_sync_local_json() {
	if ( ! function_exists( 'acf_get_local_json_files' ) || ! function_exists( 'acf_update_field_group' ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Version-gated force resync: when the theme version changes, wipe
	// existing RGeometry DB groups so JSON updates propagate.
	$stored_version = get_option( 'rgeometry_synced_version' );
	$is_version_bump = defined( 'RGEOMETRY_VERSION' ) && $stored_version !== RGEOMETRY_VERSION;
	if ( $is_version_bump ) {
		$db_groups = acf_get_field_groups();
		foreach ( $db_groups as $g ) {
			if ( empty( $g['ID'] ) || $g['ID'] <= 0 ) continue;
			if ( strpos( $g['key'], 'group_rgeometry_' ) !== 0 ) continue;
			if ( function_exists( 'acf_delete_field_group' ) ) {
				acf_delete_field_group( (int) $g['ID'] );
			}
		}
	}

	$files = acf_get_local_json_files( 'acf-field-group' );
	if ( empty( $files ) ) {
		if ( $is_version_bump ) update_option( 'rgeometry_synced_version', RGEOMETRY_VERSION );
		return;
	}

	// Re-index DB groups after the possible wipe.
	$db_groups = acf_get_field_groups();
	$db_ids    = array();
	foreach ( $db_groups as $g ) {
		if ( ! empty( $g['ID'] ) && $g['ID'] > 0 ) {
			$db_ids[ $g['key'] ] = (int) $g['ID'];
		}
	}

	foreach ( array_keys( $files ) as $key ) {
		$local = acf_get_local_field_group( $key );
		if ( ! $local ) {
			continue;
		}

		$is_deprecated = isset( $local['active'] ) && $local['active'] === false;

		// Deprecated: if a DB record exists, delete it so it stops showing up.
		if ( $is_deprecated ) {
			if ( isset( $db_ids[ $key ] ) && function_exists( 'acf_delete_field_group' ) ) {
				acf_delete_field_group( $db_ids[ $key ] );
			}
			continue;
		}

		// Already imported and not deprecated: nothing to do.
		if ( isset( $db_ids[ $key ] ) ) {
			continue;
		}

		// Fresh import.
		$fields = acf_get_fields( $local );
		$group  = acf_update_field_group( $local );
		if ( $fields ) {
			foreach ( $fields as $field ) {
				$field['parent'] = $group['ID'];
				rgeometry_acf_import_field( $field );
			}
		}
	}

	if ( $is_version_bump ) {
		update_option( 'rgeometry_synced_version', RGEOMETRY_VERSION );
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
