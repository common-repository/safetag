<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    safetag
 * @subpackage safetag/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    safetag
 * @subpackage safetag/includes
 * @author     Your Name <email@example.com>
 */
class Safetag_Activator
{

	/**
	 * This method will be fired when th plugin has been activated.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
		self::safetag_ads_txt_table_install();
		self::safetag_ads_txt_first_record();
		self::safetag_exclusion_list_table_install();
		self::safetag_exclusion_list_history_table_install();
		self::safetag_cron_job_register();
		self::safetag_post_campaign_keyword_table_install();
		Safetag_Cron_Job::generate_posts_campaign_records_cron_jobs();
		// Set option to 'on' when plugin activates
    add_option( 'safetag_iab_tag_option', 'on', '', false );
    // Set last cron run time
    Safetag_Page_Helper::last_cron_run_add_or_update_time();
	}

	/**
	 * Create safetag_ads_txt_table on wordpres db.
	 */
	private static function safetag_ads_txt_table_install()
	{
		global $wpdb;
		$prefix_table_name = $wpdb->prefix . SAFETAG_ADSTXT_TABLE;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $prefix_table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        wp_user_id INT NOT NULL,
		active BOOLEAN NULL,
        notes VARCHAR(600) NULL,
        ads_txt_content_file MEDIUMTEXT NULL,
		created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  Id(Id)
        ) $charset_collate;";

        $result = $wpdb->query($sql);
		if($result == false) {
			wp_die('Sorry, failed to create the table. Please check if your database user settings have the necessary permissions to create tables. <br><a href="' . admin_url('plugins.php') . '">&laquo; Return to Plugins</a>');
		}
	}

	/**
	 * Create an initial record at safetag_ads_txt_table.
	 */
	private static function safetag_ads_txt_first_record()
	{
		$record = Safetag_Db_Management::get_ads_text_records(0, 1, 'id', 'desc');

		if ($record['total'] <= 0) {
			$record = array(
				'id' => null,
				'wp_user_id' => get_current_user_id(),
				'active' => true,
				'notes' => "first record",
				'ads_txt_content_file' => '',
			);
			Safetag_Db_Management::insert_ads_txt_record($record);
		}
	}

	/**
	 * Create safetag_exclusion_list_table on wordpres db.
	 */
	private static function safetag_exclusion_list_table_install()
	{
		global $wpdb;
		$prefix_table_name = $wpdb->prefix . SAFETAG_EXCLUSION_LIST_TABLE;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $prefix_table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		name VARCHAR(160) NULL,
		type INT NOT NULL DEFAULT 0,
		active BOOLEAN NULL,
		created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  Id(Id)
        ) $charset_collate;";

		$result = $wpdb->query($sql);
		if($result == false) {
			wp_die('Sorry, failed to create the table. Please check if your database user settings have the necessary permissions to create tables. <br><a href="' . admin_url('plugins.php') . '">&laquo; Return to Plugins</a>');
		}
	}

	/**
	 * Create safetag_exclusion_list_history_table on wordpres db.
	 */
	private static function safetag_exclusion_list_history_table_install()
	{
		global $wpdb;
		$prefix__parent_table_name = $wpdb->prefix . SAFETAG_EXCLUSION_LIST_TABLE;
		$prefix_table_name = $wpdb->prefix . SAFETAG_EXCLUSION_LIST_HISTORY_TABLE;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $prefix_table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        wp_user_id INT NOT NULL,
		keywords MEDIUMTEXT NULL,
		active BOOLEAN NULL,
        exclusion_list_id BIGINT(20) UNSIGNED NOT NULL,
		created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY  Id(Id),
		INDEX (exclusion_list_id),
		FOREIGN KEY (exclusion_list_id)
			REFERENCES $prefix__parent_table_name(id)
        ) $charset_collate;";

		$result = $wpdb->query($sql);
		if($result == false) {
			wp_die('Sorry, failed to create the table. Please check if your database user settings have the necessary permissions to create tables. <br><a href="' . admin_url('plugins.php') . '">&laquo; Return to Plugins</a>');
		}
	}

	/**
	 * Create safetag_post_campaign_keyword_table on wordpres db.
	 */
	private static function safetag_post_campaign_keyword_table_install()
	{
		global $wpdb;
		$prefix_table_name = $wpdb->prefix . SAFETAG_POST_CAMPAIGN_KEYWORDS_TABLE;
		$prefix__parent_table_name = $wpdb->prefix . SAFETAG_EXCLUSION_LIST_TABLE;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $prefix_table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        campaign_id BIGINT(20) UNSIGNED NOT NULL,
		post_id INT NOT NULL,
        update_required BOOLEAN NULL,
        keywords MEDIUMTEXT NULL,
        PRIMARY KEY  Id(Id),
		INDEX (campaign_id),
		INDEX (post_id),
		INDEX (update_required),
		INDEX (campaign_id, post_id, update_required),
		INDEX (campaign_id, post_id),
		FOREIGN KEY (campaign_id)
			REFERENCES $prefix__parent_table_name(id)
        ) $charset_collate;";

		$result = $wpdb->query($sql);
		if($result == false) {
			wp_die('Sorry, failed to create the table. Please check if your database user settings have the necessary permissions to create tables. <br><a href="' . admin_url('plugins.php') . '">&laquo; Return to Plugins</a>');
		}
	}

	/**
	 * enable or disable the safetag cron job when the plugin has been activated.
	 */
	private static function safetag_cron_job_register()
	{
		//$is_cron_job_enable = get_option('safetag_exclution_list_chron_option');
		Safetag_Cron_Job::enable_keyword_list_cron_job(true);
	}
}
