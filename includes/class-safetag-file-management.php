<?php

/**
 * Description
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    safetag
 * @subpackage safetag/includes
 */

/**
 * description .
 *
 *
 *
 * @since      1.0.0
 * @package    safetag
 * @subpackage safetag/includes
 * @author     Your Name <email@example.com>
 */
class Safetag_File_Managemenet
{
    private static $ads_txt_file_path =  "ads.txt";


    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function get_ads_txt_file_content()
    {
        $file_path = get_home_path() . self::$ads_txt_file_path;
        $result = self::check_file_and_directory_permission($file_path);
        if (!$result['created']) {
            return $result['message'];
        } else {
            $file = fopen($file_path, "r");
            $file_size = filesize($file_path);
            $content = fread($file, $file_size == 0 ? 1 : $file_size);
            fclose($file);
            return $content;
        }
    }

    public static function check_file_and_directory_permission($file_path = '')
    {
        if ($file_path == '') {
            $file_path = get_home_path() . self::$ads_txt_file_path;
        }

        $created = true;
        $message = '';
        if (file_exists($file_path) && !is_writable($file_path)) {
            $created = false;
            $message = "file permission required!";
        }
        // suppressed warnings
        $handle = @fopen($file_path, "a");
        if ($handle !== false) {
            fclose($handle);
        } else {
            $created = false;
            $message = "Could not open the file!";
        }

        return array("created" => $created, "message" => $message);
    }

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function write_ads_txt_file($content)
    {
        $file_path = get_home_path() . self::$ads_txt_file_path;

        $result = self::check_file_and_directory_permission($file_path);
        if (!$result['created']) {
            return false;
        } else {

            $file = fopen($file_path, "w");
            fwrite($file, $content);
            fclose($file);
            return true;
        }
    }
}
