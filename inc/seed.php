<?php
/**
 * One-time seed: populate the Home page with the default content that the
 * Lovable reference ships with. Without this, a client opening the Home edit
 * screen sees empty repeaters (no services, projects, team members, steps, or
 * testimonials) even though the frontend renders the defaults from PHP fallback.
 *
 * Runs once, tracked by a theme option. Skips fields that already have a value
 * so editing the seeded content is safe.
 *
 * @package RGeometry
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Trigger the seed on theme activation AND on admin_init if it hasn't run.
 * Belt-and-suspenders because the after_switch_theme hook doesn't always fire
 * in all activation paths (e.g. WP-CLI theme activate).
 */
add_action( 'after_switch_theme', 'rgeometry_seed_home_if_needed' );
add_action( 'admin_init',         'rgeometry_seed_home_if_needed' );
function rgeometry_seed_home_if_needed() {
	$option_key = 'rgeometry_home_seeded_v1';
	if ( get_option( $option_key ) ) {
		return;
	}
	if ( ! function_exists( 'update_field' ) ) {
		return; // ACF not loaded yet
	}

	$page_id = rgeometry_get_or_create_home_page();
	if ( ! $page_id ) {
		return;
	}

	rgeometry_seed_home_page( $page_id );
	update_option( $option_key, time() );
}

/**
 * Find the page assigned as the front page, or create one named "Home" and
 * assign it. Mirrors what the v0.1 deploy did via WP-CLI, now idempotent.
 */
function rgeometry_get_or_create_home_page() {
	$front_id = (int) get_option( 'page_on_front' );
	if ( $front_id && get_post_status( $front_id ) === 'publish' ) {
		return $front_id;
	}

	$existing = get_page_by_path( 'home' );
	if ( $existing ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $existing->ID );
		return $existing->ID;
	}

	$new_id = wp_insert_post( array(
		'post_title'  => 'Home',
		'post_name'   => 'home',
		'post_type'   => 'page',
		'post_status' => 'publish',
	) );
	if ( is_wp_error( $new_id ) || ! $new_id ) {
		return 0;
	}
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $new_id );
	return $new_id;
}

/**
 * Apply the full default content to the Home page. Only sets values that are
 * currently empty. Running this twice is a no-op the second time.
 */
function rgeometry_seed_home_page( $page_id ) {
	$defaults = rgeometry_seed_defaults();

	foreach ( $defaults as $field => $value ) {
		$current = get_field( $field, $page_id );
		if ( $current === '' || $current === null || $current === false ||
			( is_array( $current ) && empty( $current ) ) ) {
			update_field( $field, $value, $page_id );
		}
	}
}

/**
 * The canonical default content, mirrored exactly from the Lovable reference.
 * Changes here only take effect for fresh installs; existing data is preserved.
 */
