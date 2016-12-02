<?php

class pvmkit_filtered_package_list {
	
	private $db = null;
	
	private $is_loaded = false;
	private $list = array();
	private $filters = array();
	private $authors = array();
	private $users = array();
	private $search_string = '';
	private $min = 0;
	private $max = 12;
	private $sorting_field = '';
	private $sorting_order = '';
	private $status = '> 7';
	
	private $where_clause = '';
	
	// static values
	private $filter_templates = array(
		
		);
	private $sorting_templates = array(
		'pa.publish_date'
		);
	
	/*
	 *	set up database connection
	 */
	public function __construct() {
		global $wpdb;
		$this->db = $wpdb;
	}
	
	/*
	 *	sets min and max index values (LIMIT in sql)
	 *	use NULL as parameter to keep the previous value
	 */
	public function set_min_max( $min, $max ) {
		if ( $min !== NULL ) {
			$this->min = $min;
			$this->is_loaded = false;
		}
		
		if ( $max !== NULL ) {
			$this->max = $max;
			$this->is_loaded = false;
		}
	}
	
	/*
	 *	returns an array with all packages matching the filters
	 */
	public function get_all() {
		if ( $this->is_loaded ) {
			return $this->list;
		} else {
			return false;
		}
	}
	
	/*
	 *	returns the package at the given index position
	 *	false if no package at that index exists or request has not been done yet
	 */
	public function get( $index ) {
		if ( $this->is_loaded ) {
			if ( ( $index < count( $this->list ) ) && ( $index >= 0 ) ) {
				return $this->list[ $index ];
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	/*
	 *	returns the number of results in 
	 */
	public function get_count() {
		if ( $this->is_loaded ) {
			return count( $this->list );
		} else {
			return false;
		}
	}
	
	/*
	 *	sets the search string filter
	 */
	public function set_search_string( $string ) {
		if ( $string !== NULL ) {
			$this->search_string = $string;
			$this->is_loaded = false;
		}
	}
	
	/*
	 *	adds a filter to the filter list
	 */
	public function add_filter( $filter_id ) {
		if ( $filter_id !== NULL ) {
			$filter_id = (int)$filter_id;
			
			// check for ID in database
			$res_prop_id = $this->db->get_row( 'SELECT type FROM ' . $this->db->prefix . 'pvmkit_properties WHERE property_id = ' . $filter_id . ' LIMIT 1' );
			if ( null !== $res_prop_id ) {
				// filter id exists
				$prop_type = $res_prop_id->type;
				
				// check if group already exists
				if ( !isset( $this->filters[ $prop_type ] ) ) {
					// if not create it
					$this->filters[ $prop_type ] = array();
				}
				
				// check if filter has already been added
				$check = array_search( $filter_id, $this->filters[ $prop_type ], true );
				if ( $check === false ) {
					// no? -> add it!
					$this->filters[ $prop_type ][] = $filter_id;
					$this->is_loaded = false;
				}
			} else {
				// filter id does not exist in database
				return false;
			}
		}
	}
	
	/*
	 *	returns an array of all applied filters
	 */
	public function get_filters() {
		return $this->filters;
	}
	
	/*
	 *	deletes all applied filters
	 */
	public function clear_filters() {
		$this->filters = array();
		$this->is_loaded = false;
	}
	
	/*
	 *	adds an author to the authors list
	 */
	public function add_author( $author_id ) {
		$author_id = (int) $author_id;
		if ( $author_id > 0 ) {
			$res = array_search( $author_id, $this->authors, true );
			if ( $res === false ) {
				// not added yet
				$this->authors[] = $author_id;
				$this->is_loaded = false;
			}
		}
	}
	
	/*
	 *	returns an array of all selected authors
	 */
	public function get_authors() {
		return $this->authors;
	}
	
	/*
	 *	deletes all selected authors
	 */
	public function clear_authors() {
		$this->authors = array();
		$this->is_loaded = false;
	}
	
	/*
	 *	adds an user to the users list
	 */
	public function add_user( $user_id ) {
		$user_id = (int) $user_id;
		if ( $user_id > 0 ) {
			$res = array_search( $user_id, $this->users, true );
			if ( $res === false ) {
				// not added yet
				$this->users[] = $user_id;
				$this->is_loaded = false;
			}
		}
	}
	
	/*
	 *	returns an array of all selected users
	 */
	public function get_users() {
		return $this->users;
	}
	
	/*
	 *	deletes all selected users
	 */
	public function clear_users() {
		$this->users = array();
		$this->is_loaded = false;
	}
	
	/*
	 *	resets all values to default
	 */
	public function reset() {
		$this->list = array();
		$this->clear_filters();
		$this->authors = array();
		$this->users = array();
		$this->search_string = '';
		$this->min = 0;
		$this->max = 12;
		$this->sorting_field = '';
		$this->sorting_order = '';
		$this->status = '> 7';
		
		$this->where_clause = '';
		
		$this->is_loaded = false;
	}
	
	// TODO: add search string support!
	private function build_sql_clause( $no_limits = false ) {
		
		// === every statement ends with a space! ===
		
		// basics
		$sql = 'SELECT pa.id AS package_id FROM ' . $this->db->prefix . 'pvmkit_packages AS pa ';
		
		// where status
		$sql_where = 'WHERE (pa.status ' . $this->status . ') ';
		
		// where filters
		foreach ( $this->filters as $filter_group => $filter_ids ) {
			
			// create a where condition to append
			$cond = 'AND ( pa.id IN ( SELECT package_id FROM ' . $this->db->prefix . 'pvmkit_property_index WHERE ';
			$op = '';
			foreach ( $filter_ids as $filter_id ) {
				$cond .= $op . '( property_id = ' . $filter_id . ' ) ';
				$op = 'OR ';
			}
			$cond .= ' ) ) ';
			
			$sql_where .= $cond;
		}
		
		// where author/user
		if ( count( $this->authors ) > 0 ) {
			$sql_where .= 'AND ( pa.author IN ( ' . implode( ' , ', $this->authors ) . ' ) ) ';
		}
		if ( count( $this->users ) > 0 ) {
			$sql_where .= 'AND ( pa.user_id IN ( ' . implode( ' , ', $this->users ) . ' ) ) ';
		}
		
		$sql .= $sql_where;
		
		// sorting
		if ( ( $this->sorting_field != '' ) && ( $this->sorting_order != '' ) ) {
			$sql .= 'ORDER BY ' . $this->sorting_field . ' ' . $this->sorting_order . ' ';
		}
		
		// no_limits mode for count, only returns where part
		if ( $no_limits ) {
			return $sql_where;
		}
		
		// limits
		$sql .= 'LIMIT ' . $this->min . ', ' . $this->max; // no space at the end!
		
		// save $sql_where AND return $sql
		$this->where_clause = $sql_where;
		return $sql;
	}
	
	/*
	 *	executes a request on the database using all applied filters
	 */
	public function request( $debug = false ) {
		
		if ( !$this->is_loaded ) {
			
			$sql = $this->build_sql_clause();
			$res = $this->db->get_results( $sql );
			$this->list = array();
			
			foreach ( $res as $res_row ) {
				$new_package = new pvmkit_package();
				$new_package->load_by_id( $res_row->package_id );
				$this->list[] = $new_package;
			}
			
			$this->is_loaded = true;
			
			if ( $debug ) {
				return $sql;
			}
		}
	}
	
	/*
	 *	returns the number of packages that match the filter
	 */
	public function get_total_result_count() {
		
		$sql = 'SELECT COUNT(*) FROM ' . $this->db->prefix . 'pvmkit_packages AS pa ';
		
		$res = $this->db->get_var( $sql . $this->build_sql_clause( true ) );
		
		if ( $res === NULL ) {
			return 0;
		} else {
			return $res;
		}
		
	}
	
	/*
	 *	sets the field used for order
	 *	TODO: possible values, see $sorting_templates
	 */
	public function set_sorting_field( $sort_id ) {
		if ( $sort_id !== NULL ) {
			$res = array_search( $sort_id, $this->sorting_templates, true );
			if ( $res !== false ) {
				$this->sorting_field = $sort_id;
				$this->is_loaded = false;
			}
		}
	}
	
	/*
	 *	sets an order rule for the result list
	 *	ASC or DESC (string, case sensitive)
	 */
	public function set_sorting_order( $order_id ) {
		if ( $order_id !== NULL ) {
			if ( ( $order_id === 'ASC' ) || ( $order_id === 'DESC' ) ) {
				$this->sorting_order = $order_id;
				$this->is_loaded = false;
			}
		}
	}
	
	/*
	 *	sets the status filter
	 *	see below for possible values (default: > 7)
	 */
	public function set_status( $cond = '' ) {
		switch ( $cond ) {
			case 'public':
				$this->status = '> 7';
				break;
			case 'byauthor':
				$this->status = '> 0';
				break;
			case 'editable':
				$this->status = '= 1';
				break;
			case 'edited':
				$this->status = '= 5';
				break;
			case 'deleted':
				$this->status = '= 0';
				break;
			default:
				$this->status = '> 7';
		}
		$this->is_loaded = false;
	}
	
	/*
	 *	sets filters, search string, etc. to values given by the $url string
	 */
	public function process_url( $url ) {
		
		// TODO
		
		$this->is_loaded = false;
	}
	
	/*
	 *	returns HTML code with debug information
	 */
	public function debug_info() {	
		return '<div>Request: ' . ( $this->is_loaded ? 'done' : 'NOT done' ) . '<br />Filters: ' . print_r( $this->filters, true) . '<br />Authors: ' . print_r( $this->authors, true) . '<br />Search: ' . $this->search_string . '<br />Limits: ' . $this->min . ' / ' . $this->max . '<br />Sorting: ' . $this->sorting_field . ' - ' . $this->sorting_order . '<br />Query WHERE: <code>' . $this->where_clause . '</code></div>';
	}
}

?>