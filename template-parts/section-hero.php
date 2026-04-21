<?php
/**
 * Section: Hero
 *
 * ACF source (field group: Hero):
 *   hero_title        (text)
 *   hero_subtitle     (textarea)
 *   hero_primary_cta  (group: label, target)
 *   hero_outline_cta  (group: label, target)
 *   hero_image        (image, return format: array)
 *   hero_image_alt    (text, optional override for alt)
 *
 * Defaults below match the Lovable reference copy so the section renders
 * something sensible even before any ACF values are set.
 *
 * @package RGeometry
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title    = rgeometry_field( 'hero_title', 'Architecture that fits the way you actually live.' );
$subtitle = rgeometry_field( 'hero_subtitle', 'RGeometry designs homes and spaces in Upstate South Carolina that are thoughtful, buildable, and built to last, not built to impress a magazine.' );

$primary_cta = rgeometry_field( 'hero_primary_cta', array( 'label' => 'See Our Work',   'target' => 'projects' ) );
$outline_cta = rgeometry_field( 'hero_outline_cta', array( 'label' => 'Get in Touch',   'target' => 'contact'  ) );

$image_url   = rgeometry_image_or_placeholder( 'hero_image', 'rgeometry-hero-architecture', 1920, 1080 );
$image_alt   = rgeometry_field( 'hero_image_alt', 'Modern residential home with natural wood and stone exterior surrounded by lush landscaping' );
?>
<section id="hero" class="rg-hero">
	<div class="rg-hero__bg">
		<img
			src="<?php echo esc_url( $image_url ); ?>"
			alt="<?php echo esc_attr( $image_alt ); ?>"
			class="rg-animate-ken-burns"
			loading="eager"
			decoding="async"
		/>
	</div>

	<div class="rg-hero__overlay" aria-hidden="true"></div>

	<div class="rg-hero__inner">
		<div class="rg-container">
			<div class="rg-hero__content">
				<h1 class="rg-hero__title"><?php echo esc_html( $title ); ?></h1>

				<p class="rg-hero__subtitle"><?php echo esc_html( $subtitle ); ?></p>

				<div class="rg-hero__ctas">
					<?php if ( ! empty( $primary_cta['label'] ) ) : ?>
						<a
							href="<?php echo esc_url( rgeometry_anchor( $primary_cta['target'] ?? 'projects' ) ); ?>"
							class="rg-btn rg-btn--primary"
						><?php echo esc_html( $primary_cta['label'] ); ?></a>
					<?php endif; ?>

					<?php if ( ! empty( $outline_cta['label'] ) ) : ?>
						<a
							href="<?php echo esc_url( rgeometry_anchor( $outline_cta['target'] ?? 'contact' ) ); ?>"
							class="rg-btn rg-btn--outline"
						><?php echo esc_html( $outline_cta['label'] ); ?></a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>
