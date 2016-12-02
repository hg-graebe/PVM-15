<?php
/*
Template Name: PVM View All Packages Template
*/
require_once( ABSPATH . '/wp-content/plugins/pvmkit/classes/pvmkit_filtered_package_list.php' );
?>

<?php get_header(); ?>

	<?php do_action( 'esteem_before_body_content' ); ?>
	<?php get_sidebar( 'all_pkg_sidebar' ); ?>
	
	<div id="primary">
		<div id="content" class="clearfix">
			<div style="margin-bottom: 20px" id="tutorial_wrapper">
				<iframe width="520" height="290" src="https://www.youtube.com/embed/RKxK3l564Uk" frameborder="0" allowfullscreen></iframe>
				<a href="<?php echo home_url(); ?>/tutorial/"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/tutorial.png" alt="Tutorial" /></a>
				<div class="pvm_clearfix"></div>
			</div>
			<div id="package_list_wrapper">	
				<?php
					$list = new pvmkit_filtered_package_list();
					
					// set up the filter
					$list->set_sorting_order( 'DESC' );
					$list->set_sorting_field( 'pa.publish_date' );
					if( isset( $_GET['filters'] ) ) {
						$filters = explode( '-', $_GET['filters'] );
						foreach( $filters as $filter ) {
							$list->add_filter( $filter );
						}
					}
					
					// pagination logic
					$page = 0;
					$ipp = 30;
					if ( isset( $_GET['mtp'] ) ) {
						$page = (int) $_GET['mtp'];
					}
					if ( $page < 1 ) {
						$page = 1;
					}
					$total = $list->get_total_result_count();
					$maxpage = ceil( $total / $ipp );
					if ( $page > $maxpage ) {
						$page = $maxpage;
					}
					$list->set_min_max( max( ( $page - 1 ) * $ipp , 0 ), $ipp );
					
					$list->request();
				?>
					<div class="img_wrapper">
				<?php
					foreach ( $list->get_all() as $package ) {
						//$image = '<a href="' . get_home_url() . '/index.php/single_package/?pkg_id=' . $package->get_id() . '"><div class="tile"><img class="tile_img" src="' .  plugins_url() . '/pvmkit/uploads/data/' . $package->get_titleimage()->get_object_id() . '_medium_' . $package->get_titleimage()->get_content() . '.jpg" alt="" /></div></a>';			
						$image = '<a href="' . get_home_url() . '/index.php/single_package/?pkg_id=' . $package->get_id() . '"><div class="tile"><img class="tile_img" src="' . $package->get_titleimage()->get_url( 'medium' ) . '" alt="" /></div></a>';			
						echo $image;	
					}
				?>
					</div>
					<ul class="pvm_pagination">
						<?php
						$page_url = home_url() . '/ausstellung/?' . $_SERVER[ 'QUERY_STRING' ];
						for ( $i = 1; $i <= $maxpage; $i++ ) {
							echo '<li><a href="' . esc_url( add_query_arg( array( 'mtp' => $i ), $page_url ) ) . '"' . ( $page == $i ? ' class="active"' : '' ) . '>' . $i . '</a></li>';
						}
						?>
					</ul>
			</div><!-- #package_list_wrapper -->

		</div><!-- #content -->
	</div><!-- #primary -->

	<?php do_action( 'esteem_after_body_content' ); ?>

<?php get_footer(); ?>