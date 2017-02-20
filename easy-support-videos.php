<?php
/**
 * Plugin Name: Easy Support Videos
 * Plugin URI: https://www.slocumstudio.com/
 * Description: Easy Support Videos for embedding helpful tutorials, training videos, and screencasts in the Admin dashboard. Works with YouTube, Vimeo, Wistia, VideoPress, and more!
 * Version: 1.0.3
 * Author: Slocum Studio
 * Author URI: http://www.slocumstudio.com/
 * Requires at least: 4.0
 * Tested up to: 4.7.2
 * License: GPL2+
 *
 * Text Domain: easy-support-videos
 * Domain Path: /languages/
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'Easy_Support_Videos' ) ) {
	final class Easy_Support_Videos {
		/**
		 * @var string
		 */
		public static $version = '1.0.3';

		/**
		 * @var Easy_Support_Videos, Instance of the class
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
		}

		/**
		 * Include required core files used in admin and on the front-end.
		 */
		private function includes() {
			// Admin Only
			if ( is_admin() ) {
				include_once 'includes/class-easy-support-videos-options.php'; // Easy Support Videos Options
				include_once 'includes/class-easy-support-videos-post-types.php'; // Easy Support Videos Post Types
				include_once 'includes/admin/class-easy-support-videos-admin-options.php'; // Easy Support Videos Admin Options
				include_once 'includes/class-easy-support-videos-upgrade.php'; // Easy Support Videos Upgrade
			}
		}


		/********************
		 * Helper Functions *
		 ********************/

		/**
		 * This function returns the plugin url for Easy_Support_Videos without a trailing slash.
		 *
		 * @return string, URL for the Easy_Support_Videos plugin
		 */
		public static function plugin_url() {
			return untrailingslashit( plugins_url( '', __FILE__ ) );
		}

		/**
		 * This function returns the plugin directory for Easy_Support_Videos without a trailing slash.
		 *
		 * @return string, Directory for the Easy_Support_Videos plugin
		 */
		public static function plugin_dir() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		/**
		 * This function returns a reference to this Easy_Support_Videos class file.
		 *
		 * @return string
		 */
		public static function plugin_file() {
			return __FILE__;
		}

		/**
		 * This function returns a boolean result comparing against the current WordPress version.
		 *
		 * @return Boolean
		 */
		public static function wp_version_compare( $version, $operator = '>=' ) {
			global $wp_version;

			return version_compare( $wp_version, $version, $operator );
		}
	}

	/**
	 * Create an instance of the Easy_Support_Videos class.
	 */
	function Easy_Support_Videos() {
		return Easy_Support_Videos::instance();
	}

	Easy_Support_Videos();
}