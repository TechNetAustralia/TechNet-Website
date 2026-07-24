<?php
/**
 * Template Name: Documents
 *
 * Institutional document library (forum notes, NEATTS materials,
 * benchmarking documents), backed by WP Document Revisions' `document`
 * post type. No bespoke UI-kit design exists for this page — the design
 * system's readme doesn't cover document storage — so it reuses the same
 * card/list/badge language as the rest of the site. Gated to active PMP
 * members, same as the member directory.
 *
 * @package TechNet_Australia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$is_member       = technet_is_member();
$doc_revisions_on = post_type_exists( 'document' );
?>

<div class="technet-page">
	<h1 class="technet-page__title"><?php esc_html_e( 'Documents', 'technet-australia' ); ?></h1>
	<p class="technet-page__lead"><?php esc_html_e( 'Forum notes, NEATTS materials and benchmarking documents shared across member institutions.', 'technet-australia' ); ?></p>

	<div style="margin-top: var(--space-6);">
		<?php if ( ! $is_member ) : ?>
			<div class="technet-directory-empty">
				<?php if ( is_user_logged_in() ) : ?>
					<?php esc_html_e( 'The document library is available to active TechNet members.', 'technet-australia' ); ?>
				<?php else : ?>
					<?php
					printf(
						/* translators: %s: log in URL */
						wp_kses( __( 'The document library is available to active TechNet members. <a href="%s">Log in</a> to view it.', 'technet-australia' ), array( 'a' => array( 'href' => array() ) ) ),
						esc_url( wp_login_url( get_permalink() ) )
					);
					?>
				<?php endif; ?>
			</div>
		<?php elseif ( ! $doc_revisions_on ) : ?>
			<div class="technet-directory-empty">
				<?php esc_html_e( 'The document library plugin (WP Document Revisions) is not active.', 'technet-australia' ); ?>
			</div>
		<?php else : ?>
			<?php
			$categories = get_terms(
				array(
					'taxonomy'   => 'document_category',
					'hide_empty' => true,
				)
			);

			$render_document_row = static function ( $doc ) {
				$row  = '<div style="flex:1;"><div class="technet-list-row__title"><a href="' . esc_url( get_permalink( $doc ) ) . '">' . esc_html( get_the_title( $doc ) ) . '</a></div>';
				$row .= '<div class="technet-list-row__meta">' . esc_html( get_the_modified_date( '', $doc ) ) . '</div></div>';
				$row .= technet_badge( strtoupper( pathinfo( get_attached_file( $doc->ID ), PATHINFO_EXTENSION ) ? pathinfo( get_attached_file( $doc->ID ), PATHINFO_EXTENSION ) : 'FILE' ), 'neutral' );
				echo technet_card( $row, array( 'row' => true, 'class' => 'technet-list-row' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			};

			if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) :
				foreach ( $categories as $category ) :
					$docs = get_posts(
						array(
							'post_type'      => 'document',
							'posts_per_page' => -1,
							'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
								array(
									'taxonomy' => 'document_category',
									'field'    => 'term_id',
									'terms'    => $category->term_id,
								),
							),
						)
					);
					if ( empty( $docs ) ) {
						continue;
					}
					?>
					<h3 style="font-size:var(--fs-h4);margin:var(--space-6) 0 var(--space-3);"><?php echo esc_html( $category->name ); ?></h3>
					<div class="technet-list technet-list--tight">
						<?php foreach ( $docs as $doc ) : $render_document_row( $doc ); endforeach; ?>
					</div>
					<?php
				endforeach;
			else :
				$docs = get_posts(
					array(
						'post_type'      => 'document',
						'posts_per_page' => -1,
					)
				);
				if ( empty( $docs ) ) :
					?>
					<div class="technet-directory-empty"><?php esc_html_e( 'No documents have been added yet.', 'technet-australia' ); ?></div>
					<?php
				else :
					?>
					<div class="technet-list technet-list--tight">
						<?php foreach ( $docs as $doc ) : $render_document_row( $doc ); endforeach; ?>
					</div>
					<?php
				endif;
			endif;
			?>
		<?php endif; ?>
	</div>
</div>

<?php get_footer(); ?>
