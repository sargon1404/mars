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
	* @param string $error The error message
	* @param bool $escape_html If true will escape the error message
	*/
	public function fatalError(string $error, bool $escape_html = true)
	{
		if ($escape_html) {
			$error = App::e($error);
		}

		echo 'Fatal Error: ' . nl2br($error) . "\n";
		die;
	}

	/**
	* Displays an error screen
	* @param string $error The error message
	* @param string $title The error title, if any
	* @param bool $escape_html If true will escape the title and error message
	*/
	public function error(string $error, string $title = '', bool $escape_html = true)
	{
		if ($escape_html) {
			$error = App::e($error);
		}

		echo 'Error: ' . $error . "\n";
		die;
	}

	/**
	* Displayes a message screen
	* @param string $message The text of the message
	* @param string $title The title of the message, if any
	* @param bool $escape_html If true will escape the title and message
	*/
	public function message(string $message, string $title = '', bool $escape_html = true)
	{
		if ($escape_html) {
			$message = App::e($message);
		}

		echo 'Message: ' . $message . "\n";
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
