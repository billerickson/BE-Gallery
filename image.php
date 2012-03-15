<?php
/**
 * Single Image
 *
 * @package      BE_Gallery
 * @since        1.0.0
 * @link         https://github.com/billerickson/BE-Gallery
 * @author       Bill Erickson <bill@billerickson.net>
 * @copyright    Copyright (c) 2011, Bill Erickson
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 *
 */

// Remove Title
remove_action( 'genesis_post_title', 'genesis_do_post_title' );

/**
 * Add prev and next class to navigation
 * @since 1.0.0
 *
 * Seems a little hacky to me, but couldn't find a better way 
 * to target the jQuery keyboard navigation.
 *
 * @param string $output
 * @param int $id Optional. Post ID.
 * @param string $size Optional, default is 'thumbnail'. Size of image, either array or string.
 * @param bool $permalink Optional, default is false. Whether to add permalink to image.
 * @param bool $icon Optional, default is false. Whether to include icon.
 * @param string $text Optional, default is false. If string, then will be link text.
 * @return string modified output. 
 */
function be_make_prev_next( $output, $id, $size, $permalink, $icon, $text ) {
	if( '&laquo; Previous Photo' == $text )
		$output = '<span class="prev">' . $output . '</span>';
	
	if( 'Next Photo &raquo;' == $text )
		$output = '<span class="next">' . $output . '</span>';
		
	return $output;
}
add_filter( 'wp_get_attachment_link', 'be_make_prev_next', 10, 6 );

/**
 * Navigate using arrow keys
 * @since 1.0.0
 *
 */
function be_navigation_script() {
	wp_enqueue_script( 'jquery-touchwipe', get_stylesheet_directory_uri() . '/lib/jquery.touchwipe.min.js', array( 'jquery' ) );
	wp_enqueue_script( 'be-navigation', get_stylesheet_directory_uri() . '/lib/navigation.js', array( 'jquery' ) );
}
add_action( 'wp_enqueue_scripts', 'be_navigation_script' );

/**
 * Post Info
 * @since 1.0.0
 *
 * @param string $post_info
 * @return string
 */
function be_single_image_post_info( $post_info ) {
	global $post;
	$post_info = '<div class="one-half first"><a href="' . get_permalink( $post->post_parent ) . '">&laquo; Back to ' . get_the_title( $post->post_parent ) . ' (' . be_gallery_count( false ) . ' Photos)</a></div>';
	$post_info .= '<div class="one-half">' . be_get_adjacent_image_link( true, false, '&laquo; Previous Photo' ) . ' | ' . be_get_adjacent_image_link( false, false, 'Next Photo &raquo;' ) . '</div>';
	return $post_info;
}
add_filter( 'genesis_post_info', 'be_single_image_post_info' );

/** 
 * Image
 * @since 1.0.0
 */
function be_single_image() {
	global $post;
	$image = wp_get_attachment_image_src( $post->ID, 'large' );
	echo '<p id="image"><img src="' . $image[0] . '" /></p>';
	
	
	// Logged in users can download images of any size
	if( is_user_logged_in() ) {
		
		$small = wp_get_attachment_image_src( $post->ID, 'be_thumbnail' );
		$medium = wp_get_attachment_image_src( $post->ID, 'medium' );
		$large = $image;
		$original = wp_get_attachment_image_src( $post->ID, 'full' );
		echo '<p>Download: <a href="' . $small[0] .'">Small ('. $small[1] . 'x' . $small[2] . ')</a> | <a href="' . $medium[0] . '">Medium (' . $medium[1] . 'x' . $medium[2] . ')</a> | <a href="' . $large[0] . '">Large (' . $large[1] . 'x' . $large[2] . ')</a> | <a href="' . $original[0] . '">Original (' . $original[1] . 'x' . $original[2] . ')</a></p>';
	
	}
}
add_action( 'genesis_post_content', 'be_single_image', 5 );


/**
 * Post Meta
 * @since 1.0.0
 *
 * @param string $post_meta
 * @return string
 */
function be_single_image_post_meta( $post_meta ) {
	$post_meta = '<div class="one-half first">' . be_get_adjacent_image_link( true, 'thumbnail' ) . '</div><div class="one-half">' . be_get_adjacent_image_link( false, 'thumbnail' ) . '</div>';
	return $post_meta;
}
add_filter( 'genesis_post_meta', 'be_single_image_post_meta' );

// Add back post meta (removed in functions.php)
add_action( 'genesis_after_post_content', 'genesis_post_meta' );

/**
 * Lower Wrapper Open
 * @since 1.0.0
 */
function be_lower_wrapper_open() {
	echo '<div class="lower"><div class="left">';
}
add_action( 'genesis_after_post', 'be_lower_wrapper_open', 1 );

/**
 * Lower Wrapper Close
 * @since 1.0.0
 */
function be_lower_wrapper_close() {
	echo '</div><!-- .left --><div class="right">';
	echo '<h3>Information</h3>';
	global $post;
	echo '<p>Title: ' . get_the_title( $post->ID ) . '</p>';
	echo '<p>Album: <a href="' . get_permalink( $post->post_parent ) . '">' . get_the_title( $post->post_parent ) . '</a></p>';
	echo '<p>License: <a href="http://creativecommons.org/licenses/by-nc/2.0/">Creative Commons, Attribution Non-Commercial</a></p>';
	echo '<p>EXIF Data:</p>';
	be_grab_exif_data_from_wp();
	echo '<br /><br /><h3>People</h3>';
	echo get_the_term_list($post->ID, 'people', '<p>People Tagged: ', ', ', '</p><hr />'); 
	echo '<div id="tagthis" class="tagthis"></div>';
	echo '</div><!-- .right --></div><!-- .lower -->';

}
add_action( 'genesis_after_post', 'be_lower_wrapper_close', 60 );

genesis();