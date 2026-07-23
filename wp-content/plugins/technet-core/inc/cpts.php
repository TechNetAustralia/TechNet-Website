<?php
/**
 * Custom post types: tn_speaker, tn_session, tn_registration,
 * tn_award_nomination — plus their admin meta boxes. None of these are
 * publicly queryable single pages; they're read through the theme's
 * technet_get_event_sessions_by_day() / technet_get_event_speakers() /
 * admin CSV export instead (see inc/helpers.php, inc/csv-export.php).
 *
 * @package TechNet_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register all four custom post types.
 */
function technet_register_post_types() {
	register_post_type(
		'tn_speaker',
		array(
			'labels'       => array(
				'name'          => __( 'Speakers', 'technet-core' ),
				'singular_name' => __( 'Speaker', 'technet-core' ),
				'add_new_item'  => __( 'Add speaker', 'technet-core' ),
			),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-groups',
			'supports'     => array( 'title' ),
		)
	);

	register_post_type(
		'tn_session',
		array(
			'labels'       => array(
				'name'          => __( 'Sessions', 'technet-core' ),
				'singular_name' => __( 'Session', 'technet-core' ),
				'add_new_item'  => __( 'Add session', 'technet-core' ),
			),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-calendar-alt',
			'supports'     => array( 'title' ),
		)
	);

	register_post_type(
		'tn_registration',
		array(
			'labels'       => array(
				'name'          => __( 'Conference Registrations', 'technet-core' ),
				'singular_name' => __( 'Registration', 'technet-core' ),
			),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-clipboard',
			'supports'     => array( 'title' ),
			'capabilities' => array(
				'create_posts' => 'do_not_allow',
			),
			'map_meta_cap' => true,
		)
	);

	register_post_type(
		'tn_award_nomination',
		array(
			'labels'       => array(
				'name'          => __( 'NEATTS Nominations', 'technet-core' ),
				'singular_name' => __( 'Nomination', 'technet-core' ),
			),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-awards',
			'supports'     => array( 'title' ),
			'capabilities' => array(
				'create_posts' => 'do_not_allow',
			),
			'map_meta_cap' => true,
		)
	);
}
add_action( 'init', 'technet_register_post_types' );

/**
 * Options list of upcoming/recent tribe_events posts for the speaker/session
 * "linked event" field, falling back to a plain numeric ID field if The
 * Events Calendar isn't active.
 *
 * @return array<int,string> post_id => post_title
 */
