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
        $pid = sanitize_text_field($_GET['pid']);
        $campaignData = Safetag_Db_Management::get_exclusion_list_by_id($pid);
        $campaign_name = isset($campaignData->name) ? $campaignData->name : '';
        $campaign = Safetag_Db_Management::get_alphabetic_keywords_match_stat_bycampaign_id($pid);
        $campaign = $campaign['result'];
        // $alphabetic_stat_total = Safetag_Db_Management::get_total_keywords_match_stat_bycampaign_id($pid);
        // $alphabetic_stat_total_records = $alphabetic_stat_total['result'];
        // foreach ($alphabetic_stat_total_records as $key => $value) {
        //     $total_keywords = $value -> total;
        // }

        $campaign_post_report = Safetag_Db_Management::get_total_campaign_post_report_bycampaign_id($pid);
        $total_post = $campaign_post_report['safe'] + $campaign_post_report['excluded'] + $campaign_post_report['update_required'];

        $campaign_keywords_detail = Safetag_Db_Management::get_exclusion_list_history_by_exclution_list_id($pid);
        $campaign_keywords_detail_array = explode(PHP_EOL, $campaign_keywords_detail->keywords);
        $delimiter = ","; 
        //replace with underscore
        $campaign_name = str_replace(' ', '_', $campaign_name);
        $filename = $campaign_name . ".csv"; 
            
        // Create a file pointer 
        $f = fopen('php://memory', 'w'); 
            
        // Set column headers 
        $fields = array('Term','Matches','%'); 
        fputcsv($f, $fields, $delimiter); 
        if (isset($_GET['all'])) {
            foreach ( $campaign_keywords_detail_array as $value ) {
                if ($value != NULL || $value != '') {
                    $t = Safetag_Db_Management::get_alphabetic_keywords_match_stat_bycampaign_id_and_keyword($pid, trim($value));
                    $t = $t['result']; 
                    foreach($t as $key => $value2){$t_total = $value2->total;}
                    $count = Safetag_Page_Helper::get_number_format(esc_html($t_total));
                    $percentage = Safetag_Page_Helper::get_number_percentage($t_total, $total_post);
                    $lineData =array($value, $count, $percentage);
                    fputcsv($f, $lineData, $delimiter);
                }
            }
        } else {
            foreach ( $campaign as $key => $value ) {
                if ($value->keywords != NULL || $value->keywords != '') {
                    $count = Safetag_Page_Helper::get_number_format(esc_html($value->total));
                    $percentage = Safetag_Page_Helper::get_number_percentage($value->total, $total_post);
                    $lineData = array($value->keywords, $count, $percentage);
                    fputcsv($f, $lineData, $delimiter);
                }
            }
        }
            
        // Move back to beginning of file 
        ob_clean();
        ob_start();
        fseek($f, 0); 
        // Set headers to download file rather than displayed 
        header('Content-Type: text/csv'); 
        header('Content-Disposition: attachment; filename="' . $filename . '";'); 
        header('Pragma: no-cache');
        header('Expires: 0');
        //output all remaining data on a file pointer 
        fpassthru($f);
        fclose($f);
        exit;
    }
?>