<?php

/**
 * Safetag license management.
 *
 * @since      1.0.0
 * @package    safetag
 * @subpackage safetag/includes
 * @author     Your Name <email@example.com>
 */
class Safetag_License
{

    /**
     *
     * Activate safetag license key
     *
      * @param string $safetag_license_key safetag license key
     */
    public static function activate($safetag_license_key)
    {
        try {
            return Safetag_License::validator($safetag_license_key, 'slm_activate');
        } catch (Exception $e) {
            return (object)array(
                "result" => "error",
                "message" =>  $e->getMessage(),
                "error_code" => 500
            );
        }
    }

    /**
     *
     * Deactivation safetag license key
     *
     * @param string $safetag_license_key safetag license key
     */
    public static function deactivation($safetag_license_key)
    {
        try {
            return Safetag_License::validator($safetag_license_key, 'slm_deactivate');
        } catch (Exception $e) {
            return (object)array(
                "result" => "error",
                "message" =>  $e->getMessage(),
                "error_code" => 500
            );
        }
    }

    /**
     *
     * Checks safetag license key
     *
     * @param string $safetag_license_key safetag license key
     */
    public static function check($safetag_license_key)
    {
        try {
            return Safetag_License::validator($safetag_license_key, 'slm_check');
        } catch (Exception $e) {
            return (object)array(
                "result" => "error",
                "status" => "invalid",
                "message" =>  $e->getMessage(),
                "error_code" => 500
            );
        }
    }

    /**
     *
     * Safetag license key validator
     *
     * @param string $safetag_license_key safetag license key
     * @param string $slm_action  api action(slm_activate, slm_deactivate, slm_check)
     */
    private static function validator($safetag_license_key, $slm_action)
    {
        // API query parameters
        $api_params = array(
            'slm_action' => $slm_action,
            'secret_key' => SAFETAG_SPECIAL_SECRET_KEY,
            'license_key' => $safetag_license_key,
            'registered_domain' => sanitize_text_field($_SERVER['SERVER_NAME']),
            'item_reference' => urlencode(SAFETAG_ITEM_REFERENCE),
            'time' => time()
        );

        // Send query to the license manager server
        $query = esc_url_raw(add_query_arg($api_params, SAFETAG_LICENSE_SERVER_URL));

        $response = wp_remote_get($query, array('timeout' => 20, 'sslverify' => false));

        // Check for error in the response
        if (is_wp_error($response)) {
            throw new Exception('Unexpected Error! The query returned with an error');
        }

        return json_decode(wp_remote_retrieve_body($response));
    }
}
