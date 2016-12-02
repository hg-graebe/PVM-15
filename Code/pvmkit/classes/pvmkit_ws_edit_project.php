<?php

class pvmkit_ws_edit_project extends pvmkit_ws_module {
	
	protected $id = 'edit_project';
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
		if ( $this->can_edit ) {
			// submitted?
			if ( isset( $_POST['mtsubmit'] ) ) {
				if ( !$this->project->exists() ) {
					$this->project->create();
				}
				
				// save changes
				$data  = array(
					'project_title' => $_POST[ 'mttitle' ],
					'institute' => $_POST[ 'mtinst' ],
					'institute_url' => $_POST[ 'mtinsturl' ],
					'institute_group' => $_POST[ 'mtgroup' ],
					'text_describtion' => $_POST[ 'mttext' ]
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

			$o .= '<form method="post" action="' . $this->ws->get_url( 'edit_project', 'mtprojid=' . $this->project->get( 'id' ) ) . '">';
			// ID
			$o .= '<input type="hidden" id="mtprojid" name="mtprojid" value="' . $this->project->get( 'id' ) . '" />';
			// set
			$o .= '<input type="hidden" id="mtset" name="mtset" value="' . $this->project->get( 'project_set' ) . '" />';
			// title
			$o .= '<label for="mttitle">Titel</label><input type="text" id="mttitle" name="mttitle" value="' . $this->project->get( 'project_title' ) . '" />';
			// institute
			$o .= '<label for="mtinst">Institution</label><input type="text" id="mtinst" name="mtinst" value="' . $this->project->get( 'institute' ) . '" />';
			// institute URL
			$o .= '<label for="mtinsturl">Website</label><input type="text" id="mtinsturl" name="mtinsturl" value="' . $this->project->get( 'institute_url' ) . '" />';
			// group
			$o .= '<label for="mtgroup">Gruppe</label><input type="text" id="mtgroup" name="mtgroup" value="' . $this->project->get( 'institute_group' ) . '" />';
			// describtion
			$o .= '<label for="mttext">Beschreibung</label><textarea id="mttext" name="mttext" rows="20">' . $this->project->get( 'text_describtion' ) . '</textarea>';
			
			$o .= '<div class="pvm_bottom_link_row"><a href="' . $this->ws->get_url( 'edit_project', 'mtprojid=' . $this->project->get( 'id' ) ) . '" class="pvm_cancel">Abbrechen</a><input type="submit" id="mtsubmit" name="mtsubmit" value="Speichern" class="pvm_save" /></div>';
			$o .= '</form>';
			
		} else {
			
			// confirm message
			$o .= 'Du kannst dieses Projekt nicht bearbeiten!';
			
		}
		
		
		return $o;
	}
	
}
?>