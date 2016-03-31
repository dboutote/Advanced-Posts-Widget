<?php

/**
 * Widget_APW_Recent_Posts Class
 *
 * Adds a Recent Posts widget with extended functionality
 *
 * @package APW_Recent_Posts
 * @subpackage Widget_APW_Recent_Posts
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
 * Core class used to implement a Recent Posts widget.
 *
 * @since 1.0
 *
 * @see WP_Widget
 */
class Widget_APW_Recent_Posts extends WP_Widget {

	/**
	 * Sets up a new Posts widget instance.
	 *
	 * @access public
	 *
	 * @since 1.0
	 */
	public function __construct()
	{
		$widget_options = array(
			'classname' => 'widget_apw_recent_posts',
			'description' => __( 'A posts widget with extended features.' ),
			'customize_selective_refresh' => true,
			);

		$control_options = array();

		parent::__construct(
			'apw-recent-posts',            // $this->id_base
			__( 'Advanced Recent Posts' ), // $this->name
			$widget_options,               // $this->widget_options
			$control_options               // $this->control_options
		);

		$this->alt_option_name = 'widget_apw_recent_posts';
	}


	/**
	 * Outputs the content for the current Recent Posts widget instance.
	 *
	 * Use 'widget_title' to filter the widget title.
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Recent Posts widget instance.
	 */
	public function widget( $args, $instance )
	{
	
		// widget title
		$_title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : '' ;
		$_title = apply_filters( 'widget_title', $_title, $instance, $this->id_base );
		
		echo $args['before_widget'];
		
		echo $args['before_title'] . $_title . $args['after_title'];
		
		echo $args['after_widget']; 
	}


	/**
	 * Handles updating settings for the current widget instance.
	 *
	 * Use 'apw_update_instance' to filter updating/sanitizing the widget instance.
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 *
	 * @return array Updated settings to save.
	 */
	public function update( $new_instance, $old_instance )
	{
		$instance = $old_instance;
		
		$instance['title']        = sanitize_text_field( $new_instance['title'] );
		$instance['thumb_size_w'] = absint( $new_instance['thumb_size_w'] );
		$instance['thumb_size_h'] = absint( $new_instance['thumb_size_h'] );
		
		$name = "apw-thumbnail-{$instance['thumb_size_w']}-{$instance['thumb_size_h']}";
		$new_size = array(
			'name'   => $name,
			'width'  => $instance['thumb_size_w'],
			'height' => $instance['thumb_size_h']
		);
		
		$apw_img_sizes = get_option( 'apw_img_sizes' );
		
		if( ! $apw_img_sizes ) {		
			$apw_img_sizes = array( $name => $new_size );
		} else {
			$apw_img_sizes[ $name ] = $new_size;
		}
		
		#_debug( $apw_img_sizes  );
		
		#wp_die(__METHOD__);
		
		#return $instance;
					
		update_option( 'apw_img_sizes', $apw_img_sizes );

		$instance = apply_filters('apw_update_instance', $instance, $new_instance, $old_instance );

		return $instance;
	}


	/**
	 * Outputs the settings form for the Recent Posts widget.
	 *
	 * Applies 'apw_form_defaults' filter on form fields to allow extension by plugins.
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance )
	{
	
		$_post_format = current_theme_supports( 'html5' ) ? 'html5' : 'xhtml';
		$_list_style = ( 'html5' == $_post_format ) ? 'div' : 'ul' ;
		
		$_defaults = array(
			'title'          => __( 'Recent Posts' ),
			'post_type'      => 'post',
			'number'         => 5,
			'order'          => 'desc',
			'orderby'        => 'date',
			'tax_term'       => '',
			'show_thumbs'    => 1,
			'thumb_size_r'   => 'none',
			'thumb_size_w'   => 55,
			'thumb_size_h'   => 55,
			'show_excerpt'   => 1,
			'excerpt_length' => 150,
			'post_format'    => $_post_format,
			'list_style'     => $_list_style,
		);
		
		$_defaults = apply_filters( 'apw_instance_defaults', $_defaults );
		$instance = wp_parse_args( (array) $instance, $_defaults );
		
		$_fields   =  array(
			'title'          => APW_Recent_Posts_Utilities::build_field_title( $instance, $this ),
			'post_type'      => APW_Recent_Posts_Utilities::build_field_post_type( $instance, $this ),			
			'number'         => APW_Recent_Posts_Utilities::build_field_number( $instance, $this ),
			'orderby'        => APW_Recent_Posts_Utilities::build_field_orderby( $instance, $this ),
			'order'          => APW_Recent_Posts_Utilities::build_field_order( $instance, $this ),
			'tax_term'          => APW_Recent_Posts_Utilities::build_field_tax_term( $instance, $this ),			
			'show_thumbs'    => APW_Recent_Posts_Utilities::build_field_show_thumbs( $instance, $this ),
			'thumb_size'     => APW_Recent_Posts_Utilities::build_field_thumb_size( $instance, $this ),
			'show_excerpt'   => APW_Recent_Posts_Utilities::build_field_show_excerpt( $instance, $this ),
			'excerpt_length' => APW_Recent_Posts_Utilities::build_field_excerpt_length( $instance, $this ),
			#'list_style'     => APW_Recent_Posts_Utilities::build_field_list_style( $instance, $this ),
			#'comment_format' => APW_Recent_Posts_Utilities::build_field_post_format( $instance, $this ),			
		);
		
		$fields = apply_filters( 'apw_form_fields', $_fields, $instance, $this );
		
		$field_keys  = array_keys( $fields );
		$first_field = reset( $field_keys );
		$last_field  = end( $field_keys );
		
		foreach ( $fields as $name => $field ) {

			if ( $first_field === $name ) {
				do_action( 'apw_form_before_fields', $instance, $this );
			}

			do_action( "apw_form_before_field_{$name}", $instance, $this );

			echo apply_filters( "apw_form_field_{$name}", $field, $instance, $this ) . "\n";

			do_action( "apw_form_after_field_{$name}", $instance, $this );

			if ( $last_field === $name ) {
				do_action( 'apw_form_after_fields', $instance, $this );
			}

		}		
	
	}



}
