<?php
	// Grab the Easy Support Videos options
	$easy_support_videos_options = Easy_Support_Videos_Options::get_options();
?>

<div class="easy-support-videos-option easy-support-videos-roles-read-option">
	<?php do_action( 'easy_support_videos_roles_read_before' ); ?>

	<select id="easy-support-videos-roles-read-option" class="easy-support-videos-select easy-support-videos-roles-read-option" name="<?php echo Easy_Support_Videos_Options::$option_name; ?>[roles][read]">
		<option value=""><?php _e( 'Select a Role', 'easy-support-videos' ); ?></option>
		<option value="manage_options" <?php selected( $easy_support_videos_options['roles']['read'], 'manage_options' ); ?>><?php _e( 'Administrator', 'easy-support-videos' ); ?></option>
		<option value="edit_pages" <?php selected( $easy_support_videos_options['roles']['read'], 'edit_pages' ); ?>><?php _e( 'Editor', 'easy-support-videos' ); ?></option>
		<option value="upload_files" <?php selected( $easy_support_videos_options['roles']['read'], 'upload_files' ); ?>><?php _e( 'Author', 'easy-support-videos' ); ?></option>
		<option value="edit_posts" <?php selected( $easy_support_videos_options['roles']['read'], 'edit_posts' ); ?>><?php _e( 'Contributor', 'easy-support-videos' ); ?></option>
		<option value="read" <?php selected( $easy_support_videos_options['roles']['read'], 'read' ); ?>><?php _e( 'Subscriber', 'easy-support-videos' ); ?></option>
		<?php do_action( 'easy_support_videos_options_roles_read_options', $easy_support_videos_options ); ?>
	</select>
	<br />
	<p class="description easy-support-videos-description"><?php _e( 'Select the role which can view Easy Support Videos. Please note: Roles inherit capabilities by default in WordPress. If the "Contributor" role is selected, every role that inherits Contributor capabilities will be able to view Easy Support Videos.', 'easy-support-videos' ); ?></p>

	<?php do_action( 'easy_support_videos_roles_read_after' ); ?>
</div>