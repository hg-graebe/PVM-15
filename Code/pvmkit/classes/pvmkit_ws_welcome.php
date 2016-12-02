<?php
class pvmkit_ws_welcome extends pvmkit_ws_module {
	
	protected $id = 'welcome';
	protected $layout = 'sidebar';
	
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
		
		$ns = $this->ws->get_notifications();
		foreach ( $ns as $n ) {
			$o .= '<div class="pvm_msg_info">' . $n . '</div>';
		}
		
		return $o;
	}
	
	/*
	 *	returns HTML code for the sidebar
	 */
	public function get_sidebar() {
		
		$o = '';
		
		return $o;
	}
}
?>