<?php

class pvmkit_ws_edit_user_image extends pvmkit_ws_module {
	
	protected $id = 'edit_user_image';
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
		
		if ( isset( $_FILES['imagefile']['tmp_name'] ) ) {
		
			if ( file_exists( $_FILES['imagefile']['tmp_name'] ) ) {
				
				// move file
				$image_file_path = PVMKIT_UPLOAD_PATH . 'user_images/' . get_current_user_id();
				move_uploaded_file( $_FILES['imagefile']['tmp_name'], $image_file_path . '_upl.jpg' );
				
				// save resized versions
				$image = wp_get_image_editor( $image_file_path . '_upl.jpg' );
				if ( !is_wp_error( $image ) ) {
					$image->save( $image_file_path . '_original.jpg' );
					$image->resize( 128, 182, true );
					$image->save( $image_file_path . '_medium.jpg' );
					$image->resize( 64, 64, true );
					$image->save( $image_file_path . '_small.jpg' );
				}
				
				// update user meta field
				update_user_meta( get_current_user_id(), 'mt_avatar', wp_upload_dir()[ 'baseurl' ] . '/pvm/user_images/' . get_current_user_id() ); // _RES.jpg
				
				// clean up
				unlink( $image_file_path . '_upl.jpg' );
			}
		}
	}
	
	/*
	 *	returns HTML code for the main area
	 */
	public function get_content() {
		
		$o = '<form method="post" action="' . $this->ws->get_url( 'edit_user_image' ) . '" enctype="multipart/form-data">';
		$o .= '<input type="file" name="imagefile" id="imagefile" />';
		$o .= '<div class="pvm_bottom_link_row"><input type="submit" id="mtsubmit" name="mtsubmit" value="Speichern" class="pvm_save" /></div>';
		$o .= '</form>';
		
		return $o;
	}
	
}
?>