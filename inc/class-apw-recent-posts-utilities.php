<?php

/**
 * APW_Recent_Posts_Utilities Class
 *
 * All methods are static, this is basically a namespacing class wrapper.
 *
 * @package APW_Recent_Posts
 * @subpackage APW_Recent_Posts_Utilities
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
 * APW_Recent_Posts_Utilities Class
 *
 * Group of utility methods for use by APW_Recent_Posts
 *
 * @since 1.0
 */
class APW_Recent_Posts_Utilities
{

	/**
	 * Builds form field: title
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $instance Current settings.
	 * @param object $widget Widget object.
	 */
	public static function build_field_title( $instance, $widget )
	{
		ob_start();
		?>

		<h4 class="apw-section-heading">General Settings</h4>

		<p>
			<label for="<?php echo $widget->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $widget->get_field_id( 'title' ); ?>" name="<?php echo $widget->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>

		<?php
		$field = ob_get_clean();

		return $field;
	}


	/**
	 * Builds form field: post_type
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $instance Current settings.
	 * @param object $widget Widget object.
	 */
	public static function build_field_post_type( $instance, $widget )
	{
		$post_types = self::get_post_types();
		ob_start();
		?>

		<p>
			<label for="<?php echo $widget->get_field_id('post_type'); ?>">
				<?php _e( 'Post Type:' ); ?>
			</label>
			<select name="<?php echo $widget->get_field_name('post_type'); ?>" id="<?php echo $widget->get_field_id('post_type'); ?>" class="widefat">
				<?php foreach( $post_types as $query_var => $label  ) { ?>
					<option value="<?php echo esc_attr( $query_var ); ?>" <?php selected( $instance['post_type'] , $query_var ); ?>><?php echo esc_html( $label ); ?></option>
				<?php } ?>
			</select>
		</p>

		<?php
		$field = ob_get_clean();

		return $field;
	}


	/**
	 * Builds form field: number
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $instance Current settings.
	 * @param object $widget Widget object.
	 */
	public static function build_field_number( $instance, $widget )
	{
		ob_start();
		?>

		<p>
			<label for="<?php echo $widget->get_field_id( 'number' ); ?>">
				<?php _e( 'Number of Posts to Show:' ); ?>
			</label>
			<input class="widefat apw-number" id="<?php echo $widget->get_field_id( 'number' ); ?>" name="<?php echo $widget->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo absint( $instance['number'] ); ?>" size="3" />
		</p>

		<?php
		$field = ob_get_clean();

		return $field;
	}


	/**
	 * Builds form field: orderby
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $instance Current settings.
	 * @param object $widget Widget object.
	 */
	public static function build_field_orderby( $instance, $widget )
	{
		ob_start();

		$_params = self::get_orderby_parameters();
		?>

		<p>
			<label for="<?php echo $widget->get_field_id('orderby'); ?>">
				<?php _e( 'Order By:' ); ?>
			</label>
			<select name="<?php echo $widget->get_field_name('orderby'); ?>" id="<?php echo $widget->get_field_id('orderby'); ?>" class="widefat">
				<?php foreach( $_params as $query_var => $label  ) { ?>
					<option value="<?php echo esc_attr( $query_var ); ?>" <?php selected( $instance['orderby'] , $query_var ); ?>><?php echo esc_html( $label ); ?></option>
				<?php } ?>
			</select>
		</p>

		<?php
		$field = ob_get_clean();

		return $field;
	}


	/**
	 * Retrieves orderby parameters
	 *
	 * Use 'apw_allowed_orderby_params' to filter parameters that can be selected in the widget.
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @return array $_ptypes Filtered array of post types.
	 */
	public function get_orderby_parameters()
	{
		$_orderby = array(
			'date'       => __( 'Published Date' ),
			'modified'   => __( 'Last Modified Date' ),
			'title'      => __( 'Title' ),
			'menu_order' => __( 'Menu Order' ),
			'rand'       => __( 'Random' ),
		);

		$_orderby = apply_filters( 'apw_allowed_orderby_params', $_orderby );
		$_orderby = self::_sanitize_select_array( $_orderby );

		return $_orderby;
	}



