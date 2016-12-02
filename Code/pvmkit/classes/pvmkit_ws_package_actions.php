<?php
class pvmkit_ws_package_actions extends pvmkit_ws_module {
	
	protected $id = 'package_actions';
	protected $layout = 'fullwidth';
	
	protected $package_id = -1;
	protected $action = '';
	protected $is_ok = true;
	
	protected $package_manager = null;
	
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
		
		// get params
		if ( isset( $_GET['mtpakid'] ) && is_numeric( $_GET['mtpakid'] ) ) {
			$this->package_id = (int) $_GET['mtpakid'];
		}
		if ( isset( $_GET['mtaction'] ) ) {
			$this->action = $_GET['mtaction'];
		}
		
		// init objects
		$this->package_manager = new pvmkit_package_manager();
		
		// process actions
		// TODO: check rights
		if ( $this->package_id > 0 ) {
			
			$package = new pvmkit_package_editable( $this->package_id );
			if ( !$package->exists() ) {
				$this->is_ok = false;
				return false;
			}
			
			switch ( $this->action ) {
				case 'lock':
					if ( $package->can_manage() ) {
						if ( $this->package_manager->lock_package( $this->package_id ) ) {
							$this->ws->add_info( 'package_locked', array() );
						} else {
							$this->ws->add_error( 'unknown_error', array() );
							$this->is_ok = false;
						}
					} else {
						$this->ws->add_error( 'unknown_error', array() );
						$this->is_ok = false;
					}
					break;
				case 'unlock':
					if ( $package->can_manage() && $this->package_manager->unlock_package( $this->package_id ) ) {
						$this->ws->add_info( 'package_unlocked', array() );
					} else {
						$this->ws->add_error( 'unknown_error', array() );
						$this->is_ok = false;
					}
					break;
				case 'publish':
					if ( current_user_can( 'pvm_publish_packages' ) && $this->package_manager->publish_package( $this->package_id ) ) {
						$this->ws->add_info( 'package_published', array() );
						$this->ws->add_notification( $package->get_user_id(), 'package_published', array( $package->get_id(), $package->get_title() ) );
						$this->ws->add_notification( 0, 'package_published_adm', array( $package->get_id(), $package->get_title(), wp_get_current_user()->user_login ) ); // ID = 0 means all admins
						$this->ws->send_email( $package->get_user_id(), 'Dein Werk bei ME&THINGS wurde veröffentlicht', "Dein Werk " . $package->get_title() . " auf ME&THINGS wurde veröffentlicht. \r\nSchau es dir direkt an: " . get_home_url() . '/index.php/single_package/?pkg_id=' . $package->get_id() );
					} else {
						$this->ws->add_error( 'unknown_error', array() );
						$this->is_ok = false;
					}
					break;
				case 'unpublishrev':
					if ( current_user_can( 'pvm_publish_packages' ) ) {
						$res = $this->package_manager->unpublish_package( $this->package_id, 5 );
						foreach ( $res as $msg ) {
							$this->ws->add_info( 'info', array( $msg ) );
						}
						$this->ws->add_info( 'package_unpublishedrev', array() );
					} else {
						$this->ws->add_error( 'access_denied', array() );
						$this->is_ok = false;
					}
					break;
				case 'unpublishws':
					if ( current_user_can( 'pvm_publish_packages' ) ) {
						$res = $this->package_manager->unpublish_package( $this->package_id, 1 );
						foreach ( $res as $msg ) {
							$this->ws->add_info( 'info', array( $msg ) );
						}
						$this->ws->add_info( 'package_unpublishedws', array() );
						$this->ws->add_notification( $package->get_user_id(), 'package_rejected', array( $package->get_id(), $package->get_title(), 'Ein Administrator hat es abgewiesen.' ) );
						$this->ws->send_email( $package->get_user_id(), 'Dein Werk bei ME&THINGS wurde zurückgestellt', "Dein Werk " . $package->get_title() . " auf ME&THINGS wurde in die Werkstatt zurückgestellt. Möglicherweise stimmt damit etwas nicht. Nimm dir bitte einen Moment Zeit und behebe das Problem, damit dein Meisterwerk wieder für alle sichtbar wird." );
					} else {
						$this->ws->add_error( 'access_denied', array() );
						$this->is_ok = false;
					}
					break;
				case 'delete':
					if ( $package->can_manage() && $this->package_manager->delete_package( $this->package_id, true ) ) {
						$this->ws->add_info( 'package_deleted', array() );
					} else {
						$this->ws->add_error( 'unknown_error', array() );
						$this->is_ok = false;
					}
					break;
			}
			
		}
	}
	
	/*
	 *	returns HTML code for the main area
	 */
	public function get_content() {
		$o = '';
		
		if ( !$this->is_ok ) {
			if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
				return '<p><a href="' . $_SERVER['HTTP_REFERER'] . '">zur&uuml;ck</a></p>';
			} else {
				return '<p><a href="' . get_home_url() . '">Startseite</a></p>';
			}
		}
		
		if ( $this->package_id > 0 ) {
			switch ( $this->action ) {
				case 'unlock':
					// edit link
					$o .= '<p><a href="' . esc_url( get_workshop_url( 'view_package', 'mtpakid=' . $this->package_id ) ) . '">Werk bearbeiten</a></p>';
					break;
				case 'lock':
				case 'delete':
				case 'unpublishrev':
				case 'unpublishws':
					// back
					if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
						return '<p><a href="' . $_SERVER['HTTP_REFERER'] . '">zur&uuml;ck</a></p>';
					} else {
						return '<p><a href="' . get_home_url() . '">Startseite</a></p>';
					}
					break;
			}
		}
		
		return $o;
	}
	
}
?>