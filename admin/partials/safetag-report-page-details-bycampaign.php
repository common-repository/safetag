<?php
    $campaign_name = sanitize_text_field($_GET['pname']);
    $campaign_keyword = sanitize_text_field($_GET['keyword']);
    $total = sanitize_text_field($_GET['total']);
    $pid = sanitize_text_field($_GET['pid']);
    $alphabetic_stat = Safetag_Db_Management::get_alphabetic_keywords_match_detailsbykeyword($pid, $campaign_keyword);
    $alphabetic_stat_records = $alphabetic_stat['result'];
?>
<div class="wrap safetag-reports">
    <div class="safetag-wrap-row safetag-align-middle safetag-report-page-details-header">
        <div class="safetag-columns safetag-sm-full">
            <h1><?php esc_html_e($campaign_name, 'safetag'); ?> > <?php echo esc_html($campaign_keyword); ?></h1>
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
        <div class="campaigns-nav-right">
          <a href="<?php echo Safetag_Admin::generate_url(
                      [
                        "setting_seccion" => "exclusion-list-report",
                        "pid" => $pid,
                        "slice_number" => 0
                      ],
                      esc_html($_GET['page'])
                    ); ?>">
            <h1>back to report</h1>
          </a>
        </div>
      </div>
    </div>
    <div class="mb-20">
  
    </div>
    <div class="safetag-wrap-row">
      <div class="safetag-columns safetag-col-8 safetag-sm-full">

        <div class="safetag-flex-container safetag-align-justify mb-20 pull-lr-10">
            <div class="plr-10">Matches : <?php echo esc_html($total); ?> </div>
            <div class="plr-10">
                <a class="page-title-action" href="<?php echo Safetag_Admin::generate_url(
                    [
                        "setting_seccion" => "safetag-report-export-bykeyword",
                        "pid" => $pid,
                        "keyword" => $campaign_keyword,
                    ],
                    esc_html($_GET['page'])
                ); ?>">DOWNLOAD CSV</a>
            </div>
        </div>

        <table class=" wp-list-table widefat fixed striped table-view-list mb-20 safetag-report-page-details-table">
            <thead>
                <tr>
                    <th class="safetag-min-col"><strong>Title</strong></th>
                    <th class="safetag-smaller-col"><strong>Date</strong></th>
                    <th class="safetag-smaller-col"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($alphabetic_stat_records as $key => $row) { ?>
                    <tr>
                        <td><?php echo esc_html($row->title); ?></td>
                        <td>
                            <?php echo esc_html(
                                date("F d, Y", strtotime($row->publish_date))
                            ); ?>
                        </td>
                        <td><a href=<?php echo esc_html(
                            get_permalink($row->post_id)
                        ); ?> target="_blank">View</a></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
      </div>
    </div>
</div>
