<?php

class pvmkit_object_image extends pvmkit_object {
	
	protected $type = 'image';
	
	/*
	 *	returns a string to access this resource via HTTP
	 */
	public function get_url( $size ){
		$version = 'large';
		switch ( $size ) {
			case 'large':
				$version = 'large';
				break;
			case 'original':
				$version = 'original';
				break;
		}
		
		if ( $this->exists() ) {
			return wp_upload_dir()[ 'baseurl' ] . '/pvm/images/' . $this->object_id . '_' . $version . '_' . $this->content . '.jpg';
		} else {
			return plugins_url() . '/pvmkit/images/default_image_' . $version . '.jpg';
		}
	}
	
	/*
	 *	modifies a given imagefile to suit the plattform requirements // LEGACY
	 */
	public function set_image( $image_path ) {
		// create resized versions
		$image = wp_get_image_editor( $image_path );
		if ( !is_wp_error( $image ) ) {
			$image->save( PVMKIT_UPLOAD_PATH . 'images/' . $this->object_id . '_original_' . $this->content . '.jpg' );
			$image->resize( 600, null, false );
			$image->save( PVMKIT_UPLOAD_PATH . 'images/' . $this->object_id . '_large_' . $this->content . '.jpg' );
			return true;
		}
		return false;
	}
	
	public function set( $author = null, $content = null ) {
		
		$image_path = '';
		$orig_name = '';
		
		$remove_old = false;
		
		// check the content parameter
		if ( ( gettype( $content ) == 'array' ) && ( count( $content ) == 2 ) ) {
			// set a new image
			$remove_old = true;
			$image_path = $content[0];
			$orig_name = $content[1];	
		} else if ( ( gettype( $content ) == 'string' ) && ( $content == '' ) ) {
			// delete old image
			$remove_old = true;
		} else if ( gettype( $content ) == 'NULL' ) {
			// nothing to change
		} else {
			return false;
		}
		
		// delete previous versions if available
		if ( $remove_old && ( $this->content != '' ) ) {
			if ( file_exists( PVMKIT_UPLOAD_PATH . 'images/' . $this->object_id . '_original_' . $this->content . '.jpg' ) ) {
				unlink( PVMKIT_UPLOAD_PATH . 'images/' . $this->object_id . '_original_' . $this->content . '.jpg' );
			}
			if ( file_exists( PVMKIT_UPLOAD_PATH . 'images/' . $this->object_id . '_large_' . $this->content . '.jpg' ) ) {
				unlink( PVMKIT_UPLOAD_PATH . 'images/' . $this->object_id . '_large_' . $this->content . '.jpg' );
			}
		}
		
		// set in database
		parent::set( $author, $orig_name );
		
		// create resized versions
		$image = wp_get_image_editor( $image_path );
		if ( !is_wp_error( $image ) ) {
			$image->save( PVMKIT_UPLOAD_PATH . 'images/' . $this->object_id . '_original_' . $this->content . '.jpg' );
			$image->resize( 600, null, false );
			$image->save( PVMKIT_UPLOAD_PATH . 'images/' . $this->object_id . '_large_' . $this->content . '.jpg' );
		} else {
			return false;
		}
		
		return true;
	}
	
	/*
	 *	html output of object
	 */
	public function __toString(){
		return '<div><p>[' . $this->get_type() . '] Object-ID: ' . $this->object_id . ' | Author-ID: ' . $this->author->get_author_id() . '</p><img src="http://pcai042.informatik.uni-leipzig.de/~pvm-15/wp/wp-content/plugins/pvmkit/uploads/data/' . $this->object_id . '_original_' . $this->content . '.jpg" /></div>';
	}
}

?>