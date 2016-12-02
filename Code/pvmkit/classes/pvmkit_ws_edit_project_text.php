<?php

class pvmkit_ws_edit_project_text extends pvmkit_ws_module {
	
	protected $id = 'edit_project_text';
	protected $layout = 'fullwidth';
	
	protected $project_id = -1;
	protected $project = null;
	
	protected $can_edit = true;
	
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
		// get the ID, if given
		if ( isset( $_POST['mtprojid'] ) && is_numeric( $_POST['mtprojid'] ) ) {
			$this->project_id = (int) $_POST['mtprojid'];
		} else {
			if ( isset( $_GET['mtprojid'] ) && is_numeric( $_GET['mtprojid'] ) ) {
				$this->project_id = (int) $_GET['mtprojid'];
			} else {
				$this->project_id = 0;
			}
		}
		
		$this->project = new pvmkit_project();
		
		// load project
		if ( $this->project_id > 0 ) {
			$this->project->load_by_id( $this->project_id );
			
			if ( !$this->project->exists() ) {
				$this->can_edit = false;
			} else if ( !$this->project->can_edit() ) {
				$this->can_edit = false;
			}
		}
		
		// save changes
		if ( $this->project->exists() && $this->can_edit ) {
			// submitted?
			if ( isset( $_POST['mtsubmit'] ) ) {
				
				// save changes
				$data  = array(
					'text_documentation' => $_POST[ 'mtdocu' ],
					'text_result' => $_POST[ 'mtresult' ]
				);
				$this->project->set( $data );
			}
		}
	}
	
	/*
	 *	returns HTML code for the main area
	 */
	public function get_content() {
		$o = '';
		
		if ( $this->can_edit ) {

			$o .= '<form method="post" action="' . $this->ws->get_url( 'edit_project_text', 'mtprojid=' . $this->project->get( 'id' ) ) . '">';
			$o .= '<input type="hidden" id="mtprojid" name="mtprojid" value="' . $this->project->get( 'id' ) . '" />';
			$o .= '<label for="mtdocu">Dokumentation</label><textarea id="mtdocu" name="mtdocu" rows="20">' . $this->project->get( 'text_documentation' ) . '</textarea>';
			$o .= '<label for="mtresult">Ergebnis</label><textarea id="mtresult" name="mtresult" rows="20">' . $this->project->get( 'text_result' ) . '</textarea>';
			$o .= '<div class="pvm_bottom_link_row"><a href="' . $this->ws->get_url( 'edit_project_text', 'mtprojid=' . $this->project->get( 'id' ) ) . '" class="pvm_cancel">Abbrechen</a><input type="submit" id="mtsubmit" name="mtsubmit" value="Speichern" class="pvm_save" /></div>';
			$o .= '</form>';
			
		} else {
			
			// confirm message
			$o .= 'Du kannst dieses Projekt nicht bearbeiten!';
			
		}
		
		
		return $o;
	}
	
}
?>