<?php
/**
 * Section: Navbar (fixed).
 *
 * Branding comes from Theme Settings > Header. Menu items come from
 * Appearance > Menus (Primary Menu location). CTA button is a menu item
 * with the `is-cta` CSS class applied to it in the menu editor.
 *
 * @package RGeometry
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$logo          = function_exists( 'get_field' ) ? get_field( 'header_logo', 'option' ) : '';
$logo_height   = (int) ( function_exists( 'get_field' ) ? get_field( 'header_logo_height', 'option' ) : 0 );
if ( $logo_height <= 0 ) { $logo_height = 28; }
$brand_text    = function_exists( 'get_field' ) ? get_field( 'header_brand_text', 'option' ) : '';
if ( ! $brand_text ) { $brand_text = get_bloginfo( 'name' ); }
?>
<nav class="rg-nav" data-nav>
	<div class="rg-container rg-nav__inner">
		<a href="#hero" class="rg-nav__brand" aria-label="<?php echo esc_attr( $brand_text ); ?>">
			<?php if ( is_array( $logo ) && ! empty( $logo['url'] ) ) : ?>
				<img
					src="<?php echo esc_url( $logo['url'] ); ?>"
					alt="<?php echo esc_attr( ! empty( $logo['alt'] ) ? $logo['alt'] : $brand_text ); ?>"
					class="rg-nav__logo"
					style="height: <?php echo esc_attr( $logo_height ); ?>px;"
				/>
			<?php else : ?>
				<span class="rg-nav__brand-text"><?php echo esc_html( $brand_text ); ?></span>
			<?php endif; ?>
		</a>

		<div class="rg-nav__links">
			<?php rgeometry_render_menu( 'primary', 'rg-nav__link' ); ?>
		</div>

		<button class="rg-nav__burger" type="button" aria-label="Toggle menu" aria-expanded="false" data-nav-toggle>
			<span></span><span></span><span></span>
		</button>
	</div>

	<div class="rg-nav__mobile" data-nav-mobile hidden>
		<?php rgeometry_render_menu( 'primary', 'rg-nav__mobile-link' ); ?>
	</div>
</nav>
