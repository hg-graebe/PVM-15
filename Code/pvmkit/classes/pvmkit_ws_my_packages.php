<?php
class pvmkit_ws_my_packages extends pvmkit_ws_module {
	
	protected $id = 'my_packages';
	protected $layout = 'fullwidth';
	
	protected $manager = null;
	protected $packages = null;
	
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
		
		$this->manager = new pvmkit_package_manager();
		
		// execute publish action
		$mtpubid = 0;
		if ( isset( $_GET['mtpubid'] ) && is_numeric( $_GET['mtpubid'] ) ) {
			$mtpubid = sanitize_text_field( $_GET['mtpubid'] );
			$this->manager->publish_by_user( $mtpubid );
		}
		
		// get list of packages
		$this->packages = $this->manager->get_packages_to_publish();
		
	}
	
	/*
	 *	returns HTML code for the main area
	 */
	public function get_content() {
		$o = '<p><a href="' . $this->ws->get_url( 'edit_package', 'mtpakid=0' ) . '"><div class="image_upload_text_ws"><img class="upload_button_ws" src="' . get_stylesheet_directory_uri() . '/img/Hochladen_Icon.png"/>Neues Werk hochladen</div></a></p>';
		
		$o .= '<div><p>' . $this->manager->debug_status() . '</p><table><tr><td>Titel</td><td>Hinzugef&uuml;gt</td><td>Aktionen</td></tr>';
		foreach ( $this->packages as $package ) {
			$o .= '<tr><td>' . $package->title . '</td><td>' . $package->publish_date_f . '</td><td><a href="' . esc_url( $this->ws->get_url( 'edit_package', 'mtpakid=' . $package->id ) ) . '">Bearbeiten</a> | <a href="' . esc_url( $this->ws->get_url( 'my_packages', 'mtpubid=' . $package->id ) ) . '">Freischalten</a></td></tr>';
		}
		$o .= '</table></div>';
		
		return $o;
	}
	
}
?>