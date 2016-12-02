<?php
class pvmkit_ws_edit_properties extends pvmkit_ws_module {
	
	protected $id = 'edit_properties';
	protected $layout = 'fullwidth';
	
	protected $is_ok = true;
	protected $package_id = -1;
	protected $package = null;
	
	protected $prop_col = array();
	protected $prop_opt = array();
	protected $prop_mat = array();
	protected $prop_sfc = array();
	protected $prop_sze = array();
	protected $prop_age = array();
	
	protected $props_selected = array();
	
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
		
		// what package are we even talking about?
		if ( isset( $_GET['mtpakid'] ) && is_numeric( $_GET['mtpakid'] ) ) {
			$this->package_id = (int) $_GET['mtpakid'];
		}
		
		// check if the user is allowed to edit this package
		if ( $this->package_id > 0 ) {
			$this->package = new pvmkit_package_editable( $this->package_id );
			
			if ( $this->package->can_edit() ) {
				
				// load and sort the properties
				$properties = $this->db->get_results( 'SELECT property_id, value, type FROM ' . $this->db->prefix . 'pvmkit_properties' );
				foreach ( $properties as $prop ) {
					switch ( $prop->type ) {
						case 'col':
							$this->prop_col[ $prop->value ] = $prop->property_id;
							break;
						case 'opt':
							$this->prop_opt[ $prop->value ] = $prop->property_id;
							break;
						case 'mat':
							$this->prop_mat[ $prop->value ] = $prop->property_id;
							break;
						case 'sfc':
							$this->prop_sfc[ $prop->value ] = $prop->property_id;
							break;
						case 'sze':
							$this->prop_sze[ $prop->value ] = $prop->property_id;
							break;
						case 'age':
							$this->prop_age[ $prop->value ] = $prop->property_id;
							break;
					}
				}
				
				// load the current property set
				$pack_properties = $this->db->get_results( 'SELECT property_id FROM ' . $this->db->prefix . 'pvmkit_property_index WHERE package_id = ' . $this->package_id );
				foreach ( $pack_properties as $prop ) {
					$this->props_selected[ $prop->property_id ] = true;
				}
				
			} else {
				$this->is_ok = false;
			}
		} else {
			$this->is_ok = false;
		}
	}
	
	/*
	 *	returns HTML code for the main area
	 */
	public function get_content() {
		$o = '';
		
		if ( $this->is_ok ) {
			
			// form output
			$o .= '<form method="post" action="' . $this->ws->get_url( 'view_package', 'mtpakid=' . $this->package_id ) . '" class="pvm_ws_boxed">';
			$o .= '<input type="hidden" id="mtpakid" name="mtpakid" value="' . $this->package_id . '" />';
			$o .= '<input type="hidden" id="mtform" name="mtform" value="properties" />';
			
			// color checkbox
			$o .= '<div><span>Farbe</span>';
			foreach ( $this->prop_col as $value => $id ) {
				$o .= '<input type="checkbox" name="mtcol[]" id="mtcol" value="' . $id . '"' . ( isset( $this->props_selected[ $id ] ) ? ' checked' : '' ) . '> ' . $value . '<br>';
			}
			$o .= '</div>';
			// optic checkbox
			$o .= '<div><span>Optik</span>';
			foreach ( $this->prop_opt as $value => $id ) {
				$o .= '<input type="checkbox" name="mtopt[]" id="mtopt" value="' . $id . '"' . ( isset( $this->props_selected[ $id ] ) ? ' checked' : '' ) . '> ' . $value . '<br>';
			}
			$o .= '</div>';
			// material checkbox
			$o .= '<div><span>Material</span>';
			foreach ( $this->prop_mat as $value => $id ) {
				$o .= '<input type="checkbox" name="mtmat[]" id="mtmat" value="' . $id . '"' . ( isset( $this->props_selected[ $id ] ) ? ' checked' : '' ) . '> ' . $value . '<br>';
			}
			$o .= '</div>';
			// surface checkbox
			$o .= '<div><span>Oberfl&auml;che</span>';
			foreach ( $this->prop_sfc as $value => $id ) {
				$o .= '<input type="checkbox" name="mtsfc[]" id="mtsfc" value="' . $id . '"' . ( isset( $this->props_selected[ $id ] ) ? ' checked' : '' ) . '> ' . $value . '<br>';
			}
			$o .= '</div>';
			
			// size radio
			$o .= '<div><span>Gr&ouml;&szlig;e</span>';
			foreach ( $this->prop_sze as $value => $id ) {
				$o .= '<input type="radio" name="mtsze" id="mtsze" value="' . $id . '"' . ( isset( $this->props_selected[ $id ] ) ? ' checked' : '' ) . '> ' . $value . '<br>';
			}
			$o .= '</div>';
			// age radio
			$o .= '<div><span>Alter</span>';
			foreach ( $this->prop_age as $value => $id ) {
				$o .= '<input type="radio" name="mtage" id="mtage" value="' . $id . '"' . ( isset( $this->props_selected[ $id ] ) ? ' checked' : '' ) . '> ' . $value . '<br>';
			}
			$o .= '</div>';

			$o .= '<div class="pvm_bottom_link_row"><a href="' . $this->ws->get_url( 'view_package', 'mtpakid=' . $this->package_id ) . '" class="pvm_cancel">Abbrechen</a><input type="submit" id="mtsubmit" name="mtsubmit" value="Speichern" class="pvm_save" /></div>';
			
			$o .= '</form>';
			
		} else {
			
			// 
			$o .= 'Du kannst dieses Werk nicht bearbeiten!';
			
		}
        
		
		return $o;
	}
	
}
?>