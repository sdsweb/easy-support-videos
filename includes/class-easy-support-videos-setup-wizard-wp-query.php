<?php
/**
 * Easy Support Videos Setup Wizard WP Query
 *
 * @class Easy_Support_Videos_Setup_Wizard_WP_Query
 * @author Slocum Studio
 * @version 2.0.0
 * @since 2.0.0
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'Easy_Support_Videos_Setup_Wizard_WP_Query' ) ) {
	final class Easy_Support_Videos_Setup_Wizard_WP_Query extends WP_Query {
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
			// Grab the fields query argument
			$fields_query_arg = $this->get( 'fields' );

			/*
			 * Set the fields query argument.
			 *
			 * Note: We're setting this here to ensure that the default
			 * WP_Query logic doesn't make any extra queries.
			 */
			$this->set( 'fields', 'ids' );

			// Hook into "posts_pre_query"
			add_filter( 'posts_pre_query', array( $this, 'posts_pre_query' ), 10, 2 );

			// Call the parent get_posts() method
			parent::get_posts();

			// Remove the "posts_pre_query" hook
			remove_filter( 'posts_pre_query', array( $this, 'posts_pre_query' ) );

			// Posts
			$posts = array();

			// Loop through the Easy Support Videos Setup Wizard posts
			foreach ( Easy_Support_Videos_Setup_Wizard::$posts as $post_id => $post ) {
				// Switch based on the fields query argument
				switch ( $fields_query_arg ) {
					// IDs
					case 'ids':
						// Add the post ID to the posts
						$posts[] = $post_id;
					break;

					// ID to Parent
					case 'id=>parent':
						// Add the post ID to parent to the posts
						$posts[] = array(
							$post_id => 0
						);
					break;

					// Default
					default:
						// Add the post to the posts
						$posts[] = new WP_Post( ( object ) $post );
					break;
				}
			}

			// If we don't have any posts
			if ( empty( $posts ) )
				// Reset posts
				$posts = null;

			// Set the posts on this class
			$this->posts = $posts;

			// Set the post count on this class
			$this->post_count = count( $this->posts );

			// Set the found posts on this class
			$this->set_found_posts( $this->query_vars, '' );

			return $posts;
		}

		/**
		 * This function sets up the current post.
		 */
		public function the_post() {
			/*
			 * Grab the next (current) post.
			 *
			 * Note: This increases the current post count on this class.
			 */
			$post = $this->next_post();

			/*
			 * Decrease the current post count.
			 *
			 * Note: This is necessary because we're calling the parent the_post() method
			 * which, as of 09/27/19, always calls the parent next_post() method.
			 */
			$this->current_post--;

			/*
			 * Grab the post ID.
			 *
			 * Note: We cannot call get_post_field() here because WordPress calls get_post()
			 * within that function.
			 */
			$post_id = $post->ID;

			/*
			 * Delete this post from the WordPress object cache.
			 *
			 * Note: We're deleting this post from the cache here because WordPress checks the cache
			 * in WP_Post::get_instance();. See below.
			 */
			wp_cache_delete( $post_id, 'posts' );

			/*
			 * Add this post to the WordPress object cache.
			 *
			 * Note: We're adding this post to the cache here because WordPress checks the cache
			 * in WP_Post::get_instance();.
			 */
			wp_cache_add( $post_id, $post, 'posts' );

			// Call the parent the_post() method
			parent::the_post();
		}

		/**
		 * This function sets up the amount of found posts and the number of pages (if limit clause was used)
		 * for the current query.
		 *
		 * Note: This method was taken directly from WP_Query on 01/29/20 (5.3.0). We are
		 * including this method in this class due to the private access in WP_Query.
		 */
		private function set_found_posts( $q, $limits ) {
			global $wpdb;

			// Bail if posts is an empty array. Continue if posts is an empty string,
			// null, or false to accommodate caching plugins that fill posts later.
			if ( $q['no_found_rows'] || ( is_array( $this->posts ) && ! $this->posts ) )
				return;

			if ( ! empty( $limits ) ) {
				/**
				 * Filters the query to run for retrieving the found posts.
				 *
				 * @since 2.1.0
				 *
				 * @param string   $found_posts The query to run to find the found posts.
				 * @param WP_Query $this        The WP_Query instance (passed by reference).
				 */
				$this->found_posts = $wpdb->get_var( apply_filters_ref_array( 'found_posts_query', array( 'SELECT FOUND_ROWS()', &$this ) ) );
			} else {
				if ( is_array( $this->posts ) ) {
					$this->found_posts = count( $this->posts );
				} else {
					if ( null === $this->posts ) {
						$this->found_posts = 0;
					} else {
						$this->found_posts = 1;
					}
				}
			}

			/**
			 * Filters the number of found posts for the query.
			 *
			 * @since 2.1.0
			 *
			 * @param int      $found_posts The number of posts found.
			 * @param WP_Query $this        The WP_Query instance (passed by reference).
			 */
			$this->found_posts = apply_filters_ref_array( 'found_posts', array( $this->found_posts, &$this ) );

			if ( ! empty( $limits ) )
				$this->max_num_pages = ceil( $this->found_posts / $q['posts_per_page'] );
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

			/*
			 * Set the posts to an empty array.
			 *
			 * Note: We're setting this to an empty array because, as of 05/20/20,
			 * WordPress checks for a null value prior to running the query. This
			 * will ensure that query is bypassed.
			 */
			$posts = array();

			return $posts;
		}
	}
}