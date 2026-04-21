<?php
/**
 * Section: About + Team
 *
 * @package RGeometry
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = rgeometry_field( 'about_eyebrow', 'About Us' );
$heading = rgeometry_field( 'about_heading', 'Small firm. Full attention.' );
$para_1  = rgeometry_field( 'about_paragraph_1', "RGeometry was founded in 2019 by Richard Shaluly, AIA. We're a team of four, two licensed architects, one intern, and a project coordinator who keeps everything honest." );
$para_2  = rgeometry_field( 'about_paragraph_2', "We take on around 12 projects a year, which means you're not a number in a queue. You get Richard or John on every call, every site visit, and every tough conversation." );

$team = rgeometry_field( 'about_team', array(
	array( 'name' => 'Richard Shaluly, AIA',  'role' => 'Principal + Founder',      'image' => '', 'seed' => 'rgeometry-team-richard' ),
	array( 'name' => 'John Doe, AIA',          'role' => 'Senior Architect',         'image' => '', 'seed' => 'rgeometry-team-john' ),
	array( 'name' => 'Jane Smith',             'role' => 'Architectural Intern',     'image' => '', 'seed' => 'rgeometry-team-jane' ),
	array( 'name' => 'Bob Evans',              'role' => 'Project Coordinator',      'image' => '', 'seed' => 'rgeometry-team-bob' ),
) );
?>
<section id="about" class="rg-section rg-about">
	<div class="rg-container">
		<div class="rg-about__grid">
			<div class="rg-about__intro" data-reveal>
				<p class="rg-eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
				<h2 class="rg-section__heading rg-about__heading"><?php echo esc_html( $heading ); ?></h2>
				<p class="rg-about__body"><?php echo esc_html( $para_1 ); ?></p>
				<p class="rg-about__body"><?php echo esc_html( $para_2 ); ?></p>
			</div>
			<div class="rg-team">
				<?php foreach ( (array) $team as $idx => $m ) :
					$img = ! empty( $m['image'] ) ? $m['image'] : rgeometry_placeholder_image( $m['seed'] ?? ( 'rgeometry-team-' . $idx ), 400, 500 );
				?>
					<div class="rg-team__member" data-reveal data-reveal-delay="<?php echo esc_attr( 0.05 * $idx ); ?>">
						<img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $m['name'] ?? '' ); ?>" loading="lazy" class="rg-team__photo" />
						<h4 class="rg-team__name"><?php echo esc_html( $m['name'] ?? '' ); ?></h4>
						<p class="rg-team__role"><?php echo esc_html( $m['role'] ?? '' ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