	/**
	 * Builds form field: order
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $instance Current settings.
	 * @param object $widget Widget object.
	 */
	public static function build_field_order( $instance, $widget )
	{
		ob_start();
		?>

		<p>
			<label for="<?php echo $widget->get_field_id('order'); ?>">
				<?php _e( 'Order:' ); ?>
			</label>
			<select name="<?php echo $widget->get_field_name('order'); ?>" id="<?php echo $widget->get_field_id('order'); ?>" class="widefat">
				<option value="desc" <?php selected( $instance['order'] , 'desc' ); ?>><?php _e( 'Newer posts first'); ?></option>
				<option value="asc" <?php selected( $instance['order'] , 'asc' ); ?>><?php _e( 'Older posts first'); ?></option>
			</select>
		</p>

		<?php
		$field = ob_get_clean();

		return $field;
	}
	
	
	public static function build_field_tax_term( $instance, $widget )
	{
		ob_start();
		?>
		
		<?php 
		$taxonomies = get_taxonomies();

		_debug( $taxonomies );
		?>
	
		<?php
		$field = ob_get_clean();

		return $field;
	}
	


	/**
	 * Builds form field: show_thumbs
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $instance Current settings.
	 * @param object $widget Widget object.
	 */
	public static function build_field_show_thumbs( $instance, $widget )
	{
		ob_start();
		?>

		<h4 class="apw-section-heading">Thumbnails</h4>

		<?php
		$_intro = "If you choose to display a thumbnail of the featured image, you can either select from an image size registered with your site, or set a custom size.";
		$_intro = apply_filters( 'apw_thumb_intro_text', $_intro );
		?>

		<p class="description apw-description"><?php _e( $_intro ); ?></p>

		<p>
			<input class="checkbox" type="checkbox" id="<?php echo $widget->get_field_id( 'show_thumbs' ); ?>" name="<?php echo $widget->get_field_name( 'show_thumbs' ); ?>" <?php checked( $instance['show_thumbs'], 1 ); ?>/>
			<label for="<?php echo $widget->get_field_id( 'show_thumbs' ); ?>">
				<?php _e( 'Display Thumbnail' ); ?>
			</label>
		</p>

		<?php
		$field = ob_get_clean();

		return $field;
	}


	/**
	 * Builds form fields: thumb_size_w / thumb_size_h
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $instance Current settings.
	 * @param object $widget Widget object.
	 */
	public static function build_field_thumb_size( $instance, $widget )
	{
		ob_start();
		?>

		<p class="apw-thumb-size-defaults">
			<label for="<?php echo $widget->get_field_id('thumb_size_r'); ?>">
				<?php _e( 'Choose Registered Size' ); ?>:
			</label>
			<?php self::build_img_select( $instance, $widget ); ?>
		</p>

		<p class="apw-thumb-size-wrap">
			<label><?php _e('Set Custom Size'); ?>: </label><br />

			<label class="apw-thumbsize" for="<?php echo $widget->get_field_id( 'thumb_size_w' ); ?>"><?php _e( 'Width' ); ?></label>
			<input class="small-text apw-thumb-size apw-thumb-width" id="<?php echo $widget->get_field_id( 'thumb_size_w' ); ?>" name="<?php echo $widget->get_field_name( 'thumb_size_w' ); ?>" type="number" value="<?php echo absint( $instance['thumb_size_w'] ); ?>" />

			<br />

			<label class="apw-thumbsize" for="<?php echo $widget->get_field_id( 'thumb_size_h' ); ?>"><?php _e( 'Height' ); ?></label>
			<input class="small-text apw-thumb-size apw-thumb-height" id="<?php echo $widget->get_field_id( 'thumb_size_h' ); ?>" name="<?php echo $widget->get_field_name( 'thumb_size_h' ); ?>" type="number" value="<?php echo absint( $instance['thumb_size_h'] ); ?>" />

			<br />

			<label class="apw-preview-l"><?php _e('Preview'); ?>: </label>
			<span class="apw-preview-container">
				<span class="apw-avatar-preview" style="font-size: <?php echo absint( $instance['thumb_size_h'] ); ?>px; height:<?php echo absint( $instance['thumb_size_h'] ); ?>px; width:<?php echo absint( $instance['thumb_size_w'] ); ?>px">
					<i class="apw-avatar dashicons dashicons-format-image"></i>
				</span>
			</span>
		</p>

		<?php
		$field = ob_get_clean();

		return $field;
	}

