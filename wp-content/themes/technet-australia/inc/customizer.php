<?php
/**
 * WP Customizer controls for editor-managed images — the CMS-native way to
 * change site imagery (Appearance -> Customize), instead of needing to
 * touch git/Finder. The homepage hero banner used to only support the
 * assets/images/hero-banner.{ext} filename convention; that's kept as a
 * fallback (see technet_hero_banner_url() in functions.php) but this
 * Customizer control is now the primary, editor-friendly way to set it.
 *
 * @package TechNet_Australia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the "Homepage" Customizer section and hero banner control.
 *
 * @param WP_Customize_Manager $wp_customize
 */
function technet_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'technet_homepage',
		array(
			'title'       => __( 'Homepage', 'technet-australia' ),
			'description' => __( 'Images and settings for the homepage.', 'technet-australia' ),
			'priority'    => 30,
		)
	);

	$wp_customize->add_setting(
		'technet_hero_banner',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'technet_hero_banner',
			array(
				'label'       => __( 'Hero banner image', 'technet-australia' ),
				'description' => __( 'Shown behind the homepage headline, with a dark overlay so the text stays readable. Leave empty for a plain flat-colour hero.', 'technet-australia' ),
				'section'     => 'technet_homepage',
			)
		)
	);

	$wp_customize->add_setting(
		'technet_conference_url',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'technet_conference_url',
		array(
			'label'       => __( "'This year's conference' button link", 'technet-australia' ),
			'description' => __( 'Where the homepage accent button goes — the external conference site if there is one this year, or leave empty to link to the built-in /conference/ page.', 'technet-australia' ),
			'section'     => 'technet_homepage',
			'type'        => 'url',
		)
	);
}
add_action( 'customize_register', 'technet_customize_register' );
