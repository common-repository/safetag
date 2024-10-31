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
        $campaign_keyword = sanitize_text_field($_GET['keyword']);
        $campaign = Safetag_Db_Management::get_alphabetic_keywords_match_detailsbykeyword($pid, $campaign_keyword);
        $campaign = $campaign['result'];
        $delimiter = ","; 
        
        //replace with underscore
        $campaign_name = str_replace(' ', '_', $campaign_name);
        $campaign_keyword = str_replace(' ', '_', $campaign_keyword);
        $filename = $campaign_name . "-" . $campaign_keyword . ".csv"; 
            
        // Create a file pointer 
        $f = fopen('php://memory', 'w'); 
            
        // Set column headers 
        $fields = array('Title','Date'); 
        fputcsv($f, $fields, $delimiter); 

        foreach ( $campaign as $key => $value ) {
            if(!isset($value->post_id)) continue;            
            $lineData =array(get_the_title($value->post_id), get_the_date('F d, Y',$value->post_id));
            fputcsv($f, $lineData, $delimiter);
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