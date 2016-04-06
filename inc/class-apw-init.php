<?php

/**
 * Advanced_Posts_Widget Class
 *
 * Initializes the plugin
 *
 * @package Advanced_Posts_Widget
 *
 * @since 1.0
 *
 */

// No direct access
if( ! defined( 'ABSPATH' ) ){
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}


class APW_Init
{

	/**
	 * Full file path to plugin file
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	protected $file = '';


	/**
	 * URL to plugin
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	protected $url = '';


	/**
	 * Filesystem directory path to plugin
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	protected $path = '';


	/**
	 * Base name for plugin
	 *
	 * e.g. "advanced-posts-widget/apw.php"
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	protected $basename = '';


	/**
	 * Constructor
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param string $file Full file path to calling plugin file
	 */
	public function __construct( $file )
	{
		$this->file	    = $file;
		$this->url	    = plugin_dir_url( $this->file );
		$this->path	    = plugin_dir_path( $this->file );
		$this->basename = plugin_basename( $this->file );
	}


	/**
	 * Loads the class
	 *
	 * @uses APW_Init::load_widget()
	 * @uses APW_Init::load_admin_scripts()
	 *
	 * @access public
	 *
	 * @since 1.0
	 */
	public function init()
	{
		$this->init_widget();
		$this->init_admin_scripts();
		$this->store_image_sizes();
		$this->init_image_sizes();
	}


	/**
	 * Loads the Comment Widget
	 *
	 * @uses APW_Init::register_widget()
	 *
	 * @access public
	 *
	 * @since 1.0
	 */
	public function init_widget()
	{
		add_action( 'widgets_init', array( $this, 'register_widget' ) );
	}

	/**
	 * Registers the Comment Widget
	 *
	 * @uses WordPress\register_widget()
	 *
	 * @access public
	 *
	 * @since 1.0
	 */
	public function register_widget()
	{
		register_widget( 'APW_Widget' );
	}


	/**
	 * Loads js/css admin scripts
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function init_admin_scripts()
	{
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'admin_styles' ) );
	}


	/**
	 * Loads js admin scripts
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function admin_scripts( $hook )
	{
		global $pagenow;

		$enqueue = false;

		if( 'customize.php' == $pagenow || 'widgets.php' == $pagenow || 'widgets.php' == $hook ){
			$enqueue = true;
		};

		if( ! $enqueue ){
			return;
		};

		wp_enqueue_script( 'apw-admin-scripts', $this->url . 'js/admin.js', array( 'jquery' ), '', true );

		$sample_excerpt = APW_Utils::sample_excerpt();

		wp_localize_script(
			'apw-admin-scripts',
			'apw_script_vars',
			array(
				'sample_excerpt' => __( $sample_excerpt )
			)
		);

	}


	/**
	 * Prints out css styles in admin head
	 *
	 * Note: Only loads on customize.php or widgets.php
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function admin_styles()
	{
		wp_enqueue_style( 'apw-admin-styles', $this->url . 'css/admin.css', null, null );
	}


	/**
	 * Registers Additional Image Sizes
	 *
	 * @see APW_Init::add_image_sizes()
	 *
	 * @access public
	 *
	 * @since 1.0
	 */
	public function store_image_sizes()
	{
		add_action( 'apw_update_widget', array( $this, 'add_image_sizes' ), 0, 4 );
		add_action( 'customize_save_widget_apw-recent-posts', array( $this, 'add_image_sizes' ), 0, 1 );
	}


	/**
	 * Stores image sizes as option
	 *
	 * @uses APW_Utils::stick_image_size()
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param object $widget Widget|WP_Customize_Setting instance; depends on calling filter.
	 * @param array $instance Current widget settings pre-save
	 * @param array $new_instance New settings for instance input by the user via WP_Widget::form().
	 * @param array $old_instance Old settings for instance.
	 */
	public function add_image_sizes( $widget, $instance = array(), $new_instance = array(), $old_instance = array() )
	{
		// If they didn't choose to register, return.
		if( ! (bool) $instance['register_thumb'] ){
			return;
		}

		$current_filter = current_filter();

		// update_option() (called by ::stick_image_size()) chokes the Customizer
		if( 'apw_update_widget' === $current_filter && is_customize_preview() ){
			return;
		}

		// The Customizer doesn't pass an $instance array like widgets.php does
		if( 'customize_save_widget_apw-recent-posts' === $current_filter ){
			$instance = $widget->post_value();
		}

		// If show_thumb isn't set, no point in storing an image size.
		if( ! (bool) $instance['show_thumb'] ){
			return;
		}

		$width = ( isset( $instance['thumb_size_w'] ) ) ? absint( $instance['thumb_size_w'] ) : 0 ;
		$height = ( isset( $instance['thumb_size_w'] ) ) ? absint( $instance['thumb_size_h'] ) : 0 ;

		if( $width > 0 && $height > 0 ){
			$size = array(
				'width'  => $width,
				'height' => $height
			);
			APW_Utils::stick_image_size( $size );
		}
	}


	/**
	 * Calls to register custom image sizes
	 *
	 * @see APW_Init::register_image_sizes()
	 *
	 * @access public
	 *
	 * @since 1.0
	 */
	public function init_image_sizes()
	{
		add_action( 'init', array( $this, 'register_image_sizes' ) );
	}


	/**
	 * Registers custom image sizes
	 *
	 * @see APW_Init::register_image_sizes()
	 *
	 * @access public
	 *
	 * @since 1.0
	 */
	public function register_image_sizes()
	{
		$_is_active = is_active_widget( false, false, 'advanced-posts-widget', false );

		if( ! $_is_active ){
			return;
		}

		$apw_img_sizes = get_option( 'apw_img_sizes' );

		if( is_array( $apw_img_sizes ) && count( $apw_img_sizes ) ) :

			foreach( $apw_img_sizes as $size ){
				$name = sanitize_text_field( $size['name'] );
				$w = absint( $size['width'] );
				$h = absint( $size['height'] );
				if( $w < 1 || $h < 1 ) {
					continue;
				}
				add_image_size( $name, $w, $h, true );
			}

		endif;

	}

}