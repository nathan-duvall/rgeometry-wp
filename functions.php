<?php
/**
 * RGeometry theme bootstrap.
 *
 * @package RGeometry
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'RGEOMETRY_VERSION', '0.5.3' );
define( 'RGEOMETRY_DIR', get_stylesheet_directory() );
define( 'RGEOMETRY_URI', get_stylesheet_directory_uri() );

// Cache-bust asset URLs during active development. Flip to RGEOMETRY_VERSION for release.
define( 'RGEOMETRY_ASSET_VER', defined( 'WP_DEBUG' ) && WP_DEBUG ? (string) time() : RGEOMETRY_VERSION );

require_once RGEOMETRY_DIR . '/inc/setup.php';
require_once RGEOMETRY_DIR . '/inc/enqueue.php';
require_once RGEOMETRY_DIR . '/inc/acf.php';
require_once RGEOMETRY_DIR . '/inc/options.php';
require_once RGEOMETRY_DIR . '/inc/helpers.php';
require_once RGEOMETRY_DIR . '/inc/icons.php';
require_once RGEOMETRY_DIR . '/inc/icon-picker.php';
require_once RGEOMETRY_DIR . '/inc/menus.php';
require_once RGEOMETRY_DIR . '/inc/admin-ui.php';
require_once RGEOMETRY_DIR . '/inc/seed.php';
