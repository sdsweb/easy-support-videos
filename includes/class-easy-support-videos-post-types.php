<?php
/**
 * Easy Support Videos Post Types
 *
 * @class Easy_Support_Videos_Post_Types
 * @version 2.0.0
 * @since 1.0.0
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'Easy_Support_Videos_Post_Types' ) ) {
	final class Easy_Support_Videos_Post_Types {
		/**
		 * @var string
		 */
		public static $version = '2.0.0';

		/**
		 * @var string
		 */
		public static $easy_support_videos_post_type = 'easy_support_videos';

		/**
		 * @var string
		 */
		public static $easy_support_videos_default_page_title = 'Support Videos';

		/**
		 * @var string
		 */
		public static $easy_support_videos_page_title = 'Support Videos';

		/**
		 * @var array
		 */
		public static $post_type_capabilities = array();

		/**
		 * @var string
		 */
		public static $easy_support_videos_menu_page = 'toplevel_page_easy-support-videos';


		/**
		 * @var string
		 */
		public static $easy_support_videos_menu_page_icon_url = 'dashicons-format-video';

		/**
		 * @var string
		 */
		public static $menu_page_prefix = 'toplevel_page_';

		/**
		 * @var array
		 */
		public static $current_user_can = array();

		/**
		 * @var array
		 */
		public static $query_args = array();

		/**
		 * @var WP_Query | mixed
		 */
		public static $easy_support_videos = null;

		/**
		 * @var string
		 */
		public static $maximum_post_excerpt_length = 300;

		/**
		 * @var string
		 */
		public static $admin_stylesheet_handle = 'easy-support-videos-admin';

		/**
		 * @var string
		 */
		public static $admin_script_handle = 'easy-support-videos-admin';

		/**
		 * @var string
		 */
		public static $admin_fitvids_script_handle = 'easy-support-videos-fitvids';

		/**
		 * @var string
		 */
		public static $published_videos_count_transient_name = 'esv_published_videos_count';

		/**
		 * @var Easy_Support_Videos_Post_Types, Instance of the class
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

			// AJAX
			add_action( 'wp_ajax_easy_support_videos_insert', array( $this, 'wp_ajax_easy_support_videos_insert' ) ); // Easy Support Videos Insert
			add_action( 'wp_ajax_easy_support_videos_edit', array( $this, 'wp_ajax_easy_support_videos_edit' ) ); // Easy Support Videos Edit
			add_action( 'wp_ajax_easy_support_videos_delete', array( $this, 'wp_ajax_easy_support_videos_delete' ) ); // Easy Support Videos Delete
		}

		/**
		 * Include required core files used in admin and on the front-end.
		 */
		private function includes() {
			// Admin Only
			if ( is_admin() )
				include_once 'admin/class-easy-support-videos-admin-views.php'; // Easy Support Videos Admin View Controller
		}

		/**
		 * This function runs on initialization.
		 */
		public function init() {
			// Setup the default page title
			self::$easy_support_videos_default_page_title = __( 'Support Videos', 'easy-support-videos' );

			// Easy Support Videos Page Title
			self::$easy_support_videos_page_title = apply_filters( 'easy_support_videos_menu_page_page_title', self::$easy_support_videos_default_page_title, $this );

			// Easy Support Videos Maximum Post Excerpt Length
			self::$maximum_post_excerpt_length = apply_filters( 'easy_support_videos_maximum_post_excerpt_length', self::$maximum_post_excerpt_length, $this );

			// Grab the Easy Support Videos options
			$easy_support_videos_options = Easy_Support_Videos_Options::get_options();

			// Grab the Easy Support Videos option defaults
			$easy_support_videos_options_defaults = Easy_Support_Videos_Options::get_options_defaults();

			/*
			 * Setup Easy Support Videos capabilities.
			 *
			 * WordPress will automatically map read_posts and read_private_posts for us when registering
			 * the post type, but we need to specify those if the value for reading in Easy Support Videos options
			 * is different from the default value.
			 */
			$easy_support_videos_capabilities = array(
				'edit_post' => $easy_support_videos_options['roles']['edit'],
				'delete_post' => $easy_support_videos_options['roles']['edit'],
				'edit_posts' => $easy_support_videos_options['roles']['edit'],
				'edit_others_posts'	 => $easy_support_videos_options['roles']['edit'],
				'publish_posts' => $easy_support_videos_options['roles']['edit']
			);

			// If the read option value is different than the default
			if ( $easy_support_videos_options['roles']['read'] !== $easy_support_videos_options_defaults['roles']['read'] ) {
				$easy_support_videos_capabilities['read_post'] = $easy_support_videos_options['roles']['read'];
				$easy_support_videos_capabilities['read_posts'] = $easy_support_videos_options['roles']['read']; // Plural
				$easy_support_videos_capabilities['read_private_posts'] = $easy_support_videos_options['roles']['read'];
			}

			// Setup post type capabilities for Easy Support Videos
			self::$post_type_capabilities[self::$easy_support_videos_post_type] = wp_parse_args( $easy_support_videos_capabilities, array(
				'edit_post' => $easy_support_videos_options_defaults['roles']['edit'],
				'delete_post' => $easy_support_videos_options_defaults['roles']['edit'],
				'edit_posts' => $easy_support_videos_options_defaults['roles']['edit'],
				'edit_others_posts'	 => $easy_support_videos_options_defaults['roles']['edit'],
				'publish_posts' => $easy_support_videos_options_defaults['roles']['edit'],
				'read_post' => $easy_support_videos_options_defaults['roles']['read'],
				'read_posts' => $easy_support_videos_options_defaults['roles']['read'], // Plural
				'read_private_posts' => $easy_support_videos_options_defaults['roles']['read']
			) );

			self::$post_type_capabilities = apply_filters( 'easy_support_videos_post_type_capabilities', self::$post_type_capabilities, $easy_support_videos_options, $easy_support_videos_options_defaults, $this );


			/*
			 * Easy Support Videos
			 */
			register_post_type( self::$easy_support_videos_post_type, apply_filters( 'easy_support_videos_post_type_args', array(
				'description' => __( 'Easy Support Videos for embedding helpful tutorials, training videos, and screencasts in the Admin dashboard. Works with YouTube, Vimeo, Wistia, VideoPress, and more!', 'easy-support-videos' ),
				'label' => __( 'Support Videos', 'easy-support-videos' ),
				'labels' => array(
					'name' => _x( 'Support Videos', 'post type general name', 'easy-support-videos' ),
					'singular_name' => _x( 'Support Video', 'post type singular name', 'easy-support-videos' ),
					'menu_name' => __( 'Support Videos', 'easy-support-videos' ),
					'name_admin_bar' => __( 'Support Video', 'easy-support-videos' ),
					'parent_item_colon' => __( 'Parent:', 'easy-support-videos' ),
					'all_items' => __( 'All Support Videos', 'easy-support-videos' ),
					'add_new_item' => __( 'Add New Support Video', 'easy-support-videos' ),
					'add_new' => __( 'Add New', 'easy-support-videos' ),
					'new_item' => __( 'New Support Video', 'easy-support-videos' ),
					'edit_item' => __( 'Edit Support Video', 'easy-support-videos' ),
					'update_item' => __( 'Update Support Video', 'easy-support-videos' ),
					'view_item' => __( 'View Support Video', 'easy-support-videos' ),
					'not_found' => __( 'No Support Videos found', 'easy-support-videos' ),
					'not_found_in_trash' => __( 'Not Support Videos found in Trash', 'easy-support-videos' ),
					'insert_into_item' => __( 'Insert into Support Video', 'easy-support-videos' ),
					'uploaded_to_this_item' => __( 'Uploaded to this Support Video', 'easy-support-videos' ),
					'items_list' => __( 'Support Videos list', 'easy-support-videos' ),
					'items_list_navigation' => __( 'Support Videos list navigation', 'easy-support-videos' ),
					'filter_items_list' => __( 'Filter Support Videos list', 'easy-support-videos' ),
				),
				'public' => false,
				'supports' => array(
					'title',
					'custom-fields'
				),
				'capabilities' => self::$post_type_capabilities[self::$easy_support_videos_post_type],
				//'map_meta_cap' => true,
				'query_var' => false
			), $this ) );

			// Easy Support Videos query arguments
			self::$query_args = apply_filters( 'easy_support_videos_query_args', array(
				'post_type' => self::$easy_support_videos_post_type,
				'posts_per_page' => get_transient( self::$published_videos_count_transient_name ),
				'orderby' => array(
					'menu_order' => 'ASC',
					'date' => 'DESC'
				)
			), $this );
		}

		/**
		 * This function creates the admin menus.
		 */
		public function admin_menu() {
			// Grab the Easy Support Videos options
			$easy_support_videos_options = Easy_Support_Videos_Options::get_options();

			// Easy Support Videos Admin Page (directly after "Settings" which is located at position 80)
			self::$easy_support_videos_menu_page = add_menu_page( self::$easy_support_videos_page_title, apply_filters( 'easy_support_videos_menu_page_menu_title', __( 'Support Videos', 'easy-support-videos' ), $this ), $easy_support_videos_options['roles']['read'], self::get_easy_support_videos_menu_page(), array( $this, 'easy_support_videos_render' ), apply_filters( 'easy_support_videos_menu_page_icon_url', self::$easy_support_videos_menu_page_icon_url, $this ), apply_filters( 'easy_support_videos_menu_page_position', '80.01000101', $this ) );
		}

		/**
		 * This function enqueues scripts and styles on the Easy Support Videos admin page.
		 */
		public function admin_enqueue_scripts( $hook ) {
			// Bail if we're not on the Easy Support Videos page
			if ( $hook !== self::get_easy_support_videos_menu_page( false ) )
				return;

			// Easy Support Videos Admin Stylesheet
			wp_enqueue_style( self::$admin_stylesheet_handle, Easy_Support_Videos::plugin_url() . '/assets/css/easy-support-videos-admin.min.css', array( 'dashicons' ), Easy_Support_Videos::$version );

			// Easy Support Videos Admin Script
			wp_enqueue_script( self::$admin_script_handle, Easy_Support_Videos::plugin_url() . '/assets/js/easy-support-videos-admin.min.js', array( 'jquery', 'underscore', 'wp-backbone', 'wp-util' ), Easy_Support_Videos::$version, true );
			wp_localize_script( self::$admin_script_handle, 'easy_support_videos', apply_filters( 'easy_support_videos_admin_localize', array(
				// Current User Can
				'current_user_can' => array(
					'edit' => self::current_user_can( self::$easy_support_videos_post_type, 'edit_posts' )
				),
				// l10n
				'l10n' => array(
					'video_url_empty' => __( 'Please enter a video URL.', 'easy-support-videos' ),
					'video_title_empty' => __( 'The video title cannot be empty.', 'easy-support-videos' )
				),
				// Videos
				'videos' => array(
					'maximum_post_excerpt_length' => self::$maximum_post_excerpt_length
				)
			), $this ) );

			// Fitvids Script
			wp_enqueue_script( self::$admin_fitvids_script_handle, Easy_Support_Videos::plugin_url() . '/assets/js/fitvids.min.js', self::$admin_script_handle, Easy_Support_Videos::$version, true );
		}

		/**
		 * This function renders the Easy Support Videos admin page.
		 */
		public function easy_support_videos_render() {
			// Render the main view
			Easy_Support_Videos_Admin_Views::render();
		}


		/********
		 * AJAX *
		 ********/

		/**
		 * This function handles the AJAX request for inserting new Easy Support Videos.
		 */
		public function wp_ajax_easy_support_videos_insert() {
			// Status
			$status = array();

			// Generic error message
			$error = apply_filters( 'wp_ajax_easy_support_videos_insert_error_message', __( 'There was an error adding the video. Please try again.', 'easy-support-videos' ), $this );

			// Check AJAX referrer
			if ( ! check_ajax_referer( 'easy_support_videos_insert', 'nonce', false ) ) {
				$status['error'] = $error;
				wp_send_json_error( $status );
			}

			// Return an error if the current user can't publish Easy Support Videos
			if ( ! self::current_user_can( self::$easy_support_videos_post_type, 'publish_posts' ) ) {
				$status['error'] = apply_filters( 'wp_ajax_easy_support_videos_insert_permissions_error_message', __( 'You do not have sufficient permissions to add Easy Support Videos to this site.', 'easy-support-videos' ), $this );
				wp_send_json_error( $status );
			}

			// Grab the WP_oEmbed object
			$wp_oembed = $this->get_wp_oembed_object();

			// Grab the URL
			$url = esc_url_raw( $_POST['url'] );

			// Grab the oEmbed provider
			$provider = apply_filters( 'wp_ajax_easy_support_videos_insert_oembed_provider', $wp_oembed->get_provider( $url ), $url, $this );

			// Return an error if we don't have a provider
			if ( ! $provider ) {
				$status['error'] = apply_filters( 'wp_ajax_easy_support_videos_insert_provider_error_message', __( 'The oEmbed provider isn\'t valid. Please try again.', 'easy-support-videos' ), $this );
				wp_send_json_error( $status );
			}

			// Grab the oEmbed data
			$data = apply_filters( 'wp_ajax_easy_support_videos_insert_oembed_data', $wp_oembed->fetch( $provider, $url ), $provider, $url, $this );

			// Determine if this is a video
			$is_video = apply_filters( 'wp_ajax_easy_support_videos_insert_is_video', ( ! empty( $data ) && $data->type === 'video' ), $data, $provider, $wp_oembed, $this, $url );

			// Return an error if this isn't a video
			if ( ! $is_video ) {
				$status['error'] = apply_filters( 'wp_ajax_easy_support_videos_insert_type_error_message', __( 'The URL provided was not associated with a video. Please try again.', 'easy-support-videos' ), $this );
				wp_send_json_error( $status );
			}

			// Grab the title
			$title = ( ! empty( $data->title ) && is_string( $data->title ) ) ? $data->title : $url;

			// Grab the HTML
			$html = apply_filters( 'wp_ajax_easy_support_videos_insert_oembed_html', $wp_oembed->get_html( $url ), $data, $provider, $url, $this );

			// Return an error if we don't have any HTML for the embed
			if ( empty( $html ) ) {
				$status['error'] = $error;
				wp_send_json_error( $status );
			}

			do_action( 'wp_ajax_easy_support_videos_insert_wp_insert_post_before', $title, $html, self::$easy_support_videos_post_type, $data, $provider, $wp_oembed, $this, $url );

			// Easy Support Videos wp_insert_post() arguments
			$wp_insert_post_args = apply_filters( 'wp_ajax_easy_support_videos_insert_args', array(
				'post_title' => $title,
				'post_content' => $url,
				'post_status' => 'publish',
				'post_type' => self::$easy_support_videos_post_type,
				'meta_input' => array(
					'easy_support_videos_url' => $url
				)
			), $title, $html, self::$easy_support_videos_post_type, $data, $provider, $wp_oembed, $this, $url );

			// Flag to determine if we can insert this Easy Support Video
			$can_insert_video = apply_filters( 'wp_ajax_easy_support_videos_insert_can_insert_video', true, $wp_insert_post_args, $title, $html, self::$easy_support_videos_post_type, $data, $provider, $wp_oembed, $url, $this );

			// Insert the new Easy Support Video
			$post_id = apply_filters( 'wp_ajax_easy_support_videos_insert_post_id', ( $can_insert_video ) ? wp_insert_post( $wp_insert_post_args ) : null, $wp_insert_post_args, $title, $html, self::$easy_support_videos_post_type, $data, $provider, $wp_oembed, $url, $this );

			// Return an error if we don't have a post ID
			if ( ! $post_id || is_wp_error( $post_id ) ) {
				$status['error'] = ( is_wp_error( $post_id ) ) ? $post_id->get_error_message() : $error;
				wp_send_json_error( $status );
			}

			// Update the Easy Support Videos published videos count transient
			self::update_published_videos_count_transient();

			do_action( 'wp_ajax_easy_support_videos_insert_wp_insert_post_after', $post_id, $title, $html, self::$easy_support_videos_post_type, $data, $provider, $wp_oembed, $this, $url );

			// Apply the the_content filter to the embed HTML
			$html = apply_filters( 'the_content', $html );
			$html = str_replace( ']]>', ']]&gt;', $html );

			// Update the status data
			$status['post_id'] = $post_id;
			$status['url'] = $url;
			$status['title'] = $title;
			$status['html'] = $html;
			$status['message'] = __( 'Video added to library', 'easy-support-videos' );
			$status['type'] = 'video';
			$status['event'] = 'insert';
			$status = apply_filters( 'wp_ajax_easy_support_videos_insert_success_status', $status, $post_id, $title, $html, self::$easy_support_videos_post_type, $data, $provider, $wp_oembed, $this );

			do_action( 'wp_ajax_easy_support_videos_insert_wp_send_json_success', $status, $post_id, $title, $html, self::$easy_support_videos_post_type, $data, $provider, $wp_oembed, $this );

			// Success
			wp_send_json_success( $status );
		}

		/**
		 * This function handles the AJAX request for editing Easy Support Videos.
		 */
		public function wp_ajax_easy_support_videos_edit() {
			// Status
			$status = array();

			// Generic error message
			$error = apply_filters( 'wp_ajax_easy_support_videos_edit_error_message', __( 'There was an error editing the video. Please try again.', 'easy-support-videos' ), $this );

			// Check AJAX referrer
			if ( ! check_ajax_referer( 'easy_support_videos_edit', 'nonce', false ) ) {
				$status['error'] = $error;
				wp_send_json_error( $status );
			}

			// Return an error if the current user can't edit Easy Support Videos
			if ( ! self::current_user_can( self::$easy_support_videos_post_type, 'edit_posts' ) ) {
				$status['error'] = apply_filters( 'wp_ajax_easy_support_videos_edit_permissions_error_message', __( 'You do not have sufficient permissions to edit Easy Support Videos on this site.', 'easy-support-videos' ), $this );
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
			if ( get_post_type( $post_id ) !== self::$easy_support_videos_post_type ) {
				$status['post_id'] = $post_id;
				$status['error'] = $error;
				wp_send_json_error( $status );
			}

			// Grab the title
			$title = sanitize_text_field( $_POST['title'] );

			// Return an error if we don't have a title
			if ( empty( $title ) ) {
				$status['post_id'] = $post_id;
				$status['error'] = $error;
				wp_send_json_error( $status );
			}

			// Grab the post excerpt
			$post_excerpt = substr( sanitize_textarea_field( $_POST['post_excerpt'] ), 0, self::$maximum_post_excerpt_length );

			do_action( 'wp_ajax_easy_support_videos_edit_wp_update_post_before', $post_id, $title, $this );

			// Update the post
			$post_id = wp_update_post( apply_filters( 'wp_ajax_easy_support_videos_edit_args', array(
				'ID' => $post_id,
				'post_title' => $title,
				'post_excerpt' => $post_excerpt
			), $post_id, $title, $this ) );

			// Return an error if the post could not be updated
			if ( ! $post_id ) {
				$status['post_id'] = $post_id;
				$status['error'] = $error;
				wp_send_json_error( $status );
			}

			do_action( 'wp_ajax_easy_support_videos_edit_wp_update_post_after', $post_id, $title, $this );

			// Update the status data
			$status['post_id'] = $post_id;
			$status['title'] = wp_unslash( $title );
			$status['message'] = __( 'Video updated.', 'easy-support-videos' );
			$status['type'] = 'video';
			$status['event'] = 'edit';
			$status = apply_filters( 'wp_ajax_easy_support_videos_edit_success_status', $status, $post_id, $title, $this );

			do_action( 'wp_ajax_easy_support_videos_edit_wp_send_json_success', $status, $post_id, $title, $this );

			// Success
			wp_send_json_success( $status );
		}

		/**
		 * This function handles the AJAX request for deleting Easy Support Videos.
		 */
		public function wp_ajax_easy_support_videos_delete() {
			// Status
			$status = array();

			// Generic error message
			$error = apply_filters( 'wp_ajax_easy_support_videos_delete_error_message', __( 'There was an error deleting the video. Please try again.', 'easy-support-videos' ), $this );

			// Check AJAX referrer
			if ( ! check_ajax_referer( 'easy_support_videos_delete', 'nonce', false ) ) {
				$status['error'] = $error;
				wp_send_json_error( $status );
			}

			// Return an error if the current user can't edit Easy Support Videos
			if ( ! self::current_user_can( self::$easy_support_videos_post_type, 'delete_post' ) ) {
				$status['error'] = apply_filters( 'wp_ajax_easy_support_videos_delete_permissions_error_message', __( 'You do not have sufficient permissions to delete Easy Support Videos on this site.', 'easy-support-videos' ), $this );
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
			if ( get_post_type( $post_id ) !== self::$easy_support_videos_post_type ) {
				$status['post_id'] = $post_id;
				$status['error'] = $error;
				wp_send_json_error( $status );
			}

			do_action( 'wp_ajax_easy_support_videos_delete_wp_delete_post_before', $post_id, $this );

			// Flag to determine if we can delete this Easy Support Video
			$can_delete_video = apply_filters( 'wp_ajax_easy_support_videos_delete_can_delete_video', true, $post_id, $this );

			// Delete the post
			$post = apply_filters( 'wp_ajax_easy_support_videos_deleted_post', ( $can_delete_video ) ? wp_delete_post( apply_filters( 'wp_ajax_easy_support_videos_delete_post_id', $post_id, $this ), true ) : null, $post_id, $this );

			// Return an error if the post could not be deleted
			if ( ! $post ) {
				$status['post_id'] = $post_id;
				$status['error'] = $error;
				wp_send_json_error( $status );
			}

			// Update the Easy Support Videos published videos count transient
			self::update_published_videos_count_transient();

			do_action( 'wp_ajax_easy_support_videos_delete_wp_delete_post_after', $post, $post_id, $this );

			// Update the status data
			$status['post_id'] = $post_id;
			$status['message'] = __( 'Video removed from library.', 'easy-support-videos' );
			$status['type'] = 'video';
			$status['event'] = 'video'; // Show the video spinner
			$status = apply_filters( 'wp_ajax_easy_support_videos_delete_success_status', $status, $post_id, $this, $post );

			do_action( 'wp_ajax_easy_support_videos_delete_wp_send_json_success', $status, $post, $post_id, $this );

			// Success
			wp_send_json_success( $status );
		}


		/********************
		 * Helper Functions *
		 ********************/

		/**
		 * This function returns the Easy Support Videos videos.
		 *
		 * @return WP_Query | mixed
		 */
		// TODO: Future: Note/Comment to developers that the returned value is expected to extend WP_Query or at the very least have the same functions
		public static function get_easy_support_videos( $force = false ) {
			// Bail if we already have the Easy Support Videos videos set on this class and we're not forcing
			if ( self::$easy_support_videos !== null && ! $force )
				return self::$easy_support_videos;

			// Grab the Easy Support Videos videos query arguments
			$esv_videos_args = apply_filters( 'easy_support_videos_videos_args', self::$query_args );

			// Grab the Easy Support Videos (default to WP_Query())
			if ( ! ( $easy_support_videos = apply_filters( 'easy_support_videos_videos', false, $esv_videos_args ) ) )
				$easy_support_videos = new WP_Query( $esv_videos_args );

			// Set the Easy Support Videos videos on this class
			self::$easy_support_videos = $easy_support_videos;

			return $easy_support_videos;
		}


		/**********************
		 * Internal Functions *
		 **********************/

		/**
		 * This function returns the menu page. The optional $strip_prefix parameter allows the prefix
		 * added by WordPress to be stripped.
		 */
		public static function get_easy_support_videos_menu_page( $strip_prefix = true ) {
			return apply_filters( 'easy_support_videos_menu_page', ( $strip_prefix ) ? str_replace( self::$menu_page_prefix, '', self::$easy_support_videos_menu_page ) : self::$easy_support_videos_menu_page, $strip_prefix, self::$menu_page_prefix );
		}

		/**
		 * This function returns the WP_oEmbed object.
		 */
		public function get_wp_oembed_object() {
			// Include the embed class
			include_once ABSPATH . WPINC . '/embed.php';

			// If the WordPress oEmbed class file exists (>= 5.3.0)
			if ( file_exists( ABSPATH . WPINC . '/class-wp-oembed.php' ) )
				// Include the WP_oEmbed class
				include_once ABSPATH . WPINC . '/class-wp-oembed.php';
			// Otherwise the WordPress oEmbed class file doesn't exist (< 5.3.0)
			else
				// Include the WP_oEmbed class
				include_once ABSPATH . WPINC . '/class-oembed.php';

			// If the _wp_oembed_get_object() function exists
			if ( function_exists( '_wp_oembed_get_object' ) )
				return _wp_oembed_get_object();

			static $wp_oembed = null;

			if ( is_null( $wp_oembed ) )
				$wp_oembed = new WP_oEmbed();

			return $wp_oembed;
		}

		/**
		 * This function determines if the current user has a specific capability.
		 */
		public static function current_user_can( $post_type, $capability ) {
			// Bail if the post type or capability aren't set
			if ( ! array_key_exists( $post_type, self::$post_type_capabilities ) || ! array_key_exists( $capability, self::$post_type_capabilities[$post_type] ) )
				return apply_filters( 'easy_support_videos_current_user_can', false, $post_type, $capability, self::$post_type_capabilities );

			// Return the cached version
			if ( array_key_exists( $post_type, self::$current_user_can ) && array_key_exists( $capability, self::$current_user_can[$post_type] ) )
				return apply_filters( 'easy_support_videos_current_user_can', self::$current_user_can[$post_type][$capability], $post_type, $capability, self::$post_type_capabilities );

			// Determine if the current user has the capability
			$current_user_can = current_user_can( self::$post_type_capabilities[$post_type][$capability] );

			// If the post type key doesn't exist in the cached current user can data
			if ( ! array_key_exists( $post_type, self::$current_user_can ) )
				// Create it now
				self::$current_user_can[$post_type] = array();

			// If the capability doesn't exist in the cached current user can data
			if ( ! array_key_exists( $capability, self::$current_user_can[$post_type] ) )
				// Create it now
				self::$current_user_can[$post_type][$capability] = $current_user_can;

			return apply_filters( 'easy_support_videos_current_user_can', self::$current_user_can[$post_type][$capability], $post_type, $capability, self::$post_type_capabilities );
		}

		/**
		 * This function updates the Easy Support Videos published videos count transient.
		 */
		public static function update_published_videos_count_transient() {
			// Set the Easy Support Videos published videos count transient
			set_transient( self::$published_videos_count_transient_name, wp_count_posts( self::$easy_support_videos_post_type )->publish );
		}
	}

	/**
	 * Create an instance of the Easy_Support_Videos_Post_Types class.
	 */
	function Easy_Support_Videos_Post_Types() {
		return Easy_Support_Videos_Post_Types::instance();
	}

	Easy_Support_Videos_Post_Types();
}