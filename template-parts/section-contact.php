<?php
/**
 * Section: Contact (dark bg, 2-column).
 *
 * All fields are global and live under Theme Settings > Footer:
 *   contact_eyebrow, contact_heading, contact_subheading, contact_form_shortcode.
 *
 * Business info (address/phone/email) comes from Theme Settings > Business Info.
 * The form itself is rendered via a shortcode (expected: Gravity Forms) so the
 * theme doesn't ship its own form handler.
 *
 * @package RGeometry
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow        = function_exists( 'get_field' ) ? get_field( 'contact_eyebrow',    'option' ) : '';
$heading        = function_exists( 'get_field' ) ? get_field( 'contact_heading',    'option' ) : '';
$subheading     = function_exists( 'get_field' ) ? get_field( 'contact_subheading', 'option' ) : '';
$form_shortcode = function_exists( 'get_field' ) ? get_field( 'contact_form_shortcode', 'option' ) : '';

if ( ! $eyebrow )    $eyebrow    = 'Contact';
if ( ! $heading )    $heading    = 'Ready to talk about your project?';
if ( ! $subheading ) $subheading = "No pitch. No pressure. Just a 30-minute conversation to see if we're the right fit.";

$address = function_exists( 'get_field' ) ? get_field( 'business_address', 'option' ) : '';
$phone   = function_exists( 'get_field' ) ? get_field( 'business_phone',   'option' ) : '';
$email   = function_exists( 'get_field' ) ? get_field( 'business_email',   'option' ) : '';
?>
<section id="contact" class="rg-section rg-contact">
	<div class="rg-container">
		<div class="rg-contact__grid">
			<div class="rg-contact__intro" data-reveal>
				<p class="rg-eyebrow rg-eyebrow--on-dark"><?php echo esc_html( $eyebrow ); ?></p>
				<h2 class="rg-section__heading rg-contact__heading"><?php echo esc_html( $heading ); ?></h2>
				<p class="rg-contact__body"><?php echo esc_html( $subheading ); ?></p>

				<ul class="rg-contact__meta">
					<?php if ( $address ) : ?><li><?php echo rgeometry_icon( 'map-pin' ); ?><span><?php echo esc_html( $address ); ?></span></li><?php endif; ?>
					<?php if ( $phone ) :   ?><li><?php echo rgeometry_icon( 'phone' );   ?><span><?php echo esc_html( $phone );   ?></span></li><?php endif; ?>
					<?php if ( $email ) :   ?><li><?php echo rgeometry_icon( 'mail' );    ?><span><a href="mailto:<?php echo esc_attr( $email ); ?>" class="rg-contact__email-link"><?php echo esc_html( $email ); ?></a></span></li><?php endif; ?>
				</ul>
			</div>

			<?php if ( ! empty( $form_shortcode ) ) : ?>
				<div class="rg-contact__form-wrap" data-reveal data-reveal-delay="0.15">
					<div class="rg-contact__form">
						<?php echo do_shortcode( $form_shortcode ); ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
