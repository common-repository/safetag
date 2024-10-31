<?php

/**
 * Safetag cron job management.
 *
 * @since      1.0.0
 * @package    safetag
 * @subpackage safetag/includes
 * @author     Your Name <email@example.com>
 */
class Safetag_Cron_Job
{
    public static $campaign_type = ['0' => ['key' => 'blacklist', 'name' => 'Exclude'], '1' => ['key' => 'whitelist', 'name' => 'Include']];

    /**
     * Create a custom cron schedule.
     *
     * @since    1.0.0
     */
    public static function cron_schedule($schedules)
    {
        if (!isset($schedules["15min"])) {
            $schedules["15min"] = array(
                'interval' => 15 * 60,
                'display' => __('Once every 15 minutes')
            );
        }
        if (!isset($schedules["6hours"])) {
            $schedules["6hours"] = array(
                'interval' => (6*60) * 60, // 6 hours
                'display' => __('Once every 6 hours')
            );
        }
        return $schedules;
    }

    /**
     * Enable or disable the safetag cron job.
     *
     * @since    1.0.0
     */
    public static function enable_keyword_list_cron_job($enable)
    {
        if ($enable) {
            if (!wp_next_scheduled('safetag_update_post_keywords_result')) {
                wp_schedule_event(strtotime("+15 minutes"), '15min', 'safetag_update_post_keywords_result');
            }
            if (!wp_next_scheduled('safetag_check_license_status')) {
                wp_schedule_event(strtotime("+6 hours"), '6hours', 'safetag_check_license_status');
            }
        } else {
            // cron job disable
            $timestamp = wp_next_scheduled('safetag_update_post_keywords_result');
            wp_unschedule_event($timestamp, 'safetag_update_post_keywords_result');
            wp_unschedule_event($timestamp, 'safetag_check_license_status');
        }
    }

    /**
     * excute keyword cron job only once.
     * @since    1.0.0
     */
    public static function execute_keyword_job_manually()
    {
        if (!wp_next_scheduled('safetag_update_post_keywords_result_temp')) {
            wp_schedule_single_event(time(), 'safetag_update_post_keywords_result_temp');
        }
    }

    /**
     * excute keyword cron job only once.
     * @since    1.0.0
     */
    public static function add_or_update_post_keyword_status($exclusion_list_id)
    {
        if (!wp_next_scheduled('safetag_add_update_post_campaign_keyword_status_temp')) {
            wp_schedule_single_event(time(), 'safetag_add_update_post_campaign_keyword_status_temp', [$exclusion_list_id]);
        }
    }

    public static function is_wp_recurring_event_in_progress($hook_name)
    {
        return wp_next_scheduled($hook_name) && (wp_next_scheduled($hook_name) - time()) <= 0;
    }

    /**
     * Excute license check cron job
     *
     * @since    1.0.0
     */
    public static function check_license_status()
    {
      $safetag_license_key = Safetag_Page_Helper::get_option_data_single('safetag_license_key', 'key');
      $license_data = Safetag_License::check($safetag_license_key);

      if ($license_data->result == 'success') {
        //Success was returned for the license activation
        $safetag_license_key = (object)array(
          "key" => $safetag_license_key,
          "status" => $license_data->status,
          "date_expiry" => $license_data->date_expiry
        );
      } else {
        $safetag_license_key = (object)array(
          "key" => $safetag_license_key,
          "status" => "invalid",
          "date_expiry" => false
        );
      }
      // update safetag license key
      update_option('safetag_license_key', json_encode($safetag_license_key));
      // error_log(json_encode($safetag_license_key));
    }

    public static function get_camp_ids_by_license_status($campaign_ids)
    {
      $status = Safetag_Page_Helper::get_option_data_single('safetag_license_key', 'status');
      if($status != 'active' && count($campaign_ids) > 0) {
        $latest_item = Safetag_Db_Management::get_latest_campaing();
        $campaign_ids = array(
          $latest_item[0]['id']
        );
      }
      return $campaign_ids;
    }

