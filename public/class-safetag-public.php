<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    safetag
 * @subpackage safetag/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    safetag
 * @subpackage safetag/public
 * @author     Your Name <email@example.com>
 */
class Safetag_Public
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $safetag    The ID of this plugin.
	 */
	private $safetag;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $safetag       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($safetag, $version)
	{

		$this->safetag = $safetag;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Safetag_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Safetag_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->safetag, plugin_dir_url(__FILE__) . 'css/safetag-public.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Safetag_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Safetag_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->safetag, plugin_dir_url(__FILE__) . 'js/safetag-public.js', array('jquery'), $this->version, false);
		$post_id = get_the_ID();

		$Safetag_API = new Safetag_API();
		$get_safetag_post_setting = $Safetag_API->get_safetag_post_setting(array('post_id' => $post_id));

		wp_localize_script($this->safetag, 'safetag_fpd', $get_safetag_post_setting['safetag_fpd']);
		wp_localize_script($this->safetag, 'safetag_lists', $get_safetag_post_setting['safetag_lists']);
	}
}
