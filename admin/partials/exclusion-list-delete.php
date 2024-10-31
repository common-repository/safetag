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

if ((isset($_GET['pid']) && trim($_GET['pid']) != '')) {
    $campaign = Safetag_Db_Management::get_exclusion_list_by_id(sanitize_text_field($_GET['pid']));
    $campaign_name = isset($campaign->name) ? $campaign->name : '';
}
?>


<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="safetag-wrap">

    <nav class="crumbs">
        <ol>
            <li class="crumb">
                <h1>
                    <a href="<?php echo Safetag_Admin::generate_url(
                                    [],
                                    esc_html($_GET['page'])
                                ); ?>"><?php echo esc_html(get_admin_page_title()); ?></a>

                </h1>
            </li>
            <li class="crumb">
                <h1>Delete</h1>
            </li>
        </ol>
    </nav>

    <span class="text-description">Are you sure you want to delete this Campaign Permanently?</span>


    <form method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="safetag_exclusion_delete_form_response">
        <input type="hidden" name="page" value="<?php echo esc_html($_GET['page']); ?>">
        <input type="hidden" name="setting_seccion" value="<?php echo esc_html($_GET['setting_seccion']); ?>">
        <input type="hidden" name="safetag_exclusion_delete_form_nonce" value="<?php echo wp_create_nonce('safetag_exclusion_delete_form_nonce') ?>" />
        <input type="hidden" name="pid" value="<?php echo isset($campaign->id) ? esc_html($campaign->id) : '' ?>">

        <ul class="flex-outer">
            <li>
                <label for="status" class="bold"></label>
                <div>
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="DELETE">
                        <a class="button button-primary" href="<?php echo Safetag_Admin::generate_url(
                      [],
                      esc_html($_GET['page'])
                  ); ?>">CANCEL</a>                    
                </div>
            </li>
        </ul>

    </form>

</div>