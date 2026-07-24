<?php
/**
 * Closes <main>, renders visual footer (ported from Footer.jsx), and
 * closes the document.
 *
 * @package TechNet_Australia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
</main>

<?php get_template_part( 'template-parts/footer' ); ?>

<?php wp_footer(); ?>
</body>
</html>
