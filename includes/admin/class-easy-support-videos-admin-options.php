<?php
/**
 * Conductor Admin Options
 *
 * @class Easy_Support_Videos_Admin_Options
 * @author Slocum Studio
 * @version 1.0.0
 * @since 1.0.0
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'Easy_Support_Videos_Admin_Options' ) ) {
	final class Easy_Support_Videos_Admin_Options {
		/**
		 * @var string
		 */
		public $version = '1.0.0';

		/**
		 * @var string
		 */
		public static $sub_menu_page = 'easy-support-videos-options';

		/**
		 * @var string
		 */
		public static $sub_menu_page_prefix = 'support-videos_page_';

		/**
		 * @var Easy_Support_Videos_Admin_Options, Instance of the class
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

			// Hooks
			add_action( 'init', array( $this, 'init' ) ); // Init
			add_action( 'admin_menu', array( $this, 'admin_menu' ) ); // Admin Menu
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) ); // Load CSS/JavaScript
			add_action( 'admin_init', array( $this, 'admin_init' ) ); // Register setting
		}

		/**
		 * Include required core files used in admin and on the front-end.
		 */
		private function includes() {
		}

		/**
		 * This function runs on initialization.
		 */
		public function init() {
			// Sub-Menu Page
			self::$sub_menu_page = apply_filters( 'easy_support_videos_admin_options_sub_menu_page', self::$sub_menu_page, $this );

			// Sub-Menu Page Prefix
			self::$sub_menu_page_prefix = apply_filters( 'easy_support_videos_admin_options_sub_menu_page_prefix', self::$sub_menu_page_prefix, $this );
		}

		/**
		 * This function creates the admin menu item.
		 */
		public function admin_menu() {
			// Easy Support Videos Admin Options Page
			self::$sub_menu_page = add_submenu_page( Easy_Support_Videos_Post_Types::get_easy_support_video_menu_page(), __( 'Options', 'easy-support-videos' ), __( 'Options', 'easy-support-videos' ), 'manage_options', self::get_sub_menu_page(), array( $this, 'render' ) );
		}

		/**
		 * This function enqueues scripts and styles on the Easy Support Videos Options admin page.
		 */
		public function admin_enqueue_scripts( $hook ) {
			// Bail if we're not on the sub-menu page
			if ( $hook !== self::get_sub_menu_page( false ) )
				return;

			// Stylesheet
			wp_enqueue_style( 'easy-support-videos-admin-options', Easy_Support_Videos::plugin_url() . '/assets/css/easy-support-videos-admin-options.css', false, Easy_Support_Videos::$version );
		}

		/**
		 * This function registers settings for Easy Support Videos via the WordPress Settings API.
		 */
		public function admin_init() {
			// Register Setting
			register_setting( Easy_Support_Videos_Options::$option_name, Easy_Support_Videos_Options::$option_name ); // Sanitize callback omitted due to sanitize_option filter in Easy_Support_Videos_Options

			// Roles
			add_settings_section( 'easy_support_videos_roles_section', __( 'Roles', 'easy-support-videos' ), array( $this, 'easy_support_videos_roles_section' ), Easy_Support_Videos_Options::$option_name . '_roles' );
			add_settings_field( 'easy_support_videos_roles_edit_field', __( 'Edit Videos', 'easy-support-videos' ), array( $this, 'easy_support_videos_roles_edit_field' ), Easy_Support_Videos_Options::$option_name . '_roles', 'easy_support_videos_roles_section' );
			add_settings_field( 'easy_support_videos_roles_read_field', __( 'View Videos', 'easy-support-videos' ), array( $this, 'easy_support_videos_roles_read_field' ), Easy_Support_Videos_Options::$option_name . '_roles', 'easy_support_videos_roles_section' );

			// Uninstall
			add_settings_section( 'easy_support_videos_uninstall_section', __( 'Uninstall', 'easy-support-videos' ), array( $this, 'easy_support_videos_uninstall_section' ), Easy_Support_Videos_Options::$option_name . '_uninstall' );
			add_settings_field( 'easy_support_videos_uninstall_data_field', __( 'Uninstall Data', 'easy-support-videos' ), array( $this, 'easy_support_videos_uninstall_data_field' ), Easy_Support_Videos_Options::$option_name . '_uninstall', 'easy_support_videos_uninstall_section' );
		}

		/**
		 * This function renders the Easy Support Videos Options page.
		 */
		public function render() {
			// Render the main view
			Easy_Support_Videos_Admin_Views::easy_support_videos_options_render();
		}

		/**
		 * This function renders the Easy Support Videos Roles Section.
		 */
		public function easy_support_videos_roles_section() {
			Easy_Support_Videos_Admin_Views::easy_support_videos_options_roles_section();
		}

		/**
		 * This function renders the Easy Support Videos Roles Edit Field.
		 */
		public function easy_support_videos_roles_edit_field() {
			Easy_Support_Videos_Admin_Views::easy_support_videos_options_roles_edit_field();
		}

		/**
		 * This function renders the Easy Support Videos Roles Read Field.
		 */
		public function easy_support_videos_roles_read_field() {
			Easy_Support_Videos_Admin_Views::easy_support_videos_options_roles_read_field();
		}

		/**
		 * This function renders the Easy Support Videos Uninstall Section.
		 */
		public function easy_support_videos_uninstall_section() {
			Easy_Support_Videos_Admin_Views::easy_support_videos_options_uninstall_section();
		}

		/**
		 * This function renders the Easy Support Videos Uninstall Data Field.
		 */
		public function easy_support_videos_uninstall_data_field() {
			Easy_Support_Videos_Admin_Views::easy_support_videos_options_uninstall_data_field();
		}


		/**********************
		 * Internal Functions *
		 **********************/

		/**
		 * This function returns the sub-menu page. The optional $strip_prefix parameter allows the prefix
		 * added by WordPress to be stripped
		 */
		public static function get_sub_menu_page( $strip_prefix = true ) {
			return apply_filters( 'easy_support_videos_admin_options_sub_menu_page', ( $strip_prefix ) ? str_replace( self::$sub_menu_page_prefix, '', self::$sub_menu_page ) : self::$sub_menu_page );
		}
	}

	/**
	 * Create an instance of the Easy_Support_Videos_Admin_Options class.
	 */
	function Easy_Support_Videos_Admin_Options() {
		return Easy_Support_Videos_Admin_Options::instance();
	}

	Easy_Support_Videos_Admin_Options();
}