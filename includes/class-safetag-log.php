<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    safetag
 * @subpackage safetag/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    safetag
 * @subpackage safetag/includes
 * @author     Your Name <email@example.com>
 */
class Safetag_Log
{

    /**
     * This method will be fired when th plugin has been activated.
     *
     * @since    1.0.0
     */
    public static function add($entry, $mode = 'a', $file_name = 'safetag_log_plugin')
    {
        $plugin_directory = WP_PLUGIN_DIR . '/safetag-plugin';

        if (is_dir($plugin_directory)) {
            // If the entry is array, json_encode.
            if (is_array($entry)) {
                $entry = json_encode($entry);
                // Write the log file.
                $file  = $plugin_directory . '/' . $file_name . '.log';
                $file  = fopen($file, $mode);
                $bytes = fwrite($file, $entry . "\n");
                fclose($file);

                return $bytes;
            }
        }
    }

    public static function get_latest_record($file_name = 'safetag_log_plugin')
    {
        $plugin_directory = WP_PLUGIN_DIR . '/safetag-plugin';
        if (is_dir($plugin_directory)) {
            // If the entry is array, json_encode.

            // Write the log file.
            $file  = $plugin_directory . '/' . $file_name . '.log';

            if (!is_file($file)) {
                $contents = '';           // Some simple example content.
                file_put_contents($file, $contents);     // Save our content to the file.
                return "";
            } else {
                $data = file($file);
                if (count($data) <= 0) {
                    return "";
                } else {
                    return $data[count($data) - 1];
                }
            }
        }
    }
}
