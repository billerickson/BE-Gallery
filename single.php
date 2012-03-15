<?php
/**
 * Single Gallery
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
 * Single Gallery Content
 * @since 1.0.0
 */
function be_single_gallery_content() {
	global $post;
	$args = array( 
		'post_parent' => $post->ID,
		'post_type' => 'attachment',
		'post_mime_type' => 'image',
		'post_status' => 'inherit',
		'posts_per_page' => '-1',
		'order' => 'ASC',
		'orderby' => 'menu_order date',
	);
	
	$images = new WP_Query( $args );
	if( !$images->have_posts() )
		return;
	echo '<div class="gallery-listing">';
	while( $images->have_posts() ): $images->the_post(); global $post;
	
		// Echo image url
		$image = wp_get_attachment_image_src( $post->ID, 'be_thumbnail' );
		$classes = 'gallery-item';
		echo '<div class="' . $classes . '"><a href="' . get_permalink() . '"><img src="' . $image[0] . '" /></a></div>';
	
	endwhile; wp_reset_query();
	echo '</div><!-- .gallery-listing -->';
	
}
add_action( 'genesis_post_content', 'be_single_gallery_content', 20 );

genesis();