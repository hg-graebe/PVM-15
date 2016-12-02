<?php
/*
Template Name: PVM Studio
*/
require_once( ABSPATH . '/wp-content/plugins/pvmkit/classes/pvmkit_workshop.php' );
require_once( ABSPATH . '/wp-content/plugins/pvmkit/classes/pvmkit_ws_module.php' );
?>

<?php get_header(); ?>

<?php do_action( 'esteem_before_body_content' ); ?>

    <div id="primary">
        <div id="content" class="clearfix">
            
			<?php
			
			// init
			$workshop = new pvmkit_workshop();
			$module_class = $workshop->find_module();
			
			require_once( ABSPATH . '/wp-content/plugins/pvmkit/classes/' . $module_class . '.php' );
			$module = new $module_class( $workshop );
			
			if ( $module->user_has_access() ) {
				// process
				$module->process();
				
				// render
				switch ( $module->get_layout() ) {
					case 'sidebar':
						
						echo '<div class="pvm_ws_messages">' . $workshop->get_messages() . '</div><div class="pvm_ws_sidebar">' . $module->get_sidebar() . '</div><div class="pvm_ws_main_slim">' . $module->get_content() . '</div><div class="pvm_ws_clear"></div>';
						
						break;
					case 'fullwidth':
					
						echo '<div class="pvm_ws_messages">' . $workshop->get_messages() . '</div><div class="pvm_ws_main_large">' . $module->get_content() . '</div>';
						
						break;
					default:
						$workshop->add_error( 'system_error', array( 'INVALID_LAYOUT' ) );
						echo '<div>' . $workshop->get_messages() . '</div>';
				}
			} else {
				$workshop->add_error( 'access_denied', array() );
				echo '<div>' . $workshop->get_messages() . '</div>';
			}
			
			?>
            
        </div><!-- #content -->
    </div><!-- #primary -->

<?php do_action( 'esteem_after_body_content' ); ?>

<?php get_footer(); ?>