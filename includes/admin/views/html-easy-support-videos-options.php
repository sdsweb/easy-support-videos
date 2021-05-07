<div class="wrap about-wrap easy-support-videos-wrap easy-support-videos-options-wrap">
	<h1><?php _e( 'Easy Support Videos Options', 'easy-support-videos' ); ?></h1>

	<?php do_action( 'easy_support_videos_notifications' ); ?>
	<?php do_action( 'easy_support_videos_options_notifications' ); ?>

	<?php
		// Settings Errors
		settings_errors();

		// Easy Support Videos Settings Errors
		settings_errors( Easy_Support_Videos_Options::$option_name );
	?>

	<form method="post" action="options.php" enctype="multipart/form-data" id="easy-support-videos-options-form">
		<?php settings_fields( Easy_Support_Videos_Options::$option_name ); ?>

		<div id="easy-support-videos-options-roles-settings" class="easy-support-videos-options-settings easy-support-videos-options-roles-settings">
			<?php
				/**
				 * Easy Support Videos Roles Settings
				 */
				do_settings_sections( Easy_Support_Videos_Options::$option_name . '_roles' );
			?>
		</div>

		<?php do_action( 'easy_support_videos_settings' ); ?>

		<div id="easy-support-videos-options-uninstall-settings" class="easy-support-videos-options-settings easy-support-videos-options-uninstall-settings">
			<?php
				/**
				 * Easy Support Videos Uninstall Settings
				 */
				do_settings_sections( Easy_Support_Videos_Options::$option_name . '_uninstall' );
			?>
		</div>

		<p class="submit">
			<input id="easy-support-videos-options-page" class="easy-support-videos-input easy-support-videos-hidden easy-support-videos-options-page" name="<?php echo Easy_Support_Videos_Options::$option_name; ?>[easy-support-videos-options-page]" type="hidden" value="1" />
			<?php submit_button( __( 'Save Options', 'easy-support-videos' ), 'primary', 'submit', false ); ?>
			<?php submit_button( __( 'Restore Defaults', 'easy-support-videos' ), 'secondary', Easy_Support_Videos_Options::$option_name . '[reset]', false ); ?>
		</p>
	</form>
</div>