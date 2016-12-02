<?php

require_once( __DIR__.'/../libs/EasyRdf.php' );

/*	addiotionally to pvmkit_package it can...
 *	- import a package from ZIP
 *	- write/update package content in database
 *	- export a package to a ZIP file
 */

class pvmkit_package_editable extends pvmkit_package {
	
	protected $text_url = '';
	protected $image_url = '';
	protected $titleimage_url = '';
	
	/*
	 *	deletes a folder with content
	 */
	private function delete_folder( $path ) {
		if ( is_dir( $path ) === true ) {
			$files = array_diff( scandir( $path ), array('.', '..') );
			foreach ( $files as $file ) {
				$this->delete_folder( realpath( $path ) . '/' . $file );
			}
			return rmdir( $path );
		} else if ( is_file( $path ) === true ) {
			return unlink( $path );
		}
		return false;
	}
	
	/*
	 *	Extracts and imports the data given by $filename
	 *	(currently external development)
	 */
	/*public function import_zip() {
		
		error_reporting(E_ALL);
		$pvmkit_msg = '';
		$is_ok = true;
		$pvmkit_file = PVMKIT_UPLOAD_PATH . 'zip/' . time() . '_' . rand(1000, 9999);
		
		$text_id = '';
		$titleimage_id = '';
		$image_id = '';
		
		if ( isset( $_FILES['zipfile']['tmp_name'] ) ) {
			//  === extract ZIP file ===
			move_uploaded_file( $_FILES['zipfile']['tmp_name'], $pvmkit_file.'.zip' );
			
			$pvmkit_zip = new ZipArchive;
			$pvmkit_zip_state = $pvmkit_zip->open($pvmkit_file.'.zip');
			if ($pvmkit_zip_state === TRUE) {
				$pvmkit_zip->extractTo($pvmkit_file.'/');
				$pvmkit_zip->close();
			} else {
				$is_ok = false;
			}
		} else {
			$is_ok = false;
		}
		
		// set up RDF
		EasyRdf_Namespace::set('pvm', 'http://pvm.uni-leipzig.de/Model/');
		$manifest = new EasyRdf_Graph("http://pvm.uni-leipzig.de/Data/Merkmale/");
		
		// === load manifest file ===
		if ($is_ok) {
			$manifest->parseFile($pvmkit_file.'/manifest.ttl');
			
			// read package properties
			foreach ( $manifest->allOfType('pvm:Package') as $n ) {
				$this->title = $n->get('dcterms:title');
				$this->publish_date = $n->get('dcterms:created');
				$this->author = new pvmkit_author();
				//$this->author->load_by_full_name( $n->get( 'dcterms:creator' )->label() );
				$this->author->load_by_user_id( get_current_user_id(), true );
				
				// import text object
				$text_id = $n->get( 'pvm:text' );
				if ( $text_id != '' ) {
					$text_url = $text_id->get( 'pvm:content' );
					if ( file_exists( $text_url ) ) {
						$text_content = file_get_contents( $text_url );
						
						$this->text = new pvmkit_object_text();
						$this->text->set_content( $text_content );
						$this->text->set_author_by_name( $text_id->get( 'dcterms:creator' )->label() );
						$this->text_url = $text_url;
					} else {
						$pvmkit_msg .= '<li class="error">Die Textdatei fehlt (' . $text_url . ')</li>';
						$is_ok = false;
					}
				} else {
					$pvmkit_msg .= '<li class="error">Das Paket enth&auml;lt keinen Text</li>';
					$is_ok = false;
				}
				
				// import title image object
				$titleimage_id = $n->get( 'pvm:titleimage' );
				if ( $titleimage_id != '' ) {
					$titleimage_url = $titleimage_id->get( 'pvm:content' );
					if ( file_exists( $titleimage_url ) ) {
						$this->titleimage = new pvmkit_object_titleimage();
						$titleimage_pathinfo = pathinfo( $titleimage_url );
						$this->titleimage->set_content( $titleimage_pathinfo['filename'] );
						$this->titleimage->set_author_by_name( $titleimage_id->get( 'dcterms:creator' )->label() );
						$this->titleimage_url = $titleimage_url;
					} else {
						$pvmkit_msg .= '<li class="error">Die Titelbilddatei fehlt (' . $titleimage_url . ')</li>';
						$is_ok = false;
					}
				} else {
					$pvmkit_msg .= '<li class="error">Das Paket enth&auml;lt kein Titelbild</li>';
					$is_ok = false;
				}
				
				// import image object
				$image_id = $n->get( 'pvm:image' );
				if ( $image_id != '' ) {
					$image_url = $image_id->get( 'pvm:content' );
					if ( file_exists( $image_url ) ) {
						$this->image = new pvmkit_object_image();
						$image_pathinfo = pathinfo( $image_url );
						$this->image->set_content( $image_pathinfo['filename'] );
						$this->image->set_author_by_name( $image_id->get( 'dcterms:creator' )->label() );
						$this->image_url = $image_url;
					} else {
						$pvmkit_msg .= '<li class="error">Die Bilddatei fehlt (' . $image_url . ')</li>';
						$is_ok = false;
					}
				} else {
					$pvmkit_msg .= '<li class="warn">Das Paket enth&auml;lt kein Bild</li>';
				}
			}
		}
		
		// === import to database ===
		if ( $is_ok ) {
			$db_res = true;
			
			$this->db->query( 'START TRANSACTION' );
			
			// insert package
			$db_res = $db_res && $this->db->insert(
				$this->db->prefix . 'pvmkit_packages', 
				array( 
					'title' => $this->title,
					'status' => 1,
					'author' => $this->author->get_author_id()
				), 
				array( '%s', '%d', '%d' ) 
			);
			$this->id = $this->db->insert_id;
			
			// insert text
			if ( !is_null( $this->text ) ) {
				$db_res = $db_res && $this->text->insert();
				$db_res = $db_res && $this->db->insert(
					$this->db->prefix . 'pvmkit_package_components', 
					array( 
						'package_id' => $this->id,
						'object_id' => $this->text->get_object_id(),
						'type' => 'text'
					), 
					array( '%d', '%d', '%s' ) 
				);
				
				foreach ( $text_id->all('pvm:properties') as $prop ) {
					if ( !$this->text->add_property_by_value( $prop->label() ) ) {
						$pvmkit_msg .= '<li class="warn">&Uuml;bersprungen: ' . $prop->label() . ' (Text)</li>';
					}
				}
			}
			if ( $db_res ) { $pvmkit_msg .= '<li>Text ok</li>'; }
			
			// insert title image
			if ( !is_null( $this->titleimage ) ) {
				$db_res = $db_res && $this->titleimage->insert();
				$db_res = $db_res && $this->db->insert(
					$this->db->prefix . 'pvmkit_package_components', 
					array( 
						'package_id' => $this->id,
						'object_id' => $this->titleimage->get_object_id(),
						'type' => 'titleimage'
					), 
					array( '%d', '%d', '%s' ) 
				);
				
				foreach ( $titleimage_id->all('pvm:properties') as $prop ) {
					if ( !$this->titleimage->add_property_by_value( $prop->label() ) ) {
						$pvmkit_msg .= '<li class="warn">&Uuml;bersprungen: ' . $prop->label() . ' (Titelbild)</li>';
					}
				}
				
				$db_res = $db_res && $this->titleimage->set_image( $this->titleimage_url );
				$pvmkit_msg .= '<li class="info">Titelbild wurde gespeichert</li>';
			}
			if ( $db_res ) { $pvmkit_msg .= '<li>Titelbild ok</li>'; }
			
			// insert image
			if ( !is_null( $this->image ) ) {
				$db_res = $db_res && $this->image->insert();
				$db_res = $db_res && $this->db->insert(
					$this->db->prefix . 'pvmkit_package_components', 
					array( 
						'package_id' => $this->id,
						'object_id' => $this->image->get_object_id(),
						'type' => 'image'
					), 
					array( '%d', '%d', '%s' ) 
				);
				
				foreach ( $image_id->all('pvm:properties') as $prop ) {
					if ( !$this->image->add_property_by_value( $prop->label() ) ) {
						$pvmkit_msg .= '<li class="warn">&Uuml;bersprungen: ' . $prop->label() . ' (Bild)</li>';
					}
				}
				
				$db_res = $db_res && $this->image->set_image( $this->image_url );
				$pvmkit_msg .= '<li class="info">Bild wurde gespeichert</li>';
			}
			if ( $db_res ) { $pvmkit_msg .= '<li>Bild ok</li>'; }
			
			// insert attributes
			
			
			if ( $db_res ) {
				$this->db->query( 'COMMIT' );
				$pvmkit_msg .= '<li class="info">Import abgeschlossen</li>';
			} else {
				$this->db->query( 'ROLLBACK' );
				$pvmkit_msg .= '<li class="error">Der Import wurde abgebrochen</li>';
			}
			
			// === delete temporary files ===
			$this->delete_folder( $pvmkit_file );
			//unlink( $pvmkit_file . '.zip' );
			
			// === move uploaded file to archive ===
			rename( $pvmkit_file . '.zip', PVMKIT_UPLOAD_PATH . 'archive/package_' . $this->id . '.zip' );
		}
		
		return $pvmkit_msg;
	}*/
	
