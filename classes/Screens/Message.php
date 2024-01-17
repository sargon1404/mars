<?php
/**
* The Message Screen
* @package Mars
*/

namespace Mars\Screens;

/**
 * The Message Screen
 * Displays a message screen
 */
class Message
{
	use \Mars\AppTrait;

	/**
	 * Outputs the error screen
	 * @param string $text The error's text
	 * @param string $title The error's title, if any
	 * @param bool $escape_html If true will escape the title and message
	 */
	public function output(string $text, string $title = '', ?bool $escape_html = null)
	{
		$escape_html = $escape_html ?? !$this->app->is_bin;

		if ($escape_html) {
			$text = $this->app->escape->html($text);
		}

		echo 'Message: ' . $text . "\n";
		die;
	}
}
