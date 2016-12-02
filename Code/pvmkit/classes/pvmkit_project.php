<?php
class pvmkit_project {
	
	private $db = null;
	
	private $project_id = -1;
	private $properties = array();
	private $managers = array();
	private $images = array();
	
	/*
	 *	sets up the object
	 */
	public function __construct( $project_id = -1 ) {
		// copy reference to database connection
		global $wpdb;
		$this->db = $wpdb;
		
		// load if ID is supplied
		if ( $project_id > 0 ) {
			$this->load_by_id( $project_id );
		}
	}
	
	public function exists() {
		return ( $this->project_id > 0 );
	}
	
	private function reset() {
		$this->project_id = -1;
		$this->properties = array(
			'state' => -1,		// edit, open, done
			'project_set' => '',
			'project_title' => '',
			'institute' => '',
			'institute_url' => '',
			'institute_group' => '',
			'group' => '',
			'date_start' => '',
			'date_end' => '',
			'text_describtion' => '',
			'text_documentation' => '',
			'text_result' => ''
		);
		$this->managers = array();
		$this->images = array();
		
	}
	
	public function load_by_id( $project_id ) {
		$this->reset();
		
		$project_id = (int) $project_id;
		if ( $project_id > 0 ) {
			$result = $this->db->get_row( 'SELECT * FROM ' . $this->db->prefix . 'pvmkit_projects WHERE project_id = ' . $project_id . ' LIMIT 1' );
		
			if ( null !== $result ) {
				$this->project_id = $project_id;
				$this->properties['state'] = $result->state;
				$this->properties['project_set'] = $result->project_set;
				$this->properties['project_title'] = $result->project_title;
				$this->properties['institute'] = $result->institute;
				$this->properties['institute_url'] = $result->institute_url;
				$this->properties['institute_group'] = $result->institute_group;
				$this->properties['date_start'] = $result->date_start;
				$this->properties['date_end'] = $result->date_end;
				$this->properties['text_describtion'] = $result->text_describtion;
				$this->properties['text_documentation'] = $result->text_documentation;
				$this->properties['text_result'] = $result->text_result;
				
				$m_list = $this->db->get_results( 'SELECT user_id FROM ' . $this->db->prefix . 'pvmkit_project_participants WHERE project_id = ' . $this->project_id );
				foreach ( $m_list as $m ) {
					$this->managers[] = $m->user_id;
				}
				
				$i_list = $this->db->get_results( 'SELECT project_image_id FROM ' . $this->db->prefix . 'pvmkit_project_images WHERE project_id = ' . $this->project_id );
				foreach ( $i_list as $i ) {
					$this->images[] = $i->project_image_id;
				}
				
				return true;
			} else {
				$this->reset();
				return false;
			}
		}
	}
	
	public function set( $data ) {
		$upd_data = array();
		$upd_format = array();
		$formats =  array(
			'state' => '%s',
			'project_set' => '%s',
			'project_title' => '%s',
			'institute' => '%s',
			'institute_url' => '%s',
			'institute_group' => '%s',
			'group' => '%s',
			'date_start' => '%s',
			'date_end' => '%s',
			'text_describtion' => '%s',
			'text_documentation' => '%s',
			'text_result' => '%s'
		);
		
		foreach ( $data as $dkey => $dval ) {
			if ( array_key_exists( $dkey, $this->properties ) ) {
				$this->properties[ $dkey ] = $dval;
				$upd_data[ $dkey ] = $dval;
				$upd_format[] = $formats[ $dkey ];
			}
		}
		
		$this->db->update(
			$this->db->prefix . 'pvmkit_projects', 
			$upd_data,
			array( 
				'project_id' => $this->project_id
			),
			$upd_format,
			array( '%d' )
		);
	}
	
	/*
	 *	returns the requested property
	 */
	public function get( $key ) {
		if ( !$this->exists() ) {
			return false;
		}
		
		switch ( $key ) {
			case 'id':
				return $this->project_id;
				break;
			default:
				if ( array_key_exists( $key, $this->properties ) ) {
					return $this->properties[ $key ];
				} else {
					return false;
				}
		}
		
	}
	
