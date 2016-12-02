<?php

/*	pvmkit_package can...
 *	- load a package from database
 *	- output its components
 *	- update stats (viewcount
 */

class pvmkit_package {
	
	protected $db = null;
	
	protected $id = -1;
	protected $status;
	protected $title;
	protected $author = null;
	protected $user_id = -1;
	protected $publish_date;
	protected $viewcount;
	
	protected $text = null;
	protected $image = null;
	protected $titleimage = null;
	
	/*
	 *	sets up the object
	 */	
	public function __construct( $package_id = -1 ) {
		global $wpdb;
		$this->db = $wpdb;
		
		$package_id = (int) $package_id;
		if ( $package_id > 0 ) {
			$this->load_by_id( $package_id );
		}
		
	}
	
	/*
	 *	Loads the package with the id from the database
	 *	returns false if ID does not exist
	 */
	public function load_by_id( $package_id ) {
		
		// sanitize values
		$package_id = (int) $package_id;
		
		// check if package exists
		$package_data = $this->db->get_row( 'SELECT * FROM ' . $this->db->prefix . 'pvmkit_packages WHERE id = ' . $package_id . ' LIMIT 1' );
		
		if ( is_null( $package_data ) ) {
			// package does not exist
			return false;
		} else {
			
			// assign package data
			$this->id 			= $package_data->id;
			$this->title		= $package_data->title;
			$this->status		= $package_data->status;
			$this->publish_date	= $package_data->publish_date;
			$this->viewcount 	= $package_data->viewcount;
			$this->user_id 		= $package_data->user_id;
			
			$this->author = new pvmkit_author( $package_data->author );
			
			// load assigned objects
			$objects_data = $this->db->get_results( 'SELECT object_id, type FROM ' . $this->db->prefix . 'pvmkit_package_components WHERE package_id = ' . $this->id );
			
			// instantiate objects (even if not in the database!)
			$this->text = new pvmkit_object_text();
			$this->titleimage = new pvmkit_object_titleimage();
			$this->image = new pvmkit_object_image();
			
			// if object has registered role in package use its data
			foreach ( $objects_data as $object_data ) {
				switch ( $object_data->type ) {
					case 'text':
						$this->text->load_from_db( $object_data->object_id );
						break;
					case 'titleimage':
						$this->titleimage->load_from_db( $object_data->object_id );
						break;
					case 'image':
						$this->image->load_from_db( $object_data->object_id );
						break;
				}
			}
		}
		
		return true;
	}

	/*
	 *	returns if the current user is allowed to see the object
	 */
	public function can_view() {
		if ( $this->status > 7 ) {
			return true;
		} else if ( current_user_can( 'pvm_manage_other_packages' ) ) {
			return true;
		} else if ( $this->get_user_id() == get_current_user_id() ) {
			return true;
		} else {
			return false;
		}
	}

	/*
	 *	updates viewcount of this package
	 */
	public function update_stats() {
		
		// increment viewcount field
		$this->db->query( 'UPDATE ' . $this->db->prefix . 'pvmkit_packages SET viewcount = (viewcount + 1) WHERE package_id = ' . $this->id );
		
		// if user is logged in, add it to his recent viewed list
		// TODO
		
	}
	
	/*
	 *	returns an array with options related to the package
	 */
	public function get_management_options() {
		$o = array();
		
		if ( $this->id < 1 ) {
			return false;
		}
		
		if ( current_user_can( 'pvm_manage_other_packages' ) ) {
			$o[] = array( 'label' => 'Werk l&ouml;schen', 'icon' => 'icon_delete', 'url' => get_workshop_url( 'package_actions', 'mtpakid=' . $this->id . '&mtaction=delete' ), 'add' => ' onclick="return window.confirm(' . "'Das Werk wird unwiederruflich gel&ouml;scht.'" . ');"' );
			$o[] = array( 'label' => 'Werk in Redaktion zur&uuml;ckstellen', 'icon' => 'icon_unpublish', 'url' => get_workshop_url( 'package_actions', 'mtpakid=' . $this->id . '&mtaction=unpublishrev' ) );
			$o[] = array( 'label' => 'Werk zur Bearbeitung zur&uuml;ckstellen', 'icon' => 'icon_unpublish', 'url' => get_workshop_url( 'package_actions', 'mtpakid=' . $this->id . '&mtaction=unpublishws' ) );
		}
		if ( current_user_can( 'pvm_edit_other_packages' ) ) {
			
		}
		if ( is_user_logged_in() ) {
			$o[] = array( 'label' => 'Melden', 'icon' => 'icon_delete', 'url' => get_workshop_url( 'report', 'mtpakid=' . $this->id ) );
		}
		
		return $o;
	}

	/*
	 *	create new package in the database with values from this package
	 */
	public function __toString() {
		
		
		return $this->id;
	}

	/*
	 *	returns true if package exists
	 *	(false if package does not exists or hasn't been loaded)
	 */
	public function exists() {
		return ( $this->id > 0 );
	}

	/*
	 *	returns URL to single package site
	 */
	public function get_url() {
		if ( $this->exists() ) {
			return get_home_url() . '/index.php/single_package/?pkg_id=' . $this->id;
		} else {
			return false;
		}
	}
	
	/*
	 *	getters
	 */
	function get_id() { return $this->id; }
	function get_status() { return $this->status; }
	function get_title() { return $this->title; }
	function get_author() { return $this->author; }
	function get_user_id() { return $this->user_id; }
	function get_publish_date() { return $this->publish_date; }
	function get_viewcount() { return $this->viewcount; }
	
	function get_text() { return $this->text; }
	function get_titleimage() { return $this->titleimage; }
	function get_image() { return $this->image; }
	
	function has_text() { return $this->text->exists(); }
	function has_titleimage() { return $this->titleimage->exists(); }
	function has_image() { return $this->image->exists(); }
	function has_properties() { return $this->db->get_var( 'SELECT COUNT(*) FROM ' . $this->db->prefix . 'pvmkit_property_index WHERE package_id = ' . $this->id ) > 0; }
}

?>