<?php
namespace whatwedo\CommentNotifier;

/**
 * WP Hooks
 *
 * @since      1.0.0
 * @package    wwd-comment-notifier
 */

class WP {

	function __construct() {

		( new Comment() )->actions();

		// add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_script' ) );
		// add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_script' ) );

		add_filter( 'comment_form_submit_field', array( Markup::class, 'action_comment_form' ), 9999 );

		add_action( 'wp', array( $this, 'get_singular_post' ) );

	}


	/*
		GET Request onto singular post
	 */

	public function get_singular_post() {

		// Make sure we're on a single post entry
		if ( is_singular( 'post' ) ) {

			$token = $_GET[ Config::PREFIX . '_token' ] ?? null;

			// Show normal post when token is not set
			if ( ! $token ) {
				return;
			}

			$post_id = get_queried_object()->ID;

			// Unsubscribe when token is set and on post data are available
			if ( isset( $post_id ) && isset( $token ) ) {
				$subscription = new Subscription( $post_id );

				$subscription->unsubscribe( $token );
				$subscription->unsubscribe_redirect();
			}
		}

	}

}
