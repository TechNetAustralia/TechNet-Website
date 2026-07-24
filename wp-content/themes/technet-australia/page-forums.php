<?php
/**
 * Template Name: Forums
 *
 * Ports ui_kits/marketing-site/ForumsPage.jsx. Lists "forum" category
 * events from The Events Calendar; falls back to the design's static
 * Adelaide/Brisbane/Sydney copy if none are scheduled yet.
 *
 * @package TechNet_Australia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$forums = array();
if ( function_exists( 'tribe_get_events' ) ) {
	$forum_events = tribe_get_events(
		array(
			'posts_per_page' => -1,
			'start_date'     => 'now',
			'order'          => 'ASC',
			'orderby'        => 'event_date',
			'tax_query'      => array(
				array(
					'taxonomy' => 'tribe_events_cat',
					'field'    => 'slug',
					'terms'    => 'forum',
				),
			),
		)
	);
	foreach ( $forum_events as $forum_event ) {
		$status = get_post_meta( $forum_event->ID, '_technet_forum_status', true );
		if ( ! $status && function_exists( 'tribe_get_start_date' ) ) {
			$days_until = ( (int) tribe_get_start_date( $forum_event, false, 'U' ) - time() ) / DAY_IN_SECONDS;
			$status     = $days_until <= 14 ? __( 'Closing soon', 'technet-australia' ) : __( 'Open', 'technet-australia' );
		}
		$forums[] = array(
			'title'  => get_the_title( $forum_event ),
			'when'   => function_exists( 'tribe_get_start_date' ) ? tribe_get_start_date( $forum_event, false, 'F Y' ) : '',
			'status' => $status ? $status : __( 'Open', 'technet-australia' ),
			'url'    => get_permalink( $forum_event ),
		);
	}
}

if ( empty( $forums ) ) {
	// Fallback copy — matches the design system's original static content.
	$forums = array(
		array(
			'title'  => __( 'Adelaide Forum', 'technet-australia' ),
			'when'   => __( 'March 2027', 'technet-australia' ),
			'status' => __( 'Open', 'technet-australia' ),
			'url'    => '#',
		),
		array(
			'title'  => __( 'Brisbane Forum', 'technet-australia' ),
			'when'   => __( 'July 2027', 'technet-australia' ),
			'status' => __( 'Open', 'technet-australia' ),
			'url'    => '#',
		),
		array(
			'title'  => __( 'Sydney Forum', 'technet-australia' ),
			'when'   => __( 'October 2027', 'technet-australia' ),
			'status' => __( 'Closing soon', 'technet-australia' ),
			'url'    => '#',
		),
	);
}
?>

<div class="technet-page">
	<h1 class="technet-page__title"><?php esc_html_e( 'Regional Forums', 'technet-australia' ); ?></h1>
	<p class="technet-page__lead"><?php esc_html_e( 'One-day forums and mini-conferences bringing technical staff together closer to home.', 'technet-australia' ); ?></p>

	<div class="technet-list" style="margin-top: var(--space-6);">
		<?php foreach ( $forums as $forum ) : ?>
			<?php
			$row  = '<div><div class="technet-list-row__title">' . esc_html( $forum['title'] ) . '</div>';
			$row .= '<div class="technet-list-row__meta">' . esc_html( $forum['when'] ) . '</div></div>';
			$row .= technet_badge( $forum['status'], 'Open' === $forum['status'] ? 'success' : 'warning' );
			$row .= technet_button(
				array(
					'label'   => __( 'Details', 'technet-australia' ),
					'variant' => 'secondary',
					'size'    => 'sm',
					'href'    => $forum['url'],
				)
			);
			echo technet_card( $row, array( 'row' => true, 'class' => 'technet-list-row' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		<?php endforeach; ?>
	</div>
</div>

<?php get_footer(); ?>
