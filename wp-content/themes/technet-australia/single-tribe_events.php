<?php
/**
 * Conference microsite — composes MicroHeader.jsx + SchedulePage.jsx +
 * SpeakersPage.jsx + RegisterPage.jsx as anchor-tab sections on a single
 * page (the design system uses in-page tabs to switch between them, which
 * this ports as real in-page anchors + a .tn-tabs nav rather than JS
 * show/hide, so each section stays crawlable/linkable).
 *
 * "Forum" category events use a lighter single-event layout instead —
 * the conference microsite pattern (schedule/speakers/register) doesn't
 * apply to a one-day regional forum.
 *
 * @package TechNet_Australia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$event_id    = get_the_ID();
	$is_forum    = has_term( 'forum', 'tribe_events_cat', $event_id );
	$venue       = function_exists( 'tribe_get_venue' ) ? tribe_get_venue( $event_id ) : '';
	$when        = function_exists( 'tribe_get_start_date' ) ? tribe_get_start_date( $event_id, false, 'j F Y' ) : get_the_date();

	if ( $is_forum ) :
		?>
		<div class="technet-micro-header">
			<div class="technet-micro-header__kicker">TechNet Australia &middot; Regional Forum</div>
			<h1 class="technet-micro-header__title"><?php the_title(); ?></h1>
		</div>
		<div class="technet-page">
			<p class="technet-page__lead">
				<?php echo esc_html( $when ); ?><?php echo $venue ? ' &middot; ' . esc_html( $venue ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</p>
			<div class="technet-page__content"><?php the_content(); ?></div>
		</div>
		<?php
	else :
		$sessions_by_day = function_exists( 'technet_get_event_sessions_by_day' ) ? technet_get_event_sessions_by_day( $event_id ) : array();
		$speakers        = function_exists( 'technet_get_event_speakers' ) ? technet_get_event_speakers( $event_id ) : array();
		$institutions    = array(
			'UNSW',
			'University of Auckland',
			'Monash',
			'UQ',
			'Other',
		);
		?>
		<div class="technet-micro-header">
			<div class="technet-micro-header__kicker">TechNet Australia &middot; National Conference</div>
			<h1 class="technet-micro-header__title"><?php the_title(); ?></h1>
		</div>

		<nav class="tn-tabs technet-container" style="margin-top: var(--space-5);" aria-label="<?php esc_attr_e( 'Conference sections', 'technet-australia' ); ?>">
			<a class="tn-tabs__item" href="#schedule"><?php esc_html_e( 'Schedule', 'technet-australia' ); ?></a>
			<a class="tn-tabs__item" href="#speakers"><?php esc_html_e( 'Speakers', 'technet-australia' ); ?></a>
			<a class="tn-tabs__item" href="#register"><?php esc_html_e( 'Register', 'technet-australia' ); ?></a>
		</nav>

		<section id="schedule" class="technet-page" style="max-width: 900px;">
			<?php if ( empty( $sessions_by_day ) ) : ?>
				<p class="technet-directory-empty"><?php esc_html_e( 'The schedule will be published closer to the conference.', 'technet-australia' ); ?></p>
			<?php else : ?>
				<?php foreach ( $sessions_by_day as $day => $sessions ) : ?>
					<div style="margin-bottom: var(--space-6);">
						<h3 style="font-size:var(--fs-h4);margin-bottom:var(--space-3);"><?php echo esc_html( $day ); ?></h3>
						<div class="technet-list technet-list--tight">
							<?php foreach ( $sessions as $session ) : ?>
								<?php
								$row  = '<span class="technet-list-row__time">' . esc_html( $session['time'] ) . '</span>';
								$row .= '<span style="flex:1;">' . esc_html( $session['title'] ) . '</span>';
								$row .= technet_tag( $session['track'] );
								echo technet_card( $row, array( 'row' => true, 'class' => 'technet-list-row' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								?>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</section>

		<section id="speakers" class="technet-page" style="max-width: 900px;">
			<h2 style="font-size:var(--fs-h3);margin-bottom:var(--space-5);"><?php esc_html_e( 'Speakers', 'technet-australia' ); ?></h2>
			<?php if ( empty( $speakers ) ) : ?>
				<p class="technet-directory-empty"><?php esc_html_e( 'Speakers will be announced soon.', 'technet-australia' ); ?></p>
			<?php else : ?>
				<div class="technet-grid-2">
					<?php foreach ( $speakers as $speaker ) : ?>
						<?php
						$row  = ! empty( $speaker['photo_url'] )
							? sprintf( '<img class="technet-avatar" src="%1$s" alt="%2$s">', esc_url( $speaker['photo_url'] ), esc_attr( $speaker['name'] ) )
							: '<div class="technet-avatar"></div>';
						$row .= '<div><div class="technet-list-row__title">' . esc_html( $speaker['name'] ) . '</div>';
						$row .= '<div class="technet-list-row__meta">' . esc_html( $speaker['institution'] . ( $speaker['role'] ? ' · ' . $speaker['role'] : '' ) ) . '</div></div>';
						echo technet_card( $row, array( 'row' => true ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</section>

		<section id="register" class="technet-form">
			<?php if ( isset( $_GET['registered'] ) && '1' === $_GET['registered'] ) : ?>
				<div class="technet-form__notice technet-form__notice--success">
					<?php esc_html_e( "You're registered — a confirmation has been sent to your email.", 'technet-australia' ); ?>
				</div>
			<?php elseif ( isset( $_GET['technet_error'] ) ) : ?>
				<div class="technet-form__notice technet-form__notice--error">
					<?php esc_html_e( 'Something was missing from that submission — please check the form and try again.', 'technet-australia' ); ?>
				</div>
			<?php endif; ?>

			<h2 class="technet-form__title"><?php esc_html_e( 'Register', 'technet-australia' ); ?></h2>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="technet_conference_register">
				<input type="hidden" name="event_id" value="<?php echo esc_attr( $event_id ); ?>">
				<?php wp_nonce_field( 'technet_conference_register', 'technet_conference_register_nonce' ); ?>

				<div class="technet-form__fields">
					<?php
					echo technet_input( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						array(
							'label'       => __( 'Full name', 'technet-australia' ),
							'name'        => 'full_name',
							'placeholder' => 'Jordan Lee',
							'required'    => true,
						)
					);
					echo technet_input( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						array(
							'label'       => __( 'Institution email', 'technet-australia' ),
							'name'        => 'email',
							'type'        => 'email',
							'placeholder' => 'you@university.edu.au',
							'required'    => true,
						)
					);
					echo technet_select( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						array(
							'label'   => __( 'Institution', 'technet-australia' ),
							'name'    => 'institution',
							'options' => $institutions,
						)
					);
					?>
					<div class="technet-form__row">
						<?php
						echo technet_radio( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							array(
								'label'   => __( 'Full conference', 'technet-australia' ),
								'name'    => 'pass_type',
								'value'   => 'full',
								'checked' => true,
							)
						);
						echo technet_radio( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							array(
								'label' => __( 'Day pass', 'technet-australia' ),
								'name'  => 'pass_type',
								'value' => 'day',
							)
						);
						?>
					</div>
					<?php
					echo technet_checkbox( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						array(
							'label'   => __( 'Add me to the TechNet Google Group', 'technet-australia' ),
							'name'    => 'google_group_optin',
							'checked' => true,
						)
					);
					echo technet_button( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						array(
							'label'   => __( 'Confirm registration', 'technet-australia' ),
							'variant' => 'accent',
							'type'    => 'submit',
						)
					);
					?>
				</div>
			</form>
		</section>
		<?php
	endif;

endwhile;

get_footer();
