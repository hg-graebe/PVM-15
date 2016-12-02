<?php
/**
* Description: Widget to display the metadata for a single package
* Version: 0.1
* Author: Alexander Lieder
*/

require_once( ABSPATH . '/wp-content/plugins/pvmkit/classes/pvmkit_filtered_package_list.php' );
require_once( ABSPATH . '/wp-content/plugins/pvmkit/classes/pvmkit_author.php' );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class pvmkit_widget_package_meta extends WP_Widget {
	
	function __construct() {
		parent::__construct( 'pvmkit_widget_package_meta', 'Werkmeta', array( 'description' => 'Single package meta', ) );
	}

	// render the widget
	public function widget( $args, $instance ) {
		global $wpdb;
		
		echo $args['before_widget'];
      
		$package = new pvmkit_package();
		$author = new pvmkit_author();
		$result = $package->load_by_id( $_GET['pkg_id'] );
		if($result) {
			$title = $package->get_title();

			$author->load_by_full_name($package->get_author());
			$author_name = '<a class="package_meta_author" href="' . get_home_url() . '/index.php/single_user/?author_id=' . $author->get_author_id() . '">' . $author->get_full_name() . '</a>';
		}
						
        // title
		if ( ! empty( $instance['title'] ) ) {
			echo '<h3 class="widgettitle">' . $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'] . '</h3>';
		}
		
		$prop_col = array();
		$prop_opt = array();
		$prop_mat = array();
		$prop_sfc = array();
		$prop_sze = array();
		$prop_age = array();

        // get and sort the properties
        $properties = $wpdb->get_results( 'SELECT ' . $wpdb->prefix . 'pvmkit_properties.property_id, value, type FROM ' . $wpdb->prefix . 'pvmkit_properties,' . $wpdb->prefix . 'pvmkit_property_index' . ' WHERE package_id=' . $_GET['pkg_id'] . ' AND ' . $wpdb->prefix . 'pvmkit_properties.property_id=' . $wpdb->prefix . 'pvmkit_property_index.property_id');
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
		
		// output title and author
		echo '<h1 class="package_meta_title">' . $title . '</h1>';
		echo '<h3 class="package_meta_author">' . $author_name . '</h3>';
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
		echo '<div class="properties_heading">Farbe</div><ul>';
		  $i = 1;
        foreach ( $prop_col as $id => $title ) {
				echo '<li class="pvmtheme_color pvmtheme_cl_' . $prop_col_css[ $title ] . '"><a href="">' . $title . '</a></li>';
			
			   if ( $i == 6 ) echo '<div class="color_separator"> </div>';
			   $i++;
			
		  }
		echo '</ul><br />';
		
        // output optics
		echo '<div class="properties_heading">optische Eigenschaften</div><ul class="package_meta_property_entries">';
        foreach ( $prop_opt as $id => $title ) {
				echo '<li><a href="">' . $title . '</a></li>';
		  }
		echo '</ul>';
		
        // output material
		echo '<div class="properties_heading">Material</div><ul class="package_meta_property_entries">';
        foreach ( $prop_mat as $id => $title ) {
				echo '<li><a href="">' . $title . '</a></li>';
		  }
		echo '</ul>';
		
        // output surface
		echo '<div class="properties_heading">Oberfl&auml;che</div><ul class="package_meta_property_entries">';
        foreach ( $prop_sfc as $id => $title ) {
				echo '<li><a href="">' . $title . '</a></li>';
		  }
		echo '</ul>';
		
        // output size
		echo '<div class="properties_heading">Gr&ouml;&szlig;e</div><ul class="package_meta_property_entries">';
        foreach ( $prop_sze as $id => $title ) {
				echo '<li><a href="">' . $title . '</a></li>';
		  }
		echo '</ul>';
		
        // output age
		echo '<div class="properties_heading">Alter</div><ul class="package_meta_property_entries">';
        foreach ( $prop_age as $id => $title ) {
				echo '<li><a href="">' . $title . '</a></li>';
		  }
		echo '</ul>';
        
		echo $args['after_widget'];
	}

	// settings UI
	public function form( $instance ) {
        $title = ( ! empty( $instance['title'] ) ) ? strip_tags( $instance['title'] ) : '';
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php 
	}

	// Einstellungen speichern
	public function update( $new_instance, $old_instance ) {
		$instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}
}
    
// register widget
function pvmkit_register_widget_package_meta() {
	register_widget( 'pvmkit_widget_package_meta' );
}
add_action( 'widgets_init', 'pvmkit_register_widget_package_meta' );

?>