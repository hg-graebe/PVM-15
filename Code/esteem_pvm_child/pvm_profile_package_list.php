<?php
/*
Template Name: PVM Profile Package List
*/
require_once( ABSPATH . '/wp-content/plugins/pvmkit/classes/pvmkit_filtered_package_list.php' );
require_once( ABSPATH . '/wp-content/plugins/pvmkit/classes/pvmkit_author.php' );
?>

<?php get_header(); ?>

<?php do_action( 'esteem_before_body_content' ); ?>

    <div id="primary">
        <div id="content" class="clearfix">
            <div id="package_list_wrapper">
                <?php
				// load author data
				$author = new pvmkit_author();
				$author->load_by_author_id( $_GET['author_id'] );
				
				$author_is_current_user = false;
				$edit_actions = false;
				$project_id = -1;
				
				// check if user is on his own profile
				if ( $author->get_user_id() == get_current_user_id() ) {
					$author_is_current_user = true;
					$edit_actions = true;
				}
				
				if ( isset( $_GET[ 'propose' ] ) ) {
					$project_id = (int) $_GET[ 'propose' ];
				}
                ?>
                <h1 class="profile_author_name">Werke von <?php echo $author->get_full_name(); ?></h1>
                <h3 class="profile_author_location">Leipzig, Deutschland</h3>

                <?php
				
				// load packages
				$list = new pvmkit_filtered_package_list();
				if ( $author_is_current_user ) {
					$list->add_user( $author->get_user_id() );
				} else {
					$list->add_author( $_GET['author_id'] );
				}
				
				// include unpublished packages if current user is the author
				if ( $author_is_current_user ) {
					$list->set_status( 'byauthor' );
				}
				
				$list->request();
                ?>
                <div class="pvm_tile_wrapper">
                    <?php
                    foreach ( $list->get_all() as $package ) {
						echo '<div class="pvm_tile"><a href="' . get_package_url( $package->get_id() ) . '"><img src="' . $package->get_titleimage()->get_url( 'medium' ) . '" alt="" /></a>';
						if ( $edit_actions ) {
							$out = '';
							$status = $package->get_status();
							
							// edit mode
							if ( $status == 1 ) {
								echo '<div class="pvm_tile_icon"><a href="' . esc_url( get_workshop_url( 'view_package', 'mtpakid=' . $package->get_id() ) ) . '"><img src="' . get_stylesheet_directory_uri() . '/img/icon_edit.png" /></a></div>';
								//$out .= '<li><a href="' . esc_url( get_workshop_url( 'edit_package', 'mtpakid=' . $package->get_id() ) ) . '">bearbeiten</li>';
								$out .= '<li><a href="' . esc_url( get_workshop_url( 'package_actions', 'mtpakid=' . $package->get_id() . '&mtaction=lock' ) ) . '">freigeben</a></li>';
								$out .= '<li><a href="' . esc_url( get_workshop_url( 'package_actions', 'mtpakid=' . $package->get_id() . '&mtaction=delete' ) ) . '" onclick="return window.confirm(' . "'Das Werk wird unwiederruflich gel&ouml;scht.'" . ');">l&ouml;schen</a></li>';
							}
							
							// free to publish
							if ( $status == 5 ) {
								$out .= '<li><a href="' . esc_url( get_workshop_url( 'package_actions', 'mtpakid=' . $package->get_id() . '&mtaction=unlock' ) ) . '">zur&uuml;ckziehen</a></li>';
								$out .= '<li><a href="' . esc_url( get_workshop_url( 'package_actions', 'mtpakid=' . $package->get_id() . '&mtaction=delete' ) ) . '" onclick="return window.confirm(' . "'Das Werk wird unwiederruflich gel&ouml;scht.'" . ');">l&ouml;schen</a></li>';
							}
							
							// published
							if ( ( $status == 8 ) && ( $project_id > 0 ) ) {
								$out .= '<li><a href="' . esc_url( get_workshop_url( 'propose_package', 'mtpakid=' . $package->get_id() . '&mtprojid=' . $project_id ) ) . '">Werk vorschlagen</a></li>';
							}
							
							if ( $out != '' ) {
								echo '<ul class="pvm_tile_action_wrapper">' . $out . '</ul>';
								// echo '<div class="pvm_tile_note">Dieses Werk wurde noch nicht ver&ouml;ffentlicht!</div>';
							}
						}
                        echo '</div>';
                    }
                    ?>
                </div>
            </div><!-- #package_list_wrapper -->

        </div><!-- #content -->
    </div><!-- #primary -->

<?php do_action( 'esteem_after_body_content' ); ?>

<?php get_footer(); ?>