<?php
/**
 * Easy Support Videos Admin Views (controller)
 *
 * @class Easy_Support_Videos_Admin_Views
 * @author Slocum Studio
 * @version 1.0.0
 * @since 1.0.0
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if( ! class_exists( 'Easy_Support_Videos_Admin_Views' ) ) {
	final class Easy_Support_Videos_Admin_Views {
		/**
		 * @var string
		 */
		public $version = '1.0.0';

		/**
		 * @var array
		 */
		public static $options = false;
		/**
		 * @var Easy_Support_Videos_Admin_Views, Instance of the class
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
			add_action( 'admin_footer', array( $this, 'admin_footer' ) ); // Admin Footer
		}

		/**
		 * This function outputs scripts in the admin footer.
		 */
		public function admin_footer() {
			// If the current user can edit Easy Support Videos
			if ( Easy_Support_Videos_Post_Types::current_user_can( Easy_Support_Videos_Post_Types::$easy_support_videos_post_type, 'edit_posts' ) )
				// UnderscoreJS Video Template
				self::js_video_template();
		}

		/**
		 * This function renders the Easy Support Videos admin page.
		 */
		public static function render() {
			// Render the main view
			self::load_template( 'html-easy-support-videos.php' );
		}

		/**
		 * This function renders the Easy Support Videos Page Title template part.
		 */
		public static function page_title_template() {
			self::load_template( 'html-easy-support-videos-page-title.php' );
		}

		/**
		 * This function renders the Easy Support Videos Sidebar template part.
		 */
		public static function sidebar() {
			self::load_template( 'html-easy-support-videos-sidebar.php' );
		}

		/**
		 * This function renders the Easy Support Videos Sidebar Item Message template part.
		 */
		public static function sidebar_item_message() {
			self::load_template( 'html-easy-support-videos-sidebar-item-message.php' );
		}

		/**
		 * This function renders the Easy Support Videos Sidebar Item Rate template part.
		 */
		public static function sidebar_item_rate() {
			self::load_template( 'html-easy-support-videos-sidebar-item-rate.php' );
		}

		/**
		 * This function renders the Easy Support Videos Insert Video template part.
		 */
		public static function insert_video_template() {
			self::load_template( 'html-easy-support-videos-insert-video.php' );
		}

		/**
		 * This function renders the Easy Support Videos Video template part.
		 */
		public static function videos_template() {
			self::load_template( 'html-easy-support-videos-videos.php' );
		}

		/**
		 * This function renders the Easy Support Videos Video UnderscoreJS template.
		 */
		public static function js_video_template() {
			self::load_template( 'js-easy-support-videos-video.php' );
		}


		/****************
		 * Settings API *
		 ****************/

		/**
		 * This function renders the Easy Support Videos admin page.
		 */
		public static function easy_support_videos_options_render() {
			self::load_template( 'html-easy-support-videos-options.php' );
		}

		/**
		 * This function renders the Easy Support Videos Options Roles Section.
		 */
		public static function easy_support_videos_options_roles_section() {
			self::load_template( 'html-easy-support-videos-options-roles-section.php' );
		}

		/**
		 * This function renders the Easy Support Videos Options Roles Edit Field.
		 */
		public static function easy_support_videos_options_roles_edit_field() {
			self::load_template( 'html-easy-support-videos-options-roles-edit-field.php' );
		}

		/**
		 * This function renders the Easy Support Videos Options Roles Read Field.
		 */
		public static function easy_support_videos_options_roles_read_field() {
			self::load_template( 'html-easy-support-videos-options-roles-read-field.php' );
		}

		/**
		 * This function renders the Easy Support Videos Options Uninstall Section.
		 */
		public static function easy_support_videos_options_uninstall_section() {
			self::load_template( 'html-easy-support-videos-options-uninstall-section.php' );
		}

		/**
		 * This function renders the Easy Support Videos Options Uninstall DataField.
		 */
		public static function easy_support_videos_options_uninstall_data_field() {
			self::load_template( 'html-easy-support-videos-options-uninstall-data-field.php' );
		}


		/********************
		 * Helper Functions *
		 ********************/

		/**
		 * This function loads a template.
		 */
		public static function load_template( $template, $require_once = false ) {
			// Template
			$the_template = '';

			// If the fallback template exists
			if ( file_exists( Easy_Support_Videos::plugin_dir() . '/includes/admin/views/' . $template ) )
				$the_template = Easy_Support_Videos::plugin_dir() . '/includes/admin/views/' . $template;

			$the_template = apply_filters( 'easy_support_videos_admin_views_load_template', $the_template, $template, $require_once );

			// If we have a template
			if ( $the_template )
				// If this template should be required once
				if ( $require_once )
					require_once $the_template;
				// Otherwise just include the template
				else
					include_once $the_template;
		}
	}

	/**
	 * Create an instance of the Easy_Support_Videos_Admin_Views class.
	 */
	function Easy_Support_Videos_Admin_Views() {
		return Easy_Support_Videos_Admin_Views::instance();
	}

	Easy_Support_Videos_Admin_Views();
}