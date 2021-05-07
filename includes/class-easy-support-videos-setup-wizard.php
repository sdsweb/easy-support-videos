<?php
/**
 * Easy Support Videos Setup Wizard
 *
 * @class Easy_Support_Videos_Setup_Wizard
 * @author Slocum Studio
 * @version 2.0.0
 * @since 2.0.0
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'Easy_Support_Videos_Setup_Wizard' ) ) {
	final class Easy_Support_Videos_Setup_Wizard {
		/**
		 * @var string
		 */
		public $version = '2.0.0';

		/**
		 * @var array
		 */
		public static $posts = array();

		/**
		 * @var string
		 */
		public static $admin_script_handle = 'easy-support-videos-setup-wizard';

		/**
		 * @var Easy_Support_Videos_Setup_Wizard, Instance of the class
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
			// Set the posts on this class
			self::$posts = array(
				// Step 1
				'esv_step_1' => array(
					/*
					 * Set the post ID.
					 *
					 * Note: This should always be set to -1 for any posts we are declaring here. WordPress does not
					 * use -1 as a post ID as of 09/27/19.
					 *
					 * This is also necessary because WP_Post::get_instance() casts the post ID to an integer and
					 * then attempts to add it to the object cache via wp_cache_add().
					 */
					'ID' => '-1',
					'ESV_ID' => 'esv_step_1', // Easy Support Videos ID
					'post_title' => __( 'Welcome to Easy Support Videos!', 'easy-support-videos' ),
					'post_content' => 'https://vimeo.com/540834455',
					'post_excerpt' => sprintf( __( 'This video will guide you through some of the features and benefits of Easy Support Videos. Visit <a href="%1$s" target="_blank">easysupportvideos.com</a> for more information.', 'easy-support-videos' ), 'https://easysupportvideos.com' ),
					'post_type' => Easy_Support_Videos_Post_Types::$easy_support_videos_post_type,
					'filter' => 'display' // Note: This is necessary to prevent default WordPress filtering/sanitizing
				)
			);

			// Hooks
			add_action( 'admin_head', array( $this, 'admin_head' ), 9999 ); // Admin Head (late)
			add_filter( 'script_loader_tag', array( $this, 'script_loader_tag' ), 10, 3 ); // Script Loader Tag

			// Easy Support Videos Hooks
			add_filter( 'easy_support_videos_page_title', array( $this, 'easy_support_videos_page_title' ) ); // Easy Support Videos - Page Title
			add_action( 'easy_support_videos_options_notifications', array( $this, 'easy_support_videos_options_notifications' ), 5 ); // Easy Support Videos - Options Notifications (before Easy Support Videos Admin Options)
			add_filter( 'easy_support_videos_current_user_can', array( $this, 'easy_support_videos_current_user_can' ), 10, 4 ); // Easy Support Videos - Current User Can
			add_filter( 'easy_support_videos_videos', array( $this, 'easy_support_videos_videos' ), 1, 2 ); // Easy Support Videos - Videos (early; Before contextual videos)

			// Easy Support Videos - Contextual Videos Hooks
			add_filter( 'easy_support_videos_contextual_videos_is_enabled', array( $this, 'easy_support_videos_contextual_videos_is_enabled' ), 10, 2 ); // Easy Support Videos - Contextual Videos - Is Enabled
			add_filter( 'easy_support_videos_contextual_videos_included_context_ids', array( $this, 'easy_support_videos_contextual_videos_included_context_ids' ) ); // Easy Support Videos - Contextual Videos - Included Context IDs
			add_filter( 'easy_support_videos_contextual_videos_section_included_context_ids', array( $this, 'easy_support_videos_contextual_videos_section_included_context_ids' ) ); // Easy Support Videos - Contextual Videos Section - Included Context IDs
			add_filter( 'easy_support_videos_contextual_videos_excluded_context_ids', array( $this, 'easy_support_videos_contextual_videos_excluded_context_ids' ) ); // Easy Support Videos - Contextual Videos - Excluded Context IDs
			add_filter( 'easy_support_videos_contextual_videos_current_user_can_read_contextual_videos', array( $this, 'easy_support_videos_contextual_videos_current_user_can_read_contextual_videos' ), 10, 4 ); // Easy Support Videos - Contextual Videos - Current User Can Read Contextual Videos
			add_filter( 'easy_support_videos_contextual_videos_video_ids_for_current_context', array( $this, 'easy_support_videos_contextual_videos_video_ids_for_current_context' ), 10, 6 ); // Easy Support Videos - Contextual Videos - Video IDs for Current Context
			add_action( 'easy_support_videos_contextual_videos_load_assets', array( $this, 'easy_support_videos_contextual_videos_load_assets' ), 10, 4 ); // Easy Support Videos - Contextual Videos - Load Assets

		}


		/**
		 * This function runs in the admin head.
		 */
		public function admin_head() {
			/*
			 * Delete the Easy Support Videos show setup wizard re-direct transient.
			 *
			 * Note: We're deleting the transient here due to Easy_Support_Videos_Upgrade running
			 * upgrade logic on the "admin_head" action.
			 */
			delete_transient( Easy_Support_Videos_Install::$show_setup_wizard_transient_name );
		}

		/**
		 * This function adjusts the script loader tag.
		 */
		public function script_loader_tag( $tag, $handle, $src ) {
			global $wp_filesystem;

			// Script allow list
			$script_handle_allow_list = apply_filters( 'easy_support_videos_setup_wizard_script_loader_tag_handle_allow_list', array(
				// Easy Support Videos Setup Wizard Script
				self::$admin_script_handle
			), $this );

			// Bail if this script handle isn't in the script handle allow list
			if ( ! in_array( $handle, $script_handle_allow_list ) )
				return $tag;

			// If the WordPress filesystem hasn't bee initialized
			if ( ! $wp_filesystem )
				// Initialize the WordPress filesystem
				WP_Filesystem();

			// Bail if we don't have the WordPress filesystem or this isn't this isn't the direct WordPress filesystem
			if ( ! $wp_filesystem || ( $wp_filesystem->method !== 'direct' ) )
				return $tag;

			// Grab the WP_Scripts instance
			$wp_scripts = wp_scripts();

			// Grab the script object
			$script_object = $wp_scripts->query( $handle );

			// Grab the script version
			$script_version = $ver = ( $script_object->ver ) ? $script_object->ver : $wp_scripts->default_version;

			// Grab the relative source
			$relative_src = wp_make_link_relative( remove_query_arg( 'ver', $src ) );

			// Grab the relative Easy Support videos URL
			$relative_easy_support_videos_url = wp_make_link_relative( Easy_Support_Videos::plugin_url() );

			// Grab the Easy Support Videos source path
			$easy_support_videos_src_path = Easy_Support_Videos::plugin_dir() . str_replace( $relative_easy_support_videos_url, '', $relative_src );

			// Bail if the Easy Support Videos source path doesn't exist or the
			if ( ! $wp_filesystem->exists( $easy_support_videos_src_path ) || ! $wp_filesystem->is_readable( $easy_support_videos_src_path ) )
				return $tag;

			// Grab the script
			$script = $wp_filesystem->get_contents( $easy_support_videos_src_path );

			// If we have the script
			if ( $script )
				// Set the tag
				$tag = '<script type="text/javascript" data-version="' . esc_attr( $script_version ) . '">' . $script . '</script>' . "\n";

			return $tag;
		}

		/**
		 * This function enqueues scripts and styles in the WordPress admin.
		 */
		public function admin_enqueue_scripts( $hook ) {
			// Easy Support Videos Setup Wizard Script
			wp_enqueue_script( self::$admin_script_handle, Easy_Support_Videos::plugin_url() . '/assets/js/easy-support-videos-setup-wizard.js', array( Easy_Support_Videos_Admin_Contextual_Videos::$script_handle ), $this->version, true );
		}


		/***********************
		 * Easy Support Videos *
		 ***********************/

		/**
		 * This function adjusts teh Easy Support Videos page title.
		 */
		public function easy_support_videos_page_title( $title ) {
			// Set the title
			$title = __( 'Setup Wizard', 'easy-support-videos' );

			return $title;
		}

		/**
		 * This function adds Easy Support Videos options notifications.
		 */
		public function easy_support_videos_options_notifications() {
		?>
			<div class="notice is-dismissible easy-support-videos-options-notice easy-support-videos-options-setup-wizard-notice">
				<p>
					<span class="dashicons dashicons-admin-tools"></span>
					<?php _e( 'Welcome to the Easy Support Videos setup wizard!', 'easy-support-videos' ); ?>
				</p>
				<button type="button" class="notice-dismiss easy-support-videos-options-notice-dismiss">
					<span class="screen-reader-text"><?php _e( 'Dismiss this notice.', 'easy-support-videos' ); ?></span>
				</button>
			</div>
		<?php
		}

		/**
		 * This function adjusts whether the current user has an Easy Support Videos capability.
		 */
		public function easy_support_videos_current_user_can( $current_user_can, $post_type, $capability, $post_type_capabilities ) {
			// Switch based on the capability
			switch ( $capability ) {
				// Edit Posts
				case 'edit_posts':
					// If the current user can edit posts
					if ( $current_user_can )
						// Reset the current user can flag
						$current_user_can = false;
				break;

				// Publish Posts
				case 'publish_posts':
					// If the current user can publish posts
					if ( $current_user_can )
						// Reset the current user can flag
						$current_user_can = false;
				break;
			}

			return $current_user_can;
		}

		/**
		 * This function adjusts the Easy Support Videos videos query.
		 */
		public function easy_support_videos_videos( $query, $query_args ) {
			// Bail if we already have a query
			if ( $query )
				return $query;

			// If the Easy Support Videos Setup Wizard WP Query PHP class doesn't exist
			if ( ! class_exists( 'Easy_Support_Videos_Setup_Wizard_WP_Query' ) )
				// Include the Easy Support Videos Setup Wizard WP Query PHP class
				include_once Easy_Support_Videos::plugin_dir() . '/includes/class-easy-support-videos-setup-wizard-wp-query.php';

			// Set the query
			$query = new Easy_Support_Videos_Setup_Wizard_WP_Query( $query_args );

			return $query;
		}


		/*******************************************
		 * Easy Support Videos - Contextual Videos *
		 *******************************************/

		/**
		 * This function adjusts whether or not Easy Support Videos contextual videos is enabled.
		 */
		public function easy_support_videos_contextual_videos_is_enabled( $is_enabled, $easy_support_videos_options ) {
			// Bail if Easy Support Videos contextual videos is enabled
			if ( $is_enabled )
				return $is_enabled;

			// Set the enabled flag
			$is_enabled = true;

			return $is_enabled;
		}

		/**
		 * This function adjusts the Easy Support Videos contextual videos included context IDs.
		 */
		public function easy_support_videos_contextual_videos_included_context_ids( $included_context_ids ) {
			// Grab the Easy Support Videos admin options sub-menu page index
			$esv_admin_options_sub_menu_page_index = array_search( Easy_Support_Videos_Admin_Options::get_sub_menu_page( false ), $included_context_ids );

			// Bail if we have the Easy Support Videos admin options sub-menu page index
			if ( $esv_admin_options_sub_menu_page_index !== false )
				return $included_context_ids;

			// Add the Easy Support Videos admin options sub-menu page to the included context IDs
			$included_context_ids[] = Easy_Support_Videos_Admin_Options::get_sub_menu_page( false );

			return $included_context_ids;
		}

		/**
		 * This function adjusts the Easy Support Videos contextual videos section included context IDs.
		 */
		public function easy_support_videos_contextual_videos_section_included_context_ids( $included_context_ids ) {
			// Grab the Easy Support Videos admin options sub-menu page index
			$esv_admin_options_sub_menu_page_index = array_search( Easy_Support_Videos_Admin_Options::get_sub_menu_page( false ), $included_context_ids );

			// Bail if we don't have the Easy Support Videos admin options sub-menu page index
			if ( $esv_admin_options_sub_menu_page_index === false )
				return $included_context_ids;

			// Unset the Easy Support Videos admin options sub-menu page from the included context IDs
			unset( $included_context_ids[$esv_admin_options_sub_menu_page_index] );

			return $included_context_ids;
		}

		/**
		 * This function adjusts the Easy Support Videos contextual videos excluded context IDs.
		 */
		public function easy_support_videos_contextual_videos_excluded_context_ids( $excluded_context_ids ) {
			// Grab the Easy Support Videos admin options sub-menu page index
			$esv_admin_options_sub_menu_page_index = array_search( Easy_Support_Videos_Admin_Options::get_sub_menu_page( false ), $excluded_context_ids );

			// Bail if we don't have the Easy Support Videos admin options sub-menu page index
			if ( $esv_admin_options_sub_menu_page_index === false )
				return $excluded_context_ids;

			// Unset the Easy Support Videos admin options sub-menu page from the excluded context IDs
			unset( $excluded_context_ids[$esv_admin_options_sub_menu_page_index] );

			return $excluded_context_ids;
		}

		/**
		 * This function adjusts whether the Easy Support Videos contextual videos current user can
		 * read contextual videos.
		 */
		public function easy_support_videos_contextual_videos_current_user_can_read_contextual_videos( $current_user_can_read_contextual_videos, $contextual_videos_read_role, $easy_support_videos_options, $easy_support_videos_default_options ) {
			// Bail if the current user can read Easy Support Videos contextual videos
			if ( $current_user_can_read_contextual_videos )
				return $current_user_can_read_contextual_videos;

			// Set the current user can read Easy Support Videos contextual videos flag
			$current_user_can_read_contextual_videos = true;

			return $current_user_can_read_contextual_videos;
		}

		/**
		 * This function adjusts the Easy Support Videos contextual videos video IDs for
		 * the current context.
		 */
		public function easy_support_videos_contextual_videos_video_ids_for_current_context( $video_ids_for_current_context, $easy_support_videos_ids_query, $easy_support_videos_ids_args, $context_id, $context ) {
			global $hook_suffix;

			// Bail if we're not on the Easy Support Videos options page
			if ( $hook_suffix !== Easy_Support_Videos_Admin_Options::get_sub_menu_page( false ) )
				return $video_ids_for_current_context;

			// If the Easy Support Videos contextual videos global video is enabled, context ID matches the global
			if ( Easy_Support_Videos_Admin_Contextual_Videos::is_global_video_enabled() && $context_id === Easy_Support_Videos_Admin_Contextual_Videos::$global_video_context ) {
				// Grab the original current context for global video flag
				$original_is_current_context_for_global_video = Easy_Support_Videos_Admin_Contextual_Videos::$is_current_context_for_global_video;

				// Set the current context for global video flag
				Easy_Support_Videos_Admin_Contextual_Videos::$is_current_context_for_global_video = false;

				// Grab the current context ID
				$current_context_id = Easy_Support_Videos_Admin_Contextual_Videos::get_current_context_id( $context );

				// Reset the current context for global video flag
				Easy_Support_Videos_Admin_Contextual_Videos::$is_current_context_for_global_video = $original_is_current_context_for_global_video;

				// Set the context ID
				$context_id = $current_context_id;
			}

			// Bail if this isn't the Easy Support Videos admin sub-menu page options context
			if ( $context_id !== Easy_Support_Videos_Admin_Options::get_sub_menu_page( false ) )
				return $video_ids_for_current_context;

			// Set the video IDs for the current context
			$video_ids_for_current_context = array_keys( self::$posts );

			return $video_ids_for_current_context;
		}

		/**
		 * This function runs when Easy Support Videos contextual videos assets are loaded.
		 */
		public function easy_support_videos_contextual_videos_load_assets( $current_user_can_edit_easy_support_videos, $current_user_can_read_contextual_easy_support_videos, $video_ids_for_current_context, $easy_support_videos_admin_contextual_videos ) {
			// Hook into "admin_enqueue_scripts"
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		}
	}

	/**
	 * Create an instance of the Easy_Support_Videos_Setup_Wizard class.
	 */
	function Easy_Support_Videos_Setup_Wizard() {
		return Easy_Support_Videos_Setup_Wizard::instance();
	}

	Easy_Support_Videos_Setup_Wizard();
}