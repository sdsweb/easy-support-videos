<?php
	// Grab the Easy Support Videos options
	$easy_support_videos_options = Easy_Support_Videos_Options::get_options();
?>

<div class="easy-support-videos-option easy-support-videos-roles-edit-option">
	<?php do_action( 'easy_support_videos_roles_edit_before' ); ?>

	<select id="easy-support-videos-roles-edit-option" class="easy-support-videos-select easy-support-videos-roles-edit-option" name="<?php echo Easy_Support_Videos_Options::$option_name; ?>[roles][edit]">
		<option value=""><?php _e( 'Select a Role', 'easy-support-videos' ); ?></option>
		<option value="manage_options" <?php selected( $easy_support_videos_options['roles']['edit'], 'manage_options' ); ?>><?php _e( 'Administrator', 'easy-support-videos' ); ?></option>
		<option value="edit_pages" <?php selected( $easy_support_videos_options['roles']['edit'], 'edit_pages' ); ?>><?php _e( 'Editor', 'easy-support-videos' ); ?></option>
		<option value="upload_files" <?php selected( $easy_support_videos_options['roles']['edit'], 'upload_files' ); ?>><?php _e( 'Author', 'easy-support-videos' ); ?></option>
		<option value="edit_posts" <?php selected( $easy_support_videos_options['roles']['edit'], 'edit_posts' ); ?>><?php _e( 'Contributor', 'easy-support-videos' ); ?></option>
		<?php do_action( 'easy_support_videos_options_roles_edit_options', $easy_support_videos_options ); ?>
	</select>
	<br />
	<p class="description easy-support-videos-description"><?php _e( 'Select the role which can edit Easy Support Videos. Please note: Roles inherit capabilities by default in WordPress. If the "Contributor" role is selected, every role that inherits Contributor capabilities will be able to edit Easy Support Videos.', 'easy-support-videos' ); ?></p>

	<?php do_action( 'easy_support_videos_roles_edit_after' ); ?>
</div>