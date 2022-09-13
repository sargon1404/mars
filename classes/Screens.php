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
		$this->handlers = new Handlers($this->screens_list, '', true);
	}
}
