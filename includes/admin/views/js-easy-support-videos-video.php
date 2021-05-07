<?php
/**
 * Easy Support Videos Single Video UnderscoreJS Template
 */
?>

<?php do_action( 'easy_support_videos_video_template_before' ); ?>

<script type="text/template" id="tmpl-easy-support-videos-video">
	<?php do_action( 'easy_support_videos_video_before', array() ); ?>

	<div class="easy-support-video easy-support-video-can-edit easy-support-video-{{ data.post_id }}" data-post-id="{{ data.post_id }}" <?php do_action( 'easy_support_videos_video_data_attributes', array() ); // TODO: Change this to a function with a filter instead of an action? ?>>
		<?php do_action( 'easy_support_videos_video_inner_before', array() ); ?>

		<span id="easy-support-videos-video-{{ data.post_id }}-spinner" class="spinner easy-support-videos-spinner easy-support-videos-video-spinner easy-support-videos-video-{{ data.post_id }}-spinner" data-type="video" data-post-id="{{ data.post_id }}" data-event="video"></span>

		<div class="easy-support-video-content">
			<?php do_action( 'easy_support_videos_video_content_before', array() ); ?>

			{{{ data.html }}}

			<?php do_action( 'easy_support_videos_video_content_after', array() ); ?>
		</div>

		<div class="easy-support-video-title easy-support-video-can-edit-title">
			<?php do_action( 'easy_support_videos_video_title_before', array() ); ?>

			<label for="easy-support-videos-video-{{ data.post_id }}-title" class="screen-reader-text"><?php _e( 'Video Title:', 'easy-support-videos' ); ?></label>
			<input id="easy-support-videos-video-{{ data.post_id }}-title" class="regular-text easy-support-videos-input easy-support-videos-video-title easy-support-videos-video-{{ data.post_id }}-title" name="easy-support-videos-video-{{ data.post_id }}-title" type="text" value="{{ data.title }}" autocomplete="off" />
			<span id="easy-support-videos-video-{{ data.post_id }}-title-spinner" class="spinner easy-support-videos-spinner easy-support-videos-video-title-spinner easy-support-videos-video-{{ data.post_id }}-title-spinner" data-type="video" data-post-id="{{ data.post_id }}" data-event="edit"></span>
			<a href="#" class="easy-support-videos-video-delete" title="<?php _e( 'Delete Video', 'easy-support-videos' ); ?>">
				<span class="dashicons dashicons-trash"></span>
			</a>
			<input id="easy-support-videos-video-{{ data.post_id }}-id" class="easy-support-videos-input easy-support-videos-input-hidden easy-support-videos-video-id easy-support-videos-post-id easy-support-videos-video-{{ data.post_id }}-id" type="hidden" value="{{ data.post_id }}" />

			<?php do_action( 'easy_support_videos_video_title_after', array() ); ?>
		</div>

		<div class="easy-support-video-excerpt easy-support-video-can-edit-excerpt">
			<?php do_action( 'easy_support_videos_video_excerpt_before', array() ); ?>

			<label for="easy-support-videos-video-{{ data.post_id }}-excerpt" class="screen-reader-text"><?php _e( 'Video Excerpt:', 'easy-support-videos' ); ?></label>
			<textarea id="easy-support-videos-video-{{ data.post_id }}-excerpt" class="large-text easy-support-videos-input easy-support-videos-textarea easy-support-videos-video-excerpt easy-support-videos-video-{{ data.post_id }}-excerpt" name="easy-support-videos-video-{{ data.post_id }}-excerpt" placeholder="<?php esc_attr_e( 'Enter a video description (optional)', 'easy-support-videos' ); ?>" maxlength="<?php echo esc_attr( Easy_Support_Videos_Post_Types::$maximum_post_excerpt_length ); ?>">{{{ data.excerpt || '' }}}</textarea>
			<div id="easy-support-videos-video-{{ data.post_id }}-excerpt-character-count" class="easy-support-videos-video-excerpt-character-count easy-support-videos-video-{{ data.post_id }}-excerpt-character-count">
				<span id="easy-support-videos-video-{{ data.post_id }}-excerpt-current-character-count" class="easy-support-videos-video-excerpt-current-character-count easy-support-videos-video-{{ data.post_id }}-excerpt-current-character-count">{{{ data.excerpt_length || '0' }}}</span>
				/
				<span id="easy-support-videos-video-{{ data.post_id }}-excerpt-character-maximum" class="easy-support-videos-video-excerpt-character-maximum easy-support-videos-video-{{ data.post_id }}-excerpt-character-maximum"><?php echo Easy_Support_Videos_Post_Types::$maximum_post_excerpt_length; ?></span>
			</div>

			<?php do_action( 'easy_support_videos_video_excerpt_after', array() ); ?>
		</div>

		<?php do_action( 'easy_support_videos_video_inner_after', array() ); ?>
	</div>

	<?php do_action( 'easy_support_videos_video_after', array() ); ?>
</script>


<?php do_action( 'easy_support_videos_video_template_after' ); ?>