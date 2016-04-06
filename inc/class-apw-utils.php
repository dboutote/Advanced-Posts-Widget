<?php

/**
 * APW_Utils Class
 *
 * All methods are static, this is basically a namespacing class wrapper.
 *
 * @package Advanced_Posts_Widget
 * @subpackage APW_Utils
 *
 * @since 1.0
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}


/**
 * APW_Utils Class
 *
 * Group of utility methods for use by Advanced_Posts_Widget
 *
 * @since 1.0
 */
class APW_Utils
{


	/**
	 * Generates path to plugin root
	 *
	 * @access public
	 *
	 * @since 1.0
	 */
	public static function get_apw_path()
	{
		$apw_path = plugin_dir_path( ADVANCED_POSTS_WIDGET_FILE );
		return $apw_path;
	}


	/**
	 * Generates path to subdirectory of plugin root
	 *
	 * @access public
	 *
	 * @since 1.0
	 */
	public static function get_apw_sub_path( $directory )
	{
		if( ! $directory ){
			return false;
		}

		$apw_path = self::get_apw_path();

		$apw_sub_path = $apw_path . trailingslashit( $directory );

		return $apw_sub_path;
	}


	/**
	 * Generates url to plugin root
	 *
	 * @access public
	 *
	 * @since 1.0
	 */
	public static function get_apw_url()
	{
		$apw_url = plugin_dir_url( ADVANCED_POSTS_WIDGET_FILE );
		return $apw_url;
	}


	/**
	 * Generates basename to plugin root
	 *
	 * @access public
	 *
	 * @since 1.0
	 */
	public static function get_apw_basename()
	{
		$apw_basename = plugin_basename( ADVANCED_POSTS_WIDGET_FILE );
		return $apw_basename;
	}


	/**
	 * Sets default parameters
	 *
	 * @access public
	 *
	 * @since 1.0
	 */
	public static function instance_defaults()
	{
		$_item_format = current_theme_supports( 'html5' ) ? 'html5' : 'xhtml';
		$_list_style = ( 'html5' == $_item_format ) ? 'div' : 'ul' ;

		$_defaults = array(
			'title'          => __( 'Recent Posts' ),
			'post_type'      => 'post',
			'number'         => 5,
			'order'          => 'desc',
			'orderby'        => 'date',
			'tax_term'       => '',
			'show_thumb'     => 1,
			'register_thumb' => 0,
			'thumb_size'     => 0,
			'thumb_size_w'   => 55,
			'thumb_size_h'   => 55,
			'show_excerpt'   => 1,
			'excerpt_length' => 15,
			'item_format'    => $_item_format,
			'list_style'     => $_list_style,
			'show_date'      => 0,
			'date_format'    => 'F j, Y',
		);

		$defaults = apply_filters( 'apw_instance_defaults', $_defaults );

		return $defaults;
	}


	/**
	 * Builds a sample excerpt
	 *
	 * Use 'apw_sample_excerpt' filter to modify excerpt text.
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @return string $excerpt Filtered excerpt.
	 */
	public static function sample_excerpt()
	{
		$excerpt = __( 'The point of the foundation is to ensure free access, in perpetuity, to the software projects we support. People and businesses may come and go, so it is important to ensure that the source code for these projects will survive beyond the current contributor base, that we may create a stable platform for web publishing for generations to come. As part of this mission, the Foundation will be responsible for protecting the WordPress, WordCamp, and related trademarks. A 501(c)3 non-profit organization, the WordPress Foundation will also pursue a charter to educate the public about WordPress and related open source software.');
		return apply_filters( 'apw_sample_excerpt', $excerpt );
	}


	/**
	 * Retrieves post types to use in widget
	 *
	 * Use 'apw_widget_post_type_args' to filter arguments for retrieving post types.
	 * Use 'apw_allowed_post_types' to filter post types that can be selected in the widget.
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @return array $_ptypes Filtered array of post types.
	 */
	public static function get_apw_post_types()
	{
		$post_type_args = apply_filters( 'apw_widget_post_type_args', array( 'public' => true) );
		$post_types = get_post_types( $post_type_args, 'objects' );

		$_ptypes = array();
		$_ptypes['all'] = __('All');

		foreach( $post_types as $post_type ){
			$query_var = ( ! $post_type->query_var ) ? $post_type->name : $post_type->query_var ;
			$_ptypes[ $query_var ] = $post_type->labels->singular_name;
		}

		$_ptypes = apply_filters( 'apw_allowed_post_types', $_ptypes );
		$_ptypes = self::sanitize_select_array( $_ptypes );

		return $_ptypes;
	}


