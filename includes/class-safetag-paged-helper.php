<?php

/**
 *
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    safetag
 * @subpackage safetag/includes
 */

/**
 *
 *
 * description.
 *
 * @since      1.0.0
 * @package    safetag
 * @subpackage safetag/includes
 * @author     Your Name <email@example.com>
 */
class Safetag_Page_Helper
{
    public static $last_cron_run_diff_time = 0;

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function paged_generator($getDataPagination, $columns, $items_per_page, $params = array())
    {
        // pagination properties.
        $page = isset($_GET['adgpaged']) ? abs((int) sanitize_text_field($_GET['adgpaged'])) : 1;
        $offset = ($page * $items_per_page) - $items_per_page;

        // Order column properties
        $column = isset($_GET['column']) && in_array($_GET['column'], $columns) ? sanitize_text_field($_GET['column']) : sanitize_text_field($columns[0]);
        $sort_order = isset($_GET['order']) && strtolower($_GET['order']) == 'asc' ? 'ASC' : 'DESC';

        // Order column properties inverse
        $up_or_down = str_replace(array('ASC', 'DESC'), array('up', 'down'), $sort_order);
        $asc_or_desc = $sort_order == 'DESC' ? 'asc' : 'desc';
        $add_class = ' class="highlight"';

        //var_dump($params);
        $function_parameters = array($offset, $items_per_page, $column, $sort_order);
        $function_parameters = array_merge($function_parameters, $params);
        $data = call_user_func_array($getDataPagination, $function_parameters);
        //$data = $getDataPagination($offset, $items_per_page, $column, $sort_order, ...$params);

        $prev_arrow = is_rtl() ? '→' : '←';
        $next_arrow = is_rtl() ? '←' : '→';
        $pagination = paginate_links(array(
            'base' => add_query_arg('adgpaged', '%#%'),
            'format' => '',
            'total' => ceil($data['total'] / $items_per_page),
            'current' => $page,
            'type'             => 'list',
            'prev_text'        => $prev_arrow,
            'next_text'        => $next_arrow,
        ));

        return array(
            "total" => $data['total'],
            "result" => $data['result'],
            "paged-link" => $pagination
        );
    }

    public static function get_iab_tags_resources() {

        if ( false === ( $iab_tags = get_transient( 'iab_tags_resources' ) ) ) {
            $json_path = plugin_dir_path(dirname(__FILE__)) . 'admin/js/iab-tags-resources.json';
            $iab_tags = json_decode(file_get_contents($json_path), true);
            set_transient( 'iab_tags_resources', $iab_tags, 10 * MINUTE_IN_SECONDS );
        }
        return $iab_tags;
    }

    public static function get_iab_audience_tags() {

        if ( false === ( $iab_audiences = get_transient( 'iab_audience_tags' ) ) ) {
            $json_path = plugin_dir_path(dirname(__FILE__)) . 'admin/js/iab-audience-tags.json';
            $iab_audiences = json_decode(file_get_contents($json_path), true);
            set_transient( 'iab_audience_tags', $iab_audiences, 10 * MINUTE_IN_SECONDS );
        }
        return $iab_audiences;
    }

    public static function get_number_format(int $value)
    {
        return number_format((float)$value, 0, '.', ',');
    }

    public static function get_number_percentage(int $value, int $total)
    {
        return round(($value / $total) * 100, 2);
    }

    public static function get_chart_color_var($type, $campaign_post_report, $total_post)
    {
      $color_array = $type == 'Include' ? ['#BEE1F0', '#67CC7D', '#333333'] : ['#67CC7D', '#E94B47', '#333333'];
      list($first, $secound, $third) = $color_array;
      $safe_end = self::get_number_percentage($campaign_post_report['safe'], $total_post);
      $excluded_end = self::get_number_percentage($campaign_post_report['excluded'], $total_post);
      $update_required_end = self::get_number_percentage($campaign_post_report['update_required'], $total_post);

      $last_end = 100 - $update_required_end;
      $color_array['safe_style'] = "$first 0 $safe_end%";
      $color_array['safe_bg'] = "$first";
      $color_array['excluded_style'] = !empty($excluded_end) ? ", $secound $safe_end% $last_end%" : '';
      $color_array['excluded_bg'] = "$secound";
      $color_array['update_required_style'] = !empty($update_required_end) ? ", $third $last_end% 100%" : '';
      $color_array['update_bg'] = "$third";

      return $color_array;
    }

