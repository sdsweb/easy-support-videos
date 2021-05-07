<?php
/**
 * Easy Support Videos Upgrade
 *
 * @class Easy_Support_Videos_Upgrade
 * @author Slocum Studio
 * @version 2.0.0
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
		public $version = '2.0.0';

		/**
		 * @var Boolean
		 */
		public $did_early_upgrade = false;

		/**
		 * @var Boolean
		 */
		public $did_upgrade = false;

		/**
		 * @var array
		 */
		public $early_upgrades_done = array();

		/**
		 * @var array
		 */
		public $upgrades_done = array();

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
			// Hooks
			add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) ); // Plugins Loaded
			add_action( 'admin_head', array( $this, 'admin_head' ) ); // Admin Head
		}

		/**
		 * This function runs when plugins are loaded.
		 */
		public function plugins_loaded() {
			// Bail if this isn't the admin
			if ( ! is_admin() )
				return;

			// Grab the Easy Support Videos version
			$esv_version = get_option( Easy_Support_Videos_Install::$option_name, null );

			// If this isn't an iframe request and the old Easy Support Videos version is less than the current Easy Support Videos version
			if ( ! defined( 'IFRAME_REQUEST' ) && version_compare( $esv_version, Easy_Support_Videos::$version, '<' ) ) {
				do_action( 'easy_support_videos_upgrade_early_before', $esv_version, Easy_Support_Videos::$version, $this );

				/*
				 * Version 2.0.0 (early)
				 */
				if ( version_compare( $esv_version, '2.0.0', '<' ) )
					$this->upgrade_200_early();

				do_action( 'easy_support_videos_upgrade_early', $esv_version, Easy_Support_Videos::$version, $this );

				do_action( 'easy_support_videos_upgrade_early_after', $esv_version, Easy_Support_Videos::$version, $this );
			}
		}

		/**
		 * This function runs in the admin head.
		 */
		public function admin_head() {
			// Grab the Easy Support Videos version
			$esv_version = get_option( Easy_Support_Videos_Install::$option_name, null );

			// If this isn't an iframe request and the old Easy Support Videos version is less than the current Easy Support Videos version
			if ( ! defined( 'IFRAME_REQUEST' ) && version_compare( $esv_version, Easy_Support_Videos::$version, '<' ) ) {
				do_action( 'easy_support_videos_upgrade_before', $esv_version, Easy_Support_Videos::$version, $this );

				/*
				 * Version 1.0.2
				 */
				if ( version_compare( $esv_version, '1.0.2', '<' ) )
					$this->upgrade_102();

				/*
				 * Version 2.0.0
				 */
				if ( version_compare( $esv_version, '2.0.0', '<' ) )
					$this->upgrade_200();

				do_action( 'easy_support_videos_upgrade', $esv_version, Easy_Support_Videos::$version, $this );

				do_action( 'easy_support_videos_upgrade_after', $esv_version, Easy_Support_Videos::$version, $this );
			}
		}


		/**********************
		 * Internal Functions *
		 **********************/

		/**
		 * This function runs upgrades for Easy Support Videos 1.0.2.
		 *
		 * Note: We are setting the post content on all Easy Support Videos videos
		 * to the URL instead of the oEmbed HTML markup.
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
					$post_id = get_post_field( 'ID', $post ) ;

					// Update the post (set the post content to the Easy Support Videos URL meta value)
					wp_update_post( array(
						'ID' => $post_id,
						'post_content' => get_post_meta( $post_id, 'easy_support_videos_url', true )
					) );
				}
			}

			// Set the did upgrade flag
			$this->did_upgrade = true;

			// Append this version to the upgrades done
			$this->upgrades_done[] = '1.0.2';
		}

		/**
		 * This function runs upgrades for Easy Support Videos 2.0.0 (early).
		 */
		public function upgrade_200_early() {
			// Grab the contextual videos active contexts transient
			$contextual_videos_active_contexts = get_transient( Easy_Support_Videos_Admin_Contextual_Videos::$active_contexts_transient_name );

			// If we don't have the contextual videos active contexts
			if ( ! $contextual_videos_active_contexts )
				// Set the contextual videos active contexts
				$contextual_videos_active_contexts = array();

			// Set the contextual videos active contexts transient
			set_transient( Easy_Support_Videos_Admin_Contextual_Videos::$active_contexts_transient_name, $contextual_videos_active_contexts );

			// Set the did upgrade flag
			$this->did_early_upgrade = true;

			// Append this version to the early upgrades done
			$this->early_upgrades_done[] = '2.0.0';
		}

		/**
		 * This function runs upgrades for Easy Support Videos 2.0.0.
		 */
		public function upgrade_200() {
			// If the Easy Support Videos setup wizard is enabled
			if ( Easy_Support_Videos_Install::is_setup_wizard_enabled() ) {
				// Set the Easy Support Videos show setup wizard transient (expires in ~1 week)
				set_transient( Easy_Support_Videos_Install::$show_setup_wizard_transient_name, 1, ( DAY_IN_SECONDS * 7 ) );

				// Update the Easy Support Videos published videos count transient
				Easy_Support_Videos_Post_Types::update_published_videos_count_transient();

				// Set the did upgrade flag
				$this->did_upgrade = true;

				// Append this version to the upgrades done
				$this->upgrades_done[] = '2.0.0';
			}
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