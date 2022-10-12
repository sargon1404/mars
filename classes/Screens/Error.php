<?php
/**
* The Error Screen
* @package Mars
*/

namespace Mars\Screens;

/**
* The Error Screen
* Displays an error screen
*/
class Error
{
	use \Mars\AppTrait;

	/**
	* Outputs the error screen
	* @param string $text The error's text
	* @param string $title The error's title, if any
	* @param bool $escape_html If true will escape the title and error message
	*/
	public function output(string $text, string $title = '', bool $escape_html = true)
	{
		if ($escape_html) {
			$text = $this->app->escape->html($text);
		}

		echo 'Error: ' . $text . "\n";
		die;
	}
}
