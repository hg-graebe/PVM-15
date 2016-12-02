<?php

class pvmkit_ws_my_projects_proposed extends pvmkit_ws_module {
	
	protected $id = 'my_projects_proposed';
	protected $layout = 'fullwidth';
	
	/*
	 *	checks if the current user is allowed to use this module
	 */
	public function user_has_access() {
		return current_user_can( 'pvm_edit_projects' );
	}
	
	/*
	 *	processes the request and prepares for output
	 */
	public function process() {
		// TODO
	}
	
	/*
	 *	returns HTML code for the main area
	 */
	public function get_content() {
		$o = '';
		
		$p_list = $this->db->get_results( 'SELECT p.package_id, p.project_id FROM ' . $this->db->prefix . 'pvmkit_project_participants AS u JOIN ' . $this->db->prefix . 'pvmkit_projects_packages AS p ON p.project_id = u.project_id WHERE u.user_id = ' . get_current_user_id() . ' AND p.status = ' . "'proposed' AND u.role = 'owner'" );
		foreach ( $p_list as $p_data ) {
			$pa = new pvmkit_package( $p_data->package_id );
			$pr = new pvmkit_project( $p_data->project_id );
			$o .= '<div><a href="' . $pa->get_url() . '">' . $pa->get_title() . '</a> wurde f&uuml;r ' . $pr->get( 'project_title' ) . ' vorgeschlagen. | <a href="">Ablehnen</a> | <a href="">Annehmen</a></div>';
		}
		
		return $o;
	}
	
}
?>