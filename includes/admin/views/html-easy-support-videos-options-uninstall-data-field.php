<?php
	// Grab the Easy Support Videos options
	$easy_support_videos_options = Easy_Support_Videos_Options::get_options();
?>

<div class="easy-support-videos-option easy-support-videos-uninstall-data-option">
	<?php do_action( 'easy_support_videos_uninstall_data_before' ); ?>

	<div class="easy-support-videos-toggle-checkbox easy-support-videos-toggle-enable" data-label-left="<?php esc_attr_e( 'Yes', 'easy-support-videos' ); ?>" data-label-right="<?php esc_attr_e( 'No', 'easy-support-videos' ); ?>">
		<input type="checkbox" id="easy-support-videos-uninstall-data-option" name="<?php echo Easy_Support_Videos_Options::$option_name; ?>[uninstall][data]" <?php checked( $easy_support_videos_options['uninstall']['data'] ); ?> />
		<label for="easy-support-videos-uninstall-data-option">| | |</label>
	</div>
	<p class="description easy-support-videos-description"><?php _e( 'Should data be removed when uninstalling Easy Support Videos?', 'easy-support-videos' ); ?></p>

	<?php do_action( 'easy_support_videos_uninstall_data_after' ); ?>
</div>