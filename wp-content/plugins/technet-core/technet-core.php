<?php
/**
 * Plugin Name: TechNet Core
 * Description: First-party glue for the TechNet Australia site — speaker/session custom post types, conference registration + NEATTS nomination handling, member directory data, and sandbox demo-content seeding. Companion to the technet-australia theme.
 * Version: 0.1.0
 * Requires at least: 6.4
 * Requires PHP: 8.0
 * Author: TechNet Australia
 * License: GPL-2.0-or-later
 * Text Domain: technet-core
 *
 * @package TechNet_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TECHNET_CORE_VERSION', '0.1.0' );
define( 'TECHNET_CORE_PATH', plugin_dir_path( __FILE__ ) );

require_once TECHNET_CORE_PATH . 'inc/cpts.php';
require_once TECHNET_CORE_PATH . 'inc/user-fields.php';
require_once TECHNET_CORE_PATH . 'inc/helpers.php';
require_once TECHNET_CORE_PATH . 'inc/forms.php';
require_once TECHNET_CORE_PATH . 'inc/csv-export.php';

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once TECHNET_CORE_PATH . 'bin/seed-demo-content.php';
}

/**
 * Flush rewrite rules once on activation so the new post types' admin
 * screens work immediately without a manual "Save" on Permalinks.
 */
function technet_core_activate() {
	technet_register_post_types();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'technet_core_activate' );

function technet_core_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'technet_core_deactivate' );
