<?php
/**
 * Easy Support Videos Install
 *
 * @class Easy_Support_Videos_Install
 * @author Slocum Studio
 * @version 2.0.0
 * @since 2.0.0
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'Easy_Support_Videos_Install' ) ) {
	final class Easy_Support_Videos_Install {
		/**
		 * @var string
		 */
		public $version = '2.0.0';

		/**
		 * @var string
		 */
		public static $option_name = 'easy_support_videos_version';

		/**
		 * @var Boolean
		 */
		public static $is_setup_wizard_enabled = null;

		/**
		 * @var Boolean
		 */
		public static $is_setup_wizard_active = null;

		/**
		 * @var string
		 */
		public static $setup_wizard_redirect_transient_name = '_esv_setup_wizard_redirect';

		/**
		 * @var string
		 */
		public static $setup_wizard_query_arg = 'esv-setup-wizard';

		/**
		 * @var string
		 */
		public static $show_setup_wizard_transient_name = '_esv_show_setup_wizard';

		/**
		 * @var Easy_Support_Videos_Install, Instance of the class
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
			add_action( 'init', array( $this, 'init' ), 5 ); // Initialization (early)
			add_action( 'current_screen', array( $this, 'current_screen' ), 5 ); // Current Screen (early; before Easy Support Videos Contextual Videos)

			// Easy Support Videos Hooks
			add_filter( 'easy_support_videos_options_sanitize_option', array( $this, 'easy_support_videos_options_sanitize_option' ), 9999, 5 ); // Easy Support Videos Options - Sanitize Option (late)
		}

		/**
		 * Include required core files used in admin and on the front-end.
		 */
		private function includes() {
			global $hook_suffix;

			// Admin Only
			if ( is_admin() ) {
				// If the Easy Support Videos setup wizard is enabled, we're on the Easy Support Videos admin options page, and we have the Easy Support Videos show setup wizard transient
				if ( self::is_setup_wizard_enabled() && $hook_suffix === Easy_Support_Videos_Admin_Options::get_sub_menu_page( false ) && get_transient( self::$show_setup_wizard_transient_name ) )
					// Include the Easy Support Videos Setup Wizard PHP class
					include_once Easy_Support_Videos::plugin_dir() . '/includes/class-easy-support-videos-setup-wizard.php';
			}
		}

		/**
		 * This function runs during initialization.
		 */
		public function init() {
			// Grab the Easy Support Videos version
			$esv_version = get_option( self::$option_name, null );

			// If this isn't an iframe request and the old Easy Support Videos version is less than the current Easy Support Videos version
			if ( ! defined( 'IFRAME_REQUEST' ) && version_compare( $esv_version, Easy_Support_Videos::$version, '<' ) ) {
				// If the Easy Support Videos setup wizard is enabled
				if ( self::is_setup_wizard_enabled() ) {
					// If this is a new Easy Support Videos install
					if ( self::is_new_install( $esv_version ) )
						// Set the Easy Support Videos setup wizard re-direct transient (expires in 5 seconds)
						set_transient( self::$setup_wizard_redirect_transient_name, get_current_user_id(), 5 );
				}

				// Hook into the "admin_head" action (late)
				add_action( 'admin_head', array( $this, 'admin_head' ), 9999 );

				do_action( 'easy_support_videos_install', $esv_version, Easy_Support_Videos::$version, $this );
			}
		}

		/**
		 * This function runs when the current screen is set.
		 */
		public function current_screen() {
			global $hook_suffix;

			// Grab the Easy Support Videos version
			$esv_version = get_option( self::$option_name, null );

			// If the Easy Support Videos setup wizard is enabled
			if ( self::is_setup_wizard_enabled() ) {
				// If we have the Easy Support Videos setup wizard re-direct transient and it matches the current logged in user ID
				if ( get_transient( self::$setup_wizard_redirect_transient_name ) === get_current_user_id() ) {
					// Flag to determine if we can re-direct to the Easy Support Videos setup wizard
					$can_redirect_to_esv_setup_wizard = true;

					// If we're doing AJAX, this is the network admin, or the current user can't view the Easy Support Videos admin options page
					if ( wp_doing_ajax() || is_network_admin() || ! current_user_can( Easy_Support_Videos_Admin_Options::$capability ) )
						// Reset the can re-direct to Easy Support Videos setup wizard flag
						$can_redirect_to_esv_setup_wizard = false;

					// If we're on the Easy Support Videos admin options page, we're activating multiple plugins, or we should prevent the Easy Support Videos setup wizard re-direct
					if ( $hook_suffix === Easy_Support_Videos_Admin_Options::get_sub_menu_page( false ) || ( isset( $_GET['activate-multi'] ) || ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'activate-selected' && isset( $_POST['checked'] ) && count( $_POST['checked'] ) > 1 ) ) || apply_filters( 'easy_support_videos_prevent_setup_wizard_redirect', false, $esv_version, Easy_Support_Videos::$version ) ) {
						// Delete the Easy Support Videos setup wizard re-direct transient
						delete_transient( self::$setup_wizard_redirect_transient_name );

						// Reset the can re-direct to Easy Support Videos setup wizard flag
						$can_redirect_to_esv_setup_wizard = false;
					}

					// If we can re-direct to the Easy Support Videos setup wizard
					if ( $can_redirect_to_esv_setup_wizard ) {
						// Delete the Easy Support Videos setup wizard re-direct transient
						delete_transient( self::$setup_wizard_redirect_transient_name );

						// Re-direct to the Easy Support Videos setup wizard
						wp_safe_redirect( add_query_arg( array(
							'page' => Easy_Support_Videos_Admin_Options::get_sub_menu_page(),
							self::$setup_wizard_query_arg => 1
						), admin_url( 'admin.php' ) ) );

						// Exit
						exit;
					}
				}

				// If the Easy Support Videos setup wizard is active
				if ( self::is_setup_wizard_active() )
					// Set the Easy Support Videos show setup wizard transient (expires in 5 seconds)
					set_transient( Easy_Support_Videos_Install::$show_setup_wizard_transient_name, 1, 5 );

			}

			// Load required assets
			$this->includes();
		}

		/**
		 * This function runs in the admin head.
		 */
		public function admin_head() {
			// Update the Easy Support Videos version
			update_option( self::$option_name, Easy_Support_Videos::$version );
		}

		/**
		 * This function adjusts the wp_redirect() URL.
		 */
		public function wp_redirect( $location, $status ) {
			// Bail if the status isn't 302
			if ( $status !== 302 )
				return $location;

			// Bail if this isn't the Easy Support Videos options re-direct
			if ( strpos( $location, Easy_Support_Videos_Admin_Options::get_sub_menu_page() ) === false )
				return $location;

			// Remove the setup wizard query argument from the location
			$location = remove_query_arg( self::$setup_wizard_query_arg, $location );

			return $location;
		}


		/***********************
		 * Easy Support Videos *
		 ***********************/

		/**
		 * This function sanitizes Easy Support Videos options.
		 */
		public function easy_support_videos_options_sanitize_option( $value, $raw_value, $easy_support_videos_options, $easy_support_videos_options_defaults, $easy_support_videos_options_class ) {
			// Hook into "wp_redirect"
			add_filter( 'wp_redirect', array( $this, 'wp_redirect' ), 10, 2 );

			return $value;
		}

		/**********************
		 * Internal Functions *
		 **********************/

		/**
		 * This function determines if this is a new Easy Support Videos install.
		 */
		public static function is_new_install( $current_esv_version = false ) {
			return ( is_null( ( ! $current_esv_version ) ? get_option( self::$option_name, null ) : $current_esv_version ) );
		}

		/**
		 * This function determines if the Easy Support Videos setup wizard is enabled.
		 */
		public static function is_setup_wizard_enabled( $current_esv_version = false ) {
			// Grab the current Easy Support Videos version
			$current_esv_version = ( ! $current_esv_version ) ? get_option( self::$option_name, null ) : $current_esv_version;

			// Bail if we have the Easy Support Videos setup wizard enabled flag
			if ( self::$is_setup_wizard_enabled !== null )
				return self::$is_setup_wizard_enabled;

			// Set the Easy Support Videos setup wizard enabled flag
			self::$is_setup_wizard_enabled = apply_filters( 'easy_support_videos_enable_setup_wizard', true, $current_esv_version, Easy_Support_Videos::$version );

			return self::$is_setup_wizard_enabled;
		}

		/**
		 * This function can be used to determine if the setup wizard is active.
		 */
		public static function is_setup_wizard_active() {
			// Bail if we have the Easy Support Videos setup wizard active flag
			if ( self::$is_setup_wizard_active !== null )
				return self::$is_setup_wizard_active;

			// Set the Easy Support Videos setup wizard active flag
			self::$is_setup_wizard_active = apply_filters( 'easy_support_videos_is_setup_wizard_active', isset( $_GET[self::$setup_wizard_query_arg] ), get_option( self::$option_name, null ), Easy_Support_Videos::$version );

			return self::$is_setup_wizard_active;
		}
	}

	/**
	 * Create an instance of the Easy_Support_Videos_Install class.
	 */
	function Easy_Support_Videos_Install() {
		return Easy_Support_Videos_Install::instance();
	}

	Easy_Support_Videos_Install();
}