    /**
     * Excute keyword cron job
     *
     * @since    1.0.0
     */
    public static function keyword_job()
    {
      Safetag_Page_Helper::last_cron_run_add_or_update_time(); // Set last cron run time
      /*if (self::is_wp_recurring_event_in_progress("safetag_update_post_keywords_result")) {
          return;
      }*/
      $start = date('Y-m-d H:i:s');
      try {

        // get posts and campaign to updata
        $posts_campaigns = Safetag_Db_Management::get_posts_campaigns_records();

        // group by post id
        $all_post_ids = array_keys(self::_group_by($posts_campaigns, 'post_id'));

        // group by campaign id
        $campaign_ids = array_keys(self::_group_by($posts_campaigns, 'campaign_id'));

        $chank_post_ids = array_chunk($all_post_ids, 1000, true);

        foreach($chank_post_ids as $post_ids) {
          //get post and campaign detail records.
          $posts_data = self::posts_pages_content_format($post_ids);
          // $campaign_ids = self::get_camp_ids_by_license_status($campaign_ids);
          $campaign_data = self::get_campaigns_keywords($campaign_ids);
          // get keywords campaign filters by post
          $post_keywords_result = self::post_campaign_filter($posts_data, $campaign_data);

          for ($i = 0; $i < count($post_keywords_result); $i++) {
            $post_id = $post_keywords_result[$i]['post_id'];

            $keyword_result = $post_keywords_result[$i]['keyword_result'];

            $selected_post_types = json_decode(get_option('safetag_post_types'));
            $selected_post_types = (array) $selected_post_types ?? [];

            if(array_key_exists(get_post_type($post_id), $selected_post_types)) {
              for ($j = 0; $j < count($keyword_result); $j++) {

                $exclusion_list_id = $keyword_result[$j]['exclusion_list_id'];
                $keywords = $keyword_result[$j]['keywords'];
                $keywords = $keywords != null || count($keywords) > 0 ? $keywords[0] : null;
                self::update_post_keywords($post_id, [(object)['id' => $exclusion_list_id]], false, $keywords);
              }
            } else {
              Safetag_Db_Management::delete_post_from_campaign_keywords_by_post_id($post_id);
            }
          }
          // add safetag to debug.log file
          $total_post = count($post_ids);
          $total_campaign = count($campaign_ids);
          $rowAffected = count($posts_campaigns);

          self::safetag_logs($start, $total_post, $total_campaign, $rowAffected);
        }
      } catch (Exception $e) {
          self::safetag_logs($start, 0, 0, $e->getMessage(), "Exception");
      }
    }

    /**
     * Update post campaign status by id.
     * @since    1.0.0
     */
    public static function update_posts_campaign_status_by_campaign_id($exclusion_list_id)
    {
        $start = date('Y-m-d H:i:s');
        $post_ids = Safetag_Db_Management::get_all_page_post_ids();
        for ($i = 0; $i < count($post_ids); $i++) {
            $post_id = $post_ids[$i];
            self::update_post_keywords($post_id, [(object)['id' => $exclusion_list_id]]);
        }

        // add safetag to debug.log file
        $total_post = count($post_ids);
        $total_campaign = 1;
        self::safetag_logs($start, $total_post, $total_campaign, count($post_ids), "update-post-keyword-status");
    }

    /**
     * Update post keyword by current post.
     * @since    1.0.0
     */
    public static function update_post_keywords($post_id, $current_campaigns, $update_required = null, $keywords = null)
    {
      for ($i = 0; $i < count($current_campaigns); $i++) {

        $campaign = $current_campaigns[$i];

        $update_required = $update_required !== null
            ? $update_required
            : (get_post_status($post_id) == 'publish' ? true : false);

        $post_campaign = Safetag_Db_Management::get_post_campain_by_campaign_id_post_id($campaign->id, $post_id);

        if ($post_campaign != null) {
          // edit
          $update_record = [
            'update_required' => ($update_required) ? 1 : 0,
            'keywords'        => $keywords
          ];

          Safetag_Db_Management::update_post_campaign_by_campaign_record($update_record, ['id' => $post_campaign->id]);
        } else {
          //create
          $create_record = [
            'campaign_id'     => $campaign->id,
            'post_id'         => $post_id,
            'update_required' => ($update_required) ? 1 : 0,
            'keywords'        => null
          ];
          if ($keywords != null) {
              $create_record['keywords'] = $keywords;
          }

          Safetag_Db_Management::insert_post_campain_by_campaign_record($create_record);
        }
      }
    }

    /**
     * generate post campaign by exclusion record id.
     * @since    1.0.0
     */
    public static function generate_posts_campaign_records_by_campaign_id($exclusion_list_id, $post_ids)
    {
        for ($i = 0; $i < count($post_ids); $i++) {
            $post_id = $post_ids[$i];
            self::update_post_keywords($post_id, [(object)['id' => $exclusion_list_id]]);
        }
    }

    /**
     * generate all post campaign by current exclusion list records actived with cron job.
     * @since    1.0.0
     */
    public static function generate_posts_campaign_records_cron_jobs()
    {
        if (!wp_next_scheduled('safetag_genetate_post_campaign_table_record_temp')) {
            wp_schedule_single_event(time(), 'safetag_genetate_post_campaign_table_record_temp');
        }
    }

