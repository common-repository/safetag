<div class="safetag-columns safetag-col-4">
    <div id="safetag-pie-chart-container" class="safetag-pie-chart-container">
      <div class="pie-chart-title"><?php esc_html_e($campaign_type, 'safetag'); ?></div>
      <div class="safetag-pie-chart-div">
        <div id="safetag-pie-chart" style="background: conic-gradient( <?php echo $safe_style . $excluded_style . $update_required_style; ?>  );"></div>
      </div>
      <div id="safetag-legends" class="safetag-legends">
        <table class="form-table pie-chart-table-data">
          <!-- campaign_type -->
          <tr class="<?php if ($campaign_post_report['excluded'] == 0) echo 'hide'; ?> ">
            <td>
                <div style="background-color: <?= $excluded_bg; ?>;" class="entry-color"></div>
                <?php esc_html_e($campaign_type, 'safetag'); ?>
            </td>
            <td><?php echo Safetag_Page_Helper::get_number_format($campaign_post_report['excluded']); ?></td>
            <td><i><?php echo Safetag_Page_Helper::get_number_percentage($campaign_post_report['excluded'], $total_post); ?>%</i></td>
          </tr>
          <!-- 'No Match' : 'Safe' -->
          <tr class="<?php if ($campaign_post_report['safe'] == 0) echo 'hide'; ?> ">
            <td>
              <div style="background-color: <?= $safe_bg; ?>;" class="entry-color"></div>
              <?= $campaign_type == 'Include' ? 'No Match' : 'Safe' ?>
            </td>
            <td>
              <?php echo Safetag_Page_Helper::get_number_format($campaign_post_report['safe']) ?>                    
            </td>
            <td>
              <i><?php echo Safetag_Page_Helper::get_number_percentage($campaign_post_report['safe'], $total_post); ?>%</i>
            </td>
          </tr>
          <!-- Not Scanned -->
          <tr class="<?php if ($campaign_post_report['update_required'] == 0) echo 'hide'; ?> ">
            <td>
              <div style="background-color: <?= $update_bg; ?>;" class="entry-color"></div>
              Not Scanned
            </td>
            <td>
              <?php echo Safetag_Page_Helper::get_number_format($campaign_post_report['update_required']); ?>
            </td>
            <td>
              <i><?php echo Safetag_Page_Helper::get_number_percentage($campaign_post_report['update_required'], $total_post); ?>%</i>
            </td>
          </tr>

        </table>

      </div>
    </div>
  </div>