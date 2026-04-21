<?php
/**
 * Section: Site footer (dark bg, brand + links + socials).
 *
 * @package RGeometry
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$brand   = rgeometry_field( 'footer_brand',   'RGeometry' );
$tagline = rgeometry_field( 'footer_tagline', 'Architecture grounded in how you live.' );

$links = rgeometry_field( 'footer_links', array(
	array( 'label' => 'Work',     'target' => 'projects' ),
	array( 'label' => 'Services', 'target' => 'services' ),
	array( 'label' => 'About',    'target' => 'about' ),
	array( 'label' => 'Contact',  'target' => 'contact' ),
) );

$socials = rgeometry_field( 'footer_socials', array(
	array( 'icon' => 'instagram', 'url' => '#', 'label' => 'Instagram' ),
	array( 'icon' => 'linkedin',  'url' => '#', 'label' => 'LinkedIn' ),
) );
$copyright = rgeometry_field( 'footer_copyright', 'All rights reserved.' );
?>
<footer class="rg-footer">
	<div class="rg-container">
		<div class="rg-footer__row">
			<div class="rg-footer__brand-col">
				<span class="rg-footer__brand"><?php echo esc_html( $brand ); ?></span>
				<p class="rg-footer__tagline"><?php echo esc_html( $tagline ); ?></p>
			</div>

			<div class="rg-footer__links">
				<?php foreach ( (array) $links as $link ) : if ( empty( $link['label'] ) ) continue; ?>
					<a href="<?php echo esc_url( rgeometry_anchor( $link['target'] ?? '' ) ); ?>" class="rg-footer__link"><?php echo esc_html( $link['label'] ); ?></a>
				<?php endforeach; ?>
			</div>

			<div class="rg-footer__socials">
				<?php foreach ( (array) $socials as $s ) : ?>
					<a class="rg-footer__social" href="<?php echo esc_url( $s['url'] ?? '#' ); ?>" aria-label="<?php echo esc_attr( $s['label'] ?? $s['icon'] ?? '' ); ?>"><?php echo rgeometry_icon( $s['icon'] ?? '', 'rg-footer__social-svg' ); ?></a>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="rg-footer__copy">
			&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( $brand ); ?>. <?php echo esc_html( $copyright ); ?>
		</div>
	</div>
</footer>
