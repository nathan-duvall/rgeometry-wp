<?php
/**
 * Contact form AJAX handler.
 *
 * Mirrors the Lovable reference's submit behavior (show a "thanks" state after
 * submit). Posts to admin-ajax, mails via wp_mail to the address configured in
 * the Contact field group, falls back to the site admin email.
 *
 * @package RGeometry
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_ajax_rgeometry_contact', 'rgeometry_contact_submit' );
add_action( 'wp_ajax_nopriv_rgeometry_contact', 'rgeometry_contact_submit' );
function rgeometry_contact_submit() {
	check_ajax_referer( 'rgeometry_contact', 'nonce' );

	$name         = isset( $_POST['name'] )         ? sanitize_text_field( wp_unslash( $_POST['name'] ) )         : '';
	$email        = isset( $_POST['email'] )        ? sanitize_email( wp_unslash( $_POST['email'] ) )              : '';
	$project_type = isset( $_POST['project_type'] ) ? sanitize_text_field( wp_unslash( $_POST['project_type'] ) ) : '';
	$message      = isset( $_POST['message'] )      ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) )   : '';

	if ( $name === '' || $email === '' || ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => 'Name and a valid email are required.' ), 400 );
	}

	$to = '';
	if ( function_exists( 'get_field' ) ) {
		$to = get_field( 'contact_recipient_email' );
	}
	if ( ! is_email( $to ) ) {
		$to = get_option( 'admin_email' );
	}

	$subject = sprintf( '[%s] New inquiry from %s', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ), $name );
	$body    = "Name: {$name}\nEmail: {$email}\nProject type: {$project_type}\n\nMessage:\n{$message}\n";
	$headers = array( 'Reply-To: ' . $name . ' <' . $email . '>' );

	$sent = wp_mail( $to, $subject, $body, $headers );

	if ( $sent ) {
		wp_send_json_success( array( 'message' => "Thanks for reaching out. We'll be in touch within 48 hours." ) );
	}
	wp_send_json_error( array( 'message' => 'Email failed to send. Try again in a minute.' ), 500 );
}
