/**
 * Easy Support Videos Admin
 */
var easy_support_videos = easy_support_videos || {};

// TODO: Future: this vs global variables (normalize in logic)

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
			save_option: 'easy_support_videos_save_option',
			contextual_videos_set_global_video: 'easy_support_videos_contextual_videos_set_global_video'
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
		current_message_timer_delay: 0,
		events: {
			// Preview Button Click
			'click #easy-support-videos-pro-preview-button': 'maybePreviewVideos',
			// Insert Video
			'click #easy-support-videos-insert-video-button': 'insertVideo',
			'keypress #easy-support-videos-insert-video-url': 'insertVideo',
			// Edit Video Title
			'input .easy-support-videos-video-title': 'editVideo',
			// Edit Video Excerpt
			'input .easy-support-videos-video-excerpt': 'editVideoExcerpt',
			// Delete Video
			'click .easy-support-videos-video-delete': 'deleteVideo',
			// Easy Support Videos AJAX Processing
			'esv-ajax-processing': function( event, processing, force ) {
				this.ajax.events.esvAjaxProcessing( event, processing, force );
			},
			// Close Message
			'click .easy-support-videos-status-message-close': 'hideMessage',
			// Edit Sidebar Message
			'input #easy-support-videos-option-sidebar-message': 'editSidebarMessage',
			// Contextual Videos - Set Global Video Toggle Change
			'change .easy-support-videos-contextual-videos-set-global-video': 'setContextualVideosGlobalVideo',
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
			/*
			 * Events
			 */
			events: {
				/**
				 * This function runs when the Easy Support Videos AJAX queue is processing.
				 */
				esvAjaxProcessing: function( event, processing, force ) {
					var $preview_button = Easy_Support_Videos_View.$el.find( '#easy-support-videos-preview-button' ),
						$spinner = Easy_Support_Videos_View.$el.find( '#easy-support-videos-video-preview-spinner' );

					// If we're processing
					if ( processing ) {
						// Show the preview spinner
						$spinner.addClass( easy_support_videos.spinner.active_css_classes );

						// Add the disabled CSS classes to the preview button
						$preview_button.addClass( 'disabled easy-support-videos-disabled' );
					}
					// Otherwise we're not processing
					else {
						// Hide the preview spinner
						$spinner.removeClass( easy_support_videos.spinner.active_css_classes );

						// Remove the disabled CSS classes to the preview button
						$preview_button.removeClass( 'disabled easy-support-videos-disabled' );
					}
				},
			},
			/**
			 * AJAX Queue
			 */
			queue: {
				processing: false,
				current_item: {},
				current_request: false,
				current_request_count: 0,
				items: [],
				/**
				 * This function adds an item to the queue for processing.
				 */
				addItem: function( item ) {
					var self = this,
						abort_current_request = false;

					// If we have a current request
					if ( this.current_request ) {
						// Grab the abort flag
						abort_current_request = this.canAbortRequest( item );

						// If we should abort the current request
						if ( abort_current_request ) {
							// Add this item to the end of the queue
							Easy_Support_Videos_View.ajax.queue.items.push( item );

							// Abort the current request
							this.current_request.abort();

							// Process the next AJAX request (force)
							this.process( true );
						}
						// Otherwise just add the item to the queue
						else {
							// Loop through the queue items
							_.each( Easy_Support_Videos_View.ajax.queue.items, function ( request_item, index ) {
								// Grab the abort flag
								abort_current_request = self.canAbortRequest( item, request_item, true );

								// If we should abort the current request
								if ( abort_current_request ) {
									// Remove this item from the queue
									Easy_Support_Videos_View.ajax.queue.items.splice( index, 1 );
								}
							} );

							// Add this item to the end of the queue
							Easy_Support_Videos_View.ajax.queue.items.push( item );
						}
					}
					// Otherwise just add the item to the queue
					else {
						// Loop through the queue items
						_.each( Easy_Support_Videos_View.ajax.queue.items, function ( request_item, index ) {
							// Grab the abort flag
							abort_current_request = self.canAbortRequest( item, request_item, true );

							// If we should abort the current request
							if ( abort_current_request ) {
								// Remove this item from the queue
								Easy_Support_Videos_View.ajax.queue.items.splice( index, 1 );
							}
						} );

						// Add this item to the end of the queue
						Easy_Support_Videos_View.ajax.queue.items.push( item );
					}

					// Return this for chaining
					return this;
				},
				/**
				 * This function determines if a request can be aborted.
				 */
				canAbortRequest: function( item, current_item, check_current_item_abort ) {
					var abort_current_request = false;

					// Defaults
					current_item = current_item || this.current_item;
					check_current_item_abort = check_current_item_abort || false;

					// If we should check the current item abort
					if ( check_current_item_abort ) {
						// If the abort property on the item is an object, the abort property on the request item is an object, and the item has the same action as the current item
						if ( _.isObject( item.abort ) && _.isObject( current_item.abort ) && item.action === current_item.action ) {
							// Set the abort flag
							abort_current_request = true;

							// Loop through each abort property (using find so we can bail if necessary)
							_.find( current_item.abort, function( value, property ) {
								// Bail if the item doesn't have this property or the value doesn't match
								if ( ! item.abort.hasOwnProperty( property ) || value !== item.abort[property] ) {

									// Reset the abort flag
									abort_current_request = false;

									return true;
								}
							} );
						}
					}
					// Otherwise we shouldn't check the current item abort
					else {
						// If this item can abort items with the same action
						if ( item.abort && ! _.isEmpty( current_item ) && item.action === current_item.action ) {
							// Set the abort flag
							abort_current_request = true;
	
							// If the abort property is an object
							if ( _.isObject( item.abort ) ) {
								// Loop through each abort property (using find so we can bail if necessary)
								_.find( item.abort, function( value, property ) {
									// Bail if the current item doesn't have this property or the value doesn't match
									if ( ! current_item.data.hasOwnProperty( property ) || value !== current_item.data[property] ) {
										// Reset the abort flag
										abort_current_request = false;
	
										return true;
									}
								} );
							}
						}
					}

					return abort_current_request;
				},
				/**
				 * This function processes the queue.
				 */
				process: function( force ) {
					// Defaults
					force = force || false;

					// Bail if we're not forcing and we're already processing or there are no items to process
					if ( ! force && ( Easy_Support_Videos_View.ajax.queue.processing || Easy_Support_Videos_View.ajax.queue.items.length === 0 ) ) {
						// If we don't have any items to process and we don't have any requests
						if ( Easy_Support_Videos_View.ajax.queue.items.length === 0 && this.current_request_count === 0 ) {
							// Reset the current request reference
							this.current_request = false;

							// Reset the current item
							this.current_item = {};

							// Set all active spinners to inactive
							Easy_Support_Videos_View.ajax.setActiveSpinnersInactive();

							// If we're processing
							if ( Easy_Support_Videos_View.ajax.queue.processing ) {
								// Reset the processing status flag
								this.setProcessingStatusFlag( false );
							}
						}

						// Trigger the processing event on the Easy Support Videos Backbone View element
						Easy_Support_Videos_View.$el.trigger( 'esv-ajax-processing', [ false, force ] );

						return;
					}

					// Bail if we're forcing, we don't have any items to process, and we don't have any requests
					if ( force && Easy_Support_Videos_View.ajax.queue.items.length === 0 && this.current_request_count === 0 ) {
						// Reset the current request reference
						this.current_request = false;

						// Reset the current item
						this.current_item = {};

						// Set all active spinners to inactive
						Easy_Support_Videos_View.ajax.setActiveSpinnersInactive();

						// If we're processing
						if ( Easy_Support_Videos_View.ajax.queue.processing ) {
							// Reset the processing status flag
							this.setProcessingStatusFlag( false );
						}

						// Trigger the processing event on the Easy Support Videos Backbone View element
						Easy_Support_Videos_View.$el.trigger( 'esv-ajax-processing', [ false, force ] );

						return;
					}

					// Trigger the processing event on the Easy Support Videos Backbone View element
					Easy_Support_Videos_View.$el.trigger( 'esv-ajax-processing', [ true, force ] );

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
				 * This function processes the current item in the queue.
				 *
				 * @uses Easy_Support_Videos_View.ajax.queue.current_item
				 */
				processCurrentItem: function() {
					var item = Easy_Support_Videos_View.ajax.queue.current_item;

					// Make the AJAX request (POST)
					this.current_request = wp.ajax.post( item.action, item.data ).done( item.success ).fail( item.fail );

					// Increase the current request count
					this.current_request_count++;

					// Return this for chaining
					return this;
				},
				/**
				 * This function sets the processing status flag.
				 */
				setProcessingStatusFlag: function( status ) {
					Easy_Support_Videos_View.ajax.queue.processing = ( status ) ? true : false;

					// Return this for chaining
					return this;
				},
				/**
				 * This function finishes processing an AJAX request.
				 */
				finishProcessingRequest: function() {
					// Decrease the current request count
					this.current_request_count--;

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
				// Bail if we don't have a message and we don't have an error or the error isn't a function
				if ( ! response.message && ( ! response.error || _.isFunction( response.error ) ) ) {
					return;
				}

				// Show the message
				Easy_Support_Videos_View.showMessage( ( response.message ) ? response.message : response.error );
			},
			/**
			 * This function sets an active spinner to inactive.
			 */
			setActiveSpinnerInactive: function( response ) {
				var spinner_css_selector = '.easy-support-videos-spinner',
					$spinner;

				// Add the type CSS selector to the spinner CSS selector
				spinner_css_selector += '[data-type="' + response.type + '"]';

				// Switch based on response type
				switch ( response.type ) {
					// Option
					case 'option':
						// If we have an option group
						if ( response.option_group ) {
							// Add the option group CSS selector to the spinner CSS selector
							spinner_css_selector += '[data-option-group="' + response.option_group + '"]';
						}

						// Add the option name CSS selector to the spinner CSS selector
						spinner_css_selector += '[data-option-name="' + response.option_name + '"]';
					break;

					// Video
					case 'video':
						// If this isn't the insert event
						if ( response.event !== 'insert' ) {
							// Add the post ID CSS selector to the spinner CSS selector
							spinner_css_selector += '[data-post-id="' + response.post_id + '"]';
						}

						// Add the event CSS selector to the spinner CSS selector
						spinner_css_selector += '[data-event="' + response.event + '"]';
					break;

					// Default
					default:
						// TODO: Future: Likely going to need to trigger an event here so Pro can hook in and create the right selector
					break;
				}

				// TODO: Future: Likely going to need to trigger an event here so Pro can hook in and create the right selector

				// Grab the spinner
				$spinner = $( spinner_css_selector );

				// If we don't have a spinner and we have a current item spinner
				if ( ! $spinner.length && Easy_Support_Videos_View.ajax.queue.current_item.spinner ) {
					// Default to the current item spinner
					$spinner = Easy_Support_Videos_View.ajax.queue.current_item.spinner
				}

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

					// Finish processing this request
					Easy_Support_Videos_View.ajax.queue.finishProcessingRequest();

					// Set the current active spinner to inactive
					Easy_Support_Videos_View.ajax.setActiveSpinnerInactive( response );

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

					// Add the "easy-support-videos-has-videos" CSS class to the Easy Support Videos view element
					Easy_Support_Videos_View.$el.addClass( 'easy-support-videos-has-videos' );

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
					var $easy_support_videos_videos = Easy_Support_Videos_View.$el.find( Easy_Support_Videos_View.videos_el_selector ),
						$single_easy_support_videos;

					// Remove the video
					Easy_Support_Videos_View.$el.find( '.easy-support-video[data-post-id="' + response.post_id + '"]' ).remove();

					// Grab the single Easy Support Videos videos
					$single_easy_support_videos = $easy_support_videos_videos.find( '.easy-support-video' );

					// If we don't have existing Easy Support Videos
					if ( ! $single_easy_support_videos.length ) {
						// Remove the "easy-support-videos-has-videos" CSS class from the Easy Support Videos view element
						Easy_Support_Videos_View.$el.removeClass( 'easy-support-videos-has-videos' );
					}

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
				/**
				 * This function runs on a successful set contextual videos global video AJAX request.
				 */
				setContextualVideosGlobalVideo: function( response ) {
					var set_global_video = response.contextual_videos.set_global_video,
						$contextual_videos_set_global_video_context_toggles = Easy_Support_Videos_View.$el.find( '.easy-support-videos-contextual-videos-set-global-video-context-toggle' ),
						$contextual_videos_set_global_video_context_toggle = $contextual_videos_set_global_video_context_toggles.filter( '[data-post-id="' + response.post_id + '"]' ),
						$other_contextual_videos_set_global_video_context_toggle_els = $contextual_videos_set_global_video_context_toggles.not( $contextual_videos_set_global_video_context_toggle );

					// If we set the global video
					if ( set_global_video ) {
						// Loop through the other contextual videos set global video context toggle elements
						$other_contextual_videos_set_global_video_context_toggle_els.each( function() {
							var $this = $( this ),
								$checkbox = $this.find( 'input[type="checkbox"]' );

							// If this contextual videos set global video context toggle is checked
							if ( $checkbox.prop( 'checked' ) ) {
								// Reset the checked property on this contextual videos set global video context toggle
								$checkbox.prop( 'checked', false );
							}
						} );
					}

					// Trigger the an event on the Easy Support Videos Backbone View element
					Easy_Support_Videos_View.$el.trigger( 'esv-ajax-success-edit-contextual-videos-set-global-video', response );

					// Call the "all" success function
					Easy_Support_Videos_View.ajax.success.all( response );
				}
			},
			/**
			 * These functions run on a failed AJAX requests.
			 */
			fail: {
				/**
				 * This function runs on all failed AJAX requests.
				 */
				all: function( response ) {
					// Set active spinners to inactive
					//Easy_Support_Videos_View.ajax.setActiveSpinnersInactive();

					// Finish processing this request
					Easy_Support_Videos_View.ajax.queue.finishProcessingRequest();

					// If the request wasn't aborted
					if ( ! response.statusText || response.statusText !== 'abort' ) {
						// Set the current active spinner to inactive
						Easy_Support_Videos_View.ajax.setActiveSpinnerInactive( response );
					}

					// Display fail message
					Easy_Support_Videos_View.ajax.displayResponseStatusMessage( response );

					// Trigger the "all" event on the Easy Support Videos Backbone View element
					Easy_Support_Videos_View.$el.trigger( 'esv-ajax-fail-all', response );

					// Process the AJAX queue
					Easy_Support_Videos_View.ajax.queue.setProcessingStatusFlag( ( response.statusText && response.statusText === 'abort' ) ).process();
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
				},
				/**
				 * This function runs on a failed set contextual videos global video AJAX request.
				 */
				setContextualVideosGlobalVideo: function( response ) {
					// Trigger an event on the Easy Support Videos Backbone View element
					Easy_Support_Videos_View.$el.trigger( 'esv-ajax-fail-edit-contextual-videos-global-video', response );

					// Call the "all" fail function
					Easy_Support_Videos_View.ajax.fail.all( response );
				},
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
				'maybePreviewVideos',
				'insertVideo',
				'editVideo',
				'deleteVideo',
				'hideMessage',
				'editSidebarMessage',
				'editVideoExcerpt',
				'setContextualVideosGlobalVideo'
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
				$sidebar_message.data( this.current_value_data_key, $sidebar_message.val() );
			}
		},
		/**
		 * This function determines if videos can be previewed.
		 */
		maybePreviewVideos: function( event ) {
			var $this = $( event.currentTarget );

			// If the preview button is disabled
			if ( $this.hasClass( 'disabled' ) ) {
				// Prevent default
				event.preventDefault();
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
				$parent = ( $this.hasClass( 'easy-support-videos-video-title' ) || $this.hasClass( 'easy-support-videos-video-excerpt' ) ) ? $this.parents( '.easy-support-video' ) : $this,
				$title = ( ! $this.hasClass( 'easy-support-videos-video-title' ) ) ? $parent.find( '.easy-support-videos-video-title' ) : $this,
				$excerpt = ( ! $this.hasClass( 'easy-support-videos-video-excerpt' ) ) ? $parent.find( '.easy-support-videos-video-excerpt' ) : $this,
				current_title_value = $title.val(),
				previous_title_value = $title.data( this.current_value_data_key ),
				$post_id = $parent.find( '.easy-support-videos-video-id' ),
				post_id = $post_id.val(),
				$spinner = $parent.find( '.easy-support-videos-video-title-spinner' ),
				data = this.ajax.setupData( {
					nonce: this.$el.find( '#easy_support_videos_nonce_edit' ).val(),
					post_id: post_id,
					title: current_title_value,
					post_excerpt: $excerpt.val()
				}, easy_support_videos.actions.edit, $this );

			// Prevent default
			event.preventDefault();

			// Bail if we're not forcing and the current title value matches the previous title value
			if ( ! force && current_title_value === previous_title_value ) {
				return;
			}

			// Bail if the current title value is empty
			if ( ! current_title_value ) {
				// Set the title back to the previous title value
				$title.val( previous_title_value );

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
				abort: {
					post_id: data.post_id
				}
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

			// If we have a message timer
			if ( this.message_timer !== -1 ) {
				// Stop the timer
				clearTimeout( this.message_timer );

				// Reset the current message timer delay
				this.current_message_timer_delay = 0;
			}

			// Set the message
			$easy_support_videos_status_message_message.html( message );

			// Show the message
			$easy_support_videos_status_message.addClass( easy_support_videos.message.active_css_classes );

			// Determine the message timer delay
			message_timer_delay += ( message.length > 160 ) ? this.message_timer_delay : 0;
			message_timer_delay += ( message.length > 20 && message.length <= 160 ) ? ( 5000 * ( ( message.length - 20 ) / 140 ) ) : 0;

			// Set the current message timer delay
			this.current_message_timer_delay = message_timer_delay;

			// Trigger the show message event event on the Easy Support Videos Backbone View element
			this.$el.trigger( 'esv-show-message', [ this ] );

			// Start the timer
			this.message_timer = setTimeout( function() {
				// Hide the message
				self.hideMessage( false );

				// Reset the current message timer delay
				self.current_message_timer_delay = 0;
			}, this.current_message_timer_delay );
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
		 * This function edits the Easy Support Videos sidebar message (delay 500ms).
		 */
		editSidebarMessage: _.debounce( function( event, $this ) {
			// Defaults
			$this = $this || $( event.currentTarget );

			var self = this, // TODO: Future: Remove if not necessary
				current_value = $this.val(),
				previous_value = $this.data( this.current_value_data_key ),
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
		}, 500 ),
		/**
		 * This function edits the Easy Support Videos video excerpt.
		 */
		editVideoExcerpt: function( event ) {
			var $this = $( event.currentTarget ),
				current_character_count = $this.val().length,
				$parent = $this.parents( '.easy-support-video' ),
				$easy_support_videos_video_excerpt_current_character_count = $parent.find( '.easy-support-videos-video-excerpt-current-character-count' );

			// Set the Easy Support Videos video excerpt current character count
			$easy_support_videos_video_excerpt_current_character_count.text( current_character_count );

			// Call the "editVideo" function
			this.editVideo( event, false, true );
		},
		/**
		 * This function sets the Easy Support Videos contextual videos global video.
		 */
		setContextualVideosGlobalVideo: function( event, $this ) {
			// Defaults
			$this = $this || $( event.currentTarget );

			var self = this, // TODO: Future: Remove if not necessary
				$toggle_global_video = ( ! $this.hasClass( 'easy-support-videos-contextual-videos-set-global-video' ) ) ? $this.find( '.easy-support-videos-contextual-videos-set-global-video' ) : $this,
				$parent = ( $this.hasClass( 'easy-support-videos-contextual-videos-set-global-video' ) ) ? $this.parents( '.easy-support-video' ) : $this,
				$post_id = $parent.find( '.easy-support-videos-video-id' ),
				post_id = $post_id.val(),
				$spinner = $parent.find( '.easy-support-videos-video-title-spinner' ),
				data = this.ajax.setupData( {
					nonce: this.$el.find( '#easy_support_videos_nonce_edit' ).val(),
					post_id: post_id,
					set_global_video: $toggle_global_video.prop( 'checked' ),
				}, easy_support_videos.actions.contextual_videos_set_global_video, $this );

			// Show the spinner
			$spinner.addClass( easy_support_videos.spinner.active_css_classes );

			// Add item to the AJAX queue and process
			this.ajax.queue.addItem( {
				action: easy_support_videos.actions.contextual_videos_set_global_video,
				data: data,
				success: this.ajax.success.setContextualVideosGlobalVideo,
				fail: this.ajax.fail.setContextualVideosGlobalVideo,
				spinner: $spinner,
				abort: {
					post_id: data.post_id
				}
			} ).process();
		}
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