<?php
/**
 * Easy Support Videos Contextual Videos WP Query
 *
 * @class Easy_Support_Videos_Contextual_Videos_WP_Query
 * @author Slocum Studio
 * @version 2.0.0
 * @since 2.0.0
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'Easy_Support_Videos_Contextual_Videos_WP_Query' ) ) {
	final class Easy_Support_Videos_Contextual_Videos_WP_Query extends WP_Query {
		/**
		 * @var string
		 */
		public $version = '2.0.0';

		/**
		 * This function sets up all of the actions and filters on instance. It also loads (includes)
		 * the required files and assets.
		 */
		function __construct( $query_args ) {
			// Call the parent constructor
			parent::__construct( $query_args );
		}

		/**
		 * This function returns posts based on the query arguments.
		 */
		public function get_posts() {
			/*
			 * Set the fields query argument.
			 *
			 * Note: We're setting this here to ensure that the default
			 * WP_Query logic doesn't make any extra queries.
			 *
			 * Note: We're setting this to an unsupported value of
			 * "esv_contextual_videos_ids" to ensure WP_Query::set_found_posts()
			 * does not run any queries.
			 */
			$this->set( 'fields', 'esv_contextual_videos_ids' );

			// Hook into "posts_pre_query"
			add_filter( 'posts_pre_query', array( $this, 'posts_pre_query' ), 10, 2 );

			// Call the parent get_posts() method
			$posts = parent::get_posts();

			//Set the fields query argument
			$this->set( 'fields', 'ids' );

			// Remove the "posts_pre_query" hook
			remove_filter( 'posts_pre_query', array( $this, 'posts_pre_query' ) );

			return $posts;
		}


		/*********
		 * Hooks *
		 *********/

		/**
		 * This function adjusts the posts before the query.
		 */
		public function posts_pre_query( $posts, $query ) {
			// Bail if the query doesnt match this query
			if ( $query !== $this )
				return $posts;

			// Remove this hook
			remove_filter( 'posts_pre_query', array( $this, 'posts_pre_query' ) );

			// Grab the Easy Support Videos contextual videos
			$video_ids_for_current_context = $this->get( Easy_Support_Videos_Admin_Contextual_Videos::$contextual_videos_query_arg );

			// Set the posts
			$posts = $video_ids_for_current_context;

			return $posts;
		}
	}
}