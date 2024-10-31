<?php
if (isset($_GET['setting_seccion']) && $_GET['setting_seccion'] == "exclusion-list-report-details") {
  include_once('safetag-report-page-details-bycampaign.php');
  exit;
}
$pid = sanitize_text_field($_GET['pid']);
$status = sanitize_text_field($_GET['campaign_status'] ?? true);

$campaign_type = '';
$per_page = 25;
$pagination_number = $pagination_number_all = 0;
if ((isset($_GET['pid']) && trim($_GET['pid']) != '')) {
  $campaign = Safetag_Db_Management::get_exclusion_list_by_id($pid);
  $campaign_name = isset($campaign->name) ? $campaign->name : '';
  $campaign_type = $campaign->type ? 'Include' : 'Exclude';
  $campaign_status = $campaign->active ? 'Active' : 'Inactive';
}
$alphabetic_stat = Safetag_Db_Management::get_alphabetic_keywords_match_stat_bycampaign_id($pid);
// $alphabetic_stat_records_slice_temp = array_slice($alphabetic_stat['result'], 0, 250);

$alphabetic_stat_records_slice = $alphabetic_stat['result'];
if(count($alphabetic_stat['result']) > $per_page) {
  $alphabetic_stat_records_slice = array_slice($alphabetic_stat_records_slice, $_GET['slice_number'], $per_page);
  $pagination_number = ceil(count($alphabetic_stat['result']) / $per_page);
}

$alphabetic_stat_total = Safetag_Db_Management::get_total_keywords_match_stat_bycampaign_id($pid);
$alphabetic_stat_total_records = $alphabetic_stat_total['result'];
foreach ($alphabetic_stat_total_records as $key => $value) {
  $total_keywords = $value->total;
}
$distinct_keywords_total = Safetag_Db_Management::get_total_distinct_keywords_match_stat_bycampaign_id($pid);
$total_distinct_keywords = count($distinct_keywords_total['result']);

$campaign_keywords_detail = Safetag_Db_Management::get_exclusion_list_history_by_exclution_list_id($pid);
// $campaign_keywords_detail_count = substr_count($campaign_keywords_detail->keywords, ' ');
// $campaign_keywords_detail_array = explode(' ', $campaign_keywords_detail->keywords);
$campaign_keywords_detail_array2 = explode(PHP_EOL, $campaign_keywords_detail->keywords);
$campaign_keywords_detail_count = count($campaign_keywords_detail_array2);
// $campaign_keywords_detail_array2_slice_temp = array_slice($campaign_keywords_detail_array2, 0, 250);
$campaign_keywords_detail_array2_slice = $campaign_keywords_detail_array2;

if(count($campaign_keywords_detail_array2) > $per_page) {
  $campaign_keywords_detail_array2_slice = array_slice($campaign_keywords_detail_array2, sanitize_text_field($_GET['slice_number']), $per_page);
  $pagination_number_all = ceil(count($campaign_keywords_detail_array2) / $per_page);
}

//campaign post report chart
$campaign_post_report = Safetag_Db_Management::get_total_campaign_post_report_bycampaign_id($pid);
$total_post = $campaign_post_report['safe'] + $campaign_post_report['excluded'] + $campaign_post_report['update_required'];

$pagination_html = function($i, $status, $pid, $name = false) use($per_page) {
  $active = isset($_GET['slice_number']) ? (($_GET['slice_number']/$per_page) == $i? 'active': '') : '';
  $link = Safetag_Admin::generate_url(
    [
      "setting_seccion" => "exclusion-list-report",
      "pid" => $pid,
      "slice_number" => $i * $per_page,
      "campaign_status" => $status ? 'true' : 'false'
    ],
    esc_html($_GET['page'])
  );
  $incr = $name ? $name : $i + 1;
  return <<<HTML
    <li><a class="{$active}" href="{$link}">{$incr}</a></li>  
  HTML;
};
$download_csv = function($pid, $all = false) {
  $arr = [
    "setting_seccion" => "safetag-report-export",
    "pid" => $pid
  ];
  if($all)
    $arr['all'] = 'true';
  $link = Safetag_Admin::generate_url(
    $arr,
    esc_html($_GET['page'])
  );
  return <<<HTML
    <a class="page-title-action" href="{$link}">DOWNLOAD CSV</a>
  HTML;
};