	/**
	 * Retrieves registered image sizes
	 *
	 * Use 'acw_allowed_image_sizes' to filter image that can be selected in the widget.
	 *
	 * @see $_wp_additional_image_sizes
	 * @see get_intermediate_image_sizes()
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @return array $_sizes Filtered array of post types.
	 */
	public static function get_images_sizes()
	{
		global $_wp_additional_image_sizes;

		$_sizes = get_intermediate_image_sizes();

		if( count( $_sizes ) ) {
			sort( $_sizes );
			$_sizes = array_combine( $_sizes, $_sizes );
		}
		
		$_sizes = apply_filters( 'acw_allowed_image_sizes', $_sizes );		
		$_sizes = self::_sanitize_select_array( $_sizes );
		
		return $_sizes;
	}


	/**
	 * Builds a <select> drop down for widget form
	 *
	 * @global array $_wp_additional_image_sizes
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $instance Current widget settings.
	 * @param object $widget Widget object.
	 *
	 * @return string <select> drop down for widget form
	 */
	public static function build_img_select( $instance, $widget )
	{
		global $_wp_additional_image_sizes;
		$wp_defaults = array( 'thumbnail', 'medium', 'medium_large', 'large' );

		$img_sizes = array();
		$img_sizes['none']['name']   = '';
		$img_sizes['none']['width']  = 0;
		$img_sizes['none']['height'] = 0;
		$img_sizes['none']['crop']   = (bool) 0;

		$_img_sizes = self::get_images_sizes();

		if( count( $_img_sizes ) ) {
		
			sort( $_img_sizes );
			
			foreach ( $_img_sizes as $_size ) {
				if ( in_array( $_size, $wp_defaults ) ) {
					$img_sizes[ $_size ]['name']   = $_size;
					$img_sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
					$img_sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
					$img_sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
				} else if(  isset( $_wp_additional_image_sizes[ $_size ] )  ) {
					$img_sizes[ $_size ]['name']   = $_size;
					$img_sizes[ $_size ]['width']  = $_wp_additional_image_sizes[ $_size ]['width'];
					$img_sizes[ $_size ]['height'] = $_wp_additional_image_sizes[ $_size ]['height'];
					$img_sizes[ $_size ]['crop']   = (bool) $_wp_additional_image_sizes[ $_size ]['crop'];
				}
			}
		};
		?>

		<select name="<?php echo $widget->get_field_name('thumb_size_r'); ?>" id="<?php echo $widget->get_field_id('thumb_size_r'); ?>" class="widefat">
			<?php foreach( $img_sizes as $query_var => $size  ) {
				$width = absint( $size['width'] );
				$height = absint( $size['height'] );
				$dimensions = ( 'none' !== $query_var ) ? ' (' . $width . ' x ' . $height . ')' : '';

				printf( '<option data-width="%1$s" data-height="%2$s" value="%3$s" %4$s>%5$s%6$s</option>' . "\n",
					$width,
					$height,
					esc_attr( $query_var ),
					selected( $instance['thumb_size_r'] , $query_var, false ),
					esc_html( $size['name'] ),
					$dimensions
				);
			} ?>
		</select>
		<?php
	}


	/**
	 * Builds form field: show_excerpt
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $instance Current settings.
	 * @param object $widget Widget object.
	 */
	public static function build_field_show_excerpt( $instance, $widget )
	{
		ob_start();
		?>

		<h4 class="apw-section-heading">Post Content</h4>

		<p>
			<input id="<?php echo $widget->get_field_id( 'show_excerpt' ); ?>" name="<?php echo $widget->get_field_name( 'show_excerpt' ); ?>" type="checkbox" <?php checked( $instance['show_excerpt'], 1 ); ?> />
			<label for="<?php echo $widget->get_field_id( 'show_excerpt' ); ?>">
				<?php _e( 'Display Post Excerpt' ); ?>
			</label>
		</p>

		<?php
		$field = ob_get_clean();

		return $field;
	}


