<div id="easy-support-videos-insert-video" class="easy-support-videos-insert-video">
	<?php do_action( 'easy_support_videos_insert_video_before' ); ?>

	<?php
		// Add an nonce field
		wp_nonce_field( 'easy_support_videos_insert', 'easy_support_videos_nonce_insert' );
	?>

	<label for="easy-support-videos-insert-video-url"><?php _e( 'Add New Video:', 'easy-support-videos' ); ?></label>
	<input id="easy-support-videos-insert-video-url" class="regular-text easy-support-videos-input easy-support-videos-insert-video-url" name="easy-support-videos-insert-video-url" type="text" value="" autocomplete="off" />
	<button id="easy-support-videos-insert-video-button" class="button button-secondary easy-support-videos-button easy-support-videos-insert-video-button"><?php _e( 'Add Video', 'easy-support-videos' ); ?></button>
	<span id="easy-support-videos-insert-video-spinner" class="spinner easy-support-videos-spinner easy-support-videos-insert-video-spinner" data-type="video" data-event="insert"></span>

	<div id="easy-support-videos-insert-video-description" class="easy-support-videos-insert-video-description">
		<p class="description easy-support-videos-description"><?php printf( __( 'To add a video to the library, paste a URL from any %1$s and "Add Video" or press enter.', 'easy-support-videos' ), sprintf( '<a href="%1$s" target="_blank">%2$s</a>', esc_url( 'https://wordpress.org/support/article/embeds/' ), __( 'embed video provider', 'easy-support-videos' ) ) ); ?></p>
	</div>

	<?php do_action( 'easy_support_videos_insert_video_after' ); ?>
</div>