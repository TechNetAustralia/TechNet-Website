<?php
/**
 * 404 template.
 *
 * @package TechNet_Australia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="technet-page" style="text-align:center;">
	<h1 class="technet-page__title"><?php esc_html_e( 'Page not found', 'technet-australia' ); ?></h1>
	<p class="technet-page__lead" style="margin: 0 auto;"><?php esc_html_e( "The page you're looking for doesn't exist or has moved.", 'technet-australia' ); ?></p>
	<p style="margin-top: var(--space-6);">
		<?php
		echo technet_button( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'label'   => __( 'Back to the homepage', 'technet-australia' ),
				'variant' => 'accent',
				'href'    => home_url( '/' ),
			)
		);
		?>
	</p>
</div>

<?php get_footer(); ?>