	/**
	 * Builds form field: excerpt_length
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $instance Current settings.
	 * @param object $widget Widget object.
	 */
	public static function build_field_excerpt_length( $instance, $widget )
	{
		ob_start();
		?>

		<p class="apw-excerpt-size-wrap">
			<label for="<?php echo $widget->get_field_id( 'excerpt_length' ); ?>">
				<?php _e( 'Excerpt Length' ); ?>:
			</label>
			<input class="widefat apw-excerpt-length" id="<?php echo $widget->get_field_id( 'excerpt_length' ); ?>" name="<?php echo $widget->get_field_name( 'excerpt_length' ); ?>" type="number" step="1" min="0" value="<?php echo absint( $instance['excerpt_length'] ); ?>" />

			<label class="apw-preview-l"><?php _e('Preview'); ?>: </label>

			<span class="apw-excerpt-preview">
				<span class="apw-excerpt"><?php echo wp_html_excerpt( self::sample_excerpt(), 150, '&hellip;' ); ?></span>
			</span>
		</p>

		<?php
		$field = ob_get_clean();

		return $field;
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
	 * Builds form field: list_style
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $instance Current settings.
	 * @param object $widget Widget object.
	 */
	public static function build_field_list_style( $instance, $widget )
	{
		ob_start();
		?>
		<h4>Post Format</h4>
		<p>
			<label for="<?php echo $widget->get_field_id('list_style'); ?>">
				<?php _e( 'Post List Format:' ); ?>
			</label>
			<select name="<?php echo $widget->get_field_name('list_style'); ?>" id="<?php echo $widget->get_field_id('list_style'); ?>" class="widefat">
				<option value="ul" <?php selected( $instance['list_style'] , 'ul' ); ?>><?php _e( 'Unordered List (ul)'); ?></option>
				<option value="ol" <?php selected( $instance['list_style'] , 'ol' ); ?>><?php _e( 'Ordered List (ol)'); ?></option>
				<option value="div" <?php selected( $instance['list_style'] , 'div' ); ?>><?php _e( 'Div (div)'); ?></option>
			</select>
		</p>
		<?php
		$field = ob_get_clean();

		return $field;
	}


	/**
	 * Builds form field: post_format
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $instance Current settings.
	 * @param object $widget Widget object.
	 */
	public static function build_field_post_format( $instance, $widget )
	{
		ob_start();
		?>
		<p>
			<?php _e( 'Post Format:' ); ?><br />
			<label>
				<input class="radio" id="<?php echo $widget->get_field_id( 'post_format' ); ?>" name="<?php echo $widget->get_field_name( 'post_format' ); ?>" type="radio" value="html5" <?php checked( $instance['post_format'], 'html5'); ?>/>
				HTML5 &nbsp;
			</label>
			<label>
				<input class="radio" id="<?php echo $widget->get_field_id( 'post_format' ); ?>" name="<?php echo $widget->get_field_name( 'post_format' ); ?>" type="radio" value="xhtml" <?php checked( $instance['post_format'], 'xhtml'); ?>/>
				XHTML
			</label>
		</p>
		<?php
		$field = ob_get_clean();

		return $field;
	}

	/**
	 * Retrieves public post types
	 *
	 * Only returns post types that have posts enabled.
	 * Use 'apw_widget_post_type_args' to filter arguments for retrieving post types.
	 * Use 'apw_allowed_post_types' to filter post types that can be selected in the widget.
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @return array $_ptypes Filtered array of post types.
	 */
	public static function get_post_types()
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
		$_ptypes = self::_sanitize_select_array( $_ptypes );

		return $_ptypes;
	}


