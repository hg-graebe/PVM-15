<?php
/*
Template Name: PVM Profile Favorite List
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
                    $author = new pvmkit_author();
                    $author->load_by_author_id( (int) $_GET['author_id'] );
					
					$pvmkit_author_profile = new pvmkit_author_profile( $author->get_author_id() );
					$pvmkit_author_favorites = $pvmkit_author_profile->get_favorites( true );
                ?>
                <h1 class="profile_author_name"><?php echo $author->get_full_name(); ?></h1>
                <h3 class="profile_author_location">Leipzig, Deutschland</h3>

                <h3 class="profile_heading">Eigene Favoriten</h3>
                <div class="img_wrapper">
                    <?php
                    foreach ( $pvmkit_author_favorites as $package ) {
                        $image = '<a href="' . get_home_url() . '/index.php/single_package/?pkg_id=' . $package->get_id() . '"><div class="tile"><img class="tile_img" src="' . $package->get_titleimage()->get_url( 'medium' ) . '" alt="" /></div></a>';
                        echo $image;
                    }
                    ?>
                </div>
            </div><!-- #package_list_wrapper -->

        </div><!-- #content -->
    </div><!-- #primary -->

<?php do_action( 'esteem_after_body_content' ); ?>

<?php get_footer(); ?>