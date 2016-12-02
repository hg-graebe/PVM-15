<?php

class pvmkit_ws_view_package extends pvmkit_ws_module {
	
	protected $id = 'view_package';
	protected $layout = 'sidebar';
	protected $package_id = -1;
	protected $package = null;
	protected $author = null;
	protected $can_edit = true;
	protected $complete = false;
	
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
		
		$spp = new pvmkit_studio_package_processor( $this->ws );
		$spp->save_form_data();
		
		if ( $this->package_id > 0 ) {
			// load existing package
			$this->package = new pvmkit_package_editable( $this->package_id );
			$this->can_edit = $this->package->can_edit();
		} else if ( $this->package_id == 0 ) {
			// create new one
			$this->package = new pvmkit_package_editable();
			$this->package->create( $this->ws->get_author()->get_user_id(), $this->ws->get_author()->get_author_id() );
			$this->package_id = $this->package->get_id();
			$this->can_edit = $this->package->can_edit();
		} else {
			$this->can_edit = false;
		}
		
		$this->complete = $this->package->has_text() && $this->package->has_titleimage() && $this->package->has_image() && $this->package->has_properties();
	}
	
	/*
	 *	returns HTML code for the main area
	 */
	public function get_content() {
		$o = '';
		
		if ( $this->can_edit ) {

			// output
			
			$o .= '<h1>' . $this->package->get_title() . '</h1>';
			$o .= wpautop( $this->package->get_text()->get_content() );
			$o .= '<p><a class="pvm_icon_link_24" href="' . $this->ws->get_url( 'edit_package', 'mtpakid=' . $this->package->get_id() ) . '"><img src="' .get_stylesheet_directory_uri() . '/img/icon_send_24.png" /><span>Text bearbeiten</span></a></p>';
			if ( $this->package->get_text()->get_content() == '' ) {
				$o .= '<div class="pvm_msg_warn">Schreibe ein paar Zeilen zu deinem Werk.</div>';
			}
			//$o .= '<p><a class="upload_button_ws_edit" href="' . $this->ws->get_url( 'edit_image', 'mttype=titleimage&mtpakid=' . $this->package->get_id() ) . '"><div class="upload_button_ws_edit_package"><img src="' . get_stylesheet_directory_uri() . '/img/Hochladen_Icon.png"/><br>Neues Bild hochladen</div></a>   ';
			//$o .= '<a class="upload_button_ws_edit" href="' . $this->ws->get_url( 'edit_image', 'mttype=image&mtpakid=' . $this->package->get_id() ) . '"><div class="upload_button_ws_edit_package"><img src="' . get_stylesheet_directory_uri() . '/img/Hochladen_Icon.png"/><br>Bildnerische Interpretation hochladen</div></a></p>';
			
			$o .= '<div class="pvm_ws_upload_row"><a class="pvm_ws_upload_image" href="' . $this->ws->get_url( 'edit_image', 'mttype=titleimage&mtpakid=' . $this->package->get_id() ) . '">' . ( $this->package->has_titleimage() ? '<img src="' . $this->package->get_titleimage()->get_url( 'large' ) . '" />' : '<img class="def" src="' . get_stylesheet_directory_uri() . '/img/icon_upload_128.png" />' ) . '<span>Titelbild hochladen</span></a>';
			$o .= '<a class="pvm_ws_upload_image" href="' . $this->ws->get_url( 'edit_image', 'mttype=image&mtpakid=' . $this->package->get_id() ) . '">' . ( $this->package->has_image() ? '<img src="' . $this->package->get_image()->get_url( 'large' ) . '" />' : '<img class="def" src="' . get_stylesheet_directory_uri() . '/img/icon_upload_128.png" />' ) . '<span>bildnerische Interpretation hochladen</span></a></div>';
			
		} else {
			
			// error message
			$o .= 'Du kannst dieses Werk nicht bearbeiten!';
			
		}
		
		
		return $o;
	}
	
	/*
	 *	returns HTML code for the sidebar
	 */
	public function get_sidebar() {
		
		$o = '<div><a class="pvm_icon_link_24" href="' . get_pvm_url( 'author_packages', array( $this->ws->get_author()->get_author_id() ) ) . '"><img src="' .get_stylesheet_directory_uri() . '/img/icon_back_24.png" /><span>zur&uuml;ck zur Werkliste</span></a></div>';
		
		if ( $this->can_edit ) {
			if ( $this->complete ) {
				$o .= '<div><a class="pvm_icon_link_24" href="' . esc_url( get_workshop_url( 'package_actions', 'mtpakid=' . $this->package->get_id() . '&mtaction=lock' ) ) . '" onclick="return window.confirm(' . "'Das Werk kann anschlie&szlig;end nicht mehr bearbeitet werden.'" . ');"><img src="' .get_stylesheet_directory_uri() . '/img/icon_send_24.png" /><span>Werk freigeben</span></a></div>';
			}
			$property_view = new pvmkit_property_view();
			$o .= $property_view->render( $this->package_id ) . '<div><a class="pvm_icon_link_24" href="' . $this->ws->get_url( 'edit_properties', 'mtpakid=' . $this->package->get_id() ) . '"><img src="' .get_stylesheet_directory_uri() . '/img/icon_send_24.png" /><span>Merkmale bearbeiten</span></a></div>';
			if ( !$this->package->has_properties() ) {
				$o .= '<div class="pvm_msg_warn">Deinem Werk fehlen noch Merkmale.</div>';
			}
		}
		
		return $o;
	}
	
}
?>