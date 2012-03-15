<?php
/**
 * Home
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
 * Home Content
 * @since 1.0.0
 */ 
function be_home_content() {
	global $post;
	echo '<div class="left">';
	echo '<p><a href="' . get_permalink() . '">' . get_the_post_thumbnail( $post->ID, 'medium' ) . '</a></p>';
	echo '</div>';
	
	echo '<div class="right">';
	the_content();
	echo '<h4>' . be_gallery_count( false ) . ' photos in this gallery</h4>';
		
	$args = array( 
		'post_parent' => $post->ID, 
		'post_status' => 'inherit', 
		'post_type' => 'attachment',
		'post_mime_type' => 'image',
		'posts_per_page' => 4,
		'order' => 'ASC',
		'orderby' => 'menu_order date',
	);
	$attachments = get_children( $args );
	echo '<div class="summary-listing">';
	foreach( $attachments as $attachment ):
		$image = wp_get_attachment_image_src( $attachment->ID, 'thumbnail' );
		echo '<a href="' . get_permalink( $attachment->ID ) . '"><img src="' . $image[0] . '" /></a>';
	endforeach;
	echo '</div><!-- .summary-listing -->';
	echo '</div>';
}
add_action( 'genesis_post_content', 'be_home_content' );
remove_action( 'genesis_post_content', 'genesis_do_post_content' );



genesis();