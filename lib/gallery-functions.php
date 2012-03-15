<?php
/**
 * General Functions
 *
 * @package      BE_Gallery
 * @since        1.0.0
 * @link         https://github.com/billerickson/BE-Genesis-Child
 * @author       Bill Erickson <bill@billerickson.net>
 * @copyright    Copyright (c) 2011, Bill Erickson
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 *
 */

/**
 * Gallery Count
 * @since 1.0.0
 *
 * Displays number of images, can be linked to gallery (default) or just number
 *
 * @param bool $full_markup
 * @return bool $count
 */
function be_gallery_count( $full_markup = true ) {
	global $post;
	$id = $post->ID;
	if( 'attachment' == get_post_type( $id ) )
		$id = $post->post_parent;
	
	$args = array( 
		'post_parent' => $id, 
		'post_status' => 'inherit', 
		'post_type' => 'attachment',
		'post_mime_type' => 'image',
		'posts_per_page' => -1,
		'order' => 'ASC',
		'orderby' => 'menu_order',
	);
	$attachments = get_children( $args );
	$count = count( $attachments );
	if( $full_markup )
		return '<a href="' . get_permalink( $id ) . '">' . $count . ' Photos</a>';
	else
		return $count;
}

/**
 * Get Adjacent Image Link
 * @since 1.0.0
 *
 * Same as adjacent_image_link(), except returns result
 *
 * @param bool $prev
 * @param string $size
 * @param string $text
 * @return string $link
 */
function be_get_adjacent_image_link($prev = true, $size = 'thumbnail', $text = false) {
	global $post;
	$post = get_post($post);
	$attachments = array_values(get_children( array('post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID') ));

	foreach ( $attachments as $k => $attachment )
		if ( $attachment->ID == $post->ID )
			break;

	$k = $prev ? $k - 1 : $k + 1;

	if ( isset($attachments[$k]) )
		return wp_get_attachment_link($attachments[$k]->ID, $size, true, false, $text);

}

/**
 * EXIF Data
 * @since 1.0.0
 *
 * @link http://wpengineer.com/2103/the-wordpress-exif-meta-datas/
 */
function be_grab_exif_data_from_wp() {
	?>
<ul id="image-meta-data">
	<?php $meta = wp_get_attachment_metadata( get_the_id() ); $image_meta = $meta['image_meta']; ?>
	<?php $total = 0; foreach ($image_meta as $value) { $total = $value + $total; } if ($total == 0) { ?>

	<?php } else { ?>

	<?php if ( !empty( $image_meta['camera'] ) ) { ?>
	<li class="camera-used">
		<strong><?php _e( 'Camera' ) ?>:</strong>
		<span><?php echo $image_meta['camera']; ?></span>
	</li><!-- .camera-used -->
	<?php } ?>

	<?php if ( !empty( $image_meta['shutter_speed'] ) ) { ?>
	<li class="shutter-speed">
		<strong><?php _e( 'Shutter Speed' ) ?>:</strong>
		<span><?php echo "1 / ". ( round( 1/$image_meta['shutter_speed'] ) ); ?></span>
	</li><!-- .shutter-speed -->
	<?php } ?>

	<?php if ( !empty( $image_meta['aperture'] ) ) { ?>
	<li class="aperture">
		<strong><?php _e( 'Aperture' ) ?>:</strong>
		<span><?php echo $image_meta['aperture']; ?></span>
	</li><!-- .aperture -->
	<?php } ?>

	<?php if ( !empty( $image_meta['focal_length'] ) ) { ?>
	<li class="focal-length">
		<strong><?php _e( 'Focal Length' ) ?>:</strong>
		<span><?php echo $image_meta['focal_length']; ?> mm</span>
	</li><!-- .focal-length -->
	<?php } ?>

	<?php if ( !empty( $image_meta['iso'] ) ) { ?>
	<li class="iso-speed">
		<strong><?php _e( 'ISO Speed' ) ?>:</strong>
		<span><?php echo $image_meta['iso']; ?></span>
	</li><!-- .iso-speed -->
	<?php } ?>

	<?php if ( !empty($image_meta['created_timestamp'] ) ) { ?>
	<li class="time-stamp">
	<strong><?php _e( 'Timestamp' ) ?>:</strong>
		<span><?php echo date( 'F jS, Y', $image_meta['created_timestamp'] ); ?></span>
	</li><!-- .time-stamp -->
	<?php } } ?>

</ul><!-- #image-meta-data -->
	<?php
}

/**
 * Get the specific template name for a page.
 * @since 1.0.0
 *
 * Already in WP trunk as get_page_template_slug(), will be in 3.4
 *
 * @param int $id The page ID to check. Defaults to the current post, when used in the loop.
 * @return string|bool Page template filename. Returns an empty string when the default page template
 * 	is in use. Returns false if the post is not a page.
 */
function be_get_page_template_slug( $post_id = null ) {
	$post = get_post( $post_id );
	if ( 'page' != $post->post_type )
		return false;
	$template = get_post_meta( $post->ID, '_wp_page_template', true );
	if ( ! $template || 'default' == $template )
		return '';
	return $template;
}