	public function add_package( $package_id ) {
		if ( !$this->exists() ) {
			return false;
		}
		
		$this->db->update(
			$this->db->prefix . 'pvmkit_projects_packages', 
			array( 
				'status' => 'approved'
			),
			array( 
				'project_id' => $this->project_id, 
				'package_id' => $package_id
			),
			array( '%s' ),
			array( '%d', '%d' )
		);
	}
	
	public function propose_package( $package_id ) {
		if ( !$this->exists() ) {
			return false;
		}
		
		$this->db->insert( 
			$this->db->prefix . 'pvmkit_projects_packages', 
			array( 'project_id' => $this->project_id, 'package_id' => $package_id, 'status' => 'proposed' ), 
			array( '%d', '%d', '%s' ) 
		); // double proposing = error
	}
	
	public function remove_package( $package_id ) {
		if ( !$this->exists() ) {
			return false;
		}
		
		$this->db->delete( 
			$this->db->prefix . 'pvmkit_projects_packages', 
			array( 'project_id' => $this->project_id, 'package_id' => $package_id ), 
			array( '%d', '%d' ) 
		);
	}
	
	/*
	 *	creates a new project and sets ID
	 */
	public function create() {
		$this->reset();
		
		$db_res = $this->db->insert(
			$this->db->prefix . 'pvmkit_projects', 
			array( 
				'state' => 'edit',
				'project_title' => 'Neuer Titel'
			), 
			array( '%s' ) 
		);
		$this->project_id = $this->db->insert_id;
		
		$this->db->insert(
			$this->db->prefix . 'pvmkit_project_participants', 
			array( 
				'project_id' => $this->project_id,
				'user_id' => get_current_user_id(),
				'role' => 'owner'
			), 
			array( '%d', '%d', '%s' ) 
		);
		
		return $this->project_id;
	}
	
	/*
	 *	
	 */
	public function get_image() {
		
	}
	
	public function get_url() {
		return home_url( '/project/?project_id=' . $this->project_id );
	}
	
	public function can_view() {
		if ( ( $this->properties['state'] == 'done' ) || ( $this->properties['state'] == 'open' ) ) {
			return true;
		} else if ( $this->properties['state'] == 'edit' ) {
			if ( in_array( get_current_user_id(), $this->managers ) ) {
				return true;
			}
		}
		return false;
	}
	
	public function can_edit() {
		if ( ( $this->properties['state'] == 'edit' ) || ( $this->properties['state'] == 'open' ) ) {
			$r = $this->db->get_row( 'SELECT role FROM ' . $this->db->prefix . 'pvmkit_project_participants WHERE project_id = ' . $this->project_id . ' AND user_id = ' . get_current_user_id() . ' LIMIT 1' );
			if ( null !== $r ) {
				if ( ( $r->role == 'owner' ) || ( $r->role == 'manager' ) ) {
					return true;
				}
			}
		}
		return false;
	}
	
	public function get_contributers( $filter = 'all' ) {
		switch ( $filter ) {
			case 'owner':
				
				break;
			case 'manager':
				
				break;
			case 'all':
				
				break;
			case 'artists':
			default:
				
		}
	}
	
	public function get_packages( $filter = 'approved' ) {
		if ( !$this->exists() ) {
			return false;
		}
		
		switch ( $filter ) {
			case 'all':
				
				break;
			case 'proposed':
				
				break;
			case 'approved':
			default:
				$p = array();
				$p_list = $this->db->get_results( 'SELECT package_id FROM ' . $this->db->prefix . 'pvmkit_projects_packages WHERE project_id = ' . $this->project_id . ' AND status = ' . "'approved'" );
				foreach ( $p_list as $p_data ) {
					$p[] = $p_data->package_id;
				}
				return $p;
		}
	}
	
	public function __toString() {
		return $this->get( 'project_title' );
	}
}

?>