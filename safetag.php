<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://safetag.io/
 * @since             1.0.1
 * @package           Safetag
 *
 * @wordpress-plugin
 * Plugin Name:       Safetag
 * Plugin URI:        https://safetag.ai/
 * Description:       This plugin lets your website interact with the safetag platform.
 * Version:           2.1.6
 * Author:            Sourcetop
 * Author URI:        https://sourcetop.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       safetag
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('SAFETAG_VERSION', '2.1.6');
define('SAFETAG_ADSTXT_TABLE', 'safetag_adstxt');
define('SAFETAG_EXCLUSION_LIST_TABLE', 'safetag_exclusion_list');
define('SAFETAG_EXCLUSION_LIST_HISTORY_TABLE', 'safetag_exclusion_list_history');
define('SAFETAG_POST_CAMPAIGN_KEYWORDS_TABLE', 'safetag_post_campaign_keywords');
//This is the secret key for Activation/Deactivation
define('SAFETAG_SPECIAL_SECRET_KEY', '6241a3d0ecf4f3.83989314');
// API endpoint for safetag staging site
define('SAFETAG_LICENSE_SERVER_URL', 'http://safetag.ai');
// API endpoint for safetag live site
//define('YOUR_LICENSE_SERVER_URL', 'https://console.safetag.io/backend-wp');
// For reference
define('SAFETAG_ITEM_REFERENCE', 'SafeTag Plugin');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-safetag-activator.php
 */
function activate_safetag()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-safetag-activator.php';
	Safetag_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-safetag-deactivator.php
 */
function deactivate_safetag()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-safetag-deactivator.php';
	Safetag_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_safetag');
register_deactivation_hook(__FILE__, 'deactivate_safetag');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-safetag.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_safetag()
{
	$plugin = new Safetag();
	$plugin->run();
}
run_safetag();

function add_safetag_capabilities() {
  // Add the custom capability to the 'administrator' role
  $role = get_role('administrator');
  if (!empty($role)) {
    $role->add_cap('manage_safetag_campaign');
  }

  if (!get_role('safetag')) {
    add_role(
      'safetag',
      'Safetag',
      array(
        'edit_posts'              => true,
        'read'                    => true,
        'upload_files'            => true,
        'manage_safetag_campaign' => true,
      )
    );
  } else {
    // Update existing role if already exists
    $role = get_role('safetag');
    if ($role && !$role->has_cap('manage_safetag_campaign')) {
      $role->add_cap('manage_safetag_campaign');
    }
  }
}

add_action('init', 'add_safetag_capabilities');

//safetag settings button added
function safetag_add_plugin_link( $plugin_actions, $plugin_file ) {
	$new_actions = array();

	if ( basename( plugin_dir_path( __FILE__ ) ) . '/safetag.php' === $plugin_file ) {
		$new_actions['cl_settings'] = sprintf( __( '<a href="%s">Settings</a>', 'safetag-setting' ), esc_url( admin_url( 'admin.php?page=safetag-setting-page' ) ) );
	}

	return array_merge( $new_actions, $plugin_actions );
}
add_filter( 'plugin_action_links', 'safetag_add_plugin_link', 10, 2 );

// check update plugin version and fire update event
function safetag_plugin_update() {
  // if not found
  if(!get_option( "safetag_version" )) add_option( 'safetag_version', SAFETAG_VERSION );
  // check version match
  if ( get_site_option( 'safetag_version' ) != SAFETAG_VERSION ) {
    $function = 'update_'.str_replace('.', '_', get_site_option( 'safetag_version' ));
    // check version function exist
    if(function_exists($function)) $function(); // call update function
    // update latest version
    update_option('safetag_version', SAFETAG_VERSION);
  }
}

add_action( 'plugins_loaded', 'safetag_plugin_update' );

/*
 * update version functions
 *
*/
function update_2_1_2() {
  //set default for the post-type if safetag_post_types is empty
  $selected_post_types = get_option('safetag_post_types');
  if(empty($selected_post_types)) {
    update_option('safetag_post_types', json_encode(['post' => 'post']));
  }
}
