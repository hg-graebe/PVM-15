<?php

class pvmkit_ws_propose_package extends pvmkit_ws_module {
	
	protected $id = 'propose_package';
	protected $layout = 'fullwidth';
	
	protected $msg = 'Es ist ein Fehler aufgetreten.';
	
	/*
	 *	checks if the current user is allowed to use this module
	 */
	public function user_has_access() {
		return is_user_logged_in();
	}
	
	/*
	 *	processes the request and prepares for output
	 */
	public function process() {
		
		$project_id = (int) $_GET[ 'mtprojid' ];
		$package_id = (int) $_GET[ 'mtpakid' ];
		
		if ( ( $project_id > 0 ) && ( $package_id > 0 ) ) {
			$project = new pvmkit_project( $project_id );
			if ( $project->exists() ) {
				$project->propose_package( $package_id );
				$this->msg = 'Das Werk wurde dem Projektteam vorgeschlagen.';
			} else {
				$this->msg = 'Das Projekt existiert nicht.';
			}
		}
	}
	
	/*
	 *	returns HTML code for the main area
	 */
	public function get_content() {
		return '<div>' . $this->msg . '</div>';
	}
	
}
?>