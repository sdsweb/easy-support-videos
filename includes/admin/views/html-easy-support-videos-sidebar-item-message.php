<?php
	// Grab the Easy Support Videos options
	$easy_support_videos_options = Easy_Support_Videos_Options::get_options();
?>

<?php do_action( 'easy_support_videos_sidebar_item_message_before' ); ?>

<div class="easy-support-videos-sidebar-item easy-support-videos-sidebar-item-message <?php echo ( ! Easy_Support_Videos_Post_Types::current_user_can( Easy_Support_Videos_Post_Types::$easy_support_videos_post_type, 'publish_posts' ) && empty( $easy_support_videos_options['sidebar']['message'] ) ) ? 'empty' : ( ( ! Easy_Support_Videos_Post_Types::current_user_can( Easy_Support_Videos_Post_Types::$easy_support_videos_post_type, 'publish_posts' ) ) ? 'not-empty' : false ) ?>">
	<?php do_action( 'easy_support_videos_sidebar_item_message_inner_before' ); ?>

	<?php
		// If the current user can publish Easy Support Videos
		if ( Easy_Support_Videos_Post_Types::current_user_can( Easy_Support_Videos_Post_Types::$easy_support_videos_post_type, 'publish_posts' ) ) :
	?>
			<div class="easy-support-videos-sidebar-item-content">
				<?php do_action( 'easy_support_videos_sidebar_item_message_content_before' ); ?>

				<input id="easy-support-videos-sidebar-message-option-name" class="easy-support-videos-input easy-support-videos-hidden-input easy-support-videos-sidebar-message-option-name" name="easy-support-videos-sidebar-message-option-name" type="hidden" value="message" />
				<input id="easy-support-videos-sidebar-message-option-group" class="easy-support-videos-input easy-support-videos-hidden-input easy-support-videos-sidebar-message-option-group" name="easy-support-videos-sidebar-message-option-group" type="hidden" value="sidebar" />
				<label for="easy-support-videos-option-sidebar-message" class="screen-reader-text"><?php _e( 'Edit Sidebar Message:', 'easy-support-videos' ); ?></label>
				<?php echo apply_filters( 'easy_support_videos_sidebar_item_message_textarea_markup', '<textarea id="easy-support-videos-option-sidebar-message" class="easy-support-videos-input easy-support-videos-textarea easy-support-videos-option-sidebar-message large-text" name="easy-support-videos-option-sidebar-message" cols="100" rows="10">' . esc_textarea( stripslashes( $easy_support_videos_options['sidebar']['message'] ) ) . '</textarea>', $easy_support_videos_options['sidebar']['message'], $easy_support_videos_options ); ?>
				<span id="easy-support-videos-video-sidebar-message-spinner" class="spinner easy-support-videos-spinner easy-support-videos-video-spinner easy-support-videos-video-sidebar-message-spinner" data-type="option" data-option-group="sidebar" data-option-name="message"></span>

				<?php do_action( 'easy_support_videos_sidebar_item_message_content_after' ); ?>
			</div>
	<?php
		// Otherwise the current user can view Easy Support Videos
		else:
	?>
			<span class="easy-support-videos-sidebar-item-content">
				<?php do_action( 'easy_support_videos_sidebar_item_message_content_before' ); ?>

				<?php
					// Apply the_content filter to the sidebar message
					$easy_support_videos_options['sidebar']['message'] = apply_filters( 'the_content', $easy_support_videos_options['sidebar']['message'] );
					$easy_support_videos_options['sidebar']['message'] = str_replace( ']]>', ']]&gt;', stripslashes( $easy_support_videos_options['sidebar']['message'] ) );

					echo $easy_support_videos_options['sidebar']['message'];
				?>

				<?php do_action( 'easy_support_videos_sidebar_item_message_content_after' ); ?>
			</span>
	<?php
		endif;
	?>

	<?php do_action( 'easy_support_videos_sidebar_item_message_inner_after' ); ?>
</div>

<?php do_action( 'easy_support_videos_sidebar_item_message_after' ); ?>