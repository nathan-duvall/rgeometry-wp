<?php
/**
 * Section: Navbar (fixed)
 *
 * @package RGeometry
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$brand     = rgeometry_field( 'nav_brand', 'RGeometry' );
$links     = rgeometry_field( 'nav_links', array(
	array( 'label' => 'Services', 'target' => 'services' ),
	array( 'label' => 'Work',     'target' => 'projects' ),
	array( 'label' => 'About',    'target' => 'about' ),
	array( 'label' => 'Contact',  'target' => 'contact' ),
) );
$cta_label  = rgeometry_field( 'nav_cta_label',  'Start a Conversation' );
$cta_target = rgeometry_field( 'nav_cta_target', 'contact' );
?>
<nav class="rg-nav" data-nav>
	<div class="rg-container rg-nav__inner">
		<a href="#hero" class="rg-nav__brand"><?php echo esc_html( $brand ); ?></a>

		<div class="rg-nav__links">
			<?php foreach ( (array) $links as $link ) : if ( empty( $link['label'] ) ) continue; ?>
				<a
					class="rg-nav__link"
					href="<?php echo esc_url( rgeometry_anchor( $link['target'] ?? '' ) ); ?>"
				><?php echo esc_html( $link['label'] ); ?></a>
			<?php endforeach; ?>
			<?php if ( $cta_label ) : ?>
				<a class="rg-btn rg-btn--primary rg-nav__cta" href="<?php echo esc_url( rgeometry_anchor( $cta_target ) ); ?>"><?php echo esc_html( $cta_label ); ?></a>
			<?php endif; ?>
		</div>

		<button class="rg-nav__burger" type="button" aria-label="Toggle menu" aria-expanded="false" data-nav-toggle>
			<span></span><span></span><span></span>
		</button>
	</div>

	<div class="rg-nav__mobile" data-nav-mobile hidden>
		<?php foreach ( (array) $links as $link ) : if ( empty( $link['label'] ) ) continue; ?>
			<a
				class="rg-nav__mobile-link"
				href="<?php echo esc_url( rgeometry_anchor( $link['target'] ?? '' ) ); ?>"
			><?php echo esc_html( $link['label'] ); ?></a>
		<?php endforeach; ?>
		<?php if ( $cta_label ) : ?>
			<a class="rg-btn rg-btn--primary rg-nav__mobile-cta" href="<?php echo esc_url( rgeometry_anchor( $cta_target ) ); ?>"><?php echo esc_html( $cta_label ); ?></a>
		<?php endif; ?>
	</div>
</nav>
