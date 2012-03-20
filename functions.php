<?php
/**
 * Functions
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
 * Theme Setup
 * @since 1.0.0
 *
 * This setup function attaches all of the site-wide functions 
 * to the correct hooks and filters. All the functions themselves
 * are defined below this setup function.
 *
 */

add_action('genesis_setup','child_theme_setup', 15);
function child_theme_setup() {

	// ** Backend **	

	// Image Sizes
	add_image_size( 'be_thumbnail', 210, 210 );
	add_image_size( 'be_archive', 272, 181, true );
	
	// Menus
	add_theme_support( 'genesis-menus', array( 'primary' => 'Primary Navigation Menu' ) );
	
	// Sidebars
	unregister_sidebar( 'sidebar' );
	unregister_sidebar('sidebar-alt');

	// Remove Unused Page Layouts
	genesis_unregister_layout( 'content-sidebar' );	
	genesis_unregister_layout( 'sidebar-content' );
	genesis_unregister_layout( 'content-sidebar-sidebar' );
	genesis_unregister_layout( 'sidebar-sidebar-content' );
	genesis_unregister_layout( 'sidebar-content-sidebar' );
	remove_theme_support( 'genesis-inpost-layouts' );
	remove_theme_support( 'genesis-archive-layouts' );
	add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );

	// Remove Unused Theme Settings
	add_action( 'genesis_theme_settings_metaboxes', 'be_remove_metaboxes' );

	// Remove Unused User Settings
	add_filter( 'user_contactmethods', 'be_contactmethods' );
	remove_action( 'show_user_profile', 'genesis_user_options_fields' );
	remove_action( 'edit_user_profile', 'genesis_user_options_fields' );
	remove_action( 'show_user_profile', 'genesis_user_archive_fields' );
	remove_action( 'edit_user_profile', 'genesis_user_archive_fields' );
	remove_action( 'show_user_profile', 'genesis_user_seo_fields' );
	remove_action( 'edit_user_profile', 'genesis_user_seo_fields' );
	remove_action( 'show_user_profile', 'genesis_user_layout_fields' );
	remove_action( 'edit_user_profile', 'genesis_user_layout_fields' );

	// Don't update theme
	add_filter( 'http_request_args', 'be_dont_update_theme', 5, 2 );
	
	// General Functions
	include_once( CHILD_DIR . '/lib/gallery-functions.php' );
	
	// Grab Geo EXIF Data
	add_filter( 'wp_read_image_metadata', 'be_add_geo_exif', '', 3 );

	// ** Frontend **		

	// Remove Edit link
	add_filter( 'genesis_edit_post_link', '__return_false' );
	
	// Viewport Meta Tag	
	add_action( 'genesis_meta', 'be_viewport_meta_tag' );
	
	// Body Class
	add_filter( 'body_class', 'be_body_class' );
	
	// Post Info
	add_filter( 'genesis_post_info', 'be_post_info' );
	
	// Post Meta
	remove_action( 'genesis_after_post_content', 'genesis_post_meta' );
	
	// Archive Query
	add_filter( 'pre_get_posts', 'be_archive_query' );

}

// ** Backend Functions ** //

/**
 * Remove Metaboxes
 * @since 1.0.0
 *
 * This removes unused or unneeded metaboxes from Genesis > Theme Settings. 
 * See /genesis/lib/admin/theme-settings for all metaboxes.
 *
 * @author Bill Erickson
 * @link http://www.billerickson.net/code/remove-metaboxes-from-genesis-theme-settings/
 */
 
function be_remove_metaboxes( $_genesis_theme_settings_pagehook ) {
	remove_meta_box( 'genesis-theme-settings-header', $_genesis_theme_settings_pagehook, 'main' );
	remove_meta_box( 'genesis-theme-settings-nav', $_genesis_theme_settings_pagehook, 'main' );
	remove_meta_box( 'genesis-theme-settings-breadcrumb', $_genesis_theme_settings_pagehook, 'main' );
	remove_meta_box( 'genesis-theme-settings-blogpage', $_genesis_theme_settings_pagehook, 'main' );
}

