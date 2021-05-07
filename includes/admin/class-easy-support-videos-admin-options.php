<?php
/**
 * Easy Support Videos Admin Options
 *
 * @class Easy_Support_Videos_Admin_Options
 * @author Slocum Studio
 * @version 2.0.0
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
		public $version = '2.0.0';

		/**
		 * @var string
		 */
		public static $sub_menu_page = 'easy-support-videos-options';

		/**
		 * @var string
		 */
		public static $sub_menu_page_prefix = 'support-videos_page_';

		/**
		 * @var string
		 */
		public static $capability = 'manage_options';

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
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) ); // Admin Enqueue Scripts
			add_action( 'admin_init', array( $this, 'admin_init' ) ); // Admin Init

			// Easy Support Videos Hooks
			add_action( 'easy_support_videos_options_notifications', array( $this, 'easy_support_videos_options_notifications' ) ); // Easy Support Videos - Options Notifications
			add_action( 'easy_support_videos_options_notifications', array( $this, 'easy_support_videos_options_notifications_setup_wizard' ), 20 ); // Easy Support Videos - Options Notifications (Setup Wizard)
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
			self::$sub_menu_page = add_submenu_page( Easy_Support_Videos_Post_Types::get_easy_support_videos_menu_page(), __( 'Options', 'easy-support-videos' ), __( 'Options', 'easy-support-videos' ), self::$capability, self::get_sub_menu_page(), array( $this, 'render' ) );
		}

		/**
		 * This function enqueues scripts and styles on the Easy Support Videos Options admin page.
		 */
		public function admin_enqueue_scripts( $hook ) {
			// Bail if we're not on the sub-menu page and we're not on the Easy Support Videos page
			if ( $hook !== self::get_sub_menu_page( false ) && $hook !== Easy_Support_Videos_Post_Types::get_easy_support_videos_menu_page( false ) )
				return;

			// Easy Support Videos Admin Options Stylesheet
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


		/***********************
		 * Easy Support Videos *
		 ***********************/

		/**
		 * This function adds Easy Support Videos options notifications.
		 */
		public function easy_support_videos_options_notifications() {
		?>
			<div class="notice easy-support-videos-options-notice easy-support-videos-options-upgrade-notice">
				<p>
					<span class="dashicons dashicons-format-video"></span>
					<?php printf( __( '<a href="%1$s" target="_blank">%2$s</a>','easy-support-videos' ), esc_url( 'https://slocumthemes.com/wordpress-plugins/easy-support-videos/?utm_source=easy-support-videos&utm_medium=link&utm_content=easy-support-videos-options-upgrade&utm_campaign=easy-support-videos' ), __( 'Upgrade for video sorting, more tabs, and white label features.', 'easy-support-videos' ) ); ?>
				</p>
			</div>
		<?php
		}

		/**
		 * This function adds the setup wizard Easy Support Videos options notifications.
		 */
		public function easy_support_videos_options_notifications_setup_wizard() {
			// If the Easy Support Videos setup wizard class doesn't exist
			if ( ! class_exists( 'Easy_Support_Videos_Setup_Wizard' ) ) :
		?>
				<div class="notice easy-support-videos-options-notice easy-support-videos-options-view-setup-wizard-notice">
					<p>
						<span class="dashicons dashicons-admin-tools"></span>
						<?php printf( __( '<a href="%1$s">%2$s</a>','easy-support-videos' ), esc_url( admin_url( sprintf( 'admin.php?page=%1$s&esv-setup-wizard=1', Easy_Support_Videos_Admin_Options::get_sub_menu_page() ) ) ), __( 'View Setup Wizard', 'easy-support-videos' ) ); ?>
					</p>
				</div>
		<?php
			endif;
		}


		/********************
		 * Helper Functions *
		 ********************/

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