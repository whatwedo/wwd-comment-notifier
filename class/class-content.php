<?php
namespace whatwedo\CommentNotifier;

/**
 * Handle content for mail
 *
 * @since      1.0.0
 * @package    wwd-comment-notifier
 */

class Content {

	protected $available_placeholders;
	protected $content;

	function __construct( $content ) {

		$this->content = $content;

	}


	/*
		Create comment excerpt
	 */

	private function comment_excerpt( $comment_content ) {

		$length = Config::get( 'mail.excerpt_length' );
		if ( $length ) {
			if ( strlen( $comment_content ) > $length ) {
				$x = strpos( $comment_content, ' ', $length );
				if ( $x !== false ) {
					$comment_content = substr( $comment_content, 0, $x ) . '...';
				}
			}
		}

		return $comment_content;

	}


	/*
		Set comment time according to wp settings format
	*/

	private function comment_time( $time ) {

		$date        = strtotime( $time );
		$date_format = date_i18n( get_option( 'date_format' ), $date );

		return $date_format;

	}


	/*
		Generate unsubscribe link
	*/

	private function unsubscribe_link( $post_id, $token ) {

		$base_url = get_the_permalink( $post_id );
		$link     = $base_url . '?' . Config::PREFIX . '_token=' . $token;

		return $link;

	}


	/*
		Replace magic placeholders on general content
	 */

	public function filter_placeholder( $post, $context, $personalized = false ) {

		// General placeholder
		if ( ! $personalized ) {

			$this->content = str_replace(
				[
					'{post_name}',
					'{post_link}',
					'{post_author}',
					'{comment_excerpt}',
					'{comment_author}',
					'{comment_link}',
					'{comment_time}',
				], [
					$post->post_title,
					get_the_permalink( $post->ID ),
					get_the_author_meta( 'display_name', $post->post_author ),
					$this->comment_excerpt( $context->comment_content ),
					$context->comment_author,
					get_comment_link( $context->comment_ID ),
					$this->comment_time( $context->comment_date ),
				],
				$this->content
			);

			// Subscriber specific
		} else {

			$this->content = str_replace(
				[
					'{name}',
					'{unsubscribe_link}',
				], [
					$context->name,
					$this->unsubscribe_link( $post->ID, $context->token ),
				],
				$this->content
			);

		}

	}


	/*
		Add line break to content
	 */

	public function add_line_break() {

		$this->content = implode( '<br>', $this->content );

	}


	/*
		Clean output prepared content
	 */

	public function output() {

		return $this->content;

	}

}
