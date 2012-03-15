<?php
/**
 * Template Name: Sets
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
 * Sets Loop
 * @since 1.0.0
 */
function be_sets_loop() {
	$sets = get_terms( 'category' );
	$count = 0;
	foreach( $sets as $set ) {
		
		$classes = 'one-third';
		if( 0 == $count || 0 == $count % 3 )
			$classes .= ' first';
		
		echo '<div class="' . $classes . '">';
		$image = get_posts( array( 'posts_per_page' => '1', 'cat' => $set->term_id, 'orderby' => 'rand' ) );
		echo '<h2><a href="' . get_term_link( $set, 'category' ) . '">' . $set->name . '<br />' . get_the_post_thumbnail( $image[0]->ID, 'be_archive' ) . '</a></h2>';		
		echo '</div>';	
		$count++;
	}
}
add_action( 'genesis_loop', 'be_sets_loop' );
remove_action( 'genesis_loop', 'genesis_do_loop' );


genesis();