	/**
	 * Retrieves taxonomies associated with allowed post types
	 *
	 * Use 'apw_allowed_taxonomies' to filter taxonomies that can be selected in the widget.
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @return array $_ptaxes Filtered array of taxonomies.
	 */
	public static function get_apw_taxonomies()
	{
		$_ptaxes = array();

		$_ptypes = self::get_apw_post_types();

		if( is_array( $_ptypes ) ){

			if( (bool) $_ptypes['all'] ) {
				unset( $_ptypes['all'] );
			}

			$_post_type_taxes = array();

			// get all taxonomies associated with our allowed post_types
			foreach( $_ptypes as $name => $label ) {
				$_otaxes = get_object_taxonomies( $name );

				if( count( $_otaxes ) ) {
					foreach ( $_otaxes as $_otax ) {
						$_post_type_taxes[] = $_otax;
					}
				}

			}

			$_post_type_taxes = array_unique( $_post_type_taxes );

			if( count( $_post_type_taxes ) ){
				foreach ( $_post_type_taxes as $_post_type_tax) {
					$_taxonomy = get_taxonomy( $_post_type_tax );
					if( (bool) $_taxonomy ) {
						$_ptaxes[ $_post_type_tax ] = $_taxonomy->labels->singular_name;
					}
				}
			}
		}

		// post_format is a registered taxonomy, but may not be supported by the theme
		if( (bool) $_ptaxes['post_format'] && ! current_theme_supports( 'post-formats' ) ) {
			unset( $_ptaxes['post_format'] );
		}

		// screw that, post_formats are for display, not classification
		if( (bool) $_ptaxes['post_format'] ) {
			unset( $_ptaxes['post_format'] );
		}

		$_ptaxes = apply_filters( 'apw_allowed_taxonomies', $_ptaxes );
		$_ptaxes = self::sanitize_select_array( $_ptaxes );

		return $_ptaxes;
	}



	/**
	 * Retrieves registered image sizes
	 *
	 * Use 'apw_allowed_image_sizes' to filter image that can be selected in the widget.
	 *
	 * @uses APW_Utils::sanitize_select_array()
	 *
	 * @global $_wp_additional_image_sizes
	 *
	 * @see get_intermediate_image_sizes()
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @return array $_sizes Filtered array of image sizes.
	 */
	public static function get_apw_image_sizes( $fields = 'name' )
	{
		global $_wp_additional_image_sizes;
		$wp_defaults = array( 'thumbnail', 'medium', 'medium_large', 'large' );

		$_sizes = get_intermediate_image_sizes();

		if( count( $_sizes ) ) {
			sort( $_sizes );
			$_sizes = array_combine( $_sizes, $_sizes );
		}

		$_sizes = apply_filters( 'apw_allowed_image_sizes', $_sizes );
		$sizes = self::sanitize_select_array( $_sizes );

		if( count( $sizes )&& 'all' === $fields ) {

			$image_sizes = array();
			asort( $sizes, SORT_NATURAL );

			foreach ( $sizes as $_size ) {
				if ( in_array( $_size, $wp_defaults ) ) {
					$image_sizes[$_size]['name']   = $_size;
					$image_sizes[$_size]['width']  = absint( get_option( "{$_size}_size_w" ) );
					$image_sizes[$_size]['height'] = absint(  get_option( "{$_size}_size_h" ) );
					$image_sizes[$_size]['crop']   = (bool) get_option( "{$_size}_crop" );
				} else if( isset( $_wp_additional_image_sizes[ $_size ] )  ) {
					$image_sizes[$_size]['name']   = $_size;
					$image_sizes[$_size]['width']  = absint( $_wp_additional_image_sizes[ $_size ]['width'] );
					$image_sizes[$_size]['height'] = absint( $_wp_additional_image_sizes[ $_size ]['height'] );
					$image_sizes[$_size]['crop']   = (bool) $_wp_additional_image_sizes[ $_size ]['crop'];
				}
			}

			$sizes = $image_sizes;

		};

		return $sizes;
	}



	/**
	 * Retrieves specific image size
	 *
	 * @uses APW_Utils::get_apw_image_sizes()
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @return string Name of image size.
	 *         array  Image size settings; name, width, height, crop.
	 *		   bool   False if size doesn't exist.
	 */
	public static function get_apw_image_size( $size = 'thumbnail', $fields = 'all' )
	{
		$sizes = self::get_apw_image_sizes( $_fields = 'all' );

		if( count( $sizes ) && isset( $sizes[$size] ) ) :
			if( 'all' === $fields ) {
				return $sizes[$size];
			} else {
				return $sizes[$size]['name'];
			}
		endif;

		return false;
	}



