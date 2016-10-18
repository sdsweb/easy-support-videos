<?php
	// Grab the Easy Support Videos options
	$easy_support_videos_options = Easy_Support_Videos_Options::get_options();
?>

<div id="easy-support-videos-sidebar" class="easy-support-videos-sidebar easy-support-videos-col">
	<div id="easy-support-videos-sidebar-inner" class="easy-support-videos-sidebar-inner">
		<?php do_action( 'easy_support_videos_sidebar_before' ); ?>

		<div class="easy-support-videos-sidebar-item easy-support-videos-sidebar-item-message <?php echo ( ! Easy_Support_Videos_Post_Types::current_user_can( Easy_Support_Videos_Post_Types::$easy_support_videos_post_type, 'publish_posts' ) && empty( $easy_support_videos_options['sidebar']['message'] ) ) ? 'empty' : ( ( ! Easy_Support_Videos_Post_Types::current_user_can( Easy_Support_Videos_Post_Types::$easy_support_videos_post_type, 'publish_posts' ) ) ? 'not-empty' : false ) ?>">
			<?php
				// If the current user can publish Easy Support Videos
				if ( Easy_Support_Videos_Post_Types::current_user_can( Easy_Support_Videos_Post_Types::$easy_support_videos_post_type, 'publish_posts' ) ) :
			?>
					<span class="easy-support-videos-sidebar-item-content">
						<input id="easy-support-videos-sidebar-message-option-name" class="easy-support-videos-input easy-support-videos-hidden-input easy-support-videos-sidebar-message-option-name" name="easy-support-videos-sidebar-message-option-name" type="hidden" value="message" />
						<input id="easy-support-videos-sidebar-message-option-group" class="easy-support-videos-input easy-support-videos-hidden-input easy-support-videos-sidebar-message-option-group" name="easy-support-videos-sidebar-message-option-group" type="hidden" value="sidebar" />
						<label for="easy-support-videos-option-sidebar-message" class="screen-reader-text"><?php _e( 'Edit Sidebar Message:', 'easy-support-videos' ); ?></label>
						<textarea id="easy-support-videos-option-sidebar-message" class="easy-support-videos-input easy-support-videos-textarea easy-support-videos-option-sidebar-message large-text" name="easy-support-videos-option-sidebar-message" cols="100" rows="10"><?php echo esc_textarea( stripslashes( $easy_support_videos_options['sidebar']['message'] ) ); ?></textarea>
						<span id="easy-support-videos-video-sidebar-message-spinner" class="spinner easy-support-videos-spinner easy-support-videos-video-spinner easy-support-videos-video-sidebar-message-spinner"></span>
					</span>
			<?php
				// Otherwise the current user can view Easy Support Videos
				else:
			?>
					<span class="easy-support-videos-sidebar-item-content">
						<?php
							// Apply the_content filter to the sidebar message
							$easy_support_videos_options['sidebar']['message'] = apply_filters( 'the_content', $easy_support_videos_options['sidebar']['message'] );
							$easy_support_videos_options['sidebar']['message'] = str_replace( ']]>', ']]&gt;', stripslashes( $easy_support_videos_options['sidebar']['message'] ) );

							echo $easy_support_videos_options['sidebar']['message'];
						?>
					</span>
			<?php
				endif;
			?>
		</div>

		<?php do_action( 'easy_support_videos_sidebar_after' ); ?>
	</div>

	<?php
		if ( $show_easy_support_videos_rate_item = apply_filters( 'easy_support_videos_sidebar_show_rate_item', true ) ) :
	?>
		<?php do_action( 'easy_support_videos_sidebar_item_rate_before' ); ?>

		<div class="easy-support-videos-sidebar-item easy-support-videos-sidebar-item-rate">
			<span class="easy-support-videos-sidebar-item-content">
				<?php printf( __( 'Love this plugin? Please rate <strong>Easy Support Videos</strong> <a href="%1$s" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a> on <a href="%1$s" target="_blank">WordPress.org</a>.', 'easy-support-videos' ), 'https://wordpress.org/support/plugin/easy-support-videos/reviews/?filter=5' ); ?>
			</span>
		</div>

		<?php do_action( 'easy_support_videos_sidebar_item_rate_after' ); ?>
	<?php
		endif;
	?>
</div>