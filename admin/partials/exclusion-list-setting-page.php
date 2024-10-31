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
if (isset($_GET['setting_seccion']) && $_GET['setting_seccion'] == "exclusion-list-edit") {
    include_once('exclusion-list-setting-page-detail.php');
    exit;
}

if (isset($_GET['setting_seccion']) && $_GET['setting_seccion'] == "exclusion-list-delete") {
    include_once('exclusion-list-delete.php');
    exit;
}

if (isset($_GET['setting_seccion']) && $_GET['setting_seccion'] == "exclusion-list-export") {
    include_once('exclusion-list-export.php');
    exit;
}

if (isset($_GET['setting_seccion']) && $_GET['setting_seccion'] == "exclusion-list-report") {
    include_once('safetag-report-page-bycampaign.php');
    exit;
}

if (isset($_GET['setting_seccion']) && $_GET['setting_seccion'] == "exclusion-list-report-details") {
    include_once('safetag-report-page-details-bycampaign.php');
    exit;
}

if (isset($_GET['setting_seccion']) && $_GET['setting_seccion'] == "safetag-report-export") {
    include_once('safetag-report-export.php');
    exit;
}

if (isset($_GET['setting_seccion']) && $_GET['setting_seccion'] == "safetag-report-export-bykeyword") {
    include_once('safetag-report-export-bykeyword.php');
    exit;
}
$licence = json_decode( get_option('safetag_license_key'));
// $campaign_type = Safetag_Cron_Job::$campaign_type;

// $exclusion_list_paged = Safetag_Page_Helper::paged_generator(['Safetag_Db_Management', 'get_exclusion_list_records'], array('name', 'active', "created_at"), 20);
// $records = $exclusion_list_paged['result'];
// $post_count = Safetag_Db_Management::get_exclusion_list_post_count();

// if( !empty( $post_count['result'] ) ){
//   $count_result = [];
//   foreach ($post_count['result']  as $value) {
//     $count_result[$value->campaign_id] = $value->count;
//   }
// }
$licenceCheck = Safetag_Page_Helper::get_option_data_single('safetag_license_key', 'status');

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="safetag-wrap">

    <nav class="crumbs">
        <ol>
            <li class="crumb">
                <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            </li>
            <?php if($licenceCheck !== 'active'):?>
            <li class="crumb" style="float: right;">
                <h1 class="campaigns-free-version">Free Version</h1>
            </li>
            <?php endif;?>
        </ol>
    </nav>

    <span class="text-description">Create Campaigns to manage Brand Safety lists or target specific keywords for targeted ads. Safetag will pass the Campaign Name and List Type (Exclude or Include) as key value pairs for ad targeting.</span>

  <?php Safetag_Admin::safetag_admin_notice(); 
  $total_campaing = Safetag_Db_Management::total_campaing(); 
  
  if(intval($total_campaing[0]->total) < 1 || (!empty($licence) &&  $licence->status === 'active')) :  ?>

    <form method="GET" action="<?php echo Safetag_Admin::generate_url(
                                    [],
                                    esc_html($_GET['page'])
                                ); ?>">
        <input type="hidden" name="page" value="<?php echo esc_html($_GET['page']); ?>">
        <input type="hidden" name="setting_seccion" value="exclusion-list-edit">
        <div class="submit add-campaing-submit">
            <input type="submit" id="submit" class="button button-primary" value="ADD CAMPAIGN">
        </div>
    </form>
    <?php else:  ?>
        <p class="notice notice-info notice-large">You are using the free version of Safetag. You can create one Campaign up to 500 terms. To purchase the full version with unlimited campaigns and terms, please visit <a target="_blank" href="https://safetag.ai/">Safetag.ai</a></p>
    <?php endif; ?>
</div>

<div class="wrap add-campaing-table">
<form action="" method="post">
    <?php
    $table = new DisplayCampaingList();
    $table->prepare_items();
    $table->display();
    ?>

  </form>
</div>