/**
 * Customize Contact Methods
 * @since 1.0.0
 *
 * @author Bill Erickson
 * @link http://sillybean.net/2010/01/creating-a-user-directory-part-1-changing-user-contact-fields/
 *
 * @param array $contactmethods
 * @return array
 */
function be_contactmethods( $contactmethods ) {
	unset( $contactmethods['aim'] );
	unset( $contactmethods['yim'] );
	unset( $contactmethods['jabber'] );

	return $contactmethods;
}

/**
 * Don't Update Theme
 * @since 1.0.0
 *
 * If there is a theme in the repo with the same name, 
 * this prevents WP from prompting an update.
 *
 * @author Mark Jaquith
 * @link http://markjaquith.wordpress.com/2009/12/14/excluding-your-plugin-or-theme-from-update-checks/
 *
 * @param array $r, request arguments
 * @param string $url, request url
 * @return array request arguments
 */

function be_dont_update_theme( $r, $url ) {
	if ( 0 !== strpos( $url, 'http://api.wordpress.org/themes/update-check' ) )
		return $r; // Not a theme update request. Bail immediately.
	$themes = unserialize( $r['body']['themes'] );
	unset( $themes[ get_option( 'template' ) ] );
	unset( $themes[ get_option( 'stylesheet' ) ] );
	$r['body']['themes'] = serialize( $themes );
	return $r;
}

/**	
 * Add image geo EXIF metadata to WordPress
 *
 * @since 1.0.2
 * @link http://www.kristarella.com/2009/04/add-image-exif-metadata-to-wordpress/ 
 * @return array geo meta data
 */
function be_add_geo_exif( $meta, $file, $sourceImageType ) {
	$exif = @exif_read_data( $file );

	if ( !empty( $exif['GPSLatitude'] ) )
		$meta['latitude'] = $exif['GPSLatitude'] ;

	if ( !empty( $exif['GPSLatitudeRef'] ) )
		$meta['latitude_ref'] = trim( $exif['GPSLatitudeRef'] );

	if ( !empty( $exif['GPSLongitude'] ) )
		$meta['longitude'] = $exif['GPSLongitude'] ;

	if ( !empty( $exif['GPSLongitudeRef'] ) )
		$meta['longitude_ref'] = trim( $exif['GPSLongitudeRef'] );

	return $meta;
}

// ** Frontend Functions ** //

/**
 * Viewport Meta Tag
 * @since 1.0.0
 *
 * This makes the page load correctly on mobile devices
 */
function be_viewport_meta_tag() {
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0"/>';
}


/**
 * Body Class
 * @since 1.0.0
 *
 * 'no-comments' body class used to hide comment area
 *
 * @param array $classes
 * @return array
 */
function be_body_class( $classes ) {
	global $post;
	if( is_singular() && 0 == $post->comment_count )
		$classes[] = 'no-comments';
		
	return $classes;
}

/** 
 * Post Info
 * @since 1.0.0
 *
 * @param string $post_info
 * @return string
 */
function be_post_info( $post_info ) {
	$post_info = '[post_date] &bull; ' . be_gallery_count();
	return $post_info;
}

/**
 * Archive Query
 * @since 1.0.0
 *
 * Allows taxonomy page to list attachments, and sets all archives to 27 per page
 * @link http://www.billerickson.net/customize-the-wordpress-query/
 *
 * @param object $query
 */
function be_archive_query( $query ) {
	if( $query->is_main_query() && $query->is_tax( 'people' ) ) {
	
		$query->set( 'post_type', 'attachment' );
		$query->set( 'post_status', 'inherit' );
	}
	
	if( $query->is_main_query() && $query->is_archive() ) {
		$query->set( 'posts_per_page', 27 );	
	}	
}