	/**
	 * Opens the post list for the current Recent Posts widget instance.
	 *
	 * Applies 'apw_start_list' filter on $output to allow extension by plugins.
	 * Applies 'apw_post_list_class' filter on list classes to allow extension by plugins.
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $instance Settings for the current Recent Posts widget instance.
	 * @param array $posts Posts to display.
	 *
	 * @return string $output Opening tag element for the post list.
	 */
	public static function start_list( $instance, $posts )
	{
        switch ( $instance['list_style'] ) {
            case 'div':
                $tag = 'div';
				break;
            case 'ol':
                $tag = 'ol';
                break;
            case 'ul':
            default:
                $tag = 'ul';
                break;
        }

		$classes = array();
		$classes[] = 'apw-posts-list';
		$classes = apply_filters( 'apw_post_list_class', $classes, $instance, $posts );
		$classes = ( ! is_array( $classes ) ) ? (array) $classes : $classes ;
		$classes = array_map( 'sanitize_html_class', $classes );
		$class_str = implode( ' ', $classes );

		$output = sprintf( '<%1$s class="%2$s">', $tag, $class_str );

		echo apply_filters( 'apw_start_list', $output, $instance, $posts );
	}


	/**
	 * Closes the post list for the current Recent Posts widget instance.
	 *
	 * Applies 'apw_end_list' filter on $output to allow extension by plugins.
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $instance Settings for the current Recent Posts widget instance.
	 * @param array $posts Posts to display.
	 *
	 * @return string $output Closing tag element for the post list.
	 */
	public static function end_list( $instance, $posts )
	{
        switch ( $instance['list_style'] ) {
            case 'div':
                $output = "</div>\n";
				break;
            case 'ol':
                $output = "</ol>\n";
                break;
            case 'ul':
            default:
                $output = "</ul>\n";
                break;
        }

		echo apply_filters( 'apw_end_list', $output, $instance, $posts );
	}


	/**
	 * Builds HTML5 post list
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $posts The posts returned from get_posts()
	 * @param array $instance Widget instance
	 *
	 * @return string $_posts HTML for post list.
	 */
	public static function build_html5_posts( $posts, $instance )
	{
		$_posts = '';

		foreach ( (array) $posts as $post ) {
			$_posts .= self::html5_post( $post, $instance );
		}

		return $_posts;
	}


	/**
	 * Builds XHTML post list
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param array $posts The posts returned from get_posts()
	 * @param array $instance Widget instance
	 *
	 * @return string $_posts HTML for post list.
	 */
	public static function build_posts( $posts, $instance )
	{
		$_posts = '';

		foreach ( (array) $posts as $post ) {
			$_posts .= self::post( $post, $instance );
		}

		return $_posts;
	}


	/**
	 * Outputs a single post in the HTML5 format.
	 *
	 * @uses APW_Recent_Posts_Utilities::get_apw_post_id()
	 * @uses APW_Recent_Posts_Utilities::get_apw_post_class()
	 * @uses APW_Recent_Posts_Utilities::get_apw_post_content()
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param object $post WP_Post Object
	 * @param object $instance Widget instance
	 */
	public static function html5_post( $post, $instance )
	{
		$apw_post_id    = self::get_apw_post_id( $post, $instance );
		$apw_post_class = self::get_apw_post_class( $post, $instance );
		$post_content   = self::get_apw_post_content( $post, $instance  );
		$tag = ( 'div' === $instance['list_style'] ) ? 'div' : 'li';
		?>

		<?php do_action( 'apw_post_before', $post, $instance ); ?>

		<<?php echo $tag; ?> id="post-<?php echo sanitize_html_class( $apw_post_id ); ?>" class="<?php echo $apw_post_class; ?>" >

			<article id="div-post-<?php echo sanitize_html_class( $apw_post_id ); ?>" class="post-body apw-post-body">

				<?php do_action( 'apw_post_top', $post, $instance ); ?>

				<footer class="post-meta apw-post-meta">

					<?php if ( $instance['show_thumbs'] ) : ?>
						<span class="post-avatar apw-post-avatar">
							<?php echo self::get_post_author_avatar( $post, $instance ); ?>
						</span>
					<?php endif; ?>

					<span class="post-header apw-post-header">
						<?php
						printf(
							_x( '%1$s <span class="on">on</span> %2$s', 'widgets' ),
							'<span class="post-author apw-post-author">' . get_post_author_link( $post ) . '</span>',
							'<span class="post-link apw-post-link"><a class="post-link apw-post-link" href="' . esc_url( get_post_link( $post ) ) . '">' . get_the_title( $post->post_post_ID ) . '</a></span>'
						);
						?>
					</span>

					<?php do_action( 'apw_post_meta', $post, $instance ); ?>

				</footer>

				<?php if ( $instance['show_excerpt'] ) : ?>
					<div class="post-content apw-post-content">
						<?php echo wp_html_excerpt( $post_content, absint( $instance['excerpt_length'] ), '&hellip;' ); ?>
					</div>
				<?php endif; ?>

				<?php do_action( 'apw_post_bottom', $post, $instance ); ?>

			</article>

		</<?php echo $tag; ?>>

		<?php do_action( 'apw_post_after', $post, $instance ); ?>

		<?php
	}


