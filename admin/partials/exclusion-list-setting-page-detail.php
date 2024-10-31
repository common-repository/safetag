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
$restore = false;
$pid_exist = (isset($_GET['pid']) && trim($_GET['pid']) != '')? true:false;
if ($pid_exist) {

    $campaign = Safetag_Db_Management::get_exclusion_list_by_id(sanitize_text_field($_GET['pid']));

    if (isset($_GET['exclusion_list_history_id']) && trim($_GET['exclusion_list_history_id']) != '') {
        $campaign_detail = Safetag_Db_Management::get_exclusion_list_history_by_id(sanitize_text_field($_GET['exclusion_list_history_id']));
        $restore = true;
    } else {
        $campaign_detail = Safetag_Db_Management::get_exclusion_list_history_by_exclution_list_id(sanitize_text_field($_GET['pid']));
    }

    $exclusion_list_history_paged = Safetag_Page_Helper::paged_generator(['Safetag_Db_Management', 'get_exclusion_list_history_records_by_exclusion_list_id'], array('created_at', 'active'), 20, [sanitize_text_field($_GET['pid'])]);
    $records = $exclusion_list_history_paged['result'];

    $campaign_value = isset($campaign->type) ? $campaign->type : -1;
}
$total_campaing = Safetag_Db_Management::total_campaing();
$latest_campaing = Safetag_Db_Management::get_latest_campaing();
$licence = json_decode( get_option('safetag_license_key'));
$status_list = ['active'];
if(( !empty($licence) && in_array($licence->status, $status_list) == false && intval($total_campaing[0]->total) > 1)) {
  if(isset($_GET['pid']) && trim($_GET['pid']) != '') {
      if($latest_campaing[0]['id'] != $_GET['pid']) { // user can edit only latest camp
        wp_safe_redirect(admin_url('admin.php?page=exclusion-list-setting-page'));
      }
    }
}
?>


