<?php
/**
 * Default single-post template. Not part of any UI kit — the design system
 * doesn't include a blog/news pattern — kept minimal and on-brand via the
 * same tokens/components as everything else.
 *
 * @package TechNet_Australia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article class="technet-page">
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="technet-post-banner"><?php the_post_thumbnail( 'large' ); ?></div>
		<?php endif; ?>
		<?php echo technet_badge( get_the_date(), 'neutral' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<h1 class="technet-page__title"><?php the_title(); ?></h1>
		<div class="technet-page__content">
			<?php the_content(); ?>
		</div>
	</article>
	<?php
endwhile;

get_footer();
