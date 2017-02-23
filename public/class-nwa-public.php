<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.navionics.com
 * @since      1.0.0
 *
 * @package    Nwa
 * @subpackage Nwa/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Nwa
 * @subpackage Nwa/public
 * @author     Alessandro Staniscia <astaniscia@navionics.com>
 */
class Nwa_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;


	/**
	 * Manage the conditiional load of webapi
	 *
	 * @var short_code used for the conditional load of scripts
	 */
	private $short_code;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $shortcode ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->short_code=$shortcode;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Nwa_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Nwa_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/nwa-public.css', array(), $this->version, 'all' );



	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Nwa_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Nwa_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/nwa-public.js', array( 'jquery' ), $this->version, false );

	}


	public function condition_enqueue_styles(){
		global $post;
		//print_r($this->short_code);
		if( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, $this->short_code) ) {
			wp_enqueue_style(  $this->plugin_name.'-navionics-webapi-style' , "//webapiv2.navionics.com/dist/webapi/webapi.min.css", array(), $this->version );
		}
	}

	public function condition_enqueue_scripts(){
		global $post;
		if( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, $this->short_code) ) {
			wp_enqueue_script( $this->plugin_name.'-navionics-webapi-scripts' , '//webapiv2.navionics.com/dist/webapi/webapi.min.no-dep.js', array(), $this->version, false );
		}
	}

}
