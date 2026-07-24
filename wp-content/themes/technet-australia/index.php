<?php
/**
 * Fallback template — required by WordPress for every classic theme (the
 * template hierarchy's last resort). Not part of any UI kit; front-page.php
 * handles the homepage and page.php/single.php handle their own post types,
 * so this only renders for anything else (search results, date archives,
 * category/tag archives, etc).
 *
 * @package TechNet_Australia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="technet-page">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article class="technet-page__content" style="margin-bottom: var(--space-7);">
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<?php the_excerpt(); ?>
			</article>
		<?php endwhile; ?>
	<?php else : ?>
		<h1 class="technet-page__title"><?php esc_html_e( 'Nothing found', 'technet-australia' ); ?></h1>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
