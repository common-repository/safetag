<?php

use GPBMetadata\Google\Firestore\V1Beta1\Write;

/**
 * Define all safetag API endpoints.
 *
 * @since      1.0.0
 * @package    safetag
 * @subpackage safetag/includes
 * @author     Your Name <email@example.com>
 */

class Safetag_API
{
    public function register()
    {
        register_rest_route('safetag-api/v1', '/iabtags(?:/(?P<search>\d+))?', array(
            'methods'             => 'GET',
            'callback'            => [$this, 'get_safetag_iab_tagas'],
            'permission_callback' => [$this, 'user_validation_middleware'],
            'args'                => ['search' => []],
        ));

        register_rest_route('safetag-api/v1', '/audiencetags(?:/(?P<search>\d+))?', array(
            'methods'             => 'GET',
            'callback'            => [$this, 'get_safetag_iab_audience_tags'],
            'permission_callback' => [$this, 'user_validation_middleware'],
            'args'                => ['search' => []],
        ));

        register_rest_route('safetag-api/v1', '/post-campaign/(?P<post_id>\d+)', array(
          'methods'             => 'GET',
          'callback'            => [$this, 'get_safetag_post_setting'],
          'permission_callback' => function () {
              return true;
          },
          'args'                => ['post_id' => []],
        ));
    }

    public function user_validation_middleware()
    {
        wp_create_nonce('wp_rest');
        return is_user_logged_in();
    }

    public function get_safetag_iab_tagas($request)
    {
        $search = $request['search'];
        $json = Safetag_Page_Helper::get_iab_tags_resources();

        $result_filtered = array_filter($json, function ($var) use ($search) {
            $tax_name = !empty($var['tax_name']) ? $var['tax_name'] : '';
            return preg_match("/{$search}/i", $tax_name);
        });

        return array_slice($result_filtered, 0, 1200, true);
    }

    public function get_safetag_iab_audience_tags($request)
    {
        $search = $request['search'];
        $json = Safetag_Page_Helper::get_iab_audience_tags();

        $result_filtered = array_filter($json, function ($var) use ($search) {
            $tax_name = !empty($var['tax_name']) ? $var['tax_name'] : '';
            return preg_match("/{$search}/i", $tax_name);
        });

        return array_slice($result_filtered, 0, 1200, true);
    }

    public function get_safetag_post_setting($request)
    {
      $post_id = $request['post_id'];
      $post_status = get_post_status($post_id);
      $is_post_id_valid = $post_status != false && $post_status == 'publish';

      $safetag_hide_programmatic_ads = false;
      $st_meta_tags = "";
      $keyword_result = "";
      $keyword_result_is_pending = false;
      $safetag_meta_tags = [];
      if ($is_post_id_valid) {
        $safetag_hide_programmatic_ads =  true;
        if ($safetag_hide_programmatic_ads) {
          $st_meta_tags = json_decode( get_post_meta($post_id, 'safetag_meta_tags', true))?? [];
          $st_audience_tags = json_decode( get_option('site_audience_iab_tags'))?? [];

          $iab_tags = Safetag_Page_Helper::get_iab_tags_resources();
          $iab_audiences = Safetag_Page_Helper::get_iab_audience_tags();

          $index = ['rtb','taxonomy','taxonomy_name'];

          $iab_new = [];
          if(!empty($st_meta_tags)){
            foreach ($st_meta_tags as $tid) {
              if(!empty($iab_tags[$tid - 1])){
                $iab_new[]['tier'] = !empty($iab_tags[$tid - 1]['tier']) ? $iab_tags[$tid - 1]['tier'] : null;
              }
            }
          }

          $iab_audience_new = [];
          if(!empty($st_audience_tags)){
            foreach ($st_audience_tags as $tid) {
              if(!empty($iab_audiences[$tid - 1])) {
                $iab_audience_new[] = [
                  "name" => $iab_audiences[$tid - 1]['tax_name']
                ];
              }
            }
          }

          if(!empty($iab_new)){
            foreach ($iab_new as $item) {
              foreach ( $item['tier'] as $key => $value) {
                switch ($key) {
                  case 0:
                    $section = 'cat';
                    break;
                  case 1:
                    $section = 'sectioncat';
                    break;
                  case 2:
                    $section = 'pagecat';
                    break;
                }

                if(!empty($value['rtb']))
                $safetag_meta_tags['rtb'][$section][$value['name']] = $value['rtb'];

                $safetag_meta_tags['taxonomy'][$section][$value['name']] = (string) $value['id'];
                $safetag_meta_tags['taxonomy_name'][$section][$value['name']] = $value['name'];
              }
            }

            foreach ($iab_new as $item) {

              foreach ( $item['tier'] as $key => $value) {
                switch ($key) {
                  case 0:
                    $section = 'cat';
                    break;
                  case 1:
                    $section = 'sectioncat';
                    break;
                  case 2:
                    $section = 'pagecat';
                    break;
                }

                foreach ($index as $index_name) {

                  if(!empty($safetag_meta_tags[$index_name]))
                  $safetag_meta_tags[$index_name][$section] = !empty($safetag_meta_tags[$index_name][$section]) ? array_values($safetag_meta_tags[$index_name][$section]) : null;
                }
              }
            }
          }

          if(!empty($iab_audience_new)) {
            foreach ($iab_audience_new as $tag) {
              $safetag_meta_tags['taxonomy_name']['pagecat'][] = $tag['name'];
            }
          }
        }

        $keyword_result_is_pending = count(Safetag_Db_Management::is_pending_keywords_result_by_post_id($post_id)) > 0;
        if (!$keyword_result_is_pending) {
          $keyword_result = json_encode(Safetag_Db_Management::get_keywords_result_by_post_id($post_id));
        }
      }

      $Exclude = [];
		  $Include = [];
      $keyword_result = json_decode($keyword_result);
      if(!empty($keyword_result)) {
        foreach( $keyword_result as $item ) {
          if( $item->type == 0 ) {
            $Exclude[] = $item->name;
          } else if( $item->type == 1 ){
            $Include[] = $item->name;
          }
        }
      }

      $safetag_lists = [
        "Exclude" => (array) $Exclude,
        "Include" => (array) $Include
      ];
      //encode to json iab tags
      $safetag_fpd = [
        "iab" => $safetag_meta_tags,
        "cattax" => 2
      ];

      return [
        'is_valid' => $is_post_id_valid,
        'post_id' => $post_id,
        'enable' => $safetag_hide_programmatic_ads,
        'safetag_fpd' => $safetag_fpd,
        "keyword_result_is_pending" => $keyword_result_is_pending,
        'safetag_lists' => $safetag_lists
      ];
    }
}
