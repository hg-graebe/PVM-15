<?php
abstract class pvmkit_ws_module {
	
	protected $db = null;
	protected $ws = null;

	protected $id = 'dashboard';
	protected $layout = 'sidebar';
	
	/*
	 *	sets up the object
	 */
	public function __construct( $ws ) {
		global $wpdb;
		$this->db = $wpdb;
		
		$this->ws = $ws;
	}
	
	/*
	 *	checks if the current user is allowed to use this module
	 */
	public abstract function user_has_access();
	
	/*
	 *	processes the request and prepares for output
	 */
	public abstract function process();
	
	/*
	 *	returns HTML code for the main area
	 */
	public abstract function get_content();
	
	/*
	 *	returns HTML code for the sidebar
	 */
	public function get_sidebar() {
		return '';
	}
	
	/*
	 *	returns a string representing the layout to render
	 */
	public function get_layout() {
		return $this->layout;
	}
	
}
?>