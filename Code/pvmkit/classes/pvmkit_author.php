<?php
class pvmkit_author {
	
	private $db = null;
	
	private $user_id = -1;
	private $author_id = -1;
	private $wp_user = null;
	private $full_name = '';
	private $location = '';
	
	/*
	 *	sets up the object
	 */
	public function __construct( $id = -1, $id_is_author_id = true ) {
		// copy reference to database connection
		global $wpdb;
		$this->db = $wpdb;
		
		// load if ID is supplied
		if ( $id > 0 ) {
			if ( $id_is_author_id ) {
				$this->load_by_author_id( $id );
			} else {
				$this->load_by_user_id( $id );
			}
		}
	}
	
	/*
	 *	loads the wordpress profile
	 */
	private function load_wp_user() {
		if ( $this->user_id > 0 ) {
			$this->wp_user = new WP_User( $this->user_id );
			
			if ( !$this->wp_user->exists() ) {
				$this->wp_user = null;
				$this->user_id = -1;
			}
		}
	}
	
	/*
	 *	loads the author data from database if available, otherwise creates new entry
	 */
	public function load_by_full_name( $fname ) {
		// check if author with this name exist
		$author_data = $this->db->get_row( $this->db->prepare( 
				'SELECT author_id, user_id, full_name FROM ' . $this->db->prefix . "pvmkit_authors WHERE full_name = %s LIMIT 1", 
				$fname
		) );
		
		if ( null !== $author_data ) {
			// load all data
			$this->author_id = $author_data->author_id;
			if ( null !== $author_data->user_id ) {
				$this->user_id = $author_data->user_id;
			}
			$this->full_name = $fname;
		} else {
			// create new author
			$this->db->insert( 
				$this->db->prefix . 'pvmkit_authors', 
				array( 'full_name' => $fname ), 
				array( '%s' ) 
			);
			
			$this->author_id = $this->db->insert_id;
			$this->full_name = $fname;
		}
		
		$this->load_wp_user();
	}
	
	/*
	 *	loads the author data from database
	 */
	public function load_by_user_id( $user_id, $autocreate = true ) {
		
		// check if author with this id exists
		$author_data = $this->db->get_row( $this->db->prepare( 'SELECT author_id, full_name, location FROM ' . $this->db->prefix . "pvmkit_authors WHERE user_id = %d ORDER BY author_id ASC LIMIT 1", $user_id ) );
		
		if ( null !== $author_data ) {
			
			// load all data
			$this->user_id = $user_id;
			$this->author_id = $author_data->author_id;
			$this->full_name = $author_data->full_name;
			$this->location = $author_data->location;
			$this->load_wp_user();
			return true;
			
		} else if ( $autocreate ) {
			
			// create profile
			$res = $this->db->insert( 
				$this->db->prefix . 'pvmkit_authors', 
				array( 'user_id' => $user_id, 'full_name' =>  wp_get_current_user()->display_name, 'location' => 'location' ), 	// todo: get user name
				array( '%d', '%s', '%s' ) 
			);
			
			// and load it
			if ( $res ) {
				
				$this->author_id = $this->db->insert_id;
				$this->user_id = $user_id;
				$this->full_name = '';
				$this->location = '';
				$this->load_wp_user();
				return true;
				
			} else {
				return false;
			}
			
		} else {
			return false;
		}
		
	}
	
	/*
	 *	loads the author data from database
	 */
	public function load_by_author_id( $author_id ) {
		// check if author with this author_id exists
		$author_data = $this->db->get_row( $this->db->prepare( 
				'SELECT user_id, full_name FROM ' . $this->db->prefix . "pvmkit_authors WHERE author_id = %d LIMIT 1", 
				$author_id
		) );
		
		if ( null !== $author_data ) {
			
			// load all data
			$this->author_id = $author_id;
			if ( null !== $author_data->user_id ) {
				$this->user_id = $author_data->user_id;
				$this->load_wp_user();
			}
			$this->full_name = $author_data->full_name;
			return true;
			
		} else {
			
			// create default profile
			
			return true;
			
		}
		
	}
	
	/*
	 *	LEGACY! loads the author data from database
	 */
	public function load_by_id( $author_id ) {
		return $this->load_by_author_id( $author_id );
	}
	
	/*
	 *	returns if author is linked to a user profile
	 */
	public function is_user() {
		return ( $this->user_id > 0 );
	}

	/*
	 *	returns url pointing to the author profile
	 */
	public function get_profile_url() {
		return home_url( '/single_user/?author_id=' . $this->author_id );
	}

	/*
	 *	returns a HTML link element pointing to the author profile
	 */
	public function get_profile_link( $class = '' ) {
		return '<a ' . ( $class == '' ? '' : ( 'class="' . $class . '"' ) ) . 'href="' . $this->get_profile_url() . '">' . ( ( $this->wp_user != null ) ? $this->wp_user->__get( 'user_login' ) : 'Unbekannter Benutzer' ) . '</a>';
	}

	/*
	 *	returns user profile image url
	 */
	public function get_profile_image_url( $version = '' ) {	// TODO: remove param
		if ( $this->wp_user != null ) {
			$img = get_user_meta( $this->user_id, 'mt_avatar', true );
			if ( $img == '' ) {
				$this->wp_user->get( 'profile_img' );
			} else {
				$img .= '_medium.jpg';
			}
			if ( $img == '' ) {
				return plugins_url() . '/pvmkit/images/default_profile_image' . $version . '.png'; // DEFAULT IMAGE
			} else {
				return $img;
			}
		} else {
			return plugins_url() . '/pvmkit/images/default_profile_image' . $version . '.jpg'; // DEFAULT IMAGE
		}
	}

	/*
	 *	returns user profile image
	 */
	public function get_profile_image() {
		return '<img src="' . $this->get_profile_image_url() . '" />';
	}
	
	public function __toString() {
		return $this->full_name;
	}
	
	/*
	 *	get and set
	 */
	function get_user_id() { return $this->user_id;	}
	function get_author_id() { return $this->author_id;	}
	function get_full_name() { return $this->full_name;	}
	function get_location() { return $this->location;	}
}

?>