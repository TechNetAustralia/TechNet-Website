<?php
/**
 * Visual site footer — ports ui_kits/marketing-site/Footer.jsx.
 *
 * @package TechNet_Australia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<footer class="technet-footer">
	<div class="technet-footer__inner technet-container">
		<div class="technet-footer__copy">
			&copy; 2000&ndash;<?php echo esc_html( gmdate( 'Y' ) ); ?> TechNet Australia. A volunteer, not-for-profit association.
		</div>
		<?php technet_footer_nav(); ?>
	</div>
</footer>
