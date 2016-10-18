<div class="wrap about-wrap easy-support-videos-wrap">
	<?php Easy_Support_Videos_Admin_Views::page_title_template(); ?>

	<?php do_action( 'easy_support_videos_notifications' ); ?>

	<?php
		// Add a save option nonce field
		wp_nonce_field( 'easy_support_videos_save_option', 'easy_support_videos_nonce_save_option' );
	?>

	<?php do_action( 'easy_support_videos_flex_wrap_before' ); ?>

	<div id="easy-support-videos-flex-wrap" class="easy-support-videos-flex easy-support-videos-flex-2-columns">
		<?php
			/*
			 * Videos
			 */
		?>

		<div id="easy-support-videos" class="easy-support-videos easy-support-videos-col">
			<?php do_action( 'easy_support_videos_videos_before' ); ?>

			<?php
				// If the current user can publish Easy Support Videos
				if ( Easy_Support_Videos_Post_Types::current_user_can( Easy_Support_Videos_Post_Types::$easy_support_videos_post_type, 'publish_posts' ) ) :
			?>
				<div id="easy-support-videos-status-message" class="easy-support-videos-message easy-support-videos-status-message">
					<span id="easy-support-videos-status-message-message" class="easy-support-videos-message easy-support-videos-status-message-message"></span>
					<span id="easy-support-videos-status-message-close-wrap" class="easy-support-videos-status-message-close-wrap">
						<a href="#" id="easy-support-videos-status-message-close" class="easy-support-videos-status-message-close" title="<?php _e( 'Close Message', 'easy-support-videos' ); ?>">
							<span class="dashicons dashicons-dismiss"></span>
						</a>
					</span>
				</div>
			<?php
				endif;

				// If the current user can publish Easy Support Videos
				if ( Easy_Support_Videos_Post_Types::current_user_can( Easy_Support_Videos_Post_Types::$easy_support_videos_post_type, 'publish_posts' ) )
					// Easy Support Videos Insert Videos
					Easy_Support_Videos_Admin_Views::insert_video_template();

				// Easy Support Videos Videos
				Easy_Support_Videos_Admin_Views::videos_template();
			?>

			<?php do_action( 'easy_support_videos_videos_after' ); ?>
		</div>

		<?php
			/*
			 * Sidebar
			 */
			Easy_Support_Videos_Admin_Views::sidebar();
		?>
	</div>

	<?php do_action( 'easy_support_videos_flex_wrap_after' ); ?>
</div>