<?php
class pvmkit_ws_edit_image extends pvmkit_ws_module {
	
	protected $id = 'edit_image';
	protected $layout = 'fullwidth';
	
	protected $package_id = -1;
	protected $package = null;
	protected $type = '';
	protected $is_ok = true;
	
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
		// image or titleimage?
		if ( isset( $_GET['mttype'] ) ) {
			$this->type = $_GET['mttype'];
		}
		
		if ( $this->package_id > 0 ) {
			$this->package = new pvmkit_package_editable( $this->package_id );
			if ( !$this->package->exists() ) {
				$this->is_ok = false;
			} else if ( !$this->package->can_edit() ) {
				$this->is_ok = false;
			} else {
				if ( ( $this->type == 'image' ) || ( $this->type == 'titleimage' ) ) {
					
				} else {
					$this->is_ok = false;
				}
			}
		} else {
			$this->is_ok = false;
		}
		
	}
	
	/*
	 *	returns HTML code for the main area
	 */
	public function get_content() {
		$o = '<p>Du kannst dieses Bild nicht bearbeiten.</p>';
		
		if ( $this->is_ok ) {
			$o = '<form method="post" action="' . $this->ws->get_url( 'view_package', 'mtpakid=' . $this->package_id ) . '" enctype="multipart/form-data">';
			$o .= '<input type="hidden" id="mtform" name="mtform" value="image" />';
			$o .= '<input type="hidden" id="mtpakid" name="mtpakid" value="' . $this->package_id . '" />';
			$o .= '<input type="hidden" id="mttype" name="mttype" value="' . $this->type . '" />';
			
			// preview image
			if ( $this->package !== null ) {
				if ( $this->type == 'image' ) {
					if ( $this->package->has_image() ) {
						$o .= '<p><img src="' . $this->package->get_image()->get_url( 'large' ) . '" /></p>';
					}
				} else if ( $this->type == 'titleimage' ) {
					if ( $this->package->has_titleimage() ) {
						$o .= '<p><img src="' . $this->package->get_titleimage()->get_url( 'large' ) . '" /></p>';
					}
				}
			} else if ( $this->image !== null ) {
				$o .= '<img src="' . $this->image->get_url( 'large' ) . '" />'; 
			}
			
			$o .= '<input type="file" name="imagefile" id="imagefile" />';
			$o .= '<div class="pvm_bottom_link_row"><a href="' . $this->ws->get_url( 'view_package', 'mtpakid=' . $this->package_id ) . '" class="pvm_abort">Abbrechen</a><input type="submit" id="mtsubmit" name="mtsubmit" value="Speichern" class="pvm_save" /></div>';
			$o .= '</form>';
		}
		
		return $o;
	}
	
	// TODO: sidebar with checklist
	
}
?>