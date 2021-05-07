/**
 * Easy Support Videos - Setup Wizard
 */

( function ( $, easy_support_videos_contextual_videos ) {
	"use strict";

	/**
	 * Document Ready
	 */
	$( function() {
		var Easy_Support_Videos_Contextual_Videos_View = easy_support_videos_contextual_videos.fn.Backbone.views.get( 'view' ),
			Easy_Support_Videos_Contextual_Videos_Widget_View = easy_support_videos_contextual_videos.fn.Backbone.views.get( 'widget_view' );

		// Start a new thread; delay 1ms
		setTimeout( function() {
			// Toggle the expanded Easy Support Videos Contextual Videos modal
			Easy_Support_Videos_Contextual_Videos_View.toggleContextualVideosModalExpand( false );

			// Toggle the Easy Support Videos Contextual Videos modal
			Easy_Support_Videos_Contextual_Videos_Widget_View.toggleContextualVideosModal( false );
		}, 1 );
	} );
} )( jQuery, easy_support_videos_contextual_videos );