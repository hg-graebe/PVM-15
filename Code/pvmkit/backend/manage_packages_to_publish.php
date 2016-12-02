<?php

/*
 *	Description: backend panel to publish packages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function pvmkit_backend_manage_packages_to_publish() {
    global $wpdb;
    
    // GET parameters
    $pvm_package_id = 0;
    if ( isset( $_GET['pvm_package_id'] ) && is_numeric( $_GET['pvm_package_id'] ) ) {
        $pvm_package_id = sanitize_text_field( $_GET['pvm_package_id'] );
    }
    $pvm_action = '';
    if ( isset( $_GET['pvm_action'] ) ) {
        $pvm_action = sanitize_text_field( $_GET['pvm_action'] );
    }
	
	
    if ( $pvm_package_id != 0 && $pvm_action == 'show-composition' ) {
		
		// ==== DO A PREVIEW ====
		
        echo '<div class="wrap">';
        echo '<p><a class="button-primary" href="' . esc_url( add_query_arg( array('pvm_package_id' => $pvm_package_id, 'pvm_action' => 'publish-composition' ), self_admin_url( 'admin.php?page=pvmkit-manage-packages-to-publish') ) ) . '">Freischalten</a> 
            <a class="button-secondary" href="' . esc_url( add_query_arg( array('pvm_package_id' => $pvm_package_id, 'pvm_action' => 'delete-composition' ), self_admin_url( 'admin.php?page=pvmkit-manage-packages-to-publish') ) ) . '" onclick="return confirm(' . "'Sind Sie sicher, dass Sie das Werk l&ouml;schen m&ouml;chten?'" . ');">L&ouml;schen</a></p>';
        echo pvmkit_viewer_shortcode ( array( 'package_id' => $pvm_package_id ) );
		echo '</div>';
		
    } else {
		
        echo '<div class="wrap"><h1>Werke freischalten</h1>';
		
        // ==== PUBLISH ====
        if ( $pvm_package_id != 0 && $pvm_action == 'publish-composition' ) {
			$wpdb->get_results( "UPDATE " . $wpdb->prefix . 'pvmkit_packages SET status = 8 WHERE id = ' . $pvm_package_id );
			if ( $wpdb->rows_affected == 1 ) {
				echo '<div class="updated"><p>' . $wpdb->rows_affected . ' Werk wurde ver&ouml;ffentlicht.</p></div>';
				$ws = new pvmkit_workshop();
				$package = new pvmkit_package( $pvm_package_id );
				$ws->add_notification( $package->get_user_id(), 'package_published', array( $package->get_id(), $package->get_title() ) );
				$ws->add_notification( 0, 'package_published_adm', array( $package->get_id(), $package->get_title(), wp_get_current_user()->user_login ) ); // ID = 0 means all admins
				$ws->send_email( $package->get_user_id(), 'Dein Werk bei ME&THINGS wurde veröffentlicht', "Dein Werk " . $package->get_title() . " auf ME&THINGS wurde veröffentlicht. \r\nSchau es dir direkt an: " . get_home_url() . '/index.php/single_package/?pkg_id=' . $package->get_id() );
				
				// now continue with the properties
				$properties = $wpdb->get_results( 'SELECT DISTINCT o.property_id AS prop FROM ' . $wpdb->prefix . 'pvmkit_object_properties AS o JOIN ' . $wpdb->prefix . 'pvmkit_package_components AS c ON c.object_id = o.object_id WHERE c.package_id = ' . $pvm_package_id );
				foreach ( $properties as $property ) {
					$res = $wpdb->insert(
						$wpdb->prefix . 'pvmkit_property_index', 
						array( 'package_id' => $pvm_package_id, 'property_id' =>  $property->prop ),
						array( '%d', '%d' ) 
					);
				}
				
			} else {
				echo '<div class="error"><p>Ein unbekannter Fehler ist aufgetreten.</p><p>' . $wpdb->last_error . '</p></div>';
			}
        }
        
        // ==== DELETE ====
        if ( $pvm_package_id != 0 && $pvm_action == 'delete-composition' ) {
            $wpdb->get_results( "UPDATE " . $wpdb->prefix . 'pvmkit_packages SET status = 0 WHERE id = ' . $pvm_package_id );
            if ( $wpdb->rows_affected > 0 ) {
                echo '<div class="updated"><p>' . $wpdb->rows_affected . ' Werk(e) wurde(n) gel&ouml;scht.</p></div>';
            } else {
                echo '<div class="error"><p>L&ouml;schen ist fehlgeschlagen!</p><p>' . $wpdb->last_error . '</p></div>';
            }
        }
		
    
        // table with all unpublished packages
        echo '<table class="widefat"><thead><tr><th>Titel</th><th>Autor</th><th>Aktionen</th></tr></thead><tfoot><tr><th>Titel</th><th>Autor</th><th>Aktionen</th></tr></tfoot><tbody>';
        $compositions_data = $wpdb->get_results( 'SELECT id, author, title FROM ' . $wpdb->prefix . 'pvmkit_packages WHERE status = 5 ORDER BY publish_date' );
        $uneven = true;
        foreach ($compositions_data as $composition_data) {
            
            $style = $uneven ? ' class="alternate"' : '';
            $uneven = !$uneven;
            
            echo '<tr' . $style . '><td>' . $composition_data->title . '</td>
                <td>' . $composition_data->author . '</td>
                <td>
                    <a href="' . esc_url( add_query_arg( array('pvm_package_id' => $composition_data->id, 'pvm_action' => 'show-composition' ), self_admin_url( 'admin.php?page=pvmkit-manage-packages-to-publish') ) ) . '">Vorschau</a> | 
                    <a href="' . esc_url( add_query_arg( array('pvm_package_id' => $composition_data->id, 'pvm_action' => 'publish-composition' ), self_admin_url( 'admin.php?page=pvmkit-manage-packages-to-publish') ) ) . '">Freischalten</a> | 
                    <a href="' . esc_url( add_query_arg( array('pvm_package_id' => $composition_data->id, 'pvm_action' => 'delete-composition' ), self_admin_url( 'admin.php?page=pvmkit-manage-packages-to-publish') ) ) . '" class="pvmkit-red-link" onclick="return confirm(' . "'Sind Sie sicher, dass Sie das Werk l&ouml;schen m&ouml;chten?'" . ');">L&ouml;schen</a>
                </td></tr>';
        }
        echo '</tbody></div>';
    }
}

// add to WP backend
function pvmkit_menu_manage_packages_to_publish(){
	add_menu_page( 'Werke freischalten', 'Neue Werke', 'edit_pages', 'pvmkit-manage-packages-to-publish', 'pvmkit_backend_manage_packages_to_publish');
}
add_action( 'admin_menu', 'pvmkit_menu_manage_packages_to_publish' );
?>