	/**
	 * Builds html for thumbnail section of post
	 *
	 * @access public
	 *
	 * @since 1.0
	 */
	public static function get_apw_post_thumbnail( $post = 0, $instance = array() )
	{
		$_post = get_post( $post );

		if ( empty( $_post ) ) {
			return '';
		}

		$_classes = array();
		$_classes[] = 'apw-post-image';

		// was registered size selected?
		$_size = $instance['thumb_size'];

		// custom size entered
		if( '' === $_size ){
			$_w = absint( $instance['thumb_size_w'] );
			$_h = absint( $instance['thumb_size_h'] );
			$_size = "apw-thumbnail-{$_w}-{$_h}";
		}

		// check if the size is registered
		$_size_exists = self::get_apw_image_size( $_size );

		// no thumbnail
		// @todo placeholder?
		if( ! has_post_thumbnail( $_post ) ) {
			return '';
		}


		if( $_size_exists ){
			$_get_size = $_size;
			$_w = absint( $_size_exists['width'] );
			$_h = absint( $_size_exists['height'] );
			$_classes[] = "size-{$_size}";
		} else {
			$_get_size = array( $_w, $_h);
		}

		$classes = apply_filters( 'apw_post_thumb_class', $_classes, $_post, $instance );
		$classes = ( ! is_array( $classes ) ) ? (array) $classes : $classes ;
		$classes = array_map( 'sanitize_html_class', $classes );

		$class_str = implode( ' ', $classes );

		$_thumb = get_the_post_thumbnail(
			$_post,
			$_get_size,
			array(
				'class' => $class_str,
				'alt' => the_title_attribute( 'echo=0' )
				)
			);

		$thumb = apply_filters( 'apw_post_thumbnail_html', $_thumb, $_post, $instance );

		return $thumb;

	}




	/**
	 * Generates publish date
	 *
	 * Use 'get_apw_post_date' filter to filter post date before output.
	 *
	 * @access public
	 * @since 1.0
	 *
	 * @param object $post Post to display.
	 * @param array  $instance Widget instance.
	 *
	 * @return string $time Publish date.
	 */
	public static function get_apw_post_date( $post = 0, $instance = array() )
	{
		$post = get_post( $post );

		if ( empty( $post ) ) {
			return '';
		}

		$time_string = '<time pubdate class="entry-date apw-entry-date published updated" datetime="%1$s">%2$s</time>';
		$time_string = sprintf( $time_string,
			esc_attr( get_the_date( 'c' ) ),
			get_the_date( $instance['date_format'] )
		);

		$_time = sprintf( '<span class="posted-on apw-posted-on"><span class="screen-reader-text">%1$s </span><a href="%2$s" rel="bookmark">%3$s</a></span>',
			_x( 'Posted on', 'Used before publish date.' ),
			esc_url( get_permalink() ),
			$time_string
		);

		return apply_filters( 'apw_post_date', $_time, $post, $instance );
	}


	/**
	 * Generates unique post id based on widget instance
	 *
	 * Use 'apw_post_id' filter to filter post ID before output.
	 *
	 * @access public
	 * @since 1.0
	 *
	 * @param object $post Post to display.
	 * @param array  $instance Widget instance.
	 *
	 * @return string $apw_post_id Filtered post ID.
	 */
	public static function get_apw_post_id( $post = 0, $instance = array() )
	{
		$post = get_post( $post );

		if ( empty( $post ) ) {
			return '';
		}

		$apw_post_id = $instance['widget_id'] . '-post-' . $post->ID;

		return apply_filters( 'apw_post_id', $apw_post_id, $post, $instance );
	}


	/**
	 * Generate post classes
	 *
	 * Use 'apw_post_class' filter to filter post classes before output.
	 *
	 * @access public
	 * @since 1.0
	 *
	 * @param object $post     Post to display.
	 * @param array  $instance Widget instance.
	 *
	 * @return string $class_str Filtered post classes.
	 */
	public static function get_apw_post_class( $post = 0, $instance = array() )
	{
		$post = get_post( $post );

		if ( empty( $post ) ) {
			return '';
		}

		$type = ( empty( $post->post_type ) ) ? 'post' : $post->post_type;

		$_classes = array();
		$_classes[] = 'apw-post';
		$_classes[] = 'type-' . $type;
		$_classes[] = 'apw-type-' . $type;

		if ( $post->post_parent > 0 ) {
			$_classes[] = 'child-post';
			$_classes[] = 'parent-' . $post->post_parent;
		}

		$classes = apply_filters( 'apw_post_class', $_classes, $post, $instance );
		$classes = ( ! is_array( $classes ) ) ? (array) $classes : $classes ;
		$classes = array_map('sanitize_html_class', $classes);

		$class_str = implode(' ', $classes);

		return $class_str;
	}