	/**
	 * Outputs a single post in the XHTML format.
	 *
	 * @uses APW_Recent_Posts_Utilities::get_apw_post_id()
	 * @uses APW_Recent_Posts_Utilities::get_apw_post_class()
	 * @uses APW_Recent_Posts_Utilities::get_apw_post_content()
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @param object $post WP_Post Object
	 * @param object $instance Widget instance
	 */
	public static function post( $post, $instance )
	{
		$apw_post_id    = self::get_apw_post_id( $post, $instance );
		$apw_post_class = self::get_apw_post_class( $post, $instance );
		$post_content   = self::get_apw_post_content( $post, $instance  );
		$tag = ( 'div' === $instance['list_style'] ) ? 'div' : 'li';
		?>

		<?php do_action( 'apw_post_before', $post, $instance ); ?>

		<<?php echo $tag; ?> id="post-<?php echo sanitize_html_class( $apw_post_id ); ?>" class="<?php echo $apw_post_class; ?>" >

			<div id="div-post-<?php echo sanitize_html_class( $apw_post_id ); ?>" class="post-body apw-post-body">

				<?php do_action( 'apw_post_top', $post, $instance ); ?>

				<div class="post-meta apw-post-meta">

					<?php if ( $instance['show_thumbs'] ) : ?>
						<span class="post-avatar apw-post-avatar">
							<?php echo self::get_post_author_avatar( $post, $instance ); ?>
						</span>
					<?php endif; ?>

					<span class="post-header apw-post-header">
						<?php
						printf(
							_x( '%1$s <span class="on">on</span> %2$s', 'widgets' ),
							'<span class="post-author apw-post-author">' . get_post_author_link( $post ) . '</span>',
							'<span class="post-link apw-post-link"><a class="post-link apw-post-link" href="' . esc_url( get_post_link( $post ) ) . '">' . get_the_title( $post->post_post_ID ) . '</a></span>'
						);
						?>
					</span>

					<?php do_action( 'apw_post_meta', $post, $instance ); ?>

				</div>

				<?php if ( $instance['show_excerpt'] ) : ?>
					<div class="post-content apw-post-content">
						<?php echo wp_html_excerpt( $post_content, absint( $instance['excerpt_length'] ), '&hellip;' ); ?>
					</div>
				<?php endif; ?>

				<?php do_action( 'apw_post_bottom', $post, $instance ); ?>

			</div>

		</<?php echo $tag; ?>>

		<?php do_action( 'apw_post_after', $post, $instance ); ?>

		<?php
	}


	/**
	 * Generate avatar markup
	 *
	 * @access public
	 * @since 1.0
	 *
	 * @param object $post  Post to display.
	 * @param array $instance Widget instance.
	 *
	 * @return string $avatar_string HTML of poster avatar.
	 */
	public static function get_post_author_avatar( $post, $instance )
	{
		$avatar_string = get_avatar( $post, $instance['thumb_size'] );
		$post_author_url = get_post_author_url( $post );
		if ( '' !== $post_author_url ) {
			$avatar_string = sprintf(
				'<a href="%1$s" class="author-link url" rel="external nofollow">%2$s</a>',
				esc_url($post_author_url),
				$avatar_string
			);
		};
		return $avatar_string;
	}


