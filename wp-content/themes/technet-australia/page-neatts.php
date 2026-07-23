<?php
/**
 * Template Name: NEATTS
 *
 * Ports ui_kits/marketing-site/NeattsPage.jsx.
 *
 * @package TechNet_Australia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$recipient_name = get_option( 'technet_neatts_recipient_name', '' );
$recipient_year  = get_option( 'technet_neatts_recipient_year', gmdate( 'Y' ) );
?>

<div class="technet-page">
	<h1 class="technet-page__title"><?php esc_html_e( 'National Excellence Award for Tertiary Technical Staff', 'technet-australia' ); ?></h1>
	<p class="technet-page__lead"><?php esc_html_e( 'NEATTS recognises outstanding contribution by technical and scientific professional staff, awarded annually at the national conference.', 'technet-australia' ); ?></p>

	<div class="technet-grid-2" style="margin-top: var(--space-6);">
		<?php
		$recipient_html  = '<h3 style="font-size:var(--fs-h4);margin-bottom:8px;">' . esc_html( sprintf( /* translators: %s: award year */ __( '%s Recipient', 'technet-australia' ), $recipient_year ) ) . '</h3>';
		$recipient_html .= '<p style="color:var(--text-secondary);font-size:14px;">';
		$recipient_html .= $recipient_name
			? esc_html( $recipient_name )
			: esc_html__( 'Announced each year at the closing conference dinner.', 'technet-australia' );
		$recipient_html .= '</p>';
		echo technet_card( $recipient_html ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		$nominate_html  = '<h3 style="font-size:var(--fs-h4);margin-bottom:8px;">' . esc_html__( 'Nominate a colleague', 'technet-australia' ) . '</h3>';
		$nominate_html .= '<p style="color:var(--text-secondary);font-size:14px;margin-bottom:16px;">' . esc_html__( 'Nominations open ahead of the national conference each year.', 'technet-australia' ) . '</p>';
		$nominate_html .= technet_button(
			array(
				'label'   => __( 'Start a nomination', 'technet-australia' ),
				'variant' => 'secondary',
				'size'    => 'sm',
				'href'    => home_url( '/neatts-nominate/' ),
			)
		);
		echo technet_card( $nominate_html ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
	</div>
</div>

<?php get_footer(); ?>
