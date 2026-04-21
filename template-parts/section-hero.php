<?php
/**
 * Section: Hero
 *
 * ACF source (field group: Hero):
 *   hero_title        (text)
 *   hero_subtitle     (textarea)
 *   hero_primary_cta  (group: label, target)
 *   hero_outline_cta  (group: label, target)
 *   hero_image        (image, return format: array — alt comes from the
 *                     media library attachment's own alt text)
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

$default_alt = 'Modern residential home with natural wood and stone exterior surrounded by lush landscaping';
$image       = function_exists( 'get_field' ) ? get_field( 'hero_image' ) : null;
if ( is_array( $image ) && ! empty( $image['url'] ) ) {
	$image_url = $image['url'];
	$image_alt = ! empty( $image['alt'] ) ? $image['alt'] : $default_alt;
} else {
	$image_url = rgeometry_placeholder_image( 'rgeometry-hero-architecture', 1920, 1080 );
	$image_alt = $default_alt;
}
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
