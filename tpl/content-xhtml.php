<?php
/**
 * The template part for displaying single posts
 *
 * @package APW_Recent_Posts
 * @subpackage Templates
 * @since 1.0
 */
$apw_post       = get_post();
$apw_post_id    = APW_Utils::get_apw_post_id( $apw_post, $instance );
$apw_post_class = APW_Utils::get_apw_post_class( $apw_post, $instance );
$excerpt_text   = APW_Utils::get_apw_post_excerpt( $apw_post, $instance );
?>

<div id="post-<?php echo sanitize_html_class( $apw_post_id ); ?>" <?php post_class( $apw_post_class ); ?>>

	<div class="entry-header">
		<?php the_title( sprintf( '<h3 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h3>' ); ?>
	</div><!-- /.entry-header -->

	<div class="entry-summary apw-entry-summary">
		<?php echo $excerpt_text; ?>
	</div><!-- /.entry-summary -->

	<div class="entry-footer"></div><!-- .entry-footer -->

</div><!-- #post-## -->