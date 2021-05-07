<?php
/**
 * Easy Support Videos Options
 *
 * @class Easy_Support_Videos_Options
 * @author Slocum Studio
 * @version 2.0.0
 * @since 1.0.0
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'Easy_Support_Videos_Options' ) ) {
	final class Easy_Support_Videos_Options {
		/**
		 * @var string
		 */
		public $version = '2.0.0';

		/**
		 * @var string
		 */
		public static $option_name = 'easy_support_videos';

		/**
		 * @var Easy_Support_Videos_Options, Instance of the class
		 */
		protected static $_instance;

		/**
		 * Function used to create instance of class.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) )
				self::$_instance = new self();

			return self::$_instance;
		}


		/**
		 * This function sets up all of the actions and filters on instance. It also loads (includes)
		 * the required files and assets.
		 */
		function __construct() {
			// Hooks
			register_activation_hook( Easy_Support_Videos::plugin_file(), array( $this, 'activate' ) ); // Activate
			add_filter( 'sanitize_option_' . self::$option_name, array( $this, 'sanitize_option' ) ); // Sanitize Easy Support Videos Option

			// AJAX
			add_action( 'wp_ajax_easy_support_videos_save_option', array( $this, 'wp_ajax_easy_support_videos_save_option' ) ); // Easy Support Videos Save Option
		}

		/**
		 * This function runs on plugin activation.
		 */
		public function activate() {
			// If the Easy Support Videos option doesn't exist
			if ( ! ( $easy_support_videos_option = get_option( self::$option_name ) ) )
				// Add it now
				add_option( self::$option_name, self::get_options_defaults() );
		}

		/**
		 * This function sanitizes the option values before they are stored in the database.
		 */
		public function sanitize_option( $value ) {
			// Grab the raw value before sanitizing
			$raw_value = $value;

			// Grab the Easy Support Videos options
			$easy_support_videos_options = self::get_options();

			// Grab Easy Support Videos option defaults
			$easy_support_videos_options_defaults = self::get_options_defaults();

			/*
			 * Merge the option values with the defaults
			 */
			$easy_support_videos_options['roles'] = wp_parse_args( $easy_support_videos_options['roles'], $easy_support_videos_options_defaults['roles'] ); // Roles
			$easy_support_videos_options['sidebar'] = wp_parse_args( $easy_support_videos_options['sidebar'], $easy_support_videos_options_defaults['sidebar'] ); // Sidebar
			$easy_support_videos_options['uninstall'] = wp_parse_args( $easy_support_videos_options['uninstall'], $easy_support_videos_options_defaults['uninstall'] ); // Uninstall

			/*
			 * Roles
			 */

			// Reset to Defaults
			if ( isset( $value['reset'] ) ) {
				$value['roles']['edit'] = $easy_support_videos_options_defaults['roles']['edit']; // Roles - Edit
				$value['roles']['read'] = $easy_support_videos_options_defaults['roles']['read']; // Roles - Read
			}
			// Otherwise use the POSTed data or existing data
			else {
				$value['roles']['edit'] = ( isset( $value['roles']['edit'] ) ) ? sanitize_text_field( $value['roles']['edit'] ) : $easy_support_videos_options['roles']['edit']; // Roles - Edit
				$value['roles']['read'] = ( isset( $value['roles']['read'] ) ) ? sanitize_text_field( $value['roles']['read'] ) : $easy_support_videos_options['roles']['read']; // Roles - Read
			}

			// If the specified roles do not match the defaults
			if ( ! isset( $value['reset'] ) && $value['roles']['edit'] !== $easy_support_videos_options_defaults['roles']['edit'] || $value['roles']['read'] !== $easy_support_videos_options_defaults['roles']['read'] ) {
				// Grab the global WP_Roles instance
				$wp_roles = wp_roles();

				// Grab all editable roles
				$editable_roles = get_editable_roles();

				// If we have roles
				if ( ! empty( $editable_roles ) ) {
					// Flags
					$is_edit_cap_valid = ( $value['roles']['edit'] !== $easy_support_videos_options_defaults['roles']['edit'] );
					$is_read_cap_valid = ( $value['roles']['read'] !== $easy_support_videos_options_defaults['roles']['read'] );

					// Loop through roles
					foreach ( array_keys( $editable_roles ) as $role ) {
						// Grab the WP_Role
						$wp_role = $wp_roles->get_role( $role );

						// Check if this role has the specified edit capability
						if ( ! $is_edit_cap_valid && $wp_role->has_cap( $value['roles']['edit'] ) )
							// Set the flag
							$is_edit_cap_valid = true;

						// Check if this role has the specified read capability
						if ( ! $is_read_cap_valid && $wp_role->has_cap( $value['roles']['read'] ) )
							// Set the flag
							$is_read_cap_valid = true;

						// Bail if both role capabilities are valid
						if ( $is_edit_cap_valid && $is_read_cap_valid )
							break;
					}

					// If the edit capability isn't valid
					if ( ! $is_edit_cap_valid )
						// Set the capability to the default
						$value['roles']['edit'] = $easy_support_videos_options_defaults['roles']['edit'];

					// If the read capability isn't valid
					if ( ! $is_read_cap_valid )
						// Set the capability to the default
						$value['roles']['read'] = $easy_support_videos_options_defaults['roles']['read'];
				}
			}


			/*
			 * Sidebar
			 */

			$value['sidebar']['message'] = ( isset( $value['sidebar']['message'] ) ) ? sanitize_textarea_field( $value['sidebar']['message'] ) : $easy_support_videos_options['sidebar']['message']; // Sidebar Message


			/*
			 * Uninstall
			 */

			// Reset to Defaults
			if ( isset( $value['reset'] ) )
				$value['uninstall']['data'] = $easy_support_videos_options_defaults['uninstall']['data']; // Uninstall - Data
			// Otherwise use the POSTed data or existing data
			else
				$value['uninstall']['data'] = ( isset( $value['easy-support-videos-options-page'] ) && isset( $value['uninstall']['data'] ) ) ? $easy_support_videos_options_defaults['uninstall']['data'] : ( ( ! isset( $value['easy-support-videos-options-page'] ) ) ? $easy_support_videos_options['uninstall']['data'] : false ); // Uninstall - Data

			$value = apply_filters( 'easy_support_videos_options_sanitize_option', $value, $raw_value, $easy_support_videos_options, $easy_support_videos_options_defaults, $this );

			// Remove the reset data
			if ( isset( $value['reset'] ) )
				unset( $value['reset'] );

			// Remove the options page data
			if ( isset( $value['easy-support-videos-options-page'] ) )
				unset( $value['easy-support-videos-options-page'] );

			return $value;
		}


		/********
		 * AJAX *
		 ********/

		/**
		 * This function handles the AJAX request for saving single Easy Support Videos options. Option values
		 * will be sanitized in Easy_Support_Videos_Options::sanitize_option().
		 */
		public function wp_ajax_easy_support_videos_save_option() {
			// Status
			$status = array();

			// Generic error message
			$error = apply_filters( 'wp_ajax_easy_support_videos_save_option_error_message', __( 'There was an error saving. Please try again.', 'easy-support-videos' ), $this );

			// Check AJAX referrer
			if ( ! check_ajax_referer( 'easy_support_videos_save_option', 'nonce', false ) ) {
				$status['error'] = $error;
				wp_send_json_error( $status );
			}

			// Return an error if the current user can't publish Easy Support Videos
			if ( ! Easy_Support_Videos_Post_Types::current_user_can( Easy_Support_Videos_Post_Types::$easy_support_videos_post_type, 'publish_posts' ) ) {
				$status['error'] = apply_filters( 'wp_ajax_easy_support_videos_insert_permissions_error_message', __( 'You do not have sufficient permissions for Easy Support Videos on this site.', 'easy-support-videos' ), $this );
				wp_send_json_error( $status );
			}

			// Grab the option name
			$option_name = sanitize_text_field( $_POST['option_name'] );

			// Grab the option group
			$option_group = ( isset( $_POST['option_group'] ) ) ? sanitize_text_field( $_POST['option_group'] ) : false;

			// Grab the option value (values will be sanitized in Easy_Support_Videos_Options::sanitize_option())
			$value = $_POST['value'];

			// Grab the current option values
			$easy_support_videos_options = self::get_options();

			// Return an error if there was no option name passed
			if ( empty( $option_name ) ) {
				$status['error'] = $error;
				wp_send_json_error( $status );
			}
			// Otherwise return an error if the option name and/or option group doesn't exist
			else {
				// If we have an option group
				if ( ! empty( $option_group ) ) {
					// If the option group doesn't exist
					if ( ! array_key_exists( $option_group, $easy_support_videos_options ) ) {
						$status['error'] = $error;
						wp_send_json_error( $status );
					}
					// Otherwise if the option name doesn't exist within the option group
					else if ( ! array_key_exists( $option_name, $easy_support_videos_options[$option_group] ) ) {
						$status['error'] = $error;
						wp_send_json_error( $status );
					}
				}
				// Otherwise if the option name doesn't exist
				else if ( ! array_key_exists( $option_name, $easy_support_videos_options ) ) {
					$status['error'] = $error;
					wp_send_json_error( $status );
				}
			}

			// Generic success message
			$message = __( 'Saved.', 'easy-support-videos' );

			// Sanitized value
			$sanitized_value = false;

			// If we have an option group
			if ( ! empty( $option_group ) ) {
				// Switch based on option group
				switch ( $option_group ) {
					// Sidebar
					case 'sidebar':
						// Switch based on option name
						switch ( $option_name ) {
							// Message
							case 'message':
								// Grab the sanitized value
								$sanitized_value = stripslashes( sanitize_textarea_field( $value ) );

								// Update the success message
								$message = __( 'Sidebar message saved.', 'easy-support-videos' );
							break;
						}
					break;
				}

				// Store the new option value
				$easy_support_videos_options[$option_group][$option_name] = $value;
			}
			// Otherwise we just have an option name
			else
				// Store the new option value
				$easy_support_videos_options[$option_name] = $value;

			// Save Easy Support Videos option
			$easy_support_videos_options = apply_filters( 'wp_ajax_easy_support_videos_save_option_options', $easy_support_videos_options, $sanitized_value, $value, $option_name, $option_group, $easy_support_videos_options, $this );
			update_option( self::$option_name, $easy_support_videos_options );

			// Sanitized value
			$sanitized_value = apply_filters( 'wp_ajax_easy_support_videos_save_option_sanitized_value', $sanitized_value, $value, $option_name, $option_group, $easy_support_videos_options, $this );

			// Message
			$message = apply_filters( 'wp_ajax_easy_support_videos_save_option_message', $message, $sanitized_value, $value, $option_name, $option_group, $easy_support_videos_options, $this );

			// Update the status data
			$status['message'] = $message;
			$status['value'] = ( $sanitized_value ) ? $sanitized_value : $value;
			$status['type'] = 'option';
			$status['option_group'] = $option_group;
			$status['option_name'] = $option_name;
			$status = apply_filters( 'wp_ajax_easy_support_videos_save_option_success_status', $status, $message, $sanitized_value, $value, $option_name, $option_group, $easy_support_videos_options, $this );

			// Success
			wp_send_json_success( $status );
		}


		/********************
		 * Helper Functions *
		 ********************/

		/**
		 * This function returns the current option values for Easy Support Videos.
		 */
		public static function get_options( $option_name = false ) {
			// If an option name is passed, return that value otherwise default to Easy Support Videos options
			if ( $option_name )
				return apply_filters( 'easy_support_videos_options_' . $option_name, wp_parse_args( get_option( $option_name ), Easy_Support_Videos_Options::get_options_defaults( $option_name ) ), $option_name );

			return apply_filters( 'easy_support_videos_options', wp_parse_args( get_option( Easy_Support_Videos_Options::$option_name ), Easy_Support_Videos_Options::get_options_defaults() ), Easy_Support_Videos_Options::get_options_defaults() );
		}

		/**
		 * This function returns the default option values for Easy Support Videos.
		 */
		public static function get_options_defaults( $option_name = false ) {
			$defaults = false;

			// If an option name is passed, return that value otherwise default to Easy Support Videos options
			if ( $option_name )
				$defaults = apply_filters( 'easy_support_videos_options_defaults_' . $option_name, $defaults, $option_name );
			else {
				$defaults = array(
					// Roles
					'roles' => array(
						// Edit
						'edit' => 'manage_options',
						// Read
						'read' => 'edit_posts'
					),
					// Sidebar
					'sidebar' => array(
						// Message
						'message' => __( 'Thanks for using Easy Support Videos. Click here to edit this message.', 'easy-support-videos' )
					),
					// Uninstall
					'uninstall' => array(
						'data' => true // Should Easy Support Videos data be removed upon uninstall?
					)
				);
			}

			return apply_filters( 'easy_support_videos_options_defaults', $defaults, $option_name );
		}
	}

	/**
	 * Create an instance of the Easy_Support_Videos_Options class.
	 */
	function Easy_Support_Videos_Options() {
		return Easy_Support_Videos_Options::instance();
	}

	Easy_Support_Videos_Options();
}