<?php
	// Grab the Easy Support Videos options
	$easy_support_videos_options = Easy_Support_Videos_Options::get_options();
?>

<div class="easy-support-videos-option easy-support-videos-contextual-videos-enabled-option">
	<?php do_action( 'easy_support_videos_contextual_videos_enabled_before' ); ?>

	<div class="easy-support-videos-toggle-checkbox easy-support-videos-toggle-enable" data-label-left="<?php esc_attr_e( 'On', 'easy-support-videos' ); ?>" data-label-right="<?php esc_attr_e( 'Off', 'easy-support-videos' ); ?>">
		<input id="easy-support-videos-contextual-videos-enabled-option" class="easy-support-videos-checkbox easy-support-videos-contextual-videos-enabled-option" name="<?php echo Easy_Support_Videos_Options::$option_name; ?>[contextual_videos][enabled]" type="checkbox" <?php checked( ( isset( $easy_support_videos_options['contextual_videos'] ) ) ? $easy_support_videos_options['contextual_videos']['enabled'] : false ); ?> />
		<label for="easy-support-videos-contextual-videos-enabled-option">| | |</label>
	</div>
	<p class="description easy-support-videos-description"><?php _e( 'Enable or disable contextual videos.', 'easy-support-videos' ); ?></p>

	<?php do_action( 'easy_support_videos_contextual_videos_enabled_after' ); ?>
</div>