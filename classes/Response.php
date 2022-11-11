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
	* @var Drivers $drivers The drivers object
	*/
	public readonly Drivers $drivers;

	/**
	* @var string $type The response's type
	*/
	public readonly string $type;

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
	* @var array $supported_drivers The supported drivers
	*/
	protected array $supported_drivers = [
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
		$this->type = $this->getType();
		$this->drivers = new Drivers($this->supported_drivers, DriverInterface::class, 'response', $this->app);
		$this->driver = $this->drivers->get($this->type);
		$this->headers = new Headers($this->app);
		$this->cookies = new Cookies($this->app);
		$this->push = new Push($this->app);
	}

	/**
	* Returns the type of the respponse to send
	* @return string
	*/
	protected function getType() : string
	{
		$driver = $this->app->request->getType();

		switch ($driver) {
			case 'ajax':
			case 'json':
				return 'ajax';
			default:
				return 'html';
		}
	}

	/**
	* Returns true if the request should be processed with ajax/json
	*/
	public function isAjax() : bool
	{
		if ($this->type == 'ajax') {
			return true;
		}

		return false;
	}

	/**
	* Returns true if the request should be processed with ajax
	*/
	public function isHtml() : bool
	{
		if ($this->type == 'html') {
			return true;
		}

		return false;
	}

	/**
	* Outputs the $content
	* @param string $content The content to output
	* @param array $data Data to output, if any
	*/
	public function output(string $content = '', array $data = [])
	{
		$this->headers->output();
		$this->push->output();

		$this->driver->output($content, $data);
	}
}
