<?php
/**
 * Advanced Posts Widget
 *
 * @package APW_Recent_Posts
 *
 * @license     http://www.gnu.org/licenses/gpl-2.0.txt GPL-2.0+
 * @version     1.0
 *
 * Plugin Name: Advanced Posts Widget
 * Plugin URI:  http://darrinb.com/plugins/advanced-posts-widget
 * Description: A highly customizable recent posts widget.
 * Version:     1.0
 * Author:      Darrin Boutote
 * Author URI:  http://darrinb.com
 * Text Domain: advanced-posts-widget
 * Domain Path: /lang
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */


// No direct access
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}


/**
 * Instantiates the main Advanced Posts Widget instance
 *
 * @since 1.0
 */
function _advanced_posts_widget_init() {

	include dirname( __FILE__ ) . '/inc/class-apw-utils.php';
	include dirname( __FILE__ ) . '/inc/class-widget-apw-recent-posts.php';
	include dirname( __FILE__ ) . '/inc/class-apw-fields.php';
	include dirname( __FILE__ ) . '/inc/class-apw-views.php';
	include dirname( __FILE__ ) . '/inc/class-apw-recent-posts.php';
	
	$APW_Views = new APW_Views( __FILE__ );

	$APW_Recent_Posts = new APW_Recent_Posts( __FILE__ );
	$APW_Recent_Posts->init();

}
add_action( 'plugins_loaded', '_advanced_posts_widget_init', 99 );