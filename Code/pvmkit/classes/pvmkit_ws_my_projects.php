<?php

class pvmkit_ws_my_projects extends pvmkit_ws_module {
	
	protected $id = 'my_projects';
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
		
	}
	
	/*
	 *	returns HTML code for the main area
	 */
	public function get_content() {
		$o = '';
		
		$p_list = $this->db->get_results( 'SELECT project_id FROM ' . $this->db->prefix . 'pvmkit_project_participants WHERE user_id = ' . get_current_user_id() );
		foreach ( $p_list as $p_data ) {
			$p = new pvmkit_project( $p_data->project_id );
			$o .= '<div><span>' . $p . '</span> | <a href="' . $this->ws->get_url( 'edit_project', 'mtprojid=' . $p->get( 'id' ) ) . '">Bearbeiten</a> | <a href="' . $this->ws->get_url( 'edit_project_text', 'mtprojid=' . $p->get( 'id' ) ) . '">Dokumentieren</a></div>';
		}
		
		return $o;
	}
	
}
?>