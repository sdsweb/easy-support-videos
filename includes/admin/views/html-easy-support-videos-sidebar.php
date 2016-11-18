<?php
	// Grab the Easy Support Videos options
	$easy_support_videos_options = Easy_Support_Videos_Options::get_options();
?>

<div id="easy-support-videos-sidebar" class="easy-support-videos-sidebar easy-support-videos-col">
	<div id="easy-support-videos-sidebar-inner" class="easy-support-videos-sidebar-inner">
		<?php do_action( 'easy_support_videos_sidebar_before' ); ?>

		<?php
			/*
			 * Sidebar Item - Message
			 */
			Easy_Support_Videos_Admin_Views::sidebar_item_message();
		?>

		<?php do_action( 'easy_support_videos_sidebar_after' ); ?>
	</div>

	<?php
		if ( $show_easy_support_videos_rate_item = apply_filters( 'easy_support_videos_sidebar_show_rate_item', true ) ) :
			/*
			 * Sidebar Item - Rate
			 */
			Easy_Support_Videos_Admin_Views::sidebar_item_rate();
		endif;
	?>
</div>