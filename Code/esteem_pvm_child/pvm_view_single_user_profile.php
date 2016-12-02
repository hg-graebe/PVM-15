<?php
/*
Template Name: PVM View Single User Profile
*/
require_once( ABSPATH . '/wp-content/plugins/pvmkit/classes/pvmkit_filtered_package_list.php' );
require_once( ABSPATH . '/wp-content/plugins/pvmkit/classes/pvmkit_package.php' );
require_once( ABSPATH . '/wp-content/plugins/pvmkit/classes/pvmkit_author.php' );
?>

<?php get_header(); ?>

<?php do_action( 'esteem_before_body_content' ); ?>

    <div id="primary">
        <div id="content" class="clearfix">
            <div id="single_package_wrapper">
                <?php
					// load all author data
                    $author = new pvmkit_author( $_GET['author_id'] );
					
					
					
					$author_is_current_user = false;
					if ( $author->get_user_id() == get_current_user_id() ) {
						$author_is_current_user = true;
					}
					
					if ( $author->get_author_id() == -1 ) {
						// Error text if user doesn't exist
						echo 'Dieses Profil existiert nicht.';
					} else {
                ?>

                <div class="profile_content_left">
                    <div class="profile_img_container">
                        <a href="<?php echo get_workshop_url( 'edit_user_image' ); ?>">
                            <img class="profile_img" src="<?php echo $author->get_profile_image_url(); ?>" />
                            <span>Profilbild ändern.</span>
                        </a>
                    </div>

                <div class="user_profile_social_media_container">
                    <div class="social_media_share">
                        <a href="http://www.pinterest.com">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/printerest_icon.png"/> 
                            <span> Share on Pinterest </span>
                        </a>
                    </div>
                    <div class="social_media_share">
                        <a href="http://www.facebook.com">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/facebook_icon.png"/> 
                            <span> Share on Facebook </span>
                        </a>
                    </div>
                    <div class="social_media_share">
                        <a href="http://www.twitter.com">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/twitter_icon.png"/> 
                            <span> Share on Twitter </span>
                        </a>
                    </div>
                    <div class="social_media_share"> <a href="about:robots"> www.homepage.com </a> </div>
                </div>
                    

                </div>
                <div class="profile_content_right">
                    <h1 class="profile_author_name"><?php echo $author->get_full_name(); ?></h1>
                    <h3 class="profile_author_location">Leipzig, Deutschland</h3>
                    <?php
                        $packages = new pvmkit_filtered_package_list();
                        $packages->add_author($author->get_author_id());
                        $packages->request();
						$image = plugins_url() . '/pvmkit/images/default_titleimage_large.jpg'; // TODO: get default image
						if ( $packages->get_count() > 0 ) {
							$package = $packages->get(0);
							$image = $package->get_titleimage()->get_url( 'large' );
						}
                    ?>

                    <a href="<?php echo get_home_url() . '/index.php/profile_package_list/?author_id=' . $_GET['author_id']; ?>">
                        <div class="profile_package_preview_container">
                            <div class="profile_package_preview_img_container">
                                <img class="profile_package_preview_img" src="<?php echo $image; ?>" alt="" /><br />
                            </div>
                            <div class="profile_package_preview_label">Eigene Werke <span>(<?php echo $packages->get_count(); ?>)</span></div>
                        </div>
                    </a>
					<?php
						// load favorites
						$pvmkit_author_profile = new pvmkit_author_profile( $author->get_author_id() );
						$pvmkit_author_favorites = $pvmkit_author_profile->get_favorites();
						
					    $image = plugins_url() . '/pvmkit/images/default_image_large.jpg'; // TODO: get default image
                    	
						if ( count( $pvmkit_author_favorites ) > 0 ) {
							
							$package = new pvmkit_package();
							$package->load_by_id( end( $pvmkit_author_favorites ) ) ;
							$image = $package->get_titleimage()->get_url( 'large' );
                    		
						}
                    ?> 
					<!-- Link added-->
                    <a href="<?php echo get_home_url() . '/index.php/autor-favoriten/?author_id=' . $_GET['author_id']; ?>">
                    <div class="profile_package_preview_fav_container">
                        <div class="profile_package_preview_img_container">
                            <img class="profile_package_preview_img" src="<?php echo $image; ?>" alt="" /><br />
                        </div>
                        <div class="profile_package_preview_label">Favoriten (<?php echo count($pvmkit_author_favorites); ?>)</div>
                    </div>
                    </a>
				</div>
				<div style="clear:both;"></div>
				<div class="pvm_2c_row_wrapper">
					<div class="pvm_author_profile">
						<div class="pvm_author_profile_l">
						
						</div>
						<div class="pvm_author_profile_r">
							<div>
							
							</div>
							<div>
								<?php
								if ( $author_is_current_user ) {
									echo '<a class="pvm_icon_link" href="' . get_workshop_url( 'view_package', 'mtpakid=0' ) . '"><img src="' . get_stylesheet_directory_uri() . '/img/icon_upload_256.png" /><span>Neues Werk hochladen</span></a>';
								}
								?>
							</div>
						</div>
					</div>
					<div class="pvm_2c_row">
						<!-- Admin options -->
						<?php
						if ( current_user_can( 'edit_users' ) ) {
							echo '<ul><li><a href="' . get_workshop_url( 'edit_user', 'mtuserid=' . $author->get_user_id() ) . '">Benutzerrechte bearbeiten</a></li></ul>';
						}
						?>
					</div>
				</div>
					<?php } ?>
            </div><!-- #single_package_wrapper -->
        </div><!-- #content -->
    </div><!-- #primary -->

<?php do_action( 'esteem_after_body_content' ); ?>

<?php get_footer(); ?>