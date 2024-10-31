<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    safetag
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
	exit;
}

$options_to_delete = [
  'safetag_exclution_list_chron_option',
  'safetag_last_cron_run_time', // track last cron run time
  'safetag_iab_tag_option',
  'safetag_license_key',
];

foreach ($options_to_delete as $option_name) {
  delete_option($option_name);
  // for site options in Multisite
  delete_site_option($option_name);
}

// drop a custom database table
global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}safetag_adstxt");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}safetag_exclusion_list");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}safetag_exclusion_list_history");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}safetag_post_campaign_keywords");
