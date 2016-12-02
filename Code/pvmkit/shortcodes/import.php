<?php

// exit on direct call
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// returns HTML code representing the view of a package
function pvmkit_import_shortcode ( $attributes, $content = null ) {
	$sc_data = shortcode_atts( array( 'package_id' => '' ), $attributes );
	$sc_out = '';
    
	if ( is_user_logged_in () ) {
		// show form
		$sc_out .= '<form action="" method="post" enctype="multipart/form-data"><input type="file" name="zipfile" /><input type="submit" value="Hochladen" /></form>';
		
		// import package from ZIP
		$package = new pvmkit_package_editable();	
		$sc_out .= '<ul>' . $package->import_zip() . '</ul>';
	} else {
		$sc_out .= '<p>Zugriff verweigert. Bitte Anmelden.</p>';
	}
	
	return $sc_out;
}

// register the shortcode
add_shortcode( 'pvmkit_import', 'pvmkit_import_shortcode' );
?>