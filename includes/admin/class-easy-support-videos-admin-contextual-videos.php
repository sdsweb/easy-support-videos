<?php
/**
 * Easy Support Videos Admin Contextual Videos
 *
 * @class Easy_Support_Videos_Admin_Contextual_Videos
 * @author Slocum Studio
 * @version 2.0.0
 * @since 2.0.0
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'Easy_Support_Videos_Admin_Contextual_Videos' ) ) {
	final class Easy_Support_Videos_Admin_Contextual_Videos {
		/**
		 * @var string
		 */
		public $version = '2.0.0';

		/**
		 * @var Boolean
		 */
		public static $enabled = null;

		/**
		 * @var array
		 */
		public static $editing_url_query_argument = 'esv_contextual_videos_edit';

		/**
		 * @var string
		 */
		public static $contextual_videos_query_arg = 'esv_contextual_videos';

		/**
		 * @var string
		 */
		public static $contextual_videos_context_meta_key = 'esv_contextual_videos_context';

		/**
		 * @var WP_Screen
		 */
		public static $current_context = false;

		/**
		 * @var string
		 */
		public static $current_context_id = false;

		/**
		 * @var Boolean
		 */
		public static $is_current_context_for_global_video = false;

		/**
		 * @var array
		 */
		public static $included_context_ids = null;

		/**
		 * @var array
		 */
		public static $excluded_context_ids = null;

		/**
		 * @var int
		 */
		public static $context_video_limit = 1;

		/**
		 * @var string
		 */
		public static $meta_query_name = 'esv_contextual_videos_for_context_id';

		/**
		 * @var Boolean
		 */
		public static $current_user_can_read_contextual_videos = null;

		/**
		 * @var int
		 */
		public static $wp_ajax_easy_support_videos_insert_post_id = false;

		/**
		 * @var int
		 */
		public static $wp_ajax_easy_support_videos_delete_post_id = false;

		/**
		 * @var string
		 */
		public static $global_video_context = 'all';

		/**
		 * @var Boolean
		 */
		public static $global_video_context_enabled = null;

		/**
		 * @var Boolean
		 */
		public static $script_handle = 'easy-support-videos-contextual-videos';

		/**
		 * @var Boolean
		 */
		public static $admin_script_handle = 'easy-support-videos-contextual-videos-admin';

		/**
		 * @var Boolean
		 */
		public static $stylesheet_handle = 'easy-support-videos-contextual-videos';

		/**
		 * @var Boolean
		 */
		public static $admin_stylesheet_handle = 'easy-support-videos-contextual-videos-admin';

		/**
		 * @var array
		 */
		public static $video_ids_for_current_context = null;

		/**
		 * @var string
		 */
		public static $video_ids_for_current_context_transient_prefix = 'esv_contextual_videos_video_ids_for_';

		/**
		 * @var string
		 */
		public static $video_ids_for_current_context_transient_suffix = '_context';

		/**
		 * @var string
		 */
		public static $active_contexts_transient_name = 'esv_contextual_videos_active_contexts';

		/**
		 * @var string
		 */
		public static $default_branding_background_color = '#ff913a';

		/**
		 * @var string
		 */
		public static $default_branding_text_color = '#ffffff';

		/**
		 * @var Easy_Support_Videos_Admin_Contextual_Videos, Instance of the class
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
			add_action( 'admin_init', array( $this, 'admin_init' ) ); // Admin Init
			add_action( 'current_screen', array( $this, 'current_screen' ) ); // Current Screen

			// Easy Support Videos Hooks
			add_action( 'easy_support_videos_settings', array( $this, 'easy_support_videos_settings' ) ); // Easy Support Videos Settings
			add_filter( 'easy_support_videos_options_defaults', array( $this, 'easy_support_videos_options_defaults' ), 20, 2 ); // Easy Support Videos Options Defaults
			add_filter( 'easy_support_videos_options_sanitize_option', array( $this, 'easy_support_videos_options_sanitize_option' ), 20, 5 ); // Easy Support Videos Options - Sanitize Option
			add_action( 'easy_support_videos_video_template_before', array( $this, 'easy_support_videos_video_template_before' ), 5 ); // Easy Support Videos - Video Template Before
			add_action( 'easy_support_videos_video_template_after', array( $this, 'easy_support_videos_video_template_after' ), 5 ); // Easy Support Videos - Video Template After
			add_action( 'wp_ajax_easy_support_videos_insert_wp_insert_post_before', array( $this, 'wp_ajax_easy_support_videos_insert_wp_insert_post_before' ), 5, 8 ); // Easy Support Videos AJAX - Insert wp_insert_post() Before
			add_filter( 'wp_ajax_easy_support_videos_insert_can_insert_video', array( $this, 'wp_ajax_easy_support_videos_insert_can_insert_video' ), 5, 10 ); // Easy Support Videos AJAX - Insert - Can Insert Video
			add_filter( 'wp_ajax_easy_support_videos_insert_post_id', array( $this, 'wp_ajax_easy_support_videos_insert_post_id' ), 5, 10 ); // Easy Support Videos AJAX - Insert - Post ID
			add_action( 'wp_ajax_easy_support_videos_insert_wp_insert_post_after', array( $this, 'wp_ajax_easy_support_videos_insert_wp_insert_post_after' ), 5, 9 ); // Easy Support Videos AJAX - Insert wp_insert_post() After
			add_filter( 'wp_ajax_easy_support_videos_insert_success_status', array( $this, 'wp_ajax_easy_support_videos_insert_success_status' ), 5, 9 ); // Easy Support Videos AJAX - Insert - Success Status
			add_action( 'wp_ajax_easy_support_videos_delete_wp_delete_post_before', array( $this, 'wp_ajax_easy_support_videos_delete_wp_delete_post_before' ), 5, 2 ); // Easy Support Videos AJAX - Insert wp_delete_post() Before
			add_filter( 'wp_ajax_easy_support_videos_delete_can_delete_video', array( $this, 'wp_ajax_easy_support_videos_delete_can_delete_video' ), 5, 3 ); // Easy Support Videos AJAX - Delete - Can Delete Video
			add_filter( 'wp_ajax_easy_support_videos_deleted_post', array( $this, 'wp_ajax_easy_support_videos_deleted_post' ), 5, 3 ); // Easy Support Videos AJAX - Deleted Post
			add_action( 'wp_ajax_easy_support_videos_delete_wp_delete_post_after', array( $this, 'wp_ajax_easy_support_videos_delete_wp_delete_post_after' ), 5, 3 ); // Easy Support Videos AJAX - Delete wp_delete_post() After
			add_filter( 'wp_ajax_easy_support_videos_delete_success_status', array( $this, 'wp_ajax_easy_support_videos_delete_success_status' ), 5, 4 ); // Easy Support Videos AJAX - Delete - Success Status

			add_filter( 'easy_support_videos_videos_args', array( $this, 'easy_support_videos_videos_args' ), 5 ); // Easy Support Videos - Videos Query Arguments
			add_filter( 'easy_support_videos_videos', array( $this, 'easy_support_videos_videos' ), 10, 2 ); // Easy Support Videos - Videos

			add_action( 'easy_support_videos_video_inner_after', array( $this, 'easy_support_videos_video_inner_after' ) ); // Easy Support Videos - Video Inner After
			add_action( 'easy_support_videos_inner_after', array( $this, 'easy_support_videos_inner_after' ), 10, 2 ); // Easy Support Videos - Inner After

			// Easy Support Videos - Contextual Videos Hooks
			add_action( 'easy_support_videos_contextual_videos_modal_videos_wrap_before', array( $this, 'easy_support_videos_contextual_videos_modal_videos_wrap_before' ), 10, 2 ); // Easy Support Videos - Contextual Videos - Modal Videos Wrap - Before
			add_action( 'easy_support_videos_contextual_videos_modal_videos_wrap_after', array( $this, 'easy_support_videos_contextual_videos_modal_videos_wrap_after' ), 10, 2 ); // Easy Support Videos - Contextual Videos - Modal Videos Wrap - After

			// AJAX
			add_action( 'wp_ajax_easy_support_videos_contextual_videos_set_global_video', array( $this, 'wp_ajax_easy_support_videos_contextual_videos_set_global_video' ) ); // Easy Support Videos - Contextual Videos - Set Global Video
		}

		/**
		 * Include required core files used in admin and on the front-end.
		 */
		private function includes() {
			// TODO
		}


		/**
		 * This function runs when the WordPress admin is initialized.
		 */
		public function admin_init() {
			// Easy Support Videos contextual videos limit per context
			// TODO: Future: Allow this value to be filtered: self::$context_video_limit = apply_filters( 'easy_support_videos_contextual_videos_context_video_limit', self::$context_video_limit, $this );


			/*
			 * Easy Support Videos Settings
			 */

			// Contextual Videos
			add_settings_section( 'easy_support_videos_contextual_videos_section', __( 'Contextual Videos', 'easy-support-videos' ), array( $this, 'easy_support_videos_contextual_videos_section' ), Easy_Support_Videos_Options::$option_name . '_contextual_videos' );
			add_settings_field( 'easy_support_videos_contextual_videos_enabled_field', __( 'Enable Contextual Videos', 'easy-support-videos' ), array( $this, 'easy_support_videos_contextual_videos_enabled_field' ), Easy_Support_Videos_Options::$option_name . '_contextual_videos', 'easy_support_videos_contextual_videos_section' );
			add_settings_field( 'easy_support_videos_contextual_videos_roles_read_field', __( 'View Contextual Videos', 'easy-support-videos' ), array( $this, 'easy_support_videos_contextual_videos_roles_read_field' ), Easy_Support_Videos_Options::$option_name . '_contextual_videos', 'easy_support_videos_contextual_videos_section' );
			add_settings_field( 'easy_support_videos_contextual_videos_global_video_enabled_field', __( 'Global Video', 'easy-support-videos' ), array( $this, 'easy_support_videos_contextual_videos_global_video_enabled_field' ), Easy_Support_Videos_Options::$option_name . '_contextual_videos', 'easy_support_videos_contextual_videos_section' );
		}

		/**
		 * This function runs when the current screen is set.
		 */
		public function current_screen( $screen ) {
			// Bail if Easy Support Videos contextual videos is not enabled
			if ( ! self::is_enabled() )
				return;

			// Grab the current context
			$current_context = self::get_current_context();

			// Bail if we don't have an Easy Support Videos contextual videos current context
			if ( ! $current_context )
				return;

			// Current user can edit Easy Support Videos
			$current_user_can_edit_easy_support_videos = Easy_Support_Videos_Post_Types::current_user_can( Easy_Support_Videos_Post_Types::$easy_support_videos_post_type, 'edit_posts' );

			// Current user can read contextual Easy Support Videos
			$current_user_can_read_contextual_easy_support_videos = self::current_user_can_read_contextual_videos();

			// Grab the Easy Support Videos contextual videos video IDs for the current context
			$video_ids_for_current_context = self::get_video_ids_for_current_context();

			// Flag to determine if we can load the contextual videos assets
			$can_load_contextual_videos_assets = ( $current_user_can_edit_easy_support_videos && ! self::is_current_context_for_global_video() );

			// If we can't load the contextual video assets and the current user can read contextual Easy Support Videos
			if ( ! $can_load_contextual_videos_assets && $current_user_can_read_contextual_easy_support_videos )
				// Set the can load contextual videos assets flag
				$can_load_contextual_videos_assets = ( count( $video_ids_for_current_context ) > 0 );

			// If we can load the contextual videos assets
			if ( $can_load_contextual_videos_assets ) {
				// Hook into "admin_enqueue_scripts"
				add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

				// Hook into "style_loader_tag"
				add_filter( 'style_loader_tag', array( $this, 'style_loader_tag' ), 10, 3 );

				// Hook into "script_loader_tag"
				add_filter( 'script_loader_tag', array( $this, 'script_loader_tag' ), 10, 3 );

				// Hook into "admin_footer"
				add_action( 'admin_footer', array( $this, 'admin_footer' ) );

				/*
				 * Note: This hook should be used to add actions to other hooks such as
				 * "admin_enqueue_scripts" or "admin_footer".
				 */
				do_action( 'easy_support_videos_contextual_videos_load_assets', $current_user_can_edit_easy_support_videos, $current_user_can_read_contextual_easy_support_videos, $video_ids_for_current_context, $this );
			}
		}

		/**
		 * This function enqueues scripts and styles in the WordPress admin.
		 */
		public function admin_enqueue_scripts( $hook ) {
			// Current user can edit Easy Support Videos
			$current_user_can_edit_easy_support_videos = Easy_Support_Videos_Post_Types::current_user_can( Easy_Support_Videos_Post_Types::$easy_support_videos_post_type, 'edit_posts' );

			// If the current user can edit Easy Support Videos
			if ( $current_user_can_edit_easy_support_videos ) {
				// Grab the Easy Support Videos Post Types instance
				$easy_support_videos_post_types = Easy_Support_Videos_Post_Types();

				// Enqueue the Easy Support Videos scripts and styles
				$easy_support_videos_post_types->admin_enqueue_scripts( Easy_Support_Videos_Post_Types::get_easy_support_videos_menu_page( false ) );
			}


			// Grab the current context
			$current_context = self::get_current_context();

			// Easy Support Videos Contextual Videos Stylesheet
			wp_enqueue_style( self::$stylesheet_handle, Easy_Support_Videos::plugin_url() . '/assets/css/easy-support-videos-contextual-videos.min.css', false, $this->version );

			// If the current user can edit Easy Support Videos
			if ( $current_user_can_edit_easy_support_videos ) {
				// Easy Support Videos Contextual Videos Admin Stylesheet
				wp_enqueue_style( self::$admin_stylesheet_handle, Easy_Support_Videos::plugin_url() . '/assets/css/easy-support-videos-contextual-videos-admin.min.css', array( 'easy-support-videos-admin' ), $this->version );
			}

			// Easy Support Videos Contextual Videos Script
			wp_enqueue_script( self::$script_handle, Easy_Support_Videos::plugin_url() . '/assets/js/easy-support-videos-contextual-videos.min.js', array( 'wp-backbone' ), $this->version, true );
			wp_localize_script( self::$script_handle, 'easy_support_videos_contextual_videos', apply_filters( 'easy_support_videos_contextual_videos_localize', array(
				// Context (WP_Screen)
				'context' => $current_context,
				// Is Editing Flag
				'is_editing' => self::is_editing(),
				// Is Preview Flag
				'is_preview' => Easy_Support_Videos_Preview::is_preview()
			), $current_user_can_edit_easy_support_videos, $current_context, $this ) );

			// If the current user can edit Easy Support Videos
			if ( $current_user_can_edit_easy_support_videos ) {
				// Easy Support Videos Contextual Videos Admin Script
				wp_enqueue_script( self::$admin_script_handle, Easy_Support_Videos::plugin_url() . '/assets/js/easy-support-videos-contextual-videos-admin.min.js', array( 'easy-support-videos-admin' ), $this->version, true );
				wp_localize_script( self::$admin_script_handle, 'easy_support_videos_contextual_videos_admin', apply_filters( 'easy_support_videos_contextual_videos_admin_localize', array(
					// Context (WP_Screen)
					'context' => $current_context
				), $current_user_can_edit_easy_support_videos, $current_context, $this ) );
			}
			// Otherwise the current user can't edit Easy Support Videos
			else {
				// Fitvids Script
				wp_enqueue_script( Easy_Support_Videos_Post_Types::$admin_fitvids_script_handle, Easy_Support_Videos::plugin_url() . '/assets/js/fitvids.min.js', self::$script_handle, Easy_Support_Videos::$version, true );
			}
		}

		/**
		 * This function adjusts the style loader tag.
		 */
		public function style_loader_tag( $tag, $handle, $src ) {
			global $wp_filesystem;

			// Style allow list
			$style_handle_allow_list = apply_filters( 'easy_support_videos_contextual_videos_style_loader_tag_handle_allow_list', array(
				// Easy Support Videos Contextual Videos Stylesheet
				self::$stylesheet_handle,
				// Easy Support Videos Contextual Videos Admin Edit Stylesheet
				self::$admin_stylesheet_handle,
				// Easy Support Videos Admin Stylesheet
				Easy_Support_Videos_Post_Types::$admin_stylesheet_handle,
			), $this );

			// Bail if this style handle isn't in the style handle allow list
			if ( ! in_array( $handle, $style_handle_allow_list ) )
				return $tag;

			// If the WordPress filesystem hasn't been initialized
			if ( ! $wp_filesystem )
				// Initialize the WordPress filesystem
				WP_Filesystem();

			// Bail if we don't have the WordPress filesystem or this isn't this isn't the direct WordPress filesystem
			if ( ! $wp_filesystem || ( $wp_filesystem->method !== 'direct' ) )
				return $tag;

			// Grab the WP_Styles instance
			$wp_styles = wp_styles();

			// Grab the style object
			$style_object = $wp_styles->query( $handle );

			// Grab the style version
			$style_version = $ver = ( $style_object->ver ) ? $style_object->ver : $wp_styles->default_version;

			// Grab the style media
			$style_media = ( isset( $style_object->args ) ) ? $style_object->args : 'all';

			// Grab the style title (Note: We are specifying the title attribute here due to the way stylesheets are handled in the browser - @see https://developer.mozilla.org/en-US/docs/Web/CSS/Alternative_style_sheets#Details)
			$style_title = ( isset( $style_object->extra['title'] ) ) ? "title='" . esc_attr( $style_object->extra['title'] ) . "'" : '';

			// Grab the relative source
			$relative_src = wp_make_link_relative( remove_query_arg( 'ver', $src ) );

			// Grab the relative Easy Support videos URL
			$relative_easy_support_videos_url = wp_make_link_relative( Easy_Support_Videos::plugin_url() );

			// Grab the Easy Support Videos source path
			$easy_support_videos_src_path = Easy_Support_Videos::plugin_dir() . str_replace( $relative_easy_support_videos_url, '', $relative_src );

			// Bail if the Easy Support Videos source path doesn't exist or the
			if ( ! $wp_filesystem->exists( $easy_support_videos_src_path ) || ! $wp_filesystem->is_readable( $easy_support_videos_src_path ) )
				return $tag;

			// Grab the style
			$style = $wp_filesystem->get_contents( $easy_support_videos_src_path );

			// If we have the style
			if ( $style )
				// Set the tag
				$tag = '<style type="text/css" id="' . esc_attr( $handle . '-css' ) . '" media="' . esc_attr( $style_media ) . '" ' . $style_title . ' data-version="' . esc_attr( $style_version ) . '">' . $style . '</style>' . "\n";

			return $tag;
		}

		/**
		 * This function adjusts the script loader tag.
		 */
		public function script_loader_tag( $tag, $handle, $src ) {
			global $wp_filesystem;

			// Script allow list
			$script_handle_allow_list = apply_filters( 'easy_support_videos_contextual_videos_script_loader_tag_handle_allow_list', array(
				// Easy Support Videos Contextual Videos Script
				self::$script_handle,
				// Easy Support Videos Contextual Videos Admin Edit Script
				self::$admin_script_handle,
				// Easy Support Videos Admin Script
				Easy_Support_Videos_Post_Types::$admin_script_handle,
				// Easy Support Videos Fitvids Script
				Easy_Support_Videos_Post_Types::$admin_fitvids_script_handle
			), $this );

			// Bail if this script handle isn't in the script handle allow list
			if ( ! in_array( $handle, $script_handle_allow_list ) )
				return $tag;

			// If the WordPress filesystem hasn't been initialized
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
		 * This function runs in the WordPress admin footer.
		 */
		public function admin_footer() {
			// Grab the Easy Support Videos Post Types instance
			$easy_support_videos_post_types = Easy_Support_Videos_Post_Types();

			// Grab the Easy Support Videos
			$easy_support_videos = Easy_Support_Videos_Post_Types::get_easy_support_videos();

			// Video IDs for the global context
			$video_ids_for_global_context = array();

			// Easy Support Videos menu page icon image
			$easy_support_videos_menu_page_icon_image = false;

			// Grab the Easy Support Videos menu page icon URL
			$easy_support_videos_menu_page_icon_url = apply_filters( 'easy_support_videos_menu_page_icon_url', Easy_Support_Videos_Post_Types::$easy_support_videos_menu_page_icon_url, $easy_support_videos_post_types );

			// Easy Support Videos contextual videos icon CSS classes
			$easy_support_videos_contextual_videos_icon_css_classes = array(
				'easy-support-videos-contextual-videos-icon'
			);

			// Switch based on the Easy Support Videos menu page icon URL
			switch ( $easy_support_videos_menu_page_icon_url ) {
				// None Div (legacy)
				case 'none':
				case 'div':
					// Append the "easy-support-videos-contextual-videos-icon-empty" CSS class to the Easy Support Videos contextual videos icon CSS classes
					$easy_support_videos_contextual_videos_icon_css_classes[] = 'easy-support-videos-contextual-videos-icon-empty';
				break;

				// Dashicons
				case ( strpos( $easy_support_videos_menu_page_icon_url, 'dashicons-' ) === 0 ):
					// Append the "dashicons" CSS class to the Easy Support Videos contextual videos icon CSS classes
					$easy_support_videos_contextual_videos_icon_css_classes[] = 'dashicons';

					// Append the Easy Support Videos menu page icon URL CSS class to the Easy Support Videos contextual videos icon CSS classes
					$easy_support_videos_contextual_videos_icon_css_classes[] = $easy_support_videos_menu_page_icon_url;
				break;

				// Default (URL)
				default:
					// Set the Easy Support Videos menu page icon image
					$easy_support_videos_menu_page_icon_image = '<img src="' . esc_url( $easy_support_videos_menu_page_icon_url, array_merge( wp_allowed_protocols(), array( 'data' ) ) ) . '" alt="' . __( 'Easy Support Videos contextual videos icon', 'easy-support-videos' ) . '" />';

					// If this is a base64-encoded SVG
					if ( strpos( $easy_support_videos_menu_page_icon_url, 'data:image/svg+xml;base64,' ) === 0 )
						// Append the "easy-support-videos-contextual-videos-icon-base64" CSS class to the Easy Support Videos contextual videos icon CSS classes
						$easy_support_videos_contextual_videos_icon_css_classes[] = 'easy-support-videos-contextual-videos-icon-base64';
					// Otherwise this isn't a base64-encoded SVG
					else
						// Append the "easy-support-videos-contextual-videos-icon-image" CSS class to the Easy Support Videos contextual videos icon CSS classes
						$easy_support_videos_contextual_videos_icon_css_classes[] = 'easy-support-videos-contextual-videos-icon-image';
				break;
			}

			// Sanitize the Easy Support Videos contextual videos icon CSS classes
			$easy_support_videos_contextual_videos_icon_css_classes = array_map( 'sanitize_html_class', $easy_support_videos_contextual_videos_icon_css_classes );

			// Ensure we have unique Easy Support Videos contextual videos icon CSS classes (no empty values)
			$easy_support_videos_contextual_videos_icon_css_classes = array_unique( array_values( array_filter( $easy_support_videos_contextual_videos_icon_css_classes ) ) );

			// Current user can edit Easy Support Videos
			$current_user_can_edit_easy_support_videos = Easy_Support_Videos_Post_Types::current_user_can( Easy_Support_Videos_Post_Types::$easy_support_videos_post_type, 'edit_posts' );

			do_action( 'easy_support_videos_contextual_videos_before', $current_user_can_edit_easy_support_videos, $easy_support_videos, $this );
		?>
			<div id="easy-support-videos-contextual-videos-widget" class="easy-support-videos-contextual-videos-widget">
				<?php do_action( 'easy_support_videos_contextual_videos_widget_before', $current_user_can_edit_easy_support_videos, $easy_support_videos, $this ); ?>

				<div id="easy-support-videos-contextual-videos-icon-wrap" class="easy-support-videos-contextual-videos-icon-wrap">
					<?php do_action( 'easy_support_videos_contextual_videos_widget_icon_before', $current_user_can_edit_easy_support_videos, $easy_support_videos, $this ); ?>

					<div id="easy-support-videos-contextual-videos-icon" class="<?php echo esc_attr( implode( ' ', $easy_support_videos_contextual_videos_icon_css_classes ) ); ?>">
						<?php echo ( $easy_support_videos_menu_page_icon_image ) ? $easy_support_videos_menu_page_icon_image : false; ?>
					</div>

					<?php do_action( 'easy_support_videos_contextual_videos_widget_icon_after', $current_user_can_edit_easy_support_videos, $easy_support_videos, $this ); ?>
				</div>

				<?php do_action( 'easy_support_videos_contextual_videos_widget_after', $current_user_can_edit_easy_support_videos, $easy_support_videos, $this ); ?>
			</div>

			<?php
				// If the Easy Support Videos contextual videos global video is enabled
				if ( self::is_global_video_enabled() ) {
					// TODO: Future: ?
					// Set the current context on this class
					//self::$current_context = self::$global_video_context;

					// Reset the current context ID on this class
					// self::$current_context_id = false;

					// Grab the original current context for global video flag on this class
					$original_is_current_context_for_global_video = self::$is_current_context_for_global_video;

					// Set the current context for global video flag on this class
					self::$is_current_context_for_global_video = true;

					// Grab the Easy Support Videos contextual videos video IDs for the global context (force)
					$video_ids_for_global_context = self::get_video_ids_for_current_context( true );

					// Reset the current context for global video flag on this class
					self::$is_current_context_for_global_video = $original_is_current_context_for_global_video;
				}

				// TODO: Future: Add actions before and after display for developers
			?>
			<div id="easy-support-videos-contextual-videos-modal" class="easy-support-videos-contextual-videos-modal <?php echo ( Easy_Support_Videos_Post_Types::current_user_can( Easy_Support_Videos_Post_Types::$easy_support_videos_post_type, 'edit_posts' ) ) ? 'easy-support-videos-can-edit' : false; ?> <?php echo ( $easy_support_videos->have_posts() ) ? 'easy-support-videos-has-videos' : false; ?> <?php echo ( $video_ids_for_global_context ) ? 'easy-support-videos-has-global-video' : false; ?>">
				<?php do_action( 'easy_support_videos_contextual_videos_modal_before', $current_user_can_edit_easy_support_videos, $easy_support_videos, $this ); ?>

				<div id="easy-support-videos-contextual-videos-modal-container" class="easy-support-videos-contextual-videos-modal-container" role="dialog" aria-modal="true" aria-labelledby="easy-support-videos-contextual-videos-modal-title">
					<div id="easy-support-videos-contextual-videos-modal-actions" class="easy-support-videos-contextual-videos-modal-actions">
						<?php
							// Grab the Easy Support Videos contextual videos modal actions
							$easy_support_videos_contextual_videos_modal_actions = self::get_modal_actions();

							// If the Easy Support Videos contextual videos modal actions
							if ( ! empty( $easy_support_videos_contextual_videos_modal_actions ) ) :
								// Loop through the Easy Support Videos contextual videos modal actions
								foreach ( $easy_support_videos_contextual_videos_modal_actions as $easy_support_videos_contextual_videos_modal_action ) :
									// Easy Support Videos contextual videos modal action style attribute
									$easy_support_videos_contextual_videos_modal_action_style_attr = array();

									// If we have CSS for this Easy Support Videos contextual videos modal action
									if ( isset( $easy_support_videos_contextual_videos_modal_action['css'] ) ) {
										// If we have a color for this Easy Support Videos contextual videos modal action
										if ( isset( $easy_support_videos_contextual_videos_modal_action['css']['color'] ) )
											// Append the color for this Easy Support Videos contextual videos modal action to the Easy Support Videos contextual videos modal action style attribute
											$easy_support_videos_contextual_videos_modal_action_style_attr['color'] = $easy_support_videos_contextual_videos_modal_action['css']['color'];

										// If we have a background color for this Easy Support Videos contextual videos modal action
										if ( isset( $easy_support_videos_contextual_videos_modal_action['css']['background-color'] ) )
											// Append the background color for this Easy Support Videos contextual videos modal action to the Easy Support Videos contextual videos modal action style attribute
											$easy_support_videos_contextual_videos_modal_action_style_attr['background-color'] = $easy_support_videos_contextual_videos_modal_action['css']['background-color'];
									}
						?>
									<a href="<?php echo esc_url( $easy_support_videos_contextual_videos_modal_action['url'] ); ?>" class="easy-support-videos-contextual-videos-modal-action easy-support-videos-contextual-videos-modal-<?php echo esc_attr( $easy_support_videos_contextual_videos_modal_action['id'] ); ?>-action" style="<?php echo esc_attr( ( ! empty( $easy_support_videos_contextual_videos_modal_action_style_attr ) ) ? str_replace( '=', ': ', urldecode( http_build_query( $easy_support_videos_contextual_videos_modal_action_style_attr, null, '; ' ) ) ) : false ); ?>">
										<?php
											// If we have an icon for this Easy Support Videos contextual videos modal action
											if ( isset( $easy_support_videos_contextual_videos_modal_action['icon'] ) ) :
												// If the icon for this Easy Support Videos contextual videos modal action is a Dashicons icon
												if ( strpos( $easy_support_videos_contextual_videos_modal_action['icon'], 'dashicons' ) === 0 ) :
										?>
													<span class="dashicons <?php echo esc_attr( $easy_support_videos_contextual_videos_modal_action['icon'] ); ?> easy-support-videos-contextual-videos-modal-action-icon easy-support-videos-contextual-videos-modal-action-dashicons-icon"></span>
										<?php
												// Otherwise if the icon for this Easy Support Videos contextual videos modal action is a URL
												elseif ( strpos( $easy_support_videos_contextual_videos_modal_action['icon'], '://' ) ) :
										?>
													<img src="<?php echo esc_url( $easy_support_videos_contextual_videos_modal_action['icon'] ); ?>" class="easy-support-videos-contextual-videos-modal-action-icon easy-support-videos-contextual-videos-modal-action-icon-image" alt="<?php echo esc_attr( sprintf( __( '%1$s Icon', 'easy-support-videos' ), $easy_support_videos_contextual_videos_modal_action['label'] ) ); ?>" />
										<?php
												// Otherwise the icon for this Easy Support Videos contextual videos modal action is not a Dashcions icon or a URL
												else:
													echo $easy_support_videos_contextual_videos_modal_action['icon'];
												endif;
											endif;
										?>

										<span class="easy-support-videos-contextual-videos-modal-action-label">
											<?php echo $easy_support_videos_contextual_videos_modal_action['label']; ?>
										</span>
									</a>
						<?php
								endforeach;
							endif;
						?>
					</div>

					<header id="easy-support-videos-contextual-videos-modal-header" class="easy-support-videos-contextual-videos-modal-header">
						<div id="easy-support-videos-contextual-videos-modal-title" class="easy-support-videos-contextual-videos-modal-title">
							<?php Easy_Support_Videos_Admin_Views::page_title_template(); ?>
						</div>

						<?php
							// If the current user can edit Easy Support Videos
							if ( $current_user_can_edit_easy_support_videos ):
						?>
								<p id="easy-support-videos-contextual-videos-modal-header-description" class="easy-support-videos-contextual-videos-modal-header-description">
									<?php _e( 'Use this widget to add videos to the current context.', 'easy-support-videos' ); ?>
								</p>
						<?php
							endif;
						?>

						<button id="easy-support-videos-contextual-videos-modal-expand" class="easy-support-videos-contextual-videos-modal-expand" aria-label="<?php esc_attr_e( 'Expand the Easy Support Videos Contextual Videos Widget', 'easy-support-videos' ); ?>">
							<span class="screen-reader-text"><?php _e( 'Expand the Easy Support Videos Contextual Videos Widget', 'easy-support-videos' ); ?></span>
							<span class="easy-support-videos-contextual-videos-modal-expand-icon"></span>
						</button>

						<button id="easy-support-videos-contextual-videos-modal-close" class="easy-support-videos-contextual-videos-modal-close" aria-label="<?php esc_attr_e( 'Close the Easy Support Videos Contextual Videos Widget', 'easy-support-videos' ); ?>" data-micromodal-close>
							<span class="screen-reader-text"><?php _e( 'Close the Easy Support Videos Contextual Videos Widget', 'easy-support-videos' ); ?></span>
							<span class="dashicons dashicons-no-alt"></span>
						</button>
					</header>

					<main id="easy-support-videos-contextual-videos-modal-content" class="easy-support-videos-contextual-videos-modal-content">
						<div id="easy-support-videos-contextual-videos-wrap" class="easy-support-videos-contextual-videos-wrap">
							<?php do_action( 'easy_support_videos_contextual_videos_modal_videos_wrap_before', $current_user_can_edit_easy_support_videos, $easy_support_videos, $this ); ?>

							<?php
								// Easy Support Videos View
								Easy_Support_Videos_Admin_Views::render();
							?>

							<?php do_action( 'easy_support_videos_contextual_videos_modal_videos_wrap_after', $current_user_can_edit_easy_support_videos, $easy_support_videos, $this ); ?>
						</div>
					</main>
				</div>

				<?php do_action( 'easy_support_videos_contextual_videos_modal_after', $current_user_can_edit_easy_support_videos, $easy_support_videos, $this ); ?>
			</div>
		<?php
			do_action( 'easy_support_videos_contextual_videos_after', $current_user_can_edit_easy_support_videos, $easy_support_videos, $this );
		}


		/****************
		 * Settings API *
		 ****************/

		/**
		 * This function renders the Easy Support Videos Contextual Videos Section.
		 */
		public function easy_support_videos_contextual_videos_section() {
			self::easy_support_videos_options_contextual_videos_section();
		}

		/**
		 * This function renders the Easy Support Videos Contextual Videos Enabled Field.
		 */
		public function easy_support_videos_contextual_videos_enabled_field() {
			self::easy_support_videos_options_contextual_videos_enabled_field();
		}

		/**
		 * This function renders the Easy Support Videos Contextual Videos Read Role Field.
		 */
		public function easy_support_videos_contextual_videos_roles_read_field() {
			self::easy_support_videos_options_contextual_videos_roles_read_field();
		}

		/**
		 * This function renders the Easy Support Videos Contextual Videos Global Video Enabled Field.
		 */
		public function easy_support_videos_contextual_videos_global_video_enabled_field() {
			self::easy_support_videos_options_contextual_videos_global_video_enabled_field();
		}

		/**
		 * This function renders the Easy Support Videos Options Contextual Videos Section.
		 */
		public static function easy_support_videos_options_contextual_videos_section() {
			Easy_Support_Videos_Admin_Views::load_template( 'html-easy-support-videos-options-contextual-videos-section.php' );
		}

		/**
		 * This function renders the Easy Support Videos Options Contextual Videos Enabled Field.
		 */
		public static function easy_support_videos_options_contextual_videos_enabled_field() {
			Easy_Support_Videos_Admin_Views::load_template( 'html-easy-support-videos-options-contextual-videos-enabled-field.php' );
		}

		/**
		 * This function renders the Easy Support Videos Options Contextual Videos Read Role Field.
		 */
		public static function easy_support_videos_options_contextual_videos_roles_read_field() {
			Easy_Support_Videos_Admin_Views::load_template( 'html-easy-support-videos-options-contextual-videos-roles-read-field.php' );
		}

		/**
		 * This function renders the Easy Support Videos Options Contextual Videos Global Video Enabled Field.
		 */
		public static function easy_support_videos_options_contextual_videos_global_video_enabled_field() {
			Easy_Support_Videos_Admin_Views::load_template( 'html-easy-support-videos-options-contextual-videos-global-video-enabled-field.php' );
		}


		/***********************
		 * Easy Support Videos *
		 ***********************/

		/**
		 * This function renders Easy Support Videos Pro settings.
		 */
		public function easy_support_videos_settings() {
			// Contextual Videos Settings
			Easy_Support_Videos_Admin_Views::load_template( 'html-easy-support-videos-options-contextual-videos-settings.php' );
		}

		/**
		 * This function adjusts Easy Support Videos options defaults.
		 */
		public function easy_support_videos_options_defaults( $defaults, $option_name = false ) {
			// Bail if we have an option name
			if ( $option_name )
				return $defaults;

			/*
			 * Contextual Videos
			 */
			if ( ! array_key_exists( 'contextual_videos', $defaults ) )
				$defaults['contextual_videos'] = array(
					// Enabled
					'enabled' => false,
					// Roles
					'roles' => array(
						// Read
						'read' => 'edit_pages'
					),
					// Global Video
					'global_video' => array(
						// Enabled
						'enabled' => true
					)
				);

			return $defaults;
		}

		/**
		 * This function sanitizes Easy Support Videos options.
		 */
		public function easy_support_videos_options_sanitize_option( $value, $raw_value, $easy_support_videos_options, $easy_support_videos_options_defaults, $easy_support_videos_options_class ) {
			/*
			 * Contextual Videos - Enabled
			 */

			// Reset to Defaults
			if ( isset( $value['reset'] ) )
				$value['contextual_videos']['enabled'] = $easy_support_videos_options_defaults['contextual_videos']['enabled']; // Contextual Videos - Enabled
			// Otherwise use the POSTed data or existing data
			else
				$value['contextual_videos']['enabled'] = ( isset( $value['easy-support-videos-options-page'] ) ) ? ( isset( $value['contextual_videos']['enabled'] ) ) : ( ( isset( $easy_support_videos_options['contextual_videos']['enabled'] ) ) ? $easy_support_videos_options['contextual_videos']['enabled'] : $easy_support_videos_options_defaults['contextual_videos']['enabled'] ); // Contextual Videos - Enabled


			/*
			 * Contextual Videos - Roles
			 */

			// Reset to Defaults
			if ( isset( $value['reset'] ) )
				$value['contextual_videos']['roles']['read'] = $easy_support_videos_options_defaults['contextual_videos']['roles']['read']; // Contextual Videos - Roles - Read
			// Otherwise use the POSTed data or existing data
			else
				$value['contextual_videos']['roles']['read'] = ( isset( $value['contextual_videos']['roles']['read'] ) ) ? sanitize_text_field( $value['contextual_videos']['roles']['read'] ) : ( ( isset( $easy_support_videos_options['contextual_videos']['roles']['read'] ) ) ? $easy_support_videos_options['contextual_videos']['roles']['read'] : $easy_support_videos_options_defaults['contextual_videos']['roles']['read'] ); // Contextual Videos - Roles - Read

			// If the specified roles do not match the defaults
			if ( ! isset( $value['reset'] ) && $value['contextual_videos']['roles']['read'] !== $easy_support_videos_options_defaults['contextual_videos']['roles']['read'] ) {
				// Grab the global WP_Roles instance
				$wp_roles = wp_roles();

				// Grab all editable roles
				$editable_roles = get_editable_roles();

				// If we have roles
				if ( ! empty( $editable_roles ) ) {
					// Flag to determine if the read capability is valid
					$is_read_cap_valid = ( $value['contextual_videos']['roles']['read'] !== $easy_support_videos_options_defaults['contextual_videos']['roles']['read'] );

					// Loop through roles
					foreach ( array_keys( $editable_roles ) as $role ) {
						// Grab the WP_Role
						$wp_role = $wp_roles->get_role( $role );

						// Check if this role has the specified read capability
						if ( ! $is_read_cap_valid && $wp_role->has_cap( $value['contextual_videos']['roles']['read'] ) ) {
							// Set the read capability valid flag
							$is_read_cap_valid = true;

							// Break from the loop
							break;
						}
					}

					// If the read capability isn't valid
					if ( ! $is_read_cap_valid )
						// Set the contextual videos read capability to the default
						$value['contextual_videos']['roles']['read'] = $easy_support_videos_options_defaults['contextual_videos']['roles']['read'];
				}
			}


			/*
			 * Contextual Videos - Global Video
			 */

			// Reset to Defaults
			if ( isset( $value['reset'] ) )
				$value['contextual_videos']['global_video']['enabled'] = $easy_support_videos_options_defaults['contextual_videos']['global_video']['enabled']; // Contextual Videos - Global Video - Enabled
			// Otherwise use the POSTed data or existing data
			else
				$value['contextual_videos']['global_video']['enabled'] = ( isset( $value['easy-support-videos-options-page'] ) ) ? ( isset( $value['contextual_videos']['global_video']['enabled'] ) ) : ( ( isset( $easy_support_videos_options['contextual_videos']['global_video']['enabled'] ) ) ? $easy_support_videos_options['contextual_videos']['global_video']['enabled'] : $easy_support_videos_options_defaults['contextual_videos']['global_video']['enabled'] ); // Contextual Videos - Global Video - Enabled

			return $value;
		}

		/**
		 * This function runs before the Easy Support Videos video template is output.
		 */
		public function easy_support_videos_video_template_before() {
			// Bail if Easy Support Videos contextual videos is not enabled
			if ( ! self::is_enabled() )
				return;

			// Grab the current context ID
			$current_context_id = self::get_current_context_id();

			// Bail if we don't the current context ID
			if ( ! $current_context_id )
				return;

			do_action( 'easy_support_videos_contextual_videos_video_template_before', $current_context_id );
		}

		/**
		 * This function runs after the Easy Support Videos video template is output.
		 */
		public function easy_support_videos_video_template_after() {
			// Bail if Easy Support Videos contextual videos is not enabled
			if ( ! self::is_enabled() )
				return;

			// Grab the current context ID
			$current_context_id = self::get_current_context_id();

			// Bail if we don't the current context ID
			if ( ! $current_context_id )
				return;

			do_action( 'easy_support_videos_contextual_videos_video_template_after', $current_context_id );
		}


		/**
		 * This function runs before wp_insert_post() is called in the Easy Support Videos Insert Video
		 * AJAX request.
		 */
		public function wp_ajax_easy_support_videos_insert_wp_insert_post_before( $title, $html, $easy_support_videos_post_type, $data, $provider, $wp_oembed, $easy_support_videos_post_types, $url ) {
			// Bail if Easy Support Videos contextual videos is not enabled
			if ( ! self::is_enabled() )
				return;

			// Grab the contextual videos context ID
			$context_id = ( isset( $_POST['contextual_videos'] ) && isset( $_POST['contextual_videos']['id'] ) ) ? sanitize_text_field( $_POST['contextual_videos']['id'] ) : false;

			// Bail if we don't have a contextual videos context ID
			if ( ! $context_id )
				return;

			// Set the current context on this class
			self::get_current_context( $context_id );

			// Grab the Easy Support Videos contextual videos video IDs for the current context
			$video_ids_for_current_context = self::get_video_ids_for_current_context();

			// Return an error if we have met the contextual videos limit
			if ( count( $video_ids_for_current_context ) >= self::$context_video_limit ) {
				$status['error'] = __( 'You cannot add any more videos to this context.', 'easy-support-videos' );
				wp_send_json_error( $status );
			}

			do_action( 'wp_ajax_easy_support_videos_contextual_videos_insert_wp_insert_post_before', $context_id, $video_ids_for_current_context, $title, $html, $easy_support_videos_post_type, $data, $provider, $wp_oembed, $easy_support_videos_post_types, $url );
		}

		/**
		 * This function adjusts the flag to determine whether or not an Easy Support Videos video
		 * can be inserted.
		 */
		public function wp_ajax_easy_support_videos_insert_can_insert_video( $can_insert_video, $wp_insert_post_args, $title, $html, $easy_support_videos_post_type, $data, $provider, $wp_oembed, $url, $easy_support_videos_post_types ) {
			// Bail if Easy Support Videos contextual videos is not enabled
			if ( ! self::is_enabled() )
				return $can_insert_video;

			// Bail if we can't insert the video
			if ( ! $can_insert_video )
				return $can_insert_video;

			// Easy Support Videos IDs query arguments
			$easy_support_videos_contextual_video_ids_args = Easy_Support_Videos_Post_Types::$query_args;

			// Set the Easy Support Videos IDs fields query argument
			$easy_support_videos_contextual_video_ids_args['fields'] = 'ids';

			// Set the posts per page
			$easy_support_videos_contextual_video_ids_args['posts_per_page'] = 1;

			// Set the meta key query argument TODO: Future: Add this meta key as a property on this class
			$easy_support_videos_contextual_video_ids_args['meta_key'] = 'easy_support_videos_url';

			// Set the meta value query argument
			$easy_support_videos_contextual_video_ids_args['meta_value'] = $url;

			// Grab the Easy Support Videos contextual video ID
			$easy_support_videos_contextual_video_id = new WP_Query( $easy_support_videos_contextual_video_ids_args );

			// Bail if we don't have the Easy Support Videos contextual video ID
			if ( ! $easy_support_videos_contextual_video_id->have_posts() )
				return $can_insert_video;

			// Set the AJAX Easy Support Videos insert post ID on this class
			self::$wp_ajax_easy_support_videos_insert_post_id = $easy_support_videos_contextual_video_id->next_post();

			// Reset the can insert video flag
			$can_insert_video = false;

			return $can_insert_video;
		}

		/**
		 * This function adjusts the Easy Support Videos AJAX insert post ID.
		 */
		public function wp_ajax_easy_support_videos_insert_post_id( $post_id, $wp_insert_post_args, $title, $html, $easy_support_videos_post_type, $data, $provider, $wp_oembed, $url, $easy_support_videos_post_types ) {
			// Bail if Easy Support Videos contextual videos is not enabled
			if ( ! self::is_enabled() )
				return $post_id;

			// Grab the contextual videos context ID
			$context_id = ( isset( $_POST['contextual_videos'] ) && isset( $_POST['contextual_videos']['id'] ) ) ? sanitize_text_field( $_POST['contextual_videos']['id'] ) : false;

			// Bail if we don't have a contextual videos context ID
			if ( ! $context_id )
				return $post_id;

			// Bail if we have a post ID or we don't have an Easy Support Videos AJAX insert post ID
			if ( $post_id !== null || ! self::$wp_ajax_easy_support_videos_insert_post_id )
				return $post_id;

			// Set the post ID
			$post_id = self::$wp_ajax_easy_support_videos_insert_post_id;

			return $post_id;
		}

		/**
		 * This function runs after wp_insert_post() is called in the Easy Support Videos Insert Video
		 * AJAX request.
		 */
		public function wp_ajax_easy_support_videos_insert_wp_insert_post_after( $post_id, $title, $html, $easy_support_videos_post_type, $data, $provider, $wp_oembed, $easy_support_videos_post_types, $url ) {
			// Bail if Easy Support Videos contextual videos is not enabled
			if ( ! self::is_enabled() )
				return;

			// Grab the contextual videos context ID
			$context_id = ( isset( $_POST['contextual_videos'] ) && isset( $_POST['contextual_videos']['id'] ) ) ? sanitize_text_field( $_POST['contextual_videos']['id'] ) : false;

			// Bail if we don't have a contextual videos context ID
			if ( ! $context_id )
				return;

			// Grab the contextual videos context meta value
			$contextual_videos_contexts = get_post_meta( $post_id, self::$contextual_videos_context_meta_key );

			// Bail if this context ID already exists in the contextual videos contexts
			if ( ! empty( $contextual_videos_contexts ) && in_array( $context_id, $contextual_videos_contexts ) )
				return;

			// Add the contextual videos context meta value
			add_post_meta( $post_id, self::$contextual_videos_context_meta_key, $context_id );

			// Grab the transient name (force)
			$transient_name = self::get_video_ids_for_current_context_transient_name( true );

			// If we have the Easy Support Videos contextual videos transient name for the current context
			if ( $transient_name ) {
				// Delete the Easy Support Videos contextual videos transient for the current context
				delete_transient( $transient_name );

				/*
				 * Grab the Easy Support Videos contextual videos video IDs for the current context.
				 *
				 * Note: This will set the transient for the current context.
				 */
				self::get_video_ids_for_current_context( true );
			}

			do_action( 'wp_ajax_easy_support_videos_contextual_videos_insert_wp_insert_post_after', $context_id, $contextual_videos_contexts, $title, $html, $easy_support_videos_post_type, $data, $provider, $wp_oembed, $easy_support_videos_post_types, $url );
		}

		/**
		 * This function adjusts the Easy Support Videos insert video AJAX request success status.
		 */
		public function wp_ajax_easy_support_videos_insert_success_status( $status, $post_id, $title, $html, $easy_support_videos_post_type, $data, $provider, $wp_oembed, $easy_support_videos_post_types ) {
			// Bail if Easy Support Videos contextual videos is not enabled
			if ( ! self::is_enabled() )
				return $status;

			// Grab the contextual videos context ID
			$context_id = ( isset( $_POST['contextual_videos'] ) && isset( $_POST['contextual_videos']['id'] ) ) ? sanitize_text_field( $_POST['contextual_videos']['id'] ) : false;

			// Bail if we don't have a contextual videos context ID
			if ( ! $context_id )
				return $status;

			// Bail if the post ID doesn't match the Easy Support Videos AJAX insert post ID
			if ( $post_id !== self::$wp_ajax_easy_support_videos_insert_post_id )
				return $status;

			// Add the contextual videos status flag
			$status['contextual_videos'] = true;

			// Grab the post
			$post = get_post( $post_id );

			// Set the status excerpt
			$status['excerpt'] = wp_unslash( get_post_field( 'post_excerpt', $post ) );

			// Set the excerpt length
			$status['excerpt_length'] = strlen( $status['excerpt'] );

			// Set the status message
			$status['message'] = __( 'Video added to context.', 'easy-support-videos' );

			return $status;
		}

		/**
		 * This function runs before wp_delete_post() is called in the Easy Support Videos Delete Video
		 * AJAX request.
		 */
		public function wp_ajax_easy_support_videos_delete_wp_delete_post_before( $post_id, $easy_support_videos_post_types ) {
			// Bail if Easy Support Videos contextual videos is not enabled
			if ( ! self::is_enabled() )
				return;

			// Grab the contextual videos context ID
			$context_id = ( isset( $_POST['contextual_videos'] ) && isset( $_POST['contextual_videos']['id'] ) ) ? sanitize_text_field( $_POST['contextual_videos']['id'] ) : false;

			// Bail if we don't have a contextual videos context ID
			if ( ! $context_id )
				return;

			// Set the current context on this class
			self::get_current_context( $context_id );

			// Grab the contextual videos context meta value
			$contextual_videos_contexts = get_post_meta( $post_id, self::$contextual_videos_context_meta_key );

			// Bail if we don't have contextual videos contexts or this context ID doesn't exist in the contextual videos contexts
			if ( empty( $contextual_videos_contexts ) || ! in_array( $context_id, $contextual_videos_contexts ) )
				return;

			// Set the AJAX Easy Support Videos delete post ID
			self::$wp_ajax_easy_support_videos_delete_post_id = $post_id;
		}

		/**
		 * This function adjusts the flag to determine whether or not an Easy Support Videos video
		 * can be deleted.
		 */
		public function wp_ajax_easy_support_videos_delete_can_delete_video( $can_delete_video, $post_id, $easy_support_videos_post_types ) {
			// Bail if Easy Support Videos contextual videos is not enabled
			if ( ! self::is_enabled() )
				return $can_delete_video;

			// Bail if we can't delete the video
			if ( ! $can_delete_video )
				return $can_delete_video;

			// Bail if we don't have an Easy Support Videos AJAX delete post ID or the post ID doesn't match the Easy Support Videos AJAX delete post ID
			if ( ! self::$wp_ajax_easy_support_videos_delete_post_id || $post_id !== self::$wp_ajax_easy_support_videos_delete_post_id ) {
				// Grab the current context for global video flag on this class
				$is_current_context_for_global_video = self::$is_current_context_for_global_video;

				// Grab the current context on this class
				$current_context = self::$current_context;

				// Grab the current context ID on this class
				$current_context_id = self::$current_context_id;

				// Grab the contextual videos active contexts transient
				$contextual_videos_active_contexts = get_transient( self::$active_contexts_transient_name );

				// If we have the contextual videos active contexts
				if ( $contextual_videos_active_contexts ) {
					// Grab the contextual videos context meta value
					$contextual_videos_contexts = get_post_meta( $post_id, self::$contextual_videos_context_meta_key );

					// Loop through the active contextual video contexts
					foreach ( $contextual_videos_active_contexts as $contextual_videos_active_context ) {
						// If this is the global video context
						if ( $contextual_videos_active_context === self::$global_video_context )
							// Set the current context for global video flag on this class
							self::$is_current_context_for_global_video = true;
						// Otherwise this isn't the global video context
						else
							// Reset the current context for global video flag on this class
							self::$is_current_context_for_global_video = false;

						// Reset the current context on this class
						self::$current_context = false;

						// Set the current context on this class
						self::get_current_context( $contextual_videos_active_context );

						/*
						 * Reset the current context ID on this class.
						 *
						 * Note: This is required for Easy_Support_Video_Admin_Contextual_Videos::get_video_ids_for_current_context_transient_name().
						 * The transient name will not be returned if we do not reset this property
						 * on this class.
						 */
						self::$current_context_id = false;

						// If this active context ID exists in the contextual videos contexts
						if ( ! empty( $contextual_videos_contexts ) && in_array( $contextual_videos_active_context, $contextual_videos_contexts ) ) {
							// Delete the contextual videos context meta value for this active context
							delete_post_meta( $post_id, self::$contextual_videos_context_meta_key, $contextual_videos_active_context );

							// Grab the transient name (force)
							$transient_name = self::get_video_ids_for_current_context_transient_name( true );

							// If we have the Easy Support Videos contextual videos transient name for the current context
							if ( $transient_name ) {
								// Delete the Easy Support Videos contextual videos transient for the current context
								delete_transient( $transient_name );

								/*
								 * Grab the Easy Support Videos contextual videos video IDs for the current context.
								 *
								 * Note: this will set the transient for the current context.
								 */
								self::get_video_ids_for_current_context( true );
							}
						}
					}
				}

				// Reset the current context on this class
				self::$current_context = $current_context;

				// Reset the current context ID on this class
				self::$current_context_id = $current_context_id;

				// Reset the current context for global video flag on this class
				self::$is_current_context_for_global_video = $is_current_context_for_global_video;

				return $can_delete_video;
			}

			// Reset the can delete video flag
			$can_delete_video = false;

			return $can_delete_video;
		}

		/**
		 * This function adjusts the Easy Support Videos AJAX deleted post.
		 */
		public function wp_ajax_easy_support_videos_deleted_post( $post, $post_id, $easy_support_videos_post_types ) {
			// Bail if Easy Support Videos contextual videos is not enabled
			if ( ! self::is_enabled() )
				return $post;

			// Grab the contextual videos context ID
			$context_id = ( isset( $_POST['contextual_videos'] ) && isset( $_POST['contextual_videos']['id'] ) ) ? sanitize_text_field( $_POST['contextual_videos']['id'] ) : false;

			// Bail if we don't have a contextual videos context ID
			if ( ! $context_id )
				return $post;

			// Bail if we have a post or we don't have an Easy Support Videos AJAX delete post ID
			if ( $post !== null || ! self::$wp_ajax_easy_support_videos_delete_post_id )
				return $post;

			// Set the post
			$post = get_post( $post_id );

			return $post;
		}

		/**
		 * This function runs after wp_delete_post() is called in the Easy Support Videos Delete Video
		 * AJAX request.
		 */
		public function wp_ajax_easy_support_videos_delete_wp_delete_post_after( $post, $post_id, $url ) {
			// Bail if Easy Support Videos contextual videos is not enabled
			if ( ! self::is_enabled() )
				return;

			// Grab the contextual videos context ID
			$context_id = ( isset( $_POST['contextual_videos'] ) && isset( $_POST['contextual_videos']['id'] ) ) ? sanitize_text_field( $_POST['contextual_videos']['id'] ) : false;

			// Bail if we don't have a contextual videos context ID
			if ( ! $context_id )
				return;

			// Grab the contextual videos context meta value
			$contextual_videos_contexts = get_post_meta( $post_id, self::$contextual_videos_context_meta_key );

			// Bail if we don't have contextual video contexts or this context ID doesn't exist in the contextual videos contexts
			if ( empty( $contextual_videos_contexts ) || ! in_array( $context_id, $contextual_videos_contexts ) )
				return;

			// Bail if we don't have an Easy Support Videos AJAX delete post ID
			if ( ! self::$wp_ajax_easy_support_videos_delete_post_id )
				return;

			// Delete the contextual videos context meta value
			delete_post_meta( $post_id, self::$contextual_videos_context_meta_key, $context_id );

			// Grab the transient name
			$transient_name = self::get_video_ids_for_current_context_transient_name();

			// If we have the Easy Support Videos contextual videos transient name for the current context
			if ( $transient_name ) {
				// Delete the Easy Support Videos contextual videos transient for the current context
				delete_transient( $transient_name );

				/*
				 * Grab the Easy Support Videos contextual videos video IDs for the current context.
				 *
				 * Note: This will set the transient for the current context.
				 */
				self::get_video_ids_for_current_context();
			}
		}

		/**
		 * This function adjusts the Easy Support Videos delete video AJAX request success status.
		 */
		public function wp_ajax_easy_support_videos_delete_success_status( $status, $post_id, $easy_support_videos_post_types, $post ) {
			// Bail if Easy Support Videos contextual videos is not enabled
			if ( ! self::is_enabled() )
				return $status;

			// Grab the contextual videos context ID
			$context_id = ( isset( $_POST['contextual_videos'] ) && isset( $_POST['contextual_videos']['id'] ) ) ? sanitize_text_field( $_POST['contextual_videos']['id'] ) : false;

			// Bail if we don't have a contextual videos context ID
			if ( ! $context_id )
				return $status;

			// Bail if the post ID doesn't match the Easy Support Videos AJAX delete post ID
			if ( $post_id !== self::$wp_ajax_easy_support_videos_delete_post_id )
				return $status;

			// Add the contextual videos status flag
			$status['contextual_videos'] = true;

			// Set the status message
			$status['message'] = __( 'Video removed from context.', 'easy-support-videos' );

			return $status;
		}

		/**
		 * This function adjusts query arguments for the Easy Support Videos video query.
		 */
		public function easy_support_videos_videos_args( $query_args ) {
			// Bail if Easy Support Videos contextual videos is not enabled
			if ( ! self::is_enabled() )
				return $query_args;

			// Grab the current context ID
			$current_context_id = self::get_current_context_id();

			// If we have a current context ID
			if ( $current_context_id ) {
				// If the meta query query argument doesn't exist
				if ( ! isset( $query_args['meta_query'] ) )
					// Set the meta query query argument
					$query_args['meta_query'] = array();

				// Add the contextual videos meta query query argument
				$query_args['meta_query'] = array_replace( $query_args['meta_query'], array(
					self::$meta_query_name => array(
						'key' => self::$contextual_videos_context_meta_key,
						'value' => $current_context_id
					)
				) );

				// If the current context is for the global video
				if ( self::is_current_context_for_global_video() )
					// Set the contextual videos meta query value query argument
					$query_args['meta_query'][self::$meta_query_name]['value'] = self::$global_video_context;
			}

			return $query_args;
		}

		/**
		 * This function adjusts the Easy Support Videos videos query.
		 */
		public function easy_support_videos_videos( $query, $query_args ) {
			// Bail if Easy Support Videos contextual videos is not enabled
			if ( ! self::is_enabled() )
				return $query;

			// Bail if we already have a query
			if ( $query )
				return $query;

			// Grab the current context
			$current_context = self::get_current_context();

			// If we have a current context
			if ( $current_context ) {
				// If the Easy Support Videos Contextual Videos WP Query PHP class doesn't exist
				if ( ! class_exists( 'Easy_Support_Videos_Contextual_Videos_WP_Query' ) )
					// Include the Easy Support Videos Contextual Videos WP Query PHP class
					include_once Easy_Support_Videos::plugin_dir() . '/includes/class-easy-support-videos-contextual-videos-wp-query.php';

				// Grab the Easy Support Videos contextual videos video IDs for the current context
				$video_ids_for_current_context = self::get_video_ids_for_current_context();

				// Set the Easy Support Videos contextual videos query argument
				$query_args[self::$contextual_videos_query_arg] = $video_ids_for_current_context;

				// If the current user can't edit Easy Support Videos
				if ( ! Easy_Support_Videos_Post_Types::current_user_can( Easy_Support_Videos_Post_Types::$easy_support_videos_post_type, 'edit_posts' ) ) {
					// Reset the update post term cache query argument
					$query_args['update_post_meta_cache'] = false;

					// Reset the update post term cache query argument
					$query_args['update_post_term_cache'] = false;
				}

				// Set the query
				$query = new Easy_Support_Videos_Contextual_Videos_WP_Query( $query_args );
			}


			return $query;
		}

		/**
		 * This function outputs content after the video within an Easy Support Videos video
		 * container element.
		 */
		public function easy_support_videos_video_inner_after( $post ) {
			global $hook_suffix;

			// Bail if Easy Support Videos contextual videos is not enabled or we're not on the Easy Support Videos menu page
			if ( ! self::is_enabled() || $hook_suffix !== Easy_Support_Videos_Post_Types::get_easy_support_videos_menu_page( false ) )
				return;

			// Current user can edit Easy Support Videos
			$current_user_can_edit_easy_support_videos = Easy_Support_Videos_Post_Types::current_user_can( Easy_Support_Videos_Post_Types::$easy_support_videos_post_type, 'edit_posts' );

			// Bail if the current user can't edit Easy Support Videos
			if ( ! $current_user_can_edit_easy_support_videos )
				return;

			// Grab the post ID (default to "{{ data.post_id }}" if the post object is empty, such as in the UnderscoreJS template)
			$post_id = ( ! empty( $post ) ) ? ( int ) get_post_field( 'ID', $post ) : '{{ data.post_id }}';

			// Grab the contextual videos contexts
			$contextual_videos_contexts = ( ! empty( $post ) ) ? $this->get_context_data_for_video( $post ): array();
		?>
			<div class="easy-support-videos-contextual-videos-contexts-wrap">
				<?php
					// If we have contextual videos contexts
					if ( ! empty( $contextual_videos_contexts ) ) :
						// Grab the contextual video contexts last index
						$contextual_videos_contexts_last_index = key( array_slice( $contextual_videos_contexts, -1, 1, true ) );
				?>
						<div class="easy-support-videos-contextual-videos-contexts">
							<span class="easy-support-videos-contextual-videos-contexts-label"><strong><?php _e( 'Context(s):', 'easy-support-videos' ); ?></strong></span>
							<?php
								// Loop through the contextual videos contexts
								foreach ( $contextual_videos_contexts as $contextual_videos_context_index => $contextual_videos_context_data ) :
									// TODO: Future: Also add $contextual_videos_context_menu_context as a CSS class (if different than context)?
							?>
									<span class="easy-support-videos-contextual-videos-context easy-support-videos-contextual-videos-context-<?php echo esc_attr( $contextual_videos_context_data['id'] ); ?>">
										<?php do_action( 'easy_support_videos_contextual_videos_context_before', $contextual_videos_context_data, $post ); // TODO: Future: Add other arguments here ?>

										<?php
											// Flag to determine if we should display the separator
											$can_display_separator = apply_filters( 'easy_support_videos_contextual_videos_context_can_display_separator', ( $contextual_videos_context_index !== $contextual_videos_contexts_last_index ), $contextual_videos_context_data, $contextual_videos_context_index, $contextual_videos_contexts_last_index, $post, $this );

											// If we have a contextual videos context menu item
											if ( ! empty( $contextual_videos_context_data['menu'] ) ) {
												// If we have a contextual videos context URL
												if ( $contextual_videos_context_data['url'] )
													// Set the displayed contextual videos context
													$displayed_contextual_videos_context = sprintf( __( '<a href="%1$s">%2$s</a>', 'easy-support-videos' ), $contextual_videos_context_data['url'], $contextual_videos_context_data['menu'][0] );
												// Otherwise we don't have a contextual videos context URL
												else
													// Set the displayed contextual videos context
													$displayed_contextual_videos_context = $contextual_videos_context_data['menu'][0];
											}
											// Otherwise we don't have a contextual videos context menu item
											else
												// Set the displayed contextual videos context
												$displayed_contextual_videos_context = $contextual_videos_context_data['id'];
										?>
										<?php
											// If we can display the separator and we have a displayed contextual videos context
											if ( $can_display_separator && $displayed_contextual_videos_context )
												// Append the separator to the displayed contextual videos context
												$displayed_contextual_videos_context .= apply_filters( 'easy_support_videos_contextual_videos_context_separator', ',', $contextual_videos_context_data, $contextual_videos_context_index, $contextual_videos_contexts_last_index, $post, $this );

											// If we have a displayed contextual videos context
											if ( ! empty( $displayed_contextual_videos_context ) )
												// Output the displayed contextual videos context
												echo $displayed_contextual_videos_context;
										?>

										<?php do_action( 'easy_support_videos_contextual_videos_context_after', $contextual_videos_context_data, $post ); // TODO: Future: Add other arguments here ?>
									</span>
							<?php
								endforeach;
							?>
						</div>
				<?php
					endif;

					// If the Easy Support Videos contextual videos global video is enabled
					if ( self::is_global_video_enabled() ) :
				?>
						<div class="easy-support-videos-contextual-videos-set-global-video-context-toggle" data-post-id="<?php echo esc_attr( $post_id  ); ?>">
							<span class="easy-support-videos-contextual-videos-set-global-video-context-toggle-label"><strong><?php _e( 'Set as Global Video?:', 'easy-support-videos' ); ?></strong></span>
							<div class="easy-support-videos-toggle-checkbox easy-support-videos-toggle-set-global-video" data-label-left="<?php esc_attr_e( 'Yes', 'easy-support-videos' ); ?>" data-label-right="<?php esc_attr_e( 'No', 'easy-support-videos' ); ?>">
								<input id="<?php echo esc_attr( 'easy-support-videos-contextual-videos-' . $post_id . '-set-global-video' ); ?>" class="easy-support-videos-checkbox easy-support-videos-contextual-videos-set-global-video" name="easy_support_videos_contextual_videos_set_global_video" type="checkbox" <?php checked( ( ! empty( $contextual_videos_contexts ) && isset( $contextual_videos_contexts[self::$global_video_context] ) ) ); ?> />
								<label for="<?php echo esc_attr( 'easy-support-videos-contextual-videos-' . $post_id . '-set-global-video' ); ?>">| | |</label>
							</div>
							<p class="description easy-support-videos-contextual-videos-set-global-video-context-toggle-description"><?php _e( 'Only visible to logged in users who cannot edit Easy Support Videos.', 'easy-support-videos' ); ?></p>
						</div>
				<?php
					endif;
				?>
			</div>
		<?php
		}

		/**
		 * This function runs after the Easy Support Videos inner section is rendered
		 */
		public function easy_support_videos_inner_after( $easy_support_videos, $current_user_can_edit_easy_support_videos ) {
			// Bail if Easy Support Videos contextual videos is not enabled
			if ( ! self::is_enabled() )
				return;

			// Bail if the current user can't edit Easy Support Videos
			if ( ! $current_user_can_edit_easy_support_videos )
				return;

			// Grab the current context ID
			$current_context_id = self::get_current_context_id();

			// Bail if we don't the current context ID
			if ( ! $current_context_id )
				return;
		?>
			<input class="easy-support-videos-input easy-support-videos-input-hidden easy-support-videos-context-id easy-support-videos-context-<?php echo esc_attr( str_replace( '|', '-', $current_context_id ) ); ?>-context-id" type="hidden" value="<?php echo esc_attr( $current_context_id ); ?>" />
		<?php

			// If the Easy Support Videos contextual videos global video is enabled
			if ( self::is_global_video_enabled() ) :
				// TODO: Future: ?
				// Set the current context on this class
				//self::$current_context = self::$global_video_context;

				// Reset the current context ID on this class
				// self::$current_context_id = false;

				// Grab the original current context for global video flag on this class
				$original_is_current_context_for_global_video = self::$is_current_context_for_global_video;

				// Set the current context for global video flag on this class
				self::$is_current_context_for_global_video = true;

				// Grab the Easy Support Videos contextual videos video IDs for the global context (force)
				$video_ids_for_global_context = self::get_video_ids_for_current_context( true );

				// Reset the current context for global video flag on this class
				self::$is_current_context_for_global_video = $original_is_current_context_for_global_video;

				// If we have video IDs for the global context
				if ( $video_ids_for_global_context ) :
					// Grab the global context video ID
					$global_context_video_id = reset( $video_ids_for_global_context );

					// Grab the global context video
					$global_context_video = get_post( $global_context_video_id );
		?>
					<div id="easy-support-videos-global-video" class="easy-support-videos-global-video">
						<p class="description easy-support-videos-description"><?php printf( __( 'Note: The <strong>%1$s</strong> global video will appear in this context for viewers unless a video is added above.', 'easy-support-videos' ), get_the_title( $global_context_video ) ); ?></p>
					</div>
		<?php
				endif;
			endif;
		}

		/**
		 * This function adjusts the Easy Support Videos admin views template.
		 */
		public function easy_support_videos_admin_views_load_template( $the_template, $template, $require_once, $data ) {
			// Bail if this isn't the sidebar template
			if ( $template !== 'html-easy-support-videos-sidebar.php' )
				return $the_template;

			// Reset the template
			$the_template = '';

			return $the_template;
		}



		/*******************************************
		 * Easy Support Videos - Contextual Videos *
		 *******************************************/

		/**
		 * This function runs before the Easy Support Videos contextual videos modal videos wrap is output.
		 */
		public function easy_support_videos_contextual_videos_modal_videos_wrap_before( $current_user_can_edit_easy_support_videos, $easy_support_videos_admin_contextual_videos ) {
			// Remove all "easy_support_videos_notifications" actions
			remove_all_actions( 'easy_support_videos_notifications' );

			// Hook into "easy_support_videos_admin_views_load_template"
			add_filter( 'easy_support_videos_admin_views_load_template', array( $this, 'easy_support_videos_admin_views_load_template' ), 10, 4 );
		}

		/**
		 * This function runs after the Easy Support Videos contextual videos modal videos wrap is output.
		 */
		public function easy_support_videos_contextual_videos_modal_videos_wrap_after( $current_user_can_edit_easy_support_videos, $easy_support_videos_admin_contextual_videos ) {
			// Remove the "easy_support_videos_admin_views_load_template" hook
			remove_filter( 'easy_support_videos_admin_views_load_template', array( $this, 'easy_support_videos_admin_views_load_template' ) );
		}


		/********
		 * AJAX *
		 ********/

		/**
		 * This function handles the AJAX request for setting the Easy Support Videos contextual
		 * videos global video.
		 */
		public function wp_ajax_easy_support_videos_contextual_videos_set_global_video() {
			global $wpdb;

			// Status
			$status = array();

			// Generic error message
			$error = apply_filters( 'wp_ajax_easy_support_videos_contextual_videos_set_global_video_error_message', __( 'There was an error editing the contextual videos global video. Please try again.', 'easy-support-videos' ), $this );

			// Bail if Easy Support Videos contextual videos is not enabled
			if ( ! self::is_enabled() ) {
				$status['error'] = $error;
				wp_send_json_error( $status );
			}

			// If the Easy Support Videos contextual videos global video isn't enabled
			if ( ! self::is_global_video_enabled() ) {
				$status['error'] = $error;
				wp_send_json_error( $status );
			}

			// Check AJAX referrer
			if ( ! check_ajax_referer( 'easy_support_videos_edit', 'nonce', false ) ) {
				$status['error'] = $error;
				wp_send_json_error( $status );
			}

			// Return an error if the current user can't edit Easy Support Videos
			if ( ! Easy_Support_Videos_Post_Types::current_user_can( Easy_Support_Videos_Post_Types::$easy_support_videos_post_type, 'edit_posts' ) ) {
				$status['error'] = apply_filters( 'wp_ajax_easy_support_videos_contextual_videos_set_global_video_permissions_error_message', __( 'You do not have sufficient permissions to edit Easy Support Videos on this site.', 'easy-support-videos' ), $this );
				wp_send_json_error( $status );
			}

			// Grab the post ID
			$post_id = ( int ) $_POST['post_id'];

			// Return an error if the post ID is missing
			if ( ! $post_id ) {
				$status['error'] = $error;
				wp_send_json_error( $status );
			}

			// Return an error if the post type isn't Easy Support Videos
			if ( get_post_type( $post_id ) !== Easy_Support_Videos_Post_Types::$easy_support_videos_post_type ) {
				$status['post_id'] = $post_id;
				$status['error'] = $error;
				wp_send_json_error( $status );
			}

			// Grab the set global video flag
			$set_global_video = ( $_POST['set_global_video'] === 'true' );

			// Grab the current global video post ID
			$current_global_video_post_id = ( int ) $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE {$wpdb->postmeta}.meta_key = %s AND {$wpdb->postmeta}.meta_value = %s", self::$contextual_videos_context_meta_key, self::$global_video_context ) );


			// TODO: Future: Add actions before and after setting global video: do_action( 'wp_ajax_easy_support_videos_edit_wp_update_post_before', $post_id, $title, $this );

			// If we're setting the global video
			if ( $set_global_video ) {
				// If we have a current global video post ID
				if ( $current_global_video_post_id ) {
					// Bail if the post ID matches the current global video post ID
					if ( $post_id === $current_global_video_post_id ) {
						$status['post_id'] = $post_id;
						$status['error'] = $error;
						wp_send_json_error( $status );
					}

					// Remove the current global video context
					delete_post_meta( $current_global_video_post_id, self::$contextual_videos_context_meta_key, self::$global_video_context );
				}

				// Add the global video context to the post ID
				add_post_meta( $post_id, self::$contextual_videos_context_meta_key, self::$global_video_context );

				// Set the current context for global video flag on this class
				self::$is_current_context_for_global_video = true;

				/*
				 * Set the current context on this class.
				 *
				 * Note: This works because we are resetting the Easy_Support_Videos_Admin_Contextual_Videos::$current_context_id
				 * property below and have already set Easy_Support_Videos_Admin_Contextual_Videos::$is_current_context_for_global_video
				 * above. Normally this would need to be an instance of WP_Screen().
				 */
				self::$current_context = self::$global_video_context;

				/*
				 * Reset the current context ID on this class.
				 *
				 * Note: This is required for Easy_Support_Video_Admin_Contextual_Videos::get_video_ids_for_current_context_transient_name().
				 * The transient name will not be returned if we do not reset this property
				 * on this class.
				 */
				self::$current_context_id = false;

				// Grab the transient name (force)
				$transient_name = self::get_video_ids_for_current_context_transient_name( true );

				// If we have the Easy Support Videos contextual videos transient name for the current context
				if ( $transient_name ) {
					// Delete the Easy Support Videos contextual videos transient for the current context
					delete_transient( $transient_name );

					/*
					 * Grab the Easy Support Videos contextual videos video IDs for the current context.
					 *
					 * Note: This will set the transient for the current context.
					 */
					self::get_video_ids_for_current_context( true );
				}
			}
			// Otherwise we're not setting the global video
			else {
				// If we have a current global video post ID
				if ( $current_global_video_post_id ) {
					// Bail if the post ID doesn't match the current global video post ID
					if ( $post_id !== $current_global_video_post_id ) {
						$status['post_id'] = $post_id;
						$status['error'] = $error;
						wp_send_json_error( $status );
					}

					// Remove the current global video context
					delete_post_meta( $post_id, self::$contextual_videos_context_meta_key, self::$global_video_context );

					// Set the current context for global video flag on this class
					self::$is_current_context_for_global_video = true;

					// Set the current context on this class
					self::$current_context = self::$global_video_context;

					/*
					 * Reset the current context ID on this class.
					 *
					 * Note: This is required for Easy_Support_Video_Admin_Contextual_Videos::get_video_ids_for_current_context_transient_name().
					 * The transient name will not be returned if we do not reset this property
					 * on this class.
					 */
					self::$current_context_id = false;

					// Grab the transient name
					$transient_name = self::get_video_ids_for_current_context_transient_name();

					// If we have the Easy Support Videos contextual videos transient name for the current context
					if ( $transient_name ) {
						// Delete the Easy Support Videos contextual videos transient for the current context
						delete_transient( $transient_name );

						/*
						 * Grab the Easy Support Videos contextual videos video IDs for the current context.
						 *
						 * Note: This will set the transient for the current context.
						 */
						self::get_video_ids_for_current_context( true );
					}
				}
				// Otherwise we don't have a current global video post ID
				else {
					$status['post_id'] = $post_id;
					$status['error'] = $error;
					wp_send_json_error( $status );
				}
			}

			// TODO: Future: Add actions before and after setting global video: do_action( 'wp_ajax_easy_support_videos_edit_wp_update_post_after', $post_id, $title, $this );

			// Update the status data
			$status['post_id'] = $post_id;
			$status['message'] = ( $set_global_video ) ? __( 'This video was set as the global video.', 'easy-support-videos' ) : __( 'This video was removed from the global video context.', 'easy-support-videos' );
			$status['type'] = 'video';
			$status['event'] = 'edit';
			$status['contextual_videos'] = array(
				'set_global_video' => $set_global_video
			);
			$status = apply_filters( 'wp_ajax_easy_support_videos_contextual_videos_set_global_video_success_status', $status, $post_id, $set_global_video, $current_global_video_post_id, $this );

			do_action( 'wp_ajax_easy_support_videos_contextual_videos_set_global_video_wp_send_json_success', $status, $post_id, $set_global_video, $current_global_video_post_id, $this );

			// Success
			wp_send_json_success( $status );
		}


		/********************
		 * Helper Functions *
		 ********************/

		/**
		 * This function returns whether or not Easy Support Videos contextual videos is enabled.
		 */
		public static function is_enabled() {
			// Bail if we already have the enabled value set on this class
			if ( self::$enabled !== null )
				return self::$enabled;

			// Grab the Easy Support Videos options
			$easy_support_videos_options = Easy_Support_Videos_Options::get_options();

			// Set the enabled flag on this class
			self::$enabled = apply_filters( 'easy_support_videos_contextual_videos_is_enabled', ( isset( $easy_support_videos_options['contextual_videos'] ) && isset( $easy_support_videos_options['contextual_videos']['enabled'] ) ) ? $easy_support_videos_options['contextual_videos']['enabled'] : false, $easy_support_videos_options );

			return self::$enabled;
		}

		/**
		 * This function determines if the current user can read Easy Support Videos
		 * contextual videos.
		 */
		public static function current_user_can_read_contextual_videos() {
			// Bail if we already have the current user can read contextual videos flag set on this class
			if ( self::$current_user_can_read_contextual_videos !== null )
				return self::$current_user_can_read_contextual_videos;

			// Grab the Easy Support Videos options
			$easy_support_videos_options = Easy_Support_Videos_Options::get_options();

			// Grab the Easy Support Videos default options
			$easy_support_videos_default_options = Easy_Support_Videos_Options::get_options_defaults();

			// Grab the contextual videos read role
			$contextual_videos_read_role = ( $easy_support_videos_options['contextual_videos'] ) ? $easy_support_videos_options['contextual_videos']['roles']['read'] : $easy_support_videos_default_options['contextual_videos']['roles']['read'];

			// Set the current user can read contextual videos flag on this class
			self::$current_user_can_read_contextual_videos = false;

			// Switch based on the contextual videos read role
			switch ( $contextual_videos_read_role ) {
				// Manage Options (Administrator)
				case 'manage_options':
					// Set the current user can read contextual videos flag on this class
					self::$current_user_can_read_contextual_videos = true;
				break;

				// Edit Pages (Editor)
				case 'edit_pages':
					// If the current user can't manage options
					if ( ! current_user_can( 'manage_options' ) )
						// Set the current user can read contextual videos flag on this class
						self::$current_user_can_read_contextual_videos = true;
				break;

				// Upload Files (Author)
				case 'upload_files':
					// If the current user can't manage options and the current user can't edit pages
					if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'edit_pages' ) )
						// Set the current user can read contextual videos flag on this class
						self::$current_user_can_read_contextual_videos = true;
				break;

				// Edit Posts (Contributor)
				case 'edit_posts':
					// If the current user can't manage options, the current user can't edit pages, and the current user can't upload files
					if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'edit_pages' ) && ! current_user_can( 'upload_files' ) )
						// Set the current user can read contextual videos flag on this class
						self::$current_user_can_read_contextual_videos = true;
				break;

				// Read (Subscriber)
				case 'read':
					// If the current user can't manage options, the current user can't edit pages, the current user can't upload files, and the current user can't edit posts
					if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'edit_pages' ) && ! current_user_can( 'upload_files' ) && ! current_user_can( 'edit_posts' ) )
						// Set the current user can read contextual videos flag on this class
						self::$current_user_can_read_contextual_videos = true;
				break;
			}

			// Set the current user can read contextual videos flag on this class
			self::$current_user_can_read_contextual_videos = apply_filters( 'easy_support_videos_contextual_videos_current_user_can_read_contextual_videos', self::$current_user_can_read_contextual_videos, $contextual_videos_read_role, $easy_support_videos_options, $easy_support_videos_default_options );

			return self::$current_user_can_read_contextual_videos;
		}

		/**
		 * This function returns the current context (WP_Screen).
		 */
		// TODO: Future: Cache contexts based on $screen_id
		public static function get_current_context( $screen_id = false ) {
			// Bail if we already have the current context
			if ( self::$current_context !== false )
				return self::$current_context;

			// Exploded screen ID
			$exploded_screen_id = ( $screen_id && strpos( $screen_id, '|' ) !== false ) ? explode( '|', $screen_id ) : false;

			// Grab the current context
			$current_context = ( $screen_id ) ? WP_Screen::get( ( $exploded_screen_id ) ? $exploded_screen_id[1] : $screen_id ) : get_current_screen();

			// Bail if we don't have the current context
			if ( $current_context === null ) {
				// Set the current context on this class
				self::$current_context = $current_context;

				return self::$current_context;
			}

			// If we have an exploded screen ID
			if ( $exploded_screen_id )
				// If the current context base doesn't match the exploded screen ID base
				if ( $current_context->base !== $exploded_screen_id[0] )
					// Set the current context base
					$current_context->base = $exploded_screen_id[0];

			// Grab the current context ID
			$current_context_id = self::get_current_context_id( $current_context );

			// Grab the included context IDs
			$included_context_ids = self::get_included_context_ids();

			// Current user can edit Easy Support Videos
			$current_user_can_edit_easy_support_videos = Easy_Support_Videos_Post_Types::current_user_can( Easy_Support_Videos_Post_Types::$easy_support_videos_post_type, 'edit_posts' );

			// If we have included context IDs
			if ( ! empty( $included_context_ids ) ) {
				// If the current context ID exists in included context IDs
				if ( in_array( $current_context_id, $included_context_ids ) ) {
					// Set the current context on this class
					self::$current_context = $current_context;

					// Grab the contextual videos active contexts transient
					$contextual_videos_active_contexts = get_transient( self::$active_contexts_transient_name );

					// If the Easy Support Videos contextual videos global video is enabled, the current user can't edit Easy Support Videos, the contextual videos active contexts is an array, and the current context ID doesn't exist in the contextual videos active contexts
					if ( self::is_global_video_enabled() && ! $current_user_can_edit_easy_support_videos && is_array( $contextual_videos_active_contexts ) && ! in_array( $current_context_id, $contextual_videos_active_contexts ) )
						// Set the current context for global video flag on this class
						self::$is_current_context_for_global_video = true;
				}

				// If we don't have a current context on this class, the Easy Support Videos contextual videos global video is enabled, and the global video context exists in the included context IDs
				if ( empty( self::$current_context ) && self::is_global_video_enabled() && in_array( self::$global_video_context, $included_context_ids ) ) {
					// Set the current context on this class
					self::$current_context = $current_context;

					// Set the current context for global video flag on this class
					self::$is_current_context_for_global_video = true;
				}
			}

			// Grab the excluded context IDs
			$excluded_context_ids = self::get_excluded_context_ids();

			// If the Easy Support Videos contextual videos global video is enabled
			if ( self::is_global_video_enabled() ) {
				// If we don't have a current context
				if ( empty( self::$current_context ) ) {
					// If the current context ID doesn't exist in excluded context IDs
					if ( ! in_array( $current_context_id, $excluded_context_ids ) ) {
						// Set the current context on this class
						self::$current_context = $current_context;

						// Grab the contextual videos active contexts transient
						$contextual_videos_active_contexts = get_transient( self::$active_contexts_transient_name );

						// If we have included context IDs or the current user can't edit Easy Support Videos and the current context ID doesn't exist in the contextual videos active contexts
						if ( ! empty( $included_context_ids ) || ( ! $current_user_can_edit_easy_support_videos && ! in_array( $current_context_id, $contextual_videos_active_contexts ) ) )
							// Set the current context for global video flag on this class
							self::$is_current_context_for_global_video = true;
					}
				}
				// Otherwise if the current context ID exists in excluded context IDs
				else if ( in_array( $current_context_id, $excluded_context_ids ) ) {
					// Set the current context on this class
					self::$current_context = null;

					// Reset the current context for global video flag on this class
					self::$is_current_context_for_global_video = false;
				}
			}

			// If we don't have a current context on this class and the current context ID doesn't exist in excluded context IDs
			if ( empty( self::$current_context ) && ! in_array( $current_context_id, $excluded_context_ids ) )
				// Set the current context on this class
				self::$current_context = $current_context;

			// Bail if we don't have the current context
			if ( self::$current_context === false )
				// Set the current context on this class
				self::$current_context = null;

			// Current context
			self::$current_context = apply_filters( 'easy_support_videos_contextual_videos_current_context', self::$current_context, $included_context_ids, $excluded_context_ids );

			return self::$current_context;
		}

		/**
		 * This function returns the current context ID.
		 *
		 * The return format is one of the following:
		 * - The current screen ID (e.g. "post" - Edit Single Post)
		 * - The current screen base + the current screen ID joined with a vertical bar (e.g. "post|page" - Edit Single Page)
		 *
		 * @return string
		 */
		// TODO: Future: Cache context IDs based on $context
		// TODO: Future: Cache valid context IDs in Easy_Support_Videos_Admin_Contextual_Videos::$current_context_id
		public static function get_current_context_id( $context = false ) {
			// Bail if we already have the current context ID
			if ( self::$current_context_id !== false )
				return self::$current_context_id;

			// Grab the current context
			$current_context = ( $context ) ? $context : self::get_current_context();

			// Bail if we don't have the current context
			if ( $current_context === null ) {
				// Set the current context ID on this class
				self::$current_context_id = $current_context;

				return self::$current_context_id;
			}

			// Grab the current context ID
			$current_context_id = ( ! self::is_current_context_for_global_video() ) ? $current_context->id : self::$global_video_context;

			// If the current context isn't for the global video, the current context base does not match the current context ID, and the current context ID doesn't start with the current context base
			if ( ! self::is_current_context_for_global_video() && $current_context->base !== $current_context_id && strpos( $current_context_id, $current_context->base ) !== 0 )
				// Prepend the current context base to the current context ID
				$current_context_id = $current_context->base . '|' . $current_context_id;

			// TODO: Future: Add a filter for developers to adjust the context ID?
			return $current_context_id;
		}

		/**
		 * This function returns the included context IDs.
		 */
		public static function get_included_context_ids() {
			// Bail if we already have the Easy Support Videos contextual videos included context IDs.
			if ( self::$included_context_ids !== null )
				return self::$included_context_ids;

			// Set the included context IDs on this class
			$included_context_ids = array(
				// Manage Posts (edit.php)
				'edit-post',
				// Single Posts (post.php, post-new.php)
				'post',
				// Manage Pages (edit.php?post_type=page)
				'edit-page',
				// Single Pages (post.php, post-new.php?post_type=page) (Note: This is an Easy Support Videos context @see Easy_Support_Videos_Admin_Contextual_Videos::get_current_context_id())
				'post|page',
				// WordPress Profile (profile.php)
				'profile'
			);

			// If Easy Support Videos contextual videos global video is enabled
			if ( self::is_global_video_enabled() )
				// Append the global video context to the included context IDs
				$included_context_ids[] = self::$global_video_context;

			// Set the included context IDs on this class
			self::$included_context_ids = apply_filters( 'easy_support_videos_contextual_videos_included_context_ids', $included_context_ids );

			return self::$included_context_ids;
		}

		/**
		 * This function returns the excluded context IDs.
		 */
		public static function get_excluded_context_ids() {
			// Bail if we already have the Easy Support Videos contextual videos excluded context IDs.
			if ( self::$excluded_context_ids !== null )
				return self::$excluded_context_ids;

			// Excluded context IDs
			$excluded_context_ids = array(
				// Easy Support Videos Menu Page
				Easy_Support_Videos_Post_Types::get_easy_support_videos_menu_page( false )
			);

			// Grab the Easy Support Videos Options Sub-Menu Page
			$easy_support_videos_options_sub_menu_page = Easy_Support_Videos_Admin_Options::get_sub_menu_page( false );

			// If we have the Easy Support Videos Options Sub-Menu Page
			if ( $easy_support_videos_options_sub_menu_page )
				// Append the Easy Support Videos Options Sub-Menu Page to the excluded context IDs
				$excluded_context_ids[] = $easy_support_videos_options_sub_menu_page;

			// Set the excluded context IDs on this class
			self::$excluded_context_ids = apply_filters( 'easy_support_videos_contextual_videos_excluded_context_ids', $excluded_context_ids );

			return self::$excluded_context_ids;
		}

		/**
		 * This function returns whether or not the Easy Support Videos contextual videos global
		 * video is enabled.
		 */
		public static function is_global_video_enabled() {
			// Bail if we already have the enabled value set on this class
			if ( self::$global_video_context_enabled !== null )
				return self::$global_video_context_enabled;

			// Grab the Easy Support Videos options
			$easy_support_videos_options = Easy_Support_Videos_Options::get_options();

			// Set the enabled flag on this class
			self::$global_video_context_enabled = apply_filters( 'easy_support_videos_contextual_videos_is_global_video_enabled', ( isset( $easy_support_videos_options['contextual_videos'] ) && isset( $easy_support_videos_options['contextual_videos']['global_video'] ) && isset( $easy_support_videos_options['contextual_videos']['global_video']['enabled'] ) && isset( $easy_support_videos_options['contextual_videos']['global_video']['enabled'] ) ) ? $easy_support_videos_options['contextual_videos']['global_video']['enabled'] : false, $easy_support_videos_options );

			return self::$global_video_context_enabled;
		}

		/**
		 * This function returns whether or not the current context is being used for the
		 * global video.
		 */
		public static function is_current_context_for_global_video() {
			// TODO: Future: Add a filter for developers to adjust this flag
			return self::$is_current_context_for_global_video;
		}

		/**
		 * This function returns the contextual video context data for an Easy Support Videos video.
		 */
		public function get_context_data_for_video( $post ) {
			// The contextual video contexts
			$the_contextual_video_contexts = array();

			// Bail if Easy Support Videos contextual videos is not enabled
			if ( ! self::is_enabled() )
				return $the_contextual_video_contexts;

			// Current user can edit Easy Support Videos
			$current_user_can_edit_easy_support_videos = Easy_Support_Videos_Post_Types::current_user_can( Easy_Support_Videos_Post_Types::$easy_support_videos_post_type, 'edit_posts' );

			// Bail if the current user can't edit Easy Support Videos
			if ( ! $current_user_can_edit_easy_support_videos )
				return $the_contextual_video_contexts;

			// Grab the post ID
			$post_id = get_post_field( 'ID', $post );

			// Grab the contextual videos context meta value
			$contextual_videos_contexts = $original_contextual_video_contexts = get_post_meta( $post_id, self::$contextual_videos_context_meta_key );

			// If we have contextual videos contexts
			if ( ! empty( $contextual_videos_contexts ) ) {
				// Grab the included context IDs
				$included_context_ids = self::get_included_context_ids();

				// Grab the excluded context IDs
				$excluded_context_ids = self::get_excluded_context_ids();

				// Loop through the contextual videos contexts
				foreach ( $contextual_videos_contexts as $contextual_videos_context_index => $contextual_videos_context ) {
					// Flag to determine if we've unset this contextual videos context
					$has_unset_contextual_videos_context = false;

					// If we have included context IDs
					if ( ! empty( $included_context_ids ) )
						// If this contextual videos context isn't in the included context IDs or the global video isn't enabled and this contextual videos context is the global video context
						if ( ! in_array( $contextual_videos_context, $included_context_ids ) || ( ! self::is_global_video_enabled() && $contextual_videos_context === self::$global_video_context ) ) {
							// Unset this contextual videos context
							unset( $contextual_videos_contexts[$contextual_videos_context_index] );

							// Set the has unset contextual videos context flag
							$has_unset_contextual_videos_context = true;
						}

					// If we haven't unset this contextual videos context
					if ( ! $has_unset_contextual_videos_context )
						// If we have excluded context IDs
						if ( ! empty( $excluded_context_ids ) )
							// If this contextual videos context is in the excluded context IDs
							if ( in_array( $contextual_videos_context, $excluded_context_ids ) )
								// Unset this contextual videos context
								unset( $contextual_videos_contexts[$contextual_videos_context_index] );
				}

				// Unset the contextual videos context index
				unset( $contextual_videos_context_index );

				// Loop through the contextual videos contexts
				foreach ( $contextual_videos_contexts as $contextual_videos_context_index => $contextual_videos_context )
					// Add this contextual videos context to the contextual videos contexts
					$the_contextual_video_contexts[$contextual_videos_context] = $this->get_context_data_for_context( $contextual_videos_context, $post );
			}

			// Sort the contextual video contexts alphabetically TODO: Future: Always keep global at the beginning or end?
			uasort( $the_contextual_video_contexts, array( $this, 'uasort_contextual_video_contexts_alphabetically' ) );

			$the_contextual_video_contexts = apply_filters( 'easy_support_videos_contextual_videos_contexts', $the_contextual_video_contexts, $original_contextual_video_contexts, $post, $this );

			return $the_contextual_video_contexts;
		}

		/**
		 * This function returns the contextual video context data for a context.
		 */
		public function get_context_data_for_context( $contextual_videos_context, $post = array() ) {
			global $menu, $submenu;

			// The contextual video context
			$the_contextual_video_context = array();

			// Bail if Easy Support Videos contextual videos is not enabled
			// TODO: Remove if not necessary (Note: We can't bail here if contextual videos is not enabled due to the Options page displaying the included contexts)
			//if ( ! self::is_enabled() )
			//	return $the_contextual_video_context;

			// Contextual videos context base
			$contextual_videos_context_base = '';

			// If the contextual videos context contains the vertical bar (|) character
			if ( strpos( $contextual_videos_context, '|' ) !== false ) {
				// Explode the contextual videos context
				$exploded_contextual_videos_context = explode( '|', $contextual_videos_context );

				// Set the contextual videos context base
				$contextual_videos_context_base = reset( $exploded_contextual_videos_context );

				// Set the contextual videos context
				$contextual_videos_context = end( $exploded_contextual_videos_context );
			}

			// If the contextual videos context contains "_page_"
			if ( strpos( $contextual_videos_context, '_page_' ) !== false ) {
				// Explode the contextual videos context
				$exploded_contextual_videos_context = explode( '_page_', $contextual_videos_context );

				// Set the contextual videos menu context
				$contextual_videos_context_menu_context = end( $exploded_contextual_videos_context );
			}
			// Otherwise the contextual videos context doesn't contain "_page_"
			else
				// Set the contextual videos menu context
				$contextual_videos_context_menu_context = $contextual_videos_context;

			// Contextual videos context with ".php" suffix
			$contextual_videos_context_with_php_suffix = $contextual_videos_context_menu_context . '.php';


			// Contextual videos context menu item
			$contextual_videos_context_menu_item = false;

			// Contextual videos context sub-menu item
			$contextual_videos_context_sub_menu_item = false;

			// Contextual videos context menu item url
			$contextual_videos_context_menu_item_url = false;

			// Possible contextual videos context menu item file
			$possible_contextual_videos_context_menu_item_file = false;

			// Switch based on the contextual videos menu context
			switch ( $contextual_videos_context_menu_context ) {
				// All (global video)
				case self::$global_video_context:
					// Create a context menu item object
					$contextual_videos_context_menu_item = apply_filters( 'easy_support_videos_contextual_videos_context_menu_item', array(
						__( 'Global', 'easy-support-videos' ),
						'', // Capability
						'', // File
						'', // Empty
						'', // CSS classes
						'', // ID
						'' // Icon
					), $contextual_videos_context, $contextual_videos_context_base, $contextual_videos_context_menu_context, $contextual_videos_context_with_php_suffix, $post, $this );
				break;

				// Single Comment
				case 'comment':
					// Create a context menu item object
					$contextual_videos_context_menu_item = apply_filters( 'easy_support_videos_contextual_videos_context_menu_item', array(
						__( 'Single Comment', 'easy-support-videos' ),
						'', // Capability
						'', // File
						'', // Empty
						'', // CSS classes
						'', // ID
						'' // Icon
					), $contextual_videos_context, $contextual_videos_context_base, $contextual_videos_context_menu_context, $contextual_videos_context_with_php_suffix, $post, $this );
				break;

				// Dashboard
				case 'dashboard':
					// Set the possible contextual videos menu context
					$possible_contextual_videos_context_menu_context = 'index';

					// Set the possible contextual videos context with ".php" suffix
					$possible_contextual_videos_context_with_php_suffix = $possible_contextual_videos_context_menu_context . '.php';
				break;

				// Media -> Add New
				case 'media':
					// Set the possible contextual videos menu context
					$possible_contextual_videos_context_menu_context = 'media-new';

					// Set the possible contextual videos context with ".php" suffix
					$possible_contextual_videos_context_with_php_suffix = $possible_contextual_videos_context_menu_context . '.php';

					// Set the possible contextual videos context menu item file
					$possible_contextual_videos_context_menu_item_file = 'upload.php';
				break;

				// Single User
				case 'user-edit':
					// Create a context menu item object
					$contextual_videos_context_menu_item = apply_filters( 'easy_support_videos_contextual_videos_context_menu_item', array(
						__( 'Single User', 'easy-support-videos' ),
						'', // Capability
						'', // File
						'', // Empty
						'', // CSS classes
						'', // ID
						'' // Icon
					), $contextual_videos_context, $contextual_videos_context_base, $contextual_videos_context_menu_context, $contextual_videos_context_with_php_suffix, $post, $this );
				break;

				// Default
				default:
					// Set the possible contextual videos menu context
					$possible_contextual_videos_context_menu_context = $contextual_videos_context;

					// Set the possible contextual videos context with ".php" suffix
					$possible_contextual_videos_context_with_php_suffix = $contextual_videos_context_with_php_suffix;
				break;
			}

			// If we don't have a contextual videos context menu item
			if ( empty( $contextual_videos_context_menu_item ) )
				// Loop through the menu
				foreach ( $menu as $menu_item )
					// If this menu item file matches the contextual videos menu context, this menu item file matches the contextual videos context with PHP suffix, this menu item file matches the possible contextual videos menu context or this menu item file matches the possible contextual videos context with PHP suffix
					if ( $menu_item[2] === $contextual_videos_context_menu_context || $menu_item[2] === $contextual_videos_context_with_php_suffix || $menu_item[2] === $possible_contextual_videos_context_menu_context || $menu_item[2] === $possible_contextual_videos_context_with_php_suffix ) {
						// Set the contextual videos context menu item
						$contextual_videos_context_menu_item = apply_filters( 'easy_support_videos_contextual_videos_context_menu_item', $menu_item, $contextual_videos_context, $contextual_videos_context_base, $contextual_videos_context_menu_context, $contextual_videos_context_with_php_suffix, $possible_contextual_videos_context_menu_context, $possible_contextual_videos_context_with_php_suffix, $post, $this );

						// Remove HTML from the contextual videos context menu item label
						$contextual_videos_context_menu_item[0] = $this->remove_html_from_menu_item_label( $contextual_videos_context_menu_item[0] );

						// If this contextual videos context menu item file exists in the sub-menu
						if ( isset( $submenu[$contextual_videos_context_menu_item[2]] ) ) {
							// Grab the first sub-menu item for this menu item
							$menu_item_first_sub_menu_item = $submenu[$contextual_videos_context_menu_item[2]][key( $submenu[$contextual_videos_context_menu_item[2]] )];

							// If the menu item label doesn't match the first sub-menu item label
							if ( $menu_item[0] !== $menu_item_first_sub_menu_item[0] ) {
								// Set the contextual videos context sub-menu item
								$contextual_videos_context_sub_menu_item = apply_filters( 'easy_support_videos_contextual_videos_context_sub_menu_item', $menu_item_first_sub_menu_item, $contextual_videos_context, $contextual_videos_context_base, $contextual_videos_context_menu_context, $contextual_videos_context_with_php_suffix, $possible_contextual_videos_context_menu_context, $possible_contextual_videos_context_with_php_suffix, $post, $this );

								// Remove HTML from the contextual videos context sub-menu item label
								$contextual_videos_context_sub_menu_item[0] = $this->remove_html_from_menu_item_label( $contextual_videos_context_sub_menu_item[0] );

								// If the menu item label doesn't match the contextual videos context sub-menu item label
								if ( $menu_item[0] !== $contextual_videos_context_sub_menu_item[0] )
									// Append the first sub-menu item label to the contextual videos context menu item label
									$contextual_videos_context_menu_item[0] .= sprintf( _x( ' (%1$s)', 'the leading space is intentional', 'easy-support-videos' ), $menu_item_first_sub_menu_item[0] );
							}
						}

						// Break from the loop
						break;
					}

			/*
			 * If we don't have a contextual videos context menu item.
			 *
			 * Note: We can check for a sub-menu item that matches the current context or
			 * context with PHP suffix.
			 */
			if ( empty( $contextual_videos_context_menu_item ) ) {
				// Grab the first sub-menu item index
				$first_sub_menu_item_index = ( isset( $submenu[$contextual_videos_context_with_php_suffix] ) ) ? key( $submenu[$contextual_videos_context_with_php_suffix] ) : ( ( isset( $submenu[$contextual_videos_context_menu_context] ) ) ? key( $submenu[$contextual_videos_context_menu_context] ) : -1 );

				// If we don't have the first sub-menu item index
				if ( $first_sub_menu_item_index === -1 )
					// Grab the first sub-menu item index
					$first_sub_menu_item_index = ( isset( $submenu[$possible_contextual_videos_context_with_php_suffix] ) ) ? key( $submenu[$possible_contextual_videos_context_with_php_suffix] ) : ( ( isset( $submenu[$possible_contextual_videos_context_menu_context] ) ) ? key( $submenu[$possible_contextual_videos_context_menu_context] ) : $first_sub_menu_item_index );

				// If we don't have the first sub-menu item index and we have a possible contextual videos context menu item file
				if ( $first_sub_menu_item_index === -1 && $possible_contextual_videos_context_menu_item_file )
					// Grab the first sub-menu item index
					$first_sub_menu_item_index = ( isset( $submenu[$possible_contextual_videos_context_menu_item_file] ) ) ? key( $submenu[$possible_contextual_videos_context_menu_item_file] ) : $first_sub_menu_item_index;

				// Loop through the menu
				foreach ( $submenu as $menu_item_file => $sub_menu_items ) {
					// Loop through the sub-menu items
					foreach ( $sub_menu_items as $sub_menu_item_index => $sub_menu_item )
						// If this sub-menu item file matches the contextual videos menu context, this sub-menu item file matches the contextual videos context with PHP suffix, this sub-menu item file matches the possible contextual videos menu context or this sub-menu item file matches the possible contextual videos context with PHP suffix
						if ( $sub_menu_item[2] === $contextual_videos_context_menu_context || $sub_menu_item[2] === $contextual_videos_context_with_php_suffix || $sub_menu_item[2] === $possible_contextual_videos_context_menu_context || $sub_menu_item[2] === $possible_contextual_videos_context_with_php_suffix ) {
							// Set the contextual videos context sub-menu item
							$contextual_videos_context_sub_menu_item = apply_filters( 'easy_support_videos_contextual_videos_context_sub_menu_item', $sub_menu_item, $contextual_videos_context, $contextual_videos_context_base, $contextual_videos_context_menu_context, $contextual_videos_context_with_php_suffix, $post, $this );

							// Remove HTML from the contextual videos context sub-menu item label
							$contextual_videos_context_sub_menu_item[0] = $this->remove_html_from_menu_item_label( $contextual_videos_context_sub_menu_item[0] );

							// Loop through the menu
							foreach ( $menu as $menu_item )
								// If this menu item file matches the contextual videos context sub_menu item file
								if ( $menu_item[2] === $contextual_videos_context_sub_menu_item[2] ) {
									// Set the contextual videos context menu item
									$contextual_videos_context_menu_item = apply_filters( 'easy_support_videos_contextual_videos_context_menu_item', $menu_item, $contextual_videos_context, $contextual_videos_context_base, $contextual_videos_context_menu_context, $contextual_videos_context_with_php_suffix, $post, $this );

									// Remove HTML from the contextual videos context menu item label
									$contextual_videos_context_menu_item[0] = $this->remove_html_from_menu_item_label( $contextual_videos_context_menu_item[0] );

									// If this contextual videos context with PHP suffix exists in the sub-menu and this sub-menu item index matches the first sub-menu item index
									if ( $sub_menu_item_index === $first_sub_menu_item_index )
										// If the contextual videos context menu item label doesn't match the contextual videos context sub-menu item label
										if ( $contextual_videos_context_menu_item[0] !== $contextual_videos_context_sub_menu_item[0] )
											// Append the first sub-menu item label to the contextual videos context menu item label
											$contextual_videos_context_menu_item[0] .= sprintf( _x( ' (%1$s)', 'the leading space is intentional', 'easy-support-videos' ), $contextual_videos_context_sub_menu_item[0] );

									// Break from the loops
									break 3;
								}
								// Otherwise if this menu item file matches the sub-menu menu item file
								else if ( $menu_item[2] === $menu_item_file ) {
									// Set the contextual videos context menu item
									$contextual_videos_context_menu_item = apply_filters( 'easy_support_videos_contextual_videos_context_menu_item', $menu_item, $contextual_videos_context, $contextual_videos_context_base, $contextual_videos_context_menu_context, $contextual_videos_context_with_php_suffix, $post, $this );

									// Remove HTML from the contextual videos context menu item label
									$contextual_videos_context_menu_item[0] = $this->remove_html_from_menu_item_label( $contextual_videos_context_menu_item[0] );

									// If the contextual videos context menu item label doesn't match the contextual videos context sub-menu item label
									if ( $contextual_videos_context_menu_item[0] !== $contextual_videos_context_sub_menu_item[0] )
										// Append the first sub-menu item label to the contextual videos context menu item label
										$contextual_videos_context_menu_item[0] .= sprintf( _x( ' > %1$s', 'the leading space is intentional', 'easy-support-videos' ), $contextual_videos_context_sub_menu_item[0] );

									// Break from the loops
									break 3;
								}

							// Break from the loops
							break 2;
						}
				}
			}


			/*
			 * If we don't have a menu item and we don't have a sub-menu item.
			 *
			 * Note: It's possible the current context is a taxonomy. We can check all registered
			 * taxonomies to determine if we have a match.
			 *
			 * @see https://github.com/WordPress/WordPress/blob/71dea21c5fe28d3caf4802c86d72b5e178a0c44f/wp-admin/menu.php#L174-L180
			 */
			if ( ( $contextual_videos_context_base === 'edit-tags' || $contextual_videos_context_base === 'term' ) && empty( $contextual_videos_context_menu_item ) && empty( $contextual_videos_context_sub_menu_item ) ) {
				// Possible contextual video context taxonomy
				$possible_contextual_video_context_taxonomy = str_replace( 'edit-', '', $contextual_videos_context );

				// Switch based on the contextual videos context base
				switch ( $contextual_videos_context_base ) {
					// Edit Tags (Manage Taxonomy)
					case 'edit-tags':
						// Grab the taxonomies
						$taxonomies = get_taxonomies( array(
							'show_ui' => true,
							'_builtin' => false
						) );

						// Built-in taxonomies
						$builtin_taxonomies = array(
							'category',
							'post_tag'
						);

						// Merge the built-in taxonomies with the taxonomies
						$taxonomies = array_merge( $builtin_taxonomies, $taxonomies );

						// Loop through the taxonomies
						foreach ( $taxonomies as $taxonomy ) {
							// Grab the taxonomy object
							$taxonomy_object = get_taxonomy( $taxonomy );

							// If the possible contextual video context taxonomy matches the taxonomy
							if ( $possible_contextual_video_context_taxonomy === $taxonomy ) {
								// Grab the capability for the contextual video context
								$capability_contextual_video_context = $taxonomy_object->cap->manage_terms;

								/*
								 * If the current user can manage terms within this taxonomy.
								 *
								 * Note: WordPress always checks the "manage_terms" capability for taxonomy menu items.
								 */
								if ( current_user_can( $capability_contextual_video_context ) ) {
									// Grab the first sub-menu item index
									$first_sub_menu_item_index = ( isset( $submenu[$contextual_videos_context_with_php_suffix] ) ) ? key( $submenu[$contextual_videos_context_with_php_suffix] ) : ( ( isset( $submenu[$contextual_videos_context_menu_context] ) ) ? key( $submenu[$contextual_videos_context_menu_context] ) : -1 );

									// Possible taxonomy sub-menu item files
									$possible_taxonomy_sub_menu_item_files = array();

									// Loop through the taxonomy objects
									foreach ( $taxonomy_object->object_type as $object )
										// Switch based on the object
										switch ( $object ) {
											// Post
											case 'post':
												// Append this object's possible taxonomy sub-menu item file to the possible taxonomy sub-menu item files
												$possible_taxonomy_sub_menu_item_files[] = sprintf( 'edit-tags.php?taxonomy=%1$s', $taxonomy );
											break;

											// Default
											default:
												// Append this object's possible taxonomy sub-menu item file to the possible taxonomy sub-menu item files
												$possible_taxonomy_sub_menu_item_files[] = sprintf( 'edit-tags.php?taxonomy=%1$s&post_type=%2$s', $taxonomy, $object );

												// Append this object's possible taxonomy sub-menu item file to the possible taxonomy sub-menu item files (escaped ampersand)
												$possible_taxonomy_sub_menu_item_files[] = sprintf( 'edit-tags.php?taxonomy=%1$s&amp;post_type=%2$s', $taxonomy, $object );
											break;
										}

									// Loop through the sub-menu
									foreach ( $submenu as $menu_item_file => $sub_menu_items ) {
										// Loop through the sub-menu items
										foreach ( $sub_menu_items as $sub_menu_item_index => $sub_menu_item )
											// If this sub-menu item capability matches the capability for the contextual videos context and this sub-menu item file is in the possible taxonomy sub-menu item files
											if ( $sub_menu_item[1] === $capability_contextual_video_context && in_array( $sub_menu_item[2], $possible_taxonomy_sub_menu_item_files ) ) {
												// Set the contextual videos context sub-menu item
												$contextual_videos_context_sub_menu_item = apply_filters( 'easy_support_videos_contextual_videos_context_sub_menu_item', $sub_menu_item, $contextual_videos_context, $contextual_videos_context_base, $contextual_videos_context_menu_context, $contextual_videos_context_with_php_suffix, $post, $this );

												// Remove HTML from the contextual videos context sub-menu item label
												$contextual_videos_context_sub_menu_item[0] = $this->remove_html_from_menu_item_label( $contextual_videos_context_sub_menu_item[0] );

												// Loop through the menu
												foreach ( $menu as $menu_item )
													// If this menu item file matches the contextual videos context sub_menu item file
													if ( $menu_item[2] === $contextual_videos_context_sub_menu_item[2] ) {
														// Set the contextual videos context menu item
														$contextual_videos_context_menu_item = apply_filters( 'easy_support_videos_contextual_videos_context_menu_item', $menu_item, $contextual_videos_context, $contextual_videos_context_base, $contextual_videos_context_menu_context, $contextual_videos_context_with_php_suffix, $post, $this );

														// Remove HTML from the contextual videos context menu item label
														$contextual_videos_context_menu_item[0] = $this->remove_html_from_menu_item_label( $contextual_videos_context_menu_item[0] );

														// If this contextual videos context with PHP suffix exists in the sub-menu and this sub-menu item index matches the first sub-menu item index
														if ( $sub_menu_item_index === $first_sub_menu_item_index )
															// If the contextual videos context menu item label doesn't match the contextual videos context sub-menu item label
															if ( $contextual_videos_context_menu_item[0] !== $contextual_videos_context_sub_menu_item[0] )
																// Append the first sub-menu item label to the contextual videos context menu item label
																$contextual_videos_context_menu_item[0] .= sprintf( _x( ' (%1$s)', 'the leading space is intentional', 'easy-support-videos' ), $contextual_videos_context_sub_menu_item[0] );

														// Break from the loops
														break 4;
													}
													// Otherwise if this menu item file matches the sub-menu menu item file
													else if ( $menu_item[2] === $menu_item_file ) {
														// Set the contextual videos context menu item
														$contextual_videos_context_menu_item = apply_filters( 'easy_support_videos_contextual_videos_context_menu_item', $menu_item, $contextual_videos_context, $contextual_videos_context_base, $contextual_videos_context_menu_context, $contextual_videos_context_with_php_suffix, $post, $this );

														// Remove HTML from the contextual videos context menu item label
														$contextual_videos_context_menu_item[0] = $this->remove_html_from_menu_item_label( $contextual_videos_context_menu_item[0] );

														// If the contextual videos context menu item label doesn't match the contextual videos context sub-menu item label
														if ( $contextual_videos_context_menu_item[0] !== $contextual_videos_context_sub_menu_item[0] )
															// Append the first sub-menu item label to the contextual videos context menu item label
															$contextual_videos_context_menu_item[0] .= sprintf( _x( ' > %1$s', 'the leading space is intentional', 'easy-support-videos' ), $contextual_videos_context_sub_menu_item[0] );

														// Break from the loops
														break 4;
													}

												// Break from the loops
												break 3;
											}
									}
								}
								// Otherwise the current user can't manage terms within this taxonomy
								else {
									// Create a context menu item object
									$contextual_videos_context_menu_item = apply_filters( 'easy_support_videos_contextual_videos_context_menu_item', array(
										sprintf( __( '%1$s', 'easy-support-videos' ), $taxonomy_object->labels->name ),
										'', // Capability
										'', // File
										'', // Empty
										'', // CSS classes
										'', // ID
										'' // Icon
									), $contextual_videos_context, $contextual_videos_context_base, $contextual_videos_context_menu_context, $contextual_videos_context_with_php_suffix, $post, $this );
								}
							}
						}
					break;

					// Term (Single Edit Term)
					case 'term':
						// Grab the taxonomy object
						$taxonomy_object = get_taxonomy( $possible_contextual_video_context_taxonomy );

						// If we have the taxonomy object
						if ( $taxonomy_object )
							// Create a context menu item object
							$contextual_videos_context_menu_item = apply_filters( 'easy_support_videos_contextual_videos_context_menu_item', array(
								sprintf( __( 'Single %1$s (<code>%2$s</code>)', 'easy-support-videos' ), $taxonomy_object->labels->singular_name, $taxonomy_object->name ),
								'', // Capability
								'', // File
								'', // Empty
								'', // CSS classes
								'', // ID
								'' // Icon
							), $contextual_videos_context, $contextual_videos_context_base, $contextual_videos_context_menu_context, $contextual_videos_context_with_php_suffix, $post, $this );
					break;
				}


			}


			/*
			 * If we don't have a menu item and we don't have a sub-menu item.
			 *
			 * Note: It's possible the current context is a post type. We can check all registered
			 * post types to determine if we have a match.
			 *
			 * @see https://github.com/WordPress/WordPress/blob/a5e57d7245afa0ca96398100140456d2e9c36f78/wp-admin/menu.php#L101-L157
			 */
			if ( empty( $contextual_videos_context_menu_item ) && empty( $contextual_videos_context_sub_menu_item ) ) {
				// Grab the capability for the contextual video context
				$capability_contextual_video_context = str_replace( '-', '_', $contextual_videos_context . 's' );

				// Possible contextual video context post type
				$possible_contextual_video_context_post_type = str_replace( 'edit_', '', substr( $capability_contextual_video_context, 0, -1 ) );

				// Grab the post types
				$post_types = get_post_types( array(
					'show_ui' => true,
					'_builtin' => false
				) );

				// Built-in post types
				$builtin_post_types = array(
					'post',
					'page'
				);

				// Merge the built-in post types with the post types
				$post_types = array_merge( $builtin_post_types, $post_types );

				// Loop through the post types
				foreach ( $post_types as $post_type ) {
					// Grab the post type object
					$post_type_object = get_post_type_object( $post_type );

					// If the post type matches the possible contextual videos context post type
					if ( $post_type === $possible_contextual_video_context_post_type ) {
						/*
						 * If the capability contextual video context matches the edit posts capability.
						 *
						 * Note: WordPress always checks the "edit_posts" capability for post type menu items.
						 */
						if ( $capability_contextual_video_context === $post_type_object->cap->edit_posts ) {
							// Grab the first sub-menu item index
							$first_sub_menu_item_index = ( isset( $submenu[$contextual_videos_context_with_php_suffix] ) ) ? key( $submenu[$contextual_videos_context_with_php_suffix] ) : ( ( isset( $submenu[$contextual_videos_context_menu_context] ) ) ? key( $submenu[$contextual_videos_context_menu_context] ) : -1 );

							// Switch based on the post type
							switch ( $post_type ) {
								// Post
								case 'post':
									// Possible post type sub-menu item file
									$possible_post_type_sub_menu_item_file = 'edit.php';
								break;

								// Default
								default:
									// Possible post type sub-menu item file
									$possible_post_type_sub_menu_item_file = 'edit.php?post_type=' . $post_type;
								break;
							}

							// Loop through the sub-menu
							foreach ( $submenu as $menu_item_file => $sub_menu_items ) {
								// Loop through the sub-menu items
								foreach ( $sub_menu_items as $sub_menu_item_index => $sub_menu_item )
									// If this sub-menu item capability matches the capability for the contextual videos context and this sub-menu item file matches the possible post type sub-menu item file
									if ( $sub_menu_item[1] === $capability_contextual_video_context && $sub_menu_item[2] === $possible_post_type_sub_menu_item_file ) {
										// Set the contextual videos context sub-menu item
										$contextual_videos_context_sub_menu_item = apply_filters( 'easy_support_videos_contextual_videos_context_sub_menu_item', $sub_menu_item, $contextual_videos_context, $contextual_videos_context_base, $contextual_videos_context_menu_context, $contextual_videos_context_with_php_suffix, $post, $this );

										// Remove HTML from the contextual videos context sub-menu item label
										$contextual_videos_context_sub_menu_item[0] = $this->remove_html_from_menu_item_label( $contextual_videos_context_sub_menu_item[0] );

										// Loop through the menu
										foreach ( $menu as $menu_item )
											// If this menu item file matches the contextual videos context sub_menu item file
											if ( $menu_item[2] === $contextual_videos_context_sub_menu_item[2] ) {
												// Set the contextual videos context menu item
												$contextual_videos_context_menu_item = apply_filters( 'easy_support_videos_contextual_videos_context_menu_item', $menu_item, $contextual_videos_context, $contextual_videos_context_base, $contextual_videos_context_menu_context, $contextual_videos_context_with_php_suffix, $post, $this );

												// Remove HTML from the contextual videos context menu item label
												$contextual_videos_context_menu_item[0] = $this->remove_html_from_menu_item_label( $contextual_videos_context_menu_item[0] );

												// If this contextual videos context with PHP suffix exists in the sub-menu and this sub-menu item index matches the first sub-menu item index
												if ( $sub_menu_item_index === $first_sub_menu_item_index )
													// If the contextual videos context menu item label doesn't match the contextual videos context sub-menu item label
													if ( $contextual_videos_context_menu_item[0] !== $contextual_videos_context_sub_menu_item[0] )
														// Append the first sub-menu item label to the contextual videos context menu item label
														$contextual_videos_context_menu_item[0] .= sprintf( _x( ' (%1$s)', 'the leading space is intentional', 'easy-support-videos' ), $contextual_videos_context_sub_menu_item[0] );

												// Break from the loops
												break 4;
											}
											// Otherwise if this menu item file matches the sub-menu menu item file
											else if ( $menu_item[2] === $menu_item_file ) {
												// Set the contextual videos context menu item
												$contextual_videos_context_menu_item = apply_filters( 'easy_support_videos_contextual_videos_context_menu_item', $menu_item, $contextual_videos_context, $contextual_videos_context_base, $contextual_videos_context_menu_context, $contextual_videos_context_with_php_suffix, $post, $this );

												// Remove HTML from the contextual videos context menu item label
												$contextual_videos_context_menu_item[0] = $this->remove_html_from_menu_item_label( $contextual_videos_context_menu_item[0] );

												// If the contextual videos context menu item label doesn't match the contextual videos context sub-menu item label
												if ( $contextual_videos_context_menu_item[0] !== $contextual_videos_context_sub_menu_item[0] )
													// Append the first sub-menu item label to the contextual videos context menu item label
													$contextual_videos_context_menu_item[0] .= sprintf( _x( ' > %1$s', 'the leading space is intentional', 'easy-support-videos' ), $contextual_videos_context_sub_menu_item[0] );

												// Break from the loops
												break 4;
											}

										// Break from the loops
										break 3;
									}
							}
						}
						// Otherwise the capability contextual video context doesn't match the edit posts capability
						else {
							// Possible edit context for post type
							$possible_edit_context_for_post_type = 'edit-' . $post_type;

							// If the possible edit context for post type matches the contextual videos context
							if ( $possible_edit_context_for_post_type === $contextual_videos_context ) {
								// Loop through the sub-menu
								foreach ( $submenu as $menu_item_file => $sub_menu_items ) {
									// Loop through the sub-menu items
									foreach ( $sub_menu_items as $sub_menu_item_index => $sub_menu_item )
										/*
										 * If the sub-menu item URL matches the post type URL.
										 *
										 * Note: In cases such as Easy Digital Downloads, we cannot just check for the "edit_posts"
										 * capability. This is because Easy Digital Downloads uses "product" as their capability
										 * base which results in the "edit_posts" capability being set to "edit_products" and
										 * this does not match the typical "edit_posts" capability we might see for a post type.
										 */
										if ( $sub_menu_item[2] === 'edit.php?post_type=' . $post_type ) {
											// Set the contextual videos context sub-menu item
											$contextual_videos_context_sub_menu_item = apply_filters( 'easy_support_videos_contextual_videos_context_sub_menu_item', $sub_menu_item, $contextual_videos_context, $contextual_videos_context_base, $contextual_videos_context_menu_context, $contextual_videos_context_with_php_suffix, $post, $this );

											// Remove HTML from the contextual videos context sub-menu item label
											$contextual_videos_context_sub_menu_item[0] = $this->remove_html_from_menu_item_label( $contextual_videos_context_sub_menu_item[0] );

											// Loop through the menu
											foreach ( $menu as $menu_item )
												// If this menu item file matches the contextual videos context sub_menu item file
												if ( $menu_item[2] === $contextual_videos_context_sub_menu_item[2] ) {
													// Set the contextual videos context menu item
													$contextual_videos_context_menu_item = apply_filters( 'easy_support_videos_contextual_videos_context_menu_item', $menu_item, $contextual_videos_context, $contextual_videos_context_base, $contextual_videos_context_menu_context, $contextual_videos_context_with_php_suffix, $post, $this );

													// Remove HTML from the contextual videos context menu item label
													$contextual_videos_context_menu_item[0] = $this->remove_html_from_menu_item_label( $contextual_videos_context_menu_item[0] );

													// If this contextual videos context with PHP suffix exists in the sub-menu and this sub-menu item index matches the first sub-menu item index
													if ( $sub_menu_item_index === $first_sub_menu_item_index )
														// If the contextual videos context menu item label doesn't match the contextual videos context sub-menu item label
														if ( $contextual_videos_context_menu_item[0] !== $contextual_videos_context_sub_menu_item[0] )
															// Append the first sub-menu item label to the contextual videos context menu item label
															$contextual_videos_context_menu_item[0] .= sprintf( _x( ' (%1$s)', 'the leading space is intentional', 'easy-support-videos' ), $contextual_videos_context_sub_menu_item[0] );

													// Break from the loops
													break 4;
												}
												// Otherwise if this menu item file matches the sub-menu menu item file
												else if ( $menu_item[2] === $menu_item_file ) {
													// Set the contextual videos context menu item
													$contextual_videos_context_menu_item = apply_filters( 'easy_support_videos_contextual_videos_context_menu_item', $menu_item, $contextual_videos_context, $contextual_videos_context_base, $contextual_videos_context_menu_context, $contextual_videos_context_with_php_suffix, $post, $this );

													// Remove HTML from the contextual videos context menu item label
													$contextual_videos_context_menu_item[0] = $this->remove_html_from_menu_item_label( $contextual_videos_context_menu_item[0] );

													// If the contextual videos context menu item label doesn't match the contextual videos context sub-menu item label
													if ( $contextual_videos_context_menu_item[0] !== $contextual_videos_context_sub_menu_item[0] )
														// Append the first sub-menu item label to the contextual videos context menu item label
														$contextual_videos_context_menu_item[0] .= sprintf( _x( ' > %1$s', 'the leading space is intentional', 'easy-support-videos' ), $contextual_videos_context_sub_menu_item[0] );

													// Break from the loops
													break 4;
												}

											// Break from the loops
											break 3;
										}
								}

								/*
								 * If we don't have a contextual videos context menu item object.
								 *
								 * Note: If we've made it this far we are assuming the context is a single post type.
								 */
								if ( empty( $contextual_videos_context_menu_item ) )
									// Create a context menu item object
									$contextual_videos_context_menu_item = apply_filters( 'easy_support_videos_contextual_videos_context_menu_item', array(
										sprintf( __( 'Single %1$s (<code>%2$s</code>)', 'easy-support-videos' ), $post_type_object->labels->singular_name, $post_type ),
										'', // Capability
										'', // File
										'', // Empty
										'', // CSS classes
										'', // ID
										'' // Icon
									), $contextual_videos_context, $contextual_videos_context_base, $contextual_videos_context_menu_context, $contextual_videos_context_with_php_suffix, $post, $this );
							}
							/*
							 * Otherwise the possible edit context for post type doesn't match the contextual videos context.
							 *
							 * Note: If we've made it this far we are assuming the context is a single post type.
							 */
							else
								// Create a contextual videos context menu item object
								$contextual_videos_context_menu_item = apply_filters( 'easy_support_videos_contextual_videos_context_menu_item', array(
									sprintf( __( 'Single %1$s (<code>%2$s</code>)', 'easy-support-videos' ), $post_type_object->labels->singular_name, $post_type ),
									'', // Capability
									'', // File
									'', // Empty
									'', // CSS classes
									'', // ID
									'' // Icon
								), $contextual_videos_context, $contextual_videos_context_base, $contextual_videos_context_menu_context, $contextual_videos_context_with_php_suffix, $post, $this );

							// Break from the loops
							break;
						}
					}
				}
			}


			/*
			 * If we have a contextual videos context menu item
			 *
			 * @see https://github.com/WordPress/WordPress/blob/92aa2f9e92a4e1ad5471d87d5292587c5cee5702/wp-admin/menu-header.php#L151-L176
			 */
			if ( $contextual_videos_context_menu_item ) {
				// If we have a contextual videos context sub-menu item
				if ( $contextual_videos_context_sub_menu_item ) {
					// Grab the sub-menu hook
					$menu_hook = get_plugin_page_hook( $contextual_videos_context_sub_menu_item[2], $contextual_videos_context_menu_item[2] );

					// Grab the sub-menu file
					$menu_file = $contextual_videos_context_sub_menu_item[2];

					// If we have a question mark in the menu file
					if ( ( $pos = strpos( $menu_file, '?' ) ) !== false )
						// Remove the question mark portion of the menu file
						$menu_file = substr( $menu_file, 0, $pos );

					// If we have a menu hook and the menu file isn't "index.php" and the menu file exists in WordPress core
					if ( ! empty( $menu_hook ) || ( ( $contextual_videos_context_sub_menu_item[2] != 'index.php' ) && file_exists( WP_PLUGIN_DIR . "/$menu_file" ) && ! file_exists( ABSPATH . "/wp-admin/$menu_file" ) ) )
						// Set the contextual videos context URL
						$contextual_videos_context_menu_item_url = 'admin.php?page=' . $contextual_videos_context_sub_menu_item[2];
					else
						// Set the contextual videos context URL
						$contextual_videos_context_menu_item_url = $contextual_videos_context_sub_menu_item[2];
				}
				// Otherwise if we have a contextual videos context menu item file and the current user has the contextual videos context menu item capability
				elseif ( ! empty( $contextual_videos_context_menu_item[2] ) && current_user_can( $contextual_videos_context_menu_item[1] ) ) {
					// Grab the menu hook
					$menu_hook = get_plugin_page_hook( $contextual_videos_context_menu_item[2], 'admin.php' );

					// Grab the menu item file
					$menu_file = $contextual_videos_context_menu_item[2];

					// If we have a question mark in the menu file
					if ( ( $pos = strpos( $menu_file, '?' ) ) !== false )
						// Remove the question mark portion of the menu file
						$menu_file = substr( $menu_file, 0, $pos );

					// If we have a menu hook and the menu file isn't "index.php" and the menu file exists in WordPress core
					if ( ! empty( $menu_hook ) || ( ( 'index.php' != $contextual_videos_context_menu_item[2] ) && file_exists( WP_PLUGIN_DIR . "/$menu_file" ) && ! file_exists( ABSPATH . "/wp-admin/$menu_file" ) ) )
						// Set the contextual videos context URL
						$contextual_videos_context_menu_item_url = 'admin.php?page=' . $contextual_videos_context_menu_item[2];
					else
						// Set the contextual videos context URL
						$contextual_videos_context_menu_item_url = $contextual_videos_context_menu_item[2];
				}
			}

			// Contextual videos context menu item url
			$contextual_videos_context_menu_item_url = apply_filters( 'easy_support_videos_contextual_videos_context_menu_item_url', $contextual_videos_context_menu_item_url, $contextual_videos_context_menu_item, $contextual_videos_context_sub_menu_item, $contextual_videos_context, $contextual_videos_context_base, $contextual_videos_context_menu_context, $contextual_videos_context_with_php_suffix, $post, $this );

			// Set the contextual videos context
			$the_contextual_video_context = apply_filters( 'easy_support_videos_contextual_videos_context', array(
				'id' => $contextual_videos_context,
				'menu_context' => $contextual_videos_context_menu_context,
				'menu' => $contextual_videos_context_menu_item,
				'submenu' => $contextual_videos_context_sub_menu_item,
				'url' => $contextual_videos_context_menu_item_url
			), $contextual_videos_context_menu_item, $contextual_videos_context_sub_menu_item, $contextual_videos_context, $contextual_videos_context_base, $contextual_videos_context_menu_context, $contextual_videos_context_with_php_suffix, $post, $this );

			return $the_contextual_video_context;
		}

		/**
		 * This function returns the Easy Support Videos contextual videos modal actions.
		 *
		 * Note: Actions should be added with the following format:
		 *
		 * 'action_id' => array(
		 * 		'id' => 'action_id',
		 * 		'label' => __( 'Label', 'text-domain' ), // Label for action
		 * 		'icon' => 'dashicons-format-video', // Dashicons icon, URL, HTML snippet
		 * 		'url' => 'https://easysupportvideos.com' // URL for action
		 * )
		 */
		public static function get_modal_actions() {
			// Actions
			$actions = array();

			// Bail if Easy Support Videos contextual videos is not enabled
			if ( ! self::is_enabled() )
				return $actions;

			return apply_filters( 'easy_support_videos_contextual_videos_modal_actions', $actions );
		}


		/**
		 * This function determines if the current request is a editing contextual
		 * videos request.
		 */
		public static function is_editing() {
			// Current user can edit Easy Support Videos
			$current_user_can_edit_easy_support_videos = Easy_Support_Videos_Post_Types::current_user_can( Easy_Support_Videos_Post_Types::$easy_support_videos_post_type, 'edit_posts' );

			return ( $current_user_can_edit_easy_support_videos && isset( $_GET[self::$editing_url_query_argument] ) );
		}


		/**********************
		 * Internal Functions *
		 **********************/

		/**
		 * This function returns the Easy Support Videos contextual videos post IDs.
		 */
		// TODO: Future: Prevent the Easy Support Videos setup wizard from setting the transient
		public static function get_video_ids_for_current_context( $force = false, $cache_results = true ) {
			// Bail if we already have the video IDs for the current context set on this class and we're not forcing
			if ( self::$video_ids_for_current_context !== null && ! $force )
				return self::$video_ids_for_current_context;

			// Grab the current context
			$current_context = self::get_current_context();

			// Grab the current context ID
			$current_context_id = self::get_current_context_id();

			// Easy Support Videos IDs
			$easy_support_videos_ids = array();

			// Current user can edit Easy Support Videos
			$current_user_can_edit_easy_support_videos = Easy_Support_Videos_Post_Types::current_user_can( Easy_Support_Videos_Post_Types::$easy_support_videos_post_type, 'edit_posts' );


			// If we have a current context ID
			if ( $current_context_id ) {
				// Grab the transient name
				$transient_name = self::get_video_ids_for_current_context_transient_name( $force );

				// Easy Support Videos IDs query arguments
				$easy_support_videos_ids_args = array();

				// Easy Support Videos IDs query
				$easy_support_videos_ids_query = false;

				// If we have the Easy Support Videos contextual videos transient name for the current context
				if ( $transient_name ) {
					// Grab the Easy Support Videos contextual videos transient for the current context
					$transient = get_transient( $transient_name );

					// If we don't have the Easy Support Videos IDs contextual videos transient for the current context
					if ( ! $transient ) {
						// Easy Support Videos IDs query arguments
						$easy_support_videos_ids_args = Easy_Support_Videos_Post_Types::$query_args;

						// Set the Easy Support Videos IDs fields query argument
						$easy_support_videos_ids_args['fields'] = 'ids';

						$easy_support_videos_ids_args = apply_filters( 'easy_support_videos_contextual_videos_ids_args', apply_filters( 'easy_support_videos_videos_args', $easy_support_videos_ids_args ) );

						// Grab the Easy Support Videos IDs query
						$easy_support_videos_ids_query = new WP_Query( $easy_support_videos_ids_args );

						// Grab the Easy Support Videos IDs
						$easy_support_videos_ids = ( $easy_support_videos_ids_query->have_posts() ) ? array_map( 'absint', $easy_support_videos_ids_query->posts ) : $easy_support_videos_ids;

						// If we have Easy Support Videos IDs and the current user can edit Easy Support Videos
						if ( $easy_support_videos_ids && $current_user_can_edit_easy_support_videos ) {
							// Set the Easy Support Videos IDs contextual videos transient for the current context
							set_transient( $transient_name, array(
								'ids' => $easy_support_videos_ids,
								'timestamp' => time()
							) );

							// Grab the contextual videos active contexts transient
							$contextual_videos_active_contexts = get_transient( self::$active_contexts_transient_name );

							// If we don't have the contextual videos active contexts
							if ( ! $contextual_videos_active_contexts )
								// Set the contextual videos active contexts
								$contextual_videos_active_contexts = array();

							// If the current context ID doesn't exist in the contextual videos active contexts
							if ( ! in_array( $current_context_id, $contextual_videos_active_contexts ) ) {
								// Append the current context ID to the contextual videos active contexts
								$contextual_videos_active_contexts[] = $current_context_id;

								// Set the contextual videos active contexts transient
								set_transient( self::$active_contexts_transient_name, $contextual_videos_active_contexts );
							}
						}
						// Otherwise if the current user can edit Easy Support Videos
						else if ( $current_user_can_edit_easy_support_videos ) {
							// Grab the contextual videos active contexts transient
							$contextual_videos_active_contexts = get_transient( self::$active_contexts_transient_name );

							// If we don't have the contextual videos active contexts
							if ( ! $contextual_videos_active_contexts )
								// Set the contextual videos active contexts
								$contextual_videos_active_contexts = array();

							// Grab the current context ID in active contexts key
							$current_context_id_in_active_contexts_key = array_search( $current_context_id, $contextual_videos_active_contexts );

							// If we have the current context ID in active contexts key
							if ( $current_context_id_in_active_contexts_key !== false ) {
								// Unset the current context ID from the active contexts
								unset( $contextual_videos_active_contexts[$current_context_id_in_active_contexts_key] );

								// Reset the contextual videos active context keys
								$contextual_videos_active_contexts = array_values( $contextual_videos_active_contexts );

								// Set the contextual videos active contexts transient
								set_transient( self::$active_contexts_transient_name, $contextual_videos_active_contexts );
							}
						}
					}
					// Otherwise we have the Easy Support Videos IDs contextual videos transient for the current context
					else {
						// Set the Easy Support Videos IDs
						$easy_support_videos_ids = ( isset( $transient['ids'] ) ) ? $transient['ids'] : $easy_support_videos_ids;

						// Set the Easy Support Videos IDs query
						$easy_support_videos_ids_query = 'transient';
					}
				}

				// Apply the "easy_support_videos_contextual_videos_video_ids_for_current_context" filter to the video IDs
				$easy_support_videos_ids = apply_filters( 'easy_support_videos_contextual_videos_video_ids_for_current_context', $easy_support_videos_ids, $easy_support_videos_ids_query, $easy_support_videos_ids_args, $current_context_id, $current_context );

				// If we should cache the results
				if ( $cache_results )
					// Set the video IDs for the current context on this class
					self::$video_ids_for_current_context = $easy_support_videos_ids;
			}

			return $easy_support_videos_ids;
		}


		/**
		 * This function returns the Easy Support Videos contextual videos post IDs transient name.
		 */
		public static function get_video_ids_for_current_context_transient_name( $force = false ) {
			// Set the current context TODO: Future: Remove if not necessary
			self::get_current_context();

			// Grab the current context ID
			$current_context_id = self::get_current_context_id();

			// Grab the contextual videos active contexts transient
			$contextual_videos_active_contexts = get_transient( self::$active_contexts_transient_name );

			// If we don't have the contextual videos active contexts
			if ( ! $contextual_videos_active_contexts )
				// Set the contextual videos active contexts
				$contextual_videos_active_contexts = array();

			/*
			 * Transient name
			 *
			 * Note: As of WordPress 4.4.0, transient names can have a maximum of 172 characters.
			 *
			 * @see: https://developer.wordpress.org/reference/functions/set_transient/
			 * @see: https://core.trac.wordpress.org/ticket/13310#comment:79
			 */
			// TODO: Future: Add a filter for developers to adjust the transient name?
			$transient_name = ( $current_context_id && ( in_array( $current_context_id, $contextual_videos_active_contexts ) || $force ) ) ? substr( self::$video_ids_for_current_context_transient_prefix . $current_context_id . self::$video_ids_for_current_context_transient_suffix, 0, 172 ) : false;

			return $transient_name;
		}

		/**
		 * This function removes HTML from a WordPress admin menu item label.
		 *
		 * Note: Menu items can have HTML in their labels (e.g. Plugins (2); when plugin
		 * updates are available).
		 */
		public function remove_html_from_menu_item_label( $menu_item_label ) {
			// Bail if we don't have HTML in this menu item label
			if ( strpos( $menu_item_label, '<' ) === false )
				return $menu_item_label;

			// DOMDocument instance
			static $dom = false;

			// Temporary menu item label
			$temp_menu_item_label = '';

			// If we don't have a DOMDocument instance
			if ( ! $dom )
				// Create a new DOMDocument instance
				$dom = new DOMDocument();

			// Load the menu item label HTML
			$dom->loadHTML( sprintf( '<body>%s</body>', $menu_item_label ) );
			//$doc->loadHTML()

			// Create a new DOMXPath instance
			$xpath = new DOMXPath( $dom );

			// Loop through the text nodes in the DOMDocument
			foreach ( $xpath->query( '//body/text()' ) as $textNode )
				// Add this text node value to the temporary menu item label
				$temp_menu_item_label .= $textNode->nodeValue;

			// Trim the temporary menu item label
			$temp_menu_item_label = trim( $temp_menu_item_label );

			// If the menu item label doesn't match the temporary menu item label
			if ( $menu_item_label !== $temp_menu_item_label )
				// Set the menu item label
				$menu_item_label = $temp_menu_item_label;

			return $menu_item_label;
		}

		/**
		 * This function is meanted to be used as a callback function for uasort() and
		 * sorts contextual video contexts alphabetically in ascending order.
		 */
		public function uasort_contextual_video_contexts_alphabetically( $a, $b ) {
			// If we have a $a contextual videos context menu item
			if ( ! empty( $a['menu'] ) )
				// Set the $a displayed contextual videos context
				$a_displayed_contextual_videos_context = $a['menu'][0];
			// Otherwise we don't have a contextual videos context menu item
			else
				// Set the $a displayed contextual videos context
				$a_displayed_contextual_videos_context = $a['id'];

			// If we have a $b contextual videos context menu item
			if ( ! empty( $b['menu'] ) )
				// Set the $b displayed contextual videos context
				$b_displayed_contextual_videos_context = $b['menu'][0];
			// Otherwise we don't have a contextual videos context menu item
			else
				// Set the $b displayed contextual videos context
				$b_displayed_contextual_videos_context = $b['id'];

			return strcasecmp( $a_displayed_contextual_videos_context, $b_displayed_contextual_videos_context );
		}
	}

	/**
	 * Create an instance of the Easy_Support_Videos_Admin_Contextual_Videos class.
	 */
	function Easy_Support_Videos_Admin_Contextual_Videos() {
		return Easy_Support_Videos_Admin_Contextual_Videos::instance();
	}

	Easy_Support_Videos_Admin_Contextual_Videos();
}