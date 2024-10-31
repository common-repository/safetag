<?php

if (!class_exists('WP_List_Table')) {
  require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}
class DisplayCampaingList extends WP_List_Table {
    public $license_status;
    public $latest_item = [];
    public $total_count = 0;

    function __construct() {
        parent::__construct([
          'singular' => 'Campaing',
          'plural' => 'Campaings',
          'ajax' => false
        ]);
        $status = Safetag_Page_Helper::get_option_data_single('safetag_license_key', 'status');
        $this->license_status = $status;
        // $this->get_all_camp_key_count();
      }

    function get_columns(){
      $columns = array(
        'name' => 'Campaign Name',
        'type'    => 'Type',
        'created_at'      => 'Created',
        'active'    => 'Status',
        'posts'     => 'Posts',
        'actions'  => "Action"
      );
      return $columns;
      
    }

    protected function column_default($item, $column_name) {
      $campaign_type = Safetag_Cron_Job::$campaign_type;
      switch ($column_name) {
        case 'name' :
          return $item[$column_name];
          break;
          case 'type' :
            return $campaign_type[$item['type']]['name'];
            break;
        case 'created_at':
          return date('m-d-Y | g:i a', strtotime($item['created_at']));
          break;
        case 'active' :
          return $item['active'] ? 'Active' : 'Inactive';
          break;
        case  'posts' :
          return 0;
          break;
        default :
          return isset($item->column_name) ? $item->column_name : '';
      }
    }

    public function column_posts($item){
      $item_id = $item['id'];
      $svg = Safetag_Page_Helper::get_camp_list_svg();
      return '<span class=camp_span_class id=camp_'.$item_id.' data-campid='.$item_id.'>'.$svg.'</span>';
    }

    public function column_actions($item){
      $page = sanitize_text_field($_GET['page']);
      $edit = Safetag_Admin::generate_url([ "setting_seccion" => "exclusion-list-edit", "pid" => $item['id']], $page);
      $delete = Safetag_Admin::generate_url(["setting_seccion" => "exclusion-list-delete", "pid" => $item['id']], $page);
      $export = Safetag_Admin::generate_url(["setting_seccion" => "exclusion-list-export", "pid" => $item['id']], $page);
      $report = Safetag_Admin::generate_url(["setting_seccion" => "exclusion-list-report", "pid" => $item['id'],   "slice_number" => 0], $page);
      
      // user can edit only one camp when license are free or expire
      $action = '<a href="' . $edit . '">EDIT</a> | <a href="'.$delete .'">DELETE</a> | <a href="' . $export . '">EXPORT</a> | <a href="'. $report .'">REPORT</a>';
      if($this->license_status !== 'active' && $this->total_count > 1) {
        if($this->latest_item[0]['id'] != $item['id']) {
          $action = '<a href="'.$delete .'">DELETE</a> | <a href="' . $export . '">EXPORT</a> | <a href="'. $report .'">REPORT</a>';
        }
      }
      return $action;

    }
    public function prepare_items() {
      $column = $this->get_columns();
      $hidden = [];
      $sortable = $this->get_sortable_columns();
      $par_page = 20;
      $current_page = $this->get_pagenum();
      $offset = ($current_page -1) * $par_page;
      $this->_column_headers = [$column, $hidden, $sortable];
      $args = [
        'per_page' => $par_page,
        'offset' => $offset,
      ];
      if(isset($_REQUEST['orderby']) && isset($_REQUEST['order'])){
        $args['orderby'] = sanitize_sql_orderby( $_REQUEST['orderby']);
        $args['order'] = sanitize_text_field( $_REQUEST['order']);
      }
      // $res = Safetag_Db_Management::get_exclusion_list_records($offset,$par_page,"'name', 'active', 'created_at'",'ASC');
      $this->items = $this->get_items($args);

      $this->set_pagination_args([
        'total_items' => $this->total_count(),
        'per_page' => $par_page,
      ]);
    }

    private function total_count(){
      global $wpdb;
      $this->total_count = (int) $wpdb->get_var("SELECT count(id) FROM {$wpdb->prefix}" . SAFETAG_EXCLUSION_LIST_TABLE );
      return $this->total_count;
    }
    private function get_items($args = []){
      global $wpdb;
      $table_name = $wpdb->prefix . SAFETAG_EXCLUSION_LIST_TABLE;
      $defaults = [
        'per_page' => 20,
        'offset'   => 0,
        'orderby'  => 'name',
        'order'    => 'ASC'
      ];
      $args = wp_parse_args($args, $defaults);

      $sql = "SELECT *
              FROM $table_name
              ORDER BY {$args['orderby']} {$args['order']}
              LIMIT %d OFFSET %d";

      $sql = $wpdb->prepare($sql, $args['per_page'], $args['offset']);
      $result = $wpdb->get_results($sql, ARRAY_A);
      if(!empty($result) && count($result) > 1) $this->latest_item = Safetag_Db_Management::get_latest_campaing();
      return $result;
    }

    public static function get_all_camp_key_count(){
      $post_count = Safetag_Db_Management::get_exclusion_list_post_count();
      $count_result = [];
      if( !empty( $post_count['result'] ) ){
        foreach ($post_count['result']  as $value) {
          $count_result[$value->campaign_id] = Safetag_Page_Helper::get_number_format($value->count);
        }
      }
      echo json_encode($count_result);
      wp_die();
    }
}
