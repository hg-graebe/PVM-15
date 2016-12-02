<?php
/*
Template Name: PVM View Single Project
*/
require_once( ABSPATH . '/wp-content/plugins/pvmkit/classes/pvmkit_author.php' );
require_once( ABSPATH . '/wp-content/plugins/pvmkit/classes/pvmkit_package.php' );
require_once( ABSPATH . '/wp-content/plugins/pvmkit/classes/pvmkit_project.php' );
?>

<?php get_header(); ?>

<?php do_action( 'esteem_before_body_content' ); ?>
	<div id="primary">
		<div id="content" class="clearfix">	
			<div id="single_package_wrapper">

				<?php
				$author = new pvmkit_author( get_current_user_id(), false );
				
				$project = new pvmkit_project( $_GET['id'] );
				if ( $project->exists() ) {
				?>
				<h3 class="profile_author_name"><?php echo $project->get( 'title'); ?></h3>
						<div class="single_project_wrapper">
							<div class="single_project_img_wrapper">
								<div class="single_project_text"><?php echo $project->get( 'text_describtion' ); ?></div>
								<div><ul>
									<?php 
									$p_list = $project->get_packages();
									foreach ( $p_list as $p_id ) {
										$p = new pvmkit_package( $p_id );
										echo '<li><a href="' . $p->get_url() . '">' . $p->get_title() . '</a></li>';
									}
									?>
								</ul></div>
								<ul class="project_actions"> <?php
									echo '<li><a href="' . get_home_url() . '/index.php/profile_package_list/?author_id=' . $author->get_author_id() . '&propose=' . $project->get( 'id' ) . '">Mein Werk vorschlagen</a></li>';
									
									$image = get_stylesheet_directory_uri() . "/img/MePROJECTS/Login.png";
									//echo "<li><a href='http://www.example.com'><img src='" . $image . "' /></</a></li>";

									$image = get_stylesheet_directory_uri() . "/img/MePROJECTS/Projektleiter_werden.png";
									//echo "<li><img src='" . $image . "' /></li>";

									$image = get_stylesheet_directory_uri() . "/img/MePROJECTS/Set_waehlen.png";
									//echo "<li><img src='" . $image . "' /></li>";

									$image = get_stylesheet_directory_uri() . "/img/MePROJECTS/Ausfuehrung_dokumentieren.png";
									//echo "<li><img src='" . $image . "' /></li>";

									$image = get_stylesheet_directory_uri() . "/img/MePROJECTS/Ergebnisse_hochladen.png";
									//echo "<li><img src='" . $image . "' /></li>";

									$image = get_stylesheet_directory_uri() . "/img/MePROJECTS/fertig.png";
									//echo "<li><img src='" . $image . "' /></li>";
								?>	
								</ul>		
							</div>
						</div>
				<?php
				} else {
					echo 'Das gew&auml;hlte Projekt existiert nicht.';
				}
				?>
			</div><!-- #single_package_wrapper -->
		</div><!-- #content -->
	</div><!-- #primary -->

	<?php do_action( 'esteem_after_body_content' ); ?>

<?php get_footer(); ?>