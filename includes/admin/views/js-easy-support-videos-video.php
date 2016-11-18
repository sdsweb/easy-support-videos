<?php
/**
 * Easy Support Videos Single Video UnderscoreJS Template
 */
?>

<script type="text/template" id="tmpl-easy-support-videos-video">
	<?php do_action( 'easy_support_videos_video_before', array() ); ?>

	<div class="easy-support-video easy-support-video-can-edit easy-support-video-{{ data.post_id }}" data-post-id="{{ data.post_id }}" <?php do_action( 'easy_support_videos_video_data_attributes', array() ); // TODO: Change this to a function with a filter instead of an action? ?>>
		<?php do_action( 'easy_support_videos_video_inner_before', array() ); ?>

		<span id="easy-support-videos-video-{{ data.post_id }}-spinner" class="spinner easy-support-videos-spinner easy-support-videos-video-spinner easy-support-videos-video-{{ data.post_id }}-spinner"></span>

		<div class="easy-support-video-content">
			<?php do_action( 'easy_support_videos_video_content_before', array() ); ?>

			{{{ data.html }}}

			<?php do_action( 'easy_support_videos_video_content_after', array() ); ?>
		</div>

		<div class="easy-support-video-title easy-support-video-can-edit-title">
			<?php do_action( 'easy_support_videos_video_title_before', array() ); ?>

			<label for="easy-support-videos-video-{{ data.post_id }}-title" class="screen-reader-text"><?php _e( 'Video Title:', 'easy-support-videos' ); ?></label>
			<input id="easy-support-videos-video-{{ data.post_id }}-title" class="regular-text easy-support-videos-input easy-support-videos-video-title easy-support-videos-video-{{ data.post_id }}-title" name="easy-support-videos-video-{{ data.post_id }}-title" type="text" value="{{ data.title }}" autocomplete="off" />
			<span id="easy-support-videos-video-{{ data.post_id }}-title-spinner" class="spinner easy-support-videos-spinner easy-support-videos-video-title-spinner easy-support-videos-video-{{ data.post_id }}-title-spinner"></span>
			<a href="#" class="easy-support-videos-video-delete" title="<?php _e( 'Delete Video', 'easy-support-videos' ); ?>">
				<span class="dashicons dashicons-dismiss"></span>
			</a>
			<input id="easy-support-videos-video-{{ data.post_id }}-id" class="easy-support-videos-input easy-support-videos-input-hidden easy-support-videos-video-id easy-support-videos-post-id easy-support-videos-video-{{ data.post_id }}-id" type="hidden" value="{{ data.post_id }}" />

			<?php do_action( 'easy_support_videos_video_title_after', array() ); ?>
		</div>

		<?php do_action( 'easy_support_videos_video_inner_after', array() ); ?>
	</div>

	<?php do_action( 'easy_support_videos_video_after', array() ); ?>
</script>