<?php
/**
 * Section: Process (4-column steps)
 *
 * @package RGeometry
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = rgeometry_field( 'process_eyebrow', 'How We Work' );
$heading = rgeometry_field( 'process_heading', 'A process that respects your time.' );

$steps = rgeometry_field( 'process_steps', array(
	array( 'num' => '01', 'title' => 'Listen',   'desc' => "We start with a conversation, not a proposal. Tell us about your life, your budget, and what's not working.", 'icon' => 'chat' ),
	array( 'num' => '02', 'title' => 'Sketch',   'desc' => "Early concepts. We keep them loose on purpose so there's room to react and redirect.",                         'icon' => 'pencil' ),
	array( 'num' => '03', 'title' => 'Develop',  'desc' => 'Drawings, specs, and permit docs. We stay in constant contact with your builder.',                             'icon' => 'document' ),
	array( 'num' => '04', 'title' => 'Deliver',  'desc' => "We don't disappear at permit approval. We're on site, catching what drawings can't catch.",                    'icon' => 'check' ),
) );
?>
<section id="process" class="rg-section rg-process">
	<div class="rg-container">
		<div data-reveal>
			<p class="rg-eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<h2 class="rg-section__heading"><?php echo esc_html( $heading ); ?></h2>
		</div>

		<div class="rg-process__grid">
			<?php foreach ( (array) $steps as $idx => $step ) : ?>
				<div class="rg-step" data-reveal data-reveal-delay="<?php echo esc_attr( 0.08 * $idx ); ?>">
					<span class="rg-step__num"><?php echo esc_html( $step['num'] ?? '' ); ?></span>
					<div class="rg-step__icon"><?php echo rgeometry_icon( $step['icon'] ?? 'check' ); ?></div>
					<h3 class="rg-step__title"><?php echo esc_html( $step['title'] ?? '' ); ?></h3>
					<p class="rg-step__desc"><?php echo esc_html( $step['desc'] ?? '' ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
