<?php
/**
 * Section: Services
 *
 * @package RGeometry
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = rgeometry_field( 'services_eyebrow', 'What We Do' );
$heading = rgeometry_field( 'services_heading', 'Three things, done well.' );

$services = rgeometry_field( 'services_items', array(
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
) );
?>
<section id="services" class="rg-section rg-services">
	<div class="rg-container">
		<div data-reveal>
			<p class="rg-eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<h2 class="rg-section__heading"><?php echo esc_html( $heading ); ?></h2>
		</div>

		<div class="rg-services__grid">
			<?php foreach ( (array) $services as $idx => $item ) : ?>
				<div class="rg-service" data-reveal data-reveal-delay="<?php echo esc_attr( 0.1 * $idx ); ?>">
					<div class="rg-service__icon"><?php echo rgeometry_icon( $item['icon'] ?? 'home' ); ?></div>
					<h3 class="rg-service__title"><?php echo esc_html( $item['title'] ?? '' ); ?></h3>
					<p class="rg-service__desc"><?php echo esc_html( $item['description'] ?? '' ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