	/*
	 *	update database with values from this package
	 */
	public function update() {
		return $this->db->update(
			$this->db->prefix . 'pvmkit_packages', 
			array( 
				'title' => $this->title,
				'status' => $this->status,
				'publish_date' => $this->publish_date,
				'author' => $this->author->get_author_id()
			),
			array( 
				'id' => $this->id
			),
			array( '%s', '%d', '%s', '%d' ),
			array( '%d' )
		);
	}
	
	/*
	 *	create new package in the database with values from this package
	 */
	public function create( $user_id, $author_id ) {
		
		$this->publish_date = current_time('mysql', 1);
		
		// create new package
		$db_res = $this->db->insert(
			$this->db->prefix . 'pvmkit_packages', 
			array( 
				'viewcount' => 0,
				'title' => '',
				'status' => 1,
				'publish_date' => $this->publish_date,
				'author' => $author_id,
				'user_id' => $user_id
			), 
			array( '%d', '%s', '%d', '%s', '%d' ) 
		);
		
		$this->id = $this->db->insert_id;
		$this->title = '';
		$this->status = 1;
		$this->author = new pvmkit_author();
		$this->author->load_by_author_id( $author_id );
		$this->user_id = $user_id;
		
		// create the text
		$this->text = new pvmkit_object_text();
		$this->text->set( (int) $author_id, '' );
		
		$this->db->insert(
			$this->db->prefix . 'pvmkit_package_components', 
			array( 
				'package_id' => $this->id,
				'object_id' => $this->text->get_object_id(),
				'type' => 'text'
			), 
			array( '%d', '%d', '%s' ) 
		);
		
		$this->titleimage = new pvmkit_object_titleimage();
		$this->image = new pvmkit_object_image();

	}
	
