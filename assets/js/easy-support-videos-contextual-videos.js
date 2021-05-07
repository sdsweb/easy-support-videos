/**
 * Easy Support Videos - Contextual Videos Edit
 */

( function ( $, easy_support_videos_contextual_videos ) {
	"use strict";

	// Defaults
	if ( ! easy_support_videos_contextual_videos.hasOwnProperty( 'Backbone' ) ) {
		easy_support_videos_contextual_videos.Backbone = {
			Views: {},
			instances: {
				views: {}
			}
		};
	}

	// Default functions
	if ( ! easy_support_videos_contextual_videos.hasOwnProperty( 'fn' ) ) {
		easy_support_videos_contextual_videos.fn = {
			/**
			 * Backbone
			 */
			Backbone: {
				/**
				 * Views
				 */
				views: {
					/**
					 * This function gets a view instance based on the type.
					 */
					get: function( type ) {
						return easy_support_videos_contextual_videos.Backbone.instances.views[type];
					}
				}
			}
		};
	}

	var Easy_Support_Videos_Contextual_Videos_Widget_View,
		Easy_Support_Videos_Contextual_Videos_View;

	/**
	 * Easy Support Videos Contextual Videos Widget View
	 */
	easy_support_videos_contextual_videos.Backbone.Views.Easy_Support_Videos_Contextual_Videos_Widget = wp.Backbone.View.extend( {
		// TODO: Future: Add all element selectors and CSS classes as properties on this view (or in localized data)
		el: '#easy-support-videos-contextual-videos-widget',
		events: {
			'click .easy-support-videos-contextual-videos-icon': 'toggleContextualVideosModal'
		},
		/**
		 * This function runs on initialization of the view.
		 */
		initialize: function() {
			var self = this;

			// Bind this to functions
			_.bindAll(
				this,
				'toggleContextualVideosModal'
			);
		},
		/**
		 * This function toggles the Easy Support Videos Contextual Videos modal.
		 */
		toggleContextualVideosModal: function( event ) {
			var is_visible = Easy_Support_Videos_Contextual_Videos_View.$el.is( ':visible' );

			// If the Easy Support Videos Contextual Videos modal isn't visible
			if ( ! is_visible ) {
				// Toggle the Easy Support Videos Contextual Videos modal
				Easy_Support_Videos_Contextual_Videos_View.$el.toggle();
			}
			// Otherwise the Easy Support Videos Contextual Videos modal is visible
			else {
				// Close the Easy Support Videos Contextual Videos modal
				Easy_Support_Videos_Contextual_Videos_View.closeContextualVideosModal( false );
			}
		}
	} );

	/**
	 * Easy Support Videos Contextual Videos View
	 */
	easy_support_videos_contextual_videos.Backbone.Views.Easy_Support_Videos_Contextual_Videos = wp.Backbone.View.extend( {
		// TODO: Future: Add all element selectors and CSS classes as properties on this view (or in localized data)
		el: '#easy-support-videos-contextual-videos-modal',
		videos_el_selector: '#easy-support-videos-videos',
		events: {
			'click .easy-support-videos-contextual-videos-modal-close': 'closeContextualVideosModal',
			'click .easy-support-videos-contextual-videos-modal-expand': 'toggleContextualVideosModalExpand'
		},
		/**
		 * This function runs on initialization of the view.
		 */
		initialize: function() {
			var self = this;

			// Bind this to functions
			_.bindAll(
				this,
				'closeContextualVideosModal',
				'toggleContextualVideosModalExpand'
			);
		},
		/**
		 * This function closes the Easy Support Videos Contextual Videos modal.
		 */
		closeContextualVideosModal: function( event ) {
			var $easy_support_videos = Easy_Support_Videos_Contextual_Videos_View.$el.find( '.easy-support-video iframe' );

			// Loop through the Easy Support Videos
			$easy_support_videos.each( function() {
				var $this = $( this ),
					src = $this.attr( 'src' );
				
				/*
				 * Note: The following logic is necessary to help prevent videos from playing
				 * after the modal is closed.
				 */

				// Reset the source attribute
				$this.attr( 'src', '' );

				// Set the source attribute
				$this.attr( 'src', src );
			} );

			// Hide the Easy Support Videos Contextual Videos modal
			Easy_Support_Videos_Contextual_Videos_View.$el.hide();
		},
		/**
		 * This function toggles the expanded Easy Support Videos Contextual modal.
		 */
		toggleContextualVideosModalExpand: function( event ) {
			// Hide the Easy Support Videos Contextual Videos modal
			Easy_Support_Videos_Contextual_Videos_View.$el.toggleClass( 'easy-support-videos-contextual-videos-modal-expanded' );
		}
	} );

	/**
	 * Document Ready
	 */
	$( function() {
		/**
		 * Backbone Views
		 */

		// Create a new instance of the Easy Support Videos Contextual Videos Widget Backbone View
		Easy_Support_Videos_Contextual_Videos_Widget_View = new easy_support_videos_contextual_videos.Backbone.Views.Easy_Support_Videos_Contextual_Videos_Widget();

		// Add the Easy Support Videos Contextual Videos Widget Backbone View to the Easy Support Videos contextual Videos Backbone view instances
		easy_support_videos_contextual_videos.Backbone.instances.views['widget_view'] = Easy_Support_Videos_Contextual_Videos_Widget_View;

		// Create a new instance of the Easy Support Videos Contextual Videos Backbone View
		Easy_Support_Videos_Contextual_Videos_View = new easy_support_videos_contextual_videos.Backbone.Views.Easy_Support_Videos_Contextual_Videos();

		// Add the Easy Support Videos Contextual Videos Backbone View to the Easy Support Videos contextual Videos Backbone view instances
		easy_support_videos_contextual_videos.Backbone.instances.views['view'] = Easy_Support_Videos_Contextual_Videos_View;


		/**
		 * FitVids
		 */

		// FitVids
		$( Easy_Support_Videos_Contextual_Videos_View.videos_el_selector ).fitVids();


		/**
		 * Easy Support Videos Preview & Editing
		 */

		// If this is an Easy Support Videos preview or this is an Easy Support Videos editing request
		if ( easy_support_videos_contextual_videos.is_preview || easy_support_videos_contextual_videos.is_editing ) {
			// Start a new thread; delay 1ms
			setTimeout( function() {
				// Toggle the Easy Support Videos Contextual Videos modal
				Easy_Support_Videos_Contextual_Videos_Widget_View.toggleContextualVideosModal( false );
			}, 1 );
		}
	} );
} )( jQuery, easy_support_videos_contextual_videos );