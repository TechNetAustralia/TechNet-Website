<?php
/**
 * Template Name: Conference
 *
 * Ports ui_kits/marketing-site/ConferencePage.jsx. Pulls the next upcoming
 * "conference" category event from The Events Calendar instead of the
 * design's hardcoded copy; falls back to that hardcoded copy if The Events
 * Calendar isn't active or nothing is scheduled yet, so the page never
 * fatals or renders empty on a fresh install.
 *
 * @package TechNet_Australia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$event = null;
if ( function_exists( 'tribe_get_events' ) ) {
	$upcoming = tribe_get_events(
		array(
			'posts_per_page' => 1,
			'start_date'     => 'now',
			'order'          => 'ASC',
			'orderby'        => 'event_date',
			'tax_query'      => array(
				array(
					'taxonomy' => 'tribe_events_cat',
					'field'    => 'slug',
					'terms'    => 'conference',
				),
			),
		)
	);
	if ( ! empty( $upcoming ) ) {
		$event = $upcoming[0];
	}
}

if ( $event ) {
	$title           = get_the_title( $event );
	$description     = has_excerpt( $event ) ? get_the_excerpt( $event ) : wp_trim_words( $event->post_content, 40 );
	$registration_url = get_permalink( $event );
	$days             = 1;
	if ( function_exists( 'tribe_get_start_date' ) && function_exists( 'tribe_get_end_date' ) ) {
		$start = tribe_get_start_date( $event, false, 'U' );
		$end   = tribe_get_end_date( $event, false, 'U' );
		if ( $end > $start ) {
			$days = max( 1, (int) ceil( ( $end - $start ) / DAY_IN_SECONDS ) + 1 );
		}
	}
	$sessions     = function_exists( 'technet_get_event_sessions' ) ? technet_get_event_sessions( $event->ID ) : array();
	$speakers     = function_exists( 'technet_get_event_speakers' ) ? technet_get_event_speakers( $event->ID ) : array();
	$institutions = array_unique( array_filter( wp_list_pluck( $speakers, 'institution' ) ) );

	$stats = array(
		array( 'value' => (string) $days, 'label' => _n( 'Day', 'Days', $days, 'technet-australia' ) ),
		array(
			'value' => count( $sessions ) . '+',
			'label' => __( 'Sessions', 'technet-australia' ),
		),
		array(
			'value' => (string) count( $institutions ),
			'label' => __( 'Institutions represented', 'technet-australia' ),
		),
	);
} else {
	// Fallback copy — matches the design system's original static content.
	$title             = __( 'TechNet 2027 National Conference', 'technet-australia' );
	$description       = __( 'Hosted by a different member university each year — three days of talks, workshops and site tours for technical and scientific staff.', 'technet-australia' );
	$registration_url  = '#';
	$stats             = array(
		array( 'value' => '3', 'label' => __( 'Days', 'technet-australia' ) ),
		array( 'value' => '40+', 'label' => __( 'Sessions', 'technet-australia' ) ),
		array( 'value' => '8', 'label' => __( 'Institutions represented', 'technet-australia' ) ),
	);
}
?>

<div class="technet-page">
	<?php echo technet_badge( $event ? __( 'Registrations open', 'technet-australia' ) : __( 'Coming soon', 'technet-australia' ), 'success' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<h1 class="technet-page__title"><?php echo esc_html( $title ); ?></h1>
	<p class="technet-page__lead"><?php echo esc_html( $description ); ?></p>

	<div class="technet-stat-row">
		<?php foreach ( $stats as $stat ) : ?>
			<?php
			$stat_html  = '<div class="technet-stat__value">' . esc_html( $stat['value'] ) . '</div>';
			$stat_html .= '<div class="technet-stat__label">' . esc_html( $stat['label'] ) . '</div>';
			echo technet_card( $stat_html, array( 'class' => 'technet-stat' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		<?php endforeach; ?>
	</div>

	<?php
	echo technet_button( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		array(
			'label'   => __( 'Register for the conference', 'technet-australia' ),
			'variant' => 'accent',
			'href'    => $registration_url,
		)
	);
	?>
</div>

<?php get_footer(); ?>
