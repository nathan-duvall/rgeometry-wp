<?php
/**
 * Small security + privacy hardening at the theme level.
 *
 * Most hardening belongs at the server/plugin layer (firewall, login limits,
 * 2FA). This file only covers the theme-controllable pieces:
 *   - Strip the WordPress <meta generator> so version fingerprinting is
 *     harder for automated scanners.
 *   - Strip the WP emoji scripts (we don't use them, dead bytes + a minor
 *     request-surface reduction).
 *
 * @package RGeometry
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Remove <meta name="generator" content="WordPress x.x.x" />. Doesn't block a
// determined attacker, but removes a trivially-scraped version marker.
remove_action( 'wp_head', 'wp_generator' );

// Remove WP's emoji scripts. Not used by this theme.
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

// Strip the emoji DNS prefetch hint too, since we're not loading them.
add_filter( 'emoji_svg_url', '__return_false' );
