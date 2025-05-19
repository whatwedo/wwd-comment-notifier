<?php
namespace whatwedo\CommentNotifier;

/**
 * Notification
 *
 * @since      1.0.0
 * @package    wwd-comment-notifier
 */

class Notification {

	private $comment = '';
	private $content = '';

	function __construct() {

		global $wpdb;
		$this->wpdb = $wpdb;

	}


	/*
		Notify all subscriber
	 */

	function notify( $comment_id ) {

		$this->comment = get_comment( $comment_id );

		// Make sure it's a real comment (no pingpack or trackback)
		if ( !$this->comment || 'trackback' == $this->comment->comment_type || 'pingback' == $this->comment->comment_type ) {
			return;
		}

		// Make sure set correct post_id via comment hook
		$post_id = apply_filters( Config::PREFIX . '_comment_postid', $this->comment->comment_post_ID );

		if ( empty( $post_id ) ) {
			return;
		}

		// Find all subscriber of post by id
		$subscriptions = $this->find_subscriber( $post_id );

		$post = get_post( $post_id );
		if ( empty( $post ) ) {
			 return;
		}

		// Prepare mail content
		$this->content = $this->prepare_content( $post );

		if ( ! is_null( $subscriptions ) ) {

			$idx = 0;
			$ok  = 0;

			foreach ( $subscriptions as $subscription ) {
			    // Don't send mail to comment author itself
                if($subscription->email == $this->comment->comment_author_email) {
                    continue;
                }

				$idx++;
				$message = $this->content['message'];

				// Filter content by personalized placeholderes
				$message = new Content( $this->content['message'] );
				$message->filter_placeholder( $post, $subscription, true );

				// Filter subject by personalized placeholderes
				$subject = new Content( $this->content['subject'] );
				$subject->filter_placeholder( $post, $subscription, true );

				// Send mail
				$mail_instance = new Mail( $subscription->email, $subject->output(), $message->output() );
				if ( $mail_instance->send() ) {
					$ok++;
				}
			}

			// Error Log
			if ( $ok != $idx ) {
				// log error - not all subscriber where informed
			} else {
				return true;
			}
		}

	}


	/*
		Find all subscriber
	 */

	protected function find_subscriber( $post_id ) {

		$subscriptions = $this->wpdb->get_results(
			$this->wpdb->prepare(
				'SELECT * FROM ' . $this->wpdb->prefix . Config::DBTABLE . ' WHERE post_id=%d',
				$post_id
			)
		);

		if ( ! $subscriptions ) {
			return;
		} else {
			return $subscriptions;
		}

	}


	/*
		Prepare mail content
	 */

	protected function prepare_content( $post ) {

		// Mail Body
		$mail_content = Config::get( 'mail.content' );

		$message = new Content( $mail_content );
		$message->add_line_break();
		$message->filter_placeholder( $post, $this->comment );

		// Mail Subject
		$mail_subject = Config::get( 'mail.subject' );

		$subject = new Content( $mail_subject );
		$subject->filter_placeholder( $post, $this->comment );

		// Return content as array
		return array(
			'message' => $message->output(),
			'subject' => $subject->output(),
		);

	}

}
