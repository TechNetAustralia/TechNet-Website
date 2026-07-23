<?php
/**
 * Member profile fields (institution, discipline) shown on the WP profile
 * screen, plus technet_get_active_members() which the theme's member
 * directory template calls. Wraps Paid Memberships Pro's own tables/API and
 * degrades to an empty array if PMP isn't active, rather than fataling.
 *
 * @package TechNet_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the extra profile fields.
 *
 * @param WP_User $user
 */
function technet_profile_fields( $user ) {
	?>
	<h2><?php esc_html_e( 'TechNet member directory', 'technet-core' ); ?></h2>
	<table class="form-table">
		<tr>
			<th><label for="technet_institution"><?php esc_html_e( 'Institution', 'technet-core' ); ?></label></th>
			<td><input type="text" id="technet_institution" name="technet_institution" value="<?php echo esc_attr( get_user_meta( $user->ID, 'technet_institution', true ) ); ?>" class="regular-text"></td>
		</tr>
		<tr>
			<th><label for="technet_discipline"><?php esc_html_e( 'Discipline', 'technet-core' ); ?></label></th>
			<td><input type="text" id="technet_discipline" name="technet_discipline" value="<?php echo esc_attr( get_user_meta( $user->ID, 'technet_discipline', true ) ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'e.g. Engineering, Science, Medicine, Arts, IT, Laboratory', 'technet-core' ); ?>"></td>
		</tr>
	</table>
	<?php
}
add_action( 'show_user_profile', 'technet_profile_fields' );
add_action( 'edit_user_profile', 'technet_profile_fields' );

/**
 * Save the extra profile fields.
 *
 * @param int $user_id
 */
function technet_save_profile_fields( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return;
	}
	if ( isset( $_POST['technet_institution'] ) ) {
		update_user_meta( $user_id, 'technet_institution', sanitize_text_field( wp_unslash( $_POST['technet_institution'] ) ) );
	}
	if ( isset( $_POST['technet_discipline'] ) ) {
		update_user_meta( $user_id, 'technet_discipline', sanitize_text_field( wp_unslash( $_POST['technet_discipline'] ) ) );
	}
}
add_action( 'personal_options_update', 'technet_save_profile_fields' );
add_action( 'edit_user_profile_update', 'technet_save_profile_fields' );

/**
 * Active TechNet members for the member directory — wraps Paid Memberships
 * Pro's membership table. Returns an empty array (not a fatal) if PMP isn't
 * active, so the directory template can show a graceful empty state.
 *
 * A member is "New" if they joined (PMP's startdate on their active
 * membership row) within the last 60 days, "Active" otherwise — matching
 * the Active/New status badges in ui_kits/member-directory/index.html.
 *
 * @return array<int,array{name:string,institution:string,discipline:string,status:string}>
 */
function technet_get_active_members() {
	global $wpdb;

	if ( ! function_exists( 'pmpro_getMembershipLevelForUser' ) || ! isset( $wpdb->pmpro_memberships_users ) ) {
		return array();
	}

	$rows = $wpdb->get_results(
		"SELECT user_id, MIN(startdate) AS joined FROM {$wpdb->pmpro_memberships_users} WHERE status = 'active' GROUP BY user_id"
	);

	if ( empty( $rows ) ) {
		return array();
	}

	$new_cutoff = strtotime( '-60 days' );
	$members    = array();

	foreach ( $rows as $row ) {
		$user = get_userdata( $row->user_id );
		if ( ! $user ) {
			continue;
		}
		$joined_ts = $row->joined ? strtotime( $row->joined ) : 0;
		$members[] = array(
			'name'        => $user->display_name,
			'institution' => get_user_meta( $user->ID, 'technet_institution', true ),
			'discipline'  => get_user_meta( $user->ID, 'technet_discipline', true ),
			'status'      => ( $joined_ts && $joined_ts > $new_cutoff ) ? __( 'New', 'technet-core' ) : __( 'Active', 'technet-core' ),
		);
	}

	usort(
		$members,
		static function ( $a, $b ) {
			return strcasecmp( $a['name'], $b['name'] );
		}
	);

	return $members;
}
