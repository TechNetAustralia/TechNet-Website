<?php
/**
 * Document head + opening <body>/<main>. Visual header content (wordmark,
 * nav, CTA — ported from Header.jsx) lives in template-parts/header.php so
 * this file stays a plain WordPress document skeleton.
 *
 * @package TechNet_Australia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="screen-reader-text" href="#technet-main"><?php esc_html_e( 'Skip to content', 'technet-australia' ); ?></a>

<?php get_template_part( 'template-parts/header' ); ?>

<main id="technet-main">
