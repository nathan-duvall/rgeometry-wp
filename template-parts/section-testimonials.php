<?php
/**
 * Section: Testimonials (rotating carousel, JS-driven)
 *
 * @package RGeometry
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow  = rgeometry_field( 'testimonials_eyebrow',  'What Clients Say' );
$interval = (int) rgeometry_field( 'testimonials_interval_ms', 5500 );

$items = rgeometry_field( 'testimonials_items', array(
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
) );
?>
<section class="rg-section rg-testimonials" data-testimonials data-testimonials-interval="<?php echo esc_attr( $interval ); ?>">
	<div class="rg-container">
		<div class="rg-testimonials__inner">
			<p class="rg-eyebrow rg-testimonials__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<div class="rg-testimonials__quote-mark"><?php echo rgeometry_icon( 'quote' ); ?></div>

			<div class="rg-testimonials__stage">
				<?php foreach ( (array) $items as $idx => $t ) : ?>
					<div class="rg-testimonial <?php echo 0 === $idx ? 'is-active' : ''; ?>" data-index="<?php echo esc_attr( $idx ); ?>">
						<blockquote class="rg-testimonial__quote">&ldquo;<?php echo esc_html( $t['quote'] ?? '' ); ?>&rdquo;</blockquote>
						<p class="rg-testimonial__author"><?php echo esc_html( $t['author'] ?? '' ); ?></p>
						<p class="rg-testimonial__location"><?php echo esc_html( $t['location'] ?? '' ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>

			<div class="rg-testimonials__nav">
				<button class="rg-icon-btn rg-icon-btn--sm" data-testimonials-prev aria-label="Previous testimonial"><?php echo rgeometry_icon( 'chev-left' ); ?></button>
				<div class="rg-testimonials__dots">
					<?php foreach ( (array) $items as $idx => $t ) : ?>
						<button class="rg-testimonials__dot <?php echo 0 === $idx ? 'is-active' : ''; ?>" data-index="<?php echo esc_attr( $idx ); ?>" aria-label="Testimonial <?php echo esc_attr( $idx + 1 ); ?>"></button>
					<?php endforeach; ?>
				</div>
				<button class="rg-icon-btn rg-icon-btn--sm" data-testimonials-next aria-label="Next testimonial"><?php echo rgeometry_icon( 'chev-right' ); ?></button>
			</div>
		</div>
	</div>
</section>
