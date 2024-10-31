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
$site_audience_iab_tags = get_option("site_audience_iab_tags");
$site_audience_iab_tags_value = json_decode($site_audience_iab_tags);
$licenceRemainCheck = Safetag_Page_Helper::get_license_expire_due_time();
$license_key = Safetag_Page_Helper::get_option_data_single('safetag_license_key', 'key');
$license_status = Safetag_Page_Helper::get_option_data_single('safetag_license_key', 'status');
$license_status_icon = $license_status == "active" ? "fa-check-circle" : "fa-exclamation-circle";
// var_dump($license_status);

?>



<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="safetag-wrap">
<div class="safetag-flex-container pull-lr-16 safetag-align-middle safetag-report-page-bycampaign-header">
    <div class="safetag-columns safetag-sm-full">
      <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    </div>
    <div class="campaigns-navigation">
          <div class="campaigns-nav-right nav-report-btn">
            <?php if($license_status !== 'active'):?>
              <h1 class="setting-free-version">Free Version</h1>
            <?php endif;?>
          </div>
        </div>
  </div>
    <?php if(!Safetag_Page_Helper::last_cron_run_get_time_spent()):?>
    <div class="not-success-message-setting-page admin-msg safetag-mb-20">
      <p><span class="dashicons dashicons-warning"></span> It seems SafeTag cron is not running. SafeTag depends on cron to process contents.</p>
      <!-- <p>Last run <?=Safetag_Page_Helper::$last_cron_run_diff_time;?> minutes ago.</p> -->
    </div>
    <?php endif;?>

    <!-- check user licnese expire date -->
    <?php if(is_numeric($licenceRemainCheck)):?>
    <?php if($licenceRemainCheck < 31):?>
    <div class="not-success-message-setting-page admin-msg">
      <p>Your Subscription Term <?=$licenceRemainCheck<0?'has already expired':'will end';?> in <?=abs($licenceRemainCheck);?> days. <button class="error-btn-close">&times;</button></p>
      <p>Your Credit Card is set to auto-renew. Please <a href="">login</a> to ensure the card is still valid or Campaigns will stop working.</p>
      <p>You can continue to run one free campaign with up to 500 Terms.</p>
    </div>
    <?php endif;?>
    <?php endif;?>


    <?php Safetag_Admin::safetag_admin_notice() ?>

    <div class="safetag-wrap-row safetag-flex-wrap safetag-no-gap">
        <div class="safetag-col-8 safetag-sm-col-12">

            <form method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">

                <input type="hidden" name="action" value="safetag_config_form_response">
                <input type="hidden" name="page" value="<?php echo esc_html($_GET['page']); ?>">
                <input type="hidden" name="safetag_config_form_nonce" value="<?php echo wp_create_nonce('safetag_config_form_nonce') ?>" />
                <!--License Key form -->


                <table class="form-table ">
                    <thead>
                        <tr>
                            <td>
                                <p><strong>Support License Key</strong></p>
                                <p><a href="https://safetag.ai/pricing/" target="_blank">LEARN MORE</a></p>
                            </td>
                        </tr>
                    </thead>
                    <tbody>

                        <tr>
                            <td>
                                A valid license key is required for access to automatic plugin upgrades and product support.
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="safetag-wrap-row" style="justify-content: flex-start;">
                                    <div style="width: 55%;">
                                        <label class="safetag-licence-key" for="safetag_license_key">Paste Your License Key Here
                                            <div class="safetag-right-inner-addon input-container">
                                                <i class="safetag-icon fa <?php echo esc_html($license_status_icon); ?> "></i>
                                                <input type="text" required name="nds[safetag_license_key]" id="safetag_license_key" value="<?php echo esc_html($license_key); ?>" class="regular-text" placeholder="License Key" autocomplete="off" />
                                                <input type="hidden" name="nds[safetag_license_status]" value="">
                                            </div>
                                        </label>
                                    </div>
                                    <div class="safetag-license-buttons">
                                        <!-- <input type="submit" name="activate_license" value="AdGrid Client" class="btn-add-grid" /> -->
                                        <input type="submit" name="activate_license" value="Activate" class="button button-primary" />
                                        <input type="submit" name="deactivate_license" value="Deactivate" class="button" />
                                        <input type="submit" name="check_license" value="Check Status" class="button" />
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
            <form method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="safetag_iab_tags_form_response">
                <input type="hidden" name="page" value="<?php echo esc_html($_GET['page']); ?>">
                <input type="hidden" name="safetag_iab_tags_form_nonce" value="<?php echo wp_create_nonce('safetag_iab_tags_form_nonce') ?>" />
                <table class="form-table tags-section">
                    <thead>
                        <tr>
                            <td>
                                <p><strong>Site Audience IAB Tags</strong></p>
                                <p><a href="https://safetag.ai/help-center/" target="_blank">VIEW TAGS</a></p>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>These tags will be passed on all ad calls in addition to the page specific content tags. These are used to identify the site’s typical audience.</td>
                        </tr>
                        <tr>
                            <td class="safetag-setting-save-btn">
                                <div class="safetag-wrap-row" style="justify-content: flex-start;">
                                <div class="site-audience-tag">
                                    <input type="hidden" name="safetag_meta_tags_values" id="safetag_meta_tags_values" value='<?php echo esc_html($site_audience_iab_tags); ?>'>
                                    <select autocapitalize="off" name="nds[site_audience_iab_tags][]" id="audience-select-tools"></select>
                                </div>
                                </div>
                                <input type="submit" class="safetag_tags_btn submit-button" name="tags_create" value="SAVE" >
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
            <form method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
              <input type="hidden" name="action" value="safetag_post_types_form_response">
              <input type="hidden" name="page" value="<?php echo esc_html($_GET['page']); ?>">
              <input type="hidden" name="safetag_post_types_form_nonce" value="<?php echo wp_create_nonce('safetag_post_types_form_nonce') ?>" />
              <table class="form-table post-types-section">
                <thead>
                  <tr>
                    <td>
                      <p><strong>Include Post Types</strong></p>
                    </td>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="safetag-setting-save-btn">
                      <div class="safetag-wrap-row" style="justify-content: flex-start;">
                        <div class="site-post-types">
                          <?php
                            $selected_post_types = json_decode(get_option('safetag_post_types'));
                            $selected_post_types = (array) $selected_post_types ?? [];

                            $post_types          = Safetag_Admin::get_safetag_post_types();

                            usort($post_types, function ($a, $b) {
                              return strcmp($a['label'], $b['label']);
                            });
                          ?>
                          <?php if(!empty($post_types)) : ?>
                            <?php foreach ($post_types as $key => $item) :
                              if($item['name'] == 'attachment') {
                                continue;
                              } ?>
                              <div>
                                <input
                                  type="checkbox"
                                  id="<?php echo $item['name']; ?>"
                                  name="post_types[<?php echo $item['name']; ?>]"
                                  <?php echo !empty($selected_post_types[$item['name']]) ? "checked='checked'" : ""; ?>
                                  value="<?php echo $item['name']; ?>"
                                >
                                <label for="<?php echo $item['name']; ?>"><?php echo $item['label']; ?></label>
                              </div>
                            <?php endforeach; ?>
                          <?php endif; ?>
                        </div>
                      </div>
                      <input type="submit" class="safetag_tags_btn submit-button" name="add_post_types" value="Save" >
                    </td>
                  </tr>
                </tbody>
              </table>
            </form>
        </div>


        <!-- <div class="safetag-col-4 safetag-sm-col-12">
            <form method="POST" action="<?php // echo esc_url(admin_url('admin-post.php')); ?>">

                <input type="hidden" name="action" value="safetag_config_form_response">
                <input type="hidden" name="page" value="<?php //echo $_GET['page'] ?>">
                <input type="hidden" name="safetag_config_form_nonce" value="<?php //echo wp_create_nonce('safetag_config_form_nonce') ?>" />

                <table class="form-table">
                    <thead>
                        <tr>
                            <th>IAB Tags</th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr>
                            <td>
                                Allow content editors the ability to select from IAB
                                taxonomy for more targeted ads.
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="switch">
                                    <input type="checkbox" name="nds[safetag_iab_tag_option]" <?php // echo get_option('safetag_iab_tag_option') != '' ? ' checked="checked" ' : ''; ?>>
                                    <span class=" slider round"></span>
                                </label>
                            </td>
                        </tr>
                    </tbody>
                </table>
        </div> -->
    </div>



        <!--License info table -->
        <!-- <table class="form-table">
            <thead>
                <tr>
                    <th>Features</th>
                </tr>
            </thead>
            <tbody>
                <table style="width: 100%;">
                    <tr>
                        <td></td>
                        <th scope="col">IAB Tags</th>
                        <th scope="col">Ads.txt</th>
                        <th scope="col">Exclusion Lists</th>
                        <th scope="col">Chron</th>
                        <th scope="col">Ad Display</th>
                        <th scope="col">Support</th>
                    </tr>
                    <tr>
                        <th scope="row">Free</th>
                        <td>1st Level</td>
                        <td>Yes</td>
                        <td>3 lists / 100 words</td>
                        <td>No</td>
                        <td>No</td>
                        <td>Online</td>
                    </tr>
                    <tr>
                        <th scope="row">Paid</th>
                        <td>All Levels</td>
                        <td>Yes</td>
                        <td>Unlimited</td>
                        <td>Yes</td>
                        <td>No</td>
                        <td>+ Email</td>
                    </tr>
                    <tr>
                        <th scope="row">SafeTag</th>
                        <td>All Levels</td>
                        <td>Yes</td>
                        <td>Unlimited</td>
                        <td>Yes</td>
                        <td>Yes</td>
                        <td>+ Person</td>
                    </tr>
            </tbody>
        </table> -->
        <!-- switch button table -->
        <div class="safetag-wrap-row">

            <div class="safetag-col-8 safetag-sm-col-12">
                <table class="form-table">
                    <thead>
                        <tr>
                            <td>
                                <strong>Status</strong>
                            </td>
                        </tr>
                    </thead>
                    <tbody>

                        <tr>
                            <td>
                                The number of posts, post types, length and number of key word lists all affect the amount of time needed to fully complete scanning the site. The first scan takes the longest with each additional scan building on top of the first. New and edited Posts will trigger a fresh scan of that post when saved.
                                <?php print_safetag_log() ?>
                                <br>
                                <hr>
                                <?php print_safetag_cron_statistics() ?>
                            </td>
                        </tr>
                        <!-- <tr>
                            <td>
                                <label class="switch">
                                    <input type="checkbox" name="nds[safetag_exclution_list_chron_option]" <?php // echo get_option('safetag_exclution_list_chron_option') != '' ? ' checked="checked" ' : ''; ?>>
                                    <span class=" slider round"></span>
                                </label>
                            </td>
                        </tr> -->
                    </tbody>
                </table>
            </div>

        </div>
        <!-- <p class="submit">
            <input type="submit" name="submit" id="submit" class="buttonx button-primary" value="Save">
        </p> -->
