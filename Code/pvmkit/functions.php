<?php
// exit on direct call
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// removes backend menu bar in frontend
if ( !is_admin() ) {
	add_filter('show_admin_bar', '__return_false');
}

// add query vars
function pvmkit_add_query_vars( $vars ){
  $vars[] = 'filters';
  $vars[] = 'rate';
  $vars[] = 'mtm';
  $vars[] = 'mtp';
  return $vars;
}
add_filter( 'query_vars', 'pvmkit_add_query_vars' );

// login redirect
function pvm_login_redirect( $redirect_to, $request, $user ) {
	if ( isset( $user->roles ) && is_array( $user->roles ) ) {
		//check for admins
		if ( in_array( 'administrator', $user->roles ) ) {
			return $redirect_to;
		} else {
			return get_home_url() . '/studio/?mtm=welcome';
		}
	} else {
		return $redirect_to;
	}
}

add_filter( 'login_redirect', 'pvm_login_redirect', 10, 3 );

/*
 *	returns the URL for the workshop with the module
 */
function get_workshop_url( $module = 'my_packages', $params = '' ) {		// default URL
	return get_home_url() . '/studio/?mtm=' . $module . '&' . $params;
}


function get_pvm_url( $site, $params = array() ) {
	switch ( $site ) {
		case 'author_profile':
			return '';
		case 'author_packages':
			return get_home_url() . vsprintf( '/profile_package_list/?author_id=%1$d', $params );
	}
}

/*
 *	returns the URL for a package
 */
function get_package_url( $package_id ) {
	return get_home_url() . '/index.php/single_package/?pkg_id=' . $package_id;
}

/*
 *	returns data from the pvmkit_authors table
 */
function get_author_profiles( $user_id = -1 ) {
	global $wpdb;
	$a = array();
	
	$user_id = (int) $user_id;
	if ( $user_id == -1 ) {
		$user_id = get_current_user_id();
	}
	
	if ( $user_id > 0 ) {
		$authors = $wpdb->get_results( 'SELECT author_id, full_name, location FROM ' . $wpdb->prefix . 'pvmkit_authors WHERE user_id = ' . $user_id . ' ORDER BY author_id ASC' );
		foreach ( $authors as $author ) {
			$a[] = array( 'author_id' => $author->author_id, 'aname' => $author->full_name, 'location' => $author->location );
		}
	}
	
	return $a;
}
?>