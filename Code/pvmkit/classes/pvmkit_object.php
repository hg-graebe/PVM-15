<?php

abstract class pvmkit_object {
	
	protected $db = null;
	
	protected $object_id = -1;
	protected $author = null;
	protected $content = '';
	protected $type = '';
	
	/*
	 *	sets up the object
	 */
	public function __construct( $object_id = -1 ) {
		
		global $wpdb;
		$this->db = $wpdb;
		
		// load if ID is given
		$object_id = (int) $object_id;
		if ( $object_id > 0 ) {
			$this->load_from_db( $object_id );
		} else {
			$this->reset();
		}
	}
	
	/*
	 *	sets data of objects
	 *	parameters: author (integer author_id or pvmkit_author object), content (usually a string)
	 *	set values to NULL to keep the old value
	 */
	public function set( $author = null, $content = null ) {
		
		// check type of author argument
		if ( gettype( $author ) == 'integer' ) {
			$this->author = new pvmkit_author( $author );
		} else if ( gettype( $author ) == 'NULL' ) {
			// nothing to change
		} else if ( ( gettype( $author ) == 'object' ) && ( get_class( $author ) == 'pvmkit_author' ) ) {
			$this->author = new pvmkit_author( $author->get_author_id() );
		} else {
			return false;
		} // TODO: check if its a string which represents a valid number
		
		// check if content should be updated
		if ( $content !== null ) {
			$this->content = $content;
		}
		
		// update the database
		if ( $this->object_id > 0 ) {
			// update
			$this->update();
		} else {
			// insert
			$this->insert();
		}
	}
	
	/*
	 *	loads an existing object from the database
	 */
	public function load_from_db( $object_id ) {
		
		$object_id = (int) $object_id;
		if ( $object_id < 1 ) {
			return false;
		}
		
		$result = $this->db->get_row( 'SELECT author, content FROM ' . $this->db->prefix . 'pvmkit_objects WHERE id = ' . $object_id . " AND type = '" . $this->type . "' LIMIT 1" );
		
		if ( null !== $result ) {
			$this->object_id = $object_id;
			$this->author = new pvmkit_author( $result->author );
			$this->content = $result->content;
			
			return true;
		} else {
			$this->reset();
			return false;
		}
	}
	
	protected function reset() {
		$this->object_id = -1;
		$this->author = new pvmkit_author();
		$this->content = '';
	}
	
	/*
	 *	returns if the object has been filled with user content
	 */
	public function exists() {
		return ( $this->object_id > 0 );
	}
	
	/*
	 *	updates an existing object (not the attached properties!)
	 */
	public function update() {
		// do not update if the object does not exist yet
		if ( $this->object_id > 0 ) {
			// update objects table
			return $this->db->update( 
				$this->db->prefix . 'pvmkit_objects',
				array(
					'type' => $this->type,
					'author' => $this->author->get_author_id(),
					'content' => $this->content
				), 
				array( 'id' => $this->object_id ), 
				array( '%s', '%d', '%s' ), 
				array( '%d' ) 
			);
		} else {
			return false;
		}
	}
	
	/*
	 *	creates a new object
	 */
	public function insert() {
		$res = $this->db->insert(
			$this->db->prefix . 'pvmkit_objects', 
			array( 
				'type' => $this->type,
				'author' => $this->author->get_author_id(),
				'content' => $this->content
			), 
			array( '%s', '%d', '%s' ) 
		);
		
		$this->object_id = $this->db->insert_id;
		
		return $res;
	}
	
	/*
	 *
	 */
	public function assign_to( $package_id ) {
		$package_id = (int) $package_id;
		if ( $package_id > 0 ) {
			// insert 
			return $this->db->replace( 
				$this->db->prefix . 'pvmkit_package_components', 
				array( 'package_id' => $package_id, 'object_id' => $this->object_id, 'type' => $this->type ), 
				array( '%d', '%d', '%s' ) 
			);
		}
	}
	
	/*
	 *	sets the author
	 */
	public function set_author_by_name( $author_name ) {
		$this->author = new pvmkit_author();
		$this->author->load_by_full_name( $author_name );
	}
	
	public function set_author_by_author_id( $author_id ) {
		$this->author = new pvmkit_author( $author_id );
	}
	
	/*
	 *	get and set
	 */
	public function get_object_id() { return $this->object_id; }
	public function get_type() { return $this->type; }
	public function get_author() { return $this->author; }
	public function get_content() { return $this->content; }
}

?>