<?php
class pvmkit_workshop {
	
	private $db = null;
	private $author = null;
	
	private $modules = array(
		'welcome' 			=> 'pvmkit_ws_welcome',
		'my_packages' 		=> 'pvmkit_ws_my_packages',
		'view_package' 		=> 'pvmkit_ws_view_package',
		'edit_package' 		=> 'pvmkit_ws_edit_package',
		'edit_properties' 	=> 'pvmkit_ws_edit_properties',
		'edit_image' 		=> 'pvmkit_ws_edit_image',
		'my_projects' 		=> 'pvmkit_ws_my_projects',
		'my_projects_proposed' => 'pvmkit_ws_my_projects_proposed',
		'propose_package' 	=> 'pvmkit_ws_propose_package',
		'edit_project' 		=> 'pvmkit_ws_edit_project',
		'edit_project_text' => 'pvmkit_ws_edit_project_text',
		'edit_project_images' => 'pvmkit_ws_edit_project_images',
		'register' 			=> 'pvmkit_ws_register',
		'edit_user' 		=> 'pvmkit_ws_edit_user',
		'edit_user_image' 	=> 'pvmkit_ws_edit_user_image',
		'package_actions' 	=> 'pvmkit_ws_package_actions',
		'manage_authors' 	=> 'pvmkit_ws_manage_authors',
		'report' 			=> 'pvmkit_ws_report'
	);	
	private $messages = array();
	private $message_templates = array(
		'upload_successful'	=> 'Die Datei &laquo;%1$s&raquo; wurde erfolgreich hochgeladen.',
		'file_not_found' 	=> 'Die Datei &laquo;%1$s&raquo; wurde nicht gefunden.',
		'file_not_valid' 	=> 'Die Datei &laquo;%1$s&raquo; hat ein ung&uuml;ltiges Format.',
		'package_locked' 	=> 'Das Werk wurde zur Ver&ouml;ffentlichung freigegeben.',
		'package_unlocked' 	=> 'Du kannst das Werk nun wieder bearbeiten.',
		'package_deleted' 	=> 'Das Werk wurde gel&ouml;scht.',
		'package_published'	=> 'Das Werk wurde ver&ouml;ffentlicht.',
		'package_unpublishedrev'	=> 'Das Werk wurde in die Redaktion gestellt.',
		'package_unpublishedws'	=> 'Das Werk wurde erneut zum Bearbeiten freigegeben.',
		'package_saved'		=> 'Die &Auml;nderungen wurden gespeichert.',
		'properties_saved'	=> 'Die Merkmale wurden gespeichert.',
		'image_saved'		=> 'Das Bild wurde gespeichert.',
		'system_error' 		=> 'Systemfehler: %1$s',
		'unknown_error' 	=> 'Ein unbekannter Fehler ist aufgetreten.',
		'missing_params' 	=> 'Parameterfehler: %1$s',
		'debug_info' 		=> '%1$s',
		'info' 				=> '%1$s',
		'access_denied' 	=> 'Zugriff verweigert.',
		'empty' 			=> ''
	);
	private $strings = array(
		'my_packages' 		=> 'Meine Werke',
		'my_projects' 		=> 'Meine Projekte',
		'new_package' 		=> 'Werk erstellen',
		'' => '',
		'admin_projects' 	=> 'Projekte verwalten',
		'admin_packages' 	=> 'Werke freischalten' 
	);
	
	/*
	 *	sets up the object
	 */
	public function __construct() {
		global $wpdb;
		$this->db = $wpdb;
		
		$this->author = new pvmkit_author( get_current_user_id(), false );
	}
	
	/*
	 *	return a display module class depending on the query
	 */
	public function find_module() {
		$module_id = 'my_packages';		// default module id
		if ( isset( $_GET[ 'mtm' ] ) ) {
			$module_id = $_GET[ 'mtm' ];
		}
		
		$module_class = 'pvmkit_ws_my_packages';		// default module
		if ( isset( $this->modules[ $module_id ] ) ) {
			$module_class = $this->modules[ $module_id ];
		}
		//$this->add_info( 'debug_info', array( 'ID: ' . $module_id . ' ; CLASS: ' . $module_class ) );
		
		return $module_class;
	}
	
	/*
	 *	returns the URL for the workshop with the module
	 */
	public function get_url( $module = 'my_packages', $params = '' ) {		// default URL
		return get_home_url() . '/studio/?mtm=' . $module . '&' . $params;
	}
	
