<?php
namespace whatwedo\CommentNotifier;

/**
 * Markup
 *
 * @since      1.0.0
 * @package    wwd-comment-notifier
 */

class Markup {

	/*
		Add a subscribe checkbox above submit button
	 */

	public static function action_comment_form( $submit_field ) {

		$checkbox = self::checkbox_html();

		return $checkbox . $submit_field;

	}


	/*
		Returns the HTML for the checkbox as a string
	 */

	protected static function checkbox_html() {

		$html = '';

		$html .= '<p class="wp-comment-form-subscription">';

			$html .= '<input
				type="checkbox"
				value="1"
				name="' . Config::CHECKBOX_ID . '"
				class="wp-comment-checkbox-subscription"
				id="wp-comment-checkbox-subscription"';

			if ( Config::get( 'checkbox.default_state' ) ) {
				$html .= ' checked="checked"';
			}

			$html .= '>';

			$html .= '<label class="wp-comment-label-subscription" for="wp-comment-checkbox-subscription">' . Config::get( 'checkbox.label' ) . '</label>';

		$html .= '</p>';

		return $html;

	}

}
