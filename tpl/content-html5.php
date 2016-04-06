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
$apw_post_date  = APW_Utils::get_apw_post_date( $apw_post, $instance );
?>

<?php do_action( 'apw_post_before', $apw_post, $instance ); ?>

<article id="post-<?php echo sanitize_html_class( $apw_post_id ); ?>" <?php post_class( $apw_post_class ); ?>>

	<?php do_action( 'apw_post_top', $apw_post, $instance ); ?>

	<header class="entry-header">
		<?php  if( $instance['show_thumb'] ) { APW_Views::apw_thumbnail_div( $apw_post, $instance ); }; ?>
		<?php the_title( sprintf( '<h3 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h3>' ); ?>
		<?php if ( $instance['show_date'] ) { echo $apw_post_date; } ?>

	</header><!-- /.entry-header -->

	<?php  if( $instance['show_excerpt'] ) { ?>
		<div class="entry-summary apw-entry-summary">
			<?php echo wpautop( $excerpt_text ); ?>
		</div><!-- /.entry-summary -->
	<?php }; ?>

	<?php do_action( 'apw_post_bottom', $apw_post, $instance ); ?>

</article><!-- #post-## -->

<?php do_action( 'apw_post_after', $apw_post, $instance ); ?>