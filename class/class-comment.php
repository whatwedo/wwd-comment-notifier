<?php
namespace whatwedo\CommentNotifier;

/**
 * Comment
 *
 * @since      1.0.0
 * @package    wwd-comment-notifier
 */

class Comment {

	/*
		Actions
	 */

	function actions() {

		add_action( 'wp_set_comment_status', array( $this, 'comment_approved' ), 10, 2 );

		add_action( 'comment_post', array( $this, 'comment_applied' ), 10, 2 );

	}


	/**
	 * Comment was applied to post
	 *
	 * @param int        $comment_id the database id of the comment.
	 * @param int|string $status Whether the comment is approved, 0 (in moderation), 1 (approved) or spam.
	 */

	public function comment_applied( $comment_id, $status ) {

		$comment = get_comment( $comment_id );
        if(!$comment) return;

		$post_id = apply_filters( Config::PREFIX . '_comment_postid', $comment->comment_post_ID );

		// Only subscribe if comment is approved; skip those in moderation.
		// If comment is approved automatically, notify subscribers
		if ( 1 === $status ) {

			( new Notification() )->notify( $comment_id );

			// If checkbox is checked add new subscriber
			if ( isset( $_POST[ Config::CHECKBOX_ID ] ) ) {
				$name  = $comment->comment_author;
				$email = filter_var( $comment->comment_author_email, FILTER_VALIDATE_EMAIL );

				( new Subscription( $post_id ) )->subscribe( $email, $name );
			}
		}

		// If comment author subscribed, add comment meta key for pending subscription.
		if ( isset( $_POST[ Config::CHECKBOX_ID ] ) ) {
			add_comment_meta( $comment_id, Config::PREFIX . '_subscribe', true, true );
		}

	}


	/*
		Comment was approved
	 */

	public function comment_approved( $comment_id, $status ) {

		$comment = get_comment( $comment_id );
        if(!$comment) return;

		// Helper::get_comment_data( $comment_id );
		$post_id = apply_filters( Config::PREFIX . '_comment_postid', $comment->comment_post_ID );

		// When a comment is approved later, notify the subscribers, and subscribe this comment author
		if ( 'approve' === $status ) {
			$email = strtolower( trim( $comment->comment_author_email ) );
			$name  = $comment->comment_author;

			( new Notification() )->notify( $comment_id );
			( new Subscription( $post_id ) )->subscribe_later( $email, $name, $comment_id );
		}

	}

}
