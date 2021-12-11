<?php
/**
* The Screen Class
* @package Mars
*/

namespace Mars\Document;

use Mars\App;

/**
* The Screen Class
* Contains 'Screen' functionality. Eg: error, message screens etc..
*/
class Screen
{
	use \Mars\AppTrait;

	/**
	* Displays a fatal error screen
	* @param string $text The error's text
	* @param bool $escape_html If true will escape the error message
	*/
	public function fatalError(string $text, bool $escape_html = true)
	{
		if ($escape_html) {
			$text = App::e($text);
		}
		
		if (!$this->app->is_bin) {
			$text = nl2br($text);
		}

		echo 'Fatal Error: ' . $text . "\n";
		die;
	}

	/**
	* Displays an error screen
	* @param string $text The error's text
	* @param string $title The error's title, if any
	* @param bool $escape_html If true will escape the title and error message
	*/
	public function error(string $text, string $title = '', bool $escape_html = true)
	{
		if ($escape_html) {
			$text = App::e($text);
		}

		echo 'Error: ' . $text . "\n";
		die;
	}

	/**
	* Displayes a message screen
	* @param string $text The text of the message
	* @param string $title The title of the message, if any
	* @param bool $escape_html If true will escape the title and message
	*/
	public function message(string $text, string $title = '', bool $escape_html = true)
	{
		if ($escape_html) {
			$text = App::e($text);
		}

		echo 'Message: ' . $text . "\n";
		die;
	}

	/**
	* Displays the Permission Denied screen
	*/
	public function permissionDenied()
	{
		echo 'Permission denied!' . "\n";
		die;
	}
}
