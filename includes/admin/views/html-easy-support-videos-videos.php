<?php
	global $post;

	// Current user can edit Easy Support Videos
	$current_user_can_edit_easy_support_videos = Easy_Support_Videos_Post_Types::current_user_can( Easy_Support_Videos_Post_Types::$easy_support_videos_post_type, 'edit_posts' );
?>

<div id="easy-support-videos-videos" class="easy-support-videos-videos">
	<?php do_action( 'easy_support_videos_inner_before' ); ?>

	<?php
		// If the current user can edit Easy Support Videos
		if ( $current_user_can_edit_easy_support_videos ) {
			// Add an edit nonce field
			wp_nonce_field( 'easy_support_videos_edit', 'easy_support_videos_nonce_edit' );

			// Add a delete nonce field
			wp_nonce_field( 'easy_support_videos_delete', 'easy_support_videos_nonce_delete' );
		}

		// Find Easy Support Videos
		$easy_support_videos = new WP_Query( apply_filters( 'easy_support_videos_videos_args', Easy_Support_Videos_Post_Types::$query_args ) );

		// If we have Easy Support Videos
		if ( $easy_support_videos->have_posts() ) :
			// Loop through Easy Support Videos
			while ( $easy_support_videos->have_posts() ) :
				// Setup the global $post data
				$easy_support_videos->the_post();

				// Grab the post ID
				$post_id = ( int ) get_the_ID();

				do_action( 'easy_support_videos_video_before', $post );
	?>
			<div <?php post_class( ( $current_user_can_edit_easy_support_videos ) ? 'easy-support-video easy-support-video-can-edit easy-support-video-' . $post_id : 'easy-support-video easy-support-video-' . $post_id ); ?> data-post-id="<?php echo $post_id; ?>" <?php do_action( 'easy_support_videos_video_data_attributes', $post ); // TODO: Change this to a function with a filter instead of an action like Conductor? ?>>
				<?php do_action( 'easy_support_videos_video_inner_before', $post ); ?>

				<?php
					// If the current user can edit Easy Support Videos
					if ( $current_user_can_edit_easy_support_videos ) :
				?>
						<span id="easy-support-videos-video-<?php echo $post_id; ?>-spinner" class="spinner easy-support-videos-spinner easy-support-videos-video-spinner easy-support-videos-video-<?php echo $post_id; ?>-spinner" data-type="video" data-post-id="<?php echo $post_id; ?>" data-event="video"></span>
				<?php
					endif;
				?>

				<div class="easy-support-video-content">
					<?php do_action( 'easy_support_videos_video_content_before', $post ); ?>

					<?php the_content(); ?>

					<?php do_action( 'easy_support_videos_video_content_after', $post ); ?>
				</div>

				<div class="easy-support-video-title <?php echo ( $current_user_can_edit_easy_support_videos ) ? 'easy-support-video-can-edit-title' : false; ?>">
					<?php do_action( 'easy_support_videos_video_title_before', $post ); ?>

					<?php
						// If the current user can edit Easy Support Videos
						if ( $current_user_can_edit_easy_support_videos ) :
							// Grab the post ID
							$post_id = ( int ) get_the_ID();
					?>
							<label for="easy-support-videos-video-<?php echo $post_id; ?>-title" class="screen-reader-text"><?php _e( 'Video Title:', 'easy-support-videos' ); ?></label>
							<input id="easy-support-videos-video-<?php echo $post_id; ?>-title" class="regular-text easy-support-videos-input easy-support-videos-video-title easy-support-videos-video-<?php echo $post_id; ?>-title" name="easy-support-videos-video-<?php echo $post_id; ?>-title" type="text" value="<?php echo esc_attr( wp_unslash( get_the_title() ) ); ?>" autocomplete="off" />
							<span id="easy-support-videos-video-<?php echo $post_id; ?>-title-spinner" class="spinner easy-support-videos-spinner easy-support-videos-video-title-spinner easy-support-videos-video-<?php echo $post_id; ?>-title-spinner" data-type="video" data-post-id="<?php echo $post_id; ?>" data-event="edit"></span>
							<a href="#" class="easy-support-videos-video-delete" title="<?php _e( 'Delete Video', 'easy-support-videos' ); ?>">
								<span class="dashicons dashicons-dismiss"></span>
							</a>
							<input id="easy-support-videos-video-<?php echo $post_id; ?>-id" class="easy-support-videos-input easy-support-videos-input-hidden easy-support-videos-video-id easy-support-videos-post-id easy-support-videos-video-<?php echo $post_id; ?>-id" type="hidden" value="<?php echo $post_id; ?>" />
					<?php
						else:
							the_title();
						endif;
					?>

					<?php do_action( 'easy_support_videos_video_title_after', $post ); ?>
				</div>

				<?php do_action( 'easy_support_videos_video_inner_after', $post ); ?>
			</div>
	<?php
				do_action( 'easy_support_videos_video_after', $post );
			endwhile;

			// Reset global $post data (set to null)
			$post = null;

		// Otherwise we don't have and Easy Support Videos
		else:
	?>
			<p id="easy-support-videos-no-videos-message" class="easy-support-videos-message easy-support-videos-no-videos-message">
				<?php
					// If the current user can edit Easy Support Videos
					if ( $current_user_can_edit_easy_support_videos )
						_e( 'Add videos above.', 'easy-support-videos' );
					// Otherwise the current user can view Easy Support Videos
					else
						_e( 'Your support administrator has not added any videos. Please check again later.', 'easy-support-videos' );
				?>
			</p>
	<?php
		endif;
	?>

	<?php do_action( 'easy_support_videos_inner_after' ); ?>
</div>