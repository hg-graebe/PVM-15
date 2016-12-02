<?php
class pvmkit_ws_edit_user extends pvmkit_ws_module {
	
	protected $id = 'edit_user';
	protected $layout = 'fullwidth';
	
	protected $user_id = -1;
	protected $user = null;
	protected $action = '';
	protected $cap = '';
	protected $is_ok = true;
	protected $capabilities = array(
		'pvm_edit_other_packages' => 'fremde Werke bearbeiten',
		'pvm_manage_other_packages' => 'fremde Werke verwalten',
		'pvm_publish_packages' => 'Werke ver&ouml;ffentlichen',
		'pvm_edit_projects' => 'Projekte bearbeiten',
		'pvm_create_projects' => 'Projekte erstellen',
		'pvm_accept_projects' => 'Projekte freischalten',
		'pvm_invite_users_to_projects' => 'andere Benutzer zu Projekt einladen',
		'pvm_delegated_authorship' => 'unter anderen Autorenprofilen hochladen'
	);
	
	/*
	 *	checks if the current user is allowed to use this module
	 */
	public function user_has_access() {
		return current_user_can( 'edit_users' );
	}
	
	/*
	 *	processes the request and prepares for output
	 */
	public function process() {
		
		// get user_id by parameter
		if ( isset( $_GET['mtuserid'] ) ) {
			$this->user_id = (int) $_GET['mtuserid'];
		} 
		
		if ( $this->user_id <= 0 ) {
			$this->ws->add_error( 'missing_params', array('User-ID fehlt') );
			$this->is_ok = false;
		} else {
			
			// load user
			$this->user = new WP_User( $this->user_id );
			
			if ( isset( $_GET['mtaction'] ) ) {
				$this->action = sanitize_text_field( $_GET['mtaction'] );
			}
			if ( isset( $_GET['mtcap'] ) ) {
				$this->cap = sanitize_text_field( $_GET['mtcap'] );
			}
			
			// check if we have to execute an action
			if ( $this->cap != '' && isset( $this->capabilities[ $this->cap ] ) ) {
				if ( $this->action == 'add' ) {
					$this->user->add_cap( $this->cap );
				} else if ( $this->action == 'rem' ) {
					$this->user->remove_cap( $this->cap );
				} else {
					// quick actions
					switch ( $this->action ) {
						case '':
						
							break;
					}
				}
			}
		
		}
		
	}
	
	/*
	 *	returns HTML code for the main area
	 */
	public function get_content() {
		$o = '';
		
		if ( $this->is_ok ) {
			
			// show basic information about the user
			$author = new pvmkit_author();
			$author->load_by_user_id( $this->user_id );
			
			$o .= '<h2>Benutzerrechte verwalten</h2>';
			
			$o .= '<table><tr><th>Benutzer</th><th>User-ID</th><th>Author-ID</th></tr>';
			$u_list = $this->db->get_results( 'SELECT author_id, full_name FROM ' . $this->db->prefix . 'pvmkit_authors WHERE user_id = ' . $this->user_id );
			foreach ( $u_list as $u_data ) {
				$o .= '<tr><td>' . $u_data->full_name . '</td><td>' . $this->user_id . '</td><td>' . $u_data->author_id . '</td></tr>';
			}
			$o .= '</table>';
			
			// quick options like project leader
			
			
			// show list of capabilities with add/remove options
			$o .= '<table><tr><th></th><th>Recht</th></tr>';
			
			foreach ( $this->capabilities as $capability => $title ) {
				if ( user_can( $this->user, $capability ) ) {
					$o .= '<tr><td>&times;</td><td>' . $title . ' <a href="' . $this->ws->get_url( 'edit_user', 'mtuserid=' . $this->user_id . '&mtaction=rem&mtcap=' . $capability ) . '">[entfernen]</a></td></tr>';
				} else {
					$o .= '<tr><td></td><td>' . $title . ' <a href="' . $this->ws->get_url( 'edit_user', 'mtuserid=' . $this->user_id . '&mtaction=add&mtcap=' . $capability ) . '">[hinzuf&uuml;gen]</a></td></tr>';
				}
				
			}
			
			$o .= '</table>';
			
		} else {
			
			// confirm message
			$o .= '';
			
		}
		
		
		return $o;
	}
	
}
?>