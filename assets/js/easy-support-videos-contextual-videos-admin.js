/**
 * Easy Support Videos - Contextual Videos Edit Admin
 */

( function ( $, easy_support_videos_contextual_videos_admin ) {
	"use strict";

	// Defaults
	if ( ! easy_support_videos_contextual_videos_admin.hasOwnProperty( 'Backbone' ) ) {
		easy_support_videos_contextual_videos_admin.Backbone = {
			Views: {},
			instances: {
				views: {}
			}
		};
	}

	// Default functions
	if ( ! easy_support_videos_contextual_videos_admin.hasOwnProperty( 'fn' ) ) {
		easy_support_videos_contextual_videos_admin.fn = {
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
						return easy_support_videos_contextual_videos_admin.Backbone.instances.views[type];
					}
				}
			}
		};
	}

	var Easy_Support_Videos_Contextual_Videos_View;

	/**
	 * Easy Support Videos Contextual Videos View
	 */
	easy_support_videos.Backbone.Views.Easy_Support_Videos_Contextual_Videos = wp.Backbone.View.extend( {
		// TODO: Future: Add all element selectors and CSS classes as properties on this view (or in localized data)
		el: '#easy-support-videos-contextual-videos-modal',
		events: {
			/*
			 * Easy Support Videos AJAX Setup Data
			 *
			 * Note: This event will bubble up from the Easy Support Videos element.
			 */
			'esv-ajax-setup-data': function( event, data, action, $el ) {
				this.ajax.events.esvAjaxSetupData( event, data, action, $el );
			},
			/*
			 * Easy Support Videos AJAX Success Insert Video
			 */
			'esv-ajax-success-insert-video': function( event, response ) {
				this.ajax.events.esvAjaxSuccessInsertVideo( event, response );
			},
			/*
			 * Easy Support Videos AJAX Success Delete Video
			 */
			'esv-ajax-success-delete-video': function( event, response ) {
				this.ajax.events.esvAjaxSuccessDeleteVideo( event, response );
			},
			'esv-show-message': 'esvShowMessage'
		},
		/**
		 * AJAX
		 *
		 * AJAX data and functions.
		 */
		ajax: {
			// Default AJAX data
			data: {
				easy_support_videos_contextual_videos: 1,
				contextual_videos: {}
			},
			/*
			 * Events
			 */
			events: {
				/**
				 * This function runs when AJAX data is setup for Easy Support Videos.
				 */
				esvAjaxSetupData: function( event, data, action, $el ) {
					var context_id = Easy_Support_Videos_Contextual_Videos_View.$el.find( '.easy-support-videos-context-id' ).val();

					// Add Easy Support Videos Contextual Videos data
					data = Easy_Support_Videos_Contextual_Videos_View.ajax.setupData( data, action, $el, true );

					// If we have an action
					if ( action ) {
						// Switch based on action
						switch ( action ) {
							// Edit Video, Insert Video, Delete Video
							case easy_support_videos.actions.edit:
							case easy_support_videos.actions.insert:
							case easy_support_videos.actions.delete:
								// Add the context ID to the contextual videos data
								data.contextual_videos.id = context_id;
							break;
						}
					}

					return data;
				},
				/**
				 * This function runs on when a video is successfully inserted to Easy Support Videos.
				 */
				esvAjaxSuccessInsertVideo: function ( event, response ) {
					// If the Easy Support Videos element has the "easy-support-videos-has-videos" CSS class
					if ( easy_support_videos.Backbone.instances.views.easy_support_videos.$el.hasClass( 'easy-support-videos-has-videos' ) ) {
						// Add the "easy-support-videos-has-videos" to the contextual videos element
						Easy_Support_Videos_Contextual_Videos_View.$el.addClass( 'easy-support-videos-has-videos' );
					}
				},
				/**
				 * This function runs on when a video is successfully deleted from Easy Support Videos.
				 */
				esvAjaxSuccessDeleteVideo: function ( event, response ) {
					// If the Easy Support Videos element doesn't have the "easy-support-videos-has-videos" CSS class
					if ( ! easy_support_videos.Backbone.instances.views.easy_support_videos.$el.hasClass( 'easy-support-videos-has-videos' ) ) {
						// Remove the "easy-support-videos-has-videos" from the contextual videos element
						Easy_Support_Videos_Contextual_Videos_View.$el.removeClass( 'easy-support-videos-has-videos' );
					}
				}
			},
			/*
			 * Flags
			 */
			flags: {
				setup_data: 'esv-contextual-videos-ajax-setup-data'
			},
			/**
			 * This function sets up AJAX data.
			 */
			setupData: function( data, action, $el, skip_easy_support_videos_data ) {
				// Defaults
				$el = $el || false;
				skip_easy_support_videos_data = skip_easy_support_videos_data || false;

				// Trigger the setup data event on the Easy Support Videos Backbone View element
				Easy_Support_Videos_Contextual_Videos_View.$el.trigger( Easy_Support_Videos_Contextual_Videos_View.ajax.flags.setup_data, [ data, action, $el, skip_easy_support_videos_data ] );

				return $.extend( data, ( ! skip_easy_support_videos_data ) ? $.extend( easy_support_videos.Backbone.instances.views.easy_support_videos.ajax.setupData( data, action, $el ), JSON.parse( JSON.stringify( this.data ) ) ) : JSON.parse( JSON.stringify( ( this.data ) ) ) );
			}
		},
		/**
		 * This function runs on initialization of the view.
		 */
		initialize: function() {
			var self = this;

			// Bind this to functions
			_.bindAll(
				this,
				'esvShowMessage'
			);
		},
		/**
		 * This function runs when the show message event is triggered on the Easy Support
		 * Videos Backbone View element.
		 */
		esvShowMessage: function( event, view ) {
			// Set the current message timer delay on the Easy Support Videos Backbone View (1750ms)
			view.current_message_timer_delay = 1750;
		}
	} );

	/**
	 * Document Ready
	 */
	$( function() {
		/**
		 * Backbone Views
		 */

		// Create a new instance of the Easy Support Videos Contextual Videos Backbone View
		Easy_Support_Videos_Contextual_Videos_View = new easy_support_videos.Backbone.Views.Easy_Support_Videos_Contextual_Videos();

		// Add the Easy Support Videos Contextual Videos Backbone View to the Easy Support Videos contextual Videos Backbone view instances
		easy_support_videos_contextual_videos_admin.Backbone.instances.views['view'] = Easy_Support_Videos_Contextual_Videos_View;
	} );
} )( jQuery, easy_support_videos_contextual_videos_admin );