<?php

/*	pvmkit_package_manager can...
 *	- a lot
 */

class pvmkit_package_manager {
	
	private $db = null;
	
	private $is_ok = false;
	private $user_id = 0;
	private $author_id = 0;
	
	/*
	 *	sets up the object
	 */
	public function __construct() {
		global $wpdb;
		$this->db = $wpdb;
		
		// check if user is logged in
		if ( is_user_logged_in() ) {
			$this->user_id = get_current_user_id();
			
			$author_id = $this->db->get_var( 'SELECT author_id FROM ' . $this->db->prefix . 'pvmkit_authors WHERE user_id = ' . $this->user_id . ' ORDER BY author_id ASC LIMIT 1' );
			if ( null !== $author_id ) {
				$this->author_id = $author_id;
			}
			
			$this->is_ok = true;
		}
	}
	
	private function delete_properties( $package_id ) {
		$package_id = (int) $package_id;
		if ( $package_id > 0 ) {
			$res = $this->db->delete( $this->db->prefix . 'pvmkit_property_index', array( 'package_id' => $package_id ), array( '%d' ) );
			return !( $res === false );
		}
	}
	
	private function delete_ratings( $package_id ) {
		$package_id = (int) $package_id;
		if ( $package_id > 0 ) {
			$res = $this->db->delete( $this->db->prefix . 'pvmkit_ratings', array( 'package_id' => $package_id ), array( '%d' ) );
			return !( $res === false );
		}
	}
	
	private function delete_package_db( $package_id, $delete_objects = true ) {
		$package_id = (int) $package_id;
		if ( $package_id > 0 ) {
			if ( $delete_objects ) {
				$this->db->query( $this->db->prepare( 'DELETE FROM ' . $this->db->prefix . 'pvmkit_objects WHERE id IN (SELECT object_id FROM ' . $this->db->prefix . 'pvmkit_package_components WHERE package_id = %d)', $package_id ) ); 
				$this->db->query( $this->db->prepare( 'DELETE FROM ' . $this->db->prefix . 'pvmkit_package_components WHERE package_id = %d', $package_id ) ); 
			}
			return $this->db->delete( $this->db->prefix . 'pvmkit_packages', array( 'id' => $package_id ), array( '%d' ) );
		}
	}
	