	/**
	 * Generates unique post id based on widget instance
	 *
	 * Applies 'apw_post_id' filter on post ID to allow extension by plugins.
	 *
	 * @access public
	 * @since 1.0
	 *
	 * @param object $post Post to display.
	 * @param array $instance Widget instance.
	 *
	 * @return string $apw_post_id Filtered post ID.
	 */
	public static function get_apw_post_id( $post, $instance )
	{
		$apw_post_id = $instance['widget_id'] . '-post-' . $post->post_ID;

		return apply_filters( 'apw_post_id', $apw_post_id, $post, $instance );
	}


	/**
	 * Generate post classes
	 *
	 * Applies 'apw_post_class' filter on post classes to allow extension by plugins.
	 *
	 * @access public
	 * @since 1.0
	 *
	 * @param object $post  Post to display.
	 * @param array $instance Widget instance.
	 *
	 * @return string $class_str Filtered post classes.
	 */
	public static function get_apw_post_class( $post, $instance )
	{
		$type = ( empty( $post->post_type ) ) ? 'post' : $post->post_type;

		$classes = array();
		$classes[] = 'post';
		$classes[] = 'apw-post';
		$classes[] = 'type-' . $type;
		$classes[] = 'apw-type-' . $type;
		$classes[] = 'recentposts';

		if ( $post->post_parent > 0 ) {
			$classes[] = 'child-post';
			$classes[] = 'parent-' . $post->post_parent;
		}

		$classes = apply_filters( 'apw_post_class', $classes, $post, $instance );
		$classes = ( ! is_array( $classes ) ) ? (array) $classes : $classes ;
		$classes = array_map('sanitize_html_class', $classes);

		$class_str = implode(' ', $classes);

		return $class_str;
	}


	/**
	 * Retrieves post content
	 *
	 * Applies 'apw_post_content' filter on post content to allow extension by plugins.
	 * Applies 'post_text' filter on post content to allow extension by plugins.
	 * Note: 'post_text' is Core WordPress filter
	 *
	 * @access public
	 * @since 1.0
	 *
	 * @param object $post  Post to display.
	 * @param array $instance Widget instance.
	 *
	 * @return string $post_content Filtered post content.
	 */
	public static function get_apw_post_content( $post, $instance )
	{
		$post_content = apply_filters('apw_post_content', $post->post_content, $post, $instance );

		return apply_filters( 'post_text', $post_content, $post, $instance );
	}


	/**
	 * Outputs plugin attribution
	 *
	 * @access public
	 *
	 * @since 1.0
	 *
	 * @return string Plugin attribution.
	 */
	public static function colophon( $echo = true )
	{
		$attribution = '<!-- Advanced Posts Widget by darrinb http://darrinb.com/plugins/advanced-posts-widget -->';

		if ( $echo ) {
			echo $attribution;
		} else {
			return $attribution;
		}
	}



	/**
	 * Cleans array of keys/values used in select drop downs
	 *
	 * @access private
	 *
	 * @since 1.0
	 *
	 * @param array $_options Values used for select options
	 * @param bool $sort Flag to sort the values alphabetically.
	 *
	 * @return array $_options Sanitized values.
	 */
	private static function _sanitize_select_array( $_options, $sort = true )
	{
		$_options = ( ! is_array( $_options ) ) ? (array) $_options : $_options ;

		// Clean the values (since it can be filtered by other plugins)
		$_options = array_map( 'esc_html', $_options );

		/**
		 * Flip to clean the keys (used as <option> values in <select> field on form)
		 */
		$_options = array_flip( $_options );
		$_options = array_map( 'sanitize_key', $_options );

		// Flip back
		$_options = array_flip( $_options );

		if( $sort ) { 
			asort( $_options );
		};

		return $_options;
	}

}