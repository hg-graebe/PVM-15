<?php
/*
Template Name: PVM View All Projects Template
*/
require_once( ABSPATH . '/wp-content/plugins/pvmkit/classes/pvmkit_filtered_package_list.php' );
?>

<?php get_header(); ?>

	<?php do_action( 'esteem_before_body_content' ); ?>
	<?php get_sidebar( 'all_pkg_sidebar' ); ?>
	
	<div id="primary">
		<div id="content" class="clearfix">
			<div id="package_list_wrapper">	
				<div class="img_wrapper">
				<?php
					$p_list = $wpdb->get_results( 'SELECT project_id FROM ' . $wpdb->prefix . 'pvmkit_projects WHERE state = ' . "'done'" );
					foreach ( $p_list as $p_data ) {
						$p = new pvmkit_project( $p_data->project_id );
						echo '<div><a href="">' . $p . '</a></div>';
					}
				?>
				</div>
			</div><!-- #package_list_wrapper -->

		</div><!-- #content -->
	</div><!-- #primary -->

	<?php do_action( 'esteem_after_body_content' ); ?>

<?php get_footer(); ?>