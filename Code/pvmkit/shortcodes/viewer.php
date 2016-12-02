<?php

// exit on direct call
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// returns HTML code representing the view of a package
function pvmkit_viewer_shortcode ( $attributes, $content = null ) {
	$sc_data = shortcode_atts( array('package_id' => '' ), $attributes );
    
	$out = '';
	
    // load packge from database
	$package = new pvmkit_package();
	$result = $package->load_by_id( $sc_data['package_id'] );
	
    if ( $result ) {
        
		$out .= '<h1>' . $package->get_title() . '</h1>';
		$out .= '<p>' . $package->get_author() . ' | ' . $package->get_publish_date() . '</p>';
		
		if ( $package->has_titleimage() ) {
			$out .= '<img src="' .  $package->get_titleimage()->get_url( 'large' ) . '" alt="" /><p><small>von ' . $package->get_titleimage()->get_author() . '</small></p>';
		}
		
		if ( $package->has_text() ) {
			$out .= '<p>' . $package->get_text()->get_content() . '</p><p><small>von ' . $package->get_text()->get_author() . '</small></p>';
		}
		
		if ( $package->has_image() ) {
			$out .= '<img src="' .  $package->get_image()->get_url( 'large' ) . '" alt="" /><p><small>von ' . $package->get_image()->get_author() . '</small></p>';
		}
    } else {
        $out .= '<h2>Fehler</h2><p>Das Paket konnte nicht gefunden werden.</p>';
    }
	
	return $out;
}

// register the shortcode
add_shortcode( 'pvmkit_viewer', 'pvmkit_viewer_shortcode' );
?>