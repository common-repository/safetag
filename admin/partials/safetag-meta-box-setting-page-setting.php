<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    safetag
 * @subpackage safetag/admin/partials
 */

$pending_keyword_result = Safetag_Db_Management::is_pending_keywords_result_by_post_id($post->ID);
$missing_keyword_result = Safetag_Db_Management::is_missing_campaign_result_by_post_id($post->ID);
$is_pending_keyword_result = count($pending_keyword_result) > 0;
$is_missing_keyword_result = count($missing_keyword_result) > 0;


$keyword_result = Safetag_Db_Management::get_keywords_result_by_post_id($post->ID);

$keyword_result = array_map(function ($value) {
  return [
    "id" => $value->id,
    "name" => $value->name,
    "type" => Safetag_Cron_Job::$campaign_type[$value->type]['key'],
    "keywords" => $value->keywords
  ];
}, $keyword_result);

$keyword_result_by_type = Safetag_Cron_Job::_group_by($keyword_result, 'type');

$campaign_type = Safetag_Cron_Job::$campaign_type;
// rsort($campaign_type);

$whitelist = isset($keyword_result_by_type['whitelist'])
  ?   array_filter($keyword_result_by_type['whitelist'], function ($value) {
    return $value['type'] = 'whitelist';
  })
  : [];

$blacklist = isset($keyword_result_by_type['blacklist'])
  ?
  array_filter($keyword_result_by_type['blacklist'], function ($value) {
    return $value['type'] = 'blacklist';
  })
  : [];

$index  = max([count($whitelist), count($blacklist)]);


// Add a nonce field so we can check for it later.
wp_nonce_field('safetag_meta_box_notice_nonce', 'safetag_meta_box_notice_nonce');

$safetag_hide_programmatic_ads = get_post_meta($post->ID, 'safetag_hide_programmatic_ads', true);
$safetag_iab_tag_option_enable = get_option('safetag_iab_tag_option');
$value = get_post_meta($post->ID, 'safetag_meta_tags', true);
?>
<div class="safetag-wrap safetag-full-width">

  <?php

    $printSection = function($list, $title){

      if( count($list) == 0 ) return;

      $rows = '';
      foreach ($list as $key => $value) {
        $rows .= <<<HTML
          <p class="safetag-table-data">{$value}</p>
        HTML;
      }


      return <<<HTML
      <table class="form-table safetag-right-table">
        <thead>
          <tr>
            <th>{$title}</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              {$rows}
            </td>
          </tr>
        </tbody>
      </table>
      HTML;
    };

  ?>

  <?php print $printSection( array_map( function($item) { return $item['name']; } , $blacklist),
    'Exclude'); ?>
  <?php print $printSection( array_map( function($item) { return $item['name']; } , $whitelist),
    'Include'); ?>
  <?php print $printSection( array_map( function($item) { return esc_html($item->name); } , $missing_keyword_result),
    'No Matches'); ?>
  <?php print $printSection( array_map( function($item) { return esc_html($item->name); } , $pending_keyword_result),
    'Processing'); ?>


  <?php if ($safetag_iab_tag_option_enable == 'on') { ?>
    <table class="form-table safetag-right-table">
      <thead>
        <tr>
          <th>IAB Tags</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Categorize this content with IAB standard tags.</td>
        </tr>
        <tr>
          <td>
            <input type="hidden" name="safetag_meta_tags_values" id="safetag_meta_tags_values" value='<?php echo esc_html($value); ?>'>
            <select autocapitalize="off" name="nds[safetag_meta_tags][]" id="select-tools"></select>
          </td>
        </tr>
      </tbody>
    </table>

  <?php } ?>
</div>
