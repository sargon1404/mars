<?php
/**
* The Screens Class
* @package Mars
*/

namespace Mars;

use Mars\Serializers\DriverInterface;

/**
* The Screen Class
* Contains 'Screen' functionality. Eg: error, message, fatal error screens etc..
*/
class Screens
{
	use AppTrait;

	/**
	* @var Handlers $handlers The screens handlers
	*/
	public readonly Handlers $handlers;

	/**
	* @var array $screens_list The list of supported screens
	*/
	protected array $screens_list = [
		'error' => '\Mars\Screens\Error',
		'message' => '\Mars\Screens\Message',
		'fatal_error' => '\Mars\Screens\FatalError',
		'permission_denied' => '\Mars\Screens\PermissionDenied'
	];

	/**
	* Constructs the screens object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;
		$this->handlers = new Handlers($this->screens_list, $this->app);
	}
	
	/**
	* Displays an error screen
	* @param string $text The error's text
	* @param string $title The error's title, if any
	* @param bool $escape_html If true will escape the title and error message
	*/
	public function error(string $text, string $title = '', bool $escape_html = true)
	{
		$this->handlers->get('error')->output($text, $title, $escape_html);
	}

	/**
	* Displayes a message screen
	* @param string $text The text of the message
	* @param string $title The title of the message, if any
	* @param bool $escape_html If true will escape the title and message
	*/
	public function message(string $text, string $title = '', bool $escape_html = true)
	{
		$this->handlers->get('message')->output($text, $title, $escape_html);
	}
	
	/**
	* Displays an error screen
	* @param string $text The error's text
	* @param bool $escape_html If true will escape the error message
	*/
	public function fatalError(string $text, bool $escape_html = true)
	{
		$this->handlers->get('fatal_error')->output($text, $escape_html);
	}
	
	/**
	* Displays the Permission Denied screen
	*/
	public function permissionDenied()
	{
		$this->handlers->get('permission_denied')->output();
	}
}
