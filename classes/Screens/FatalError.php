<?php
/**
* The Fatal Error Screen
* @package Mars
*/

namespace Mars\Screens;

/**
 * The Fatal Error Screen
 * Displays a fatal error screen
 */
class FatalError
{
	use \Mars\AppTrait;

	/**
	 * Displays a fatal error screen
	 * @param string $text The error's text
	 * @param bool $escape_html If true will escape the error message
	 */
	public function output(string $text, ?bool $escape_html = null)
	{
		$escape_html = $escape_html ?? !$this->app->is_bin;
		
		if ($escape_html) {
			$text = $this->app->escape->html($text);
		}

		if (!$this->app->is_bin) {
			$text = nl2br($text);
		}

		echo 'Fatal Error: ' . $text . "\n";
		die;
	}
}
