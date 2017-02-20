<?php
/**
 * Easy Support Videos Upgrade
 *
 * @class Easy_Support_Videos_Upgrade
 * @author Slocum Studio
 * @version 1.0.2
 * @since 1.0.0
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'Easy_Support_Videos_Upgrade' ) ) {
	final class Easy_Support_Videos_Upgrade {
		/**
		 * @var string
		 */
		public $version = '1.0.2';

		/**
		 * @var Boolean
		 */
		public $did_upgrade = false;

		/**
		 * @var string
		 */
		public static $option_name = 'easy_support_videos_version';

		/**
		 * @var Easy_Support_Videos_Upgrade, Instance of the class
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
			add_action( 'admin_head', array( $this, 'admin_head' ) ); // Admin Head
		}

		/**
		 * This function runs in the admin head.
		 */
		public function admin_head() {
			global $hook_suffix;

			// Bail if we're not on the Easy Support Videos page or the Easy Support Videos Options page
			if ( $hook_suffix !== Easy_Support_Videos_Post_Types::get_easy_support_videos_menu_page( false ) && $hook_suffix !== Easy_Support_Videos_Admin_Options::get_sub_menu_page( false ) )
				return;

			// Grab the Easy Support Videos version (default to 1.0.0)
			if ( ! ( $esv_version = get_option( self::$option_name ) ) )
				$esv_version = '1.0.0';

			/*
			 * Version 1.0.2
			 */
			if ( version_compare( $esv_version, '1.0.2', '<' ) )
				$this->upgrade_102();

			do_action( 'easy_support_videos_upgrade', $esv_version, Easy_Support_Videos::$version, $this );

			// If we performed an upgrade or we're on the Easy Support Videos Options page
			// if ( $this->did_upgrade || $hook_suffix === Easy_Support_Videos_Admin_Options::get_sub_menu_page( false ) )

			// Update the Easy Support Videos version
			update_option( self::$option_name, Easy_Support_Videos::$version );
		}


		/**********************
		 * Internal Functions *
		 **********************/

		/**
		 * This function runs upgrades for Easy Support Videos 1.0.2. It sets the post content on all
		 * Easy Support Videos videos to the URL instead of the oEmbed HTML markup.
		 */
		public function upgrade_102() {
			// Find Easy Support Videos
			$easy_support_videos = new WP_Query( apply_filters( 'easy_support_videos_videos_args', Easy_Support_Videos_Post_Types::$query_args ) );

			// If we have Easy Support Videos
			if ( $easy_support_videos->have_posts() ) {
				// Loop through Easy Support Videos
				while ( $easy_support_videos->have_posts() )  {
					// Grab the post
					$post = $easy_support_videos->next_post();

					// Grab the post ID
					$post_id = get_post_field( 'ID', $post) ;

					// Update the post (set the post content to the Easy Support Videos URL meta value)
					wp_update_post( array(
						'ID' => $post_id,
						'post_content' => get_post_meta( $post_id, 'easy_support_videos_url', true )
					) );
				}
			}

			// Set the did upgrade flag
			$this->did_upgrade = true;
		}
	}

	/**
	 * Create an instance of the Easy_Support_Videos_Upgrade class.
	 */
	function Easy_Support_Videos_Upgrade() {
		return Easy_Support_Videos_Upgrade::instance();
	}

	Easy_Support_Videos_Upgrade();
}