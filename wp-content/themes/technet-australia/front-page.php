<?php
/**
 * Home — ports ui_kits/marketing-site/Home.jsx. Hero headline/lead text is
 * editable via the "Home" page (Pages -> Home) rather than hardcoded here;
 * see the $home_page_content fallback logic below.
 *
 * @package TechNet_Australia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$hero_banner  = technet_hero_banner_url();
$recent_posts = new WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => 3,
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
	)
);

// Editable hero headline/lead — pulled from the "Home" page (Pages -> Home
// in wp-admin) so it's editable like any other page, instead of hardcoded
// here. Falls back to the original copy if that page doesn't exist yet or
// is empty, so this never renders blank.
$home_page_id      = (int) get_option( 'page_on_front' );
$home_page         = $home_page_id ? get_post( $home_page_id ) : get_page_by_path( 'home' );
$home_page_content = $home_page ? apply_filters( 'the_content', $home_page->post_content ) : '';
?>

<section class="technet-hero<?php echo $hero_banner ? ' technet-hero--has-banner' : ''; ?>"<?php echo $hero_banner ? ' style="--hero-banner-image:url(' . esc_url( $hero_banner ) . ')"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_url() already applied to the dynamic part. ?>>
	<div class="technet-hero__kicker">Since 2000 &middot; 540+ members</div>
	<?php if ( trim( wp_strip_all_tags( $home_page_content ) ) ) : ?>
		<div class="technet-hero__editable"><?php echo $home_page_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- the_content filter already sanitizes/renders blocks. ?></div>
	<?php else : ?>
		<h1 class="technet-hero__title">Connecting technical &amp; scientific staff across Australian and NZ tertiary institutions</h1>
		<p class="technet-hero__lead">TechNet is the national network for the people who keep teaching and research running &mdash; across arts, science, medicine and engineering.</p>
		<div class="technet-hero__actions">
			<?php
			echo technet_button( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				array(
					'label'   => __( "This year's conference", 'technet-australia' ),
					'variant' => 'accent',
					'href'    => get_theme_mod( 'technet_conference_url', 'https://www.technetconference2026.com/' ),
				)
			);
			echo technet_button( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				array(
					'label'   => __( 'Join the Google Group', 'technet-australia' ),
					'variant' => 'secondary',
					'href'    => 'https://groups.google.com/',
				)
			);
			?>
		</div>
	<?php endif; ?>
</section>

<section class="technet-grid-3">
	<?php
	$what_we_do = array(
		array(
			'badge' => __( 'Annual', 'technet-australia' ),
			'title' => __( 'National Conference', 'technet-australia' ),
			'desc'  => __( 'A multi-day conference hosted by a different member university each year.', 'technet-australia' ),
		),
		array(
			'badge' => __( '3 cities', 'technet-australia' ),
			'title' => __( 'Regional Forums', 'technet-australia' ),
			'desc'  => __( 'One-day forums and mini-conferences in Adelaide, Brisbane and Sydney.', 'technet-australia' ),
		),
		array(
			'badge' => __( 'Annual', 'technet-australia' ),
			'title' => __( 'NEATTS Award', 'technet-australia' ),
			'desc'  => __( 'The National Excellence Award for Tertiary Technical Staff.', 'technet-australia' ),
		),
	);
	foreach ( $what_we_do as $item ) :
		$content  = technet_badge( $item['badge'], 'info' );
		$content .= '<h3>' . esc_html( $item['title'] ) . '</h3>';
		$content .= '<p>' . esc_html( $item['desc'] ) . '</p>';
		echo technet_card( $content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	endforeach;
	?>
</section>

<?php if ( $recent_posts->have_posts() ) : ?>
<section class="technet-page" style="max-width: var(--container-max);">
	<h2 style="font-size: var(--fs-h3); margin-bottom: var(--space-5);"><?php esc_html_e( 'Latest updates', 'technet-australia' ); ?></h2>
	<div class="technet-grid-3">
		<?php
		while ( $recent_posts->have_posts() ) :
			$recent_posts->the_post();
			$image_url = get_the_post_thumbnail_url( get_the_ID(), 'medium_large' );
			$body      = technet_badge( get_the_date(), 'neutral' );
			$body     .= sprintf(
				'<h3 style="margin: var(--space-3) 0 var(--space-2);"><a href="%1$s" style="color: inherit; text-decoration: none;">%2$s</a></h3>',
				esc_url( get_permalink() ),
				esc_html( get_the_title() )
			);
			$body     .= '<p style="color: var(--text-secondary); font-size: 14px; margin: 0;">' . esc_html( wp_trim_words( get_the_excerpt(), 20 ) ) . '</p>';
			echo technet_media_card( $image_url, get_the_title(), $body ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		endwhile;
		wp_reset_postdata();
		?>
	</div>
</section>
<?php endif; ?>

<section class="technet-tag-row">
	<?php
	foreach ( array( 'Engineering', 'Science', 'Medicine', 'Arts', 'IT', 'Laboratory' ) as $discipline ) {
		echo technet_tag( $discipline ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	?>
</section>

<?php get_footer(); ?>
