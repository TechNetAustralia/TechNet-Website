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

<div class="technet-page" style="max-width: var(--container-max);">
	<?php if ( have_posts() ) : ?>
		<div class="technet-grid-3">
			<?php
			while ( have_posts() ) :
				the_post();
				$image_url = get_the_post_thumbnail_url( get_the_ID(), 'medium_large' );
				$body      = sprintf(
					'<h3 style="margin: 0 0 var(--space-2);"><a href="%1$s" style="color: inherit; text-decoration: none;">%2$s</a></h3>',
					esc_url( get_permalink() ),
					esc_html( get_the_title() )
				);
				$body     .= '<p style="color: var(--text-secondary); font-size: 14px; margin: 0;">' . esc_html( wp_trim_words( get_the_excerpt(), 20 ) ) . '</p>';
				echo technet_media_card( $image_url, get_the_title(), $body ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			endwhile;
			?>
		</div>
	<?php else : ?>
		<h1 class="technet-page__title"><?php esc_html_e( 'Nothing found', 'technet-australia' ); ?></h1>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
