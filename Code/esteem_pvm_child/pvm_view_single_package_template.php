<?php
/*
Template Name: PVM View Single Package Template
*/
require_once( ABSPATH . '/wp-content/plugins/pvmkit/classes/pvmkit_filtered_package_list.php' );
require_once( ABSPATH . '/wp-content/plugins/pvmkit/classes/pvmkit_rating.php' );
?>

<?php get_header(); ?>

	<?php do_action( 'esteem_before_body_content' ); ?>
	<?php get_sidebar( 'single_pkg_sidebar' ); ?>

	<div id="primary">
		<div id="content" class="clearfix">	
			<div id="single_package_wrapper">
				<?php
				$package = new pvmkit_package( $_GET['pkg_id'] );
					
				if ( $package->exists() && $package->can_view() ) {
				?>
					<div class="single_package_wrapper">
						<div class="pvm_viewer_img_wrapper">
							<?php
							if ( $package->has_titleimage() ) {
								echo '<img id="pvm_viewer_img_large" src="' . $package->get_titleimage()->get_url( 'large' ) . '" alt="" />';
							}
							
							if ( $package->has_image() ) {
								echo '<div class="pvm_viewer_preview_wrapper"><img class="pvm_viewer_img_small" src="' . $package->get_image()->get_url( 'medium' ) . '" alt="" /></div>';
							}
							?>
						</div>
						<script type="text/javascript">
						jQuery(function($){
							$(".pvm_viewer_img_small").live('click', function() {
								var current = $("#pvm_viewer_img_large").attr("src");
								$("#pvm_viewer_img_large").attr("src", $(this).attr("src"));
								this.src = current;
							});
						});
						</script>
						<div class="single_package_text">
							<?php
							if ( $package->has_text() ) {
								echo wpautop( $package->get_text()->get_content() );
							}
							?>			
						</div>

						<div class="single_package_ratings" id="ratings">
							<?php
								$rating = new pvmkit_rating( $package->get_id() );
								$rating->update();
								$ratings = $rating->get_ratings();
								$rating_counts = $rating->get_rating_counts();
							?>
							<div class="rating_icon_wrapper">
								<?php if(is_user_logged_in()) { ?>
									<a href="<?php echo get_home_url() . '/index.php/single_package/?pkg_id=' . $package->get_id() . '&rate=' . ($ratings['nicetext'] ? 'no_nicetext' : 'nicetext') . '#ratings'; ?>">
										<img  class="rating_icon" src="<?php echo get_stylesheet_directory_uri(); ?>/img/nicetext<?php echo $ratings['nicetext'] ? '_set' : '_unset'; ?>.png" />
									</a>
								<?php } else { ?>
									<img  class="rating_icon" src="<?php echo get_stylesheet_directory_uri(); ?>/img/nicetext_unset.png" />
								<?php } ?>
								<span class="rating_text">Nice Text (<?php echo $rating_counts['nicetext']; ?>)</span>
							</div>
							<div class="rating_icon_wrapper">
								<?php if(is_user_logged_in()) { ?>
									<a href="<?php echo get_home_url() . '/index.php/single_package/?pkg_id=' . $package->get_id() . '&rate=' . ($ratings['coolfoto'] ? 'no_coolfoto' : 'coolfoto') . '#ratings'; ?>">
										<img  class="rating_icon" src="<?php echo get_stylesheet_directory_uri(); ?>/img/coolfoto<?php echo $ratings['coolfoto'] ? '_set' : '_unset'; ?>.png" />
									</a>
								<?php } else { ?>
									<img  class="rating_icon" src="<?php echo get_stylesheet_directory_uri(); ?>/img/coolfoto_unset.png" />
								<?php } ?>
								<span class="rating_text">Cool Foto (<?php echo $rating_counts['coolfoto']; ?>)</span>
							</div>
							<div class="rating_icon_wrapper">
								<?php if(is_user_logged_in()) { ?>
									<a href="<?php echo get_home_url() . '/index.php/single_package/?pkg_id=' . $package->get_id() . '&rate=' . ($ratings['goodwork'] ? 'no_goodwork' : 'goodwork') . '#ratings'; ?>">
										<img  class="rating_icon" src="<?php echo get_stylesheet_directory_uri(); ?>/img/goodwork<?php echo $ratings['goodwork'] ? '_set' : '_unset'; ?>.png" />
									</a>
								<?php } else { ?>
									<img  class="rating_icon" src="<?php echo get_stylesheet_directory_uri(); ?>/img/goodwork_unset.png" />
								<?php } ?>
								<span class="rating_text">Good Work (<?php echo $rating_counts['goodwork']; ?>)</span>
							</div>
							<div class="rating_icon_wrapper">
								<?php if(is_user_logged_in()) { ?>
									<a href="<?php echo get_home_url() . '/index.php/single_package/?pkg_id=' . $package->get_id() . '&rate=' . ($ratings['mething'] ? 'no_mething' : 'mething') . '#ratings'; ?>">
										<img  class="rating_icon" src="<?php echo get_stylesheet_directory_uri(); ?>/img/mething<?php echo $ratings['mething'] ? '_set' : '_unset'; ?>.png" />
									</a>
								<?php } else { ?>
									<img  class="rating_icon" src="<?php echo get_stylesheet_directory_uri(); ?>/img/mething_unset.png" />
								<?php } ?>
								<span class="rating_text">Me Thing (<?php echo $rating_counts['mething']; ?>)</span>
							</div>
						</div>
						<?php
						if ( current_user_can( 'pvm_manage_other_packages' ) ) {
							echo '<ul>';
							
							foreach ( $package->get_management_options() as $opt ) {
								echo '<li><a href="' . $opt['url'] . '"' . ( isset( $opt['add'] ) ? $opt['add'] : '' ) . '>' . $opt['label'] . '</a></li>';
							}
							echo '</ul>';
						}
						?>
					</div>
				<?php
							
					}					

				?>
			</div><!-- #single_package_wrapper -->
		</div><!-- #content -->
	</div><!-- #primary -->

	<?php do_action( 'esteem_after_body_content' ); ?>

<?php get_footer(); ?>