<?php
	global $post;

	// Current user can edit Easy Support Videos
	$current_user_can_edit_easy_support_videos = Easy_Support_Videos_Post_Types::current_user_can( Easy_Support_Videos_Post_Types::$easy_support_videos_post_type, 'edit_posts' );
?>

<div id="easy-support-videos-videos" class="easy-support-videos-videos">
	<?php do_action( 'easy_support_videos_inner_before', $easy_support_videos, $current_user_can_edit_easy_support_videos ); ?>

	<?php
		// If the current user can edit Easy Support Videos
		if ( $current_user_can_edit_easy_support_videos ) {
			// Add an edit nonce field
			wp_nonce_field( 'easy_support_videos_edit', 'easy_support_videos_nonce_edit' );

			// Add a delete nonce field
			wp_nonce_field( 'easy_support_videos_delete', 'easy_support_videos_nonce_delete' );
		}

		// If we have Easy Support Videos
		if ( $easy_support_videos->have_posts() ) :
			// Loop through Easy Support Videos
			while ( $easy_support_videos->have_posts() ) :
				// Setup the global $post data
				$easy_support_videos->the_post();

				// Grab the post ID
				$post_id = get_the_ID();

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
							$post_id = get_the_ID();
					?>
							<label for="easy-support-videos-video-<?php echo $post_id; ?>-title" class="screen-reader-text"><?php _e( 'Video Title:', 'easy-support-videos' ); ?></label>
							<input id="easy-support-videos-video-<?php echo $post_id; ?>-title" class="regular-text easy-support-videos-input easy-support-videos-video-title easy-support-videos-video-<?php echo $post_id; ?>-title" name="easy-support-videos-video-<?php echo $post_id; ?>-title" type="text" value="<?php echo esc_attr( wp_unslash( get_the_title() ) ); ?>" placeholder="<?php echo esc_attr( wp_unslash( get_the_title() ) ); ?>" autocomplete="off" />
							<span id="easy-support-videos-video-<?php echo $post_id; ?>-title-spinner" class="spinner easy-support-videos-spinner easy-support-videos-video-title-spinner easy-support-videos-video-<?php echo $post_id; ?>-title-spinner" data-type="video" data-post-id="<?php echo $post_id; ?>" data-event="edit"></span>
							<a href="#" class="easy-support-videos-video-delete" title="<?php _e( 'Delete Video', 'easy-support-videos' ); ?>">
								<span class="dashicons dashicons-trash"></span>
							</a>
							<input id="easy-support-videos-video-<?php echo $post_id; ?>-id" class="easy-support-videos-input easy-support-videos-input-hidden easy-support-videos-video-id easy-support-videos-post-id easy-support-videos-video-<?php echo $post_id; ?>-id" type="hidden" value="<?php echo $post_id; ?>" />
					<?php
						else:
							the_title();
						endif;
					?>

					<?php do_action( 'easy_support_videos_video_title_after', $post ); ?>
				</div>

				<div class="easy-support-video-excerpt <?php echo ( $current_user_can_edit_easy_support_videos ) ? 'easy-support-video-can-edit-excerpt' : false; ?>">
					<?php do_action( 'easy_support_videos_video_excerpt_before', $post ); ?>

					<?php
						// If the current user can edit Easy Support Videos
						if ( $current_user_can_edit_easy_support_videos ) :
							// Grab the post ID
							$post_id = get_the_ID();

						// Grab the post excerpt
						$post_excerpt = wp_unslash( get_post_field( 'post_excerpt', $post ) );
					?>
							<label for="easy-support-videos-video-<?php echo $post_id; ?>-excerpt" class="screen-reader-text"><?php _e( 'Video Excerpt:', 'easy-support-videos' ); ?></label>
							<textarea id="easy-support-videos-video-<?php echo $post_id; ?>-excerpt" class="large-text easy-support-videos-input easy-support-videos-textarea easy-support-videos-video-excerpt easy-support-videos-video-<?php echo $post_id; ?>-excerpt" name="easy-support-videos-video-<?php echo $post_id; ?>-excerpt" placeholder="<?php esc_attr_e( 'Enter a video description (optional)', 'easy-support-videos' ); ?>" maxlength="<?php echo esc_attr( Easy_Support_Videos_Post_Types::$maximum_post_excerpt_length ); ?>"><?php echo esc_html( $post_excerpt ); ?></textarea>
							<div id="easy-support-videos-video-<?php echo $post_id; ?>-excerpt-character-count" class="easy-support-videos-video-excerpt-character-count easy-support-videos-video-<?php echo $post_id; ?>-excerpt-character-count">
								<span id="easy-support-videos-video-<?php echo $post_id; ?>-excerpt-current-character-count" class="easy-support-videos-video-excerpt-current-character-count easy-support-videos-video-<?php echo $post_id; ?>-excerpt-current-character-count"><?php echo strlen( $post_excerpt ); ?></span>
								/
								<span id="easy-support-videos-video-<?php echo $post_id; ?>-excerpt-character-maximum" class="easy-support-videos-video-excerpt-character-maximum easy-support-videos-video-<?php echo $post_id; ?>-excerpt-character-maximum"><?php echo Easy_Support_Videos_Post_Types::$maximum_post_excerpt_length; ?></span>
							</div>
					<?php
						else:
							// Remove the "wp_trim_excerpt" callback from the "get_the_excerpt" hook (WordPres default)
							remove_filter( 'get_the_excerpt', 'wp_trim_excerpt' );

							the_excerpt();

							// Add the "wp_trim_excerpt" callback to the "get_the_excerpt" hook (WordPres default)
							add_filter( 'get_the_excerpt', 'wp_trim_excerpt', 10, 2 );
						endif;
					?>

					<?php do_action( 'easy_support_videos_video_excerpt_after', $post ); ?>
				</div>

				<?php do_action( 'easy_support_videos_video_inner_after', $post ); ?>
			</div>
	<?php
				do_action( 'easy_support_videos_video_after', $post );
			endwhile;

			// Reset global post data
			wp_reset_postdata();

		// Otherwise we don't have Easy Support Videos
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

	<?php do_action( 'easy_support_videos_inner_after', $easy_support_videos, $current_user_can_edit_easy_support_videos ); ?>
</div>