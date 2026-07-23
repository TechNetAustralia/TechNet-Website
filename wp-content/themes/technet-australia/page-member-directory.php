<?php
/**
 * Template Name: Member Directory
 *
 * Ports ui_kits/member-directory/index.html. Public visitors get the navy
 * hero + a teaser CTA; the searchable directory itself is gated to logged-in
 * Paid Memberships Pro members via technet_is_member(), matching the plan's
 * "membership content" gating decision. Falls back to a friendly empty
 * state (rather than an empty list) if Paid Memberships Pro isn't active.
 *
 * @package TechNet_Australia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$is_member = technet_is_member();
$members   = ( $is_member && function_exists( 'technet_get_active_members' ) ) ? technet_get_active_members() : array();
?>

<section class="technet-hero technet-hero--inverse">
	<div class="technet-hero__kicker">540+ members</div>
	<h1 class="technet-hero__title">The TechNet Google Group</h1>
	<p class="technet-hero__lead">A shared space for technical and scientific professional staff to trade problems, solutions and ideas across institutions.</p>
	<div class="technet-hero__actions">
		<?php
		echo technet_button( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'label'   => __( 'Request to join', 'technet-australia' ),
				'variant' => 'accent',
				'href'    => 'https://groups.google.com/',
			)
		);
		?>
	</div>
</section>

<section class="technet-page">
	<div class="technet-directory-toolbar">
		<h2><?php esc_html_e( 'Member directory', 'technet-australia' ); ?></h2>
		<?php if ( $is_member ) : ?>
			<div class="technet-directory-search">
				<?php
				echo technet_input( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					array(
						'id'          => 'technet-directory-search',
						'placeholder' => __( 'Search by institution or discipline', 'technet-australia' ),
					)
				);
				?>
			</div>
		<?php endif; ?>
	</div>

	<?php if ( ! $is_member ) : ?>
		<div class="technet-directory-empty">
			<?php if ( is_user_logged_in() ) : ?>
				<?php esc_html_e( 'The full member directory is available to active TechNet members. Join the Google Group above to get access.', 'technet-australia' ); ?>
			<?php else : ?>
				<?php
				printf(
					/* translators: %s: log in URL */
					wp_kses( __( 'The full member directory is available to active TechNet members. <a href="%s">Log in</a> or request to join above.', 'technet-australia' ), array( 'a' => array( 'href' => array() ) ) ),
					esc_url( wp_login_url( get_permalink() ) )
				);
				?>
			<?php endif; ?>
		</div>
	<?php elseif ( empty( $members ) ) : ?>
		<div class="technet-directory-empty">
			<?php esc_html_e( 'No members in the directory yet.', 'technet-australia' ); ?>
		</div>
	<?php else : ?>
		<div class="technet-list technet-list--tight" id="technet-directory-list">
			<?php foreach ( $members as $member ) : ?>
				<?php
				$searchable = strtolower( $member['name'] . ' ' . $member['institution'] . ' ' . $member['discipline'] );
				$row  = '<div class="technet-avatar"></div>';
				$row .= '<div style="flex:1;"><div class="technet-list-row__title">' . esc_html( $member['name'] ) . '</div>';
				$row .= '<div class="technet-list-row__meta">' . esc_html( $member['institution'] ) . '</div></div>';
				$row .= technet_tag( $member['discipline'] );
				$row .= technet_badge( $member['status'], 'New' === $member['status'] ? 'info' : 'success' );
				printf(
					'<div class="technet-directory-row" data-search="%1$s">%2$s</div>',
					esc_attr( $searchable ),
					technet_card( $row, array( 'row' => true, 'class' => 'technet-list-row' ) ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				);
				?>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</section>

<?php get_footer(); ?>
