<?php
class pvmkit_ws_ extends pvmkit_ws_module {
	
	protected $id = '';
	protected $layout = 'fullwidth';
	
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
		
	}
	
	/*
	 *	returns HTML code for the main area
	 */
	public function get_content() {
		$o = '';
		
		
		
		return $o;
	}
	
}
?>