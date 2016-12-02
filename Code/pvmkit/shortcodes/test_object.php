<?php
if ( ! defined( 'ABSPATH' ) ) {	exit; }

function pvmkit_test_object_text_shortcode ( $attributes, $content = null ) {
	$out = '';
	$text = new pvmkit_object_text();
	$out .=  '<p>load_from_db(): ' . print_r( $text->load_from_db(3), true ) . '</p>';
	$out .= $text;
	$out .= '<p>set_content(): ' . print_r( $text->set_content( $text->get_content() . ' ' . $text->get_content() ), true ) . '</p>';
	$out .= '<p>update(): ' . print_r( $text->update(), true ) . '</p>';
	$out .= $text;
	$out .= '<p>insert(): ' . print_r( $text->insert(), true ) . '</p>';
	$out .= '<p>add_property(): ' . print_r( $text->add_property(5), true ) . '</p>';
	$out .= '<p>remove_property(): ' . print_r( $text->remove_property(5), true ) . '</p>';
	return $out;
}
add_shortcode( 'pvmkit_test_object_text', 'pvmkit_test_object_text_shortcode' );

function pvmkit_test_object_titleimage_shortcode ( $attributes, $content = null ) {
	$out = '';
	$image = new pvmkit_object_titleimage();
	$out .=  '<p>load_from_db(): ' . print_r( $image->load_from_db(19), true ) . '</p>';
	$imagename = pathinfo( 'DSC123.jpg' );
	$out .= '<p>set_content(): ' . print_r( $image->set_content( $imagename['filename'] ), true ) . '</p>';
	$out .= $image;
	$out .= '<p>update(): ' . print_r( $image->update(), true ) . '</p>';
	$out .= '<p>' . ABSPATH . 'wp-content/plugins/pvmkit/DSC123.jpg : ' . $image->set_image( ABSPATH . 'wp-content/plugins/pvmkit/DSC123.jpg' ) . '</p>';
	return $out;
}
add_shortcode( 'pvmkit_test_object_titleimage', 'pvmkit_test_object_titleimage_shortcode' );
?>