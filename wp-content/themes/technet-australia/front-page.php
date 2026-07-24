<?php
/**
 * Home — ports ui_kits/marketing-site/Home.jsx.
 *
 * @package TechNet_Australia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$hero_banner = technet_hero_banner_url();
?>

<section class="technet-hero<?php echo $hero_banner ? ' technet-hero--has-banner' : ''; ?>"<?php echo $hero_banner ? ' style="--hero-banner-image:url(' . esc_url( $hero_banner ) . ')"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_url() already applied to the dynamic part. ?>>
	<div class="technet-hero__kicker">Since 2000 &middot; 540+ members</div>
	<h1 class="technet-hero__title">Connecting technical &amp; scientific staff across Australian and NZ tertiary institutions</h1>
	<p class="technet-hero__lead">TechNet is the national network for the people who keep teaching and research running &mdash; across arts, science, medicine and engineering.</p>
	<div class="technet-hero__actions">
		<?php
		echo technet_button( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'label'   => __( "This year's conference", 'technet-australia' ),
				'variant' => 'accent',
				'href'    => home_url( '/conference/' ),
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

<section class="technet-tag-row">
	<?php
	foreach ( array( 'Engineering', 'Science', 'Medicine', 'Arts', 'IT', 'Laboratory' ) as $discipline ) {
		echo technet_tag( $discipline ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	?>
</section>

<?php get_footer(); ?>
