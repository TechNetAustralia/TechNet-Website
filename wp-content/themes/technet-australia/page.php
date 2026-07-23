<?php
/**
 * Default page template — plain editable WP content (e.g. "About", which
 * is in the nav but has no bespoke UI-kit design; see readme.md).
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
		<h1 class="technet-page__title"><?php the_title(); ?></h1>
		<div class="technet-page__content">
			<?php the_content(); ?>
		</div>
	</article>
	<?php
endwhile;

get_footer();