	private function delete_zip_public( $package_id ) {
		$package_id = (int) $package_id;
		$zipfile = PVMKIT_UPLOAD_PATH . 'published/package_' . $package_id . '.zip';
		if ( file_exists( $zipfile ) ) {
			if ( unlink( $zipfile ) ) {
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}
	
	private function delete_zip_archive( $package_id ) {
		$package_id = (int) $package_id;
		$zipfile = PVMKIT_UPLOAD_PATH . 'archive/package_' . $package_id . '.zip';
		if ( file_exists( $zipfile ) ) {
			if ( unlink( $zipfile ) ) {
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}
	
	/*
	 *	get all unpublished packages
	 */
	public function get_packages_to_publish() {
		
		if ( !$this->is_ok ) { return false; }
		
		/* 	status indicators
		 *	0 - deleted
		 *	1 - just created
		 *	2 - 
		 *	3 - 
		 *	4 - 
		 *	5 - published by user - waiting
		 *	6 - 
		 *	7 - 
		 *	8 - 
		 *	9 - 
		 */
		
		// load packages
		return $this->db->get_results( 'SELECT `id`, `title`, DATE_FORMAT(`publish_date`, ' . "'%d.%m.%Y, %H:%i'" . ') AS publish_date_f FROM ' . $this->db->prefix . 'pvmkit_packages WHERE status < 5 AND status != 0 AND author = ' . $this->author_id . ' ORDER BY publish_date' );
	}
	
	/*
	 *	removes the package from the database and the imported data from the image folder
	 *	set $delete_zip to true to also delete the ZIP backup file
	 */
	public function delete_package( $package_id, $delete_zip = false ) {
		
		$msg = array();
		$package_id = (int) $package_id;
		$package = new pvmkit_package_editable( $package_id );
		
		if ( $package->exists() ) {
			// delete published ZIP file
			if ( $this->delete_zip_public( $package_id ) ) {
				$msg[] = 'Das Paket wurde aus dem Bereitstellungsraum entfernt.';
			} else {
				$msg[] = 'Fehler: Das Paket konnte nicht aus dem Bereitstellungsraum entfernt werden.';
			}
			
			// delete archive ZIP file
			if ( $delete_zip ) {
				if ( $this->delete_zip_archive( $package_id ) ) {
					$msg[] = 'Das Paket wurde aus dem Archiv entfernt.';
				} else {
					$msg[] = 'Fehler: Das Paket konnte nicht aus dem Archiv entfernt werden.';
				}
			}	
			
			// ratings
			$this->delete_ratings( $package_id );
			
			// properties
			$this->delete_properties( $package_id );
			
			// delete associated database entries
			$this->delete_package_db( $package_id, true );
			
			return true;
		}
		
		return false;
	}
	
	/*
	 *	removes a package from the public frontend, but keeps the data in the database
	 * 	$status can be set to 1 or 5
	 * 	1 - package will reappear in the users unpublished collection
	 * 	5 - package will reappear in the mod/admin review section
	 * 	default is 1; if $status != 5 then $status = 1
	 *	returns an array with messages
	 */
	public function unpublish_package( $package_id, $status = 1 ) {
		
		$msg = array();
		$package_id = (int) $package_id;
		$status = (int) $status;
		
		// default $status to 1
		if ( $status != 5 ) {
			$status = 1;
			$msg[] = 'Aktion: Werk zur &Uuml;berarbeitung durch Nutzer freigeben';
		} else {
			$msg[] = 'Aktion: Werk f&uuml;r erneuten Review zur&uuml;ckstellen';
		}
		
		// delete published ZIP file
		$zipfile = PVMKIT_UPLOAD_PATH . 'published/package_' . $package_id . '.zip';
		if ( file_exists( $zipfile ) ) {
			if ( unlink( $zipfile ) ) {
				$msg[] = 'Das Paket wurde aus dem Bereitstellungsraum entfernt.';
			} else {
				$msg[] = 'Fehler: Das Paket konnte nicht aus dem Bereitstellungsraum entfernt werden.';
			}
		} else {
			$msg[] = 'Zum Werk wurde kein Paket im Bereitstellungsraum gefunden.';
		}
		
		// unpublish in database
		$res = $this->db->query( $this->db->prepare( 'UPDATE ' . $this->db->prefix . "pvmkit_packages SET status = %d WHERE id = %d AND status > 5", $status, $package_id ) );
		if ( $res !== false ) {
			$msg[] = $res . ' Werk(e) wurde(n) aktualisiert.';
		}
		
		return $msg;
		
	}
	
	/*
	 *	
	 */
	public function publish_package() {
		
	}
	
	/*
	 *	updates status from 1 to 5 (edit mode to published by user)
	 */
	public function lock_package( $package_id ) {
		$package_id = (int) $package_id;
		
		$package = new pvmkit_package_editable( $package_id );
		if ( !$package->can_manage() ) {
			return false;
		}
		
		// does it have all components?
		if ( !$package->has_text() || !$package->has_titleimage() || !$package->has_image() ) {
			return false;
		}
		
		// does it have properties?
		if ( $this->db->get_var( 'SELECT COUNT(*) FROM ' . $this->db->prefix . 'pvmkit_property_index WHERE package_id = ' . $package_id ) == 0 ) {
			return false;
		}
		
		$ret = $this->db->update( 
			$this->db->prefix . 'pvmkit_packages', 
			array( 'status' => 5 ), 
			array( 'id' => $package_id, 'status' => 1 ), 
			array( '%d' ), 
			array( '%d', '%d' ) 
		);
		
		return ( $ret == 1 );
	}
	
	/*
	 *	updates status from 5 to 1 (published by user to edit mode)
	 */
	public function unlock_package( $package_id ) {
		$package_id = (int) $package_id;
		
		$package = new pvmkit_package_editable( $package_id );
		if ( !$package->can_manage() ) {
			return false;
		}
		
		$ret = $this->db->update( 
			$this->db->prefix . 'pvmkit_packages', 
			array( 'status' => 1 ), 
			array( 'id' => $package_id, 'status' => 5 ), 
			array( '%d' ), 
			array( '%d', '%d' ) 
		);
		
		return ( $ret == 1 );
	}
	
	/*
	 *	LEGACY
	 */
	public function publish_by_user( $package_id ) {
		
		return $this->lock_package( $package_id );
		
	}

	/*
	 *	short debug info
	 */
	public function debug_status() {
		return 'User-ID: ' . $this->user_id . ' - Author-ID: ' . $this->author_id;
	}

	/*
	 *	plain text output
	 */
	public function __toString() {
		return '';
	}
	
	/*
	 *	getters
	 */
	function get_user_id() { return $this->user_id; }
	function get_author_id() { return $this->author_id; }

}

?>