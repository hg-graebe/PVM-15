<?php

class pvmkit_property_view {
	
	private $db = null;
	
	/*
	 *	set up database connection
	 */
	public function __construct() {
		global $wpdb;
		$this->db = $wpdb;
	}
	
	/*
	 *	
	 */
	public function render( $package_id ) {
		
		$package_id = (int) $package_id;
		
		if ( $package_id > 0 ) {
			
			$out = '';
			$package = new pvmkit_package( $package_id );
			
			$prop_col = array();
			$prop_opt = array();
			$prop_mat = array();
			$prop_sfc = array();
			$prop_sze = array();
			$prop_age = array();

			// get and sort the properties
			$properties = $this->db->get_results( 'SELECT ' . $this->db->prefix . 'pvmkit_properties.property_id, value, type FROM ' . $this->db->prefix . 'pvmkit_properties,' . $this->db->prefix . 'pvmkit_property_index' . ' WHERE package_id=' . $package_id . ' AND ' . $this->db->prefix . 'pvmkit_properties.property_id=' . $this->db->prefix . 'pvmkit_property_index.property_id');
			foreach ( $properties as $prop ) {
				switch ( $prop->type ) {
					case 'col':
						$prop_col[ $prop->property_id ] = $prop->value;
						break;
					case 'opt':
						$prop_opt[ $prop->property_id ] = $prop->value;
						break;
					case 'mat':
						$prop_mat[ $prop->property_id ] = $prop->value;
						break;
					case 'sfc':
						$prop_sfc[ $prop->property_id ] = $prop->value;
						break;
					case 'sze':
						$prop_sze[ $prop->property_id ] = $prop->value;
						break;
					case 'age':
						$prop_age[ $prop->property_id ] = $prop->value;
						break;
				}
			}
			
			// process URL input
			$url_args = get_query_var( 'filters', '-' );
			$url_args_array = array_filter( explode( '-', $url_args ) );
			$args_set = array();
			foreach ( $url_args_array as $arg_id ) {
				$args_set[ $arg_id ] = true;
			}
			
			/*// output title and author
			$out .= '<h1 class="package_meta_title">' . $package->get_title() . '</h1>';
			$out .= '<h3 class="package_meta_author">' . $package->get_author()->get_profile_link( 'package_meta_author' ) . '</h3>';*/
			
			// output colors
			$prop_col_css =array(
				'gelb' => 'yellow',
				'orange' => 'orange',
				'rot' => 'red',
				'pink' => 'pink',
				'rosa' => 'rose',
				'braun' => 'brown',
				'violett' => 'violet',
				'blau' => 'blue',
				'gr&uuml;n' => 'green',
				'schwarz' => 'black',
				'wei&szlig;' => 'white',
				'grau' => 'gray',
				'silber' => 'silver',
				'gold' => 'gold'
			);
			if ( count( $prop_col ) > 0 ) {
				$out .= '<div class="properties_heading">Farbe</div><ul>';
				$i = 0;
				foreach ( $prop_col as $id => $title ) {
						$out .= '<li class="pvmtheme_color pvmtheme_cl_' . $prop_col_css[ $title ] . '"><span>' . $title . '</span></li>';
					
					   if ( $i == 8 ) $out .= '<div class="color_separator"> </div>';
					   $i++;
					
				  }
				$out .= '</ul>';
			}
			
			// output optics
			if ( count( $prop_opt ) > 0 ) {
				$out .= '<div class="properties_heading">optische Eigenschaften</div><ul class="package_meta_property_entries">';
				foreach ( $prop_opt as $id => $title ) {
						$out .= '<li>' . $title . '</li>';
				  }
				$out .= '</ul>';
			}
			
			// output material
			if ( count( $prop_mat ) > 0 ) {
				$out .= '<div class="properties_heading">Material</div><ul class="package_meta_property_entries">';
				foreach ( $prop_mat as $id => $title ) {
						$out .= '<li>' . $title . '</li>';
				  }
				$out .= '</ul>';
			}
			
			// output surface
			if ( count( $prop_sfc ) > 0 ) {
				$out .= '<div class="properties_heading">Oberfl&auml;che</div><ul class="package_meta_property_entries">';
				foreach ( $prop_sfc as $id => $title ) {
						$out .= '<li>' . $title . '</li>';
				  }
				$out .= '</ul>';
			}
			
			// output size
			if ( count( $prop_sze ) > 0 ) {
				$out .= '<div class="properties_heading">Gr&ouml;&szlig;e</div><ul class="package_meta_property_entries">';
				foreach ( $prop_sze as $id => $title ) {
					$out .= '<li>' . $title . '</li>';
				}
				$out .= '</ul>';
			}
			
			// output age
			if ( count( $prop_age ) > 0 ) {
				$out .= '<div class="properties_heading">Alter</div><ul class="package_meta_property_entries">';
				foreach ( $prop_age as $id => $title ) {
					$out .= '<li>' . $title . '</li>';
				}
				$out .= '</ul>';
			}

			return $out;
			
		} else {
			return false;
		}
	}
}

?>