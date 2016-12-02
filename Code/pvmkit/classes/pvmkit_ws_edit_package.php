<?php

class pvmkit_ws_edit_package extends pvmkit_ws_module {
	
	protected $id = 'edit_package';
	protected $layout = 'fullwidth';
	protected $package_id = -1;
	protected $package = null;
	protected $can_edit = true;
	
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
		
		// what package are we even talking about?
		if ( isset( $_GET['mtpakid'] ) && is_numeric( $_GET['mtpakid'] ) ) {
			$this->package_id = (int) $_GET['mtpakid'];
		}
		
		if ( $this->package_id > 0 ) {
			$this->package = new pvmkit_package_editable( $this->package_id );
			
			if ( !$this->package->exists() ) {
				$this->can_edit = false;
			} else if ( !$this->package->can_edit() ) {
				$this->can_edit = false;
			}
		}
	}
	
	/*
	 *	returns HTML code for the main area
	 */
	public function get_content() {
		$o = '';
		
		if ( $this->can_edit ) {

			$o .= '<div class="ws_edit_username">
                    <h1 class="profile_author_name">' . $this->package->get_author()->get_full_name() . '</h1>
                    <h3 class="profile_author_location">' . $this->package->get_author()->get_location() . '</h3>
                </div>';
			
			// form output
			$o .= '<form method="post" action="' . $this->ws->get_url( 'view_package', 'mtpakid=' . $this->package_id ) . '">';
			$o .= '<input type="hidden" id="mtform" name="mtform" value="text" />';
			
			$o .= '<input type="hidden" id="mtpakid" name="mtpakid" value="' . $this->package->get_id() . '" />';
			$o .= '<div class="ws_edit_title"><label>Titel</label><input type="text" id="mttitle" name="mttitle" value="' . $this->package->get_title() . '" required /></div>';
			$o .= '<div class="ws_edit_textarea"><label>Text</label><textarea id="mttext" name="mttext" rows="20">' . $this->package->get_text()->get_content() . '</textarea></div>';
			
			if ( current_user_can( 'pvm_delegated_authorship' ) ) {
				$current_aid = $this->package->get_author()->get_author_id();
				
				$o .= '<select id="mtauthor" name="mtauthor">';
				$authors = get_author_profiles();
				foreach ( $authors as $author ) {
					$o .= ' <option value="' . $author['author_id'] . '"' . ( $current_aid == $author['author_id'] ? ' selected' : '' ) . '>' . $author['aname'] . '</option>';
				}
				$o .= '</select>';
			}
			
			//$o .= '<div><input type="submit" id="mtsubmit" name="mtsubmit" value="abschicken" /></div>';
			$o .= '<div class="pvm_bottom_link_row"><a href="' . $this->ws->get_url( 'view_package', 'mtpakid=' . $this->package_id ) . '" class="pvm_cancel">Abbrechen</a><input type="submit" id="mtsubmit" name="mtsubmit" value="Speichern" class="pvm_save" /></div>';
			
			$o .= '</form>';
			

		} else {
			
			// confirm message
			$o .= 'Du kannst dieses Werk nicht bearbeiten!';
			
		}
		
		
		return $o;
	}
	
}
?>