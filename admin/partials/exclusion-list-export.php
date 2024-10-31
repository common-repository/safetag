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
        $campaign = Safetag_Db_Management::get_post_campain_by_campaign_id($pid);
        $delimiter = ","; 
        $filename = $campaign_name . ".csv"; 
            
        // Create a file pointer 
        $f = fopen('php://memory', 'w'); 
            
        // Set column headers 
        $fields = array('Post ID','Keywords','Post Status'); 
        fputcsv($f, $fields, $delimiter); 

        foreach ( $campaign as $campaignvalue ) {
            if ($campaignvalue->keywords != NULL || $campaignvalue->keywords != ''){
                $lineData =array($campaignvalue->post_id,$campaignvalue->keywords,get_post_status($campaignvalue->post_id));
                fputcsv($f, $lineData, $delimiter);
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