<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">

    <nav class="crumbs">
        <ol class="edit-campaign-item-main">           
            <div class="edit-campaign-item-left">
                <li class="crumb">
                    <h1>
                        <a href="<?php echo Safetag_Admin::generate_url(
                                        [],
                                        esc_html($_GET['page'])
                                    ); ?>"><?php echo esc_html(get_admin_page_title()); ?></a>

                    </h1>
                </li>
                <li class="crumb">
                    <h1><?php echo $pid_exist?($restore ? 'Restore' : 'Edit'):'Add' ?></h1>
                </li>
            </div>
            <div class="edit-campaign-item-right">
                <?php if(isset($campaign)):?>
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
                                            "pid" => $campaign->id,
                                            "slice_number" => 0
                                        ],
                                        esc_html($_GET['page'])
                                    ); ?>">
                            <h1>report</h1>
                        </a>
                    </div>
                </div>
            <?php endif?>
            </div>
        </ol>
    </nav>

    <span class="text-description">Safetag will find each unique keyword or phrase on all post types and pass the Campaign Name and List Type (Exclude or Include) as key value pairs for brand safety and ad targeting.</span>

    <?php Safetag_Admin::safetag_admin_notice() ?>

    <form method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="safetag_exclusion_list_form_response">
        <input type="hidden" name="page" value="<?php echo esc_html($_GET['page']); ?>">
        <input type="hidden" name="setting_seccion" value="<?php echo esc_html($_GET['setting_seccion']); ?>">
        <input type="hidden" name="safetag_exclusion_list_form_nonce" value="<?php echo wp_create_nonce('safetag_exclusion_list_form_nonce') ?>" />
        <input type="hidden" name="pid" value="<?php echo isset($campaign->id) ? esc_html($campaign->id) : '' ?>">

        <ul class="flex-outer">
            <li>
                <label for="campaign_name">
                    <span class="bold">Campaign Name <span class="safetag-required">*</span></span>
                </label>
                <input name="nds[safetag_campaign_name]" id="campaign_name" required type="text" value="<?php echo isset($campaign->name) ? esc_html($campaign->name) : '' ?>" class="regular-text" />
                <div class="flex-end">This Key Value will be passed to target ads</div>
            </li>
            <li>
                <label for="campaign_name">
                    <span class=" bold">List type <span class="safetag-required">*</span></span>
                </label>

                <div class="safetag-flex-container safetag-align-middle safetag-align-justify">
                    <div>
                        <label class="container">Exclude
                            <input type="radio" name="nds[safetag_type]" <?php echo isset($campaign_value) ? ($campaign_value == 0 ? 'checked="checked"' : '') : 'checked="checked"'; ?> value="0">
                            <span class="checkmark"></span>
                        </label>

                        <label class="container">Include
                            <input type="radio" name="nds[safetag_type]" <?php echo isset($campaign_value) ? ($campaign_value == 1 ? 'checked="checked"' : '') : ''; ?> value="1">
                            <span class="checkmark"></span>
                        </label>
                    </div>
                    <div class="safetag-right-spacer">
                        <input type="button" name="import_button" id="import_button" class="button button-primary safetag-import-button" value="Import ( csv/xls/xlsx )">
                        <input type="file" name="safetag_file_import" id="safetag_file_import" accept=".csv,.xls,.xlsx" hidden>
                        <!-- <label for="import_button">
                            <div id="safetag-file-name-label" class="safetag-file-name-label"></div>
                        </label> -->
                    </div>
                </div>
            </li>
            <li>
                <label for="keywords" class="bold" style="align-self: flex-start;">Keywords <span class="safetag-required">*</span></label>
                <div>
                    <div class="safetag-editor" id="editor-number" style="min-width: auto;">
                        <textarea required class="safetag-textarea" rows="10" cols="40" id="keywords" name="nds[safetag_keywords]" style="overflow-y: auto;"><?php echo isset($campaign_detail->keywords) ? esc_html($campaign_detail->keywords) : ''; ?></textarea>
                    </div>
                </div>
                <div class="flex-end">Enter one word or phrase per line
                    <br>
                    <br>
                    These terms will be used to determine if the content
                    meets the list requirements.
                </div>
            </li>
            <li class="safetag-status-section">
                <label for="status" class="bold">Status<span class="safetag-required">*</span></label>
                <div>
                    <label class="switch">
                        <input id="status" type="checkbox" name="nds[safetag_active]" <?php echo (isset($campaign->active) ? !empty($campaign->active) : true) != '' ? ' checked="checked" ' : ''; ?>>
                        <span class="slider round"></span>
                        <span class="switch-active" aria-hidden="true">On</span>
                        <span class="switch-inactive" aria-hidden="true">Off</span>
                    </label>
                </div>
            </li>
            <li class="safetag-save-section">
                <div class="save-section-label">
                <label for="status" class="bold"></label>
                </div>
                <div class="save-section-button">
                    <?php if (!$restore) { ?>
                        <input type="submit" name="submit" id="submit" class="button button-primary submit-button" value="Save">
                        <!-- <div style="display: inline;">
                            <input type="button" name="import_button" id="import_button" class="button button-primary safetag-import-button" value="Import">
                            <input type="file" name="safetag_file_import" id="safetag_file_import" accept=".csv" hidden>
                            <label for="import_button">
                                <div id="safetag-file-name-label" class="safetag-file-name-label"></div>
                            </label>
                        </div> -->

                    <?php } else { ?>
                        <form method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display: inline;">
                            <input type="hidden" name="action" value="safetag_exclusion_list_form_restore_response">
                            <input type="hidden" name="safetag_exclusion_list_form_restore_nonce" value="<?php echo wp_create_nonce('safetag_exclusion_list_form_restore_nonce') ?>" />
                            <input type="hidden" name="page" value="<?php echo esc_html($_GET['page']); ?>">
                            <input type="hidden" name="setting_seccion" value="<?php echo esc_html($_GET['setting_seccion']); ?>">
                            <input type="hidden" name="nds[exclusion_list_history_id]" value="<?php echo esc_html($campaign_detail->id); ?>">
                            <input type="hidden" name="nds[pid]" value="<?php echo isset($campaign->id) ? esc_html($campaign->id) : ''; ?>">
                            <input type="submit" name="submit" id="submit" class="button button-primary" value="RESTORE">
                        </form>
                    <?php } ?>
                </div>
                <div class="safetag-save-message" style="">
                On “Save”, Safetag will scan all post types.
                This will take significant time based on number of posts, content length and number of Campaign Keywords.
                </div>
            </li>
        </ul>

    </form>


    <div class="loginPopup">
      <div class="formPopup" id="popupForm">
        <form action="" class="formContainer">
          <h2>Import Options</h2>
          <label for="keywordsDropdown">
            <strong>Keyword Column:</strong>
          </label>
          <select name="keywordsDropdown" id="keywordsDropdown">
          </select><br>
          <input type="checkbox" id="include2" name="vehicle1" value="No">
          <label for="include2">  Exclude Header from Import</label><br>
          <button type="button" id="ok" class="btn cancel" >Import</button>
        </form>
      </div>
    </div>
    <style>
        * {
        box-sizing: border-box;
      }
      .openBtn {
        display: flex;
        justify-content: left;
      }
      .openButton {
        border: none;
        border-radius: 5px;
        background-color: #1c87c9;
        color: white;
        padding: 14px 20px;
        cursor: pointer;
        position: fixed;
      }
      .loginPopup {
        position: relative;
        text-align: center;
        width: 100%;
      }
      .formPopup {
        display: none;
        position: fixed;
        left: 50%;
        top: 10%;
        -webkit-transform: translateX(-50%);
        -ms-transform: translateX(-50%);
        transform: translateX(-50%);
        box-shadow: 0 0 3px 0 #999999;
        z-index: 9;
        background-color: #fff;
        max-width: 450px;
        width: 100%;
      }
      .formPopup h2{
        margin-top: 0;
        margin-bottom: 1rem;
      }
      .formContainer {
        padding: 20px;
        background-color: #fff;
      }
      .formContainer label{
        margin-bottom: 6px;
        display: inline-block;
      }
      .formContainer select{
        margin-bottom: 1rem;
      }
      .formContainer input[type=text],
      .formContainer input[type=password] {
        width: 100%;
        padding: 15px;
        margin: 5px 0 20px 0;
        border: none;
        background: #eee;
      }
      .formContainer input[type=text]:focus,
      .formContainer input[type=password]:focus {
        background-color: #ddd;
        outline: none;
      }
      .formContainer .btn {
        padding: 12px 20px;
        border: none;
        background-color: #8ebf42;
        color: #fff;
        cursor: pointer;
        width: 100%;
        opacity: 0.8;
        max-width: 160px;
        margin-top: 1rem;
      }
      .formContainer .cancel {
        background-color: #cc0000;
      }
      .formContainer .btn:hover,
      .openButton:hover {
        opacity: 1;
      }
    </style>

    <h2>Change History </h2>
    <div class="pagination">
        <?php echo isset($exclusion_list_history_paged['paged-link']) ? wp_kses($exclusion_list_history_paged['paged-link'] , ['a' =>['href' => [], 'title'=>[]], 'ul' =>['class' => []], 'li' => ['class'=> []]]) : '' ?>
    </div>
    <table class="wp-list-table widefat fixed striped table-view-list" >
        <thead>
            <tr>
                <th><strong>Saved</strong></th>
                <th><strong>User</strong></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (isset($records)) {
                foreach ($records as $key => $value) { ?>
                    <tr>
                        <td><?php echo get_date_from_gmt($value->created_at, 'm-d-Y | g:i a') ?></td>
                        <td><?php echo get_user_by('ID', $value->wp_user_id)->display_name ?></td>
                        <td>
                            <a href="<?php echo Safetag_Admin::generate_url(
                                            [
                                                "setting_seccion" => "exclusion-list-edit",
                                                "pid" => $campaign->id,
                                                "exclusion_list_history_id" => $value->id
                                            ],
                                            esc_html($_GET['page'])
                                        ); ?>">VIEW</a>
                            |
                            <form method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display: inline;">
                                <input type="hidden" name="action" value="safetag_exclusion_list_form_restore_response">
                                <input type="hidden" name="safetag_exclusion_list_form_restore_nonce" value="<?php echo wp_create_nonce('safetag_exclusion_list_form_restore_nonce') ?>" />
                                <input type="hidden" name="page" value="<?php echo esc_html($_GET['page']); ?>">
                                <input type="hidden" name="setting_seccion" value="<?php echo esc_html($_GET['setting_seccion']) ?>">
                                <input type="hidden" name="nds[exclusion_list_history_id]" value="<?php echo esc_html($value->id) ?>">
                                <input type="hidden" name="nds[pid]" value="<?php echo isset($campaign->id) ? esc_html($campaign->id) : '' ?>">
                                <button type="submit" name="restore_button" value="restore" class="btn-link">RESTORE</button>
                            </form>
                        </td>
                    </tr>
            <?php }
            } ?>
        </tbody>
    </table>
</div>
