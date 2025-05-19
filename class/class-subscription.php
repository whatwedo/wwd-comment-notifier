<?php
namespace whatwedo\CommentNotifier;

/**
 * Subscription
 *
 * @since      1.0.0
 * @package    wwd-comment-notifier
 */

class Subscription {

	private $post_id;

	function __construct( $post_id ) {

		$this->post_id = $post_id;

	}


	/**
	 * Subscribe to a post
	 *
	 * @param string $email user's email
	 * @param string $name user's name
	 */

	public function subscribe( $email, $name ) {

		global $wpdb;

		// Check if user is already subscribed to this post
		$subscribed = $wpdb->get_var(
			$wpdb->prepare( 'SELECT count(*) FROM ' . $wpdb->prefix . Config::DBTABLE . ' WHERE post_id=%d AND email=%s', $this->post_id, $email )
		);

		if ( $subscribed > 0 ) {
			return;
		}

		$token = md5( rand() ); // The random token for unsubscription

		$res = $wpdb->insert(
			$wpdb->prefix . Config::DBTABLE,
			array(
				'post_id' => $this->post_id,
				'email'   => $email,
				'name'    => $name,
				'token'   => $token,
			)
		);
	}


	/*
	protected function has_subscriber() {



	}
	*/


	/**
	 * Subscribe after comment has been approved
	 *
	 * @param string $email comment author's email
	 * @param string $name comment author's name
	 * @param int    $comment_id comment id
	 */

	public function subscribe_later( $email, $name, $comment_id ) {
		global $wpdb;

		// Check if user is already subscribed to this post
		$subscribed = $wpdb->get_var(
			$wpdb->prepare(
				'SELECT count(*) FROM ' . $wpdb->prefix . Config::DBTABLE . ' WHERE post_id=%d AND email=%s',
				$this->post_id, $email
			)
		);

		if ( $subscribed > 0 ) {
			 return;
		}

		// Did the comment author check the box to subscribe?
		if ( $comment_id ) {
			if ( get_comment_meta( $comment_id, Config::PREFIX . '_subscribe', true ) ) {

				// The random token for unsubscription
				$token = md5( rand() );
				$res   = $wpdb->insert(
					$wpdb->prefix . Config::DBTABLE, array(
						'post_id' => $this->post_id,
						'email'   => $email,
						'name'    => $name,
						'token'   => $token,
					)
				);

				delete_comment_meta( $comment_id, Config::PREFIX . '_subscribe' );
			}
		}

	}


	/**
	 * Removes a subscription
	 *
	 * @param int    $id unsubsrciption id
	 * @param string $token verification code
	 */

	public function unsubscribe( $token ) {
		global $wpdb;

		$wpdb->delete(
			$wpdb->prefix . Config::DBTABLE,
			array(
				'post_id' => $this->post_id,
				'token'   => $token,
			),
			array( '%d', '%s' )
		);

		// $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->prefix . WWDCN_DBTABLE . " WHERE post_id = %d AND token = %s", $this->post_id, $token));
	}


	/**
	 * Unsubscription redirect
	 */

	public function unsubscribe_redirect() {

		$unsubscribe_url = Config::get( 'unsubscribe.url' );

		// When custom url is set just make a simple redirect
		if ( $unsubscribe_url ) {

			header( 'Location: ' . $unsubscribe_url );

		} else {

			// Redirect to subscriped post permalink
			$redirect_url = Config::get( 'unsubscribe.redirect_url' );
			if ( ! $redirect_url && ! is_null( $this->post_id ) ) {
				$redirect_url = get_permalink( $this->post_id );
			}

			$timeout = Config::get( 'unsubscribe.redirect_time' );
			$message = Config::get( 'unsubscribe.text' );
			$title   = Config::get( 'unsubscribe.title' );
			$status  = 301;

			header( 'Refresh: ' . $timeout . ';' . $redirect_url );
			wp_die( $message, $title, array( 'response' => $status ) );

		}

	}

}
