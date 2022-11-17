<?php
/**
* The Response Class
* @package Mars
*/

namespace Mars;

use Mars\Response\Types\DriverInterface;
use Mars\Response\Cookies;
use Mars\Response\Headers;
use Mars\Response\Push;

/**
* The Response Class
* Outputs the system's html/ajax response
*/
class Response
{
	use AppTrait;

	/**
	* @var Handlers $handlers The handlers object
	*/
	public readonly Handlers $handlers;

	/**
	* @var Cookies $cookies The cookies object
	*/
	public Cookies $cookies;

	/**
	* @var Headers $headers The headers object
	*/
	public Headers $headers;

	/**
	* @var Push $push The server push object
	*/
	public Push $push;

	/**
	* @var DriverInterface $driver The driver object
	*/
	protected DriverInterface $driver;

	/**
	* @var array $supported_handlers The supported handlers
	*/
	protected array $supported_handlers = [
		'ajax' => '\Mars\Response\Types\Ajax',
		'html' => '\Mars\Response\Types\Html'
	];

	/**
	* Builds the Response object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;
		$this->handlers = new Handlers($this->supported_handlers, $this->app);
		$this->handlers->setInterface(DriverInterface::class);
		$this->headers = new Headers($this->app);
		$this->cookies = new Cookies($this->app);
		$this->push = new Push($this->app);
	}

	/**
	* Returns the type of the respponse to send
	* @return string
	*/
	protected function getType(string $type) : string
	{
		switch ($type) {
			case 'ajax':
			case 'json':
				return 'ajax';
			default:
				return 'html';
		}
	}

	/**
	* Returns the converted content to $type
	* @param mixed $content The content
	* @param string $type The type
	* @return mixed
	*/
	public function get($content, string $type)
	{
		return $this->handlers->get($type)->get($content);
	}

	/**
	* Outputs the $content
	* @param string string The content to output
	* @param string $type The content's type
	*/
	public function output(string $content, string $type)
	{
		$this->headers->output();

		$this->handlers->get($type)->output($content);
	}
}