$view_posts = function($pid, $campaign_name, $value, $total) {
  $link = Safetag_Admin::generate_url(
    [
      "setting_seccion" => "exclusion-list-report-details",
      "pid" => $pid,
      "pname" => $campaign_name,
      "keyword" => urlencode($value),
      "total" => $total
    ],
    esc_html($_GET['page'])
  );
  return <<<HTML
    <a href="{$link}">VIEW POSTS</a>
  HTML;
};
$color_chart = Safetag_Page_Helper::get_chart_color_var($campaign_type, $campaign_post_report, $total_post);
extract($color_chart);
?>
<style>
  .page-numbers a.active {
      font-weight: bold;
      border-color: blue;
  }
</style>
<div class="wrap safetag-reports">
  <div class="safetag-flex-container pull-lr-16 safetag-align-middle safetag-report-page-bycampaign-header">
    <div class="safetag-columns safetag-sm-full">
      <h1><?php esc_html_e($campaign_name, 'safetag'); ?></h1>
    </div>
    <div class="campaigns-navigation">
          <div class="campaigns-nav-left">
            <a href="<?php echo Safetag_Admin::generate_url(
                        [],
                        esc_html($_GET['page'])
                      ); ?>">
              <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            </a>
          </div>
          <div class="campaigns-nav-middle"></div>
          <div class="campaigns-nav-right nav-report-btn">
            <h1>Report</h1>
          </div>
        </div>
  </div>
  <div class="mb-25">
    <div>
        <?php esc_html_e($campaign_type, 'safetag'); ?> <span class="safetag-separator">|</span> <?php esc_html_e($campaign_status, 'safetag'); ?> <span class="safetag-separator">|</span> <?php echo Safetag_Page_Helper::get_number_format(esc_html($campaign_keywords_detail_count)); ?> Terms
    </div>

  </div>
  <div>
    <div class="safetag-flex-container pull-lr-10 safetag-align-middle mb-20">
      <div class="plr-10">
        <label class="switch" for="show-matched-terms-status">
          <input id="show-matched-terms-status" type="checkbox" checked="checked" onclick="safeTagToggleMatchTerms();">
          <span class="slider round"></span>
          <span class="switch-active" aria-hidden="true">Yes</span>
          <span class="switch-inactive" aria-hidden="true">No</span>
        </label>
      </div>
      <div class="plr-15">
        <label for="show-matched-terms-status" class="bold">Show Matched Terms Only</label>
      </div>
    </div>
    <div class="safetag-flex-container pull-lr-16">
      <div class="safetag-columns safetag-col-8">
        <div id="report_table_by_matches">
          <div class="safetag-flex-container pull-lr-16">
            <div class="safetag-columns safetag-col-12 safetag-sm-full">
              <div class="safetag-flex-container safetag-align-justify mb-20 pull-lr-10">
                <div class="plr-10">
                  Matched Terms : <?php echo Safetag_Page_Helper::get_number_format(esc_html($total_distinct_keywords)); ?>
                </div>
                <div class="plr-10">
                  <?=$download_csv($pid);?>
                </div>
              </div>
              <table class=" wp-list-table widefat fixed striped table-view-list mb-20 safetag-report-table">
                <thead>
                  <tr>
                    <th class="safetag-min-col"><strong>Term</strong></th>
                    <th class="safetag-smaller-col"><strong>Matches</strong></th>
                    <th class="safetag-percentage-col"><strong>%</strong></th>
                    <th class="safetag-smaller-col"></th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($alphabetic_stat_records_slice as $key => $value) {  ?>
                    <tr>
                      <td><?php echo esc_html($value->keywords); ?></td>
                      <td><?php echo Safetag_Page_Helper::get_number_format(esc_html($value->total)); ?></td>
                      <td><?php echo Safetag_Page_Helper::get_number_percentage($value->total, $total_post); ?>%</td>
                      <td>
                        <?=$view_posts($pid, $campaign_name, $value->keywords, $value->total);?>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
          <div class="pagination">
            <ul class="page-numbers">
              <?php if($pagination_number > 0) :?>
                <?=$pagination_html(0, true, $pid, 'First');?>                    
                <?php for ($i = 0; $i < $pagination_number; $i++) : ?>
                    <?php if($i >= ($_GET['slice_number']/$per_page)-5 && $i < ($_GET['slice_number']/$per_page)+5) :?>
                    <?=$pagination_html($i, true, $pid);?>
                  <?php endif; ?>
                <?php endfor; ?>
                <?=$pagination_html(($pagination_number-1), true, $pid, 'Last');?>
              <?php endif; ?>
            </ul>
          </div>
        </div>
        <div id="report_table_by_matches_all">
          <div class="safetag-flex-container pull-lr-16">
            <div class="safetag-columns safetag-col-12 safetag-sm-full">
              <div class="safetag-flex-container safetag-align-justify mb-20 pull-lr-10">
                <div class="plr-10">
                  Matched Terms : <?php echo Safetag_Page_Helper::get_number_format(esc_html($campaign_keywords_detail_count)); ?>
                </div>
                <div class="plr-10">
                  <?=$download_csv($pid, true)?>
                </div>
              </div>
              <table class=" mb-40 wp-list-table widefat fixed striped table-view-list safetag-report-table">
                <thead>
                  <tr>
                    <th class="safetag-min-col"><strong>Term</strong></th>
                    <th class="safetag-smaller-col"><strong>Matches</strong></th>
                    <th class="safetag-percentage-col"><strong>%</strong></th>
                    <th class="safetag-smaller-col"></th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($campaign_keywords_detail_array2_slice as $value) { ?>
                    <?php if (!empty($value)) { ?>
                      <tr>
                        <td><?php echo esc_html($value); ?></td>
                        <td><?php $t = Safetag_Db_Management::get_alphabetic_keywords_match_stat_bycampaign_id_and_keyword($pid, trim($value));
                            $t = $t['result'];
                            $t_total = 0;
                            foreach ($t as $key => $value3) {
                              $t_total = $value3->total;
                            };
                            echo Safetag_Page_Helper::get_number_format(esc_html($t_total)); ?></td>
                        <td><?php echo Safetag_Page_Helper::get_number_percentage($t_total, $total_post); ?>%</td>
                        <td>
                          <?=$view_posts($pid, $campaign_name, $value, $t_total);?>
                        </td>
                      </tr>
                    <?php } ?>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
          <div class="pagination">
            <input type="hidden" value="<?php echo $status; ?>" id="campaign_status">
            <ul class="page-numbers">
              <?php if($pagination_number_all > 0) :?>
                <?=$pagination_html(0, false, $pid, 'First');?>                    
                <?php for ($i = 0; $i < $pagination_number_all; $i++) : ?>
                    <?php if($i >= ($_GET['slice_number']/$per_page)-5 && $i < ($_GET['slice_number']/$per_page)+5) :?>
                    <?=$pagination_html($i, false, $pid);?>
                  <?php endif; ?>
                <?php endfor; ?>
                <?=$pagination_html(($pagination_number_all-1), false, $pid, 'Last');?>
              <?php endif; ?>
            </ul>

          </div>
        </div>
      </div>
      <?php
        include_once plugin_dir_path(dirname(__FILE__)) . 'partials/report_sections/chart_section.php';
      ?>
    </div>

    <script type="text/javascript">
      document.getElementById("show-matched-terms-status").checked = JSON.parse(document.getElementById('campaign_status').value);
      const show_matched_terms_status = document.querySelector("#show-matched-terms-status");
      const report_table_by_matches = document.querySelector("#report_table_by_matches");
      const report_table_by_matches_all = document.querySelector("#report_table_by_matches_all");
      safeTagToggleMatchTerms();

      function safeTagToggleMatchTerms() {
        if (show_matched_terms_status.checked == true) {
          report_table_by_matches.style.display = "block";
          report_table_by_matches_all.style.display = "none";
        } else {
          report_table_by_matches.style.display = "none";
          report_table_by_matches_all.style.display = "block";
        }
      }
    </script>