    public static  function get_license_expire_due_time()
    {
      $date_expiry = self::get_option_data_single('safetag_license_key', 'date_expiry');
      if($date_expiry) {
        $now = time(); 
        $your_date = strtotime($date_expiry);
        $datediff = $your_date - $now;
        return round($datediff / (60 * 60 * 24)); // return days
      }
      return false;
    }

    public static function get_option_data_single($option_key, $type='')
    {
      $option_data = get_option($option_key);
      $decode_data = json_decode($option_data);
      if(empty($type)) return $option_data;
      return isset($decode_data->$type) ? $decode_data->$type : '';
    }

    public static function get_camp_list_svg() 
    {
        return '<div class="loader loader--style8" title="7">
        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
           width="18px" height="20px" viewBox="0 0 18 20" style="enable-background:new 0 0 50 50;" xml:space="preserve">
          <rect x="0" y="10" width="3" height="5" fill="#333" opacity="0.2">
            <animate attributeName="opacity" attributeType="XML" values="0.2; 1; .2" begin="0s" dur="0.6s" repeatCount="indefinite" />
            <animate attributeName="height" attributeType="XML" values="10; 20; 10" begin="0s" dur="0.6s" repeatCount="indefinite" />
            <animate attributeName="y" attributeType="XML" values="10; 5; 10" begin="0s" dur="0.6s" repeatCount="indefinite" />
          </rect>
          <rect x="8" y="10" width="3" height="5" fill="#333"  opacity="0.2">
            <animate attributeName="opacity" attributeType="XML" values="0.2; 1; .2" begin="0.15s" dur="0.6s" repeatCount="indefinite" />
            <animate attributeName="height" attributeType="XML" values="10; 20; 10" begin="0.15s" dur="0.6s" repeatCount="indefinite" />
            <animate attributeName="y" attributeType="XML" values="10; 5; 10" begin="0.15s" dur="0.6s" repeatCount="indefinite" />
          </rect>
          <rect x="16" y="10" width="3" height="5" fill="#333"  opacity="0.2">
            <animate attributeName="opacity" attributeType="XML" values="0.2; 1; .2" begin="0.3s" dur="0.6s" repeatCount="indefinite" />
            <animate attributeName="height" attributeType="XML" values="10; 20; 10" begin="0.3s" dur="0.6s" repeatCount="indefinite" />
            <animate attributeName="y" attributeType="XML" values="10; 5; 10" begin="0.3s" dur="0.6s" repeatCount="indefinite" />
          </rect>
        </svg>
      </div>';
    }

    /**
     * Issue: there was no track, if safetag cron are not running
     * Solve: set time if last cron run, 
     * and check if cron last run time more then 15 min, 
     * then show warning message in setting screen.
    */
    public static function last_cron_run_add_or_update_time()
    {
      list($option_name, $last_cron_run) = self::last_cron_run_get_option();
      $current_time = time();

      if($last_cron_run) {        
        update_option($option_name, $current_time);
      } else {
        add_option($option_name, $current_time);        
      }
    }

    private static function last_cron_run_get_option()
    {
      $option_name = 'safetag_last_cron_run_time';
      $last_cron_run = get_option($option_name);
      $default_time = 60; // 60 minutes / 1 Hour
      return [$option_name, $last_cron_run, $default_time];
    }

    public static function last_cron_run_get_time_spent() 
    {      
      self::last_cron_run_insert_only_first_time();
      list($option_name, $last_cron_run, $default_time) = self::last_cron_run_get_option();

      $diff_minutes = (int) round(abs($last_cron_run - time()) / 60); // diff last run cron time to current time
      self::$last_cron_run_diff_time = $diff_minutes;

      return ($diff_minutes > $default_time) ? false : true;
    }

    /**
     * Issue&Solve: in existing project safetag are already activated, 
     * so need to add option on first time
     *  
    */
    private static function last_cron_run_insert_only_first_time()
    {
      list($option_name, $last_cron_run) = self::last_cron_run_get_option();
      if($last_cron_run == false) add_option($option_name, time());
    }
}