    /**
     * generate all post campaign by current exclusion list records actived.
     * @since    1.0.0
     */
    public static function generate_posts_campaign_records()
    {
        $start = date('Y-m-d H:i:s');

        $campaigns = Safetag_Db_Management::get_exclusion_list_records_active();
        $post_ids = Safetag_Db_Management::get_all_page_post_ids();

        for ($i = 0; $i < count($campaigns); $i++) {
            $campaign = $campaigns[$i];
            self::generate_posts_campaign_records_by_campaign_id($campaign->id, $post_ids);
        }
        $total_post = count($post_ids);
        $total_campaign = count($campaigns);
        self::safetag_logs($start, count($post_ids), count($campaigns), $total_post * $total_campaign, "create-post-keyword-records");
    }

    public static function _group_by($array, $key)
    {
        $return = array();
        foreach ($array as $val) {
            if (is_array($val)) {
                $return[$val[$key]][] = $val;
            } else {

                $return[$val->{$key}][] = $val;
            }
        }
        return $return;
    }

    private static function post_campaign_filter($posts_data, $campaign_data)
    {
        $result = [];
        for ($i = 0; $i < count($posts_data); $i++) {
            $post_id = $posts_data[$i]['id'];
            $keywords_result = self::campaign__keyword_filter($posts_data[$i]['content'], $campaign_data);
            if (count($keywords_result) > 0) {

                array_push($result, [
                    'post_id' => $post_id,
                    'keyword_result' => $keywords_result
                ]);
            }
        }

        return $result;
    }

    private static function campaign__keyword_filter($content, $campaigns)
    {
        $keywords_result = [];
        $keyword_bank = [];
        for ($c_intex = 0; $c_intex < count($campaigns); $c_intex++) {
            $keywords = $campaigns[$c_intex]['keywords'];
            $keyword_chunks = array_chunk($keywords, 50);

            $keywords_found = [];

            foreach( $keyword_chunks as $keywords) {
              $diff_keywords = array_diff($keywords, $keyword_bank);

              if (empty($diff_keywords)) continue;
              $merge = implode('|', $diff_keywords);
              $pattern = "/\b(\s?{$merge}\s?)\b/i";

              $matches = [];
              preg_match_all($pattern, $content, $matches, PREG_PATTERN_ORDER);
              if( isset( $matches[0]) and is_array($matches[0]) ) {
                $keywords_found = array_unique($matches[0]);

                if (count($keywords_found) > 0) {
                  break;
                }
              }
              $keyword_bank = array_merge($keyword_bank, $diff_keywords);

            }

          $exclusion_list_id = $campaigns[$c_intex]['exclusion_list_id'];
            //            $keywords_found = [];
            //            foreach( $keywords as $keyword) {
            //                $keyword_found = preg_match("/\b(\s?{$keyword}\s?)\b/", $content);
            //                if ($keyword_found) {
            //                    array_push($keywords_found, $keyword);
            //                }
            //            }

            array_push($keywords_result, [
                'exclusion_list_id' => $exclusion_list_id,
                'keywords' => $keywords_found,
                'found' => count($keywords_found) > 0
            ]);
        }

        return $keywords_result;
    }

    private static function posts_pages_content_format($posts)
    {
        return array_map(function ($post_id) {
          return Safetag_Admin::get_post_data_formatted($post_id);
        }, $posts);
    }

    private static function tag_category_values($tag_category)
    {
        return  "$tag_category->name $tag_category->description";
    }

    private static function get_campaigns_keywords($campaign_ids)
    {
        if (count($campaign_ids) > 0) {
            $data = Safetag_Db_Management::get_exclusion_list_keywords_exclution_list_id($campaign_ids);
            return array_map(function ($value) {

                $keywordsList =  array_map(function ($keyword_string) {
                    return trim($keyword_string);
                }, explode("\r\n", trim($value->keywords)));

                return [
                    "exclusion_list_id" => $value->exclusion_list_id,
                    "keywords" => array_values(array_unique($keywordsList))
                ];
            }, $data);
        }
        return [];
    }

    private static function safetag_logs($start, $posts, $campaign, $rowAffected, $type = "keyword-cron-job")
    {
        $startDateTime = new DateTime($start);
        $end = date('Y-m-d H:i:s');
        $endDateTime = new DateTime($end);
        $diffInSeconds = $endDateTime->getTimestamp() - $startDateTime->getTimestamp();
        $log = array(
            "start" => $start,
            "end" => $end,
            "totalSeconds" => $diffInSeconds,
            "posts" => $posts,
            "campaign" => $campaign,
            "rowAffected" => $rowAffected,
            "type" => $type
        );

        Safetag_Log::add($log);

        //error_log(json_encode($log));
    }
}
