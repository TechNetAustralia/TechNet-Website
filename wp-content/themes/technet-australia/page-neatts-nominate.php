<?php
/**
 * Template Name: NEATTS Nominate
 *
 * A nomination form for the NEATTS award, in the same visual language as
 * ui_kits/conference-microsite/RegisterPage.jsx (the design system has no
 * bespoke nomination-form design, so this reuses that pattern rather than
 * inventing a new one). Submits to technet-core's admin-post handler
 * `technet_neatts_nominate`.
 *
 * @package TechNet_Australia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="technet-page technet-form">
	<h2 class="technet-form__title"><?php esc_html_e( 'Nominate a colleague for NEATTS', 'technet-australia' ); ?></h2>

	<?php if ( isset( $_GET['nominated'] ) && '1' === $_GET['nominated'] ) : ?>
		<div class="technet-form__notice technet-form__notice--success">
			<?php esc_html_e( "Thanks — your nomination has been received. TechNet's committee reviews nominations ahead of the national conference.", 'technet-australia' ); ?>
		</div>
	<?php elseif ( isset( $_GET['technet_error'] ) ) : ?>
		<div class="technet-form__notice technet-form__notice--error">
			<?php esc_html_e( 'Something was missing from that submission — please check the form and try again.', 'technet-australia' ); ?>
		</div>
	<?php endif; ?>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="technet_neatts_nominate">
		<?php wp_nonce_field( 'technet_neatts_nominate', 'technet_neatts_nominate_nonce' ); ?>

		<div class="technet-form__fields">
			<?php
			echo technet_input( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				array(
					'label'       => __( 'Your name', 'technet-australia' ),
					'name'        => 'nominator_name',
					'placeholder' => 'Jordan Lee',
					'required'    => true,
				)
			);
			echo technet_input( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				array(
					'label'       => __( 'Your institution email', 'technet-australia' ),
					'name'        => 'nominator_email',
					'type'        => 'email',
					'placeholder' => 'you@university.edu.au',
					'required'    => true,
				)
			);
			echo technet_input( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				array(
					'label'       => __( 'Nominee name', 'technet-australia' ),
					'name'        => 'nominee_name',
					'placeholder' => 'Alison Ford',
					'required'    => true,
				)
			);
			echo technet_input( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				array(
					'label'       => __( 'Nominee institution', 'technet-australia' ),
					'name'        => 'nominee_institution',
					'placeholder' => 'UNSW',
					'required'    => true,
				)
			);
			?>
			<label class="tn-field" for="tn-field-reason">
				<span><?php esc_html_e( 'Why are you nominating them?', 'technet-australia' ); ?> *</span>
				<textarea class="tn-input" id="tn-field-reason" name="reason" rows="5" required></textarea>
			</label>

			<?php
			echo technet_button( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				array(
					'label'   => __( 'Submit nomination', 'technet-australia' ),
					'variant' => 'accent',
					'type'    => 'submit',
				)
			);
			?>
		</div>
	</form>
</div>

<?php get_footer(); ?>
