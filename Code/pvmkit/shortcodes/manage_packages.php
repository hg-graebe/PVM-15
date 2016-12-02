<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }



// renderer
function pvmkit_manage_packages_shortcode ( $attributes, $content = null ) {
	$sc_data = shortcode_atts( array(), $attributes );
	global $wpdb;
	$out = '';
	
    if ( is_user_logged_in() ) {
		
		// just show all packages for now
		$manager = new pvmkit_package_manager();
		
		// handle actions
		$pvm_publish = 0;
		if ( isset( $_GET['pvm_publish'] ) && is_numeric( $_GET['pvm_publish'] ) ) {
			$pvm_publish = sanitize_text_field( $_GET['pvm_publish'] );
			$manager->publish_by_user( $pvm_publish );
		}
		
		// show list of packages
		$packages = $manager->get_packages_to_publish();
		$out .= '<div><p>' . $manager->debug_status() . '</p><table><tr><td>Titel</td><td>Hinzugef&uuml;gt</td><td>Aktionen</td></tr>';
		foreach ( $packages as $package ) {
			$out .= '<tr><td>' . $package->title . '</td><td>' . $package->publish_date_f . '</td><td><a href="' . esc_url( home_url( '/werk-bearbeiten/?pvm_packageid=' . $package->id ) ) . '">Bearbeiten</a> | <a href="' . esc_url( home_url( '/werke-verwalten/?pvm_publish=' . $package->id ) ) . '">Freischalten</a></td></tr>';
		}
		$out .= '</table></div>';
		
	} else {
		$out = '<div><h2>Fehler</h2><p>Du musst angemeldet sein, um diese Funktion nutzen zu k&ouml;nnen!</p></div>';
	}
	
	return $out;
}

// register
add_shortcode( 'pvmkit_manage_packages', 'pvmkit_manage_packages_shortcode' );
?>