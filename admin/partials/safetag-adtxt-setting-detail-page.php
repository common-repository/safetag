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
    $record = Safetag_Db_Management::get_ads_text_record_by_id(sanitize_text_field($_GET['pid']));
}

if ((isset($_GET['errors']))) {
    $errors = sanitize_text_field( $_GET['errors']);

    //Process the text to add into textarea
    $text = json_decode($_SESSION['text']);
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
                <h1>Edit</h1>
            </li>
        </ol>
    </nav>

    <span class="text-description">Editing this file requires root access to the server. This file should only be edited by users that understand the requirements and formatting restrictions.</span>
    
    <?php if(isset($errors)) { ?>
        <div class="error">
            <p>There are errors in the text.</p>
        </div>
        <br>
    <?php } ?>

    <?php if ($record == null) { ?>
        <div class="error">
            <p>Record not found.</p>
        </div>
    <?php exit;
    }
    ?>



    <form method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="safetag_ads_text_form_response">
        <input type="hidden" name="page" value="<?php echo esc_html($_GET['page']); ?>">
        <input type="hidden" name="safetag_ads_text_form_nonce" value="<?php echo wp_create_nonce('safetag_ads_text_form_nonce') ?>" />

        <div class="safetag-wrap-row">
            <div class="">
                <div class="safetag-editor" id="editor">
                    <div class="gutter"><span>1</span></div>
                    <textarea required class="safetag-textarea" rows="10" cols="40" id="nds[safetag_ads_txt_content_file]" name="nds[safetag_ads_txt_content_file]">
                        <?php 
                        if (isset($errors)) {
                            echo esc_html(implode("\n", $text));
                        } else {
                            echo $record->active
                                ? esc_html(safetag_File_Managemenet::get_ads_txt_file_content())
                                : $record->ads_txt_content_file;
                        }
                        ?>
                    </textarea>
                </div>
            </div>

            <div>
                <div class="bold">Errors</div>
                <?php if (isset($errors)) : ?>
                    <?php foreach ($errors as $error) { ?>

                        <!-- <div class="adstxt-error-item">Line 2: invalid record</div>
                <div class="adstxt-error-item">Line 7: formating</div> -->
                        <div class="adstxt-error-item"><?php echo esc_html( $error); ?></div>

                    <?php } ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="safetag-wrap-row">
            <div class="notes-space">
                <label for="safetag_notes">
                    <input required autocomplete="off" name="nds[safetag_notes]" id="safetag_notes" type="text" value="<?php echo esc_html($record->notes); ?>" class="regular-text" />
                    <span class="bold">Notes<span class="safetag-required">*</span></span>
                </label>
            </div>
            <span class="enable-section bold">Active: <i class="<?php echo $record->active ? 'fa fa-check-circle green' : 'fa fa-check-circle default' ?>"></i></span>
        </div>

        <?php if ($_GET['pid']) : ?>
            <input id="pid" name="pid" type="hidden" value="<?php echo esc_html($_GET['pid']); ?>">
        <?php endif; ?>

        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Save">
        </p>

    </form>
</div>
