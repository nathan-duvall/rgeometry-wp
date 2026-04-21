<?php
/**
 * Small helpers used across template parts.
 *
 * @package RGeometry
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pull an ACF field and fall back to a default when empty. Keeps templates terse
 * and prevents empty fields from rendering nothing on the front end.
 *
 * @param string $name    ACF field key.
 * @param string $default Default when the field is empty.
 * @return string
 */
function rgeometry_field( $name, $default = '' ) {
	if ( function_exists( 'get_field' ) ) {
		$val = get_field( $name, 'option' );
		if ( empty( $val ) ) {
			$val = get_field( $name );
		}
		if ( ! empty( $val ) ) {
			return $val;
		}
	}
	return $default;
}

/**
 * Return a Lorem Picsum placeholder URL. Seeded for stability so the same slot
 * renders the same image across reloads and deploys.
 */
function rgeometry_placeholder_image( $seed, $w = 1920, $h = 1080 ) {
	$seed = preg_replace( '/[^a-z0-9-]/i', '-', strtolower( $seed ) );
	return "https://picsum.photos/seed/{$seed}/{$w}/{$h}";
}

/**
 * Return an ACF image URL or a seeded placeholder fallback.
 *
 * @param string $field_name ACF field name (return format: array or URL).
 * @param string $seed       Placeholder seed used when no image is set.
 * @param int    $w          Placeholder width.
 * @param int    $h          Placeholder height.
 */
function rgeometry_image_or_placeholder( $field_name, $seed, $w = 1920, $h = 1080 ) {
	if ( function_exists( 'get_field' ) ) {
		$val = get_field( $field_name );
		if ( is_array( $val ) && ! empty( $val['url'] ) ) {
			return $val['url'];
		}
		if ( is_string( $val ) && $val !== '' ) {
			return $val;
		}
	}
	return rgeometry_placeholder_image( $seed, $w, $h );
}

/**
 * Smooth-scroll anchor helper. Returns an href like "#services" so sections can
 * link to each other without hardcoding.
 */
function rgeometry_anchor( $id ) {
	return '#' . sanitize_html_class( $id );
}
