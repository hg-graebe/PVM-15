<?php
/*
 *	saves submitted form data related to package editing to the database
 *	use: create an instance and use save_form_data()
 *	Author: Sebastian Guenther (sec.sebastian@gmail.com)
 */
 
class pvmkit_studio_package_processor {
	
	protected $db = null;
	protected $ws = null;
	
	protected $package = null;
	protected $package_id = -1;
	protected $form = '';
	
	public function __construct( $ws ) {
		global $wpdb;
		$this->db = $wpdb;
		$this->ws = $ws;
	}
	
	/*
	 *	checks which form has been submitted and saves data accordingly
	 *	returns true on success, false if any error occurs
	 */
	public function save_form_data() {
		$ret = true;
		
		// get package_id
		if ( isset( $_POST['mtpakid'] ) && is_numeric( $_POST['mtpakid'] ) ) {
			$this->package_id = (int) $_POST['mtpakid'];
		} else {
			return false;
		}
		
		if ( $this->package_id > 0 ) {
			
			$this->package = new pvmkit_package_editable( $this->package_id );
			if ( !$this->package->exists() ) {
				return false;
			}
			
			if ( isset( $_POST['mtform'] ) ) {
				$this->form = $_POST['mtform'];
				
				// check permission and save
				if ( $this->package->can_edit() ) {
					
					switch ( $this->form ) {
						case 'text':
							$ret = $this->save_text();
							break;
						case 'image':
							$ret = $this->save_image();
							break;
						case 'properties':
							$ret = $this->save_properties();
							break;
						default:
							return false;
					}
					
				} else {
					return false;
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
		
		return $ret;
	}
	
	/*
	 *	
	 */
	private function save_text() {
		
		// handle if we have delegated authorship
		$set_author = false;
		$new_author = -1;
		
		if ( current_user_can( 'pvm_delegated_authorship' ) && isset( $_POST['mtauthor'] ) ) {
			
			$new_author = (int) $_POST['mtauthor'];
			$authors = get_author_profiles();
			$found = false;
			
			foreach ( $authors as $author ) {
				if ( $author['author_id'] == $new_author ) {
					$found = true;
				}
			}
			
			if ( $found && ( $new_author > 0 ) ) {
				$set_author = true;
				echo 'changed author';
			}
		}
		
		if ( $set_author ) {
			$this->package->get_text()->set( $new_author, $_POST['mttext'] ); // NO sanitize to keep the line breaks
			$this->package->change_author( $new_author, false );
		} else {
			$this->package->get_text()->set( NULL, $_POST['mttext'] );
		}
		
		// update title
		$this->package->set_title( sanitize_text_field( $_POST['mttitle'] ) );
		$this->package->set_publish_date( current_time('mysql', 1) );
		$this->package->update();
	}
	
	/*
	 *	processes both image and titleimage upload form
	 */
	private function save_image() {
		
		$type = '';
		
		// image or titleimage?
		if ( isset( $_POST['mttype'] ) ) {
			$type = $_POST['mttype'];
		}
		
		if ( ( $type == 'image' ) || ( $type == 'titleimage' ) ) {
			
			if ( isset( $_FILES['imagefile']['tmp_name'] ) ) {
				
				$fe = file_exists( $_FILES['imagefile']['tmp_name'] );
				
				$image_file_path = PVMKIT_UPLOAD_PATH . 'zip/' . time() . '_' . rand(1000, 9999);
				$image_name = '';
				if ( $fe ) {
					move_uploaded_file( $_FILES['imagefile']['tmp_name'], $image_file_path . '.jpg' );
					$image_name = $_FILES['imagefile']['name'];
				}
				$res = '';
				
				$author = new pvmkit_author( get_current_user_id(), false );
				
				if ( $type == 'image' ) {
					
					// IMAGE
					$new = !$this->package->has_image();
					
					$res = $this->package->get_image()->set( $author, array( $image_file_path . '.jpg', $image_name ) );
					if ( $new ) {
						$this->db->insert(
							$this->db->prefix . 'pvmkit_package_components', 
							array( 
								'package_id' => $this->package_id,
								'object_id' => $this->package->get_image()->get_object_id(),
								'type' => 'image'
							), 
							array( '%d', '%d', '%s' ) 
						);
					}
					
					if ( $res && $fe ) {
						$this->ws->add_info( 'upload_successful', array( $image_name ) );
					}
					
				} else if ( $type == 'titleimage' ) {
					
					// TITLEIMAGE
					$new = !$this->package->has_titleimage();
					
					$res = $this->package->get_titleimage()->set( $author, array( $image_file_path . '.jpg', $image_name ) );
					if ( $new ) {
						$this->db->insert(
							$this->db->prefix . 'pvmkit_package_components', 
							array( 
								'package_id' => $this->package_id,
								'object_id' => $this->package->get_titleimage()->get_object_id(),
								'type' => 'titleimage'
							), 
							array( '%d', '%d', '%s' ) 
						);
					}
					
					if ( $res && $fe ) {
						$this->ws->add_info( 'upload_successful', array( $image_name ) );
					}
					
				}
				
				// clean up
				if ( $fe ) {
					unlink( $image_file_path . '.jpg' );
				}
			}
		}
	}
	
	/*
	 *	saves the properties form
	 */
	private function save_properties() {
		
		// delete previous values in DB
		$this->db->delete( $this->db->prefix . 'pvmkit_property_index', array( 'package_id' => $this->package_id ), array( '%d' ) );
		
		// collect new selected properties
		$new_properties = array();
		if ( isset( $_POST[ 'mtcol' ] ) ) {
			foreach ( $_POST[ 'mtcol' ] as $id ) {
				$new_properties[] = (int) $id;
			}
		}
		if ( isset( $_POST[ 'mtopt' ] ) ) {
			foreach ( $_POST[ 'mtopt' ] as $id ) {
				$new_properties[] = (int) $id;
			}
		}
		if ( isset( $_POST[ 'mtmat' ] ) ) {
			foreach ( $_POST[ 'mtmat' ] as $id ) {
				$new_properties[] = (int) $id;
			}
		}
		if ( isset( $_POST[ 'mtsfc' ] ) ) {
			foreach ( $_POST[ 'mtsfc' ] as $id ) {
				$new_properties[] = (int) $id;
			}
		}
		
		if ( isset( $_POST[ 'mtsze' ] ) ) {
			$new_properties[] = (int) $_POST[ 'mtsze' ];
		}
		if ( isset( $_POST[ 'mtage' ] ) ) {
			$new_properties[] = (int) $_POST[ 'mtage' ];
		}
		
		// insert previously collected IDs into the DB
		foreach ( $new_properties as $id ) {
			if ( $id > 0 ) { // FLAW: we can still use wrong/higher values here!
				$this->db->insert(
					$this->db->prefix . 'pvmkit_property_index', 
					array( 'package_id' => $this->package_id, 'property_id' => $id ), 
					array( '%d', '%d' ) 
				);
			}
		}
		
		$this->ws->add_info( 'properties_saved' );
		
	}
	
}
?>