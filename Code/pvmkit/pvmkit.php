<?php
/**
* Plugin Name: PVMkit
* Plugin URI: 
* Description: Plugin for the PVM project
* Version: 0.2
* Author: Sebastian Guenther
* Author URI:
* License: 
*/

// exit on direct call
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// path defines
define( 'PVMKIT_UPLOAD_PATH', ABSPATH . 'wp-content/uploads/pvm/' );
define( 'PVMKIT_DIR', ABSPATH . 'wp-content/plugins/pvmkit/' );

// file for general functions
include_once( 'functions.php' );

// classes
require_once( 'classes/pvmkit_rating.php' );
require_once( 'classes/pvmkit_author_profile.php' );
require_once( 'classes/pvmkit_package_manager.php' );
require_once( 'classes/pvmkit_author.php' );
require_once( 'classes/pvmkit_package.php' );
require_once( 'classes/pvmkit_package_editable.php' );
require_once( 'classes/pvmkit_property_view.php' );
require_once( 'classes/pvmkit_project.php' );
require_once( 'classes/pvmkit_studio_package_processor.php' );
require_once( 'classes/pvmkit_object.php' );
require_once( 'classes/pvmkit_object_text.php' );
require_once( 'classes/pvmkit_object_titleimage.php' );
require_once( 'classes/pvmkit_object_image.php' );
require_once( 'classes/pvmkit_filtered_package_list.php' );
require_once( 'classes/pvmkit_workshop.php' );

// shortcodes
include_once( 'shortcodes/viewer.php' );
include_once( 'shortcodes/import.php' );
include_once( 'shortcodes/editor.php' );
include_once( 'shortcodes/manage_packages.php' );
// include_once( 'shortcodes/test_object.php' ); // tests only

// widgets
include_once( 'widgets/properties.php' );
include_once( 'widgets/package_meta.php' );

// backend
if ( is_admin() ) {
	include_once( 'backend/manage_packages_to_publish.php' );
}
?>