</div>
<script type="text/javascript">
    // The DOM element you wish to replace with Tagify
    var input = document.querySelector('input[id=safetag-input-tags]');

    // initialize Tagify on the above input node reference
    new Tagify(input)
</script>
<?php

function print_safetag_log()
{
    $log = Safetag_Log::get_latest_record();

    if ($log != null && $log != '') {
        echo "<br><hr>";
        echo "Log:";
        echo "<br>";
        $logData = json_decode($log);
        if ($logData->type == "Exception") {
            echo "Exception:" . esc_html($logData->rowAffected);
        } else if ($logData->type == "Starting") {
            echo "start: " . esc_html($logData->start) . " | Status: In progress.";
        } else {
            echo "start: " . esc_html($logData->start) . " | end: " . esc_html( $logData->end) . " | seconds: " . esc_html( $logData->totalSeconds ) . " | post: " . esc_html($logData->posts) . " | rowAffected: " . esc_html($logData->rowAffected);
        }
    };
}

function print_safetag_cron_statistics()
{
    $updated = Safetag_Db_Management::get_post_campain_keywords_all_count(0)->count;
    $require_update = Safetag_Db_Management::get_post_campain_keywords_all_count(1)->count;
    $total = $require_update + $updated;
    $percentage = 0;
    if(!empty($total)) {
        $percentage = number_format((100 * $updated / $total), 2);
    }
    if($percentage == 'nan')
        $percentage = '0';

    // echo "Total: " .  esc_html($total) . " | " . "missing: " . esc_html($require_update) . " | " . "updated: " .  esc_html($updated) . " | " . "Completed: " . esc_html($percentage)  . "%";
    echo "<div style='display:none;'>";
    echo "Total: " .  Safetag_Page_Helper::get_number_format(esc_html($total)) . " | " . "Processing: " .  esc_html($require_update) . " | " . "Completed: " . esc_html($percentage)  . "%";
    echo "</div>";

    echo "Completed: " . esc_html($percentage)  . "%";
}
?>
