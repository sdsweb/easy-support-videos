<?php
/**
 * Easy Support Videos Preview
 *
 * @class Easy_Support_Videos_Preview
 * @version 2.0.0
 * @since 2.0.0
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'Easy_Support_Videos_Preview' ) ) {
	final class Easy_Support_Videos_Preview {
		/**
		 * @var string
		 */
		public static $version = '2.0.0';

		/**
		 * @var array
		 */
		public static $url_query_argument = 'esv_preview';

		/**
		 * @var array
		 */
		public static $original_current_user_capabilities = array();

		/**
		 * @var Easy_Support_Videos_Preview, Instance of the class
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
			// Load required assets
			$this->includes();

			// Easy Support Videos Hooks
			add_filter( 'easy_support_videos_current_user_can', array( $this, 'easy_support_videos_current_user_can' ), 1, 4 ); // Easy Support Videos - Current User Can (early)
			add_action( 'easy_support_videos_page_title_before', array( $this, 'easy_support_videos_page_title_before' ) ); // Easy Support Videos - Page Title - Before

			// Easy Support Videos - Contextual Videos Hooks
			add_filter( 'easy_support_videos_contextual_videos_current_user_can_read_contextual_videos', array( $this, 'easy_support_videos_contextual_videos_current_user_can_read_contextual_videos' ), 10, 4 ); // Easy Support Videos - Contextual Videos - Current User Can Read Contextual Videos
			add_filter( 'easy_support_videos_contextual_videos_modal_actions', array( $this, 'easy_support_videos_contextual_videos_modal_actions' ) ); // Easy Support Videos - Contextual Videos - Modal Actions
		}

		/**
		 * Include required core files used in admin and on the front-end.
		 */
		private function includes() {
			// TODO
		}


		/***********************
		 * Easy Support Videos *
		 ***********************/

		/**
		 * This function adjusts the current user can Easy Support Videos capabilities.
		 */
		public function easy_support_videos_current_user_can( $current_user_can, $post_type, $capability, $post_type_capabilities ) {
			// Flag to determine if this is the Easy Support Videos post type
			$is_easy_support_videos_post_type = ( $post_type === Easy_Support_Videos_Post_Types::$easy_support_videos_post_type );

			// If this is the Easy Support Videos post type
			if ( $is_easy_support_videos_post_type )
				// Add this capability to the original current user capabilities
				self::$original_current_user_capabilities[$capability] = $current_user_can;

			// If the current user can, this is the Easy Support Videos post type, and the capability is "edit_posts" or "publish_posts"
			if ( $current_user_can && $is_easy_support_videos_post_type && in_array( $capability, array( 'edit_posts', 'publish_posts' ), true ) ) {
				// If this is a preview
				if ( self::is_preview() )
					// Reset the current user can flag
					$current_user_can = false;
			}

			return $current_user_can;
		}

		/**
		 * This function outputs content before the Easy Support Videos page title.
		 */
		public static function easy_support_videos_page_title_before() {
			global $hook_suffix;

			// If we're on the Easy Support Videos menu page
			if ( $hook_suffix === Easy_Support_Videos_Post_Types::get_easy_support_videos_menu_page( false ) )
				// Load the Easy Support Videos Preview template
				Easy_Support_Videos_Admin_Views::load_template( 'html-easy-support-videos-preview.php' );
		}


		/*******************************************
		 * Easy Support Videos - Contextual Videos *
		 *******************************************/

		/**
		 * This function adjusts whether or not the current user can read Easy Support Videos contextual videos.
		 */
		public function easy_support_videos_contextual_videos_current_user_can_read_contextual_videos( $current_user_can_read_contextual_videos, $contextual_videos_read_role, $easy_support_videos_options, $easy_support_videos_default_options ) {
			// Bail if this isn't a preview or the current user can read Easy Support Videos contextual videos
			if ( ! self::is_preview() || $current_user_can_read_contextual_videos )
				return $current_user_can_read_contextual_videos;

			// Set the current user can read contextual videos flag
			$current_user_can_read_contextual_videos = true;

			return $current_user_can_read_contextual_videos;
		}

		/**
		 * This function adjusts the Easy Support Videos contextual videos modal actions.
		 */
		public function easy_support_videos_contextual_videos_modal_actions( $actions ) {
			// Bail if we already have the Easy Support Videos preview action
			if ( isset( $actions[self::$url_query_argument] ) )
				return $actions;

			// Original current user can edit Easy Support Videos
			$original_current_user_can_edit_easy_support_videos = self::get_original_capability_for_current_user( 'edit_posts' );

			// Grab the request URL
			$request_url = remove_query_arg( wp_removable_query_args(), set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ) );

			// If the current user can edit Easy Support Videos and the Easy Support Videos Setup Wizard is not active
			if ( $original_current_user_can_edit_easy_support_videos && ! Easy_Support_Videos_Install::is_setup_wizard_active() )
				// Add the preview action to the Easy Support Videos contextual videos modal actions
				$actions[self::$url_query_argument] = array(
					'id' => self::$url_query_argument,
					'label' => ( $original_current_user_can_edit_easy_support_videos && self::is_preview() ) ? __( 'Edit Contextual Videos', 'easy-support-videos' ) : __( 'Preview as Viewer', 'easy-support-videos' ),
					'icon' => ( $original_current_user_can_edit_easy_support_videos && self::is_preview() ) ? 'dashicons-edit' : 'dashicons-visibility',
					'url' => ( $original_current_user_can_edit_easy_support_videos && self::is_preview() ) ? add_query_arg( Easy_Support_Videos_Admin_Contextual_Videos::$editing_url_query_argument, true, remove_query_arg( self::$url_query_argument, $request_url ) ) : self::get_preview_url( $request_url )
				);

			return $actions;
		}


		/********************
		 * Helper Functions *
		 ********************/

		/**
		 * This function determines if the current request is a preview.
		 */
		public static function is_preview() {
			return ( self::get_original_capability_for_current_user( 'edit_posts' ) && isset( $_GET[self::$url_query_argument] ) );
		}

		/**
		 * This function returns an original capability for the current user.
		 */
		public static function get_original_capability_for_current_user( $capability ) {
			return ( isset( self::$original_current_user_capabilities[$capability] ) ) ? self::$original_current_user_capabilities[$capability] : null;
		}

		/**
		 * This function returns a preview URL.
		 */
		public static function get_preview_url( $url = null ) {
			// Set the URL
			$url = ( is_null( $url ) ) ? add_query_arg( 'page', Easy_Support_Videos_Post_Types::get_easy_support_videos_menu_page(), admin_url( 'admin.php' ) ) : $url;

			return apply_filters( 'easy_support_videos_preview_url', add_query_arg( self::$url_query_argument, true, $url ), $url );
		}
	}

	/**
	 * Create an instance of the Easy_Support_Videos_Preview class.
	 */
	function Easy_Support_Videos_Preview() {
		return Easy_Support_Videos_Preview::instance();
	}

	Easy_Support_Videos_Preview();
}