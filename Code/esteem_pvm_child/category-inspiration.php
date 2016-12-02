<?php
/**
 * The template for displaying Archive page.
 *
 * @package ThemeGrill
 * @subpackage Esteem
 * @since Esteem 1.0
 */
?>

<?php get_header(); ?>

	<?php do_action( 'esteem_before_body_content' ); ?>

	<div id="primary">
		<div class="pvm_cat_desc">
			<h1><?php single_cat_title(''); ?></h1>
			<p><?php echo category_description( get_category( get_query_var( 'cat' ) )->cat_ID ); ?></p>
		</div>
		<div id="content" class="clearfix">

			<?php if ( have_posts() ) : ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php get_template_part( 'content', get_post_format() ); ?>

				<?php endwhile; ?>

				<?php get_template_part( 'navigation', 'archive' ); ?>

			<?php else : ?>

				<?php get_template_part( 'no-results', 'archive' ); ?>
				
			<?php endif; ?>

		</div><!-- #content -->
	</div><!-- #primary -->
	
	<?php esteem_sidebar_select(); ?>
	
	<?php do_action( 'esteem_after_body_content' ); ?>

<?php get_footer(); ?>