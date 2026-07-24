<?php
/**
 * Read helpers the theme calls to render sessions/speakers linked to a
 * conference event. Kept in the plugin (not the theme) because they read
 * plugin-owned data (tn_session / tn_speaker CPTs) — see inc/cpts.php.
 *
 * @package TechNet_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Flat list of sessions linked to an event, sorted by day then time.
 * Session `time` meta is stored as zero-padded 24-hour "HH:MM" (enforced in
 * the admin meta box, see inc/cpts.php) specifically so a plain string sort
 * is also a correct chronological sort.
 *
 * @param int $event_id
 * @return array<int,array{day:string,time:string,title:string,track:string}>
 */
function technet_get_event_sessions( $event_id ) {
	$posts = get_posts(
		array(
			'post_type'      => 'tn_session',
			'posts_per_page' => -1,
			'meta_key'       => '_technet_event_id', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'     => $event_id, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);

	$sessions = array();
	foreach ( $posts as $post ) {
		$sessions[] = array(
			'day'   => get_post_meta( $post->ID, '_technet_day', true ),
			'time'  => get_post_meta( $post->ID, '_technet_time', true ),
			'title' => $post->post_title,
			'track' => get_post_meta( $post->ID, '_technet_track', true ),
		);
	}

	usort(
		$sessions,
		static function ( $a, $b ) {
			return strcmp( $a['day'] . $a['time'], $b['day'] . $b['time'] );
		}
	);

	return $sessions;
}

/**
 * Same as technet_get_event_sessions() but grouped by day, in the shape the
 * schedule template iterates over directly.
 *
 * @param int $event_id
 * @return array<string,array<int,array{time:string,title:string,track:string}>>
 */
function technet_get_event_sessions_by_day( $event_id ) {
	$grouped = array();
	foreach ( technet_get_event_sessions( $event_id ) as $session ) {
		$day = $session['day'] ? $session['day'] : __( 'Schedule', 'technet-core' );
		if ( ! isset( $grouped[ $day ] ) ) {
			$grouped[ $day ] = array();
		}
		$grouped[ $day ][] = array(
			'time'  => $session['time'],
			'title' => $session['title'],
			'track' => $session['track'],
		);
	}
	return $grouped;
}

/**
 * Speakers linked to an event.
 *
 * @param int $event_id
 * @return array<int,array{name:string,role:string,institution:string,photo_url:string|false}>
 */
function technet_get_event_speakers( $event_id ) {
	$posts = get_posts(
		array(
			'post_type'      => 'tn_speaker',
			'posts_per_page' => -1,
			'meta_key'       => '_technet_event_id', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'     => $event_id, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);

	$speakers = array();
	foreach ( $posts as $post ) {
		$speakers[] = array(
			'name'        => $post->post_title,
			'role'        => get_post_meta( $post->ID, '_technet_role', true ),
			'institution' => get_post_meta( $post->ID, '_technet_institution', true ),
			'photo_url'   => get_the_post_thumbnail_url( $post->ID, 'medium' ),
		);
	}
	return $speakers;
}
