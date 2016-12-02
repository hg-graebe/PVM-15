<?php

class pvmkit_ws_manage_authors extends pvmkit_ws_module {
	
	protected $id = 'manage_authors';
	protected $layout = 'sidebar';
	
	/*
	 *	checks if the current user is allowed to use this module
	 */
	public function user_has_access() {
		return current_user_can( 'pvm_delegated_authorship' );
	}
	
	/*
	 *	processes the request and prepares for output
	 */
	public function process() {
		
		if ( isset( $_POST['action'] ) ) {
			
			// create new profile if sent
			$action = $_POST['action'];
			$aname = $_POST['mtaname'];
			$location = $_POST['mtlocation'];
			
			if ( ( $action == 'create' ) && ( $aname != '' ) ) {
				$this->db->insert( 
					$this->db->prefix . 'pvmkit_authors', 
					array( 'user_id' => get_current_user_id(), 'full_name' =>  $aname, 'location' =>  $location ), 
					array( '%d', '%s', '%s' ) 
				);
			}
			
		} else if ( isset( $_GET['mtaction'] ) ) {
			
			// delete action
			$action = $_GET['mtaction'];
			
			// TODO - only delete, if no packages are left
			if ( $action == 'delete' ) {
				$this->ws->add_info( 'debug_info', array( 'Das L&ouml;schen von Autorenprofilen ist derzeit noch nicht m&ouml;glich.' ) );
			}
			
		}
	}
	
	/*
	 *	returns HTML code for the main area
	 */
	public function get_content() {
		// list all profiles in a table
		$authors = $this->db->get_results( 'SELECT author_id, full_name, location FROM ' . $this->db->prefix . 'pvmkit_authors WHERE user_id = ' . get_current_user_id() . ' ORDER BY author_id' );

		$o = '<div class="pvm_msg_info">Das Bearbeiten und L&ouml;schen von Autoren ist derzeit noch nicht m&ouml;glich.</div><table class="pvm_ws_table"><tr><th>Name</th><th>Ort</th><th>Aktionen</th></tr>';
		foreach ( $authors as $author ) {
			$o .= '<tr><td>' . $author->full_name . '</td><td>' . $author->location . '</td><td><a href="' . $author->author_id . '">Bearbeiten</a> <a href="">L&ouml;schen</a></td></tr>';
		}
		$o .= '</table>';
		
		return $o;
	}
	
	/*
	 *	returns HTML code for the sidebar
	 */
	public function get_sidebar() {
		
		// display a form to create a new author profile
		$o = '<form method="post" class="pvm_form_sidebar">';
		$o .= '<label>Name</label><input type="text" id="mtaname" name="mtaname" value="" required />';
		$o .= '<label>Ort</label><input type="text" id="mtlocation" name="mtlocation" value="" required />';
		$o .= '<button type="submit" name="action" value="create">Profil anlegen</button>';
		$o .= '</form>';
		
		return $o;
	}
}
?>