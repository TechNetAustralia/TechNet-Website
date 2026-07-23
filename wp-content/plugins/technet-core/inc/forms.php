<?php
/**
 * admin-post.php handlers for the two front-end forms ported from the
 * design system: conference registration (RegisterPage.jsx, submitted from
 * single-tribe_events.php) and NEATTS nomination (from
 * page-neatts-nominate.php). Both are open to logged-out visitors — a
 * prospective member registering for their first conference isn't a
 * TechNet member yet — so each is hooked for both admin_post_{action} and
 * admin_post_nopriv_{action}.
 *
 * @package TechNet_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle conference registration submissions.
 */
function technet_handle_conference_register() {
	$redirect = wp_get_referer() ? wp_get_referer() : home_url( '/' );

	if (
		! isset( $_POST['technet_conference_register_nonce'] ) ||
		! wp_verify_nonce( sanitize_key( $_POST['technet_conference_register_nonce'] ), 'technet_conference_register' )
	) {
		wp_safe_redirect( add_query_arg( 'technet_error', 'nonce', $redirect ) );
		exit;
	}

	$full_name = isset( $_POST['full_name'] ) ? sanitize_text_field( wp_unslash( $_POST['full_name'] ) ) : '';
	$email     = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

	if ( '' === $full_name || ! is_email( $email ) ) {
		wp_safe_redirect( add_query_arg( 'technet_error', 'required', $redirect ) );
		exit;
	}

	$institution = isset( $_POST['institution'] ) ? sanitize_text_field( wp_unslash( $_POST['institution'] ) ) : '';
	$pass_type   = isset( $_POST['pass_type'] ) && 'day' === $_POST['pass_type'] ? 'day' : 'full';
	$optin       = ! empty( $_POST['google_group_optin'] ) ? 'yes' : 'no';
	$event_id    = isset( $_POST['event_id'] ) ? absint( $_POST['event_id'] ) : 0;

	$registration_id = wp_insert_post(
		array(
			'post_type'   => 'tn_registration',
			'post_title'  => $full_name,
			'post_status' => 'publish',
		)
	);

	if ( is_wp_error( $registration_id ) || ! $registration_id ) {
		wp_safe_redirect( add_query_arg( 'technet_error', 'save_failed', $redirect ) );
		exit;
	}

	update_post_meta( $registration_id, '_technet_email', $email );
	update_post_meta( $registration_id, '_technet_institution', $institution );
	update_post_meta( $registration_id, '_technet_pass_type', $pass_type );
	update_post_meta( $registration_id, '_technet_google_group_optin', $optin );
	update_post_meta( $registration_id, '_technet_event_id', $event_id );

	wp_mail(
		get_option( 'admin_email' ),
		sprintf( /* translators: %s: registrant name */ __( 'New conference registration: %s', 'technet-core' ), $full_name ),
		sprintf(
			"%1\$s <%2\$s>\n%3\$s\n%4\$s pass\nGoogle Group opt-in: %5\$s",
			$full_name,
			$email,
			$institution,
			ucfirst( $pass_type ),
			$optin
		)
	);

	wp_safe_redirect( add_query_arg( 'registered', '1', $redirect ) );
	exit;
}
add_action( 'admin_post_technet_conference_register', 'technet_handle_conference_register' );
add_action( 'admin_post_nopriv_technet_conference_register', 'technet_handle_conference_register' );

/**
 * Handle NEATTS nomination submissions.
 */
function technet_handle_neatts_nominate() {
	$redirect = wp_get_referer() ? wp_get_referer() : home_url( '/' );

	if (
		! isset( $_POST['technet_neatts_nominate_nonce'] ) ||
		! wp_verify_nonce( sanitize_key( $_POST['technet_neatts_nominate_nonce'] ), 'technet_neatts_nominate' )
	) {
		wp_safe_redirect( add_query_arg( 'technet_error', 'nonce', $redirect ) );
		exit;
	}

	$nominator_name  = isset( $_POST['nominator_name'] ) ? sanitize_text_field( wp_unslash( $_POST['nominator_name'] ) ) : '';
	$nominator_email = isset( $_POST['nominator_email'] ) ? sanitize_email( wp_unslash( $_POST['nominator_email'] ) ) : '';
	$nominee_name    = isset( $_POST['nominee_name'] ) ? sanitize_text_field( wp_unslash( $_POST['nominee_name'] ) ) : '';

	if ( '' === $nominator_name || ! is_email( $nominator_email ) || '' === $nominee_name ) {
		wp_safe_redirect( add_query_arg( 'technet_error', 'required', $redirect ) );
		exit;
	}

	$nominee_institution = isset( $_POST['nominee_institution'] ) ? sanitize_text_field( wp_unslash( $_POST['nominee_institution'] ) ) : '';
	$reason              = isset( $_POST['reason'] ) ? sanitize_textarea_field( wp_unslash( $_POST['reason'] ) ) : '';

	$nomination_id = wp_insert_post(
		array(
			'post_type'   => 'tn_award_nomination',
			/* translators: %s: nominee name */
			'post_title'  => sprintf( __( 'Nomination: %s', 'technet-core' ), $nominee_name ),
			'post_status' => 'publish',
		)
	);

	if ( is_wp_error( $nomination_id ) || ! $nomination_id ) {
		wp_safe_redirect( add_query_arg( 'technet_error', 'save_failed', $redirect ) );
		exit;
	}

	update_post_meta( $nomination_id, '_technet_nominator_name', $nominator_name );
	update_post_meta( $nomination_id, '_technet_nominator_email', $nominator_email );
	update_post_meta( $nomination_id, '_technet_nominee_name', $nominee_name );
	update_post_meta( $nomination_id, '_technet_nominee_institution', $nominee_institution );
	update_post_meta( $nomination_id, '_technet_reason', $reason );

	wp_mail(
		get_option( 'admin_email' ),
		sprintf( /* translators: %s: nominee name */ __( 'New NEATTS nomination: %s', 'technet-core' ), $nominee_name ),
		sprintf(
			"Nominated by %1\$s <%2\$s>\nNominee: %3\$s, %4\$s\n\n%5\$s",
			$nominator_name,
			$nominator_email,
			$nominee_name,
			$nominee_institution,
			$reason
		)
	);

	wp_safe_redirect( add_query_arg( 'nominated', '1', $redirect ) );
	exit;
}
add_action( 'admin_post_technet_neatts_nominate', 'technet_handle_neatts_nominate' );
add_action( 'admin_post_nopriv_technet_neatts_nominate', 'technet_handle_neatts_nominate' );
