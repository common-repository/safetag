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
if (isset($_GET['setting_seccion']) && $_GET['setting_seccion'] == "adstext-edit") {
    include_once('safetag-adtxt-setting-detail-page.php');
    exit;
}

$file_permission_result = Safetag_File_Managemenet::check_file_and_directory_permission();

$ads_txt_data = Safetag_Page_Helper::paged_generator(['Safetag_Db_Management', 'get_ads_text_records'], array('updated_at', 'active', "notes"), 20);
$records = $ads_txt_data['result'];
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class=wrap">


    <nav class="crumbs">
        <ol>
            <li class="crumb">
                <h1>
                    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

                </h1>
            </li>
        </ol>
    </nav>


    <p class="text-description">Editing this file requires root access to the server. This file should only be edited by users that understand the requirements and formatting restrictions.</p>

    <?php Safetag_Admin::safetag_admin_notice() ?>

    <?php if (!$file_permission_result['created']) { ?>
        <div class="error">
            <p>Could not create ads.txt file, check server permissions</p>
        </div>
    <?php } ?>

    <table class=" wp-list-table widefat fixed striped table-view-list mb-20">
        <thead>
            <tr>
                <th class="safetag-min-col"><strong>Date / Time</strong></th>
                <th><strong>User</strong></th>
                <th class="safetag-narrow-col"><strong>Active</strong></th>
                <th><strong>Notes</strong></th>
                <th class="safetag-narrow-col">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($records as $key => $value) { ?>
                <tr>
                    <td><?php echo esc_html( date('m-d-Y | g:i a', strtotime($value->updated_at))) ?></td>
                    <td><?php echo esc_html( get_user_by('ID', $value->wp_user_id)->display_name); ?></td>
                    <td> <i class="<?php echo $value->active ? 'fa fa-check-circle green' : '' ?>"></i></td>
                    <td><?php echo esc_html($value->notes) ?></td>
                    <td>
                        <?php if ($value->active) {
                        ?>
                            <a href="<?php echo Safetag_Admin::generate_url(
                                            [
                                                "setting_seccion" => "adstext-edit",
                                                "pid" => $value->id
                                            ],
                                            esc_html($_GET['page'])
                                        ); ?>">EDIT</a>
                        <?php } else { ?>
                            <form method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                                <input type="hidden" name="action" value="safetag_ads_text_form_restore_response">
                                <input type="hidden" name="page" value="<?php echo esc_html($_GET['page']); ?>">
                                <input type="hidden" name="safetag_ads_text_restore_form_nonce" value="<?php echo wp_create_nonce('safetag_ads_text_restore_form_nonce') ?>" />
                                <input type="hidden" name="nds[pid]" value="<?php echo esc_html($value->id); ?>">
                                <button type="submit" name="restore_button" value="restore" class="btn-link">RESTORE</button>
                            </form>
                        <?php } ?>

                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php echo wp_kses($ads_txt_data['paged-link'], ['a' =>['href' => [], 'title'=>[]], 'ul' =>['class' => []], 'li' => ['class'=> []]]) ?>
    </div>
</div>
