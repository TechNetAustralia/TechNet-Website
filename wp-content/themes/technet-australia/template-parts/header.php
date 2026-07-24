<?php
/**
 * Visual site header — ports ui_kits/marketing-site/Header.jsx: navy bar,
 * wordmark ("TechNet.Australia" with a green full stop, per the design
 * system — no real logo was supplied, see readme.md "Iconography"), nav,
 * and an accent CTA button (text/link editable via Appearance -> Customize
 * -> Header, see inc/customizer.php).
 *
 * @package TechNet_Australia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<header class="technet-header">
	<div class="technet-header__inner technet-container">
		<a class="technet-header__wordmark" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			TechNet<span class="technet-header__wordmark-dot">.</span><span class="technet-header__wordmark-sub">Australia</span>
		</a>

		<nav class="technet-header__nav" aria-label="<?php esc_attr_e( 'Primary', 'technet-australia' ); ?>">
			<?php technet_primary_nav(); ?>
		</nav>

		<?php
		echo technet_button( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- technet_button() escapes internally.
			array(
				'label'   => get_theme_mod( 'technet_header_cta_label', 'Join the Google Group' ),
				'variant' => 'accent',
				'size'    => 'sm',
				'href'    => get_theme_mod( 'technet_header_cta_url', 'https://groups.google.com/' ),
			)
		);
		?>
	</div>
</header>
