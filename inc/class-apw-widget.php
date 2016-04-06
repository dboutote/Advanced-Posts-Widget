<?php
/**
 * APW_Widget Class
 *
 * Adds a Posts widget with extended functionality
 *
 * @package Advanced_Posts_Widget
 * @subpackage APW_Widget
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
 * Core class used to implement a Posts widget.
 *
 * @since 1.0
 *
 * @see WP_Widget
 */
class APW_Widget extends WP_Widget {

	/**
	 * Sets up a new widget instance.
	 *
	 * @access public
	 *
	 * @since 1.0
	 */
	public function __construct()
	{
		$widget_options = array(
			'classname'                   => 'widget_apw_recent_posts widget_recent_entries advanced-posts-widget',
			'description'                 => __( 'A posts widget with extended features.' ),
			'customize_selective_refresh' => true,
			);

		$control_options = array();

		parent::__construct(
			'advanced-posts-widget',       // $this->id_base
			__( 'Advanced Posts Widget' ), // $this->name
			$widget_options,               // $this->widget_options
			$control_options               // $this->control_options
		);

		$this->alt_option_name = 'widget_apw_recent_posts';
	}


	/**
	 * Outputs the content for the current widget instance.
	 *
	 * Use 'widget_title' to filter the widget title.
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Posts widget instance.
	 */
	public function widget( $args, $instance )
	{
		if ( ! isset( $args['widget_id'] ) ){
			$args['widget_id'] = $this->id;
		}
		
		$_defaults = APW_Utils::instance_defaults();
		$instance = wp_parse_args( (array) $instance, $_defaults );
		
		// build out the instance for devs
		$instance['id_base']       = $this->id_base;
		$instance['widget_number'] = $this->number;
		$instance['widget_id']     = $this->id;

		// widget title
		$_title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		// post types
		$_post_types = ( ! is_array( $instance['post_type'] ) ) ? (array) $instance['post_type'] : $instance['post_type'] ;

		// taxonomies
		$_tax_query = '';
		$_tax_terms = array_filter( (array) $instance['tax_term'] );

		if( is_array( $_tax_terms ) && count( $_tax_terms ) ) {
			foreach( $_tax_terms as $_taxonomy => $_term ) {
				$_tax_query[] = array(
					'taxonomy' => $_taxonomy,
					'field' => 'slug',
					'terms' => (array) $_term,
				);
			}
			$_tax_query['relation'] = 'AND';
		}

		// query
		$_query_args = array(
			'post_type'           => $_post_types,
			'posts_per_page'      => absint( $instance['number'] ),
			'no_found_rows'       => true,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'order'               => $instance['order'],
			'orderby'             => $instance['orderby'],
			'tax_query'           => $_tax_query,
		);

		$query_args = apply_filters( 'apw_widget_posts_query_args', $_query_args );

		$r = new WP_Query( $query_args );

		if ( $r->have_posts() ) :

			echo $args['before_widget'];

			if( $_title ) {
				echo $args['before_title'] . $_title . $args['after_title'];
			};

			do_action( 'apw_widget_title_after', $instance, $r );
			?>

			<div class="advanced-posts-widget apw-recent-posts apw-posts-wrap">

				<?php

				do_action( 'apw_post_list_before', $instance, $r );

				APW_Views::start_list( $instance, $r );

				while ( $r->have_posts() ) : $r->the_post();

					APW_Views::start_list_item( $instance, $r );

						APW_Utils::get_template( "content-{$instance['item_format']}", $load = true, $require_once = false, $instance );

					APW_Views::end_list_item( $instance, $r );

				endwhile;

				APW_Views::end_list( $instance, $r );

				do_action( 'apw_post_list_after', $instance, $r );

				?>

			</div><!-- /.apw-posts-wrap -->

			<?php APW_Views::colophon(); ?>

			<?php echo $args['after_widget']; ?>

		<?php endif; ?>

		<?php wp_reset_postdata();

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

		// general
		$instance['title']     = sanitize_text_field( $new_instance['title'] );
		$instance['post_type'] = sanitize_text_field( $new_instance['post_type'] );
		$_number               = absint( $new_instance['number'] );
		$instance['number']    = ( $_number < 1 ) ? 1 : $_number ;
		$instance['orderby']   = sanitize_text_field( $new_instance['orderby'] );
		$instance['order']     = sanitize_text_field( $new_instance['order'] );

		// taxonomies & filters
		if( is_array( $new_instance['tax_term'] ) ) {
			$_tax_terms           = array_filter( $new_instance['tax_term'] );
			$instance['tax_term'] = array_map( 'sanitize_key', $_tax_terms );
		} else {
			$instance['tax_term'] = sanitize_key( $new_instance['tax_term'] );
		}

		// thumbnails
		$instance['show_thumb']   = isset( $new_instance['show_thumb'] ) ? 1 : 0 ;
		$instance['thumb_size']   = sanitize_text_field( $new_instance['thumb_size'] );

		$_thumb_size_w            = absint( $new_instance['thumb_size_w'] );
		$instance['thumb_size_w'] = ( $_thumb_size_w < 1 ) ? 55 : $_thumb_size_w ;
		
		$_thumb_size_h            = absint( $new_instance['thumb_size_h'] );
		$instance['thumb_size_h'] = ( $_thumb_size_h < 1 ) ? $_thumb_size_w : $_thumb_size_h ;
		
		$instance['register_thumb'] = isset( $new_instance['register_thumb'] ) ? 1 : 0 ;

		// excerpts
		$instance['show_excerpt']   = isset( $new_instance['show_excerpt'] ) ? 1 : 0 ;
		$instance['excerpt_length'] = absint( $new_instance['excerpt_length'] );

		// list format
		$instance['list_style']  = ( '' !== $new_instance['list_style'] ) ? sanitize_key( $new_instance['list_style'] ) : 'ul ';
		$instance['item_format'] = ( '' !== $new_instance['item_format'] ) ? sanitize_key( $new_instance['item_format'] ) : 'xhtml ';
		$instance['show_date']   = isset( $new_instance['show_date'] ) ? 1 : 0 ;
		$instance['date_format'] = esc_html( $new_instance['date_format'] );

		$instance = apply_filters('apw_update_instance', $instance, $new_instance, $old_instance );

		do_action( 'apw_update_widget', $this, $instance, $new_instance, $old_instance );

		return $instance;
	}


	/**
	 * Outputs the settings form for the Posts widget.
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
		$defaults = APW_Utils::instance_defaults();

		$instance = wp_parse_args( (array) $instance, $defaults );

		include( APW_Utils::get_apw_sub_path('inc') . 'widget-form.php' );

	}

}