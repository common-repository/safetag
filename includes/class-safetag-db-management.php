<?php

/**
 * Safetag Data Base management.
 *
 * @since      1.0.0
 * @package    safetag
 * @subpackage safetag/includes
 * @author     Your Name <email@example.com>
 */
class Safetag_Db_Management
{

    /**
     * Get all safetag_ads_txt recods with pagination.
     *
     * @since    1.0.0
     */
    public static function get_ads_text_records($offset, $items_per_page, $column, $sort_order)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . SAFETAG_ADSTXT_TABLE;

        $total_query = "SELECT COUNT(1) FROM $table_name";

        $total = $wpdb->get_var($total_query);

        $sql = "SELECT *
                FROM $table_name
                ORDER BY active DESC, $column $sort_order
                LIMIT %d OFFSET %d";

        $sql = $wpdb->prepare($sql, $items_per_page, $offset);

        return array("total" => $total, "result" => $wpdb->get_results($sql, OBJECT));
    }

    /**
     * Get a safetag_ads_txt record by id and active opcion.
     *
     * @since    1.0.0
     */
    public static function get_ads_text_record_by_id($id, $active = true)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . SAFETAG_ADSTXT_TABLE;

        $active_number = $active ? 1 : 0;

        $sql = "SELECT *
                FROM $table_name
                WHERE id = %s AND active = $active_number";

        $sql = $wpdb->prepare($sql, $id);

        return $wpdb->get_row($sql, OBJECT);
    }

    /**
     * Insert a new safetag_ads_txt record.
     *
     * @since    1.0.0
     */
    public static function insert_ads_txt_record($ads_txt_record)
    {
        global $wpdb;

        $wpdb->query("START TRANSACTION");
        $table_name = $wpdb->prefix . SAFETAG_ADSTXT_TABLE;

        $wpdb->update($table_name, array('active' => false), array('active' => true));

        $result = $wpdb->insert(
            $table_name,
            $ads_txt_record
        );

        if ($result) {
          $wpdb->query("COMMIT");
        } else {
          $wpdb->query("ROLLBACK");
        }

    }

    /**
     * Restore a safetag_ads_txt record.
     *
     * @since    1.0.0
     */
    public static function restore_ads_txt_record($ads_txt_record_id)
    {
        global $wpdb;

        $wpdb->query("START TRANSACTION");
        $table_name = $wpdb->prefix . SAFETAG_ADSTXT_TABLE;

        $wpdb->update($table_name, array('active' => false), array('active' => true));

        $result = $wpdb->update($table_name, array('active' => true), array('id' => $ads_txt_record_id));

        if ($result) {
          $wpdb->query("COMMIT");
        } else {
          $wpdb->query("ROLLBACK");
        }
    }

    /**
     * Update safetag_ads_txt records by filter.
     *
     * @since    1.0.0
     */
    public static function update_column_ads_txt_record($columnsUpdate, $filter)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . SAFETAG_ADSTXT_TABLE;

        return $wpdb->update($table_name, $columnsUpdate, $filter);
    }

    /**
     * Insert a new exclusion_list record.
     *
     * @since    1.0.0
     */
    public static function insert_exclusion_list_record($exclusion_list_record)
    {
      global $wpdb;

      $wpdb->query("START TRANSACTION");
      $table_name = $wpdb->prefix . SAFETAG_EXCLUSION_LIST_TABLE;

      // Check if the 'name' already exists in the table
      $existing_record = $wpdb->get_var(
        $wpdb->prepare(
          "SELECT COUNT(*) FROM $table_name WHERE name = %s",
          $exclusion_list_record['name']
        )
      );

      // If the name already exists, return false
      if ($existing_record > 0) {
        return false;
      }

      $result = $wpdb->insert(
        $table_name,
        $exclusion_list_record
      );

      if ($result) {
        $wpdb->query("COMMIT");
      } else {
        $wpdb->query("ROLLBACK");
      }

      return $wpdb->insert_id;
    }

    /**
     * Update an exclusion_list record.
     *
     * @since    1.0.0
     */
    public static function update_exclusion_list_record($columnsUpdate, $filter)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . SAFETAG_EXCLUSION_LIST_TABLE;

        return $wpdb->update($table_name, $columnsUpdate, $filter);
    }

    /**
     * Insert an exclusion_list_history record.
     *
     * @since    1.0.0
     */
    public static function insert_exclusion_list_history_record($exclusion_list_history, $exclusion_list_id)
    {
        global $wpdb;

        $wpdb->query("START TRANSACTION");
        $table_name = $wpdb->prefix . SAFETAG_EXCLUSION_LIST_HISTORY_TABLE;

        $wpdb->update($table_name, array('active' => false), array('active' => true, 'exclusion_list_id' => $exclusion_list_id));

        $result = $wpdb->insert(
            $table_name,
            $exclusion_list_history
        );

        if ($result) {
          $wpdb->query("COMMIT");
        } else {
          $wpdb->query("ROLLBACK");
        }
    }

    /**
     * Get all exclusion_list records by pagination filter.
     *
     * @since    1.0.0
     */
    public static function get_exclusion_list_records($offset, $items_per_page, $column, $sort_order)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . SAFETAG_EXCLUSION_LIST_TABLE;

        $total_query = "SELECT COUNT(1) FROM $table_name";

        $total = $wpdb->get_var($total_query);

        $sql = "SELECT *
                FROM $table_name
                ORDER BY created_at DESC, $column $sort_order
                LIMIT %d OFFSET %d";

        $sql = $wpdb->prepare($sql, $items_per_page, $offset);

        return array("total" => $total, "result" => $wpdb->get_results($sql, OBJECT));
    }

    /**
     * Get an exclusion_list record by id.
     *
     * @since    1.0.0
     */
    public static function get_exclusion_list_by_id($id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . SAFETAG_EXCLUSION_LIST_TABLE;

        $sql = "SELECT *
                FROM $table_name
                WHERE id = %s";

        $sql = $wpdb->prepare($sql, $id);

        return $wpdb->get_row($sql, OBJECT);
    }

    /**
     * Get exclusion_list history records by id.
     *
     * @since    1.0.0
     */
    public static function get_exclusion_list_history_by_camp_id($exclusion_list_id)
    {
        global $wpdb;
        $SAFETAG_EXCLUSION_LIST_TABLE = $wpdb->prefix . SAFETAG_EXCLUSION_LIST_TABLE;
        $SAFETAG_EXCLUSION_LIST_HISTORY_TABLE = $wpdb->prefix . SAFETAG_EXCLUSION_LIST_HISTORY_TABLE;

        $sql = "SELECT $SAFETAG_EXCLUSION_LIST_TABLE.id, name, type, keywords
            FROM $SAFETAG_EXCLUSION_LIST_TABLE
            INNER JOIN $SAFETAG_EXCLUSION_LIST_HISTORY_TABLE ON $SAFETAG_EXCLUSION_LIST_TABLE.id = $SAFETAG_EXCLUSION_LIST_HISTORY_TABLE.exclusion_list_id AND $SAFETAG_EXCLUSION_LIST_HISTORY_TABLE.active = 1
            WHERE $SAFETAG_EXCLUSION_LIST_TABLE.id = %s";

        $sql = $wpdb->prepare($sql, $exclusion_list_id);

        return $wpdb->get_row($sql, OBJECT);
    }

    /**
     * Get exclusion_list history records by id.
     *
     * @since    1.0.0
     */
    public static function get_exclusion_list_history_by_exclution_list_id($exclusion_list_id, $active = true)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . SAFETAG_EXCLUSION_LIST_HISTORY_TABLE;

        $active = $active ? 1 : 0;

        $sql = "SELECT *
                FROM $table_name
                WHERE exclusion_list_id = %s AND active = $active";

        $sql = $wpdb->prepare($sql, $exclusion_list_id);

        return $wpdb->get_row($sql, OBJECT);
    }

    /**
     * Get keywords records by exclusion ids
     *
     * @since    1.0.0
     */
    public static function get_exclusion_list_keywords_exclution_list_id($ids, $active = true)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . SAFETAG_EXCLUSION_LIST_HISTORY_TABLE;

        $active = $active ? 1 : 0;

        $ids_format_string = rtrim(str_repeat('%d,', count($ids)), ',');

        $sql = "SELECT exclusion_list_id, keywords
                FROM $table_name
                WHERE active = $active AND exclusion_list_id  IN ( $ids_format_string )";

        $sql = $wpdb->prepare($sql, $ids);

        return $wpdb->get_results($sql, OBJECT);
    }

    /**
     * Get all exclusion_list records actives.
     *
     * @since    1.0.0
     */
    public static function get_exclusion_list_records_active()
    {
        global $wpdb;
        $SAFETAG_EXCLUSION_LIST_TABLE = $wpdb->prefix . SAFETAG_EXCLUSION_LIST_TABLE;
        $SAFETAG_EXCLUSION_LIST_HISTORY_TABLE = $wpdb->prefix . SAFETAG_EXCLUSION_LIST_HISTORY_TABLE;

        $sql = "SELECT $SAFETAG_EXCLUSION_LIST_TABLE.id, name, type, keywords
            FROM $SAFETAG_EXCLUSION_LIST_TABLE
            INNER JOIN $SAFETAG_EXCLUSION_LIST_HISTORY_TABLE ON $SAFETAG_EXCLUSION_LIST_TABLE.id = $SAFETAG_EXCLUSION_LIST_HISTORY_TABLE.exclusion_list_id
            WHERE $SAFETAG_EXCLUSION_LIST_TABLE.active = %s AND $SAFETAG_EXCLUSION_LIST_HISTORY_TABLE.active = %s";

        $sql = $wpdb->prepare($sql, 1, 1);

        return $wpdb->get_results($sql, OBJECT);
    }

    /**
     * Get exclusion_list_history by id.
     *
     * @since    1.0.0
     */
    public static function get_exclusion_list_history_by_id($id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . SAFETAG_EXCLUSION_LIST_HISTORY_TABLE;

        $sql = "SELECT *
                FROM $table_name
                WHERE id = %s";

        $sql = $wpdb->prepare($sql, $id);

        return $wpdb->get_row($sql, OBJECT);
    }

    /**
     * Get exclusion_list_history records by id and pagination filter.
     *
     * @since    1.0.0
     */
    public static function get_exclusion_list_history_records_by_exclusion_list_id($offset, $items_per_page, $column, $sort_order, $exclusion_list_id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . SAFETAG_EXCLUSION_LIST_HISTORY_TABLE;

        $total_query = "SELECT COUNT(1) FROM $table_name";

        $total = $wpdb->get_var($total_query);

        $sql = "SELECT *
                FROM $table_name
                WHERE exclusion_list_id = %s AND  active = 0
                ORDER BY active DESC, $column $sort_order
                LIMIT %d OFFSET %d";

        $sql = $wpdb->prepare($sql, $exclusion_list_id, $items_per_page, $offset);

        return array("total" => $total, "result" => $wpdb->get_results($sql, OBJECT));
    }

    /**
     * Restore an exclusion_list_history record.
     *
     * @since    1.0.0
     */
    public static function restore_exclusion_list_history_record($exclusion_list_history_id)
    {
        global $wpdb;

        $exclusion_list_history =  self::get_exclusion_list_history_by_id($exclusion_list_history_id);

        $wpdb->query("START TRANSACTION");
        $table_name = $wpdb->prefix . SAFETAG_EXCLUSION_LIST_HISTORY_TABLE;

        $wpdb->update($table_name, array('active' => false), array('active' => true, 'exclusion_list_id' => $exclusion_list_history->exclusion_list_id));

        $result = $wpdb->update($table_name, array('active' => true), array('id' => $exclusion_list_history_id));

        if ($result) {
          $wpdb->query("COMMIT");
        } else {
          $wpdb->query("ROLLBACK");
        }
    }

    /**
     * Get post_campaign record by campaign id and post id.
     *
     * @since    1.0.0
     */
    public static function get_post_campain_by_campaign_id_post_id($campaign_id, $post_id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . SAFETAG_POST_CAMPAIGN_KEYWORDS_TABLE;

        $sql = "SELECT *
                FROM $table_name
                WHERE campaign_id = %s AND post_id = %s";

        $sql = $wpdb->prepare($sql, $campaign_id, $post_id);

        return $wpdb->get_row($sql, OBJECT);
    }

    /**
     * Get post_campaign record by campaign id.
     *
     * @since    1.0.0
     */
    public static function get_post_campain_by_campaign_id($campaign_id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . SAFETAG_POST_CAMPAIGN_KEYWORDS_TABLE;

        $sql = "SELECT post_id, keywords
                FROM $table_name
                WHERE campaign_id = %s
                ORDER by post_id ASC";

        $sql = $wpdb->prepare($sql, $campaign_id);

        return $wpdb->get_results($sql, OBJECT);
    }

    /**
     * Insert a new post campaign record.
     *
     * @since    1.0.0
     */
    public static function insert_post_campain_by_campaign_record($post_campaign)
    {
        global $wpdb;

        $wpdb->query("START TRANSACTION");
        $table_name = $wpdb->prefix . SAFETAG_POST_CAMPAIGN_KEYWORDS_TABLE;

        $result = $wpdb->insert(
            $table_name,
            $post_campaign
        );

        if ($result) {
          $wpdb->query("COMMIT");
        } else {
          $wpdb->query("ROLLBACK");
        }
    }

    /**
     * Update a post campaign record.
     *
     * @since    1.0.0
     */
    public static function update_post_campaign_by_campaign_record($columnsUpdate, $filter)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . SAFETAG_POST_CAMPAIGN_KEYWORDS_TABLE;

        return $wpdb->update($table_name, $columnsUpdate, $filter);
    }

    /**
     * Get all post campaign record by update required column
     *
     * @since    1.0.0
     */
    public static function get_posts_campaigns_records()
    {
        global $wpdb;
        $SAFETAG_EXCLUSION_LIST_TABLE = $wpdb->prefix . SAFETAG_EXCLUSION_LIST_TABLE;
        $table_name = $wpdb->prefix . SAFETAG_POST_CAMPAIGN_KEYWORDS_TABLE;
        $free_version_only_one_camp_id = self::check_free_version_limit_camp_scan();

        $sql = "SELECT $table_name.id, campaign_id, post_id
                FROM $table_name
                INNER JOIN $SAFETAG_EXCLUSION_LIST_TABLE ON $SAFETAG_EXCLUSION_LIST_TABLE.id = $table_name.campaign_id
                WHERE $SAFETAG_EXCLUSION_LIST_TABLE.active = 1 AND update_required = %s $free_version_only_one_camp_id
                ORDER by post_id DESC
                LIMIT 5000"; // TODO: Remove this limit

        $sql = $wpdb->prepare($sql, 1);

        return $wpdb->get_results($sql, OBJECT);
    }

    /**
     * check if license are free, if free then only get one latest campaign
     */
    private static function check_free_version_limit_camp_scan()
    {
      $free_version_only_one_camp_id = '';
        $license_status = Safetag_Page_Helper::get_option_data_single('safetag_license_key', 'status');
        if($license_status !== 'active' && self::total_count() > 1) {
            $latest_item = Safetag_Db_Management::get_latest_campaing();
            $free_version_only_one_camp_id = 'AND campaign_id = '.$latest_item[0]['id'];
        }
      return $free_version_only_one_camp_id;
    }

    /**
     * get total campaign count
     */
    private static function total_count(){
      global $wpdb;
      return (int) $wpdb->get_var("SELECT count(id) FROM {$wpdb->prefix}" . SAFETAG_EXCLUSION_LIST_TABLE );
    }

  /**
   * insert post id into `wp_safetag_post_campaign_keywords` table with campaign_id wise --- single
   *
   * @since    1.0.0
   */
  public static function insert_safetag_records_campain_wise_single($campaign_id, $post_types = [])
  {
    global $wpdb;
    $table_name = $wpdb->prefix . SAFETAG_POST_CAMPAIGN_KEYWORDS_TABLE;

    if(empty($post_types)) {
      $post_types = json_decode(get_option('safetag_post_types'));
      $post_types = (array) $post_types ?? [];
    }

    $keywords_imploded = implode("','", $post_types);

    $sql = "INSERT INTO $table_name (campaign_id, post_id, update_required)
                SELECT %d, id, 1 FROM $wpdb->posts
                WHERE post_status = 'publish'
                AND
                post_type IN ('" . $keywords_imploded . "')";

    $sql = $wpdb->prepare($sql, $campaign_id);

    return $wpdb->query($sql);
  }

    /**
   * insert post id into `wp_safetag_post_campaign_keywords` table with campaign_id wise --- single
   *
   * @since    1.0.0
   */
  public static function delete_safetag_records_campaign_post_types_wise( $post_types = [] )
  {
    global $wpdb;
    $table_name = $wpdb->prefix . SAFETAG_POST_CAMPAIGN_KEYWORDS_TABLE;

    $keywords_imploded = implode("','", $post_types);

    $sql = "DELETE $table_name
            FROM  $table_name JOIN $wpdb->posts ON
            $table_name.post_id = $wpdb->posts.ID
            WHERE post_type IN ('" . $keywords_imploded . "')";

    $sql = $wpdb->prepare($sql);

    return $wpdb->query($sql);
  }

    /**
     * Delete a post campaign record
     *
     * @since    1.0.0
     */
    public static function delete_posts_campaigns_not_in_ids($post_id, $ids)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . SAFETAG_POST_CAMPAIGN_KEYWORDS_TABLE;

        $ids_format_string = rtrim(str_repeat('%d,', count($ids)), ',');

        $sql = "DELETE FROM $table_name
            WHERE post_id = $post_id AND campaign_id IN ($ids_format_string)";

        $sql = $wpdb->prepare($sql, $ids);
        return $wpdb->query($sql);
    }

    /**
	 * delete_post_from_campaigns_meta_data
	 *
     * @since    2.0.4
	 */
	public static function delete_post_from_campaign_keywords_by_post_id($post_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . SAFETAG_POST_CAMPAIGN_KEYWORDS_TABLE;

        $sql = "DELETE FROM $table_name
            WHERE post_id = $post_id";

        return $wpdb->query($sql);
	}

    /**
     * Get all post id where post type is page or post.
     *
     * @since    1.0.0
     */
    public static function get_all_page_post_ids($post_types = [])
    {
        global $wpdb;
        if(empty($post_types)) {
          $post_types = json_decode(get_option('safetag_post_types'));
          $post_types = (array) $post_types ?? [];
        }

        $keywords_imploded = implode("','", $post_types);
        $page_ids = wp_cache_get('safetag_all_page_post_ids', 'safetag_posts');
        if (!is_array($page_ids)) {
            $page_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE post_status = 'publish'  AND post_type in ('" . $keywords_imploded . "')");
            wp_cache_add('safetag_all_page_post_ids', $page_ids, 'safetag_posts');
        }

        return $page_ids;
    }

    /**
     * Get all kewords results by post id.
     *
     * @since    1.0.0
     */
    public static function get_keywords_result_by_post_id($post_id)
    {
        global $wpdb;
        $SAFETAG_EXCLUSION_LIST_TABLE = $wpdb->prefix . SAFETAG_EXCLUSION_LIST_TABLE;
        $SAFETAG_POST_CAMPAIGN_KEYWORDS_TABLE = $wpdb->prefix . SAFETAG_POST_CAMPAIGN_KEYWORDS_TABLE;

        $sql = "SELECT $SAFETAG_EXCLUSION_LIST_TABLE.id, $SAFETAG_EXCLUSION_LIST_TABLE.name,$SAFETAG_EXCLUSION_LIST_TABLE.type, $SAFETAG_POST_CAMPAIGN_KEYWORDS_TABLE.keywords
            FROM $SAFETAG_POST_CAMPAIGN_KEYWORDS_TABLE
            INNER JOIN $SAFETAG_EXCLUSION_LIST_TABLE ON $SAFETAG_EXCLUSION_LIST_TABLE.id = $SAFETAG_POST_CAMPAIGN_KEYWORDS_TABLE.campaign_id
            WHERE post_id = %d AND update_required = 0 AND keywords is NOT NULL AND $SAFETAG_EXCLUSION_LIST_TABLE.active = 1";

        $sql = $wpdb->prepare($sql, $post_id);

        return $wpdb->get_results($sql, OBJECT);
    }

    /**
     * check if post campaign require update
     *
     * @since    1.0.0
     */
    public static function is_pending_keywords_result_by_post_id($post_id)
    {
        global $wpdb;
        $SAFETAG_EXCLUSION_LIST_TABLE = $wpdb->prefix . SAFETAG_EXCLUSION_LIST_TABLE;
        $SAFETAG_POST_CAMPAIGN_KEYWORDS_TABLE = $wpdb->prefix . SAFETAG_POST_CAMPAIGN_KEYWORDS_TABLE;

        $sql = "SELECT $SAFETAG_EXCLUSION_LIST_TABLE.id, $SAFETAG_EXCLUSION_LIST_TABLE.name
            FROM $SAFETAG_POST_CAMPAIGN_KEYWORDS_TABLE
            INNER JOIN $SAFETAG_EXCLUSION_LIST_TABLE ON $SAFETAG_EXCLUSION_LIST_TABLE.id = $SAFETAG_POST_CAMPAIGN_KEYWORDS_TABLE.campaign_id
            WHERE post_id = %d AND update_required = 1 AND keywords is NULL AND $SAFETAG_EXCLUSION_LIST_TABLE.active = 1";

        $sql = $wpdb->prepare($sql, $post_id);

        return $wpdb->get_results($sql, OBJECT);
    }

    /**
     * check if post campaign missing
     *
     * @since    1.0.0
     */
    public static function is_missing_campaign_result_by_post_id($post_id)
    {
        global $wpdb;
        $SAFETAG_EXCLUSION_LIST_TABLE = $wpdb->prefix . SAFETAG_EXCLUSION_LIST_TABLE;
        $SAFETAG_POST_CAMPAIGN_KEYWORDS_TABLE = $wpdb->prefix . SAFETAG_POST_CAMPAIGN_KEYWORDS_TABLE;

        $sql = "SELECT $SAFETAG_EXCLUSION_LIST_TABLE.id, $SAFETAG_EXCLUSION_LIST_TABLE.name
            FROM $SAFETAG_POST_CAMPAIGN_KEYWORDS_TABLE
            INNER JOIN $SAFETAG_EXCLUSION_LIST_TABLE ON $SAFETAG_EXCLUSION_LIST_TABLE.id = $SAFETAG_POST_CAMPAIGN_KEYWORDS_TABLE.campaign_id
            WHERE post_id = %d AND update_required = 0 AND keywords is NULL AND $SAFETAG_EXCLUSION_LIST_TABLE.active = 1";

        $sql = $wpdb->prepare($sql, $post_id);

        return $wpdb->get_results($sql, OBJECT);
    }


    //TODO: Testing function... remove
    public static function get_post_campain_keywords_all($update_require, $limit)
    {
        global $wpdb;
        $SAFETAG_EXCLUSION_LIST_TABLE = $wpdb->prefix . SAFETAG_EXCLUSION_LIST_TABLE;
        $table_name = $wpdb->prefix . SAFETAG_POST_CAMPAIGN_KEYWORDS_TABLE;

        $sql = "SELECT $table_name.id, campaign_id, post_id, update_required, keywords
                FROM $table_name
                INNER JOIN $SAFETAG_EXCLUSION_LIST_TABLE ON $SAFETAG_EXCLUSION_LIST_TABLE.id = $table_name.campaign_id
                WHERE $SAFETAG_EXCLUSION_LIST_TABLE.active = 1 AND update_required = $update_require
                ORDER by post_id ASC
                LIMIT $limit";

        return $wpdb->get_results($sql, OBJECT);
    }

    public static function get_post_campain_keywords_all_count($update_required)
    {
        global $wpdb;
        $SAFETAG_EXCLUSION_LIST_TABLE = $wpdb->prefix . SAFETAG_EXCLUSION_LIST_TABLE;
        $table_name = $wpdb->prefix . SAFETAG_POST_CAMPAIGN_KEYWORDS_TABLE;

        $sql = "SELECT count($table_name.id) as count
                FROM $table_name
                INNER JOIN $SAFETAG_EXCLUSION_LIST_TABLE ON $SAFETAG_EXCLUSION_LIST_TABLE.id = $table_name.campaign_id
                WHERE $SAFETAG_EXCLUSION_LIST_TABLE.active = 1 AND update_required = $update_required";

        return $wpdb->get_row($sql, OBJECT);
    }

    public static function get_post_campain_keywords_all_delete()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . SAFETAG_POST_CAMPAIGN_KEYWORDS_TABLE;

        $sql = "DELETE FROM $table_name";

        return $wpdb->query($sql, OBJECT);
    }

    public static function get_post_by_type($types)
    {
        global $wpdb;
        //$post_types = Safetag_Admin::get_post_type_screem();
        $keywords_imploded = implode("','", $types);
        //$page_ids = wp_cache_get('safetag_all_page_post_ids', 'safetag_posts');

        //if (!is_array($page_ids)) {
        $page_ids = $wpdb->get_row("SELECT count(ID) FROM $wpdb->posts WHERE post_status = 'publish'  AND post_type in ('" . $keywords_imploded . "')");
        //wp_cache_add('safetag_all_page_post_ids', $page_ids, 'safetag_posts');
        //}

        return $page_ids;
    }


    public static function get_post_id_completed($limit)
    {
        global $wpdb;
        $post_types = Safetag_Admin::get_post_type_screem();
        $keywords_imploded = implode("','", $post_types);

        $sql = "SELECT `pc1`.post_id FROM `wp_safetag_post_campaign_keywords` AS `pc1`
                INNER JOIN `wp_safetag_exclusion_list` ON `wp_safetag_exclusion_list`.`id` = `pc1`.`campaign_id`
                INNER JOIN `wp_posts` AS `wpost` ON `wpost`.id = `pc1`.post_id
                WHERE `wp_safetag_exclusion_list`.`active` = 1 AND `wpost`.post_status = 'publish' AND post_type in ('" . $keywords_imploded . "')
                GROUP by `pc1`.post_id
                HAVING `pc1`.post_id NOT IN (
                    SELECT `pc2`.post_id FROM `wp_safetag_post_campaign_keywords` AS `pc2`
                    WHERE `pc2`.post_id = `pc1`.post_id && `pc2`.`update_required` = 1
                )
                ORDER BY `pc1`.`post_id`  ASC
                LIMIT $limit";

        return $wpdb->get_results($sql, OBJECT);
    }

    /**
     * Delete a post campaign record
     *
     * @since    1.0.0
     */
    public static function delete_campaigns_list($pid)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . SAFETAG_POST_CAMPAIGN_KEYWORDS_TABLE;

        $sql = "DELETE FROM $table_name WHERE campaign_id = $pid ";

        $wpdb->query($sql,OBJECT);

        $table_name = $wpdb->prefix . SAFETAG_EXCLUSION_LIST_HISTORY_TABLE;

        $sql = "DELETE FROM $table_name WHERE exclusion_list_id = $pid ";

        $wpdb->query($sql,OBJECT);

        $table_name = $wpdb->prefix . SAFETAG_EXCLUSION_LIST_TABLE;

        $sql = "DELETE FROM $table_name WHERE id = $pid ";

        return $wpdb->query($sql,OBJECT);
    }

    /**
     * Get all exclusion_list records by pagination filter.
     *
     * @since    1.0.0
     */
    public static function get_exclusion_list_post_count()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . SAFETAG_POST_CAMPAIGN_KEYWORDS_TABLE;

        $sql = "select count(*) as count, campaign_id from $table_name where length(keywords) > 0 group by campaign_id";

        return array("result" => $wpdb->get_results($sql, OBJECT));
    }

    /**
     * Get all post count.
     *
     * @since    1.0.0
     */
    public static function get_total_post_count()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'posts';

        $sql = "select count(*) as count from $table_name where post_status = 'publish'";

        return array("result" => $wpdb->get_results($sql, OBJECT));
    }

    /**
     * Get keywords matching stat by alphabetic order.
     *
     * @since    1.0.0
     */
    public static function get_alphabetic_keywords_match_stat()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . SAFETAG_POST_CAMPAIGN_KEYWORDS_TABLE;

        $sql = "select keywords, count(*) as total
        from $table_name
        where keywords IS NOT NULL AND keywords != ''
        group by keywords
        order by keywords
        limit 10";

        return array("result" => $wpdb->get_results($sql, OBJECT));
    }

    /**
     * Get keywords matching stat by campaign id by alphabetic order.
     *
     * @since    1.0.0
     */
    public static function get_alphabetic_keywords_match_stat_bycampaign_id($pid)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . SAFETAG_POST_CAMPAIGN_KEYWORDS_TABLE;

        $sql = "select LOWER(TRIM(keywords)) AS keywords, count(*) as total
        from $table_name
        where keywords IS NOT NULL AND keywords != '' AND campaign_id = $pid
        group by LOWER(TRIM(keywords))
        order by total DESC";

        return array("result" => $wpdb->get_results($sql, OBJECT));
    }

    /**
     * Get keywords matching stat by campaign id by alphabetic order.
     *
     * @since    1.0.0
     */
    public static function get_alphabetic_keywords_match_stat_bycampaign_id_and_keyword($pid, $keyword)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . SAFETAG_POST_CAMPAIGN_KEYWORDS_TABLE;

        $sql = "select count(*) as total
        from $table_name
        where keywords = TRIM('$keyword') AND campaign_id = $pid";

        return array("result" => $wpdb->get_results($sql, OBJECT));
    }

    /**
     * Get keywords matching stat details by keyword.
     *
     * @since    1.0.0
     */
    public static function get_alphabetic_keywords_match_detailsbykeyword($pid, $keyword)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . SAFETAG_POST_CAMPAIGN_KEYWORDS_TABLE;

        $sql = "select $table_name.*, wp_posts.post_date AS publish_date, wp_posts.post_title AS title
            from $table_name
            join wp_posts
            on $table_name.post_id = wp_posts.ID
            where $table_name.keywords = '$keyword' AND $table_name.campaign_id = $pid
            order by wp_posts.post_date DESC;";

        return array("result" => $wpdb->get_results($sql, OBJECT));
    }

    /**
     * Get total keywords matching stat .
     *
     * @since    1.0.0
     */
    public static function get_total_keywords_match_stat_bycampaign_id($pid)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . SAFETAG_POST_CAMPAIGN_KEYWORDS_TABLE;

        $sql = "select count(*) as total
        from $table_name
        where keywords IS NOT NULL AND keywords != '' AND campaign_id = $pid";

        return array("result" => $wpdb->get_results($sql, OBJECT));
    }

    /**
     * Get total distinct keywords matching stat .
     *
     * @since    1.0.0
     */
    public static function get_total_distinct_keywords_match_stat_bycampaign_id($pid)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . SAFETAG_POST_CAMPAIGN_KEYWORDS_TABLE;

        $sql = "select DISTINCT(keywords)
        from $table_name
        where keywords IS NOT NULL AND keywords != '' AND campaign_id = $pid";

        return array("result" => $wpdb->get_results($sql, OBJECT));
    }

    /**
     * Get total safe post keywords matching stat .
     *
     * @since    1.0.0
     */
    public static function get_total_campaign_post_report_bycampaign_id($pid)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . SAFETAG_POST_CAMPAIGN_KEYWORDS_TABLE;

        $safe = "select*
        from $table_name
        where keywords IS NULL AND update_required = 0 AND campaign_id = $pid";
        $excluded = "select*
        from $table_name
        where keywords IS NOT NULL AND update_required = 0 AND campaign_id = $pid";
        $update_required = "select*
        from $table_name
        where update_required = 1 AND campaign_id = $pid";

        return array(
            "safe" => count($wpdb->get_results($safe, OBJECT)),
            "excluded" => count($wpdb->get_results($excluded, OBJECT)),
            "update_required" => count($wpdb->get_results($update_required, OBJECT))
        );
    }

    /**
     * Get keywords matching stat by content order.
     *
     * @since    1.0.0
     */
    public static function get_content_keywords_match_stat()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . SAFETAG_POST_CAMPAIGN_KEYWORDS_TABLE;

        $sql = "select keywords, count(*) as total
        from $table_name
        where keywords IS NOT NULL AND keywords != ''
        group by keywords
        order by total DESC
        limit 10";

        return array("result" => $wpdb->get_results($sql, OBJECT));
    }

    /**
     * Get keywords matching stat by content order.
     *
     * @since    1.0.0
     */
    public static function get_campaign_keywords_match_stat()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . SAFETAG_POST_CAMPAIGN_KEYWORDS_TABLE;

        $sql = "select b.name,b.type, count(a.post_id) as total from wp_safetag_post_campaign_keywords as a
        INNER JOIN wp_safetag_exclusion_list as b on a.campaign_id = b.id
        where a.keywords IS NOT NULL group by a.campaign_id order by b.name";

        return array("result" => $wpdb->get_results($sql, OBJECT));
    }

    /**
     * Get keywords matching stat by campaign id by alphabetic order.
     *
     * @since    1.0.0
     */
    public static function json_format_decode()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . SAFETAG_POST_CAMPAIGN_KEYWORDS_TABLE;

        $sql = "select *
        from $table_name
        where keywords IS NOT NULL AND keywords != ''
        order by id";
        $res = $wpdb->get_results($sql, OBJECT);
        foreach ($res as $key => $value) {
            $test = $value->keywords;
            $test1 = json_decode($test);
            if ($test1 == NULL) {
                $test1 = $test;
            } else {
                foreach($test1 as $key1 => $value1){
                    $test1 = strtolower($value1);
                    break;
                }
                $columnsUpdate = array(
					'id' => $value->id,
					'campaign_id' => $value->campaign_id,
					'post_id' => $value->post_id,
					'update_required' => $value->update_required,
					'keywords' => $test1
				);
                $filter = array('id' => $value->id);
                $wpdb->update($table_name, $columnsUpdate, $filter);
            }
        }

        return true;
    }

    /**
     * Get total campaing
     *  @since 1.0.1
     */
   public static function total_campaing(){
    global $wpdb;
    $table_name = $wpdb->prefix . SAFETAG_EXCLUSION_LIST_TABLE;
    $sql = "select count(*) as total from $table_name";
    return $wpdb->get_results($sql);
   }

    /**
     * Get all campaign
     *  @since 1.0.1
     */
   public static function get_all_campaign(){
    global $wpdb;
    $table_name = $wpdb->prefix . SAFETAG_EXCLUSION_LIST_TABLE;
    $sql = "SELECT id FROM $table_name WHERE active = 1";
    return $wpdb->get_results($sql);
   }

   public static function get_latest_campaing(){
    global $wpdb;
    $table_name = $wpdb->prefix . SAFETAG_EXCLUSION_LIST_TABLE;
    return $wpdb->get_results($wpdb->prepare("select id from $table_name ORDER BY id DESC LIMIT 1;"), ARRAY_A);
   }
}
