<?php

/*	pvmkit_author_profile can...
 *	- 
 */

class pvmkit_author_profile {
	
	private $db = null;
	
	private $user_id = 0;
	private $author_id = 0;
	private $full_name = '';
	private $location = '';
	
	private $favorites = array();
	
	/*
	 *	sets up the object
	 */
	public function __construct( $author_id ) {
		global $wpdb;
		$this->db = $wpdb;
		
		$this->author_id = (int) $author_id;
		
		// check if the author exists
		$author_data = $this->db->get_row( 'SELECT user_id, full_name, location FROM ' . $this->db->prefix . 'pvmkit_authors WHERE author_id = ' . $this->author_id . ' LIMIT 1' );
		
		if ( is_null( $author_data ) ) {
			// author does not exist
			$this->author_id = 0;
			return false;
		} else {
			// check if author is user
			if ( ( (int) $author_data->user_id ) == 0 ) {
				$this->user_id = 0;
			} else {
				$this->user_id = $author_data->user_id;
				$this->full_name = $author_data->full_name;
				$this->location = $author_data->location;
			}
		}
	}
	
	/*
	 *	load all package_ids that have been rated "ME THING"
	 */
	function get_favorites( $out_objects = false ) {
		
		if ( $this->user_id != 0 ) {
			
			$favorites = array();
			$i = 0;
			
			// load package_id
			$favorites_data = $this->db->get_results( 'SELECT package_id FROM ' . $this->db->prefix . 'pvmkit_ratings WHERE type = ' . "'mething'" . ' AND user_id = ' . $this->user_id );
			foreach ( $favorites_data as $favorit_data ) {
				
				if ( $out_objects ) {
					
					$favorites[ $i ] = new pvmkit_package();
					$favorites[ $i ]->load_by_id( $favorit_data->package_id );
					$this->favorites[] = $favorit_data->package_id;
					$i++;
					
				} else {
					$this->favorites[] = $favorit_data->package_id;
				}
				
			} 
			
			if ( $out_objects ) {
				return $favorites;
			} else {
				return $this->favorites;
			}
			
		}
		
		return false; 
	}

	/*
	 *	plain text output
	 */
	public function __toString() {
		return 'Author: ' . $this->author_id . ' - User: ' . $this->user_id . ' - Name: ' . $this->full_name . ' - Location: ' . $this->location;
	}

	/*
	 *	true if the author profile has an active WP user
	 */
	function is_user() { return ( $this->user_id != 0 ); }
	
	/*
	 *	getters
	 */
	function get_user_id() { return $this->user_id; }
	function get_author_id() { return $this->author_id; }
	
}

?>