	/*
	 *	returns true if the current user is somehow allowed to edit the package
	 */
	public function can_edit() {
		$can_edit = false;
		
		// is the user the owner of the package and the package is in edit mode?
		if ( ($this->get_user_id() == get_current_user_id()) && ($this->status == 1) ) {
			$can_edit = true;
		} else {
			// is the user an admin (has the right to edit all packages)
			if ( current_user_can( 'pvm_edit_other_packages' ) ) {
				$can_edit = true;
			}
		}
		
		return $can_edit;
	}
	
	/*
	 *	returns true if the current user is somehow allowed to manage the package
	 */
	public function can_manage() {
		$can_manage = false;
		
		// is the user the owner of the package
		if ( ( $this->get_user_id() == get_current_user_id() ) && ( $this->status < 6 ) && ( $this->status > 0 ) ) {
			$can_manage = true;
		} else {
			// is the user an admin (has the right to edit all packages)
			if ( current_user_can( 'pvm_manage_other_packages' ) ) {
				$can_manage = true;
			}
		}
		
		return $can_manage;
	}
	
	function change_author( $author, $can_do = true ) { 
		
		if ( !$this->exists() ) {
			return false;
		}
		
		// check rights
		if ( !$can_do ) {
			$can_do = $this->can_edit();
		}
		
		if ( $can_do ) {
			// handle diffrent data types in $author
			if ( ( gettype( $author ) == 'object' ) && ( get_class( $author ) == 'pvmkit_author' ) ) {
				$author = $author->get_author_id();
			} else {
				$author = (int) $author;
			}
			
			// change the author
			$this->author = new pvmkit_author( $author );
			return $this->db->update( $this->db->prefix . 'pvmkit_packages', array( 'author' => $author ), array( 'id' => $this->id ), array( '%d' ), array( '%d' ) );
		} else {
			return false;
		}
	}
	
	function set_author($author) { 
		$this->author = $author; 
	}
	
	/*
	 *	setters
	 */
	function set_status($status) { $this->status = $status; }
	function set_title($title) { $this->title = $title; }
	function set_publish_date($publish_date) { $this->publish_date = $publish_date; }
	function set_publish_date_now() { $this->publish_date = current_time('mysql', 1); }
}

?>