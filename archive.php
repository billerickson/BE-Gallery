<?php
/**
 * Archive
 *
 * @package      BE_Gallery
 * @since        1.0.0
 * @link         https://github.com/billerickson/BE-Gallery
 * @author       Bill Erickson <bill@billerickson.net>
 * @copyright    Copyright (c) 2011, Bill Erickson
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 *
 */

/**
 * Person Intro
 * @since 1.0.0
 *
 * Displays "Photos of .. " name on term archive of people taxonomy
 * @link http://www.billerickson.net/code/default-term-meta/
 *
 * @param string $headline
 * @param object $term
 * @return string $headline
 */
function be_person_intro( $headline, $term ) {
	if( !is_tax( 'people' ) || !empty( $headline ) )
		return $headline;
		
	return 'Photos of ' . $term->name;
}
add_filter( 'genesis_term_meta_headline', 'be_person_intro', 10, 2 );

/**
 * Archive Post Class
 * @since 1.0.0
 *
 * Breaks the posts into three columns
 * @link http://www.billerickson.net/code/grid-loop-using-post-class
 *
 * @param array $classes
 * @return array
 */
function be_archive_post_class( $classes ) {
	$classes[] = 'one-third';
	global $wp_query;
	if( 0 == $wp_query->current_post || 0 == $wp_query->current_post % 3 )
		$classes[] = 'first';
	return $classes;
}
add_filter( 'post_class', 'be_archive_post_class' );

/**
 * Archive Image
 * @since 1.0.0
 *
 */
function be_archive_image() {
	global $post;
	if( 'attachment' == get_post_type( $post->ID ) )
		$image = wp_get_attachment_image_src( $post->ID, 'be_archive' );
	else
		$image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'be_archive' );
		
	echo '<a href="' . get_permalink() . '"><img src="' . $image[0] . '" /></a>';
}
add_action( 'genesis_post_content', 'be_archive_image' );
remove_action( 'genesis_post_content', 'genesis_do_post_content' );

genesis();