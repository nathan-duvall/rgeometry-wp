<?php
/**
 * WordPress menus: register locations, seed defaults, render with a walker
 * that matches the RGeometry markup.
 *
 * @package RGeometry
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the two menu locations used by the theme.
 */
add_action( 'after_setup_theme', 'rgeometry_register_menu_locations', 20 );
function rgeometry_register_menu_locations() {
	register_nav_menus( array(
		'primary' => __( 'Primary Menu (header)', 'rgeometry' ),
		'footer'  => __( 'Footer Menu', 'rgeometry' ),
	) );
}

/**
 * Seed both menus on first activation with the default Lovable-reference items,
 * assign them to their menu locations, and never run again (tracked by an option).
 *
 * Runs on activation and on admin_init as belt-and-suspenders (WP-CLI
 * activations don't always fire after_switch_theme reliably).
 */
add_action( 'after_switch_theme', 'rgeometry_seed_menus_if_needed' );
add_action( 'admin_init',         'rgeometry_seed_menus_if_needed' );
function rgeometry_seed_menus_if_needed() {
	if ( get_option( 'rgeometry_menus_seeded_v1' ) ) {
		return;
	}

	$locations = get_theme_mod( 'nav_menu_locations', array() );

	// Primary menu
	$primary_id = rgeometry_ensure_menu( 'Primary Menu' );
	if ( $primary_id && ! wp_get_nav_menu_items( $primary_id ) ) {
		rgeometry_add_menu_item( $primary_id, 'Services', '#services' );
		rgeometry_add_menu_item( $primary_id, 'Work',     '#projects' );
		rgeometry_add_menu_item( $primary_id, 'About',    '#about' );
		rgeometry_add_menu_item( $primary_id, 'Contact',  '#contact' );
		rgeometry_add_menu_item( $primary_id, 'Start a Conversation', '#contact', array( 'is-cta' ) );
	}
	$locations['primary'] = (int) $primary_id;

	// Footer menu
	$footer_id = rgeometry_ensure_menu( 'Footer Menu' );
	if ( $footer_id && ! wp_get_nav_menu_items( $footer_id ) ) {
		rgeometry_add_menu_item( $footer_id, 'Work',     '#projects' );
		rgeometry_add_menu_item( $footer_id, 'Services', '#services' );
		rgeometry_add_menu_item( $footer_id, 'About',    '#about' );
		rgeometry_add_menu_item( $footer_id, 'Contact',  '#contact' );
	}
	$locations['footer'] = (int) $footer_id;

	set_theme_mod( 'nav_menu_locations', $locations );
	update_option( 'rgeometry_menus_seeded_v1', time() );
}

/**
 * Get an existing menu by name, or create it if missing. Returns term_id on
 * success, 0 on failure.
 */
function rgeometry_ensure_menu( $name ) {
	$menu = wp_get_nav_menu_object( $name );
	if ( $menu && ! empty( $menu->term_id ) ) {
		return (int) $menu->term_id;
	}
	$new = wp_create_nav_menu( $name );
	if ( is_wp_error( $new ) ) {
		return 0;
	}
	return (int) $new;
}

/**
 * Add a Custom Link menu item to the given menu. Optional CSS classes applied
 * to the <li>.
 */
function rgeometry_add_menu_item( $menu_id, $title, $url, $classes = array() ) {
	return wp_update_nav_menu_item( $menu_id, 0, array(
		'menu-item-title'     => $title,
		'menu-item-url'       => $url,
		'menu-item-type'      => 'custom',
		'menu-item-status'    => 'publish',
		'menu-item-classes'   => implode( ' ', (array) $classes ),
	) );
}

/**
 * Walker that outputs a flat list of links (no nested ul/li) so the existing
 * CSS for .rg-nav__links and .rg-footer__links works without changes.
 * Each anchor picks up:
 *   - rg-nav__link (desktop), rg-nav__mobile-link (mobile), rg-footer__link (footer)
 *     via the `item_class` passed in args.
 *   - Any custom classes the user added in the menu editor, normalized to
 *     the `rg-` namespace (e.g. "is-cta" -> "rg-btn rg-btn--primary rg-nav__cta").
 */
class RGeometry_Menu_Walker extends Walker_Nav_Menu {
	public $tree_type = array( 'post_type', 'taxonomy', 'custom' );
	public $db_fields = array( 'parent' => 'menu_item_parent', 'id' => 'db_id' );

	public function start_lvl( &$output, $depth = 0, $args = null ) { /* flat */ }
	public function end_lvl( &$output, $depth = 0, $args = null ) { /* flat */ }
	public function end_el( &$output, $item, $depth = 0, $args = null ) { /* flat */ }

	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$item_class     = ! empty( $args->item_class ) ? $args->item_class : '';
		$user_classes   = (array) ( $item->classes ?? array() );
		$user_classes   = array_filter( array_map( 'trim', $user_classes ) );

		$classes = array( $item_class );

		// Map known UX classes to their component styles.
		if ( in_array( 'is-cta', $user_classes, true ) ) {
			// CTA: render as a primary button inside the nav. Replace the base link class.
			$classes = array( 'rg-btn', 'rg-btn--primary', 'rg-nav__cta' );
			// For mobile rendering, also add the mobile-cta class.
			if ( 'rg-nav__mobile-link' === $item_class ) {
				$classes[] = 'rg-nav__mobile-cta';
			}
		}

		$class_attr = trim( implode( ' ', array_unique( array_filter( $classes ) ) ) );
		$url_attr   = ! empty( $item->url ) ? esc_url( $item->url ) : '#';
		$title_attr = esc_html( wp_strip_all_tags( $item->title ) );

		$output .= sprintf(
			'<a class="%s" href="%s">%s</a>',
			esc_attr( $class_attr ),
			$url_attr,
			$title_attr
		);
	}
}

/**
 * Render helper: calls wp_nav_menu with the RGeometry walker, applying the
 * item_class that downstream CSS expects (rg-nav__link, rg-nav__mobile-link,
 * rg-footer__link).
 *
 * @param string $location   Theme location name.
 * @param string $item_class Base CSS class for each anchor.
 * @param string $fallback   HTML to output if no menu is assigned.
 */
function rgeometry_render_menu( $location, $item_class, $fallback = '' ) {
	if ( ! has_nav_menu( $location ) ) {
		// $fallback is developer-provided HTML. Run through wp_kses_post as a
		// defense-in-depth measure in case a future caller passes user input.
		echo wp_kses_post( $fallback );
		return;
	}
	wp_nav_menu( array(
		'theme_location' => $location,
		'container'      => false,
		'items_wrap'     => '%3$s',
		'depth'          => 1,
		'walker'         => new RGeometry_Menu_Walker(),
		'item_class'     => $item_class,
		'fallback_cb'    => false,
	) );
}
