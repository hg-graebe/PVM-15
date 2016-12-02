<?php

// exit on direct call
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// returns HTML code
function pvmkit_editor_shortcode ( $attributes, $content = null ) {
	$sc_data = shortcode_atts( array(), $attributes );
	$sc_out = '';
	global $wpdb;
    
	if ( is_user_logged_in () ) {
		
		// process possible save action
		$pvm_packageid = 0;
		if ( isset( $_POST['pvm_packageid'] ) && is_numeric( $_POST['pvm_packageid'] ) ) {
			$pvm_packageid = (int) sanitize_text_field( $_POST['pvm_packageid'] );
			
			if ( $pvm_packageid > 0 ) {
				$author = new pvmkit_author();
				$author->load_by_user_id( get_current_user_id() );
				$package_data = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'pvmkit_packages WHERE id = ' . $pvm_packageid . ' AND author = ' . $author->get_author_id() . ' AND status < 5 AND status != 0 LIMIT 1' );
				if ( $package_data !== null ) {
					// its ok to edit, now save the package
					$package = new pvmkit_package_editable();
					$package->load_by_id( $pvm_packageid );
					
					// show form
					$pvm_title = sanitize_text_field( $_POST['pvm_title'] );
					$package->set_title( $pvm_title );
					$res = $package->update();
					
					$pvm_text = sanitize_text_field( $_POST['pvm_text'] );
					$package->get_text()->set_content( $pvm_text );
					$res = $package->get_text()->update() && $res;
					
					if ( $res === false ) {
						$sc_out .= '<p>Es ist ein Fehler aufgetreten.</p>';
					} else {
						$sc_out .= '<p>Gespeichert</p>';
					}
				}
			}
		}
		
		// if a package is referenced, check if it belongs to the user and has the right status
		$package = new pvmkit_package();
		$pvm_packageid = 0;
		if ( isset( $_GET['pvm_packageid'] ) && is_numeric( $_GET['pvm_packageid'] ) ) {
			$pvm_packageid = (int) sanitize_text_field( $_GET['pvm_packageid'] );
		}
		
		if ( $pvm_packageid > 0 ) {
			$author = new pvmkit_author();
			$author->load_by_user_id( get_current_user_id() );
			$package_data = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'pvmkit_packages WHERE id = ' . $pvm_packageid . ' AND author = ' . $author->get_author_id() . ' AND status < 5 AND status != 0 LIMIT 1' );
			if ( $package_data !== null ) {
				// its ok to edit, now load the package
				$package = new pvmkit_package();
				$package->load_by_id( $pvm_packageid );
				
				// show form
				$sc_out .= '<form action="" method="post" enctype="multipart/form-data">';
				$sc_out .= '<input type="hidden" name="pvm_packageid" id="pvm_packageid" value="' . $package->get_id() . '" /><br />';
				$sc_out .= '<input type="text" name="pvm_title" id="pvm_title" value="' . $package->get_title() . '" /><br />';
				$sc_out .= '<textarea name="pvm_text" id="pvm_text" cols="200" rows="10">' . $package->get_text()->get_content() . '</textarea><br />';
				$sc_out .= '<input type="submit" value="Speichern" /></form>';
			}
		}
	} else {
		$sc_out .= '<p>Zugriff verweigert. Bitte Anmelden.</p>';
	}
	
	return $sc_out;
}

// register the shortcode
add_shortcode( 'pvmkit_editor', 'pvmkit_editor_shortcode' );
?>