<?php
//session_start();
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    safetag
 * @subpackage safetag/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    safetag
 * @subpackage safetag/admin
 * @author     Your Name <email@example.com>
 */
class Safetag_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $safetag    The ID of this plugin.
	 */
	private $safetag;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $safetag       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($safetag, $version) {

		$this->safetag = $safetag;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles($hook) {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Safetag_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Safetag_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style('font-awesom', plugin_dir_url(__FILE__) . 'css/font-awesome.min.css', array(), $this->version, 'all');
		wp_enqueue_style($this->safetag, plugin_dir_url(__FILE__) . 'css/safetag-admin.css', array(), $this->version, 'all');

		if ( $hook == 'post-new.php' || $hook == 'post.php' || $hook == 'toplevel_page_safetag-setting-page' || $hook == 'safetag_page_safetag-setting-page' ) {
		  wp_enqueue_style('safetag-selectize-css', plugin_dir_url(__FILE__) . 'css/selectize.css', array(), $this->version, 'all');
		}
		if( $hook == 'toplevel_page_exclusion-list-setting-page') {
			wp_enqueue_style('picharts-css', plugin_dir_url(__FILE__) . 'css/picharts.css', array(), $this->version, 'all');
		}
		wp_enqueue_style('tagify-all', plugin_dir_url(__FILE__) . 'css/tagify.min.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts( $hook ) {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Safetag_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Safetag_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script('xlsx', plugin_dir_url(__FILE__) . 'js/xlsx.js', [], $this->version, false);
		wp_enqueue_script('jszip', plugin_dir_url(__FILE__) . 'js/jszip.js', [], $this->version, false);
		wp_enqueue_script('xlsx-full', plugin_dir_url(__FILE__) . 'js/xlsx.full.min.js', [], $this->version, false);
		wp_enqueue_script($this->safetag, plugin_dir_url(__FILE__) . 'js/safetag-admin.js', array('jquery'), $this->version, false);

		if ( $hook == 'post-new.php' || $hook == 'post.php' || $hook == 'toplevel_page_safetag-setting-page' || $hook == 'safetag_page_safetag-setting-page' ) {
			wp_enqueue_script("safetag-selectize-js", plugin_dir_url(__FILE__) . 'js/selectize.min.js', array('jquery'), $this->version, false);
		}
		wp_enqueue_script("tagify-js", plugin_dir_url(__FILE__) . 'js/tagify.min.js', array('jquery'), $this->version, false);
		wp_localize_script($this->safetag, 'safetagSetting',
			[
				'nonce' => wp_create_nonce('wp_rest'),
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			]
		);
	}

	/**
	 * Register admnin menu item pages.
	 *
	 * @since    1.0.0
	 */
	public function safetag_add_pages() {
		add_menu_page(__('Safetag Settings', 'menu-test'), __('Safetag ', 'menu-test'), 'manage_safetag_campaign', 'exclusion-list-setting-page', array($this, 'exclusion_list_setting_page'), 'dashicons-lock', 4);

		add_submenu_page('exclusion-list-setting-page', __('Campaigns', 'menu-test'), __('Campaigns', 'menu-test'), 'manage_safetag_campaign', 'exclusion-list-setting-page', array($this, 'exclusion_list_setting_page'));

		// add_submenu_page('exclusion-list-setting-page', __('Ads.txt', 'menu-tests'), __('Ads.txt', 'menu-test'), 'manage_safetag_campaign', 'safetag-txt-setting-page', array($this, 'safetag_txt_setting_page'));

		add_submenu_page('exclusion-list-setting-page', __('Settings', 'menu-test'), __('Settings', 'menu-test'), 'manage_safetag_campaign', 'safetag-setting-page', array($this, 'safetag_setting_page'));
		add_submenu_page('safetag-setting-page', __('Reports', 'menu-test'), __('Reports', 'menu-test'), 'manage_safetag_campaign', 'safetag-report-page', array($this, 'safetag_report_page'));
	}

	public function safetag_setting_page() {
		include_once('partials/safetag-setting-page.php');
	}

	public function safetag_txt_setting_page() {
		include_once('partials/safetag-adtxt-setting-page.php');
	}

	public function exclusion_list_setting_page() {
		include_once('partials/exclusion-list-setting-page.php');
	}

	public function safetag_report_page() {
		include_once('partials/safetag-report-page.php');
	}

	/**
	 * Activate the licence of the plugin.
	 *
	 * @since    1.0.1
	 */
	public function admin_form_activate_license() {
		if (isset($_REQUEST['activate_license'])) {

			if (isset($_POST['safetag_config_form_nonce']) && wp_verify_nonce($_POST['safetag_config_form_nonce'], 'safetag_config_form_nonce')) {

				$safetag_license_key = sanitize_text_field($_POST['nds']['safetag_license_key']);

				// activate safetag license key
				$license_data = Safetag_License::activate($safetag_license_key);

				if ($license_data->result == 'success') {
					//Success was returned for the license activation
					$admin_notice = 'Activation status: ' . $license_data->message;
					$isValidForm = true;

					// set key and status
          $license_data = Safetag_License::check($safetag_license_key);
					$safetag_license_key = (object)array(
						"key" => $safetag_license_key,
						"status" => "active",
            "date_expiry" => $license_data->date_expiry
					);
					// update safetag license key
					update_option('safetag_license_key', json_encode($safetag_license_key));
				} else {
					//Show error to the user. Probably entered incorrect license key.
					$admin_notice = 'Activation status: ' . $license_data->message;
					$isValidForm = false;
				}


				$result = array(
					'admin_notice' => $admin_notice,
					'is_valid_form' => $isValidForm,
					'form_submitted' => true,
				);

				// redirect the user to the appropriate page
				Safetag_Admin::custom_redirect($result, sanitize_text_field($_POST['page']));

				exit;
			} else {
				wp_die(__('Invalid nonce specified', "sdsd"), __('Error', sanitize_text_field($_POST['page'])), array(
					'response' 	=> 403,
					'back_link' => 'admin.php?page=' . sanitize_text_field($_POST['page']),
				));
			}
		}
	}

	/**
	 * Deactivate the licence of the plugin.
	 *
	 * @since    1.0.1
	 */
	public function admin_form_deactivate_license() {
		if (isset($_REQUEST['deactivate_license'])) {
			if (isset($_POST['safetag_config_form_nonce']) && wp_verify_nonce($_POST['safetag_config_form_nonce'], 'safetag_config_form_nonce')) {
				$safetag_license_key = sanitize_text_field($_POST['nds']['safetag_license_key']);

				$license_data = Safetag_License::deactivation($safetag_license_key);

				if ($license_data->result == 'success') { //Success was returned for the license activation

					//Uncomment the followng line to see the message that returned from the license server
					$admin_notice = 'Desactivation status: ' . $license_data->message;
					$isValidForm = true;

					$safetag_license_key = (object)array(
						"key" => $safetag_license_key,
						"status" => "inactive",
            "date_expiry" => false
					);
				} else {
					//Show error to the user. Probably entered incorrect license key.
					$admin_notice = 'Desactivation status: ' . $license_data->message;
					$isValidForm = false;

					$safetag_license_key = (object)array(
						"key" => $safetag_license_key,
						"status" => "invalid",
            "date_expiry" => false
					);
				}

				// update safetag license key
				update_option('safetag_license_key', json_encode($safetag_license_key));

				$result = array(
					'admin_notice' => $admin_notice,
					'is_valid_form' => $isValidForm,
					'form_submitted' => true,
				);

				// redirect the user to the appropriate page
				Safetag_Admin::custom_redirect($result, sanitize_text_field($_POST['page']));
				exit;
			} else {
				wp_die(__('Invalid nonce specified', "sdsd"), __('Error', sanitize_text_field($_POST['page'])), array(
					'response' 	=> 403,
					'back_link' => 'admin.php?page=' . sanitize_text_field($_POST['page']),
				));
			}
		}
	}

  private function check_multidomain_limit($license_data)
  {
    $status = false;
    if(isset($license_data->registered_domains)) {
      if(!empty($license_data->registered_domains)) { // number of domain registered, now check if our domain exist
        $list_of_domains = array_column($license_data->registered_domains, 'registered_domain');
        if(in_array($_SERVER['SERVER_NAME'], $list_of_domains) !== false) {
          $status = true;
        }
      } else { // no domain registerd yet!
        $status = true;
      }
    }
    return $status;
  }

	/**
	 * Deactivate the licence of the plugin.
	 *
	 * @since    1.0.0
	 */
	public function admin_form_check_license() {
		if (isset($_POST['check_license'])) {
			if (isset($_POST['safetag_config_form_nonce']) && wp_verify_nonce($_POST['safetag_config_form_nonce'], 'safetag_config_form_nonce')) {

				$safetag_license_key = sanitize_text_field($_POST['nds']['safetag_license_key']);

				$license_data = Safetag_License::check($safetag_license_key);
				if ($license_data->result == 'success') {
          //Success was returned for the license activation

            $admin_notice = 'License status: ' . $license_data->status;
            $isValidForm = true;
            // $safetag_license_key = (object)array(
            //   "key" => $safetag_license_key,
            //   "status" => $license_data->status,
            //   "date_expiry" => $license_data->date_expiry
            // );
				} else {
					//Show error to the user. Probably entered incorrect license key.
					$message = isset($license_data->status) ? $license_data->status : $license_data->message;
					$admin_notice = 'License status: ' . $message;
					$isValidForm = false;
					// $safetag_license_key = (object)array(
					// 	"key" => $safetag_license_key,
					// 	"status" => "invalid",
          //   "date_expiry" => false
					// );
				}

				// update safetag license key
				// update_option('safetag_license_key', json_encode($safetag_license_key));

				$result = array(
					'admin_notice' => $admin_notice,
					'is_valid_form' => $isValidForm,
					'form_submitted' => true,
				);

				// redirect the user to the appropriate page
				Safetag_Admin::custom_redirect($result, sanitize_text_field($_POST['page']));
				exit;
			} else {
				wp_die(__('Invalid nonce specified', "sdsd"), __('Error', sanitize_text_field($_POST['page'])), array(
					'response' 	=> 403,
					'back_link' => 'admin.php?page=' . sanitize_text_field($_POST['page']),
				));
			}
		}
	}


	/**
	 * Register admnin menu item pages.
	 *
	 * @since    1.0.0
	 */
	public function admin_form_save_setting() {
		if (isset($_POST['safetag_config_form_nonce']) && wp_verify_nonce($_POST['safetag_config_form_nonce'], 'safetag_config_form_nonce')) {

			$safetag_iab_tag_option = isset($_POST['nds']['safetag_iab_tag_option']) ? sanitize_text_field($_POST['nds']['safetag_iab_tag_option']) : '';
			$safetag_exclution_list_chron_option = isset($_POST['nds']['safetag_exclution_list_chron_option']) ? sanitize_text_field($_POST['nds']['safetag_exclution_list_chron_option']) : '';

			update_option("safetag_iab_tag_option", $safetag_iab_tag_option);
			update_option("safetag_exclution_list_chron_option", $safetag_exclution_list_chron_option);

			Safetag_Cron_Job::enable_keyword_list_cron_job($safetag_exclution_list_chron_option == 'on' ? true : false);

			// add the admin notice
			$admin_notice = "Your configuration were saved.";
			$isValidForm = true;

			$result = array(
				'admin_notice' => $admin_notice,
				'is_valid_form' => $isValidForm,
				'form_submitted' => true,
			);


			// redirect the user to the appropriate page
			Safetag_Admin::custom_redirect($result, sanitize_text_field($_POST['page']));
			exit;
		} else {
			wp_die(__('Invalid nonce specified', "sdsd"), __('Error', sanitize_text_field($_POST['page'])), array(
				'response' 	=> 403,
				'back_link' => 'admin.php?page=' . sanitize_text_field($_POST['page']),

			));
		}
	}

	/**
	 * description
	 *
	 * @since    1.0.0
	 */
	public function admin_form_save_edit_ads_text_setting() {
		if (isset($_POST['safetag_ads_text_form_nonce']) && wp_verify_nonce($_POST['safetag_ads_text_form_nonce'], 'safetag_ads_text_form_nonce')) {

			$safetag_ads_txt_content_file = sanitize_textarea_field( $_POST['nds']['safetag_ads_txt_content_file']);
			$safetag_ads_txt_notes = sanitize_text_field($_POST['nds']['safetag_notes']);

			if ($safetag_ads_txt_content_file != null && trim($safetag_ads_txt_content_file) != '' && $safetag_ads_txt_notes != null && trim($safetag_ads_txt_notes)) {

				$text = str_replace(' ', '', $safetag_ads_txt_content_file);
				$lines = preg_split('/\s+/', $text);
				$regex = "/((CONTACT|SUBDOMAIN)=(.*?)+)|(#(.*)+)|(.*?)(\s+)?\,(\s+)?(.*?)(\s+)?\,(\s+)?(DIRECT|RESELLER)((\s+)?\,(\s+)?.*)?(\r)?/";
				$errors = [];

				for ($i = 0; $i <= count($lines) - 1; $i++) {
					$lines[$i] = str_replace(',', ', ', $lines[$i]);
				}

				for ($i = 0; $i <= count($lines) - 1; $i++) {

					// separate the characters to evaluate if the line is a comment
					$words = str_split($lines[$i]);

					if ($words[0] != '#') {

						// evaluate if the line complies with the format
						if (!preg_match($regex, $lines[$i])) {

							$line = $i + 1;
							array_push($errors, 'Line ' . $line . ' has an error');
						}
					}
				}


				if (count($errors) != 0) {

					$isValidForm = true;

					$_SESSION['text'] = json_encode($lines);

					$result = array(
						'errors' => $errors,
						'is_valid_form' => $isValidForm,
						'form_submitted' => true
					);

					// redirect the user to the appropriate page
					//Safetag_Admin::custom_redirect($result, $_POST['page'] . '&setting_seccion=adstext-edit&pid=' . $_POST['pid']);

					//exit;
				}

				$record = array(
					'id' => null,
					'wp_user_id' => get_current_user_id(),
					'active' => true,
					'notes' => $safetag_ads_txt_notes,
					'ads_txt_content_file' => $safetag_ads_txt_content_file,
				);


				Safetag_Db_Management::insert_ads_txt_record($record);
				Safetag_File_Managemenet::write_ads_txt_file($safetag_ads_txt_content_file);

				// add the admin notice
				$admin_notice = "Your configuration were saved.";
				$isValidForm = true;
			} else {
				// add the admin notice
				$admin_notice = "fields are required.";
				$isValidForm = false;
			}

			$result = array(
				'admin_notice' => $admin_notice,
				'is_valid_form' => $isValidForm,
				'form_submitted' => true
			);

			// redirect the user to the appropriate page
			Safetag_Admin::custom_redirect($result, sanitize_text_field($_POST['page']));

			exit;
		} else {
			wp_die(__('Invalid nonce specified', "sdsd"), __('Error', sanitize_text_field($_POST['page'])), array(
				'response' 	=> 403,
				'back_link' => 'admin.php?page=' . sanitize_text_field($_POST['page']),

			));
		}
	}

	/**
	 * Register admnin menu item pages.
	 *
	 * @since    1.0.0
	 */
	public function admin_form_restore_ads_text_setting() {
		if (isset($_POST['safetag_ads_text_restore_form_nonce']) && wp_verify_nonce($_POST['safetag_ads_text_restore_form_nonce'], 'safetag_ads_text_restore_form_nonce')) {

			$safetag_ads_txt_id = sanitize_text_field($_POST['nds']['pid']);

			if ($safetag_ads_txt_id != null && trim($safetag_ads_txt_id) != '') {

				$ads_txt_record = Safetag_Db_Management::get_ads_text_record_by_id($safetag_ads_txt_id, false);
				if ($ads_txt_record == null) {
					$admin_notice = "record not found.";
					$isValidForm = false;
				} else {
					$record = array(
						'id' => $safetag_ads_txt_id,
						'active' => true,
						'ads_txt_content_file' => "",
					);
					try {
						Safetag_Db_Management::restore_ads_txt_record($safetag_ads_txt_id);
						Safetag_File_Managemenet::write_ads_txt_file($ads_txt_record->ads_txt_content_file);
					} catch (Exception $th) {
						$admin_notice = "internal error.";
						$isValidForm = false;
					}


					// add the admin notice
					$admin_notice = "Your configuration were saved.";
					$isValidForm = true;
				}
			} else {
				// add the admin notice
				$admin_notice = "fields are required.";
				$isValidForm = false;
			}

			$result = array(
				'admin_notice' => $admin_notice,
				'is_valid_form' => $isValidForm,
				'form_submitted' => true
			);

			// redirect the user to the appropriate page
			Safetag_Admin::custom_redirect($result, sanitize_text_field($_POST['page']));

			exit;
		} else {
			wp_die(__('Invalid nonce specified', "sdsd"), __('Error', sanitize_text_field($_POST['page'])), array(
				'response' 	=> 403,
				'back_link' => 'admin.php?page=' . sanitize_text_field($_POST['page']),

			));
		}
	}
		/**
	 * Add iab tags of the plugin.
	 *
	 * @since    1.0.1
	 */
	public function admin_form_save_iab_tags_setting() {
		if (isset($_REQUEST['tags_create'])) {
			if (isset($_POST['safetag_iab_tags_form_nonce']) && wp_verify_nonce($_POST['safetag_iab_tags_form_nonce'], 'safetag_iab_tags_form_nonce')) {

				if (isset($_POST['nds']['site_audience_iab_tags'])) {
					// Sanitize user input.
					foreach($_POST['nds']['site_audience_iab_tags'] as $key => $value) {
						$site_audience_iab_tags[$key] = sanitize_text_field($value);
					}
					// Update the meta field in the database.
					update_option('site_audience_iab_tags', json_encode($site_audience_iab_tags));

				} else {
					update_option('site_audience_iab_tags', '');
				}
				// add the admin notice
				$admin_notice = "Your tags have been saved.";
				$isValidForm = true;

				$result = array(
					'admin_notice' => $admin_notice,
					'is_valid_form' => $isValidForm,
					'form_submitted' => true,
				);
				// redirect the user to the appropriate page
				Safetag_Admin::custom_redirect($result, sanitize_text_field($_POST['page']));
				exit;
			} else {
				wp_die(__('Invalid nonce specified', "sdsd"), __('Error', sanitize_text_field($_POST['page'])), array(
					'response' 	=> 403,
					'back_link' => 'admin.php?page=' . sanitize_text_field($_POST['page']),
				));
			}
		}
	}

		/**
	 * Add post types of the plugin.
	 *
	 * @since    1.0.1
	 */
	public function admin_form_save_post_types() {
		if (isset($_REQUEST['add_post_types'])) {
			if (isset($_POST['safetag_post_types_form_nonce']) && wp_verify_nonce($_POST['safetag_post_types_form_nonce'], 'safetag_post_types_form_nonce')) {

        // add the admin notice
				$admin_notice = "You have saved post types.";
				$isValidForm = true;
				if (isset($_POST['post_types']) && !empty($_POST['post_types'])) {
          $post_types = $_POST['post_types'];
          $selected_post_types = json_decode(get_option('safetag_post_types'));
          $selected_post_types = (array) $selected_post_types ?? [];

          $insert_post_types = array_diff($post_types, $selected_post_types);
          $delete_post_types = array_diff($selected_post_types, $post_types);

          $all_campaign = Safetag_Db_Management::get_all_campaign();

          if(!empty($all_campaign) && !empty($insert_post_types)) {
            foreach ($all_campaign as $item) {
              Safetag_Db_Management::insert_safetag_records_campain_wise_single($item->id, $insert_post_types);
            }
          }

          if(!empty($delete_post_types)) {
            Safetag_Db_Management::delete_safetag_records_campaign_post_types_wise($delete_post_types);
          }

					update_option('safetag_post_types', json_encode($post_types));
				} else {
          // add the admin notice
          $admin_notice = "Select at least one post type.";
          $isValidForm = false;
				}

				$result = array(
					'admin_notice' => $admin_notice,
					'is_valid_form' => $isValidForm,
					'form_submitted' => true,
				);
				// redirect the user to the appropriate page
				Safetag_Admin::custom_redirect($result, sanitize_text_field($_POST['page']));
				exit;
			} else {
				wp_die(__('Invalid nonce specified', "sdsd"), __('Error', sanitize_text_field($_POST['page'])), array(
					'response' 	=> 403,
					'back_link' => 'admin.php?page=' . sanitize_text_field($_POST['page']),
				));
			}
		}
	}

	/**
	 * description
	 *
	 * @since    1.0.0
	 */
	public function admin_form_save_edit_exclusion_list_setting() {
		if (isset($_POST['safetag_exclusion_list_form_nonce']) && wp_verify_nonce($_POST['safetag_exclusion_list_form_nonce'], 'safetag_exclusion_list_form_nonce')) {

			$exclusion_list_id = sanitize_text_field($_POST['pid']);
			$safetag_keywords = sanitize_textarea_field($_POST['nds']['safetag_keywords']);
			$safetag_type = sanitize_text_field($_POST['nds']['safetag_type']);
			$safetag_campaign_name = sanitize_text_field($_POST['nds']['safetag_campaign_name']);
			$safetag_active = isset($_POST['nds']['safetag_active']) ? true : false;

      // trim keywords from input - convert to array then remove white spaces
      $safetag_keywords       = preg_split ('/\n/',$safetag_keywords);
      $safetag_keywords = array_filter(array_map('trim', $safetag_keywords));  // remove empty element and trim white spaces
      $safetag_keywords       = implode('\r\n', $safetag_keywords);

      $change = false;
      $over500 = false;
      $result = [];
			if ($safetag_keywords != null && trim($safetag_keywords) != '' && $safetag_campaign_name != null && trim($safetag_campaign_name)) {
				// $licence 				= json_decode( get_option('safetag_license_key'));
        $licence_status = Safetag_Page_Helper::get_option_data_single('safetag_license_key', 'status');

				$keywords_array = explode("\r\n", trim($safetag_keywords));
				if(count($keywords_array) > 500 && (empty($licence_status) || $licence_status !== 'active')) {
					$safetag_keywords = array_slice($keywords_array, 0, 500);
					$safetag_keywords = implode('\r\n', $safetag_keywords);
          $over500 = true;
				}
        // check if license are expired
        $this->check_license_expired($licence_status, $exclusion_list_id, $over500);

				$exclusion_list = array(
					'name' => $safetag_campaign_name,
					'type' => $safetag_type,
					'active' => $safetag_active
				);

        $safetag_keywords = stripcslashes($safetag_keywords);
				if ($exclusion_list_id == null || $exclusion_list_id == '') { // insert campaign
					$exclusion_list_id = Safetag_Db_Management::insert_exclusion_list_record($exclusion_list);

          // check if campaign name already exist
          if(empty($exclusion_list_id)) {
            $result = array_merge($result, array(
              'setting_seccion' => sanitize_text_field($_POST['setting_seccion']),
              'pid' => '',
              'admin_notice' => 'The campaign name already exists',
              'is_valid_form' => false,
              'form_submitted' => true
            ));

            Safetag_Admin::custom_redirect($result, sanitize_text_field($_POST['page']));
            exit;
          }

					if ($safetag_active) { // insert all post against canpaign (becouse its new)
						Safetag_Db_Management::insert_safetag_records_campain_wise_single($exclusion_list_id);
					}
					$change = true;
				} else { // update campaign
					$change = $this->check_camp_keyword_change($exclusion_list_id, $safetag_keywords);
					Safetag_Db_Management::update_exclusion_list_record($exclusion_list, array('id' => $exclusion_list_id));

					if($change) { // if user change keyword in edit mood, otherwise nothing will happen in update
						// Create or update post keyword row status.
						if ($safetag_active) { // update all post against canpaign also missing post
						  Safetag_Cron_Job::add_or_update_post_keyword_status($exclusion_list_id);
						}
					}
				}

				if($change){ // if user change keyword in edit mood, otherwise nothing will happen in update
					$exclusion_list_history = array(
						'id' => null,
						'wp_user_id' => get_current_user_id(),
						'keywords' => $safetag_keywords,
						'active' => true,
						'exclusion_list_id' => $exclusion_list_id
					);
					Safetag_Db_Management::insert_exclusion_list_history_record($exclusion_list_history, $exclusion_list_id);
				}

				// add the admin notice
        $admin_notice = "Your list of terms has successfully uploaded.";
        $isValidForm = true;
        if($over500){ // free version and camp was contain over 500 line
          $result['over_500_error'] = true;
        }
        if(empty($licence_status) || ($licence_status !== 'active' && !$over500)) { // free version but contain within 500 line
          $result['over_500_success'] = true;
        }
			} else {
				// add the admin notice
				$admin_notice = "fields are required.";
				$isValidForm = false;
			}

      $result = array_merge($result, array(
				'setting_seccion' => sanitize_text_field($_POST['setting_seccion']),
				'pid' => $exclusion_list_id,
				'admin_notice' => $admin_notice,
				'is_valid_form' => $isValidForm,
				'form_submitted' => true
			));

			// redirect the user to the appropriate page
			Safetag_Admin::custom_redirect($result, sanitize_text_field($_POST['page']));

			exit;
		} else {
			wp_die(__('Invalid nonce specified', "sdsd"), __('Error', sanitize_text_field($_POST['page'])), array(
				'response' 	=> 403,
				'back_link' => 'admin.php?page=' . sanitize_text_field($_POST['page']),

			));
		}
	}


  private function check_license_expired($status, $exclusion_list_id, $over500)
  {
    if($status == 'expired' && $exclusion_list_id !== '' && $over500) {
      $result = array(
				'setting_seccion' => sanitize_text_field($_POST['setting_seccion']),
				'pid' => $exclusion_list_id,
				'admin_notice' => 'Your license is expired. Cannot update campaign with more then 500 characters.',
				'is_valid_form' => false,
				'form_submitted' => true
			);

			// redirect the user to the appropriate page
			Safetag_Admin::custom_redirect($result, sanitize_text_field($_POST['page']));
      exit;
    }
  }

  private function check_camp_keyword_change($exclusion_list_id, $new_keywords)
  {
    $list_history_old = Safetag_Db_Management::get_exclusion_list_history_by_camp_id($exclusion_list_id);
		if(strcmp($list_history_old->keywords, $new_keywords) !== 0) {  // strcmp — Binary safe string comparison // 0 if they are equal.
      return true;
    }
    return false;
  }

	/**
	 * description
	 *
	 * @since    1.0.0
	 */
	public function admin_form_restore_exclusion_list_setting() {
		if (isset($_POST['safetag_exclusion_list_form_restore_nonce']) && wp_verify_nonce($_POST['safetag_exclusion_list_form_restore_nonce'], 'safetag_exclusion_list_form_restore_nonce')) {

			$exclusion_list_history_id = sanitize_text_field($_POST['nds']['exclusion_list_history_id']);
			$exclusion_list_id = sanitize_text_field($_POST['nds']['pid']);

			if ($exclusion_list_history_id != null && trim($exclusion_list_history_id) != '') {

				Safetag_Db_Management::restore_exclusion_list_history_record($exclusion_list_history_id);

				$exclusion_list = Safetag_Db_Management::get_exclusion_list_by_id($exclusion_list_id);

				if ($exclusion_list->active) {
					Safetag_Cron_Job::update_posts_campaign_status_by_campaign_id($exclusion_list_id);
				}

				// add the admin notice
				$admin_notice = "Your configuration was restored.";
				$isValidForm = true;
			} else {
				// add the admin notice
				$admin_notice = "fields are required.";
				$isValidForm = false;
			}

			$result = array(
				'setting_seccion' => sanitize_text_field($_POST['setting_seccion']),
				'pid' => $exclusion_list_id,
				'admin_notice' => $admin_notice,
				'is_valid_form' => $isValidForm,
				'form_submitted' => true
			);

			// redirect the user to the appropriate page
			Safetag_Admin::custom_redirect($result, sanitize_text_field($_POST['page']));

			exit;
		} else {
			wp_die(__('Invalid nonce specified', "sdsd"), __('Error', sanitize_text_field($_POST['page'])), array(
				'response' 	=> 403,
				'back_link' => 'admin.php?page=' . sanitize_text_field($_POST['page']),

			));
		}
	}

	public static function custom_redirect($params, $page) {
		wp_redirect(esc_url_raw(add_query_arg(
			$params,
			admin_url('admin.php?page=' . $page)
		)));
	}

	public static function generate_url($params, $page) {
		return esc_url_raw(add_query_arg(
			$params,
			admin_url('admin.php?page=' . $page)
		));
	}

	public static function safetag_admin_notice() {
		if (isset($_GET['form_submitted'])) {

			if (!isset($_GET['is_valid_form']) && isset($_GET['admin_notice'])) { ?>

          <div class="error not-success-message-edit-campiagns admin-msg">
            <p><?php echo esc_html( $_GET['admin_notice']) ?></p>
          </div>

			<?php } else { ?>
        <?php if(isset($_GET['over_500_error'])) { ?>
          <div class="updated not-success-message-edit-campiagns admin-msg">
            <p>Your list has more than 500 terms and has been truncated to only include the first 500 lines. <button class="error-btn-close">&times;</button></p>
            <p>To purchase a license for unlimited Campaigns and Terms <a href="https://safetag.ai/pricing/" target="_blank">visit our site</a>.</p>
          </div>
        <?php } elseif(isset($_GET['over_500_success'])) { ?>
          <div class="updated success-message-edit-campiagns admin-msg">
            <p>Your list of terms has successfully uploaded. <button class="success-btn-close">&times;</button></p>
            <p>To purchase a license for unlimited Campaigns and Terms <a href="https://safetag.ai/pricing/" target="_blank">visit our site</a>.</p>
          </div>
        <?php } else { ?>
				<div class="updated success-message-edit-campiagns admin-msg">
					<p><?php echo esc_html( $_GET['admin_notice']) ?></p>
				</div>
        <?php } ?>
<?php
			}
		}
	}

	/**
	 * description
	 *
	 * @since    1.0.0
	 */
	public function safetag_meta_box_setting() {

		$result_screen = self::get_post_type_screem();

		foreach ($result_screen as $screen) {
			add_meta_box(
				'global-notice',
				__('Safetag', 'textdomain'),
				[$this, 'safetag_meta_box_partial_page'],
				$screen,
				'side',
				'high',
				array(
					'__back_compat_meta_box' => false,
				)
			);
		}
	}

	public static function get_post_type_screem() {
		$args = array(
			'public'   => true,
			'_builtin' => false
		);
		$screens = get_post_types($args);
		$screens_default = array('post', 'page');
		return array_merge($screens_default, $screens);
	}

	/**
	 * description
	 *
	 * @since    1.0.0
	 */
	public function safetag_meta_box_partial_page($post) {
		include_once('partials/safetag-meta-box-setting-page-setting.php');
	}

	/**
	 * description
	 *
	 * @since    1.0.0
	 */
	public function save_safetag_meta_box_data($post_id) {

		// Check if our nonce is set.
		if (!isset($_POST['safetag_meta_box_notice_nonce'])) {
			return;
		}

		// Verify that the nonce is valid.
		if (!wp_verify_nonce($_POST['safetag_meta_box_notice_nonce'], 'safetag_meta_box_notice_nonce')) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}

		// Check the user's permissions.
		if (isset($_POST['post_type']) && 'page' == $_POST['post_type']) {

			if (!current_user_can('edit_page', $post_id)) {
				return;
			}
		} else {

			if (!current_user_can('edit_post', $post_id)) {
				return;
			}
		}

		/* OK, it's safe for us to save the data now. */

		// Make sure that it is set.
		if (isset($_POST['nds']['safetag_hide_programmatic_ads'])) {
			// Sanitize user input.
			$safetag_hide_programmatic_ads = sanitize_text_field($_POST['nds']['safetag_hide_programmatic_ads']);

			// Update the meta field in the database.
			update_post_meta($post_id, 'safetag_hide_programmatic_ads', $safetag_hide_programmatic_ads);
		} else {
			update_post_meta($post_id, 'safetag_hide_programmatic_ads', '');
		}

		if (isset($_POST['nds']['safetag_meta_tags'])) {
			// Sanitize user input.
			foreach($_POST['nds']['safetag_meta_tags'] as $key => $value) {
				$safetag_meta_tags[$key] = sanitize_text_field($value);
			}
			// Update the meta field in the database.
			update_post_meta($post_id, 'safetag_meta_tags', json_encode($safetag_meta_tags));
		} else {
			update_post_meta($post_id, 'safetag_meta_tags', '');
		}
    // update post keywords
		$this->update_single_post_keywords($post_id);
	}

  public function save_safetag_future_to_publish($post_id) {
    // if post status comes from schedule to publish
		$this->update_single_post_keywords($post_id);
  }

  protected function update_single_post_keywords($post_id) {
    // if post status comes from schedule to publish
		$current_campaigns = Safetag_Db_Management::get_exclusion_list_records_active();
    $selected_post_types = json_decode(get_option('safetag_post_types'));
    $selected_post_types = (array) $selected_post_types ?? [];
    if(array_key_exists(get_post_type($post_id), $selected_post_types)) {
      Safetag_Cron_Job::update_post_keywords($post_id, $current_campaigns);
    }
  }

	/**
	 * description
	 *
	 * @since    2.0.4
	 */
	public function trash_safetag_meta_box_data($post_id) {
		if(empty($post_id)) return;

		Safetag_Db_Management::delete_post_from_campaign_keywords_by_post_id($post_id);
	}

	/**
	 * description
	 *
	 * @since    1.0.0
	 */
	public function admin_form_delete_exclusion_list_setting() {
		if (isset($_POST['safetag_exclusion_delete_form_nonce']) && wp_verify_nonce($_POST['safetag_exclusion_delete_form_nonce'], 'safetag_exclusion_delete_form_nonce')) {

			$exclusion_list_id = sanitize_text_field($_POST['pid']);

			if ($exclusion_list_id > 0) {

				Safetag_Db_Management::delete_campaigns_list($exclusion_list_id);
				$admin_notice = "Your campaign has been deleted.";
				$isValidForm = true;
			} else {
				// add the admin notice
				$admin_notice = "Campaign not found.";
				$isValidForm = false;
			}

			$result = array(
				'admin_notice' => $admin_notice,
				'is_valid_form' => $isValidForm,
				'form_submitted' => true
			);

			// redirect the user to the appropriate page
			Safetag_Admin::custom_redirect($result, 'exclusion-list-setting-page');

			exit;
		} else {
			$page = sanitize_text_field($_POST['page']);
			wp_die(__('Invalid nonce specified', "sdsd"), __('Error', $page), array(
				'response' => 403,
				'back_link' => 'admin.php?page=' . $page,
			));
		}
	}

	public static function get_post_data_formatted($post_id) {
    $post = get_post($post_id);
    $post_content = strip_tags($post->post_content, '<br>');

    $post_meta = json_encode(get_post_meta($post_id));
    if(!$post_meta){
      $post_meta = strip_tags($post_meta, '<br>');
    } else {
      $post_meta = '';
    }

    $tags = get_the_tags($post_id);
    if(is_array($tags)) {
      $tags = implode(", ", array_map(['Safetag_Admin', 'tag_category_values'], $tags != false ? $tags : []));
      $tags = strip_tags($tags, '<br>');
    } else {
      $tags = '';
    }

    $category = implode(", ", array_map(['Safetag_Admin', 'tag_category_values'], get_the_category($post_id)));
    $category = strip_tags($category, '<br>');

		return [
			"id" => $post_id,
			"post_type" => $post->post_type,
			"content" => "$post->ID, $post->post_title, $post->post_name, $post_meta, $tags, $category, $post_content"
		];
	}

	public static function tag_category_values($tag_category) {
		return  "$tag_category->name $tag_category->description";
	}

  public static function get_safetag_post_types()
  {
    $post_types = get_transient('safetag_post_types');

    // Check if the transient cache is empty
    if (empty($post_types)) {
      $post_types = get_post_types(
        [
          "public"    => true,
          "_builtin"  => false
        ],
        'objects'
      );
      set_transient('safetag_post_types', $post_types, 300);
    }

    $post_types_in_array = [
      [
        'name'  => 'post',
        'label' => 'Posts'
      ],
      [
        'name'  => 'page',
        'label' => 'Pages'
      ]
    ];
    foreach ($post_types as $type) {
      $post_types_in_array[] = [
        'name'  => $type->name,
        'label' => $type->label
      ];
    }

    return $post_types_in_array;
  }
}
