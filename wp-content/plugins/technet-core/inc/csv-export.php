<?php
/**
 * Admin-only CSV export for conference registrations and NEATTS
 * nominations — Tools -> TechNet Exports.
 *
 * @package TechNet_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function technet_register_export_page() {
	add_management_page(
		__( 'TechNet Exports', 'technet-core' ),
		__( 'TechNet Exports', 'technet-core' ),
		'manage_options',
		'technet-exports',
		'technet_render_export_page'
	);
}
add_action( 'admin_menu', 'technet_register_export_page' );

function technet_render_export_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'TechNet Exports', 'technet-core' ); ?></h1>
		<p>
			<a class="button button-primary" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=technet_export_registrations' ), 'technet_export' ) ); ?>">
				<?php esc_html_e( 'Download conference registrations (CSV)', 'technet-core' ); ?>
			</a>
		</p>
		<p>
			<a class="button button-primary" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=technet_export_nominations' ), 'technet_export' ) ); ?>">
				<?php esc_html_e( 'Download NEATTS nominations (CSV)', 'technet-core' ); ?>
			</a>
		</p>
	</div>
	<?php
}

/**
 * Stream a CSV of the given post type + meta fields and exit.
 *
 * @param string   $post_type
 * @param string   $filename
 * @param string[] $meta_fields Meta keys without the leading underscore.
 */
function technet_stream_csv_export( $post_type, $filename, $meta_fields ) {
	if ( ! current_user_can( 'manage_options' ) || ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ), 'technet_export' ) ) {
		wp_die( esc_html__( 'You are not allowed to export this.', 'technet-core' ) );
	}

	$posts = get_posts(
		array(
			'post_type'      => $post_type,
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);

	nocache_headers();
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=' . $filename );

	$out = fopen( 'php://output', 'w' );
	fputcsv( $out, array_merge( array( 'title', 'submitted' ), $meta_fields ) );

	foreach ( $posts as $post ) {
		$row = array( $post->post_title, $post->post_date );
		foreach ( $meta_fields as $field ) {
			$row[] = get_post_meta( $post->ID, '_technet_' . $field, true );
		}
		fputcsv( $out, $row );
	}
	fclose( $out );
	exit;
}

function technet_export_registrations() {
	technet_stream_csv_export(
		'tn_registration',
		'technet-registrations-' . gmdate( 'Y-m-d' ) . '.csv',
		array( 'email', 'institution', 'pass_type', 'google_group_optin', 'event_id' )
	);
}
add_action( 'admin_post_technet_export_registrations', 'technet_export_registrations' );

function technet_export_nominations() {
	technet_stream_csv_export(
		'tn_award_nomination',
		'technet-neatts-nominations-' . gmdate( 'Y-m-d' ) . '.csv',
		array( 'nominator_name', 'nominator_email', 'nominee_name', 'nominee_institution', 'reason' )
	);
}
add_action( 'admin_post_technet_export_nominations', 'technet_export_nominations' );
