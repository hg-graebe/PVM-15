<?php

class pvmkit_ws_register extends pvmkit_ws_module {
	
	protected $id = 'register';
	protected $layout = 'fullwidth';
	
	protected $submit = false;
	protected $error = false;
	protected $error_messages = array();
	
	/*
	 *	checks if the current user is allowed to use this module
	 */
	public function user_has_access() {
		return !is_user_logged_in();
	}
	
	/*
	 *	processes the request and prepares for output
	 */
	public function process() {
		
		// what package are we even talking about?
		if ( isset( $_GET['mtpakid'] ) && is_numeric( $_GET['mtpakid'] ) ) {
			$this->package_id = (int) $_GET['mtpakid'];
		}
		
		// validate data, if submitted
		if ( isset($_POST['submit'] ) ) {
			
			$this->submit = true;
			
			$username   =   sanitize_user( $_POST['mt_username'] );
			$password   =   esc_attr( $_POST['mt_password'] );
			$password2  =   esc_attr( $_POST['mt_password2'] );
			$email      =   sanitize_email( $_POST['mt_email'] );
			$vname 		=   sanitize_text_field( $_POST['mt_vname'] );
			$nname  	=   sanitize_text_field( $_POST['mt_nname'] );
			$age  		=   sanitize_text_field( $_POST['mt_age'] );
			$location  	=   sanitize_text_field( $_POST['mt_location'] );
			
			if ( empty( $username ) || empty( $password ) || empty( $email ) ) {
				$this->error_messages[] = 'Du musst alle Pflichtfelder ausf&uuml;llen.';
			}
			if ( 4 > strlen( $username ) ) {
				$this->error_messages[] = 'Dein Benutzername muss mindestens 4 Zeichen lang sein.';
			}
			if ( username_exists( $username ) ) {
				$this->error_messages[] = 'Dieser Benutzername ist bereits vergeben.';
			}
			if ( !validate_username( $username ) ) {
				$this->error_messages[] = 'Der eingegebene Benutzername ist ung&uuml;ltig.';
			}
			if ( strlen( $password ) < 5 ) {
				$this->error_messages[] = 'Das Passwort muss mindestens 6 Zeichen enthalten.';
			}
			if ( $password != $password2 ) {
				$this->error_messages[] = 'Die Passw&ouml;rter müssen identisch sein.';
			}
			if ( !is_email( $email ) ) {
				$this->error_messages[] = 'Die E-Mail-Adresse ist ung&uuml;ltig.';
			}
			if ( email_exists( $email ) ) {
				$this->error_messages[] = 'Diese E-Mail-Adresse ist bereits in Verwendung.';
			}
			
			// if everythings is ok, register
			if ( count( $this->error_messages ) == 0 ) {
				
				$userdata = array(
					'user_login'    =>   $username,
					'user_email'    =>   $email,
					'user_pass'     =>   $password,
					'first_name'    =>   $vname,
					'last_name'     =>   $nname,
					'nickname'      =>   $username
				);
				$user_id = wp_insert_user( $userdata );
				
				if ( !is_wp_error( $user_id ) ) {
					update_user_meta( $user_id, 'age', $age );
					update_user_meta( $user_id, 'location', $location );
				}
				
			} else {
				$this->error = true;
			}
			
			
		}
		
		if ( isset($_POST['submit'] ) ) {
			registration_validation(
			$_POST['username'],
			$_POST['password'],
			$_POST['email'],
			$_POST['website'],
			$_POST['fname'],
			$_POST['lname'],
			$_POST['nickname'],
			$_POST['bio']
			);

			// sanitize user form input
			global $username, $password, $email, $website, $first_name, $last_name, $nickname, $bio;
			$username   =   sanitize_user( $_POST['username'] );
			$password   =   esc_attr( $_POST['password'] );
			$email      =   sanitize_email( $_POST['email'] );
			$website    =   esc_url( $_POST['website'] );
			$first_name =   sanitize_text_field( $_POST['fname'] );
			$last_name  =   sanitize_text_field( $_POST['lname'] );
			$nickname   =   sanitize_text_field( $_POST['nickname'] );
			$bio        =   esc_textarea( $_POST['bio'] );

			// call @function complete_registration to create the user
			// only when no WP_error is found
			if ( 1 > count( $reg_errors->get_error_messages() ) ) {
				$userdata = array(
				'user_login'    =>   $username,
				'user_email'    =>   $email,
				'user_pass'     =>   $password,
				'user_url'      =>   $website,
				'first_name'    =>   $first_name,
				'last_name'     =>   $last_name,
				'nickname'      =>   $nickname,
				'description'   =>   $bio,
				);
				//$user = wp_insert_user( $userdata );
				//echo 'Registration complete. Goto <a href="' . get_site_url() . '/wp-login.php">login page</a>.';   
			}
		}
		
		
		
	}
	
	/*
	 *	returns HTML code for the main area
	 */
	public function get_content() {
		$o = '';
		
		if ( !$this->submit || $this->error ) {

			$o .= '<form action="' . $_SERVER['REQUEST_URI'] . '" method="post">';
			
			
			$o .= '<div><label for="mt_username">Benutzername <strong>*</strong></label>
			<input type="text" name="mt_username" value="' . ( isset( $_POST['mt_username'] ) ? $_POST['mt_username'] : null ) . '"></div>';
			$o .= '<div><label for="mt_email">E-Mail <strong>*</strong></label>
			<input type="text" name="mt_email" value="' . ( isset( $_POST['mt_email'] ) ? $_POST['mt_email'] : null ) . '"></div>';
			
			$o .= '<div><label for="mt_password">Passwort <strong>*</strong></label>
			<input type="password" name="mt_password" value="' . ( isset( $_POST['mt_password'] ) ? $_POST['mt_password'] : null ) . '"></div>';
			
			$o .= '<div><label for="mt_password2">Passwort bestätigen<strong>*</strong></label>
			<input type="password" name="mt_password2" value="' . ( isset( $_POST['mt_password2'] ) ? $_POST['mt_password2'] : null ) . '"></div>';
			
			$o .= '<div><label for="mt_vname">Vorname</label>
			<input type="text" name="mt_vname" value="' . ( isset( $_POST['mt_vname'] ) ? $_POST['mt_vname'] : null ) . '"></div>';
			$o .= '<div><label for="mt_nname">Nachname</label>
			<input type="text" name="mt_nname" value="' . ( isset( $_POST['mt_nname'] ) ? $_POST['mt_nname'] : null ) . '"></div>';
			
			$o .= '<div><label for="mt_age">Alter</label>
			<input type="text" name="mt_age" value="' . ( isset( $_POST['mt_age'] ) ? $_POST['mt_age'] : null ) . '"></div>';
			
			$o .= '<div><label for="mt_location">Ort</label>
			<input type="text" name="mt_location" value="' . ( isset( $_POST['mt_location'] ) ? $_POST['mt_location'] : null ) . '"></div>';
			
			$o .= '<input type="submit" name="submit" value="Register"/></form>';	

		} else if ( $this->submit && !$this->error ) {
			
			// confirm message
			$o .= 'Du hast dich erfolgreich registriert. Nach einer Best&auml;tigung kannst du dich <a href="' . get_site_url() . '/wp-login.php">anmelden</a>.';
			
		}
		
		
		return $o;
	}
	
}
?>