	/**
	 * Retrieves post content
	 *
	 * Use 'apw_post_excerpt' to filter the text before output.
	 *
	 * @access public
	 * @since 1.0
	 *
	 * @param object $post     Post to display.
	 * @param array  $instance Widget instance.
	 * @param string $trim     Flag to trim by word or character.
	 *
	 * @return string $text Filtered post content.
	 */
	public static function get_apw_post_excerpt( $post = 0, $instance = array(), $trim = 'words' )
	{
		$post = get_post( $post );

		if ( empty( $post ) ) {
			return '';
		}

		if ( post_password_required() ) {
			$_protected = __( 'This is a protected post.' );
			return apply_filters( 'apw_protected_post_notice', $_protected );
		}

		$_text = $post->post_excerpt;

		if( '' === $_text ) {
			$_text = $post->post_content;
			$_text = strip_shortcodes( $_text );
			$_text = str_replace(']]>', ']]&gt;', $_text);
		}

		$text = apply_filters('apw_post_excerpt', $_text, $post, $instance );

		$_length = ( ! empty( $instance['excerpt_length'] ) ) ? absint( $instance['excerpt_length'] ) : 55 ;
		$length = apply_filters( 'apw_post_excerpt_length', $_length );

		$_aposiopesis = ( ! empty( $instance['excerpt_more'] ) ) ? $instance['excerpt_more'] : '&hellip;' ;
		$aposiopesis = apply_filters( 'apw_post_excerpt_more', $_aposiopesis );

		if( 'chars' === $trim ){
			$text = wp_html_excerpt( $text, $length, $aposiopesis );
		} else {
			$text = wp_trim_words( $text, $length, $aposiopesis );
		}

		return $text;
	}


	/**
	 * Stores a selected image size
	 *
	 * @see APW_Utils::build_field_thumb_custom()
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $size Width and Height values of the selected size.
	 */
	public static function stick_image_size( $size )
	{
		$apw_img_sizes = get_option( 'apw_img_sizes' );

		$new_size = array();
		$new_size['width']  = absint( $size['width'] );
		$new_size['height'] = absint( $size['height'] );

		$name = 'apw-thumbnail-' . $new_size['width'] . '-' . $new_size['height'];
		$key = sanitize_key( $name );

		$new_size['name'] = $name;

		if ( ! is_array($apw_img_sizes) ) {
			$apw_img_sizes = array( $key => $new_size );
		} else {
			$apw_img_sizes[$key] = $new_size;
		}

		update_option( 'apw_img_sizes', $apw_img_sizes );
	}


	/**
	 * Removes a selected image size from the apw_img_sizes option
	 *
	 * @see APW_Utils::build_field_thumb_custom()
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param string $name Name of the image size to remove.
	 */
	public function unstick_image_size( $name )
	{
		$apw_img_sizes = get_option( 'apw_img_sizes' );

		$key = sanitize_key( $name );

		if( ! $key ) {
			return;
		}

		if ( ! is_array( $apw_img_sizes ) ) {
			return;
		}

		if ( ! array_key_exists( $key, $apw_img_sizes ) ) {
			return;
		}

		unset( $apw_img_sizes[ $key ] );

		update_option( 'apw_img_sizes', $apw_img_sizes );

	}


	/**
	 * Retrieves template file
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param string $file         Template file to search for.
	 * @param boo    $load         If true the template file will be loaded if it is found.
	 * @param bool   $require_once Whether to require_once or require. Default true. Has no effect if $load is false.
	 * @param array  $instance     Widget instance.
	 *
	 * @return string $_located The template filename if one is located.
	 */
	public static function get_template( $file, $load = false, $require_once = true, $instance = array() )
	{
		$_located = '';

		$template_name = "{$file}.php";

		$template_path = APW_Utils::get_apw_sub_path('tpl');

		if ( file_exists( $template_path . $template_name ) ) {
			$_located = $template_path . $template_name;
		}

		if ( $load && '' != $_located ){
			if ( $require_once ) {
				require_once( $_located );
			} else {
				require( $_located );
			}
		}

		return $_located;
	}


	/**
	 * Cleans array of keys/values used in select drop downs
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $options Values used for select options
	 * @param bool  $sort    Flag to sort the values alphabetically.
	 *
	 * @return array $options Sanitized values.
	 */
	public static function sanitize_select_array( $options, $sort = true )
	{
		$options = ( ! is_array( $options ) ) ? (array) $options : $options ;

		// Clean the values (since it can be filtered by other plugins)
		$options = array_map( 'esc_html', $options );

		// Flip to clean the keys (used as <option> values in <select> field on form)
		$options = array_flip( $options );
		$options = array_map( 'sanitize_key', $options );

		// Flip back
		$options = array_flip( $options );

		if( $sort ) {
			asort( $options );
		};

		return $options;
	}

}