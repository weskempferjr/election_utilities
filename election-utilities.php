<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://tnotw.com
 * @since             1.0.0
 * @package           Election_Utilities
 *
 * @wordpress-plugin
 * Plugin Name:       Election Utilities
 * Plugin URI:        https://tnotw.com/election-utilities
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Wes Kempfer
 * Author URI:        https://tnotw.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       election-utilities
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '1.0.0' );



if ( !defined('PLUGIN_VENDOR_PREFIX') )
	define('PLUGIN_VENDOR_PREFIX', 'tnotw_');

if (!defined('ELECTION_UTILITIES_TEXTDOMAIN'))
	define( 'ELECTION_UTILITIES_TEXTDOMAIN', 'election_utilities');

if (!defined('ELECTION_UTILITIES_OPTIONS_NAME'))
	define('ELECTION_UTILITIES_OPTIONS_NAME', 'election_utilities_options');


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-election-utilities-activator.php
 */
function activate_election_utilities() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-election-utilities-activator.php';
	Election_Utilities_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-election-utilities-deactivator.php
 */
function deactivate_election_utilities() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-election-utilities-deactivator.php';
	Election_Utilities_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_election_utilities' );
register_deactivation_hook( __FILE__, 'deactivate_election_utilities' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-election-utilities.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_election_utilities() {

	$plugin = new Election_Utilities();
	$plugin->run();

}
run_election_utilities();
