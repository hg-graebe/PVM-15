<?php

class pvmkit_ws_report extends pvmkit_ws_module {
	
	protected $id = 'report';
	protected $layout = 'fullwidth';
	
	protected $package_id = -1;
	protected $package = null;
	protected $showform = true;
	
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
		// get the ID, if given
		if ( isset( $_POST['mtpakid'] ) && is_numeric( $_POST['mtpakid'] ) ) {
			$this->package_id = (int) $_POST['mtpakid'];
		} else {
			if ( isset( $_GET['mtpakid'] ) && is_numeric( $_GET['mtpakid'] ) ) {
				$this->package_id = (int) $_GET['mtpakid'];
			} else {
				$this->package_id = 0;
			}
		}
		
		$this->package = new pvmkit_package( $this->package_id );
		
		if ( isset( $_POST['mtsubmit'] ) ) {
			if ( $this->package->exists() ) {
				$this->ws->add_notification( 0, 'package_reported', array( $this->package->get_id(), $this->package->get_title(), wp_get_current_user()->user_login, get_current_user_id(), $_POST[ 'mtreport' ] ) ); // 0 = admin notification
				$this->showform = false;
			}
		}
	}
	
	/*
	 *	returns HTML code for the main area
	 */
	public function get_content() {
		$o = '';
		
		if ( $this->showform ) {

			$o .= '<form method="post" action="' . $this->ws->get_url( 'report', 'mtpakid=' . $this->package->get_id() ) . '">';
			$o .= '<input type="hidden" id="mtpakid" name="mtpakid" value="' . $this->package->get_id() . '" />';
			$o .= '<label for="mtreport">Beschwerde</label><textarea id="mtreport" name="mtreport" rows="20"></textarea>';
			$o .= '<div class="pvm_bottom_link_row"><input type="submit" id="mtsubmit" name="mtsubmit" value="Melden" class="pvm_save" /></div>';
			$o .= '</form>';
			
		} else {
			
			// confirm message
			$o .= 'Deine Beschwerde wurde eingereicht.';
			
		}
		
		
		return $o;
	}
	
}
?>