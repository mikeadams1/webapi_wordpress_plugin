<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.navionics.com
 * @since      1.0.0
 *
 * @package    Nwa
 * @subpackage Nwa/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Nwa
 * @subpackage Nwa/admin
 * @author     Alessandro Staniscia <astaniscia@navionics.com>
 */
class Nwa_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ,$shortcode) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->short_code=$shortcode;


	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/nwa-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/nwa-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Manage the Shortcode
	 *
	 * @param $atts
	 * @since 1.0.0
	 */
	public function manage_short_code( $atts ) {

		// Attributes
		$atts = shortcode_atts(
			array(
				'item' => null,
				'classname' => 'test_map_div',
				'css_code' => 'width: 100%; height: 400px',
				'js_code' => 'var webapi = new JNC.Views.BoatingNavionicsMap({ tagId: \'.test_map_div\', center: [ 12.0, 46.0 ], navKey: \'Navionics_support_00001\' });',
			),
			$atts,
			$this->short_code
		);

		if ($atts['item'] != null ){
			$atts['classname']  = get_post_meta($atts['item'],'navionics_webapi_class_name',true);
			$atts['css_code']  = esc_js(get_post_meta($atts['item'],'navionics_webapi_style_content',true));
			$atts['js_code']  = (get_post_meta($atts['item'],'navionics_webapi_code_content',true));
		}

		ob_start();
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/nwa-scripts-template.php';
		return ob_get_clean();
	}

	/**
	 * Register Custom Post Type
	 *
	 */
	function map_post_type() {

		$labels = array(
			'name'                  => _x( 'Navionics WebAPIs v2', 'Post Type General Name', 'nwa' ),
			'singular_name'         => _x( 'navionics WebAPI v2', 'Post Type Singular Name', 'nwa' ),
			'menu_name'             => __( 'Navionics WebAPIv2', 'nwa' ),
			'name_admin_bar'        => __( 'Navionics WebAPIv2', 'nwa' ),
			'archives'              => __( 'Component Archives', 'nwa' ),
			'attributes'            => __( 'Component Attributes', 'nwa' ),
			'parent_item_colon'     => __( 'Parent Component:', 'nwa' ),
			'all_items'             => __( 'All Components', 'nwa' ),
			'add_new_item'          => __( 'Add New Component', 'nwa' ),
			'add_new'               => __( 'Add New', 'nwa' ),
			'new_item'              => __( 'New Component', 'nwa' ),
			'edit_item'             => __( 'Edit Component', 'nwa' ),
			'update_item'           => __( 'Update Component', 'nwa' ),
			'view_item'             => __( 'View Component', 'nwa' ),
			'view_items'            => __( 'View Components', 'nwa' ),
			'search_items'          => __( 'Search Component', 'nwa' ),
			'not_found'             => __( 'Not found', 'nwa' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'nwa' ),
			'featured_image'        => __( 'Featured Image', 'nwa' ),
			'set_featured_image'    => __( 'Set featured image', 'nwa' ),
			'remove_featured_image' => __( 'Remove featured image', 'nwa' ),
			'use_featured_image'    => __( 'Use as featured image', 'nwa' ),
			'insert_into_item'      => __( 'Insert into component', 'nwa' ),
			'uploaded_to_this_item' => __( 'Uploaded to this component', 'nwa' ),
			'items_list'            => __( 'Components list', 'nwa' ),
			'items_list_navigation' => __( 'Components list navigation', 'nwa' ),
			'filter_items_list'     => __( 'Filter components list', 'nwa' ),
		);
		$args = array(
			'label'                 => __( 'Navionics WebAPIv2', 'nwa' ),
			'description'           => __( 'Add a new feature provided by Navionics WebAPI', 'nwa' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'author', ),
			'taxonomies'            => array( 'maps', 'catalog' ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'menu_icon'             => 'dashicons-admin-site',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'capability_type'       => 'page',
			'show_in_rest'          => false,
		);
		register_post_type( 'navionics_webapi', $args );
	}

}
