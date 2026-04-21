<?php
/**
 * Section: Contact (dark bg, 2-column, form submits via admin-ajax)
 *
 * @package RGeometry
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow    = rgeometry_field( 'contact_eyebrow', 'Contact' );
$heading    = rgeometry_field( 'contact_heading', 'Ready to talk about your project?' );
$subheading = rgeometry_field( 'contact_subheading', "No pitch. No pressure. Just a 30-minute conversation to see if we're the right fit." );
$address    = rgeometry_field( 'contact_address', '201 Sikes Hall, Clemson, SC 29634' );
$phone      = rgeometry_field( 'contact_phone',   '(864) 207-0500' );
$email      = rgeometry_field( 'contact_email',   'hello@rgeometry.com' );

$project_types = rgeometry_field( 'contact_project_types', array(
	array( 'label' => 'Custom Home' ),
	array( 'label' => 'Renovation' ),
	array( 'label' => 'Commercial' ),
	array( 'label' => 'Not Sure' ),
) );
?>
<section id="contact" class="rg-section rg-contact">
	<div class="rg-container">
		<div class="rg-contact__grid">
			<div class="rg-contact__intro" data-reveal>
				<p class="rg-eyebrow rg-eyebrow--on-dark"><?php echo esc_html( $eyebrow ); ?></p>
				<h2 class="rg-section__heading rg-contact__heading"><?php echo esc_html( $heading ); ?></h2>
				<p class="rg-contact__body"><?php echo esc_html( $subheading ); ?></p>

				<ul class="rg-contact__meta">
					<li><?php echo rgeometry_icon( 'map-pin' ); ?><span><?php echo esc_html( $address ); ?></span></li>
					<li><?php echo rgeometry_icon( 'phone' ); ?><span><?php echo esc_html( $phone ); ?></span></li>
					<li><?php echo rgeometry_icon( 'mail' ); ?><span><?php echo esc_html( $email ); ?></span></li>
				</ul>
			</div>

			<div class="rg-contact__form-wrap" data-reveal data-reveal-delay="0.15">
				<form class="rg-contact__form" data-contact-form autocomplete="on">
					<div class="rg-field">
						<label class="rg-field__label">Name</label>
						<input type="text" name="name" required class="rg-field__input" placeholder="Your name" />
					</div>
					<div class="rg-field">
						<label class="rg-field__label">Email</label>
						<input type="email" name="email" required class="rg-field__input" placeholder="your@email.com" />
					</div>
					<div class="rg-field">
						<label class="rg-field__label">Project Type</label>
						<select name="project_type" required class="rg-field__input" defaultValue="">
							<option value="" disabled selected>Select one</option>
							<?php foreach ( (array) $project_types as $opt ) : if ( empty( $opt['label'] ) ) continue; ?>
								<option value="<?php echo esc_attr( $opt['label'] ); ?>"><?php echo esc_html( $opt['label'] ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="rg-field">
						<label class="rg-field__label">Message</label>
						<textarea name="message" rows="4" class="rg-field__input rg-field__input--textarea" placeholder="Tell us a bit about your project..."></textarea>
					</div>
					<button type="submit" class="rg-btn rg-btn--primary rg-contact__submit">Send It</button>
					<p class="rg-contact__status" data-contact-status aria-live="polite"></p>
				</form>

				<div class="rg-contact__thanks" data-contact-thanks hidden>
					<h3 class="rg-contact__thanks-title">Thanks for reaching out.</h3>
					<p class="rg-contact__thanks-body">We'll be in touch within 48 hours.</p>
				</div>
			</div>
		</div>
	</div>
</section>
