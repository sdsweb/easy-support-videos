<?php
	// Grab the Easy Support Videos options
	$easy_support_videos_options = Easy_Support_Videos_Options::get_options();
?>

<div class="easy-support-videos-option easy-support-videos-contextual-videos-global-video-enabled-option">
	<?php do_action( 'easy_support_videos_contextual_videos_global_video_enabled_before' ); ?>

	<div class="easy-support-videos-toggle-checkbox easy-support-videos-toggle-global-video-enabled" data-label-left="<?php esc_attr_e( 'On', 'easy-support-videos' ); ?>" data-label-right="<?php esc_attr_e( 'Off', 'easy-support-videos' ); ?>">
		<input id="easy-support-videos-contextual-videos-global-video-enabled-option" class="easy-support-videos-checkbox easy-support-videos-contextual-videos-global-video-enabled-option" name="<?php echo Easy_Support_Videos_Options::$option_name; ?>[contextual_videos][global_video][enabled]" type="checkbox" <?php checked( ( isset( $easy_support_videos_options['contextual_videos'] ) ) ? $easy_support_videos_options['contextual_videos']['global_video']['enabled'] : false ); ?> />
		<label for="easy-support-videos-contextual-videos-global-video-enabled-option">| | |</label>
	</div>
	<p class="description easy-support-videos-description"><?php _e( 'Enable or disable the global contextual video. The global contextual video is displayed in all contexts. You can toggle the global contextual video on the Support Videos page.', 'easy-support-videos' ); ?></p>

	<?php do_action( 'easy_support_videos_contextual_videos_global_video_enabled_after' ); ?>
</div>