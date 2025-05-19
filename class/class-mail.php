<?php
namespace whatwedo\CommentNotifier;

/**
 * Mail
 *
 * @since      1.0.0
 * @package    wwd-comment-notifier
 */

class Mail {

	protected $to;
	protected $subject;
	protected $message;

	function __construct( $to, $subject, $message ) {

		$this->to      = $to;
		$this->subject = $subject;
		$this->message = $message;

	}


	/*
		Set headers
	 */

	protected function headers() {

		$headers[] = 'Content-Type: text/html; charset=UTF-8';
		/*
		if ( ! empty( $options['name'] ) && ! empty( $options['from'] ) ) {
			$headers[] = 'From: "' . $options['name'] . '" <' . $options['from'] . ">\n";
		}
		*/

		return $headers;

	}


	/*
		Set content
	 */

	protected function content() {

		return wpautop( $this->message );

	}


	/*
		Send mail
	 */

	public function send() {

		return wp_mail( $this->to, $this->subject, $this->message, $this->headers() );

	}

}
