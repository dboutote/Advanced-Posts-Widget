<?php

/**
 * APW_Recent_Posts Class
 *
 * Adds a Recent Posts widget with extended functionality
 *
 * @package APW_Recent_Posts
 *
 * @since 1.0
 *
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}


class APW_Recent_Posts
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
	 * e.g. "advanced-posts-widget/advanced-posts-widget.php"
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
	public function __construct( $file ){
		$this->file	    = $file;
		$this->url	    = plugin_dir_url( $this->file );
		$this->path	    = plugin_dir_path( $this->file );
		$this->basename = plugin_basename( $this->file );
	}


	/**
	 * Loads the class
	 *
	 * @uses APW_Recent_Posts::load_widget()
	 * @uses APW_Recent_Posts::load_admin_scripts()
	 *
	 * @access public
	 *
	 * @since 1.0
	 */
	public function init()
	{
		$this->load_widget();
		$this->load_admin_scripts();
		$this->load_image_sizes();
	}

	/**
	 * Loads the Comment Widget
	 *
	 * @uses APW_Recent_Posts::register_widget()
	 *
	 * @access public
	 *
	 * @since 1.0
	 */
	public function load_widget()
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
		register_widget( 'Widget_APW_Recent_Posts' );
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
	public function load_admin_scripts()
	{
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'admin_head', array( $this, 'admin_styles' ) );
		add_action( 'customize_controls_print_styles', array( $this, 'admin_styles' ) );
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

		if( 'customize.php' == $pagenow || 'widgets.php' == $pagenow || 'widgets.php' == $hook ) {
			$enqueue = true;
		};

		if ( ! $enqueue ) {
			return;
		};

		wp_enqueue_script( 'apw-scripts', $this->url . 'js/admin.js', array( 'jquery' ), '', true );

		$sample_excerpt = APW_Recent_Posts_Utilities::sample_excerpt();

		wp_localize_script(
			'apw-scripts',
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
		?>
		<style type="text/css">
			.apw-preview-container {
				display: block;
				height: auto;
				margin: 0 0 13px;
				overflow: hidden;
				width: 100%;
			}
			.apw-avatar-preview {
				background-color: #eee;
				border: 1px solid #ddd;
				display: block;

				padding: 5px;
				text-align: center;
				}
			.apw-avatar-preview .dashicons {  color: #ccc; font-size: inherit; height: 100%; width: 100%; }
			.apw-excerpt-preview {
				background-color: #fafafa;
				display: block;
				font-style: italic;
				padding: 6px;
			}
			.apw-excerpt {opacity: 0.8;}
			.apw-thumbsize { display: inline-block; min-width: 50px;}
			.apw-preview-l { display: block; margin-top: 1em; }
			p.apw-description { margin-bottom: 1em !important; padding-bottom: 0 !important; }
			.apw-section-heading {text-align: center;}
		</style>
		<?php
	}



	/**
	 * Registers Additional Image Sizes
	 *
	 * @uses APW_Recent_Posts::register_image_sizes()
	 *
	 * @access public
	 *
	 * @since 1.0
	 */
	public function load_image_sizes()
	{
		#add_filter( 'acw_allowed_image_sizes', array( $this, 'register_image_sizes' ) );
		add_action( 'init', array( $this, 'register_image_sizes' ) );
	}


	public function register_image_sizes( $_sizes )
	{
		$apw_img_sizes = get_option( 'apw_img_sizes' );
		
		#_debug( $apw_img_sizes );

		if( is_array( $apw_img_sizes ) && count( $apw_img_sizes ) ) :

			foreach( $apw_img_sizes as $size ){
				$key = sanitize_key(  $size['name'] );
				$name = sanitize_text_field( $size['name'] );
				$w = absint( $size['width'] );
				$h = absint( $size['height'] );
				$_sizes[$key] = $name;
				add_image_size( $name, $w, $h );
			}

		endif;

		return $_sizes;

	}

}