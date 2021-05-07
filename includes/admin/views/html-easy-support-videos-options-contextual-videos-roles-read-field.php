<?php
	// Grab the Easy Support Videos options
	$easy_support_videos_options = Easy_Support_Videos_Options::get_options();
?>

<div class="easy-support-videos-option easy-support-videos-contextual-videos-roles-read-option">
	<?php do_action( 'easy_support_videos_contextual_videos_roles_read_before' ); ?>

	<select id="easy-support-videos-contextual-videos-roles-read-option" class="easy-support-videos-select easy-support-videos-contextual-videos-roles-read-option" name="<?php echo Easy_Support_Videos_Options::$option_name; ?>[contextual_videos][roles][read]">
		<option value=""><?php _e( 'Select a Role', 'easy-support-videos' ); ?></option>
		<option value="manage_options" <?php selected( ( isset( $easy_support_videos_options['contextual_videos'] ) ) ? $easy_support_videos_options['contextual_videos']['roles']['read'] : false, 'manage_options' ); ?>><?php _e( 'Administrator', 'easy-support-videos' ); ?></option>
		<option value="edit_pages" <?php selected( ( isset( $easy_support_videos_options['contextual_videos'] ) ) ? $easy_support_videos_options['contextual_videos']['roles']['read'] : false, 'edit_pages' ); ?>><?php _e( 'Editor', 'easy-support-videos' ); ?></option>
		<option value="upload_files" <?php selected( ( isset( $easy_support_videos_options['contextual_videos'] ) ) ? $easy_support_videos_options['contextual_videos']['roles']['read'] : false, 'upload_files' ); ?>><?php _e( 'Author', 'easy-support-videos' ); ?></option>
		<option value="edit_posts" <?php selected( ( isset( $easy_support_videos_options['contextual_videos'] ) ) ? $easy_support_videos_options['contextual_videos']['roles']['read'] : false, 'edit_posts' ); ?>><?php _e( 'Contributor', 'easy-support-videos' ); ?></option>
		<option value="read" <?php selected( ( isset( $easy_support_videos_options['contextual_videos'] ) ) ? $easy_support_videos_options['contextual_videos']['roles']['read'] : false, 'read' ); ?>><?php _e( 'Subscriber', 'easy-support-videos' ); ?></option>
		<?php do_action( 'easy_support_videos_options_contextual_videos_roles_read_options', $easy_support_videos_options ); ?>
	</select>
	<br />
	<p class="description easy-support-videos-description"><?php _e( 'Select the role which can view contextual Easy Support Videos. <strong>Please note: This setting functions differently (and separately) from the "Roles" settings above. The selected role and <em>all roles considered lower than the selected role</em> will be able to view contextual Easy Support Videos.</strong> If the "Contributor" role is selected, both Contributors and Subscribers will be able to view contextual Easy Support Videos.', 'easy-support-videos' ); ?></p>

	<?php do_action( 'easy_support_videos_contextual_videos_roles_read_after' ); ?>
</div>