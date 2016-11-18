<?php do_action( 'easy_support_videos_sidebar_item_rate_before' ); ?>

<div class="easy-support-videos-sidebar-item easy-support-videos-sidebar-item-rate">
	<?php do_action( 'easy_support_videos_sidebar_item_rate_inner_before' ); ?>

	<span class="easy-support-videos-sidebar-item-content">
		<?php do_action( 'easy_support_videos_sidebar_item_rate_content_before' ); ?>

		<?php printf( __( 'Love this plugin? Please rate <strong>Easy Support Videos</strong> <a href="%1$s" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a> on <a href="%1$s" target="_blank">WordPress.org</a>.', 'easy-support-videos' ), 'https://wordpress.org/support/plugin/easy-support-videos/reviews/?filter=5' ); ?>

		<?php do_action( 'easy_support_videos_sidebar_item_rate_content_after' ); ?>
	</span>

	<?php do_action( 'easy_support_videos_sidebar_item_rate_inner_after' ); ?>
</div>

<?php do_action( 'easy_support_videos_sidebar_item_rate_after' ); ?>