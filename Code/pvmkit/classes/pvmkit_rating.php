<?php

/*	pvmkit_rating can...
 *	- load the ratings of a user for one package
 *	- update the users rating
 */

class pvmkit_rating {
	
	private $db = null;
	
	private $package_id = 0;
	private $user_id = 0;
	private $ratings = array( 'coolfoto' => false, 'goodwork' => false, 'mething' => false, 'nicetext' => false );
	private $rating_counts = array( 'coolfoto' => 0, 'goodwork' => 0, 'mething' => 0, 'nicetext' => 0 );
	
	/*
	 *	sets up the object
	 */
	public function __construct( $package_id ) {
		global $wpdb;
		$this->db = $wpdb;
		
		$this->package_id = (int) $package_id;
		
		// check if package exists
		$package_data = $this->db->get_row( 'SELECT * FROM ' . $this->db->prefix . 'pvmkit_packages WHERE id = ' . $this->package_id . ' LIMIT 1' );
		
		if ( is_null( $package_data ) ) {
			// package does not exist
			$this->package_id = 0;
			return false;
		} else {
			// check if user is logged in
			if ( is_user_logged_in() ) {
				$this->user_id = get_current_user_id();
			}
		}
	}
	
	/*
	 *	processes rating actions by GET
	 */
	public function update() {
		
		if ( ($this->user_id == 0) || ($this->package_id == 0) ) { return  false; }
		
		$rate_action = get_query_var( 'rate', '');
		
		switch ( $rate_action ) {
			case 'coolfoto':
				$this->add_rating('coolfoto');
				break;
			case 'no_coolfoto':
				$this->remove_rating('coolfoto');
				break;
			case 'goodwork':
				$this->add_rating('goodwork');
				break;
			case 'no_goodwork':
				$this->remove_rating('goodwork');
				break;
			case 'nicetext':
				$this->add_rating('nicetext');
				break;
			case 'no_nicetext':
				$this->remove_rating('nicetext');
				break;
			case 'mething':
				$this->add_rating('mething');
				break;
			case 'no_mething':
				$this->remove_rating('mething');
				break;
		}
	}
	
	private function add_rating( $type ) {
		return $this->db->replace( 
			$this->db->prefix . 'pvmkit_ratings', 
			array( 'package_id' => $this->package_id, 'user_id' => $this->user_id, 'type' => $type ), 
			array( '%d', '%d', '%s' ) 
		);
	}
	
	private function remove_rating( $type ) {
		return $this->db->delete( $this->db->prefix . 'pvmkit_ratings', array( 'package_id' => $this->package_id, 'user_id' => $this->user_id, 'type' => $type ) );
	}
	
	/*
	 *	loads ratings for user + package from database
	 */
	function get_ratings() {
		
		if ( ($this->package_id != 0) && ($this->user_id != 0) ) {
			
			// load ratings
			$ratings_data = $this->db->get_results( 'SELECT type FROM ' . $this->db->prefix . 'pvmkit_ratings WHERE package_id = ' . $this->package_id . ' AND user_id = ' . $this->user_id );
			foreach ( $ratings_data as $rating_data ) {
				$this->ratings[ $rating_data->type ] = true;
			}
			return $this->ratings; 
			
		}
		
		return false; 
	}
	
	/*
	 *	loads ratings counts for package from database
	 */
	function get_rating_counts() {
		
		if ( $this->package_id != 0 ) {
			
			// load rating counts
			$rating_count_data = $this->db->get_results( 'SELECT type, COUNT(*) as ratings FROM ' . $this->db->prefix . 'pvmkit_ratings WHERE package_id = ' . $this->package_id . ' GROUP BY type' );
			foreach ( $rating_count_data as $rating_count ) {
				$this->rating_counts[ $rating_count->type ] = $rating_count->ratings;
			}
			return $this->rating_counts; 
			
		}
		
		return false; 
		
	}

	/*
	 *	plain text output
	 */
	public function __toString() {
		return 
			'[' . ( $this->ratings[ 'coolfoto' ] ? 'X' : '_' ) . '] COOL FOTO! | ' . 
			'[' . ( $this->ratings[ 'goodwork' ] ? 'X' : '_' ) . '] GOOD WORK! | ' . 
			'[' . ( $this->ratings[ 'nicetext' ] ? 'X' : '_' ) . '] NICE TEXT! | ' . 
			'[' . ( $this->ratings[ 'mething' ] ? 'X' : '_' ) . '] ME THING!';
	}
	
	/*
	 *	getters
	 */
	function get_package_id() { return $this->package_id; }
	function get_user_id() { return $this->user_id; }
	
}

?>