function technet_event_options() {
	if ( ! post_type_exists( 'tribe_events' ) ) {
		return array();
	}
	$events = get_posts(
		array(
			'post_type'      => 'tribe_events',
			'posts_per_page' => 50,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);
	$options = array();
	foreach ( $events as $event ) {
		$options[ $event->ID ] = $event->post_title;
	}
	return $options;
}

/**
 * Register meta boxes for tn_speaker and tn_session.
 */
function technet_register_meta_boxes() {
	add_meta_box( 'technet_speaker_details', __( 'Speaker details', 'technet-core' ), 'technet_render_speaker_meta_box', 'tn_speaker', 'normal', 'high' );
	add_meta_box( 'technet_session_details', __( 'Session details', 'technet-core' ), 'technet_render_session_meta_box', 'tn_session', 'normal', 'high' );
	add_meta_box( 'technet_registration_details', __( 'Registration details', 'technet-core' ), 'technet_render_registration_meta_box', 'tn_registration', 'normal', 'high' );
	add_meta_box( 'technet_nomination_details', __( 'Nomination details', 'technet-core' ), 'technet_render_nomination_meta_box', 'tn_award_nomination', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'technet_register_meta_boxes' );

/**
 * Render + save helpers share a nonce field name per post type.
 */
function technet_meta_nonce_field( $post_type ) {
	wp_nonce_field( 'technet_save_' . $post_type, 'technet_' . $post_type . '_nonce' );
}

function technet_event_picker_field( $name, $selected ) {
	$options = technet_event_options();
	if ( empty( $options ) ) {
		printf(
			'<input type="number" name="%1$s" value="%2$s" class="regular-text" placeholder="%3$s">',
			esc_attr( $name ),
			esc_attr( $selected ),
			esc_attr__( 'Event post ID (The Events Calendar not active)', 'technet-core' )
		);
		return;
	}
	echo '<select name="' . esc_attr( $name ) . '">';
	echo '<option value="">' . esc_html__( '— Select an event —', 'technet-core' ) . '</option>';
	foreach ( $options as $id => $label ) {
		printf( '<option value="%1$s"%2$s>%3$s</option>', esc_attr( $id ), selected( $selected, $id, false ), esc_html( $label ) );
	}
	echo '</select>';
}

function technet_render_speaker_meta_box( $post ) {
	technet_meta_nonce_field( 'tn_speaker' );
	$role        = get_post_meta( $post->ID, '_technet_role', true );
	$institution = get_post_meta( $post->ID, '_technet_institution', true );
	$event_id    = get_post_meta( $post->ID, '_technet_event_id', true );
	?>
	<p>
		<label><?php esc_html_e( 'Role / title', 'technet-core' ); ?><br>
		<input type="text" name="technet_role" value="<?php echo esc_attr( $role ); ?>" class="regular-text"></label>
	</p>
	<p>
		<label><?php esc_html_e( 'Institution', 'technet-core' ); ?><br>
		<input type="text" name="technet_institution" value="<?php echo esc_attr( $institution ); ?>" class="regular-text"></label>
	</p>
	<p>
		<label><?php esc_html_e( 'Conference event', 'technet-core' ); ?><br>
		<?php technet_event_picker_field( 'technet_event_id', $event_id ); ?></label>
	</p>
	<?php
}

function technet_render_session_meta_box( $post ) {
	technet_meta_nonce_field( 'tn_session' );
	$day      = get_post_meta( $post->ID, '_technet_day', true );
	$time     = get_post_meta( $post->ID, '_technet_time', true );
	$track    = get_post_meta( $post->ID, '_technet_track', true );
	$event_id = get_post_meta( $post->ID, '_technet_event_id', true );
	?>
	<p>
		<label><?php esc_html_e( 'Day label (e.g. "Day 1 · Tue")', 'technet-core' ); ?><br>
		<input type="text" name="technet_day" value="<?php echo esc_attr( $day ); ?>" class="regular-text"></label>
	</p>
	<p>
		<label><?php esc_html_e( 'Time — 24-hour HH:MM, so sessions sort correctly (e.g. "09:00")', 'technet-core' ); ?><br>
		<input type="text" name="technet_time" value="<?php echo esc_attr( $time ); ?>" pattern="[0-2][0-9]:[0-5][0-9]" class="regular-text"></label>
	</p>
	<p>
		<label><?php esc_html_e( 'Track / tag', 'technet-core' ); ?><br>
		<input type="text" name="technet_track" value="<?php echo esc_attr( $track ); ?>" class="regular-text"></label>
	</p>
	<p>
		<label><?php esc_html_e( 'Conference event', 'technet-core' ); ?><br>
		<?php technet_event_picker_field( 'technet_event_id', $event_id ); ?></label>
	</p>
	<?php
}

function technet_render_registration_meta_box( $post ) {
	$fields = array( 'email', 'institution', 'pass_type', 'google_group_optin', 'event_id' );
	echo '<table class="widefat"><tbody>';
	foreach ( $fields as $field ) {
		printf(
			'<tr><th style="width:180px;">%1$s</th><td>%2$s</td></tr>',
			esc_html( ucwords( str_replace( '_', ' ', $field ) ) ),
			esc_html( get_post_meta( $post->ID, '_technet_' . $field, true ) )
		);
	}
	echo '</tbody></table>';
}

function technet_render_nomination_meta_box( $post ) {
	$fields = array( 'nominator_name', 'nominator_email', 'nominee_name', 'nominee_institution', 'reason' );
	echo '<table class="widefat"><tbody>';
	foreach ( $fields as $field ) {
		printf(
			'<tr><th style="width:180px;">%1$s</th><td>%2$s</td></tr>',
			esc_html( ucwords( str_replace( '_', ' ', $field ) ) ),
			esc_html( get_post_meta( $post->ID, '_technet_' . $field, true ) )
		);
	}
	echo '</tbody></table>';
}

/**
 * Save speaker/session meta boxes (registration + nomination meta are
 * written only by the front-end form handlers in inc/forms.php, so they
 * have no save_post handler here — the meta box above is read-only).
 */
function technet_save_meta_boxes( $post_id, $post ) {
	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
		return;
	}

	if ( 'tn_speaker' === $post->post_type ) {
		if ( ! isset( $_POST['technet_tn_speaker_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['technet_tn_speaker_nonce'] ), 'technet_save_tn_speaker' ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		update_post_meta( $post_id, '_technet_role', isset( $_POST['technet_role'] ) ? sanitize_text_field( wp_unslash( $_POST['technet_role'] ) ) : '' );
		update_post_meta( $post_id, '_technet_institution', isset( $_POST['technet_institution'] ) ? sanitize_text_field( wp_unslash( $_POST['technet_institution'] ) ) : '' );
		update_post_meta( $post_id, '_technet_event_id', isset( $_POST['technet_event_id'] ) ? absint( $_POST['technet_event_id'] ) : '' );
	}

	if ( 'tn_session' === $post->post_type ) {
		if ( ! isset( $_POST['technet_tn_session_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['technet_tn_session_nonce'] ), 'technet_save_tn_session' ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		update_post_meta( $post_id, '_technet_day', isset( $_POST['technet_day'] ) ? sanitize_text_field( wp_unslash( $_POST['technet_day'] ) ) : '' );
		update_post_meta( $post_id, '_technet_time', isset( $_POST['technet_time'] ) ? sanitize_text_field( wp_unslash( $_POST['technet_time'] ) ) : '' );
		update_post_meta( $post_id, '_technet_track', isset( $_POST['technet_track'] ) ? sanitize_text_field( wp_unslash( $_POST['technet_track'] ) ) : '' );
		update_post_meta( $post_id, '_technet_event_id', isset( $_POST['technet_event_id'] ) ? absint( $_POST['technet_event_id'] ) : '' );
	}
}
add_action( 'save_post', 'technet_save_meta_boxes', 10, 2 );
