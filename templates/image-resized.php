<?php
/**
 * Resized image stream template.
 */

// See if the server supports what we want to do.
$image_editor_args = array( 'methods' => array( 'resize' ) );

// If so, onward!
if( wp_image_editor_supports( $image_editor_args ) ) {

	$img = wp_get_image_editor( $_GET['img'] );

	if ( ! is_wp_error( $img ) ) {

		$img->resize( $_GET['width'], $_GET['height'], true );
		$img->set_quality( 100 );
		$img->stream();

	}

}
?>