<?php
/**
  * Visual site header — ports ui_kits/marketing-site/Header.jsx: navy bar,
 * real logo (assets/images/logo-dark.png — reversed/white lockup supplied
 * for TechNet's dark header background), nav, and an accent CTA button
 * (text/link editable via Appearance -> Customize -> Header, see
 * inc/customizer.php).
 *
 * @package TechNet_Australia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<header class="technet-header">
	<div class="technet-header__inner technet-container">
				<a class="technet-header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/logo-dark.png' ); ?>" alt="<?php esc_attr_e( 'TechNet Australia', 'technet-australia' ); ?>" width="326" height="104" />
		</a>

		<nav class="technet-header__nav" aria-label="<?php esc_attr_e( 'Primary', 'technet-australia' ); ?>">
			<?php technet_primary_nav(); ?>
		</nav>

		<?php
		echo technet_button( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- technet_button() escapes internally.
			array(
				'label'   => __( 'Join the Google Group', 'technet-australia' ),
				'variant' => 'accent',
				'size'    => 'sm',
				'href'    => 'https://groups.google.com/',
			)
		);
		?>
	</div>
</header>
