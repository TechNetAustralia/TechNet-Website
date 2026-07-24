<?php
/**
 * TechNet Australia theme bootstrap.
 *
 * @package TechNet_Australia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TECHNET_THEME_VERSION', '0.1.0' );

require_once get_template_directory() . '/inc/components.php';

/**
 * Theme setup: supports, nav menus, image sizes.
 */
function technet_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);
	add_theme_support( 'automatic-feed-links' );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Navigation', 'technet-australia' ),
			'footer'  => __( 'Footer Links', 'technet-australia' ),
		)
	);
}
add_action( 'after_setup_theme', 'technet_setup' );

/**
 * File-modification-time version string for a theme asset, so every save
 * auto-busts browser cache instead of relying on someone remembering to
 * bump TECHNET_THEME_VERSION by hand.
 *
 * @param string $relative_path e.g. 'assets/css/tokens.css'.
 * @return string
 */
function technet_asset_version( $relative_path ) {
	$path = get_template_directory() . '/' . ltrim( $relative_path, '/' );
	return file_exists( $path ) ? (string) filemtime( $path ) : TECHNET_THEME_VERSION;
}

/**
 * Enqueue the ported design-system stylesheets in dependency order, plus
 * page-specific scripts (currently just the member directory live filter).
 */
function technet_enqueue_assets() {
	$theme_uri = get_template_directory_uri();

	wp_enqueue_style( 'technet-tokens', $theme_uri . '/assets/css/tokens.css', array(), technet_asset_version( 'assets/css/tokens.css' ) );
	wp_enqueue_style( 'technet-base', $theme_uri . '/assets/css/base.css', array( 'technet-tokens' ), technet_asset_version( 'assets/css/base.css' ) );
	wp_enqueue_style( 'technet-components', $theme_uri . '/assets/css/components.css', array( 'technet-tokens' ), technet_asset_version( 'assets/css/components.css' ) );
	wp_enqueue_style( 'technet-layout', $theme_uri . '/assets/css/layout.css', array( 'technet-tokens', 'technet-components' ), technet_asset_version( 'assets/css/layout.css' ) );
	wp_enqueue_style( 'technet-style', $theme_uri . '/style.css', array( 'technet-tokens', 'technet-base', 'technet-components', 'technet-layout' ), technet_asset_version( 'style.css' ) );

	if ( is_page_template( 'page-member-directory.php' ) ) {
		wp_enqueue_script( 'technet-member-directory', $theme_uri . '/assets/js/member-directory.js', array(), technet_asset_version( 'assets/js/member-directory.js' ), true );
	}
}
add_action( 'wp_enqueue_scripts', 'technet_enqueue_assets' );

/**
 * Primary nav menu, falling back to the design system's placeholder nav
 * (Conference / NEATTS / Forums / About) when no menu has been assigned yet
 * in Appearance -> Menus — see Header.jsx in the source design system.
 */
function technet_primary_nav() {
	if ( has_nav_menu( 'primary' ) ) {
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'technet-header__nav-list',
				'depth'          => 1,
			)
		);
		return;
	}

	$fallback = array(
		'Conference' => home_url( '/conference/' ),
		'NEATTS'     => home_url( '/neatts/' ),
		'Forums'     => home_url( '/forums/' ),
		'About'      => home_url( '/about/' ),
	);

	echo '<ul class="technet-header__nav-list">';
	foreach ( $fallback as $label => $url ) {
		printf(
			'<li><a href="%1$s">%2$s</a></li>',
			esc_url( $url ),
			esc_html( $label )
		);
	}
	echo '</ul>';
}

/**
 * Footer links menu, falling back to the design system's placeholder footer
 * links (Google Group / Conference archive / Contact) — see Footer.jsx.
 */
function technet_footer_nav() {
	if ( has_nav_menu( 'footer' ) ) {
		wp_nav_menu(
			array(
				'theme_location' => 'footer',
				'container'      => false,
				'menu_class'     => 'technet-footer__links',
				'depth'          => 1,
			)
		);
		return;
	}

	$fallback = array(
		'Google Group'       => 'https://groups.google.com/',
		'Conference archive' => home_url( '/conference/' ),
		'Contact'            => home_url( '/about/' ),
	);

	echo '<div class="technet-footer__links">';
	foreach ( $fallback as $label => $url ) {
		printf(
			'<a href="%1$s">%2$s</a>',
			esc_url( $url ),
			esc_html( $label )
		);
	}
	echo '</div>';
}

/**
 * URL of the homepage hero banner image, or '' if none has been added yet.
 * Checked by filename convention (assets/images/hero-banner.{ext}) rather
 * than a media-library upload, so the hero degrades gracefully — no banner
 * file means no banner markup at all, not a broken image.
 *
 * @return string
 */
function technet_hero_banner_url() {
	foreach ( array( 'jpg', 'jpeg', 'png', 'webp' ) as $ext ) {
		$path = get_template_directory() . '/assets/images/hero-banner.' . $ext;
		if ( file_exists( $path ) ) {
			return get_template_directory_uri() . '/assets/images/hero-banner.' . $ext;
		}
	}
	return '';
}

/**
 * True while PMP considers the current user an active member of the given
 * level (or any level if $level_id is null). Returns false gracefully if
 * Paid Memberships Pro isn't active, so templates can degrade to a public
 * teaser instead of fataling.
 *
 * @param int|null $level_id Optional PMP membership level ID.
 * @return bool
 */
function technet_is_member( $level_id = null ) {
	if ( ! function_exists( 'pmpro_hasMembershipLevel' ) ) {
		return false;
	}
	return (bool) pmpro_hasMembershipLevel( $level_id );
}