	/*
	 *	return text for string ID
	 */
	public function get_string( $string_id ) {
		return $this->strings[ $string_id ];
	}
	
	/*
	 *	converts an array of modules to a HTML5 menu
	 */
	public function menu_array_to_html( $menu_array ) {
		
		$html = '<ul class="pvm_menu">';
		
		foreach ( $menu_array as $menu_id => $menu_url ) {
			if ( is_array( $menu_url ) ) {
				// if item is array, make a submenu
				$html .= '<li>' . menu_array_to_html( $menu_url ) . '</li>';
			} else {
				// create item as link
				$html .= '<li><a href="' . $menu_url . '">' . $this->strings[ $menu_id ] . '</a></li>';
			}
		}
		
		$html .= '</ul>';
		
		return $html;
	}
	
	/*
	 *	add_info, add_warning, add_error: adds the given message/information to the list for display
	 */
	private function add_message( $msg, $type ) {
		$this->messages[] = '<div class="pvm_msg_' . $type . '">' . $msg . '</div>';
	}
	
	public function add_info( $message_id, $params = array() ) {
		$this->add_message( vsprintf( $this->message_templates[ $message_id ], $params ), 'info' );
	}
	
	public function add_warning( $message_id, $params = array() ) {
		$this->add_message( vsprintf( $this->message_templates[ $message_id ], $params ), 'warn' );
	}
	
	public function add_error( $message_id, $params = array() ) {
		$this->add_message( vsprintf( $this->message_templates[ $message_id ], $params ), 'error' );
	}

	/*
	 *	Returns a string containing all messages that have been added by the above functions
	 */
	public function get_messages(  ) {
		$out = '';
		foreach ( $this->messages as $msg ) {
			$out .= $msg;
		}
		return $out;
	}
	

	/*
	 *	returns the author object of the current user
	 */
	public function get_author() {
		return $this->author;
	}

	/*
	 *	
	 */
	public function add_notification( $user_id, $type, $data ) {
		return $this->db->insert(
			$this->db->prefix . 'pvmkit_notifications', 
			array( 'user_id' => $user_id, 'type' => $type, 'data' => serialize( $data ) ), 
			array( '%d', '%s', '%s' ) 
		);
	}

	/*
	 *	returns notifcations for the current user
	 */
	public function get_notifications( $time = 1209600 ) {
		
		$n = array();
		$t = array(
			'package_published' => 'Dein Werk &laquo;<a href="' . get_home_url() . '/index.php/single_package/?pkg_id=%1$d">%2$s</a>&raquo; wurde ver&ouml;ffentlicht.',
			'package_published_adm' => 'Das Werk &laquo;<a href="' . get_home_url() . '/index.php/single_package/?pkg_id=%1$d">%2$s</a>&raquo; wurde von %3$s ver&ouml;ffentlicht.',
			'package_rejected' => 'Dein Werk &laquo;<a href="' . get_home_url() . '/index.php/single_package/?pkg_id=%1$d">%2$s</a>&raquo; wurde abgewiesen: %3$s',
			'package_reported' => 'Report: Werk &laquo;<a href="' . get_home_url() . '/index.php/single_package/?pkg_id=%1$d">%2$s</a>&raquo; wurde von %3$s (UID %4$d) gemeldet: %5$s'
		);
		
		$n_data = $this->db->get_results( 'SELECT `notification_id`, DATE_FORMAT(`date`,"%d.%m. %H:%i") AS datef, `type`, `data` FROM ' . $this->db->prefix . 'pvmkit_notifications WHERE ( user_id = ' . get_current_user_id() . ( current_user_can( 'pvm_publish_packages' ) ? ' OR user_id = 0' : '' ) . ' ) AND `date` > ' . ( time() - $time ) );
		foreach ( $n_data as $noti ) {
			$n[] = $noti->datef . ' Uhr | ' . vsprintf( $t[ $noti->type ], unserialize($noti->data) );
		}
		
		return $n;
	}

	/*
	 *	
	 */
	public function send_email( $user_id, $subject, $message ) {
		$u = get_userdata( $user_id );
		if ( $u === false ) {
			return false;
		} else {
			return wp_mail( $u->user_email, $subject, $message );
		}
	}
}
?>