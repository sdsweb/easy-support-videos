<?php
	// Current user can edit Easy Support Videos
	$current_user_can_edit_easy_support_videos = Easy_Support_Videos_Post_Types::current_user_can( Easy_Support_Videos_Post_Types::$easy_support_videos_post_type, 'edit_posts' );

	// Original current user can edit Easy Support Videos
	$original_current_user_can_edit_easy_support_videos = Easy_Support_Videos_Preview::get_original_capability_for_current_user( 'edit_posts' );
?>

<?php do_action( 'easy_support_videos_preview_before' ); ?>

<div id="easy-support-videos-video-preview-wrap" class="easy-support-videos-video-preview-wrap">
	<?php
		// If the current user can edit Easy Support Videos
		if ( $current_user_can_edit_easy_support_videos ) :
	?>
			<a href="<?php echo esc_url( Easy_Support_Videos_Preview::get_preview_url() ); ?>" id="easy-support-videos-preview-button" class="button button-secondary easy-support-videos-button easy-support-videos-preview-button"><?php _e( 'Preview as Viewer', 'easy-support-videos' ); ?></a>
			<span id="easy-support-videos-video-preview-spinner" class="spinner easy-support-videos-spinner easy-support-videos-preview-spinner"></span>
	<?php
		else:
			// If the current user originally could edit Easy Support Videos
			if ( $original_current_user_can_edit_easy_support_videos ) :
	?>
				<a href="<?php echo esc_url( add_query_arg( array( 'page' => Easy_Support_Videos_Post_Types::get_easy_support_videos_menu_page() ), admin_url( 'admin.php' ) ) ); ?>" id="easy-support-videos-preview-button" class="button button-secondary easy-support-videos-button easy-support-videos-preview-button"><?php _e( 'Edit Videos', 'easy-support-videos' ); ?></a>
	<?php
			endif;
		endif;
	?>
</div>

<?php do_action( 'easy_support_videos_preview_after' ); ?>