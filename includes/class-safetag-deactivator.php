<?php

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    safetag
 * @subpackage safetag/includes
 * @author     Your Name <email@example.com>
 */
class Safetag_Deactivator
{

	/**
	 * this method will be fired when the plugin has been deactivated
	 *
	 * @since    1.0.0
	 */
	public static function deactivate()
	{
		// cron job disable
		Safetag_Cron_Job::enable_keyword_list_cron_job(false);
	}
}
