/**
 * Easy Support Videos Admin
 */
var easy_support_videos = easy_support_videos || {};

// TODO: this vs global variables (normalize in logic)

( function ( $ ) {
	"use strict";

	var Easy_Support_Videos_View;

	/*
	 * Defaults
	 */
	if ( ! easy_support_videos.hasOwnProperty( 'Backbone' ) ) {
		easy_support_videos.Backbone = {
			Views: {},
			instances: {
				views: []
			}
		};
	}

	if ( ! easy_support_videos.hasOwnProperty( 'actions' ) ) {
		easy_support_videos.actions = {
			insert: 'easy_support_videos_insert',
			edit: 'easy_support_videos_edit',
			delete: 'easy_support_videos_delete',
			save_option: 'easy_support_videos_save_option'
		};
	}

	if ( ! easy_support_videos.hasOwnProperty( 'spinner' ) ) {
		easy_support_videos.spinner = {
			active_css_classes: 'is-active easy-support-videos-is-active easy-support-videos-spinner-is-active'
		};
	}

	if ( ! easy_support_videos.hasOwnProperty( 'message' ) ) {
		easy_support_videos.message = {
			active_css_classes: 'is-active easy-support-videos-is-active easy-support-videos-message-is-active'
		};
	}

	/**
	 * Easy Support Videos View
	 */
	easy_support_videos.Backbone.Views.Easy_Support_Videos = wp.Backbone.View.extend( {
		el: '.easy-support-videos-wrap',
		template: wp.template( 'easy-support-videos-video' ),
		videos_el_selector: '#easy-support-videos-videos',
		current_value_data_key: 'easy-support-videos-current-value',
		message_timer: -1,
		message_timer_delay: 5000,
		events: {
			// Insert Video
			'click #easy-support-videos-insert-video-button': 'insertVideo',
			'keypress #easy-support-videos-insert-video-url': 'insertVideo',
			// Edit Video
			'keyup .easy-support-videos-video-title': 'editVideo',
			// Delete Video
			'click .easy-support-videos-video-delete': 'deleteVideo',
			// Close Message
			'click .easy-support-videos-status-message-close': 'hideMessage',
			// Edit Sidebar Message
			'keyup #easy-support-videos-option-sidebar-message': 'editSidebarMessage'
		},
		/**
		 * AJAX
		 *
		 * AJAX data and functions.
		 */
		ajax: {
			// Default AJAX data
			data: {
				easy_support_videos: 1
			},
			/**
			 * AJAX Queue
			 */
			queue: {
				processing: false,
				current_item: {},
				current_request: false,
				items: [],
				/**
				 * This function adds an item to the queue for processing.
				 */
				addItem: function( item ) {
					var self = this,
						abort_current_request = false;

					// If this item can abort items with the same action and we have a current request
					if ( item.abort && ! _.isEmpty( this.current_item ) && item.action === this.current_item.action && this.current_request ) {
						// Set the abort flag
						abort_current_request = true;

						// If the abort property is an object
						if ( _.isObject( item.abort ) ) {
							// Loop through each abort property (using find so we can bail if necessary)
							_.find( item.abort, function( value, property ) {
								// Bail if the current item doesn't have this property or the value doesn't match
								if ( ! self.current_item.data.hasOwnProperty( property ) || value !== self.current_item.data[property] ) {
									// Reset the abort flag
									abort_current_request = false;

									return true;
								}
							} );
						}

						// If we should abort
						if ( abort_current_request ) {
							// Abort the current request
							this.current_request.abort();

							// Set the current item
							Easy_Support_Videos_View.ajax.queue.current_item = item;

							// Process the current item
							this.processCurrentItem();
						}
						// Otherwise just add the item to the queue
						else {
							// Add this item to the end of the queue
							Easy_Support_Videos_View.ajax.queue.items.push( item );
						}

					}
					// Otherwise just add the item to the queue
					else {
						// Add this item to the end of the queue
						Easy_Support_Videos_View.ajax.queue.items.push( item );
					}

					// Return this for chaining
					return this;
				},
				/**
				 * This function processes the queue.
				 */
				process: function() {
					// Bail if we're already processing or there are no items to process
					if ( Easy_Support_Videos_View.ajax.queue.processing || Easy_Support_Videos_View.ajax.queue.items.length === 0 ) {
						// If we don't have any items to process
						if ( Easy_Support_Videos_View.ajax.queue.items.length === 0 ) {
							// Reset the current request reference
							this.current_request = false;

							// Reset the current item
							this.current_item = {};

							// Set all active spinners to inactive
							Easy_Support_Videos_View.ajax.setActiveSpinnersInactive();
						}

						return;
					}

					// Reset the current request reference
					this.current_request = false;

					// Set the processing status flag
					this.setProcessingStatusFlag( true );

					// Setup the current item
					Easy_Support_Videos_View.ajax.queue.current_item = Easy_Support_Videos_View.ajax.queue.items.shift();

					// Process the current item
					this.processCurrentItem();

					// Return this for chaining
					return this;
				},
				/**
				 * This function processes the current item in the queue
				 *
				 * @uses Easy_Support_Videos_View.ajax.queue.current_item
				 */
				processCurrentItem: function() {
					var item = Easy_Support_Videos_View.ajax.queue.current_item;

					// Make the AJAX request (POST)
					this.current_request = wp.ajax.post( item.action, item.data ).done( item.success ).fail( item.fail );

					// Return this for chaining
					return this;
				},
				/**
				 * This function sets the processing status flag
				 */
				setProcessingStatusFlag: function( status ) {
					Easy_Support_Videos_View.ajax.queue.processing = ( status ) ? true : false;

					// Return this for chaining
					return this;
				}
			},
			/**
			 * This function sets up AJAX data.
			 */
			setupData: function( data, action, $el ) {
				// Defaults
				action = action || false;
				$el = $el || false;

				// Trigger the setup data event on the Easy Support Videos Backbone View element
				Easy_Support_Videos_View.$el.trigger( 'esv-ajax-setup-data', [ data, action, $el ] );

				return $.extend( data, this.data );
			},
			/**
			 * This function displays an AJAX response message if it exists.
			 */
			displayResponseStatusMessage: function( response ) {
				// Bail if we don't have a message or an error
				if ( ! response.message && ! response.error ) {
					return;
				}

				// Show the message
				Easy_Support_Videos_View.showMessage( ( response.message ) ? response.message : response.error );
			},
			/**
			 * This function sets an active spinner to inactive.
			 */
			setActiveSpinnerInactive: function( $spinner ) {
				// Hide the spinner
				$spinner.removeClass( easy_support_videos.spinner.active_css_classes );
			},
			/**
			 * This function sets all active spinners to inactive.
			 */
			setActiveSpinnersInactive: function() {
				var active_el_selector = '.' + easy_support_videos.spinner.active_css_classes.split( ' ' ).join( '.' );

				// Hide the spinners
				Easy_Support_Videos_View.$el.find( '.easy-support-videos-spinner' + active_el_selector ).removeClass( easy_support_videos.spinner.active_css_classes );
			},
			/**
			 * These functions run on successful AJAX requests.
			 */
			success: {
				/**
				 * This function runs on all successful AJAX requests.
				 */
				all: function( response ) {
					// Set active spinners to inactive
					// Easy_Support_Videos_View.ajax.setActiveSpinnersInactive();

					// If we have a spinner for the current item
					if ( Easy_Support_Videos_View.ajax.queue.current_item.spinner ) {
						// Set the current active spinner to inactive
						Easy_Support_Videos_View.ajax.setActiveSpinnerInactive( Easy_Support_Videos_View.ajax.queue.current_item.spinner );
					}

					// Display success message
					Easy_Support_Videos_View.ajax.displayResponseStatusMessage( response );

					// Trigger the "all" event on the Easy Support Videos Backbone View element
					Easy_Support_Videos_View.$el.trigger( 'esv-ajax-success-all', response );

					// Process the AJAX queue
					Easy_Support_Videos_View.ajax.queue.setProcessingStatusFlag( false ).process();
				},
				/**
				 * This function runs on a successful insert video AJAX request.
				 */
				insertVideo: function( response ) {
					var $easy_support_videos_videos = Easy_Support_Videos_View.$el.find( Easy_Support_Videos_View.videos_el_selector ),
						$single_easy_support_videos = $easy_support_videos_videos.find( '.easy-support-video' ),
						$video_url = Easy_Support_Videos_View.$el.find( '#easy-support-videos-insert-video-url' );

					// If we have existing Easy Support Videos
					if ( $single_easy_support_videos.length ) {
						$single_easy_support_videos.first().before( Easy_Support_Videos_View.template( response ) );
					}
					// Otherwise we have to append the Easy Support Video to the videos container and remove the no videos message
					else {
						$easy_support_videos_videos.append( Easy_Support_Videos_View.template( response ) ).find( '#easy-support-videos-no-videos-message' ).remove();
					}

					// Empty the video URL field
					$video_url.val( '' );

					// Re-initialize FitVids
					$easy_support_videos_videos.fitVids();

					// Trigger the an event on the Easy Support Videos Backbone View element
					Easy_Support_Videos_View.$el.trigger( 'esv-ajax-success-insert-video', response );

					// Call the "all" success function
					Easy_Support_Videos_View.ajax.success.all( response );
				},
				/**
				 * This function runs on a successful edit video AJAX request.
				 */
				editVideo: function( response ) {
					// Update the current value data
					Easy_Support_Videos_View.$el.find( '.easy-support-video[data-post-id="' + response.post_id + '"] .easy-support-videos-video-title' ).data( Easy_Support_Videos_View.current_value_data_key, response.title );

					// Trigger the an event on the Easy Support Videos Backbone View element
					Easy_Support_Videos_View.$el.trigger( 'esv-ajax-success-edit-video', response );

					// Call the "all" success function
					Easy_Support_Videos_View.ajax.success.all( response );
				},
				/**
				 * This function runs on a successful delete video AJAX request.
				 */
				deleteVideo: function( response ) {
					// Remove the video
					Easy_Support_Videos_View.$el.find( '.easy-support-video[data-post-id="' + response.post_id + '"]' ).remove();

					// Trigger the an event on the Easy Support Videos Backbone View element
					Easy_Support_Videos_View.$el.trigger( 'esv-ajax-success-delete-video', response );

					// Call the "all" success function
					Easy_Support_Videos_View.ajax.success.all( response );
				},
				/**
				 * This function runs on a successful edit sidebar message AJAX request.
				 */
				editSidebarMessage: function( response ) {
					// Update the current value data
					Easy_Support_Videos_View.$el.find( '#easy-support-videos-option-sidebar-message' ).data( Easy_Support_Videos_View.current_value_data_key, response.value );

					// Trigger the an event on the Easy Support Videos Backbone View element
					Easy_Support_Videos_View.$el.trigger( 'esv-ajax-success-edit-sidebar-message', response );

					// Call the "all" success function
					Easy_Support_Videos_View.ajax.success.all( response );
				},
			},
			/**
			 * These functions run on a failed AJAX requests.
			 */
			fail:  {
				/**
				 * This function runs on all failed AJAX requests.
				 */
				all: function( response ) {
					// Set active spinners to inactive
					//Easy_Support_Videos_View.ajax.setActiveSpinnersInactive();

					// If we have a spinner for the current item
					if ( Easy_Support_Videos_View.ajax.queue.current_item.spinner ) {
						// Set the current active spinner to inactive
						Easy_Support_Videos_View.ajax.setActiveSpinnerInactive( Easy_Support_Videos_View.ajax.queue.current_item.spinner );
					}
					// Display fail message
					Easy_Support_Videos_View.ajax.displayResponseStatusMessage( response );

					// Trigger the "all" event on the Easy Support Videos Backbone View element
					Easy_Support_Videos_View.$el.trigger( 'esv-ajax-fail-all', response );

					// Process the AJAX queue
					Easy_Support_Videos_View.ajax.queue.setProcessingStatusFlag( false ).process();
				},
				/**
				 * This function runs on a failed insert video AJAX request.
				 */
				insertVideo: function( response ) {
					// Trigger an event on the Easy Support Videos Backbone View element
					Easy_Support_Videos_View.$el.trigger( 'esv-ajax-fail-insert-video', response );

					// Call the "all" fail function
					Easy_Support_Videos_View.ajax.fail.all( response );
				},
				/**
				 * This function runs on a failed edit video AJAX request.
				 */
				editVideo: function( response ) {
					// Trigger an event on the Easy Support Videos Backbone View element
					Easy_Support_Videos_View.$el.trigger( 'esv-ajax-fail-edit-video', response );

					// Call the "all" fail function
					Easy_Support_Videos_View.ajax.fail.all( response );
				},
				/**
				 * This function runs on a failed delete video AJAX request.
				 */
				deleteVideo: function( response ) {
					// Trigger an event on the Easy Support Videos Backbone View element
					Easy_Support_Videos_View.$el.trigger( 'esv-ajax-fail-delete-video', response );

					// Call the "all" fail function
					Easy_Support_Videos_View.ajax.fail.all( response );
				}
			}
		},
		/**
		 * This function runs on initialization of the view.
		 */
		initialize: function() {
			var self = this,
				$sidebar_message = this.$el.find( '#easy-support-videos-option-sidebar-message' );

			// Bind this to functions
			_.bindAll(
				this,
				'insertVideo',
				'editVideo',
				'deleteVideo',
				'hideMessage',
				'editSidebarMessage'
			);

			// If the current user can edit
			if ( easy_support_videos.current_user_can.edit ) {
				// Loop through each title input
				this.$el.find( '.easy-support-videos-video-title' ).each( function() {
					var $this = $( this );

					// Setup the current value data
					$this.data( self.current_value_data_key, $this.val() );
				} );

				// Setup the Easy Support Videos sidebar message current value data
				$sidebar_message.data( self.current_value_data_key, $sidebar_message.val() );
			}
		},
		/**
		 * This function inserts Easy Support Videos.
		 */
		insertVideo: function( event ) {
			// Bail if this was a keypress event but not the enter key
			if ( event.type === 'keypress' && event.which !== 13 ) {
				return;
			}

			var self = this,
				$video_url = this.$el.find( '#easy-support-videos-insert-video-url' ),
				video_url = $video_url.val(),
				$spinner = this.$el.find( '#easy-support-videos-insert-video-spinner' ),
				data = this.ajax.setupData( {
					nonce: this.$el.find( '#easy_support_videos_nonce_insert' ).val(),
					url: video_url
				}, easy_support_videos.actions.insert );

			// Prevent default
			event.preventDefault();

			// Bail if the video URL is empty
			if ( ! video_url ) {
				// Show a message
				this.showMessage( easy_support_videos.l10n.video_url_empty );

				return;
			}

			// Show the spinner
			$spinner.addClass( easy_support_videos.spinner.active_css_classes );

			// Add item to the AJAX queue and process
			this.ajax.queue.addItem( {
				action: easy_support_videos.actions.insert,
				data: data,
				success: this.ajax.success.insertVideo,
				fail: this.ajax.fail.insertVideo,
				spinner: $spinner
			} ).process();
		},
		/**
		 * This function edits Easy Support Videos (delay 500ms).
		 */
		editVideo: _.debounce( function( event, $this, force ) {
			// Defaults
			$this = $this || $( event.currentTarget );
			force = force || false;

			var self = this,
				$title = ( ! $this.hasClass( 'easy-support-videos-video-title' ) ) ? $this.find( '.easy-support-videos-video-title' ) : $this,
				current_value = $title.val(),
				previous_value = $title.data( this.current_value_data_key ),
				$parent = ( $this.hasClass( 'easy-support-videos-video-title' ) ) ? $this.parent() : $this,
				$post_id = $parent.find( '.easy-support-videos-video-id' ),
				post_id = $post_id.val(),
				$spinner = $parent.find( '.easy-support-videos-video-title-spinner' ),
				data = this.ajax.setupData( {
					nonce: this.$el.find( '#easy_support_videos_nonce_edit' ).val(),
					post_id: post_id,
					title: current_value
				}, easy_support_videos.actions.edit, $this );

			// Prevent default
			event.preventDefault();

			// Bail if the current value matches the previous value
			if ( ! force && current_value === previous_value ) {
				return;
			}

			// Bail if the current value is empty
			if ( ! current_value ) {
				// Set the title back to the previous value
				$title.val( previous_value );

				// Show a message
				this.showMessage( easy_support_videos.l10n.video_title_empty );

				return;
			}

			// Show the spinner
			$spinner.addClass( easy_support_videos.spinner.active_css_classes );

			// Add item to the AJAX queue and process
			this.ajax.queue.addItem( {
				action: easy_support_videos.actions.edit,
				data: data,
				success: this.ajax.success.editVideo,
				fail: this.ajax.fail.editVideo,
				spinner: $spinner,
				abort: true
			} ).process();
		}, 500 ),
		/**
		 * This function deletes Easy Support Videos.
		 */
		deleteVideo: function( event, $this ) {
			// Defaults
			$this = $this || $( event.currentTarget );

			var self = this,
				$post_id = $this.parent().find( '.easy-support-videos-video-id' ),
				post_id = $post_id.val(),
				$easy_support_video = $this.parents( '.easy-support-video' ),
				$spinner = $this.parents( '.easy-support-video' ).find( '.easy-support-videos-video-spinner' ),
				data = this.ajax.setupData( {
					nonce: this.$el.find( '#easy_support_videos_nonce_delete' ).val(),
					post_id: post_id
				}, easy_support_videos.actions.delete, $this );

			// Prevent default
			event.preventDefault();

			// Show the spinner and add the active CSS classes to the video container
			$spinner.add( $easy_support_video ).addClass( easy_support_videos.spinner.active_css_classes );

			// Add item to the AJAX queue and process
			this.ajax.queue.addItem( {
				action: easy_support_videos.actions.delete,
				data: data,
				success: this.ajax.success.deleteVideo,
				fail: this.ajax.fail.deleteVideo,
				spinner: $spinner
			} ).process();
		},
		/**
		 * This function shows Easy Support Videos messages.
		 */
		showMessage: function( message ) {
			var self = this,
				message_timer_delay = this.message_timer_delay,
				$easy_support_videos_status_message = this.$el.find( '#easy-support-videos-status-message' ),
				$easy_support_videos_status_message_message = $easy_support_videos_status_message.find( '.easy-support-videos-message' );

			// Set the message
			$easy_support_videos_status_message_message.html( message );

			// Show the message
			$easy_support_videos_status_message.addClass( easy_support_videos.message.active_css_classes );

			// Determine the message timer delay
			message_timer_delay += ( message.length > 160 ) ? this.message_timer_delay : 0;
			message_timer_delay += ( message.length > 20 && message.length <= 160 ) ? ( 5000 * ( ( message.length - 20 ) / 140 ) ) : 0;

			// Start the timer
			this.message_timer = setTimeout( function() {
				// Hide the message
				self.hideMessage( false );
			}, message_timer_delay );
		},
		/**
		 * This function hides Easy Support Videos messages.
		 */
		hideMessage: function( event ) {
			var has_event = ( event && ! _.isEmpty( event ) ),
				$easy_support_videos_status_message = this.$el.find( '#easy-support-videos-status-message' );

			// If we have an event
			if ( has_event ) {
				// Prevent default
				event.preventDefault();
			}

			// Hide the message
			$easy_support_videos_status_message.removeClass( easy_support_videos.message.active_css_classes );

			// Stop the timer
			clearTimeout( this.message_timer );
		},
		/**
		 * This function edits the Easy Support Videos sidebar message(delay 500ms).
		 */
		editSidebarMessage: _.debounce( function( event, $this ) {
			// Defaults
			$this = $this || $( event.currentTarget );

			var self = this,
				current_value = $this.val(),
				previous_value = $this.data( self.current_value_data_key ),
				$parent = $this.parents( '.easy-support-videos-sidebar-item-message' ),
				$option_name = $parent.find( '#easy-support-videos-sidebar-message-option-name' ),
				option_name = $option_name.val(),
				$option_group = $parent.find( '#easy-support-videos-sidebar-message-option-group' ),
				option_group = $option_group.val(),
				$spinner = $parent.find( '#easy-support-videos-video-sidebar-message-spinner' ),
				data = this.ajax.setupData( {
					nonce: this.$el.find( '#easy_support_videos_nonce_save_option' ).val(),
					option_name: option_name,
					option_group: option_group,
					value: current_value
				}, easy_support_videos.actions.save_option, $this );

			// Prevent default
			event.preventDefault();

			// Bail if the current value matches the previous value
			if ( current_value === previous_value ) {
				return;
			}

			// Show the spinner
			$spinner.addClass( easy_support_videos.spinner.active_css_classes );

			// Add item to the AJAX queue and process
			this.ajax.queue.addItem( {
				action: easy_support_videos.actions.save_option,
				data: data,
				success: this.ajax.success.editSidebarMessage,
				fail: this.ajax.fail.all,
				spinner: $spinner,
				abort: {
					option_name: data.option_name,
					option_group: data.option_group
				}
			} ).process();
		}, 500 )
	} );

	/**
	 * Document Ready
	 */
	$( function() {
		/**
		 * Backbone Views
		 */
		// Create a new instance of the Easy Support Videos Backbone View
		Easy_Support_Videos_View = new easy_support_videos.Backbone.Views.Easy_Support_Videos();
		easy_support_videos.Backbone.instances.views.easy_support_videos = Easy_Support_Videos_View;

		// FitVids
		$( Easy_Support_Videos_View.videos_el_selector ).fitVids();
	} );
} )( jQuery );