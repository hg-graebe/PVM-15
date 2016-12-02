<?php

class pvmkit_ws_edit_project_images extends pvmkit_ws_module {
	
	protected $id = 'edit_project_images';
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
				
				if ( isset( $_FILES['imagefile']['tmp_name'] ) ) {
				
					if ( file_exists( $_FILES['imagefile']['tmp_name'] ) ) {
						
						// insert into DB
						$this->db->insert(
							$this->db->prefix . 'pvmkit_project_images', 
							array( 'project_id' => $this->project_id ), 
							array( '%d' ) 
						);
						$image_id = $this->db->insert_id;

						// move file
						$image_file_path = PVMKIT_UPLOAD_PATH . 'project_images/' . $image_id . '_upl.jpg';
						move_uploaded_file( $_FILES['imagefile']['tmp_name'], $image_file_path );
						
						// save resized versions
						$image = wp_get_image_editor( $image_file_path );
						if ( !is_wp_error( $image ) ) {
							$image->save( PVMKIT_UPLOAD_PATH . 'project_images/' . $image_id . '_original.jpg' );
							$image->resize( 200, null, false );
							$image->save( PVMKIT_UPLOAD_PATH . 'project_images/' . $image_id . '_medium.jpg' );
						}
						
						// clean up
						unlink( $image_file_path );
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
		
		if ( $this->can_edit ) {

			$o .= '<form method="post" action="' . $this->ws->get_url( 'edit_project_images', 'mtprojid=' . $this->project->get( 'id' ) ) . '" enctype="multipart/form-data">';
			// ID
			$o .= '<input type="hidden" id="mtprojid" name="mtprojid" value="' . $this->project->get( 'id' ) . '" />';
			// image
			$o .= '<input type="file" name="imagefile" id="imagefile" />';
			$o .= '<div class="pvm_bottom_link_row"><a href="' . $this->ws->get_url( 'edit_project_images', 'mtprojid=' . $this->project->get( 'id' ) ) . '" class="pvm_abort">Abbrechen</a><input type="submit" id="mtsubmit" name="mtsubmit" value="Speichern" class="pvm_save" /></div>';
			$o .= '</form>';
			
		} else {
			
			// confirm message
			$o .= 'Du kannst dieses Projekt nicht bearbeiten!';
			
		}
		
		
		return $o;
	}
	
}
?>