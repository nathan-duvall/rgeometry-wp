<?php
/**
 * Section: Site footer (dark bg, brand + links + socials).
 *
 * Branding and socials come from Theme Settings > Footer. Links come from
 * Appearance > Menus (Footer Menu location).
 *
 * @package RGeometry
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$logo        = function_exists( 'get_field' ) ? get_field( 'footer_logo', 'option' ) : '';
$logo_height = (int) ( function_exists( 'get_field' ) ? get_field( 'footer_logo_height', 'option' ) : 0 );
if ( $logo_height <= 0 ) { $logo_height = 24; }
$brand_text  = function_exists( 'get_field' ) ? get_field( 'footer_brand_text', 'option' ) : '';
if ( ! $brand_text ) { $brand_text = get_bloginfo( 'name' ); }
$tagline     = function_exists( 'get_field' ) ? get_field( 'footer_tagline', 'option' ) : '';
$copyright   = function_exists( 'get_field' ) ? get_field( 'footer_copyright', 'option' ) : 'All rights reserved.';
$socials     = function_exists( 'get_field' ) ? get_field( 'footer_socials', 'option' ) : array();
?>
<footer class="rg-footer">
	<div class="rg-container">
		<div class="rg-footer__row">
			<div class="rg-footer__brand-col">
				<?php if ( is_array( $logo ) && ! empty( $logo['url'] ) ) : ?>
					<img
						src="<?php echo esc_url( $logo['url'] ); ?>"
						alt="<?php echo esc_attr( ! empty( $logo['alt'] ) ? $logo['alt'] : $brand_text ); ?>"
						class="rg-footer__logo"
						style="height: <?php echo esc_attr( $logo_height ); ?>px;"
					/>
				<?php else : ?>
					<span class="rg-footer__brand"><?php echo esc_html( $brand_text ); ?></span>
				<?php endif; ?>
				<?php if ( $tagline ) : ?>
					<p class="rg-footer__tagline"><?php echo esc_html( $tagline ); ?></p>
				<?php endif; ?>
			</div>

			<div class="rg-footer__links">
				<?php rgeometry_render_menu( 'footer', 'rg-footer__link' ); ?>
			</div>

			<div class="rg-footer__socials">
				<?php foreach ( (array) $socials as $s ) : ?>
					<a class="rg-footer__social" href="<?php echo esc_url( $s['url'] ?? '#' ); ?>" aria-label="<?php echo esc_attr( $s['label'] ?? $s['icon'] ?? '' ); ?>"><?php echo rgeometry_icon( $s['icon'] ?? '', 'rg-footer__social-svg' ); ?></a>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="rg-footer__copy">
			&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( $brand_text ); ?>. <?php echo esc_html( $copyright ); ?>
		</div>
	</div>
</footer>