function rgeometry_seed_defaults() {
	return array(
		// ----- Hero -----
		'hero_title'         => 'Architecture that fits the way you actually live.',
		'hero_subtitle'      => "RGeometry designs homes and spaces in Upstate South Carolina that are thoughtful, buildable, and built to last, not built to impress a magazine.",
		'hero_primary_cta'   => array( 'label' => 'See Our Work',  'target' => 'projects' ),
		'hero_outline_cta'   => array( 'label' => 'Get in Touch',  'target' => 'contact'  ),
		'hero_image_alt'     => 'Modern residential home with natural wood and stone exterior surrounded by lush landscaping',

		// ----- Navbar -----
		'nav_brand'     => 'RGeometry',
		'nav_cta_label' => 'Start a Conversation',
		'nav_cta_target'=> 'contact',
		'nav_links'     => array(
			array( 'label' => 'Services', 'target' => 'services' ),
			array( 'label' => 'Work',     'target' => 'projects' ),
			array( 'label' => 'About',    'target' => 'about'    ),
			array( 'label' => 'Contact',  'target' => 'contact'  ),
		),

		// ----- Services -----
		'services_eyebrow' => 'What We Do',
		'services_heading' => 'Three things, done well.',
		'services_items'   => array(
			array(
				'title'       => 'Custom Residential Design',
				'description' => 'New construction from the ground up. We work closely with you from site selection through final permit drawings.',
				'icon'        => 'home',
			),
			array(
				'title'       => 'Renovation + Addition',
				'description' => "Expanding what you have without losing what you love. We work with the bones of your home, not against them.",
				'icon'        => 'renovation',
			),
			array(
				'title'       => 'Small Commercial + Mixed-Use',
				'description' => 'Local shops, studios, and small offices. Spaces that reflect the business and the people who run it.',
				'icon'        => 'commercial',
			),
		),

		// ----- Projects -----
		'projects_eyebrow' => 'Selected Work',
		'projects_heading' => 'Recent projects.',
		'projects_items'   => array(
			array(
				'name'     => 'The Garrett Residence',
				'meta'     => '2,400 SQ FT · CUSTOM HOME',
				'location' => 'Weaverville, NC',
				'desc'     => 'A custom home with a dogtrot layout, exposed timber framing, and a south-facing passive solar design.',
				'image'    => '',
				'seed'     => 'rgeometry-project-garrett',
				'link'     => '',
			),
			array(
				'name'     => 'Depot Street Studio',
				'meta'     => '900 SQ FT · COMMERCIAL RENOVATION',
				'location' => 'Greenville, SC',
				'desc'     => 'A commercial renovation for a local ceramics studio. Polished concrete, north-facing skylights, open plan.',
				'image'    => '',
				'seed'     => 'rgeometry-project-depot',
				'link'     => '',
			),
			array(
				'name'     => 'Lakeview Addition',
				'meta'     => 'TWO-STORY ADDITION',
				'location' => 'Greenville, SC',
				'desc'     => 'A two-story rear addition that doubled the living space without touching the original 1970s character.',
				'image'    => '',
				'seed'     => 'rgeometry-project-lakeview',
				'link'     => '',
			),
		),

		// ----- About -----
		'about_eyebrow'     => 'About Us',
		'about_heading'     => 'Small firm. Full attention.',
		'about_paragraph_1' => "RGeometry was founded in 2019 by Richard Shaluly, AIA. We're a team of four, two licensed architects, one intern, and a project coordinator who keeps everything honest.",
		'about_paragraph_2' => "We take on around 12 projects a year, which means you're not a number in a queue. You get Richard or John on every call, every site visit, and every tough conversation.",
		'about_team'        => array(
			array( 'name' => 'Richard Shaluly, AIA', 'role' => 'Principal + Founder',   'image' => '', 'seed' => 'rgeometry-team-richard' ),
			array( 'name' => 'John Doe, AIA',        'role' => 'Senior Architect',      'image' => '', 'seed' => 'rgeometry-team-john'    ),
			array( 'name' => 'Jane Smith',           'role' => 'Architectural Intern',  'image' => '', 'seed' => 'rgeometry-team-jane'    ),
			array( 'name' => 'Bob Evans',            'role' => 'Project Coordinator',   'image' => '', 'seed' => 'rgeometry-team-bob'     ),
		),

		// ----- Process -----
		'process_eyebrow' => 'How We Work',
		'process_heading' => 'A process that respects your time.',
		'process_steps'   => array(
			array( 'num' => '01', 'title' => 'Listen',   'desc' => "We start with a conversation, not a proposal. Tell us about your life, your budget, and what's not working.", 'icon' => 'chat'     ),
			array( 'num' => '02', 'title' => 'Sketch',   'desc' => "Early concepts. We keep them loose on purpose so there's room to react and redirect.",                         'icon' => 'pencil'   ),
			array( 'num' => '03', 'title' => 'Develop',  'desc' => 'Drawings, specs, and permit docs. We stay in constant contact with your builder.',                             'icon' => 'document' ),
			array( 'num' => '04', 'title' => 'Deliver',  'desc' => "We don't disappear at permit approval. We're on site, catching what drawings can't catch.",                    'icon' => 'check'    ),
		),

		// ----- Testimonials -----
		'testimonials_eyebrow'    => 'What Clients Say',
		'testimonials_interval_ms'=> 5500,
		'testimonials_items'      => array(
			array(
				'quote'    => "We'd talked to three other firms before RGeometry. Richard was the first person who listened more than he talked. The house he designed is better than anything we would have thought to ask for.",
				'author'   => 'James + Sara Whitfield',
				'location' => 'Weaverville, NC',
			),
			array(
				'quote'    => "Our studio renovation came in under budget and finished two weeks early. I still don't fully understand how that happened.",
				'author'   => 'Priya Okonkwo',
				'location' => 'Depot Street Ceramics',
			),
			array(
				'quote'    => 'Richard caught a structural issue in our existing plans that two other firms missed. It would have cost us $40K to fix later. Worth every penny.',
				'author'   => 'Mark Reaves',
				'location' => 'Swannanoa, NC',
			),
		),

		// ----- Contact -----
		'contact_eyebrow'    => 'Contact',
		'contact_heading'    => 'Ready to talk about your project?',
		'contact_subheading' => "No pitch. No pressure. Just a 30-minute conversation to see if we're the right fit.",
		'contact_address'    => '201 Sikes Hall, Clemson, SC 29634',
		'contact_phone'      => '(864) 207-0500',
		'contact_email'      => 'hello@rgeometry.com',
		'contact_project_types' => array(
			array( 'label' => 'Custom Home' ),
			array( 'label' => 'Renovation' ),
			array( 'label' => 'Commercial' ),
			array( 'label' => 'Not Sure' ),
		),
		'contact_submit_label' => 'Send It',
		'contact_thanks_title' => 'Thanks for reaching out.',
		'contact_thanks_body'  => "We'll be in touch within 48 hours.",

		// ----- Footer -----
		'footer_brand'     => 'RGeometry',
		'footer_tagline'   => 'Architecture grounded in how you live.',
		'footer_copyright' => 'All rights reserved.',
		'footer_links'     => array(
			array( 'label' => 'Work',     'target' => 'projects' ),
			array( 'label' => 'Services', 'target' => 'services' ),
			array( 'label' => 'About',    'target' => 'about'    ),
			array( 'label' => 'Contact',  'target' => 'contact'  ),
		),
		'footer_socials'   => array(
			array( 'icon' => 'instagram', 'url' => '#', 'label' => 'Follow us on Instagram' ),
			array( 'icon' => 'linkedin',  'url' => '#', 'label' => 'Connect on LinkedIn'    ),
		),
	);
}
