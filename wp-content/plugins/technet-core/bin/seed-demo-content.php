<?php
/**
 * `wp technet seed-demo` — populates the sandbox with sample content so
 * it isn't empty on first boot: a national conference event with speakers
 * and a two-day schedule, three regional forums, five sample members (with
 * an active Paid Memberships Pro level, when PMP is active), and the
 * marketing-site pages wired to their templates. Content mirrors the
 * placeholder data already used in the design system's UI kits
 * (ConferencePage/SchedulePage/SpeakersPage/ForumsPage/member-directory).
 *
 * Loaded only under WP-CLI (see technet-core.php).
 *
 * @package TechNet_Core
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'WP_CLI' ) ) {
	exit;
}

class TechNet_CLI_Command {

	/**
	 * Seed demo content for local/CI preview.
	 *
	 * ## EXAMPLES
	 *
	 *     wp technet seed-demo
	 *
	 * @when after_wp_load
	 */
	public function seed_demo( $args, $assoc_args ) {
		$this->seed_pages();
		$conference_id = $this->seed_conference();
		$this->seed_forums();
		if ( $conference_id ) {
			$this->seed_speakers( $conference_id );
			$this->seed_sessions( $conference_id );
		}
		$this->seed_members();

		WP_CLI::success( 'TechNet demo content seeded.' );
	}

	/**
	 * Create the marketing-site pages and set each one's page template,
	 * skipping any that already exist (by title) so this is safe to re-run.
	 */
	private function seed_pages() {
		$pages = array(
			'Conference'         => 'page-conference.php',
			'Forums'             => 'page-forums.php',
			'NEATTS'             => 'page-neatts.php',
			'NEATTS Nominate'    => 'page-neatts-nominate.php',
			'Member Directory'   => 'page-member-directory.php',
			'Documents'          => 'page-documents.php',
			'About'              => '',
		);

		foreach ( $pages as $title => $template ) {
			$existing = get_page_by_title( $title, OBJECT, 'page' );
			if ( $existing ) {
				continue;
			}
			$page_id = wp_insert_post(
				array(
					'post_type'   => 'page',
					'post_title'  => $title,
					'post_status' => 'publish',
					'post_content' => 'About' === $title
						? 'TechNet Australia is a volunteer, not-for-profit association of technical and scientific professional staff supporting teaching and research at tertiary institutions across Australia and New Zealand, operating since 2000.'
						: '',
				)
			);
			if ( $template && $page_id && ! is_wp_error( $page_id ) ) {
				update_post_meta( $page_id, '_wp_page_template', $template );
			}
			WP_CLI::log( "Created page: {$title}" );
		}

		$front = get_option( 'page_on_front' );
		if ( ! $front ) {
			update_option( 'show_on_front', 'posts' ); // front-page.php in this theme handles is_front_page() regardless.
		}
	}

	/**
	 * Create the national conference event. Returns 0 (and warns) if The
	 * Events Calendar isn't active.
	 *
	 * @return int
	 */
	private function seed_conference() {
		if ( ! post_type_exists( 'tribe_events' ) ) {
			WP_CLI::warning( 'The Events Calendar is not active — skipping conference/forum events.' );
			return 0;
		}

		$existing = get_page_by_title( 'TechNet 2027 — Newcastle', OBJECT, 'tribe_events' );
		if ( $existing ) {
			return $existing->ID;
		}

		$start = gmdate( 'Y-m-d H:i:s', strtotime( '+90 days 09:00' ) );
		$end   = gmdate( 'Y-m-d H:i:s', strtotime( '+92 days 17:00' ) );

		$event_id = wp_insert_post(
			array(
				'post_type'    => 'tribe_events',
				'post_title'   => 'TechNet 2027 — Newcastle',
				'post_content' => 'Hosted by a different member university each year — three days of talks, workshops and site tours for technical and scientific staff.',
				'post_status'  => 'publish',
			)
		);

		if ( is_wp_error( $event_id ) || ! $event_id ) {
			WP_CLI::warning( 'Could not create the conference event.' );
			return 0;
		}

		update_post_meta( $event_id, '_EventStartDate', $start );
		update_post_meta( $event_id, '_EventEndDate', $end );
		update_post_meta( $event_id, '_EventAllDay', false );

		$this->set_event_category( $event_id, 'conference' );

		WP_CLI::log( 'Created conference event.' );
		return $event_id;
	}

	/**
	 * Create the three regional forums.
	 */
	private function seed_forums() {
		if ( ! post_type_exists( 'tribe_events' ) ) {
			return;
		}

		$forums = array(
			array( 'title' => 'Adelaide Forum', 'offset' => '+30 days', 'status' => 'Open' ),
			array( 'title' => 'Brisbane Forum', 'offset' => '+120 days', 'status' => 'Open' ),
			array( 'title' => 'Sydney Forum', 'offset' => '+10 days', 'status' => 'Closing soon' ),
		);

		foreach ( $forums as $forum ) {
			if ( get_page_by_title( $forum['title'], OBJECT, 'tribe_events' ) ) {
				continue;
			}
			$start = gmdate( 'Y-m-d H:i:s', strtotime( $forum['offset'] . ' 09:00' ) );
			$end   = gmdate( 'Y-m-d H:i:s', strtotime( $forum['offset'] . ' 17:00' ) );

			$forum_id = wp_insert_post(
				array(
					'post_type'    => 'tribe_events',
					'post_title'   => $forum['title'],
					'post_content' => 'A one-day forum bringing technical staff together closer to home.',
					'post_status'  => 'publish',
				)
			);
			if ( is_wp_error( $forum_id ) || ! $forum_id ) {
				continue;
			}
			update_post_meta( $forum_id, '_EventStartDate', $start );
			update_post_meta( $forum_id, '_EventEndDate', $end );
			update_post_meta( $forum_id, '_EventAllDay', false );
			update_post_meta( $forum_id, '_technet_forum_status', $forum['status'] );
			$this->set_event_category( $forum_id, 'forum' );

			WP_CLI::log( "Created forum: {$forum['title']}" );
		}
	}

	/**
	 * Assign (creating if necessary) a tribe_events_cat term to an event.
	 */
	private function set_event_category( $event_id, $slug ) {
		$term = term_exists( $slug, 'tribe_events_cat' );
		if ( ! $term ) {
			$term = wp_insert_term( ucfirst( $slug ), 'tribe_events_cat', array( 'slug' => $slug ) );
		}
		if ( ! is_wp_error( $term ) ) {
			wp_set_object_terms( $event_id, (int) $term['term_id'], 'tribe_events_cat' );
		}
	}

	/**
	 * Speakers — mirrors ui_kits/conference-microsite/SpeakersPage.jsx.
	 */
	private function seed_speakers( $event_id ) {
		$speakers = array(
			array( 'Dr. Alison Ford', 'Engineering', 'UNSW' ),
			array( 'James Whitton', 'IT', 'University of Auckland' ),
			array( 'Priya Nair', 'Medicine', 'Monash' ),
			array( 'Tom Reardon', 'Science', 'UQ' ),
		);

		foreach ( $speakers as $speaker ) {
			list( $name, $role, $institution ) = $speaker;
			if ( get_page_by_title( $name, OBJECT, 'tn_speaker' ) ) {
				continue;
			}
			$speaker_id = wp_insert_post(
				array(
					'post_type'   => 'tn_speaker',
					'post_title'  => $name,
					'post_status' => 'publish',
				)
			);
			if ( $speaker_id && ! is_wp_error( $speaker_id ) ) {
				update_post_meta( $speaker_id, '_technet_role', $role );
				update_post_meta( $speaker_id, '_technet_institution', $institution );
				update_post_meta( $speaker_id, '_technet_event_id', $event_id );
			}
		}
		WP_CLI::log( 'Created speakers.' );
	}

	/**
	 * Sessions — mirrors ui_kits/conference-microsite/SchedulePage.jsx.
	 */
	private function seed_sessions( $event_id ) {
		$days = array(
			'Day 1 · Tue' => array(
				array( '09:00', 'Registration & welcome', 'All' ),
				array( '10:30', 'Keynote: Technical careers in research', 'All' ),
				array( '13:00', 'Lab safety workshop', 'Science' ),
			),
			'Day 2 · Wed' => array(
				array( '09:00', 'Site tours', 'All' ),
				array( '11:00', 'Panel: Supporting multidisciplinary teams', 'Arts' ),
				array( '14:00', 'NEATTS award ceremony', 'All' ),
			),
		);

		foreach ( $days as $day => $sessions ) {
			foreach ( $sessions as $session ) {
				list( $time, $title, $track ) = $session;
				if ( get_page_by_title( $title, OBJECT, 'tn_session' ) ) {
					continue;
				}
				$session_id = wp_insert_post(
					array(
						'post_type'   => 'tn_session',
						'post_title'  => $title,
						'post_status' => 'publish',
					)
				);
				if ( $session_id && ! is_wp_error( $session_id ) ) {
					update_post_meta( $session_id, '_technet_day', $day );
					update_post_meta( $session_id, '_technet_time', $time );
					update_post_meta( $session_id, '_technet_track', $track );
					update_post_meta( $session_id, '_technet_event_id', $event_id );
				}
			}
		}
		WP_CLI::log( 'Created sessions.' );
	}

	/**
	 * Sample members — mirrors ui_kits/member-directory/index.html. Skipped
	 * (with a warning) if Paid Memberships Pro isn't active, since directory
	 * membership is gated by PMP membership status.
	 */
	private function seed_members() {
		if ( ! function_exists( 'pmpro_changeMembershipLevel' ) ) {
			WP_CLI::warning( 'Paid Memberships Pro is not active — skipping sample members.' );
			return;
		}

		$level_id = $this->ensure_free_member_level();

		$members = array(
			array( 'Alison Ford', 'alison.ford', 'UNSW', 'Engineering' ),
			array( 'James Whitton', 'james.whitton', 'University of Auckland', 'IT' ),
			array( 'Priya Nair', 'priya.nair', 'Monash University', 'Medicine' ),
			array( 'Tom Reardon', 'tom.reardon', 'University of Queensland', 'Science' ),
			array( 'Sarah Kim', 'sarah.kim', 'University of Otago', 'Arts' ),
		);

		foreach ( $members as $member ) {
			list( $name, $login, $institution, $discipline ) = $member;
			$user = get_user_by( 'login', $login );
			if ( ! $user ) {
				$user_id = wp_insert_user(
					array(
						'user_login'   => $login,
						'user_pass'    => wp_generate_password(),
						'user_email'   => $login . '@example.technet.org.au',
						'display_name' => $name,
						'role'         => 'subscriber',
					)
				);
				if ( is_wp_error( $user_id ) ) {
					continue;
				}
			} else {
				$user_id = $user->ID;
			}
			update_user_meta( $user_id, 'technet_institution', $institution );
			update_user_meta( $user_id, 'technet_discipline', $discipline );
			if ( $level_id ) {
				pmpro_changeMembershipLevel( $level_id, $user_id );
			}
		}
		WP_CLI::log( 'Created sample members.' );
	}

	/**
	 * Find or create a single free "Member" PMP level, matching the plan's
	 * "one free membership level" decision.
	 *
	 * @return int
	 */
	private function ensure_free_member_level() {
		global $wpdb;
		if ( ! isset( $wpdb->pmpro_membership_levels ) ) {
			return 0;
		}
		$existing = $wpdb->get_var( "SELECT id FROM {$wpdb->pmpro_membership_levels} WHERE name = 'Member' LIMIT 1" );
		if ( $existing ) {
			return (int) $existing;
		}
		$wpdb->insert(
			$wpdb->pmpro_membership_levels,
			array(
				'name'            => 'Member',
				'description'     => 'Free TechNet Australia membership.',
				'initial_payment' => 0,
				'billing_amount'  => 0,
				'allow_signups'   => 1,
			)
		);
		return (int) $wpdb->insert_id;
	}
}

WP_CLI::add_command( 'technet', 'TechNet_CLI_Command' );
