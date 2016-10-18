<?php
/**
 * Easy Support Videos Uninstall
 *
 * @author Slocum Studio
 * @version 1.0.0
 * @since 1.0.0
 */

// Bail if not actually uninstalling
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit;

/**
 * Includes
 */
include_once 'easy-support-videos.php'; // Easy Support Videos Plugin

/**
 * Uninstall
 */

// Grab the Easy Support Videos options
$easy_support_videos_options = Easy_Support_Videos_Options::get_options();

// Remove Easy Support Videos data upon uninstall
if ( $easy_support_videos_options['uninstall']['data'] ) {
	// Find Easy Support Videos
	$easy_support_videos = get_posts( array(
		'post_type' => Easy_Support_Videos_Post_Types::$easy_support_videos_post_type,
		'numberposts' => -1,
		'post_status' => 'any',
		'fields' => 'ids'
	) );

	if ( $easy_support_videos )
		foreach ( $easy_support_videos as $easy_support_video )
			wp_delete_post( $easy_support_video, true );

	// Delete the Easy Support Videos options
	delete_option( Easy_Support_Videos_Options::$option_name );
}