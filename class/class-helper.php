<?php
namespace whatwedo\CommentNotifier;

/**
 * Helper
 *
 * @since      1.0.0
 * @package    wwd-comment-notifier
 */

class Helper {

	/*
		Get basic comment data
	 */

	public static function get_comment_data( $comment_id ) {

		$comment = get_comment( $comment_id );

		$post_id = $comment->comment_post_ID;
		$email   = strtolower( trim( $comment->comment_author_email ) );
		$name    = $comment->comment_author;

		return ''; // ?? how to expose best from variable and static method ??

	}

}
