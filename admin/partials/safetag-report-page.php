<?php
    $post_count = Safetag_Db_Management::get_total_post_count();
    if( !empty( $post_count['result'] ) ){
        $total_posts = null;
        foreach ($post_count['result']  as $value) {
          $total_posts = $value->count;
        }
    }

    $alphabetic_stat = Safetag_Db_Management::get_alphabetic_keywords_match_stat();
    $alphabetic_stat_records = $alphabetic_stat['result'];
    $content_stat = Safetag_Db_Management::get_content_keywords_match_stat();
    $content_stat_records = $content_stat['result'];
    $campaign_stat = Safetag_Db_Management::get_campaign_keywords_match_stat();
    $campaign_stat_records = $campaign_stat['result'];
?>
<div class="safetag-wrap safetag-reports">
    <div class="safetag-flex-container pull-lr-16 safetag-align-middle safetag-reports-title">
      <h1 class="safetag-columns safetag-sm-full">Safetag Reports</h1>
      <h3 class="safetag-columns safetag-shrink safetag-sm-full">Total Posts: <?php echo esc_html( $total_posts ); ?></h3>
    </div>
    <div class="safetag-flex-container pull-lr-16">
      <div class="safetag-columns safetag-col-6 safetag-sm-full">
        <div><em>Alphabetical</em></div>
        <table class="mb-40">
            <thead>
                <tr>
                    <th class="safetag-min-col">Phrase</th>
                    <th class="safetag-smaller-col">Matches</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($alphabetic_stat_records as $key => $value) { ?>
                    <tr>
                        <td><?php echo esc_html($value->keywords); ?></td>
                        <td><?php echo esc_html($value->total); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
      </div>
      <div class="safetag-columns safetag-col-6 safetag-sm-full">
        <div><em>By Content</em></div>
        <table class="mb-40">
            <thead>
                <tr>
                    <th class="safetag-min-col">Phrase</th>
                    <th class="safetag-smaller-col">Matches</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($content_stat_records as $key => $value) { ?>
                    <tr>
                        <td><?php echo esc_html($value->keywords); ?></td>
                        <td><?php echo esc_html($value->total); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
      </div>
      <div class="safetag-columns safetag-col-6 safetag-sm-full">
        <table class="mb-40">
            <thead>
                <tr>
                    <th class="safetag-min-col">Excluded</th>
                    <th class="safetag-smaller-col">Matches</th>
                    <th class="safetag-smaller-col">Safe</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($campaign_stat_records as $key => $value) { ?>
                    <?php if ($value->type == 0) : ?>
                    <tr>
                        <td><?php echo esc_html($value->name); ?></td>
                        <td><?php echo esc_html($value->total); ?></td>
                        <td><?php echo esc_html($total_posts - $value->total); ?></td>
                    </tr>
                    <?php endif; ?>
                <?php } ?>
            </tbody>
        </table>
        <table class="mb-40">
            <thead>
                <tr>
                    <th class="safetag-min-col">Included</th>
                    <th class="safetag-smaller-col">Matches</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($campaign_stat_records as $key => $value) { ?>
                    <?php if ($value->type != 0) : ?>
                    <tr>
                        <td><?php echo esc_html($value->name); ?></td>
                        <td><?php echo esc_html($value->total); ?></td>
                    </tr>
                    <?php endif; ?>
                <?php } ?>
            </tbody>
        </table>

      </div>
    </div>
</div>
