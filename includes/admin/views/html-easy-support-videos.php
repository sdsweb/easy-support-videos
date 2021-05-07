<?php
// Grab the Easy Support Videos
$easy_support_videos = Easy_Support_Videos_Post_Types::get_easy_support_videos();

// Easy Support Videos wrap CSS classes
$easy_support_videos_wrap_css_classes = array(
	'wrap',
	'about-wrap',
	'easy-support-videos-wrap'
);

// If we have Easy Support Videos
if ( $easy_support_videos->have_posts() )
	// Append the "easy-support-videos-has-videos" CSS class to the Easy Support Videos wrap CSS classes
	$easy_support_videos_wrap_css_classes[] = 'easy-support-videos-has-videos';

// If the current user can edit Easy Support Videos
if ( Easy_Support_Videos_Post_Types::current_user_can( Easy_Support_Videos_Post_Types::$easy_support_videos_post_type, 'edit_posts' ) )
	// Append the "easy-support-videos-can-edit" CSS class to the Easy Support Videos wrap CSS classes
	$easy_support_videos_wrap_css_classes[] = 'easy-support-videos-can-edit';

// Sanitize the Easy Support Videos wrap CSS classes
$easy_support_videos_wrap_css_classes = array_map( 'sanitize_html_class', $easy_support_videos_wrap_css_classes );

// Ensure we have unique Easy Support Videos wrap CSS classes (no empty values)
$easy_support_videos_wrap_css_classes = array_unique( array_values( array_filter( $easy_support_videos_wrap_css_classes ) ) );
?>

<div class="<?php echo esc_attr( implode( ' ', $easy_support_videos_wrap_css_classes ) ); ?>">
	<?php Easy_Support_Videos_Admin_Views::page_title_template(); ?>

	<?php do_action( 'easy_support_videos_notifications' ); ?>

	<?php
		// Add a save option nonce field
		wp_nonce_field( 'easy_support_videos_save_option', 'easy_support_videos_nonce_save_option' );
	?>

	<?php do_action( 'easy_support_videos_flex_wrap_before' ); ?>

	<div id="easy-support-videos-flex-wrap" class="easy-support-videos-flex easy-support-videos-flex-2-columns">
		<?php
			/*
			 * Videos
			 */
		?>

		<div id="easy-support-videos" class="easy-support-videos easy-support-videos-col">
			<?php do_action( 'easy_support_videos_videos_before' ); ?>

			<?php
				// If the current user can publish Easy Support Videos
				if ( Easy_Support_Videos_Post_Types::current_user_can( Easy_Support_Videos_Post_Types::$easy_support_videos_post_type, 'publish_posts' ) ) :
			?>
				<div id="easy-support-videos-status-message" class="easy-support-videos-message easy-support-videos-status-message">
					<span id="easy-support-videos-status-message-message" class="easy-support-videos-message easy-support-videos-status-message-message"></span>
					<span id="easy-support-videos-status-message-close-wrap" class="easy-support-videos-status-message-close-wrap">
						<a href="#" id="easy-support-videos-status-message-close" class="easy-support-videos-status-message-close" title="<?php _e( 'Close Message', 'easy-support-videos' ); ?>">
							<span class="dashicons dashicons-dismiss"></span>
						</a>
					</span>
				</div>
			<?php
				endif;

				// If the current user can publish Easy Support Videos
				if ( Easy_Support_Videos_Post_Types::current_user_can( Easy_Support_Videos_Post_Types::$easy_support_videos_post_type, 'publish_posts' ) )
					// Easy Support Videos Insert Videos
					Easy_Support_Videos_Admin_Views::insert_video_template();

				// Easy Support Videos Videos
				Easy_Support_Videos_Admin_Views::videos_template( array(
					'easy_support_videos' => $easy_support_videos
				) );
			?>

			<?php do_action( 'easy_support_videos_videos_after' ); ?>
		</div>

		<?php
			/*
			 * Sidebar
			 */
			Easy_Support_Videos_Admin_Views::sidebar();
		?>
	</div>

	<?php do_action( 'easy_support_videos_flex_wrap_after' ); ?>
</div>