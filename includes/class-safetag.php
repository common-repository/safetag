<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    safetag
 * @subpackage safetag/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    safetag
 * @subpackage safetag/includes
 * @author     Your Name <email@example.com>
 */
class Safetag
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Safetag_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $safetag    The string used to uniquely identify this plugin.
	 */
	protected $safetag;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		if (defined('SAFETAG_VERSION')) {
			$this->version = SAFETAG_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->safetag = 'safetag';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Safetag_Loader. Orchestrates the hooks of the plugin.
	 * - Safetag_i18n. Defines internationalization functionality.
	 * - Safetag_Admin. Defines all hooks for the admin area.
	 * - Safetag_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-safetag-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-safetag-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-safetag-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-safetag-public.php';

		/**
		 * The class responsible for defining all  custom sql query methods.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-safetag-db-management.php';

		/**
		 * The class responsible for file management methods.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-safetag-file-management.php';

		/**
		 * safetag paged helper.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-safetag-paged-helper.php';

		/**
		 * The class responsible for defining all safetag API endpoints.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-safetag-api.php';

		/**
		 * The class responsible for defining all safetag cron jobs.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-safetag-cron-job.php';

		/**
		 * The class responsible for validate safetag license key.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-safetag-license.php';

		/**
		 * The class responsible for logs.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-safetag-log.php';

 		/**  The class display table for logs.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-display-campaign-list.php';
		$this->loader = new Safetag_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Safetag_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{
		$plugin_i18n = new Safetag_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{
		$plugin_admin = new Safetag_Admin($this->get_safetag(), $this->get_version());
		$plugin_admin_res_api = new Safetag_API();

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
		$this->loader->add_action('admin_menu', $plugin_admin, 'safetag_add_pages');

		//License validator methods
		$this->loader->add_action('admin_post_safetag_config_form_response', $plugin_admin, 'admin_form_activate_license');
		$this->loader->add_action('admin_post_safetag_config_form_response', $plugin_admin, 'admin_form_deactivate_license');
		$this->loader->add_action('admin_post_safetag_config_form_response', $plugin_admin, 'admin_form_check_license');
		$this->loader->add_action('admin_post_safetag_config_form_response', $plugin_admin, 'admin_form_save_setting');
		$this->loader->add_action('admin_post_safetag_iab_tags_form_response', $plugin_admin, 'admin_form_save_iab_tags_setting');
		$this->loader->add_action('admin_post_safetag_post_types_form_response', $plugin_admin, 'admin_form_save_post_types');

		// $this->loader->add_action('admin_post_safetag_ads_text_form_response', $plugin_admin, 'admin_form_save_edit_ads_text_setting');
		// $this->loader->add_action('admin_post_safetag_ads_text_form_restore_response', $plugin_admin, 'admin_form_restore_ads_text_setting');
		$this->loader->add_action('admin_post_safetag_exclusion_list_form_response', $plugin_admin, 'admin_form_save_edit_exclusion_list_setting');
		$this->loader->add_action('admin_post_safetag_exclusion_delete_form_response', $plugin_admin, 'admin_form_delete_exclusion_list_setting');
		$this->loader->add_action('admin_post_safetag_exclusion_list_form_restore_response', $plugin_admin, 'admin_form_restore_exclusion_list_setting');
		$this->loader->add_action('add_meta_boxes', $plugin_admin, 'safetag_meta_box_setting');
		$this->loader->add_action('save_post', $plugin_admin, 'save_safetag_meta_box_data');
		$this->loader->add_action('publish_future_post', $plugin_admin, 'save_safetag_future_to_publish');
		$this->loader->add_action('wp_trash_post', $plugin_admin, 'trash_safetag_meta_box_data', 10, 1);
		$this->loader->add_action('rest_api_init', $plugin_admin_res_api, 'register');

		// safetag custom schedule cron job registed.
		$this->loader->add_filter('cron_schedules', 'Safetag_Cron_Job', 'cron_schedule');

		$this->loader->add_action('safetag_update_post_keywords_result', 'Safetag_Cron_Job', 'keyword_job');

		$this->loader->add_action('safetag_update_post_keywords_result_temp', 'Safetag_Cron_Job', 'keyword_job');

		$this->loader->add_action('safetag_check_license_status', 'Safetag_Cron_Job', 'check_license_status');

		$this->loader->add_action('safetag_add_update_post_campaign_keyword_status_temp', 'Safetag_Cron_Job', 'update_posts_campaign_status_by_campaign_id');

		$this->loader->add_action('safetag_genetate_post_campaign_table_record_temp', 'Safetag_Cron_Job', 'generate_posts_campaign_records');

    $this->loader->add_action( 'wp_ajax_get_all_camp_key_count', 'DisplayCampaingList', 'get_all_camp_key_count');
		$this->loader->add_action( 'wp_ajax_nopriv_get_all_camp_key_count', 'DisplayCampaingList', 'get_all_camp_key_count');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{
		$plugin_public = new Safetag_Public($this->get_safetag(), $this->get_version());

		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
	}


	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_safetag()
	{
		return $this->safetag;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Safetag_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}
}
