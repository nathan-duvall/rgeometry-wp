<?php
/**
 * Section: Projects (horizontal scroll gallery)
 *
 * @package RGeometry
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = rgeometry_field( 'projects_eyebrow', 'Selected Work' );
$heading = rgeometry_field( 'projects_heading', 'Recent projects.' );

$projects = rgeometry_field( 'projects_items', array(
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
) );
?>
<section id="projects" class="rg-section rg-projects">
	<div class="rg-container">
		<div class="rg-projects__head" data-reveal>
			<div>
				<p class="rg-eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
				<h2 class="rg-section__heading"><?php echo esc_html( $heading ); ?></h2>
			</div>
			<div class="rg-projects__nav">
				<button class="rg-icon-btn" data-projects-prev aria-label="Previous project"><?php echo rgeometry_icon( 'chev-left' ); ?></button>
				<button class="rg-icon-btn" data-projects-next aria-label="Next project"><?php echo rgeometry_icon( 'chev-right' ); ?></button>
			</div>
		</div>
	</div>

	<div class="rg-projects__track" data-projects-track>
		<?php foreach ( (array) $projects as $idx => $p ) :
			$img = ! empty( $p['image'] ) ? $p['image'] : rgeometry_placeholder_image( $p['seed'] ?? ( 'rgeometry-project-' . $idx ), 800, 600 );
		?>
			<article class="rg-project" data-reveal data-reveal-delay="<?php echo esc_attr( 0.05 * $idx ); ?>">
				<div class="rg-project__media">
					<img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $p['name'] ?? '' ); ?>" loading="lazy" />
				</div>
				<p class="rg-project__meta"><?php echo esc_html( $p['meta'] ?? '' ); ?></p>
				<h3 class="rg-project__title"><?php echo esc_html( $p['name'] ?? '' ); ?></h3>
				<p class="rg-project__location"><?php echo esc_html( $p['location'] ?? '' ); ?></p>
				<p class="rg-project__desc"><?php echo esc_html( $p['desc'] ?? '' ); ?></p>
				<?php if ( ! empty( $p['link'] ) ) : ?>
					<a class="rg-project__link" href="<?php echo esc_url( $p['link'] ); ?>">View Project <span aria-hidden="true">&rarr;</span></a>
				<?php endif; ?>
			</article>
		<?php endforeach; ?>
		<div class="rg-projects__spacer" aria-hidden="true"></div>
	</div>
</section>
